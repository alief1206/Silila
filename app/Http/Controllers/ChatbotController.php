<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PermohonanSurat;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    public function respond(Request $request)
    {
        $message = strtolower(trim($request->input('message')));
        $reply = $this->getBotResponse($message);

        return response()->json(['reply' => $reply]);
    }

    private function getBotResponse($message)
    {
        if (str_contains($message, 'halo') || str_contains($message, 'hai') || str_contains($message, 'mulai')) {
            return "Halo! Selamat datang di **Asisten Virtual SILILA** (Sistem Informasi Perlindungan Lahan Kab. Banyuwangi).\n\nSilakan pilih menu layanan yang Anda butuhkan di bawah ini:\n\n1️⃣ **Chat Admin** — Konsultasi langsung dengan petugas admin.\n2️⃣ **Mengajukan Surat Permohonan** — Panduan interaktif pemrosesan Surat Keterangan Kesesuaian Lahan (LP2B & LSD).";
        } elseif (str_contains($message, 'lp2b')) {
            return "🌱 **LP2B (Lahan Pertanian Pangan Berkelanjutan)** adalah kawasan lahan budidaya pertanian yang dilindungi untuk menjamin ketahanan pangan daerah.\n\nUntuk menerbitkan **Surat Keterangan Resminya**, silakan pilih menu **'Mengajukan Surat Permohonan'**.";
        } elseif (str_contains($message, 'lsd')) {
            return "🌾 **LSD (Lahan Sawah Dilindungi)** adalah penetapan lahan sawah oleh pemerintah untuk mengendalikan alih fungsi lahan sawah.\n\nUntuk memproses **Surat Keterangan Kesesuaian Lahan**, silakan pilih menu **'Mengajukan Surat Permohonan'**.";
        } elseif (str_contains($message, 'login')) {
            return "Untuk keperluan login admin, silakan menuju halaman `/login`.";
        } elseif (str_contains($message, 'peta') || str_contains($message, 'map')) {
            return "Peta interaktif SILILA menampilkan data Geometri, LSD, dan LP2B Kabupaten Banyuwangi. Anda dapat memasukkan titik koordinat lahan untuk pengecekan lokasi secara instan.";
        } else {
            return "Terima kasih atas pesan Anda. Silakan pilih menu utama:\n• Ketik **'1'** atau **'Chat Admin'** untuk berbicara dengan petugas.\n• Ketik **'2'** atau **'Permohonan'** untuk mengajukan Surat Keterangan Lahan secara interaktif.";
        }
    }

    public function submitPermohonan(Request $request)
    {
        $request->validate([
            'nama_pemohon' => 'required|string',
            'nik'          => 'required|string',
            'no_hp'        => 'required|string',
        ]);

        try {
            $datePrefix = date('Ymd');
            $randomStr = strtoupper(Str::random(4));
            $kodeRegistrasi = "REG-SILILA-{$datePrefix}-{$randomStr}";

            $uploadDir = public_path('uploads/permohonan');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $filePaths = [];
            $docFields = ['file_ktp', 'file_petok_c', 'file_skt_kades', 'file_penguasaan_fisik', 'file_shp'];

            foreach ($docFields as $field) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $filename = time() . '_' . $field . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                    $file->move($uploadDir, $filename);
                    $filePaths[$field] = 'uploads/permohonan/' . $filename;
                } else {
                    $filePaths[$field] = null;
                }
            }

            $permohonan = PermohonanSurat::create([
                'kode_registrasi'       => $kodeRegistrasi,
                'jenis_surat'           => 'Surat Keterangan Kesesuaian Lahan (LP2B & LSD)',
                'nama_pemohon'          => $request->input('nama_pemohon'),
                'nik'                   => $request->input('nik'),
                'no_hp'                 => $request->input('no_hp'),
                'alamat_pemohon'        => $request->input('alamat_pemohon'),
                'kecamatan'             => $request->input('kecamatan'),
                'desa'                  => $request->input('desa'),
                'alamat_lahan'          => $request->input('alamat_lahan'),
                'luas_lahan'            => $request->input('luas_lahan'),
                'koordinat'             => $request->input('koordinat'),
                'dokumen_pendukung'     => $request->input('dokumen_pendukung'),
                'file_ktp'              => $filePaths['file_ktp'],
                'file_petok_c'          => $filePaths['file_petok_c'],
                'file_skt_kades'        => $filePaths['file_skt_kades'],
                'file_penguasaan_fisik' => $filePaths['file_penguasaan_fisik'],
                'file_shp'              => $filePaths['file_shp'],
                'geojson_polygon'       => $request->input('geojson_polygon'),
                'status'                => 'Menunggu Verifikasi',
            ]);

            return response()->json([
                'success'         => true,
                'kode_registrasi' => $kodeRegistrasi,
                'data'            => $permohonan,
                'message'         => 'Permohonan surat berhasil diajukan!'
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal simpan permohonan surat: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan permohonan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cekStatusPermohonan($kode)
    {
        $permohonan = PermohonanSurat::where('kode_registrasi', $kode)->first();

        if (!$permohonan) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor registrasi permohonan tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $permohonan
        ]);
    }
}
