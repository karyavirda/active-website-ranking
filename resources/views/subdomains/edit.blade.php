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
            <form action="{{ route('subdomains.update', $subdomain->id) }}" method="POST">
                @csrf @method('PUT')subdomain

                <input type="text" name="subdomain" value="{{ $subdomain->subdomain }}"
                    class="w-full mb-2 border rounded p-2" required>
                <label class="block font-medium text-gray-700">Paket</label>
                <select name="paket" class="w-full mb-2 border rounded p-2" required>
                    <option value="basic" {{ $subdomain->paket == 'basic' ? 'selected' : '' }}>Basic</option>
                    <option value="profesional" {{ $subdomain->paket == 'profesional' ? 'selected' : '' }}>Profesional
                    </option>
                    <option value="exclusive" {{ $subdomain->paket == 'exclusive' ? 'selected' : '' }}>Exclusive</option>
                    <option value="custom" {{ $subdomain->paket == 'custom' ? 'selected' : '' }}>Custom</option>
                    <option value="vip" {{ $subdomain->paket == 'vip' ? 'selected' : '' }}>VIP</option>
                </select>

                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
            </form>
        </div>
    </div>
</x-app-layout>