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

    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 12px; border-left: 4px solid #10b981; background: #ecfdf5; color: #065f46; font-size: 13.5px;">
        <i class="fas fa-check-circle mr-2 text-success"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 12px; border-left: 4px solid #ef4444; background: #fef2f2; color: #991b1b; font-size: 13.5px;">
        <i class="fas fa-exclamation-triangle mr-2 text-danger"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    @endif
    @if($errors->any())
      <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 12px; border-left: 4px solid #ef4444; background: #fef2f2; color: #991b1b; font-size: 13.5px;">
        <ul class="mb-0 pl-3">
          @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    @endif

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
          <button id="import-geojson-lsd" type="button" class="btn btn-silila-sunrise mx-1 my-1" data-toggle="modal" data-target="#modalImportLsd" data-bs-toggle="modal" data-bs-target="#modalImportLsd">
            <i class="material-icons" style="font-size: 18px;">upload_file</i> Import Data LSD
          </button>
          <button id="import-geojson-lp2b" type="button" class="btn btn-silila-emerald mx-1 my-1" data-toggle="modal" data-target="#modalImportLp2b" data-bs-toggle="modal" data-bs-target="#modalImportLp2b">
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
                <div class="col-md-2 d-flex align-items-end mt-2 mt-md-0">
                    <button class="btn btn-silila-emerald btn-sm btn-filter-lp2b d-flex align-items-center justify-content-center" style="gap: 4px; padding: 6px 16px; font-size: 13px;">
                      <i class="material-icons" style="font-size: 15px;">filter_alt</i> Filter
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

  <!-- Modal Import Data LSD (Streaming & Smart Upsert Engine) -->
  <div class="modal fade" id="modalImportLsd" tabindex="-1" role="dialog" aria-labelledby="modalImportLsdLabel" aria-hidden="true">
      <div class="modal-dialog modal-md modal-dialog-centered" role="document">
          <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 15px 35px rgba(0,0,0,0.18); overflow: hidden;">
              <div class="modal-header text-white" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); padding: 18px 24px;">
                  <div class="d-flex align-items-center">
                      <div class="rounded-circle mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: rgba(255,255,255,0.2);">
                          <i class="material-icons text-white" style="font-size: 24px;">cloud_upload</i>
                      </div>
                      <div>
                          <h5 class="modal-title font-weight-bold text-white m-0" id="modalImportLsdLabel">Import Data LSD</h5>
                          <small class="text-white-50" style="font-size: 12px;">Lahan Sawah Dilindungi (ATR/BPN Banyuwangi)</small>
                      </div>
                  </div>
                  <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="opacity: 0.9;">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>
              <form action="{{ route('import.lsd') }}" method="POST" enctype="multipart/form-data" id="form-import-lsd">
                  @csrf
                  <div class="modal-body p-4">
                      <div class="alert alert-warning py-2 px-3 mb-2 d-flex align-items-start" style="font-size: 12.5px; border-radius: 12px; background: #fffbeb; border: 1px solid #fde68a; color: #92400e;">
                          <i class="material-icons mr-2" style="font-size: 18px; color: #d97706;">bolt</i>
                          <div>
                              <strong>Streaming &amp; Smart Upsert:</strong>
                              Mendukung file besar (.geojson, .json, .zip, .dbf, .shp). Data baru di-insert, data berubah di-update, dan data identik otomatis dilewati (skip) hemat RAM server.
                          </div>
                      </div>

                      <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-start" style="font-size: 12px; border-radius: 12px; background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af;">
                          <i class="material-icons mr-2" style="font-size: 18px; color: #2563eb;">tips_and_updates</i>
                          <div>
                              <strong>Saran Unggah Cepat:</strong> Jika file berukuran di atas <strong>40 MB</strong>, kompres berkas menjadi <strong>.ZIP</strong> terlebih dahulu (klik kanan berkas &rarr; <em>Send to &rarr; Compressed (zipped) folder</em>). Ukuran akan menyusut hingga 90% (~15MB) dan terunggah seketika tanpa risiko timeout koneksi lokal.
                          </div>
                      </div>

                      <div class="form-group mb-3">
                          <label class="font-weight-bold text-dark mb-1" style="font-size: 13px;">Pilih Berkas Spasial LSD:</label>
                          <div class="p-3 text-center" style="border: 2px dashed #cbd5e1; border-radius: 14px; background: #f8fafc;">
                              <i class="material-icons text-muted mb-1" style="font-size: 36px; color: #d97706;">description</i>
                              <input type="file" name="file" id="file-input-lsd" class="form-control-file" accept=".json,.geojson,.txt,.dbf,.shp,.zip,.rar" required style="font-size: 13px;">
                              <small class="text-muted d-block mt-2 font-italic">Format didukung: GeoJSON, JSON, ZIP, DBF, SHP</small>
                          </div>
                      </div>

                      <div id="lsd-upload-progress-info" class="d-none mt-3 p-3 text-center" style="background: #fef3c7; border-radius: 12px; border: 1px solid #fcd34d;">
                          <div class="spinner-border spinner-border-sm text-warning mr-2" role="status"></div>
                          <span style="font-size: 13px; font-weight: 600; color: #92400e;">Sedang memproses &amp; streaming data LSD ke database...</span>
                          <small class="d-block text-muted mt-1">Mohon jangan me-refresh atau menutup tab hingga proses selesai.</small>
                      </div>
                  </div>
                  <div class="modal-footer border-top pt-3 pb-3 px-4 d-flex justify-content-between bg-light">
                      <button type="button" class="btn btn-outline-secondary" data-dismiss="modal" data-bs-dismiss="modal" style="border-radius: 10px; font-weight: 600;">Batal</button>
                      <button type="submit" id="btn-submit-lsd" class="btn text-white shadow-sm font-weight-bold" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 10px; border: none; padding: 8px 20px;">
                          <i class="material-icons mr-1" style="font-size: 18px; vertical-align: text-bottom;">cloud_upload</i> Mulai Import Data
                      </button>
                  </div>
              </form>
          </div>
      </div>
  </div>

  <!-- Modal Import Data LP2B (Streaming & Smart Spatial Importer) -->
  <div class="modal fade" id="modalImportLp2b" tabindex="-1" role="dialog" aria-labelledby="modalImportLp2bLabel" aria-hidden="true">
      <div class="modal-dialog modal-md modal-dialog-centered" role="document">
          <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 15px 35px rgba(0,0,0,0.18); overflow: hidden;">
              <div class="modal-header text-white" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); padding: 18px 24px;">
                  <div class="d-flex align-items-center">
                      <div class="rounded-circle mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: rgba(255,255,255,0.2);">
                          <i class="material-icons text-white" style="font-size: 24px;">grass</i>
                      </div>
                      <div>
                          <h5 class="modal-title font-weight-bold text-white m-0" id="modalImportLp2bLabel">Import Data LP2B</h5>
                          <small class="text-white-50" style="font-size: 12px;">Lahan Pertanian Pangan Berkelanjutan (Smart Spatial Importer)</small>
                      </div>
                  </div>
                  <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="opacity: 0.9;">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>
              <form action="{{ route('import.lp2b') }}" method="POST" enctype="multipart/form-data" id="form-import-lp2b">
                  @csrf
                  <div class="modal-body p-4">
                      <div class="alert alert-success py-2 px-3 mb-2 d-flex align-items-start" style="font-size: 12.5px; border-radius: 12px; background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46;">
                          <i class="material-icons mr-2" style="font-size: 18px; color: #059669;">bolt</i>
                          <div>
                              <strong>Streaming &amp; Smart Upsert:</strong>
                              Mendukung file GeoJSON, JSON (ESRI Format), dan arsip .ZIP. Koordinat meter (WKID 54034) otomatis direproyeksi ke WGS84, dan nama Desa/Kecamatan otomatis dideteksi dari titik tengah poligon.
                          </div>
                      </div>

                      <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-start" style="font-size: 12px; border-radius: 12px; background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af;">
                          <i class="material-icons mr-2" style="font-size: 18px; color: #2563eb;">tips_and_updates</i>
                          <div>
                              <strong>Saran Unggah Cepat:</strong> Jika file berukuran di atas <strong>40 MB</strong>, kompres berkas menjadi <strong>.ZIP</strong> terlebih dahulu. Sistem otomatis membaca berkas JSON/GeoJSON di dalam arsip.
                          </div>
                      </div>

                      <div class="form-group mb-3">
                          <label class="font-weight-bold text-dark mb-1" style="font-size: 13px;">Pilih Berkas Spasial LP2B:</label>
                          <div class="p-3 text-center" style="border: 2px dashed #cbd5e1; border-radius: 14px; background: #f8fafc;">
                              <i class="material-icons text-muted mb-1" style="font-size: 36px; color: #059669;">description</i>
                              <input type="file" name="file" id="file-input-lp2b" class="form-control-file" accept=".json,.geojson,.txt,.dbf,.shp,.zip,.rar" required style="font-size: 13px;">
                              <small class="text-muted d-block mt-2 font-italic">Format didukung: GeoJSON, JSON, ZIP, DBF, SHP</small>
                          </div>
                      </div>

                      <div id="lp2b-upload-progress-info" class="d-none mt-3 p-3 text-center" style="background: #ecfdf5; border-radius: 12px; border: 1px solid #6ee7b7;">
                          <div class="spinner-border spinner-border-sm text-success mr-2" role="status"></div>
                          <span style="font-size: 13px; font-weight: 600; color: #065f46;">Sedang memproses &amp; streaming data LP2B ke database...</span>
                          <small class="d-block text-muted mt-1">Mohon jangan me-refresh atau menutup tab hingga proses selesai.</small>
                      </div>
                  </div>
                  <div class="modal-footer border-top pt-3 pb-3 px-4 d-flex justify-content-between bg-light">
                      <button type="button" class="btn btn-outline-secondary" data-dismiss="modal" data-bs-dismiss="modal" style="border-radius: 10px; font-weight: 600;">Batal</button>
                      <button type="submit" id="btn-submit-lp2b" class="btn text-white shadow-sm font-weight-bold" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); border-radius: 10px; border: none; padding: 8px 20px;">
                          <i class="material-icons mr-1" style="font-size: 18px; vertical-align: text-bottom;">cloud_upload</i> Mulai Import Data
                      </button>
                  </div>
              </form>
          </div>
      </div>
  </div>

@endsection
