@extends('layout/main')

@section('content')
    <div class="content-inner mt-5 py-0">
        <div class="row">
            <div class="col-md-5 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Template Shift</h4>
                        <small>Katalog jam kerja yang bisa dipakai ulang</small>
                    </div>
                    <div class="card-body">
                        <div class="row g-2 mb-3">
                            <div class="col-3">
                                <input type="text" class="form-control" id="tplKode" placeholder="Kode">
                            </div>
                            <div class="col-4">
                                <input type="text" class="form-control" id="tplNama" placeholder="Nama Shift">
                            </div>
                            <div class="col-2">
                                <input type="time" class="form-control" id="tplMasuk">
                            </div>
                            <div class="col-2">
                                <input type="time" class="form-control" id="tplPulang">
                            </div>
                            <div class="col-1">
                                <button class="btn btn-primary w-100" id="addTemplateBtn">+</button>
                            </div>
                        </div>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama</th>
                                    <th>Masuk</th>
                                    <th>Pulang</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="templateBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-7 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Atur Jadwal Karyawan</h4>
                        <small>Shift yang lewat tengah malam otomatis terhitung pulang di hari berikutnya</small>
                    </div>
                    <div class="card-body">
                        <div class="row g-2 mb-2">
                            <div class="col-md-5">
                                <label class="form-label">Karyawan</label>
                                <select class="form-control select2" id="rosterEmployee" style="width:100%"></select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tanggal Kerja</label>
                                <input type="date" class="form-control" id="rosterTanggal">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Template Shift</label>
                                <select class="form-control" id="rosterTemplate">
                                    <option value="">Custom</option>
                                </select>
                            </div>
                        </div>
                        <div class="row g-2 mb-2" id="customTimeRow">
                            <div class="col-md-4">
                                <label class="form-label">Jam Masuk</label>
                                <input type="time" class="form-control" id="rosterMasukTime">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Jam Pulang</label>
                                <input type="time" class="form-control" id="rosterPulangTime">
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-md-9">
                                <input type="text" class="form-control" id="rosterNotes" placeholder="Catatan (opsional)">
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-success w-100" id="saveRosterBtn">Simpan Jadwal</button>
                            </div>
                        </div>

                        <table class="display app-data-table table-hover" id="rosterTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Karyawan</th>
                                    <th>Tanggal</th>
                                    <th>Shift</th>
                                    <th>Masuk</th>
                                    <th>Pulang</th>
                                    <th>-</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        var rosterTable;
        var templates = [];

        function loadTemplates() {
            $.ajax({
                url: "{{ url('jadwal-kerja/getTemplates') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(res) {
                    templates = res.data;
                    var rows = '';
                    var options = '<option value="">Custom</option>';
                    templates.forEach(function(t) {
                        rows += `<tr>
                            <td>${t.kode}</td>
                            <td>${t.nama}</td>
                            <td>${t.jammasuk}</td>
                            <td>${t.jampulang}</td>
                            <td><a class="btn btn-xs btn-outline-danger" onclick="deleteTemplate(${t.id})">&times;</a></td>
                        </tr>`;
                        options += `<option value="${t.id}">${t.kode} - ${t.nama} (${t.jammasuk}-${t.jampulang})</option>`;
                    });
                    $('#templateBody').html(rows);
                    $('#rosterTemplate').html(options);
                }
            });
        }

        $('#addTemplateBtn').on('click', function() {
            var kode = $('#tplKode').val(), nama = $('#tplNama').val();
            var masuk = $('#tplMasuk').val(), pulang = $('#tplPulang').val();
            if (!kode || !nama || !masuk || !pulang) {
                return Toast.fire({
                    icon: 'warning',
                    title: 'Lengkapi kode, nama, jam masuk & pulang'
                });
            }
            $.ajax({
                url: "{{ url('jadwal-kerja/storeTemplate') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    kode: kode,
                    nama: nama,
                    jammasuk: masuk,
                    jampulang: pulang
                },
                success: function() {
                    Toast.fire({
                        icon: 'success',
                        title: 'Template ditambahkan'
                    });
                    $('#tplKode,#tplNama,#tplMasuk,#tplPulang').val('');
                    loadTemplates();
                },
                error: function(xhr) {
                    Toast.fire({
                        icon: 'error',
                        title: JSON.parse(xhr.responseText).error ?? 'Gagal menambah template'
                    });
                }
            });
        });

        function deleteTemplate(id) {
            Swal.fire({
                title: 'Hapus template shift ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('jadwal-kerja/deleteTemplate') }}/" + id,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function() {
                            Toast.fire({
                                icon: 'success',
                                title: 'Template dihapus'
                            });
                            loadTemplates();
                        },
                        error: function(xhr) {
                            Toast.fire({
                                icon: 'error',
                                title: JSON.parse(xhr.responseText).error ?? 'Gagal menghapus'
                            });
                        }
                    });
                }
            });
        }

        $('#rosterTemplate').on('change', function() {
            $('#customTimeRow').toggle($(this).val() === '');
        });

        $('#saveRosterBtn').on('click', function() {
            var employee = $('#rosterEmployee').val();
            var tanggal = $('#rosterTanggal').val();
            if (!employee || !tanggal) {
                return Toast.fire({
                    icon: 'warning',
                    title: 'Pilih karyawan dan tanggal dulu'
                });
            }
            $.ajax({
                url: "{{ url('jadwal-kerja/store') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    fk_employee: employee,
                    tanggal: tanggal,
                    fk_jadwalkerja: $('#rosterTemplate').val(),
                    jam_masuk_time: $('#rosterMasukTime').val(),
                    jam_pulang_time: $('#rosterPulangTime').val(),
                    notes: $('#rosterNotes').val(),
                },
                success: function() {
                    Toast.fire({
                        icon: 'success',
                        title: 'Jadwal disimpan'
                    });
                    $('#rosterNotes').val('');
                    rosterTable.ajax.reload();
                },
                error: function(xhr) {
                    Toast.fire({
                        icon: 'error',
                        title: JSON.parse(xhr.responseText).error ?? 'Gagal menyimpan jadwal'
                    });
                }
            });
        });

        function deleteRoster(id) {
            Swal.fire({
                title: 'Hapus jadwal ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('jadwal-kerja/delete') }}/" + id,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function() {
                            Toast.fire({
                                icon: 'success',
                                title: 'Jadwal dihapus'
                            });
                            rosterTable.ajax.reload();
                        }
                    });
                }
            });
        }

        $(document).ready(function() {
            loadTemplates();

            $('#rosterEmployee').select2({
                ajax: {
                    url: "{{ url('jadwal-kerja/getEmployeeList') }}",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(res) {
                        return {
                            results: res.data.map(e => ({
                                id: e.id,
                                text: e.nik + ' | ' + e.fullname
                            }))
                        };
                    }
                },
                placeholder: 'Cari nama/NIK karyawan'
            });

            rosterTable = $('#rosterTable').DataTable({
                destroy: true,
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    url: "{{ url('jadwal-kerja/getList') }}",
                    type: "POST",
                },
                columns: [
                    { data: 'employee' },
                    { data: 'tanggal' },
                    { data: 'template' },
                    { data: 'jam_masuk' },
                    {
                        data: 'jam_pulang',
                        render: function(data, type, row) {
                            return row.lintas_hari ? `${data} <span class="badge bg-info">+1 hari</span>` : data;
                        }
                    },
                    {
                        data: 'id',
                        render: function(id) {
                            return `<a class="btn btn-xs btn-outline-danger" onclick="deleteRoster(${id})">Hapus</a>`;
                        }
                    },
                ],
            });
        });
    </script>
@endsection
