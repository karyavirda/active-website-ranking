<?php

namespace App\Http\Controllers;

use App\Models\Subdomains;  // Sesuaikan jika nama modelmu Subdomain / Subdomains
use App\Models\Criterias;   // Model kriteria yang kita buat kemarin
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Ambil Jumlah Data (Counters)
        $totalDomains = Subdomains::count();
        $totalNews = DB::table('data_news')->count();
        $totalParts = DB::table('data_parts')->count();
        $totalPages = DB::table('data_pages')->count();
        $totalImages = DB::table('data_images')->count();
        $totalLogs = DB::table('data_logs')->count();

        // 2. Ambil Data Bobot Kriteria yang Aktif (Single Record)
        $currentCriteria = Criterias::first();

        // 3. Slot untuk Top 5 Perhitungan SAW (Sementara kita buat data dummy dulu)
        $top5Rankings = [
            ['no' => 1, 'subdomain' => 'smk-budimulya.sch.id', 'score' => 0.95],
            ['no' => 2, 'subdomain' => 'sman1-bantul.sch.id', 'score' => 0.89],
            ['no' => 3, 'subdomain' => 'sd-pelitaharapan.sch.id', 'score' => 0.84],
            ['no' => 4, 'subdomain' => 'mts-alhikmah.web.id', 'score' => 0.78],
            ['no' => 5, 'subdomain' => 'smpn2-cirebon.web.id', 'score' => 0.72],
        ];

        return view('dashboard', compact(
            'totalDomains',
            'totalNews',
            'totalParts',
            'totalPages',
            'totalImages',
            'totalLogs',
            'currentCriteria',
            'top5Rankings'
        ));
    }
}
