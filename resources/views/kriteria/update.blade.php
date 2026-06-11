<x-app-layout>
    <div class="py-12 max-w-4xl mx-auto">
        <form action="{{ route('kriteria.update', $kriteria->id) }}" method="POST">
            @csrf @method('PUT')
            <input type="text" name="kode_kriteria" value="{{ $kriteria->kode_kriteria }}" class="w-full mb-2 border rounded p-2">
            <input type="text" name="nama_kriteria" value="{{ $kriteria->nama_kriteria }}" class="w-full mb-2 border rounded p-2">
            <input type="number" step="0.01" name="bobot" value="{{ $kriteria->bobot }}" class="w-full mb-2 border rounded p-2">
            <input type="text" name="satuan" value="{{ $kriteria->satuan }}" placeholder="Satuan (ex: kg, %)" class="w-full mb-2 border rounded p-2">
            <textarea name="deskripsi" placeholder="Deskripsi Kriteria" class="w-full mb-2 border rounded p-2">{{ $kriteria->deskripsi }}</textarea>
            <select name="jenis" class="w-full mb-4 border rounded p-2">
                <option value="Benefit" {{ $kriteria->jenis == 'Benefit' ? 'selected' : '' }}>Benefit</option>
                <option value="Cost" {{ $kriteria->jenis == 'Cost' ? 'selected' : '' }}>Cost</option>
            </select>
            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Update</button>
        </form>
    </div>
</x-app-layout>