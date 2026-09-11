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
                        <a href="#" class="edit-button" data-bs-toggle="modal" data-bs-target="#edit-geometri--{{$data->id}}"
                        data-id="{{$data->id}}" data-koordinat="{{ $data->koordinat}}" data-tipe="{{$data->tipe}}"
                        data-form-id="edit-form-{{$data->id}}">
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

        @foreach ($geometri as $data)

        @include('dashboard.geometri.edit-geometri')
        @endforeach


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
                            $('#table-1').DataTable({
                                "paging": true,
                                "pageLength": 25
                            });

                            // Menampilkan koordinat dalam modal saat tombol mata ditekan
                            $('.show-coordinate').on('click', function () {
                                var coordinate = $(this).data('coordinate');
                                $('#coordinateContent').text("Koordinat: " + coordinate);
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
