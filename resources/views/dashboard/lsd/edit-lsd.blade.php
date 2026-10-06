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





<div class="modal fade" id="edit-lsd--{{$data->id}}" tabindex="-1" role="dialog" aria-labelledby="editLsdModalLabel-{{$data->id}}"
aria-hidden="true">
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="editLsdModalLabel-{{$data->id}}">Edit Data LSD</h5>
            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <form action="{{ route('update-lsd', $data->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="edit-lsd-geometri_id-{{$data->id}}">Geometri ID <span class="text-danger">*</span></label>
                    <input type="text" name="geometri_id" value="{{ $data->geometri_id }}" class="form-control" id="edit-lsd-geometri_id-{{$data->id}}" readonly required>
                    <small class="text-muted">Geometri ID terhubung ke data spasial.</small>
                </div>

                <div class="form-group">
                    <label for="edit-lsd-desa_id-{{$data->id}}">Desa / Wilayah Terkait</label>
                    <select name="desa_id" id="edit-lsd-desa_id-{{$data->id}}" class="form-control">
                        <option value="">-- Pilih Desa --</option>
                        @if(isset($desas))
                            @foreach($desas as $desaItem)
                                <option value="{{ $desaItem->id }}" {{ (optional($data->geometri)->desa_id == $desaItem->id) ? 'selected' : '' }}>
                                    {{ $desaItem->nama }} (Kec. {{ optional($desaItem->kecamatan)->nama ?? '-' }})
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="form-group">
                    <label for="edit-lsd-hutan-{{$data->id}}">Status Hutan <span class="text-danger">*</span></label>
                    <input type="text" name="hutan" value="{{ $data->hutan }}" class="form-control" id="edit-lsd-hutan-{{$data->id}}" placeholder="Masukkan Status Hutan" required>
                </div>

                <div class="form-group">
                    <label for="edit-lsd-luas-{{$data->id}}">Luas Lahan <span class="text-danger">*</span></label>
                    <input type="text" name="luas" value="{{ $data->luas }}" class="form-control" id="edit-lsd-luas-{{$data->id}}" placeholder="Masukkan Luas" required>
                </div>

                <div class="form-group">
                    <label for="edit-lsd-ba-{{$data->id}}">Berita Acara (BA) <span class="text-danger">*</span></label>
                    <input type="text" name="ba" value="{{ $data->ba }}" class="form-control" id="edit-lsd-ba-{{$data->id}}" placeholder="Masukkan BA" required>
                </div>

                <div class="form-group">
                    <label for="edit-lsd-luascea_hm-{{$data->id}}">Luas CEA HM (Opsional)</label>
                    <input type="text" name="luascea_hm" value="{{ $data->luascea_hm }}" class="form-control" id="edit-lsd-luascea_hm-{{$data->id}}" placeholder="Masukkan Luas CEA HM">
                </div>

                <div class="form-group">
                    <label for="edit-lsd-koordinat-{{$data->id}}">Koordinat (Opsional)</label>
                    @php
                        $koordinatValue = '';
                        if($data->geometri && $data->geometri->koordinat_geojson) {
                            $parsed = json_decode($data->geometri->koordinat_geojson, true);
                            if(isset($parsed['coordinates'])) {
                                if (isset($parsed['type']) && $parsed['type'] === 'Point') {
                                    $koordinatValue = implode(', ', $parsed['coordinates']);
                                } else {
                                    $koordinatValue = json_encode($parsed['coordinates']);
                                }
                            }
                        }
                    @endphp
                    <textarea name="koordinat" class="form-control" id="edit-lsd-koordinat-{{$data->id}}" rows="3" placeholder="Contoh: 114.36, -8.21">{{ $koordinatValue }}</textarea>
                    <small class="text-muted">Masukkan koordinat Lng, Lat (cth: 114.36, -8.21) atau GeoJSON array untuk poligon.</small>
                </div>

                <div class="modal-footer px-0 pb-0">
                    <button type="button" class="btn btn-silila-outline" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-silila-sunrise">
                        <i class="material-icons mr-1" style="font-size: 16px;">check_circle</i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
