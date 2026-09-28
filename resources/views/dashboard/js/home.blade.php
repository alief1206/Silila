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
        let tipeName = polygon.tipe === 1 ? 'LP2B' : (polygon.tipe === 2 ? 'LSD' : (polygon.tipe === 3 ? 'LBS' : 'Kawasan Pertanian'));
        let kecText = polygon.kecamatan || (data && data.kecamatan) || '-';
        let desaText = polygon.desa || (data && data.desa) || '-';
        let luasText = (data && data.luas) ? Number(data.luas).toLocaleString('id-ID') + ' m²' : '-';
        let ketText = (data && (data.ket || data.hutan)) || '-';

        let contentString = `
        <div class="infowindow-silila" style="min-width: 250px; font-family: var(--font-sans);">
            <div class="infowindow-header" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); color: #ffffff; padding: 10px 14px; border-top-left-radius: 12px; border-top-right-radius: 12px; display: flex; justify-content: space-between; align-items: center;">
                <span style="font-weight: 700; font-size: 13.5px; font-family: var(--font-heading);">Detail Bidang Lahan</span>
                <span style="background: rgba(255,255,255,0.25); color: #fff; font-size: 10px; font-weight: 800; padding: 2px 7px; border-radius: 10px;">${tipeName}</span>
            </div>
            <div class="infowindow-body" style="padding: 12px 14px; background: #ffffff; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                <div style="display:flex; justify-content:space-between; margin-bottom: 6px; font-size: 12px;">
                    <span style="color:#64748b; font-weight:600;">Kecamatan:</span>
                    <span style="color:#0f172a; font-weight:700;">${kecText}</span>
                </div>
                <div style="display:flex; justify-content:space-between; margin-bottom: 6px; font-size: 12px;">
                    <span style="color:#64748b; font-weight:600;">Desa/Kelurahan:</span>
                    <span style="color:#0f172a; font-weight:700;">${desaText}</span>
                </div>
                <div style="display:flex; justify-content:space-between; margin-bottom: 6px; font-size: 12px;">
                    <span style="color:#64748b; font-weight:600;">Estimasi Luas:</span>
                    <span style="color:#059669; font-weight:800;">${luasText}</span>
                </div>
                <div style="display:flex; justify-content:space-between; font-size: 12px;">
                    <span style="color:#64748b; font-weight:600;">Keterangan:</span>
                    <span style="color:#0f172a; font-weight:600;">${ketText}</span>
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
                        const coordinates = JSON.parse(val.koordinat);
                        const polygonCoords = coordinates[0][0].map(function(coord) {
                            return [coord[1], coord[0]];
                        });

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

                        const polygon = L.polygon(polygonCoords, {
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

                        // Interactive Hover Effects
                        polygon.on('mouseover', function() {
                            this.setStyle({
                                weight: 2.8,
                                fillOpacity: 0.82,
                                color: '#ffffff'
                            });
                        });
                        polygon.on('mouseout', function() {
                            this.setStyle({
                                weight: 1.8,
                                fillOpacity: 0.55,
                                color: borderColor
                            });
                        });

                        polygon.addTo(map);
                        polygons.push(polygon);
                        if (i == 0 && (kecamatan != 0 || desa != 0)) {
                            map.setView(polygonCoords[0], 14);
                        }

                        polygon.on('click', function() {
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
                let coords = polygon.getLatLngs()[0].map(latlng => [latlng.lng, latlng.lat]);
                coords.push(coords[0]); // close the polygon for turf
                let poly = turf.polygon([coords]);
                if (turf.booleanPointInPolygon(pt, poly)) {
                    isInPolygon = true;
                    polygonWithCoordinat = polygon;
                    polygonsWithCoordinat.push(polygon)
                }
            });

            let contentString = `
                <div class="infowindow-content">
                    <p><strong>Koordinat</strong> ${location.lat}, ${location.lng}</p>
                    <p style="margin: 10px 0; font-size: 14px;">
                        <span style="color: #059669; font-weight: 500;">*Data lokasi tersebut merupakan referensi dan bukan merupakan ijin peruntukan lahan.<br>
                        Terkait periijinan lebih lanjut bisa melakukan koordinasi dengan tim Forum Penataan Ruang Daerah (FPRD) Kabupaten Banyuwangi</span>
                    </p>
                </div>
            `;

            map.setView(location, 20);

            const marker = L.marker(location, {
                title: 'Searched Location'
            }).addTo(map);

            if (isInPolygon) {
                const kecamatan = polygonWithCoordinat.kecamatan;
                const desa = polygonWithCoordinat.desa;
                const userName = '{{ Auth::check() ? Auth::user()->nama : '' }}' || window.guestName || 'Pengunjung';
                const userNip = '{{ Auth::check() ? Auth::user()->nip : '' }}' || window.guestNik || '-';

                let contentString = `
                    <div class="infowindow-content">
                        <p><strong>Koordinat</strong> ${location.lat}, ${location.lng}</p>
                        <p><strong>Kecamatan:</strong> ${kecamatan}</p>
                        <p><strong>Desa:</strong> ${desa}</p>
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
                    const geometriId = element.geometri_id;
                    const tipe = element.tipe;
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
                                <hr>
                                <h5 class="mb-3"><strong>Tipe:</strong> LP2B</h5>
                                <p><strong>Geometri ID:</strong> ${res.data.geometri_id}</p>
                                <p><strong>Luas:</strong> ${formattedLuas}</p>
                                <p><strong>KP2B:</strong> ${res.data.kp2b}</p>
                                <p><strong>Keterangan:</strong> ${res.data.ket}</p>
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
                                    <span style="color: rgba(54, 162, 235) !important;">${res.data.kp2b}</span>
                                </p>
                                <p style="margin: 10px 0; font-size: 14px;">
                                    <strong>Keterangan:</strong>
                                    <span style="color: rgba(54, 162, 235) !important;">${res.data.ket}</span>
                                </p>
                            `;
                        } else if (res.data.tipe === 2) {
                            contentString += `
                                <hr>
                                <h5 class="mb-3"><strong>Tipe:</strong> LSD</h5>
                                <p><strong>Geometri ID:</strong> ${res.data.geometri_id}</p>
                                <p><strong>Luas:</strong> ${formattedLuas}</p>
                                <p><strong>Hutan:</strong> ${res.data.hutan}</p>
                                <p><strong>BA:</strong> ${res.data.ba}</p>
                                <p><strong>Luas CEA HM:</strong> ${res.data.luascea_hm}</p>
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
                                <hr>
                                <p><strong>Geometri ID:</strong> ${res.data.geometri_id}</p>
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
                    contentString += `</div>`;

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
