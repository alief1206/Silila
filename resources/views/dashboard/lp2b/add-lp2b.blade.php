<div class="modal fade" id="add-lp2b" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel"
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
           <form action="{{ route('add-lp2b') }}" class="needs-validation" novalidate="" method="POST" enctype="multipart/form-data">
            @csrf
            @method('POST')
            <div class="form-group">
                <label for="edit-geometri_id">Geometri ID</label>
                <input type="text" name="geometri_id" class="form-control"  placeholder="Masukkan ID Geometri">
            </div>
            <div class="form-group">
                <label for="edit-desa_id">Desa ID</label>
                <input type="text" name="desa_id" class="form-control"  placeholder="Masukkan ID Desa ">
            </div>
            <div class="form-group">
                <label for="edit-kp2b">KP2B</label>
                <input type="text" name="kp2b" class="form-control"  placeholder="Masukkan Data KP2B ">
            </div>
            <div class="form-group">
                <label for="edit-ket">Keterangan</label>
                <input type="text" name="ket" class="form-control"  placeholder="Masukkan Keterangan ">
            </div>
            <div class="form-group">
                <label for="edit-luas">luas</label>
                <input type="text" name="luas" class="form-control"  placeholder="Masukkan Luas ">
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
