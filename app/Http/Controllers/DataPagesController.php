<?php

namespace App\Http\Controllers;

use App\Models\DataPages;
use App\Imports\DataPagesImport;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;

class DataPagesController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DataPages::query();
            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('data-pages.edit', $row->id) . '" class="text-blue-600 mr-2">Edit</a>';
                    $btn .= '<form action="' . route('data-pages.destroy', $row->id) . '" method="POST" class="inline" onsubmit="return confirm(\'Hapus?\')">
                            ' . csrf_field() . method_field("DELETE") . '
                            <button type="submit" class="text-red-600">Hapus</button>
                         </form>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('pages.index');
    }

    public function store(Request $request)
    {
        $request->validate(['id' => 'required|unique:data_pages,id,', 'subdomain' => 'required', 'judul' => 'required', 'created_at' => 'nullable|date'], [
            'id.unique' => 'ID ini sudah terdaftar! Silakan gunakan ID lain.',
            'id.required' => 'ID wajib diisi!',
        ]);
        DataPages::create($request->all());
        return redirect()->route('data-pages.index')->with('success', 'Data berhasil ditambah');
    }


    public function destroy($id)
    {
        DataPages::where('id', $id)->delete();
        return back()->with('success', 'Data dihapus');
    }

    // Modul Import
    public function import()
    {
        return view('pages.import');
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
            \Excel::import(new \App\Imports\DataPagesImport, $file);
        }

        return redirect()->route('data-pages.index')->with('success', 'Data berhasil diimpor!');
    }

    public function create()
    {
        return view('pages.create');
    }

    public function edit($id)
    {
        $pages = DataPages::findOrFail($id);
        return view('pages.edit', compact('pages'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            // 'unique:tabel,kolom,id_yang_dikecualikan'
            'id' => 'required|unique:data_pages,id,' . $id,
            'subdomain' => 'required',
            'judul' => 'required',
            'created_at' => 'nullable|date'
        ], [
            'id.unique' => 'ID ini sudah dipakai oleh data lain!',
            'id.required' => 'ID wajib diisi!',
        ]);

        DataPages::where('id', $id)->update($request->except(['_token', '_method']));

        return redirect()->route('data-pages.index')->with('success', 'Data berhasil diupdate!');
    }

}
