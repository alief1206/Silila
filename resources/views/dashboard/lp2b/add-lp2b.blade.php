<div class="modal fade" id="add-lp2b" tabindex="-1" role="dialog" aria-labelledby="addLp2bModalLabel"
aria-hidden="true">
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="addLp2bModalLabel">Tambah Data LP2B</h5>
            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
           <form action="{{ route('add-lp2b') }}" class="needs-validation" novalidate="" method="POST" enctype="multipart/form-data">
            @csrf
            @method('POST')

            <div class="form-group">
                <label for="add-desa_id">Pilih Desa / Wilayah</label>
                <select name="desa_id" id="add-desa_id" class="form-control select2">
                    <option value="">-- Pilih Desa Terkait (Opsional) --</option>
                    @if(isset($desas))
                        @foreach($desas as $desaItem)
                            <option value="{{ $desaItem->id }}">{{ $desaItem->nama }} (Kec. {{ optional($desaItem->kecamatan)->nama ?? '-' }})</option>
                        @endforeach
                    @endif
                </select>
                <small class="text-muted">Desa ini akan dikaitkan ke data spasial LP2B.</small>
            </div>

            <div class="form-group">
                <label for="add-geometri_id">ID Geometri (Opsional)</label>
                <input type="number" name="geometri_id" id="add-geometri_id" class="form-control" placeholder="Kosongkan untuk otomatis generate ID Geometri baru">
                <small class="text-muted">Jika Anda memiliki ID Geometri tertentu, masukkan di sini.</small>
            </div>

            <div class="form-group">
                <label for="add-kp2b">Status / Kategori KP2B <span class="text-danger">*</span></label>
                <input type="text" name="kp2b" id="add-kp2b" class="form-control" placeholder="Contoh: Kawasan Pertanian Pangan Berkelanjutan" required>
            </div>

            <div class="form-group">
                <label for="add-luas">Luas Lahan <span class="text-danger">*</span></label>
                <input type="text" name="luas" id="add-luas" class="form-control" placeholder="Contoh: 12.5 ha atau 125000" required>
            </div>

            <div class="form-group">
                <label for="add-ket">Keterangan</label>
                <textarea name="ket" id="add-ket" class="form-control" rows="2" placeholder="Catatan atau keterangan lahan"></textarea>
            </div>

            <div class="form-group">
                <label for="add-koordinat">Koordinat (Opsional)</label>
                <textarea name="koordinat" id="add-koordinat" class="form-control" rows="3" placeholder="Contoh: 114.36, -8.21"></textarea>
                <small class="text-muted">Masukkan koordinat Lng, Lat (cth: 114.36, -8.21) atau GeoJSON array untuk poligon.</small>
            </div>

            <div class="modal-footer px-0 pb-0">
                <button type="button" class="btn btn-silila-outline" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-silila-emerald">
                    <i class="material-icons mr-1" style="font-size: 16px;">save</i> Simpan Data LP2B
                </button>
            </div>
           </form>
        </div>
    </div>
</div>
</div>
