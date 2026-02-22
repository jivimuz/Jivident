@extends('layout/main')
@section('style')
@endsection

@section('content')
    @php
        $breadcrumbItems = [
            'title' => 'Master ICD',
            'items' => [['name' => 'Master', 'active' => false], ['name' => 'ICD', 'active' => true]],
        ];
    @endphp

    {{-- Ganti ini sesuai komponen/include breadcrumb kamu di Laravel --}}
    {{-- Contoh 1: --}}
    {{-- @include('components.content-header-breadcrumb', $breadcrumbItems) --}}

    {{-- Contoh 2 (jika pakai component): --}}
    {{-- <x-content-header-breadcrumb :title="$breadcrumbItems['title']" :items="$breadcrumbItems['items']" /> --}}

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-6">
                <div class="card" data-iq-gsap="onStart" data-iq-opacity="0" data-iq-position-y="-40" data-iq-duration=".6"
                    data-iq-delay=".4" data-iq-trigger="scroll" data-iq-ease="none">
                    <div class="card-header">
                        <a onclick="addICDIX()" class="btn btn-primary btn-sm float-end">
                            <i class="fa fa-plus"></i>
                        </a>
                        <h4 class="card-title">ICD IX</h4>
                    </div>

                    <div class="card-body">
                        <form class="row" id="cari">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="col-form-label col-md-12">Kode</label>
                                    <input class="form-control" type="text" name="kode" placeholder="Kode">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="col-form-label col-md-12">Nama ICDX</label>
                                    <input class="form-control" type="text" name="nama" placeholder="Nama">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="col-form-label col-md-2">Jml</label>
                                    <input class="form-control" type="text" name="numrows" placeholder="50"
                                        value="50">
                                </div>
                            </div>
                            <div class="col-md-3 pt-4">
                                <button type="button"
                                    class="waves-effect waves-light btn btn-primary btn-social-icon btn-bitbucket mt-27"
                                    onclick="cari()">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table id="icdix-table" class="table table-striped" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode</th>
                                        <th>Nama</th>
                                        <th>-</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    {{-- <div class="card-footer">
                        Footer
                    </div> --}}
                </div>
            </div>

            {{-- ICD X --}}
            <div class="col-md-6">
                <div class="card" data-iq-gsap="onStart" data-iq-opacity="0" data-iq-position-y="-40"
                    data-iq-duration=".6" data-iq-delay=".4" data-iq-trigger="scroll" data-iq-ease="none">
                    <div class="card-header">
                        <a onclick="addICDX()" class="btn btn-primary btn-sm float-end">
                            <i class="fa fa-plus"></i>
                        </a>
                        <h4 class="card-title">ICD X</h4>
                    </div>

                    <div class="card-body">
                        <form class="row" id="cari2">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="col-form-label col-md-12">Kode</label>
                                    <input class="form-control" type="text" name="kode" placeholder="Kode">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="col-form-label col-md-12">Nama ICDX</label>
                                    <input class="form-control" type="text" name="nama" placeholder="Nama">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="col-form-label col-md-2">Jml</label>
                                    <input class="form-control" type="text" name="numrows" placeholder="50"
                                        value="50">
                                </div>
                            </div>
                            <div class="col-md-3 p-4">
                                <button type="button"
                                    class="waves-effect waves-light btn btn-primary btn-social-icon btn-bitbucket mt-27"
                                    onclick="cari2()">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table id="icdx-table" class="table table-striped" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode</th>
                                        <th>Nama</th>
                                        <th>-</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    {{-- <div class="card-footer">
                        Footer
                    </div> --}}
                </div>
            </div>
        </div>

        <div class="modal fade" id="thisModal" tabindex="-1">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content p-4">
                    <div class="card-header with-border">
                        <h4 class="card-title" id="card-title"></h4>
                        <a onclick="tutup()" class="float-end"><i class="fa fa-times"></i></a>
                    </div>
                    <div id="modalContainer"></div>
                    <div class="modal-footer">
                        <div class="row">
                            <div class="col-lg-12"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script')
    <script>
        // ===== URL constants (Laravel) =====
        const URLS = {
            dataICDIX: @json(url('icd/dataICDIX')),
            dataICDX: @json(url('icd/dataICDX')),
            addICDIX: @json(url('icd/addICDIX')),
            addICDX: @json(url('icd/addICDX')),
            showICDBase: @json(url('icd/showICD')),
            editICDIXBase: @json(url('icd/editICDIX')),
            editICDXBase: @json(url('icd/editICDX')),
            deleteICDBase: @json(url('icd/deleteICD')),
        };

        $(document).ready(function() {
            // Kalau pakai AJAX POST/DELETE di Laravel, pastikan CSRF header terpasang
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            showICDIX();
            showICDX();
        });

        const cari = () => showICDIX();
        const cari2 = () => showICDX();

        function tutup() {
            $('#thisModal').modal('hide');
        }

        function showToastError(e) {
            let textError = 'Terjadi kesalahan';

            if (e?.responseJSON?.messages) {
                if (typeof e.responseJSON.messages === 'object') {
                    textError = '';
                    $.each(e.responseJSON.messages, function(key, value) {
                        textError += `${value}<br>`;
                    });
                } else {
                    textError = e.responseJSON.messages;
                }
            } else if (e?.responseJSON?.message) {
                textError = e.responseJSON.message;
            }



            Toast.fire({
                title: textError,
                icon: 'error',
            });
        }

        function showToastSuccess(message) {
            Toast.fire({
                title: message,
                icon: 'success',
            });
        }

        let rowSelected = "";

        const showICDIX = () => {
            const filterData = $('#cari').serialize();
            let no = 1;

            const columns = [{
                    data: 'code',
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                    orderable: false, // [TAMBAH] nonaktifkan sorting untuk nomor
                    searchable: false, // [TAMBAH] nonaktifkan search untuk nomor
                    width: '10',
                },
                {
                    data: 'code'
                },
                {
                    data: 'name'
                },
                {
                    data: 'code',
                    sortable: false,
                    render: function(data, type, full, meta) {
                        return `
                        <a onclick="showICD(9, ${full.id})"><button class="btn rounded-pill btn-outline-info btn-sm"><i class="fa fa-eye"></i></button></a>
                        <a onclick="editICD(9, ${full.id})"><button class="btn rounded-pill btn-outline-warning btn-sm"><i class="fa fa-edit"></i></button></a>
                        <a onclick="deleteICD(9, ${full.id})"><button class="btn rounded-pill btn-outline-danger btn-sm"><i class="fa fa-trash"></i></button></a>
                    `;
                    }
                },
            ];

            $('#icdix-table').DataTable({
                responsive: true,
                searching: false,
                destroy: true,
                lengthChange: false,
                processing: true,
                serverSide: true,
                ajax: {
                    url: `${URLS.dataICDIX}?${filterData}`,
                },
                columns: columns
            });
        };

        const showICDX = () => {
            const filterData = $('#cari2').serialize();
            let no = 1;

            const columns = [{
                    data: 'code',
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                    orderable: false, // [TAMBAH] nonaktifkan sorting untuk nomor
                    searchable: false, // [TAMBAH] nonaktifkan search untuk nomor
                    width: '10',
                },
                {
                    data: 'code'
                },
                {
                    data: 'name'
                },
                {
                    data: 'code',
                    sortable: false,
                    render: function(data, type, full, meta) {
                        return `
                        <a onclick="showICD(10, ${full.id})"><button class="btn rounded-pill btn-outline-info btn-sm"><i class="fa fa-eye"></i></button></a>
                        <a onclick="editICD(10, ${full.id})"><button class="btn rounded-pill btn-outline-warning btn-sm"><i class="fa fa-edit"></i></button></a>
                        <a onclick="deleteICD(10, ${full.id})"><button class="btn rounded-pill btn-outline-danger btn-sm"><i class="fa fa-trash"></i></button></a>
                    `;
                    }
                },
            ];

            $('#icdx-table').DataTable({
                responsive: true,
                searching: false,
                destroy: true,
                lengthChange: false,
                processing: true,
                serverSide: true,
                ajax: {
                    url: `${URLS.dataICDX}?${filterData}`,
                },
                columns: columns
            });
        };

        function addICDIX() {
            $.ajax({
                type: 'GET',
                url: URLS.addICDIX,
                success: function(response) {
                    $('#card-title').html('Tambah ICDIX');
                    $('#modalContainer').html(response);
                    $('#thisModal').modal('show');
                },
                error: function(e) {
                    showToastError(e);
                }
            });
        }

        function addICDX() {
            $.ajax({
                type: 'GET',
                url: URLS.addICDX,
                success: function(response) {
                    $('#card-title').html('Tambah ICDX');
                    $('#modalContainer').html(response);
                    $('#thisModal').modal('show');
                },
                error: function(e) {
                    showToastError(e);
                }
            });
        }

        function showICD(tipe, id) {
            $.ajax({
                type: 'GET',
                url: `${URLS.showICDBase}/${id}/${tipe}`,
                success: function(response) {
                    $('#card-title').html('SHOW ICD');
                    $('#modalContainer').html(response);
                    $('#thisModal').modal('show');
                },
                error: function(e) {
                    showToastError(e);
                }
            });
        }

        function editICD(tipe, id) {
            let link = '';
            if (tipe == 9) {
                link = `${URLS.editICDIXBase}/${id}`;
            } else if (tipe == 10) {
                link = `${URLS.editICDXBase}/${id}`;
            }

            $.ajax({
                type: 'GET',
                url: link,
                success: function(response) {
                    $('#card-title').html('Edit ICD ' + tipe);
                    $('#modalContainer').html(response);
                    $('#thisModal').modal('show');
                },
                error: function(e) {
                    showToastError(e);
                }
            });
        }

        const deleteICD = (tipe, id) => {
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "untuk menghapus data tersebut!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Iya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    const route = `${URLS.deleteICDBase}/${id}/${tipe}`;

                    $.ajax({
                        url: route,
                        type: 'POST',
                        data: {
                            _method: 'DELETE',
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(res) {
                            showToastSuccess(res.messages || 'Data berhasil dihapus');
                            if (tipe == 10) {
                                showICDX();
                            } else {
                                showICDIX();
                            }
                        },
                        error: function(e) {
                            showToastError(e);
                        }
                    });
                }
            });
        };

        // Submit form create/update dari modal (delegated karena form di-load via AJAX)
        $(document).on('submit', '#formICD', function(e) {
            e.preventDefault();

            const $form = $(this);
            const action = $form.attr('action');
            const method = ($form.find('input[name="_method"]').val() || $form.attr('method') || 'POST')
                .toUpperCase();

            // reset error text
            $form.find('.error-text').html('');
            $('#btnSaveICD').prop('disabled', true).text('Menyimpan...');

            $.ajax({
                url: action,
                type: 'POST', // tetap POST, method spoof via _method untuk PUT
                data: $form.serialize(),
                success: function(res) {
                    $('#btnSaveICD').prop('disabled', false).text('Simpan');
                    $('#thisModal').modal('hide');

                    showToastSuccess(res.messages || 'Berhasil disimpan');

                    // refresh table sesuai judul modal / tipe yang dibuka
                    const title = ($('#card-title').text() || '').toLowerCase();
                    if (title.includes('icd x') && !title.includes('ix')) {
                        showICDX();
                    } else {
                        // default ICD IX
                        // kalau edit/show ICD X kadang title = "Edit ICD 10", fallback cek text
                        if (title.includes('10')) {
                            showICDX();
                        } else {
                            showICDIX();
                        }
                    }
                },
                error: function(xhr) {
                    $('#btnSaveICD').prop('disabled', false).text(
                        ($form.find('input[name="_method"]').length ? 'Update' : 'Simpan')
                    );

                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON?.errors || {};
                        Object.keys(errors).forEach(function(field) {
                            $form.find(`.error-text[data-field="${field}"]`).html(errors[field][
                                0
                            ]);
                        });

                        // optional toast juga
                        showToastError({
                            responseJSON: {
                                message: 'Validasi gagal, cek input.'
                            }
                        });
                        return;
                    }

                    showToastError(xhr);
                }
            });
        });
    </script>
@endsection
