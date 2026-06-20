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
        // 1. Tangkap parameter input dari form depan (Tanggal Maksimal & Rentang Analisis)
        $maxDateInput = $request->input('max_date'); // Ini tanggal_max dari form
        $periodRange = $request->input('period_range'); // Ini pilihan dropdown rentang waktu

        // Ambil bobot kriteria yang aktif dari database
        $currentCriteria = \DB::table('criterias')->first();

        // Set nilai bobot (C1 - C4) dari DB, jika belum ada diset default 25%
        $w1 = $currentCriteria ? $currentCriteria->c1 : 25;
        $w2 = $currentCriteria ? $currentCriteria->c2 : 25;
        $w3 = $currentCriteria ? $currentCriteria->c3 : 25;
        $w4 = $currentCriteria ? $currentCriteria->c4 : 25;

        // 2. Tentukan jumlah hari interval berdasarkan pilihan dropdown period_range untuk rumus C3 & query C1
        $intervalHari = 90; // Default 3 bulan
        if ($periodRange === '7_days') {
            $intervalHari = 7;
        } elseif ($periodRange === '30_days') {
            $intervalHari = 30;
        } elseif ($periodRange === '6_months') {
            $intervalHari = 180;
        } elseif ($periodRange === '1_year') {
            $intervalHari = 365;
        }

        // Konversi objek tanggal Carbon berdasarkan input user
        $maxDate = \Carbon\Carbon::parse($maxDateInput)->endOfDay();
        $minDate = \Carbon\Carbon::parse($maxDateInput)->subDays($intervalHari)->startOfDay();

        // ==================== [C1] QUERY GELONDONGAN DENGAN INTERVAL TANGGAL ====================
        $newsCountsC1 = \DB::table('data_news')->whereBetween('created_at', [$minDate, $maxDate])->groupBy('subdomain')->pluck(\DB::raw('count(*)'), 'subdomain')->toArray();
        $imageCountsC1 = \DB::table('data_images')->whereBetween('created_at', [$minDate, $maxDate])->groupBy('subdomain')->pluck(\DB::raw('count(*)'), 'subdomain')->toArray();
        $pageCountsC1 = \DB::table('data_pages')->whereBetween('created_at', [$minDate, $maxDate])->groupBy('subdomain')->pluck(\DB::raw('count(*)'), 'subdomain')->toArray();
        $partsCountsC1 = \DB::table('data_parts')->whereBetween('created_at', [$minDate, $maxDate])->groupBy('subdomain')->pluck(\DB::raw('count(*)'), 'subdomain')->toArray();

        // ==================== [C2] QUERY GELONDONGAN TANPA BATASAN WAKTU ====================
        $newsCountsC2 = \DB::table('data_news')->groupBy('subdomain')->pluck(\DB::raw('count(*)'), 'subdomain')->toArray();
        $imageCountsC2 = \DB::table('data_images')->groupBy('subdomain')->pluck(\DB::raw('count(*)'), 'subdomain')->toArray();
        $pageCountsC2 = \DB::table('data_pages')->groupBy('subdomain')->pluck(\DB::raw('count(*)'), 'subdomain')->toArray();
        $partsCountsC2 = \DB::table('data_parts')->groupBy('subdomain')->pluck(\DB::raw('count(*)'), 'subdomain')->toArray();

        // ==================== [C3] QUERY MENCARI TANGGAL POST TERBARU SEBELUM HARI-H ====================
        $latestNews = \DB::table('data_news')->select('subdomain', \DB::raw('MAX(created_at) as max_date'))->where('created_at', '<=', $maxDate)->groupBy('subdomain')->pluck('max_date', 'subdomain')->toArray();
        $latestImage = \DB::table('data_images')->select('subdomain', \DB::raw('MAX(created_at) as max_date'))->where('created_at', '<=', $maxDate)->groupBy('subdomain')->pluck('max_date', 'subdomain')->toArray();
        $latestPage = \DB::table('data_pages')->select('subdomain', \DB::raw('MAX(created_at) as max_date'))->where('created_at', '<=', $maxDate)->groupBy('subdomain')->pluck('max_date', 'subdomain')->toArray();
        $latestPart = \DB::table('data_parts')->select('subdomain', \DB::raw('MAX(created_at) as max_date'))->where('created_at', '<=', $maxDate)->groupBy('subdomain')->pluck('max_date', 'subdomain')->toArray();

        // 3. Ambil daftar subdomain untuk dipetakan ke dalam Matriks Keputusan Awal
        $subdomains = \DB::table('subdomains')->get();
        $matrixKeputusan = [];

        foreach ($subdomains as $sub) {
            $subdomainName = $sub->subdomain;

            // --- PROSES HITUNG C1 (KONSISTENSI) ---
            $nilaiC1 = (($newsCountsC1[$subdomainName] ?? 0) + ($imageCountsC1[$subdomainName] ?? 0) + ($pageCountsC1[$subdomainName] ?? 0) + ($partsCountsC1[$subdomainName] ?? 0)) / 12;

            // --- PROSES HITUNG C2 (VOLUME) ---
            $nilaiC2 = ($newsCountsC2[$subdomainName] ?? 0) + ($imageCountsC2[$subdomainName] ?? 0) + ($pageCountsC2[$subdomainName] ?? 0) + ($partsCountsC2[$subdomainName] ?? 0);

            // --- PROSES HITUNG C3 (SISA INTERVAL HARI - SINKRON EXCEL) ---
            $dates = array_filter([
                $latestNews[$subdomainName] ?? null,
                $latestImage[$subdomainName] ?? null,
                $latestPage[$subdomainName] ?? null,
                $latestPart[$subdomainName] ?? null,
            ]);

            // Di Excel: IFERROR(..., 90) -> Jika kosong, selisih dianggap sama dengan jatah interval (skor akhir 0)
            $selisihHari = $intervalHari;

            if (!empty($dates)) {
                $lastPostStr = max($dates);
                $lastPostDate = \Carbon\Carbon::parse($lastPostStr);

                // Salin instance maxDate agar perbandingan diffInDays tidak memodifikasi objek asli
                $currentMaxDate = \Carbon\Carbon::parse($maxDateInput)->endOfDay();

                if ($lastPostDate->greaterThan($currentMaxDate)) {
                    // 1. Jika tanggal posting ada di masa depan melebihi tanggal input form
                    $selisihHari = $intervalHari;
                } else {
                    // Hitung selisih hari asli (pastikan argumen ke-2 bernilai false agar tipenya absolut aman)
                    $selisihHariAsli = (int) $currentMaxDate->diffInDays($lastPostDate, true);

                    if ($selisihHariAsli > $intervalHari) {
                        // 2. Jika postingannya sudah terlalu lama (kedaluwarsa melewati jatah interval)
                        $selisihHari = $intervalHari;
                    } else {
                        // 3. Jika masih masuk di dalam rentang waktu interval yang sah
                        $selisihHari = $selisihHariAsli;
                    }
                }
            }

            // Rumus Excel-mu: Interval - Selisih Hari
            $hitungSkor = $intervalHari - $selisihHari;

            // Rumus Excel-mu: MAX(0; hasil_pengurangan)
            $nilaiC3 = $hitungSkor < 0 ? 0 : $hitungSkor;

            // --- HITUNG C4 (VARIASI KONTEN DENGAN THRESHOLD) ---
            // 1. Cek apakah jumlah konten memenuhi batas minimal (threshold) masing-masing
            $hasNews = ($newsCountsC2[$subdomainName] ?? 0) > 10 ? 1 : 0;   // Harus > 10
            $hasImage = ($imageCountsC2[$subdomainName] ?? 0) >= 10 ? 1 : 0; // Harus minimal 10
            $hasPage = ($pageCountsC2[$subdomainName] ?? 0) >= 5 ? 1 : 0;  // Harus minimal 5
            $hasParts = ($partsCountsC2[$subdomainName] ?? 0) >= 1 ? 1 : 0;  // Minimal 1 aja oke

            // 2. Hitung total kategori yang berhasil lolos threshold
            $totalKategoriTerisi = $hasNews + $hasImage + $hasPage + $hasParts;

            // 3. Hitung nilai akhir C4 (skala 100)
            $nilaiC4 = $totalKategoriTerisi;

            $matrixKeputusan[] = [
                'subdomain' => $subdomainName,
                'c1' => round($nilaiC1, 2),
                'c2' => (int) $nilaiC2,
                'c3' => (int) $nilaiC3,
                'c4' => (int) $nilaiC4,
            ];
        }

        // 4. LOGIKA SAW: Cari nilai MAX untuk tiap kolom kriteria (C1 - C4)
        $maxC1 = max(array_column($matrixKeputusan, 'c1')) ?: 1;
        $maxC2 = max(array_column($matrixKeputusan, 'c2')) ?: 1;
        $maxC3 = max(array_column($matrixKeputusan, 'c3')) ?: 1;
        $maxC4 = max(array_column($matrixKeputusan, 'c4')) ?: 1;

        // 5. LOGIKA SAW: Hitung Matriks Ternormalisasi ($R$) & Perkalian Bobot ($V$)
        $matrixNormalisasi = [];
        $hasilRanking = [];

        foreach ($matrixKeputusan as $row) {
            $r1 = $row['c1'] / $maxC1;
            $r2 = $row['c2'] / $maxC2;
            $r3 = $row['c3'] / $maxC3;
            $r4 = $row['c4'] / $maxC4;

            $matrixNormalisasi[] = [
                'subdomain' => $row['subdomain'],
                'c1' => round($r1, 2),
                'c2' => round($r2, 2),
                'c3' => round($r3, 2),
                'c4' => round($r4, 2),
            ];
            // Hitung Nilai Preferensi (V) -> Mengalikan dengan Bobot desimal (dibagi 100)
            $skorAkhir = ($r1 * ($w1 / 100)) + ($r2 * ($w2 / 100)) + ($r3 * ($w3 / 100)) + ($r4 * ($w4 / 100));

            // Kita gunakan key 'skor' agar sinkron dengan yang dipanggil di view index.blade.php kemarin
            $hasilRanking[] = [
                'subdomain' => $row['subdomain'],
                'skor' => round($skorAkhir, 4),
                'detail_kriteria' => $row
            ];
        }

        // 6. Urutkan hasil ranking descending (Peringkat tertinggi ke terendah)
        // Sudah diperbaiki menggunakan key 'skor' secara konsisten
        usort($hasilRanking, function ($a, $b) {
            return $b['skor'] <=> $a['skor'];
        });

        return view('saw.index', compact(
            'maxDateInput',
            'periodRange',
            'currentCriteria',
            'matrixKeputusan',
            'matrixNormalisasi',
            'hasilRanking'
        ));
    }
}
