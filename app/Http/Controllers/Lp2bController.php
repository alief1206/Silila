<?php

namespace App\Http\Controllers;

use App\Models\DataLp2b;
use App\Models\Geometri;
use App\Models\Desa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Lp2bController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lp2b = DataLp2b::with(['geometri' => function($q) {
            $q->addSelect('*', DB::raw('ST_AsGeoJSON(koordinat) as koordinat_geojson'));
        }, 'geometri.desa.kecamatan'])->latest()->paginate(25);
        $geometris = Geometri::with('desa')->where('tipe', 1)->latest()->limit(100)->get();
        $desas = Desa::orderBy('nama', 'asc')->get();
        return view("dashboard.lp2b.lp2b", compact("lp2b", "geometris", "desas"));
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
        $validatedData = $request->validate([
            'geometri_id' => 'nullable|integer',
            'desa_id'     => 'nullable|exists:desa,id',
            'kp2b'        => 'required|string|max:50',
            'ket'         => 'nullable|string|max:100',
            'luas'        => 'required|string|max:100',
        ], [
            'kp2b.required' => 'Data KP2B wajib diisi.',
            'luas.required' => 'Luas lahan wajib diisi.',
        ]);

        try {
            DB::beginTransaction();

            $geometriId = $request->geometri_id;

            // Jika geometri_id kosong atau belum terdaftar di tabel geometri, buat record geometri tipe 1 (LP2B)
            if (empty($geometriId) || !Geometri::where('id', $geometriId)->exists()) {
                $desaId = $request->desa_id;
                // Default ke desa pertama jika kosong
                if (empty($desaId)) {
                    $firstDesa = Desa::first();
                    $desaId = $firstDesa ? $firstDesa->id : null;
                }

                $koordinatRaw = null;
                if ($request->filled('koordinat')) {
                    $geoJson = $this->parseKoordinat($request->koordinat);
                    if ($geoJson) {
                        $koordinatRaw = "ST_GeomFromGeoJSON('" . $geoJson . "')";
                    }
                }

                $insertGeom = [
                    'desa_id'   => $desaId,
                    'tipe'      => 1, // 1: LP2B
                ];
                if ($koordinatRaw) {
                    $insertGeom['koordinat'] = DB::raw($koordinatRaw);
                }

                $newGeom = Geometri::create($insertGeom);
                $geometriId = $newGeom->id;
            } else {
                // Update koordinat untuk geometri_id yang sudah ada jika diinput
                if ($request->filled('koordinat')) {
                    $geoJson = $this->parseKoordinat($request->koordinat);
                    if ($geoJson) {
                        Geometri::where('id', $geometriId)->update([
                            'koordinat' => DB::raw("ST_GeomFromGeoJSON('" . $geoJson . "')")
                        ]);
                    }
                }
            }

            DataLp2b::create([
                'geometri_id' => $geometriId,
                'kp2b'        => $validatedData['kp2b'],
                'ket'         => $validatedData['ket'] ?? '-',
                'luas'        => $validatedData['luas'],
            ]);

            DB::commit();

            return redirect()->route('dashboard.lp2b')->with('success', 'Data LP2B berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data LP2B: ' . $e->getMessage());
        }
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
        $validatedData = $request->validate([
            'geometri_id' => 'required|integer|exists:geometri,id',
            'desa_id'     => 'nullable|exists:desa,id',
            'kp2b'        => 'required|string|max:50',
            'ket'         => 'nullable|string|max:100',
            'luas'        => 'required|string|max:100',
        ]);

        $lp2b = DataLp2b::findOrFail($id);

        $lp2b->geometri_id = $validatedData['geometri_id'];
        $lp2b->kp2b = $validatedData['kp2b'];
        $lp2b->ket = $validatedData['ket'] ?? '-';
        $lp2b->luas = $validatedData['luas'];
        $lp2b->save();

        if ($request->filled('desa_id')) {
            Geometri::where('id', $validatedData['geometri_id'])->update([
                'desa_id' => $request->desa_id
            ]);
        }
        if ($request->filled('koordinat')) {
            $geoJson = $this->parseKoordinat($request->koordinat);
            if ($geoJson) {
                Geometri::where('id', $validatedData['geometri_id'])->update([
                    'koordinat' => DB::raw("ST_GeomFromGeoJSON('" . $geoJson . "')")
                ]);
            }
        }

        return redirect()->route('dashboard.lp2b')->with('success', 'Data LP2B berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $lp2b = DataLp2b::findOrFail($id);
        $lp2b->delete();
        return redirect()->route('dashboard.lp2b')->with('success', 'Data LP2B berhasil dihapus.');
    }

    private function parseKoordinat($input)
    {
        $input = trim($input);
        if (empty($input)) return null;

        // Jika diawali kurung siku atau kurung kurawal, anggap JSON (array atau object)
        if (strpos($input, '[') === 0 || strpos($input, '{') === 0) {
            $decoded = json_decode($input, true);
            if (!$decoded) return null;

            // Jika input sudah full GeoJSON object
            if (isset($decoded['type']) && isset($decoded['coordinates'])) {
                return json_encode($decoded);
            }

            // Jika input hanya array coordinates
            $depth = 0;
            $temp = $decoded;
            while (is_array($temp)) {
                $depth++;
                if (empty($temp)) break;
                $temp = reset($temp);
            }

            $type = 'MultiPolygon';
            if ($depth === 1) $type = 'Point';
            elseif ($depth === 2) $type = 'LineString';
            elseif ($depth === 3) $type = 'Polygon';

            return json_encode([
                'type' => $type,
                'coordinates' => $decoded
            ]);
        }

        // Format string misal "114.36, -8.21"
        $parts = explode(',', $input);
        if (count($parts) >= 2) {
            $lng = (float) trim($parts[0]);
            $lat = (float) trim($parts[1]);
            return json_encode([
                'type' => 'Point',
                'coordinates' => [$lng, $lat]
            ]);
        }

        return null;
    }
}
