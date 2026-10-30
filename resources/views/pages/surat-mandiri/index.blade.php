@extends('layout/main')
@section('style')
    <style>
        .surat-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            transition: all 0.3s ease;
            cursor: pointer;
            background-color: #ffffff;
        }

        .surat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
            border-color: #3a57e8;
        }

        .surat-icon-box {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }

        .bg-soft-danger {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        .bg-soft-success {
            background-color: rgba(25, 135, 84, 0.1);
            color: #198754;
        }

        .bg-soft-primary {
            background-color: rgba(58, 87, 232, 0.1);
            color: #3a57e8;
        }

        .bg-soft-secondary {
            background-color: rgba(108, 117, 125, 0.1);
            color: #6c757d;
        }
    </style>
@endsection

@section('content')
    <div class="content-inner mt-5 py-0">

        @if ($harga <= 0)
            <div class="alert alert-warning">
                <i class="iconoir-warning-triangle me-1"></i>
                Biaya surat keterangan belum diatur (masih Rp 0). Atur di menu <b>Pengaturan &raquo; Setting</b>,
                parameter "Biaya Cetak Surat Keterangan", sebelum menerbitkan surat.
            </div>
        @endif

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom pb-3 pt-4">
                <h5 class="card-title fw-bold text-primary mb-0">
                    <i class="iconoir-submit-document me-2"></i>Surat Keterangan Mandiri
                </h5>
                <small class="text-muted">Untuk yang mau bikin surat keterangan tanpa periksa. Tetap dikenakan biaya
                    sebesar <b>Rp {{ number_format($harga, 0, ',', '.') }}</b>, dibayar langsung saat surat
                    diterbitkan.</small>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-3 col-sm-6">
                        <div class="surat-card p-4 text-center h-100" onclick="bukaModal('sehat')">
                            <div class="surat-icon-box bg-soft-success mx-auto">
                                <i class="iconoir-database-script" style="font-size: 28px;"></i>
                            </div>
                            <h6 class="fw-bold mb-1">Surat Keterangan Sehat</h6>
                            <p class="text-muted mb-0" style="font-size: 12px;">Tanpa periksa, data diisi manual.</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="surat-card p-4 text-center h-100" onclick="bukaModal('sakit')">
                            <div class="surat-icon-box bg-soft-danger mx-auto">
                                <i class="iconoir-home-hospital" style="font-size: 28px;"></i>
                            </div>
                            <h6 class="fw-bold mb-1">Surat Izin Sakit</h6>
                            <p class="text-muted mb-0" style="font-size: 12px;">Tanpa periksa, data diisi manual.</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="surat-card p-4 text-center h-100" onclick="bukaModal('rujukan')">
                            <div class="surat-icon-box bg-soft-primary mx-auto">
                                <i class="iconoir-car" style="font-size: 28px;"></i>
                            </div>
                            <h6 class="fw-bold mb-1">Surat Rujukan</h6>
                            <p class="text-muted mb-0" style="font-size: 12px;">Tanpa periksa, data diisi manual.</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="surat-card p-4 text-center h-100" onclick="bukaModal('umum')">
                            <div class="surat-icon-box bg-soft-secondary mx-auto">
                                <i class="iconoir-page-edit" style="font-size: 28px;"></i>
                            </div>
                            <h6 class="fw-bold mb-1">Surat Keterangan Umum</h6>
                            <p class="text-muted mb-0" style="font-size: 12px;">Isi surat bebas/free text.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Riwayat Surat Diterbitkan</h4>
                <small>Surat keterangan mandiri yang sudah dibuat &amp; dibayar.</small>
            </div>
            <div class="card-body" style="padding-left: 40px; padding-right:40px">
                <div class="app-datatable-default overflow-auto">
                    <table class="display app-data-table default-data-table table-hover" id="main-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Nama</th>
                                <th>Dokter</th>
                                <th>Metode Bayar</th>
                                <th>Biaya</th>
                                <th>-</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Form: dipakai bersama untuk 4 jenis surat, section jenis-spesifik ditoggle via JS -->
    <div class="modal fade" id="modalSurat" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="modalSuratTitle"><i class="iconoir-submit-document me-2"></i>
                        Terbitkan Surat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formSurat">
                    <div class="modal-body p-4">
                        <input type="hidden" name="jenis" id="jenis">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold mb-1" style="font-size: 13px;">Nama Lengkap <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="fw-bold mb-1" style="font-size: 13px;">Jenis Kelamin</label>
                                <select class="form-select" name="jenis_kelamin">
                                    <option value="">-</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="fw-bold mb-1" style="font-size: 13px;">Umur</label>
                                <input type="text" class="form-control" name="umur" placeholder="Contoh: 30 Tahun">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold mb-1" style="font-size: 13px;">Pekerjaan</label>
                                <input type="text" class="form-control" name="pekerjaan">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold mb-1" style="font-size: 13px;">Keperluan</label>
                                <input type="text" class="form-control" name="keperluan"
                                    placeholder="Contoh: Melamar kerja">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="fw-bold mb-1" style="font-size: 13px;">Alamat</label>
                                <textarea class="form-control" name="alamat" rows="2"></textarea>
                            </div>
                        </div>

                        <hr>

                        <!-- Section: Sakit -->
                        <div class="row jenis-section" data-jenis="sakit" style="display:none">
                            <div class="col-6 mb-3">
                                <label class="fw-bold mb-1" style="font-size: 13px;">Mulai Tanggal</label>
                                <input type="date" name="detail[tanggal_mulai]" class="form-control"
                                    value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="fw-bold mb-1" style="font-size: 13px;">Lama Istirahat</label>
                                <div class="input-group">
                                    <input type="number" name="detail[lama_istirahat]" class="form-control"
                                        value="3" min="1">
                                    <span class="input-group-text bg-white">Hari</span>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="fw-bold mb-1" style="font-size: 13px;">Keterangan (opsional)</label>
                                <input type="text" name="detail[keterangan]" class="form-control"
                                    placeholder="Contoh: Demam">
                            </div>
                        </div>

                        <!-- Section: Sehat -->
                        <div class="row jenis-section" data-jenis="sehat" style="display:none">
                            <div class="col-12 mb-3">
                                <label class="fw-bold mb-1" style="font-size: 13px;">Catatan Kesehatan
                                    (opsional)</label>
                                <textarea class="form-control" name="detail[catatan]" rows="2"
                                    placeholder="Dalam keadaan sehat jasmani dan rohani"></textarea>
                            </div>
                        </div>

                        <!-- Section: Rujukan -->
                        <div class="row jenis-section" data-jenis="rujukan" style="display:none">
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold mb-1" style="font-size: 13px;">RS / Faskes Tujuan <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="detail[tujuan_rujukan]" class="form-control"
                                    placeholder="Contoh: RSUD Kota Medika">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold mb-1" style="font-size: 13px;">Spesialis Tujuan</label>
                                <input type="text" name="detail[spesialis_tujuan]" class="form-control"
                                    placeholder="Contoh: Penyakit Dalam">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="fw-bold mb-1" style="font-size: 13px;">Alasan Rujukan (opsional)</label>
                                <textarea class="form-control" name="detail[alasan_rujukan]" rows="2"></textarea>
                            </div>
                        </div>

                        <!-- Section: Umum -->
                        <div class="row jenis-section" data-jenis="umum" style="display:none">
                            <div class="col-12 mb-3">
                                <label class="fw-bold mb-1" style="font-size: 13px;">Judul Surat <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="detail[judul_surat]" class="form-control"
                                    placeholder="Contoh: Surat Keterangan Domisili">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="fw-bold mb-1" style="font-size: 13px;">Isi Surat <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" name="detail[isi_surat]" rows="4"
                                    placeholder="Tulis isi surat keterangan di sini..."></textarea>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold mb-1" style="font-size: 13px;">Dokter Penandatangan <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" name="fk_employee" required>
                                    <option value="">- Pilih Dokter -</option>
                                    @foreach ($dokters as $d)
                                        <option value="{{ $d->id }}">{{ $d->fullname }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold mb-1" style="font-size: 13px;">Metode Pembayaran <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" name="fk_payment" required>
                                    <option value="">- Pilih Metode Bayar -</option>
                                    @foreach ($payments as $p)
                                        <option value="{{ $p->id }}">{{ $p->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="alert alert-info mb-0">
                            Biaya yang akan dikenakan: <b>Rp {{ number_format($harga, 0, ',', '.') }}</b> — langsung
                            tercatat sebagai pemasukan kasir begitu disimpan.
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light rounded-pill px-4"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold" id="btnSimpanSurat">
                            <i class="iconoir-coin me-1"></i> Simpan &amp; Bayar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        const jenisConfig = {
            sehat: {
                title: 'Surat Keterangan Sehat',
                btnClass: 'btn-success'
            },
            sakit: {
                title: 'Surat Izin Sakit',
                btnClass: 'btn-danger'
            },
            rujukan: {
                title: 'Surat Rujukan',
                btnClass: 'btn-primary'
            },
            umum: {
                title: 'Surat Keterangan Umum',
                btnClass: 'btn-secondary'
            },
        };

        function bukaModal(jenis) {
            $('#formSurat')[0].reset();
            $('#jenis').val(jenis);
            $('#modalSuratTitle').text('Terbitkan ' + jenisConfig[jenis].title);
            $('.jenis-section').hide();
            $('.jenis-section[data-jenis="' + jenis + '"]').show();

            $('#btnSimpanSurat').removeClass('btn-primary btn-success btn-danger btn-secondary')
                .addClass(jenisConfig[jenis].btnClass);

            $('#modalSurat').modal('show');
        }

        $('#formSurat').submit(function(e) {
            e.preventDefault();

            $('#btnSimpanSurat').prop('disabled', true).html('Menyimpan...');

            $.ajax({
                url: "{{ url('surat-mandiri/store') }}",
                type: "POST",
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                complete: function() {
                    $('#btnSimpanSurat').prop('disabled', false).html(
                        '<i class="iconoir-coin me-1"></i> Simpan &amp; Bayar');
                },
                success: function(res) {
                    $('#modalSurat').modal('hide');
                    Toast.fire({
                        icon: 'success',
                        title: 'Surat tersimpan, biaya Rp ' + parseFloat(res.harga).toLocaleString('id-ID')
                    });
                    getList();
                    openPopup("{{ url('print/print-surat-mandiri') }}/" + btoa(btoa(res.id)), true);
                },
                error: function(xhr) {
                    var msg = 'Gagal menyimpan';
                    try {
                        var body = JSON.parse(xhr.responseText);
                        msg = body.error || Object.values(body.errors ?? {}).flat().join(', ') || msg;
                    } catch (e) {}
                    Toast.fire({
                        icon: 'error',
                        title: msg
                    });
                }
            });
        });

        $(document).ready(function() {
            getList();
        });

        function getList() {
            let columns = [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                    orderable: false,
                    searchable: false,
                    width: '10',
                },
                {
                    data: "created_at"
                },
                {
                    data: "jenis"
                },
                {
                    data: "nama"
                },
                {
                    data: "dokter"
                },
                {
                    data: "payment"
                },
                {
                    data: "harga",
                    render: function(data) {
                        return parseFloat(data).toLocaleString("id-ID", {
                            style: "currency",
                            currency: "IDR"
                        });
                    },
                },
                {
                    data: "id",
                    render: function(id) {
                        return `<a class="btn btn-xs btn-outline-primary rounded-pill" href="{{ url('print/print-surat-mandiri') }}/${btoa(btoa(id))}" target="_blank">Cetak Ulang</a>`;
                    },
                },
            ];

            $('#main-table').DataTable({
                destroy: true,
                lengthChange: false,
                responsive: true,
                ajax: {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    url: "{{ url('surat-mandiri/getList') }}",
                    type: "POST",
                    data: {}
                },
                columns: columns,
            });
        }
    </script>
@endsection
