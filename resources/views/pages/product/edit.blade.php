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

        .form-switch .form-check-input {
            width: 2.5em;
            height: 1.25em;
            cursor: pointer;
        }

        .form-switch .form-check-label {
            margin-top: 2px;
            margin-left: 10px;
            cursor: pointer;
            font-weight: 500;
        }

        .bg-light-primary {
            background-color: rgba(58, 87, 232, 0.05);
            border-radius: 10px;
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
                        Tambah Master Obat / Farmasi
                    </span>
                    <div style="width:45px;" class="flex-shrink-0"></div>
                </div>
            </div>
        </div>

        <!-- Form Content -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <form id="form-produk-farmasi">
                            <!-- SECTION 1: Informasi Dasar -->
                            <h5 class="form-section-title"><i class="iconoir-box-iso"></i> Informasi Dasar</h5>
                            <div class="row mb-4">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">Kode Obat <span class="text-danger">*</span></label>
                                    <input type="text" name="code" class="form-control text-uppercase"
                                        placeholder="Contoh: OBT-001" value="{{ $data->code ?? '' }}" required>
                                </div>
                                <div class="col-md-5 mb-3">
                                    <label class="form-label fw-bold">Nama Obat <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control"
                                        placeholder="Contoh: Paracetamol" value="{{ $data->name ?? '' }}" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Kekuatan (Strength) <span
                                            class="text-danger">*</span></label>

                                    <div class="input-group">
                                        <input type="number" name="strength" id="strength" oninput="noMinus(this)"
                                            class="form-control" placeholder="Contoh: 10" min="0"
                                            value="{{ $data->strength ?? 0 }}" required step="1">

                                        <select name="satuanfk" id="satuanfk" class="form-select "
                                            style="max-width: 140px;" required>
                                            @foreach ($satuan as $i)
                                                <option value="{{ $i->id }}"
                                                    {{ $i->id == $data->satuanfk ? 'selected' : '' }}>
                                                    {{ $i->code }}
                                                    ({{ $i->name }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <small class="text-muted">Contoh hasil: 10 mg, 500 ml, 1 g</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Kategori (FK)</label>
                                    <select name="kategorifk" class="form-select select2">
                                        <option value="" selected hidden>-- Pilih Kategori --</option>
                                        <!-- Contoh Value menggunakan ID -->
                                        @foreach ($categories as $i)
                                            <option value="{{ $i->id }}"
                                                {{ $i->id == $data->kategorifk ? 'selected' : '' }}> {{ $i->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Unit</label>
                                    <select name="unitfk" class="form-select select2">
                                        <option value="" selected hidden>-- Pilih Satuan --</option>
                                        <!-- Contoh Value menggunakan ID -->
                                        @foreach ($unit as $i)
                                            <option value="{{ $i->id }}"
                                                {{ $i->id == $data->unitfk ? 'selected' : '' }}> {{ $i->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- SECTION 2: Klasifikasi & Atribut Obat -->
                            <h5 class="form-section-title"><i class="iconoir-shield-check"></i> Klasifikasi Obat</h5>
                            <div class="row mb-4 bg-light-primary p-3 mx-0">
                                <div class="col-md-4 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_fornas" name="is_fornas"
                                            value="1" {{ $data->is_fornas ? 'checked' : '' }}>
                                        <label class="form-check-label text-dark" for="is_fornas">Fornas (Formularium
                                            Nasional)</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_generik" name="is_generik"
                                            value="1" {{ $data->is_generik ? 'checked' : '' }}>
                                        <label class="form-check-label text-dark" for="is_generik">Obat Generik</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_antibiotik"
                                            name="is_antibiotik" value="1"
                                            {{ $data->is_antibiotik ? 'checked' : '' }}>
                                        <label class="form-check-label text-danger" for="is_antibiotik">Antibiotik</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_psikotropika"
                                            name="is_psikotropika" value="1"
                                            {{ $data->is_psikotropika ? 'checked' : '' }}>
                                        <label class="form-check-label text-danger"
                                            for="is_psikotropika">Psikotropika</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_narkotika"
                                            name="is_narkotika" value="1"
                                            {{ $data->is_narkotika ? 'checked' : '' }}>
                                        <label class="form-check-label text-danger fw-bold"
                                            for="is_narkotika">Narkotika</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_kronis" name="is_kronis"
                                            value="1" {{ $data->is_kronis ? 'checked' : '' }}>
                                        <label class="form-check-label text-dark" for="is_kronis">Obat Penyakit
                                            Kronis</label>
                                    </div>
                                </div>
                            </div>

                            <!-- SECTION 3: Harga & Keuangan -->
                            <h5 class="form-section-title"><i class="iconoir-coins"></i> Setup Harga</h5>
                            <div class="row mb-4">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Harga Beli (Modal)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" id="harga_beli" name="harga_beli" class="form-control"
                                            placeholder="0" min="0" value="{{ $data->harga_beli ?? '' }}">
                                    </div>
                                    <small class="text-muted">Harga dasar dari PBF/Distributor</small>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">HPP (Harga Pokok Penjualan)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" id="hpp" name="hpp" class="form-control"
                                            placeholder="0" min="0" value="{{ $data->hpp ?? '' }}">
                                    </div>
                                    <small class="text-muted">Auto-copy dari Harga Beli (bisa diedit jika ada PPN)</small>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold text-primary">Harga Jual</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-primary text-white border-primary">Rp</span>
                                        <input type="number" id="harga_jual" name="harga_jual"
                                            class="form-control border-primary fw-bold" placeholder="0" min="0"
                                            value="{{ $data->harga_jual ?? '' }}">
                                    </div>
                                    <small class="text-muted">Margin: <span id="margin-percentage"
                                            class="fw-bold text-success">0%</span></small>
                                </div>
                            </div>

                            <hr class="mt-4 mb-4">

                            <div class="d-flex justify-content-end gap-2">
                                <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold" id="btn-submit">
                                    <i class="fa fa-savek"></i> Simpan Data Obat
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // Setup CSRF Token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('.select2').select2()

            // 1. Fitur Auto-Calculate Pricing
            $('#harga_beli').on('input', function() {
                let hargaBeli = parseFloat($(this).val()) || 0;

                // Secara default, set HPP sama dengan Harga Beli
                // (Jika apotek Anda menghitung PPN 11%, bisa ubah rumusnya jadi: hargaBeli * 1.11)
                $('#hpp').val(hargaBeli);

                calculateMargin();
            });

            $('#hpp, #harga_jual').on('input', function() {
                calculateMargin();
            });

            function calculateMargin() {
                let hpp = parseFloat($('#hpp').val()) || 0;
                let hargaJual = parseFloat($('#harga_jual').val()) || 0;
                let marginText = $('#margin-percentage');

                if (hpp > 0 && hargaJual >= hpp) {
                    let profit = hargaJual - hpp;
                    let marginPercent = (profit / hpp) * 100;
                    marginText.text(marginPercent.toFixed(2) + '%').removeClass('text-danger').addClass(
                        'text-success');
                } else if (hargaJual < hpp && hargaJual > 0) {
                    marginText.text('Rugi!').removeClass('text-success').addClass('text-danger');
                } else {
                    marginText.text('0%').removeClass('text-danger').addClass('text-success');
                }
            }

            // 2. Submit Form via AJAX
            $('#form-produk-farmasi').on('submit', function(e) {
                e.preventDefault();

                let btnSubmit = $('#btn-submit');
                let originalText = btnSubmit.html();

                // Validasi agar harga jual tidak lebih rendah dari HPP
                let hpp = parseFloat($('#hpp').val()) || 0;
                let hargaJual = parseFloat($('#harga_jual').val()) || 0;

                if (hargaJual < hpp) {
                    Swal.fire('Peringatan', 'Harga Jual tidak boleh lebih kecil dari HPP!', 'warning');
                    return;
                }

                btnSubmit.html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...'
                ).prop('disabled', true);

                $.ajax({
                    url: "{{ url('product/update/' . $id) }}", // Ganti dengan route POST Anda
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Data master obat berhasil ditambahkan!',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            // Redirect ke halaman list produk atau reset form
                            window.location.href = "{{ url('product') }}";
                            // $('#form-produk-farmasi')[0].reset();
                            // $('#margin-percentage').text('0%').removeClass(
                            //     'text-danger').addClass('text-success');
                        });
                    },
                    error: function(xhr) {
                        let errorMsg = "Terjadi kesalahan sistem.";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            // Menangkap response error validasi (seperti error unik code)
                            errorMsg = typeof xhr.responseJSON.message === 'string' ? xhr
                                .responseJSON.message :
                                "Periksa kembali data yang Anda masukkan.";
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
        });
    </script>
@endsection
