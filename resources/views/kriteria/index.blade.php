<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Data Kriteria</h2>
            <a href="{{ route('kriteria.create') }}"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md shadow transition">
                + Tambah Kriteria
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b">
                                <th class="p-3 font-semibold text-gray-700">Kode</th>
                                <th class="p-3 font-semibold text-gray-700">Nama</th>
                                <th class="p-3 font-semibold text-gray-700">Bobot</th>
                                <th class="p-3 font-semibold text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kriterias as $item)
                                <tr class="border-b hover:bg-gray-50 transition">
                                    <td class="p-3">{{ $item->kode_kriteria }}</td>
                                    <td class="p-3">{{ $item->nama_kriteria }}</td>
                                    <td class="p-3">{{ $item->bobot }}</td>
                                    <td class="p-3">
                                        <div class="flex items-center space-x-4">
                                            <a href="{{ route('kriteria.edit', $item->id) }}"
                                                class="text-amber-600 hover:text-amber-900 font-semibold text-sm">
                                                Edit
                                            </a>

                                            <form action="{{ route('kriteria.destroy', $item->id) }}" method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus kriteria {{ $item->nama_kriteria }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-600 hover:text-red-900 font-semibold text-sm">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>