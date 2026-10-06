
<div class="modal fade" id="edit-desa--{{$data->id}}" tabindex="-1" role="dialog" aria-labelledby="editDesaModalLabel-{{$data->id}}"
aria-hidden="true">
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="editDesaModalLabel-{{$data->id}}">Edit Data Desa</h5>
            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <form action="{{ route('update-desa', $data->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="edit-kecamatan_id-{{$data->id}}">Kecamatan</label>
                    <select name="kecamatan_id" id="edit-kecamatan_id-{{$data->id}}" class="form-control" required>
                        @foreach($kecamatan as $kecItem)
                            <option value="{{ $kecItem->id }}" {{ $data->kecamatan_id == $kecItem->id ? 'selected' : '' }}>
                                {{ $kecItem->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="edit-nama-desa-{{$data->id}}">Nama Desa</label>
                    <input type="text" name="desa" value="{{ $data->nama }}" class="form-control" id="edit-nama-desa-{{$data->id}}" placeholder="Masukkan Nama Desa" required>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-silila-outline" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-silila-emerald">
                        <i class="material-icons mr-1" style="font-size: 16px;">check_circle</i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
