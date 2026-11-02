@extends('layout/main')
@section('style')
    <style>
    </style>
@endsection

@section('content')
    <div class="content-inner mt-5 py-0">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="float-end">
                            <a class="btn btn-sm btn-outline-primary rounded-pill mt-2 ml-2" onclick="openAdd()">
                                <i class="iconoir-plus-circle"></i> Tambah Ruangan
                            </a>
                        </div>
                        <h4 class="card-title">Ruangan Operasi</h4>
                        <small>Dipakai sebagai pilihan ruangan di Jadwal Operasi.</small>
                    </div>
                    <div class="card-body" style="padding-left: 40px; padding-right:40px">
                        <div class="app-datatable-default overflow-auto">
                            <table class="display app-data-table default-data-table table-hover" id="main-table">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Kode</th>
                                        <th>Lantai</th>
                                        <th>Status</th>
                                        <th>Jadwal Aktif</th>
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
    </div>

    <div class="modal fade" id="modalForm" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Ruangan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formRuangan">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id">
                        <div class="mb-3">
                            <label class="form-label">Nama Ruangan</label>
                            <input type="text" class="form-control" name="nama" id="nama" required maxlength="100"
                                placeholder="Contoh: OK 1">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kode (opsional)</label>
                            <input type="text" class="form-control" name="kode" id="kode" maxlength="50">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lantai (opsional)</label>
                            <input type="text" class="form-control" name="lantai" id="lantai" maxlength="50">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="status" required>
                                <option value="tersedia">Tersedia</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan (opsional)</label>
                            <textarea class="form-control" name="keterangan" id="keterangan" maxlength="1000"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmit">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDelete" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <input type="hidden" id="deleteId">
                    <p>Hapus ruangan ini? Tidak bisa dihapus kalau masih punya jadwal operasi aktif.</p>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="btnDeleteConfirm">Ya, Hapus</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        var mainTable;

        function statusBadge(status) {
            return status === 'maintenance' ?
                '<span class="badge bg-warning">Maintenance</span>' :
                '<span class="badge bg-success">Tersedia</span>';
        }

        function getList() {
            var columns = [
                { data: 'nama' },
                { data: 'kode', render: (d) => d ? d : '-' },
                { data: 'lantai', render: (d) => d ? d : '-' },
                { data: 'status', render: (d) => statusBadge(d) },
                { data: 'jadwal_aktif_count' },
                {
                    data: 'id',
                    orderable: false,
                    searchable: false,
                    render: function(id) {
                        return `
                            <button class="btn btn-xs btn-outline-warning rounded-pill" onclick="openEdit('${id}')">
                                <i class="iconoir-edit-pencil"></i>
                            </button>
                            <button class="btn btn-xs btn-outline-danger rounded-pill" onclick="openDelete('${id}')">
                                <i class="iconoir-trash"></i>
                            </button>
                        `;
                    },
                },
            ];

            mainTable = $('#main-table').DataTable({
                searching: true,
                destroy: true,
                lengthChange: false,
                responsive: true,
                ajax: {
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    url: "{{ url('ruangan-operasi/getList') }}",
                    type: 'POST',
                },
                columns: columns,
            });
        }

        $(document).ready(function() {
            getList();
        });

        function openAdd() {
            $('#modalTitle').text('Tambah Ruangan');
            $('#formRuangan')[0].reset();
            $('#id').val('');
            $('#modalForm').modal('show');
        }

        function openEdit(id) {
            var row = mainTable.rows().data().toArray().find((r) => String(r.id) === String(id));
            if (!row) return;

            $('#modalTitle').text('Edit Ruangan');
            $('#id').val(row.id);
            $('#nama').val(row.nama);
            $('#kode').val(row.kode);
            $('#lantai').val(row.lantai);
            $('#status').val(row.status);
            $('#keterangan').val(row.keterangan);
            $('#modalForm').modal('show');
        }

        function openDelete(id) {
            $('#deleteId').val(id);
            $('#modalDelete').modal('show');
        }

        $('#formRuangan').submit(function(e) {
            e.preventDefault();

            var id = $('#id').val();
            var url = id ? "{{ url('ruangan-operasi/update') }}/" + id : "{{ url('ruangan-operasi/store') }}";

            $('#btnSubmit').prop('disabled', true).text('Menyimpan...');

            $.ajax({
                url: url,
                type: 'POST',
                data: $(this).serialize(),
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                complete: function() {
                    $('#btnSubmit').prop('disabled', false).text('Simpan');
                },
                success: function() {
                    $('#modalForm').modal('hide');
                    mainTable.ajax.reload();
                    Toast.fire({ icon: 'success', title: 'Ruangan tersimpan' });
                },
                error: function(xhr) {
                    Toast.fire({
                        icon: 'error',
                        title: xhr.responseJSON?.error ?? 'Gagal menyimpan'
                    });
                }
            });
        });

        $('#btnDeleteConfirm').click(function() {
            var id = $('#deleteId').val();

            $.ajax({
                url: "{{ url('ruangan-operasi/delete') }}/" + id,
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function() {
                    $('#modalDelete').modal('hide');
                    mainTable.ajax.reload();
                    Toast.fire({ icon: 'success', title: 'Ruangan dihapus' });
                },
                error: function(xhr) {
                    $('#modalDelete').modal('hide');
                    Toast.fire({
                        icon: 'error',
                        title: xhr.responseJSON?.error ?? 'Gagal menghapus'
                    });
                }
            });
        });
    </script>
@endsection
