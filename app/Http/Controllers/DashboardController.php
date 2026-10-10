<?php

namespace App\Http\Controllers;

use App\Models\Subdomains;
use App\Services\SawService;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(SawService $saw)
    {
        // 1. Ambil Kuantitas Data Dasar (Kecuali Logs)
        $totalDomains = Subdomains::count();
        $totalNews = DB::table('data_news')->count();
        $totalParts = DB::table('data_parts')->count();
        $totalPages = DB::table('data_pages')->count();
        $totalImages = DB::table('data_images')->count();

        // 2. Hitung SAW dengan tanggal maksimal hari ini (rentang 90 hari)
        $result = $saw->calculate(now()->toDateString());

        // 3. Kriteria & bobot aktif, serta Top 5 ranking
        $kriteria = $result['kriteria'];
        $totalBobot = $result['totalBobot'];
        $top5Rankings = array_slice($result['hasilRanking'], 0, 5);

        return view('dashboard', compact(
            'totalDomains',
            'totalNews',
            'totalParts',
            'totalPages',
            'totalImages',
            'kriteria',
            'totalBobot',
            'top5Rankings'
        ));
    }
}
