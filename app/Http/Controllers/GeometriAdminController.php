<?php

namespace App\Http\Controllers;

use App\Models\Geometri;
use App\Models\Desa;
use Illuminate\Http\Request;

class GeometriAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    $geometri = Geometri::paginate(25);
    return view("dashboard.geometri.geometri", compact("geometri"));
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
        // Validasi data input jika diperlukan

        $data = $request->validate([
            'koordinat' => 'required|string',
            'tipe' => 'required|integer|in:1,2,3',
        ]);

        // Simpan data geometri ke dalam database
        $geometri = Geometri::create([
            'koordinat' => $data['koordinat'],
            'tipe' => $data['tipe'],
        ]);

        // Berikan respons sesuai kebutuhan Anda, misalnya respons berhasil atau alihkan ke halaman lain
        return redirect()->route('dashboardGeometri')->with('success', 'Data geometri berhasil diperbarui.');
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
        // dd($request->all()); // <--- Add this line for debugging
        $validatedData = $request->validate([

            'koordinat' => 'required|string',
            'tipe' => 'required|integer|in:1,2,3',
        ]);


        try {
            // Dapatkan data geometri berdasarkan ID
            $geometri = Geometri::findorFail($id);
            // Perbarui data geometri
            if (!$geometri) {
                return redirect()->route('dashboardGeometri')->with('error', 'Data geometri tidak ditemukan.');
            }
            // Perbarui data geometri
            $geometri->koordinat = $validatedData['koordinat'];
            $geometri->tipe = $validatedData['tipe'];
            $geometri->save();
            return redirect()->route('dashboardGeometri')->with('success', 'Data geometri berhasil diperbarui.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            // Tangani jika geometri tidak ditemukan

            return redirect()->route('dashboardGeometri')->with('error', 'Data geometri tidak ditemukan.');

        }

    }




    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $geometri = Geometri::findorfail($id);
        $geometri->delete();
        return redirect()->route('dashboardGeometri');}
    }
