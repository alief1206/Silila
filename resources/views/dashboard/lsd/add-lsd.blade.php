<div class="modal fade" id="add-lsd" tabindex="-1" role="dialog" aria-labelledby="addLsdModalLabel"
aria-hidden="true">
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="addLsdModalLabel">Tambah Data LSD</h5>
            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
           <form action="{{ route('add-lsd') }}" class="needs-validation" novalidate="" method="POST" enctype="multipart/form-data">
            @csrf
            @method('POST')

            <div class="form-group">
                <label for="add-lsd-desa_id">Pilih Desa / Wilayah Terkait <span class="text-danger">*</span></label>
                <select name="desa_id" id="add-lsd-desa_id" class="form-control select2" required>
                    <option value="">-- Pilih Desa Terkait --</option>
                    @if(isset($desas))
                        @foreach($desas as $desaItem)
                            <option value="{{ $desaItem->id }}">{{ $desaItem->nama }} (Kec. {{ optional($desaItem->kecamatan)->nama ?? '-' }})</option>
                        @endforeach
                    @endif
                </select>
                <small class="text-muted">Desa ini akan dikaitkan ke data spasial LSD.</small>
                <div class="invalid-feedback">Pilih desa terkait.</div>
            </div>

            <div class="form-group">
                <label for="add-lsd-geometri_id">ID Geometri <span class="text-muted" style="font-size:12px;">(Opsional)</span></label>
                <input type="number" name="geometri_id" id="add-lsd-geometri_id" class="form-control" placeholder="Kosongkan untuk otomatis generate ID Geometri baru">
                <small class="text-muted">Masukkan jika sudah ada ID Geometri spasial khusus.</small>
            </div>

            <div class="form-group">
                <label for="add-lsd-hutan">Kategori / Status Hutan <span class="text-danger">*</span></label>
                <input type="text" name="hutan" id="add-lsd-hutan" class="form-control" placeholder="Contoh: Bukan Kawasan Hutan / Hutan Lindung" required>
                <div class="invalid-feedback">Kategori / Status Hutan wajib diisi.</div>
            </div>

            <div class="form-group">
                <label for="add-lsd-luas">Luas Lahan <span class="text-danger">*</span></label>
                <input type="text" name="luas" id="add-lsd-luas" class="form-control" placeholder="Contoh: 25.4 ha" required>
                <div class="invalid-feedback">Luas lahan wajib diisi.</div>
            </div>

            <div class="form-group">
                <label for="add-lsd-ba">Nomor / Keterangan Berita Acara (BA) <span class="text-danger">*</span></label>
                <input type="text" name="ba" id="add-lsd-ba" class="form-control" placeholder="Contoh: BA/12/VI/2024" required>
                <div class="invalid-feedback">Nomor Berita Acara wajib diisi.</div>
            </div>

            <div class="form-group">
                <label for="add-lsd-luascea_hm">Luas CEA HM <span class="text-danger">*</span></label>
                <input type="text" name="luascea_hm" id="add-lsd-luascea_hm" class="form-control" placeholder="Masukkan Luas CEA HM" required>
                <div class="invalid-feedback">Luas CEA HM wajib diisi.</div>
            </div>

            <div class="form-group">
                <label for="add-lsd-koordinat">Koordinat <span class="text-danger">*</span></label>
                <textarea name="koordinat" id="add-lsd-koordinat" class="form-control" rows="3" placeholder="Contoh: 114.36, -8.21" required></textarea>
                <small class="text-muted">Masukkan koordinat Lng, Lat (cth: 114.36, -8.21) atau GeoJSON array untuk poligon.</small>
                <div class="invalid-feedback">Koordinat wajib diisi.</div>
            </div>

            <div class="modal-footer px-0 pb-0">
                <button type="button" class="btn btn-silila-outline" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-silila-sunrise">
                    <i class="material-icons mr-1" style="font-size: 16px;">save</i> Simpan Data LSD
                </button>
            </div>
           </form>
        </div>
    </div>
</div>
</div>
