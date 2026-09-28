@push("script")

<script>
    var map;
    var polygons = [];
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
                            hoverBorderWidth: 3,
                            data: data,
                            backgroundColor: [
                                '#10b981', // Emerald Verdant
                                '#f59e0b', // Sunrise Amber
                                '#0d9488', // Forest Teal
                                '#059669', // Deep Emerald
                                '#ea580c', // Sunrise Tangerine
                                '#84cc16'  // Agricultural Lime
                            ],
                            borderColor: '#ffffff',
                            borderWidth: 2
                        }],
                        labels: labels
                    };

                    var ubdOptions = {
                        responsive: true,
                        maintainAspectRatio: false,
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 14,
                                boxWidth: 12,
                                usePointStyle: true,
                                fontColor: '#475569',
                                fontFamily: "'Plus Jakarta Sans', sans-serif"
                            }
                        },
                        cutoutPercentage: 65,
                        tooltips: {
                            backgroundColor: '#0f172a',
                            titleFontFamily: "'Plus Jakarta Sans', sans-serif",
                            bodyFontFamily: "'Plus Jakarta Sans', sans-serif",
                            cornerRadius: 10,
                            xPadding: 12,
                            yPadding: 10,
                            displayColors: true
                        }
                    };

                    var ubdCtx = document.getElementById('myChart');
                    const myChart = new Chart(ubdCtx, {
                        type: 'doughnut',
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

    function initMap(type = 1, kecamatan = 0, desa = 0) {
        if (!$('#map').length) { return; }
        if (map) { map.remove(); }
        map = L.map('map').setView([-8.36667, 114.16667], 11);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

        setTimeout(() => {
            if (map) {
                map.invalidateSize();
            }
        }, 250);

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
                if (kecamatan != 0 || desa != 0) {
                    _notif('#alert-message','danger', message);
                }
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

                            var color = '#10b981';
                            var strokeColor = '#059669';
                            if (val.tipe == 1) {
                                color = '#10b981'; // LP2B (Emerald Verdant)
                                strokeColor = '#059669';
                            } else if (val.tipe == 2) {
                                color = '#f59e0b'; // LSD (Banyuwangi Sunrise Gold)
                                strokeColor = '#d97706';
                            } else if (val.tipe == 3) {
                                color = '#0d9488'; // LBS (Deep Forest Teal)
                                strokeColor = '#0f766e';
                            } else {
                                color = '#84cc16'; // Kawasan Pertanian
                                strokeColor = '#65a30d';
                            }

                            const polygon = L.polygon(polygonCoords, {
                                color: strokeColor,
                                opacity: 0.9,
                                weight: 1.6,
                                fillColor: color,
                                fillOpacity: 0.5,
                                geometri_id: val.geometri_id,
                                tipe: val.tipe
                            }).addTo(map);

                            polygon.on('mouseover', function() {
                                this.setStyle({ weight: 2.8, fillOpacity: 0.82, color: '#ffffff' });
                            });
                            polygon.on('mouseout', function() {
                                this.setStyle({ weight: 1.6, fillOpacity: 0.5, color: strokeColor });
                            });

                            polygons.push(polygon);

                            if (i == 0 && (kecamatan != 0 || desa != 0)) {
                                map.setView(polygonCoords[0], 14);
                            }

                            polygon.on('click', function () {
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
                        });
                    }
                } else {
                    if (kecamatan != 0 || desa != 0) {
                        _notif('#alert-message','warning',res.message);
                    }
                }
            }
        });

        // Cartographic Administrative Boundaries (Banyuwangi Outline)
        $.getJSON((typeof BASE_URL !== 'undefined' ? BASE_URL : '') + '/assets/batas/batas.json', function(data) {
            if (data && data.features) {
                data.features.forEach(feature => {
                    const coordinates = feature.geometry.coordinates[0][0].map(function(coord) {
                        return [coord[1], coord[0]];
                    });

                    const polygonbatas = L.polygon(coordinates, {
                        color: '#64748b',
                        opacity: 0.55,
                        weight: 1.2,
                        dashArray: '3, 4',
                        fillColor: '#94a3b8',
                        fillOpacity: 0.02
                    });

                    polygonbatas.addTo(map);
                });
            }
            if (map) {
                map.invalidateSize();
            }
        }).fail(function(err) {
            console.warn('Gagal memuat batas.json:', err);
        });

    }

    function displayInfoWindow(data, map, polygon) {
        let contentString = '';
        if (polygon.tipe === 1) {
            contentString = `
            <div class="infowindow-silila">
                <div class="infowindow-header" style="background: linear-gradient(135deg, #059669, #10b981);">
                    <span>🌾 Lahan Pertanian LP2B</span>
                    <small style="opacity: 0.9;">#${data.geometri_id || ''}</small>
                </div>
                <div class="infowindow-body">
                    <div class="infowindow-row">
                        <span class="infowindow-label">Kecamatan</span>
                        <span class="infowindow-val">${data.kecamatan || '-'}</span>
                    </div>
                    <div class="infowindow-row">
                        <span class="infowindow-label">Desa</span>
                        <span class="infowindow-val">${data.desa || '-'}</span>
                    </div>
                    <div class="infowindow-row">
                        <span class="infowindow-label">Status KP2B</span>
                        <span class="infowindow-val"><span class="badge px-2 py-1" style="background: #ecfdf5; color: #047857; font-weight: 700;">${data.kp2b || '-'}</span></span>
                    </div>
                    <div class="infowindow-row">
                        <span class="infowindow-label">Luas Lahan</span>
                        <span class="infowindow-val" style="color: #059669; font-weight: 800;">${data.luas || '0'} ha</span>
                    </div>
                    <div class="infowindow-row">
                        <span class="infowindow-label">Keterangan</span>
                        <span class="infowindow-val">${data.ket || '-'}</span>
                    </div>
                </div>
            </div>
            `;
        } else if (polygon.tipe === 2) {
            contentString = `
            <div class="infowindow-silila">
                <div class="infowindow-header" style="background: linear-gradient(135deg, #f59e0b, #ea580c);">
                    <span>🛡️ Lahan Sawah LSD</span>
                    <small style="opacity: 0.9;">#${data.geometri_id || ''}</small>
                </div>
                <div class="infowindow-body">
                    <div class="infowindow-row">
                        <span class="infowindow-label">Kawasan Hutan</span>
                        <span class="infowindow-val">${data.hutan || '-'}</span>
                    </div>
                    <div class="infowindow-row">
                        <span class="infowindow-label">Luas Lahan</span>
                        <span class="infowindow-val" style="color: #d97706; font-weight: 800;">${data.luas || '0'} ha</span>
                    </div>
                    <div class="infowindow-row">
                        <span class="infowindow-label">BA</span>
                        <span class="infowindow-val">${data.ba || '-'}</span>
                    </div>
                    <div class="infowindow-row">
                        <span class="infowindow-label">Luas CEA HM</span>
                        <span class="infowindow-val">${data.luascea_hm || '-'}</span>
                    </div>
                </div>
            </div>
            `;
        } else if (polygon.tipe === 3) {
            contentString = `
            <div class="infowindow-silila">
                <div class="infowindow-header" style="background: linear-gradient(135deg, #0d9488, #14b8a6);">
                    <span>☀️ Lahan Baku Sawah LBS</span>
                    <small style="opacity: 0.9;">#${data.geometri_id || ''}</small>
                </div>
                <div class="infowindow-body">
                    <div class="infowindow-row">
                        <span class="infowindow-label">Kecamatan</span>
                        <span class="infowindow-val">${data.kecamatan || '-'}</span>
                    </div>
                    <div class="infowindow-row">
                        <span class="infowindow-label">Desa</span>
                        <span class="infowindow-val">${data.desa || '-'}</span>
                    </div>
                    <div class="infowindow-row">
                        <span class="infowindow-label">Luas Lahan</span>
                        <span class="infowindow-val" style="color: #0d9488; font-weight: 800;">${data.luas || '0'} ha</span>
                    </div>
                </div>
            </div>
            `;
        }

        polygon.bindPopup(contentString).openPopup();
    }

    var UploadFile = null;
    if ($('#file--upload').length) {
        UploadFile = new FileUpload('#file--upload',{
            accept: [
                'geojson',
                'json'
            ],
            maxSize: 60,
            maxFile: 1
        });
    }

    var UploadFileLsd = null;
    if ($('#file--upload-lsd').length) {
        UploadFileLsd = new FileUpload('#file--upload-lsd',{
            accept: [
                'geojson',
                'json'
            ],
            maxSize: 60,
            maxFile: 1
        });
    }

    $(document).on('submit', '#modalImportLp2b form', function(e){
        e.preventDefault()

        if (!UploadFile || UploadFile.getFiles() == null) {
            _notif('#modalImportLp2b .alert--message','danger', "File tidak ditemukan! pilih file terlebih dahulu.")
            return;
        }

        $("#modalImportLp2b form [type='submit']").addClass('disabled')
        $("#modalImportLp2b form [type='submit']").html('<i class="fas fa-spinner fa-spin"></i>  Loading...')

        let data = new FormData()
        data.append('file', UploadFile.getFiles())

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
                    if (UploadFile && UploadFile.reset) UploadFile.reset()
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

        if (!UploadFileLsd || UploadFileLsd.getFiles() == null) {
            _notif('#modalImportLsd .alert--message','danger', "File tidak ditemukan! pilih file terlebih dahulu.")
            return;
        }

        $("#modalImportLsd form [type='submit']").addClass('disabled')
        $("#modalImportLsd form [type='submit']").html('<i class="fas fa-spinner fa-spin"></i>  Loading...')

        let data = new FormData()
        data.append('file', UploadFileLsd.getFiles())

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
