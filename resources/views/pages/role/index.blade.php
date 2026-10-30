@extends('layout/main')

@section('content')
    <div class="content-inner mt-5 py-0">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="float-end">
                            <a type="button" class="btn btn-primary rounded-pill" onclick="addModal()">+ Tambah
                                Role</a>
                        </div>
                        <h4 class="card-title">Role</h4>
                        <small>Template akses modul yang bisa dipakai ulang saat assign akses user</small>
                    </div>
                    <div class="card-body" style="padding-left: 40px; padding-right:40px">
                        <div class="app-datatable-default overflow-auto">
                            <table class="display app-data-table default-data-table table-hover" id="main-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Role</th>
                                        <th>Jumlah Modul</th>
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

    @include('component/modal-lg')
@endsection

@section('script')
    <script>
        var noD = 1; // var, not let: Turbo re-runs this script on every visit to this page
        var table;

        $(document).ready(function() {
            table = $('#main-table').DataTable({
                destroy: true,
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    url: "{{ url('roles/getList') }}",
                    type: "POST",
                },
                columns: [{
                        data: 'id',
                        render: function() {
                            return `${noD++}.`
                        },
                        className: 'text-center'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'total_modules'
                    },
                    {
                        data: 'id',
                        render: function(id) {
                            return `
                                <a class="btn btn-xs btn-outline-primary rounded-pill mt-2 ml-2" onclick="editModal(${id})">Edit</a>
                                <a class="btn btn-xs btn-outline-danger rounded-pill mt-2 ml-2" onclick="deleteRole(${id})">Hapus</a>
                            `
                        },
                        className: 'text-center'
                    },
                ],
                drawCallback: function() {
                    noD = 1
                }
            });
        });

        function addModal() {
            $.ajax({
                url: "{{ url('roles/add') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(data) {
                    $('#ThisModalLabel').html("Tambah Role")
                    $('#thisModalBody').html(data)
                    $('#ThisModal').modal('show')
                },
                error: function(xhr) {
                    Toast.fire({
                        icon: "error",
                        title: JSON.parse(xhr.responseText).error ?? 'Gagal memuat form'
                    });
                }
            });
        }

        function editModal(id) {
            $.ajax({
                url: "{{ url('roles/edit') }}/" + id,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(data) {
                    $('#ThisModalLabel').html("Edit Role")
                    $('#thisModalBody').html(data)
                    $('#ThisModal').modal('show')
                },
                error: function(xhr) {
                    Toast.fire({
                        icon: "error",
                        title: JSON.parse(xhr.responseText).error ?? 'Gagal memuat form'
                    });
                }
            });
        }

        function deleteRole(id) {
            Swal.fire({
                title: 'Hapus role ini?',
                text: "Role yang sudah dipakai sebagai template tidak akan mengubah akses user yang sudah di-set sebelumnya.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('roles/delete') }}/" + id,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function() {
                            Toast.fire({
                                icon: "success",
                                title: "Role dihapus"
                            });
                            table.ajax.reload();
                        },
                        error: function(xhr) {
                            Toast.fire({
                                icon: "error",
                                title: JSON.parse(xhr.responseText).error ?? 'Gagal menghapus'
                            });
                        }
                    });
                }
            });
        }
    </script>
@endsection
