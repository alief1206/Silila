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
    <link href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.min.css">
    <link href="https://cdn.datatables.net/2.0.7/js/dataTables.min.js">
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <link href="{{ url('assets/styles/select2.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/styles/select2-bootstrap5.min.css') }}" rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">

    <!-- jQuery -->
    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script>

    <!-- DataTables JS -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.css" />

    <script src="https://cdn.datatables.net/2.0.7/js/dataTables.js"></script>


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
            <nav class="navbar align-items-stretch navbar-light flex-md-nowrap p-0">
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
                    <img class="user-avatar rounded-circle mr-2" src="{{ url('assets') }}/images/avatars/0.jpg" alt="User Avatar">
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
              <nav class="nav">
                <a href="#" class="nav-link nav-link-icon toggle-sidebar d-md-inline d-lg-none text-center border-left" data-toggle="collapse" data-target=".header-navbar" aria-expanded="false" aria-controls="header-navbar">
                  <i class="material-icons">&#xE5D2;</i>
                </a>
              </nav>
            </nav>
          </div>
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
      <div class="modal-dialog modal-lg modal-dialog-center">
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
    <script src="{{ url('assets/scripts/select2.min.js') }}"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.css" />

    <script src="https://cdn.datatables.net/2.0.7/js/dataTables.js"></script>


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
 <script>
  document.addEventListener('DOMContentLoaded', function() {
      const apiKey = '{{ config('services.google_maps.key') }}';
      const script = document.createElement('script');
      script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}&callback=initMap&libraries=places`;
      script.async = true;
      script.defer = true;
      document.head.appendChild(script);
  });
</script>
    <script src="{{ url('assets') }}/scripts/main.js"></script>
    <script>
        let table = new DataTable('#myTable');
    </script>
    <script>
        $(document).ready( function () {
            $('#table-1').DataTable();
        });
    </script>

    <script>
        $('#example').dataTable( {
            paging: false,
            searching: false
        } );
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
  </body>

</html>
