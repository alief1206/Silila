@push('script')
<script>
    

    $('.btn-restart').on('click', function (){
        window.location.reload();
    })
</script>

<script>
    const printBtn = document.getElementById('printButton');
    if (printBtn) {
        printBtn.addEventListener('click', function() {
            const geometriId = this.getAttribute('data-geometri_id');
            if (geometriId) {
                const printUrl = (typeof BASE_URL !== 'undefined' ? BASE_URL : '') + '/print/' + geometriId;
                window.location.href = printUrl;
            } else {
                console.error('Geometri ID tidak ditemukan.');
            }
        });
    }
</script>

<script>
    $(document).ready(function() {
        // Clean up any stray backdrop
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css('padding-right', '');

        // Mengambil data kecamatan dari server
        $.getJSON(api_url('geometri/kecamatan'))
            .done(function(data) {
                if (Array.isArray(data)) {
                    data.forEach(function(kecamatan) {
                        const option = new Option(kecamatan.nama, kecamatan.id);
                        $('#kecamatan').append(option);
                    });
                }
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                console.error('Error fetching kecamatan:', textStatus, errorThrown);
            });

        // Event listener untuk perubahan pada dropdown kecamatan
        $('#kecamatan').change(function() {
            const kecamatanId = $(this).val();
            const kecamatanName = $(this).find('option:selected').text();

            // Kosongkan dropdown desa
            $('#desa').empty();
            $('#desa').append(new Option('Pilih Desa', ''));

            if (kecamatanId) {
                // Mengambil data desa berdasarkan kecamatanId dari server
                $.getJSON(api_url(`geometri/desa/${kecamatanId}`))
                    .done(function(data) {
                        if (Array.isArray(data)) {
                            data.forEach(function(desa) {
                                const option = new Option(desa.nama, desa.id);
                                $('#desa').append(option);
                            });
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        console.error('Error fetching desa:', textStatus, errorThrown);
                    });
            }
        });

        // Panggil initMap saat halaman pertama kali dimuat
        setTimeout(() => {
            initMap(1, '', '', '', '', true);
        }, 300);
    });
</script>

<script>
    // Show & hide non-blocking loading indicator
    function showLoadingModal() {
        $('#loading-map-indicator').stop(true, true).fadeIn(150);
    }
    function hideLoadingModal() {
        $('#loading-map-indicator').stop(true, true).fadeOut(250);
        // Force cleanup any modal backdrop
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css('padding-right', '');
    }
    function displayInfoWindow(data, map, polygon) {
        let tipeName = (polygon.options && polygon.options.tipe) ? 
            (polygon.options.tipe == 1 ? 'LP2B' : (polygon.options.tipe == 2 ? 'LSD' : (polygon.options.tipe == 3 ? 'LBS' : 'Kawasan Pertanian'))) :
            (polygon.tipe === 1 ? 'LP2B' : (polygon.tipe === 2 ? 'LSD' : (polygon.tipe === 3 ? 'LBS' : 'Kawasan Pertanian')));
            
        let kecText = (polygon.options && polygon.options.kecamatan) || polygon.kecamatan || (data && data.kecamatan) || '-';
        let desaText = (polygon.options && polygon.options.desa) || polygon.desa || (data && data.desa) || '-';
        let luasText = (data && data.luas) ? Number(data.luas).toLocaleString('id-ID') + ' m²' : '-';
        let ketText = (data && (data.ket || data.hutan)) || '-';
        
        let logoSilila = "{{ url('assets/images/header-login.png') }}";
        let logoBwi = "{{ url('assets/images/Banyuwangi.png') }}";

        let contentString = `
        <div class="infowindow-silila" style="min-width: 290px; font-family: 'Inter', sans-serif; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
            <div class="infowindow-header" style="background: #ffffff; color: #0f172a; padding: 12px 16px; display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #f1f5f9;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <img src="${logoBwi}" alt="Banyuwangi" style="height: 30px; width: auto;">
                    <img src="${logoSilila}" alt="SILILA" style="height: 26px; width: auto;">
                </div>
                <span style="background: #059669; color: #fff; font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-transform: uppercase;">${tipeName}</span>
            </div>
            <div class="infowindow-body" style="padding: 16px; background: #ffffff;">
                <div style="display:flex; justify-content:space-between; margin-bottom: 10px; font-size: 13px; align-items: center; border-bottom: 1px dashed #e2e8f0; padding-bottom: 8px;">
                    <span style="color:#64748b; font-weight:600;"><i class="fas fa-map" style="color: #059669; width:16px;"></i> Kecamatan</span>
                    <span style="color:#0f172a; font-weight:700;">${kecText}</span>
                </div>
                <div style="display:flex; justify-content:space-between; margin-bottom: 10px; font-size: 13px; align-items: center; border-bottom: 1px dashed #e2e8f0; padding-bottom: 8px;">
                    <span style="color:#64748b; font-weight:600;"><i class="fas fa-layer-group" style="color: #059669; width:16px;"></i> Desa/Kelurahan</span>
                    <span style="color:#0f172a; font-weight:700;">${desaText}</span>
                </div>
                <div style="display:flex; justify-content:space-between; margin-bottom: 10px; font-size: 13px; align-items: center; border-bottom: 1px dashed #e2e8f0; padding-bottom: 8px;">
                    <span style="color:#64748b; font-weight:600;"><i class="fas fa-ruler-combined" style="color: #059669; width:16px;"></i> Estimasi Luas</span>
                    <span style="color:#059669; font-weight:800;">${luasText}</span>
                </div>
                <div style="display:flex; justify-content:space-between; font-size: 13px; align-items: flex-start; margin-top: 4px;">
                    <span style="color:#64748b; font-weight:600;"><i class="fas fa-info-circle" style="color: #059669; width:16px;"></i> Keterangan</span>
                    <span style="color:#0f172a; font-weight:600; text-align: right; max-width: 60%;">${ketText}</span>
                </div>
            </div>
        </div>
        `;

        polygon.bindPopup(contentString).openPopup();
        return polygon;
    }
    function AddDataRiwayat(geometriId,latlng) {
        const userId = {{ auth()->id() ?? '1' }};
        // Lakukan permintaan AJAX untuk menyimpan data ke tabel riwayat
        $.ajax({
            url: 'api/geometri/AddRiwayat ',
            type: 'POST',
            data: {
                geometri_id: geometriId,
                user_id: userId,
                koordinat:latlng
            },
            success: function(response) {
                // Tampilkan pesan sukses atau lakukan tindakan lain jika berhasil
                console.log('Data berhasil disimpan ke tabel riwayat');
            },
            error: function(xhr, status, error) {
                // Tangani kesalahan jika permintaan AJAX gagal
                console.error('Gagal menyimpan data ke tabel riwayat:', error.message);
            }
        });
    }

    var kecamatanId = '';
    var desaId = '';
    var kecamatanName = '';
    var desaName = '';
    $(document).on('change', 'input[name="searchType"]', function (e) {
        e.preventDefault();
        // $('#loading-map-indicator').removeClass('d-none');
        $('#filter-lp2b').addClass('d-none');
        $('[name=input-desa]').val('0').trigger('change');

        // Ambil semua nilai checkbox yang dipilih
        var selectedTypes = [];
        $('input[name="searchType"]:checked').each(function() {
            selectedTypes.push($(this).val());
        });

        // Ambil nilai kecamatan dan desa yang dipilih
        var kecamatanId = $('select[name="kecamatan"]').val();
        var desaId = $('select[name="desa"]').val();
        var kecamatanName = $('select[name="kecamatan"]').find('option:selected').text();
        var desaName = $('select[name="desa"]').find('option:selected').text();

        // Panggil fungsi initMap dengan nilai selectedTypes, kecamatanId, dan desaId
        initMap(selectedTypes, kecamatanId, desaId,kecamatanName,desaName);
    });

    // Event listener untuk perubahan pada select kecamatan
    $('select[name="kecamatan"]').change(function() {
        kecamatanId = $(this).val();
        desaId = $('select[name="desa"]').val();
        kecamatanName = $('select[name="kecamatan"]').find('option:selected').text();
        desaName = $('select[name="desa"]').find('option:selected').text();

        // Panggil fungsi initMap dengan nilai selectedTypes, kecamatanId, dan desaId
        initMap([], kecamatanId, desaId,kecamatanName,desaName);
    });

    // Event listener untuk perubahan pada select desa
    $('select[name="desa"]').change(function() {
        kecamatanId = $('select[name="kecamatan"]').val();
        desaId = $(this).val();
        kecamatanName = $('select[name="kecamatan"]').find('option:selected').text();
        desaName = $('select[name="desa"]').find('option:selected').text();


        // Panggil fungsi initMap dengan nilai selectedTypes, kecamatanId, dan desaId
        initMap([], kecamatanId, desaId,kecamatanName,desaName);
    });
    function getUserData() {
        return fetch('api/geometri/get-user-data', {
            method: 'GET',
            credentials: 'include' // Sertakan kredensial (misalnya, cookies) dalam permintaan
        })
        .then(response => response.json())
        .then(data => {
            if (data.userData) {
                return data.userData;
            } else {
                throw new Error('User data not found');
            }
        });
    }

    let polygons = [];
    let polygonsbatas = [];
    let map;
    let activeInfoWindow = null;
    function initMap(type, kecamatan, desa,kecamatanName,desaName, isInitialLoad = false) {
        if (map) {
            map.remove();
        }
        map = L.map('maps', {
            zoomControl: false,
            scrollWheelZoom: true,
            dragging: true,
            touchZoom: true
        }).setView([-8.36667, 114.16667], 11);

        setTimeout(() => {
            if (map) {
                map.invalidateSize();
            }
        }, 250);

        // Ensure map re-renders on mobile resize/orientation change
        window.addEventListener('resize', function() {
            if (map) {
                setTimeout(() => map.invalidateSize(), 200);
            }
        });
        window.addEventListener('orientationchange', function() {
            if (map) {
                setTimeout(() => map.invalidateSize(), 300);
            }
        });

        L.control.zoom({
            position: 'bottomleft'
        }).addTo(map);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        showLoadingModal();
        polygons = [];
        polygonsbatas = [];

        // Load polygon data from your API
        $.ajax({
            url: api_url('geometri/all?tipe=' + type + '&kecamatan=' + kecamatan + '&desa=' + desa),
            data: [],
            type: 'GET',
            contentType: false,
            processData: false,
            headers: {
                'kecamatan': kecamatan,
                'desa': desa
            },
            error: function(err) {
                let message = "Terjadi kesalahan.";
                if (err.responseJSON && err.responseJSON.message) {
                    message = err.responseJSON.message;
                }
                _notif('#alert-message', 'danger', message);
                // $('#loading-map-indicator').addClass('d-none');
                hideLoadingModal();
            },
            success: function(res) {
                // $('#loading-map-indicator').addClass('d-none');
                $('#cari-koordinat-segment').removeClass('d-none');
                hideLoadingModal();
                if (!res.error) {
                    if (type == 1) {
                        $('#filter-lp2b').removeClass('d-none');
                    } else {
                        $('#filter-lp2b').addClass('d-none');
                    }

                    $.each(res.data, (i, val) => {
                        let parsedGeom = JSON.parse(val.koordinat);
                        
                        // Handle possible nested parsing
                        if (typeof parsedGeom === 'string') {
                            parsedGeom = JSON.parse(parsedGeom);
                        }

                        let isPoint = false;
                        let mapFeature;
                        let latLngs = [];

                        if (parsedGeom.type === 'Point') {
                            isPoint = true;
                            latLngs = [parsedGeom.coordinates[1], parsedGeom.coordinates[0]];
                        } else {
                            let coordsArray = parsedGeom.coordinates;
                            if (parsedGeom.type === 'MultiPolygon') {
                                latLngs = coordsArray[0][0].map(c => [c[1], c[0]]);
                            } else if (parsedGeom.type === 'Polygon') {
                                latLngs = coordsArray[0].map(c => [c[1], c[0]]);
                            } else {
                                // Fallback
                                latLngs = (coordsArray[0][0] || coordsArray[0]).map(c => [c[1], c[0]]);
                            }
                        }

                        let color = '#10b981';
                        let borderColor = '#059669';
                        if (val.tipe == 1) {
                            color = '#10b981'; // Emerald Verdant
                            borderColor = '#059669';
                        } else if (val.tipe == 2) {
                            color = '#f59e0b'; // Banyuwangi Sunrise Gold
                            borderColor = '#d97706';
                        } else if (val.tipe == 3) {
                            color = '#0d9488'; // Deep Forest Teal
                            borderColor = '#0f766e';
                        } else {
                            color = '#84cc16'; // Spring Verdant
                            borderColor = '#65a30d';
                        }

                        if (isPoint) {
                            mapFeature = L.circleMarker(latLngs, {
                                radius: 8,
                                color: borderColor,
                                fillColor: color,
                                fillOpacity: 0.8,
                                geometri_id: val.geometri_id,
                                tipe: val.tipe,
                                kecamatan: val.kecamatan,
                                desa: val.desa
                            });
                            mapFeature.isPoint = true;
                        } else {
                            mapFeature = L.polygon(latLngs, {
                                color: borderColor,
                                opacity: 0.9,
                                weight: 1.8,
                                fillColor: color,
                                fillOpacity: 0.55,
                                geometri_id: val.geometri_id,
                                tipe: val.tipe,
                                kecamatan: val.kecamatan,
                                desa: val.desa
                            });
                            mapFeature.isPoint = false;
                        }

                        // Interactive Hover Effects
                        mapFeature.on('mouseover', function() {
                            this.setStyle({
                                weight: 2.8,
                                fillOpacity: 0.82,
                                color: '#ffffff'
                            });
                        });
                        mapFeature.on('mouseout', function() {
                            this.setStyle({
                                weight: 1.8,
                                fillOpacity: isPoint ? 0.8 : 0.55,
                                color: borderColor
                            });
                        });

                        mapFeature.addTo(map);
                        polygons.push(mapFeature);
                        if (i == 0 && (kecamatan != 0 || desa != 0)) {
                            map.setView(latLngs[0] || latLngs, 14);
                        }

                        mapFeature.on('click', function() {
                            const geometriId = this.options.geometri_id;
                            const tipe = this.options.tipe;
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
                                    let res = err.responseJSON;
                                    _notif('#alert-message', 'danger', res.message);
                                },
                                success: function(res) {
                                    if (activeInfoWindow) {
                                        activeInfoWindow.close();
                                    }
                                    activeInfoWindow = displayInfoWindow(res.data, map, polygonRef);
                                }
                            });
                        });
                    });
                } else {
                    if (!isInitialLoad) {
                        _notif('#alert-message', 'warning', res.message);
                    }
                }
            }
        });

        // Refined Cartographic Administrative Boundaries (Warm Slate with subtle dash)
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
                    polygonsbatas.push(polygonbatas);
                });
            }
            if (map) {
                map.invalidateSize();
            }
        }).fail(function(err) {
            console.warn('Gagal memuat batas.json:', err);
        });

    }


    document.addEventListener('DOMContentLoaded', function() {
        const isLoggedIn = '{{ Auth::check() }}';

        window.performCoordinateSearch = function(searchTypeSelect, latValue, lngValue, kecamatanName, desaName) {
            let location;

            if (searchTypeSelect === 'latlng') {
                location = L.latLng(latValue, lngValue);
            } else if (searchTypeSelect === 'cea') {
                location = ceaToLatLng({x: latValue, y: lngValue});
            } else {
                _notif('#alert-message', 'danger', 'Please select a search type.');
                return;
            }

            let isInPolygon = false;
            let polygonWithCoordinat;

            // Jika Banyak
            let polygonsWithCoordinat = []

            let pt = turf.point([location.lng, location.lat]);
            polygons.forEach((polygon) => {
                if (polygon.isPoint) {
                    let pointCoord = polygon.getLatLng();
                    let distance = turf.distance(pt, turf.point([pointCoord.lng, pointCoord.lat]), {units: 'kilometers'});
                    // Jika jarak sangat dekat (misal di bawah 50 meter)
                    if (distance < 0.05) {
                        isInPolygon = true;
                        polygonWithCoordinat = polygon;
                        polygonsWithCoordinat.push(polygon);
                    }
                } else {
                    let latlngsArray = polygon.getLatLngs()[0];
                    if (latlngsArray && latlngsArray.length >= 3) {
                        let coords = latlngsArray.map(latlng => [latlng.lng, latlng.lat]);
                        coords.push(coords[0]); // close the polygon for turf
                        let poly = turf.polygon([coords]);
                        if (turf.booleanPointInPolygon(pt, poly)) {
                            isInPolygon = true;
                            polygonWithCoordinat = polygon;
                            polygonsWithCoordinat.push(polygon);
                        }
                    }
                }
            });

            let logoSilila = "{{ url('assets/images/header-login.png') }}";
            let logoBwi = "{{ url('assets/images/Banyuwangi.png') }}";

            let contentString = `
                <div class="infowindow-silila" style="min-width: 300px; font-family: 'Inter', sans-serif; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
                    <div class="infowindow-header" style="background: #ffffff; color: #0f172a; padding: 12px 16px; display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #f1f5f9;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <img src="${logoBwi}" alt="Banyuwangi" style="height: 30px; width: auto;">
                            <img src="${logoSilila}" alt="SILILA" style="height: 26px; width: auto;">
                        </div>
                        <span style="font-weight: 800; font-size: 13px; color: #0f172a;">Lokasi</span>
                    </div>
                    <div class="infowindow-body" style="padding: 16px; background: #ffffff;">
                        <div style="display:flex; justify-content:space-between; margin-bottom: 10px; font-size: 13px; align-items: center;">
                            <span style="color:#64748b; font-weight:600;"><i class="fas fa-map-marker-alt" style="color: #059669; width:16px;"></i> Koordinat</span>
                            <span style="color:#0f172a; font-weight:700;">${location.lat}, ${location.lng}</span>
                        </div>
                        <div style="margin-top: 12px; padding: 10px; background: #fffbeb; border-radius: 8px; border-left: 3px solid #f59e0b; font-size: 11px; line-height: 1.5; color: #92400e;">
                            <i class="fas fa-info-circle" style="color: #d97706; margin-right: 4px;"></i>
                            <strong>Perhatian:</strong> Data lokasi tersebut merupakan referensi dan bukan merupakan ijin peruntukan lahan. Terkait perizinan lebih lanjut bisa melakukan koordinasi dengan tim Forum Penataan Ruang Daerah (FPRD) Kabupaten Banyuwangi.
                        </div>
            `;

            map.setView(location, 20);

            const marker = L.marker(location, {
                title: 'Searched Location'
            }).addTo(map);

            if (isInPolygon) {
                const kecamatan = polygonWithCoordinat.options.kecamatan;
                const desa = polygonWithCoordinat.options.desa;
                const userName = '{{ Auth::check() ? Auth::user()->nama : '' }}' || window.guestName || 'Pengunjung';
                const userNip = '{{ Auth::check() ? Auth::user()->nip : '' }}' || window.guestNik || '-';

                contentString = `
                <div class="infowindow-silila" style="min-width: 310px; font-family: 'Inter', sans-serif; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
                    <div class="infowindow-header" style="background: #ffffff; color: #0f172a; padding: 12px 16px; display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #f1f5f9;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <img src="${logoBwi}" alt="Banyuwangi" style="height: 30px; width: auto;">
                            <img src="${logoSilila}" alt="SILILA" style="height: 26px; width: auto;">
                        </div>
                        <span style="font-weight: 800; font-size: 13px; color: #0f172a;">Hasil Pencarian</span>
                    </div>
                    <div class="infowindow-body" style="padding: 16px; background: #ffffff;">
                        <div style="display:flex; justify-content:space-between; margin-bottom: 10px; font-size: 13px; align-items: center; border-bottom: 1px dashed #e2e8f0; padding-bottom: 8px;">
                            <span style="color:#64748b; font-weight:600;"><i class="fas fa-map-marker-alt" style="color: #059669; width:16px;"></i> Koordinat</span>
                            <span style="color:#0f172a; font-weight:700;">${location.lat}, ${location.lng}</span>
                        </div>
                        <div style="display:flex; justify-content:space-between; margin-bottom: 10px; font-size: 13px; align-items: center; border-bottom: 1px dashed #e2e8f0; padding-bottom: 8px;">
                            <span style="color:#64748b; font-weight:600;"><i class="fas fa-map" style="color: #059669; width:16px;"></i> Kecamatan</span>
                            <span style="color:#0f172a; font-weight:700;">${kecamatan}</span>
                        </div>
                        <div style="display:flex; justify-content:space-between; margin-bottom: 10px; font-size: 13px; align-items: center; border-bottom: 1px dashed #e2e8f0; padding-bottom: 8px;">
                            <span style="color:#64748b; font-weight:600;"><i class="fas fa-layer-group" style="color: #059669; width:16px;"></i> Desa/Kel</span>
                            <span style="color:#0f172a; font-weight:700;">${desa}</span>
                        </div>
                `;
                let contentprint = `
                    <div class="infowindow-content" style="font-family: Arial, sans-serif; color: #333; background-color: #f9f9f9; border: 1px solid #ccc; border-radius: 10px; padding: 20px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
                        <table width="200px">
                        <tr>
                            <td style="font-size: 18px;">
                                <span style="display: inline-block; width: 15px; height: 15px; background-color: rgba(255, 99, 132) !important; margin-right: 3px;"></span>
                                LSD
                            </td>
                            <td style="font-size: 18px;">
                                <span style="display: inline-block; width: 15px; height: 15px; background-color: rgba(54, 162, 235) !important; margin-right: 3px;"></span>
                                LP2B
                            </td>
                        </tr>
                        </table>
                        <p style="margin: 10px 0; font-size: 14px;">
                            Dari titik koordinat
                            <span style="font-weight: bold; color: #059669;">${location.lat}, ${location.lng}</span>
                            yang berada di
                            <span style="font-weight: bold;">Kelurahan/Desa ${desa}</span>,
                            <span style="font-weight: bold;">Kec. ${kecamatan}</span>.
                        </p>
                `;

                let promises = [];

                polygonsWithCoordinat.forEach(element => {
                    const geometriId = element.options.geometri_id;
                    const tipe = element.options.tipe;
                    const url = `geometri/get-data/${tipe}/${geometriId}`;

                    let ajaxPromise = $.ajax({
                        url: api_url(url),
                        data: [],
                        type: 'GET',
                        contentType: false,
                        processData: false,
                        headers: {},
                    });

                    promises.push(ajaxPromise.then(res => {
                        const geometriId = res.data.geometri_id;
                        const latlng = location.lat + ',' + location.lng;
                        AddDataRiwayat(geometriId, latlng);
                        const formattedLuas = parseFloat(res.data.luas).toFixed(3);

                        if (res.data.tipe === 1) {
                            contentString += `
                                <div style="margin-top: 14px; padding: 12px; background: #f8fafc; border-radius: 8px; border-left: 4px solid #10b981;">
                                    <div style="font-weight: 800; font-size: 13px; color: #047857; margin-bottom: 8px;">LP2B (Geometri ID: ${res.data.geometri_id})</div>
                                    <div style="display:flex; justify-content:space-between; font-size: 12px; margin-bottom: 6px;">
                                        <span style="color:#64748b;">Luas:</span><span style="font-weight:700; color:#0f172a;">${formattedLuas} m²</span>
                                    </div>
                                    <div style="display:flex; justify-content:space-between; font-size: 12px; margin-bottom: 6px;">
                                        <span style="color:#64748b;">KP2B:</span><span style="font-weight:700; color:#0f172a; text-align:right; max-width:60%;">${res.data.kp2b || '-'}</span>
                                    </div>
                                    <div style="display:flex; justify-content:space-between; font-size: 12px;">
                                        <span style="color:#64748b;">Ket:</span><span style="font-weight:700; color:#0f172a; text-align:right; max-width:60%;">${res.data.ket || '-'}</span>
                                    </div>
                                </div>
                            `;
                            contentprint += `
                                <hr>
                                <p style="margin: 10px 0; font-size: 14px;">
                                    <strong>Tipe:</strong>
                                    <span style="color: rgba(54, 162, 235) !important;">LP2B</span>
                                </p>
                                <p style="margin: 10px 0; font-size: 14px;">
                                    <strong>Geometri ID:</strong>
                                    <span style="color: rgba(54, 162, 235) !important;">${res.data.geometri_id}</span>
                                </p>
                                <p style="margin: 10px 0; font-size: 14px;">
                                    <strong>Luas:</strong>
                                    <span style="color: rgba(54, 162, 235) !important;">${formattedLuas}</span>
                                </p>
                                <p style="margin: 10px 0; font-size: 14px;">
                                    <strong>KP2B:</strong>
                                    <span style="color: rgba(54, 162, 235) !important;">${res.data.kp2b || '-'}</span>
                                </p>
                                <p style="margin: 10px 0; font-size: 14px;">
                                    <strong>Keterangan:</strong>
                                    <span style="color: rgba(54, 162, 235) !important;">${res.data.ket || '-'}</span>
                                </p>
                            `;
                        } else if (res.data.tipe === 2) {
                            contentString += `
                                <div style="margin-top: 14px; padding: 12px; background: #fffbeb; border-radius: 8px; border-left: 4px solid #f59e0b;">
                                    <div style="font-weight: 800; font-size: 13px; color: #b45309; margin-bottom: 8px;">LSD (Geometri ID: ${res.data.geometri_id})</div>
                                    <div style="display:flex; justify-content:space-between; font-size: 12px; margin-bottom: 6px;">
                                        <span style="color:#64748b;">Luas:</span><span style="font-weight:700; color:#0f172a;">${formattedLuas} m²</span>
                                    </div>
                                    <div style="display:flex; justify-content:space-between; font-size: 12px; margin-bottom: 6px;">
                                        <span style="color:#64748b;">Hutan:</span><span style="font-weight:700; color:#0f172a; text-align:right; max-width:60%;">${res.data.hutan || '-'}</span>
                                    </div>
                                    <div style="display:flex; justify-content:space-between; font-size: 12px;">
                                        <span style="color:#64748b;">BA:</span><span style="font-weight:700; color:#0f172a; text-align:right; max-width:60%;">${res.data.ba || '-'}</span>
                                    </div>
                                </div>
                            `;
                            contentprint += `
                                <hr>
                                <p style="margin: 10px 0; font-size: 14px;">
                                    <strong>Tipe:</strong>
                                    <span style="color: rgba(255, 99, 132) !important;">LSD</span>
                                </p>
                                <p style="margin: 10px 0; font-size: 14px;">
                                    <strong>Geometri ID:</strong>
                                    <span style="color: rgba(255, 99, 132) !important;">${res.data.geometri_id}</span>
                                </p>
                                <p style="margin: 10px 0; font-size: 14px;">
                                    <strong>Luas:</strong>
                                    <span style="color: rgba(255, 99, 132) !important;">${formattedLuas}</span>
                                </p>
                                <p style="margin: 10px 0; font-size: 14px;">
                                    <strong>Hutan:</strong>
                                    <span style="color: rgba(255, 99, 132) !important;">${res.data.hutan}</span>
                                </p>
                                <p style="margin: 10px 0; font-size: 14px;">
                                    <strong>BA:</strong>
                                    <span style="color: rgba(255, 99, 132) !important;">${res.data.ba}</span>
                                </p>
                                <p style="margin: 10px 0; font-size: 14px;">
                                    <strong>Luas CEA HM:</strong>
                                    <span style="color: rgba(255, 99, 132) !important;">${res.data.luascea_hm}</span>
                                </p>
                            `;
                        } else {
                            contentString += `
                                <div style="margin-top: 14px; padding: 12px; background: #f7fee7; border-radius: 8px; border-left: 4px solid #84cc16;">
                                    <div style="font-weight: 800; font-size: 13px; color: #4d7c0f; margin-bottom: 8px;">Kawasan Pertanian (Geometri ID: ${res.data.geometri_id})</div>
                                </div>
                            `;
                            contentprint += `
                                <hr>
                                <p style="margin: 10px 0; font-size: 14px;">
                                    <strong>Geometri ID:</strong>
                                    <span style="color: #000000;">${res.data.geometri_id}</span>
                                </p>
                            `;
                        }
                    }).catch(err => {
                        Swal.fire({
                            icon: "warning",
                            title: "Mohon Maaf",
                            text: "Koordinat yang dicari tidak masuk dalam wilayah",
                        });
                    }));
                });

                Promise.all(promises).then(() => {
                    const currentDate = new Date();
                    const formattedDate = `${currentDate.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })}, ${currentDate.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })} WIB`;
                    contentprint += `
                        <p style="margin: 10px 0; font-size: 14px;">
                            <span style="color: rgba(0, 0, 0, 0.5);">*Data lokasi tersebut merupakan referensi dan bukan merupakan ijin peruntukan lahan.<br>
                                Terkait periijinan lebih lanjut bisa melakukan koordinasi dengan tim Forum Penataan Ruang Daerah (FPRD) Kabupaten Banyuwangi</span>
                        </p>
                        <div style="margin-top: 20px; padding: 10px; background-color: #e6f7ff; border: 1px solid #b3e0ff; border-radius: 5px;">
                            <p style="margin: 5px 0; font-size: 14px; text-align: center;">
                                Dicetak oleh
                                <span style="font-weight: bold;">${userName}</span><br>
                                <span style="font-weight: bold;">${userNip}</span><br>
                                ${formattedDate}
                            </p>
                        </div>
                    </div>
                    `;
                    contentString += `</div></div>`;

                    document.getElementById('modal-body-content').innerHTML = contentString;
                    document.getElementById('cetak-print').innerHTML = contentprint;
                    
                    // Tampilkan popup di atas marker dengan Leaflet
                    marker.bindPopup(contentString).openPopup();

                    document.getElementById('printButton').addEventListener('click', function() {
                        var cetakContent = document.getElementById('cetak-print').innerHTML;
                        var originalContent = document.body.innerHTML;

                        var printArea = document.createElement('div');
                        printArea.id = 'printArea';
                        printArea.style.position = 'absolute';
                        printArea.style.top = '-10000px';
                        document.body.appendChild(printArea);
                        printArea.innerHTML = `
                            <div>
                                <h1 style="color:blue; text-align:center;"><strong>DETAIL INFORMASI</strong></h1>
                                <div id="print-map" style="height: 500px; width: 100%; margin: auto;"></div>
                                ${cetakContent}
                            </div>
                        `;

                        document.body.innerHTML = printArea.innerHTML;

                        var printMap = L.map('print-map', { zoomControl: false, scrollWheelZoom: false, dragging: false, touchZoom: false }).setView(map.getCenter(), map.getZoom());
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '© OpenStreetMap' }).addTo(printMap);

                        polygonsWithCoordinat.forEach((polygon) => { L.polygon(polygon.getLatLngs(), { color: polygon.options.color, opacity: polygon.options.opacity, weight: polygon.options.weight, fillColor: polygon.options.fillColor, fillOpacity: polygon.options.fillOpacity, geometri_id:polygon.options.geometri_id, tipe:polygon.options.tipe }).addTo(printMap); });

                        const marker = L.marker(location, { title: 'Searched Location' }).addTo(printMap);

                        setTimeout(function() {
                            window.print();

                            window.location.reload()

                            document.body.innerHTML = originalContent;

                            document.body.removeChild(printArea);
                        }, 1000);
                    });
                });
            } else {
                Swal.fire({
                    icon: "warning",
                    title: "Mohon Maaf",
                    text: "Koordinat yang dicari tidak masuk dalam wilayah",
                });
                const userName = '{{ Auth::check() ? Auth::user()->nama : '' }}' || window.guestName || 'Pengunjung';
                const userNip = '{{ Auth::check() ? Auth::user()->nip : '' }}' || window.guestNik || '-';
                const currentDate = new Date();
                const formattedDate = `${currentDate.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })}, ${currentDate.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })} WIB`;

                // Update modal content and show modal
                document.getElementById('modal-body-content').innerHTML = contentString;
                
                // Tampilkan popup di atas marker dengan Leaflet
                marker.bindPopup(contentString).openPopup();
                // Fungsi untuk mencetak informasi yang ada di modal
            document.getElementById('printButton').addEventListener('click', function() {
                var cetakContent = document.getElementById('cetak-print').innerHTML;
                var originalContent = document.body.innerHTML;

                var printArea = document.createElement('div');
                printArea.id = 'printArea';
                printArea.style.position = 'absolute';
                printArea.style.top = '-10000px';
                document.body.appendChild(printArea);
                printArea.innerHTML = `
                <div class="infowindow-content" style="font-family: 'Plus Jakarta Sans', sans-serif; color: #333; background-color: #f9f9f9; border: 1px solid #e2e8f0; border-radius: 14px; padding: 20px; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);">
                    <h2 style="color: #064e3b; text-align: center; font-weight: 800; font-size: 18px; letter-spacing: -0.02em;">DETAIL INFORMASI</h2>
                                        <div id="print-map" style="height: 500px; width: 100%; margin: auto; border-radius: 12px; overflow: hidden;"></div>
                                    <h4 style="text-align: center; color: #059669; font-weight: 700; margin: 15px 0;">Informasi Lokasi</h4>
                                    <p style="margin: 10px 0; font-size: 14px;">
                                        Dari titik koordinat
                                        <span style="font-weight: bold; color: #059669;">${location.lat}, ${location.lng}</span>
                                        yang berada di
                                        <span style="font-weight: bold;">Kelurahan/Desa ${desaName  || '-'}</span>,
                                        <span style="font-weight: bold;">Kec. ${kecamatanName  || '-'}</span> tidak termasuk wilayah LP2B atau LSD.
                                    </p>
                                    <p style="margin: 10px 0; font-size: 14px;">
                                        <strong>Geometri ID:</strong>
                                        <span style="color: #059669;"> - </span>
                                    </p>
                                    <p style="margin: 10px 0; font-size: 14px;">
                                        <strong>KP2B:</strong>
                                        <span style="color: #059669;"> - </span>
                                    </p>
                                    <p style="margin: 10px 0; font-size: 14px;">
                                        <strong>Luas:</strong>
                                        <span style="color: #059669;"> - </span>
                                    </p>
                                    <p style="margin: 10px 0; font-size: 14px;">
                                        <strong>Keterangan:</strong>
                                        <span style="color: #059669;">-</span>
                                    </p>
                                    <p style="margin: 10px 0; font-size: 14px;">
                                        <span style="color: #059669;">*Data lokasi tersebut merupakan referensi dan bukan merupakan ijin peruntukan lahan.<br>
                                            Terkait periijinan lebih lanjut bisa melakukan koordinasi dengan tim Forum Penataan Ruang Daerah (FPRD) Kabupaten Banyuwangi</span>
                                    </p>

                                    <div style="margin-top: 20px; padding: 12px; background-color: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 10px;">
                                        <p style="margin: 5px 0; font-size: 14px; text-align: center; color: #065f46;">
                                            Dicetak oleh
                                            <span style="font-weight: bold;">${userName}</span><br>
                                            <span style="font-weight: bold;">${userNip}</span><br>
                                            ${formattedDate}
                                        </p>
                                    </div>
                                </div>
                `;

                document.body.innerHTML = printArea.innerHTML;

                var printMap = L.map('print-map', { zoomControl: false, scrollWheelZoom: false, dragging: false, touchZoom: false }).setView(map.getCenter(), map.getZoom());
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '© OpenStreetMap' }).addTo(printMap);

                polygons.forEach((polygon) => { L.polygon(polygon.getLatLngs(), { color: polygon.options.color, opacity: polygon.options.opacity, weight: polygon.options.weight, fillColor: polygon.options.fillColor, fillOpacity: polygon.options.fillOpacity }).addTo(printMap); });

                const marker = L.marker(location, { title: 'Searched Location' }).addTo(printMap);

                setTimeout(function() {
                    window.print();

                    document.body.innerHTML = originalContent;

                    document.body.removeChild(printArea);
                }, 1000);
                    });
                }
        };

        $(document).on('submit', '#cari-koordinat', function(e){
            e.preventDefault();
            const searchTypeSelect = $('#tipe-pencarian').val();
            const latValue = document.getElementById('latitude').value;
            const lngValue = document.getElementById('longitude').value;
            const kecamatanName = $('select[name="kecamatan"]').find('option:selected').text();
            const desaName = $('select[name="desa"]').find('option:selected').text();
            
            window.performCoordinateSearch(searchTypeSelect, latValue, lngValue, kecamatanName, desaName);
        });
    });
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


    function initMapsCard() {
        const images = document.querySelectorAll('.card-post__image');

        images.forEach(image => {
            const lat = parseFloat(image.getAttribute('data-lat'));
            const lng = parseFloat(image.getAttribute('data-lng'));
            const polygonData = JSON.parse(image.getAttribute('data-polygon'));
            const polygonColor = image.getAttribute('data-polygon-color') || '#FF0000'; // Default color if not provided

            if (!isNaN(lat) && !isNaN(lng)) {
                setTimeout(() => {
                    const smallMap = L.map(image, {
                        zoomControl: false,
                        scrollWheelZoom: false,
                        dragging: false,
                        touchZoom: false
                    }).setView([lat, lng], 15);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(smallMap);

                    L.marker([lat, lng]).addTo(smallMap);

                    // Add polygon
                    if (polygonData) {
                        const polygonCoords = polygonData.map(coord => [coord[0], coord[1]]); // Adjusting based on logic
                        L.polygon(polygonCoords, {
                            color: polygonColor,
                            opacity: 0.8,
                            weight: 2,
                            fillColor: polygonColor,
                            fillOpacity: 0.35
                        }).addTo(smallMap);
                    }
                }, 500); // Delay to ensure element is fully rendered
            } else {
                console.error(`Invalid coordinates: lat=${lat}, lng=${lng}`);
            }
        });
    }
    document.addEventListener('DOMContentLoaded', initMapsCard);



    function renderGeometries(data) {
        // Hapus semua geometri yang sudah ada dari peta
        polygons.forEach(function(polygon) {
            polygon.remove();
        });
        polygons = [];

        // Loop melalui data geometri dan tambahkan setiap geometri pada peta sebagai polygon
        data.forEach(function(geometry) {
            var coordinates = JSON.parse(geometry.coordinates).map(coord => [coord[1], coord[0]]);
            var polygon = L.polygon(coordinates, {
                color: '#FF0000',
                opacity: 0.8,
                weight: 2,
                fillColor: '#FF0000',
                fillOpacity: 0.35
            }).addTo(map);
            polygons.push(polygon);
        });
    }

</script>

@endpush
