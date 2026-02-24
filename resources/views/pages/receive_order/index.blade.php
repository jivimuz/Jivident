@extends('layout/main')

@section('style')
    <style>
        .table-custom th {
            font-size: 14px;
            background-color: rgba(58, 87, 232, 0.05);
            color: #3a57e8;
            font-weight: 600;
        }

        .table-custom tr {
            border-bottom: 1px solid black !important
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
                        Daftar Penerimaan Barang (Receive Order)
                    </span>
                    <a href="{{ url('receive_order/create') }}"
                        class="btn btn-primary rounded-pill fw-bold d-flex align-items-center flex-shrink-0 px-4">
                        <i class="iconoir-plus-circle me-1"></i> Tambah Baru
                    </a>
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
                                <label class="fw-bold mb-1 text-muted" style="font-size: 13px;">No. Faktur</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="iconoir-search"></i></span>
                                    <input type="text" name="no_faktur" id="filter_no_faktur" class="form-control"
                                        placeholder="Cari No. Faktur...">
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
                            <table class="table table-hover table-bordered table-striped table-custom" id="receive-table"
                                style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">No</th>
                                        <th width="15%">Tanggal Faktur</th>
                                        <th width="20%">No. Faktur</th>
                                        <th width="30%">Supplier (PBF)</th>
                                        <th width="20%" class="text-end">Grand Total</th>
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
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // Setup CSRF Token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Load data pertama kali
            loadData();
        });

        const formatRupiah = (angka) => {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(angka);
        };

        const formatDate = (dateString) => {
            if (!dateString) return '-';
            const options = {
                year: 'numeric',
                month: 'short',
                day: '2-digit'
            };
            return new Date(dateString).toLocaleDateString('id-ID', options);
        };

        const loadData = () => {
            let filterData = $('#form-filter').serialize();
            let no = 1;

            const columns = [{
                    data: null,
                    className: 'text-center',
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    data: 'tgl_faktur',
                    render: function(data) {
                        return formatDate(data);
                    }
                },
                {
                    data: 'no_faktur',
                    className: 'fw-bold'
                },
                {
                    data: 'supplier_name'
                },
                {
                    data: 'grand_total',
                    className: 'text-end fw-bold text-primary',
                    render: function(data) {
                        return formatRupiah(data);
                    }
                },
                {
                    data: null,
                    className: 'text-center',
                    sortable: false,
                    render: function(data, type, row) {
                        let a = ''
                        if (parseInt(row.is_processed) == 0) {
                            a = ` <a class="btn btn-sm btn-outline-success rounded-pill" style="margin-top:2px" onclick="approveRO('${btoa(btoa(row.id))}')" title="Approve">
                                <i class="iconoir-check"></i>
                            </a>
                             <a class="btn btn-sm btn-outline-warning rounded-pill" style="margin-top:2px" href="{{ url('receive_order') }}/edit/${btoa(btoa(row.id))}" title="Detail">
                                <i class="iconoir-edit-pencil"></i>
                            </a>
                            <button class="btn btn-sm btn-outline-danger rounded-pill" style="margin-top:2px" onclick="hapusRO(${row.id})" title="Hapus">
                                <i class="iconoir-trash"></i>
                            </button>`
                        } else {
                            a = `   <a class="btn btn-sm btn-outline-info rounded-pill" style="margin-top:2px" href="{{ url('receive_order') }}/show/${btoa(btoa(row.id))}" title="Detail">
                                <i class="iconoir-eye"></i>
                            </a>`
                        }
                        return a;
                    }
                }
            ];

            $('#receive-table').DataTable({
                responsive: true,
                searching: false, // Kita gunakan form pencarian kustom di atas
                destroy: true,
                lengthChange: false,
                pageLength: 10,
                processing: true,
                serverSide: true,
                ajax: {
                    url: `{{ url('receive_order/get-data') }}?${filterData}`,
                    type: 'GET'
                },
                columns: columns,
                language: {
                    emptyTable: "Tidak ada data penerimaan barang ditemukan",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    paginate: {
                        previous: "<i class='iconoir-nav-arrow-left'></i>",
                        next: "<i class='iconoir-nav-arrow-right'></i>"
                    }
                }
            });
        };

        const approveRO = (idRO) => {

            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Data penerimaan barang ini akan diapprove dan akan melakukan pembayaran dalam sistem!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Approve',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ url('receive_order/approve') }}/${idRO}`, // Sesuaikan Route Delete Anda
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Disetujui!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                loadData()
                                // window.location.href =
                                //     "{{ url('receive_order') }}"; // Kembali ke daftar list RO
                            });
                        },
                        error: function(xhr) {
                            let errorMsg = xhr.responseJSON?.message ||
                                'Gagal menghapus data transaksi.';
                            Swal.fire('Error', errorMsg, 'error');
                        }
                    });
                }
            });
        };

        const hapusRO = (idRO) => {

            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Data penerimaan barang ini akan dihapus permanen beserta riwayat stok masuknya!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus Data!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ url('receive_order/delete') }}/${idRO}`, // Sesuaikan Route Delete Anda
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                loadData()
                                // window.location.href =
                                //     "{{ url('receive_order') }}"; // Kembali ke daftar list RO
                            });
                        },
                        error: function(xhr) {
                            let errorMsg = xhr.responseJSON?.message ||
                                'Gagal menghapus data transaksi.';
                            Swal.fire('Error', errorMsg, 'error');
                        }
                    });
                }
            });
        };
    </script>
@endsection
