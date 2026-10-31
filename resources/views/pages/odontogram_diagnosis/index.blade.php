@extends('layout/main')
@section('style')
    <style>
        .warna-dot {
            display: inline-block;
            width: 16px;
            height: 16px;
            border-radius: 4px;
            border: 1px solid #ccc;
            vertical-align: middle;
            margin-right: 6px;
        }
    </style>
@endsection

@section('content')
    <div class="content-inner mt-5 py-0">
        <div class="row">
            <div class="col-md-12">
                <div class="card" data-iq-gsap="onStart" data-iq-opacity="0" data-iq-position-y="-40"
                    data-iq-duration=".6" data-iq-delay=".4" data-iq-trigger="scroll" data-iq-ease="none">
                    <div class="card-header">
                        <div class="float-end">
                            <a class="btn btn-sm btn-outline-primary rounded-pill mt-2 ml-2" onclick="openAdd()">
                                <i class="iconoir-plus-circle"></i> Tambah Diagnosis
                            </a>
                        </div>
                        <h4 class="card-title">Master Diagnosis Odontogram</h4>
                        <small>Dipakai di tab Odontogram pada Pemeriksaan — "3D Diagnosis" untuk titik tanda di model
                            3D, "2D Kondisi Gigi" untuk tombol kondisi pada chart 2D Global.</small>
                    </div>
                    <div class="card-body" style="padding-left: 40px; padding-right:40px">
                        <div class="app-datatable-default overflow-auto">
                            <table class="display app-data-table default-data-table table-hover" id="main-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kategori</th>
                                        <th>Nama</th>
                                        <th>Warna</th>
                                        <th>Simbol</th>
                                        <th>Urutan</th>
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

    <!-- Modal Add/Edit -->
    <div class="modal fade" id="modalForm" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Diagnosis</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="formDiagnosis">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id">

                        <div class="mb-3">
                            <label class="form-label">Kategori</label>
                            <select class="form-select" name="kategori" id="kategori" onchange="toggleKategoriFields()"
                                required>
                                <option value="3d">3D Diagnosis (model gigi)</option>
                                <option value="2d">2D Kondisi Gigi (chart global)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" class="form-control" name="nama" id="nama" required maxlength="100">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Warna</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" id="warnaPicker"
                                    value="#3b82f6" onchange="$('#warna').val(this.value)" style="max-width:60px">
                                <input type="text" class="form-control" name="warna" id="warna" value="#3b82f6"
                                    pattern="^#[0-9A-Fa-f]{6}$" oninput="syncWarnaPicker()" required>
                            </div>
                            <small class="text-muted">Format hex, contoh #3b82f6</small>
                        </div>

                        <div class="mb-3" id="simbolField">
                            <label class="form-label">Simbol (khusus 2D)</label>
                            <input type="text" class="form-control" name="simbol" id="simbol" maxlength="20"
                                placeholder="Contoh: UE, PE, &amp;nbsp;X">
                            <small class="text-muted">Kode singkat yang tampil di chart 2D, boleh kosong.</small>
                        </div>

                        <div class="mb-3 form-check" id="resetField">
                            <input type="checkbox" class="form-check-input" name="is_reset" id="is_reset">
                            <label class="form-check-label" for="is_reset">
                                Ini tombol "Bersih" (menghapus tanda, bukan mewarnai) — khusus 2D
                            </label>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Urutan Tampil</label>
                            <input type="number" class="form-control" name="urutan" id="urutan" value="0">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmit">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Delete -->
    <div class="modal fade" id="modalDelete" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Hapus Diagnosis?</h6>
                </div>
                <div class="modal-body">
                    <p class="mb-1">Yakin hapus item ini? Odontogram lama yang sudah pernah memakainya tidak akan
                        berubah (datanya sudah tersimpan sendiri saat disimpan), tapi item ini tidak akan muncul lagi
                        di pilihan baru.</p>
                    <input type="hidden" id="deleteId">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-danger" id="btnDeleteConfirm">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function toggleKategoriFields() {
            var is2d = $('#kategori').val() === '2d';
            $('#simbolField').toggle(is2d);
            $('#resetField').toggle(is2d);
            if (!is2d) {
                $('#simbol').val('');
                $('#is_reset').prop('checked', false);
            }
        }

        function syncWarnaPicker() {
            var v = $('#warna').val();
            if (/^#[0-9A-Fa-f]{6}$/.test(v)) {
                $('#warnaPicker').val(v);
            }
        }

        function kategoriLabel(k) {
            return k === '2d' ? '2D Kondisi Gigi' : '3D Diagnosis';
        }

        $(document).ready(function() {
            getList();
            toggleKategoriFields();
        });

        var mainTable;

        function getList() {
            var columns = [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                    orderable: false,
                    searchable: false,
                    width: '10',
                },
                {
                    data: 'kategori',
                    render: function(data) {
                        var badgeClass = data === '2d' ? 'bg-info' : 'bg-primary';
                        return `<span class="badge ${badgeClass}">${kategoriLabel(data)}</span>`;
                    },
                },
                {
                    data: 'nama',
                },
                {
                    data: 'warna',
                    render: function(data) {
                        return `<span class="warna-dot" style="background:${data}"></span>${data}`;
                    },
                },
                {
                    data: 'simbol',
                    render: function(data) {
                        return data ? data : '-';
                    },
                },
                {
                    data: 'urutan',
                },
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
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    url: "{{ url('odontogram-diagnosis/getList') }}",
                    type: 'POST',
                    data: {}
                },
                columns: columns,
            });
        }

        function openAdd() {
            const btn = event.currentTarget;
            btn.disabled = true;
            setTimeout(() => btn.disabled = false, 500);
            $('#modalTitle').text('Tambah Diagnosis');
            $('#formDiagnosis')[0].reset();
            $('#id').val('');
            $('#warna').val('#3b82f6');
            $('#warnaPicker').val('#3b82f6');
            toggleKategoriFields();
            $('#modalForm').modal('show');
        }

        function openEdit(id) {
            const btn = event.currentTarget;
            btn.disabled = true;
            setTimeout(() => btn.disabled = false, 500);
            $.ajax({
                url: "{{ url('odontogram-diagnosis/showDiagnosis') }}",
                type: 'POST',
                data: {
                    id: id
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(res) {
                    $('#modalTitle').text('Edit Diagnosis');
                    $('#id').val(res.id);
                    $('#kategori').val(res.kategori);
                    $('#nama').val(res.nama);
                    $('#warna').val(res.warna);
                    $('#warnaPicker').val(res.warna);
                    $('#simbol').val(res.simbol);
                    $('#is_reset').prop('checked', !!res.is_reset);
                    $('#urutan').val(res.urutan);
                    toggleKategoriFields();
                    $('#modalForm').modal('show');
                }
            });
        }

        function openDelete(id) {
            $('#deleteId').val(id);
            $('#modalDelete').modal('show');
        }

        $('#formDiagnosis').submit(function(e) {
            e.preventDefault();

            let formData = $(this).serialize();

            $('#btnSubmit').prop('disabled', true).text('Saving...');

            $.ajax({
                url: "{{ url('odontogram-diagnosis/saveDiagnosis') }}",
                type: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                complete: function() {
                    $('#btnSubmit').prop('disabled', false).text('Save');
                },
                success: function() {
                    $('#modalForm').modal('hide');
                    mainTable.ajax.reload();
                },
                error: function(xhr) {
                    Toast.fire({
                        icon: 'error',
                        title: xhr.responseJSON?.message ?? 'Gagal menyimpan'
                    });
                }
            });
        });

        $('#btnDeleteConfirm').click(function() {
            let id = $('#deleteId').val();

            Swal.fire({
                title: 'Anda yakin?',
                text: 'Hapus diagnosis ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus',
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#btnDeleteConfirm').prop('disabled', true).text('Deleting...');
                    $.ajax({
                        url: "{{ url('odontogram-diagnosis/deleteDiagnosis') }}",
                        type: 'POST',
                        data: {
                            id
                        },
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        complete: function() {
                            $('#btnDeleteConfirm').prop('disabled', false).text('Delete');
                        },
                        success: function() {
                            $('#modalDelete').modal('hide');
                            mainTable.ajax.reload();
                        }
                    });
                }
            });
        });
    </script>
@endsection
