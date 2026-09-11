<!-- Modal Edit Data -->


{{-- <script>
    $(document).ready(function() {
        $('.edit-button').on('click', function(event) {
            var id_geometri = $(this).data('id_geometri');
            var koordinat = $(this).data('koordinat');
            var tipe = $(this).data('tipe');

            var modal = $('#edit-geometri');
            modal.find('.modal-body #id_geometri').val(id_geometri);
            modal.find('.modal-body #koordinat').val(koordinat);
            modal.find('.modal-body #tipe').val(tipe);
            // modal.find('.modal-body #foto_produk').val(foto_produk); // Ini tidak bisa di-set secara langsung karena input tipe file


            modal.modal('show');
        });
    });
</script>

 --}}



<div class="modal fade" id="edit-geometri--{{$data->id}}"  tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel"
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
            <!-- Form for editing user data -->
            {{-- @if ($data->count() == 0)
            <p>data kosong</p>
        @else --}}
            <form  action="{{route('update-geometri',$data->id)}}" method="POST" enctype="multipart/form-data" id="edit-form-{{$data->id}}>
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="edit-id">ID geometri</label>
                    <input type="hidden" name="id" value="{{$data->id}}" class="form-control" id="id" placeholder="ID Geometri">
                </div>
                <div class="form-group">
                    <label for="edit-koordinat">Koordinat</label>
                    <input type="text" name="koordinat" class="form-control" value="{{$data->koordinat}}" id="koordinat" placeholder="Enter luas">
                </div>
                <div class="form-group">
                    <label for="edit-tipe">tipe</label>
                    <input type="text" name="tipe" class="form-control" value="{{$data->tipe}}" id="tipe" placeholder="Enter tipe">
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
