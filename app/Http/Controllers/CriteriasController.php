<?php

namespace App\Http\Controllers;

use App\Models\Criterias;
use Illuminate\Http\Request;

class CriteriasController extends Controller
{
    public function index()
    {
        // Mengambil kriteria pertama yang ada (asumsi 1 record per sistem/subdomain)
        $criteria = Criterias::first();
        return view('criterias.index', compact('criteria'));
    }

    public function store(Request $request)
    {
        // 1. Validasi: Pastikan total adalah 100 (atau 1 jika menggunakan desimal)
        // Kita gunakan custom validator agar fleksibel
        $request->validate([
            'c1' => 'required|numeric',
            'c2' => 'required|numeric',
            'c3' => 'required|numeric',
            'c4' => 'required|numeric',
        ]);

        $total = $request->c1 + $request->c2 + $request->c3 + $request->c4;

        // Cek jika total bukan 100
        if ($total != 100) {
            return back()->with('error', 'Total bobot harus berjumlah 100! Sekarang totalnya: ' . $total);
        }

        // 2. Logic Update atau Create
        $criteria = Criterias::first();

        if ($criteria) {
            $criteria->update($request->all());
        } else {
            Criterias::create($request->all());
        }

        return redirect()->route('criterias.index')->with('success', 'Bobot kriteria berhasil disimpan!');
    }
}
