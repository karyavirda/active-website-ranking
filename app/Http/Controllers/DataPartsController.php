<?php

namespace App\Http\Controllers;

use App\Models\DataParts;
use App\Imports\DataPartsImport;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;

class DataPartsController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DataParts::query();
            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('data-parts.edit', $row->id) . '" class="text-blue-600 mr-2">Edit</a>';
                    $btn .= '<form action="' . route('data-parts.destroy', $row->id) . '" method="POST" class="inline" onsubmit="return confirm(\'Hapus?\')">
                            ' . csrf_field() . method_field("DELETE") . '
                            <button type="submit" class="text-red-600">Hapus</button>
                         </form>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('parts.index');
    }

    public function store(Request $request)
    {
        $request->validate(['id' => 'required|unique:data_parts,id,', 'subdomain' => 'required', 'judul' => '', 'tipe' => 'required', 'created_at' => 'nullable|date'], [
            'id.unique' => 'ID ini sudah terdaftar! Silakan gunakan ID lain.',
            'id.required' => 'ID wajib diisi!',
            'tipe.required' => 'Tipe wajib diisi!',
        ]);
        DataParts::create($request->all());
        return redirect()->route('data-parts.index')->with('success', 'Data berhasil ditambah');
    }


    public function destroy($id)
    {
        DataParts::where('id', $id)->delete();
        return back()->with('success', 'Data dihapus');
    }

    // Modul Import
    public function import()
    {
        return view('parts.import');
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
            \Excel::import(new \App\Imports\DataPartsImport, $file);
        }

        return redirect()->route('data-parts.index')->with('success', 'Data berhasil diimpor!');
    }

    public function create()
    {
        return view('parts.create');
    }

    public function edit($id)
    {
        $parts = DataParts::findOrFail($id);
        return view('parts.edit', compact('parts'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            // 'unique:tabel,kolom,id_yang_dikecualikan'
            'id' => 'required|unique:data_parts,id,' . $id,
            'subdomain' => 'required',
            'judul' => '',
            'tipe' => 'required',
            'created_at' => 'nullable|date'
        ], [
            'id.unique' => 'ID ini sudah dipakai oleh data lain!',
            'id.required' => 'ID wajib diisi!',
            'tipe.required' => 'Tipe wajib diisi!',
        ]);

        DataParts::where('id', $id)->update($request->except(['_token', '_method']));

        return redirect()->route('data-parts.index')->with('success', 'Data berhasil diupdate!');
    }
}
