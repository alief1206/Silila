
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
    <link rel="stylesheet" id="main-stylesheet" data-version="1.1.0" href="{{ url('assets/styles/shards-dashboards.1.1.0.min.css') }}">
    <link rel="stylesheet" href="{{ url('assets/styles/extras.1.1.0.min.css') }}">
    <link rel="stylesheet" href="{{ url('assets/styles/silila-theme.css') }}">
    <link href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.css" rel="stylesheet">
    <script async defer src="https://buttons.github.io/buttons.js"></script>

    <!-- Leaflet CSS and JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script src="https://unpkg.com/leaflet-geometryutil@0.10.3/src/leaflet.geometryutil.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>
    
<style>
  .form-check-input.lp2b:checked {
    background-color: #10b981; /* Emerald for LP2B */
    border-color: #10b981;
  }

  .form-check-input.lsd:checked {
    background-color: #f59e0b; /* Sunrise Gold for LSD */
    border-color: #f59e0b;
  }

  .form-check-input.lbs:checked {
    background-color: #0d9488; /* Deep Teal for LBS */
    border-color: #0d9488;
  }

  .form-check-input.agricultural:checked {
    background-color: #84cc16; /* Spring Verdant for Kawasan Pertanian */
    border-color: #84cc16;
  }
</style>
    <!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
{{-- <link rel="stylesheet" href="sweetalert2.min\.css"> --}}
<!-- Proj4js and Leaflet Plugins -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.11.0/proj4.min.js" integrity="sha512-JfEOeAU2TD7AtE3xJPSBwBFCxURVqQCysNBwOnNhEJS9LgTHTWGSyYd11JUBOaJ+xVHPaA0ZhLin365CapD8EQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<body class="h-100">
    @yield('content')

    <script>
        var BASE_URL = "{{ url('') }}"
    </script>
    <!-- Single Consistent jQuery & Bootstrap Bundle -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script>
    <script src="https://unpkg.com/shards-ui@latest/dist/js/shards.min.js"></script>
    <script src="{{ url('assets/scripts/shards-dashboards.1.1.0.min.js') }}"></script>
    <script src="{{ url('assets/scripts/main.js') }}"></script>
    
    <!-- DataTables 2.0.7 Core & Bootstrap Integration -->
    <script src="https://cdn.datatables.net/2.0.7/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.7/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Page Scripts -->
    @stack('script')
<script>
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
</script>
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
  });
</script>
</html>
