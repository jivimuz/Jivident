<div class="col-md-6">
    <div class="form-group mb-3">
        <label class="form-label">NPWP</label>
        <input type="text" class="form-control" id="statNpwp" value="{{ $data->npwp ?? '' }}">
    </div>
    <div class="form-group mb-3">
        <label class="form-label">Kategori PTKP</label>
        <select class="form-control" id="statPtkp">
            <option value="">- Pilih PTKP -</option>
            @foreach ($ptkp as $p)
                <option value="{{ $p->id }}" {{ ($data->fk_ptkp ?? null) == $p->id ? 'selected' : '' }}>
                    {{ $p->name }} (Rp {{ number_format($p->nominal, 0, ',', '.') }}/tahun)
                    @if ($p->ter_kategori)
                        — TER {{ $p->ter_kategori }}
                    @endif
                </option>
            @endforeach
        </select>
        <small class="text-muted">Kategori PTKP menentukan tabel TER yang dipakai untuk hitung PPh21 di tab
            Salary.</small>
    </div>
    <div class="form-group mb-3">
        <label class="form-label">No. BPJS Kesehatan</label>
        <input type="text" class="form-control" id="statBpjsKesehatan"
            value="{{ $data->bpjs_kesehatan_no ?? '' }}">
    </div>
    <div class="form-group mb-3">
        <label class="form-label">No. BPJS Ketenagakerjaan</label>
        <input type="text" class="form-control" id="statBpjsTk" value="{{ $data->bpjs_tk_no ?? '' }}">
    </div>
    <button class="btn btn-success" id="saveStatutoryBtn">Simpan</button>
</div>

<script>
    $('#saveStatutoryBtn').off('click').on('click', function() {
        $.ajax({
            url: "<?= url('employees/' . $idn) ?>/statutory/save",
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {
                fk_ptkp: $('#statPtkp').val(),
                npwp: $('#statNpwp').val(),
                bpjs_kesehatan_no: $('#statBpjsKesehatan').val(),
                bpjs_tk_no: $('#statBpjsTk').val(),
            },
            success: function() {
                Toast.fire({
                    icon: 'success',
                    title: 'Data statutory disimpan'
                });
            },
            error: function(xhr) {
                Toast.fire({
                    icon: 'error',
                    title: JSON.parse(xhr.responseText).error ?? 'Gagal menyimpan'
                });
            }
        });
    });
</script>
