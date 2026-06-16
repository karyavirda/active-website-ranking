<?php

namespace App\Http\Controllers;

use App\Models\DataLogs;
use App\Imports\DataLogsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class DataLogsController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DataLogs::query();
            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('data-logs.edit', $row->id) . '" class="text-blue-600 mr-2">Edit</a>';
                    $btn .= '<form action="' . route('data-logs.destroy', $row->id) . '" method="POST" class="inline" onsubmit="return confirm(\'Hapus?\')">
                            ' . csrf_field() . method_field("DELETE") . '
                            <button type="submit" class="text-red-600">Hapus</button>
                         </form>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('logs.index');
    }

    public function store(Request $request)
    {
        $request->validate(['id' => 'required|unique:data_logs,id,', 'subdomain' => 'required', 'nama_admin' => 'required', 'aktivitas' => 'required', 'activity_date' => 'required|date'], [
            'id.unique' => 'ID ini sudah terdaftar! Silakan gunakan ID lain.',
            'id.required' => 'ID wajib diisi!',
        ]);
        DataLogs::create($request->all());
        return redirect()->route('data-logs.index')->with('success', 'Data berhasil ditambah');
    }


    public function destroy($id)
    {
        DataLogs::where('id', $id)->delete();
        return back()->with('success', 'Data dihapus');
    }

    // Modul Import
    public function import()
    {
        return view('logs.import');
    }

    public function importProcess(Request $request)
    {
        $request->validate([
            'file' => 'required|max:2048'
        ]);

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());

        // Filter manual ekstensi
        if (!in_array($extension, ['xlsx', 'xls', 'sql'])) {
            return back()->with('error', 'Format file tidak didukung! Gunakan .xlsx, .xls, atau .sql');
        }

        if ($extension === 'sql') {
            $sql = file_get_contents($file->getRealPath());

            $sql = str_ireplace('INSERT INTO', 'INSERT IGNORE INTO', $sql);

            // Coba eksekusi langsung
            $success = \DB::statement($sql);

            if (!$success) {
                return back()->with('error', 'Query SQL gagal dijalankan.');
            }
        } else {
            // HAPUS try-catch di sini untuk sementara agar error terlihat
            \Excel::import(new \App\Imports\DataLogsImport, $file);
        }

        return redirect()->route('data-logs.index')->with('success', 'Data berhasil diimpor!');
    }

    public function create()
    {
        return view('logs.create');
    }

    public function edit($id)
    {
        $logs = DataLogs::findOrFail($id);
        return view('logs.edit', compact('logs'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            // 'unique:tabel,kolom,id_yang_dikecualikan'
            'id' => 'required|unique:data_logs,id,' . $id,
            'subdomain' => 'required',
            'nama_admin' => 'required',
            'aktivitas' => 'required',
            'activity_date' => 'required|date'
        ], [
            'id.unique' => 'ID ini sudah dipakai oleh data lain!',
            'id.required' => 'ID wajib diisi!',
        ]);

        DataLogs::where('id', $id)->update($request->except(['_token', '_method']));

        return redirect()->route('data-logs.index')->with('success', 'Data berhasil diupdate!');
    }
}
