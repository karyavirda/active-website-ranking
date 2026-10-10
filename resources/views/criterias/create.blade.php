<x-app-layout>
    <div class="py-12 max-w-2xl mx-auto">
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
            <h2 class="text-xl font-bold mb-4">Tambah Kriteria</h2>
            <form action="{{ route('criterias.store') }}" method="POST">
                @csrf

                @include('criterias._form')

                <div class="mt-6 flex items-center gap-3">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Simpan</button>
                    <a href="{{ route('criterias.index') }}" class="text-gray-600 hover:underline">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
