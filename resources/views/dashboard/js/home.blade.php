@push('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const apiKey = '{{ config('services.google_maps.key') }}';
        const script = document.createElement('script');
        script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}&callback=initMap&libraries=places`;
        script.async = true;
        script.defer = true;
        document.head.appendChild(script);
    });

    $('.btn-restart').on('click', function (){
        window.location.reload();
    })
</script>

<script>
    document.getElementById('printButton').addEventListener('click', function() {
            const geometriId = this.getAttribute('data-geometri_id');
            if (geometriId) {
                const printUrl = print/geometriId;
                window.location.href = printUrl;
            } else {
                console.error('Geometri ID tidak ditemukan.');
            }
    });
</script>

<script>
    $(document).ready(function() {
        // Mengambil data kecamatan dari server
        $.getJSON('api/geometri/kecamatan')
            .done(function(data) {
            data.forEach(function(kecamatan) {
                const option = new Option(kecamatan.nama, kecamatan.id);
                $('#kecamatan').append(option);
            });
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
                $.getJSON(`api/geometri/desa/${kecamatanId}`)
                    .done(function(data) {
                    data.forEach(function(desa) {
                        const option = new Option(desa.nama, desa.id);
                        $('#desa').append(option);
                    });
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                    console.error('Error fetching desa:', textStatus, errorThrown);
                });
            }
        });
    });
</script>

<script>
    // Hide the modal when loading ends
    function showLoadingModal() {
        $('#loadingModal').modal('show');
    }
    function hideLoadingModal() {
        $('#loadingModal').modal('hide');
    }
    function displayInfoWindow(data, map, polygon) {
        let contentString = '';
        if (polygon.tipe === 1) {
            contentString = `
            <div class="infowindow-content">
                <h5>Informasi</h5>
                <table class="table table-striped table-sm">
                <tr>
                    <th>Tipe Data</th>
                    <td>LP2B</td>
                </tr>
                </table>
            </div>
            `;
        } else if (polygon.tipe === 2) {
            contentString = `
            <div class="infowindow-content">
                <h5>Informasi</h5>
                <table class="table table-striped table-sm">
                    <tr>
                    <th>Tipe Data</th>
                    <td>LSD</td>
                </tr>
                </table>
            </div>
            `;
        }

        const infowindow = new google.maps.InfoWindow({
            content: contentString
        });
        infowindow.setPosition(polygon.getPath().getAt(0));
        infowindow.open(map);
        return infoWindow;
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
    function initMap(type, kecamatan, desa,kecamatanName,desaName) {
        map = new google.maps.Map(document.getElementById('maps'), {
            center: { lat: -8.36667, lng: 114.16667 }, // Koordinat Banyuwangi
            zoom: 11, // Tingkat Zoom
            mapTypeId: google.maps.MapTypeId.SATELLITE,
            disableDefaultUI: true,
            gestureHandling: 'none', // Menonaktifkan semua gestur
            scrollwheel: false, // Menonaktifkan zoom dengan scrollwheel
            draggable: true, // Mengizinkan pengguliran (scrolling) peta
        });

        // Mengizinkan pengguliran dengan sentuhan pada perangkat mobile
        map.setOptions({ gestureHandling: 'greedy' });

        // Menonaktifkan zoom dengan tombol + dan -
        map.setOptions({ zoomControl: false });

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
                let res = err.responseJSON;
                _notif('#alert-message', 'danger', res.message);
                // $('#loading-map-indicator').addClass('d-none');
                hideLoadingModal();
                Swal.fire({
                    icon: "warning",
                    title: "Mohon Maaf",
                    text: "Koordinat yang dicari tidak masuk dalam wilayah",
                });
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
                            return new google.maps.LatLng(coord[1], coord[0]);
                        });

                        let color = '#000000';
                        let borderColor = '#000000';
                        if (val.tipe == 1) {
                            color = 'rgba(54, 162, 235, 0.5)';  // Light blue with transparency
                            borderColor = 'rgba(54, 162, 235, 0.5)';  // Solid blue
                        } else if (val.tipe == 2) {
                            color = 'rgba(255, 99, 132, 0.5)';  // Light red with transparency
                            borderColor = 'rgba(255, 99, 132, 0.5)';  // Solid red
                        } else {
                            color = 'rgba(255, 206, 86, 0.5)';  // Light yellow with transparency
                            borderColor = 'rgba(255, 206, 86, 0.5)';  // Solid yellow
                        }

                        const polygon = new google.maps.Polygon({
                            paths: polygonCoords,
                            strokeColor: borderColor,
                            strokeOpacity: 0.3,
                            strokeWeight: 1,
                            fillColor: color,
                            fillOpacity: 0.8,
                            geometri_id: val.geometri_id,
                            tipe: val.tipe,
                            kecamatan:val.kecamatan,
                            desa:val.desa
                        });
                        polygon.setMap(map);
                        polygons.push(polygon);
                        if (i == 0 && (kecamatan != 0 || desa != 0)) {
                            map.setCenter(polygonCoords[0]);
                            map.setZoom(14);
                        }

                        polygon.addListener('click', function() {
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
                    Swal.fire({
                        icon: "warning",
                        title: "Mohon Maaf",
                        text: "Koordinat yang dicari tidak masuk dalam wilayah",
                    });
                }
            }
        });

        $.getJSON('assets/batas/batas.json', function(data) {
            data.features.forEach(feature => {
                    const coordinates = feature.geometry.coordinates[0][0].map(function(coord) {
                        return new google.maps.LatLng(coord[1],coord[0]);
                    });

                    const polygonbatas = new google.maps.Polygon({
                        paths: coordinates,
                        strokeColor: 'rgba(129, 15, 203, 1)',
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillColor: 'rgba(129, 15, 203, 0.3)',
                        fillOpacity: 0
                    });

                    polygonbatas.setMap(map);
                    polygonsbatas.push(polygonbatas);
            });
        });

    }


    document.addEventListener('DOMContentLoaded', function() {
        const isLoggedIn = '{{ Auth::check() }}';

        if (!isLoggedIn) {
            // Pengguna belum login, tampilkan modal login
            $('#login').modal('show');
        }else{
            $(document).on('submit', '#cari-koordinat', function(e){
            e.preventDefault();
            const searchTypeSelect = $('#tipe-pencarian').val();
            const searchKecamatanSelect = $('#kecamatan').val();
            const searchDesaSelect = $('#desa').val();
            const latitude = document.getElementById('latitude');
            const longitude = document.getElementById('longitude');
            let location;

            if (searchTypeSelect === 'latlng') {
                location = new google.maps.LatLng(latitude.value, longitude.value);
            } else if (searchTypeSelect === 'cea') {
                location = ceaToLatLng({x: latitude.value, y: longitude.value});
            } else {
                _notif('#alert-message', 'danger', 'Please select a search type.');
                return;
            }

            let isInPolygon = false;
            let polygonWithCoordinat;

            // Jika Banyak
            let polygonsWithCoordinat = []

            polygons.forEach((polygon) => {
                if (google.maps.geometry.poly.containsLocation(location, polygon)) {
                    isInPolygon = true;
                    polygonWithCoordinat = polygon;
                    polygonsWithCoordinat.push(polygon)
                }
            });

            let contentString = `
                <div class="infowindow-content">
                    <p><strong>Koordinat</strong> ${location.lat()}, ${location.lng()}</p>
                    <p style="margin: 10px 0; font-size: 14px;">
                        <span style="color: #0066cc;">*Data lokasi tersebut merupakan referensi dan bukan merupakan ijin peruntukan lahan.<br>
                        Terkait periijinan lebih lanjut bisa melakukan koordinasi dengan tim Forum Penataan Ruang Daerah (FPRD) Kabupaten Banyuwangi</span>
                    </p>
                </div>
            `;

            map.setCenter(location);
            map.setZoom(20);

            const marker = new google.maps.Marker({
                position: location,
                map: map,
                title: 'Searched Location'
            });

            if (isInPolygon) {
                const kecamatan = polygonWithCoordinat.kecamatan;
                const desa = polygonWithCoordinat.desa;
                const userName = '{{ Auth::check() ? Auth::user()->nama : '' }}';
                const userNip = '{{ Auth::check() ? Auth::user()->nip : '' }}';

                let contentString = `
                    <div class="infowindow-content">
                        <p><strong>Koordinat</strong> ${location.lat()}, ${location.lng()}</p>
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
                            <span style="font-weight: bold; color: #0066cc;">${location.lat()}, ${location.lng()}</span>
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
                        const latlng = location.lat() + ',' + location.lng();
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

                        var printMap = new google.maps.Map(document.getElementById('print-map'), {
                            center: map.getCenter(),
                            zoom: map.getZoom(),
                            mapTypeId: google.maps.MapTypeId.SATELLITE,
                        });

                        polygonsWithCoordinat.forEach((polygon) => {
                            new google.maps.Polygon({
                                paths: polygon.getPath(),
                                strokeColor: polygon.strokeColor,
                                strokeOpacity: polygon.strokeOpacity,
                                strokeWeight: polygon.strokeWeight,
                                fillColor: polygon.fillColor,
                                fillOpacity: polygon.fillOpacity,
                                map: printMap,
                                geometri_id:polygon.geometriId,
                                tipe:polygon.tipe
                            });
                        });

                        const marker = new google.maps.Marker({
                            position: location,
                            map: printMap,
                            title: 'Searched Location'
                        });

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
                const userName = '{{ Auth::check() ? Auth::user()->nama : '' }}';
                const userNip = '{{ Auth::check() ? Auth::user()->nip : '' }}';
                const currentDate = new Date();
                const formattedDate = `${currentDate.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })}, ${currentDate.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })} WIB`;

                // Update modal content and show modal
                document.getElementById('modal-body-content').innerHTML = contentString;
                // $('#informasi').modal('show');
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
                <div class="infowindow-content" style="font-family: Arial, sans-serif; color: #333; background-color: #f9f9f9; border: 1px solid #ccc; border-radius: 10px; padding: 20px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
                    <h1 style="color:blue; text-align:center;"><strong>DETAIL INFORMASI</strong></h1>
                                        <div id="print-map" style="height: 500px; width: 100%; margin: auto;"></div>
                                    <h2 style="text-align: center; color: #0066cc; margin-bottom: 20px;">Informasi Lokasi</h2>
                                    <p style="margin: 10px 0; font-size: 14px;">
                                        Dari titik koordinat
                                        <span style="font-weight: bold; color: #0066cc;">${location.lat()}, ${location.lng()}</span>
                                        yang berada di
                                        <span style="font-weight: bold;">Kelurahan/Desa ${desaName  || '-'}</span>,
                                        <span style="font-weight: bold;">Kec. ${kecamatanName  || '-'}</span> tidak termasuk wilayah LP2B atau LSD.
                                    </p>
                                    <p style="margin: 10px 0; font-size: 14px;">
                                        <strong>Geometri ID:</strong>
                                        <span style="color: #0066cc;"> - </span>
                                    </p>
                                    <p style="margin: 10px 0; font-size: 14px;">
                                        <strong>KP2B:</strong>
                                        <span style="color: #0066cc;"> - </span>
                                    </p>
                                    <p style="margin: 10px 0; font-size: 14px;">
                                        <strong>Luas:</strong>
                                        <span style="color: #0066cc;"> - </span>
                                    </p>
                                    <p style="margin: 10px 0; font-size: 14px;">
                                        <strong>Keterangan:</strong>
                                        <span style="color: #0066cc;">-</span>
                                    </p>
                                    <p style="margin: 10px 0; font-size: 14px;">
                                        <span style="color: #0066cc;">*Data lokasi tersebut merupakan referensi dan bukan merupakan ijin peruntukan lahan.<br>
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

                document.body.innerHTML = printArea.innerHTML;

                var printMap = new google.maps.Map(document.getElementById('print-map'), {
                    center: map.getCenter(),
                    zoom: 20,
                    mapTypeId: google.maps.MapTypeId.SATELLITE,
                });

                polygons.forEach((polygon) => {
                    new google.maps.Polygon({
                        paths: polygon.getPath(),
                        strokeColor: polygon.strokeColor,
                        strokeOpacity: polygon.strokeOpacity,
                        strokeWeight: polygon.strokeWeight,
                        fillColor: polygon.fillColor,
                        fillOpacity: polygon.fillOpacity,
                        map: printMap
                    });
                });

                const marker = new google.maps.Marker({
                    position: location,
                    map: printMap,
                    title: 'Searched Location'
                });

                setTimeout(function() {
                    window.print();

                    document.body.innerHTML = originalContent;

                    document.body.removeChild(printArea);
                }, 1000);
                    });
                }
            });
        }
    });


    function isNumber(value) {
        return !isNaN(parseFloat(value)) && isFinite(value);
    }

    // Convert the CEA coordinates to LatLng coordinates
    function ceaToLatLng(ceaCoords) {
        let CEA_PROJECTION = map.getProjection()
        const point = CEA_PROJECTION.fromPointToLatLng(new google.maps.Point(ceaCoords.x, ceaCoords.y));
        return new google.maps.LatLng(point.lat(), point.lng());
    }
    // Convert the LatLng coordinates to CEA coordinates
    function latLngToCea(latLng) {
        let CEA_PROJECTION = map.getProjection()
        const point = CEA_PROJECTION.fromLatLngToPoint(latLng);
        return { x: point.x, y: point.y };
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
                    const map = new google.maps.Map(image, {
                        center: { lat: lat, lng: lng },
                        zoom: 15,
                        mapTypeId: google.maps.MapTypeId.SATELLITE,
                    });

                    new google.maps.Marker({
                        position: { lat: lat, lng: lng },
                        map: map,
                        title: 'Location'
                    });

                    // Add polygon
                    if (polygonData) {
                        const polygonCoords = polygonData.map(coord => ({ lat: coord[0], lng: coord[1] }));
                        const polygon = new google.maps.Polygon({
                            paths: polygonCoords,
                            strokeColor: polygonColor,
                            strokeOpacity: 0.8,
                            strokeWeight: 2,
                            fillColor: polygonColor,
                            fillOpacity: 0.35
                        });
                        polygon.setMap(map);
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
            polygon.setMap(null);
        });
        polygons = [];

        // Loop melalui data geometri dan tambahkan setiap geometri pada peta sebagai polygon
        data.forEach(function(geometry) {
            var coordinates = JSON.parse(geometry.coordinates);
            var polygon = new google.maps.Polygon({
                paths: coordinates,
                strokeColor: '#FF0000',
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: '#FF0000',
                fillOpacity: 0.35,
                editable: false, // Ubah menjadi true jika Anda ingin memungkinkan pengeditan geometri
                map: map
            });
            polygons.push(polygon);
        });
    }

</script>

@endpush
