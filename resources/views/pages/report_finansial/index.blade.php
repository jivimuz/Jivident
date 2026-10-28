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
                        <h4 class="card-title">Laba-Rugi Sederhana</h4>
                        <small>Periode sesuai filter tanggal di atas, dikelompokkan per jenis transaksi</small>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Jenis</th>
                                    <th class="text-end">Masuk</th>
                                    <th class="text-end">Keluar</th>
                                </tr>
                            </thead>
                            <tbody id="labaRugiBody"></tbody>
                            <tfoot>
                                <tr class="fw-bold">
                                    <td>Total</td>
                                    <td class="text-end" id="labaRugiTotalIn">-</td>
                                    <td class="text-end" id="labaRugiTotalOut">-</td>
                                </tr>
                                <tr class="fw-bold">
                                    <td colspan="2">Laba Bersih</td>
                                    <td class="text-end" id="labaRugiBersih">-</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Rekap Pembayaran per Metode</h4>
                        <small>Pembayaran pasien (billing) sesuai filter tanggal di atas</small>
                    </div>
                    <div class="card-body">
                        <div id="chartRekapPembayaran"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Laporan Piutang</h4>
                        <small>Pasien dengan status pembayaran belum lunas, sesuai filter tanggal registrasi di atas</small>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive" style="max-height:350px">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>No Registrasi</th>
                                        <th>Pasien</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                        <th class="text-end">Sisa</th>
                                    </tr>
                                </thead>
                                <tbody id="piutangBody"></tbody>
                                <tfoot>
                                    <tr class="fw-bold">
                                        <td colspan="4">Total Piutang</td>
                                        <td class="text-end" id="piutangTotal">-</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Pembelian per Supplier</h4>
                        <small>Berdasarkan tanggal faktur, sesuai filter tanggal di atas</small>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Supplier</th>
                                    <th class="text-end">Jumlah Faktur</th>
                                    <th class="text-end">Total Pembelian</th>
                                </tr>
                            </thead>
                            <tbody id="pembelianSupplierBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <div class="float-end">
                            <select class="form-control" id="kartuStokProduct" style="width:220px"></select>
                        </div>
                        <h4 class="card-title">Kartu Stok</h4>
                        <small>Pilih produk, mengikuti filter tanggal di atas</small>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">Saldo Awal: <strong id="kartuStokSaldoAwal">0</strong></div>
                        <div class="table-responsive" style="max-height:300px">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Sumber</th>
                                        <th>Tipe</th>
                                        <th class="text-end">Qty</th>
                                        <th class="text-end">Saldo</th>
                                    </tr>
                                </thead>
                                <tbody id="kartuStokBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Stok Opname (Nilai Stok Saat Ini)</h4>
                        <small>Tidak terpengaruh filter tanggal — snapshot stok sekarang</small>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive" style="max-height:300px">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th class="text-end">Stok</th>
                                        <th class="text-end">HPP</th>
                                        <th class="text-end">Nilai Stok</th>
                                    </tr>
                                </thead>
                                <tbody id="stokOpnameBody"></tbody>
                                <tfoot>
                                    <tr class="fw-bold">
                                        <td colspan="3">Total Nilai Stok</td>
                                        <td class="text-end" id="stokOpnameTotal">-</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function formatRupiahReportFinansial(v) {
            return 'Rp ' + Number(v).toLocaleString('id-ID');
        }

        var chartRekapPembayaran;

        function loadLabaRugi() {
            $.ajax({
                url: "{{ url('report-finansial/getLabaRugi') }}",
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
                        <td>${d.label}</td>
                        <td class="text-end">${formatRupiahReportFinansial(d.in)}</td>
                        <td class="text-end">${formatRupiahReportFinansial(d.out)}</td>
                    </tr>`);
                    $('#labaRugiBody').html(rows || '<tr><td colspan="3" class="text-muted text-center">Tidak ada data</td></tr>');
                    $('#labaRugiTotalIn').text(formatRupiahReportFinansial(res.total_in));
                    $('#labaRugiTotalOut').text(formatRupiahReportFinansial(res.total_out));
                    $('#labaRugiBersih').text(formatRupiahReportFinansial(res.laba_bersih));
                }
            });
        }

        function loadRekapPembayaran() {
            $.ajax({
                url: "{{ url('report-finansial/getRekapPembayaran') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    start_date: $('#reportStart').val(),
                    end_date: $('#reportEnd').val()
                },
                success: function(res) {
                    var labels = res.data.map(d => d.metode);
                    var values = res.data.map(d => d.total);

                    if (chartRekapPembayaran) {
                        chartRekapPembayaran.updateOptions({
                            labels: labels
                        });
                        chartRekapPembayaran.updateSeries(values);
                    } else {
                        chartRekapPembayaran = new ApexCharts(document.querySelector("#chartRekapPembayaran"), {
                            chart: {
                                type: 'donut',
                                height: 280
                            },
                            labels: labels,
                            series: values,
                            legend: {
                                position: 'bottom'
                            },
                            tooltip: {
                                y: {
                                    formatter: formatRupiahReportFinansial
                                }
                            }
                        });
                        chartRekapPembayaran.render();
                    }
                }
            });
        }

        function loadPiutang() {
            $.ajax({
                url: "{{ url('report-finansial/getPiutang') }}",
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
                        <td>${d.noregistrasi}</td>
                        <td>${d.pasien_nama ?? '-'}</td>
                        <td>${d.tglregistrasi}</td>
                        <td>${d.statuspembayaran}</td>
                        <td class="text-end">${formatRupiahReportFinansial(d.sisa)}</td>
                    </tr>`);
                    $('#piutangBody').html(rows || '<tr><td colspan="5" class="text-muted text-center">Tidak ada piutang</td></tr>');
                    $('#piutangTotal').text(formatRupiahReportFinansial(res.total_piutang));
                }
            });
        }

        function loadPembelianSupplier() {
            $.ajax({
                url: "{{ url('report-finansial/getPembelianSupplier') }}",
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
                        <td>${d.supplier}</td>
                        <td class="text-end">${d.jumlah_faktur}</td>
                        <td class="text-end">${formatRupiahReportFinansial(d.total_pembelian)}</td>
                    </tr>`);
                    $('#pembelianSupplierBody').html(rows || '<tr><td colspan="3" class="text-muted text-center">Tidak ada data</td></tr>');
                }
            });
        }

        function loadKartuStok() {
            var productId = $('#kartuStokProduct').val();
            if (!productId) {
                $('#kartuStokBody').html('<tr><td colspan="5" class="text-muted text-center">Pilih produk dulu</td></tr>');
                $('#kartuStokSaldoAwal').text('0');
                return;
            }
            $.ajax({
                url: "{{ url('report-finansial/getKartuStok') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    product_id: productId,
                    start_date: $('#reportStart').val(),
                    end_date: $('#reportEnd').val()
                },
                success: function(res) {
                    $('#kartuStokSaldoAwal').text(res.saldo_awal);
                    var rows = '';
                    res.data.forEach(d => rows += `<tr>
                        <td>${d.tanggal}</td>
                        <td>${d.source}</td>
                        <td>${d.type}</td>
                        <td class="text-end">${d.qty}</td>
                        <td class="text-end">${d.saldo}</td>
                    </tr>`);
                    $('#kartuStokBody').html(rows || '<tr><td colspan="5" class="text-muted text-center">Tidak ada mutasi</td></tr>');
                }
            });
        }

        function loadStokOpname() {
            $.ajax({
                url: "{{ url('report-finansial/getStokOpname') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(res) {
                    var rows = '';
                    res.data.forEach(d => rows += `<tr>
                        <td>${d.name}</td>
                        <td class="text-end">${d.stok}</td>
                        <td class="text-end">${formatRupiahReportFinansial(d.hpp)}</td>
                        <td class="text-end">${formatRupiahReportFinansial(d.nilai_stok)}</td>
                    </tr>`);
                    $('#stokOpnameBody').html(rows || '<tr><td colspan="4" class="text-muted text-center">Tidak ada produk</td></tr>');
                    $('#stokOpnameTotal').text(formatRupiahReportFinansial(res.total_nilai_stok));
                }
            });
        }

        function loadProductOptions() {
            $.ajax({
                url: "{{ url('report-finansial/getProductOptions') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(res) {
                    var options = '<option value="">- Pilih Produk -</option>';
                    res.data.forEach(p => options += `<option value="${p.id}">${p.name}</option>`);
                    $('#kartuStokProduct').html(options);
                }
            });
        }

        $('#applyFilterBtn').on('click', function() {
            loadLabaRugi();
            loadRekapPembayaran();
            loadPiutang();
            loadPembelianSupplier();
            loadKartuStok();
        });

        $('#kartuStokProduct').on('change', loadKartuStok);

        $(document).ready(function() {
            var firstDayOfMonth = new Date();
            firstDayOfMonth.setDate(1);
            $('#reportStart').val(firstDayOfMonth.toISOString().slice(0, 10));
            $('#reportEnd').val(new Date().toISOString().slice(0, 10));

            loadProductOptions();
            loadLabaRugi();
            loadRekapPembayaran();
            loadPiutang();
            loadPembelianSupplier();
            loadStokOpname();
        });
    </script>
@endsection
