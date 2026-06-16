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
            <form action="{{ route('data-logs.update', $logs->id) }}" method="POST">
                @csrf @method('PUT')
                <input type="number" name="id" value="{{ $logs->id }}" class="w-full mb-2 border rounded p-2" required>

                <input type="text" name="subdomain" value="{{ $logs->subdomain }}"
                    class="w-full mb-2 border rounded p-2" required>

                <input type="text" name="nama_admin" value="{{ $logs->nama_admin }}"
                    class="w-full mb-2 border rounded p-2" required>
                <input type="text" name="aktivitas" value="{{ $logs->aktivitas }}"
                    class="w-full mb-2 border rounded p-2" required>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Aktivitas</label>
                    <input type="datetime-local" name="activity_date" value="{{ $logs->activity_date }}"
                        class="w-full border rounded p-2" required>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
            </form>
        </div>
    </div>
</x-app-layout>