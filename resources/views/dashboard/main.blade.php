@extends('dashboard.template')
@section('title', 'Dashboard')

@include('dashboard.js.main')

@section('content')

<div class="main-content-container container-fluid px-4">
    <!-- Page Header -->
    <div class="page-header row no-gutters ms-2 py-4">
      <div class="col-12 col-sm-4 text-sm-left mb-0">
        <span class="text-uppercase page-subtitle">Dashboard</span>
        <h3 class="page-title">Map Overview</h3>
      </div>
    </div>
    <!-- End Page Header -->
    <div id="alert-message"></div>
    <!-- Small Stats Blocks -->
    <div class="row">
      <div class="col-lg col-md-6 col-sm-6 mb-4">
        <div class="stats-small stats-small--1 card card-small">
          <div class="card-body p-0 d-flex">
            <div class="d-flex flex-column m-auto">
              <div class="stats-small__data text-center">
                <span class="stats-small__label text-uppercase">Kecamatan Tercakup</span>
                <h6 style="font-size: 18pt !important;" class="stats-small__value count my-3 jumlah-kecamatan">0</h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg col-md-6 col-sm-6 mb-4">
        <div class="stats-small stats-small--1 card card-small">
          <div class="card-body p-0 d-flex">
            <div class="d-flex flex-column m-auto">
              <div class="stats-small__data text-center">
                <span class="stats-small__label text-uppercase">Desa Tercakup</span>
                <h6 style="font-size: 18pt !important;" class="stats-small__value count my-3 jumlah-desa">0</h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg col-md-6 col-sm-6 mb-4">
        <div class="stats-small stats-small--1 card card-small">
          <div class="card-body p-0 d-flex">
            <div class="d-flex flex-column m-auto">
              <div class="stats-small__data text-center">
                <span class="stats-small__label text-uppercase">Total Wilayah KP2B</span>
                <h6 style="font-size: 18pt !important;" class="stats-small__value count my-3 jumlah-kp2b">0</h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg col-md-6 col-sm-6 mb-4">
        <div class="stats-small stats-small--1 card card-small">
          <div class="card-body p-0 d-flex">
            <div class="d-flex flex-column m-auto">
              <div class="stats-small__data text-center">
                <span class="stats-small__label text-uppercase">Total Wilayah LSD</span>
                <h6 style="font-size: 18pt !important;" class="stats-small__value count my-3 jumlah-lsd">0</h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg col-md-6 col-sm-6 mb-4">
        <div class="stats-small stats-small--1 card card-small">
          <div class="card-body p-0 d-flex">
            <div class="d-flex flex-column m-auto">
              <div class="stats-small__data text-center">
                <span class="stats-small__label text-uppercase">Total Wilayah LBS</span>
                <h6 style="font-size: 18pt !important;" class="stats-small__value count my-3 jumlah-lbs">0</h6>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- End Small Stats Blocks -->

    <div class="rowbg-light mb-4 text-right">
        <div class="col-12">
            <button type="button" class="btn btn-primary mx-2 mb-1 disabled"><i class="material-icons">download</i> Import geoJSON LBS</button>
            <button id="import-geojson-lsd" type="button" class="btn btn-primary mx-2 mb-1" data-toggle="modal" data-target="#modalImportLsd"><i class="material-icons">download</i> Import geoJSON LSD</button>
            <button id="import-geojson-lp2b" type="button" class="btn btn-primary mx-2 mb-1" data-toggle="modal" data-target="#modalImportLp2b"><i class="material-icons">download</i> Import geoJSON LP2B</button>
        </div>
    </div>

    <div class="row">
      <!-- Users Stats -->
      <div class="col-lg-8 col-md-12 col-sm-12 mb-4">
        <div class="card card-small">
          <div class="card-header border-bottom">
            <div class="row">
                <div class="col">
                    <h6 class="m-0">Map Wilayah Banyuwangi</h6>
                </div>
                <div class="col text-right">
                    <select id="map-filter" class="custom-select custom-select-sm" style="max-width: 130px;">
                      <option value="1" selected>LP2B</option>
                      <option value="2">LSD</option>
                      <option value="3">LBS</option>
                    </select>
                </div>
                <div id="loading-map-indicator" class="col-12 text-center text-danger">
                    <b><i class="fas fa-spinner fa-spin"></i> Memuat komponen...</b>
                </div>
              </div>
          </div>
          <div class="card-body pt-2">
            <div id="filter-lp2b" class="col-12 row d-none">
                <div class="form-group col-5">
                    <select name="input-kecamatan" class="form-control" style="width: 100% !important;">
                        <option selected="" value="0">Semua Kecamatan</option>
                    </select>
                </div>
                <div class="form-group col-5">
                    <select name="input-desa" class="form-control" style="width: 100% !important;">
                        <option selected="" value="0">Semua Desa</option>
                    </select>
                </div>
                <div class="col-2 text-right">
                    <button class="btn btn-sm btn-primary btn-filter-lp2b"><i class="material-icons">filter_alt</i> Filter</button>
                </div>
                <hr>
            </div>
            <div id="cari-koordinat-segment" class="col-12 d-none">
                <form id="cari-koordinat" class="row">
                    <div class="form-group col-4">
                        <input type="text" class="form-control" style="width: 100% !important;" id="latitude" placeholder="Latitude/x">
                    </div>
                    <div class="form-group col-4">
                        <input type="text" class="form-control" style="width: 100% !important;" id="longitude" placeholder="Longitude/y">
                    </div>
                    <div class="form-group col-2">
                        <select id="tipe-pencarian" class="form-control" style="width: 100% !important;">
                            <option value="latlng">LatLng</option>
                            <option value="cea">CEA</option>
                        </select>
                    </div>
                    <div class="col-2 text-right">
                        <button type="submit" class="btn btn-primary btn-cari-koordinat"><i class="material-icons">search</i> Cari</button>
                    </div>
                </form>
            </div>
            <div id="map" style="height: 400px; max-width: 100% !important;"></div>
          </div>
        </div>
      </div>
      <!-- End Users Stats -->
      <!-- Users By Device Stats -->
      <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
        <div class="card card-small h-100">
          <div class="card-header border-bottom">
            <h6 class="m-0">Jumlah Wilayah</h6>
          </div>
          <div class="card-body d-flex py-0">
            <canvas class="mt-5 mb-1 w-100" id="myChart" style="height: 300px"></canvas>
          </div>
        </div>
      </div>
      <!-- End Users By Device Stats -->
    </div>
  </div>

  <div class="modal fade" id="modalImportLp2b" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload File GeoJSON.json LP2B</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="p-3">
                    <div class="alert--message mb-3"></div>
                    <form action="">
                        <div id="file--upload"></div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                            <button class="btn btn-success text-white" type="submit">Upload</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalImportLsd" tabindex="-1" role="dialog" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
              <div class="modal-content">
                  <div class="modal-header">
                      <h5 class="modal-title">Upload File GeoJSON.json LSD</h5>
                      <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">×</span>
                      </button>
                  </div>
                  <div class="p-3">
                      <div class="alert--message mb-3"></div>
                      <form action="">
                          <div id="file--upload-lsd"></div>
                          <div class="modal-footer">
                              <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                              <button class="btn btn-success text-white" type="submit">Upload</button>
                          </div>
                      </form>
                  </div>
              </div>
          </div>
      </div>

  @endsection
