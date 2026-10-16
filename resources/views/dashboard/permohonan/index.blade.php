@extends('dashboard.template')
@section('title', 'Permohonan Surat')

@section('content')
<div class="main-content-container container-fluid px-4 py-3">
    <!-- Page Header -->
    <div class="page-header row no-gutters align-items-center justify-content-between py-3">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <span class="page-subtitle"><i class="material-icons mr-1" style="font-size: 14px; vertical-align: middle;">description</i> Permohonan Surat</span>
            <h3 class="page-title">Daftar Permohonan Masuk</h3>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card card-small mb-4">
                <div class="card-header border-bottom">
                    <h6 class="m-0">Semua Permohonan</h6>
                </div>
                <div class="card-body p-0 pb-3 text-center">
                    <div class="table-responsive">
                        <table class="table mb-0 table-striped">
                            <thead class="bg-light">
                                <tr>
                                    <th scope="col" class="border-0">No</th>
                                    <th scope="col" class="border-0">Kode Registrasi</th>
                                    <th scope="col" class="border-0">Pemohon</th>
                                    <th scope="col" class="border-0">Tanggal</th>
                                    <th scope="col" class="border-0 text-center">Kelengkapan Lahan</th>
                                    <th scope="col" class="border-0">Status</th>
                                    <th scope="col" class="border-0">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($permohonan as $index => $item)
                                @php
                                    $docs = [
                                        'Surat Permohonan' => !empty($item->file_surat_permohonan),
                                        'KTP Pemohon' => !empty($item->file_ktp),
                                        'Petok C / Sertifikat Hak Milik' => !empty($item->file_petok_c),
                                        'SKT Kepala Desa' => !empty($item->file_skt_kades),
                                        'Surat Penguasaan Fisik' => !empty($item->file_penguasaan_fisik),
                                    ];
                                    $docsCount = count(array_filter($docs));
                                    $hasCoord = !empty($item->koordinat);
                                    $hasLuas = !empty($item->luas_lahan);
                                    $hasSpatial = !empty($item->geojson_polygon) || !empty($item->file_shp);
                                    $totalParams = 8;
                                    $validParamsCount = $docsCount + ($hasCoord ? 1 : 0) + ($hasLuas ? 1 : 0) + ($hasSpatial ? 1 : 0);
                                    $isLahanLengkap = ($validParamsCount === $totalParams);
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $item->kode_registrasi }}</strong></td>
                                    <td>{{ $item->nama_pemohon }}<br><small class="text-muted">{{ $item->nik }}</small></td>
                                    <td>{{ $item->created_at->format('d M Y H:i') }}</td>
                                    <td class="text-center">
                                        @if($isLahanLengkap)
                                            <span class="badge badge-pill badge-success" style="font-size: 11px; padding: 4px 8px;">
                                                <i class="fas fa-check-circle mr-1"></i> Lahan Lengkap
                                            </span>
                                        @else
                                            <span class="badge badge-pill badge-warning text-dark" style="font-size: 11px; padding: 4px 8px;">
                                                <i class="fas fa-exclamation-triangle mr-1"></i> Belum Lengkap ({{ $validParamsCount }}/{{ $totalParams }})
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-pill 
                                            @if($item->status == 'Menunggu Verifikasi') badge-warning
                                            @elseif($item->status == 'Diproses') badge-info
                                            @elseif($item->status == 'Selesai') badge-success
                                            @elseif($item->status == 'Ditolak') badge-danger
                                            @else badge-secondary @endif
                                        ">
                                            {{ $item->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#detailModal{{ $item->id }}">
                                            <i class="material-icons">visibility</i> Detail
                                        </button>
                                    </td>
                                </tr>

                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">Belum ada data permohonan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
@foreach($permohonan as $item)
<div class="modal fade text-left" id="detailModal{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Permohonan - {{ $item->kode_registrasi }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Data Pemohon</h6>
                        <table class="table table-sm">
                            <tr><th>Nama</th><td>{{ $item->nama_pemohon }}</td></tr>
                            <tr><th>NIK</th><td>{{ $item->nik }}</td></tr>
                            <tr><th>No. HP</th><td>{{ $item->no_hp }}</td></tr>
                            <tr><th>Alamat</th><td>{{ $item->alamat_pemohon }}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Data Lahan</h6>
                        <table class="table table-sm">
                            <tr><th>Kecamatan</th><td>{{ $item->kecamatan }}</td></tr>
                            <tr><th>Desa</th><td>{{ $item->desa }}</td></tr>
                            <tr><th>Alamat Lahan</th><td>{{ $item->alamat_lahan }}</td></tr>
                            <tr><th>Luas Lahan</th><td>{{ $item->luas_lahan }}</td></tr>
                            <tr><th>Koordinat</th><td>{{ $item->koordinat }}</td></tr>
                            <tr>
                                <th>Zonasi Spasial</th>
                                <td>
                                    @if(!empty($item->koordinat))
                                        <button type="button" class="btn btn-xs btn-outline-success btn-verify-spatial shadow-sm py-1 px-2" 
                                                data-id="{{ $item->id }}" 
                                                data-coord="{{ $item->koordinat }}" 
                                                data-geojson="{{ htmlspecialchars($item->geojson_polygon ?? '') }}"
                                                data-nama="{{ $item->nama_pemohon }}"
                                                style="border-radius: 8px; font-weight: 600; font-size: 11.5px;">
                                            <i class="material-icons" style="font-size:14px; vertical-align:text-top;">fact_check</i> Cek Kesesuaian LSD
                                        </button>
                                        <div id="spatial-result-{{ $item->id }}" class="mt-2" style="display:none;"></div>
                                    @else
                                        <span class="text-muted" style="font-size: 12px;">Koordinat belum diisi</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <hr>
                @php
                    $docs = [
                        'Surat Permohonan' => !empty($item->file_surat_permohonan),
                        'KTP Pemohon' => !empty($item->file_ktp),
                        'Petok C / Sertifikat Hak Milik' => !empty($item->file_petok_c),
                        'SKT Kepala Desa' => !empty($item->file_skt_kades),
                        'Surat Penguasaan Fisik' => !empty($item->file_penguasaan_fisik),
                    ];
                    $docsCount = count(array_filter($docs));
                    $hasCoord = !empty($item->koordinat);
                    $hasLuas = !empty($item->luas_lahan);
                    $hasSpatial = !empty($item->geojson_polygon) || !empty($item->file_shp);
                    $totalParams = 8;
                    $validParamsCount = $docsCount + ($hasCoord ? 1 : 0) + ($hasLuas ? 1 : 0) + ($hasSpatial ? 1 : 0);
                    $isLahanLengkap = ($validParamsCount === $totalParams);

                    $missingList = [];
                    if(!$item->file_surat_permohonan) $missingList[] = 'Surat Permohonan';
                    if(!$item->file_ktp) $missingList[] = 'KTP Pemohon';
                    if(!$item->file_petok_c) $missingList[] = 'Petok C / Sertifikat Hak Milik';
                    if(!$item->file_skt_kades) $missingList[] = 'SKT Kepala Desa';
                    if(!$item->file_penguasaan_fisik) $missingList[] = 'Surat Penguasaan Fisik';
                    if(!$hasCoord) $missingList[] = 'Titik Koordinat Lahan';
                    if(!$hasLuas) $missingList[] = 'Luas Lahan';
                    $missingText = "Kelengkapan berkas/data lahan belum lengkap. Mohon pemohon melengkapi dokumen: " . implode(', ', $missingList) . ".";
                @endphp

                <!-- Panel Cek Kelengkapan Lahan Lengkap -->
                <div class="card border mb-3" style="border-radius: 10px; overflow: hidden; border-color: #cbd5e1 !important;">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2 px-3 border-bottom">
                        <h6 class="m-0 font-weight-bold" style="font-size: 13px; color: #0f172a;">
                            <i class="fas fa-clipboard-check text-success mr-1"></i> Cek Kelengkapan Lahan & Berkas Pemohon
                        </h6>
                        @if($isLahanLengkap)
                            <span class="badge badge-success px-2 py-1" style="font-size: 11px;">
                                <i class="fas fa-check-double mr-1"></i> Lahan Lengkap (100%)
                            </span>
                        @else
                            <span class="badge badge-warning text-dark px-2 py-1" style="font-size: 11px;">
                                <i class="fas fa-exclamation-circle mr-1"></i> Terpenuhi {{ $validParamsCount }}/{{ $totalParams }} Parameter
                            </span>
                        @endif
                    </div>
                    <div class="card-body p-2 p-md-3">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0" style="font-size: 12px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 35px;" class="text-center">No</th>
                                        <th>Item Pemeriksaan Kelengkapan</th>
                                        <th style="width: 130px;" class="text-center">Status</th>
                                        <th style="width: 120px;" class="text-center">Berkas / Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center">1</td>
                                        <td><strong>Surat Permohonan Resmi</strong> (Tanda tangan basah/digital)</td>
                                        <td class="text-center">
                                            @if($item->file_surat_permohonan)
                                                <span class="badge badge-success"><i class="fas fa-check mr-1"></i> Tersedia</span>
                                            @else
                                                <span class="badge badge-danger"><i class="fas fa-times mr-1"></i> Belum Ada</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($item->file_surat_permohonan)
                                                <a href="{{ asset($item->file_surat_permohonan) }}" target="_blank" class="btn btn-xs btn-outline-primary py-0 px-2" style="font-size: 11px;">Lihat Berkas</a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">2</td>
                                        <td><strong>KTP Pemohon</strong> (NIK: {{ $item->nik }})</td>
                                        <td class="text-center">
                                            @if($item->file_ktp)
                                                <span class="badge badge-success"><i class="fas fa-check mr-1"></i> Tersedia</span>
                                            @else
                                                <span class="badge badge-danger"><i class="fas fa-times mr-1"></i> Belum Ada</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($item->file_ktp)
                                                <a href="{{ asset($item->file_ktp) }}" target="_blank" class="btn btn-xs btn-outline-primary py-0 px-2" style="font-size: 11px;">Lihat Berkas</a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">3</td>
                                        <td><strong>Petok C / Sertifikat Hak Milik</strong> (Legalitas Kepemilikan)</td>
                                        <td class="text-center">
                                            @if($item->file_petok_c)
                                                <span class="badge badge-success"><i class="fas fa-check mr-1"></i> Tersedia</span>
                                            @else
                                                <span class="badge badge-danger"><i class="fas fa-times mr-1"></i> Belum Ada</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($item->file_petok_c)
                                                <a href="{{ asset($item->file_petok_c) }}" target="_blank" class="btn btn-xs btn-outline-primary py-0 px-2" style="font-size: 11px;">Lihat Berkas</a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">4</td>
                                        <td><strong>SKT Kepala Desa</strong> (Surat Keterangan Tanah)</td>
                                        <td class="text-center">
                                            @if($item->file_skt_kades)
                                                <span class="badge badge-success"><i class="fas fa-check mr-1"></i> Tersedia</span>
                                            @else
                                                <span class="badge badge-danger"><i class="fas fa-times mr-1"></i> Belum Ada</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($item->file_skt_kades)
                                                <a href="{{ asset($item->file_skt_kades) }}" target="_blank" class="btn btn-xs btn-outline-primary py-0 px-2" style="font-size: 11px;">Lihat Berkas</a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">5</td>
                                        <td><strong>Surat Pernyataan Penguasaan Fisik</strong></td>
                                        <td class="text-center">
                                            @if($item->file_penguasaan_fisik)
                                                <span class="badge badge-success"><i class="fas fa-check mr-1"></i> Tersedia</span>
                                            @else
                                                <span class="badge badge-danger"><i class="fas fa-times mr-1"></i> Belum Ada</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($item->file_penguasaan_fisik)
                                                <a href="{{ asset($item->file_penguasaan_fisik) }}" target="_blank" class="btn btn-xs btn-outline-primary py-0 px-2" style="font-size: 11px;">Lihat Berkas</a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">6</td>
                                        <td><strong>Titik Koordinat Lahan</strong> (Latitude, Longitude)</td>
                                        <td class="text-center">
                                            @if($hasCoord)
                                                <span class="badge badge-success"><i class="fas fa-check mr-1"></i> Valid</span>
                                            @else
                                                <span class="badge badge-danger"><i class="fas fa-times mr-1"></i> Belum Ada</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="text-muted" style="font-size: 11px;">{{ $item->koordinat ?: '-' }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">7</td>
                                        <td><strong>Luas & Detail Lokasi Lahan</strong></td>
                                        <td class="text-center">
                                            @if($hasLuas)
                                                <span class="badge badge-success"><i class="fas fa-check mr-1"></i> Lengkap</span>
                                            @else
                                                <span class="badge badge-warning text-dark"><i class="fas fa-minus mr-1"></i> Belum Ada</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="text-muted" style="font-size: 11px;">{{ $item->luas_lahan ?: '-' }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">8</td>
                                        <td><strong>Batas Polygon Spasial / SHP</strong></td>
                                        <td class="text-center">
                                            @if($hasSpatial)
                                                <span class="badge badge-success"><i class="fas fa-check mr-1"></i> Terdata</span>
                                            @else
                                                <span class="badge badge-secondary"><i class="fas fa-minus mr-1"></i> Opsional</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($item->file_shp)
                                                <a href="{{ asset($item->file_shp) }}" target="_blank" class="btn btn-xs btn-outline-info py-0 px-2" style="font-size: 11px;">Unduh SHP</a>
                                            @elseif($item->geojson_polygon)
                                                <span class="badge badge-info" style="font-size: 10px;">GeoJSON Polygon</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        @if(!$isLahanLengkap)
                            <div class="mt-2 d-flex flex-wrap justify-content-between align-items-center p-2 rounded" style="background: #fffbeb; border: 1px solid #fde68a;">
                                <div class="text-warning text-dark mr-2 mb-1 mb-md-0" style="font-size: 11.5px;">
                                    <i class="fas fa-exclamation-triangle mr-1 text-warning"></i> Berkas belum lengkap: <strong>{{ implode(', ', $missingList) }}</strong>
                                </div>
                                <button type="button" class="btn btn-xs btn-outline-warning text-dark font-weight-bold btn-copy-missing py-1 px-2" 
                                        data-target-input="#catatan-admin-{{ $item->id }}" 
                                        data-text="{{ $missingText }}"
                                        style="font-size: 11px; border-radius: 6px;">
                                    <i class="fas fa-copy mr-1"></i> Salin ke Catatan Admin
                                </button>
                            </div>
                        @else
                            <div class="mt-2 p-2 rounded text-success" style="background: #ecfdf5; border: 1px solid #a7f3d0; font-size: 11.5px;">
                                <i class="fas fa-check-circle mr-1"></i> <strong>Lahan Lengkap:</strong> Seluruh berkas persyaratan dan data spasial telah terpenuhi secara lengkap.
                            </div>
                        @endif
                    </div>
                </div>

                <h6>Update Status</h6>
                <form action="{{ route('dashboard.permohonan.status', $item->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label>Status Permohonan</label>
                        <select name="status" class="form-control">
                            <option value="Menunggu Verifikasi" {{ $item->status == 'Menunggu Verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                            <option value="Diproses" {{ $item->status == 'Diproses' ? 'selected' : '' }}>Diproses</option>
                            <option value="Selesai" {{ $item->status == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="Ditolak" {{ $item->status == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Catatan Admin</label>
                        <textarea name="catatan_admin" id="catatan-admin-{{ $item->id }}" class="form-control" rows="3">{{ $item->catatan_admin }}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Upload Surat Balasan (Opsional)</label>
                        @if($item->file_surat_balasan)
                            <div class="mb-2">
                                <a href="{{ asset($item->file_surat_balasan) }}" target="_blank" class="badge badge-success"><i class="material-icons" style="font-size:12px;">check_circle</i> Surat Balasan Tersedia (Lihat)</a>
                            </div>
                        @endif
                        <input type="file" name="file_surat_balasan" class="form-control-file" accept=".pdf,.doc,.docx,.jpg,.png">
                        <small class="text-muted">Upload file surat balasan resmi untuk diunduh oleh pengguna (PDF/Word/Image).</small>
                    </div>
                    <button type="submit" class="btn btn-success">Simpan Status</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Modal Peta Overlay Telaah Permohonan -->
<div class="modal fade" id="modalOverlayPermohonan" tabindex="-1" role="dialog" aria-labelledby="modalOverlayPermohonanLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden; border: none; box-shadow: 0 15px 35px rgba(0,0,0,0.25);">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); padding: 16px 20px;">
                <h5 class="modal-title font-weight-bold text-white" id="modalOverlayPermohonanLabel">
                    <i class="fas fa-layer-group mr-2"></i> Peta Telaah Kesesuaian Lahan Pemohon vs LSD
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0 position-relative">
                <div id="map-permohonan-overlay" style="height: 440px; width: 100%;"></div>
                <div style="position: absolute; bottom: 15px; left: 15px; z-index: 1000; background: rgba(255,255,255,0.95); padding: 8px 14px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); font-size: 11.5px;">
                    <div class="d-flex align-items-center mb-1">
                        <span style="display:inline-block; width:14px; height:14px; background:#3b82f6; border:2px solid #1d4ed8; border-radius:3px; margin-right:6px;"></span>
                        <strong>Lahan Dimohonkan Pemohon</strong>
                    </div>
                    <div class="d-flex align-items-center">
                        <span style="display:inline-block; width:14px; height:14px; background:#f59e0b; border:2px solid #d97706; border-radius:3px; margin-right:6px;"></span>
                        <strong>Batas Kawasan LSD BPN</strong>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light py-2 px-3">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    let permohonanMapInstance = null;
    let permohonanLayerGroup = null;

    $(document).ready(function () {
        // Tombol Verifikasi Spasial Otomatis
        $('.btn-verify-spatial').on('click', function () {
            let btn = $(this);
            let id = btn.data('id');
            let coordStr = btn.data('coord');
            let geojsonRaw = btn.data('geojson');
            let resultContainer = $('#spatial-result-' + id);

            if (!coordStr) {
                resultContainer.show().html('<div class="alert alert-warning py-1 px-2 mb-0" style="font-size:12px;">Koordinat tidak ditemukan.</div>');
                return;
            }

            // Parsing koordinat (misal "-8.2155, 114.3655" atau "114.3655, -8.2155")
            let parts = coordStr.split(',');
            if (parts.length < 2) {
                resultContainer.show().html('<div class="alert alert-warning py-1 px-2 mb-0" style="font-size:12px;">Format koordinat tidak valid (harus Lat, Lng).</div>');
                return;
            }

            let num1 = parseFloat(parts[0].trim());
            let num2 = parseFloat(parts[1].trim());
            let lat = (Math.abs(num1) <= 90 && num1 < 0) ? num1 : num2;
            let lng = (lat === num1) ? num2 : num1;

            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Memverifikasi...');
            resultContainer.show().html('<small class="text-muted"><i class="fas fa-spinner fa-spin mr-1"></i> Menghubungkan ke basis data spasial BPN...</small>');

            $.getJSON('/api/geometri/check-location?lat=' + lat + '&lng=' + lng)
                .done(function (res) {
                    btn.prop('disabled', false).html('<i class="material-icons" style="font-size:14px; vertical-align:text-top;">fact_check</i> Cek Ulang');

                    if (res && res.found && res.data) {
                        let item = res.data;
                        let kat = item.kategori || {};
                        let info = kat.info_utama || {};
                        let tani = kat.pertanian_irigasi || {};
                        let ruang = kat.tata_ruang || {};

                        let html = `
                            <div class="alert alert-warning p-2 mb-1 shadow-sm" style="font-size: 11.5px; border-left: 4px solid #f59e0b; border-radius: 6px;">
                                <div class="font-weight-bold text-dark mb-1">
                                    <i class="fas fa-exclamation-triangle text-warning mr-1"></i> <strong>TERMASUK KAWASAN ${item.tipe_nama}</strong>
                                </div>
                                <div><strong>Status:</strong> ${info.status_lsd || 'LSD'}</div>
                                <div><strong>Luas Kawasan:</strong> ${info.luas ? Number(info.luas).toLocaleString('id-ID') + ' m²' : '-'}</div>
                                <div><strong>Jaringan Irigasi:</strong> ${tani.irigasi || '-'} (${tani.kewenangan || '-'})</div>
                                <div><strong>Pola Ruang RTRW:</strong> ${ruang.pola_ruang || '-'}</div>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-xs btn-warning text-dark font-weight-bold btn-show-overlay py-1 px-2 shadow-sm"
                                            data-applicant-geojson="${encodeURIComponent(geojsonRaw || '')}"
                                            data-lsd-geojson="${encodeURIComponent(JSON.stringify(item.geojson || null))}"
                                            data-lat="${lat}" data-lng="${lng}">
                                        <i class="fas fa-map-marked-alt mr-1"></i> Buka Peta Overlay Telaah
                                    </button>
                                </div>
                            </div>
                        `;
                        resultContainer.html(html);
                    } else {
                        let html = `
                            <div class="alert alert-success p-2 mb-1 shadow-sm" style="font-size: 11.5px; border-left: 4px solid #10b981; border-radius: 6px;">
                                <div class="font-weight-bold text-success mb-1">
                                    <i class="fas fa-check-circle mr-1"></i> <strong>BEBAS DARI KAWASAN LSD / LP2B</strong>
                                </div>
                                <div>Titik koordinat (${lat.toFixed(6)}, ${lng.toFixed(6)}) berada di <strong>LUAR</strong> batas zonasi Lahan Sawah Dilindungi BPN.</div>
                                ${geojsonRaw ? `
                                <div class="mt-2">
                                    <button type="button" class="btn btn-xs btn-outline-success font-weight-bold btn-show-overlay py-1 px-2 shadow-sm"
                                            data-applicant-geojson="${encodeURIComponent(geojsonRaw || '')}"
                                            data-lsd-geojson=""
                                            data-lat="${lat}" data-lng="${lng}">
                                        <i class="fas fa-map-marked-alt mr-1"></i> Buka Peta Lokasi Pemohon
                                    </button>
                                </div>` : ''}
                            </div>
                        `;
                        resultContainer.html(html);
                    }
                })
                .fail(function (xhr) {
                    btn.prop('disabled', false).html('<i class="material-icons" style="font-size:14px; vertical-align:text-top;">fact_check</i> Coba Lagi');
                    resultContainer.html('<div class="alert alert-danger py-1 px-2 mb-0" style="font-size:12px;">Gagal memverifikasi ke server spasial.</div>');
                });
        });

        // Tampilkan Modal Peta Overlay
        $(document).on('click', '.btn-show-overlay', function () {
            let appGeojsonEnc = $(this).attr('data-applicant-geojson');
            let lsdGeojsonEnc = $(this).attr('data-lsd-geojson');
            let lat = parseFloat($(this).attr('data-lat'));
            let lng = parseFloat($(this).attr('data-lng'));

            $('#modalOverlayPermohonan').modal('show');

            setTimeout(function () {
                if (!permohonanMapInstance) {
                    permohonanMapInstance = L.map('map-permohonan-overlay').setView([lat, lng], 16);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '© OpenStreetMap'
                    }).addTo(permohonanMapInstance);
                    permohonanLayerGroup = L.layerGroup().addTo(permohonanMapInstance);
                } else {
                    permohonanMapInstance.invalidateSize();
                    permohonanLayerGroup.clearLayers();
                    permohonanMapInstance.setView([lat, lng], 16);
                }

                let bounds = null;

                // 1. Gambar layer LSD BPN (Warna Emas / Kuning Oranye)
                if (lsdGeojsonEnc) {
                    try {
                        let lsdGeojson = JSON.parse(decodeURIComponent(lsdGeojsonEnc));
                        if (lsdGeojson) {
                            let lsdLayer = L.geoJSON(lsdGeojson, {
                                style: {
                                    color: '#d97706',
                                    weight: 2.5,
                                    fillColor: '#f59e0b',
                                    fillOpacity: 0.45
                                }
                            }).addTo(permohonanLayerGroup);
                            bounds = lsdLayer.getBounds();
                        }
                    } catch (e) {
                        console.error('Gagal parse LSD GeoJSON:', e);
                    }
                }

                // 2. Gambar layer Pemohon (Warna Hijau Zamrud Tegas)
                if (appGeojsonEnc) {
                    try {
                        let appGeojson = JSON.parse(decodeURIComponent(appGeojsonEnc));
                        if (appGeojson) {
                            let appLayer = null;
                            if (Array.isArray(appGeojson) && appGeojson.length > 0 && typeof appGeojson[0][0] === 'number') {
                                appLayer = L.polygon(appGeojson, {
                                    color: '#047857',
                                    weight: 3.5,
                                    fillColor: '#10b981',
                                    fillOpacity: 0.45
                                }).addTo(permohonanLayerGroup);
                            } else {
                                appLayer = L.geoJSON(appGeojson, {
                                    style: {
                                        color: '#047857',
                                        weight: 3.5,
                                        fillColor: '#10b981',
                                        fillOpacity: 0.45
                                    }
                                }).addTo(permohonanLayerGroup);
                            }

                            if (appLayer) {
                                if (bounds) {
                                    bounds.extend(appLayer.getBounds());
                                } else {
                                    bounds = appLayer.getBounds();
                                }
                            }
                        }
                    } catch (e) {
                        console.error('Gagal parse Applicant GeoJSON:', e);
                    }
                }

                // Marker titik permohonan
                L.marker([lat, lng], { title: 'Titik Lokasi Pemohon' }).addTo(permohonanLayerGroup);

                if (bounds) {
                    permohonanMapInstance.fitBounds(bounds, { padding: [40, 40] });
                }
            }, 350);
        });

        // Handler Salin Catatan Berkas Kurang ke Textarea Catatan Admin
        $(document).on('click', '.btn-copy-missing', function() {
            let targetSelector = $(this).data('target-input');
            let text = $(this).data('text');
            if (targetSelector && text) {
                let input = $(targetSelector);
                let currentVal = input.val().trim();
                if (currentVal && !currentVal.includes(text)) {
                    input.val(currentVal + '\n' + text);
                } else {
                    input.val(text);
                }
                $(this).html('<i class="fas fa-check mr-1"></i> Tersalin!').removeClass('btn-outline-warning').addClass('btn-success');
                setTimeout(() => {
                    $(this).html('<i class="fas fa-copy mr-1"></i> Salin ke Catatan Admin').removeClass('btn-success').addClass('btn-outline-warning');
                }, 2000);
            }
        });
    });
</script>
@endsection
