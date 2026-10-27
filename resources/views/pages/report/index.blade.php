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
                        <div class="float-end">
                            <select class="form-control" id="omsetYear" style="width:100px"></select>
                        </div>
                        <h4 class="card-title">Omset Bulanan</h4>
                    </div>
                    <div class="card-body">
                        <div id="chartOmsetBulanan"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Demografi Pasien</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div id="chartGender"></div>
                            </div>
                            <div class="col-6">
                                <div id="chartUsia"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Tindakan Terlaris</h4>
                        <small>Periode sesuai filter tanggal di atas</small>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Tindakan</th>
                                    <th>Jumlah</th>
                                    <th>Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody id="tindakanTerlarisBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Performa Dokter</h4>
                        <small>Periode sesuai filter tanggal di atas</small>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Dokter</th>
                                    <th>Jumlah Tindakan</th>
                                    <th>Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody id="performaDokterBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function formatRupiahReport(v) {
            return 'Rp ' + Number(v).toLocaleString('id-ID');
        }

        var chartOmset, chartGender, chartUsia;

        function loadOmsetBulanan() {
            $.ajax({
                url: "{{ url('report/getOmsetBulanan') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    year: $('#omsetYear').val()
                },
                success: function(res) {
                    var categories = res.data.map(d => d.bulan);
                    var values = res.data.map(d => d.total);

                    if (chartOmset) {
                        chartOmset.updateSeries([{
                            name: 'Omset',
                            data: values
                        }]);
                        return;
                    }

                    chartOmset = new ApexCharts(document.querySelector("#chartOmsetBulanan"), {
                        chart: {
                            type: 'bar',
                            height: 280,
                            toolbar: {
                                show: false
                            }
                        },
                        series: [{
                            name: 'Omset',
                            data: values
                        }],
                        xaxis: {
                            categories: categories
                        },
                        yaxis: {
                            labels: {
                                formatter: formatRupiahReport
                            }
                        },
                        tooltip: {
                            y: {
                                formatter: formatRupiahReport
                            }
                        },
                        colors: ['#3b82f6']
                    });
                    chartOmset.render();
                }
            });
        }

        function loadTindakanTerlaris() {
            $.ajax({
                url: "{{ url('report/getTindakanTerlaris') }}",
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
                        <td>${d.tindakan}</td>
                        <td>${d.jumlah}</td>
                        <td>${formatRupiahReport(d.total_pendapatan)}</td>
                    </tr>`);
                    $('#tindakanTerlarisBody').html(rows || '<tr><td colspan="3" class="text-muted text-center">Tidak ada data</td></tr>');
                }
            });
        }

        function loadPerformaDokter() {
            $.ajax({
                url: "{{ url('report/getPerformaDokter') }}",
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
                        <td>${d.dokter}</td>
                        <td>${d.jumlah_tindakan}</td>
                        <td>${formatRupiahReport(d.total_pendapatan)}</td>
                    </tr>`);
                    $('#performaDokterBody').html(rows || '<tr><td colspan="3" class="text-muted text-center">Tidak ada data</td></tr>');
                }
            });
        }

        function loadDemografi() {
            $.ajax({
                url: "{{ url('report/getDemografiPasien') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(res) {
                    var genderLabels = res.data.gender.map(g => g.label);
                    var genderValues = res.data.gender.map(g => g.jumlah);

                    if (chartGender) {
                        chartGender.updateSeries(genderValues);
                    } else {
                        chartGender = new ApexCharts(document.querySelector("#chartGender"), {
                            chart: {
                                type: 'donut',
                                height: 220
                            },
                            labels: genderLabels,
                            series: genderValues,
                            legend: {
                                position: 'bottom'
                            }
                        });
                        chartGender.render();
                    }

                    var usiaLabels = Object.keys(res.data.usia);
                    var usiaValues = Object.values(res.data.usia);

                    if (chartUsia) {
                        chartUsia.updateSeries([{
                            data: usiaValues
                        }]);
                    } else {
                        chartUsia = new ApexCharts(document.querySelector("#chartUsia"), {
                            chart: {
                                type: 'bar',
                                height: 220,
                                toolbar: {
                                    show: false
                                }
                            },
                            series: [{
                                name: 'Pasien',
                                data: usiaValues
                            }],
                            xaxis: {
                                categories: usiaLabels
                            },
                            colors: ['#10b981']
                        });
                        chartUsia.render();
                    }
                }
            });
        }

        $('#applyFilterBtn').on('click', function() {
            loadTindakanTerlaris();
            loadPerformaDokter();
        });

        $('#omsetYear').on('change', loadOmsetBulanan);

        $(document).ready(function() {
            var currentYear = new Date().getFullYear();
            var yearOptions = '';
            for (var y = currentYear; y >= currentYear - 4; y--) {
                yearOptions += `<option value="${y}">${y}</option>`;
            }
            $('#omsetYear').html(yearOptions);

            var firstDayOfMonth = new Date();
            firstDayOfMonth.setDate(1);
            $('#reportStart').val(firstDayOfMonth.toISOString().slice(0, 10));
            $('#reportEnd').val(new Date().toISOString().slice(0, 10));

            loadOmsetBulanan();
            loadTindakanTerlaris();
            loadPerformaDokter();
            loadDemografi();
        });
    </script>
@endsection
