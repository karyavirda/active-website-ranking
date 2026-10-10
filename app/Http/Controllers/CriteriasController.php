<?php

namespace App\Http\Controllers;

use App\Models\Criterias;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CriteriasController extends Controller
{
    public function index()
    {
        $criterias = Criterias::with('weight')->orderBy('kode')->get();
        return view('criterias.index', compact('criterias'));
    }

    public function create()
    {
        return view('criterias.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['kode'] = strtoupper($data['kode']);

        $criteria = Criterias::create($data);
        // Bobot awal 0, diatur kemudian di menu Bobot Kriteria
        $criteria->weight()->create(['bobot' => 0]);

        return redirect()->route('criterias.index')->with('success', 'Kriteria berhasil ditambahkan. Jangan lupa atur bobotnya di menu Bobot.');
    }

    public function edit(Criterias $criteria)
    {
        return view('criterias.edit', compact('criteria'));
    }

    public function update(Request $request, Criterias $criteria)
    {
        $data = $this->validated($request, $criteria->id);
        $data['kode'] = strtoupper($data['kode']);

        $criteria->update($data);

        return redirect()->route('criterias.index')->with('success', 'Kriteria berhasil diupdate');
    }

    public function destroy(Criterias $criteria)
    {
        // Hapus bobot secara eksplisit, karena cascade tidak berjalan jika tabel memakai MyISAM
        $criteria->weight()->delete();
        $criteria->delete();

        return redirect()->route('criterias.index')->with('success', 'Kriteria dihapus. Periksa kembali total bobot kriteria.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'kode' => ['required', 'string', 'max:10', Rule::unique('criterias', 'kode')->ignore($ignoreId)],
            'nama' => 'required|string|max:255',
            'tipe' => 'required|in:benefit,cost',
            'satuan' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
        ], [
            'kode.unique' => 'Kode kriteria ini sudah digunakan!',
            'tipe.in' => 'Tipe kriteria harus benefit atau cost.',
        ]);
    }
}
