<?php

namespace App\Http\Controllers;

use App\Models\DataImages;
use App\Imports\DataImagesImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class DataImagesController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DataImages::query();
            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('data-images.edit', $row->id) . '" class="text-blue-600 mr-2">Edit</a>';
                    $btn .= '<form action="' . route('data-images.destroy', $row->id) . '" method="POST" class="inline" onsubmit="return confirm(\'Hapus?\')">
                            ' . csrf_field() . method_field("DELETE") . '
                            <button type="submit" class="text-red-600">Hapus</button>
                         </form>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('images.index');
    }

    public function store(Request $request)
    {
        $request->validate(['id' => 'required|unique:data_images,id,', 'subdomain' => 'required', 'nama' => 'required', 'created_at' => 'nullable|date'], [
            'id.unique' => 'ID ini sudah terdaftar! Silakan gunakan ID lain.',
            'id.required' => 'ID wajib diisi!',
        ]);
        DataImages::create($request->all());
        return redirect()->route('data-images.index')->with('success', 'Data berhasil ditambah');
    }

    public function destroy($id)
    {
        DataImages::where('id', $id)->delete();
        return back()->with('success', 'Data dihapus');
    }

    // Modul Import
    public function import()
    {
        return view('images.import');
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
            \Excel::import(new \App\Imports\DataImagesImport, $file);
        }

        return redirect()->route('data-images.index')->with('success', 'Data berhasil diimpor!');
    }

    public function create()
    {
        return view('images.create');
    }

    public function edit($id)
    {
        $image = DataImages::findOrFail($id);
        return view('images.edit', compact('image'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            // 'unique:tabel,kolom,id_yang_dikecualikan'
            'id' => 'required|unique:data_images,id,' . $id,
            'subdomain' => 'required',
            'nama' => 'required',
            'created_at' => 'nullable|date'
        ], [
            'id.unique' => 'ID ini sudah dipakai oleh data lain!',
            'id.required' => 'ID wajib diisi!',
        ]);

        DataImages::where('id', $id)->update($request->except(['_token', '_method']));

        return redirect()->route('data-images.index')->with('success', 'Data berhasil diupdate!');
    }
}
