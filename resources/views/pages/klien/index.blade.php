@extends('layout/main')

@section('content')
    <div class="content-inner mt-5 py-0">
        <div class="row">
            <div class="col-md-5 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title" id="formTitle">Tambah Cabang</h4>
                    </div>
                    <div class="card-body">
                        <input type="hidden" id="klienId">
                        <div class="mb-3">
                            <label class="form-label">Kode Klinik</label>
                            <input type="text" class="form-control" id="kodeklinik" maxlength="30">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Cabang</label>
                            <input type="text" class="form-control" id="nama">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tipe</label>
                            <input type="text" class="form-control" id="tipe" placeholder="misal: Klinik Utama, Cabang">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No. HP</label>
                            <input type="text" class="form-control" id="nohp">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="email">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" rows="2"></textarea>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="isholding">
                            <label class="form-check-label" for="isholding">Kantor Pusat / Holding</label>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ID Organisasi SATUSEHAT (IHS Number)</label>
                            <input type="text" class="form-control" id="ihs_id" placeholder="Isi setelah cabang ini terdaftar di SATUSEHAT">
                            <small class="text-muted">Opsional untuk saat ini — belum ada fitur yang memakainya, cuma tempat simpan dulu.</small>
                        </div>
                        <button class="btn btn-success" id="saveKlienBtn">Simpan</button>
                        <button class="btn btn-outline-secondary" id="resetFormBtn" style="display:none">Batal Edit</button>
                    </div>
                </div>
            </div>

            <div class="col-md-7 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Daftar Cabang</h4>
                    </div>
                    <div class="card-body">
                        <table class="display app-data-table table-hover" id="klienTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama</th>
                                    <th>No. HP</th>
                                    <th>Holding</th>
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
        var klienTable;

        function resetForm() {
            $('#klienId').val('');
            $('#kodeklinik,#nama,#tipe,#nohp,#email,#alamat,#ihs_id').val('');
            $('#isholding').prop('checked', false);
            $('#formTitle').text('Tambah Cabang');
            $('#resetFormBtn').hide();
        }

        $('#resetFormBtn').on('click', resetForm);

        function editKlien(id) {
            $.get("{{ url('klien/edit') }}/" + id, function(res) {
                var d = res.data;
                $('#klienId').val(d.id);
                $('#kodeklinik').val(d.kodeklinik);
                $('#nama').val(d.nama);
                $('#tipe').val(d.tipe);
                $('#nohp').val(d.nohp);
                $('#email').val(d.email);
                $('#alamat').val(d.alamat);
                $('#ihs_id').val(d.ihs_id);
                $('#isholding').prop('checked', !!d.isholding);
                $('#formTitle').text('Edit Cabang: ' + d.nama);
                $('#resetFormBtn').show();
            });
        }

        $('#saveKlienBtn').on('click', function() {
            var id = $('#klienId').val();
            var url = id ? "{{ url('klien/update') }}/" + id : "{{ url('klien/store') }}";

            $.ajax({
                url: url,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    kodeklinik: $('#kodeklinik').val(),
                    nama: $('#nama').val(),
                    tipe: $('#tipe').val(),
                    nohp: $('#nohp').val(),
                    email: $('#email').val(),
                    alamat: $('#alamat').val(),
                    ihs_id: $('#ihs_id').val(),
                    isholding: $('#isholding').is(':checked') ? 1 : 0,
                },
                success: function() {
                    Toast.fire({
                        icon: 'success',
                        title: 'Cabang disimpan'
                    });
                    resetForm();
                    klienTable.ajax.reload();
                },
                error: function(xhr) {
                    var msg = 'Gagal menyimpan';
                    try {
                        var body = JSON.parse(xhr.responseText);
                        msg = body.error || Object.values(body.errors ?? {}).flat().join(', ') || msg;
                    } catch (e) {}
                    Toast.fire({
                        icon: 'error',
                        title: msg
                    });
                }
            });
        });

        function deleteKlien(id) {
            Swal.fire({
                title: 'Hapus cabang ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('klien/delete') }}/" + id,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function() {
                            Toast.fire({
                                icon: 'success',
                                title: 'Cabang dihapus'
                            });
                            klienTable.ajax.reload();
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
            klienTable = $('#klienTable').DataTable({
                destroy: true,
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    url: "{{ url('klien/getList') }}",
                    type: "POST",
                },
                columns: [
                    { data: 'kodeklinik' },
                    { data: 'nama' },
                    { data: 'nohp' },
                    {
                        data: 'isholding',
                        render: function(v) {
                            return v ? '<span class="badge bg-primary">Holding</span>' : '-';
                        }
                    },
                    {
                        data: 'id',
                        render: function(id) {
                            return `
                                <a class="btn btn-xs btn-outline-primary" onclick="editKlien(${id})">Edit</a>
                                <a class="btn btn-xs btn-outline-danger" onclick="deleteKlien(${id})">Hapus</a>
                            `;
                        }
                    },
                ],
            });
        });
    </script>
@endsection
