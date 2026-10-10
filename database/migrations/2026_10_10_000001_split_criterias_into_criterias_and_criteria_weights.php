<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Kriteria bawaan yang sudah memiliki rumus perhitungan di SawService.
     */
    private array $defaultCriterias = [
        [
            'kode' => 'C1',
            'nama' => 'Konsistensi',
            'satuan' => 'Rata-rata post/minggu',
            'deskripsi' => 'Mengukur kontinuitas admin dalam mempublikasikan konten riil. Dihitung dari rata-rata jumlah unggahan (berita, gambar, halaman, modul) setiap minggunya dalam kurun waktu 3 bulan terakhir.',
        ],
        [
            'kode' => 'C2',
            'nama' => 'Volume Konten',
            'satuan' => 'Total akumulasi item',
            'deskripsi' => 'Mengukur total akumulasi aset digital yang dimiliki website. Menghitung seluruh jumlah data (berita + foto + halaman + modul) sampai tanggal maksimal analisis.',
        ],
        [
            'kode' => 'C3',
            'nama' => 'Freshness',
            'satuan' => 'Skor kesegaran data (0 - 90)',
            'deskripsi' => 'Mengukur tingkat kebaruan informasi. Dihitung dari 90 dikurangi selisih hari antara tanggal maksimal analisis dengan tanggal konten terakhir yang diunggah.',
        ],
        [
            'kode' => 'C4',
            'nama' => 'Variasi Konten',
            'satuan' => 'Jumlah jenis konten (0 - 4)',
            'deskripsi' => 'Mengukur keberagaman tipe informasi (Berita, Galeri, Halaman, Modul) dengan ambang batas minimal jumlah konten per jenis.',
        ],
    ];

    public function up(): void
    {
        // Simpan bobot lama (format kolom c1..c4) sebelum tabel dibentuk ulang
        $oldWeights = Schema::hasColumn('criterias', 'c1') ? DB::table('criterias')->first() : null;

        Schema::dropIfExists('criterias');

        // InnoDB agar foreign key & cascade on delete benar-benar berjalan
        Schema::create('criterias', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->id();
            $table->string('kode', 10)->unique();
            $table->string('nama');
            $table->enum('tipe', ['benefit', 'cost'])->default('benefit');
            $table->string('satuan')->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        Schema::create('criteria_weights', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->id();
            $table->foreignId('criteria_id')->unique()->constrained('criterias')->cascadeOnDelete();
            $table->float('bobot')->default(0);
            $table->timestamps();
        });

        $now = now();

        foreach ($this->defaultCriterias as $criteria) {
            $criteriaId = DB::table('criterias')->insertGetId($criteria + [
                'tipe' => 'benefit',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $column = strtolower($criteria['kode']);

            DB::table('criteria_weights')->insert([
                'criteria_id' => $criteriaId,
                'bobot' => $oldWeights->{$column} ?? 25,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        $weights = DB::table('criteria_weights')
            ->join('criterias', 'criterias.id', '=', 'criteria_weights.criteria_id')
            ->pluck('criteria_weights.bobot', 'criterias.kode');

        Schema::dropIfExists('criteria_weights');
        Schema::dropIfExists('criterias');

        Schema::create('criterias', function (Blueprint $table) {
            $table->id();
            $table->float('c1');
            $table->float('c2');
            $table->float('c3');
            $table->float('c4');
            $table->float('c5');
            $table->timestamps();
        });

        if ($weights->isNotEmpty()) {
            DB::table('criterias')->insert([
                'c1' => $weights['C1'] ?? 0,
                'c2' => $weights['C2'] ?? 0,
                'c3' => $weights['C3'] ?? 0,
                'c4' => $weights['C4'] ?? 0,
                'c5' => $weights['C5'] ?? 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};
