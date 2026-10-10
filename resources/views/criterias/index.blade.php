<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Data Kriteria</h2>
            <div class="flex gap-2">
                <a href="{{ route('criteria-weights.index') }}"
                    class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-50 transition">Atur Bobot</a>
                <a href="{{ route('criterias.create') }}"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Tambah Kriteria</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-500 text-white p-4 rounded mb-6 shadow-sm">{{ session('success') }}</div>
            @endif

            <div class="bg-white p-6 shadow-sm sm:rounded-lg border border-gray-200">
                <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-500 text-blue-700 text-sm">
                    <ul class="list-disc ml-5 space-y-1">
                        <li><b>Benefit</b>: semakin besar nilainya semakin baik (normalisasi: nilai / nilai maksimal).</li>
                        <li><b>Cost</b>: semakin kecil nilainya semakin baik (normalisasi: nilai minimal / nilai).</li>
                        <li>Kode yang sudah memiliki rumus perhitungan: <b>{{ implode(', ', \App\Services\SawService::SUPPORTED_CODES) }}</b>. Kriteria dengan kode lain akan bernilai 0 pada perhitungan SAW.</li>
                    </ul>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-600">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                            <tr>
                                <th class="px-4 py-3 w-20">Kode</th>
                                <th class="px-4 py-3">Nama Kriteria</th>
                                <th class="px-4 py-3 w-28">Tipe</th>
                                <th class="px-4 py-3">Satuan</th>
                                <th class="px-4 py-3">Deskripsi</th>
                                <th class="px-4 py-3 w-20 text-center">Bobot</th>
                                <th class="px-4 py-3 w-32">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($criterias as $criteria)
                                <tr class="hover:bg-gray-50 align-top">
                                    <td class="px-4 py-3 font-bold text-gray-800">{{ $criteria->kode }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $criteria->nama }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold {{ $criteria->tipe === 'cost' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                            {{ ucfirst($criteria->tipe) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-xs">{{ $criteria->satuan ?? '-' }}</td>
                                    <td class="px-4 py-3 text-xs text-gray-500 max-w-md">{{ $criteria->deskripsi ?? '-' }}</td>
                                    <td class="px-4 py-3 text-center font-semibold text-blue-600">{{ $criteria->weight->bobot ?? 0 }}%</td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <a href="{{ route('criterias.edit', $criteria) }}" class="text-blue-600 mr-2">Edit</a>
                                        <form action="{{ route('criterias.destroy', $criteria) }}" method="POST" class="inline"
                                            onsubmit="return confirm('Hapus kriteria {{ $criteria->kode }} beserta bobotnya?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-6 text-center text-gray-400 italic">Belum ada data kriteria.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
