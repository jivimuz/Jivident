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
                                <i class="iconoir-plus-circle"></i> Tambah Biaya
                            </a>
                        </div>
                        <h4 class="card-title">Biaya Tambahan</h4>
                        <small>Tambahkan biaya di luar tindakan/diagnosa/lab/radiologi/resep langsung ke tagihan
                            kunjungan pasien — misalnya biaya operasi. Langsung ikut terhitung di tagihan &amp;
                            bisa dibayar lewat halaman Billing seperti biasa.</small>
                    </div>
                    <div class="card-body" style="padding-left: 40px; padding-right:40px">
                        <div class="app-datatable-default overflow-auto">
                            <table class="display app-data-table default-data-table table-hover" id="main-table">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>No. RM</th>
                                        <th>Pasien</th>
                                        <th>No. Registrasi</th>
                                        <th>Nama Biaya</th>
                                        <th>Jumlah</th>
                                        <th>Ditambahkan Oleh</th>
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
                    <h5 class="modal-title">Tambah Biaya</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formBiaya">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Kunjungan Pasien</label>
                            <select class="form-control select2" name="daftarpasien_id" id="btDaftarPasien"
                                style="width:100%" required></select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Biaya</label>
                            <input type="text" class="form-control" name="nama" id="btNama" required maxlength="255"
                                placeholder="Contoh: Biaya Operasi" list="btPreset">
                            <datalist id="btPreset">
                                <option value="Biaya Operasi">
                                <option value="Biaya Administrasi">
                                <option value="Biaya Alat Medis Habis Pakai">
                                <option value="Biaya Konsultasi Tambahan">
                            </datalist>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jumlah (Rp)</label>
                            <input type="number" class="form-control" name="jumlah" id="btJumlah" required min="1"
                                step="1">
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Keterangan (opsional)</label>
                            <textarea class="form-control" name="keterangan" id="btKeterangan" rows="2"></textarea>
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
                    <p>Hapus biaya ini dari tagihan pasien?</p>
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

        function getList() {
            var columns = [
                { data: 'created_at', render: (d) => new Date(d).toLocaleDateString('id-ID') },
                { data: 'pasien_norm' },
                { data: 'pasien_nama' },
                { data: 'noregistrasi' },
                { data: 'nama' },
                { data: 'jumlah', render: (d) => formatRupiah(d) },
                { data: 'dibuat_oleh', render: (d) => d ? d : '-' },
                {
                    data: 'id',
                    orderable: false,
                    searchable: false,
                    render: function(id) {
                        return `<button class="btn btn-xs btn-outline-danger rounded-pill" onclick="openDelete('${id}')">
                            <i class="iconoir-trash"></i>
                        </button>`;
                    },
                },
            ];

            mainTable = $('#main-table').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                lengthChange: false,
                responsive: true,
                ajax: {
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    url: "{{ url('biaya-tambahan/getList') }}",
                    type: 'POST',
                },
                order: [[0, 'desc']],
                columns: columns,
            });
        }

        $(document).ready(function() {
            getList();

            $('#btDaftarPasien').select2({
                ajax: {
                    url: "{{ url('biaya-tambahan/getDaftarPasienList') }}",
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    delay: 250,
                    data: function(params) {
                        return { params: params.term };
                    },
                    processResults: function(res) {
                        return {
                            results: res.data.map(d => ({
                                id: d.id,
                                text: d.norm + ' | ' + d.nama + ' | ' + d.noregistrasi
                            }))
                        };
                    }
                },
                placeholder: 'Cari No. RM/nama pasien/no. registrasi',
                dropdownParent: $('#modalForm')
            });
        });

        function openAdd() {
            $('#formBiaya')[0].reset();
            $('#btDaftarPasien').val(null).trigger('change');
            $('#modalForm').modal('show');
        }

        function openDelete(id) {
            $('#deleteId').val(id);
            $('#modalDelete').modal('show');
        }

        $('#formBiaya').submit(function(e) {
            e.preventDefault();

            if (!$('#btDaftarPasien').val()) {
                return Toast.fire({ icon: 'warning', title: 'Pilih kunjungan pasien dulu' });
            }

            $('#btnSubmit').prop('disabled', true).text('Menyimpan...');

            $.ajax({
                url: "{{ url('biaya-tambahan/store') }}",
                type: 'POST',
                data: $(this).serialize(),
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                complete: function() {
                    $('#btnSubmit').prop('disabled', false).text('Simpan');
                },
                success: function() {
                    $('#modalForm').modal('hide');
                    mainTable.ajax.reload(null, false);
                    Toast.fire({ icon: 'success', title: 'Biaya berhasil ditambahkan ke tagihan' });
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
                url: "{{ url('biaya-tambahan/delete') }}/" + id,
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function() {
                    $('#modalDelete').modal('hide');
                    mainTable.ajax.reload(null, false);
                    Toast.fire({ icon: 'success', title: 'Biaya dihapus' });
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
