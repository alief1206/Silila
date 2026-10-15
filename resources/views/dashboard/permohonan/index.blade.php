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
                                    <th scope="col" class="border-0">Status</th>
                                    <th scope="col" class="border-0">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($permohonan as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->kode_registrasi }}</td>
                                    <td>{{ $item->nama_pemohon }}<br><small>{{ $item->nik }}</small></td>
                                    <td>{{ $item->created_at->format('d M Y H:i') }}</td>
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
                <h6>Dokumen Pendukung</h6>
                <ul>
                    @if($item->file_surat_permohonan)
                    <li>Surat Permohonan: <a href="{{ asset($item->file_surat_permohonan) }}" target="_blank">Lihat Dokumen</a></li>
                    @endif
                    @if($item->file_ktp)
                    <li>KTP: <a href="{{ asset($item->file_ktp) }}" target="_blank">Lihat Dokumen</a></li>
                    @endif
                    @if($item->file_petok_c)
                    <li>Petok C: <a href="{{ asset($item->file_petok_c) }}" target="_blank">Lihat Dokumen</a></li>
                    @endif
                    @if($item->file_skt_kades)
                    <li>SKT Kades: <a href="{{ asset($item->file_skt_kades) }}" target="_blank">Lihat Dokumen</a></li>
                    @endif
                    @if($item->file_penguasaan_fisik)
                    <li>Penguasaan Fisik: <a href="{{ asset($item->file_penguasaan_fisik) }}" target="_blank">Lihat Dokumen</a></li>
                    @endif
                    @if($item->file_shp)
                    <li>File SHP/GeoJSON: <a href="{{ asset($item->file_shp) }}" target="_blank">Lihat Dokumen</a></li>
                    @endif
                </ul>

                <hr>
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
                        <textarea name="catatan_admin" class="form-control" rows="3">{{ $item->catatan_admin }}</textarea>
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

                // 2. Gambar layer Pemohon (Warna Biru Kontras)
                if (appGeojsonEnc) {
                    try {
                        let appGeojson = JSON.parse(decodeURIComponent(appGeojsonEnc));
                        if (appGeojson) {
                            let appLayer = L.geoJSON(appGeojson, {
                                style: {
                                    color: '#1d4ed8',
                                    weight: 3,
                                    fillColor: '#3b82f6',
                                    fillOpacity: 0.65
                                }
                            }).addTo(permohonanLayerGroup);

                            if (bounds) {
                                bounds.extend(appLayer.getBounds());
                            } else {
                                bounds = appLayer.getBounds();
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
    });
</script>
@endsection
