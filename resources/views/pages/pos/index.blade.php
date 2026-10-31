@extends('layout/fullscreen')
<?php
use App\Models\Setting;

$logo = Setting::where('parameter', 'company_logo')->first()->value ?: url('/assets/logo.png');
?>
@section('style')
    <style>
        /* position:fixed (not just width/height:100%) because .pos-fullscreen
           is a child of the admin theme's .app-wrapper, which is `display:
           flex` — every other direct child of it (nav, footer, .go-top, the
           boot-splash loader) escapes that flex sizing the same way, via
           fixed/absolute positioning, rather than relying on flex-item
           stretch (which isn't guaranteed here since .app-wrapper itself has
           no explicit height and this is the only normal-flow flex item). */
        .pos-fullscreen {
            position: fixed;
            inset: 0;
            /* Above the theme's fixed copyright footer (z-index 1000) and
               go-top button (1001) so they're fully covered, not peeking
               through at the edges of this fullscreen page; below the boot
               splash loader (1005) so that still shows briefly on first load. */
            z-index: 1002;
            display: flex;
            flex-direction: column;
            height: 100vh;
            width: 100%;
            background-color: #f6f7f9;
        }

        .pos-topbar {
            flex: 0 0 auto;
        }

        /* Every icon+text button/heading on this page centers via flex
           rather than inline baseline + margin utilities — iconoir icons
           are a block-level mask-image, so they don't share a text baseline
           with the label next to them and end up looking "off" otherwise. */
        .pos-topbar .btn,
        .pos-checkout-btn,
        #modalRiwayat .modal-title {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .35rem;
        }

        .pos-clock {
            font-size: 13px;
            color: rgba(var(--dark), .55);
            line-height: 1.2;
            text-align: center;
        }

        .pos-clock b {
            font-size: 15px;
            color: rgba(var(--dark), .85);
        }

        .pos-body {
            flex: 1 1 auto;
            overflow: hidden;
            padding: 1rem;
        }

        .pos-layout {
            display: flex;
            gap: 1rem;
            height: 100%;
        }

        /* ===== Left: product catalog ===== */
        .pos-catalog {
            flex: 1 1 auto;
            min-width: 0;
            display: flex;
            flex-direction: column;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .05);
            padding: 1rem;
        }

        .pos-search-wrap {
            position: relative;
            flex: 0 0 auto;
            margin-bottom: .75rem;
        }

        .pos-search-wrap .iconoir-search {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(var(--dark), .4);
            font-size: 18px;
        }

        #productSearch {
            height: 48px;
            padding-left: 42px;
            border-radius: 10px;
            font-size: 15px;
        }

        #productResults {
            flex: 1 1 auto;
            overflow-y: auto;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
            gap: .75rem;
            align-content: flex-start;
        }

        /* `.iconoir-*` icons are a mask-image on a `::before`, not a text
           glyph — `text-align: center` on the wrapper does nothing for them
           once they're `display: block` (they just sit flush left), so
           every icon-above/next-to-text spot here centers via flex instead. */
        .pos-empty-state {
            grid-column: 1 / -1;
            display: flex;
            flex-direction: column;
            align-items: center;
            color: rgba(var(--dark), .4);
            padding: 3rem 1rem;
            text-align: center;
        }

        .pos-empty-state i {
            font-size: 42px;
            margin-bottom: .5rem;
            opacity: .6;
        }

        #productResults .item {
            cursor: pointer;
            border: 1px solid rgba(0, 0, 0, .07);
            border-radius: 12px;
            padding: .85rem .75rem;
            background: #fff;
            transition: border-color .15s ease, transform .1s ease, box-shadow .15s ease;
            display: flex;
            flex-direction: column;
            gap: .35rem;
        }

        #productResults .item:hover {
            border-color: rgba(var(--primary), .5);
            box-shadow: 0 4px 14px rgba(var(--primary), .12);
            transform: translateY(-2px);
        }

        #productResults .item .item-name {
            font-weight: 600;
            font-size: 13.5px;
            color: rgba(var(--dark), .85);
            line-height: 1.3;
            min-height: 2.6em;
        }

        #productResults .item .item-stock {
            font-size: 11px;
            width: fit-content;
            padding: .15rem .5rem;
            border-radius: 20px;
            background: rgba(var(--success), .1);
            color: rgba(var(--success), 1);
            font-weight: 600;
        }

        #productResults .item .item-stock.low {
            background: rgba(var(--danger), .1);
            color: rgba(var(--danger), 1);
        }

        #productResults .item .item-price {
            margin-top: auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-weight: 700;
            color: rgba(var(--primary), 1);
        }

        #productResults .item .item-price i {
            background: rgba(var(--primary), 1);
            color: #fff;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        /* ===== Right: cart / payment ticket ===== */
        .pos-cart-panel {
            flex: 0 0 400px;
            display: flex;
            flex-direction: column;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .05);
            overflow: hidden;
        }

        .pos-cart-head {
            flex: 0 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.1rem;
            border-bottom: 1px solid rgba(0, 0, 0, .06);
        }

        .pos-cart-head h5 {
            margin: 0;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: .4rem;
        }

        #cartBody {
            flex: 1 1 auto;
            overflow-y: auto;
            padding: .5rem .9rem;
        }

        .cart-row {
            padding: .7rem .3rem;
            border-bottom: 1px dashed rgba(0, 0, 0, .09);
        }

        .cart-row:last-child {
            border-bottom: none;
        }

        .cart-row .cart-row-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: .5rem;
        }

        .cart-row .cart-row-name {
            font-weight: 600;
            font-size: 13.5px;
            color: rgba(var(--dark), .85);
        }

        .cart-row .cart-row-unit {
            font-size: 12px;
            color: rgba(var(--dark), .45);
        }

        .cart-row .cart-remove-btn {
            color: rgba(var(--danger), 1);
            background: rgba(var(--danger), .08);
            border: none;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            flex: 0 0 auto;
            font-size: 13px;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cart-row .cart-row-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: .4rem;
        }

        .qty-stepper {
            display: inline-flex;
            align-items: center;
            border: 1px solid rgba(0, 0, 0, .1);
            border-radius: 20px;
            overflow: hidden;
        }

        .qty-stepper button {
            border: none;
            background: #f1f2f5;
            width: 26px;
            height: 26px;
            line-height: 1;
            font-size: 15px;
            color: rgba(var(--dark), .7);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qty-stepper button:active {
            background: #e3e5ea;
        }

        .qty-stepper .qty-val {
            width: 32px;
            text-align: center;
            font-weight: 600;
            font-size: 13px;
        }

        .cart-row-subtotal {
            font-weight: 700;
            font-size: 13.5px;
            color: rgba(var(--dark), .85);
        }

        .cart-discount-link {
            font-size: 11.5px;
            color: rgba(var(--primary), 1);
            cursor: pointer;
            text-decoration: underline;
        }

        .cart-discount-input {
            width: 90px;
            display: inline-block;
        }

        .pos-cart-footer {
            flex: 0 0 auto;
            border-top: 1px solid rgba(0, 0, 0, .06);
            padding: .9rem 1.1rem 1.1rem;
            background: #fafbfc;
        }

        .pos-summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            color: rgba(var(--dark), .6);
            margin-bottom: .2rem;
        }

        .pos-summary-total {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            font-size: 15px;
            font-weight: 700;
            margin: .4rem 0 .8rem;
            padding-top: .5rem;
            border-top: 1px dashed rgba(0, 0, 0, .12);
        }

        .pos-summary-total b {
            font-size: 22px;
            color: rgba(var(--primary), 1);
        }

        .pos-buyer-toggle {
            font-size: 12.5px;
            color: rgba(var(--dark), .55);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: .25rem;
            margin-bottom: .5rem;
        }

        .pos-payment-methods {
            display: flex;
            gap: .5rem;
            margin-bottom: .75rem;
        }

        .pos-payment-btn {
            flex: 1 1 0;
            border: 1.5px solid rgba(0, 0, 0, .1);
            background: #fff;
            border-radius: 10px;
            padding: .5rem .25rem;
            font-size: 12px;
            font-weight: 600;
            color: rgba(var(--dark), .6);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .pos-payment-btn i {
            font-size: 18px;
            margin-bottom: .2rem;
        }

        .pos-payment-btn.active {
            border-color: rgba(var(--primary), 1);
            background: rgba(var(--primary), .08);
            color: rgba(var(--primary), 1);
        }

        .pos-cash-calc {
            background: #fff;
            border: 1px solid rgba(0, 0, 0, .08);
            border-radius: 10px;
            padding: .6rem .75rem;
            margin-bottom: .75rem;
        }

        .pos-cash-calc .form-label {
            font-size: 12px;
            margin-bottom: .25rem;
        }

        .pos-checkout-btn {
            border-radius: 10px;
            padding: .75rem;
            font-weight: 700;
        }
    </style>
@endsection

@section('content')
    <div class="pos-fullscreen">
        <header
            class="pos-topbar d-flex align-items-center justify-content-between px-4 py-2 bg-white border-bottom shadow-sm">
            <div class="d-flex align-items-center gap-2">
                <img src="{{ $logo }}" alt="logo" height="36">
                <span class="fw-bold fs-5 text-primary ms-2">Kasir Apotek</span>
            </div>
            <div class="pos-clock d-none d-md-block">
                <div id="posClock"><b>--:--:--</b></div>
                <div id="posDate">Memuat tanggal...</div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill" data-bs-toggle="modal"
                    data-bs-target="#modalRiwayat">
                    <i class="iconoir-text-box"></i> Riwayat
                </button>
                <a href="{{ url('/') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
                    <i class="iconoir-home-simple"></i> Dashboard
                </a>
                <a href="{{ url('/logout') }}" data-turbo="false" class="btn btn-outline-danger btn-sm rounded-pill">
                    <i class="iconoir-log-out"></i> Logout
                </a>
            </div>
        </header>

        <main class="pos-body">
            <div class="pos-layout">
                <!-- Katalog Produk -->
                <section class="pos-catalog">
                    <div class="pos-search-wrap">
                        <i class="iconoir-search"></i>
                        <input type="text" id="productSearch" class="form-control"
                            placeholder="Cari nama atau kode produk...">
                    </div>
                    <div id="productResults">
                        <div class="pos-empty-state">
                            <i class="iconoir-shopping-bag"></i>
                            Ketik nama atau kode produk untuk mulai mencari
                        </div>
                    </div>
                </section>

                <!-- Keranjang & Pembayaran -->
                <aside class="pos-cart-panel">
                    <div class="pos-cart-head">
                        <h5><i class="iconoir-cart"></i>Keranjang</h5>
                        <span class="badge rounded-pill bg-primary" id="cartCount">0 item</span>
                    </div>

                    <div id="cartBody">
                        <div class="pos-empty-state">
                            <i class="iconoir-cart"></i>
                            Keranjang masih kosong
                        </div>
                    </div>

                    <div class="pos-cart-footer">
                        <div class="pos-summary-row">
                            <span>Subtotal</span>
                            <b id="sumGross">Rp 0</b>
                        </div>
                        <div class="pos-summary-row">
                            <span>Diskon</span>
                            <b id="sumDiscount">Rp 0</b>
                        </div>
                        <div class="pos-summary-total">
                            <span>Total</span>
                            <b id="sumNet">Rp 0</b>
                        </div>

                        <span class="pos-buyer-toggle" data-bs-toggle="collapse" data-bs-target="#buyerFields">
                            <i class="iconoir-user"></i> Data pembeli (opsional)
                            <i class="iconoir-nav-arrow-down"></i>
                        </span>
                        <div class="collapse mb-2" id="buyerFields">
                            <input type="text" class="form-control form-control-sm mb-2" id="buyerName"
                                placeholder="Nama pembeli">
                            <input type="text" class="form-control form-control-sm" id="buyerPhone"
                                placeholder="No. HP pembeli">
                        </div>

                        <div class="pos-payment-methods" id="paymentMethods">
                            @foreach ($payments as $p)
                                <?php
                                $lower = strtolower($p->title);
$icon = str_contains($lower, 'cash') || str_contains($lower, 'tunai')
    ? 'iconoir-cash'
    : (str_contains($lower, 'qr')
        ? 'iconoir-smartphone-device'
        : 'iconoir-credit-card');
?>
                                <button type="button" class="pos-payment-btn" data-id="{{ $p->id }}"
                                    data-title="{{ $p->title }}">
                                    <i class="{{ $icon }}"></i>{{ $p->title }}
                                </button>
                            @endforeach
                        </div>
                        <input type="hidden" id="fkPayment" value="{{ $payments->first()->id ?? '' }}">

                        <div class="pos-cash-calc" id="cashCalculator" style="display:none">
                            <label class="form-label mb-0">Uang Diterima</label>
                            <input type="number" min="0" class="form-control form-control-sm" id="cashReceived"
                                placeholder="0">
                            <div class="d-flex justify-content-between mt-2" style="font-size:12.5px">
                                <span>Kembalian</span>
                                <b id="cashChange">Rp 0</b>
                            </div>
                        </div>

                        <button class="btn btn-success w-100 pos-checkout-btn" id="checkoutBtn">
                            <i class="iconoir-printer"></i> Proses & Cetak Struk
                        </button>
                    </div>
                </aside>
            </div>
        </main>
    </div>

    <!-- Modal Riwayat Penjualan -->
    <div class="modal fade" id="modalRiwayat" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="iconoir-text-box"></i> Riwayat Penjualan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
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
@endsection

@section('script')
    <script>
        var cart = [];
        var salesTable;
        var currentTotalNet = 0;

        // formatRupiah() itself comes from the shared, data-turbo-permanent
        // customFunction.js (loaded once via footer.blade.php) — redeclaring
        // it here as a `function` collided with its global `const`
        // declaration there and threw ("Can't create duplicate variable")
        // the moment this page's own script ran.

        function updatePosClock() {
            var now = new Date();
            $('#posClock').html('<b>' + now.toTimeString().split(' ')[0] + '</b>');
            $('#posDate').text(now.toLocaleDateString('id-ID', {
                weekday: 'long',
                day: '2-digit',
                month: 'long',
                year: 'numeric'
            }));
        }
        setInterval(updatePosClock, 1000);
        updatePosClock();

        $('#productSearch').on('keyup', function() {
            var q = $(this).val();
            if (q.length < 2) {
                $('#productResults').html(
                    '<div class="pos-empty-state"><i class="iconoir-shopping-bag"></i>Ketik nama atau kode produk untuk mulai mencari</div>'
                );
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
                    if (!res.data.length) {
                        $('#productResults').html(
                            '<div class="pos-empty-state"><i class="iconoir-shopping-bag"></i>Produk tidak ditemukan</div>'
                        );
                        return;
                    }
                    var html = '';
                    res.data.forEach(function(p) {
                        var lowStock = Number(p.stock) <= 5;
                        html += `<div class="item" data-id="${p.id}" data-name="${p.name}" data-price="${p.harga_jual}">
                            <div class="item-name">${p.name}</div>
                            <span class="item-stock ${lowStock ? 'low' : ''}">Stok: ${p.stock}</span>
                            <div class="item-price">
                                <span>${formatRupiah(p.harga_jual)}</span>
                                <i class="iconoir-plus"></i>
                            </div>
                        </div>`;
                    });
                    $('#productResults').html(html);
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
                    showDiscount: false,
                });
            }
            Toast.fire({
                icon: 'success',
                title: $(this).data('name') + ' ditambahkan'
            });
            renderCart();
        });

        function renderCart() {
            $('#cartCount').text(cart.reduce((s, i) => s + i.qty, 0) + ' item');

            if (cart.length === 0) {
                $('#cartBody').html(
                    '<div class="pos-empty-state"><i class="iconoir-cart"></i>Keranjang masih kosong</div>');
                updateSummary();
                return;
            }

            var html = '';
            cart.forEach(function(item, idx) {
                var subtotal = (item.qty * item.price) - item.discount;
                html += `<div class="cart-row">
                    <div class="cart-row-top">
                        <div>
                            <div class="cart-row-name">${item.name}</div>
                            <div class="cart-row-unit">${formatRupiah(item.price)} / item</div>
                        </div>
                        <button type="button" class="cart-remove-btn" onclick="removeCartItem(${idx})">
                            <i class="iconoir-xmark"></i>
                        </button>
                    </div>
                    <div class="cart-row-bottom">
                        <div class="qty-stepper">
                            <button type="button" onclick="updateCartField(${idx}, 'qty', ${item.qty - 1})">−</button>
                            <span class="qty-val">${item.qty}</span>
                            <button type="button" onclick="updateCartField(${idx}, 'qty', ${item.qty + 1})">+</button>
                        </div>
                        <span class="cart-row-subtotal">${formatRupiah(subtotal)}</span>
                    </div>
                    <div class="mt-1">
                        ${item.showDiscount ?
                            `<input type="number" min="0" class="form-control form-control-sm cart-discount-input" value="${item.discount}" placeholder="Diskon (Rp)" onchange="updateCartField(${idx}, 'discount', this.value)">`
                            :
                            `<span class="cart-discount-link" onclick="toggleCartDiscount(${idx})">${item.discount > 0 ? 'Diskon: ' + formatRupiah(item.discount) : '+ Tambah diskon'}</span>`
                        }
                    </div>
                </div>`;
            });
            $('#cartBody').html(html);
            updateSummary();
        }

        function updateCartField(idx, field, value) {
            if (field === 'qty' && parseFloat(value) < 1) {
                return removeCartItem(idx);
            }
            cart[idx][field] = parseFloat(value) || 0;
            renderCart();
        }

        function toggleCartDiscount(idx) {
            cart[idx].showDiscount = true;
            renderCart();
        }

        function removeCartItem(idx) {
            cart.splice(idx, 1);
            renderCart();
        }

        function updateSummary() {
            var gross = cart.reduce((s, i) => s + (i.qty * i.price), 0);
            var discount = cart.reduce((s, i) => s + i.discount, 0);
            currentTotalNet = gross - discount;
            $('#sumGross').text(formatRupiah(gross));
            $('#sumDiscount').text(formatRupiah(discount));
            $('#sumNet').text(formatRupiah(currentTotalNet));
            updateCashChange();
        }

        function updateCashChange() {
            var received = parseFloat($('#cashReceived').val()) || 0;
            var change = received - currentTotalNet;
            $('#cashChange').text(formatRupiah(Math.max(change, 0))).toggleClass('text-danger', change < 0);
        }
        $('#cashReceived').on('input', updateCashChange);

        $('#paymentMethods').on('click', '.pos-payment-btn', function() {
            $('.pos-payment-btn').removeClass('active');
            $(this).addClass('active');
            $('#fkPayment').val($(this).data('id'));

            var title = String($(this).data('title')).toLowerCase();
            var isCash = title.includes('cash') || title.includes('tunai');
            $('#cashCalculator').toggle(isCash);
            if (isCash) updateCashChange();
        });
        // Default: metode pembayaran pertama aktif, sama seperti <select> lama
        // yang otomatis memilih opsi pertama.
        $('#paymentMethods .pos-payment-btn:first').trigger('click');

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
                    $('#cashReceived').val('');
                    $('#paymentMethods .pos-payment-btn:first').trigger('click');
                    renderCart();
                    if (salesTable) salesTable.ajax.reload();
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

        function initSalesTable() {
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
        }

        // DataTables mis-measures column widths if initialized while its modal
        // is still hidden (display:none), so init on first open rather than on
        // page load, then just reload data + re-adjust columns afterwards.
        $('#modalRiwayat').on('shown.bs.modal', function() {
            if (!salesTable) {
                initSalesTable();
            } else {
                salesTable.ajax.reload();
                salesTable.columns.adjust();
            }
        });
    </script>
@endsection
