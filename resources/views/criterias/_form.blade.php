@php($criteria = $criteria ?? null)

<div class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block font-semibold text-sm text-gray-800 mb-1">Kode</label>
            <input type="text" name="kode" value="{{ old('kode', $criteria->kode ?? '') }}" maxlength="10"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 uppercase"
                placeholder="Contoh: C5" required>
        </div>

        <div class="md:col-span-2">
            <label class="block font-semibold text-sm text-gray-800 mb-1">Nama Kriteria</label>
            <input type="text" name="nama" value="{{ old('nama', $criteria->nama ?? '') }}"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                placeholder="Contoh: Konsistensi" required>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block font-semibold text-sm text-gray-800 mb-1">Tipe</label>
            @php($tipe = old('tipe', $criteria->tipe ?? ''))
            <select name="tipe" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                <option value="" disabled {{ $tipe === '' ? 'selected' : '' }}>Pilih Tipe</option>
                <option value="benefit" {{ $tipe === 'benefit' ? 'selected' : '' }}>Benefit (semakin besar semakin baik)</option>
                <option value="cost" {{ $tipe === 'cost' ? 'selected' : '' }}>Cost (semakin kecil semakin baik)</option>
            </select>
        </div>

        <div class="md:col-span-2">
            <label class="block font-semibold text-sm text-gray-800 mb-1">Satuan <span class="text-xs font-normal text-gray-400">(opsional)</span></label>
            <input type="text" name="satuan" value="{{ old('satuan', $criteria->satuan ?? '') }}"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                placeholder="Contoh: Rata-rata post/minggu">
        </div>
    </div>

    <div>
        <label class="block font-semibold text-sm text-gray-800 mb-1">Deskripsi <span class="text-xs font-normal text-gray-400">(opsional)</span></label>
        <textarea name="deskripsi" rows="4"
            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            placeholder="Jelaskan apa yang diukur dan cara menghitung kriteria ini">{{ old('deskripsi', $criteria->deskripsi ?? '') }}</textarea>
    </div>
</div>
