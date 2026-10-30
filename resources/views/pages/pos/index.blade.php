@extends('layout/main')
@section('style')
    <style>
        #productResults {
            position: absolute;
            z-index: 10;
            width: 100%;
            max-height: 260px;
            overflow-y: auto;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 6px;
            display: none;
        }

        #productResults .item {
            padding: 8px 12px;
            cursor: pointer;
        }

        #productResults .item:hover {
            background: #f0f6ff;
        }

        .cart-row input.qty-input,
        .cart-row input.discount-input {
            width: 90px;
        }
    </style>
@endsection

@section('content')
    <div class="content-inner mt-5 py-0">
        <div class="row">
            <div class="col-md-7 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Kasir Apotek</h4>
                        <small>Cari produk, masukkan ke keranjang, lalu checkout</small>
                    </div>
                    <div class="card-body">
                        <div class="position-relative mb-3">
                            <input type="text" id="productSearch" class="form-control"
                                placeholder="Cari nama/kode produk...">
                            <div id="productResults"></div>
                        </div>

                        <table class="table table-sm" id="cartTable">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Qty</th>
                                    <th>Harga</th>
                                    <th>Diskon</th>
                                    <th>Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="cartBody">
                                <tr id="cartEmptyRow">
                                    <td colspan="6" class="text-center text-muted">Keranjang masih kosong</td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="text-end">
                            <div>Subtotal: <b id="sumGross">Rp 0</b></div>
                            <div>Diskon: <b id="sumDiscount">Rp 0</b></div>
                            <div class="fs-5">Total: <b id="sumNet">Rp 0</b></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-5 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Pembayaran</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Pembeli (opsional)</label>
                            <input type="text" class="form-control" id="buyerName">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No. HP Pembeli (opsional)</label>
                            <input type="text" class="form-control" id="buyerPhone">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Metode Pembayaran</label>
                            <select class="form-control" id="fkPayment">
                                @foreach ($payments as $p)
                                    <option value="{{ $p->id }}">{{ $p->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button class="btn btn-success w-100" id="checkoutBtn">Proses & Cetak Struk</button>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h4 class="card-title">Riwayat Penjualan</h4>
                    </div>
                    <div class="card-body">
                        <table class="display app-data-table table-hover" id="salesTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Waktu</th>
                                    <th>Pembeli</th>
                                    <th>Total</th>
                                    <th>Status</th>
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
        var cart = [];
        var salesTable;

        function formatRupiah(v) {
            return 'Rp ' + Number(v).toLocaleString('id-ID');
        }

        $('#productSearch').on('keyup', function() {
            var q = $(this).val();
            if (q.length < 2) {
                $('#productResults').hide();
                return;
            }
            $.ajax({
                url: "{{ url('pos/getProductList') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    q: q
                },
                success: function(res) {
                    var html = '';
                    res.data.forEach(function(p) {
                        html += `<div class="item" data-id="${p.id}" data-name="${p.name}" data-price="${p.harga_jual}">
                            ${p.name} <span class="text-muted">(stok: ${p.stock})</span> — ${formatRupiah(p.harga_jual)}
                        </div>`;
                    });
                    $('#productResults').html(html || '<div class="item text-muted">Tidak ditemukan</div>').show();
                }
            });
        });

        $('main').on('click', '#productResults .item', function() {
            var id = $(this).data('id');
            if (!id) return;
            var existing = cart.find(c => c.product_id === id);
            if (existing) {
                existing.qty += 1;
            } else {
                cart.push({
                    product_id: id,
                    name: $(this).data('name'),
                    price: parseFloat($(this).data('price')),
                    qty: 1,
                    discount: 0,
                });
            }
            $('#productResults').hide();
            $('#productSearch').val('');
            renderCart();
        });

        function renderCart() {
            if (cart.length === 0) {
                $('#cartBody').html('<tr id="cartEmptyRow"><td colspan="6" class="text-center text-muted">Keranjang masih kosong</td></tr>');
                updateSummary();
                return;
            }

            var html = '';
            cart.forEach(function(item, idx) {
                var subtotal = (item.qty * item.price) - item.discount;
                html += `<tr class="cart-row">
                    <td>${item.name}</td>
                    <td><input type="number" min="1" class="form-control qty-input" value="${item.qty}" onchange="updateCartField(${idx}, 'qty', this.value)"></td>
                    <td>${formatRupiah(item.price)}</td>
                    <td><input type="number" min="0" class="form-control discount-input" value="${item.discount}" onchange="updateCartField(${idx}, 'discount', this.value)"></td>
                    <td>${formatRupiah(subtotal)}</td>
                    <td><a class="btn btn-xs btn-outline-danger" onclick="removeCartItem(${idx})">&times;</a></td>
                </tr>`;
            });
            $('#cartBody').html(html);
            updateSummary();
        }

        function updateCartField(idx, field, value) {
            cart[idx][field] = parseFloat(value) || 0;
            renderCart();
        }

        function removeCartItem(idx) {
            cart.splice(idx, 1);
            renderCart();
        }

        function updateSummary() {
            var gross = cart.reduce((s, i) => s + (i.qty * i.price), 0);
            var discount = cart.reduce((s, i) => s + i.discount, 0);
            $('#sumGross').text(formatRupiah(gross));
            $('#sumDiscount').text(formatRupiah(discount));
            $('#sumNet').text(formatRupiah(gross - discount));
        }

        $('#checkoutBtn').on('click', function() {
            if (cart.length === 0) {
                return Toast.fire({
                    icon: 'warning',
                    title: 'Keranjang masih kosong'
                });
            }

            $.ajax({
                url: "{{ url('pos/checkout') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    fk_payment: $('#fkPayment').val(),
                    buyer_name: $('#buyerName').val(),
                    buyer_phone: $('#buyerPhone').val(),
                    items: cart.map(i => ({
                        product_id: i.product_id,
                        qty: i.qty,
                        discount: i.discount
                    })),
                },
                success: function(res) {
                    Toast.fire({
                        icon: 'success',
                        title: 'Transaksi berhasil: ' + res.data.no_sale
                    });
                    window.open("{{ url('pos/receipt') }}/" + res.data.id, '_blank');
                    cart = [];
                    $('#buyerName').val('');
                    $('#buyerPhone').val('');
                    renderCart();
                    salesTable.ajax.reload();
                },
                error: function(xhr) {
                    Toast.fire({
                        icon: 'error',
                        title: JSON.parse(xhr.responseText).error ?? 'Checkout gagal'
                    });
                }
            });
        });

        function voidSale(id) {
            Swal.fire({
                title: 'Void transaksi ini?',
                text: 'Stok akan dikembalikan dan transaksi keuangan akan dibalik.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Void',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('pos/void') }}/" + id,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function() {
                            Toast.fire({
                                icon: 'success',
                                title: 'Transaksi di-void'
                            });
                            salesTable.ajax.reload();
                        },
                        error: function(xhr) {
                            Toast.fire({
                                icon: 'error',
                                title: JSON.parse(xhr.responseText).error ?? 'Gagal void'
                            });
                        }
                    });
                }
            });
        }

        $(document).ready(function() {
            let noD = 1;
            salesTable = $('#salesTable').DataTable({
                destroy: true,
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    url: "{{ url('pos/getList') }}",
                    type: "POST",
                },
                columns: [{
                        data: 'id',
                        render: function() {
                            return `${noD++}.`
                        }
                    },
                    {
                        data: 'created_at'
                    },
                    {
                        data: 'buyer_name'
                    },
                    {
                        data: 'total_net',
                        render: formatRupiah
                    },
                    {
                        data: 'status',
                        render: function(s) {
                            return s === 'voided' ?
                                '<span class="badge bg-danger">voided</span>' :
                                '<span class="badge bg-success">completed</span>';
                        }
                    },
                    {
                        data: 'id',
                        render: function(id, type, row) {
                            var btn =
                                `<a class="btn btn-xs btn-outline-primary" onclick="window.open('{{ url('pos/receipt') }}/${id}','_blank')">Struk</a>`;
                            if (row.status !== 'voided') {
                                btn += ` <a class="btn btn-xs btn-outline-danger" onclick="voidSale(${id})">Void</a>`;
                            }
                            return btn;
                        }
                    },
                ],
                drawCallback: function() {
                    noD = 1
                }
            });
        });
    </script>
@endsection
