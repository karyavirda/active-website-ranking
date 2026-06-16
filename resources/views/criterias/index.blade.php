<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Pengaturan Bobot Kriteria</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if(session('error'))
                <div class="bg-red-500 text-white p-4 rounded mb-4">{{ session('error') }}</div>
            @endif
            @if(session('success'))
                <div class="bg-green-500 text-white p-4 rounded mb-4">{{ session('success') }}</div>
            @endif

            <div class="bg-white p-6 shadow sm:rounded-lg">
                <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-500 text-blue-700">
                    <h3 class="font-bold">Panduan Pengisian Bobot:</h3>
                    <ul class="list-disc ml-5 mt-2 text-sm">
                        <li>Pastikan total seluruh bobot (C1 + C2 + C3 + C4 + C5) berjumlah tepat <b>100</b>.</li>
                        <li>Nilai yang diinput berupa angka bulat atau desimal (contoh: 20 atau 20.5).</li>
                        <li>Bobot ini digunakan sebagai parameter utama dalam perhitungan sistem registrasi sekolah.
                        </li>
                    </ul>
                </div>

                <form action="{{ route('criterias.store') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div>
                            <label class="block font-medium text-gray-700 mb-1">
                                <span class="block text-gray-800 font-semibold text-sm">Konsistensi (C1)</span>
                                <span class="text-xs text-gray-400">Rata-rata post/minggu</span>
                            </label>
                            <input type="number" name="c1" value="{{ $criteria->c1 ?? '' }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required step="0.01" placeholder="Contoh: 20">
                        </div>

                        <div>
                            <label class="block font-medium text-gray-700 mb-1">
                                <span class="block text-gray-800 font-semibold text-sm">Volume Konten (C2)</span>
                                <span class="text-xs text-gray-400">Total akumulasi item</span>
                            </label>
                            <input type="number" name="c2" value="{{ $criteria->c2 ?? '' }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required step="0.01" placeholder="Contoh: 20">
                        </div>

                        <div>
                            <label class="block font-medium text-gray-700 mb-1">
                                <span class="block text-gray-800 font-semibold text-sm">Freshness (C3)</span>
                                <span class="text-xs text-gray-400">Skor kesegaran data (0 - 90)</span>
                            </label>
                            <input type="number" name="c3" value="{{ $criteria->c3 ?? '' }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required step="0.01" placeholder="Contoh: 20">
                        </div>

                        <div>
                            <label class="block font-medium text-gray-700 mb-1">
                                <span class="block text-gray-800 font-semibold text-sm">Variasi Konten (C4)</span>
                                <span class="text-xs text-gray-400">Keberagaman jenis konten</span>
                            </label>
                            <input type="number" name="c4" value="{{ $criteria->c4 ?? '' }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required step="0.01" placeholder="Contoh: 20">
                        </div>

                        <div>
                            <label class="block font-medium text-gray-700 mb-1">
                                <span class="block text-gray-800 font-semibold text-sm">Admin Activity (C5)</span>
                                <span class="text-xs text-gray-400">Intensitas log aktivitas</span>
                            </label>
                            <input type="number" name="c5" value="{{ $criteria->c5 ?? '' }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required step="0.01" placeholder="Contoh: 20">
                        </div>

                    </div>

                    <div class="mt-6 flex items-center gap-4">
                        <button type="submit"
                            class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
                            Simpan Bobot
                        </button>
                        <span id="total-text" class="text-gray-600 font-semibold">Total saat ini: 0</span>
                    </div>
                </form>
            </div>

            <script>
                const inputs = document.querySelectorAll('input[type="number"]');
                const totalText = document.getElementById('total-text');

                function calculateTotal() {
                    let total = 0;
                    inputs.forEach(input => {
                        total += parseFloat(input.value) || 0;
                    });
                    totalText.innerText = 'Total saat ini: ' + total;
                    totalText.style.color = (total === 100) ? 'green' : 'red';
                }

                inputs.forEach(input => input.addEventListener('input', calculateTotal));
                document.addEventListener('DOMContentLoaded', calculateTotal);
            </script>
        </div>
    </div>
</x-app-layout>