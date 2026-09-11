<div class="modal fade" id="add-geometri" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel"
aria-hidden="true">
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="editUserModalLabel">Tambah Data Geometri</h5>
            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
           <form action="{{ route('add-geometri') }}" class="needs-validation" novalidate="" method="POST" enctype="multipart/form-data">
            @csrf
            @method('POST')
        <div class="form-group">
            <label for="edit-name">Koordinat</label>
            <input type="text" name="koordinat" class="form-control"  placeholder="Masukkan Koordinat">
        </div>
        <div class="form-group">
            <label for="edit-email">Tipe</label>
            <input type="text" name="tipe" class="form-control"  placeholder="Masukkan Nama Penyakit">
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
