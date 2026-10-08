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
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.css" />
    <link href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.css" rel="stylesheet">
    <link href="{{ url('assets/styles/select2.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/styles/select2-bootstrap5.min.css') }}" rel="stylesheet">
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Leaflet CSS and JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://unpkg.com/leaflet-geometryutil@0.10.3/src/leaflet.geometryutil.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.11.0/proj4.min.js" integrity="sha512-JfEOeAU2TD7AtE3xJPSBwBFCxURVqQCysNBwOnNhEJS9LgTHTWGSyYd11JUBOaJ+xVHPaA0ZhLin365CapD8EQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ url('assets/styles/silila-theme.css') }}?v={{ file_exists(public_path('assets/styles/silila-theme.css')) ? filemtime(public_path('assets/styles/silila-theme.css')) : time() }}">
  </head>
  <body class="h-100">
    <div class="container-fluid">
      <div class="row">
        @yield('print')

        @include('dashboard.sidebar')
        <!-- End Main Sidebar -->
        <main class="main-content col-lg-10 col-md-9 col-sm-12 p-0 offset-lg-2 offset-md-3">
          <div class="main-navbar sticky-top bg-white">
            <!-- Main Navbar -->
            <nav class="navbar align-items-stretch navbar-light flex-md-nowrap p-0 w-100 justify-content-between">
              <nav class="nav">
                <a href="#" class="nav-link nav-link-icon toggle-sidebar d-md-inline d-lg-none text-center border-right" style="padding: 0.85rem 1.5rem; color: #059669; cursor: pointer;">
                  <i class="material-icons">&#xE5D2;</i>
                </a>
              </nav>
              <form action="#" class="main-navbar__search w-100 d-none d-md-flex d-lg-flex">
                <div class="input-group input-group-seamless ml-3">
                  <div class="input-group-prepend">
                    <div class="input-group-text">
                      <i class="fas fa-search"></i>
                    </div>
                  </div>
                  <input class="navbar-search form-control" type="text" placeholder="Search for something..." aria-label="Search" > </div>
              </form>
              <ul class="navbar-nav border-left flex-row ">
                {{-- <li class="nav-item border-right dropdown notifications">
                  <a class="nav-link nav-link-icon text-center" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <div class="nav-link-icon__wrapper">
                      <i class="material-icons">&#xE7F4;</i>
                      <span class="badge badge-pill badge-danger">1</span>
                    </div>
                  </a>
                  <div class="dropdown-menu dropdown-menu-small" aria-labelledby="dropdownMenuLink">
                    <a class="dropdown-item" href="#">
                      <div class="notification__icon-wrapper">
                        <div class="notification__icon">
                          <i class="material-icons">pin_drop</i>
                        </div>
                      </div>
                      <div class="notification__content">
                        <span class="notification__category">Pencarian Lokasi</span>
                        <p>User
                          <span class="text-success text-semibold">{Nama user}</span> melakukan pencarian lokasi dengan koordinat lat: -8.36667, lng: 114.16667</p>
                      </div>
                    </a>
                    <a class="dropdown-item notification__all text-center" href="#"> Tampilkan Seluruh Riwayat </a>
                  </div>
                </li> --}}
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle text-nowrap px-3" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                    <img class="user-avatar rounded-circle mr-2" src="{{ Auth::user()->foto ? asset('storage/' . Auth::user()->foto) : url('assets/images/avatars/0.jpg') }}" alt="User Avatar" style="width: 40px; height: 40px; object-fit: cover;">
                    <span class="d-none d-md-inline-block">{{ Auth::user()->nama }}</span>
                  </a>
                  <div class="dropdown-menu dropdown-menu-small">
                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#profile">
                      <i class="material-icons">&#xE7FD;</i> Profile</a>
                    <div class="dropdown-divider"></div>
                    {{-- <a class="dropdown-item text-danger" href="#">
                      <i class="material-icons text-danger">&#xE879;</i> Logout </a> --}}
                      <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                  </div>
                </li>
              </ul>
            </nav>
          </div>
          
          <style>
            @media (min-width: 768px) {
                body.hide-sidebar .main-sidebar {
                    display: none !important;
                }
                body.hide-sidebar .main-content {
                    margin-left: 0 !important;
                    flex: 0 0 100% !important;
                    max-width: 100% !important;
                }
                body.hide-sidebar #top-navbar-toggle-container {
                    display: flex !important;
                }
            }
          </style>
          <!-- / .main-navbar -->


          @yield('content')


          <footer class="main-footer d-flex p-2 px-3 bg-white border-top">
            <ul class="nav">
              <li class="nav-item">
                <a class="nav-link" href="{{ url('') }}">Home</a>
              </li>
            </ul>
            <span class="copyright ml-auto my-auto mr-2">Copyright © {{ date('Y') }}
              <a href="javascript:void(0);" rel="nofollow">SILILA Supported By ITernity - IT Consulting</a>
            </span>
          </footer>
        </main>
      </div>
    </div>

    <div class="modal fade" id="profile" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; overflow: hidden; border: none; box-shadow: 0 15px 35px rgba(0,0,0,0.15);">
          <div class="modal-header text-white" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%) !important; padding: 18px 24px;">
            <div class="d-flex align-items-center">
              <i class="material-icons mr-2" style="font-size: 24px;">manage_accounts</i>
              <h5 class="modal-title font-weight-bold mb-0 text-white" style="font-family: var(--font-heading);">Update Profil Pengguna</h5>
            </div>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.9;">
                <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body p-4">
            @if ($errors->any())
                <div class="alert alert-danger" style="border-radius: 12px;">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if(session('success'))
                <div class="alert alert-success" style="border-radius: 12px;">
                    {{ session('success') }}
                </div>
            @endif
            <form method="POST" action="{{ route('profile.update', Auth::user()->id) }}" enctype="multipart/form-data">
              @csrf
              @method('PUT')
              <div class="row align-items-center">
                <div class="col-sm-12 col-md-4 text-center border-right pr-md-4 mb-3 mb-md-0">
                    <div class="position-relative d-inline-block mb-3">
                        <img id="avatar-preview-dashboard" class="user-avatar rounded-circle shadow" 
                             src="{{ Auth::user()->foto ? asset('storage/' . Auth::user()->foto) : url('assets/images/avatars/0.jpg') }}" 
                             alt="User Avatar" 
                             onerror="this.onerror=null; this.src='{{ url('assets/images/avatars/0.jpg') }}';"
                             style="width: 130px; height: 130px; object-fit: cover; border: 3px solid #10b981;">
                    </div>
                    <div class="form-group mb-1">
                        <label for="profile-photo-input-dash" class="btn btn-sm btn-outline-success btn-pill px-3 py-1 cursor-pointer" style="font-weight: 600;">
                            <i class="material-icons mr-1" style="font-size: 16px; vertical-align: -3px;">photo_camera</i> Pilih Foto
                        </label>
                        <input type="file" name="foto" id="profile-photo-input-dash" style="display: none;" accept="image/jpeg,image/png,image/jpg,image/webp" onchange="previewProfilePhoto(this, 'avatar-preview-dashboard')">
                    </div>
                    <small class="text-muted d-block" style="font-size: 11px;">Maksimal 2MB (JPG, PNG, WEBP)</small>
                </div>
                <div class="col-sm-12 col-md-8 pl-md-4">
                    <div class="form-group mb-3">
                        <label class="form-label font-weight-bold" style="font-size: 13px;">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" value="{{ Auth::user()->nama }}" style="border-radius: 10px;" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label font-weight-bold" style="font-size: 13px;">Email</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" style="border-top-left-radius: 10px; border-bottom-left-radius: 10px;">@</span>
                            </div>
                            <input type="email" name="email" class="form-control" value="{{ Auth::user()->email }}" style="border-top-right-radius: 10px; border-bottom-right-radius: 10px;" required>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label font-weight-bold" style="font-size: 13px;">NIP</label>
                        <input type="text" name="nip" class="form-control" value="{{ Auth::user()->nip }}" style="border-radius: 10px;">
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label font-weight-bold" style="font-size: 13px;">Password Baru</label>
                        <input type="password" name="password" class="form-control" placeholder="Kosongkan bila tidak ingin mengganti password" style="border-radius: 10px;">
                    </div>
                </div>
              </div>
              <div class="modal-footer border-top pt-3 pb-0 px-0 mt-3">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 10px;">Batal</button>
                  <button class="btn btn-success text-white shadow-sm" type="submit" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); border-radius: 10px; border: none; font-weight: 600;">Simpan Perubahan</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <script>
        var BASE_URL = "{{ url('') }}"
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script>
    <script src="https://unpkg.com/shards-ui@latest/dist/js/shards.min.js"></script>
    <script src="{{ url('assets') }}/scripts/shards-dashboards.1.1.0.min.js"></script>
    <script src="{{ url('assets') }}/scripts/main.js"></script>
    <script src="{{ url('assets') }}/scripts/file-upload.js"></script>
    <!-- Sumber eksternal: DataTables -->
    <script src="https://cdn.datatables.net/2.0.7/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.7/js/dataTables.bootstrap5.js"></script>
    <script src="{{ url('assets/scripts/select2.min.js') }}"></script>

    <!-- Script khusus untuk halaman web Anda -->
    @stack('script')

    @if ($errors->any())
    <script>
        $(document).ready(function() {
            $('#profile').modal('show');
        });
    </script>
    @endif

    <script>
      function previewProfilePhoto(input, targetId) {
          if (input.files && input.files[0]) {
              var reader = new FileReader();
              reader.onload = function(e) {
                  var target = document.getElementById(targetId);
                  if (target) {
                      target.src = e.target.result;
                  }
              };
              reader.readAsDataURL(input.files[0]);
          }
      }

      // Change Datatable Button
      function change_datatable_button() {
        $('.dt-button').removeClass("dt-button");
      }

      $(document).ready(function() {
        change_datatable_button();
        if ($('#myTable').length) {
            new DataTable('#myTable');
        }
        if ($('#table-1').length) {
            $('#table-1').DataTable();
        }
        if ($('#example').length) {
            $('#example').dataTable({
                paging: false,
                searching: false
            });
        }
      });
    </script>
    {{-- <script>
      var timeout;

      function resetTimeout() {
          clearTimeout(timeout);
          timeout = setTimeout(function() {
              document.getElementById('logout-form').submit();
          }, 300000); // 60000 milidetik = 1 menit
      }

      // Event listeners untuk mendeteksi aktivitas pengguna
      window.addEventListener('mousemove', resetTimeout);
      window.addEventListener('keydown', resetTimeout);
      window.addEventListener('click', resetTimeout);

      // Mulai timer saat halaman dimuat
      resetTimeout();
  </script> --}}
    <script>
      $(document).ready(function() {
          $('.desktop-toggle-sidebar-action').click(function(e) {
              e.preventDefault();
              if (window.innerWidth >= 768) {
                  $('body').toggleClass('hide-sidebar');
              } else {
                  $('.main-sidebar').toggleClass('open');
              }
          });
      });
    </script>
  </body>

</html>
