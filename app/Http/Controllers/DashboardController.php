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
        // 1. Ambil Kuantitas Data Dasar (Kecuali Logs)
        $totalDomains = \App\Models\Subdomains::count();
        $totalNews = \DB::table('data_news')->count();
        $totalParts = \DB::table('data_parts')->count();
        $totalPages = \DB::table('data_pages')->count();
        $totalImages = \DB::table('data_images')->count();

        // 2. Ambil Data Bobot Kriteria yang Aktif
        $currentCriteria = \App\Models\Criterias::first();
        $w1 = $currentCriteria ? $currentCriteria->c1 : 25;
        $w2 = $currentCriteria ? $currentCriteria->c2 : 25;
        $w3 = $currentCriteria ? $currentCriteria->c3 : 25;
        $w4 = $currentCriteria ? $currentCriteria->c4 : 25;

        // 3. Setup Parameter Default Sinkronisasi SAW (Hari Ini & Rentang 90 Hari)
        $maxDate = \Carbon\Carbon::now()->endOfDay();
        $intervalHari = 90;
        $minDate = \Carbon\Carbon::now()->subDays($intervalHari)->startOfDay();

        // 4. Query Aggregation Kilat (Optimasi dari Perbaikan Sebelumnya)
        $newsCountsC1 = \DB::table('data_news')->whereBetween('created_at', [$minDate, $maxDate])->groupBy('subdomain')->pluck(\DB::raw('count(*)'), 'subdomain')->toArray();
        $imageCountsC1 = \DB::table('data_images')->whereBetween('created_at', [$minDate, $maxDate])->groupBy('subdomain')->pluck(\DB::raw('count(*)'), 'subdomain')->toArray();
        $pageCountsC1 = \DB::table('data_pages')->whereBetween('created_at', [$minDate, $maxDate])->groupBy('subdomain')->pluck(\DB::raw('count(*)'), 'subdomain')->toArray();
        $partsCountsC1 = \DB::table('data_parts')->whereBetween('created_at', [$minDate, $maxDate])->groupBy('subdomain')->pluck(\DB::raw('count(*)'), 'subdomain')->toArray();

        $newsCountsC2 = \DB::table('data_news')->groupBy('subdomain')->pluck(\DB::raw('count(*)'), 'subdomain')->toArray();
        $imageCountsC2 = \DB::table('data_images')->groupBy('subdomain')->pluck(\DB::raw('count(*)'), 'subdomain')->toArray();
        $pageCountsC2 = \DB::table('data_pages')->groupBy('subdomain')->pluck(\DB::raw('count(*)'), 'subdomain')->toArray();
        $partsCountsC2 = \DB::table('data_parts')->groupBy('subdomain')->pluck(\DB::raw('count(*)'), 'subdomain')->toArray();

        $latestNews = \DB::table('data_news')->select('subdomain', \DB::raw('MAX(created_at) as max_date'))->where('created_at', '<=', $maxDate)->groupBy('subdomain')->pluck('max_date', 'subdomain')->toArray();
        $latestImage = \DB::table('data_images')->select('subdomain', \DB::raw('MAX(created_at) as max_date'))->where('created_at', '<=', $maxDate)->groupBy('subdomain')->pluck('max_date', 'subdomain')->toArray();
        $latestPage = \DB::table('data_pages')->select('subdomain', \DB::raw('MAX(created_at) as max_date'))->where('created_at', '<=', $maxDate)->groupBy('subdomain')->pluck('max_date', 'subdomain')->toArray();
        $latestPart = \DB::table('data_parts')->select('subdomain', \DB::raw('MAX(created_at) as max_date'))->where('created_at', '<=', $maxDate)->groupBy('subdomain')->pluck('max_date', 'subdomain')->toArray();

        $subdomains = \DB::table('subdomains')->get();
        $matrixKeputusan = [];

        foreach ($subdomains as $sub) {
            $subdomainName = $sub->subdomain;

            // Hitung C1
            $nilaiC1 = (($newsCountsC1[$subdomainName] ?? 0) + ($imageCountsC1[$subdomainName] ?? 0) + ($pageCountsC1[$subdomainName] ?? 0) + ($partsCountsC1[$subdomainName] ?? 0)) / 12;

            // Hitung C2
            $nilaiC2 = ($newsCountsC2[$subdomainName] ?? 0) + ($imageCountsC2[$subdomainName] ?? 0) + ($pageCountsC2[$subdomainName] ?? 0) + ($partsCountsC2[$subdomainName] ?? 0);

            // Hitung C3 (Logika Fix Excel Aman)
            $dates = array_filter([$latestNews[$subdomainName] ?? null, $latestImage[$subdomainName] ?? null, $latestPage[$subdomainName] ?? null, $latestPart[$subdomainName] ?? null]);
            $selisihHari = $intervalHari;
            if (!empty($dates)) {
                $lastPostDate = \Carbon\Carbon::parse(max($dates));
                $selisihHari = $lastPostDate->greaterThan($maxDate) ? $intervalHari : min((int) $maxDate->diffInDays($lastPostDate, true), $intervalHari);
            }
            $nilaiC3 = max(0, $intervalHari - $selisihHari);

            // Hitung C4 (Threshold Skripsi)
            $totalKategoriTerisi = (($newsCountsC2[$subdomainName] ?? 0) > 10 ? 1 : 0) + (($imageCountsC2[$subdomainName] ?? 0) >= 10 ? 1 : 0) + (($pageCountsC2[$subdomainName] ?? 0) >= 5 ? 1 : 0) + (($partsCountsC2[$subdomainName] ?? 0) >= 1 ? 1 : 0);
            $nilaiC4 = ($totalKategoriTerisi / 4) * 100;

            $matrixKeputusan[] = ['subdomain' => $subdomainName, 'c1' => $nilaiC1, 'c2' => $nilaiC2, 'c3' => $nilaiC3, 'c4' => $nilaiC4];
        }

        // 5. Kalkulasi Normalisasi & Preferensi Akhir SAW
        $maxC1 = max(array_column($matrixKeputusan, 'c1')) ?: 1;
        $maxC2 = max(array_column($matrixKeputusan, 'c2')) ?: 1;
        $maxC3 = max(array_column($matrixKeputusan, 'c3')) ?: 1;
        $maxC4 = max(array_column($matrixKeputusan, 'c4')) ?: 1;

        $hasilRanking = [];
        foreach ($matrixKeputusan as $row) {
            $skor = (($row['c1'] / $maxC1) * ($w1 / 100)) + (($row['c2'] / $maxC2) * ($w2 / 100)) + (($row['c3'] / $maxC3) * ($w3 / 100)) + (($row['c4'] / $maxC4) * ($w4 / 100));
            $hasilRanking[] = [
                'subdomain' => $row['subdomain'],
                'skor' => round($skor, 4)
            ];
        }

        // 6. Urutkan Ranking & Ambil Top 5
        usort($hasilRanking, function ($a, $b) {
            return $b['skor'] <=> $a['skor'];
        });

        $top5Rankings = array_slice($hasilRanking, 0, 5);

        return view('dashboard', compact(
            'totalDomains',
            'totalNews',
            'totalParts',
            'totalPages',
            'totalImages',
            'currentCriteria',
            'top5Rankings'
        ));
    }
}
