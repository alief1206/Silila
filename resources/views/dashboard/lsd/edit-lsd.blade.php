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





<div class="modal fade" id="edit-lsd--{{$data->id}}" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel"
aria-hidden="true">
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="editUserModalLabel">Edit Data LSD</h5>
            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <!-- Form for editing user data -->
            {{-- @if ($data->count() == 0)
            <p>data kosong</p>
        @else --}}
            <form  action="{{route('update-lsd',$data->id)}}" nonvalidate="" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-group" @style('display:none;')>
                    <label for="edit-id">ID LSD</label>
                    <input type="text" name="id" value="{{$data->id}}" class="form-control" id="id" placeholder="Enter name">
                </div>
                <div class="form-group">
                    <label for="edit-geometri_id">Geometri id</label>
                    <input type="text" name="geometri_id"  value="{{$data->geometri_id}}" class="form-control" id="geometri_id" placeholder="Enter Object ID">
                </div>
                <div class="form-group">
                    <label for="edit-desa_id">LSD</label>
                    <input type="text" name="lsd"  value="{{$data->lsd}}" class="form-control" id="lsd" placeholder="Enter Object ID">
                </div>
                <div class="form-group">
                    <label for="edit-kp2b">Hutan</label>
                    <input type="text" name="hutan"  value="{{$data->hutan}}" class="form-control" id="hutan" placeholder="Enter KP2B">
                </div>
                <div class="form-group">
                    <label for="edit-luas">luas</label>
                    <input type="text" name="luas" class="form-control" value="{{$data->luas}}" id="luas" placeholder="Enter luas">
                </div>
                <div class="form-group">
                    <label for="edit-ket">Keterangan</label>
                    <input type="text" name="ket" class="form-control"  value="{{$data->ket}}" id="ket"  placeholder="Enter Keterangan">

                </div>
                <div class="form-group">
                    <label for="edit-ket">Irigasi</label>
                    <input type="text" name="irigasi_pr" class="form-control"  value="{{$data->irigasi_pr}}" id="irigasi_pr"  placeholder="Enter Keterangan">

                </div>
                <div class="form-group">
                    <label for="edit-ket">Kewenanangan</label>
                    <input type="text" name="kewenangan" class="form-control"  value="{{$data->kewenangan}}" id="kewenangan"  placeholder="Enter Keterangan">

                </div>
                <div class="form-group">
                    <label for="edit-ket">IP</label>
                    <input type="text" name="ip" class="form-control"  value="{{$data->ip}}" id="ip"  placeholder="Enter Keterangan">

                </div>
                <div class="form-group">
                    <label for="edit-ket">Prod</label>
                    <input type="text" name="prod" class="form-control"  value="{{$data->prod}}" id="prod"  placeholder="Enter Keterangan">

                </div>
                <div class="form-group">
                    <label for="edit-ket">Irigasi</label>
                    <input type="text" name="irigasi" class="form-control"  value="{{$data->irigasi}}" id="irigasi"  placeholder="Enter Keterangan">

                </div>
                <div class="form-group">
                    <label for="edit-ket">Kondisi Gab</label>
                    <input type="text" name="kondisigab" class="form-control"  value="{{$data->kondisigab}}" id="kondisigab"  placeholder="Enter Keterangan">

                </div>
                <div class="form-group">
                    <label for="edit-ket">Kontam Gab</label>
                    <input type="text" name="kontamgab" class="form-control"  value="{{$data->kontamgab}}" id="kontamgab"  placeholder="Enter Keterangan">

                </div>
                <div class="form-group">
                    <label for="edit-ket">Polru</label>
                    <input type="text" name="polru" class="form-control"  value="{{$data->polru}}" id="polru"  placeholder="Enter Keterangan">

                </div>
                <div class="form-group">
                    <label for="edit-ket">Asal RTR</lRabel>
                    <input type="text" name="asalrtr" class="form-control"  value="{{$data->asalrtr}}" id="asalrtr"  placeholder="Enter Keterangan">

                </div>
                <div class="form-group">
                    <label for="edit-ket">FPGAB 1 </label>
                    <input type="text" name="fpgab_1" class="form-control"  value="{{$data->fpgab_1}}" id="fpgab_1"  placeholder="Enter Keterangan">

                </div>
                <div class="form-group">
                    <label for="edit-ba">BA</label>
                    <input type="text" name="ba" class="form-control"  value="{{$data->ba}}" id="ba"  placeholder="Enter Keterangan">

                </div>
                <div class="form-group">
                    <label for="edit-tipehak">Tipe Hak</label>
                    <input type="text" name="tipehak" class="form-control"  value="{{$data->tipehak}}" id="tipehak"  placeholder="Enter Keterangan">

                </div>
                <div class="form-group">
                    <label for="edit-luascea_hm">Luas CEA hm</label>
                    <input type="text" name="luascea_hm" class="form-control"  value="{{$data->luascea_hm}}" id="luascea_hm"  placeholder="Enter Keterangan">

                </div>
                <div class="form-group">
                    <label for="edit-golluas_hm">Gol Luas hm</label>
                    <input type="text" name="golluas_hm" class="form-control"  value="{{$data->golluas_hm}}" id="golluas_hm"  placeholder="Enter Keterangan">

                </div>
                <label for="edit-golluas_hm2">Gol Luas hm2</label>
                <input type="text" name="golluas_hm2" class="form-control"  value="{{$data->golluas_hm2}}" id="golluas_hm2"  placeholder="Enter Keterangan">

            </div>
            <label for="edit-hmkeluar">hm keluar</label>
        <input type="text" name="hmkeluar" class="form-control"  value="{{$data->hmkeluar}}" id="hmkeluar"  placeholder="Enter Keterangan">
    </div>
            <label for="edit-investasi">Investasi</label>
            <input type="text" name="golluas_hm" class="form-control"  value="{{$data->golluas_hm}}" id="golluas_hm"  placeholder="Enter Keterangan">
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
