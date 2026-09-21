<div class="modal fade" id="edit-geometri" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel"
aria-hidden="true">
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="editUserModalLabel">Edit Data Geometri</h5>
            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <form action="" method="POST" enctype="multipart/form-data" id="edit-form">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="koordinat">Koordinat (GeoJSON Array)</label>
                    <input type="text" name="koordinat" class="form-control" value="" id="koordinat" placeholder="Enter koordinat">
                </div>
                <div class="form-group">
                    <label for="tipe">Tipe</label>
                    <select name="tipe" class="form-control" id="tipe">
                        <option value="1">1 (LP2B)</option>
                        <option value="2">2 (LSD)</option>
                        <option value="3">3 (LBS)</option>
                    </select>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
