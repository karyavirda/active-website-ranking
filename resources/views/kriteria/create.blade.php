<x-app-layout>
    <div class="py-12 max-w-4xl mx-auto">
        <div class="bg-white p-6 shadow sm:rounded-lg">
            <h2 class="text-xl font-bold mb-4">Tambah Kriteria</h2>
            <form action="{{ route('kriteria.store') }}" method="POST">
                @csrf
                <input type="text" name="kode_kriteria" placeholder="Kode (ex: C1)" class="w-full mb-2 border rounded p-2">
                <input type="text" name="nama_kriteria" placeholder="Nama Kriteria" class="w-full mb-2 border rounded p-2">
                <input type="number" step="0.01" name="bobot" placeholder="Bobot (ex: 0.25)" class="w-full mb-2 border rounded p-2">
                <input type="text" name="satuan" placeholder="Satuan (ex: %)" class="w-full mb-2 border rounded p-2">
                <textarea name="deskripsi" placeholder="Deskripsi" class="w-full mb-2 border rounded p-2"></textarea>
                <select name="jenis" class="w-full mb-4 border rounded p-2">
                    <option value="Benefit">Benefit</option>
                    <option value="Cost">Cost</option>
                </select>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Simpan</button>
            </form>
        </div>
    </div>
</x-app-layout>