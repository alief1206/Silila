<div class="modal fade" id="add-kecamatan" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel"
aria-hidden="true">
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="editUserModalLabel">Tambah Data Kecamatan</h5>
            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
           <form action="{{ route('add-kecamatan') }}" class="needs-validation" novalidate="" method="POST" enctype="multipart/form-data">
            @csrf
            @method('POST')

            <div class="form-group">
                <label for="edit-kecamatan">Kecamatan</label>
                <input type="text" name="kecamatan" class="form-control"  placeholder="Masukkan Nama Kecamatan ">
            </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-silila-outline" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-silila-emerald">
                <i class="material-icons mr-1" style="font-size: 16px;">save</i> Simpan Data Kecamatan
            </button>
        </div>
</form>

        </div>

    </div>
</div>
</div>
