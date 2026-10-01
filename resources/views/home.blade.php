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
    .btn-silila-main-opt {
        display: flex;
        align-items: center;
        gap: 10px;
        width: 100%;
        padding: 10px 12px;
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 12px;
        text-align: left;
        transition: all 0.2s ease;
        cursor: pointer;
        box-shadow: 0 2px 5px rgba(0,0,0,0.03);
    }
    .btn-silila-main-opt:hover {
        border-color: #10b981;
        background: #ecfdf5;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(16, 185, 129, 0.12);
    }
</style>
@php
use Carbon\Carbon;
@endphp
<div id="alert-message"></div>
    <div class="container-fluid">
        <!-- Floating Non-blocking Map Loading Pill -->
        <div id="loading-map-indicator" style="display: none; position: fixed; top: 24px; left: 50%; transform: translateX(-50%); z-index: 99999; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(8px); padding: 8px 22px; border-radius: 30px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); border: 1px solid rgba(16, 185, 129, 0.3); font-size: 13px; font-weight: 600; color: #065f46; pointer-events: none;">
            <i class="fas fa-spinner fa-spin mr-2" style="color: #10b981;"></i> Memuat Peta & Layer Spasial...
        </div>
        <!-- Hidden compatibility modal container -->
        <div id="loadingModal" style="display: none;" aria-hidden="true"></div>
        <div style="position: relative;">
            <div id="maps" style="height: 1000px; max-width: 100% !important;"></div>
            <!-- Header Floating Glassmorphism di Pojok Kiri Atas Peta -->
            <div class="map-header-card">
                <div class="d-flex align-items-center">
                    <img src="{{ url('assets/images/header-login.png') }}" alt="Logo SILILA" style="height: 40px; object-fit: contain;">
                    <img src="{{ url('assets/images/Banyuwangi.png') }}" alt="Logo Banyuwangi" style="height: 40px; object-fit: contain; margin-left: 10px;">
                </div>
                <div style="margin-left: 8px; border-left: 2px solid #e2e8f0; padding-left: 14px; display: flex; flex-direction: column; justify-content: center;">
                    <div class="d-flex align-items-center">
                        <h5 ondblclick="window.location.href='{{ route('login') }}'" style="margin: 0; font-family: var(--font-heading); font-weight: 800; color: #0f172a; font-size: 17px; letter-spacing: 0.5px; cursor: pointer;" title="Klik dua kali untuk login">SILILA</h5>
                        <span style="background: var(--silila-emerald-50); color: var(--silila-emerald-700); font-size: 10.5px; font-weight: 700; padding: 2px 8px; border-radius: 20px; border: 1px solid var(--silila-emerald-200); margin-left: 8px; letter-spacing: 0.05em;">GIS BANYUWANGI</span>
                    </div>
                    <span style="font-size: 11.5px; color: #64748b; font-weight: 500; margin-top: 1px;">Sistem Informasi Perlindungan Lahan Pertanian & LSD</span>
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
          <button type="submit" class="mb-2 btn btn-silila-emerald px-4 shadow-sm">
            <i class="material-icons mr-1" style="font-size: 16px;">search</i> Cari
          </button>
      </div>
    </form>
    </div>
    <hr>
    <div style="display: flex; justify-content: center; align-items: center;">
      <button type="button" class="mb-2 btn btn-silila-emerald px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#informasi">
        <i class="material-icons mr-1" style="font-size: 16px;">info</i> Lihat Informasi Lahan
      </button>
    </div>
    <div style="display: flex; justify-content: center; align-items: center;">
      @auth
        <div class="dropdown mb-2">
            <button class="btn btn-silila-emerald dropdown-toggle px-4 shadow-sm" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
      @else
      <button type="button" class="mb-2 btn btn-silila-emerald px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#login">
        <i class="material-icons mr-1" style="font-size: 16px;">login</i> Login
      </button>
      @endauth
    </div>
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
          {{-- modal profil --}}
          <div class="modal fade" style="z-index: 99999;" id="profile" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md modal-dialog-centered">
              <div class="modal-content" style="border-radius: 20px; overflow: hidden; border: none; box-shadow: 0 15px 35px rgba(0,0,0,0.15);">
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%) !important; padding: 18px 24px;">
                  <div class="d-flex align-items-center">
                    <i class="material-icons mr-2 text-white" style="font-size: 24px;">manage_accounts</i>
                    <h5 class="modal-title font-weight-bold mb-0 text-white" style="font-family: var(--font-heading);">Update Profil Pengguna</h5>
                  </div>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                  <form method="POST" action="{{ route('profile.update', Auth::user()->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="text-center mb-4">
                        <div class="position-relative d-inline-block mb-2">
                            <img id="avatar-preview-home" class="user-avatar rounded-circle shadow" 
                                 src="{{ Auth::user()->foto ? asset('storage/' . Auth::user()->foto) : url('assets/images/avatars/0.jpg') }}" 
                                 alt="User Avatar"
                                 onerror="this.onerror=null; this.src='{{ url('assets/images/avatars/0.jpg') }}';"
                                 style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #10b981;">
                        </div>
                        <div>
                            <label for="profile-photo-input-home" class="btn btn-sm btn-outline-success btn-pill px-3 py-1 cursor-pointer" style="font-weight: 600;">
                                <i class="material-icons mr-1" style="font-size: 16px; vertical-align: -3px;">photo_camera</i> Ganti Foto Profil
                            </label>
                            <input type="file" name="foto" id="profile-photo-input-home" style="display: none;" accept="image/jpeg,image/png,image/jpg,image/webp" onchange="previewProfilePhoto(this, 'avatar-preview-home')">
                        </div>
                        <small class="text-muted d-block mt-1" style="font-size: 11px;">Maksimal 2MB (JPG, PNG, WEBP)</small>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label font-weight-bold" style="font-size: 13px;">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" value="{{ Auth::user()->nama }}" style="border-radius: 10px;" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label font-weight-bold" style="font-size: 13px;">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ Auth::user()->email }}" style="border-radius: 10px;" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label font-weight-bold" style="font-size: 13px;">NIP</label>
                        <input type="text" name="nip" class="form-control" value="{{ Auth::user()->nip }}" style="border-radius: 10px;">
                    </div>
                    <div class="form-group mb-4">
                        <label class="form-label font-weight-bold" style="font-size: 13px;">Password Baru</label>
                        <input type="password" name="password" class="form-control" placeholder="Kosongkan bila tidak ingin mengganti password" style="border-radius: 10px;">
                    </div>
                    <div class="modal-footer border-top pt-3 pb-0 px-0 d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 10px;">Batal</button>
                        <button class="btn btn-success text-white shadow-sm" type="submit" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); border-radius: 10px; border: none; font-weight: 600;">Simpan Perubahan</button>
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
          <div class="modal-header text-white" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%) !important; border-top-left-radius: 16px; border-top-right-radius: 16px;">
            <div class="d-flex align-items-center gap-2">
              <i class="material-icons text-white" style="font-size: 22px;">support_agent</i>
              <h5 class="modal-title font-weight-bold" id="dataDiriLabel" style="color: white; font-family: var(--font-heading); margin-bottom: 0;">Hubungkan dengan Admin SILILA</h5>
            </div>
            <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="opacity: 0.9; font-size: 24px; text-shadow: none;">
                <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body p-4">
            <p class="text-center text-muted" style="font-size: 13.5px; margin-bottom: 20px;">Lengkapi data diri dan koordinat lahan untuk memulai sesi konsultasi langsung dengan petugas SILILA.</p>
            <form id="form-data-diri">
              <div class="mb-3">
                <label for="guest-nik" class="form-label font-weight-bold" style="font-size: 13px;">Nomor Induk Kependudukan (NIK)</label>
                <input type="text" class="form-control rounded-pill" id="guest-nik" placeholder="Masukkan 16 digit NIK" required>
              </div>
              <div class="mb-3">
                <label for="guest-nama" class="form-label font-weight-bold" style="font-size: 13px;">Nama Lengkap</label>
                <input type="text" class="form-control rounded-pill" id="guest-nama" placeholder="Masukkan Nama Lengkap Anda" required>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="guest-lat" class="form-label font-weight-bold" style="font-size: 13px;">Latitude</label>
                  <input type="text" class="form-control rounded-pill" id="guest-lat" placeholder="Contoh: -8.188387" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="guest-lng" class="form-label font-weight-bold" style="font-size: 13px;">Longitude</label>
                  <input type="text" class="form-control rounded-pill" id="guest-lng" placeholder="Contoh: 114.295038" required>
                </div>
              </div>
              <div class="modal-footer d-flex justify-content-center border-0 mt-3 p-0">
                <button type="submit" class="btn px-4 py-2 font-weight-bold text-white shadow-sm" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); border-radius: 25px; border: none;">Hubungkan dengan Admin</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    {{-- Modal Form Permohonan Surat & Upload Dokumen Spasial --}}
    <div class="modal fade" style="z-index: 999999;" id="permohonanFormModal" tabindex="-1" aria-labelledby="permohonanFormLabel" aria-hidden="true" data-bs-backdrop="static">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content style-silila-modal" style="border-radius: 16px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
          <div class="modal-header text-white" style="background: linear-gradient(135deg, #059669 0%, #047857 100%) !important; border-top-left-radius: 16px; border-top-right-radius: 16px; padding: 18px 24px;">
            <div class="d-flex align-items-center gap-2">
              <i class="material-icons text-white" style="font-size: 26px;">description</i>
              <div>
                <h5 class="modal-title font-weight-bold" id="permohonanFormLabel" style="color: white; font-family: var(--font-heading); margin-bottom: 2px;">Form Permohonan Surat Keterangan Kesesuaian Lahan</h5>
                <div style="font-size: 11.5px; color: #a7f3d0;">Layanan Resmi LP2B &amp; LSD Kab. Banyuwangi</div>
              </div>
            </div>
            <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="opacity: 0.9; font-size: 24px; text-shadow: none;">
                <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body p-4" style="background-color: #f8fafc;">
            <form id="form-permohonan-surat" enctype="multipart/form-data">
              @csrf
              <!-- Section 1: Data Pemohon -->
              <div class="card mb-3 border-0 shadow-sm" style="border-radius: 12px; background: #ffffff;">
                <div class="card-header bg-transparent font-weight-bold text-success d-flex align-items-center gap-2" style="font-size: 14px; border-bottom: 1px solid #f1f5f9;">
                  <i class="material-icons" style="font-size: 18px; color: #059669;">person</i> 1. Informasi Data Pemohon
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label for="form-nama-pemohon" class="form-label font-weight-bold" style="font-size: 12.5px; color: #334155;">Nama Lengkap Pemohon <span class="text-danger">*</span></label>
                      <input type="text" class="form-control form-control-sm rounded-lg" id="form-nama-pemohon" name="nama_pemohon" placeholder="Sesuai KTP" required>
                    </div>
                    <div class="col-md-6 mb-3">
                      <label for="form-nik" class="form-label font-weight-bold" style="font-size: 12.5px; color: #334155;">NIK Pemohon (16 Digit) <span class="text-danger">*</span></label>
                      <input type="text" class="form-control form-control-sm rounded-lg" id="form-nik" name="nik" placeholder="3510xxxxxxxxxxxx" required>
                    </div>
                    <div class="col-md-6 mb-3">
                      <label for="form-no-hp" class="form-label font-weight-bold" style="font-size: 12.5px; color: #334155;">No. HP / WhatsApp <span class="text-danger">*</span></label>
                      <input type="text" class="form-control form-control-sm rounded-lg" id="form-no-hp" name="no_hp" placeholder="08xxxxxxxxxx" required>
                    </div>
                    <div class="col-md-6 mb-3">
                      <label for="form-alamat-pemohon" class="form-label font-weight-bold" style="font-size: 12.5px; color: #334155;">Alamat Lengkap Pemohon <span class="text-danger">*</span></label>
                      <input type="text" class="form-control form-control-sm rounded-lg" id="form-alamat-pemohon" name="alamat_pemohon" placeholder="Dusun, RT/RW, Desa, Kecamatan" required>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Section 2: Informasi Lokasi Lahan -->
              <div class="card mb-3 border-0 shadow-sm" style="border-radius: 12px; background: #ffffff;">
                <div class="card-header bg-transparent font-weight-bold text-success d-flex align-items-center gap-2" style="font-size: 14px; border-bottom: 1px solid #f1f5f9;">
                  <i class="material-icons" style="font-size: 18px; color: #059669;">location_on</i> 2. Informasi Lokasi &amp; Luas Lahan
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label for="form-kecamatan" class="form-label font-weight-bold" style="font-size: 12.5px; color: #334155;">Kecamatan Lahan <span class="text-danger">*</span></label>
                      <select class="form-control form-control-sm rounded-lg" id="form-kecamatan" name="kecamatan" required>
                        <option value="">-- Pilih Kecamatan --</option>
                        <option value="Banyuwangi">Banyuwangi</option>
                        <option value="Bangorejo">Bangorejo</option>
                        <option value="Blimbingsari">Blimbingsari</option>
                        <option value="Cluring">Cluring</option>
                        <option value="Gambiran">Gambiran</option>
                        <option value="Genteng">Genteng</option>
                        <option value="Giri">Giri</option>
                        <option value="Glagah">Glagah</option>
                        <option value="Glenmore">Glenmore</option>
                        <option value="Kabat">Kabat</option>
                        <option value="Kalibaru">Kalibaru</option>
                        <option value="Kalipuro">Kalipuro</option>
                        <option value="Licin">Licin</option>
                        <option value="Muncar">Muncar</option>
                        <option value="Pesanggaran">Pesanggaran</option>
                        <option value="Purwoharjo">Purwoharjo</option>
                        <option value="Rogojampi">Rogojampi</option>
                        <option value="Sempu">Sempu</option>
                        <option value="Siliragung">Siliragung</option>
                        <option value="Singojuruh">Singojuruh</option>
                        <option value="Songgon">Songgon</option>
                        <option value="Srono">Srono</option>
                        <option value="Tegaldlimo">Tegaldlimo</option>
                        <option value="Tegalsari">Tegalsari</option>
                        <option value="Wongsorejo">Wongsorejo</option>
                      </select>
                    </div>
                    <div class="col-md-6 mb-3">
                      <label for="form-desa" class="form-label font-weight-bold" style="font-size: 12.5px; color: #334155;">Desa / Kelurahan Lahan <span class="text-danger">*</span></label>
                      <input type="text" class="form-control form-control-sm rounded-lg" id="form-desa" name="desa" placeholder="Nama Desa/Kelurahan" required>
                    </div>
                    <div class="col-md-6 mb-3">
                      <label for="form-alamat-lahan" class="form-label font-weight-bold" style="font-size: 12.5px; color: #334155;">Alamat Detail Lahan <span class="text-danger">*</span></label>
                      <input type="text" class="form-control form-control-sm rounded-lg" id="form-alamat-lahan" name="alamat_lahan" placeholder="Blok / Dusun / No. Persil" required>
                    </div>
                    <div class="col-md-3 mb-3">
                      <label for="form-luas-lahan" class="form-label font-weight-bold" style="font-size: 12.5px; color: #334155;">Luas Lahan <span class="text-danger">*</span></label>
                      <input type="text" class="form-control form-control-sm rounded-lg" id="form-luas-lahan" name="luas_lahan" placeholder="Contoh: 1500 m²" required>
                    </div>
                    <div class="col-md-3 mb-3">
                      <label for="form-koordinat" class="form-label font-weight-bold" style="font-size: 12.5px; color: #334155;">Titik Koordinat (Lat, Long)</label>
                      <input type="text" class="form-control form-control-sm rounded-lg" id="form-koordinat" name="koordinat" placeholder="-8.2188, 114.3644">
                    </div>
                  </div>
                </div>
              </div>

              <!-- Section 3: Bagian Khusus Upload Dokumen Pendukung -->
              <div class="card mb-3 border-0 shadow-sm" style="border-radius: 12px; background: #ffffff;">
                <div class="card-header bg-transparent font-weight-bold text-success d-flex align-items-center gap-2" style="font-size: 14px; border-bottom: 1px solid #f1f5f9;">
                  <i class="material-icons" style="font-size: 18px; color: #059669;">cloud_upload</i> 3. Upload Dokumen Pendukung Permohonan
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label for="file_ktp" class="form-label font-weight-bold" style="font-size: 12px; color: #334155;">📁 Upload KTP Pemohon <span class="text-danger">*</span></label>
                      <input type="file" class="form-control form-control-sm" id="file_ktp" name="file_ktp" accept=".pdf,.jpg,.jpeg,.png" required>
                      <small class="text-muted" style="font-size: 11px;">Format PDF/JPG/PNG (Maks 5MB)</small>
                    </div>
                    <div class="col-md-6 mb-3">
                      <label for="file_petok_c" class="form-label font-weight-bold" style="font-size: 12px; color: #334155;">📁 Upload Copy Petok C / Sertifikat <span class="text-danger">*</span></label>
                      <input type="file" class="form-control form-control-sm" id="file_petok_c" name="file_petok_c" accept=".pdf,.jpg,.jpeg,.png" required>
                      <small class="text-muted" style="font-size: 11px;">Format PDF/JPG/PNG (Maks 5MB)</small>
                    </div>
                    <div class="col-md-6 mb-3">
                      <label for="file_skt_kades" class="form-label font-weight-bold" style="font-size: 12px; color: #334155;">📁 Upload SKT Kades (Surat Keterangan Tanah) <span class="text-danger">*</span></label>
                      <input type="file" class="form-control form-control-sm" id="file_skt_kades" name="file_skt_kades" accept=".pdf,.jpg,.jpeg,.png" required>
                      <small class="text-muted" style="font-size: 11px;">Surat dari Kepala Desa (PDF/JPG/PNG)</small>
                    </div>
                    <div class="col-md-6 mb-3">
                      <label for="file_penguasaan_fisik" class="form-label font-weight-bold" style="font-size: 12px; color: #334155;">📁 Upload Surat Penguasaan Fisik Bidang Tanah <span class="text-danger">*</span></label>
                      <input type="file" class="form-control form-control-sm" id="file_penguasaan_fisik" name="file_penguasaan_fisik" accept=".pdf,.jpg,.jpeg,.png" required>
                      <small class="text-muted" style="font-size: 11px;">Surat Pernyataan Penguasaan Fisik (PDF/JPG/PNG)</small>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Section 4: Fitur Spasial (Polygon Peta & Upload SHP) -->
              <div class="card mb-3 border-0 shadow-sm" style="border-radius: 12px; background: #ffffff; border: 1.5px dashed #059669 !important;">
                <div class="card-header bg-transparent font-weight-bold text-success d-flex align-items-center justify-content-between" style="font-size: 14px; border-bottom: 1px solid #f1f5f9;">
                  <div class="d-flex align-items-center gap-2">
                    <i class="material-icons" style="font-size: 20px; color: #059669;">map</i> 4. Fitur Spasial Polygon Area Lahan
                  </div>
                  <span class="badge bg-success text-white" style="font-weight: 500; font-size: 11px;">Interaktif Spasial</span>
                </div>
                <div class="card-body">
                  <p class="text-muted" style="font-size: 12px; margin-bottom: 15px;">Lengkapi data spasial area lahan Anda dengan memilih salah satu atau kedua opsi di bawah ini:</p>
                  
                  <div class="row">
                    <!-- Option A: Draw Polygon on Map -->
                    <div class="col-md-6 mb-3">
                      <div class="p-3 border rounded-lg h-100" style="background: #f0fdf4; border-color: #bbf7d0 !important;">
                        <h6 class="font-weight-bold text-success mb-2" style="font-size: 13px;">
                          <i class="material-icons" style="font-size: 16px; vertical-align: middle;">draw</i> Opsi 1: Menggambar Area Lahan di Peta
                        </h6>
                        <p class="text-muted" style="font-size: 11.5px; line-height: 1.4; margin-bottom: 12px;">Klik tombol di bawah ini untuk membuka peta dan menandai batas-batas polygon area lahan secara langsung.</p>
                        <button type="button" class="btn btn-sm btn-success w-100 font-weight-bold shadow-sm" id="btn-start-draw-polygon" style="border-radius: 8px; background: #059669;">
                          ✏️ Gambar Polygon di Peta
                        </button>
                        <div id="polygon-status-text" class="mt-2 text-center" style="font-size: 11px; font-weight: 600; color: #047857; display: none;">
                          ✅ Polygon Lahan Berhasil Digambar!
                        </div>
                      </div>
                    </div>

                    <!-- Option B: Upload SHP file -->
                    <div class="col-md-6 mb-3">
                      <div class="p-3 border rounded-lg h-100" style="background: #f8fafc; border-color: #cbd5e1 !important;">
                        <h6 class="font-weight-bold text-secondary mb-2" style="font-size: 13px;">
                          <i class="material-icons" style="font-size: 16px; vertical-align: middle;">folder_zip</i> Opsi 2: Upload File SHP (Shapefile)
                        </h6>
                        <p class="text-muted" style="font-size: 11.5px; line-height: 1.4; margin-bottom: 8px;">Upload arsip Zip (.zip) Shapefile, file .shp, atau GeoJSON (.geojson / .json) polygon lahan.</p>
                        <input type="file" class="form-control form-control-sm" id="file_shp" name="file_shp" accept=".zip,.shp,.geojson,.json,.dbf">
                        <small class="text-muted" style="font-size: 10.5px;">Mendukung format .zip, .shp, .geojson, .json</small>
                      </div>
                    </div>
                  </div>

                  <input type="hidden" id="geojson_polygon" name="geojson_polygon">
                </div>
              </div>

              <!-- Footer Buttons -->
              <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="button" class="btn btn-light px-4 font-weight-bold" data-dismiss="modal" data-bs-dismiss="modal" style="border-radius: 20px;">Batal</button>
                <button type="submit" class="btn px-4 font-weight-bold text-white shadow" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); border-radius: 20px; border: none;" id="btn-submit-permohonan-surat">
                  🚀 Kirim Permohonan Surat &amp; Dokumen
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Drawing Banner Overlay (Displayed when user clicks "Gambar Polygon di Peta") -->
    <div id="silila-draw-banner" style="display: none; position: fixed; top: 80px; left: 50%; transform: translateX(-50%); z-index: 9999999; background: #0f172a; color: white; padding: 12px 24px; border-radius: 30px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); font-size: 13px; align-items: center; gap: 12px;">
      <span>📌 <strong>Mode Gambar Polygon Lahan Aktif:</strong> Klik pada peta untuk membuat titik-titik polygon area lahan Anda.</span>
      <button type="button" class="btn btn-sm btn-success font-weight-bold px-3" id="btn-finish-draw" style="border-radius: 20px;">✅ Selesai Gambar</button>
      <button type="button" class="btn btn-sm btn-outline-light font-weight-bold px-3" id="btn-cancel-draw" style="border-radius: 20px;">❌ Batal</button>
    </div>

    <!-- Chatbot Widget (Spacious Geospatial Assistant Hub) -->
    <div id="chatbot-widget">
        <!-- Chatbot Greeting Tooltip -->
        <div id="chatbot-greeting">
            <span style="font-size: 18px;">🌱</span>
            <span>Butuh bantuan cek lahan Banyuwangi? <strong>Tanya Asisten</strong></span>
        </div>

        <!-- Chatbot Launcher Button -->
        <button id="chatbot-toggle" type="button" title="Buka Asisten Virtual SILILA">
            <div class="pulse-ring"></div>
            <div class="toggle-icon-wrap">
                <i class="material-icons" style="font-size: 20px;">chat_bubble</i>
            </div>
            <span class="toggle-text">Asisten SILILA</span>
        </button>

        <!-- Chatbot Window (Expanded & Elegant Panel) -->
        <div id="chatbot-window" style="display: none;">
            <!-- Header -->
            <div class="silila-chat-header">
                <div class="d-flex align-items-center">
                    <div class="silila-chat-avatar">
                        <i class="material-icons text-white" style="font-size: 22px;">smart_toy</i>
                        <span class="silila-chat-status-dot"></span>
                    </div>
                    <div style="margin-left: 12px;">
                        <h5 class="silila-chat-title">Asisten Virtual SILILA</h5>
                        <p class="silila-chat-subtitle">
                            <i class="fas fa-circle" style="font-size: 7px; color: #34d399;"></i> Online &bull; Siap Membantu Lahan Banyuwangi
                        </p>
                    </div>
                </div>
                <div class="silila-chat-actions">
                    <button type="button" id="chatbot-restart" title="Bersihkan & Mulai Ulang">
                        <i class="material-icons" style="font-size: 18px;">restart_alt</i>
                    </button>
                    <button type="button" id="chatbot-close" title="Tutup Asisten">
                        <i class="material-icons" style="font-size: 18px;">close</i>
                    </button>
                </div>
            </div>

            <!-- Quick Action Chips Bar -->
            <div class="silila-quick-chips">
                <button type="button" class="silila-chip" data-quick="option-1-chat">💬 Opsi 1: Chat Admin</button>
                <button type="button" class="silila-chip" data-quick="option-2-surat">📄 Opsi 2: Permohonan Surat</button>
                <button type="button" class="silila-chip" data-quick="cek-lp2b">🌱 Cek LP2B</button>
                <button type="button" class="silila-chip" data-quick="cek-lsd">🌾 Cek LSD</button>
            </div>
            
            <!-- Messages Container -->
            <div id="chatbot-messages">
                <div class="chat-msg-bot">
                    <div class="d-flex align-items-center mb-1" style="font-weight: 700; color: #059669; font-size: 11.5px; gap: 4px;">
                        <i class="material-icons" style="font-size: 14px;">eco</i> Asisten SILILA
                    </div>
                    Halo! Selamat datang di <strong>Sistem Informasi Perlindungan Lahan Banyuwangi (SILILA)</strong>.
                    <div class="mt-2" style="font-size: 12.5px; color: #334155; line-height: 1.5;">
                        Silakan pilih menu utama layanan yang Anda butuhkan di bawah ini:
                    </div>
                    <div class="mt-3 d-flex flex-column" style="gap: 8px;">
                        <button type="button" class="btn-silila-main-opt" onclick="selectBotMainOption(1)">
                            <i class="material-icons" style="font-size: 20px; color: #059669;">chat</i>
                            <div style="flex: 1;">
                                <div style="font-weight: 700; color: #0f172a; font-size: 13px;">Opsi 1: Chat Admin</div>
                                <div style="font-size: 11px; color: #64748b;">Konsultasi &amp; tanya jawab langsung dengan petugas admin</div>
                            </div>
                        </button>
                        <button type="button" class="btn-silila-main-opt" onclick="selectBotMainOption(2)">
                            <i class="material-icons" style="font-size: 20px; color: #d97706;">description</i>
                            <div style="flex: 1;">
                                <div style="font-weight: 700; color: #0f172a; font-size: 13px;">Opsi 2: Mengajukan Surat Permohonan</div>
                                <div style="font-size: 11px; color: #64748b;">Panduan interaktif Surat Keterangan Kesesuaian Lahan (LP2B &amp; LSD)</div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Input Bar -->
            <div class="silila-chat-input-bar">
                <input type="text" id="chatbot-input" placeholder="Ketik pertanyaan atau titik koordinat..." autocomplete="off">
                <button id="chatbot-send" type="button" title="Kirim Pesan">
                    <i class="material-icons" style="font-size: 20px;">send</i>
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

                const isHidden = (chatWindow.style.display === 'none') || (window.getComputedStyle(chatWindow).display === 'none');
                if (isHidden) {
                    chatWindow.style.display = 'flex';
                    chatWindow.style.flexDirection = 'column';
                    if (liveChatSessionId) {
                        fetch(`/chat/${liveChatSessionId}/read`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ reader_type: 'user' })
                        }).catch(e => console.log('Read mark error:', e));
                    }
                    setTimeout(() => {
                        if (chatInput) chatInput.focus();
                    }, 100);
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
                msgDiv.className = sender === 'user' ? 'chat-msg-user' : 'chat-msg-bot';

                if (sender === 'user') {
                    const textSpan = document.createElement('span');
                    textSpan.innerHTML = text.replace(/\n/g, '<br>');
                    msgDiv.appendChild(textSpan);

                    if (id) {
                        msgDiv.setAttribute('data-msg-id', id);
                        const tickDiv = document.createElement('div');
                        tickDiv.style.textAlign = 'right';
                        tickDiv.style.marginTop = '2px';
                        tickDiv.className = 'msg-tick';
                        if (isRead) {
                            tickDiv.innerHTML = '<i class="fas fa-check-double" style="font-size:10px; color:#a7f3d0;"></i>';
                            tickDiv.dataset.read = 'true';
                        } else {
                            tickDiv.innerHTML = '<i class="fas fa-check" style="font-size:10px; color:rgba(255,255,255,0.7);"></i>';
                            tickDiv.dataset.read = 'false';
                        }
                        msgDiv.appendChild(tickDiv);
                    } else {
                        msgDiv.classList.add('pending-msg');
                    }
                } else {
                    const botHeader = document.createElement('div');
                    botHeader.className = 'd-flex align-items-center mb-1';
                    botHeader.style.cssText = 'font-weight: 700; color: #059669; font-size: 11.5px; gap: 4px;';
                    botHeader.innerHTML = '<i class="material-icons" style="font-size: 14px;">eco</i> Asisten SILILA';
                    msgDiv.appendChild(botHeader);

                    const contentSpan = document.createElement('div');
                    // Render bold formatting and newlines
                    let formatted = text
                        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                        .replace(/\n/g, '<br>');
                    contentSpan.innerHTML = formatted;
                    msgDiv.appendChild(contentSpan);
                }

                messagesContainer.appendChild(msgDiv);
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
                
                return msgDiv;
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

            // State penanganan permohonan surat interaktif (Opsi 2)
            window.permohonanState = {
                active: false,
                step: 0,
                data: {
                    nama_pemohon: '',
                    nik: '',
                    no_hp: '',
                    alamat_pemohon: '',
                    kecamatan: '',
                    desa: '',
                    alamat_lahan: '',
                    luas_lahan: '',
                    koordinat: '',
                    dokumen_pendukung: ''
                }
            };

            window.selectBotMainOption = function(opt) {
                if (opt === 1) {
                    window.permohonanState.active = false;
                    window.permohonanState.step = 0;
                    appendMessage("Opsi 1: Chat Admin", 'user');
                    setTimeout(() => {
                        appendMessage("👨‍💼 Anda memilih **Opsi 1: Chat Admin**.\n\nFitur ini digunakan bagi pemohon yang ingin bertanya-tanya atau berkonsultasi informasi awal secara langsung dengan petugas admin Dinas Pertanian & Pangan Kab. Banyuwangi.\n\nSilakan isi data diri Anda pada formulir yang muncul untuk dihubungkan langsung ke sesi Live Chat Admin:", 'bot');
                        setTimeout(() => {
                            $('#dataDiriModal').modal('show');
                        }, 500);
                    }, 400);
                } else if (opt === 2) {
                    window.startPermohonanFlow();
                }
            };

            window.startPermohonanFlow = function() {
                window.permohonanState.active = true;
                window.permohonanState.step = 1;

                appendMessage("Opsi 2: Mengajukan Surat Permohonan", 'user');
                setTimeout(() => {
                    var botMsg = "📄 Anda memilih **Opsi 2: Mengajukan Surat Permohonan**.\n\nFormulir permohonan **Surat Keterangan Kesesuaian Lahan (LP2B & LSD)** interaktif telah dibuka.\n\nSilakan isi data pemohon, lokasi lahan, upload dokumen pendukung (KTP, Petok C, SKT Kades, Surat Penguasaan Fisik), serta tentukan data spasial polygon lahan Anda pada formulir tersebut.\n\n<button type='button' class='btn btn-sm btn-success mt-2 font-weight-bold' onclick=\"$('#permohonanFormModal').modal('show')\" style='border-radius:15px;'><i class='material-icons' style='font-size:16px; vertical-align:middle;'>assignment</i> Buka Form Permohonan & Upload Dokumen</button>";
                    appendMessage(botMsg, 'bot');
                    setTimeout(() => {
                        $('#permohonanFormModal').modal('show');
                    }, 400);
                }, 300);
            };

            window.handlePermohonanInput = function(inputVal) {
                const val = inputVal.trim();
                if (!val) return;

                switch (window.permohonanState.step) {
                    case 1:
                        window.permohonanState.data.nama_pemohon = val;
                        window.permohonanState.step = 2;
                        setTimeout(() => {
                            appendMessage("📌 **Langkah 2 dari 10**:\nMasukkan **16 Digit NIK Pemohon** (Nomor Induk Kependudukan):", 'bot');
                        }, 300);
                        break;
                    case 2:
                        if (val.length < 8 || isNaN(val)) {
                            setTimeout(() => {
                                appendMessage("⚠️ Format NIK harus berupa angka (minimal 8-16 digit). Silakan masukkan NIK Anda kembali:", 'bot');
                            }, 300);
                            return;
                        }
                        window.permohonanState.data.nik = val;
                        window.permohonanState.step = 3;
                        setTimeout(() => {
                            appendMessage("📌 **Langkah 3 dari 10**:\nMasukkan **Nomor HP / WhatsApp Active** yang dapat dihubungi:", 'bot');
                        }, 300);
                        break;
                    case 3:
                        window.permohonanState.data.no_hp = val;
                        window.permohonanState.step = 4;
                        setTimeout(() => {
                            appendMessage("📌 **Langkah 4 dari 10**:\nMasukkan **Alamat Lengkap Domisili Pemohon**:", 'bot');
                        }, 300);
                        break;
                    case 4:
                        window.permohonanState.data.alamat_pemohon = val;
                        window.permohonanState.step = 5;
                        setTimeout(() => {
                            appendMessage("📌 **Langkah 5 dari 10**:\nKetik nama **Kecamatan** lokasi lahan di Kab. Banyuwangi (contoh: *Banyuwangi, Genteng, Rogojampi, Kabat, Singojuruh, dll*):", 'bot');
                        }, 300);
                        break;
                    case 5:
                        window.permohonanState.data.kecamatan = val;
                        window.permohonanState.step = 6;
                        setTimeout(() => {
                            appendMessage("📌 **Langkah 6 dari 10**:\nKetik nama **Desa / Kelurahan** lokasi lahan:", 'bot');
                        }, 300);
                        break;
                    case 6:
                        window.permohonanState.data.desa = val;
                        window.permohonanState.step = 7;
                        setTimeout(() => {
                            appendMessage("📌 **Langkah 7 dari 10**:\nMasukkan **Alamat Detail Lokasi Lahan** (Blok / Dusun / No. Persil / RT RW):", 'bot');
                        }, 300);
                        break;
                    case 7:
                        window.permohonanState.data.alamat_lahan = val;
                        window.permohonanState.step = 8;
                        setTimeout(() => {
                            appendMessage("📌 **Langkah 8 dari 10**:\nMasukkan **Luas Lahan** (contoh: `1500 m²` atau `0.15 ha`):", 'bot');
                        }, 300);
                        break;
                    case 8:
                        window.permohonanState.data.luas_lahan = val;
                        window.permohonanState.step = 9;
                        setTimeout(() => {
                            appendMessage("📌 **Langkah 9 dari 10**:\nMasukkan **Titik Koordinat Lahan** (Latitude, Longitude), contoh: `-8.188, 114.295` (atau ketik `-` jika belum tahu):", 'bot');
                        }, 300);
                        break;
                    case 9:
                        window.permohonanState.data.koordinat = val;
                        
                        let coords = val.replace(/,/g, ' ').replace(/\s+/g, ' ').trim().split(' ');
                        if (coords.length === 2 && !isNaN(parseFloat(coords[0])) && !isNaN(parseFloat(coords[1]))) {
                            let lat = parseFloat(coords[0]);
                            let lng = parseFloat(coords[1]);
                            if (Math.abs(lat) > Math.abs(lng)) {
                                let temp = lat; lat = lng; lng = temp;
                            }
                            if (typeof window.performCoordinateSearch === 'function') {
                                window.performCoordinateSearch('latlng', lat, lng, '', '');
                            }
                        }

                        window.permohonanState.step = 10;
                        setTimeout(() => {
                            appendMessage("📌 **Langkah 10 dari 10**:\nSebutkan **Dokumen Pendukung Kepemilikan Lahan** (contoh: `Sertifikat Hak Milik No. 1234 / SPPT PBB / Surat Keterangan Tanah`):", 'bot');
                        }, 300);
                        break;
                    case 10:
                        window.permohonanState.data.dokumen_pendukung = val;
                        window.permohonanState.step = 11;
                        setTimeout(() => {
                            window.renderPermohonanSummary();
                        }, 300);
                        break;
                }
            };

            window.renderPermohonanSummary = function() {
                const d = window.permohonanState.data;
                const summaryHtml = `
📋 <strong>RINGKASAN PERMOHONAN SURAT KESESUAIAN LAHAN</strong>
<hr style="margin: 8px 0; border-color: #cbd5e1;">
<div style="font-size: 12px; line-height: 1.6; color: #1e293b;">
  <div><b>• Jenis Surat:</b> Surat Keterangan Kesesuaian Lahan (LP2B &amp; LSD)</div>
  <div><b>• Nama Pemohon:</b> ${d.nama_pemohon}</div>
  <div><b>• NIK Pemohon:</b> ${d.nik}</div>
  <div><b>• No. HP/WA:</b> ${d.no_hp}</div>
  <div><b>• Alamat Pemohon:</b> ${d.alamat_pemohon}</div>
  <div><b>• Lokasi Lahan:</b> Desa ${d.desa}, Kec. ${d.kecamatan}</div>
  <div><b>• Alamat Lahan:</b> ${d.alamat_lahan}</div>
  <div><b>• Luas Lahan:</b> ${d.luas_lahan}</div>
  <div><b>• Koordinat:</b> ${d.koordinat}</div>
  <div><b>• Dokumen Pendukung:</b> ${d.dokumen_pendukung}</div>
</div>
<hr style="margin: 8px 0; border-color: #cbd5e1;">
<div style="font-size: 11.5px; color: #475569; margin-bottom: 10px;">
  Apakah data permohonan di atas sudah benar dan siap dikirimkan ke Dinas Pertanian &amp; Pangan Kab. Banyuwangi?
</div>
<div class="d-flex" style="gap: 8px;">
  <button type="button" class="btn btn-silila-emerald btn-sm w-100" style="background: #10b981; color: white; border-radius: 8px; font-weight: 600; padding: 6px 12px;" onclick="window.confirmSubmitPermohonan()">
     ✅ Kirim Permohonan
  </button>
  <button type="button" class="btn btn-outline-secondary btn-sm w-100" style="border-radius: 8px; font-weight: 600; padding: 6px 12px;" onclick="window.cancelPermohonanFlow()">
     🔄 Batal / Ulangi
  </button>
</div>
`;
                appendMessage(summaryHtml, 'bot');
            };

            window.confirmSubmitPermohonan = function() {
                appendMessage("✅ Kirim Permohonan", 'user');
                
                setTimeout(() => {
                    appendMessage("Mengirim data permohonan Anda ke sistem SILILA...", 'bot');

                    fetch('/permohonan/submit', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(window.permohonanState.data)
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.success) {
                            window.permohonanState.active = false;
                            window.permohonanState.step = 0;
                            
                            const resultMsg = `
🎉 <strong>PERMOHONAN SURAT BERHASIL DIAJUKAN!</strong>

Nomor Registrasi Tiket Anda:
<div style="background: #ecfdf5; border: 1.5px dashed #10b981; padding: 10px; border-radius: 10px; text-align: center; margin: 8px 0; color: #047857; font-weight: 800; font-size: 15px;">
   🔖 <code>${res.kode_registrasi}</code>
</div>

Status: <span style="background: #fef3c7; color: #d97706; padding: 4px 8px; border-radius: 6px; font-weight: 700; font-size: 11px;">Menunggu Verifikasi Tim Dinas</span>

Surat Keterangan Resmi akan menerangkan status kesesuaian lahan Anda terhadap kawasan <strong>LP2B</strong> dan <strong>LSD</strong>. Tim Dinas Pertanian &amp; Pangan Kab. Banyuwangi akan segera memverifikasi berkas serta lokasi lahan Anda.

Simpan Nomor Registrasi Tiket ini untuk keperluan pengecekan status permohonan Anda!`;
                            appendMessage(resultMsg, 'bot');
                        } else {
                            appendMessage("❌ Gagal mengirim permohonan: " + (res.message || "Terjadi kesalahan."), 'bot');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        appendMessage("❌ Terjadi kesalahan sistem saat mengirim permohonan.", 'bot');
                    });
                }, 400);
            };

            window.cancelPermohonanFlow = function() {
                window.permohonanState.active = false;
                window.permohonanState.step = 0;
                appendMessage("Pengisian permohonan dibatalkan.", 'user');
                setTimeout(() => {
                    appendMessage("Pengisian permohonan telah dibatalkan. Silakan pilih menu utama jika ingin memulai kembali.", 'bot');
                }, 300);
            };

            function sendMessage() {
                const text = chatInput.value.trim();
                if (!text) return;

                const pendingMsgDiv = appendMessage(text, 'user');
                chatInput.value = '';

                // Jika sedang dalam pengisian permohonan interaktif
                if (window.permohonanState && window.permohonanState.active) {
                    window.handlePermohonanInput(text);
                    return;
                }

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

                if (lowerText === '1' || lowerText.includes('chat admin') || lowerText.includes('opsi 1')) {
                    window.selectBotMainOption(1);
                    return;
                } else if (lowerText === '2' || lowerText.includes('permohonan') || lowerText.includes('opsi 2') || lowerText.includes('surat')) {
                    window.selectBotMainOption(2);
                    return;
                }

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
                    } else {
                        reply = "Maaf, sistem pencarian belum siap. Silakan refresh halaman.";
                    }
                } else if (lowerText.includes('lp2b')) {
                    reply = "🌱 **LP2B (Lahan Pertanian Pangan Berkelanjutan)** adalah kawasan lahan budidaya pertanian yang dilindungi untuk menjamin ketahanan pangan daerah.\n\nUntuk memproses **Surat Keterangan Resminya**, silakan pilih menu **'Opsi 2: Mengajukan Surat Permohonan'**.";
                } else if (lowerText.includes('lsd')) {
                    reply = "🌾 **LSD (Lahan Sawah Dilindungi)** adalah penetapan lahan sawah oleh pemerintah untuk mengendalikan alih fungsi lahan sawah.\n\nUntuk memproses **Surat Keterangan Kesesuaian Lahan**, silakan pilih menu **'Opsi 2: Mengajukan Surat Permohonan'**.";
                } else if (lowerText.includes('hai') || lowerText.includes('halo')) {
                    reply = "Halo! Saya Asisten Virtual SILILA Banyuwangi.\n\nSilakan pilih menu layanan:\n1️⃣ Ketik **'1'** untuk **Chat Admin**.\n2️⃣ Ketik **'2'** untuk **Mengajukan Surat Permohonan**.";
                } else {
                    reply = "Terima kasih atas pesan Anda. Silakan pilih menu utama:\n1️⃣ Ketik **'1'** atau **'Chat Admin'** untuk berkonsultasi.\n2️⃣ Ketik **'2'** atau **'Permohonan'** untuk membuat Surat Keterangan Kesesuaian Lahan (LP2B & LSD).";
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

            // Quick Action Chips Drag-to-Scroll & Mouse Wheel Handler (Geser Manual)
            const chipsBar = document.querySelector('.silila-quick-chips');
            let isChipDragging = false;
            let chipMouseDown = false;
            let chipStartX = 0;
            let chipScrollLeft = 0;

            if (chipsBar) {
                chipsBar.addEventListener('mousedown', function(e) {
                    chipMouseDown = true;
                    isChipDragging = false;
                    chipStartX = e.pageX - chipsBar.offsetLeft;
                    chipScrollLeft = chipsBar.scrollLeft;
                    chipsBar.classList.add('is-dragging');
                });

                document.addEventListener('mouseup', function() {
                    if (chipMouseDown) {
                        chipMouseDown = false;
                        if (chipsBar) chipsBar.classList.remove('is-dragging');
                        setTimeout(() => { isChipDragging = false; }, 60);
                    }
                });

                chipsBar.addEventListener('mousemove', function(e) {
                    if (!chipMouseDown) return;
                    e.preventDefault();
                    const x = e.pageX - chipsBar.offsetLeft;
                    const walk = (x - chipStartX) * 1.8;
                    if (Math.abs(x - chipStartX) > 5) {
                        isChipDragging = true;
                    }
                    chipsBar.scrollLeft = chipScrollLeft - walk;
                });

                // Dukungan geser roda mouse (Horizontal scroll via mouse wheel)
                chipsBar.addEventListener('wheel', function(e) {
                    if (e.deltaY !== 0) {
                        e.preventDefault();
                        chipsBar.scrollLeft += e.deltaY * 0.9;
                    }
                }, { passive: false });
            }

            // Quick Chips Action Listeners
            document.querySelectorAll('.silila-chip').forEach(chip => {
                chip.addEventListener('click', function(e) {
                    if (isChipDragging) {
                        e.preventDefault();
                        e.stopPropagation();
                        return;
                    }
                    e.preventDefault();
                    const action = this.getAttribute('data-quick');
                    if (action === 'option-1-chat') {
                        window.selectBotMainOption(1);
                    } else if (action === 'option-2-surat') {
                        window.selectBotMainOption(2);
                    } else if (action === 'cek-lp2b') {
                        chatInput.value = 'Bagaimana status lahan LP2B di Banyuwangi?';
                        sendMessage();
                    } else if (action === 'cek-lsd') {
                        chatInput.value = 'Apa itu Lahan Sawah Dilindungi (LSD)?';
                        sendMessage();
                    }
                });
            });

            // Restart / Clear Chat Listener
            const restartBtn = document.getElementById('chatbot-restart');
            if (restartBtn) {
                restartBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.permohonanState.active = false;
                    window.permohonanState.step = 0;
                    messagesContainer.innerHTML = '';
                    
                    const welcomeHtml = `
                    <div class="chat-msg-bot">
                        <div class="d-flex align-items-center mb-1" style="font-weight: 700; color: #059669; font-size: 11.5px; gap: 4px;">
                            <i class="material-icons" style="font-size: 14px;">eco</i> Asisten SILILA
                        </div>
                        Halo! Selamat datang di <strong>Sistem Informasi Perlindungan Lahan Banyuwangi (SILILA)</strong>.
                        <div class="mt-2" style="font-size: 12.5px; color: #334155; line-height: 1.5;">
                            Silakan pilih menu utama layanan yang Anda butuhkan di bawah ini:
                        </div>
                        <div class="mt-3 d-flex flex-column" style="gap: 8px;">
                            <button type="button" class="btn-silila-main-opt" onclick="selectBotMainOption(1)">
                                <i class="material-icons" style="font-size: 20px; color: #059669;">chat</i>
                                <div style="flex: 1;">
                                    <div style="font-weight: 700; color: #0f172a; font-size: 13px;">Opsi 1: Chat Admin</div>
                                    <div style="font-size: 11px; color: #64748b;">Konsultasi &amp; tanya jawab langsung dengan petugas admin</div>
                                </div>
                            </button>
                            <button type="button" class="btn-silila-main-opt" onclick="selectBotMainOption(2)">
                                <i class="material-icons" style="font-size: 20px; color: #d97706;">description</i>
                                <div style="flex: 1;">
                                    <div style="font-weight: 700; color: #0f172a; font-size: 13px;">Opsi 2: Mengajukan Surat Permohonan</div>
                                    <div style="font-size: 11px; color: #64748b;">Panduan interaktif Surat Keterangan Kesesuaian Lahan (LP2B &amp; LSD)</div>
                                </div>
                            </button>
                        </div>
                    </div>`;
                    messagesContainer.innerHTML = welcomeHtml;
                });
            }

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

            // Spatial Drawing state & Handlers
            window.drawnPolyPoints = [];
            window.tempPolygonLayer = null;

            window.onMapDrawClick = function(e) {
                var lat = e.latlng.lat;
                var lng = e.latlng.lng;
                window.drawnPolyPoints.push([lat, lng]);

                if (window.tempPolygonLayer && typeof map !== 'undefined' && map) {
                    map.removeLayer(window.tempPolygonLayer);
                }

                if (window.drawnPolyPoints.length >= 3) {
                    window.tempPolygonLayer = L.polygon(window.drawnPolyPoints, {
                        color: '#059669',
                        fillColor: '#10b981',
                        fillOpacity: 0.5,
                        weight: 3
                    }).addTo(map);
                } else if (window.drawnPolyPoints.length >= 2) {
                    window.tempPolygonLayer = L.polyline(window.drawnPolyPoints, {
                        color: '#059669',
                        weight: 3
                    }).addTo(map);
                }
            };

            // Start Draw Polygon Button handler
            $(document).on('click', '#btn-start-draw-polygon', function() {
                $('#permohonanFormModal').modal('hide');
                window.drawnPolyPoints = [];
                if (window.tempPolygonLayer && typeof map !== 'undefined' && map) {
                    map.removeLayer(window.tempPolygonLayer);
                }
                
                $('#silila-draw-banner').css('display', 'flex');
                
                if (typeof map !== 'undefined' && map) {
                    map.getContainer().style.cursor = 'crosshair';
                    map.on('click', window.onMapDrawClick);
                } else {
                    alert('Peta belum sepenuhnya dimuat. Silakan tunggu beberapa detik dan coba lagi.');
                }
            });

            // Finish Draw Button handler
            $(document).on('click', '#btn-finish-draw', function() {
                $('#silila-draw-banner').hide();
                if (typeof map !== 'undefined' && map) {
                    map.getContainer().style.cursor = '';
                    map.off('click', window.onMapDrawClick);
                }

                if (window.drawnPolyPoints.length >= 3) {
                    var geojsonStr = JSON.stringify(window.drawnPolyPoints);
                    $('#geojson_polygon').val(geojsonStr);
                    
                    var sumLat = 0, sumLng = 0;
                    for (var i = 0; i < window.drawnPolyPoints.length; i++) {
                        sumLat += window.drawnPolyPoints[i][0];
                        sumLng += window.drawnPolyPoints[i][1];
                    }
                    var centerLat = (sumLat / window.drawnPolyPoints.length).toFixed(6);
                    var centerLng = (sumLng / window.drawnPolyPoints.length).toFixed(6);
                    $('#form-koordinat').val(centerLat + ', ' + centerLng);
                    
                    $('#polygon-status-text').show().html('✅ Polygon Lahan (' + window.drawnPolyPoints.length + ' titik) Berhasil Digambar!');
                } else if (window.drawnPolyPoints.length > 0) {
                    alert('Minimal 3 titik koordinat untuk membentuk polygon area lahan.');
                }
                
                $('#permohonanFormModal').modal('show');
            });

            // Cancel Draw Button handler
            $(document).on('click', '#btn-cancel-draw', function() {
                $('#silila-draw-banner').hide();
                if (typeof map !== 'undefined' && map) {
                    map.getContainer().style.cursor = '';
                    map.off('click', window.onMapDrawClick);
                }
                $('#permohonanFormModal').modal('show');
            });

            // Handle file_shp upload client-side preview / GeoJSON parsing
            $(document).on('change', '#file_shp', function(e) {
                var file = e.target.files[0];
                if (!file) return;

                if (file.name.endsWith('.geojson') || file.name.endsWith('.json')) {
                    var reader = new FileReader();
                    reader.onload = function(evt) {
                        try {
                            var json = JSON.parse(evt.target.result);
                            $('#geojson_polygon').val(JSON.stringify(json));
                            
                            if (typeof map !== 'undefined' && map && json) {
                                var geoLayer = L.geoJSON(json, {
                                    style: { color: '#059669', fillColor: '#10b981', fillOpacity: 0.5 }
                                }).addTo(map);
                                map.fitBounds(geoLayer.getBounds());
                                var center = geoLayer.getBounds().getCenter();
                                $('#form-koordinat').val(center.lat.toFixed(6) + ', ' + center.lng.toFixed(6));
                            }
                            alert('GeoJSON Polygon lahan berhasil dibaca!');
                        } catch (err) {
                            console.log('Error parsing GeoJSON:', err);
                        }
                    };
                    reader.readAsText(file);
                } else {
                    alert('File Shapefile (' + file.name + ') berhasil dipilih dan akan dikirim ke server untuk diproses.');
                }
            });

            // Submit Form Permohonan Surat via AJAX
            $(document).on('submit', '#form-permohonan-surat', function(e) {
                e.preventDefault();
                
                var submitBtn = $('#btn-submit-permohonan-surat');
                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Mengirim Permohonan...');

                var formData = new FormData(this);

                $.ajax({
                    url: '{{ route("permohonan.submit") }}',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        submitBtn.prop('disabled', false).html('🚀 Kirim Permohonan Surat & Dokumen');
                        if (res.success) {
                            $('#permohonanFormModal').modal('hide');
                            $('#form-permohonan-surat')[0].reset();
                            $('#polygon-status-text').hide();

                            // Open Chatbot window if not visible
                            const chatWindow = document.getElementById('chatbot-window');
                            if (chatWindow) chatWindow.style.display = 'flex';

                            var msgSuccess = `🎉 <strong>PERMOHONAN SURAT BERHASIL DIAJUKAN!</strong><br><br>` +
                                `📋 <strong>Nomor Registrasi:</strong> <span class="badge bg-success" style="font-size:13px; color:white;">${res.kode_registrasi}</span><br>` +
                                `👤 <strong>Nama Pemohon:</strong> ${res.data.nama_pemohon}<br>` +
                                `📌 <strong>Status:</strong> ${res.data.status}<br><br>` +
                                `📁 <strong>Dokumen Terunggah:</strong> KTP, Petok C, SKT Kades, Surat Penguasaan Fisik.<br>` +
                                `🗺️ <strong>Data Spasial:</strong> ${res.data.geojson_polygon || res.data.file_shp ? '✅ Tersimpan' : 'Titik Koordinat'}<br><br>` +
                                `Surat Keterangan Resmi akan menerangkan status kesesuaian lahan Anda terhadap kawasan <strong>LP2B</strong> dan <strong>LSD</strong>. Tim Dinas Pertanian & Pangan Kab. Banyuwangi akan segera memverifikasi berkas serta lokasi lahan Anda. Harap simpan Nomor Registrasi di atas.`;

                            appendMessage(msgSuccess, 'bot');
                        } else {
                            alert('Gagal mengajukan permohonan: ' + (res.message || 'Terjadi kesalahan.'));
                        }
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).html('🚀 Kirim Permohonan Surat & Dokumen');
                        var errMessage = 'Terjadi kesalahan server saat menyimpan permohonan.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errMessage = xhr.responseJSON.message;
                        }
                        alert('Gagal: ' + errMessage);
                    }
                });
            });
        });
    </script>
    @endsection
