<?php

namespace App\Services;

use App\Models\Criterias;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SawService
{
    /**
     * Kode kriteria yang sudah memiliki rumus perhitungan nilai di rawValues().
     * Kriteria lain yang ditambahkan dari menu Kriteria akan bernilai 0.
     */
    public const SUPPORTED_CODES = ['C1', 'C2', 'C3', 'C4'];

    private const CONTENT_TABLES = ['data_news', 'data_images', 'data_pages', 'data_parts'];

    public function __construct(private int $intervalHari = 90)
    {
    }

    /**
     * Jalankan seluruh tahapan SAW sampai tanggal maksimal yang diberikan.
     */
    public function calculate(string $maxDateInput): array
    {
        $criterias = Criterias::with('weight')->orderBy('kode')->get();

        // Daftar kriteria aktif beserta bobot & tipenya (benefit / cost)
        $kriteria = $criterias->map(fn ($c) => [
            'kode' => $c->kode,
            'nama' => $c->nama,
            'tipe' => $c->tipe,
            'satuan' => $c->satuan,
            'bobot' => (float) ($c->weight->bobot ?? 0),
        ])->values()->all();

        $unsupported = $criterias->pluck('kode')->diff(self::SUPPORTED_CODES)->values()->all();
        $totalBobot = array_sum(array_column($kriteria, 'bobot'));

        // 1. Matriks Keputusan (X)
        $rawValues = $this->rawValues($maxDateInput);
        $matrixKeputusan = [];

        foreach ($rawValues as $subdomain => $values) {
            $nilai = [];
            foreach ($kriteria as $k) {
                $nilai[$k['kode']] = $values[$k['kode']] ?? 0;
            }
            $matrixKeputusan[] = ['subdomain' => $subdomain, 'nilai' => $nilai];
        }

        // 2. Cari nilai MAX (benefit) dan MIN (cost) tiap kolom kriteria
        $max = [];
        $min = [];
        foreach ($kriteria as $k) {
            $kolom = array_map(fn ($row) => $row['nilai'][$k['kode']], $matrixKeputusan);
            $max[$k['kode']] = $kolom ? max($kolom) : 0;
            $min[$k['kode']] = $kolom ? min($kolom) : 0;
        }

        // 3. Normalisasi (R) & Nilai Preferensi (V)
        $matrixNormalisasi = [];
        $hasilRanking = [];

        foreach ($matrixKeputusan as $row) {
            $r = [];
            $v = [];

            foreach ($kriteria as $k) {
                $kode = $k['kode'];
                $x = $row['nilai'][$kode];

                if ($k['tipe'] === 'cost') {
                    // Cost: nilai terkecil adalah yang terbaik -> min / x
                    $r[$kode] = $x == 0 ? 1 : $min[$kode] / $x;
                } else {
                    // Benefit: nilai terbesar adalah yang terbaik -> x / max
                    $r[$kode] = $x / ($max[$kode] ?: 1);
                }

                $v[$kode] = $r[$kode] * ($k['bobot'] / 100);
            }

            $matrixNormalisasi[] = [
                'subdomain' => $row['subdomain'],
                'nilai' => array_map(fn ($n) => round($n, 4), $r),
            ];

            $hasilRanking[] = [
                'subdomain' => $row['subdomain'],
                'nilai' => array_map(fn ($n) => round($n, 4), $v),
                'skor' => round(array_sum($v), 4),
            ];
        }

        // 4. Urutkan hasil ranking descending (Peringkat tertinggi ke terendah)
        usort($hasilRanking, fn ($a, $b) => $b['skor'] <=> $a['skor']);

        return compact('kriteria', 'unsupported', 'totalBobot', 'matrixKeputusan', 'matrixNormalisasi', 'hasilRanking');
    }

    /**
     * Hitung nilai riil C1 - C4 untuk setiap subdomain.
     *
     * @return array<string, array<string, int|float>>
     */
    private function rawValues(string $maxDateInput): array
    {
        $intervalHari = $this->intervalHari;
        $maxDate = Carbon::parse($maxDateInput)->endOfDay();
        $minDate = Carbon::parse($maxDateInput)->subDays($intervalHari)->startOfDay();

        $countsC1 = [];
        $countsC2 = [];
        $latest = [];

        foreach (self::CONTENT_TABLES as $table) {
            // [C1] Jumlah konten di dalam interval tanggal
            $countsC1[$table] = DB::table($table)->whereBetween('created_at', [$minDate, $maxDate])->groupBy('subdomain')->pluck(DB::raw('count(*)'), 'subdomain')->toArray();
            // [C2] Jumlah konten sampai tanggal maksimal
            $countsC2[$table] = DB::table($table)->where('created_at', '<=', $maxDate)->groupBy('subdomain')->pluck(DB::raw('count(*)'), 'subdomain')->toArray();
            // [C3] Tanggal posting terbaru sebelum tanggal maksimal
            $latest[$table] = DB::table($table)->select('subdomain', DB::raw('MAX(created_at) as max_date'))->where('created_at', '<=', $maxDate)->groupBy('subdomain')->pluck('max_date', 'subdomain')->toArray();
        }

        // Ambang batas minimal tiap jenis konten untuk C4
        $passThreshold = [
            'data_news' => fn ($n) => $n > 10,
            'data_images' => fn ($n) => $n >= 10,
            'data_pages' => fn ($n) => $n >= 5,
            'data_parts' => fn ($n) => $n >= 1,
        ];

        $result = [];

        foreach (DB::table('subdomains')->pluck('subdomain') as $subdomain) {
            $totalC1 = 0;
            $totalC2 = 0;
            $dates = [];
            $kategoriTerisi = 0;

            foreach (self::CONTENT_TABLES as $table) {
                $totalC1 += $countsC1[$table][$subdomain] ?? 0;
                $totalC2 += $countsC2[$table][$subdomain] ?? 0;
                $dates[] = $latest[$table][$subdomain] ?? null;
                $kategoriTerisi += $passThreshold[$table]($countsC2[$table][$subdomain] ?? 0) ? 1 : 0;
            }

            // --- C1 (KONSISTENSI): rata-rata post per minggu dalam 12 minggu ---
            $nilaiC1 = $totalC1 / 12;

            // --- C3 (FRESHNESS): interval - selisih hari posting terakhir ---
            // Jika tidak ada posting / di masa depan / kedaluwarsa, selisih = interval (skor 0)
            $selisihHari = $intervalHari;
            $dates = array_filter($dates);

            if (!empty($dates)) {
                $lastPostDate = Carbon::parse(max($dates));

                if (!$lastPostDate->greaterThan($maxDate)) {
                    $selisihHari = min((int) $maxDate->diffInDays($lastPostDate, true), $intervalHari);
                }
            }

            $nilaiC3 = max(0, $intervalHari - $selisihHari);

            $result[$subdomain] = [
                'C1' => round($nilaiC1, 4),
                'C2' => (int) $totalC2,
                'C3' => (int) $nilaiC3,
                'C4' => $kategoriTerisi,
            ];
        }

        return $result;
    }
}
