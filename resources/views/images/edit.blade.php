<x-app-layout>
    <div class="py-12 max-w-2xl mx-auto">
        <div class="bg-white p-6 shadow sm:rounded-lg">
            <h2 class="text-xl font-bold mb-4">Edit Data</h2>
            <form action="{{ route('data-images.update', $image->id) }}" method="POST">
                @csrf @method('PUT')
                <input type="text" name="subdomain" value="{{ $image->subdomain }}"
                    class="w-full mb-2 border rounded p-2">
                <input type="text" name="nama" value="{{ $image->nama }}" class="w-full mb-2 border rounded p-2">
                <button type="submit" class="bg-amber-600 text-white px-4 py-2 rounded">Update</button>
            </form>
        </div>
    </div>
</x-app-layout>