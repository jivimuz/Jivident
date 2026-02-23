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
        <!-- Tajuk -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="bg-white w-100 rounded-pill p-2 d-flex align-items-center shadow-sm">
                    <a href="javascript:history.back()"
                        class="btn btn-primary rounded-pill d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width:45px; height:45px;">
                        <i class="iconoir-fast-arrow-left" style="font-size:20px;"></i>
                    </a>
                    <span class="text-primary fw-bold text-center flex-grow-1 fs-5 fs-md-4">
                        Penerimaan Barang (Receive Order)
                    </span>
                    <div style="width:45px;" class="flex-shrink-0"></div>
                </div>
            </div>
        </div>

        <form id="form-receive-order">
            <!-- Kad Maklumat Invois -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">
                            <h5 class="form-section-title"><i class="iconoir-page"></i> Informasi Faktur / PBF</h5>
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">Supplier (PBF) <span
                                            class="text-danger">*</span></label>
                                    <select name="supplier_id" id="supplier_id" class="form-select" required>
                                        <option value="">-- Pilih PBF --</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">No. Faktur <span class="text-danger">*</span></label>
                                    <input type="text" name="no_faktur" class="form-control text-uppercase"
                                        placeholder="Masukkan No. Faktur" required>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label fw-bold">Tgl. Faktur <span class="text-danger">*</span></label>
                                    <input type="date" name="tgl_faktur" class="form-control" value="{{ date('Y-m-d') }}"
                                        required>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label fw-bold">Tgl. Jatuh Tempo</label>
                                    <input type="date" name="tgl_jatuh_tempo" class="form-control">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label fw-bold">No. PO (Opsional)</label>
                                    <input type="text" name="no_po" class="form-control" placeholder="Rujukan PO">
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
                                <h5 class="card-title text-primary fw-bold mb-0"><i class="iconoir-box"></i> Perincian Item
                                    Obat</h5>
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" id="btn-add-row">
                                    <i class="iconoir-plus-circle"></i> Tambah Baris
                                </button>
                            </div>

                            <div class="table-responsive" style="min-height: 250px;">
                                <table class="table table-bordered table-input" id="table-items">
                                    <thead>
                                        <tr>
                                            <th width="20%">Produk / Obat*</th>
                                            <th width="12%">No. Batch*</th>
                                            <th width="10%">Exp. Date*</th>
                                            <th width="8%">Qty*</th>
                                            <th width="10%">Unit*</th>
                                            <th width="12%">Harga Beli*</th>
                                            <th width="8%">Disc (%)</th>
                                            <th width="15%">Subtotal</th>
                                            <th width="5%"><i class="iconoir-trash"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody id="item-tbody">
                                        <!-- Baris Pertama -->
                                        <tr class="item-row">
                                            <td>
                                                <select name="product_id[]" class="form-select select-product" required>
                                                    <option value="">Pilih Obat...</option>
                                                </select>
                                            </td>
                                            <td><input type="text" name="batch_no[]" class="form-control text-uppercase"
                                                    placeholder="Batch" required></td>
                                            <td><input type="date" name="expired_date[]" class="form-control" required>
                                            </td>
                                            <td><input type="number" name="qty[]"
                                                    class="form-control input-qty text-center" value="1" min="1"
                                                    required></td>
                                            <td>
                                                <select name="unit_id[]" class="form-select select-unit" required>
                                                </select>
                                            </td>
                                            <td><input type="number" name="harga_beli[]"
                                                    class="form-control input-harga text-end" value="0" min="0"
                                                    required></td>
                                            <td><input type="number" name="diskon_persen[]"
                                                    class="form-control input-diskon text-center" value="0"
                                                    min="0" max="100" step="0.01"></td>
                                            <td>
                                                <input type="text"
                                                    class="form-control input-subtotal-display text-end fw-bold"
                                                    value="0" readonly>
                                                <input type="hidden" name="subtotal[]" class="input-subtotal"
                                                    value="0">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-danger btn-remove-row"
                                                    disabled><i class="iconoir-trash"></i></button>
                                            </td>
                                        </tr>
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
                            <textarea name="keterangan" class="form-control" rows="4" placeholder="Tambahkan nota jika perlu..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card shadow-sm border-0 summary-box">
                        <div class="row mb-2">
                            <div class="col-6 summary-label align-self-center">Total Bruto</div>
                            <div class="col-6 summary-value" id="display-total-bruto">Rp 0</div>
                            <input type="hidden" name="total_bruto" id="input-total-bruto" value="0">
                        </div>

                        <div class="row mb-2">
                            <div class="col-6 summary-label align-self-center">Discount Tambahan (Rp)</div>
                            <div class="col-6">
                                <input type="number" name="diskon_global" id="input-diskon-global"
                                    class="form-control text-end fw-bold" value="0" min="0">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-6 summary-label align-self-center">PPN (%)</div>
                            <div class="col-6">
                                <div class="input-group">
                                    <input type="number" name="ppn_persen" id="input-ppn-persen"
                                        class="form-control text-end fw-bold" value="11" min="0"
                                        max="100">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row mb-4">
                            <div class="col-5 summary-label align-self-center">Grand Total</div>
                            <div class="col-7 summary-value grand-total" id="display-grand-total">Rp 0</div>
                            <input type="hidden" name="grand_total" id="input-grand-total" value="0">
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-light rounded-pill px-4"
                                onclick="history.back()">Batal</button>
                            <button type="submit" class="btn btn-primary rounded-pill w-100 fw-bold" id="btn-submit">
                                <i class="fa fa-save"></i> Simpan Penerimaan
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </form>

        <!-- Templat Tersembunyi untuk Tambah Baris (Klon) -->
        <table style="display: none;">
            <tbody id="row-template">
                <tr class="item-row">
                    <td>
                        <select name="product_id[]" class="form-select select-product" required>
                            <option value="">Pilih Obat...</option>

                        </select>
                    </td>
                    <td><input type="text" name="batch_no[]" class="form-control text-uppercase" placeholder="Batch"
                            required></td>
                    <td><input type="date" name="expired_date[]" class="form-control" required></td>
                    <td><input type="number" name="qty[]" class="form-control input-qty text-center" value="1"
                            min="1" required></td>
                    <td>
                        <select name="unit_id[]" class="form-select select-unit">
                        </select>
                    </td>
                    <td><input type="number" name="harga_beli[]" class="form-control input-harga text-end"
                            value="0" min="0" required></td>
                    <td><input type="number" name="diskon_persen[]" class="form-control input-diskon text-center"
                            value="0" min="0" max="100" step="0.01"></td>
                    <td>
                        <input type="text" class="form-control input-subtotal-display text-end fw-bold" value="0"
                            readonly>
                        <input type="hidden" name="subtotal[]" class="input-subtotal" value="0">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger btn-remove-row"><i
                                class="iconoir-trash"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>

    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // Persediaan CSRF Token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const formatRupiah = (angka) => {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(angka);
            };

            // --- FUNGSI PENGIRAAN ---

            // 1. Kira Subjumlah per Baris
            const calculateRow = (row) => {
                let qty = parseFloat(row.find('.input-qty').val()) || 0;
                let harga = parseFloat(row.find('.input-harga').val()) || 0;
                let diskonPersen = parseFloat(row.find('.input-diskon').val()) || 0;

                let dpp = qty * harga; // Dasar Pengenaan Cukai / Harga Kasar per item
                let nominalDiskon = dpp * (diskonPersen / 100);
                let subtotal = dpp - nominalDiskon;

                row.find('.input-subtotal').val(subtotal);
                row.find('.input-subtotal-display').val(formatRupiah(subtotal).replace('Rp', '').trim());

                calculateGrandTotal();
            };

            // 2. Kira Keseluruhan (Grand Total)
            const calculateGrandTotal = () => {
                let totalBruto = 0;

                // Jumlahkan semua subjumlah baris
                $('#item-tbody .input-subtotal').each(function() {
                    totalBruto += parseFloat($(this).val()) || 0;
                });

                let diskonGlobal = parseFloat($('#input-diskon-global').val()) || 0;
                let ppnPersen = parseFloat($('#input-ppn-persen').val()) || 0;

                // DPP selepas diskaun tambahan
                let dppGlobal = totalBruto - diskonGlobal;
                if (dppGlobal < 0) dppGlobal = 0;

                let nominalPpn = dppGlobal * (ppnPersen / 100);
                let grandTotal = dppGlobal + nominalPpn;

                // Kemas kini UI
                $('#input-total-bruto').val(totalBruto);
                $('#display-total-bruto').text(formatRupiah(totalBruto));

                $('#input-grand-total').val(grandTotal);
                $('#display-grand-total').text(formatRupiah(grandTotal));
            };

            // --- PENGURUSAN PILIHAN PRODUK (ELAK PENDUA) ---
            const updateProductOptions = () => {
                let selectedValues = [];
                // Kumpulkan semua nilai produk yang sedang dipilih
                $('#item-tbody .select-product').each(function() {
                    let val = $(this).val();
                    if (val) selectedValues.push(val);
                });

                // Lumpuhkan (disable) pilihan yang sudah dipilih di baris lain
                $('#item-tbody .select-product').each(function() {
                    let currentSelect = $(this);
                    let currentVal = currentSelect.val();

                    currentSelect.find('option').each(function() {
                        let optionVal = $(this).val();
                        // Jika pilihan sudah ada di baris lain, maka lumpuhkan
                        if (optionVal && selectedValues.includes(optionVal) && optionVal !==
                            currentVal) {
                            $(this).prop('disabled', true);
                        } else {
                            $(this).prop('disabled', false);
                        }
                    });
                });
            };

            // --- EVENT LISTENER BARIS ---

            // Delegasi Event untuk input di dalam jadual (Berlaku untuk baris baharu yg diklon)
            $('#table-items').on('input', '.input-qty, .input-harga, .input-diskon', function() {
                let row = $(this).closest('.item-row');
                calculateRow(row);
            });

            // Semak pertukaran pilihan produk
            $('#table-items').on('change', '.select-product', function() {
                updateProductOptions();
            });

            // Kira semula jika diskaun global / PPN berubah
            $('#input-diskon-global, #input-ppn-persen').on('input', function() {
                calculateGrandTotal();
            });

            // --- PENGURUSAN BARIS ---

            const updateDeleteButtons = () => {
                let rowCount = $('#item-tbody .item-row').length;
                if (rowCount === 1) {
                    $('#item-tbody .btn-remove-row').prop('disabled', true);
                } else {
                    $('#item-tbody .btn-remove-row').prop('disabled', false);
                }
            };

            // Tambah Baris Baharu (Hanya Satu Persatu)
            $('#btn-add-row').on('click', function() {
                // Semak baris terakhir, pastikan produk telah dipilih
                let lastRow = $('#item-tbody .item-row').last();
                let lastProduct = lastRow.find('.select-product').val();

                if (!lastProduct) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Harap pilih product terlebih dahulu.'
                    });
                    return; // Hentikan proses jika kosong
                }

                // Tambah klon baris baharu
                let newRow = $($('#row-template').html());
                $('#item-tbody').append(newRow);

                updateDeleteButtons();
                updateProductOptions(); // Kemas kini pilihan produk untuk baris baharu
            });

            // Padam Baris
            $('#item-tbody').on('click', '.btn-remove-row', function() {
                if ($('#item-tbody .item-row').length > 1) {
                    $(this).closest('.item-row').remove();
                    calculateGrandTotal();
                    updateDeleteButtons();
                    updateProductOptions(); // Lepaskan sekatan produk yang dipadam
                }
            });

            // --- SUBMIT AJAX ---
            $('#form-receive-order').on('submit', function(e) {
                e.preventDefault();

                let btnSubmit = $('#btn-submit');
                let originalText = btnSubmit.html();
                // Semakan ringkas: Pastikan Jumlah Keseluruhan tidak minus
                let grandTotal = parseFloat($('#input-grand-total').val()) || 0;
                if (grandTotal <= 0) {
                    Swal.fire('Ralat', 'Jumlah keseluruhan tidak boleh atau kurang dari 0.', 'error');
                    return;
                }

                btnSubmit.html('<span class="spinner-border spinner-border-sm"></span> Menyimpan...').prop(
                    'disabled', true);

                $.ajax({
                    url: "{{ url('receive_order/store') }}", // Ganti dengan route POST anda
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Penerimaan barang berhasil disimpan!',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = "{{ url('receive_order') }}";
                        });
                    },
                    error: function(xhr) {
                        let errorMsg = "Terjadi kesalahan sistem.";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menyimpan',
                            text: errorMsg,
                        });
                    },
                    complete: function() {
                        btnSubmit.html(originalText).prop('disabled', false);
                    }
                });
            });

            // Penetapan awal semasa muat turun pertama
            calculateGrandTotal();
            updateDeleteButtons();
            updateProductOptions();
            select2run()
        });

        $('#supplier_id').select2({
            ajax: {
                url: "<?= url('receive_order/getSupplierList') ?>",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                delay: 250,
                datatype: 'json',
                data: function(params) {
                    return {
                        q: params.term
                    }
                },
                processResults: function(response) {
                    var option = [];
                    for (data of response.data) {
                        option.push({
                            text: data.name,
                            id: data.id,

                        })
                    }
                    return {
                        results: option
                    };
                },

            },
            placeholder: "Input Name for search",
        })

        function select2run() {
            $('.select-product').select2({
                ajax: {
                    url: "<?= url('receive_order/getProductList') ?>",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    delay: 250,
                    datatype: 'json',
                    data: function(params) {
                        return {
                            q: params.term
                        }
                    },
                    processResults: function(response) {
                        var option = [];
                        for (data of response.data) {
                            option.push({
                                text: data.name,
                                id: data.id,

                            })
                        }
                        return {
                            results: option
                        };
                    },

                },
                placeholder: "Input Name for search",
            })

            $('.select-unit').select2({
                ajax: {
                    url: "<?= url('receive_order/getUnitList') ?>",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    delay: 250,
                    datatype: 'json',
                    data: function(params) {
                        return {
                            q: params.term
                        }
                    },
                    processResults: function(response) {
                        var option = [];
                        for (data of response.data) {
                            option.push({
                                text: data.name,
                                id: data.id,

                            })
                        }
                        return {
                            results: option
                        };
                    },

                },
                placeholder: "Input Name for search",
            })

        }
    </script>
@endsection
