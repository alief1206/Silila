@extends('layouts.home')
@section('title', 'Home')

@include('dashboard.js.home')

@section('content')
@php
use Carbon\Carbon;
@endphp
<div id="alert-message"></div>
    <div class="container-fluid">
      <div class="row">
        <div class="modal" style="z-index: 999999;" id="loadingModal" tabindex="-1" role="dialog" aria-labelledby="loadingModalLabel" aria-hidden="true" data-bs-backdrop="static">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-body text-center">
                <div id="loading-map-indicator" class="text-danger">
                  <b><i class="fas fa-spinner fa-spin"></i> Memuat Peta...</b>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div id="maps" style="height: 1000px; max-width: 100% !important;"></div>

 </div>
    </div>
    @auth

    <div class="promo-popup animated" style="bottom: 15px !important;">
      <div class="pp-intro-bar">
        SILILA

        <span class="close btn-restart">
          <i class="material-icons">restart_alt</i>
        </span>
        <span class="up">
          <i class="material-icons">keyboard_arrow_up</i>
        </span>
      </div>

    <div class="col-sm-12 col-md-12">
          @if (session('success'))
          <div class="alert alert-success">
              {{ session('success') }}
          </div>
        @endif
        <div class="row">
          <div class="col-md-4">
              <img src="assets/images/Banyuwangi.png" alt="Deskripsi Gambar 1" class="img-fluid mx-auto d-block" style="max-width: 100%; height: auto;">
          </div>
          <div class="col-md-8">
              <img src="assets/images/silila.png" alt="Deskripsi Gambar 2" class="img-fluid mx-auto d-block" style="max-width: 100%; height: auto;">
          </div>
      </div>
      <strong class="text-muted d-block mb-2">Cari Lahan</strong>
      {{-- <img src="assets/images/silila.png" width="25" height="25"> --}}
      <form id="cari-koordinat">
        <div class="form-row">
            <div class="form-group col-md-12">
                {{-- <label for="coordinateType">Pilih jenis derajat:</label> --}}
                <select class="form-control" id="tipe-pencarian" required>
                  <option value="">Pilih Tipe Pencarian</option>
                  <option value="latlng">LatLng</option>
                  <option value="cea">CEA</option>
                </select>
            </div>
            <div class="form-group col-md-6">
              <select class="form-control" name="kecamatan" id="kecamatan">
                <option value="">Pilih Kecamatan</option>
              </select>
            </div>
            <div class="form-group col-md-6">
              <select class="form-control" name="desa" id="desa">
                <option value="">Pilih Desa</option>
              </select>
            </div>
            <div class="form-check form-check-inline mt-2 mb-4">
              <input class="form-check-input lp2b" type="checkbox" name="searchType" id="lp2bCheckbox" value="1">
              <label class="form-check-label" for="lp2bCheckbox">LP2B</label>
          </div>
          <div class="form-check form-check-inline mt-2 mb-4">
              <input class="form-check-input lsd" type="checkbox" name="searchType" id="lsdCheckbox" value="2">
              <label class="form-check-label" for="lsdCheckbox">LSD</label>
          </div>
          <div class="form-check form-check-inline mt-2 mb-4">
              <input class="form-check-input lbs" type="checkbox" name="searchType" id="lbsCheckbox" value="3">
              <label class="form-check-label" for="lbsCheckbox">LBS</label>
          </div>
          <div class="form-check form-check-inline mt-2 mb-4">
              <input class="form-check-input agricultural" type="checkbox" name="searchType" id="agriculturalCheckbox" value="4">
              <label class="form-check-label" for="agriculturalCheckbox">Kawasan Pertanian</label>
          </div>

            <div class="form-group col-md-6">
                {{-- <label for="latitude">Latitude:</label> --}}
                <input type="text" class="form-control" id="latitude" placeholder="Latitude" required>
                <p style="font-size: 12px; line-height: 1.2;">Contoh: -8.188387</p>
            </div>
            <div class="form-group col-md-6">
                {{-- <label for="longitude">Longitude:</label> --}}
                <input type="text" class="form-control" id="longitude" placeholder="Longitude" required>
                <p style="font-size: 12px; line-height: 1.2;">Contoh: 114.295038</p>
            </div>
        </div>
        <div style="display: flex; justify-content: center; align-items: center;">
          <button type="submit" class="mb-2 btn" style="background-color: #074173; color: #ffffff;">Cari</button>
      </div>
    </form>
    </div>
    <hr>
    <div style="display: flex; justify-content: center; align-items: center;">
      <button type="button" class="mb-2 btn" style="background-color: #074173; color: #ffffff;"   data-bs-toggle="modal" data-bs-target="#informasi">Lihat Informasi Lahan</button>
    </div>
    <div style="display: flex; justify-content: center; align-items: center;">
      @auth
        <div class="dropdown mb-2">
            <button class="btn btn-primary dropdown-toggle" style="background-color: #074173; color: #ffffff;" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Hai, {{ Auth::user()->nama }}
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
              @if(Auth::user()->role == '1')
                <a class="dropdown-item" href="/dashboard">Dashboard</a>
                @endif
                <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#profile">Profile</a>
                <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#riwayat">Riwayat</a>
                <form action="{{ route('logout') }}" method="POST">
                  @csrf
                  <button type="submit" class="dropdown-item">Logout</button>
                </form>
            </div>
        </div>
      {{-- <button type="button" class="mb-2 btn" style="background-color: #074173; color: #ffffff;" data-bs-toggle="modal" data-bs-target="#profile">Hai, {{ Auth::user()->nama }}</button> --}}
      @else
      <button type="button" class="mb-2 btn" style="background-color: #074173; color: #ffffff;" data-bs-toggle="modal" data-bs-target="#login">Login</button>
      @endauth
    </div>
    {{-- <div style="display: flex; justify-content: center; align-items: center;">
      <button type="button" class="mb-2 btn" style="background-color: #074173; color: #ffffff;" data-bs-toggle="modal" data-bs-target="#riwayat">Riwayat</button>
    </div> --}}
    <br>
    <br>

  </div>
  @endauth



    {{-- modal informasi --}}
    {{-- modal informasi --}}
<div class="modal fade" style="z-index: 99999;" id="informasi" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-left">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Informasi Lahan</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="modal-body-content">
          <!-- Konten akan dimasukkan di sini -->
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" id="printButton">Cetak</button>
        </div>
      </div>
    </div>
</div>
  {{-- cetak --}}
<div class="modal fade" style="z-index: 99999;" id="cetak" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-center">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Informasi Lahan</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div id="maps" style="height: 500px; max-width: 100% !important;"></div>
        <div class="modal-body" id="cetak-print">

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="printButton">Cetak</button>
        </div>
      </div>
    </div>
</div>

    {{-- modal login --}}
    <div class="modal fade" style="z-index: 99999;" id="login" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static">
      <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <img src="assets/images/images/header-login.png" alt="Header Image" class="img-fluid mx-auto d-block">
          </div>
          <div class="modal-body">
              <p class="text-center" style="font-size: 14px; line-height: 1.2;">Sudah punya akun?</p>
              <form method="POST" action="{{ route('login') }}">
                @csrf
                  <div class="mb-1">
                    <label for="nip" class="col-md-4 col-form-label">{{ __('NIK') }}</label>
                    <input id="nip" type="text" class="form-control @error('nip') is-invalid @enderror" name="nip" value="{{ old('nip') }}" required autocomplete="nip" autofocus>
                    @error('nip')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                  </div>
                  <div class="mb-1">
                    <label for="password" class="col-md-4 col-form-label">{{ __('Password') }}</label>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                  </div>
                  <p class="text-center" style="font-size: 14px; line-height: 1.2;">Belum punya akun? <a href="#" data-bs-toggle="modal" data-bs-target="#register">Register</a></p>

          </div>
          <div class="modal-footer d-flex justify-content-center">
              <button type="submit" class="btn btn-primary">Login</button>
            </div>
          </form>
        </div>
      </div>
    </div>

     {{-- modal register--}}
     <div class="modal fade" style="z-index: 99999;" id="register" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static">
      <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <img src="assets/images/images/header-login.png" alt="Header Image" class="img-fluid mx-auto d-block">
          </div>
          <div class="modal-body">
              <p class="text-center" style="font-size: 14px; line-height: 1.2;">Silahkan masukkan data anda</a></p>
              <form method="POST" action="{{ route('register') }}">
                @csrf
                  <div class="mb-1">
                    <input id="nip" type="text" class="form-control @error('nip') is-invalid @enderror" name="nip" value="{{ old('nip') }}" required autocomplete="nip" placeholder="NIK" autofocus>

                    @error('nip')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                  </div>
                  <div class="mb-1">
                    <input id="nama" type="text" class="form-control @error('nama') is-invalid @enderror" name="nama" value="{{ old('nama') }}" required autocomplete="nama" placeholder="Nama Lengkap" autofocus>

                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    </div>
                    <div class="mb-1">
                      <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="Email" required autocomplete="email">

                      @error('email')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                      @enderror
                    </div>
                    <div class="mb-1">
                      <input id="telp" type="text" class="form-control @error('telp') is-invalid @enderror" name="telp" value="{{ old('telp') }}" placeholder="Nomor Telefon" required autocomplete="telp" autofocus>
                                <input id="telp" type="hidden" class="form-control @error('telp') is-invalid @enderror" name="role" value="2" required autocomplete="telp" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                    </div>
                    <p class="text-center" style="font-size: 14px; line-height: 1.2;">Sudah punya akun? <a href="#" data-bs-toggle="modal" data-bs-target="#login">Login</a></p>
                    <p style="font-size: 12px; line-height: 1.2;">Password default : 12345678</p>
                    <p style="font-size: 12px; line-height: 1.2;">Segera ganti password anda!</p>
                  </div>
                  <div class="modal-footer d-flex justify-content-center">
                    <button type="submit" class="btn btn-primary">Register</button>
                  </form>
            </div>

        </div>
      </div>
    </div>
@auth
          {{-- modal profil --}}
          <div class="modal fade" style="z-index: 99999;" id="profile" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md modal-dialog-left">
              <div class="modal-content">
                <div class="modal-header">
                 <h5>Update Profil</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <form method="POST" action="{{ route('profile.update',Auth::user()->id)}}">
                    @csrf
                    @method('PUT')
                        <li class="list-group-item p-3">
                            <div class="row">
                            <div class="col-sm-12 col-md-4">
                                <strong class="text-muted d-block mb-2"></strong>
                                <div id="file--upload">&nbsp;&nbsp;&nbsp;
                                    <img class="user-avatar rounded-circle mr-2" src="{{ url('assets') }}/images/avatars/0.jpg" alt="User Avatar">
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-8">
                                <strong class="text-muted d-block mb-2">Ubah profil</strong>
                                <form>
                                <div class="form-group">
                                    <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1">@</span>
                                    </div>
                                    <input type="text" name="email" class="form-control" placeholder="Email" aria-label="Email" aria-describedby="basic-addon1" value="{{ Auth::user()->email }}"> </div>
                                </div>
                                <div class="form-group">
                                    <input type="password" name="password" class="form-control" id="inputPassword4" placeholder="Ubah Password anda"> </div>
                                <div class="form-group">
                                    <input type="text" name="nip" class="form-control" id="inputAddress" placeholder="1234 Main St" value="{{ Auth::user()->nip }}"> </div>
                                <div class="form-row">
                                    <input type="text" name="nama" class="form-control" id="inputCity" value="{{ Auth::user()->nama }}"> </div>
                                </div>
                            </div>
                            </div>
                        </li>
                        <div class="modal-footer">
                            <button class="btn btn-success text-white" type="submit">Simpan</button>
                        </div>
                      </form>
                </div>
              </div>
            </div>
          </div>
    @endauth
    @auth
        {{-- modal riwayat --}}
        <div class="modal fade" style="z-index: 99999;" id="riwayat" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md modal-dialog-scrollable modal-dialog-left">
              <div class="modal-content">
                <div class="modal-header d-flex">
                    <h5 class="mr-auto flex-grow-1">Riwayat</h5>
                    <form method="GET" action="{{ route('home') }}" class="form-inline">
                        <input class="form-control" type="search" placeholder="Cari" aria-label="Search" name="koordinat"  id="koordinat-search">
                        <div class="input-group-append">
                          <button class="btn btn-primary"><i class="fas fa-search"></i></button>
                      </div>
                    </form>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div class="row" id="search-results">
                      @foreach ($riwayat as $item)
                      <div class="col-lg-12 col-sm-12 mb-4">
                          <div class="card card-small card-post card-post--aside card-post--1">
                              <div class="card-body mb-2">
                                  <div class="d-flex align-items-center">
                                      <div class="card-post__image"
                                           data-lat="{{ explode(',', $item->koordinat)[0] }}"
                                           data-lng="{{ explode(',', $item->koordinat)[1] }}"
                                           style="width: 100px; height: 100px;">
                                      </div>
                                      <div class="ml-3 mb-1 paragraph-container">
                                          <p style="font-size: 12px; line-height: 1.2; margin-bottom: 5px;">Titik Koordinat : {{ $item->koordinat }}</p>
                                          <p style="font-size: 12px; line-height: 1.2; margin-bottom: 5px;">Kecamatan : {{ $item->kecamatan }}</p>
                                          <p style="font-size: 12px; line-height: 1.2; margin-bottom: 5px;">Desa : {{ $item->desa }}</p>
                                          <p style="font-size: 12px; line-height: 1.2; margin-bottom: 5px;">Luas : {{ $item->luas }}</p>
                                          <p style="font-size: 12px; line-height: 1.2; margin-bottom: 5px;">Keterangan : {{ $item->ket }}</p>
                                          <span class="text-muted" style="font-size: 12px; line-height: 1.2;">{{ Carbon::parse($item->tgl)->translatedFormat('d F Y') }}</span>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                      @endforeach
                  </div>
              </div>
              </div>
            </div>
        </div>
    @endauth
    @endsection
