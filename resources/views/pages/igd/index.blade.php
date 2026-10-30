@extends('layout/main')

@section('style')
    <style>
        .triase-btn {
            flex: 1;
            padding: 10px 8px;
            border-radius: 8px;
            border: 2px solid transparent;
            font-weight: 700;
            font-size: 12px;
            cursor: pointer;
            color: #fff;
            text-align: center;
        }

        .triase-btn.selected {
            border-color: #000;
        }

        .triase-merah {
            background: #dc3545;
        }

        .triase-kuning {
            background: #ffc107;
            color: #000;
        }

        .triase-hijau {
            background: #198754;
        }

        .triase-hitam {
            background: #212529;
        }

        .badge-triase {
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            color: #fff;
        }
    </style>
@endsection

@section('content')
    <div class="content-inner mt-5 py-0">
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Daftarkan Pasien IGD</h4>
                        <small class="text-muted">Pasien harus sudah terdaftar lewat menu Pasien Baru dulu kalau
                            belum ada.</small>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Pasien</label>
                            <select class="form-control select2" id="igdPasien" style="width:100%"></select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ruangan / Dokter Jaga</label>
                            <select class="form-control select2" id="igdRuangan" style="width:100%"></select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Triase</label>
                            <div class="d-flex gap-2">
                                <button type="button" class="triase-btn triase-merah" data-triase="Merah">MERAH</button>
                                <button type="button" class="triase-btn triase-kuning" data-triase="Kuning">KUNING</button>
                                <button type="button" class="triase-btn triase-hijau" data-triase="Hijau">HIJAU</button>
                                <button type="button" class="triase-btn triase-hitam" data-triase="Hitam">HITAM</button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keluhan</label>
                            <textarea class="form-control" id="igdKeluhan" rows="2"></textarea>
                        </div>
                        <button class="btn btn-danger w-100" id="igdSubmitBtn">Daftarkan ke IGD</button>
                    </div>
                </div>
            </div>

            <div class="col-md-8 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Antrian IGD — Diurutkan per Triase</h4>
                        <small class="text-muted">Merah &gt; Kuning &gt; Hijau &gt; Hitam, bukan urut waktu daftar.</small>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm" id="igdQueueTable">
                            <thead>
                                <tr>
                                    <th>Triase</th>
                                    <th>No. Antrian</th>
                                    <th>Pasien</th>
                                    <th>Keluhan</th>
                                    <th>Status</th>
                                    <th>-</th>
                                </tr>
                            </thead>
                            <tbody id="igdQueueBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        var selectedTriase = null;
        var triaseBadgeColor = {
            Merah: '#dc3545',
            Kuning: '#ffc107',
            Hijau: '#198754',
            Hitam: '#212529'
        };

        $('#igdPasien').select2({
            ajax: {
                url: "{{ url('igd/getPatientList') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                delay: 250,
                data: function(params) {
                    return {
                        params: params.term
                    };
                },
                processResults: function(res) {
                    return {
                        results: res.data.map(p => ({
                            id: p.id,
                            text: p.norm + ' | ' + p.nama
                        }))
                    };
                }
            },
            placeholder: 'Cari nama/No. RM pasien'
        });

        $('#igdRuangan').select2({
            ajax: {
                url: "{{ url('igd/getRuanganList') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                delay: 250,
                data: function(params) {
                    return {
                        params: params.term
                    };
                },
                processResults: function(res) {
                    return {
                        results: res.data.map(r => ({
                            id: r.id,
                            text: r.name + ' (' + r.dokter + ')'
                        }))
                    };
                }
            },
            placeholder: 'Cari ruangan jaga'
        });

        $('.triase-btn').on('click', function() {
            $('.triase-btn').removeClass('selected');
            $(this).addClass('selected');
            selectedTriase = $(this).data('triase');
        });

        $('#igdSubmitBtn').on('click', function() {
            if (!$('#igdPasien').val() || !$('#igdRuangan').val() || !selectedTriase) {
                return Toast.fire({
                    icon: 'warning',
                    title: 'Pilih pasien, ruangan, dan triase dulu'
                });
            }
            $.ajax({
                url: "{{ url('igd/store') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    pasienfk: $('#igdPasien').val(),
                    ruanganfk: $('#igdRuangan').val(),
                    triase: selectedTriase,
                    keluhan: $('#igdKeluhan').val(),
                },
                success: function() {
                    Toast.fire({
                        icon: 'success',
                        title: 'Pasien terdaftar di antrian IGD'
                    });
                    $('#igdPasien,#igdRuangan').val(null).trigger('change');
                    $('#igdKeluhan').val('');
                    $('.triase-btn').removeClass('selected');
                    selectedTriase = null;
                    loadQueue();
                },
                error: function(xhr) {
                    Toast.fire({
                        icon: 'error',
                        title: JSON.parse(xhr.responseText).error ?? 'Gagal mendaftarkan'
                    });
                }
            });
        });

        function ubahTriase(id, current) {
            Swal.fire({
                title: 'Ubah Triase',
                input: 'select',
                inputOptions: {
                    Merah: 'Merah',
                    Kuning: 'Kuning',
                    Hijau: 'Hijau',
                    Hitam: 'Hitam'
                },
                inputValue: current,
                showCancelButton: true,
                confirmButtonText: 'Simpan'
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('igd/updateTriase') }}/" + id,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            triase: result.value
                        },
                        success: function() {
                            Toast.fire({
                                icon: 'success',
                                title: 'Triase diperbarui'
                            });
                            loadQueue();
                        }
                    });
                }
            });
        }

        function mulaiPeriksaIgd(id) {
            Turbo.visit("{{ url('pemeriksaan/pasien') }}/" + btoa(btoa(id)));
        }

        function loadQueue() {
            $.ajax({
                url: "{{ url('igd/getQueueList') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(res) {
                    var rows = '';
                    res.data.forEach(function(d) {
                        rows += `<tr>
                            <td><span class="badge-triase" style="background:${triaseBadgeColor[d.triase]}">${d.triase}</span></td>
                            <td>${d.noantrian}</td>
                            <td>${d.pasien_nama} <br><small class="text-muted">${d.pasien_norm}</small></td>
                            <td>${d.keluhan ?? '-'}</td>
                            <td>${d.status}</td>
                            <td>
                                <a class="btn btn-xs btn-outline-danger" onclick="mulaiPeriksaIgd(${d.id})">Periksa</a>
                                <a class="btn btn-xs btn-outline-secondary" onclick="ubahTriase(${d.id}, '${d.triase}')">Ubah Triase</a>
                            </td>
                        </tr>`;
                    });
                    $('#igdQueueBody').html(rows || '<tr><td colspan="6" class="text-muted text-center">Tidak ada pasien IGD saat ini</td></tr>');
                }
            });
        }

        $(document).ready(function() {
            loadQueue();
            setInterval(loadQueue, 10000);
        });
    </script>
@endsection
