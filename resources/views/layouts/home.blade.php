
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
    <link rel="stylesheet" id="main-stylesheet" data-version="1.1.0" href="/assets/styles/shards-dashboards.1.1.0.min.css">
    <link rel="stylesheet" href="{{ url('assets') }}/styles/extras.1.1.0.min.css">
    <link rel="stylesheet" href="{{ url('assets') }}/styles/accents/danger.1.1.0.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.css" rel="stylesheet">
    <script async defer src="https://buttons.github.io/buttons.js"></script>
<style>
  .form-check-input.lp2b:checked {
    background-color: #ADD8E6; /* Light Blue for LP2B */
    border-color: #ADD8E6;
}

.form-check-input.lsd:checked {
    background-color: #FFB6C1; /* Light Pink for LSD */
    border-color: #FFB6C1;
}

.form-check-input.lbs:checked {
    background-color: #90EE90; /* Light Green for LBS */
    border-color: #90EE90;
}

.form-check-input.agricultural:checked {
    background-color: #FFD700; /* Gold for Kawasan Pertanian */
    border-color: #FFD700;
}

</style>
    <!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
{{-- <link rel="stylesheet" href="sweetalert2.min\.css"> --}}
<!-- jQuery -->
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script>
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.8.0/proj4.js"></script> --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.11.0/proj4.min.js" integrity="sha512-JfEOeAU2TD7AtE3xJPSBwBFCxURVqQCysNBwOnNhEJS9LgTHTWGSyYd11JUBOaJ+xVHPaA0ZhLin365CapD8EQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<!-- DataTables JS -->
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
  <body class="h-100">
    @yield('content')

</body>





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
{{-- <script src="sweetalert2.min.js"></script> --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Script khusus untuk halaman web Anda -->
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
    // Change Datatable Button
  function change_datatable_button() {
    $('.dt-button').removeClass("dt-button");
  }

  $(document).ready(function() {
    change_datatable_button();
  })
</script>
</html>
