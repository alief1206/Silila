@extends('dashboard.template')
@section('title', 'Data LP2B (Lahan Pertanian Berkelanjutan)')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@include('dashboard.js.main')

@section('content')
<div class="main-content-container container-fluid px-4">
    <!-- Page Header -->
    <div class="page-header row no-gutters ms-2 py-4">
        <div class="col-12 col-sm-4 text-sm-left mb-0">
            <span class="text-uppercase page-subtitle">Dashboard</span>
            <h3 class="page-title">Data LP2B</h3>
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
    @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    @endif
    @if($errors->any())
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
          @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    @endif

    <div class="card card-small mb-4">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <div>
                <button type="button" class="btn btn-silila-emerald shadow-sm" data-toggle="modal" data-target="#add-lp2b" data-bs-toggle="modal" data-bs-target="#add-lp2b">
                    <i class="material-icons mr-1" style="font-size: 18px; vertical-align: text-top;">add_circle</i> Tambah Data LP2B
                </button>
            </div>
            <span class="text-muted" style="font-size: 13px;">Total: <strong>{{ number_format($lp2b->total()) }}</strong> bidang lahan</span>
        </div>

        <div class="card-body p-0 pb-3">
            @include('dashboard.lp2b.add-lp2b')
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0" id="table-lp2b">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>ID / FID</th>
                            <th>Desa / Kecamatan</th>
                            <th>KP2B</th>
                            <th>Keterangan</th>
                            <th>Luas (m²)</th>
                            <th class="text-center">Peta</th>
                            <th class="text-center" style="width: 100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($lp2b as $data)
                        @php
                            $geomJson = optional($data->geometri)->koordinat_geojson;
                            $desaNama = ($data->geometri && $data->geometri->desa) ? $data->geometri->desa->nama : '-';
                            $kecNama  = ($data->geometri && $data->geometri->desa && $data->geometri->desa->kecamatan) ? $data->geometri->desa->kecamatan->nama : '-';
                        @endphp
                        <tr>
                            <td class="text-center">{{ $loop->iteration + ($lp2b->currentPage() - 1) * $lp2b->perPage() }}</td>
                            <td>
                                <strong>#{{ $data->geometri_id }}</strong>
                                @if($data->geometri && $data->geometri->object_id)
                                    <br><small class="text-muted">FID: {{ $data->geometri->object_id }}</small>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $desaNama }}</strong>
                                @if($kecNama !== '-')
                                    <br><small class="text-muted">Kec. {{ $kecNama }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-success" style="font-size: 11px; font-weight: 600;">{{ $data->kp2b }}</span>
                            </td>
                            <td>{{ $data->ket ?: '-' }}</td>
                            <td>
                                <strong>{{ is_numeric($data->luas) ? number_format((float)$data->luas, 2, ',', '.') . ' m²' : ($data->luas ?: '-') }}</strong>
                            </td>
                            <td class="text-center">
                                @if(!empty($geomJson))
                                    <button type="button" class="btn btn-xs btn-outline-success btn-preview-map py-1 px-2"
                                        data-title="LP2B #{{ $data->geometri_id }} ({{ $data->kp2b }})"
                                        data-status="{{ $data->kp2b }}"
                                        data-desa="{{ $desaNama }}"
                                        data-kecamatan="{{ $kecNama }}"
                                        data-luas="{{ is_numeric($data->luas) ? number_format((float)$data->luas, 2, ',', '.') . ' m²' : '-' }}"
                                        data-geojson="{{ $geomJson }}"
                                        title="Pratinjau Poligon Bidang">
                                        <i class="fas fa-map-marked-alt mr-1"></i> Lihat Peta
                                    </button>
                                @else
                                    <span class="text-muted" style="font-size: 11px;">Tidak ada koordinat</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="#" class="edit-button text-primary mr-2" data-bs-toggle="modal" data-bs-target="#edit-lp2b--{{$data->id}}"
                                   data-id="{{ $data->id }}" data-geometri_id="{{ $data->geometri_id }}"
                                   data-desa_id="{{ $data->desa_id }}" data-kp2b="{{ $data->kp2b}}" data-ket="{{ $data->ket}}" data-luas="{{ $data->luas}}" data-koordinat="{{ $data->koordinat}}" data-tipe="{{ $data->tipe}}"
                                   title="Edit Data">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('delete-lp2b', $data->id) }}" onclick="return confirm('Apakah Anda yakin ingin menghapus data LP2B ini?')" class="text-danger" title="Hapus Data">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="fas fa-info-circle mr-1"></i> Belum ada data spasial LP2B. Silakan tambah manual atau impor data spasial.
                            </td>
                        </tr>
                        @endforelse

                        @foreach ($lp2b as $data)
                            @include('dashboard.lp2b.edit-lp2b')
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3 px-3">
                <span class="text-muted" style="font-size: 13px;">Halaman {{ $lp2b->currentPage() }} dari {{ $lp2b->lastPage() }}</span>
                <div>
                    {{ $lp2b->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Modal Pratinjau Peta Per Bidang -->
<div class="modal fade" id="mapPreviewModal" tabindex="-1" role="dialog" aria-labelledby="mapPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden; border: none; box-shadow: 0 15px 35px rgba(0,0,0,0.2);">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
                <h5 class="modal-title font-weight-bold" id="mapPreviewModalLabel">
                    <i class="fas fa-map-marked-alt mr-2"></i> Pratinjau Bidang Lahan LP2B
                </h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close" style="opacity: 0.9;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div id="mini-map-preview-lp2b" style="height: 440px; width: 100%;"></div>
            </div>
            <div class="modal-footer bg-light d-flex justify-content-between">
                <small class="text-muted"><i class="fas fa-info-circle mr-1"></i> Gunakan kontrol lapisan di kanan atas untuk beralih ke citra Satelit Google.</small>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
    let previewMapLp2b = null;
    let previewLayerLp2b = null;
    let currentLp2bData = null;

    $(document).ready(function () {


        $(document).on('click', '.btn-preview-map', function (e) {
            e.preventDefault();
            let rawData = $(this).attr('data-geojson');
            let title = $(this).attr('data-title');
            let luas = $(this).attr('data-luas') || '-';
            let status = $(this).attr('data-status') || 'LP2B';
            let desa = $(this).attr('data-desa') || '-';
            let kecamatan = $(this).attr('data-kecamatan') || '-';

            if (!rawData) return;

            let parsedGeojson = null;
            try {
                if (!rawData.trim().startsWith('{') && !rawData.trim().startsWith('[')) {
                    parsedGeojson = JSON.parse(atob(rawData));
                } else {
                    parsedGeojson = JSON.parse(rawData);
                }
            } catch (err) {
                console.error('Gagal decode GeoJSON:', err);
                alert('Gagal memuat format koordinat poligon.');
                return;
            }

            currentLp2bData = {
                geojson: parsedGeojson,
                title: title,
                luas: luas,
                status: status,
                desa: desa,
                kecamatan: kecamatan
            };

            $('#mapPreviewModalLabel').html('<i class="fas fa-map-marked-alt mr-2"></i> ' + title);
            $('#mapPreviewModal').modal('show');
        });

        $('#mapPreviewModal').on('shown.bs.modal shown', function () {
            if (!currentLp2bData || !currentLp2bData.geojson) return;

            if (!previewMapLp2b) {
                let osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap'
                });
                let satelit = L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
                    maxZoom: 20,
                    attribution: '© Google Satellite'
                });

                previewMapLp2b = L.map('mini-map-preview-lp2b', {
                    layers: [osm]
                }).setView([-8.36667, 114.16667], 13);

                L.control.layers({
                    'OpenStreetMap': osm,
                    'Satelit Google': satelit
                }, null, { position: 'topright' }).addTo(previewMapLp2b);

                previewLayerLp2b = L.layerGroup().addTo(previewMapLp2b);
            }

            previewMapLp2b.invalidateSize();
            previewLayerLp2b.clearLayers();

            try {
                let poly = L.geoJSON(currentLp2bData.geojson, {
                    style: {
                        color: '#047857',
                        weight: 3.5,
                        opacity: 1,
                        fillColor: '#10b981',
                        fillOpacity: 0.65
                    }
                }).addTo(previewLayerLp2b);

                let bounds = poly.getBounds();
                if (bounds.isValid()) {
                    previewMapLp2b.fitBounds(bounds, { maxZoom: 18, padding: [60, 60] });

                    let center = bounds.getCenter();
                    let centerMarker = L.circleMarker(center, {
                        radius: 8,
                        fillColor: '#ef4444',
                        color: '#ffffff',
                        weight: 2.5,
                        opacity: 1,
                        fillOpacity: 0.95
                    }).addTo(previewLayerLp2b);

                    let popupContent = `
                        <div style="font-family: inherit; font-size: 12.5px; line-height: 1.5; min-width: 180px;">
                            <strong style="color: #047857; font-size: 13px;">${currentLp2bData.title}</strong><br>
                            <span class="badge badge-success my-1" style="font-size: 11px;">${currentLp2bData.status}</span><br>
                            <b>Luas:</b> ${currentLp2bData.luas}<br>
                            <b>Wilayah:</b> ${currentLp2bData.desa}, ${currentLp2bData.kecamatan}<br>
                            <small class="text-muted">Titik: ${center.lat.toFixed(6)}, ${center.lng.toFixed(6)}</small>
                        </div>
                    `;
                    centerMarker.bindPopup(popupContent).openPopup();
                }
            } catch (err) {
                console.error('Gagal menampilkan layer poligon di peta:', err);
            }
        });
    });
</script>
@endpush
