<div class="content-inner py-0">
    <div class="row">
        @if ($status->status == 'Di Periksa')
            <!-- LEFT PANEL: FORM INPUT -->
            <div class="col-lg-5 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3 text-dark">Tulis Resep</h4>

                        <!-- Toggle Jenis Resep -->
                        <div class="type-toggle">
                            <div class="toggle-btn active" id="btn-non-racik" onclick="setResepType('non-racik')">
                                <i class="fa fa-pills"></i> Non-Racikan
                            </div>
                            <div class="toggle-btn" id="btn-racik" onclick="setResepType('racik')">
                                <i class="fa fa-minus-circle"></i> Racikan (Puyer/Salep)
                            </div>
                        </div>

                        <!-- ================= FORM NON-RACIKAN ================= -->
                        <div id="form-non-racik">
                            <div class="mb-3">
                                <label>Pilih Obat / Alkes <span class="text-danger">*</span></label>
                                <select class="form-select select-product" id="nr_obat">
                                    <option value="">Ketik nama obat...</option>
                                </select>
                                <div class="d-flex justify-content-between mt-1 px-1">
                                    <small class="text-muted">Sisa Stok: <span id="nr_stok"
                                            class="fw-bold text-success">-</span></small>
                                    <small class="text-muted">Harga: Rp <span id="nr_harga">-</span></small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-6">
                                    <label>Jumlah (Qty) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="nr_qty" min="1"
                                            value="1">
                                        <span class="input-group-text" id="nr_satuan_label">Tab</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label>Rute Pemberian</label>
                                    <select class="form-select" id="nr_rute">
                                        <option value="Oral">Oral (Diminum)</option>
                                        <option value="Topikal">Topikal (Dioles)</option>
                                        <option value="Injeksi">Injeksi (Suntik)</option>
                                        <option value="Tetes">Tetes</option>
                                        <option value="Suppositoria">Suppositoria</option>
                                    </select>
                                </div>
                            </div>

                            <div class="section-title mt-4">Aturan Pakai (Signa)</div>

                            <div class="row mb-3">
                                <div class="col-6">
                                    <label>Frekuensi <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control text-center" id="nr_signa_1"
                                            placeholder="3" value="3">
                                        <span class="input-group-text">x</span>
                                        <input type="number" class="form-control text-center" id="nr_signa_2"
                                            placeholder="1" value="1">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label>Waktu Konsumsi <span class="text-danger">*</span></label>
                                    <select class="form-select select-penggunaan" id="nr_waktu">
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label>Jadwal Minum</label>
                                <div class="time-checkboxes">
                                    <div class="custom-check"><input type="checkbox" id="nr_w_p" value="Pagi"> Pagi
                                    </div>
                                    <div class="custom-check"><input type="checkbox" id="nr_w_s" value="Siang">
                                        Siang</div>
                                    <div class="custom-check"><input type="checkbox" id="nr_w_sr" value="Sore"> Sore
                                    </div>
                                    <div class="custom-check"><input type="checkbox" id="nr_w_m" value="Malam">
                                        Malam</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label>Keterangan Tambahan (Opsional)</label>
                                <input type="text" class="form-control" id="nr_keterangan"
                                    placeholder="Cth: Habiskan, Kocok dahulu">
                            </div>

                            <button type="button" class="btn btn-primary w-100 fw-bold rounded-pill"
                                onclick="addNonRacik()">
                                <i class="fa fa-plus"></i> Masukkan ke Resep
                            </button>
                        </div>
                        <!-- ================= END FORM NON-RACIKAN ================= -->


                        <!-- ================= FORM RACIKAN ================= -->
                        <div id="form-racik" style="display: none;">
                            <div class="row mb-3">
                                <div class="col-7">
                                    <label>Nama Racikan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="r_nama"
                                        placeholder="Cth: Puyer Batuk Pilek">
                                </div>
                                <div class="col-5">
                                    <label>Bentuk Sediaan <span class="text-danger">*</span></label>
                                    <select class="form-select" id="r_bentuk">
                                        <option value="Pulveres (Puyer)">Puyer</option>
                                        <option value="Kapsul">Kapsul</option>
                                        <option value="Salep">Salep/Krim</option>
                                        <option value="Sirup">Sirup</option>
                                    </select>
                                </div>
                            </div>

                            <div class="section-title mt-4 d-flex justify-content-between align-items-center">
                                Komposisi Obat
                                <button type="button" class="btn btn-sm btn-outline-primary"
                                    onclick="addIngredientRow()">+ Obat</button>
                            </div>

                            <div id="ingredients-container">
                                <!-- Ingredient Row Template -->
                                <div class="row mb-2 ingredient-row align-items-end">
                                    <div class="col-6">
                                        <label>Obat <span class="text-danger">*</span></label>
                                        <select class="form-select ing-obat select-product">
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <label>Dosis/Jumlah</label>
                                        <input type="number" class="form-control ing-qty" placeholder="Qty"
                                            step="0.1" value="1">
                                    </div>
                                    <div class="col-2 text-center">
                                        <button type="button" class="btn btn-outline-danger  btn-sm "
                                            onclick="removeIngredient(this)"><i class="iconoir-trash"></i></button>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="row mb-3">
                                <div class="col-6">
                                    <label>Jumlah Dibuat (Kemasan) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="r_qty_buat" min="1"
                                            value="15">
                                        <span class="input-group-text" id="r_satuan_label">Bks</span>
                                    </div>
                                    <small class="text-muted" style="font-size: 10px;">Cth: Buat 15 bungkus
                                        puyer</small>
                                </div>
                            </div>

                            <div class="section-title mt-3">Aturan Pakai (Signa)</div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label>Frekuensi <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control text-center" id="r_signa_1"
                                            placeholder="3" value="3">
                                        <span class="input-group-text">x</span>
                                        <input type="number" class="form-control text-center" id="r_signa_2"
                                            placeholder="1" value="1">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label>Jadwal Minum <span class="text-danger">*</span></label>
                                    <select class="form-select select-penggunaan" id="r_waktu">
                                    </select>
                                </div>
                            </div>

                            <button type="button" class="btn btn-primary w-100 fw-bold rounded-pill"
                                onclick="addRacikan()">
                                <i class="fa fa-plus"></i> Masukkan ke Resep
                            </button>
                        </div>
                        <!-- ================= END FORM RACIKAN ================= -->

                    </div>
                </div>
            </div>
        @endif
        <!-- RIGHT PANEL: CART / PREVIEW RESEP -->
        <div class="{{ $status->status == 'Di Periksa' ? 'col-lg-7' : 'col-lg-12' }}">
            <div class="card shadow-sm border-0 h-100" style="background-color: #f8fafc;">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="fw-bold mb-3 text-dark ">
                            <span>Kertas Resep (Preview)</span> <br>
                            <span class="badge bg-primary rounded-pill" id="total-item-badge">0 Item</span>
                        </h4>
                        <a class="btn btn-outline-info rounded-pill"
                            onclick="openPopup(`{{ url('print/print-resep-pasien/' . base64_encode(base64_encode($status->pasienfk))) }}`)"><i
                                class="fa fa-print"></i> Print
                            Resep</a>
                    </div>
                    @if ($status->status == 'Di Periksa')
                        <div class="info-box mb-3">
                            <i class="iconoir-info-circle"></i> Resep di bawah ini belum tersimpan ke database.
                            Pastikan
                            untuk menekan tombol <b>Submit</b>.
                        </div>
                    @endif

                    <!-- Kertas Resep Area -->
                    <div class="bg-white border rounded p-3 mb-3 flex-grow-1"
                        style="min-height: 400px; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.03);">
                        <div class="table-responsive">
                            <table class="cart-table" id="cart-table">
                                <thead>
                                    <tr>
                                        <th width="8%">R/</th>
                                        <th width="45%">Deskripsi Obat & Signa</th>
                                        <th width="12%">Qty</th>
                                        <th width="20%">Subtotal Harga</th>
                                        @if ($status->status == 'Di Periksa')
                                            <th width="15%" class="text-center">Aksi</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody id="cart-body">
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-5">
                                            <i class="fa fa-shopping-cart mb-2" style="font-size: 40px;"></i><br>
                                            Belum ada obat yang dimasukkan ke dalam resep.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Summary & Submit -->
                    <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded border mb-3">
                        <span class="fw-bold text-secondary">Estimasi Total Harga:</span>
                        <span class="fw-bold text-primary fs-4" id="grand-total-label">Rp 0</span>
                    </div>
                    @if ($status->status == 'Di Periksa')
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-danger rounded-pill px-4"
                                onclick="clearCart()">Kosongkan</button>
                            <button class="btn btn-success rounded-pill w-100 fw-bold fs-5"
                                onclick="savePrescription()">
                                <i class="iconoir-check-circle"></i> Submit
                            </button>

                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        selectProduct();
        selectPenggunaan();
        cart = [];
        rCounter = 1;
        loadData()
    });

    // Render Cart UI
    function renderCart() {
        let tbody = $('#cart-body');
        tbody.empty();
        let grandTotal = 0;

        if (cart.length === 0) {
            tbody.html(
                `<tr><td colspan="5" class="text-center text-muted py-5"><i class="iconoir-shopping-cart" style="font-size: 40px; color: #cbd5e1;"></i><br>Belum ada obat yang dimasukkan ke dalam resep.</td></tr>`
            );
            $('#grand-total-label').text('Rp 0');
            $('#total-item-badge').text('0 Item');
            return;
        }

        cart.forEach((item, index) => {
            grandTotal += item.subtotal;

            if (item.type === 'non-racik') {
                // HTML untuk Non-Racik
                tbody.append(`
                        <tr>
                            <td class="r-number">R/ ${item.r_num}</td>
                            <td>
                                <strong class="text-dark fs-6">${item.obat_name}</strong><br>
                                <span class="signa-badge text-info mt-1 d-inline-block">${item.signa}</span>
                            </td>
                            <td class="fw-bold text-black">${item.qty} ${item.satuan}</td>
                            <td class="text-end fw-bold text-secondary">${formatRupiah(item.subtotal)}</td>
                            @if ($status->status == 'Di Periksa')
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-danger rounded-pill" onclick="removeItem(${index})"><i class="iconoir-trash"></i></button>
                            </td>
                            @endif
                        </tr>
                    `);
            } else {
                // HTML untuk Racikan (Header + Sub rows)
                let ingHtml = item.ingredients.map(ing => `
                        <tr class="row-racikan-item">
                            <td></td>
                            <td colspan="4"><i class="iconoir-arrow-right text-muted"></i> ${ing.name} <span class="badge bg-light text-dark border ms-2">Qty: ${ing.qty}</span></td>
                        </tr>
                    `).join('');

                tbody.append(`
                        <tr class="row-racikan-header">
                            <td class="r-number">R/ ${item.r_num}</td>
                            <td>
                                <strong class="text-dark fs-6">Racikan: ${item.nama_racikan}</strong> <span class="text-warning">(${item.bentuk})</span><br>
                                <span class="signa-badge text-info mt-1 d-inline-block">${item.signa}</span>
                            </td>
                            <td class="fw-bold text-black">${item.qty_buat} ${item.satuan}</td>
                            <td class="text-end fw-bold text-secondary">
                                ${formatRupiah(item.subtotal)}<br>
                                <small style="font-size:10px; font-weight:normal;">(+ Jasa: ${formatRupiah(item.jasa_racik)})</small>
                            </td>
                            @if ($status->status == 'Di Periksa')
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-danger rounded-pill rounded-circle" onclick="removeItem(${index})"><i class="iconoir-trash"></i></button>
                            </td>
                            @endif

                        </tr>
                        ${ingHtml}
                    `);
            }
        });

        $('#grand-total-label').text(formatRupiah(grandTotal));
        $('#total-item-badge').text(`${cart.length} Resep (R/)`);
    }

    function loadData() {
        $.ajax({
            url: `<?= url('pemeriksaan/pasien/' . $id) ?>/resep/load`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                let data = response.data
                data.map((item) => {
                    cart.push({
                        type: item.type,
                        r_num: rCounter++,
                        obat_id: item.obat_id,
                        nama_racikan: item.nama_racikan,
                        bentuk: item.bentuk,
                        obat_name: item.obat_name,
                        qty: item.qty,
                        qty_buat: item.qty_buat,
                        harga_satuan: item.harga_satuan,
                        satuan: item.satuan,
                        penggunaan_id: item.penggunaan_id,
                        ingredients: item.ingredients,
                        signa: item.signa,
                        subtotal: item.subtotal,
                        jasa_racik: item.jasa_racik
                    });

                })
                renderCart()

            },
            error: function(xhr) {
                renderCart()
                const msg = xhr.responseJSON?.error || 'Terjadi kesalahan.';
                Toast.fire({
                    icon: "error",
                    title: msg
                });
            },

        })
    }
</script>
@if ($status->status == 'Di Periksa')
    <script>
        // Toggle Racik/Non-Racik
        function setResepType(type) {
            $('.toggle-btn').removeClass('active');
            if (type === 'non-racik') {
                $('#btn-non-racik').addClass('active');
                $('#form-non-racik').show();
                $('#form-racik').hide();
            } else {
                $('#btn-racik').addClass('active');
                $('#form-racik').show();
                $('#form-non-racik').hide();
            }
        }

        // On Obat Select (Non-Racik) - Auto fill info
        $('#nr_obat').on('select2:select', function(e) {
            // Select2 menyimpan seluruh object data dari JSON API di sini
            let data = e.params.data;

            if (data.id) {
                // Pastikan nama property (satuan_name, qty, harga) sesuai dengan JSON dari API Anda
                $('#nr_satuan_label').text(data.satuan || 'Unit');
                $('#nr_stok').text((data.stok || 0) + ' ' + (data.satuan || ''));
                $('#nr_harga').text(formatRupiah(data.harga || 0).replace('Rp', ''));
            }
        });

        // Event untuk reset saat pilihan dihapus (jika allowClear: true)
        $('#nr_obat').on('select2:unselect', function() {
            $('#nr_satuan_label').text('Unit');
            $('#nr_stok').text('-');
            $('#nr_harga').text('-');
        });

        // ================= LOGIKA KERANJANG (CART) =================

        // Tambah Non-Racikan ke Cart
        function addNonRacik() {
            let obatOpt = $('#nr_obat').select2('data')[0];

            if (!obatOpt) {
                Toast.fire({
                    icon: 'warning',
                    title: 'Pilih obat dan masukkan Qty yang benar.'
                })
                return;
            }

            let obatId = obatOpt.id;
            let obatName = obatOpt.text;
            let qty = parseFloat($('#nr_qty').val());
            let harga = parseFloat(obatOpt.harga) || 0;

            if (!obatId || qty < 1) {
                Toast.fire({
                    icon: 'warning',
                    title: 'Pilih obat dan masukkan Qty yang benar.'
                })
                return;
            }

            // Build Signa String
            let sig1 = $('#nr_signa_1').val();
            let sig2 = $('#nr_signa_2').val();
            let waktu = $('#nr_waktu').select2('data')[0];
            if (!waktu) {
                Toast.fire({
                    icon: 'error',
                    title: 'Silahkan Pilih Waktu Konsumsi'
                })
                return;
            }
            let waktuId = $('#nr_waktu').select2('data')[0].id;
            let rute = $('#nr_rute').val();
            let ket = $('#nr_keterangan').val();

            // Get Checkboxes
            let times = [];
            if ($('#nr_w_p').is(':checked')) times.push('Pagi');
            if ($('#nr_w_s').is(':checked')) times.push('Siang');
            if ($('#nr_w_sr').is(':checked')) times.push('Sore');
            if ($('#nr_w_m').is(':checked')) times.push('Malam');
            let timeStr = times.length > 0 ? ` (${times.join(', ')})` : '';

            let signaFull = `S ${sig1} x ${sig2} ${obatOpt.satuan} ${rute} - ${waktu.text}${timeStr}`;
            if (ket) signaFull += `<br><small class="text-muted">Ket: ${ket}</small>`;

            let subtotal = qty * harga;

            let item = {
                type: 'non-racik',
                r_num: rCounter++,
                obat_id: obatId,
                nama_racikan: null,
                bentuk: null,
                obat_name: obatName,
                qty: qty,
                qty_buat: null,
                harga_satuan: harga,
                satuan: obatOpt.satuan,
                penggunaan_id: waktuId,
                ingredients: [],
                signa: signaFull,
                subtotal: subtotal,
                jasa_racik: 0
            };

            cart.push(item);
            resetFormNonRacik();
            renderCart();
        }

        // Logic Form Racikan Dinamis
        function addIngredientRow() {
            let html = `
                <div class="row mb-2 ingredient-row align-items-end">
                    <div class="col-6">
                        <select class="form-select ing-obat select-product">
                            <option value="">Pilih Obat...</option>

                        </select>
                    </div>
                    <div class="col-4">
                        <input type="number" class="form-control ing-qty" placeholder="Qty" step="0.1" value="1">
                    </div>
                    <div class="col-2 text-center">
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeIngredient(this)"><i class="iconoir-trash"></i></button>
                    </div>
                </div>
            `;
            $('#ingredients-container').append(html);
            selectProduct()
        }

        function removeIngredient(btn) {
            $(btn).closest('.ingredient-row').remove();
        }

        // Tambah Racikan ke Cart
        function addRacikan() {
            let namaRacikan = $('#r_nama').val();
            let bentuk = $('#r_bentuk').val();
            let qtyBuat = parseFloat($('#r_qty_buat').val());

            if (!namaRacikan || qtyBuat < 1) {
                Toast.fire({
                    icon: 'warning',
                    title: 'Nama Racikan dan Jumlah Kemasan wajib diisi.'
                })
                return;
            }

            let ingredients = [];
            let subtotalRacikan = 0;
            let isValid = true;

            $('.ingredient-row').each(function() {
                let opt = $(this).find('.ing-obat').select2('data')[0];
                console.log("opt", opt)
                let id = opt.id;
                let name = opt.text;
                let qty = parseFloat($(this).find('.ing-qty').val()) || 0;
                let harga = parseFloat(opt.harga) || 0;

                if (id && qty > 0) {
                    let hrgTotalIng = qty * harga;
                    subtotalRacikan += hrgTotalIng;
                    ingredients.push({
                        id: id,
                        name: name,
                        qty: qty
                    });
                } else if (id && qty <= 0) {
                    isValid = false;
                }
            });

            if (ingredients.length === 0 || !isValid) {
                Toast.fire({
                    icon: 'error',
                    title: 'Masukkan minimal 1 obat dengan Qty yang benar untuk racikan.'
                })
                return;
            }

            // Tambahan jasa racik (Opsional)
            let jasaRacik = parseFloat("{{ $racikanprice->value ?? 5000 }}");
            subtotalRacikan += jasaRacik;

            let sig1 = $('#r_signa_1').val();
            let sig2 = $('#r_signa_2').val();
            let waktu = $('#r_waktu').select2('data')[0];
            if (!waktu) {
                Toast.fire({
                    icon: 'error',
                    title: 'Silahkan Pilih Waktu Konsumsi'
                })
                return;
            }
            let waktuId = $('#r_waktu').select2('data')[0].id;
            let satuanRacik = bentuk === 'Pulveres (Puyer)' ? 'Bungkus' : (bentuk === 'Kapsul' ? 'Kapsul' :
                'Pot');

            let signaFull = `S ${sig1} x ${sig2} ${satuanRacik} - ${waktu.text}`;

            let item = {
                type: 'racik',
                r_num: rCounter++,
                obat_id: null,
                nama_racikan: namaRacikan,
                bentuk: bentuk,
                qty: null,
                qty_buat: qtyBuat,
                harga_satuan: 0,
                satuan: satuanRacik,
                penggunaan_id: waktuId,
                ingredients: ingredients,
                signa: signaFull,
                subtotal: subtotalRacikan,
                jasa_racik: jasaRacik
            };


            cart.push(item);
            resetFormRacik();
            renderCart();
        }

        // Actions
        function removeItem(index) {
            cart.splice(index, 1);
            renderCart();
        }

        function clearCart() {
            if (cart.length === 0) return;
            Swal.fire({
                title: 'Kosongkan Resep?',
                text: "Semua obat yang telah diinput akan dihapus.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonText: 'Batal',
                confirmButtonText: 'Ya, Kosongkan'
            }).then((result) => {
                if (result.isConfirmed) {
                    cart = [];
                    rCounter = 1;
                    renderCart();
                }
            });
        }

        function resetFormNonRacik() {
            $('#nr_obat').val('').change();
            $('#nr_qty').val(1);
            $('#nr_keterangan').val('');
            $('.time-checkboxes input').prop('checked', false);
            $('#nr_satuan_label').text('Unit');
            $('#nr_stok').text('-');
            $('#nr_harga').text('-');
        }

        function resetFormRacik() {
            $('#r_nama').val('');
            $('#r_qty_buat').val(15);
            $('#ingredients-container').html(`
                <div class="row mb-2 ingredient-row align-items-end">
                    <div class="col-6">
                        <select class="form-select ing-obat select-product">
                            <option value="">Pilih Obat...</option>
                        </select>
                    </div>
                    <div class="col-4">
                        <input type="number" class="form-control ing-qty" placeholder="Qty" step="0.1" value="1">
                    </div>
                    <div class="col-2 text-center">
                        <button type="button" class="btn btn-danger btn-sm w-100" onclick="removeIngredient(this)"><i class="iconoir-trash"></i></button>
                    </div>
                </div>
            `);
            selectProduct()

        }

        function savePrescription() {
            if (cart.length === 0) {
                Toast.fire({
                    icon: 'warning',
                    title: 'Keranjang resep masih kosong.'
                })
                return;
            }

            Swal.fire({
                title: 'Simpan Resep?',
                text: "Resep akan dikirim ke apotek untuk diproses.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                confirmButtonText: 'Ya, Simpan Final',
                cancelButtonText: 'Cek Kembali'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `<?= url('pemeriksaan/pasien/' . $id) ?>/resep/save`,
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            cart: cart,
                        },
                        success: function(response) {
                            Toast.fire({
                                icon: "success",
                                title: "Data berhasil disimpan"
                            });

                        },
                        error: function(xhr) {
                            Swal.close();
                            const msg = xhr.responseJSON?.error || 'Terjadi kesalahan.';
                            Toast.fire({
                                icon: "error",
                                title: msg
                            });
                        },
                    })
                }
            });
        }
    </script>
@endif
