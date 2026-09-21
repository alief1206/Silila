@extends('dashboard.template')
@section('title', 'Geometri')

@include('dashboard.js.main')

@section('content')
<div class="card-body">
    <div class="table-responsive" id="tab">
        <table class="table table-striped" id="table-1">
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th>Koordinat</th>
                    <th>Tipe</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tfoot>
                <!-- footer content -->
            </tfoot>
            <tbody>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add-geometri">Tambah Data Geometri</button>
                @include('dashboard.geometri.add-geometri')
                @foreach ($geometri as $data)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>
                        <button class="btn btn-sm btn-primary show-coordinate" data-coordinate="{{ $data->koordinat }}" data-toggle="modal" data-target="#coordinateModal">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                    <td>{{ $data->tipe }}</td>
                    <td>
                        <a href="#" class="edit-button" data-bs-toggle="modal" data-bs-target="#edit-geometri"
                        data-id="{{$data->id}}" data-koordinat="{{ $data->koordinat}}" data-tipe="{{$data->tipe}}">
                         <i class="fas fa-edit"></i>
                     </a>

                        <a href="{{ route('delete-geometri', $data->id) }}" onclick="return confirm('Apakah Anda yakin ingin menghapus data geometri ini?')">
                            <i class="fas fa-trash-alt" style="color: _red"></i>
                        </a>


                    </td>
                </tr>

                @endforeach


            </tbody>
        </table>

        @include('dashboard.geometri.edit-geometri')
        
        <script>
            $(document).ready(function() {
                $('.edit-button').on('click', function() {
                    var id = $(this).data('id');
                    var tipe = $(this).data('tipe');
                    var coordinate = $(this).data('koordinat');
                    
                    // Convert GeoJSON back to array string for the form input
                    try {
                        var geoJson = JSON.parse(coordinate);
                        if(geoJson && geoJson.coordinates) {
                            coordinate = JSON.stringify(geoJson.coordinates);
                        }
                    } catch(e) {}
                    
                    var form = $('#edit-form');
                    form.attr('action', '/geometri/' + id);
                    form.find('input[name="koordinat"]').val(coordinate);
                    form.find('select[name="tipe"]').val(tipe);
                });
            });
        </script>


        <div class="d-flex justify-content-center mt-4">
            {{ $geometri->links('pagination::bootstrap-4') }}
        </div>

        <!-- Modal untuk menampilkan koordinat -->
        <div class="modal fade" id="coordinateModal" tabindex="-1" role="dialog" aria-labelledby="coordinateModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="coordinateModalLabel">Koordinat</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p id="coordinateContent"></p>

                    </div>
                    <script>
                       $(document).ready(function () {
                            // Menampilkan koordinat dalam modal saat tombol mata ditekan
                            $('.show-coordinate').on('click', function () {
                                var coordinate = $(this).data('coordinate');
                                // Parse GeoJSON back to coordinates array to show in UI
                                try {
                                    var geoJson = JSON.parse(coordinate);
                                    if(geoJson && geoJson.coordinates) {
                                        $('#coordinateContent').text("Koordinat: " + JSON.stringify(geoJson.coordinates));
                                    } else {
                                        $('#coordinateContent').text("Koordinat: " + coordinate);
                                    }
                                } catch(e) {
                                    $('#coordinateContent').text("Koordinat: " + coordinate);
                                }
                            });
                        });
                    </script>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>



        <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-+4pfP8tKG+KqF02zbAOqPIdHUglq3eRsTp3h2iKZnM4=" crossorigin="anonymous"></script>

@endsection
