@php
    $appBaseTitle = 'SILILA Sistem Informasi Perlindungan Lahan Pertanian & LSD';
    $rawTitle = trim($__env->yieldContent('title'));
    
    // Penentuan Judul Dinamis berdasarkan Halaman / Rute
    if (!empty($rawTitle) && !in_array(strtolower($rawTitle), ['home', 'silila', 'index', 'laravel'])) {
        $pageTitle = $rawTitle . ' | ' . $appBaseTitle;
    } elseif (request()->is('dashboard')) {
        $pageTitle = 'Dashboard Overview | ' . $appBaseTitle;
    } elseif (request()->is('login')) {
        $pageTitle = 'Login Petugas | ' . $appBaseTitle;
    } elseif (request()->is('register')) {
        $pageTitle = 'Registrasi | ' . $appBaseTitle;
    } else {
        $pageTitle = $appBaseTitle;
    }

    $seoDescription = "SILILA (Sistem Informasi Perlindungan Lahan Pertanian & LSD) Kabupaten Banyuwangi. Layanan peta spasial interaktif LP2B & LSD, verifikasi titik koordinat lahan pertanian pangan berkelanjutan, serta pengajuan Surat Keterangan Kesesuaian Lahan resmi Dinas Pertanian & Pangan Kabupaten Banyuwangi.";
    $seoKeywords = "SILILA, LP2B Banyuwangi, LSD Banyuwangi, Perlindungan Lahan Pertanian, Lahan Sawah Dilindungi, GIS Banyuwangi, Peta Lahan Pertanian Banyuwangi, Dinas Pertanian Banyuwangi, Pemkab Banyuwangi, Cek Kesesuaian Lahan, Surat Keterangan Lahan";
    $canonicalUrl = url()->current();
    
    // Asset URL Absolut untuk Open Graph (WhatsApp, Facebook, Twitter Card)
    $ogImage = asset('assets/images/og-silila.jpg');
    $banyuwangiLogo = asset('assets/images/Banyuwangi.png');
@endphp

<!-- Judul Tab Halaman Dinamis -->
<title>{{ $pageTitle }}</title>

<!-- Meta Tag Utama & SEO -->
<meta name="title" content="{{ $pageTitle }}">
<meta name="description" content="{{ $seoDescription }}">
<meta name="keywords" content="{{ $seoKeywords }}">
<meta name="author" content="Dinas Pertanian dan Pangan Kabupaten Banyuwangi">
<meta name="robots" content="index, follow">
<meta name="language" content="Indonesian">
<meta name="geo.region" content="ID-JI">
<meta name="geo.placename" content="Kabupaten Banyuwangi">
<link rel="canonical" href="{{ $canonicalUrl }}">

<!-- Favicon & Touch Icons Resmi Logo Kabupaten Banyuwangi -->
<link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=2">
<link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=2">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}?v=2">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}?v=2">
<link rel="icon" type="image/png" sizes="48x48" href="{{ asset('favicon-48x48.png') }}?v=2">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}?v=2">
<link rel="apple-touch-icon" href="{{ $banyuwangiLogo }}?v=2">
<meta name="msapplication-TileImage" content="{{ $banyuwangiLogo }}">
<meta name="msapplication-TileColor" content="#064e3b">
<meta name="theme-color" content="#059669">

<!-- Open Graph / WhatsApp / Facebook Preview Card -->
<meta property="og:type" content="website">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:site_name" content="SILILA Kabupaten Banyuwangi">
<meta property="og:locale" content="id_ID">
<meta property="og:title" content="{{ $pageTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:image" content="{{ $ogImage }}?v=2">
<meta property="og:image:secure_url" content="{{ $ogImage }}?v=2">
<meta property="og:image:type" content="image/jpeg">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="SILILA - Sistem Informasi Perlindungan Lahan Pertanian & LSD Kabupaten Banyuwangi">

<!-- Twitter Card Meta Tags -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="{{ $canonicalUrl }}">
<meta name="twitter:title" content="{{ $pageTitle }}">
<meta name="twitter:description" content="{{ $seoDescription }}">
<meta name="twitter:image" content="{{ $ogImage }}?v=2">
<meta name="twitter:image:alt" content="SILILA - Sistem Informasi Perlindungan Lahan Pertanian & LSD Kabupaten Banyuwangi">
