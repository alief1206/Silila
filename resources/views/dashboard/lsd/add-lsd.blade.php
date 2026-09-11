<div class="modal fade" id="add-lsd" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel"
aria-hidden="true">
<div class="modal-dialog modal-dialog-scrollable" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="editUserModalLabel">Tambah Data LP2B</h5>
            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
           <form action="{{ route('add-lsd') }}" class="needs-validation" novalidate="" method="POST" enctype="multipart/form-data">
            @csrf
            @method('POST')
            <div class="form-group">
                <label for="edit-geometri_id">Geometri ID</label>
                <input type="text" name="geometri_id" class="form-control"  placeholder="Masukkan ID Geometri">
            </div>
            <div class="form-group">
                <label for="edit-lsd">LSD</label>
                <input type="text" name="lsd" class="form-control"  placeholder="Masukkan LSD">
            </div>
            <div class="form-group">
                <label for="edit-hutan">Hutan</label>
                <input type="text" name="hutan" class="form-control"  placeholder="Masukkan Data Hutan">
            </div>
            <div class="form-group">
                <label for="edit-luas">Luas</label>
                <input type="text" name="luas" class="form-control"  placeholder="Masukkan Luas">
            </div>
            <div class="form-group">
                <label for="edit-ket">Keterangan</label>
                <input type="text" name="ket" class="form-control"  placeholder="Masukkan Keterangan">
            </div>
            <div class="form-group">
                <label for="edit-irigasi_pr">Irigasi PR</label>
                <input type="text" name="irigasi_pr" class="form-control"  placeholder="Masukkan Irigasi PR">
            </div>
            <div class="form-group">
                <label for="edit-kewenangan">Kewenangan</label>
                <input type="text" name="kewenangan" class="form-control"  placeholder="Masukkan Kewenangan">
            </div>
            <div class="form-group">
                <label for="edit-ip">IP</label>
                <input type="text" name="ip" class="form-control"  placeholder="Masukkan IP">
            </div>
            <div class="form-group">
                <label for="edit-prod">Prod</label>
                <input type="text" name="prod" class="form-control"  placeholder="Masukkan Prod">
            </div>
            <div class="form-group">
                <label for="edit-irigasi">Irigasi</label>
                <input type="text" name="irigasi" class="form-control"  placeholder="Masukkan Irigasi">
            </div>
            <div class="form-group">
                <label for="edit-kondisigab">Kondisi Gab</label>
                <input type="text" name="kondisigab" class="form-control"  placeholder="Masukkan Kondisi Gab">
            </div>
            <div class="form-group">
                <label for="edit-kontamgab">Kontam Gab</label>
                <input type="text" name="kontamgab" class="form-control"  placeholder="Masukkan Kontam Gab">
            </div>
            <div class="form-group">
                <label for="edit-polru">Polru</label>
                <input type="text" name="polru" class="form-control"  placeholder="Masukkan Polru">
            </div>
            <div class="form-group">
                <label for="edit-asalrtr">Asal RTR</label>
                <input type="text" name="asalrtr" class="form-control"  placeholder="Masukkan Asal RTR">
            </div>
            <div class="form-group">
                <label for="edit-fpgab_1">FPGAB 1</label>
                <input type="text" name="fpgab_1" class="form-control"  placeholder="Masukkan FPGAB 1">
            </div>
            <div class="form-group">
                <label for="edit-ba">BA</label>
                <input type="text" name="ba" class="form-control"  placeholder="Masukkan BA">
            </div>
            <div class="form-group">
                <label for="edit-tipehak">Tipe Hak</label>
                <input type="text" name="tipehak" class="form-control"  placeholder="Masukkan Tipe Hak">
            </div>
            <div class="form-group">
                <label for="edit-luascea_hm">Luas CEA HM</label>
                <input type="text" name="luascea_hm" class="form-control"  placeholder="Masukkan Luas CEA HM">
            </div>
            <div class="form-group">
                <label for="edit-golluas_hm">Gol Luas HM</label>
                <input type="text" name="golluas_hm" class="form-control"  placeholder="Masukkan Gol Luas HM">
            </div>
            <div class="form-group">
                <label for="edit-golluas_hm2">Gol Luas HM2</label>
                <input type="text" name="golluas_hm2" class="form-control"  placeholder="Masukkan Gol Luas HM2">
            </div>
            <div class="form-group">
                <label for="edit-hmkeluar">HM Keluar</label>
                <input type="text" name="hmkeluar" class="form-control"  placeholder="Masukkan HM Keluar">
            </div>
            <div class="form-group">
                <label for="edit-investasi">Investasi</label>
                <input type="text" name="investasi" class="form-control"  placeholder="Masukkan Investasi">
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
