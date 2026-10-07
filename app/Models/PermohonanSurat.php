<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermohonanSurat extends Model
{
    use HasFactory;

    protected $table = 'permohonan_surat';

    protected $fillable = [
        'kode_registrasi',
        'jenis_surat',
        'nama_pemohon',
        'nik',
        'no_hp',
        'alamat_pemohon',
        'kecamatan',
        'desa',
        'alamat_lahan',
        'luas_lahan',
        'koordinat',
        'dokumen_pendukung',
        'file_surat_permohonan',
        'file_ktp',
        'file_petok_c',
        'file_skt_kades',
        'file_penguasaan_fisik',
        'file_shp',
        'geojson_polygon',
        'status',
        'catatan_admin',
        'file_surat_balasan',
    ];
}
