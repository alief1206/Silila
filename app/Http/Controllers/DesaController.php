<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Desa;
use App\Models\Kecamatan;

class DesaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $desa = Desa::with('kecamatan')->get();
        $kecamatan = Kecamatan::orderBy('nama', 'asc')->get();
        return view("dashboard.desa.desa", compact("desa", "kecamatan"));
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
            'kecamatan_id' => 'required|exists:kecamatan,id',
            'desa' => 'required|string|max:100',
        ], [
            'kecamatan_id.required' => 'Kecamatan wajib dipilih.',
            'kecamatan_id.exists' => 'Kecamatan yang dipilih tidak valid.',
            'desa.required' => 'Nama desa wajib diisi.',
        ]);

        Desa::create([
            'kecamatan_id' => $validated['kecamatan_id'],
            'nama' => $validated['desa'],
        ]);

        return redirect()->route('dashboardDesa')->with('success', 'Data Desa berhasil ditambahkan.');
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
            'kecamatan_id' => 'required|exists:kecamatan,id',
            'desa' => 'required|string|max:100',
        ], [
            'kecamatan_id.required' => 'Kecamatan wajib dipilih.',
            'kecamatan_id.exists' => 'Kecamatan yang dipilih tidak valid.',
            'desa.required' => 'Nama desa wajib diisi.',
        ]);

        $desa = Desa::findOrFail($id);
        $desa->update([
            'kecamatan_id' => $validated['kecamatan_id'],
            'nama' => $validated['desa'],
        ]);

        return redirect()->route('dashboardDesa')->with('success', 'Data Desa berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $desa = Desa::findOrFail($id);
        $desa->delete();

        return redirect()->route('dashboardDesa')->with('success', 'Data Desa berhasil dihapus.');
    }
}
