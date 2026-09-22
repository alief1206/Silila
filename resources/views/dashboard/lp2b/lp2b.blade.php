@extends('dashboard.template')
@section('title', 'Geometri')

@include('dashboard.js.main')

@section('content')
<div class="main-content-container container-fluid px-4 m-2">
    <!-- Page Header -->
    <div class="page-header row no-gutters py-4">
      <div class="col-12 col-sm-4  text-sm-left mb-0">
        <span class="text-uppercase page-subtitle">Dashboard</span>
        <h3 class="page-title">Data LP2B</h3>
      </div>
    </div>
    <div class="row">
        <div class="col">
          <div class="card card-small mb-4">
            <div class="card-header border-bottom">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add-lp2b">Tambah Data LP2B</button>
            </div>
            <div class="card-body p-0 pb-3 text-center">
        <table class="table table-striped" id="table-1">
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th>Geometri ID</th>
                    <th>Desa</th>
                    <th>KP2B</th>
                    <th>Keterangan</th>
                    <th>Luas</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tfoot>
                <!-- footer content -->
            </tfoot>
            @include('dashboard.lp2b.add-lp2b')
            <tbody>
                @foreach ($lp2b as $data)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $data->geometri_id }}</td>
                    <td>{{ \App\Models\Desa::find($data->geometri->desa_id)->nama }}</td>
                    <td>{{ $data->kp2b }}</td>
                    <td>{{ $data->ket }}</td>
                    <td>{{ $data->luas }}</td>
                    <td>
                        <a href="#" class="edit-button" data-bs-toggle="modal" data-bs-target="#edit-lp2b--{{$data->id}}"
                           data-id="{{ $data->id }}" data-geometri_id="{{ $data->geometri_id }}"
                           data-desa_id="{{ $data->desa_id }}" data-kp2b="{{ $data->kp2b}}" data-ket="{{ $data->ket}}" data-luas="{{ $data->luas}}" data-koordinat="{{ $data->koordinat}}" data-tipe="{{ $data->tipe}}">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="{{ route('delete-lp2b', $data->id) }}" onclick="return confirm('Apakah Anda yakin ingin menghapus data lp2b ini?')">
                            <i class="fas fa-trash-alt" style="color: red"></i>
                        </a>
                    </td>
                </tr>

                @endforeach
                @foreach ($lp2b as $data)
                @include('dashboard.lp2b.edit-lp2b')
                @endforeach
            </tbody>
        </table>
    </div>
    </div>
    </div>
    </div>
    </div>

        <!-- Modal untuk menampilkan koordinat -->
        <div class="modal fade" id="coordinateModal" tabindex="-1" role="dialog" aria-labelledby="coordinateModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable" role="document">
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
                            $('#table-3').DataTable({
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
