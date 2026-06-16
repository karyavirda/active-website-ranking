<x-app-layout>
    <div class="py-12 max-w-xl mx-auto">
        @if ($errors->any())
            <div class="p-4 bg-red-500 text-white">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="bg-white p-6 shadow sm:rounded-lg">
            <h2 class="text-xl font-bold mb-4">Impor Data</h2>

            <div class="bg-blue-50 p-4 rounded mb-4 text-sm text-blue-700 border border-blue-200">
                <p class="font-bold mb-2">Pilih File untuk Diimpor:</p>
                <p class="mb-2">Anda dapat mengunggah file dalam format <strong>Excel (.xlsx, .xls)</strong> atau
                    <strong>SQL (.sql)</strong>.
                </p>

                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div>
                        <p class="font-bold">Format Excel:</p>
                        <ul class="list-disc ml-4 text-xs">
                            <li>Kolom A: No</li>
                            <li>Kolom B: Subdomain</li>
                            <li>Kolom C: Paket</li>
                        </ul>
                    </div>
                    <div>
                        <p class="font-bold">Format SQL:</p>
                        <ul class="list-disc ml-4 text-xs">
                            <li>Tabel: <code>subdomains</code></li>
                            <li>Gunakan <code>INSERT INTO</code></li>
                            <li>Jangan gunakan <code>DROP/DELETE</code></li>
                        </ul>
                    </div>
                </div>
            </div>

            <form action="{{ route('subdomains.import-process') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="file" accept=".xlsx, .xls, .sql" class="w-full mb-4 border p-2 rounded"
                    required>
                <div class="flex gap-2">
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Mulai Import</button>
                    <a href="{{ route('subdomains.index') }}" class="px-4 py-2 bg-gray-200 rounded">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>