@push('script')
<script>
    

    $('.btn-restart').on('click', function (){
        window.location.reload();
    })
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

        // Panggil initMap saat halaman pertama kali dimuat (peta bersih, search-on-demand)
        setTimeout(() => {
            initMap('', '', '', '', '', true);
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

        // Peta Satelit Google (Hybrid) sebagai layer utama
        let googleSatellite = L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            attribution: '© Google Satellite'
        });

        let osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        });

        // Terapkan Satelit Google ke peta
        googleSatellite.addTo(map);

        // Kontrol Layer Peta di pojok kanan atas
        L.control.layers({
            'Satelit Google': googleSatellite,
            'OpenStreetMap': osm
        }, null, { position: 'topright' }).addTo(map);

        // Mode default: Peta hanya bisa digeser (pan/drag) dan di-zoom (zoom in/out) tanpa pencarian otomatis saat peta diklik.
        // Pencarian lahan hanya dipicu saat pengguna melakukan pencarian secara eksplisit (melalui Asisten SILILA atau form koordinat).

        // Search-on-Demand: Jika tipe kosong (peta awal), jangan muat poligon massal
        if (!type || type === '' || (Array.isArray(type) && type.length === 0)) {
            hideLoadingModal();
            return;
        }

        // Proteksi: Jika tipe LSD (2) dipilih tanpa filter kecamatan/desa, minta pengguna memilih wilayah
        if (type == 2 && !kecamatan && !desa && !isInitialLoad) {
            _notif('#alert-message', 'info', 'Silakan pilih Kecamatan atau Desa terlebih dahulu untuk menampilkan data LSD secara spesifik.');
            hideLoadingModal();
            return;
        }

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

            showLoadingModal();

            // Panggil API backend Search-on-Demand (Point-in-Polygon ST_Contains)
            $.getJSON(api_url(`geometri/check-location?lat=${location.lat}&lng=${location.lng}`))
                .done(function(res) {
                    hideLoadingModal();

                    let logoSilila = "{{ url('assets/images/header-login.png') }}";
                    let logoBwi = "{{ url('assets/images/Banyuwangi.png') }}";
                    const userName = '{{ Auth::check() ? Auth::user()->nama : '' }}' || window.guestName || 'Pengunjung';
                    const userNip = '{{ Auth::check() ? Auth::user()->nip : '' }}' || window.guestNik || '-';

                    map.setView(location, 18);

                    // Bersihkan marker pencarian sebelumnya jika ada
                    if (window.lastSearchMarker) {
                        map.removeLayer(window.lastSearchMarker);
                    }
                    window.lastSearchMarker = L.marker(location, {
                        title: 'Lokasi Dicari'
                    }).addTo(map);

                    if (res && res.found && res.data) {
                        let item = res.data;
                        let kat = item.kategori || {};
                        let info = kat.info_utama || {};
                        let tani = kat.pertanian_irigasi || {};
                        let ruang = kat.tata_ruang || {};
                        let hak = kat.pertanahan_investasi || {};
                        let adm = kat.administrasi_teknis || {};

                        // Gambar poligon bidang tanah di peta jika ada GeoJSON dan belum digambar
                        if (item.geojson) {
                            let isLsd = (item.tipe == 2);
                            let polyColor = isLsd ? '#f59e0b' : '#10b981';
                            let polyBorder = isLsd ? '#d97706' : '#059669';

                            let polyLayer = L.geoJSON(item.geojson, {
                                style: {
                                    color: polyBorder,
                                    weight: 2.5,
                                    opacity: 0.95,
                                    fillColor: polyColor,
                                    fillOpacity: 0.55
                                }
                            }).addTo(map);

                            polygons.push(polyLayer);
                            try {
                                map.fitBounds(polyLayer.getBounds(), { padding: [40, 40], maxZoom: 18 });
                            } catch (e) {}
                        }

                        // Simpan riwayat pencarian
                        if (item.geometri_id) {
                            AddDataRiwayat(item.geometri_id, location.lat + ',' + location.lng);
                            let btnPrint = document.getElementById('printButton');
                            if (btnPrint) {
                                btnPrint.setAttribute('data-geometri_id', item.geometri_id);
                            }
                        }

                        // Buat kartu informasi popup (5 kategori terstruktur)
                        let badgeColor = (item.tipe == 2) ? '#f59e0b' : '#10b981';
                        let contentString = `
                        <div class="infowindow-silila" style="min-width: 320px; max-width: 380px; font-family: 'Inter', sans-serif; border-radius: 14px; overflow: hidden; box-shadow: 0 12px 30px rgba(0,0,0,0.18);">
                            <div class="infowindow-header" style="background: #ffffff; color: #0f172a; padding: 12px 16px; display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #f1f5f9;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <img src="${logoBwi}" alt="Banyuwangi" style="height: 28px; width: auto;">
                                    <img src="${logoSilila}" alt="SILILA" style="height: 24px; width: auto;">
                                </div>
                                <span style="background: ${badgeColor}; color: #fff; font-size: 11px; font-weight: 800; padding: 3px 10px; border-radius: 20px; text-transform: uppercase;">${item.tipe_nama}</span>
                            </div>
                            <div class="infowindow-body" style="padding: 14px 16px; background: #ffffff; max-height: 420px; overflow-y: auto;">
                                <div style="margin-bottom: 12px; padding: 8px 12px; background: #ecfdf5; border-radius: 8px; border-left: 3px solid #10b981; font-size: 11.5px; color: #065f46;">
                                    <strong>Lokasi Terverifikasi:</strong> Desa ${adm.desa || '-'}, Kec. ${adm.kecamatan || '-'}
                                </div>

                                <!-- Accordion / Kategori Rinci -->
                                <div class="detail-kategori-group" style="font-size: 12.5px;">
                                    <!-- 1. Info Utama -->
                                    <div style="font-weight: 700; color: #059669; margin: 8px 0 4px 0; border-bottom: 1px solid #e2e8f0; padding-bottom: 3px;">
                                        <i class="fas fa-check-circle mr-1"></i> Informasi Pokok
                                    </div>
                                    <div style="display:flex; justify-content:space-between; margin-bottom: 4px;">
                                        <span style="color:#64748b;">Status:</span><span style="font-weight:600; color:#0f172a;">${info.status_lsd || '-'}</span>
                                    </div>
                                    <div style="display:flex; justify-content:space-between; margin-bottom: 4px;">
                                        <span style="color:#64748b;">Estimasi Luas:</span><span style="font-weight:700; color:#059669;">${info.luas ? Number(info.luas).toLocaleString('id-ID') + ' m²' : '-'}</span>
                                    </div>
                                    <div style="display:flex; justify-content:space-between; margin-bottom: 4px;">
                                        <span style="color:#64748b;">Nomor BA:</span><span style="font-weight:600; color:#0f172a;">${info.nomor_ba || '-'}</span>
                                    </div>

                                    <!-- 2. Pertanian & Irigasi -->
                                    <div style="font-weight: 700; color: #059669; margin: 12px 0 4px 0; border-bottom: 1px solid #e2e8f0; padding-bottom: 3px;">
                                        <i class="fas fa-water mr-1"></i> Pertanian &amp; Irigasi
                                    </div>
                                    <div style="display:flex; justify-content:space-between; margin-bottom: 4px;">
                                        <span style="color:#64748b;">Jaringan Irigasi:</span><span style="font-weight:600; color:#0f172a;">${tani.irigasi || '-'}</span>
                                    </div>
                                    <div style="display:flex; justify-content:space-between; margin-bottom: 4px;">
                                        <span style="color:#64748b;">Kewenangan:</span><span style="font-weight:600; color:#0f172a;">${tani.kewenangan || '-'}</span>
                                    </div>
                                    <div style="display:flex; justify-content:space-between; margin-bottom: 4px;">
                                        <span style="color:#64748b;">Indeks Pertanaman:</span><span style="font-weight:600; color:#0f172a;">${tani.ip ? 'IP ' + tani.ip : '-'}</span>
                                    </div>
                                    <div style="display:flex; justify-content:space-between; margin-bottom: 4px;">
                                        <span style="color:#64748b;">Produktivitas:</span><span style="font-weight:600; color:#0f172a;">${tani.prod ? tani.prod + ' Ton/Ha' : '-'}</span>
                                    </div>

                                    <!-- 3. Tata Ruang & Kehutanan -->
                                    <div style="font-weight: 700; color: #059669; margin: 12px 0 4px 0; border-bottom: 1px solid #e2e8f0; padding-bottom: 3px;">
                                        <i class="fas fa-map mr-1"></i> Tata Ruang &amp; Hak Tanah
                                    </div>
                                    <div style="display:flex; justify-content:space-between; margin-bottom: 4px;">
                                        <span style="color:#64748b;">Pola Ruang:</span><span style="font-weight:600; color:#0f172a; text-align:right; max-width:60%;">${ruang.pola_ruang || '-'}</span>
                                    </div>
                                    <div style="display:flex; justify-content:space-between; margin-bottom: 4px;">
                                        <span style="color:#64748b;">Kawasan Hutan:</span><span style="font-weight:600; color:#0f172a;">${ruang.status_hutan || '-'}</span>
                                    </div>
                                    <div style="display:flex; justify-content:space-between; margin-bottom: 4px;">
                                        <span style="color:#64748b;">Tipe Hak:</span><span style="font-weight:600; color:#0f172a;">${hak.tipe_hak || '-'}</span>
                                    </div>
                                    <div style="display:flex; justify-content:space-between; margin-bottom: 4px;">
                                        <span style="color:#64748b;">Status Investasi:</span><span style="font-weight:600; color:#0f172a;">${hak.investasi || '-'}</span>
                                    </div>
                                </div>

                                <div style="margin-top: 14px; padding: 8px 10px; background: #fffbeb; border-radius: 8px; border-left: 3px solid #f59e0b; font-size: 11px; line-height: 1.4; color: #92400e;">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    Bidang ini dilindungi regulasi pangan daerah. Wajib mempertahankan fungsi lahan sawah.
                                </div>
                            </div>
                        </div>
                        `;

                        window.lastSearchMarker.bindPopup(contentString).openPopup();
                        document.getElementById('modal-body-content').innerHTML = contentString;

                        _notif('#alert-message', 'success', 'Lokasi terverifikasi berada di dalam ' + item.tipe_nama);
                    } else {
                        // Titik berada di luar LSD / LP2B
                        let contentString = `
                        <div class="infowindow-silila" style="min-width: 290px; font-family: 'Inter', sans-serif; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
                            <div class="infowindow-header" style="background: #ffffff; color: #0f172a; padding: 12px 16px; display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #f1f5f9;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <img src="${logoBwi}" alt="Banyuwangi" style="height: 28px; width: auto;">
                                    <img src="${logoSilila}" alt="SILILA" style="height: 24px; width: auto;">
                                </div>
                                <span style="background: #10b981; color: #fff; font-size: 11px; font-weight: 800; padding: 3px 10px; border-radius: 20px;">BEBAS LSD</span>
                            </div>
                            <div class="infowindow-body" style="padding: 16px; background: #ffffff;">
                                <div style="display:flex; justify-content:space-between; margin-bottom: 8px; font-size: 13px;">
                                    <span style="color:#64748b; font-weight:600;"><i class="fas fa-map-marker-alt text-success mr-1"></i> Koordinat</span>
                                    <span style="color:#0f172a; font-weight:700;">${location.lat}, ${location.lng}</span>
                                </div>
                                <div style="margin-top: 10px; padding: 10px; background: #ecfdf5; border-radius: 8px; border-left: 3px solid #10b981; font-size: 12px; line-height: 1.4; color: #065f46;">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Titik koordinat berada di <strong>LUAR KAWASAN LSD / LP2B</strong>.
                                </div>
                            </div>
                        </div>
                        `;

                        window.lastSearchMarker.bindPopup(contentString).openPopup();
                        document.getElementById('modal-body-content').innerHTML = contentString;
                        let btnPrint = document.getElementById('printButton');
                        if (btnPrint) {
                            btnPrint.removeAttribute('data-geometri_id');
                        }
                        _notif('#alert-message', 'info', 'Titik koordinat berada di luar kawasan LSD / LP2B.');
                    }
                })
                .fail(function(err) {
                    hideLoadingModal();
                    console.error('Gagal mengecek koordinat ke server:', err);
                    _notif('#alert-message', 'danger', 'Gagal memverifikasi koordinat ke server.');
                });
        };

        // Handler cetak informasi lahan dari popup / modal
        $(document).on('click', '#printButton', function(e) {
            let geometriId = this.getAttribute('data-geometri_id');
            if (geometriId) {
                let printUrl = (typeof BASE_URL !== 'undefined' ? BASE_URL : '') + '/print/' + geometriId;
                window.open(printUrl, '_blank');
                return;
            }

            let modalContent = document.getElementById('modal-body-content');
            if (modalContent && modalContent.innerHTML.trim()) {
                let printWin = window.open('', '_blank', 'width=800,height=600');
                if (printWin) {
                    printWin.document.write('<html><head><title>Cetak Informasi Lahan</title>');
                    printWin.document.write('<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">');
                    printWin.document.write('<style>body{font-family:sans-serif;padding:24px;line-height:1.5;}</style></head><body>');
                    printWin.document.write('<div style="text-align:center;margin-bottom:20px;"><h2>SISTEM INFORMASI LAHAN PERTANIAN (SILILA)</h2><h3>KABUPATEN BANYUWANGI</h3><hr></div>');
                    printWin.document.write(modalContent.innerHTML);
                    printWin.document.write('</body></html>');
                    printWin.document.close();
                    printWin.focus();
                    setTimeout(function() {
                        printWin.print();
                        printWin.close();
                    }, 500);
                }
            } else {
                Swal.fire('Informasi', 'Belum ada data lahan yang dipilih untuk dicetak.', 'info');
            }
        });

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
                    L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
                        maxZoom: 20,
                        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                        attribution: '© Google Satellite'
                    }).addTo(smallMap);

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
