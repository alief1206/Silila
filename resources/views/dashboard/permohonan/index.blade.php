@extends('dashboard.template')
@section('title', 'Permohonan Surat')

@section('content')
<div class="main-content-container container-fluid px-4 py-3">
    <!-- Page Header -->
    <div class="page-header row no-gutters align-items-center justify-content-between py-3">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <span class="page-subtitle"><i class="material-icons mr-1" style="font-size: 14px; vertical-align: middle;">description</i> Permohonan Surat</span>
            <h3 class="page-title">Daftar Permohonan Masuk</h3>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card card-small mb-4">
                <div class="card-header border-bottom">
                    <h6 class="m-0">Semua Permohonan</h6>
                </div>
                <div class="card-body p-0 pb-3 text-center">
                    <div class="table-responsive">
                        <table class="table mb-0 table-striped">
                            <thead class="bg-light">
                                <tr>
                                    <th scope="col" class="border-0">No</th>
                                    <th scope="col" class="border-0">Kode Registrasi</th>
                                    <th scope="col" class="border-0">Pemohon</th>
                                    <th scope="col" class="border-0">Tanggal</th>
                                    <th scope="col" class="border-0">Status</th>
                                    <th scope="col" class="border-0">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($permohonan as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->kode_registrasi }}</td>
                                    <td>{{ $item->nama_pemohon }}<br><small>{{ $item->nik }}</small></td>
                                    <td>{{ $item->created_at->format('d M Y H:i') }}</td>
                                    <td>
                                        <span class="badge badge-pill 
                                            @if($item->status == 'Menunggu Verifikasi') badge-warning
                                            @elseif($item->status == 'Diproses') badge-info
                                            @elseif($item->status == 'Selesai') badge-success
                                            @elseif($item->status == 'Ditolak') badge-danger
                                            @else badge-secondary @endif
                                        ">
                                            {{ $item->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#detailModal{{ $item->id }}">
                                            <i class="material-icons">visibility</i> Detail
                                        </button>
                                    </td>
                                </tr>

                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">Belum ada data permohonan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
@foreach($permohonan as $item)
<div class="modal fade text-left" id="detailModal{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Permohonan - {{ $item->kode_registrasi }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Data Pemohon</h6>
                        <table class="table table-sm">
                            <tr><th>Nama</th><td>{{ $item->nama_pemohon }}</td></tr>
                            <tr><th>NIK</th><td>{{ $item->nik }}</td></tr>
                            <tr><th>No. HP</th><td>{{ $item->no_hp }}</td></tr>
                            <tr><th>Alamat</th><td>{{ $item->alamat_pemohon }}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Data Lahan</h6>
                        <table class="table table-sm">
                            <tr><th>Kecamatan</th><td>{{ $item->kecamatan }}</td></tr>
                            <tr><th>Desa</th><td>{{ $item->desa }}</td></tr>
                            <tr><th>Alamat Lahan</th><td>{{ $item->alamat_lahan }}</td></tr>
                            <tr><th>Luas Lahan</th><td>{{ $item->luas_lahan }}</td></tr>
                            <tr><th>Koordinat</th><td>{{ $item->koordinat }}</td></tr>
                        </table>
                    </div>
                </div>
                <hr>
                <h6>Dokumen Pendukung</h6>
                <ul>
                    @if($item->file_surat_permohonan)
                    <li>Surat Permohonan: <a href="{{ asset($item->file_surat_permohonan) }}" target="_blank">Lihat Dokumen</a></li>
                    @endif
                    @if($item->file_ktp)
                    <li>KTP: <a href="{{ asset($item->file_ktp) }}" target="_blank">Lihat Dokumen</a></li>
                    @endif
                    @if($item->file_petok_c)
                    <li>Petok C: <a href="{{ asset($item->file_petok_c) }}" target="_blank">Lihat Dokumen</a></li>
                    @endif
                    @if($item->file_skt_kades)
                    <li>SKT Kades: <a href="{{ asset($item->file_skt_kades) }}" target="_blank">Lihat Dokumen</a></li>
                    @endif
                    @if($item->file_penguasaan_fisik)
                    <li>Penguasaan Fisik: <a href="{{ asset($item->file_penguasaan_fisik) }}" target="_blank">Lihat Dokumen</a></li>
                    @endif
                    @if($item->file_shp)
                    <li>File SHP/GeoJSON: <a href="{{ asset($item->file_shp) }}" target="_blank">Lihat Dokumen</a></li>
                    @endif
                </ul>

                <hr>
                <h6>Update Status</h6>
                <form action="{{ route('dashboard.permohonan.status', $item->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label>Status Permohonan</label>
                        <select name="status" class="form-control">
                            <option value="Menunggu Verifikasi" {{ $item->status == 'Menunggu Verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                            <option value="Diproses" {{ $item->status == 'Diproses' ? 'selected' : '' }}>Diproses</option>
                            <option value="Selesai" {{ $item->status == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="Ditolak" {{ $item->status == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Catatan Admin</label>
                        <textarea name="catatan_admin" class="form-control" rows="3">{{ $item->catatan_admin }}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Upload Surat Balasan (Opsional)</label>
                        @if($item->file_surat_balasan)
                            <div class="mb-2">
                                <a href="{{ asset($item->file_surat_balasan) }}" target="_blank" class="badge badge-success"><i class="material-icons" style="font-size:12px;">check_circle</i> Surat Balasan Tersedia (Lihat)</a>
                            </div>
                        @endif
                        <input type="file" name="file_surat_balasan" class="form-control-file" accept=".pdf,.doc,.docx,.jpg,.png">
                        <small class="text-muted">Upload file surat balasan resmi untuk diunduh oleh pengguna (PDF/Word/Image).</small>
                    </div>
                    <button type="submit" class="btn btn-success">Simpan Status</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection
