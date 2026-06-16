<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subdomains;
use App\Models\Criterias;

class SawController extends Controller
{
    public function index()
    {
        return view('saw.index');
    }

    public function process(Request $request)
    {
        // 1. Tangkap parameter input
        $maxDate = $request->input('max_date');
        $periodRange = $request->input('period_range');

        // 2. Ambil bobot kriteria yang aktif
        $currentCriteria = Criterias::first();

        // 3. Matriks Keputusan Awal ($X$)
        $matrixKeputusan = [
            ['subdomain' => 'smk-budimulya.sch.id', 'c1' => 80, 'c2' => 95, 'c3' => 85, 'c4' => 70, 'c5' => 90],
            ['subdomain' => 'sman1-bantul.sch.id', 'c1' => 75, 'c2' => 85, 'c3' => 90, 'c4' => 80, 'c5' => 75],
            ['subdomain' => 'sd-pelitaharapan.sch.id', 'c1' => 90, 'c2' => 70, 'c3' => 80, 'c4' => 65, 'c5' => 85],
            ['subdomain' => 'mts-alhikmah.web.id', 'c1' => 65, 'c2' => 80, 'c3' => 75, 'c4' => 85, 'c5' => 70],
            ['subdomain' => 'smpn2-cirebon.web.id', 'c1' => 85, 'c2' => 60, 'c3' => 70, 'c4' => 90, 'c5' => 65],
        ];

        // 4. LOGIKA SAW: Cari nilai MAX untuk tiap kriteria (karena semuanya BENEFIT)
        $maxC1 = max(array_column($matrixKeputusan, 'c1'));
        $maxC2 = max(array_column($matrixKeputusan, 'c2'));
        $maxC3 = max(array_column($matrixKeputusan, 'c3'));
        $maxC4 = max(array_column($matrixKeputusan, 'c4'));
        $maxC5 = max(array_column($matrixKeputusan, 'c5'));

        // 5. LOGIKA SAW: Hitung Matriks Ternormalisasi ($R$)
        $matrixNormalisasi = [];
        foreach ($matrixKeputusan as $row) {
            $matrixNormalisasi[] = [
                'subdomain' => $row['subdomain'],
                'c1' => round($row['c1'] / $maxC1, 2),
                'c2' => round($row['c2'] / $maxC2, 2),
                'c3' => round($row['c3'] / $maxC3, 2),
                'c4' => round($row['c4'] / $maxC4, 2),
                'c5' => round($row['c5'] / $maxC5, 2),
            ];
        }

        // 6. Lempar data ke View
        return view('saw.index', compact(
            'maxDate',
            'periodRange',
            'currentCriteria',
            'matrixKeputusan',
            'matrixNormalisasi'
        ));
    }
}
