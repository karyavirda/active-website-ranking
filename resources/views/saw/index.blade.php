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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block font-medium text-gray-700 mb-1">
                                <span class="block text-gray-800 font-semibold text-sm">Tanggal Maksimal</span>
                                <span class="text-xs text-gray-400">Batasan akhir penarikan data (Tanggal J4)</span>
                            </label>
                            <input type="date" name="max_date" value="{{ $maxDateInput ?? date('Y-m-d') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                required>
                        </div>

                        <div>
                            <label class="block font-medium text-gray-700 mb-1">
                                <span class="block text-gray-800 font-semibold text-sm">Rentang Analisis Data</span>
                                <span class="text-xs text-gray-400">Periode mundur analisis data</span>
                            </label>
                            <select name="period_range" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                                <option value="7_days" {{ ($periodRange ?? '') == '7_days' ? 'selected' : '' }}>7 Hari Terakhir</option>
                                <option value="30_days" {{ ($periodRange ?? '') == '30_days' ? 'selected' : '' }}>30 Hari Terakhir</option>
                                <option value="3_months" {{ ($periodRange ?? '3_months') == '3_months' ? 'selected' : '' }}>3 Bulan Terakhir (Rekomendasi C1)</option>
                                <option value="6_months" {{ ($periodRange ?? '') == '6_months' ? 'selected' : '' }}>6 Bulan Terakhir</option>
                                <option value="1_year" {{ ($periodRange ?? '') == '1_year' ? 'selected' : '' }}>1 Tahun Terakhir</option>
                            </select>
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
                
                <div class="bg-white p-6 shadow sm:rounded-lg border border-gray-100">
                    <div class="mb-4 flex flex-wrap gap-2 items-center justify-between text-xs text-gray-500 bg-gray-50 p-3 rounded-md">
                        <div>
                            📅 Tanggal Maksimal: <span class="font-bold text-gray-700">{{ $maxDateInput }}</span>
                        </div>
                        <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded font-semibold uppercase">Step 1: Matriks Keputusan</span>
                    </div>

                    <h3 class="text-base font-bold text-gray-800 mb-1">Tabel 1: Matriks Keputusan Awal ($X$)</h3>

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
                                @foreach($matrixKeputusan as $row)
                                    <tr class="hover:bg-gray-50/80 transition">
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ $row['subdomain'] }}</td>
                                        <td class="px-4 py-3 text-center font-semibold text-blue-600 bg-blue-50/20">{{ number_format($row['c1'], 2) }}</td>
                                        <td class="px-4 py-3 text-center font-medium text-gray-800">{{ number_format($row['c2'], 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-center">{{ $row['c3'] }}</td>
                                        <td class="px-4 py-3 text-center">{{ $row['c4'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white p-6 shadow sm:rounded-lg border border-gray-100">
                    <div class="mb-4 flex justify-between items-center text-xs text-gray-500 bg-gray-50 p-3 rounded-md">
                        <span class="font-medium text-gray-600">💡 Rumus Normalisasi: Nilai Sel / Nilai Maksimal Kolom (Kriteria Benefit)</span>
                        <span class="bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded font-semibold uppercase">Step 2: Normalisasi Matriks</span>
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
                                @foreach($matrixNormalisasi as $row)
                                    <tr class="hover:bg-gray-50/80 transition">
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ $row['subdomain'] }}</td>
                                        <td class="px-4 py-3 text-center font-medium text-gray-800">{{ number_format($row['c1'], 2) }}</td>
                                        <td class="px-4 py-3 text-center font-medium text-gray-800">{{ number_format($row['c2'], 2) }}</td>
                                        <td class="px-4 py-3 text-center font-medium text-gray-800">{{ number_format($row['c3'], 2) }}</td>
                                        <td class="px-4 py-3 text-center font-medium text-gray-800">{{ number_format($row['c4'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white p-6 shadow sm:rounded-lg border-2 border-green-500/20">
                    <div class="mb-4 flex justify-between items-center text-xs text-gray-500 bg-green-50/50 p-3 rounded-md">
                        <span class="font-medium text-green-700">👑 Hasil Akhir: Nilai Preferensi ($V$) Berdasarkan Bobot Kriteria Aktif</span>
                        <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded font-semibold uppercase">Step 3: Hasil Perankingan</span>
                    </div>

                    <h3 class="text-base font-bold text-gray-800 mb-1">Tabel 3: Hasil Rekomendasi Peringkat</h3>
                    <p class="text-xs text-gray-400 mb-4">Urutan peringkat subdomain sekolah terbaik hasil perkalian matriks ternormalisasi dengan bobot parameter.</p>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-600">
                            <thead class="text-xs text-gray-700 uppercase bg-green-50/30 border-b">
                                <tr>
                                    <th class="px-6 py-3 text-center w-24">Peringkat</th>
                                    <th class="px-4 py-3">Nama Subdomain</th>
                                    <th class="px-6 py-3 text-center w-40">Nilai Preferensi ($V_i$)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($hasilRanking as $index => $rank)
                                    <tr class="{{ $index == 0 ? 'bg-yellow-50/50 hover:bg-yellow-50 font-semibold' : 'hover:bg-gray-50/80' }} transition">
                                        <td class="px-6 py-3 text-center">
                                            @if($index == 0)
                                                <span class="inline-block bg-yellow-100 text-yellow-800 text-xs font-bold px-2 py-1 rounded">🥇 ke-1</span>
                                            @elseif($index == 1)
                                                <span class="inline-block bg-slate-100 text-slate-800 text-xs font-bold px-2 py-1 rounded">🥈 ke-2</span>
                                            @elseif($index == 2)
                                                <span class="inline-block bg-amber-100 text-amber-800 text-xs font-bold px-2 py-1 rounded">🥉 ke-3</span>
                                            @else
                                                <span class="text-gray-500 text-xs">Ke-{{ $index + 1 }}</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-gray-900">{{ $rank['subdomain'] }}</td>
                                        <td class="px-6 py-3 text-center">
                                            <span class="inline-block {{ $index == 0 ? 'bg-green-100 text-green-800' : 'bg-blue-50 text-blue-700' }} font-bold px-2.5 py-1 rounded-full text-xs">
                                                {{ number_format($rank['skor'], 4) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>