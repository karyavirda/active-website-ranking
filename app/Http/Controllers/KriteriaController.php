<?php

namespace App\Http\Controllers;

use App\Models\Kriteria; 
use Illuminate\Http\Request;

class KriteriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kriterias = \App\Models\Kriteria::all();
        return view('kriteria.index', compact('kriterias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('kriteria.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(['kode_kriteria'=>'required', 'nama_kriteria'=>'required', 'bobot'=>'required', 'satuan' => 'nullable|string|max:50', 'deskripsi' => 'nullable|string', 'jenis'=>'required']);
        Kriteria::create($request->all());
        return redirect()->route('kriteria.index')->with('success', 'Data berhasil ditambah');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return view('kriteria.show', compact('kriteria'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $kriteria = Kriteria::findOrFail($id);
        return view('kriteria.update', compact('kriteria'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'kode_kriteria' => 'required',
            'nama_kriteria' => 'required',
            'bobot'         => 'required',
            'jenis'         => 'required'
        ]);

        $Kriteria = Kriteria::findOrFail($id);
        $Kriteria->update($request->all());
        return redirect()->route('kriteria.index')->with('success', 'Data berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $Kriteria = Kriteria::findOrFail($id);
        $Kriteria->delete();
        return redirect()->route('kriteria.index')->with('success', 'Data dihapus');
    }
}
