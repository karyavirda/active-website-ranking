<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Data Images</h2>
            <div class="flex gap-2">
                <a href="{{ route('data-images.create') }}"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Data</a>

                <button @click="showImport = !showImport"
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    Import Excel
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ showImport: false }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div x-show="showImport" x-cloak class="bg-white p-6 shadow sm:rounded-lg border-l-4 border-green-500">
                <h3 class="text-lg font-bold mb-3">Upload File Excel</h3>
                <form action="{{ route('data-images.import') }}" method="POST" enctype="multipart/form-data"
                    class="flex gap-4">
                    @csrf
                    <input type="file" name="file" class="border p-2 rounded" required>
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Upload &
                        Import</button>
                </form>
            </div>

            <div class="bg-white p-6 shadow sm:rounded-lg">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="p-3">ID</th>
                            <th class="p-3">Subdomain</th>
                            <th class="p-3">Nama</th>
                            <th class="p-3">Created At</th>
                            <th class="p-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($images as $item)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-3">{{ $item->id }}</td>
                                <td class="p-3">{{ $item->subdomain }}</td>
                                <td class="p-3">{{ $item->nama }}</td>
                                <td class="p-3">{{ $item->created_at }}</td>
                                <td class="p-3">
                                    <a href="{{ route('data-images.edit', $item->id) }}" class="text-blue-600">Edit</a>
                                    <form action="{{ route('data-images.destroy', $item->id) }}" method="POST"
                                        onsubmit="return confirm('Hapus?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

<style>
    [x-cloak] {
        display: none !important;
    }
</style>