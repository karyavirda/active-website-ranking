<?php

namespace App\Http\Controllers;

use App\Models\Subdomains;
use App\Imports\SubdomainsImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class SubdomainsController extends Controller
{


    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Tambahkan orderBy('nama_kolom', 'asc') di sini. 
            // Pastikan sesuaikan nama kolomnya (misal: 'name' atau 'subdomain')
            $data = Subdomains::query()->orderBy('subdomain', 'asc');

            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('subdomains.edit', $row->id) . '" class="text-blue-600 mr-2">Edit</a>';
                    $btn .= '<form action="' . route('subdomains.destroy', $row->id) . '" method="POST" class="inline" onsubmit="return confirm(\'Hapus?\')">
                            ' . csrf_field() . method_field("DELETE") . '
                            <button type="submit" class="text-red-600">Hapus</button>
                         </form>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('subdomains.index');
    }

    public function store(Request $request)
    {
        $request->validate(['subdomain' => 'required|unique:subdomains,subdomain', 'paket' => 'required'], [
            'subdomain.unique' => 'Subdomain ini sudah terdaftar! Silakan gunakan subdomain lain.',
            'subdomain.required' => 'Subdomain wajib diisi!',
        ]);
        Subdomains::create($request->all());
        return redirect()->route('subdomains.index')->with('success', 'Data berhasil ditambah');
    }


    public function destroy($id)
    {
        Subdomains::where('id', $id)->delete();
        return back()->with('success', 'Data dihapus');
    }

    // Modul Import
    public function import()
    {
        return view('subdomains.import');
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
            \Excel::import(new \App\Imports\SubdomainsImport, $file);
        }

        return redirect()->route('subdomains.index')->with('success', 'Data berhasil diimpor!');
    }

    public function create()
    {
        return view('subdomains.create');
    }

    public function edit($id)
    {
        $subdomain = Subdomains::findOrFail($id);
        return view('subdomains.edit', compact('subdomain'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            // 'unique:tabel,kolom,id_yang_dikecualikan'
            'subdomain' => 'required|unique:subdomains,id,' . $id,
            'paket' => 'required'
        ], [
            'subdomain.unique' => 'Subdomain ini sudah terdaftar! Silakan gunakan subdomain lain.',
            'subdomain.required' => 'Subdomain wajib diisi!',
        ]);

        Subdomains::where('id', $id)->update($request->except(['_token', '_method']));

        return redirect()->route('subdomains.index')->with('success', 'Data berhasil diupdate!');
    }

}
