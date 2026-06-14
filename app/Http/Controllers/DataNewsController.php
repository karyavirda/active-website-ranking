<?php

namespace App\Http\Controllers;

use App\Models\DataNews;
use App\Imports\DataNewsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;


class DataNewsController extends Controller
{
    public function index()
    {
        $news = DataNews::all();
        return view('news.index', compact('news'));
    }

    public function store(Request $request)
    {
        $request->validate(['id' => 'required|unique:data_news,id,', 'subdomain' => 'required', 'judul' => 'required', 'created_at' => 'nullable|date'], [
            'id.unique' => 'ID ini sudah terdaftar! Silakan gunakan ID lain.',
            'id.required' => 'ID wajib diisi!',
        ]);
        DataNews::create($request->all());
        return redirect()->route('data-news.index')->with('success', 'Data berhasil ditambah');
    }


    public function destroy($id)
    {
        DataNews::where('id', $id)->delete();
        return back()->with('success', 'Data dihapus');
    }

    // Modul Import
    public function import()
    {
        return view('news.import');
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
            \Excel::import(new \App\Imports\DataNewsImport, $file);
        }

        return redirect()->route('data-news.index')->with('success', 'Data berhasil diimpor!');
    }

    public function create()
    {
        return view('news.create');
    }

    public function edit($id)
    {
        $news = DataNews::findOrFail($id);
        return view('news.edit', compact('news'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            // 'unique:tabel,kolom,id_yang_dikecualikan'
            'id' => 'required|unique:data_news,id,' . $id,
            'subdomain' => 'required',
            'judul' => 'required',
            'created_at' => 'nullable|date'
        ], [
            'id.unique' => 'ID ini sudah dipakai oleh data lain!',
            'id.required' => 'ID wajib diisi!',
        ]);

        DataNews::where('id', $id)->update($request->except(['_token', '_method']));

        return redirect()->route('data-news.index')->with('success', 'Data berhasil diupdate!');
    }
}
