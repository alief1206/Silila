<div class="modal fade" id="add-desa" tabindex="-1" role="dialog" aria-labelledby="addDesaModalLabel"
aria-hidden="true">
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="addDesaModalLabel">Tambah Data Desa</h5>
            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
           <form action="{{ route('add-desa') }}" class="needs-validation" novalidate="" method="POST" enctype="multipart/form-data">
            @csrf
            @method('POST')

            <div class="form-group">
                <label for="add-kecamatan_id">Kecamatan</label>
                <select name="kecamatan_id" id="add-kecamatan_id" class="form-control" required>
                    <option value="" disabled selected>-- Pilih Kecamatan --</option>
                    @foreach($kecamatan as $item)
                        <option value="{{ $item->id }}">{{ $item->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="add-nama-desa">Nama Desa</label>
                <input type="text" name="desa" id="add-nama-desa" class="form-control" placeholder="Masukkan Nama Desa" required>
            </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-silila-outline" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-silila-emerald">
                <i class="material-icons mr-1" style="font-size: 16px;">save</i> Simpan Data Desa
            </button>
        </div>
</form>

        </div>

    </div>
</div>
</div>
