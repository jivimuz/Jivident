<div class="form-group border-b">
    <label for="typeodontogram" class="form-label"><strong>Tipe Odontogram</strong></label>
    <div class="input-group">
        <select class="form-control" id="typeodontogram" name="typeodontogram" onchange="changeOdonto()">
            <option value="-" {{ !$odontogram || $odontogram->typeodontogram == '-' ? 'selected' : '' }}>
                Pilih salah satu
            </option>
            <option value="3D Dewasa (32 Gigi)"
                {{ $odontogram && $odontogram->typeodontogram == '3D Dewasa (32 Gigi)' ? 'selected' : '' }}>
                3D Dewasa (32 Gigi)
            </option>
            <option value="3D Remaja (24 Gigi)"
                {{ $odontogram && $odontogram->typeodontogram == '3D Remaja (24 Gigi)' ? 'selected' : '' }}>
                3D Remaja (24 Gigi)
            </option>
            <option value="3D Anak (20 Gigi)"
                {{ $odontogram && $odontogram->typeodontogram == '3D Anak (20 Gigi)' ? 'selected' : '' }}>
                3D Anak (20 Gigi)
            </option>
            <option value="2D Global" {{ $odontogram && $odontogram->typeodontogram == '2D Global' ? 'selected' : '' }}>
                2D Global
            </option>
        </select>

    </div>
    <br>
    <small>*Pilih salah satu untuk di laporan </small>
</div>
<br>
<div id="tab-odonto"></div>

<script>
    $(document).ready(function() {
        changeOdonto()
    })
    $('#typeodontogram').select2()

    function changeOdonto() {
        const odontotype = $('#typeodontogram').val();
        const url = "<?= url('pemeriksaan/pasien') ?>/" + id + '/odontogram/field';

        $("#tab-odonto").html('<div class="p-3 text-center text-muted">Loading...</div>');

        $.ajax({
            url: url,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {
                odontotype: odontotype,
            },
            success: function(res) {
                $("#tab-odonto").html(res);
            },
            error: function() {
                $("#tab-odonto").html(
                    '<div class="p-3 text-danger">Gagal memuat konten.</div>');
            }
        });

    };
</script>
