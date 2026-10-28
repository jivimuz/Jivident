@extends('layout/main')

@section('content')
    <div class="content-inner mt-5 py-0">
        <div class="row">
            <div class="col-md-12 mb-3 d-flex gap-2 align-items-end">
                <div>
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" class="form-control" id="reportStart">
                </div>
                <div>
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" class="form-control" id="reportEnd">
                </div>
                <button class="btn btn-primary" id="applyFilterBtn">Terapkan</button>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Trend Kunjungan</h4>
                        <small>Jumlah pasien baru vs lama per hari, sesuai filter tanggal di atas</small>
                    </div>
                    <div class="card-body">
                        <div id="chartTrendKunjungan"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Diagnosa Terbanyak</h4>
                        <small>Top 10, sesuai filter tanggal di atas</small>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Diagnosa</th>
                                    <th class="text-end">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody id="diagnosaTerbanyakBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <div class="float-end">
                            <input type="month" class="form-control" id="payrollPeriode" style="width:150px">
                        </div>
                        <h4 class="card-title">Rekap Payroll Bulanan</h4>
                        <small>Berdasarkan periode slip gaji yang sudah dihitung</small>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive" style="max-height:300px">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Karyawan</th>
                                        <th class="text-end">Gaji Pokok</th>
                                        <th class="text-end">PPh21</th>
                                        <th class="text-end">Gaji Bersih</th>
                                    </tr>
                                </thead>
                                <tbody id="rekapPayrollBody"></tbody>
                                <tfoot>
                                    <tr class="fw-bold">
                                        <td>Total</td>
                                        <td class="text-end" id="rekapPayrollBebanPerusahaan">-</td>
                                        <td></td>
                                        <td class="text-end" id="rekapPayrollGajiBersih">-</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <small class="text-muted">Kolom "Gaji Pokok" pada baris Total menunjukkan total beban perusahaan (gaji pokok + BPJS perusahaan).</small>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Kehadiran & Cuti</h4>
                        <small>Sesuai filter tanggal di atas</small>
                    </div>
                    <div class="card-body">
                        <h6>Kehadiran (Jadwal Kerja)</h6>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Karyawan</th>
                                    <th class="text-end">Jumlah Shift</th>
                                    <th class="text-end">Total Jam</th>
                                </tr>
                            </thead>
                            <tbody id="kehadiranBody"></tbody>
                        </table>
                        <h6 class="mt-3">Cuti Disetujui</h6>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Karyawan</th>
                                    <th class="text-end">Jumlah Pengajuan</th>
                                    <th class="text-end">Total Hari</th>
                                </tr>
                            </thead>
                            <tbody id="cutiBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function formatRupiahReportKlinis(v) {
            return 'Rp ' + Number(v).toLocaleString('id-ID');
        }

        var chartTrendKunjungan;

        function loadTrendKunjungan() {
            $.ajax({
                url: "{{ url('report-klinis/getTrendKunjungan') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    start_date: $('#reportStart').val(),
                    end_date: $('#reportEnd').val()
                },
                success: function(res) {
                    var categories = res.data.map(d => d.tanggal);
                    var baru = res.data.map(d => d.baru);
                    var lama = res.data.map(d => d.lama);

                    if (chartTrendKunjungan) {
                        chartTrendKunjungan.updateOptions({
                            xaxis: {
                                categories: categories
                            }
                        });
                        chartTrendKunjungan.updateSeries([{
                            name: 'Pasien Baru',
                            data: baru
                        }, {
                            name: 'Pasien Lama',
                            data: lama
                        }]);
                    } else {
                        chartTrendKunjungan = new ApexCharts(document.querySelector("#chartTrendKunjungan"), {
                            chart: {
                                type: 'bar',
                                height: 280,
                                stacked: true,
                                toolbar: {
                                    show: false
                                }
                            },
                            series: [{
                                name: 'Pasien Baru',
                                data: baru
                            }, {
                                name: 'Pasien Lama',
                                data: lama
                            }],
                            xaxis: {
                                categories: categories
                            },
                            colors: ['#3b82f6', '#10b981']
                        });
                        chartTrendKunjungan.render();
                    }
                }
            });
        }

        function loadDiagnosaTerbanyak() {
            $.ajax({
                url: "{{ url('report-klinis/getDiagnosaTerbanyak') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    start_date: $('#reportStart').val(),
                    end_date: $('#reportEnd').val()
                },
                success: function(res) {
                    var rows = '';
                    res.data.forEach(d => rows += `<tr>
                        <td>${d.diagnosa}</td>
                        <td class="text-end">${d.jumlah}</td>
                    </tr>`);
                    $('#diagnosaTerbanyakBody').html(rows || '<tr><td colspan="2" class="text-muted text-center">Tidak ada data</td></tr>');
                }
            });
        }

        function loadRekapPayroll() {
            $.ajax({
                url: "{{ url('report-klinis/getRekapPayroll') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    periode: $('#payrollPeriode').val()
                },
                success: function(res) {
                    var rows = '';
                    res.data.forEach(d => rows += `<tr>
                        <td>${d.fullname}</td>
                        <td class="text-end">${formatRupiahReportKlinis(d.gaji_pokok)}</td>
                        <td class="text-end">${formatRupiahReportKlinis(d.pph21_nominal)}</td>
                        <td class="text-end">${formatRupiahReportKlinis(d.gaji_bersih)}</td>
                    </tr>`);
                    $('#rekapPayrollBody').html(rows || '<tr><td colspan="4" class="text-muted text-center">Belum ada slip gaji periode ini</td></tr>');
                    $('#rekapPayrollBebanPerusahaan').text(formatRupiahReportKlinis(res.total_beban_perusahaan));
                    $('#rekapPayrollGajiBersih').text(formatRupiahReportKlinis(res.total_gaji_bersih));
                }
            });
        }

        function loadKehadiranCuti() {
            $.ajax({
                url: "{{ url('report-klinis/getKehadiranCuti') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    start_date: $('#reportStart').val(),
                    end_date: $('#reportEnd').val()
                },
                success: function(res) {
                    var kehadiranRows = '';
                    res.kehadiran.forEach(d => kehadiranRows += `<tr>
                        <td>${d.fullname}</td>
                        <td class="text-end">${d.jumlah_shift}</td>
                        <td class="text-end">${d.total_jam}</td>
                    </tr>`);
                    $('#kehadiranBody').html(kehadiranRows || '<tr><td colspan="3" class="text-muted text-center">Tidak ada data</td></tr>');

                    var cutiRows = '';
                    res.cuti.forEach(d => cutiRows += `<tr>
                        <td>${d.fullname}</td>
                        <td class="text-end">${d.jumlah_pengajuan}</td>
                        <td class="text-end">${d.total_hari}</td>
                    </tr>`);
                    $('#cutiBody').html(cutiRows || '<tr><td colspan="3" class="text-muted text-center">Tidak ada data</td></tr>');
                }
            });
        }

        $('#applyFilterBtn').on('click', function() {
            loadTrendKunjungan();
            loadDiagnosaTerbanyak();
            loadKehadiranCuti();
        });

        $('#payrollPeriode').on('change', loadRekapPayroll);

        $(document).ready(function() {
            var firstDayOfMonth = new Date();
            firstDayOfMonth.setDate(1);
            $('#reportStart').val(firstDayOfMonth.toISOString().slice(0, 10));
            $('#reportEnd').val(new Date().toISOString().slice(0, 10));
            $('#payrollPeriode').val(new Date().toISOString().slice(0, 7));

            loadTrendKunjungan();
            loadDiagnosaTerbanyak();
            loadRekapPayroll();
            loadKehadiranCuti();
        });
    </script>
@endsection
