@extends('dashboard.template')
@section('title', 'Geometri')

@include('dashboard.js.main')

@section('content')
<div class="main-content-container container-fluid px-4">
    <div class="page-header row no-gutters ms-2 py-4">
        <div class="col-12 col-sm-4 text-sm-left mb-0">
          <span class="text-uppercase page-subtitle">Dashboard</span>
          <h3 class="page-title">Data LSD</h3>
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
<div class="card-body">
    <div class="table-responsive" id="tab">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center">
                <button type="button" class="btn btn-silila-sunrise shadow-sm mr-2" data-bs-toggle="modal" data-bs-target="#add-lsd">
                    <i class="material-icons mr-1" style="font-size: 18px; vertical-align: text-top;">add_circle</i> Tambah Data LSD
                </button>
            </div>
            <span class="text-muted" style="font-size: 13px;">Total: <strong>{{ number_format($lsd->total()) }}</strong> bidang lahan</span>
        </div>
        @include('dashboard.lsd.add-lsd')
        <table class="table table-striped table-hover" id="table-2">
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th>ID / Object ID</th>
                    <th>Desa / Kecamatan</th>
                    <th>Status LSD</th>
                    <th>Irigasi</th>
                    <th>Pola Ruang</th>
                    <th>Luas (m²)</th>
                    <th>Peta</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lsd as $data)
                @php
                    $geomJson = optional($data->geometri)->koordinat_geojson;
                    $desaNama = ($data->geometri && $data->geometri->desa) ? $data->geometri->desa->nama : '-';
                    $kecNama = ($data->geometri && $data->geometri->desa && $data->geometri->desa->kecamatan) ? $data->geometri->desa->kecamatan->nama : '-';

                    // Format luas lahan selalu dalam meter persegi (m²)
                    $luasRaw = $data->luas;
                    $luasDisplay = '-';
                    $luasText = '-';
                    if ($luasRaw !== null && $luasRaw !== '') {
                        if (is_numeric($luasRaw)) {
                            $num = (float)$luasRaw;
                            $formattedNum = number_format($num, 2, ',', '.');
                            $luasDisplay = $formattedNum . ' m²';
                            $luasText = $formattedNum . ' m²';
                        } else {
                            $luasDisplay = e($luasRaw);
                            $luasText = $luasRaw;
                        }
                    }
                @endphp
                <tr>
                    <td class="text-center">{{ $loop->iteration + ($lsd->currentPage() - 1) * $lsd->perPage() }}</td>
                    <td>
                        <strong>#{{ $data->geometri_id }}</strong>
                        @if($data->geometri && $data->geometri->object_id)
                            <br><small class="text-muted">OID: {{ $data->geometri->object_id }}</small>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $desaNama }}</strong>
                        <br><small class="text-muted">{{ $kecNama }}</small>
                    </td>
                    <td>
                        <span class="badge badge-warning" style="background: #fef3c7; color: #b45309; font-weight: 700;">
                            {{ $data->status_lsd ?: 'LSD' }}
                        </span>
                    </td>
                    <td>{{ $data->irigasi ?: '-' }}</td>
                    <td><small>{{ Str::limit($data->polru ?: '-', 35) }}</small></td>
                    <td><div style="font-size: 13px; font-weight: 700;">{!! $luasDisplay !!}</div></td>
                    <td>
                        @if(!empty($geomJson))
                            <button type="button" class="btn btn-sm btn-outline-info btn-preview-map shadow-sm" 
                                    data-geojson="{{ base64_encode($geomJson) }}"
                                    data-title="LSD #{{ $data->geometri_id }} - Desa {{ $desaNama }}"
                                    data-luas="{{ $luasText }}"
                                    data-status="{{ $data->status_lsd ?: 'LSD' }}"
                                    data-kecamatan="{{ $kecNama }}"
                                    data-desa="{{ $desaNama }}"
                                    title="Pratinjau Bidang di Peta">
                                <i class="fas fa-map-marked-alt mr-1"></i> Peta
                            </button>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        <a href="#" class="edit-button text-primary mr-2" data-bs-toggle="modal" data-bs-target="#edit-lsd--{{$data->id}}" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="{{ route('delete-lsd', $data->id) }}" onclick="return confirm('Apakah Anda yakin ingin menghapus data LSD ini?')" class="text-danger" title="Hapus">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
                @foreach ($lsd as $data)
                @include('dashboard.lsd.edit-lsd')
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-between align-items-center mt-4">
            <span class="text-muted" style="font-size: 13px;">Halaman {{ $lsd->currentPage() }} dari {{ $lsd->lastPage() }}</span>
            <div>
                {{ $lsd->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>

    <!-- Modal Pratinjau Peta Per Bidang -->
    <div class="modal fade" id="mapPreviewModal" tabindex="-1" role="dialog" aria-labelledby="mapPreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius: 16px; overflow: hidden; border: none; box-shadow: 0 15px 35px rgba(0,0,0,0.2);">
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
                    <h5 class="modal-title font-weight-bold" id="mapPreviewModalLabel">
                        <i class="fas fa-map-marked-alt mr-2"></i> Pratinjau Bidang Lahan LSD
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="opacity: 0.9;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div id="mini-map-preview" style="height: 440px; width: 100%;"></div>
                </div>
                <div class="modal-footer bg-light d-flex justify-content-between">
                    <small class="text-muted"><i class="fas fa-info-circle mr-1"></i> Gunakan tombol kontrol lapisan di kanan atas untuk beralih ke citra Satelit.</small>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
<script>
    let previewMapInstance = null;
    let previewLayerGroup = null;
    let currentPreviewData = null;

    $(document).ready(function () {
        $(document).on('click', '.btn-preview-map', function (e) {
            e.preventDefault();
            let rawData = $(this).attr('data-geojson');
            let title = $(this).attr('data-title');
            let luas = $(this).attr('data-luas') || '-';
            let status = $(this).attr('data-status') || 'LSD';
            let desa = $(this).attr('data-desa') || '-';
            let kecamatan = $(this).attr('data-kecamatan') || '-';

            if (!rawData) return;

            let parsedGeojson = null;
            try {
                // Decode base64 jika bukan JSON string mentah
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

            currentPreviewData = {
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

        // Gambar peta saat modal benar-benar sudah terbuka (shown.bs.modal)
        $('#mapPreviewModal').on('shown.bs.modal shown', function () {
            if (!currentPreviewData || !currentPreviewData.geojson) return;

            // Inisialisasi peta sekali
            if (!previewMapInstance) {
                let osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap'
                });
                let satelit = L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
                    maxZoom: 20,
                    attribution: '© Google Satellite'
                });

                previewMapInstance = L.map('mini-map-preview', {
                    layers: [osm]
                }).setView([-8.36667, 114.16667], 13);

                L.control.layers({
                    'OpenStreetMap': osm,
                    'Satelit Google': satelit
                }, null, { position: 'topright' }).addTo(previewMapInstance);

                previewLayerGroup = L.layerGroup().addTo(previewMapInstance);
            }

            previewMapInstance.invalidateSize();
            previewLayerGroup.clearLayers();

            try {
                let poly = L.geoJSON(currentPreviewData.geojson, {
                    style: {
                        color: '#b45309',
                        weight: 3.5,
                        opacity: 1,
                        fillColor: '#f59e0b',
                        fillOpacity: 0.65
                    }
                }).addTo(previewLayerGroup);

                let bounds = poly.getBounds();
                if (bounds.isValid()) {
                    previewMapInstance.fitBounds(bounds, { maxZoom: 18, padding: [60, 60] });

                    let center = bounds.getCenter();
                    let centerMarker = L.circleMarker(center, {
                        radius: 8,
                        fillColor: '#ef4444',
                        color: '#ffffff',
                        weight: 2.5,
                        opacity: 1,
                        fillOpacity: 0.95
                    }).addTo(previewLayerGroup);

                    let popupContent = `
                        <div style="font-family: inherit; font-size: 12.5px; line-height: 1.5; min-width: 180px;">
                            <strong style="color: #b45309; font-size: 13px;">${currentPreviewData.title}</strong><br>
                            <span class="badge badge-warning text-dark my-1" style="font-size: 11px;">${currentPreviewData.status}</span><br>
                            <b>Luas:</b> ${currentPreviewData.luas}<br>
                            <b>Wilayah:</b> ${currentPreviewData.desa}, ${currentPreviewData.kecamatan}<br>
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
