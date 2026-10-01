@extends('dashboard.template')
@section('title', 'Dashboard Overview')

@include('dashboard.js.main')

@section('content')

<div class="main-content-container container-fluid px-4 py-3">
    <!-- Page Header -->
    <div class="page-header row no-gutters align-items-center justify-content-between py-3">
      <div class="col-12 col-md-6 mb-2 mb-md-0">
        <span class="page-subtitle"><i class="material-icons mr-1" style="font-size: 14px; vertical-align: middle;">eco</i> SIKP2B & LSD Banyuwangi</span>
        <h3 class="page-title">Ringkasan Spasial & Lahan</h3>
      </div>
      <div class="col-12 col-md-6 text-md-right">
        <span class="badge px-3 py-2" style="background: rgba(16, 185, 129, 0.1); color: #059669; font-weight: 700; border-radius: 30px; font-size: 12px; border: 1px solid rgba(16, 185, 129, 0.2);">
          <i class="fas fa-circle mr-1" style="font-size: 8px; vertical-align: middle; color: #10b981;"></i> Data Terintegrasi Real-Time
        </span>
      </div>
    </div>
    <!-- End Page Header -->

    <div id="alert-message"></div>

    <!-- Small Stats Blocks (Emerald & Sunrise Theme) -->
    <div class="row mb-2">
      <!-- 1. Kecamatan -->
      <div class="col-xl col-lg-4 col-md-6 col-sm-6 mb-4">
        <div class="stats-card-silila theme-teal">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="stats-label">Kecamatan</span>
            <div class="stats-icon-bubble theme-teal mb-0">
              <i class="material-icons">domain</i>
            </div>
          </div>
          <div class="stats-value count jumlah-kecamatan">0</div>
          <small class="text-muted font-weight-bold" style="font-size: 11px;">Tercakup dalam sistem</small>
        </div>
      </div>

      <!-- 2. Desa -->
      <div class="col-xl col-lg-4 col-md-6 col-sm-6 mb-4">
        <div class="stats-card-silila theme-forest">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="stats-label">Desa / Kelurahan</span>
            <div class="stats-icon-bubble theme-forest mb-0">
              <i class="material-icons">holiday_village</i>
            </div>
          </div>
          <div class="stats-value count jumlah-desa">0</div>
          <small class="text-muted font-weight-bold" style="font-size: 11px;">Wilayah terpetakan</small>
        </div>
      </div>

      <!-- 3. KP2B -->
      <div class="col-xl col-lg-4 col-md-6 col-sm-6 mb-4">
        <div class="stats-card-silila theme-emerald">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="stats-label">Wilayah KP2B</span>
            <div class="stats-icon-bubble theme-emerald mb-0">
              <i class="material-icons">grass</i>
            </div>
          </div>
          <div class="stats-value count jumlah-kp2b">0</div>
          <small class="text-muted font-weight-bold" style="font-size: 11px;">Pertanian Pangan Abadi</small>
        </div>
      </div>

      <!-- 4. LSD -->
      <div class="col-xl col-lg-6 col-md-6 col-sm-6 mb-4">
        <div class="stats-card-silila theme-sunrise">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="stats-label">Wilayah LSD</span>
            <div class="stats-icon-bubble theme-sunrise mb-0">
              <i class="material-icons">verified_user</i>
            </div>
          </div>
          <div class="stats-value count jumlah-lsd">0</div>
          <small class="text-muted font-weight-bold" style="font-size: 11px;">Lahan Sawah Dilindungi</small>
        </div>
      </div>

      <!-- 5. LBS -->
      <div class="col-xl col-lg-6 col-md-6 col-sm-6 mb-4">
        <div class="stats-card-silila theme-amber">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="stats-label">Wilayah LBS</span>
            <div class="stats-icon-bubble theme-amber mb-0">
              <i class="material-icons">wb_sunny</i>
            </div>
          </div>
          <div class="stats-value count jumlah-lbs">0</div>
          <small class="text-muted font-weight-bold" style="font-size: 11px;">Lahan Baku Sawah</small>
        </div>
      </div>
    </div>
    <!-- End Small Stats Blocks -->

    <!-- Action Section Bar -->
    <div class="card p-3 mb-4 border-0 shadow-sm" style="border-radius: 16px; background: #ffffff;">
      <div class="d-flex flex-wrap justify-content-between align-items-center">
        <div class="mb-2 mb-md-0">
          <h6 class="m-0 font-weight-bold" style="color: #0f172a; font-size: 15px;">
            <i class="material-icons mr-1" style="font-size: 18px; vertical-align: text-bottom; color: #059669;">cloud_upload</i> Integrasi Data Spasial
          </h6>
          <small class="text-muted">Import peta spasial digital format (.zip, .geojson, .json, .dbf, .shp) ke database SILILA</small>
        </div>
        <div class="d-flex flex-wrap align-items-center">
          <button type="button" class="btn btn-silila-outline mx-1 my-1 disabled" title="Modul LBS segera aktif">
            <i class="material-icons" style="font-size: 18px;">cloud_sync</i> Import LBS
          </button>
          <button id="import-geojson-lsd" type="button" class="btn btn-silila-sunrise mx-1 my-1" data-toggle="modal" data-target="#modalImportLsd">
            <i class="material-icons" style="font-size: 18px;">upload_file</i> Import Data LSD
          </button>
          <button id="import-geojson-lp2b" type="button" class="btn btn-silila-emerald mx-1 my-1" data-toggle="modal" data-target="#modalImportLp2b">
            <i class="material-icons" style="font-size: 18px;">upload_file</i> Import Data LP2B
          </button>
        </div>
      </div>
    </div>

    <!-- Main Content Area: Map & Chart -->
    <div class="row">
      <!-- Users Stats / GIS Map Container -->
      <div class="col-lg-8 col-md-12 col-sm-12 mb-4">
        <div class="card card-small h-100 border-0 shadow-sm">
          <div class="card-header border-bottom py-3">
            <div class="row align-items-center">
                <div class="col">
                    <div class="d-flex align-items-center">
                      <div class="rounded-circle mr-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(16, 185, 129, 0.1); color: #059669;">
                        <i class="material-icons" style="font-size: 18px;">travel_explore</i>
                      </div>
                      <h6 class="m-0 font-weight-bold" style="color: #0f172a;">Peta Sebaran Lahan Banyuwangi</h6>
                    </div>
                </div>
                <div class="col-auto text-right">
                    <div class="d-flex align-items-center">
                      <span class="text-muted font-weight-bold mr-2" style="font-size: 12px;">Filter Layer:</span>
                      <select id="map-filter" class="custom-select-silila" style="min-width: 140px;">
                        <option value="1" selected>🌾 LP2B</option>
                        <option value="2">🛡️ LSD</option>
                        <option value="3">☀️ LBS</option>
                      </select>
                    </div>
                </div>
                <div id="loading-map-indicator" class="col-12 text-center mt-2" style="color: #059669;">
                    <b><i class="fas fa-spinner fa-spin mr-1"></i> Memuat layer spasial...</b>
                </div>
              </div>
          </div>
          <div class="card-body p-3">
            <!-- Filter LP2B Wilayah -->
            <div id="filter-lp2b" class="col-12 row mb-3 d-none align-items-center p-2 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                <div class="form-group col-md-5 mb-2 mb-md-0">
                    <label class="small text-muted font-weight-bold mb-1">Kecamatan:</label>
                    <select name="input-kecamatan" class="form-control" style="width: 100% !important;">
                        <option selected="" value="0">Semua Kecamatan</option>
                    </select>
                </div>
                <div class="form-group col-md-5 mb-2 mb-md-0">
                    <label class="small text-muted font-weight-bold mb-1">Desa:</label>
                    <select name="input-desa" class="form-control" style="width: 100% !important;">
                        <option selected="" value="0">Semua Desa</option>
                    </select>
                </div>
                <div class="col-md-2 text-right mt-md-4">
                    <button class="btn btn-silila-emerald btn-sm w-100 btn-filter-lp2b">
                      <i class="material-icons" style="font-size: 16px;">filter_alt</i> Terapkan
                    </button>
                </div>
            </div>

            <!-- Cari Koordinat Widget -->
            <div id="cari-koordinat-segment" class="col-12 d-none mb-3">
                <form id="cari-koordinat" class="row align-items-center">
                    <div class="form-group col-md-4 mb-2 mb-md-0">
                        <label class="small text-muted font-weight-bold mb-1">Latitude / X:</label>
                        <input type="text" class="form-control" id="latitude" placeholder="Contoh: -8.36667">
                    </div>
                    <div class="form-group col-md-4 mb-2 mb-md-0">
                        <label class="small text-muted font-weight-bold mb-1">Longitude / Y:</label>
                        <input type="text" class="form-control" id="longitude" placeholder="Contoh: 114.16667">
                    </div>
                    <div class="form-group col-md-2 mb-2 mb-md-0">
                        <label class="small text-muted font-weight-bold mb-1">Sistem:</label>
                        <select id="tipe-pencarian" class="form-control">
                            <option value="latlng">LatLng</option>
                            <option value="cea">CEA</option>
                        </select>
                    </div>
                    <div class="col-md-2 text-right mt-md-4">
                        <button type="submit" class="btn btn-silila-sunrise btn-sm w-100 btn-cari-koordinat">
                          <i class="material-icons" style="font-size: 16px;">search</i> Cari
                        </button>
                    </div>
                </form>
            </div>

            <!-- Leaflet Map Container -->
            <div id="map" style="height: 480px; width: 100%;"></div>
          </div>
        </div>
      </div>
      <!-- End Users Stats -->

      <!-- Users By Device / Distribution Chart -->
      <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
        <div class="card card-small h-100 border-0 shadow-sm">
          <div class="card-header border-bottom py-3">
            <div class="d-flex align-items-center">
              <div class="rounded-circle mr-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(245, 158, 11, 0.1); color: #d97706;">
                <i class="material-icons" style="font-size: 18px;">pie_chart</i>
              </div>
              <h6 class="m-0 font-weight-bold" style="color: #0f172a;">Distribusi Lahan</h6>
            </div>
          </div>
          <div class="card-body d-flex flex-column align-items-center justify-content-center p-4">
            <div style="position: relative; width: 100%; height: 340px;">
              <canvas id="myChart"></canvas>
            </div>
            <div class="text-center mt-3">
              <small class="text-muted font-weight-bold">
                <i class="material-icons mr-1" style="font-size: 14px; vertical-align: text-bottom; color: #10b981;">check_circle</i> Proporsi luas per kategori perlindungan lahan
              </small>
            </div>
          </div>
        </div>
      </div>
      <!-- End Chart -->
    </div>
  </div>

  <!-- Modal Import GeoJSON LP2B -->
  <div class="modal fade" id="modalImportLp2b" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                      <div class="rounded-circle mr-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: #ecfdf5; color: #059669;">
                        <i class="material-icons">grass</i>
                      </div>
                      <div>
                        <h5 class="modal-title m-0">Import Data LP2B</h5>
                        <small class="text-muted">Lahan Pertanian Pangan Berkelanjutan (.zip, .geojson, .json, .dbf)</small>
                      </div>
                    </div>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="p-4">
                    <div class="alert--message mb-3"></div>
                    <form action="">
                        <div id="file--upload"></div>
                        <div class="modal-footer px-0 pb-0 pt-3">
                            <button class="btn btn-silila-outline" type="button" data-dismiss="modal">Batal</button>
                            <button class="btn btn-silila-emerald" type="submit">
                              <i class="material-icons mr-1">cloud_upload</i> Mulai Upload
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Import Data LSD -->
    <div class="modal fade" id="modalImportLsd" tabindex="-1" role="dialog" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
              <div class="modal-content">
                  <div class="modal-header d-flex align-items-center justify-content-between">
                      <div class="d-flex align-items-center">
                        <div class="rounded-circle mr-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: #fffbeb; color: #d97706;">
                          <i class="material-icons">verified_user</i>
                        </div>
                        <div>
                          <h5 class="modal-title m-0">Import Data LSD</h5>
                          <small class="text-muted">Lahan Sawah Dilindungi (.zip, .geojson, .json, .dbf)</small>
                        </div>
                      </div>
                      <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                  </div>
                  <div class="p-4">
                      <div class="alert--message mb-3"></div>
                      <form action="">
                          <div id="file--upload-lsd"></div>
                          <div class="modal-footer px-0 pb-0 pt-3">
                              <button class="btn btn-silila-outline" type="button" data-dismiss="modal">Batal</button>
                              <button class="btn btn-silila-sunrise" type="submit">
                                <i class="material-icons mr-1">cloud_upload</i> Mulai Upload
                              </button>
                          </div>
                      </form>
                  </div>
              </div>
          </div>
      </div>

<!-- Modal Import LSD -->
<div class="modal fade" style="z-index: 99999;" id="modalImportLsd" tabindex="-1" aria-labelledby="modalImportLsdLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 15px 35px rgba(0,0,0,0.15);">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%); padding: 18px 24px;">
                <h5 class="modal-title font-weight-bold text-white" id="modalImportLsdLabel">Import Data LSD</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('import.lsd') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-muted" style="font-size: 13px;">Pilih file spasial berformat `.zip`, `.geojson`, `.json`, atau `.dbf` untuk data Lahan Sawah Dilindungi (LSD).</p>
                    <div class="mb-3">
                        <input type="file" name="file" class="form-control" accept=".json,.geojson,.txt,.dbf,.shp,.zip,.rar" required style="border-radius: 10px;">
                    </div>
                </div>
                <div class="modal-footer border-top pt-3 pb-3 px-4 d-flex justify-content-end">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 10px;">Batal</button>
                    <button type="submit" class="btn text-white shadow-sm" style="background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%); border-radius: 10px; border: none; font-weight: 600;">Unggah & Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Import LP2B -->
<div class="modal fade" style="z-index: 99999;" id="modalImportLp2b" tabindex="-1" aria-labelledby="modalImportLp2bLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 15px 35px rgba(0,0,0,0.15);">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); padding: 18px 24px;">
                <h5 class="modal-title font-weight-bold text-white" id="modalImportLp2bLabel">Import Data LP2B</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('import.lp2b') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-muted" style="font-size: 13px;">Pilih file spasial berformat `.zip`, `.geojson`, `.json`, atau `.dbf` untuk data Lahan Pertanian Pangan Berkelanjutan (LP2B).</p>
                    <div class="mb-3">
                        <input type="file" name="file" class="form-control" accept=".json,.geojson,.txt,.dbf,.shp,.zip,.rar" required style="border-radius: 10px;">
                    </div>
                </div>
                <div class="modal-footer border-top pt-3 pb-3 px-4 d-flex justify-content-end">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 10px;">Batal</button>
                    <button type="submit" class="btn text-white shadow-sm" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); border-radius: 10px; border: none; font-weight: 600;">Unggah & Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
