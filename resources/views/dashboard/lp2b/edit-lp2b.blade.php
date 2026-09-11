<!-- Modal Edit Data -->


<script>
    $(document).ready(function() {
        $('.edit-button').on('click', function(event) {
            var id= $(this).data('id');
            var geometri_id = $(this).data('geometri_id');
            var desa_id = $(this).data('desa_id');
            var kp2b = $(this).data('kp2b');
            var ket = $(this).data('ket');
            var luas = $(this).data('luas');
            var koordinat = $(this).data('koordinat');
            var tipe = $(this).data('tipe');

            var modal = $('#edit-lp2b');
            modal.find('.modal-body #id').val(id);
            modal.find('.modal-body #geometri_id').val(geometri_id);
            modal.find('.modal-body #desa_id').val(desa_id);
            modal.find('.modal-body #kp2b').val(kp2b);
            modal.find('.modal-body #ket').val(ket);
            modal.find('.modal-body #luas').val(luas);
            modal.find('.modal-body #koordinat').val(koordinat);
            modal.find('.modal-body #tipe').val(tipe);
            // modal.find('.modal-body #foto_produk').val(foto_produk); // Ini tidak bisa di-set secara langsung karena input tipe file


            modal.modal('show');
        });
    });
</script>





<div class="modal fade" id="edit-lp2b--{{$data->id}}" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel"
aria-hidden="true">
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="editUserModalLabel">Edit Data LP2B</h5>
            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <!-- Form for editing user data -->
            {{-- @if ($data->count() == 0)
            <p>data kosong</p>
        @else --}}
            <form  action="{{route('update-lp2b',$data->id)}}" nonvalidate="" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-group" @style('display:none;')>
                    <label for="edit-id">ID LP2b</label>
                    <input type="text" name="id" value="{{$data->id}}" class="form-control" id="id" placeholder="Enter name">
                </div>
                <div class="form-group">
                    <label for="edit-geometri_id">Geometri id</label>
                    <input type="text" name="geometri_id"  value="{{$data->geometri_id}}" class="form-control" id="geometri_id" placeholder="Enter Object ID">
                </div>
                <div class="form-group">
                    <label for="edit-desa_id">Desa</label>
                    <input type="text" name="desa_id"  value="{{$data->desa_id}}" class="form-control" id="desa_id" placeholder="Enter Object ID">
                </div>
                <div class="form-group">
                    <label for="edit-kp2b">KP2B</label>
                    <input type="text" name="kp2b"  value="{{$data->kp2b}}" class="form-control" id="kp2b" placeholder="Enter KP2B">
                </div>
                <div class="form-group">
                    <label for="edit-ket">Keterangan</label>
                    <input type="text" name="ket" class="form-control"  value="{{$data->ket}}" id="ket"  placeholder="Enter Keterangan">

                </div>
                <div class="form-group">
                    <label for="edit-luas">luas</label>
                    <input type="text" name="luas" class="form-control" value="{{$data->luas}}" id="luas" placeholder="Enter luas">
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
