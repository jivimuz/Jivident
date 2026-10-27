@extends('layout/main')

@section('content')
    <div class="content-inner mt-5 py-0">
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Ajukan Cuti</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Karyawan</label>
                            <select class="form-control select2" id="cutiEmployee" style="width:100%"></select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jenis Cuti</label>
                            <select class="form-control" id="cutiJenis"></select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="cutiStart">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" class="form-control" id="cutiEnd">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea class="form-control" id="cutiNotes" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat Selama Cuti (opsional)</label>
                            <input type="text" class="form-control" id="cutiAlamat">
                        </div>
                        <button class="btn btn-success w-100" id="submitCutiBtn">Ajukan</button>
                    </div>
                </div>
            </div>

            <div class="col-md-8 mb-3">
                <div class="card">
                    <div class="card-header">
                        <div class="float-end">
                            <select class="form-control" id="filterStatus" style="width:160px">
                                <option value="">Semua Status</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                        <h4 class="card-title">Daftar Pengajuan Cuti</h4>
                    </div>
                    <div class="card-body">
                        <table class="display app-data-table table-hover" id="cutiTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Karyawan</th>
                                    <th>Jenis</th>
                                    <th>Periode</th>
                                    <th>Status</th>
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
        var cutiTable;

        $('#submitCutiBtn').on('click', function() {
            var employee = $('#cutiEmployee').val();
            var jenis = $('#cutiJenis').val();
            var start = $('#cutiStart').val();
            var end = $('#cutiEnd').val();
            if (!employee || !jenis || !start || !end) {
                return Toast.fire({
                    icon: 'warning',
                    title: 'Lengkapi karyawan, jenis cuti, dan tanggal'
                });
            }
            $.ajax({
                url: "{{ url('cuti/store') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    fk_employee: employee,
                    fk_jeniscuti: jenis,
                    startdate: start,
                    enddate: end,
                    notes: $('#cutiNotes').val(),
                    alamat: $('#cutiAlamat').val(),
                },
                success: function() {
                    Toast.fire({
                        icon: 'success',
                        title: 'Pengajuan cuti terkirim'
                    });
                    $('#cutiNotes,#cutiAlamat').val('');
                    cutiTable.ajax.reload();
                },
                error: function(xhr) {
                    Toast.fire({
                        icon: 'error',
                        title: JSON.parse(xhr.responseText).error ?? 'Gagal mengajukan cuti'
                    });
                }
            });
        });

        function approveCuti(id) {
            Swal.fire({
                title: 'Approve pengajuan ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Approve',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('cuti/approve') }}/" + id,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function() {
                            Toast.fire({
                                icon: 'success',
                                title: 'Disetujui'
                            });
                            cutiTable.ajax.reload();
                        },
                        error: function(xhr) {
                            Toast.fire({
                                icon: 'error',
                                title: JSON.parse(xhr.responseText).error ?? 'Gagal approve'
                            });
                        }
                    });
                }
            });
        }

        function rejectCuti(id) {
            Swal.fire({
                title: 'Alasan penolakan',
                input: 'text',
                showCancelButton: true,
                confirmButtonText: 'Tolak',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('cuti/reject') }}/" + id,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            reason: result.value
                        },
                        success: function() {
                            Toast.fire({
                                icon: 'success',
                                title: 'Ditolak'
                            });
                            cutiTable.ajax.reload();
                        },
                        error: function(xhr) {
                            Toast.fire({
                                icon: 'error',
                                title: JSON.parse(xhr.responseText).error ?? 'Gagal menolak'
                            });
                        }
                    });
                }
            });
        }

        function deleteCuti(id) {
            Swal.fire({
                title: 'Hapus pengajuan ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('cuti/delete') }}/" + id,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function() {
                            Toast.fire({
                                icon: 'success',
                                title: 'Dihapus'
                            });
                            cutiTable.ajax.reload();
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

        $(document).ready(function() {
            $('#cutiEmployee').select2({
                ajax: {
                    url: "{{ url('cuti/getEmployeeList') }}",
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

            $.ajax({
                url: "{{ url('cuti/getJenisCutiList') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(res) {
                    var options = '';
                    res.data.forEach(j => options += `<option value="${j.id}">${j.name}</option>`);
                    $('#cutiJenis').html(options);
                }
            });

            cutiTable = $('#cutiTable').DataTable({
                destroy: true,
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    url: "{{ url('cuti/getList') }}",
                    type: "POST",
                    data: function(d) {
                        d.status = $('#filterStatus').val();
                    }
                },
                columns: [
                    {
                        data: 'nopermohonan'
                    },
                    {
                        data: 'employee'
                    },
                    {
                        data: 'jenis_cuti'
                    },
                    {
                        data: 'startdate',
                        render: function(data, type, row) {
                            return data + ' s/d ' + row.enddate;
                        }
                    },
                    {
                        data: 'status',
                        render: function(s, type, row) {
                            var cls = s === 'approved' ? 'success' : (s === 'rejected' ? 'danger' : 'warning');
                            var html = `<span class="badge bg-${cls}">${s}</span>`;
                            if (s === 'rejected' && row.rejected_reason) {
                                html += `<div class="small text-muted">${row.rejected_reason}</div>`;
                            }
                            return html;
                        }
                    },
                    {
                        data: 'id',
                        render: function(id, type, row) {
                            if (row.status !== 'pending') return '-';
                            return `
                                <a class="btn btn-xs btn-outline-success" onclick="approveCuti(${id})">Approve</a>
                                <a class="btn btn-xs btn-outline-danger" onclick="rejectCuti(${id})">Tolak</a>
                                <a class="btn btn-xs btn-outline-secondary" onclick="deleteCuti(${id})">Hapus</a>
                            `;
                        }
                    },
                ],
            });

            $('#filterStatus').on('change', function() {
                cutiTable.ajax.reload();
            });
        });
    </script>
@endsection
