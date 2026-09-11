<!doctype html>
<html class="no-js h-100" lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>SILILA - @yield('title')</title>
    <meta name="description" content="SILILA, Sistem Informasi Perlindungan Lahan Kabupaten Banyuwangi.">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" id="main-stylesheet" data-version="1.1.0" href="{{ url('assets') }}/styles/shards-dashboards.1.1.0.min.css">
    <link rel="stylesheet" href="{{ url('assets') }}/styles/extras.1.1.0.min.css">
    <link rel="stylesheet" href="{{ url('assets') }}/styles/accents/danger.1.1.0.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.css" rel="stylesheet">
    <script async defer src="https://buttons.github.io/buttons.js"></script>

    <!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">

<!-- jQuery -->
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script>

<!-- DataTables JS -->
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
  <body class="h-100">
    <div class="container-fluid">
      <div class="row">
{{-- ISI PETA --}}
      </div>
    </div>
    <div class="promo-popup animated">
        <div class="pp-intro-bar">
          SILILA

          <span class="close">
            <i class="material-icons">close</i>
          </span>
          <span class="up">
            <i class="material-icons">keyboard_arrow_up</i>
          </span>
        </div>

      <div class="col-sm-12 col-md-12">
        <div style="display: flex; align-items: center;">
            <img src="/assets/images/silila.png" width="200" height="150" style="margin-right: 10px;">
            <strong class="text-muted d-block mb-2">Cari Lahan</strong>
        </div>
        {{-- <strong class="text-muted d-block mb-2">Cari Lahan</strong>
        <img src="assets/images/silila.png" width="25" height="25"> --}}
        <form>
          <div class="form-row">
            <div class="form-group col-md-12">
                <select class="form-control ">
                  <option selected>Pilih jenis derajat</option>
                  <option>Derajat Desimal</option>
                  <option>Derajat Degree</option>
                </select>
              </div>
            <div class="form-group col-md-6">
              <input type="text" class="form-control " id="validationServer01" placeholder="First name" value="Catalin" required>
              <p style="font-size: 12px; line-height: 1.2;">Contoh: 114.295038</p>
            </div>
            <div class="form-group col-md-6">
              <input type="text" class="form-control " id="validationServer02" placeholder="Last name" value="Vasile" required>
              <p style="font-size: 12px; line-height: 1.2;">Contoh: -8.188387</p>
            </div>
          </div>
          <div style="display: flex; justify-content: center; align-items: center;">
            <button type="button" class="mb-2 btn" style="background-color: #074173; color: #ffffff;">Cari</button>

          </div>
          <div class="form-check form-check-inline mt-2 mb-2">
            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1">
            <label class="form-check-label" for="inlineRadio1">LP2B</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2">
            <label class="form-check-label" for="inlineRadio2">LSD</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2">
            <label class="form-check-label" for="inlineRadio2">LBS</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2">
            <label class="form-check-label" for="inlineRadio2">Kawasan Pertanian</label>
          </div>
        </form>
      </div>

      <div style="display: flex; justify-content: center; align-items: center;">
        <button type="button" class="mb-2 btn" style="background-color: #074173; color: #ffffff;"   data-bs-toggle="modal" data-bs-target="#informasi">Informasi Lahan</button>
      </div>
      <div style="display: flex; justify-content: center; align-items: center;">
        <button type="button" class="mb-2 btn" style="background-color: #074173; color: #ffffff;" data-bs-toggle="modal" data-bs-target="#login">Login</button>
      </div>
      <div style="display: flex; justify-content: center; align-items: center;">
        <button type="button" class="mb-2 btn" style="background-color: #074173; color: #ffffff;" data-bs-toggle="modal" data-bs-target="#riwayat">Riwayat</button>
      </div>

    </div>
    <script>
        var BASE_URL = "{{ url('') }}"
    </script>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script>
    <script src="https://unpkg.com/shards-ui@latest/dist/js/shards.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sharrre/2.0.1/jquery.sharrre.min.js"></script>
    <script src="{{ url('assets') }}/scripts/extras.1.1.0.min.js"></script>
    <script src="{{ url('assets') }}/scripts/shards-dashboards.1.1.0.min.js"></script>
    <script src="{{ url('assets') }}/scripts/main.js"></script>
    <script src="{{ url('assets') }}/scripts/file-upload.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Sumber eksternal: Bootstrap Bundle (JS) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <!-- Sumber eksternal: DataTables -->
    <script src="https://cdn.datatables.net/2.0.7/js/jquery.dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.7/js/dataTables.bootstrap5.js"></script>

    <!-- Script khusus untuk halaman web Anda -->
    @stack('script')

    <script>
        // Change Datatable Button
      function change_datatable_button() {
        $('.dt-button').removeClass("dt-button");
      }

      $(document).ready(function() {
        change_datatable_button();
      })
    </script>


    {{-- modal informasi --}}
    <div class="modal fade" id="informasi" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-left">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="exampleModalLabel">Informasi Lahan</h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p style="font-size: 12px; line-height: 1.2;">Titik Koordinat</p>
                <P style="font-size: 12px; line-height: 1.2;">Kecamatan</P>
                <p style="font-size: 12px; line-height: 1.2;">Desa</p>
                <p style="font-size: 12px; line-height: 1.2;">Luas</p>
                <p style="font-size: 12px; line-height: 1.2;">Keterangan</p>
                <p style="font-size: 12px; line-height: 1.2;">Lahan</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#cetak">Cetak</button>
            </div>
          </div>
        </div>
      </div>

    {{-- modal cetak --}}
    <div class="modal fade" id="cetak" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-md modal-dialog-left">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Cetak Informasi Lahan</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <p style="font-size: 12px; line-height: 1.2;">Titik Koordinat</p>
              <P style="font-size: 12px; line-height: 1.2;">Kecamatan</P>
              <p style="font-size: 12px; line-height: 1.2;">Desa</p>
              <p style="font-size: 12px; line-height: 1.2;">Luas</p>
              <p style="font-size: 12px; line-height: 1.2;">Keterangan</p>
              <p style="font-size: 12px; line-height: 1.2;">Lahan</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary">Cetak</button>
          </div>
        </div>
      </div>
    </div>

      {{-- modal login --}}
      <div class="modal fade" id="login" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-left">
          <div class="modal-content">
            <div class="modal-header">
              <img src="assets/images/header-login.png" alt="Header Image" class="img-fluid mx-auto d-block">
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-center" style="font-size: 14px; line-height: 1.2;">Sudah punya akun?</p>
                <form>
                    <div class="mb-1">
                      <input type="text" class="form-control" id="recipient-name" placeholder="NIK">
                    </div>
                    <div class="mb-1">
                      <input type="password" class="form-control" id="recipient-name" placeholder="Password">
                    </div>
                    <p class="text-center" style="font-size: 14px; line-height: 1.2;">Belum punya akun? <a href="#" data-bs-toggle="modal" data-bs-target="#register">Register</a></p>
                  </form>
            </div>
            <div class="modal-footer d-flex justify-content-center">
                <button type="button" class="btn btn-primary">Login</button>
              </div>

          </div>
        </div>
      </div>

       {{-- modal register--}}
       <div class="modal fade" id="register" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-left">
          <div class="modal-content">
            <div class="modal-header">
              <img src="assets/images/header-login.png" alt="Header Image" class="img-fluid mx-auto d-block">
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-center" style="font-size: 14px; line-height: 1.2;">Silahkan masukkan data anda</a></p>
                <form>
                    <div class="mb-1">
                      <input type="text" class="form-control" id="recipient-name" placeholder="NIK">
                    </div>
                    <div class="mb-1">
                        <input type="text" class="form-control" id="recipient-name" placeholder="Nama">
                      </div>
                      <div class="mb-1">
                        <input type="text" class="form-control" id="recipient-name" placeholder="Email">
                      </div>
                      <p class="text-center" style="font-size: 14px; line-height: 1.2;">Sudah punya akun? <a href="#" data-bs-toggle="modal" data-bs-target="#login">Login</a></p>
                      <p style="font-size: 12px; line-height: 1.2;">Password default : 12345678</p>
                      <p style="font-size: 12px; line-height: 1.2;">Segera ganti password anda!</p>
                  </form>
            </div>
            <div class="modal-footer d-flex justify-content-center">
                <button type="button" class="btn btn-primary">Register</button>
              </div>

          </div>
        </div>
      </div>

      {{-- modal profil --}}
      <div class="modal fade" id="profile" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-left">
          <div class="modal-content">
            <div class="modal-header">
             <h5>Update Profil</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
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
                                <input type="text" class="form-control" placeholder="Email" aria-label="Email" aria-describedby="basic-addon1"> </div>
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control" id="inputPassword4" placeholder="Password" value="myCoolPassword"> </div>
                            <div class="form-group">
                                <input type="text" class="form-control" id="inputAddress" placeholder="1234 Main St" value="NIK"> </div>
                            <div class="form-row">
                                <input type="text" class="form-control" id="inputCity" value="Nama"> </div>
                            </div>
                            </form>
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

    {{-- modal riwayat --}}
    <div class="modal fade" id="riwayat" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-scrollable modal-dialog-left">
          <div class="modal-content">
            <div class="modal-header d-flex">
                <h5 class="mr-auto flex-grow-1">Riwayat</h5>
                <form class="form-inline">
                    <input class="form-control" type="search" placeholder="Cari" aria-label="Search" id="searchInput">
                </form>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12 col-sm-12 mb-4">
                        <div class="card card-small card-post card-post--aside card-post--1">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="card-post__image" style="background-image: url('assets/images/content-management/6.jpeg'); width: 100px; height: 100px;">
                                        <!-- Gambar -->
                                    </div>
                                    <div class="ml-3 mb-1 paragraph-container">
                                        <p style="font-size: 12px; line-height: 1.2; margin-bottom: 5px;">Titik Koordinat</p>
                                        <p style="font-size: 12px; line-height: 1.2; margin-bottom: 5px;">Kecamatan</p>
                                        <p style="font-size: 12px; line-height: 1.2; margin-bottom: 5px;">Desa</p>
                                        <p style="font-size: 12px; line-height: 1.2; margin-bottom: 5px;">Luas</p>
                                        <p style="font-size: 12px; line-height: 1.2; margin-bottom: 5px;">Keterangan</p>
                                        <p style="font-size: 12px; line-height: 1.2; margin-bottom: 5px;">Lahan</p>
                                        <span class="text-muted" style="font-size: 12px; line-height: 1.2;">29 February 2019</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
          </div>
        </div>
    </div>


  </body>

</html>
