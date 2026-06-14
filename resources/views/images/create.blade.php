<x-app-layout>
    <div class="py-12 max-w-2xl mx-auto">
        <div class="bg-white p-6 shadow sm:rounded-lg">
            <h2 class="text-xl font-bold mb-4">Tambah Data</h2>
            <form action="{{ route('data-images.store') }}" method="POST">
                @csrf

                <input type="number" name="id" placeholder="ID" class="w-full mb-2 border rounded p-2" required>

                <input type="text" name="subdomain" placeholder="Subdomain" class="w-full mb-2 border rounded p-2"
                    required>

                <input type="text" name="nama" placeholder="Nama" class="w-full mb-2 border rounded p-2" required>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Dibuat</label>
                    <input type="datetime-local" name="created_at" class="w-full border rounded p-2" required>
                </div>

                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
            </form>
        </div>
    </div>
</x-app-layout>