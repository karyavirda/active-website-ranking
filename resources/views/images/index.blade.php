<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Data Images</h2>
            <div class="flex gap-2">
                <a href="{{ route('data-images.create') }}"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Data</a>

                <a href="{{ route('data-images.import') }}"
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Import Excel</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ showImport: false }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-green-500 text-white p-4 rounded mb-4">{{ session('success') }}</div>
            @endif
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