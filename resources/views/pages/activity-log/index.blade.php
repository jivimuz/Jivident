@extends('layout/main')
@section('style')
    <style>
        pre.log-changes {
            white-space: pre-wrap;
            font-size: 11px;
            margin: 0;
            max-height: 150px;
            overflow-y: auto;
        }
    </style>
@endsection

@section('content')
    <div class="content-inner mt-5 py-0">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="float-end">
                            <input type="search" class="form-control" placeholder="Cari model/aksi..."
                                id="searchLog" style="width: 220px">
                        </div>
                        <h4 class="card-title">Activity Log</h4>
                        <small>Riwayat perubahan data pasien & keuangan</small>
                    </div>
                    <div class="card-body" style="padding-left: 40px; padding-right:40px">
                        <div class="app-datatable-default overflow-auto">
                            <table class="display app-data-table default-data-table table-hover" id="main-table">
                                <thead>
                                    <tr>
                                        <th>Waktu</th>
                                        <th>User</th>
                                        <th>Aksi</th>
                                        <th>Model</th>
                                        <th>ID</th>
                                        <th>Detail Perubahan</th>
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
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            var table = $('#main-table').DataTable({
                searching: false,
                destroy: true,
                lengthChange: false,
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    url: "{{ url('activity-log/getList') }}",
                    type: "POST",
                    data: function(d) {
                        d.search = {
                            value: $('#searchLog').val()
                        };
                    }
                },
                columns: [
                    {
                        data: 'created_at'
                    },
                    {
                        data: 'user'
                    },
                    {
                        data: 'action',
                        render: function(data) {
                            var cls = data === 'created' ? 'success' : (data === 'deleted' ?
                                'danger' : 'warning');
                            return `<span class="badge bg-${cls}">${data}</span>`;
                        }
                    },
                    {
                        data: 'model'
                    },
                    {
                        data: 'model_id'
                    },
                    {
                        data: 'changes',
                        render: function(data) {
                            return `<pre class="log-changes">${JSON.stringify(data, null, 1)}</pre>`;
                        }
                    },
                ],
            });

            $('#searchLog').on('keyup', function() {
                table.ajax.reload();
            });
        });
    </script>
@endsection
