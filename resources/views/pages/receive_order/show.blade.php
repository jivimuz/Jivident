@extends('layout/main')

@section('style')
    <style>
        .form-section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #3a57e8;
            border-bottom: 2px solid rgba(58, 87, 232, 0.2);
            padding-bottom: 8px;
            margin-bottom: 20px;
        }

        .table-input th {
            font-size: 13px;
            text-align: center;
            background-color: rgba(58, 87, 232, 0.05);
            color: #3a57e8;
        }

        .table-input td {
            padding: 5px;
            vertical-align: middle;
        }

        .table-input .form-control,
        .table-input .form-select {
            font-size: 13px;
            padding: 6px 10px;
            border-radius: 6px;
        }

        .summary-box {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            border: 1px dashed #ced4da;
        }

        .summary-label {
            font-weight: 600;
            color: #6c757d;
        }

        .summary-value {
            font-size: 1.2rem;
            font-weight: 700;
            color: #212529;
            text-align: right;
        }

        .grand-total {
            font-size: 1.5rem;
            color: #3a57e8;
        }
    </style>
@endsection

@section('content')
    <div class="content-inner mt-5 py-0">
        <!-- Header -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="bg-white w-100 rounded-pill p-2 d-flex align-items-center shadow-sm">
                    <a href="javascript:history.back()"
                        class="btn btn-primary text-white rounded-pill d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width:45px; height:45px;">
                        <i class="iconoir-fast-arrow-left" style="font-size:20px;"></i>
                    </a>
                    <span class="text-primary fw-bold text-center flex-grow-1 fs-5 fs-md-4">
                        Info Receive Order ({{ $receiveOrder->no_receipt }})
                    </span>
                    <div style="width:45px;" class="flex-shrink-0"></div>
                </div>
            </div>
        </div>

        <form id="form-edit-receive">
            <!-- Hidden ID RO -->
            <input disabled type="hidden" name="id" value="{{ $receiveOrder->id }}">

            <!-- Kad Maklumat Invois -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">
                            <h5 class="form-section-title"><i class="iconoir-page"></i> Maklumat Faktur / PBF</h5>
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">Supplier (PBF) <span
                                            class="text-danger">*</span></label>
                                    <select disabled name="supplier_id" id="supplier_id" class="form-select" required>
                                        <option value="" hidden>-- Pilih PBF --</option>
                                        @if ($receiveOrder->supplier)
                                            <option value="{{ $receiveOrder->supplier_id }}" selected>
                                                {{ $receiveOrder->supplier }}
                                            </option>
                                        @endif

                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">No. Faktur <span class="text-danger">*</span></label>
                                    <input disabled type="text" name="no_faktur" class="form-control text-uppercase"
                                        value="{{ $receiveOrder->no_faktur }}" required>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label fw-bold">Tgl. Faktur <span class="text-danger">*</span></label>
                                    <input disabled type="date" name="tgl_faktur" class="form-control"
                                        value="{{ $receiveOrder->tgl_faktur }}" required>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label fw-bold">Tgl. Jatuh Tempo</label>
                                    <input disabled type="date" name="tgl_jatuh_tempo" class="form-control"
                                        value="{{ $receiveOrder->tgl_jatuh_tempo }}">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label fw-bold">No. PO (Opsional)</label>
                                    <input disabled type="text" name="no_po" class="form-control"
                                        value="{{ $receiveOrder->no_po }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kad Item Terperinci -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                                <h5 class="card-title text-primary fw-bold mb-0"><i class="iconoir-box"></i> Detail Item
                                </h5>

                            </div>

                            <div class="table-responsive" style="min-height: 250px;">
                                <table class="table table-bordered table-input" id="table-items">
                                    <thead>
                                        <tr>
                                            <th width="20%">Produk / Obat</th>
                                            <th width="12%">No. Batch</th>
                                            <th width="10%">Tgl Kadaluwarsa</th>
                                            <th width="8%">Qty</th>
                                            <th width="10%">Unit</th>
                                            <th width="12%">Harga Beli</th>
                                            <th width="8%">Disc (%)</th>
                                            <th width="15%">Subjumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody id="item-tbody">

                                        @foreach ($details as $detail)
                                            <tr class="item-row">
                                                <td>
                                                    <select disabled name="product_id[]" class="form-select select-product"
                                                        required>
                                                        <!-- Jika menggunakan Select2 via AJAX, value ini akan muncul duluan -->
                                                        <option value="{{ $detail->product_id }}" selected>
                                                            {{ $detail->product_name ?? 'Produk ID: ' . $detail->product_id }}
                                                        </option>
                                                    </select>
                                                </td>
                                                <td><input disabled type="text" name="batch_no[]"
                                                        class="form-control text-uppercase" value="{{ $detail->batch_no }}"
                                                        required></td>
                                                <td><input disabled type="date" name="expired_date[]"
                                                        class="form-control" value="{{ $detail->expired_date }}" required>
                                                </td>
                                                <td><input disabled type="number" name="qty[]"
                                                        class="form-control input-qty text-center"
                                                        value="{{ $detail->qty }}" min="1" required></td>
                                                <td>
                                                    <select disabled name="unit_id[]" class="form-select select-unit">

                                                        @if ($detail->unit_id)
                                                            <option value="{{ $detail->unit_id }}" selected>
                                                                {{ $detail->unit_name }}
                                                            </option>
                                                        @endif
                                                    </select>
                                                </td>
                                                <td><input disabled type="number" name="harga_beli[]"
                                                        class="form-control input-harga text-end"
                                                        value="{{ $detail->harga_beli }}" min="0" required></td>
                                                <td><input disabled type="number" name="diskon_persen[]"
                                                        class="form-control input-diskon text-center"
                                                        value="{{ $detail->diskon_persen }}" min="0"
                                                        max="100" step="0.01"></td>
                                                <td>
                                                    <input disabled type="text"
                                                        class="form-control input-subtotal-display text-end fw-bold"
                                                        value="{{ $detail->subtotal }}" readonly>
                                                    <input disabled type="hidden" name="subtotal[]"
                                                        class="input-subtotal" value="{{ $detail->subtotal }}">
                                                </td>

                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kad Ringkasan & Hantar -->
            <div class="row">
                <div class="col-lg-7 mb-4 mb-lg-0">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body p-4">
                            <h5 class="form-section-title"><i class="iconoir-notes"></i> Nota Tambahan</h5>
                            <textarea disabled name="keterangan" class="form-control" rows="4">{{ $receiveOrder->keterangan }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card shadow-sm border-0 summary-box">
                        <div class="row mb-2">
                            <div class="col-6 summary-label align-self-center">Total Bruto</div>
                            <div class="col-6 summary-value" id="display-total-bruto">Rp 0</div>
                            <input disabled type="hidden" name="total_bruto" id="input-total-bruto"
                                value="{{ $receiveOrder->total_bruto }}">
                        </div>

                        <div class="row mb-2">
                            <div class="col-6 summary-label align-self-center">Discount Tambahan (Rp)</div>
                            <div class="col-6">
                                <input disabled type="number" name="diskon_global" id="input-diskon-global"
                                    class="form-control text-end fw-bold" value="{{ $receiveOrder->diskon_global }}"
                                    min="0">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-6 summary-label align-self-center">PPN (%)</div>
                            <div class="col-6">
                                <div class="input-group">
                                    <input disabled type="number" name="ppn_persen" id="input-ppn-persen"
                                        class="form-control text-end fw-bold" value="{{ $receiveOrder->ppn_persen }}"
                                        min="0" max="100">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row mb-4">
                            <div class="col-5 summary-label align-self-center">Grand Total</div>
                            <div class="col-7 summary-value grand-total" id="display-grand-total">Rp 0</div>
                            <input disabled type="hidden" name="grand_total" id="input-grand-total"
                                value="{{ $receiveOrder->grand_total }}">
                        </div>
                    </div>
                </div>
            </div>

        </form>


    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const calculateRow = (row) => {
                let qty = parseFloat(row.find('.input-qty').val()) || 0;
                let harga = parseFloat(row.find('.input-harga').val()) || 0;
                let diskonPersen = parseFloat(row.find('.input-diskon').val()) || 0;

                let dpp = qty * harga;
                let nominalDiskon = dpp * (diskonPersen / 100);
                let subtotal = dpp - nominalDiskon;

                row.find('.input-subtotal').val(subtotal);
                row.find('.input-subtotal-display').val(formatRupiah(subtotal).replace('Rp', '').trim());

                calculateGrandTotal();
            };

            const calculateGrandTotal = () => {
                let totalBruto = 0;

                $('#item-tbody .input-subtotal').each(function() {
                    totalBruto += parseFloat($(this).val()) || 0;
                });

                let diskonGlobal = parseFloat($('#input-diskon-global').val()) || 0;
                let ppnPersen = parseFloat($('#input-ppn-persen').val()) || 0;

                let dppGlobal = totalBruto - diskonGlobal;
                if (dppGlobal < 0) dppGlobal = 0;

                let nominalPpn = dppGlobal * (ppnPersen / 100);
                let grandTotal = dppGlobal + nominalPpn;

                $('#input-total-bruto').val(totalBruto);
                $('#display-total-bruto').text(formatRupiah(totalBruto));

                $('#input-grand-total').val(grandTotal);
                $('#display-grand-total').text(formatRupiah(grandTotal));
            };

            // PENGURUSAN PILIHAN PRODUK (ELAK PENDUA)
            const updateProductOptions = () => {
                let selectedValues = [];
                $('#item-tbody .select-product').each(function() {
                    let val = $(this).val();
                    if (val) selectedValues.push(val);
                });

                $('#item-tbody .select-product').each(function() {
                    let currentSelect = $(this);
                    let currentVal = currentSelect.val();

                    currentSelect.find('option').each(function() {
                        let optionVal = $(this).val();
                        if (optionVal && selectedValues.includes(optionVal) && optionVal !==
                            currentVal) {
                            $(this).prop('disabled', true);
                        } else {
                            $(this).prop('disabled', false);
                        }
                    });
                });
            };



            // Format Subtotal pada saat Load Halaman
            $('#item-tbody .item-row').each(function() {
                calculateRow($(this));
            });

            calculateGrandTotal();
            updateDeleteButtons();
            updateProductOptions(); // Kunci produk yang sudah ter-load dari database
        });
    </script>
@endsection
