<x-app-layout>
    <div class="py-12 max-w-2xl mx-auto">
        @if(session('error'))
            <div class="bg-red-500 text-white p-4 rounded mb-4">{{ session('error') }}</div>
        @endif

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
            <h2 class="text-xl font-bold mb-4">Edit Data</h2>
            <form action="{{ route('data-images.update', $image->id) }}" method="POST">
                @csrf @method('PUT')

                <input type="text" name="subdomain" value="{{ $image->subdomain }}"
                    class="w-full mb-2 border rounded p-2" required>

                <input type="text" name="nama" value="{{ $image->nama }}" class="w-full mb-2 border rounded p-2"
                    required>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Dibuat</label>
                    <input type="datetime-local" name="created_at" value="{{ $image->created_at }}"
                        class="w-full border rounded p-2" required>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
            </form>
        </div>
    </div>
</x-app-layout>