<form class="row mt-4" id="vitalsign-form">
    @csrf

    <div class="row">
        <div class="col-lg-3">
            <label class="form-label">Tekanan Darah Siastole</label>
            <input type="text" name="siastole" class="form-control" placeholder="Siastole" maxlength="10"
                value="{{ $vital_sign->siastole ?? '' }}" required>
        </div>

        <div class="col-lg-3">
            <label class="form-label">Tekanan Darah Diastole</label>
            <input type="text" name="diastole" class="form-control" placeholder="Diastole"maxlength="10"
                value="{{ $vital_sign->diastole ?? '' }}" required>
        </div>

        <div class="col-lg-3">
            <label class="form-label">Tinggi Badan</label>
            <input type="text" name="tinggibadan" class="form-control" placeholder="Tinggi Badan"maxlength="10"
                value="{{ $vital_sign->tinggibadan ?? '' }}" required>
        </div>

        <div class="col-lg-3">
            <label class="form-label">Berat Badan</label>
            <input type="text" name="beratbadan" class="form-control" placeholder="Berat Badan"maxlength="10"
                value="{{ $vital_sign->beratbadan ?? '' }}" required>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-lg-3">
            <label class="form-label">Suhu</label>
            <input type="text" name="suhu" class="form-control" placeholder="Suhu"maxlength="10"
                value="{{ $vital_sign->suhu ?? '37' }}" required>
        </div>

        <div class="col-lg-3">
            <label class="form-label">Nadi</label>
            <input type="text" name="nadi" class="form-control" placeholder="Nadi"maxlength="10"
                value="{{ $vital_sign->nadi ?? '' }}" required>
        </div>

        <div class="col-lg-3">
            <label class="form-label">Pernapasan</label>
            <input type="text" name="pernapasan" class="form-control" placeholder="Pernapasan"maxlength="10"
                value="{{ $vital_sign->pernapasan ?? '' }}" required>
        </div>

        <div class="col-lg-3">
            <label class="form-label">Lingkar Perut</label>
            <input type="text" name="lingkarperut" class="form-control" placeholder="Lingkar Perut"maxlength="10"
                value="{{ $vital_sign->lingkarperut ?? '' }}" required>
        </div>
    </div>
    <div class="d-flex justify-content-end mt-2">
        <button type="submit" class="btn btn-primary">
            <i class="fa fa-save"></i>
            {{ $vital_sign ? 'Update' : 'Save' }}
        </button>
    </div>
</form>

<script>
    $(function() {
        $('#vitalsign-form').on('submit', function(e) {
            e.preventDefault();
            let form = $(this);
            $.ajax({
                url: "<?= url('pemeriksaan/pasien/' . $id) ?>/vitalsign/save",
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    Toast.fire({
                        icon: "success",
                        title: "Data berhasil disimpan"
                    });
                },
                error: function(xhr) {
                    Swal.close();
                    const msg = xhr.responseJSON?.error || 'Terjadi kesalahan.';
                    Toast.fire({
                        icon: "error",
                        title: msg
                    });
                },
            });
        });
    });
</script>
