@extends('layout/main')


@section('content')
    <div class="content-inner mt-5 py-0">
        <div class="card">
            <div class="card-header">
                <div class="float-end">
                    <a class="btn btn-sm btn-outline-primary rounded-pill" href="{{ url('product/add') }}">
                        + Add Product
                    </a>
                    <button class="btn btn-sm btn-outline-success rounded-pill ms-2" id="btnCategory">
                        Categories
                    </button>
                    <button class="btn btn-sm btn-outline-dark rounded-pill ms-2" id="btnImport">
                        Import CSV
                    </button>
                </div>

                <h4 class="card-title">Products</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table  table-hover" id="main-table" style="width: 100% !important">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Satuan</th>
                                <th>Unit</th>
                                <th>Total QTY</th>
                                <th>Harga Beli</th>
                                <th>HPP</th>
                                <th>Harga Jual</th>
                                <th>Margin</th>
                                <th>-</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalCategory">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">

                    <h5>Product Categories</h5>
                    <a onclick="closeModal('modalCategory')" class="btn text-lg">x</a>
                </div>

                <div class="modal-body">
                    {{-- FORM --}}
                    <form id="formCategory" class="mb-3 p-2">
                        <input type="hidden" id="cat_id" name="id">
                        <div class="row">
                            <div class="col-md-4">
                                <input name="name" class="form-control" placeholder="Category name" required>
                            </div>
                            <div class="col-md-6">
                                <textarea name="description" class="form-control" placeholder="Description"></textarea>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-outline-success btn-sm"> <svg width="18" viewBox="0 0 24 24"
                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M21.419 15.732C21.419 19.31 19.31 21.419 15.732 21.419H7.95C4.363 21.419 2.25 19.31 2.25 15.732V7.932C2.25 4.359 3.564 2.25 7.143 2.25H9.143C9.861 2.251 10.537 2.588 10.967 3.163L11.88 4.377C12.312 4.951 12.988 5.289 13.706 5.29H16.536C20.123 5.29 21.447 7.116 21.447 10.767L21.419 15.732Z"
                                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round"></path>
                                        <path d="M7.48145 14.4629H16.2164" stroke="currentColor" stroke-width="1.5"
                                            stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg> </button>
                            </div>
                        </div>
                    </form>

                    {{-- TABLE --}}
                    <table class="table table-sm" id="cat-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th width="120">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="modalImport">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Import Produk dari CSV</h5>
                    <a onclick="closeModal('modalImport')" class="btn text-lg">x</a>
                </div>
                <div class="modal-body">
                    <p>
                        <a href="{{ url('product/import/template') }}" class="btn btn-sm btn-outline-primary">
                            Download Template
                        </a>
                    </p>
                    <form id="formImport">
                        <div class="mb-3">
                            <input type="file" class="form-control" name="file" id="importFile" accept=".csv">
                        </div>
                        <button type="submit" class="btn btn-success">Upload & Import</button>
                    </form>

                    <div id="importResult" class="mt-3" style="display:none">
                        <div id="importSummary" class="alert"></div>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Baris</th>
                                    <th>Kode</th>
                                    <th>Status</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody id="importDetailBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')
    <script>
        $(document).ready(function() {
            toggleStock();

            $('#is_stockneeded').on('change', function() {
                toggleStock();
            });

            function toggleStock() {
                if ($('#is_stockneeded').is(':checked')) {
                    $('#stock').show();
                } else {
                    $('#stock').val(0).hide();
                }
            }
        });

        let table = $('#main-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ url('product/getList') }}",
            columns: [{
                    data: 'name'
                },
                {
                    data: 'category_name'
                },
                {
                    data: 'satuan_name',
                    render: function(data, b, c) {
                        return `${c.strength} ${data}`
                    },

                },

                {
                    data: 'unit_name'
                },
                {
                    data: 'total_qty'
                },


                {
                    data: 'harga_beli',
                    render: function(data, b, c) {
                        return parseFloat(data ?? 0).toLocaleString("id-ID", {
                            style: "currency",
                            currency: "IDR"
                        })
                    },

                },
                {
                    data: 'hpp',
                    render: function(data, b, c) {
                        return parseFloat(data ?? 0).toLocaleString("id-ID", {
                            style: "currency",
                            currency: "IDR"
                        })
                    },

                },
                {
                    data: 'harga_jual',
                    render: function(data, b, c) {
                        return parseFloat(data ?? 0).toLocaleString("id-ID", {
                            style: "currency",
                            currency: "IDR"
                        })
                    },

                },
                {
                    data: 'harga_beli',
                    render: function(data, b, c) {
                        let hpp = parseFloat(c.hpp) || 0;
                        let hargaJual = parseFloat(c.harga_jual) || 0;
                        let marginText = '';

                        if (hpp > 0 && hargaJual >= hpp) {
                            let profit = hargaJual - hpp;
                            let marginPercent = (profit / hpp) * 100;
                            marginText = marginPercent.toFixed(2) + '%'
                        } else if (hargaJual < hpp && hargaJual > 0) {
                            marginText = 'Rugi!'
                        } else {
                            marginText = '0%'
                        }

                        return marginText
                    },

                },
                {
                    data: "id",
                    render: function(data, b, c) {
                        return `<a class="btn btn-xs btn-primary rounded-pill" href="{{ url('product/edit') }}/${btoa(btoa(data))}">
                            <svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M11.4925 2.78906H7.75349C4.67849 2.78906 2.75049 4.96606 2.75049 8.04806V16.3621C2.75049 19.4441 4.66949 21.6211 7.75349 21.6211H16.5775C19.6625 21.6211 21.5815 19.4441 21.5815 16.3621V12.3341" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M8.82812 10.921L16.3011 3.44799C17.2321 2.51799 18.7411 2.51799 19.6721 3.44799L20.8891 4.66499C21.8201 5.59599 21.8201 7.10599 20.8891 8.03599L13.3801 15.545C12.9731 15.952 12.4211 16.181 11.8451 16.181H8.09912L8.19312 12.401C8.20712 11.845 8.43412 11.315 8.82812 10.921Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M15.1655 4.60254L19.7315 9.16854" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg></a>

                                <button class="btn btn-xs btn-danger delete rounded-pill" data-id="${btoa(btoa(data))}">
                                    <svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M19.3248 9.46826C19.3248 9.46826 18.7818 16.2033 18.4668 19.0403C18.3168 20.3953 17.4798 21.1893 16.1088 21.2143C13.4998 21.2613 10.8878 21.2643 8.27979 21.2093C6.96079 21.1823 6.13779 20.3783 5.99079 19.0473C5.67379 16.1853 5.13379 9.46826 5.13379 9.46826" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M20.708 6.23975H3.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M17.4406 6.23973C16.6556 6.23973 15.9796 5.68473 15.8256 4.91573L15.5826 3.69973C15.4326 3.13873 14.9246 2.75073 14.3456 2.75073H10.1126C9.53358 2.75073 9.02558 3.13873 8.87558 3.69973L8.63258 4.91573C8.47858 5.68473 7.80258 6.23973 7.01758 6.23973" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                        </svg>
                                        </button>`;


                    },
                },

            ]
        });


        $('main').on('click', '.delete', function() {
            let id = $(this).data('id');

            Swal.fire({
                title: 'Delete product?',
                text: 'Product akan dihapus permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete!',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ url('product/delete') }}/${id}`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function() {
                            table.ajax.reload();

                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: 'Product berhasil dihapus.',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops!',
                                text: 'Gagal menghapus product.'
                            });
                        }
                    });
                }
            });
        });


        let isSubmitting = false;

        $('#btnCategory').click(() => {
            $('#modalCategory').modal('show');
            catTable();
        });

        function catTable() {

            var tabled = $('#cat-table').DataTable({
                searching: true,
                destroy: true,
                lengthChange: false,
                responsive: true,
                // pageLength: 2,
                ajax: {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    url: "{{ url('categories/getList') }}",
                    type: "GET",
                    data: {}

                },
                columns: [{
                        data: 'name',
                        render: function(data, b, c) {
                            return `<span id="cat_${c.id}" class="cats">${data}</span>`

                        },

                    },
                    {
                        data: 'description',
                        render: function(data, b, c) {
                            return `<span id="catd_${c.id}" class="cats">${data}</span>`

                        },
                    },
                    {
                        data: 'name',
                        render: function(data, b, c) {
                            return `<button class="btn btn-xs btn-primary editCat" data-id="${c.id}"><i class="fa fa-pencil"></i></button>
                            <button class="btn btn-xs btn-danger deleteCat" data-id="${c.id}"><i class="fa fa-trash"></i></button>`

                        },

                    },
                ]
                // initComplete: function(data) {}
            });

        }

        // CREATE & UPDATE
        $('#formCategory').submit(function(e) {
            e.preventDefault();

            let id = $('#cat_id').val();
            let url = id ? `{{ url('categories/update') }}/${id}` :
                `{{ url('categories/store') }}`;

            $.ajax({
                url: url,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: $(this).serialize(),
                success: function(data) {
                    $('#formCategory')[0].reset();
                    $('#cat_id').val('');
                    // refresh dropdown category product
                    catTable()
                    refreshCategoryDropdown();
                    Toast.fire({
                        icon: 'success',
                        title: 'Category updated',
                    });
                },
                error: function(xhr, status, error) {
                    Toast.fire({
                        icon: "error",
                        title: JSON.parse(xhr.responseText).error
                    });

                }
            });

        });

        // EDIT
        $('main').on('click', '.editCat', function() {
            $('.cats').removeClass('text-primary');
            $.get(`{{ url('categories/edit') }}/${$(this).data('id')}`, function(res) {
                $('#cat_' + res.id).addClass('text-primary');
                $('#catd_' + res.id).addClass('text-primary');
                $('#cat_id').val(res.id);
                $('[name=name]').val(res.name);
                $('[name=description]').val(res.description);
            });
        });

        // DELETE
        $('main').on('click', '.deleteCat', function() {
            let id = $(this).data('id');

            Swal.fire({
                title: 'Delete category?',
                text: 'Data yang dihapus tidak bisa dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete!',
                cancelButtonText: 'Cancel',
                reverseButtons: false,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ url('categories/delete') }}/${id}`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function() {
                            // reload datatable kategori
                            catTable();

                            // refresh dropdown product
                            refreshCategoryDropdown();

                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: 'Category berhasil dihapus.',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops!',
                                text: 'Gagal menghapus category.'
                            });
                        }
                    });
                }
            });
        });


        // IMPORT CSV
        $('#btnImport').click(() => {
            $('#importResult').hide();
            $('#formImport')[0].reset();
            $('#modalImport').modal('show');
        });

        $('#formImport').submit(function(e) {
            e.preventDefault();

            var fileInput = $('#importFile')[0];
            if (!fileInput.files.length) {
                return Toast.fire({
                    icon: 'warning',
                    title: 'Pilih file CSV dulu'
                });
            }

            var formData = new FormData();
            formData.append('file', fileInput.files[0]);

            $.ajax({
                url: "{{ url('product/import') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: formData,
                contentType: false,
                processData: false,
                success: function(res) {
                    var s = res.summary;
                    $('#importSummary')
                        .removeClass('alert-success alert-warning')
                        .addClass(s.failed > 0 ? 'alert-warning' : 'alert-success')
                        .text(`Total ${s.total} baris — ${s.success} berhasil, ${s.failed} gagal.`);

                    var rows = '';
                    res.details.forEach(function(d) {
                        var badge = d.status === 'success' ?
                            '<span class="badge bg-success">OK</span>' :
                            '<span class="badge bg-danger">Gagal</span>';
                        rows += `<tr>
                            <td>${d.row}</td>
                            <td>${d.code}</td>
                            <td>${badge}</td>
                            <td>${d.message ?? '-'}</td>
                        </tr>`;
                    });
                    $('#importDetailBody').html(rows);
                    $('#importResult').show();

                    if (s.success > 0) {
                        table.ajax.reload();
                    }
                },
                error: function(xhr) {
                    Toast.fire({
                        icon: 'error',
                        title: JSON.parse(xhr.responseText).error ?? JSON.parse(xhr.responseText).message ?? 'Import gagal'
                    });
                }
            });
        });

        // REFRESH SELECT CATEGORY
        function refreshCategoryDropdown() {
            $.get("{{ url('categories/getList') }}", function(res) {
                let opt = '<option value=""> - </option>';
                res.data.forEach(r => {
                    opt += `<option value="${r.id}">${r.name}</option>`;
                });
                $('select[name=category_id]').html(opt);
            });
        }
    </script>
@endsection
