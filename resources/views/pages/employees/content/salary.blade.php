<div class="col-md-5">
    <div class="form-group mb-3">
        <label class="form-label">Gaji Pokok</label>
        <div class="input-group">
            <span class="input-group-text">Rp</span>
            <input type="number" class="form-control" id="salGajiPokok" value="{{ $data->gajipokok ?? 0 }}">
            <button class="btn btn-success" id="saveSalaryBtn">Simpan</button>
        </div>
    </div>

    <hr>

    <h6>Kalkulator Slip Gaji</h6>
    <small class="text-muted d-block mb-2">
        PPh21 pakai metode TER (PMK 168/2023) berdasarkan kategori PTKP di tab Statutory.
        BPJS pakai tarif dari menu Setting. <b>Verifikasi dulu tarif TER & BPJS sebelum dipakai ke payroll
            sungguhan.</b>
    </small>
    <div class="form-group mb-3">
        <label class="form-label">Periode</label>
        <input type="month" class="form-control" id="payrollPeriode">
    </div>
    <button class="btn btn-primary" id="calculatePayrollBtn">Hitung</button>
    <button class="btn btn-outline-success" id="saveSlipBtn" disabled>Simpan sebagai Slip</button>

    <div id="payrollResult" class="mt-3" style="display:none">
        <table class="table table-sm">
            <tr>
                <td>Gaji Pokok</td>
                <td class="text-end" id="resGajiPokok"></td>
            </tr>
            <tr>
                <td>BPJS Kesehatan (karyawan)</td>
                <td class="text-end" id="resBpjsKesehatan"></td>
            </tr>
            <tr>
                <td>BPJS JHT (karyawan)</td>
                <td class="text-end" id="resBpjsJht"></td>
            </tr>
            <tr>
                <td>BPJS JP (karyawan)</td>
                <td class="text-end" id="resBpjsJp"></td>
            </tr>
            <tr>
                <td>PPh21 (<span id="resTerInfo"></span>)</td>
                <td class="text-end" id="resPph21"></td>
            </tr>
            <tr class="fw-bold">
                <td>Gaji Bersih (Take Home Pay)</td>
                <td class="text-end" id="resGajiBersih"></td>
            </tr>
        </table>
        <div id="resAlreadyExists" class="alert alert-warning" style="display:none">
            Slip untuk periode ini sudah pernah disimpan.
        </div>
    </div>
</div>

<div class="col-md-7">
    <h6>Riwayat Slip Gaji</h6>
    <table class="table table-sm" id="slipHistoryTable">
        <thead>
            <tr>
                <th>Periode</th>
                <th>Gaji Pokok</th>
                <th>PPh21</th>
                <th>Gaji Bersih</th>
            </tr>
        </thead>
        <tbody id="slipHistoryBody"></tbody>
    </table>
</div>

<script>
    function formatRupiahSalary(v) {
        return 'Rp ' + Number(v).toLocaleString('id-ID');
    }

    $('#saveSalaryBtn').off('click').on('click', function() {
        $.ajax({
            url: "<?= url('employees/' . $idn) ?>/salary/save",
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {
                gajipokok: $('#salGajiPokok').val()
            },
            success: function() {
                Toast.fire({
                    icon: 'success',
                    title: 'Gaji pokok disimpan'
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

    $('#calculatePayrollBtn').off('click').on('click', function() {
        var periode = $('#payrollPeriode').val();
        if (!periode) {
            return Toast.fire({
                icon: 'warning',
                title: 'Pilih periode dulu'
            });
        }
        $.ajax({
            url: "<?= url('employees/' . $idn) ?>/salary/calculate",
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {
                periode: periode,
                gajipokok: $('#salGajiPokok').val()
            },
            success: function(res) {
                var d = res.data;
                $('#resGajiPokok').text(formatRupiahSalary(d.gaji_pokok));
                $('#resBpjsKesehatan').text(formatRupiahSalary(d.bpjs_kesehatan_employee));
                $('#resBpjsJht').text(formatRupiahSalary(d.bpjs_jht_employee));
                $('#resBpjsJp').text(formatRupiahSalary(d.bpjs_jp_employee));
                $('#resTerInfo').text('TER ' + d.ter_kategori + ', ' + d.pph21_persen + '%');
                $('#resPph21').text(formatRupiahSalary(d.pph21_nominal));
                $('#resGajiBersih').text(formatRupiahSalary(d.gaji_bersih));
                $('#resAlreadyExists').toggle(!!d.already_exists);
                $('#payrollResult').show();
                $('#saveSlipBtn').prop('disabled', !!d.already_exists);
            },
            error: function(xhr) {
                $('#payrollResult').hide();
                Toast.fire({
                    icon: 'error',
                    title: JSON.parse(xhr.responseText).error ?? 'Gagal menghitung'
                });
            }
        });
    });

    $('#saveSlipBtn').off('click').on('click', function() {
        var periode = $('#payrollPeriode').val();
        $.ajax({
            url: "<?= url('employees/' . $idn) ?>/salary/generateSlip",
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {
                periode: periode,
                gajipokok: $('#salGajiPokok').val()
            },
            success: function() {
                Toast.fire({
                    icon: 'success',
                    title: 'Slip gaji disimpan'
                });
                $('#saveSlipBtn').prop('disabled', true);
                loadSlipHistory();
            },
            error: function(xhr) {
                Toast.fire({
                    icon: 'error',
                    title: JSON.parse(xhr.responseText).error ?? 'Gagal menyimpan slip'
                });
            }
        });
    });

    function loadSlipHistory() {
        $.ajax({
            url: "<?= url('employees/' . $idn) ?>/salary/getSlips",
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(res) {
                var rows = '';
                res.data.forEach(function(s) {
                    rows += `<tr>
                        <td>${s.periode}</td>
                        <td>${formatRupiahSalary(s.gaji_pokok)}</td>
                        <td>${formatRupiahSalary(s.pph21_nominal)}</td>
                        <td>${formatRupiahSalary(s.gaji_bersih)}</td>
                    </tr>`;
                });
                $('#slipHistoryBody').html(rows || '<tr><td colspan="4" class="text-muted text-center">Belum ada slip</td></tr>');
            }
        });
    }

    loadSlipHistory();
</script>
