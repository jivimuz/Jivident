@extends('layout/main')

@section('content')
    <div class="content-inner mt-5 py-0">
        <div class="row">
            <div class="col-md-8 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Stock Opname / Cycle Count</h4>
                        <small class="text-muted">Hitung stok fisik produk, lalu klik "Opname" untuk mencocokkan
                            dengan stok sistem. Selisihnya otomatis tercatat sebagai mutasi stok.</small>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="opnameTable" style="width: 100% !important">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Produk</th>
                                        <th>Kategori</th>
                                        <th>Satuan</th>
                                        <th>Stok Sistem</th>
                                        <th>-</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Riwayat Opname Terakhir</h4>
                        <small class="text-muted">50 koreksi terakhir, terbaru di atas.</small>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm" id="opnameHistoryTable">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Selisih</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody id="opnameHistoryBody"></tbody>
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
            var table = $('#opnameTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('stock-opname/getList') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                },
                columns: [
                    {
                        data: 'code'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'category_name',
                        defaultContent: '-'
                    },
                    {
                        data: 'satuan_name',
                        defaultContent: '-'
                    },
                    {
                        data: 'qty_sistem'
                    },
                    {
                        data: 'id',
                        orderable: false,
                        render: function(data, type, row) {
                            return `<button type="button" class="btn btn-xs btn-outline-primary rounded-pill" onclick="doOpname(${data}, ${row.qty_sistem})">Opname</button>`;
                        }
                    }
                ]
            });

            window.doOpname = function(productId, qtySistem) {
                Swal.fire({
                    title: 'Opname Produk',
                    html: `Stok sistem saat ini: <b>${qtySistem}</b>`,
                    input: 'number',
                    inputLabel: 'Qty Fisik (hasil hitung)',
                    inputValue: qtySistem,
                    inputAttributes: {
                        min: 0,
                        step: 1
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Simpan',
                    inputValidator: function(value) {
                        if (value === '' || value === null || parseFloat(value) < 0) {
                            return 'Qty fisik wajib diisi dan tidak boleh negatif';
                        }
                    }
                }).then(function(result) {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: "{{ url('stock-opname/adjust') }}",
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            product_id: productId,
                            qty_fisik: result.value
                        },
                        success: function(res) {
                            var delta = parseFloat(res.delta ?? 0);
                            var msg = delta === 0 ?
                                'Tidak ada selisih stok' :
                                (delta > 0 ?
                                    `Stok disesuaikan: +${delta}` :
                                    `Stok disesuaikan: ${delta}`);

                            Toast.fire({
                                icon: 'success',
                                title: msg
                            });
                            table.ajax.reload(null, false);
                            loadHistory();
                        },
                        error: function(xhr) {
                            Toast.fire({
                                icon: 'error',
                                title: xhr.responseJSON?.error ?? 'Gagal menyimpan opname'
                            });
                        }
                    });
                });
            };

            function loadHistory() {
                $.ajax({
                    url: "{{ url('stock-opname/getHistory') }}",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        var rows = '';
                        res.data.forEach(function(d) {
                            var sign = d.type === 'in' ? '+' : '-';
                            var cls = d.type === 'in' ? 'text-success' : 'text-danger';
                            rows += `<tr>
                                <td>${d.product_name}</td>
                                <td class="${cls}">${sign}${d.qty}</td>
                                <td>${new Date(d.created_at).toLocaleString('id-ID')}</td>
                            </tr>`;
                        });
                        $('#opnameHistoryBody').html(rows ||
                            '<tr><td colspan="3" class="text-muted text-center">Belum ada riwayat opname</td></tr>'
                        );
                    }
                });
            }

            loadHistory();
        });
    </script>
@endsection
