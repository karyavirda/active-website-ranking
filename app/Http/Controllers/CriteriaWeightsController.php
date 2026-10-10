<?php

namespace App\Http\Controllers;

use App\Models\Criterias;
use App\Models\CriteriaWeights;
use Illuminate\Http\Request;

class CriteriaWeightsController extends Controller
{
    public function index()
    {
        $criterias = Criterias::with('weight')->orderBy('kode')->get();
        return view('criteria-weights.index', compact('criterias'));
    }

    public function store(Request $request)
    {
        $criteriaIds = Criterias::pluck('id');

        $request->validate([
            'bobot' => 'required|array',
            'bobot.*' => 'required|numeric|min:0|max:100',
        ]);

        // Hanya ambil bobot untuk kriteria yang memang ada
        $bobot = collect($request->input('bobot'))->only($criteriaIds->all());
        $total = round($bobot->sum(), 2);

        if ($bobot->count() !== $criteriaIds->count()) {
            return back()->withInput()->with('error', 'Bobot untuk semua kriteria wajib diisi.');
        }

        if ($total != 100) {
            return back()->withInput()->with('error', 'Total bobot harus berjumlah 100! Sekarang totalnya: ' . $total);
        }

        foreach ($bobot as $criteriaId => $nilai) {
            CriteriaWeights::updateOrCreate(
                ['criteria_id' => $criteriaId],
                ['bobot' => $nilai]
            );
        }

        return redirect()->route('criteria-weights.index')->with('success', 'Bobot kriteria berhasil disimpan!');
    }
}
