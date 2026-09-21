@push("script")

<script>
    (function () {
        $.ajax({
            url: api_url(`dashboard`),
            type: 'GET',
            headers: HttpHeaders,
            success: function(res) {
                if(!res.error){
                    $('.jumlah-kecamatan').html(res.data.jumlahKecamatan)
                    $('.jumlah-desa').html(res.data.jumlahDesa)
                    $('.jumlah-kp2b').html(res.data.jumlahKp2b+" ha")
                    $('.jumlah-lsd').html(res.data.jumlahLsd+" ha")
                    $('.jumlah-lbs').html(res.data.jumlahLbs)
                }
            }
        })

        $.ajax({
            url: api_url(`dashboard/pie-chart`),
            type: 'GET',
            headers: HttpHeaders,
            success: function(res) {
                if(!res.error){
                    const labels = res.data.map(item => item.ket);
                    const data = res.data.map(item => item.jumlah);
                    var ubdData = {
                        datasets: [{
                            hoverBorderColor: '#ffffff',
                            data: data,
                            backgroundColor: [
                                'rgba(54, 162, 235, 0.7)',
                                'rgba(255, 99, 132, 0.7)',
                                'rgba(255, 206, 86, 0.7)',
                                'rgba(75, 192, 192, 0.7)',
                                'rgba(153, 102, 255, 0.7)',
                                'rgba(255, 159, 64, 0.7)'
                            ],
                            borderColor: [
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 99, 132, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)'
                            ],
                            borderWidth: 1
                        }],
                        labels: labels
                    };

                    var ubdOptions = {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 25,
                                boxWidth: 20
                            }
                        },
                        cutoutPercentage: 0,
                        // Uncomment the following line in order to disable the animations.
                        // animation: false,
                        tooltips: {
                            custom: false,
                            mode: 'index',
                            position: 'nearest'
                        }
                    };

                    var ubdCtx = document.getElementById('myChart');
                    const myChart = new Chart(ubdCtx, {
                        type: 'pie',
                        data: ubdData,
                        options: ubdOptions
                    });
                }
            }
        })

        $.ajax({
            url: api_url(`dashboard/kecamatan`),
            type: 'GET',
            headers: HttpHeaders,
            success: function(res) {
                if(!res.error){
                    $("[name='input-kecamatan']").empty()
                    $("[name='input-kecamatan']").append(`<option selected="" value="0">Semua Kecamatan</option>`)
                    $.each(res.data,(i,val)=>{
                        $("[name='input-kecamatan']").append(`<option value="${val.id}">${val.nama}</option>`)
                    })

                    $("[name='input-kecamatan']").select2()
                }
            }
        })

        $.ajax({
            url: api_url(`dashboard/desa/0`),
            type: 'GET',
            headers: HttpHeaders,
            success: function(res) {
                if(!res.error){
                    $("[name='input-desa']").empty()
                    $("[name='input-desa']").append(`<option selected="" value="0">Semua Desa</option>`)
                    $.each(res.data,(i,val)=>{
                        $("[name='input-desa']").append(`<option value="${val.id}">${val.nama}</option>`)
                    })
                    // $("[name='input-desa']").removeAttr('disabled')
                    $("[name='input-desa']").select2()
                }
            }
        })

        // Initialize map on page load
        initMap(1);
    })();

    $(document).on('change', "[name='input-kecamatan']", function (e) {
        e.preventDefault()
        let kecamatan_id = $(this).val()

        $.ajax({
            url: api_url(`dashboard/desa/${kecamatan_id}`),
            type: 'GET',
            headers: HttpHeaders,
            success: function(res) {
                if(!res.error){
                    $("[name='input-desa']").empty()
                    $("[name='input-desa']").append(`<option selected="" value="0">Semua Desa</option>`)
                    $.each(res.data,(i,val)=>{
                        $("[name='input-desa']").append(`<option value="${val.id}">${val.nama}</option>`)
                    })
                    // $("[name='input-desa']").removeAttr('disabled')
                    $("[name='input-desa']").select2()
                }
            }
        })
    })

    let polygons = [];
    let map;
    function initMap(type = 1, kecamatan = 0, desa = 0) {
        if (map) { map.remove(); }
        map = L.map('map').setView([-8.36667, 114.16667], 11);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

        polygons = [];

        $.ajax({
            url: api_url('geometri/all?tipe=' + type),
            data: [],
            type: 'GET',
            contentType: false,
            processData: false,
            headers: {
                'kecamatan': kecamatan,
                'desa': desa
            },
            error: function(err) {
                $('#loading-map-indicator').addClass('d-none');
                let message = "Terjadi kesalahan saat memuat data.";
                if (err.responseJSON && err.responseJSON.message) {
                    message = err.responseJSON.message;
                } else if (err.statusText) {
                    message = err.statusText;
                }
                _notif('#alert-message','danger', message);
            },
            success: function(res) {
                $('#loading-map-indicator').addClass('d-none')
                $('#cari-koordinat-segment').removeClass('d-none')

                if(!res.error){
                    $('#filter-lp2b').removeClass('d-none')

                    if (res.data && res.data.length > 0) {
                        $.each(res.data, (i, val) => {
                        const coordinates = JSON.parse(val.koordinat);
                        const polygonCoords = coordinates[0][0].map(function(coord) {
                            return [coord[1], coord[0]];
                        });

                        var color = '#000000';
                        if (val.tipe == 1) {
                            color = 'rgba(54, 162, 235, 1)'
                        } else if (val.tipe == 2) {
                            color = 'rgba(255, 99, 132, 1)'
                        } else {
                            color = 'rgba(255, 206, 86, 1)'
                        }

                        const polygon = L.polygon(polygonCoords, {
                            color: color,
                            opacity: 0.55,
                            weight: 1,
                            fillColor: color,
                            fillOpacity: 0.50,
                            geometri_id: val.geometri_id,
                            tipe: val.tipe
                        }).addTo(map);
                        polygons.push(polygon);

                        if (i == 0 && (kecamatan != 0 || desa != 0)) {
                            map.setView(polygonCoords[0], 14);
                        }

                        polygon.addListener('click', function () {
                            const geometriId = this.geometri_id;
                            const tipe = this.tipe;
                            const url = `geometri/get-data/${tipe}/${geometriId}`;
                            let polygonRef = this;

                            // Make an AJAX request to the URL to get the data
                            $.ajax({
                                url: api_url(url),
                                data: [],
                                type: 'GET',
                                contentType: false,
                                processData: false,
                                headers: {},
                                error: function(err) {
                                    let message = "Terjadi kesalahan.";
                                    if (err.responseJSON && err.responseJSON.message) {
                                        message = err.responseJSON.message;
                                    }
                                    _notif('#alert-message','danger', message);
                                },
                                success: function(res) {
                                    displayInfoWindow(res.data, map, polygonRef);
                                }
                            });
                        });
                    }
                }else{
                    _notif('#alert-message','danger',res.message)
                }
            }
        })

    }

    function displayInfoWindow(data, map, polygon) {
        let contentString = '';
        if (polygon.tipe === 1) {
            contentString = `
            <div class="infowindow-content">
                <h5>Informasi Geometri</h5>
                <table class="table table-striped table-sm">
                <tr>
                    <th>Geometri ID</th>
                    <td>${data.geometri_id}</td>
                </tr>
                <tr>
                    <th>Kecamatan</th>
                    <td>${data.kecamatan}</td>
                </tr>
                <tr>
                    <th>Desa</th>
                    <td>${data.desa}</td>
                </tr>
                <tr>
                    <th>KP2B</th>
                    <td>${data.kp2b}</td>
                </tr>
                <tr>
                    <th>Luas</th>
                    <td>${data.luas}</td>
                </tr>
                <tr>
                    <th>Keterangan</th>
                    <td>${data.ket}</td>
                </tr>
                </table>
            </div>
            `;
        } else if (polygon.tipe === 2) {
            contentString = `
            <div class="infowindow-content">
                <h5>Informasi Geometri</h5>
                <table class="table table-striped table-sm">
                <tr>
                    <th>Geometri ID</th>
                    <td>${data.geometri_id}</td>
                </tr>
                <tr>
                    <th>Hutan</th>
                    <td>${data.hutan}</td>
                </tr>
                <tr>
                    <th>Luas</th>
                    <td>${data.luas}</td>
                </tr>
                <tr>
                    <th>BA</th>
                    <td>${data.ba}</td>
                </tr>
                <tr>
                    <th>Luas CEA HM</th>
                    <td>${data.luascea_hm}</td>
                </tr>
                </table>
            </div>
            `;
        }

        polygon.bindPopup(contentString).openPopup();
    }

    let UploadFile = new FileUpload('#file--upload',{
        accept: [
            'geojson'
        ],
        maxSize: 60,
        maxFile: 1
    });

    let UploadFileLsd = new FileUpload('#file--upload-lsd',{
        accept: [
            'geojson'
        ],
        maxSize: 60,
        maxFile: 1
    });

    $(document).on('submit', '#modalImportLp2b form', function(e){
        e.preventDefault()

        $("#modalImportLp2b form [type='submit']").addClass('disabled')
        $("#modalImportLp2b form [type='submit']").html('<i class="fas fa-spinner fa-spin"></i>  Loading...')

        let data = new FormData()
        data.append('file', UploadFile.getFiles())
        if (UploadFile.getFiles() == null) {
            $("#modalImportLp2b form [type='submit']").removeClass('disabled')
            $("#modalImportLp2b form [type='submit']").html('Upload')

            _notif('#modalImportLp2b .alert--message','danger', "File tidak ditemukan! pilih file terlebih dahulu.")
            return;
        }

        $.ajax({
            url: api_url('geometri/import-geojson/lp2b'),
            data: data,
            type: 'POST',
            contentType: false,
            processData: false,
            headers: {},
            error: function(err) {
                $("#modalImportLp2b form [type='submit']").removeClass('disabled')
                $("#modalImportLp2b form [type='submit']").html('Upload')
                let message = "Terjadi kesalahan.";
                if (err.responseJSON && err.responseJSON.message) {
                    message = err.responseJSON.message;
                }
                _notif('#modalImportLp2b .alert--message','danger', message)
            },
            success: function(res) {
                if(!res.error){
                    $('#modalImportLp2b').modal('hide')
                    $('#modalImportLp2b form')[0].reset()
                    _notif('#alert-message','success',res.message)
                    window.scrollTo({
                        top: 0,
                        behavior: "smooth"
                    });
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000);
                }else{
                    _notif('#modalImportLp2b .alert--message','danger',res.message)
                }

                $("#modalImportLp2b form [type='submit']").removeClass('disabled')
                $("#modalImportLp2b form [type='submit']").html('Upload')
            }
        })
    });

    $(document).on('submit', '#modalImportLsd form', function(e){
        e.preventDefault()

        $("#modalImportLsd form [type='submit']").addClass('disabled')
        $("#modalImportLsd form [type='submit']").html('<i class="fas fa-spinner fa-spin"></i>  Loading...')

        let data = new FormData()
        data.append('file', UploadFileLsd.getFiles())
        if (UploadFileLsd.getFiles() == null) {
            $("#modalImportLsd form [type='submit']").removeClass('disabled')
            $("#modalImportLsd form [type='submit']").html('Upload')

            _notif('#modalImportLsd .alert--message','danger', "File tidak ditemukan! pilih file terlebih dahulu.")
            return;
        }

        $.ajax({
            url: api_url('geometri/import-geojson/lsd'),
            data: data,
            type: 'POST',
            contentType: false,
            processData: false,
            headers: {},
            error: function(err) {
                $("#modalImportLsd form [type='submit']").removeClass('disabled')
                $("#modalImportLsd form [type='submit']").html('Upload')
                let message = "Terjadi kesalahan.";
                if (err.responseJSON && err.responseJSON.message) {
                    message = err.responseJSON.message;
                }
                _notif('#modalImportLsd .alert--message','danger', message)
            },
            success: function(res) {
                if(!res.error){
                    $('#modalImportLsd').modal('hide')
                    $('#modalImportLsd form')[0].reset()
                    _notif('#alert-message','success',res.message)
                    window.scrollTo({
                        top: 0,
                        behavior: "smooth"
                    });
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000);
                }else{
                    _notif('#modalImportLsd .alert--message','danger',res.message)
                }

                $("#modalImportLsd form [type='submit']").removeClass('disabled')
                $("#modalImportLsd form [type='submit']").html('Upload')
            }
        })
    })

    $(document).on('change', '#map-filter', function (e) {
        e.preventDefault()
        $('#cari-koordinat-segment').addClass('d-none')
        $('#loading-map-indicator').removeClass('d-none')
        // $('#filter-lp2b').addClass('d-none')
        $('[name=input-desa]').val('0').trigger('change');
        initMap($(this).val());
    })

    $(document).on('click', '.btn-filter-lp2b', function (e) {
        $('#loading-map-indicator').removeClass('d-none')
        initMap(1, $('[name=input-kecamatan]').select2('val'), $('[name=input-desa]').select2('val'));
    })

    $(document).on('submit', '#cari-koordinat', function(e){
        e.preventDefault();
        const searchTypeSelect = $('#tipe-pencarian').val();
        const latitude = document.getElementById('latitude');
        const longitude = document.getElementById('longitude');
        let location;

        if (searchTypeSelect === 'latlng') {
            location = L.latLng(latitude.value, longitude.value);
        } else if (searchTypeSelect === 'cea') {
            location = ceaToLatLng({x:latitude.value, y:longitude.value});
        } else {
            _notif('#alert-message', 'danger', 'Please select a search type.');
            return;
        }

        // Test CEA
        // console.log(latLngToCea(location));

        let isInPolygon = false;
        let polygonWithCoordinat;

        polygons.forEach((polygon) => {
            let coords = polygon.getLatLngs()[0].map(latlng => [latlng.lng, latlng.lat]);
                coords.push(coords[0]);
                let poly = turf.polygon([coords]);
                let pt = turf.point([location.lng, location.lat]);
                if (turf.booleanPointInPolygon(pt, poly)) {
                isInPolygon = true;
                polygonWithCoordinat = polygon;
            }
        });

        if (isInPolygon) {
            map.setView(location, 15);

            const marker = L.marker(location, { title: 'Searched Location' }).addTo(map);

            const geometriId = polygonWithCoordinat.geometri_id;
            const tipe = polygonWithCoordinat.tipe;
            const url = `geometri/get-data/${tipe}/${geometriId}`;
            let polygonRef = polygonWithCoordinat;

            // Make an AJAX request to the URL to get the data
            $.ajax({
                url: api_url(url),
                data: [],
                type: 'GET',
                contentType: false,
                processData: false,
                headers: {},
                error: function(err) {
                    let message = "Terjadi kesalahan.";
                    if (err.responseJSON && err.responseJSON.message) {
                        message = err.responseJSON.message;
                    }
                    _notif('#alert-message','danger', message);
                },
                success: function(res) {
                    // Tampilkan Di Modal
                    alert(res.data.geometri_id)
                }
            });
        } else {
            _notif('#alert-message', 'danger', 'Location is not within a polygon');
        }
    })

    function isNumber(value) {
        return !isNaN(parseFloat(value)) && isFinite(value);
    }

    // Convert the CEA coordinates to LatLng coordinates
    proj4.defs('EPSG:6933', '+proj=cea +lon_0=0 +lat_ts=30 +x_0=0 +y_0=0 +datum=WGS84 +units=m +no_defs');
    function ceaToLatLng(ceaCoords) {
        let latLng = proj4('EPSG:6933', 'EPSG:4326', [parseFloat(ceaCoords.x), parseFloat(ceaCoords.y)]);
        return L.latLng(latLng[1], latLng[0]);
    }
    // Convert the LatLng coordinates to CEA coordinates
    function latLngToCea(latLng) {
        let cea = proj4('EPSG:4326', 'EPSG:6933', [latLng.lng, latLng.lat]);
        return { x: cea[0], y: cea[1] };
    }
</script>
@endpush
