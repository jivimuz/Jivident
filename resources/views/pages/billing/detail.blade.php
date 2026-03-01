@extends('layout/main')

@section('style')
    <style>
        .payment-summary {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            border: 1px dashed #0d6efd;
            /* warna primary bootstrap */
        }

        .paid-summary {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            border: 1px dashed #83e169;
            /* warna primary bootstrap */
        }

        .table-responsive {
            max-height: 400px;
            overflow-y: auto;
        }

        .payment-row {
            transition: all 0.3s ease;
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
                        class="btn btn-primary rounded-pill d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width:45px; height:45px;">
                        <i class="iconoir-fast-arrow-left" style="font-size:20px;"></i>
                    </a>
                    <span class="text-primary fw-bold text-center flex-grow-1 fs-5 fs-md-4">
                        Detail Pembayaran Pasien
                    </span>
                    <div style="width:45px;" class="flex-shrink-0"></div>
                </div>
            </div>
        </div>

        <form id="form-pembayaran">
            <!-- ID Daftar Pasien -->
            <input type="hidden" name="fk_daftarpasien" id="fk_daftarpasien" value="{{ $id ?? '' }}">

            <div class="row">
                <!-- Card List Tagihan -->
                <div class="col-lg-8">
                    <div class="card" data-iq-gsap="onStart" data-iq-opacity="0" data-iq-position-y="-40"
                        data-iq-duration=".6" data-iq-delay=".2" data-iq-trigger="scroll" data-iq-ease="none">
                        <div class="card-body p-4">
                            <div class="border-bottom pb-2 mb-3">
                                <h5 class="card-title mb-0">Rincian Tagihan Tindakan</h5>
                                <small class="text-muted">Semua tagihan di bawah wajib dibayar (bisa dicicil).</small>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover table-striped align-middle" id="billing-table">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Tipe</th>
                                            <th>Nama Item</th>
                                            <th>Dokter</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-end">Harga</th>
                                            <th class="text-end">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody id="billing-list">
                                        <!-- Data Tagihan di-load via AJAX ke sini -->
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                <div class="spinner-border text-primary spinner-border-sm" role="status">
                                                </div> Mengambil data tagihan...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel Multi Pembayaran -->
                <div class="col-lg-4">
                    <div class="card " data-iq-gsap="onStart" data-iq-opacity="0" data-iq-position-y="-40"
                        data-iq-duration=".6" data-iq-delay=".4" data-iq-trigger="scroll" data-iq-ease="none">
                        <div class="card-body">
                            <h5 class="card-title border-bottom pb-2">Metode Pembayaran</h5>

                            <div class="payment-summary mb-1 mt-3">
                                <h6 class="text-muted mb-1">Total Tagihan</h6>
                                <h3 class="text-danger fw-bold mb-0 text-break" id="display-total-tagihan">Rp 0</h3>
                                <input type="hidden" id="input-total-tagihan" name="total_tagihan" value="0">
                            </div>
                            <div class="paid-summary mb-2 mt-1">
                                <h6 class="text-muted mb-1">Telah Dibayar</h6>
                                <h3 class="text-success fw-bold mb-0 text-break" id="display-total-dibayar">Rp 0</h3>
                                <input type="hidden" id="input-total-dibayar" name="total_dibayar" value="0">
                            </div>


                            <!-- Container untuk List Pembayaran Dinamis -->
                            <div id="payment-methods-container">
                                <!-- Baris Pertama (Default) -->
                                <div class="row payment-row mb-3">
                                    <div class="col-12 col-xl-5 pe-xl-1 mb-2 mb-xl-0">
                                        <label class="fw-bold text-muted mb-1 d-none d-xl-block"
                                            style="font-size: 12px;">Metode</label>
                                        <select name="pembayaran_metode[]" class="form-select select-metode">
                                            @foreach ($payments as $pay)
                                                <option value="{{ $pay->id }}">{{ $pay->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-10 col-xl-6 px-xl-1">
                                        <label class="fw-bold text-muted mb-1 d-none d-xl-block"
                                            style="font-size: 12px;">Nominal</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" name="pembayaran_nominal[]"
                                                class="form-control input-bayar" value="0" min="0">
                                        </div>
                                    </div>
                                    <div class="col-2 col-xl-1 ps-1 d-flex align-items-end">
                                        <a class="btn text-danger btn-remove-payment rounded-pill px-1" disabled><i
                                                class="iconoir-trash"></i></a>
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-sm btn-outline-primary mb-3 mt-2 w-100"
                                id="btn-add-payment">
                                <i class="iconoir-plus-circle"></i> Tambah Metode
                            </button>

                            <hr>

                            <!-- Kalkulasi Akhir -->
                            <div class="d-flex flex-column flex-sm-row justify-content-between mb-2">
                                <span class="fw-bold text-muted">Total Bayar:</span>
                                <span class="fw-bold text-break text-sm-end" id="display-total-bayar">Rp 0</span>
                            </div>
                            <div class="d-flex flex-column flex-sm-row justify-content-between mb-3">
                                <span class="fw-bold text-muted" id="label-kembalian">Sisa Tagihan:</span>
                                <span class="fw-bold text-danger text-break text-sm-end" id="display-kembalian">Rp
                                    0</span>
                            </div>

                            <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill fw-bold"
                                id="btn-submit" disabled>
                                <i class="iconoir-check-circle"></i> Proses Pembayaran
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Template Hidden untuk Tambah Baris Pembayaran (Clone) -->
        <div id="payment-template" style="display: none;">
            <div class="row payment-row mb-3">
                <div class="col-12 col-xl-5 pe-xl-1 mb-2 mb-xl-0">
                    <select name="pembayaran_metode[]" class="form-select select-metode">
                        @foreach ($payments as $pay)
                            <option value="{{ $pay->id }}">{{ $pay->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-10 col-xl-6 px-xl-1">
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="pembayaran_nominal[]" class="form-control input-bayar"
                            value="0" min="0">
                    </div>
                </div>
                <div class="col-2 col-xl-1 ps-1 d-flex align-items-end">
                    <button type="button" class="btn text-danger btn-remove-payment rounded-pill px-1"><i
                            class="iconoir-trash"></i></button>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // Setup CSRF Token untuk AJAX

            let selisih = 0
            const fk_daftarpasien = $('#fk_daftarpasien').val();

            // 1. Ambil Data Tagihan via AJAX
            const loadTagihan = () => {
                $.ajax({
                    url: `{{ url('billing/getTagihanList') }}`,
                    type: 'POST',
                    data: {
                        daftarpasien_id: fk_daftarpasien
                    },
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        let html = '';
                        let totalAll = 0;
                        if (response.registrasi) {
                            totalAll += parseFloat(response.registrasi) || 0;
                            html += `
                                    <tr>
                                        <td>REG</td>
                                        <td>
                                            <!-- Hidden input agar tetap terbaca backend sebagai array -->
                                            <input type="hidden" name="tagihan_id[]" value="0">
                                            Registrasi
                                        </td>
                                        <td>-</td>
                                        <td class="text-center">1</td>
                                        <td class="text-end text-nowrap">${formatRupiah(response.registrasi)}</td>
                                        <td class="text-end fw-bold text-primary text-nowrap">${formatRupiah(response.registrasi)}</td>
                                    </tr>
                                `;
                        }

                        if ((response.tagihan_tindakan && response.tagihan_tindakan.length > 0) || (
                                response.tagihanDiagnosa && response.tagihanDiagnosa.length > 0)) {

                            if (response.tagihan_tindakan && response.tagihan_tindakan.length > 0) {
                                response.tagihan_tindakan.forEach(function(item) {
                                    totalAll += parseFloat(item.amount) || 0;
                                    html += `
                                    <tr>
                                        <td>TDK</td>
                                        <td>
                                            <!-- Hidden input agar tetap terbaca backend sebagai array -->
                                            <input type="hidden" name="tagihan_id[]" value="${item.id}">
                                            ${(item.tindakan || '').length > 20 ? (item.tindakan || '').slice(0, 20) + '...' : (item.tindakan || '')}
                                        </td>
                                        <td>${item.dokter || '-'}</td>
                                        <td class="text-center">${parseInt(item.qty)}</td>
                                        <td class="text-end text-nowrap">${formatRupiah(item.price)}</td>
                                        <td class="text-end fw-bold text-primary text-nowrap">${formatRupiah(item.amount)}</td>
                                    </tr>
                                `;
                                });
                            }
                            if (response.tagihan_diagnosa && response.tagihan_diagnosa.length > 0) {
                                response.tagihan_diagnosa.forEach(function(item) {
                                    totalAll += parseFloat(item.amount) || 0;
                                    html += `
                                    <tr>
                                        <td>ICD</td>
                                        <td>
                                            <!-- Hidden input agar tetap terbaca backend sebagai array -->
                                            <input type="hidden" name="tagihan_id[]" value="${item.id}">
                                            IDC ${item.tipe_diagnosa} || ${(item.diagnosa || '').length > 20 ? (item.diagnosa || '').slice(0, 20) + '...' : (item.diagnosa || '')}
                                        </td>
                                        <td>${item.dokter || '-'}</td>
                                        <td class="text-center">${parseInt(item.qty)}</td>
                                        <td class="text-end text-nowrap">${formatRupiah(item.price)}</td>
                                        <td class="text-end fw-bold text-primary text-nowrap">${formatRupiah(item.amount)}</td>
                                    </tr>
                                `;
                                });
                            }

                            $('#input-total-tagihan').val(totalAll);
                            $('#display-total-tagihan').text(formatRupiah(totalAll));
                        } else {
                            html =
                                `<tr><td colspan="5" class="text-center text-muted">Tidak ada tagihan.</td></tr>`;
                            $('#input-total-tagihan').val(0);
                            $('#display-total-tagihan').text(formatRupiah(0));
                        }

                        if (response.totalPaid) {
                            $('#input-total-dibayar').val(response.totalPaid);
                            $('#display-total-dibayar').text(formatRupiah(response.totalPaid));
                        }


                        $('#billing-list').html(html);
                        $('#billing-table').DataTable()

                        kalkulasiPembayaran();
                    },
                    error: function(xhr) {
                        $('#billing-list').html(
                            `<tr><td colspan="5" class="text-center text-danger">Gagal memuat data tagihan.</td></tr>`
                        );
                        console.error(xhr.responseText);
                    }
                });
            };

            // 2. Kalkulasi Multi-Metode Pembayaran Dinamis (Boleh Cicil)
            const kalkulasiPembayaran = () => {
                let totalTagihan = parseFloat($('#input-total-tagihan').val()) || 0;
                let totalDibayar = parseFloat($('#input-total-dibayar').val()) || 0;
                let totalBayar = 0;

                // Hitung total dari baris pembayaran yg sedang aktif
                $('#payment-methods-container .input-bayar').each(function() {
                    totalBayar += parseFloat($(this).val()) || 0;
                });

                selisih = totalBayar - (totalTagihan - totalDibayar);

                $('#display-total-bayar').text(formatRupiah(totalBayar));

                let elKembalian = $('#display-kembalian');
                let labelKembalian = $('#label-kembalian');

                if (selisih < 0) {
                    // Karena boleh cicil, kita sebut sebagai Sisa Tagihan alih-alih Kurang
                    labelKembalian.text("Sisa Tagihan:");
                    elKembalian.text(formatRupiah(Math.abs(selisih)));
                    elKembalian.removeClass('text-success').addClass('text-danger');
                } else {
                    labelKembalian.text("Kembalian:");
                    elKembalian.text(formatRupiah(selisih));
                    elKembalian.removeClass('text-danger').addClass('text-success');
                }

                // Aturan Tombol Submit:
                // Disable jika tidak ada tagihan, ATAU bayarnya masih Rp 0.
                if (totalTagihan <= 0 || totalBayar === 0 || (totalTagihan - totalDibayar) <= 0) {
                    $('#btn-submit').prop('disabled', true);
                } else {
                    $('#btn-submit').prop('disabled', false);
                }
            };

            // --- Manajemen Baris Multi Pembayaran ---

            // Fungsi Mencegah Duplikat dan Limit Metode Pembayaran
            const updatePaymentOptions = () => {
                let selectedValues = [];
                // Kumpulkan semua value yang sedang dipilih
                $('#payment-methods-container .select-metode').each(function() {
                    let val = $(this).val();
                    if (val) selectedValues.push(val);
                });

                // Disable option yang sudah dipilih di baris lain
                $('#payment-methods-container .select-metode').each(function() {
                    let currentSelect = $(this);
                    let currentVal = currentSelect.val();

                    currentSelect.find('option').each(function() {
                        let optionVal = $(this).val();
                        // Jika option sudah ada di baris lain, maka disable
                        if (selectedValues.includes(optionVal) && optionVal !== currentVal) {
                            $(this).prop('disabled', true);
                        } else {
                            $(this).prop('disabled', false);
                        }
                    });
                });

                // Cek limit tombol tambah berdasarkan jumlah option yang ada
                let maxOptions = $('#payment-template .select-metode option').length;
                let currentRows = $('#payment-methods-container .payment-row').length;

                if (currentRows >= maxOptions) {
                    $('#btn-add-payment').prop('disabled', true);
                } else {
                    $('#btn-add-payment').prop('disabled', false);
                }
            };

            // Trigger saat dropdown metode diubah
            $('#payment-methods-container').on('change', '.select-metode', function() {
                updatePaymentOptions();
            });

            // Check Status Tombol Hapus (Minimal 1 baris tidak bisa dihapus)
            const checkDeleteButtons = () => {
                let rows = $('#payment-methods-container .payment-row');
                if (rows.length === 1) {
                    rows.find('.btn-remove-payment').prop('disabled', true);
                } else {
                    rows.find('.btn-remove-payment').prop('disabled', false);
                }
            };

            // Tambah Metode Pembayaran (Clone Row Template)
            $('#btn-add-payment').on('click', function() {
                let maxOptions = $('#payment-template .select-metode option').length;
                let currentRows = $('#payment-methods-container .payment-row').length;

                if (currentRows < maxOptions) {
                    let newRow = $($('#payment-template').html());

                    // Cari value pertama yang belum terpakai untuk diset sebagai default baris baru
                    let selectedValues = [];
                    $('#payment-methods-container .select-metode').each(function() {
                        selectedValues.push($(this).val());
                    });

                    let availableValue = null;
                    newRow.find('.select-metode option').each(function() {
                        if (!selectedValues.includes($(this).val())) {
                            availableValue = $(this).val();
                            return false; // hentikan loop
                        }
                    });

                    if (availableValue) {
                        newRow.find('.select-metode').val(availableValue);
                    }

                    $('#payment-methods-container').append(newRow);
                    checkDeleteButtons();
                    updatePaymentOptions(); // Update limit & opsi dropdown
                }
            });

            // Hapus Metode Pembayaran
            $('#payment-methods-container').on('click', '.btn-remove-payment', function() {
                if ($('#payment-methods-container .payment-row').length > 1) {
                    $(this).closest('.payment-row').remove();
                    kalkulasiPembayaran();
                    checkDeleteButtons();
                    updatePaymentOptions(); // Update kembali opsi yang kini "tersedia"
                }
            });

            // --- Event Listeners Delegation ---

            // Input Nominal Saat Diketik
            $('#payment-methods-container').on('input', '.input-bayar', function() {
                if ($(this).val() === '' || $(this).val() < 0) {
                    $(this).val(0);
                }
                kalkulasiPembayaran();
            });

            // Fitur auto-fill (Double Click untuk otomatis isi sisa tagihan)
            $('#payment-methods-container').on('dblclick', '.input-bayar', function() {
                let totalTagihan = parseFloat($('#input-total-tagihan').val()) || 0;
                let totalLainnya = 0;

                $('#payment-methods-container .input-bayar').not(this).each(function() {
                    totalLainnya += parseFloat($(this).val()) || 0;
                });

                let sisaTunggakan = totalTagihan - totalLainnya;

                if (sisaTunggakan > 0) {
                    $(this).val(sisaTunggakan);
                    kalkulasiPembayaran();
                }
            });

            // 3. Proses Submit AJAX
            $('#form-pembayaran').on('submit', function(e) {
                e.preventDefault();

                let btnSubmit = $('#btn-submit');
                let originalText = btnSubmit.html();

                if (parseInt(selisih) > 100000) {
                    return Toast.fire({
                        icon: "error",
                        title: "Kembalian tidak bisa melebihi Rp. 100Rb"
                    });
                }

                btnSubmit.html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...'
                ).prop('disabled', true);

                // --- FORMATTING REQUEST DATA ---
                let paymentsArray = [];
                $('#payment-methods-container .payment-row').each(function() {
                    paymentsArray.push({
                        metode: $(this).find('.select-metode').val(),
                        nominal: $(this).find('.input-bayar').val()
                    });
                });

                let tagihanArray = [];
                $('input[name="tagihan_id[]"]').each(function() {
                    tagihanArray.push($(this).val());
                });

                let requestData = {
                    fk_daftarpasien: $('#fk_daftarpasien').val(),
                    total_tagihan: $('#input-total-tagihan').val(),
                    tagihan_id: tagihanArray,
                    payment: paymentsArray,
                    selisih: selisih,
                };

                $.ajax({
                    url: "{{ url('billing/process-paid') }}",
                    type: "POST",
                    data: requestData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Pembayaran berhasil diproses!',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = "{{ url('billing') }}";
                        });
                    },
                    error: function(xhr) {
                        let errorMsg = "Terjadi kesalahan sistem.";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: errorMsg,
                        });
                        btnSubmit.html(originalText).prop('disabled', false);
                    }
                });
            });

            // Jalankan fetch data saat halaman siap
            if (fk_daftarpasien) {
                loadTagihan();
            } else {
                $('#billing-list').html(
                    `<tr><td colspan="5" class="text-center text-danger">ID Pasien tidak ditemukan.</td></tr>`);
            }

            // Inisialisasi awal proteksi duplikat dropdown
            updatePaymentOptions();
        });
    </script>
@endsection
