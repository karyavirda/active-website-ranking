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
        $request->validate(['subdomain' => 'required', 'judul' => '', 'tipe' => 'required', 'created_at' => 'nullable|date'], [
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

        if (!in_array($extension, ['xlsx', 'xls', 'sql'])) {
            return back()->with('error', 'Format file tidak didukung! Gunakan .xlsx, .xls, atau .sql');
        }

        if ($extension === 'sql') {
            $sqlContent = file_get_contents($file->getRealPath());

            // 1. Ganti INSERT INTO menjadi INSERT IGNORE INTO agar tidak duplikat
            $sqlContent = str_ireplace('INSERT INTO', 'INSERT IGNORE INTO', $sqlContent);

            // 2. Bersihkan baris komentar (-- atau #) agar tidak mengganggu PDO
            $lines = explode("\n", $sqlContent);
            $cleanSql = '';
            foreach ($lines as $line) {
                $trimmed = trim($line);
                if ($trimmed === '' || str_starts_with($trimmed, '--') || str_starts_with($trimmed, '#')) {
                    continue;
                }
                $cleanSql .= $line . "\n";
            }

            $cleanSql = trim($cleanSql);

            if (!empty($cleanSql)) {
                \DB::beginTransaction();
                try {
                    // 3. Gunakan koneksi PDO mentah bawaan Laravel untuk mengeksekusi multi-query sekaligus
                    // Ini cara paling aman agar teks berita yang mengandung tanda baca tidak rusak atau terpotong
                    $pdo = \DB::connection()->getPdo();
                    $pdo->exec($cleanSql);

                    \DB::commit();
                } catch (\Exception $e) {
                    \DB::rollBack();
                    // Jika gagal, tangkap error database asli dan tampilkan ke layar!
                    return back()->with('error', 'Proses SQL Gagal! Kendala: ' . $e->getMessage());
                }
            } else {
                return back()->with('error', 'File SQL kosong atau hanya berisi komentar.');
            }

        } else {
            \Excel::import(new \App\Imports\DataNewsImport, $file);
        }

        return redirect()->route('data-news.index')->with('success', 'Semua data SQL berhasil diimpor sepenuhnya!');
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
            'subdomain' => 'required',
            'judul' => '',
            'tipe' => 'required',
            'created_at' => 'nullable|date'
        ], [
            'tipe.required' => 'Tipe wajib diisi!',
        ]);

        DataParts::where('id', $id)->update($request->except(['_token', '_method']));

        return redirect()->route('data-parts.index')->with('success', 'Data berhasil diupdate!');
    }
}
