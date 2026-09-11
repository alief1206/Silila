<div class="modal fade" id="add-kecamatan" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel"
aria-hidden="true">
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="editUserModalLabel">Tambah Data LP2B</h5>
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
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Tambah</button>
        </div>
</form>

        </div>

    </div>
</div>
</div>
