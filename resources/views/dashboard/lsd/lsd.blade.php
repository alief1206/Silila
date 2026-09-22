@extends('dashboard.template')
@section('title', 'Geometri')

@include('dashboard.js.main')

@section('content')
<div class="main-content-container container-fluid px-4">
    <div class="page-header row no-gutters ms-2 py-4">
        <div class="col-12 col-sm-4 text-sm-left mb-0">
          <span class="text-uppercase page-subtitle">Dashboard</span>
          <h3 class="page-title">Data LSD</h3>
        </div>
      </div>
<div class="card-body">
    <div class="table-responsive" id="tab">
        <table class="table table-striped" id="table-2">
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th>Geometri ID</th>
                    <th>Hutan</th>
                    <th>Luas</th>
                    <th>Ba</th>
                    <th>Luascea_hm</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tfoot>
                <!-- footer content -->
            </tfoot>
            <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#add-lsd">Tambah Data LSD</button>
            @include('dashboard.lsd.add-lsd')
            <tbody>
                @foreach ($lsd as $data)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $data->geometri_id }}</td>
                    <td>{{ $data->hutan }}</td>
                    <td>{{ $data->luas }}</td>
                    <td>{{ $data->ba}}</td>
                    <td>{{ $data->luascea_hm}}</td>
                    <td>
                        <a href="#" class="edit-button" data-bs-toggle="modal" data-bs-target="#edit-lsd--{{$data->id}}">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="{{ route('delete-lsd', $data->id) }}" onclick="return confirm('Apakah Anda yakin ingin menghapus data lsd ini?')">
                            <i class="fas fa-trash-alt" style="color: red"></i>
                        </a>
                    </td>
                </tr>

                @endforeach
                @foreach ($lsd as $data)
                @include('dashboard.lsd.edit-lsd')
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-center mt-4">
            {{ $lsd->links('pagination::bootstrap-4') }}
        </div>
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
                            $('#table-2').DataTable({
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
