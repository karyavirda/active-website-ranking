<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-5 gap-6">

                <div
                    class="bg-white p-4 shadow sm:rounded-lg border-l-4 border-blue-500 flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Subdomain</p>
                        <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $totalDomains }}</h3>
                    </div>
                </div>

                <div
                    class="bg-white p-4 shadow sm:rounded-lg border-l-4 border-green-500 flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Data Berita</p>
                        <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $totalNews }}</h3>
                    </div>
                </div>

                <div
                    class="bg-white p-4 shadow sm:rounded-lg border-l-4 border-purple-500 flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Data Parts</p>
                        <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $totalParts }}</h3>
                    </div>
                </div>

                <div
                    class="bg-white p-4 shadow sm:rounded-lg border-l-4 border-yellow-500 flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Data Pages</p>
                        <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $totalPages }}</h3>
                    </div>
                </div>

                <div
                    class="bg-white p-4 shadow sm:rounded-lg border-l-4 border-pink-500 flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Data Images</p>
                        <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $totalImages }}</h3>
                    </div>
                </div>

            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="bg-white p-6 shadow sm:rounded-lg lg:col-span-1">
                    <h3 class="font-bold text-lg text-gray-800 mb-4 flex items-center gap-2">
                        ⚙️ Bobot Parameter SAW
                    </h3>
                    @if($currentCriteria)
                        <div class="space-y-4">
                            <div class="flex justify-between items-center border-b pb-2">
                                <div>
                                    <span class="block text-gray-800 font-semibold text-sm">Konsistensi (C1)</span>
                                    <span class="text-xs text-gray-400">Rata-rata post/minggu</span>
                                </div>
                                <span class="font-bold text-blue-600 text-lg">{{ $currentCriteria->c1 }}%</span>
                            </div>

                            <div class="flex justify-between items-center border-b pb-2">
                                <div>
                                    <span class="block text-gray-800 font-semibold text-sm">Volume Konten (C2)</span>
                                    <span class="text-xs text-gray-400">Total akumulasi item</span>
                                </div>
                                <span class="font-bold text-blue-600 text-lg">{{ $currentCriteria->c2 }}%</span>
                            </div>

                            <div class="flex justify-between items-center border-b pb-2">
                                <div>
                                    <span class="block text-gray-800 font-semibold text-sm">Freshness (C3)</span>
                                    <span class="text-xs text-gray-400">Skor kesegaran data (0 - 90)</span>
                                </div>
                                <span class="font-bold text-blue-600 text-lg">{{ $currentCriteria->c3 }}%</span>
                            </div>

                            <div class="flex justify-between items-center border-b pb-2">
                                <div>
                                    <span class="block text-gray-800 font-semibold text-sm">Variasi Konten (C4)</span>
                                    <span class="text-xs text-gray-400">Keberagaman jenis konten</span>
                                </div>
                                <span class="font-bold text-blue-600 text-lg">{{ $currentCriteria->c4 }}%</span>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-red-500 italic">Bobot kriteria belum diatur di sistem.</p>
                    @endif
                </div>

                <div class="bg-white p-6 shadow sm:rounded-lg lg:col-span-2">
                    <h3 class="font-bold text-lg text-gray-800 mb-4 flex items-center gap-2">
                        🏆 Top 5 Subdomain Terbaik (SAW)
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-600">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-center w-24">Peringkat</th>
                                    <th class="px-4 py-3">Nama Subdomain</th>
                                    <th class="px-4 py-3 text-center w-32">Skor Akhir</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($top5Rankings as $index => $rank)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-3 text-center font-bold text-gray-800">
                                            @if($index == 0) 🥇
                                            @elseif($index == 1) 🥈
                                            @elseif($index == 2) 🥉
                                            @else #{{ $index + 1 }}
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 font-medium text-gray-900">
                                            {{ $rank['subdomain'] }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span
                                                class="inline-block bg-green-50 text-green-700 font-semibold px-2.5 py-1 rounded-full text-xs">
                                                {{ number_format($rank['skor'], 4) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-xs text-gray-400 italic">Belum ada data
                                            subdomain teranalisis.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <p class="text-xs text-gray-400 mt-4 italic">*Data ranking 5 besar di atas dihitung otomatis
                        menggunakan rentang evaluasi default 90 hari terakhir.</p>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>