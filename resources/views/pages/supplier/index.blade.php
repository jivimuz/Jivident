@extends('layout/main')

@section('style')
    <style>
        .table-custom th {
            font-size: 14px;
            background-color: rgba(58, 87, 232, 0.05);
            color: #3a57e8;
            font-weight: 600;
        }

        .table-custom td {
            vertical-align: middle;
            font-size: 14px;
        }
    </style>
@endsection

@section('content')
    <div class="content-inner mt-5 py-0">
        <!-- Header -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="bg-white w-100 rounded-pill p-2 d-flex align-items-center justify-content-between shadow-sm">
                    <span class="text-primary fw-bold text-center flex-grow-1 fs-5 fs-md-4">
                        Master Supplier / PBF
                    </span>
                    <button type="button" onclick="addData()"
                        class="btn btn-primary rounded-pill fw-bold d-flex align-items-center flex-shrink-0 px-4">
                        <i class="iconoir-plus-circle me-1"></i> Tambah Supplier
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0" data-iq-gsap="onStart" data-iq-opacity="0" data-iq-position-y="-40"
                    data-iq-duration=".6" data-iq-delay=".2" data-iq-trigger="scroll" data-iq-ease="none">
                    <div class="card-body p-4">

                        <!-- Form Filter Pencarian -->
                        <form id="form-filter" class="row mb-4">
                            <div class="col-md-4 mb-2">
                                <label class="fw-bold mb-1 text-muted" style="font-size: 13px;">Nama Supplier</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="iconoir-search"></i></span>
                                    <input type="text" name="search_name" id="search_name" class="form-control"
                                        placeholder="Cari nama supplier...">
                                </div>
                            </div>
                            <div class="col-md-2 mb-2 d-flex align-items-end">
                                <button type="button" class="btn btn-primary w-100 rounded" onclick="loadData()">
                                    Cari Data
                                </button>
                            </div>
                        </form>

                        <!-- Tabel Data -->
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered table-custom" id="supplier-table"
                                style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">No</th>
                                        <th width="25%">Nama Supplier</th>
                                        <th width="15%">No. Telepon</th>
                                        <th width="20%">Contact Person</th>
                                        <th width="20%">Alamat</th>
                                        <th width="5%" class="text-center">Status</th>
                                        <th width="10%" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data akan dimuat melalui AJAX -->
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Form Tambah/Edit Supplier -->
    <div class="modal fade" id="modal-supplier" tabindex="-1" aria-labelledby="modalSupplierLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="form-supplier">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold text-primary" id="modalSupplierLabel">Tambah Supplier Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <input type="hidden" id="supplier_id" name="id">

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="fw-bold mb-1">Nama Supplier (PBF) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required
                                    placeholder="PT. Nama Supplier">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="fw-bold mb-1">No. Telepon</label>
                                <input type="text" class="form-control" id="phone" name="phone"
                                    placeholder="0812xxxxxx">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="fw-bold mb-1">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="email@perusahaan.com">
                            </div>

                            <div class="col-md-8 mb-3">
                                <label class="fw-bold mb-1">Contact Person (Sales)</label>
                                <input type="text" class="form-control" id="contact_person" name="contact_person"
                                    placeholder="Nama sales/PIC">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="fw-bold mb-1">Status</label>
                                <select class="form-select" id="is_active" name="is_active">
                                    <option value="1">Aktif</option>
                                    <option value="0">Tidak Aktif</option>
                                </select>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="fw-bold mb-1">Alamat Lengkap</label>
                                <textarea class="form-control" id="address" name="address" rows="3"
                                    placeholder="Alamat lengkap supplier..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary rounded-pill px-4"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4" id="btn-save">
                            <i class="fa fa-save"></i> Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        var table; // var, not let: Turbo re-runs this script on every visit to this page

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            loadData();

            // Submit Form Simpan/Update
            $('#form-supplier').on('submit', function(e) {
                e.preventDefault();
                let id = $('#supplier_id').val();
                let url = id ? `{{ url('supplier/update') }}/${id}` :
                    `{{ url('supplier/store') }}`;

                let btn = $('#btn-save');
                let originalText = btn.html();
                btn.html('<span class="spinner-border spinner-border-sm"></span> Menyimpan...').prop(
                    'disabled', true);

                $.ajax({
                    url: url,
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#modal-supplier').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        loadData(); // Reload Table
                    },
                    error: function(xhr) {
                        let errorMsg = xhr.responseJSON?.message || "Terjadi kesalahan.";
                        Swal.fire('Error', errorMsg, 'error');
                    },
                    complete: function() {
                        btn.html(originalText).prop('disabled', false);
                    }
                });
            });
        });

        var loadData = () => {
            let filterData = $('#form-filter').serialize();

            table = $('#supplier-table').DataTable({
                responsive: true,
                searching: false,
                destroy: true,
                lengthChange: false,
                pageLength: 10,
                ajax: {
                    url: `{{ url('supplier/get-data') }}?${filterData}`,
                    type: 'GET'
                },
                columns: [{
                        data: null,
                        className: 'text-center',
                        render: (data, type, row, meta) => meta.row + 1
                    },
                    {
                        data: 'name',
                        className: 'fw-bold text-primary'
                    },
                    {
                        data: 'phone',
                        render: data => data || '-'
                    },
                    {
                        data: 'contact_person',
                        render: data => data || '-'
                    },
                    {
                        data: 'address',
                        render: data => data || '-'
                    },
                    {
                        data: 'is_active',
                        className: 'text-center',
                        render: data => data == 1 ?
                            '<span class="badge bg-success">Aktif</span>' :
                            '<span class="badge bg-danger">Nonaktif</span>'
                    },
                    {
                        data: null,
                        className: 'text-center',
                        sortable: false,
                        render: function(data, type, row) {
                            return `
                                <button class="btn btn-sm btn-warning text-white rounded-pill" onclick="editData(${row.id})" title="Edit">
                                    <i class="iconoir-edit-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-danger rounded-pill" onclick="hapus(${row.id})" title="Hapus">
                                    <i class="iconoir-trash"></i>
                                </button>
                            `;
                        }
                    }
                ]
            });
        };

        var addData = () => {
            $('#form-supplier')[0].reset();
            $('#supplier_id').val('');
            $('#modalSupplierLabel').text('Tambah Supplier Baru');
            $('#modal-supplier').modal('show');
        };

        var editData = (id) => {
            $('#form-supplier')[0].reset();
            $.ajax({
                url: `{{ url('supplier/show') }}/${id}`,
                type: 'GET',
                success: function(response) {
                    let data = response.data;
                    $('#supplier_id').val(data.id);
                    $('#name').val(data.name);
                    $('#phone').val(data.phone);
                    $('#email').val(data.email);
                    $('#contact_person').val(data.contact_person);
                    $('#address').val(data.address);
                    $('#is_active').val(data.is_active);

                    $('#modalSupplierLabel').text('Edit Supplier');
                    $('#modal-supplier').modal('show');
                },
                error: function() {
                    Swal.fire('Error', 'Gagal mengambil data supplier', 'error');
                }
            });
        };

        var hapus = (id) => {
            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Data supplier ini akan dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ url('supplier/delete') }}/${id}`,
                        type: 'POST',

                        success: function(response) {
                            Swal.fire('Terhapus!', response.message, 'success');
                            loadData();
                        },
                        error: function() {
                            Swal.fire('Error', 'Gagal menghapus data.', 'error');
                        }
                    });
                }
            });
        };
    </script>
@endsection
