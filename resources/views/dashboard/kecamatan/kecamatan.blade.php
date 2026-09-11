@extends('dashboard.template')
@section('title', 'Geometri')

@include('dashboard.js.main')

@section('content')
<div class="main-content-container container-fluid px-4">
    <!-- Page Header -->
    <div class="page-header row no-gutters py-4">
      <div class="col-12 col-sm-4  text-sm-left mb-0">
        <span class="text-uppercase page-subtitle">Dashboard</span>
        <h3 class="page-title">Data Kecamatan</h3>
      </div>
    </div>
    <div class="row">
        <div class="col">
          <div class="card card-small mb-4">
            <div class="card-header border-bottom">
              {{-- <h6> --}}
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add-kecamatan">Tambah Data Kecamatan</button>

              {{-- </h6> --}}
            </div>
            <div class="card-body p-0 pb-3 text-center">
        <table class="table table-striped" id="table-1">
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th>Nama</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tfoot>
                <!-- footer content -->
            </tfoot>
            @include('dashboard.kecamatan.add-kecamatan')
            <tbody>
                @foreach ($kec as $data)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $data->nama }}</td>

                    <td>
                        <a href="#" class="edit-button" data-bs-toggle="modal" data-bs-target="#edit-kecamatan--{{$data->id}}"
                           data-id="{{ $data->id }}" data-geometri_id="{{ $data->geometri_id }}"
                           data-desa_id="{{ $data->desa_id }}" data-kp2b="{{ $data->kp2b}}" data-ket="{{ $data->ket}}" data-luas="{{ $data->luas}}" data-koordinat="{{ $data->koordinat}}" data-tipe="{{ $data->tipe}}">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="{{ route('delete-kecamatan', $data->id) }}" onclick="return confirm('Apakah Anda yakin ingin menghapus data kecamatan ini?')">
                            <i class="fas fa-trash-alt" style="color: red"></i>
                        </a>


                    </td>
                </tr>

                @endforeach
                @foreach ($kec as $data)
                @include('dashboard.kecamatan.edit-kecamatan')
                @endforeach



            </tbody>
        </table>
    </div>
    </div>
    </div>
    </div>
    </div>

        {{-- <div class="d-flex justify-content-center mt-4">
            {{ $lp2b->links('pagination::bootstrap-4') }}
        </div> --}}

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
