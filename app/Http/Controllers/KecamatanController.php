<?php

namespace App\Http\Controllers;

use App\Models\Kecamatan;
use Illuminate\Http\Request;

class KecamatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kec = Kecamatan::all();
        return view("dashboard.kecamatan.kecamatan", compact("kec"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kecamatan' => 'required|string|max:100|unique:kecamatan,nama',
        ], [
            'kecamatan.required' => 'Nama kecamatan wajib diisi.',
            'kecamatan.unique' => 'Nama kecamatan sudah terdaftar.',
        ]);

        Kecamatan::create([
            'nama' => $validated['kecamatan'],
        ]);

        return redirect()->route('dashboardKecamatan')->with('success', 'Data Kecamatan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'kecamatan' => 'required|string|max:100|unique:kecamatan,nama,' . $id,
        ], [
            'kecamatan.required' => 'Nama kecamatan wajib diisi.',
            'kecamatan.unique' => 'Nama kecamatan sudah terdaftar.',
        ]);

        $kecamatan = Kecamatan::findOrFail($id);
        $kecamatan->update([
            'nama' => $validated['kecamatan'],
        ]);

        return redirect()->route('dashboardKecamatan')->with('success', 'Data Kecamatan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $kecamatan = Kecamatan::findOrFail($id);
        $kecamatan->delete();

        return redirect()->route('dashboardKecamatan')->with('success', 'Data Kecamatan berhasil dihapus.');
    }
}
