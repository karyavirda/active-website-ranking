<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Proses Hitung Algoritma SAW') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <div class="bg-white p-6 shadow sm:rounded-lg border border-gray-100">
                <div class="mb-6 pb-4 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800">Konfigurasi Parameter Perhitungan</h3>
                    <p class="text-xs text-gray-400 mt-1">Tentukan batasan tanggal dan rentang waktu data operasional subdomain yang akan dinormalisasi dan dihitung skor akhirnya.</p>
                </div>

                <form action="{{ route('saw.process') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
                        <div>
                            <label class="block font-medium text-gray-700 mb-1">
                                <span class="block text-gray-800 font-semibold text-sm">Tanggal Maksimal</span>
                                <span class="text-xs text-gray-400">Batasan akhir penarikan data (Tanggal J4)</span>
                            </label>
                            <input type="date" name="max_date" value="{{ $maxDateInput ?? date('Y-m-d') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                required>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg text-sm font-semibold shadow-sm transition">
                            Mulai Analisis &rarr;
                        </button>
                    </div>
                </form>
            </div>

            @if(isset($matrixKeputusan))
                @php
                    // Sertakan nomor peringkat asli agar tetap benar saat data dicari / dipaginasi
                    $rankingRows = collect($hasilRanking)->values()->map(fn ($rank, $i) => [
                        'rank' => $i + 1,
                        'subdomain' => $rank['subdomain'],
                        'v1' => $rank['v1'],
                        'v2' => $rank['v2'],
                        'v3' => $rank['v3'],
                        'v4' => $rank['v4'],
                        'skor' => $rank['skor'],
                    ]);
                @endphp

                <script>
                    function sawTabs(data) {
                        return {
                            data: data,
                            tab: 'keputusan',
                            search: '',
                            perPage: 10,
                            page: { keputusan: 1, normalisasi: 1, ranking: 1 },

                            filtered(key) {
                                const q = this.search.trim().toLowerCase();
                                if (!q) return this.data[key];
                                return this.data[key].filter(row => String(row.subdomain).toLowerCase().includes(q));
                            },
                            totalPages(key) {
                                return Math.max(1, Math.ceil(this.filtered(key).length / this.perPage));
                            },
                            rows(key) {
                                const start = (this.page[key] - 1) * this.perPage;
                                return this.filtered(key).slice(start, start + this.perPage);
                            },
                            goTo(key, p) {
                                if (p < 1 || p > this.totalPages(key)) return;
                                this.page[key] = p;
                            },
                            resetPages() {
                                this.page = { keputusan: 1, normalisasi: 1, ranking: 1 };
                            },
                            info(key) {
                                const total = this.filtered(key).length;
                                if (total === 0) return 'Tidak ada data';
                                const start = (this.page[key] - 1) * this.perPage + 1;
                                const end = Math.min(start + this.perPage - 1, total);
                                return `Menampilkan ${start}–${end} dari ${total} data`;
                            },
                            // Nomor halaman dengan "..." supaya tidak terlalu panjang
                            pageNumbers(key) {
                                const total = this.totalPages(key);
                                const current = this.page[key];
                                if (total <= 7) return Array.from({ length: total }, (_, i) => i + 1);

                                const pages = [1];
                                const from = Math.max(2, current - 1);
                                const to = Math.min(total - 1, current + 1);
                                if (from > 2) pages.push('...');
                                for (let i = from; i <= to; i++) pages.push(i);
                                if (to < total - 1) pages.push('...');
                                pages.push(total);
                                return pages;
                            },
                            fmt(value, digits = 4) {
                                return Number(value).toFixed(digits);
                            },
                            fmtInt(value) {
                                return Number(value).toLocaleString('id-ID');
                            },
                        };
                    }
                </script>

                <div class="bg-white shadow sm:rounded-lg border border-gray-100"
                    x-data="sawTabs({
                        keputusan: @js(array_values($matrixKeputusan)),
                        normalisasi: @js(array_values($matrixNormalisasi)),
                        ranking: @js($rankingRows),
                    })">

                    {{-- Navigasi Tab --}}
                    <div class="border-b border-gray-200 px-6 pt-4">
                        <nav class="flex flex-wrap gap-2 -mb-px">
                            <button type="button" @click="tab = 'keputusan'"
                                :class="tab === 'keputusan' ? 'border-blue-600 text-blue-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="px-4 py-3 border-b-2 text-sm font-semibold transition">
                                Step 1: Matriks Keputusan
                            </button>
                            <button type="button" @click="tab = 'normalisasi'"
                                :class="tab === 'normalisasi' ? 'border-indigo-600 text-indigo-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="px-4 py-3 border-b-2 text-sm font-semibold transition">
                                Step 2: Normalisasi
                            </button>
                            <button type="button" @click="tab = 'ranking'"
                                :class="tab === 'ranking' ? 'border-green-600 text-green-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="px-4 py-3 border-b-2 text-sm font-semibold transition">
                                Step 3: Hasil Perankingan
                            </button>
                        </nav>
                    </div>

                    <div class="p-6">
                        {{-- Toolbar: info tanggal, pencarian, jumlah baris --}}
                        <div class="mb-4 flex flex-wrap gap-3 items-center justify-between text-xs text-gray-500 bg-gray-50 p-3 rounded-md">
                            <div>
                                📅 Tanggal Maksimal: <span class="font-bold text-gray-700">{{ $maxDateInput }}</span>
                            </div>
                            <div class="flex flex-wrap items-center gap-3">
                                <input type="text" x-model="search" @input="resetPages()" placeholder="Cari subdomain..."
                                    class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-xs py-1.5 w-48">
                                <label class="flex items-center gap-2">
                                    Tampilkan
                                    <select x-model.number="perPage" @change="resetPages()"
                                        class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-xs py-1.5 pr-8">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                    baris
                                </label>
                            </div>
                        </div>

                        {{-- TAB 1: Matriks Keputusan --}}
                        <div x-show="tab === 'keputusan'">
                            <h3 class="text-base font-bold text-gray-800 mb-4">Tabel 1: Matriks Keputusan Awal ($X$)</h3>

                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left text-gray-600">
                                    <thead class="text-xs text-gray-700 uppercase bg-slate-50 border-b">
                                        <tr>
                                            <th class="px-4 py-3">Nama Subdomain</th>
                                            <th class="px-4 py-3 text-center bg-blue-50/50 text-blue-700">C1 (Konsistensi)</th>
                                            <th class="px-4 py-3 text-center">C2 (Volume)</th>
                                            <th class="px-4 py-3 text-center">C3 (Freshness)</th>
                                            <th class="px-4 py-3 text-center">C4 (Variasi)</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <template x-for="row in rows('keputusan')" :key="row.subdomain">
                                            <tr class="hover:bg-gray-50/80 transition">
                                                <td class="px-4 py-3 font-medium text-gray-900" x-text="row.subdomain"></td>
                                                <td class="px-4 py-3 text-center font-semibold text-blue-600 bg-blue-50/20" x-text="fmt(row.c1)"></td>
                                                <td class="px-4 py-3 text-center font-medium text-gray-800" x-text="fmtInt(row.c2)"></td>
                                                <td class="px-4 py-3 text-center" x-text="row.c3"></td>
                                                <td class="px-4 py-3 text-center" x-text="row.c4"></td>
                                            </tr>
                                        </template>
                                        <tr x-show="filtered('keputusan').length === 0">
                                            <td colspan="5" class="px-4 py-6 text-center text-gray-400">Data tidak ditemukan.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- TAB 2: Normalisasi --}}
                        <div x-show="tab === 'normalisasi'" x-cloak>
                            <div class="mb-3 text-xs font-medium text-gray-600 bg-indigo-50/50 p-3 rounded-md">
                                💡 Rumus Normalisasi: Nilai Sel / Nilai Maksimal Kolom (Kriteria Benefit)
                            </div>
                            <h3 class="text-base font-bold text-gray-800 mb-1">Tabel 2: Matriks Ternormalisasi ($R$)</h3>
                            <p class="text-xs text-gray-400 mb-4">Hasil transformasi nilai riil menjadi skala indeks antara 0 sampai 1 berdasarkan nilai pencapaian tertinggi.</p>

                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left text-gray-600">
                                    <thead class="text-xs text-gray-700 uppercase bg-slate-50 border-b">
                                        <tr>
                                            <th class="px-4 py-3">Nama Subdomain</th>
                                            <th class="px-4 py-3 text-center">C1 (Konsistensi)</th>
                                            <th class="px-4 py-3 text-center">C2 (Volume)</th>
                                            <th class="px-4 py-3 text-center">C3 (Freshness)</th>
                                            <th class="px-4 py-3 text-center">C4 (Variasi)</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <template x-for="row in rows('normalisasi')" :key="row.subdomain">
                                            <tr class="hover:bg-gray-50/80 transition">
                                                <td class="px-4 py-3 font-medium text-gray-900" x-text="row.subdomain"></td>
                                                <td class="px-4 py-3 text-center font-medium text-gray-800" x-text="fmt(row.c1)"></td>
                                                <td class="px-4 py-3 text-center font-medium text-gray-800" x-text="fmt(row.c2)"></td>
                                                <td class="px-4 py-3 text-center font-medium text-gray-800" x-text="fmt(row.c3)"></td>
                                                <td class="px-4 py-3 text-center font-medium text-gray-800" x-text="fmt(row.c4)"></td>
                                            </tr>
                                        </template>
                                        <tr x-show="filtered('normalisasi').length === 0">
                                            <td colspan="5" class="px-4 py-6 text-center text-gray-400">Data tidak ditemukan.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- TAB 3: Hasil Perankingan --}}
                        <div x-show="tab === 'ranking'" x-cloak>
                            <div class="mb-3 text-xs font-medium text-green-700 bg-green-50/50 p-3 rounded-md">
                                👑 Hasil Akhir: Nilai Preferensi ($V$) Berdasarkan Bobot Kriteria Aktif
                            </div>
                            <h3 class="text-base font-bold text-gray-800 mb-1">Tabel 3: Hasil Rekomendasi Peringkat</h3>
                            <p class="text-xs text-gray-400 mb-4">Urutan peringkat subdomain sekolah terbaik hasil perkalian matriks ternormalisasi dengan bobot parameter.</p>

                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left text-gray-600">
                                    <thead class="text-xs text-gray-700 uppercase bg-green-50/30 border-b">
                                        <tr>
                                            <th class="px-4 py-3 text-center w-20">Peringkat</th>
                                            <th class="px-4 py-3">Nama Subdomain</th>
                                            <th class="px-3 py-3 text-center bg-slate-50/50">C1 ({{ $currentCriteria->c1 }}%)</th>
                                            <th class="px-3 py-3 text-center bg-slate-50/50">C2 ({{ $currentCriteria->c2 }}%)</th>
                                            <th class="px-3 py-3 text-center bg-slate-50/50">C3 ({{ $currentCriteria->c3 }}%)</th>
                                            <th class="px-3 py-3 text-center bg-slate-50/50">C4 ({{ $currentCriteria->c4 }}%)</th>
                                            <th class="px-6 py-3 text-center w-40 font-bold bg-green-50/50 text-green-800">Total Skor ($V_i$)</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <template x-for="row in rows('ranking')" :key="row.subdomain">
                                            <tr :class="row.rank === 1 ? 'bg-yellow-50/50 hover:bg-yellow-50 font-semibold' : 'hover:bg-gray-50/80'" class="transition">
                                                <td class="px-4 py-3 text-center">
                                                    <template x-if="row.rank === 1">
                                                        <span class="inline-block bg-yellow-100 text-yellow-800 text-xs font-bold px-2 py-1 rounded">🥇 ke-1</span>
                                                    </template>
                                                    <template x-if="row.rank === 2">
                                                        <span class="inline-block bg-slate-100 text-slate-800 text-xs font-bold px-2 py-1 rounded">🥈 ke-2</span>
                                                    </template>
                                                    <template x-if="row.rank === 3">
                                                        <span class="inline-block bg-amber-100 text-amber-800 text-xs font-bold px-2 py-1 rounded">🥉 ke-3</span>
                                                    </template>
                                                    <template x-if="row.rank > 3">
                                                        <span class="text-gray-500 text-xs font-medium" x-text="'Ke-' + row.rank"></span>
                                                    </template>
                                                </td>
                                                <td class="px-4 py-3 text-gray-900 font-medium" x-text="row.subdomain"></td>
                                                <td class="px-3 py-3 text-center text-xs text-gray-500 bg-slate-50/20" x-text="fmt(row.v1)"></td>
                                                <td class="px-3 py-3 text-center text-xs text-gray-500 bg-slate-50/20" x-text="fmt(row.v2)"></td>
                                                <td class="px-3 py-3 text-center text-xs text-gray-500 bg-slate-50/20" x-text="fmt(row.v3)"></td>
                                                <td class="px-3 py-3 text-center text-xs text-gray-500 bg-slate-50/20" x-text="fmt(row.v4)"></td>
                                                <td class="px-6 py-3 text-center bg-green-50/20">
                                                    <span :class="row.rank === 1 ? 'bg-green-200 text-green-900' : 'bg-green-100 text-green-800'"
                                                        class="inline-block font-bold px-3 py-1 rounded-full text-xs" x-text="fmt(row.skor)"></span>
                                                </td>
                                            </tr>
                                        </template>
                                        <tr x-show="filtered('ranking').length === 0">
                                            <td colspan="7" class="px-4 py-6 text-center text-gray-400">Data tidak ditemukan.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Pagination (mengikuti tab yang aktif) --}}
                        <div class="mt-4 pt-4 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3">
                            <p class="text-xs text-gray-500" x-text="info(tab)"></p>

                            <nav class="flex items-center gap-1" x-show="totalPages(tab) > 1">
                                <button type="button" @click="goTo(tab, page[tab] - 1)" :disabled="page[tab] === 1"
                                    class="px-3 py-1.5 text-xs rounded-md border border-gray-300 text-gray-600 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed">
                                    &laquo; Prev
                                </button>

                                <template x-for="(p, i) in pageNumbers(tab)" :key="tab + '-' + i">
                                    <button type="button" @click="p !== '...' && goTo(tab, p)" :disabled="p === '...'"
                                        :class="p === page[tab]
                                            ? 'bg-blue-600 border-blue-600 text-white'
                                            : (p === '...' ? 'border-transparent text-gray-400 cursor-default' : 'border-gray-300 text-gray-600 hover:bg-gray-50')"
                                        class="min-w-[2rem] px-2 py-1.5 text-xs rounded-md border" x-text="p">
                                    </button>
                                </template>

                                <button type="button" @click="goTo(tab, page[tab] + 1)" :disabled="page[tab] === totalPages(tab)"
                                    class="px-3 py-1.5 text-xs rounded-md border border-gray-300 text-gray-600 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed">
                                    Next &raquo;
                                </button>
                            </nav>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
