
<div class="modal fade" id="edit-kecamatan--{{$data->id}}" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel"
aria-hidden="true">
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="editUserModalLabel">Edit Data Kecamatan</h5>
            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <!-- Form for editing user data -->
            {{-- @if ($data->count() == 0)
            <p>data kosong</p>
        @else --}}
            <form  action="{{route('update-kecamatan',$data->id)}}" nonvalidate="" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-group" @style('display:none;')>
                    <label for="edit-id">ID LP2b</label>
                    <input type="text" name="id" value="{{$data->id}}" class="form-control" id="id" placeholder="Enter name">
                </div>

                <div class="form-group">
                    <label for="edit-kecamatan">Kecamatan</label>
                    <input type="text" name="kecamatan"  value="{{$data->kecamatan}}" class="form-control" id="desa_id" placeholder="Enter Object ID">
                </div>

                <!-- Tombol Submit untuk mengirimkan formulir -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
            {{-- @endif --}}
        </div>
    </div>
</div>
</div>
