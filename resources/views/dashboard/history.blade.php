@extends('dashboard.template')
@section('title', 'Dashboard')

@include('dashboard.js.log')

@section('content')
          <!-- / .main-navbar -->
          <div class="main-content-container container-fluid px-4">
            <!-- Page Header -->
            <div class="page-header row no-gutters py-4">
              <div class="col-12 col-sm-4 text-sm-left mb-0">
                <span class="text-uppercase page-subtitle">Dashboard</span>
                <h3 class="page-title">History Pencarian</h3>
              </div>
            </div>
            <!-- End Page Header -->
            <!-- Default Light Table -->
            <div class="row">
              <div class="col">
                <div class="card card-small mb-4">
                  <div class="card-header border-bottom py-3">
                    <h6 class="m-0 font-weight-bold" style="color: #0f172a;">
                      <i class="material-icons mr-1" style="font-size: 18px; vertical-align: text-top; color: #059669;">history</i> Riwayat Pencarian Titik Spasial
                    </h6>
                  </div>
                  <div class="card-body p-0 pb-3 text-center">
                   <table id="table-1" class="table table-hover" style="width:100%">
        <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Nama user</th>
              <th scope="col">Titik Koordinat</th>
              <th scope="col">Kecamatan</th>
              <th scope="col">Desa</th>
              <th scope="col">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($riwayat as $item)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $item->namauser }}</td>
                    <td>{{ $item->koordinat }}</td>
                    <td>{{ $item->kecamatan }}</td>
                    <td>{{ $item->desa }}</td>
                    <td>{{ $item->ket }}</td>
                </tr>
            @endforeach
            
        </tbody>
        <tfoot>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Nama user</th>
                <th scope="col">Titik Koordinat</th>
                <th scope="col">Kecamatan</th>
                <th scope="col">Desa</th>
                <th scope="col">Keterangan</th>
            </tr>
        </tfoot>
     
    </table>
    <div class="d-flex justify-content-center mt-4">
      {{ $riwayat->links('pagination::bootstrap-4') }}
  </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
@endsection
