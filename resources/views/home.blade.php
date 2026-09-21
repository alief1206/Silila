@extends('layouts.home')
@section('title', 'Home')

@include('dashboard.js.home')

@section('content')
<style>
    /* Make zoom controls stick to the bottom left of the screen */
    .leaflet-control-zoom {
        position: fixed !important;
        bottom: 30px !important;
        left: 20px !important;
        z-index: 9999;
    }
</style>
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
        <div style="position: relative;">
            <div id="maps" style="height: 1000px; max-width: 100% !important;"></div>
            <!-- Header Blok Putih di Pojok Kiri Atas Peta -->
            <div style="position: fixed; top: 15px; left: 60px; z-index: 1000; display: flex; align-items: center; gap: 15px; background: rgba(255, 255, 255, 0.95); padding: 12px 25px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); backdrop-filter: blur(5px);">
                <img src="{{ url('assets/images/header-login.png') }}" alt="Logo SILILA" style="height: 45px; object-fit: contain;">
                <img src="{{ url('assets/images/Banyuwangi.png') }}" alt="Logo Banyuwangi" style="height: 45px; object-fit: contain;">
                <div style="margin-left: 10px; border-left: 2px solid #e0e0e0; padding-left: 15px; display: flex; flex-direction: column; justify-content: center;">
                    <h5 ondblclick="window.location.href='{{ route('login') }}'" style="margin: 0; font-weight: 700; color: #2c3e50; font-size: 16px; letter-spacing: 0.5px; cursor: pointer; user-select: none;" title="Klik dua kali untuk login">SILILA</h5>
                    <span style="font-size: 12px; color: #7f8c8d; font-weight: 500;">Sistem Informasi Perlindungan Lahan Banyuwangi</span>
                </div>
            </div>
        </div>
 </div>
    </div>
    @auth

    <div class="promo-popup animated" style="bottom: 15px !important; display: none;">
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

    {{-- Modal Data Diri untuk Chatbot --}}
    <div class="modal fade" style="z-index: 999999;" id="dataDiriModal" tabindex="-1" aria-labelledby="dataDiriLabel" aria-hidden="true" data-bs-backdrop="static">
      <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white" style="background-color: #074173 !important;">
            <h5 class="modal-title" id="dataDiriLabel" style="color: white;">Data Pencarian Lahan</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p class="text-center" style="font-size: 14px;">Silakan masukkan data diri dan koordinat lahan Anda.</p>
            <form id="form-data-diri">
              <div class="mb-3">
                <label for="guest-nik" class="form-label">NIK</label>
                <input type="text" class="form-control" id="guest-nik" placeholder="Masukkan NIK" required>
              </div>
              <div class="mb-3">
                <label for="guest-nama" class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" id="guest-nama" placeholder="Masukkan Nama Anda" required>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="guest-lat" class="form-label">Latitude</label>
                  <input type="text" class="form-control" id="guest-lat" placeholder="Contoh: -8.188" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="guest-lng" class="form-label">Longitude</label>
                  <input type="text" class="form-control" id="guest-lng" placeholder="Contoh: 114.295" required>
                </div>
              </div>
              <div class="modal-footer d-flex justify-content-center border-0 mt-2">
                <button type="submit" class="btn btn-primary" style="background-color: #074173;">Hubungkan dengan Admin</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Chatbot Widget -->
    <div id="chatbot-widget" style="position: fixed; bottom: 20px; right: 20px; z-index: 999999;">
        <!-- Chatbot Greeting Bubble -->
        <div id="chatbot-greeting" style="position: absolute; bottom: 75px; right: 0; background: white; padding: 10px 15px; border-radius: 15px; box-shadow: 0 4px 8px rgba(0,0,0,0.2); white-space: nowrap; font-size: 14px; color: #333; z-index: 10;">
            Apakah Anda butuh bantuan untuk mencari lahan Anda?
            <div style="position: absolute; bottom: -8px; right: 20px; width: 0; height: 0; border-left: 8px solid transparent; border-right: 8px solid transparent; border-top: 8px solid white;"></div>
        </div>

        <!-- Chatbot Button -->
        <button id="chatbot-toggle" style="background-color: #074173; color: white; border: none; border-radius: 50%; width: 60px; height: 60px; box-shadow: 0 4px 8px rgba(0,0,0,0.2); cursor: pointer; display: flex; justify-content: center; align-items: center; position: relative;">
            <i class="material-icons" style="font-size: 30px;">chat</i>
        </button>

        <!-- Chatbot Window -->
        <div id="chatbot-window" style="display: none; position: absolute; bottom: 70px; right: 0; width: 350px; background: white; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.2); overflow: hidden; flex-direction: column;">
            <!-- Header -->
            <div style="background-color: #074173; color: white; padding: 15px; display: flex; justify-content: space-between; align-items: center;">
                <h5 style="margin: 0; font-size: 16px; color: white;">Asisten SILILA</h5>
                <button id="chatbot-close" style="background: none; border: none; color: white; cursor: pointer;">
                    <i class="material-icons">close</i>
                </button>
            </div>
            
            <!-- Messages -->
            <div id="chatbot-messages" style="height: 300px; padding: 15px; overflow-y: auto; background-color: #f9f9f9; display: flex; flex-direction: column; gap: 10px;">
                <div style="align-self: flex-start; background: #e0e0e0; padding: 10px 15px; border-radius: 15px; font-size: 14px; max-width: 80%;">
                    Halo! Saya asisten virtual SILILA. Silakan masukkan koordinat lahan Anda (contoh: -8.123, 114.456) untuk mengecek lokasi.
                </div>
            </div>

            <!-- Input -->
            <div style="padding: 10px; border-top: 1px solid #ddd; display: flex; gap: 10px; background: white;">
                <input type="text" id="chatbot-input" placeholder="Ketik pesan..." style="flex: 1; padding: 8px 12px; border: 1px solid #ddd; border-radius: 20px; outline: none; font-size: 14px;">
                <button id="chatbot-send" style="background-color: #074173; color: white; border: none; border-radius: 50%; width: 35px; height: 35px; display: flex; justify-content: center; align-items: center; cursor: pointer;">
                    <i class="material-icons" style="font-size: 18px;">send</i>
                </button>
            </div>
        </div>
    </div>

    <!-- Chatbot Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('chatbot-toggle');
            const closeBtn = document.getElementById('chatbot-close');
            const chatWindow = document.getElementById('chatbot-window');
            const chatInput = document.getElementById('chatbot-input');
            const sendBtn = document.getElementById('chatbot-send');
            const messagesContainer = document.getElementById('chatbot-messages');

            function toggleChat() {
                // Hide greeting when chat is opened
                const greeting = document.getElementById('chatbot-greeting');
                if (greeting) greeting.style.display = 'none';

                if (chatWindow.style.display === 'none') {
                    chatWindow.style.display = 'flex';
                    if (liveChatSessionId) {
                        fetch(`/chat/${liveChatSessionId}/read`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ reader_type: 'user' })
                        });
                    }
                } else {
                    chatWindow.style.display = 'none';
                }
            }

            // Expose toggleChat to global scope for the button inside chatbot
            window.toggleChat = toggleChat;

            toggleBtn.addEventListener('click', toggleChat);
            closeBtn.addEventListener('click', toggleChat);

            function appendMessage(text, sender, id = null, isRead = false) {
                const msgDiv = document.createElement('div');
                msgDiv.style.padding = '10px 15px';
                msgDiv.style.borderRadius = '15px';
                msgDiv.style.fontSize = '14px';
                msgDiv.style.maxWidth = '80%';
                msgDiv.style.wordWrap = 'break-word';

                if (sender === 'user') {
                    msgDiv.style.alignSelf = 'flex-end';
                    msgDiv.style.background = '#074173';
                    msgDiv.style.color = 'white';
                    
                    const textSpan = document.createElement('span');
                    textSpan.textContent = text;
                    msgDiv.appendChild(textSpan);

                    if (id) {
                        msgDiv.setAttribute('data-msg-id', id);
                        const tickDiv = document.createElement('div');
                        tickDiv.style.textAlign = 'right';
                        tickDiv.style.marginTop = '2px';
                        tickDiv.className = 'msg-tick';
                        if (isRead) {
                            tickDiv.innerHTML = '<i class="fas fa-check-double" style="font-size:10px; color:#34b7f1;"></i>';
                            tickDiv.dataset.read = 'true';
                        } else {
                            tickDiv.innerHTML = '<i class="fas fa-check" style="font-size:10px; color:#ccc;"></i>';
                            tickDiv.dataset.read = 'false';
                        }
                        msgDiv.appendChild(tickDiv);
                    } else {
                        // For messages that are waiting for ID
                        msgDiv.classList.add('pending-msg');
                    }
                } else {
                    msgDiv.style.alignSelf = 'flex-start';
                    msgDiv.style.background = '#e0e0e0';
                    msgDiv.style.color = 'black';
                    msgDiv.textContent = text;
                }

                messagesContainer.appendChild(msgDiv);
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
                
                return msgDiv; // Return so we can manipulate it later if needed
            }

            let liveChatSessionId = localStorage.getItem('liveChatSessionId');
            let lastMessageId = 0;
            let chatPollingInterval = null;

            function startLiveChatPolling() {
                if (chatPollingInterval) clearInterval(chatPollingInterval);
                chatPollingInterval = setInterval(() => {
                    if (!liveChatSessionId) return;
                    fetch(`/chat/${liveChatSessionId}/messages?last_id=${lastMessageId}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'closed') {
                                clearInterval(chatPollingInterval);
                                localStorage.removeItem('liveChatSessionId');
                                liveChatSessionId = null;
                                appendMessage("Sesi percakapan telah ditutup oleh admin.", 'bot');
                                return;
                            }
                            
                            let needsMarkRead = false;
                            
                            if (data.messages && data.messages.length > 0) {
                                data.messages.forEach(msg => {
                                    if (msg.sender_type === 'admin') {
                                        appendMessage(msg.message, 'bot');
                                        if (chatWindow.style.display !== 'none') needsMarkRead = true;
                                    } else if (msg.sender_type === 'user' && lastMessageId === 0) {
                                        // Restoring user's own message on first load
                                        appendMessage(msg.message, 'user', msg.id, msg.is_read);
                                    }
                                    lastMessageId = Math.max(lastMessageId, msg.id);
                                });
                            }
                            
                            if (data.admin_last_read_id) {
                                document.querySelectorAll('.msg-tick').forEach(tickDiv => {
                                    const msgDiv = tickDiv.closest('[data-msg-id]');
                                    if (msgDiv) {
                                        const msgId = parseInt(msgDiv.getAttribute('data-msg-id'));
                                        if (msgId <= data.admin_last_read_id && tickDiv.dataset.read !== 'true') {
                                            tickDiv.innerHTML = '<i class="fas fa-check-double" style="font-size:10px; color:#34b7f1;"></i>';
                                            tickDiv.dataset.read = 'true';
                                        }
                                    }
                                });
                            }
                            
                            if (needsMarkRead) {
                                fetch(`/chat/${liveChatSessionId}/read`, {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                    body: JSON.stringify({ reader_type: 'user' })
                                });
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            if(err.status === 404) {
                                clearInterval(chatPollingInterval);
                                localStorage.removeItem('liveChatSessionId');
                                liveChatSessionId = null;
                            }
                        });
                }, 3000);
            }

            // Jika ada session tersimpan, langsung fetch
            if (liveChatSessionId) {
                messagesContainer.innerHTML = '';
                appendMessage("Memuat percakapan Anda sebelumnya...", 'bot');
                startLiveChatPolling();
            }

            function sendMessage() {
                const text = chatInput.value.trim();
                if (!text) return;

                const pendingMsgDiv = appendMessage(text, 'user');
                chatInput.value = '';

                // Mode Live Chat
                if (liveChatSessionId) {
                    fetch('{{ route("chat.send") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            session_id: liveChatSessionId,
                            sender_type: 'user',
                            message: text
                        })
                    }).then(res => res.json()).then(data => {
                        if(data.success) {
                            lastMessageId = Math.max(lastMessageId, data.message.id);
                            
                            pendingMsgDiv.setAttribute('data-msg-id', data.message.id);
                            pendingMsgDiv.classList.remove('pending-msg');
                            
                            const tickDiv = document.createElement('div');
                            tickDiv.style.textAlign = 'right';
                            tickDiv.style.marginTop = '2px';
                            tickDiv.className = 'msg-tick';
                            tickDiv.innerHTML = '<i class="fas fa-check" style="font-size:10px; color:#ccc;"></i>';
                            tickDiv.dataset.read = 'false';
                            pendingMsgDiv.appendChild(tickDiv);
                        }
                    });
                    return; // Hentikan logika bot otomatis
                }

                // Mode Bot Otomatis
                let lowerText = text.toLowerCase();
                let reply = '';
                let extraReply = null;

                // Coba deteksi koordinat
                let coords = text.replace(/,/g, ' ').replace(/\s+/g, ' ').trim().split(' ');
                if (coords.length === 2 && !isNaN(parseFloat(coords[0])) && !isNaN(parseFloat(coords[1]))) {
                    let lat = parseFloat(coords[0]);
                    let lng = parseFloat(coords[1]);
                    
                    // Normalisasi lat/lng
                    if (Math.abs(lat) > Math.abs(lng)) {
                        let temp = lat;
                        lat = lng;
                        lng = temp;
                    }

                    if (typeof window.performCoordinateSearch === 'function') {
                        window.performCoordinateSearch('latlng', lat, lng, '', '');
                        reply = `Mencari koordinat ${lat}, ${lng}... Silakan lihat peta untuk melihat hasil pencarian lahan Anda!`;
                        extraReply = "Jika Anda ingin berbicara langsung dengan admin, silakan balas dengan kata 'iya'.";
                    } else {
                        reply = "Maaf, sistem pencarian belum siap. Silakan refresh halaman.";
                    }
                } else if (lowerText === 'iya' || lowerText === 'ya') {
                    reply = "Baik, silakan lengkapi data diri dan koordinat lahan Anda pada form yang muncul untuk dihubungkan ke admin.";
                    $('#dataDiriModal').modal('show');
                } else if (lowerText.includes('hai') || lowerText.includes('halo')) {
                    reply = "Halo! Jika Anda butuh bantuan, balas 'iya' untuk terhubung dengan admin, atau ketikkan koordinat untuk mengecek lahan secara otomatis.";
                } else if (lowerText.includes('lahan') || lowerText.includes('tanah')) {
                    reply = "Anda dapat mengecek secara otomatis dengan memasukkan koordinat, atau balas 'iya' untuk berbicara dengan admin.";
                } else {
                    reply = "Maaf, saya kurang mengerti. Untuk mencari lahan, masukkan Latitude dan Longitude. Untuk berbicara dengan admin, balas 'iya'.";
                }

                setTimeout(() => {
                    appendMessage(reply, 'bot');
                    if (extraReply) {
                        setTimeout(() => {
                            appendMessage(extraReply, 'bot');
                        }, 600);
                    }
                }, 500);
            }

            sendBtn.addEventListener('click', sendMessage);
            chatInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    sendMessage();
                }
            });

            // Event listener untuk form data diri
            document.getElementById('form-data-diri').addEventListener('submit', function(e) {
                e.preventDefault();
                let nik = document.getElementById('guest-nik').value;
                let nama = document.getElementById('guest-nama').value;
                let lat = parseFloat(document.getElementById('guest-lat').value);
                let lng = parseFloat(document.getElementById('guest-lng').value);

                if (isNaN(lat) || isNaN(lng)) {
                    alert('Koordinat tidak valid. Harap masukkan angka yang benar.');
                    return;
                }

                if (Math.abs(lat) > Math.abs(lng)) {
                    let temp = lat;
                    lat = lng;
                    lng = temp;
                }

                window.guestNik = nik;
                window.guestName = nama;
                $('#dataDiriModal').modal('hide');

                // Mulai sesi Live Chat via AJAX
                fetch('{{ route("chat.start") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        nik: nik,
                        nama: nama,
                        koordinat: `${lat}, ${lng}`
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        liveChatSessionId = data.session_id;
                        localStorage.setItem('liveChatSessionId', liveChatSessionId);
                        startLiveChatPolling();

                        // Lakukan pencarian peta di background
                        if (typeof window.performCoordinateSearch === 'function') {
                            window.performCoordinateSearch('latlng', lat, lng, '', '');
                        }

                        // Beri tahu user bahwa mereka terhubung
                        setTimeout(() => {
                            appendMessage(`Halo ${nama}, kami telah menerima data Anda. Silakan sampaikan pesan atau pertanyaan Anda di bawah ini, admin akan segera membalasnya.`, 'bot');
                        }, 500);
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert("Terjadi kesalahan saat menghubungi server.");
                });
            });
        });
    </script>
    @endsection
