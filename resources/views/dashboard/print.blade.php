<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Cetak</title>
    <meta name="description" content="SILILA, Sistem Informasi Perlindungan Lahan Kabupaten Banyuwangi.">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        .data {
            line-height: 1.6;
        }
        .data p {
            margin: 10px 0;
        }
        .data strong {
            display: inline-block;
            width: 150px;
        }
        #map {
            height: 400px;
            width: 100%;
        }
    </style>
</head>
<body>
    @include('dashboard.js.home')
    <div class="container">
        <div class="header">
            <h1>Detail Informasi</h1>
        </div>
        <div class="data">
            <p><strong>Geometri ID:</strong> </span></p>
            <p><strong>Kecamatan:</strong> <span></span></p>
            <p><strong>Desa:</strong> <span></span></p>
            <p><strong>KP2B:</strong> <span></span></p>
            <p><strong>Luas:</strong> <span></span></p>
            <p><strong>Keterangan:</strong> <span></span></p>
        </div>
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="m-0">Peta Wilayah</h6>
            </div>
            <div class="card-body">
                <div id="maps"></div>
            </div>
        </div>
    </div>

    @stack('script')
</body>
</html>
