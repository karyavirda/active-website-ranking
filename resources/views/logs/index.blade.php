<x-app-layout>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css">

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Data Admin Activity</h2>
            <div class="flex gap-2">
                <a href="{{ route('data-logs.create') }}"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Add Data</a>
                <a href="{{ route('data-logs.import') }}"
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">Import Data</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-500 text-white p-4 rounded mb-6 shadow-sm">{{ session('success') }}</div>
            @endif

            <div class="bg-white p-6 shadow-sm sm:rounded-lg border border-gray-200">
                <table class="w-full text-sm text-left text-gray-600" id="logs-table">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                        <tr>
                            <th class="px-6 py-4">ID</th>
                            <th class="px-6 py-4">Subdomain</th>
                            <th class="px-6 py-4">Nama Admin</th>
                            <th class="px-6 py-4">Aktivitas</th>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script>

<script>
    $(document).ready(function () {
        $('#logs-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{!! route("data-logs.index") !!}',
            columns: [
                { data: 'id', name: 'id', className: 'px-6 py-4' },
                { data: 'subdomain', name: 'subdomain', className: 'px-6 py-4' },
                { data: 'nama_admin', name: 'nama_admin', className: 'px-6 py-4' },
                { data: 'aktivitas', name: 'aktivitas', className: 'px-6 py-4' },
                { data: 'activity_date', name: 'activity_date', className: 'px-6 py-4' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'px-6 py-4' }
            ],
            // Menyesuaikan tampilan agar lebih minimalis
            dom: '<"flex justify-between items-center mb-4"f>t<"flex justify-between items-center mt-4"ip>',
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Cari data...",
                lengthMenu: "_MENU_",
            }
        });
    });
</script>