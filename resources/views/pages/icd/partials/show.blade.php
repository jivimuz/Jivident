<div class="modal-body p-3">
    <div class="row">
        <div class="col-md-6 mb-2">
            <label class="form-label fw-bold">Tipe</label>
            <div>{{ $tipe == 9 ? 'ICD IX' : 'ICD X' }}</div>
        </div>
        <div class="col-md-6 mb-2">
            <label class="form-label fw-bold">Kode</label>
            <div>{{ $row->code }}</div>
        </div>
        <div class="col-md-12 mb-2">
            <label class="form-label fw-bold">Nama</label>
            <div>{{ $row->name }}</div>
        </div>

        <div class="col-md-3 mb-2">
            <label class="form-label fw-bold">Tarif</label>
            <div>{{ number_format((float) $row->tarif, 2, ',', '.') }}</div>
        </div>

    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-light" onclick="tutup()">Tutup</button>
</div>
