<?php

namespace App\Http\Controllers;

use App\Models\DataImages;
use App\Imports\DataImagesImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DataImagesController extends Controller
{
    public function index()
    {
        $images = DataImages::all();
        return view('images.index', compact('images'));
    }

    public function store(Request $request)
    {
        $request->validate(['id' => 'required', 'subdomain' => 'required', 'nama' => 'required', 'created_at' => 'nullable|date  format:Y-m-d H:i:s']);
        DataImages::create($request->all());
        return redirect()->route('data-images.index')->with('success', 'Data berhasil ditambah');
    }

    public function destroy($id)
    {
        DataImages::where('id', $id)->delete();
        return back()->with('success', 'Data dihapus');
    }

    // Modul Import
    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls']);
        Excel::import(new DataImagesImport, $request->file('file'));
        return back()->with('success', 'Data diimport sukses');
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
        DataImages::where('id', $id)->update($request->except(['_token', '_method']));
        return redirect()->route('data-images.index')->with('success', 'Data diupdate');
    }
}
