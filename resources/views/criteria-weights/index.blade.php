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
            @if ($errors->any())
                <div class="bg-red-500 text-white p-4 rounded mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white p-6 shadow sm:rounded-lg">
                <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-500 text-blue-700">
                    <h3 class="font-bold">Panduan Pengisian Bobot:</h3>
                    <ul class="list-disc ml-5 mt-2 text-sm">
                        <li>Pastikan total seluruh bobot kriteria berjumlah tepat <b>100</b>.</li>
                        <li>Nilai yang diinput berupa angka bulat atau desimal (contoh: 20 atau 20.5).</li>
                        <li>Untuk menambah, mengubah tipe, atau menghapus kriteria, buka menu
                            <a href="{{ route('criterias.index') }}" class="underline font-semibold">Kriteria</a>.</li>
                    </ul>
                </div>

                @if($criterias->isEmpty())
                    <p class="text-sm text-gray-500 italic">
                        Belum ada kriteria. Silakan <a href="{{ route('criterias.create') }}" class="text-blue-600 underline">tambah kriteria</a> terlebih dahulu.
                    </p>
                @else
                    <form action="{{ route('criteria-weights.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($criterias as $criteria)
                                <div>
                                    <label class="block font-medium text-gray-700 mb-1">
                                        <span class="flex items-center gap-2 text-gray-800 font-semibold text-sm">
                                            {{ $criteria->nama }} ({{ $criteria->kode }})
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold uppercase {{ $criteria->tipe === 'cost' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                                {{ $criteria->tipe }}
                                            </span>
                                        </span>
                                        <span class="text-xs text-gray-400">{{ $criteria->satuan }}</span>
                                    </label>
                                    <input type="number" name="bobot[{{ $criteria->id }}]"
                                        value="{{ old('bobot.' . $criteria->id, $criteria->weight->bobot ?? 0) }}"
                                        class="weight-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        required min="0" max="100" step="0.01" placeholder="Contoh: 20">
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6 flex items-center gap-4">
                            <button type="submit"
                                class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
                                Simpan Bobot
                            </button>
                            <span id="total-text" class="text-gray-600 font-semibold">Total saat ini: 0</span>
                        </div>
                    </form>
                @endif
            </div>

            <script>
                const inputs = document.querySelectorAll('.weight-input');
                const totalText = document.getElementById('total-text');

                function calculateTotal() {
                    if (!totalText) return;
                    let total = 0;
                    inputs.forEach(input => {
                        total += parseFloat(input.value) || 0;
                    });
                    total = Math.round(total * 100) / 100;
                    totalText.innerText = 'Total saat ini: ' + total;
                    totalText.style.color = (total === 100) ? 'green' : 'red';
                }

                inputs.forEach(input => input.addEventListener('input', calculateTotal));
                document.addEventListener('DOMContentLoaded', calculateTotal);
            </script>
        </div>
    </div>
</x-app-layout>
