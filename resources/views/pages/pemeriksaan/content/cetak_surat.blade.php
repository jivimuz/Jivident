<!--
  Letakkan kode ini di dalam Tab atau Panel khusus "Surat Keterangan"
  pada halaman Form Pemeriksaan / Rekam Medis Pasien Anda.
-->

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
</style>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom pb-3 pt-4">
        <h5 class="card-title fw-bold text-primary mb-0">
            <i class="iconoir-printer me-2"></i>Cetak Surat Keterangan Medis
        </h5>
        <small class="text-muted">Pilih jenis surat yang ingin diterbitkan untuk pasien ini.</small>
    </div>
    <div class="card-body p-4">
        <div class="row g-4">

            <!-- Tombol Surat Sakit -->
            <div class="col-md-4 col-sm-6">
                <div class="surat-card p-4 text-center h-100" onclick="bukaModalSakit()">
                    <div class="surat-icon-box bg-soft-danger mx-auto">
                        <i class="iconoir-home-hospital" style="font-size: 30px;"></i>
                    </div>
                    <h6 class="fw-bold mb-1">Surat Izin Sakit</h6>
                    <p class="text-muted mb-0" style="font-size: 12px;">Cetak surat keterangan istirahat karena sakit.
                    </p>
                </div>
            </div>

            <!-- Tombol Surat Sehat -->
            <div class="col-md-4 col-sm-6">
                <div class="surat-card p-4 text-center h-100" onclick="bukaModalSehat()">
                    <div class="surat-icon-box bg-soft-success mx-auto">
                        <i class="iconoir-database-script" style="font-size: 30px;"></i>
                    </div>
                    <h6 class="fw-bold mb-1">Surat Keterangan Sehat</h6>
                    <p class="text-muted mb-0" style="font-size: 12px;">Cetak surat sehat (BB, TB, Golongan Darah,
                        Tensi).</p>
                </div>
            </div>

            <!-- Anda bisa menambahkan jenis surat lain di sini (misal: Rujukan, dll) -->
            <!-- Tombol Surat Rujukan -->
            <div class="col-md-4 col-sm-6">
                <div class="surat-card p-4 text-center h-100" onclick="bukaModalRujukan()">
                    <div class="surat-icon-box bg-soft-primary mx-auto">
                        <i class="iconoir-car" style="font-size: 30px;"></i>
                    </div>
                    <h6 class="fw-bold mb-1">Surat Rujukan</h6>
                    <p class="text-muted mb-0" style="font-size: 12px;">Rujuk pasien ke Faskes tingkat lanjut.</p>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- ====================================================================== -->
<!-- MODAL POPUP: SURAT SAKIT -->
<!-- ====================================================================== -->
<div class="modal fade" id="modalSuratSakit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger bg-opacity-10 text-danger border-0">
                <h5 class="modal-title fw-bold"><i class="iconoir-home-hospital me-2"></i>Parameter Surat Sakit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3 p-2 bg-light rounded text-center border">
                    <strong id="sakit_display_nama" class="text-dark"></strong>
                </div>

                <form id="form-cetak-sakit" method="GET">
                    <!-- Lempar ID Daftar Pasien ke Controller Print -->

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="fw-bold mb-1" style="font-size: 13px;">Mulai Tanggal</label>
                            <input type="date" name="tgl_mulai" class="form-control" value="{{ date('Y-m-d') }}"
                                required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="fw-bold mb-1" style="font-size: 13px;">Lama Istirahat</label>
                            <div class="input-group">
                                <input type="number" name="lama_hari" class="form-control" value="3"
                                    min="1" required>
                                <span class="input-group-text bg-white">Hari</span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <button type="button" class="btn btn-light rounded-pill px-4"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">
                            <i class="iconoir-printer me-1"></i> Cetak Surat
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ====================================================================== -->
<!-- MODAL POPUP: SURAT SEHAT -->
<!-- ====================================================================== -->
<div class="modal fade" id="modalSuratSehat" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success bg-opacity-10 text-success border-0">
                <h5 class="modal-title fw-bold"><i class="iconoir-database-script me-2"></i>Parameter Surat Sehat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3 p-2 bg-light rounded text-center border">
                    <strong id="sehat_display_nama" class="text-dark"></strong>
                </div>

                <form id="form-cetak-sehat" method="GET">
                    <!-- Lempar ID Daftar Pasien ke Controller Print -->

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="fw-bold mb-1" style="font-size: 13px;">Tinggi Badan</label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="tb" value="{{ $vitalsign->tinggibadan ?? 0 }}"
                                    id="sehat_tb" class="form-control" placeholder="160" required>
                                <span class="input-group-text bg-white">cm</span>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="fw-bold mb-1" style="font-size: 13px;">Berat Badan</label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="bb" id="sehat_bb"
                                    value="{{ $vitalsign->beratbadan ?? 0 }}" class="form-control" placeholder="60"
                                    required>
                                <span class="input-group-text bg-white">kg</span>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="fw-bold mb-1" style="font-size: 13px;">Tekanan Darah</label>
                            <div class="input-group input-group-sm">
                                <input type="text" name="td" id="sehat_td" class="form-control"
                                    placeholder="120/80"
                                    value="{{ $vitalsign->siastole ?? 0 }}/{{ $vitalsign->diastole ?? 0 }}" required>
                                <span class="input-group-text bg-white">mmHg</span>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="fw-bold mb-1" style="font-size: 13px;">Golongan Darah</label>
                            <select name="goldar" id="sehat_goldar" class="form-select form-select-sm">
                                <option value="-" selected>Tidak Tahu / -</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="AB">AB</option>
                                <option value="O">O</option>
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="fw-bold mb-1" style="font-size: 13px;">Buta Warna</label>
                            <select name="buta_warna" class="form-select form-select-sm" required>
                                <option value="Tidak">Tidak</option>
                                <option value="Ya (Parsial)">Ya (Parsial)</option>
                                <option value="Ya (Total)">Ya (Total)</option>
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="fw-bold mb-1" style="font-size: 13px;">Visus Mata</label>
                            <input type="text" name="visus" class="form-control form-control-sm"
                                placeholder="Normal / ODS 6/6" value="Normal" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="fw-bold mb-1" style="font-size: 13px;">Keperluan / Tujuan</label>
                            <input type="text" name="keperluan" class="form-control form-control-sm"
                                placeholder="Contoh: Persyaratan Melamar Pekerjaan" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <button type="button" class="btn btn-light rounded-pill px-4"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">
                            <i class="iconoir-printer me-1"></i> Cetak Surat
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ====================================================================== -->
<!-- MODAL POPUP: SURAT RUJUKAN -->
<!-- ====================================================================== -->
<div class="modal fade" id="modalSuratRujukan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary bg-opacity-10 text-primary border-0">
                <h5 class="modal-title fw-bold"><i class="iconoir-car me-2"></i>Parameter Surat Rujukan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3 p-2 bg-light rounded text-center border">
                    <strong id="rujukan_display_nama" class="text-dark"></strong>
                </div>

                <form id="form-cetak-rujukan" method="GET">
                    <!-- Lempar ID Daftar Pasien ke Controller Print -->
                    <input type="hidden" name="daftarpasien_id" id="rujukan_daftarpasien_id">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold mb-1" style="font-size: 13px;">Spesialis Tujuan <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="spesialis_tujuan" class="form-control form-control-sm"
                                placeholder="Contoh: Penyakit Dalam / Saraf" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold mb-1" style="font-size: 13px;">RS / Faskes Tujuan <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="rs_tujuan" class="form-control form-control-sm"
                                placeholder="Contoh: RSUD Kota Medika" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="fw-bold mb-1" style="font-size: 13px;">Keluhan Utama <span
                                    class="text-danger">*</span></label>
                            <textarea name="keluhan" class="form-control form-control-sm" rows="2"
                                placeholder="Contoh: Nyeri dada menjalar ke punggung sejak 2 hari yang lalu..." required>{{ $status->status ?? '-' }}</textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="fw-bold mb-1" style="font-size: 13px;">Pemeriksaan Fisik <span
                                    class="text-danger">*</span></label>
                            <textarea name="pemeriksaan_fisik" id="rujukan_fisik" class="form-control form-control-sm" rows="2"
                                placeholder="Contoh: TD: 160/100 mmHg, N: 110 x/m, S: 37 C, RR: 24 x/m." required>TD: {{ $vitalsign->siastole }}/{{ $vitalsign->diastole }} mmHg, N: {{ $vitalsign->nadi }} x/m, S: {{ $vitalsign->suhu }}, RR: {{ $vitalsign->pernapasan }} x/m</textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="fw-bold mb-1" style="font-size: 13px;">Diagnosis Sementara <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="diagnosis" id="rujukan_diagnosis"
                                class="form-control form-control-sm"
                                placeholder="Contoh: Obs. Chest Pain e.c Susp. CAD"
                                value="{{ $diagnosa && $diagnosa->diagnosa ? $diagnosa->diagnosa : '' }}" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="fw-bold mb-1" style="font-size: 13px;">Terapi yang telah diberikan</label>
                            <input type="text" name="terapi" class="form-control form-control-sm"
                                placeholder="Contoh: Oksigen 3 LPM, ISDN 5mg Sublingual">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <button type="button" class="btn btn-light rounded-pill px-4"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                            <i class="iconoir-printer me-1"></i> Cetak Surat
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Letakkan script ini di bagian @section('script') halaman pemeriksaan Anda -->
    <script>
        var idDaftarPasien = "{{ $id }}"
        var namaPasien = "{{ $status->nama }}"

        function bukaModalSakit() {
            if (!idDaftarPasien) {
                Swal.fire('Error', 'ID Daftar Pasien tidak ditemukan. Silakan refresh halaman.', 'error');
                return;
            }
            $('#sakit_display_nama').text(namaPasien);
            $('#modalSuratSakit').modal('show');
        }

        function bukaModalSehat() {
            if (!idDaftarPasien) {
                Swal.fire('Error', 'ID Daftar Pasien tidak ditemukan. Silakan refresh halaman.', 'error');
                return;
            }

            $('#sehat_display_nama').text(namaPasien);

            // OPSIONAL: Jika data Tanda Vital sudah pernah diinput di form pemeriksaan (SOAP),
            // Anda bisa me-load/auto-fill ke form surat sehat ini.
            // Contoh:
            // $('#sehat_tb').val( $('#input_tb_pemeriksaan').val() );
            // $('#sehat_bb').val( $('#input_bb_pemeriksaan').val() );
            // $('#sehat_td').val( $('#input_td_pemeriksaan').val() );

            $('#modalSuratSehat').modal('show');
        }

        function bukaModalRujukan() {
            if (!idDaftarPasien) {
                Swal.fire('Error', 'ID Daftar Pasien tidak ditemukan. Silakan refresh halaman.', 'error');
                return;
            }

            $('#rujukan_daftarpasien_id').val(idDaftarPasien);
            $('#rujukan_display_nama').text(namaPasien);

            // OPSIONAL: Jika diagnosis dan hasil pemeriksaan (SOAP) sudah terisi di form pemeriksaan,
            // bisa di auto-fill ke form surat rujukan di sini
            // $('#rujukan_diagnosis').val( $('#input_diagnosis').val() );

            $('#form-cetak-rujukan')[0].reset(); // Kosongkan form sebelum tampil
            $('#modalSuratRujukan').modal('show');
        }

        $('#form-cetak-sehat').submit(function(e) {
            e.preventDefault()
            let data = $(this).serialize();

            openPopup("{{ url('print/print-surat-sehat-pasien/' . $id) }}?" + data, true)
        })
        $('#form-cetak-sakit').submit(function(e) {
            e.preventDefault()
            let data = $(this).serialize();
            openPopup("{{ url('print/print-surat-sakit-pasien/' . $id) }}?" + data, true)
        })
        $('#form-cetak-rujukan').submit(function(e) {
            e.preventDefault()
            let data = $(this).serialize();
            openPopup("{{ url('print/print-surat-rujukan-pasien/' . $id) }}?" + data, true)
        })
    </script>
