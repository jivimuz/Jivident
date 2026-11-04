@extends('layout/main')

@section('style')
    <style>
        .bed-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .bed-slot {
            width: 58px;
            height: 58px;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 600;
            border: 2px solid transparent;
            transition: transform .1s ease;
        }

        .bed-slot i {
            font-size: 18px;
            margin-bottom: 2px;
        }

        .bed-kosong {
            background: #e8f8ee;
            color: #1f9d55;
            border-color: #bfe8cf;
            cursor: pointer;
        }

        .bed-kosong:hover {
            transform: scale(1.06);
            border-color: #1f9d55;
        }

        .bed-terisi {
            background: #fdeceb;
            color: #d9453d;
            border-color: #f6c9c6;
            cursor: pointer;
        }

        .bed-slot.bed-selected {
            background: #3a57e8;
            color: #fff;
            border-color: #3a57e8;
            transform: scale(1.06);
        }

        .bed-slot.bed-disabled {
            opacity: .45;
            cursor: not-allowed;
        }

        .ruangan-card {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 14px;
            margin-bottom: 14px;
        }

        .ruangan-legend {
            font-size: 12px;
            color: #6b7280;
            display: flex;
            gap: 14px;
            margin-bottom: 10px;
        }

        .ruangan-legend span {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .ruangan-legend .dot {
            width: 10px;
            height: 10px;
            border-radius: 3px;
            display: inline-block;
        }

        .lantai-filter {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 12px;
        }
    </style>
@endsection

@section('content')
    <div class="content-inner mt-5 py-0">
        <div class="row">
            <div class="col-md-5 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title" id="ruanganFormTitle">Tambah Ruangan</h4>
                        <small class="text-muted">Satu ruangan bisa berisi beberapa ranjang — atur jumlahnya di
                            sini, ranjangnya dibuat otomatis.</small>
                    </div>
                    <div class="card-body">
                        <input type="hidden" id="ruanganId">
                        <div class="mb-3">
                            <label class="form-label">Nama Ruangan</label>
                            <input type="text" class="form-control" id="ruanganNama" placeholder="Contoh: Ruang Melati">
                        </div>
                        <div class="row">
                            <div class="col-4 mb-3">
                                <label class="form-label">Lantai</label>
                                <input type="text" class="form-control" id="ruanganLantai" placeholder="Contoh: 2">
                            </div>
                            <div class="col-4 mb-3">
                                <label class="form-label">Kelas</label>
                                <input type="text" class="form-control" id="ruanganKelas" placeholder="Contoh: VIP">
                            </div>
                            <div class="col-4 mb-3">
                                <label class="form-label">Tarif per Hari</label>
                                <input type="number" class="form-control" id="ruanganTarif" min="0">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jumlah Ranjang</label>
                            <input type="number" class="form-control" id="ruanganJumlahRanjang" min="1"
                                max="50" value="1">
                            <small class="text-muted">Mengubah jumlah ini saat edit akan menambah/menghapus ranjang —
                                ranjang yang sedang terisi tidak akan terhapus.</small>
                        </div>
                        <button class="btn btn-success" id="saveRuanganBtn">Simpan</button>
                        <button class="btn btn-outline-secondary" id="resetRuanganBtn" style="display:none">Batal
                            Edit</button>
                    </div>
                </div>

                {{-- <div class="card mt-3">
                    <div class="card-header">
                        <h4 class="card-title">Admisi Baru</h4>
                        <small class="text-muted">Pasien harus sudah terdaftar lewat Pasien Baru/IGD dulu.</small>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Kunjungan Pasien</label>
                            <select class="form-control select2" id="admitDaftarPasien" style="width:100%"></select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pilih Ranjang</label>
                            <input type="hidden" id="admitKamar">
                            <div class="ruangan-legend">
                                <span><span class="dot" style="background:#1f9d55"></span> Kosong — klik untuk
                                    pilih</span>
                                <span><span class="dot" style="background:#d9453d"></span> Terisi</span>
                            </div>
                            <div class="lantai-filter" id="admitLantaiFilter"></div>
                            <div id="admitBedGrid" class="text-muted">Memuat ranjang...</div>
                            <div id="admitBedGridPager"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Dokter Penanggung Jawab</label>
                            <select class="form-control select2" id="admitDokter" style="width:100%"></select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan Masuk</label>
                            <textarea class="form-control" id="admitCatatan" rows="2"></textarea>
                        </div>
                        <button class="btn btn-primary w-100" id="admitBtn">Admisi / Rawat Inap</button>
                    </div>
                </div> --}}


                <div class="card mt-3">
                    <div class="card-header">
                        <div class="float-end">
                            <select class="form-control" id="admissionStatus" style="width:160px">
                                <option value="dirawat" selected>Sedang Dirawat</option>
                                <option value="keluar">Riwayat (Keluar)</option>
                            </select>
                        </div>
                        <h4 class="card-title">Daftar Rawat Inap</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm" id="admissionTable">
                            <thead>
                                <tr>
                                    <th>Pasien</th>
                                    <th>Kamar</th>
                                    <th>Dokter</th>
                                    <th>Tgl Masuk</th>
                                    <th>-</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-7 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Ruangan & Ranjang</h4>
                        <small class="text-muted">Klik ranjang terisi untuk lihat siapa yang menempatinya.</small>
                    </div>
                    <div class="card-body">
                        <div class="ruangan-legend">
                            <span><span class="dot" style="background:#1f9d55"></span> Kosong</span>
                            <span><span class="dot" style="background:#d9453d"></span> Terisi</span>
                        </div>
                        <div class="lantai-filter" id="managementLantaiFilter"></div>
                        <div id="ruanganListContainer">Memuat...</div>
                        <div id="ruanganListPager"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal Catatan Harian -->
    <div class="modal fade" id="modalCatatan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Catatan Harian Rawat Inap</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="catatanRawatInapId">
                    <div class="row">
                        <div class="col-3">
                            <label class="form-label">Suhu</label>
                            <input type="text" class="form-control" id="catatanSuhu">
                        </div>
                        <div class="col-3">
                            <label class="form-label">Nadi</label>
                            <input type="text" class="form-control" id="catatanNadi">
                        </div>
                        <div class="col-3">
                            <label class="form-label">Napas</label>
                            <input type="text" class="form-control" id="catatanPernapasan">
                        </div>
                        <div class="col-3">
                            <label class="form-label">Tensi</label>
                            <input type="text" class="form-control" id="catatanTensi">
                        </div>
                        <div class="col-3 mt-2">
                            <label class="form-label">Kesadaran</label>
                            <select class="form-select" id="catatanKesadaran">
                                <option value="">Pilih</option>
                                <option>Compos Mentis</option>
                                <option>Apatis</option>
                                <option>Somnolen</option>
                                <option>Sopor</option>
                                <option>Koma</option>
                            </select>
                        </div>
                        <div class="col-3 mt-2">
                            <label class="form-label">GCS</label>
                            <input type="text" class="form-control" id="catatanGcs" placeholder="E4V5M6">
                        </div>
                        <div class="col-3 mt-2">
                            <label class="form-label">SpO₂ (%)</label>
                            <input type="number" class="form-control" id="catatanSpo2" min="0" max="100">
                        </div>
                        <div class="col-3 mt-2">
                            <label class="form-label">Skala Nyeri</label>
                            <input type="number" class="form-control" id="catatanSkalaNyeri" min="0" max="10">
                        </div>
                        <div class="col-3 mt-2">
                            <label class="form-label">Intake (ml)</label>
                            <input type="number" class="form-control" id="catatanIntake" min="0" step="0.01">
                        </div>
                        <div class="col-3 mt-2">
                            <label class="form-label">Output (ml)</label>
                            <input type="number" class="form-control" id="catatanOutput" min="0" step="0.01">
                        </div>
                        <div class="col-12 mt-2">
                            <label class="form-label">Catatan</label>
                            <textarea class="form-control" id="catatanIsi" rows="2"></textarea>
                        </div>
                        <div class="col-md-6 mt-2">
                            <label class="form-label">Intervensi Keperawatan</label>
                            <textarea class="form-control" id="catatanIntervensi" rows="2"></textarea>
                        </div>
                        <div class="col-md-6 mt-2">
                            <label class="form-label">Respons Pasien</label>
                            <textarea class="form-control" id="catatanRespons" rows="2"></textarea>
                        </div>
                        <div class="col-12 mt-2">
                            <button class="btn btn-primary btn-sm" id="btnTambahCatatan">Tambah Catatan</button>
                        </div>
                    </div>
                    <hr>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>TTV</th>
                                <th>Observasi & Catatan</th>
                            </tr>
                        </thead>
                        <tbody id="catatanBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Care Plan Keperawatan -->
    <div class="modal fade" id="modalCarePlan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Asuhan Keperawatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="carePlanRawatInapId">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Diagnosis Keperawatan</label>
                            <input type="text" class="form-control" id="carePlanDiagnosis"
                                placeholder="Contoh: Nyeri akut">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Target Tanggal</label>
                            <input type="date" class="form-control" id="carePlanTargetDate">
                        </div>
                        <div class="col-md-4 mb-2 d-flex align-items-end">
                            <button class="btn btn-primary" id="btnSaveCarePlan">Tambah Care Plan</button>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Tujuan / Luaran</label>
                            <textarea class="form-control" id="carePlanGoal" rows="3"
                                placeholder="Hasil yang ingin dicapai"></textarea>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Intervensi</label>
                            <textarea class="form-control" id="carePlanInterventions" rows="3"
                                placeholder="Tindakan keperawatan yang direncanakan"></textarea>
                        </div>
                    </div>
                    <hr>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead>
                                <tr>
                                    <th>Diagnosis & Tujuan</th>
                                    <th>Intervensi</th>
                                    <th>Status & Evaluasi</th>
                                    <th>Target</th>
                                </tr>
                            </thead>
                            <tbody id="carePlanBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Rekam Medis Rawat Inap -->
    <div class="modal fade" id="modalMedicalRecord" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Rekam Medis Rawat Inap</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="medicalRecordRawatInapId">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Jenis Dokumen</label>
                            <select class="form-select" id="medicalRecordType">
                                <option value="cppt">CPPT</option>
                                <option value="instruksi_dokter">Instruksi Dokter</option>
                                <option value="resume_medis">Resume Medis</option>
                                <option value="surat_kontrol">Surat Kontrol</option>
                            </select>
                        </div>
                    </div>
                    <div class="row" id="medicalRecordFields"></div>
                    <button class="btn btn-primary mb-3" id="btnSaveMedicalRecord">Simpan Dokumen</button>
                    <hr>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead>
                                <tr>
                                    <th>Jenis</th>
                                    <th>Isi</th>
                                    <th>Dibuat</th>
                                </tr>
                            </thead>
                            <tbody id="medicalRecordBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Pulangkan -->
    <div class="modal fade" id="modalPulangkan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pulangkan Pasien</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="pulangRawatInapId">
                    <table class="table table-sm" id="billingTable">
                        <tbody></tbody>
                    </table>
                    <div class="mb-3">
                        <label class="form-label">Ringkasan Pulang</label>
                        <textarea class="form-control" id="ringkasanPulang" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="btnKonfirmasiPulang">Konfirmasi Pulang</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Riwayat Pindah Ruangan -->
    <div class="modal fade" id="modalRiwayatPindah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Riwayat Pindah Ruangan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <small class="text-muted d-block mb-2">Pindah ruangan dilakukan dari tab Rawat Inap di halaman
                        Pemeriksaan pasien, bukan di sini.</small>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Dari</th>
                                <th>Ke</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody id="riwayatPindahBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        var ruanganListCache = [];
        var selectedBedId = null;
        var managementLantaiAktif = null;
        var admitLantaiAktif = null;
        var RWI_ROOMS_PER_PAGE = 6;
        var managementPage = 1;
        var admitPage = 1;

        // Dipakai untuk kedua grid ruangan (manajemen & picker admisi) — klinik
        // dengan banyak ruangan bikin daftar kartu jadi sangat panjang kalau
        // ditampilkan semua, jadi dipaging per-ruangan (bukan per-ranjang,
        // karena ranjang dalam satu ruangan tetap ditampilkan utuh).
        function renderPager(containerId, page, totalPages, onGotoPage) {
            if (totalPages <= 1) {
                $('#' + containerId).html('');
                return;
            }
            var html = `<div class="d-flex justify-content-center align-items-center gap-2 mt-2 mb-1">
                <button type="button" class="btn btn-xs btn-outline-secondary" data-rwi-page="${page - 1}" ${page <= 1 ? 'disabled' : ''}><i class="iconoir-nav-arrow-left"></i></button>
                <span class="small text-muted">Halaman ${page} / ${totalPages}</span>
                <button type="button" class="btn btn-xs btn-outline-secondary" data-rwi-page="${page + 1}" ${page >= totalPages ? 'disabled' : ''}><i class="iconoir-nav-arrow-right"></i></button>
            </div>`;
            var $container = $('#' + containerId);
            $container.html(html);
            $container.off('click.rwiPager', '[data-rwi-page]').on('click.rwiPager', '[data-rwi-page]', function() {
                onGotoPage(Number($(this).data('rwi-page')));
            });
        }

        function gotoManagementPage(p) {
            managementPage = p;
            renderRuanganGrid();
        }

        function gotoAdmitPage(p) {
            admitPage = p;
            renderAdmitBedGrid();
        }

        function resetRuanganForm() {
            $('#ruanganId').val('');
            $('#ruanganNama,#ruanganLantai,#ruanganKelas,#ruanganTarif').val('');
            $('#ruanganJumlahRanjang').val(1);
            $('#ruanganFormTitle').text('Tambah Ruangan');
            $('#resetRuanganBtn').hide();
        }
        $('#resetRuanganBtn').on('click', resetRuanganForm);

        function editRuangan(id) {
            var ruangan = ruanganListCache.find(r => r.id === id);
            if (!ruangan) return;
            $('#ruanganId').val(ruangan.id);
            $('#ruanganNama').val(ruangan.nama);
            $('#ruanganLantai').val(ruangan.lantai ?? '');
            $('#ruanganKelas').val(ruangan.kelas ?? '');
            $('#ruanganTarif').val(ruangan.tarif_per_hari);
            $('#ruanganJumlahRanjang').val(ruangan.beds.length);
            $('#ruanganFormTitle').text('Edit: ' + ruangan.nama);
            $('#resetRuanganBtn').show();
        }

        // Dipakai untuk grid manajemen & grid pilih ranjang admisi — masing-
        // masing punya state filter lantai sendiri, supaya staf bisa lihat
        // Lantai 2 di satu panel sambil pilih ranjang di Lantai 1 di panel lain.
        function renderLantaiFilter(containerId, lantaiAktif, onPilih) {
            var lantaiList = [...new Set(ruanganListCache.map(r => r.lantai).filter(Boolean))];
            if (lantaiList.length < 2) {
                $('#' + containerId).html('');
                return;
            }
            var html =
                `<button type="button" class="btn btn-xs ${lantaiAktif === null ? 'btn-primary' : 'btn-outline-secondary'}" onclick="${onPilih}(null)">Semua Lantai</button>`;
            lantaiList.sort().forEach(function(l) {
                html +=
                    `<button type="button" class="btn btn-xs ${lantaiAktif === l ? 'btn-primary' : 'btn-outline-secondary'}" onclick="${onPilih}('${l}')">Lantai ${l}</button>`;
            });
            $('#' + containerId).html(html);
        }

        function pilihLantaiManagement(lantai) {
            managementLantaiAktif = lantai;
            managementPage = 1;
            renderRuanganGrid();
        }

        function pilihLantaiAdmit(lantai) {
            admitLantaiAktif = lantai;
            admitPage = 1;
            renderAdmitBedGrid();
        }

        function formatTglRWI(v) {
            if (!v) return '-';
            var d = new Date(String(v).replace(' ', 'T'));
            if (isNaN(d.getTime())) return v;
            return d.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        // selectable=false (ruangan management grid): klik ranjang terisi
        // membuka info pasien. selectable=true (admisi): klik ranjang kosong
        // memilihnya untuk diadmisi, ranjang terisi tidak bisa diklik.
        function bedSquareHtml(bed, selectable) {
            var kosong = bed.status === 'kosong';
            var cls = kosong ? 'bed-kosong' : 'bed-terisi';
            if (selectable && !kosong) cls += ' bed-disabled';
            var title = kosong ?
                ('Ranjang ' + bed.nomor_ranjang + ' - Kosong') :
                ('Ranjang ' + bed.nomor_ranjang + ' - ' + (bed.pasien_nama ?? 'Terisi') +
                    (bed.tgl_masuk ? ' (masuk ' + formatTglRWI(bed.tgl_masuk) + ')' : ''));
            var onclick = selectable ?
                (kosong ? `onclick="pilihBedAdmisi(${bed.id})"` : '') :
                `onclick="lihatBed(${bed.id})"`;
            return `<div class="bed-slot ${cls}" data-bed-id="${bed.id}" title="${title}" ${onclick}>
                <i class="iconoir-bed"></i>
                <span>${bed.nomor_ranjang}</span>
            </div>`;
        }

        function lihatBed(bedId) {
            var bed = null,
                ruangan = null;
            ruanganListCache.forEach(function(r) {
                var found = r.beds.find(b => b.id === bedId);
                if (found) {
                    bed = found;
                    ruangan = r;
                }
            });
            if (!bed) return;
            if (bed.status === 'kosong') {
                return Toast.fire({
                    icon: 'info',
                    title: 'Ranjang ' + bed.nomor_ranjang + ' (' + ruangan.nama + ') kosong'
                });
            }
            Swal.fire({
                title: ruangan.nama + ' - Ranjang ' + bed.nomor_ranjang,
                html: 'Pasien: <b>' + (bed.pasien_nama ?? '-') + '</b><br>Masuk: ' + formatTglRWI(bed.tgl_masuk),
                icon: 'info'
            });
        }

        function renderRuanganGrid() {
            renderLantaiFilter('managementLantaiFilter', managementLantaiAktif, 'pilihLantaiManagement');

            if (!ruanganListCache.length) {
                $('#ruanganListContainer').html(
                    '<p class="text-muted text-center mb-0">Belum ada ruangan. Tambahkan lewat form di kiri.</p>'
                );
                $('#ruanganListPager').html('');
                return;
            }
            var ruanganTampil = ruanganListCache.filter(r => managementLantaiAktif === null || r.lantai ===
                managementLantaiAktif);
            if (!ruanganTampil.length) {
                $('#ruanganListContainer').html(
                    '<p class="text-muted text-center mb-0">Tidak ada ruangan di lantai ini.</p>');
                $('#ruanganListPager').html('');
                return;
            }
            var totalPages = Math.ceil(ruanganTampil.length / RWI_ROOMS_PER_PAGE);
            if (managementPage > totalPages) managementPage = totalPages;
            if (managementPage < 1) managementPage = 1;
            var ruanganHalamanIni = ruanganTampil.slice((managementPage - 1) * RWI_ROOMS_PER_PAGE, managementPage *
                RWI_ROOMS_PER_PAGE);
            var html = '';
            ruanganHalamanIni.forEach(function(r) {
                var jumlahKosong = r.beds.filter(b => b.status === 'kosong').length;
                html += `<div class="ruangan-card">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <strong>${r.nama}</strong> ${r.lantai ? `<span class="badge bg-light text-dark border ms-1">Lantai ${r.lantai}</span>` : ''} ${r.kelas ? `<span class="badge bg-light text-dark border ms-1">${r.kelas}</span>` : ''}
                            <div class="text-muted small">Rp ${Number(r.tarif_per_hari).toLocaleString('id-ID')}/hari &middot; ${jumlahKosong}/${r.beds.length} ranjang kosong</div>
                        </div>
                        <div>
                            <a class="btn btn-xs btn-outline-primary" onclick="editRuangan(${r.id})" title="Edit"><i class="iconoir-edit-pencil"></i></a>
                            <a class="btn btn-xs btn-outline-danger" onclick="deleteRuangan(${r.id})" title="Hapus"><i class="iconoir-trash"></i></a>
                        </div>
                    </div>
                    <div class="bed-grid">
                        ${r.beds.map(b => bedSquareHtml(b, false)).join('')}
                    </div>
                </div>`;
            });
            $('#ruanganListContainer').html(html);
            renderPager('ruanganListPager', managementPage, totalPages, gotoManagementPage);
        }

        function renderAdmitBedGrid() {
            renderLantaiFilter('admitLantaiFilter', admitLantaiAktif, 'pilihLantaiAdmit');

            if (!ruanganListCache.length) {
                $('#admitBedGrid').html(
                    '<p class="text-muted mb-0">Belum ada ruangan/ranjang. Tambahkan dulu lewat form Ruangan.</p>'
                );
                $('#admitBedGridPager').html('');
                return;
            }
            var ruanganTampil = ruanganListCache.filter(r => admitLantaiAktif === null || r.lantai ===
                admitLantaiAktif);
            if (!ruanganTampil.length) {
                $('#admitBedGrid').html('<p class="text-muted mb-0">Tidak ada ruangan di lantai ini.</p>');
                $('#admitBedGridPager').html('');
                return;
            }
            var totalPages = Math.ceil(ruanganTampil.length / RWI_ROOMS_PER_PAGE);
            if (admitPage > totalPages) admitPage = totalPages;
            if (admitPage < 1) admitPage = 1;
            var ruanganHalamanIni = ruanganTampil.slice((admitPage - 1) * RWI_ROOMS_PER_PAGE, admitPage *
                RWI_ROOMS_PER_PAGE);
            var html = '';
            ruanganHalamanIni.forEach(function(r) {
                html += `<div class="mb-2">
                    <div class="small fw-bold mb-1">${r.nama}</div>
                    <div class="bed-grid mb-2">${r.beds.map(b => bedSquareHtml(b, true)).join('')}</div>
                </div>`;
            });
            $('#admitBedGrid').html(html);
            if (selectedBedId) {
                $('#admitBedGrid .bed-slot[data-bed-id="' + selectedBedId + '"]').addClass('bed-selected');
            }
            renderPager('admitBedGridPager', admitPage, totalPages, gotoAdmitPage);
        }

        function pilihBedAdmisi(bedId) {
            selectedBedId = bedId;
            $('#admitKamar').val(bedId);
            $('#admitBedGrid .bed-slot').removeClass('bed-selected');
            $('#admitBedGrid .bed-slot[data-bed-id="' + bedId + '"]').addClass('bed-selected');
        }

        function loadRuanganList() {
            $.ajax({
                url: "{{ url('rawat-inap/getRuanganList') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(res) {
                    ruanganListCache = res.data;
                    managementPage = 1;
                    admitPage = 1;
                    renderRuanganGrid();
                    renderAdmitBedGrid();
                },
                error: function(xhr) {
                    var message = xhr.responseJSON?.error ?? 'Gagal memuat data ruangan';
                    $('#ruanganListContainer').html('<p class="text-danger text-center mb-0">' + message +
                        '</p>');
                    $('#admitBedGrid').html('<p class="text-danger mb-0">' + message + '</p>');
                    $('#ruanganListPager,#admitBedGridPager').html('');
                }
            });
        }

        $('#saveRuanganBtn').on('click', function() {
            $.ajax({
                url: "{{ url('rawat-inap/saveRuangan') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    id: $('#ruanganId').val(),
                    nama: $('#ruanganNama').val(),
                    lantai: $('#ruanganLantai').val(),
                    kelas: $('#ruanganKelas').val(),
                    tarif_per_hari: $('#ruanganTarif').val(),
                    jumlah_ranjang: $('#ruanganJumlahRanjang').val(),
                },
                success: function() {
                    Toast.fire({
                        icon: 'success',
                        title: 'Tersimpan'
                    });
                    resetRuanganForm();
                    loadRuanganList();
                },
                error: function(xhr) {
                    Toast.fire({
                        icon: 'error',
                        title: JSON.parse(xhr.responseText).error ?? 'Gagal menyimpan'
                    });
                }
            });
        });

        function deleteRuangan(id) {
            Swal.fire({
                title: 'Hapus ruangan ini?',
                text: 'Semua ranjang di ruangan ini juga akan terhapus.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('rawat-inap/deleteRuangan') }}/" + id,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function() {
                            Toast.fire({
                                icon: 'success',
                                title: 'Terhapus'
                            });
                            loadRuanganList();
                        },
                        error: function(xhr) {
                            Toast.fire({
                                icon: 'error',
                                title: JSON.parse(xhr.responseText).error ?? 'Gagal menghapus'
                            });
                        }
                    });
                }
            });
        }

        $('#admitDaftarPasien').select2({
            ajax: {
                url: "{{ url('rawat-inap/getDaftarPasienOptions') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                delay: 250,
                data: function(params) {
                    return {
                        params: params.term
                    };
                },
                processResults: function(res) {
                    return {
                        results: res.data.map(d => ({
                            id: d.id,
                            text: d.norm + ' | ' + d.nama + ' | ' + d.noregistrasi
                        }))
                    };
                }
            },
            placeholder: 'Cari No. RM/nama pasien'
        });

        $('#admitDokter').select2({
            ajax: {
                url: "{{ url('rawat-inap/getDokterList') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                delay: 250,
                data: function(params) {
                    return {
                        params: params.term
                    };
                },
                processResults: function(res) {
                    return {
                        results: res.data.map(d => ({
                            id: d.id,
                            text: d.fullname
                        }))
                    };
                }
            },
            placeholder: 'Cari dokter'
        });

        $('#admitBtn').on('click', function() {
            if (!$('#admitDaftarPasien').val() || !$('#admitKamar').val()) {
                return Toast.fire({
                    icon: 'warning',
                    title: 'Pilih kunjungan pasien dan ranjang dulu'
                });
            }
            $.ajax({
                url: "{{ url('rawat-inap/admit') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    fk_daftarpasien: $('#admitDaftarPasien').val(),
                    fk_kamar: $('#admitKamar').val(),
                    fk_dokter_penanggungjawab: $('#admitDokter').val(),
                    catatan_masuk: $('#admitCatatan').val(),
                },
                success: function() {
                    Toast.fire({
                        icon: 'success',
                        title: 'Pasien diadmisi'
                    });
                    $('#admitDaftarPasien,#admitDokter').val(null).trigger('change');
                    $('#admitKamar').val('');
                    selectedBedId = null;
                    $('#admitCatatan').val('');
                    loadRuanganList();
                    loadAdmissionList();
                },
                error: function(xhr) {
                    Toast.fire({
                        icon: 'error',
                        title: JSON.parse(xhr.responseText).error ?? 'Gagal admisi'
                    });
                }
            });
        });

        function loadAdmissionList() {
            $.ajax({
                url: "{{ url('rawat-inap/getAdmissionList') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    status: $('#admissionStatus').val()
                },
                success: function(res) {
                    var rows = '';
                    res.data.forEach(function(d) {
                        var aksi = d.status === 'dirawat' ?
                            `<a class="btn btn-xs btn-outline-info" onclick="bukaCatatan(${d.id})"><i class="fa fa-eye"></i></a>
                                 <a class="btn btn-xs btn-outline-primary" onclick="bukaCarePlan(${d.id})">Asuhan</a>
                                 <a class="btn btn-xs btn-outline-success" onclick="bukaRekamMedis(${d.id})">RM Inap</a>
                                 <a class="btn btn-xs btn-outline-secondary" onclick="bukaRiwayatPindah(${d.id})"><i class="fa fa-book"></i></a>
                                 <a class="btn btn-xs btn-outline-danger" onclick="bukaPulangkan(${d.id})">Pulangkan</a>` :
                            `<a class="btn btn-xs btn-outline-success" onclick="bukaRekamMedis(${d.id})">RM Inap</a>
                                 <a class="btn btn-xs btn-outline-secondary" onclick="bukaRiwayatPindah(${d.id})"><i class="fa fa-book"></i></a>`;
                        rows += `<tr>
                            <td>${d.pasien_nama}<br><small class="text-muted">${d.pasien_norm}</small></td>
                            <td>${d.kamar_nama}</td>
                            <td>${d.dokter_nama ?? '-'}</td>
                            <td>${d.tgl_masuk}</td>
                            <td>${aksi}</td>
                        </tr>`;
                    });
                    $('#admissionTable tbody').html(rows ||
                        '<tr><td colspan="5" class="text-muted text-center">Tidak ada data</td></tr>');
                }
            });
        }
        $('#admissionStatus').on('change', loadAdmissionList);

        function bukaCatatan(id) {
            $('#catatanRawatInapId').val(id);
            $('#catatanSuhu,#catatanNadi,#catatanPernapasan,#catatanTensi,#catatanKesadaran,#catatanGcs,#catatanSpo2,#catatanSkalaNyeri,#catatanIntake,#catatanOutput,#catatanIsi,#catatanIntervensi,#catatanRespons').val('');
            loadCatatanList(id);
            $('#modalCatatan').modal('show');
        }

        function loadCatatanList(id) {
            $.ajax({
                url: "{{ url('rawat-inap/getCatatanList') }}/" + id,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(res) {
                    var rows = '';
                    res.data.forEach(d => rows += `<tr>
                        <td>${d.waktu}</td>
                        <td>
                            S:${d.suhu ?? '-'} N:${d.nadi ?? '-'} RR:${d.pernapasan ?? '-'} TD:${d.tensi ?? '-'}<br>
                            Kesadaran:${d.kesadaran ?? '-'} GCS:${d.gcs ?? '-'} SpO₂:${d.spo2 ?? '-'} Nyeri:${d.skala_nyeri ?? '-'}<br>
                            Intake:${d.intake_ml ?? '-'} ml · Output:${d.output_ml ?? '-'} ml<br>
                            <strong>Catatan:</strong> ${d.catatan}<br>
                            <strong>Intervensi:</strong> ${d.intervensi ?? '-'}<br>
                            <strong>Respons:</strong> ${d.respons ?? '-'}
                        </td>
                    </tr>`);
                    $('#catatanBody').html(rows ||
                        '<tr><td colspan="3" class="text-muted text-center">Belum ada catatan</td></tr>');
                }
            });
        }

        $('#btnTambahCatatan').on('click', function() {
            var id = $('#catatanRawatInapId').val();
            if (!$('#catatanIsi').val()) {
                return Toast.fire({
                    icon: 'warning',
                    title: 'Isi catatan dulu'
                });
            }
            $.ajax({
                url: "{{ url('rawat-inap/saveCatatan') }}/" + id,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    suhu: $('#catatanSuhu').val(),
                    nadi: $('#catatanNadi').val(),
                    pernapasan: $('#catatanPernapasan').val(),
                    tensi: $('#catatanTensi').val(),
                    kesadaran: $('#catatanKesadaran').val(),
                    gcs: $('#catatanGcs').val(),
                    spo2: $('#catatanSpo2').val(),
                    skala_nyeri: $('#catatanSkalaNyeri').val(),
                    intake_ml: $('#catatanIntake').val(),
                    output_ml: $('#catatanOutput').val(),
                    catatan: $('#catatanIsi').val(),
                    intervensi: $('#catatanIntervensi').val(),
                    respons: $('#catatanRespons').val(),
                },
                success: function() {
                    Toast.fire({
                        icon: 'success',
                        title: 'Catatan ditambahkan'
                    });
                    $('#catatanSuhu,#catatanNadi,#catatanPernapasan,#catatanTensi,#catatanKesadaran,#catatanGcs,#catatanSpo2,#catatanSkalaNyeri,#catatanIntake,#catatanOutput,#catatanIsi,#catatanIntervensi,#catatanRespons').val('');
                    loadCatatanList(id);
                }
            });
        });

        function bukaCarePlan(id) {
            $('#carePlanRawatInapId').val(id);
            $('#carePlanDiagnosis,#carePlanGoal,#carePlanInterventions,#carePlanTargetDate').val('');
            loadCarePlans(id);
            $('#modalCarePlan').modal('show');
        }

        function loadCarePlans(id) {
            $.ajax({
                url: "{{ url('rawat-inap/getCarePlans') }}/" + id,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(res) {
                    var rows = '';
                    res.data.forEach(function(d) {
                        rows += `<tr>
                            <td><strong>${d.diagnosis}</strong><br><small>${d.goal}</small></td>
                            <td>${d.interventions}</td>
                            <td>${d.status}<br><small>${d.evaluation ?? '-'}</small></td>
                            <td>${d.target_date ?? '-'}</td>
                        </tr>`;
                    });
                    $('#carePlanBody').html(rows ||
                        '<tr><td colspan="4" class="text-muted text-center">Belum ada care plan</td></tr>');
                }
            });
        }

        $('#btnSaveCarePlan').on('click', function() {
            var id = $('#carePlanRawatInapId').val();
            if (!$('#carePlanDiagnosis').val() || !$('#carePlanGoal').val() || !$('#carePlanInterventions').val()) {
                return Toast.fire({
                    icon: 'warning',
                    title: 'Diagnosis, tujuan, dan intervensi wajib diisi'
                });
            }
            $.ajax({
                url: "{{ url('rawat-inap/saveCarePlan') }}/" + id,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    diagnosis: $('#carePlanDiagnosis').val(),
                    goal: $('#carePlanGoal').val(),
                    interventions: $('#carePlanInterventions').val(),
                    target_date: $('#carePlanTargetDate').val(),
                },
                success: function() {
                    Toast.fire({
                        icon: 'success',
                        title: 'Care plan ditambahkan'
                    });
                    $('#carePlanDiagnosis,#carePlanGoal,#carePlanInterventions,#carePlanTargetDate').val('');
                    loadCarePlans(id);
                }
            });
        });

        function bukaRekamMedis(id) {
            $('#medicalRecordRawatInapId').val(id);
            $('#medicalRecordType').val('cppt');
            $('#medicalRecordFields').html('');
            loadMedicalRecords(id);
            renderMedicalRecordFields();
            $('#modalMedicalRecord').modal('show');
        }

        function renderMedicalRecordFields() {
            var type = $('#medicalRecordType').val();
            var fields = '';
            if (type === 'cppt') {
                fields = `
                    <div class="col-md-6 mb-2"><label class="form-label">Subjective</label><textarea class="form-control" id="medicalSubjective" rows="3"></textarea></div>
                    <div class="col-md-6 mb-2"><label class="form-label">Objective</label><textarea class="form-control" id="medicalObjective" rows="3"></textarea></div>
                    <div class="col-md-6 mb-2"><label class="form-label">Assessment</label><textarea class="form-control" id="medicalAssessment" rows="3"></textarea></div>
                    <div class="col-md-6 mb-2"><label class="form-label">Plan</label><textarea class="form-control" id="medicalPlan" rows="3"></textarea></div>`;
            } else if (type === 'instruksi_dokter') {
                fields = `<div class="col-12 mb-2"><label class="form-label">Instruksi Dokter</label><textarea class="form-control" id="medicalInstruction" rows="4"></textarea></div>`;
            } else if (type === 'resume_medis') {
                fields = `
                    <div class="col-md-6 mb-2"><label class="form-label">Diagnosis Akhir</label><textarea class="form-control" id="medicalDiagnosisFinal" rows="3"></textarea></div>
                    <div class="col-md-6 mb-2"><label class="form-label">Ringkasan Tindakan</label><textarea class="form-control" id="medicalProcedureSummary" rows="3"></textarea></div>
                    <div class="col-12 mb-2"><label class="form-label">Kondisi Saat Pulang</label><textarea class="form-control" id="medicalConditionDischarge" rows="3"></textarea></div>`;
            } else {
                fields = `
                    <div class="col-md-4 mb-2"><label class="form-label">Tanggal Kontrol</label><input type="date" class="form-control" id="medicalFollowUpDate"></div>
                    <div class="col-md-8 mb-2"><label class="form-label">Tujuan Kontrol / Fasilitas</label><input type="text" class="form-control" id="medicalFollowUpDestination"></div>`;
            }
            $('#medicalRecordFields').html(fields);
        }

        $('#medicalRecordType').on('change', renderMedicalRecordFields);

        function escapeMedicalRecordValue(value) {
            return $('<div>').text(value ?? '-').html();
        }

        function loadMedicalRecords(id) {
            $.ajax({
                url: "{{ url('rawat-inap/getMedicalRecords') }}/" + id,
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function(res) {
                    var rows = '';
                    res.data.forEach(function(d) {
                        var detail = d.type === 'cppt' ?
                            `S: ${escapeMedicalRecordValue(d.subjective)}<br>O: ${escapeMedicalRecordValue(d.objective)}<br>A: ${escapeMedicalRecordValue(d.assessment)}<br>P: ${escapeMedicalRecordValue(d.plan)}` :
                            d.type === 'instruksi_dokter' ? escapeMedicalRecordValue(d.instruction) :
                            d.type === 'resume_medis' ? `Diagnosis: ${escapeMedicalRecordValue(d.diagnosis_final)}<br>Tindakan: ${escapeMedicalRecordValue(d.procedure_summary)}<br>Kondisi: ${escapeMedicalRecordValue(d.condition_discharge)}` :
                            `Tanggal: ${escapeMedicalRecordValue(d.follow_up_date)}<br>Tujuan: ${escapeMedicalRecordValue(d.follow_up_destination)}`;
                        rows += `<tr><td>${escapeMedicalRecordValue(d.type.replaceAll('_', ' '))}</td><td>${detail}</td><td>${escapeMedicalRecordValue(d.created_at)}</td></tr>`;
                    });
                    $('#medicalRecordBody').html(rows ||
                        '<tr><td colspan="3" class="text-muted text-center">Belum ada dokumen medis</td></tr>');
                }
            });
        }

        $('#btnSaveMedicalRecord').on('click', function() {
            var id = $('#medicalRecordRawatInapId').val();
            var type = $('#medicalRecordType').val();
            var data = { type: type };
            if (type === 'cppt') {
                data.subjective = $('#medicalSubjective').val();
                data.objective = $('#medicalObjective').val();
                data.assessment = $('#medicalAssessment').val();
                data.plan = $('#medicalPlan').val();
            } else if (type === 'instruksi_dokter') {
                data.instruction = $('#medicalInstruction').val();
            } else if (type === 'resume_medis') {
                data.diagnosis_final = $('#medicalDiagnosisFinal').val();
                data.procedure_summary = $('#medicalProcedureSummary').val();
                data.condition_discharge = $('#medicalConditionDischarge').val();
            } else {
                data.follow_up_date = $('#medicalFollowUpDate').val();
                data.follow_up_destination = $('#medicalFollowUpDestination').val();
            }
            $.ajax({
                url: "{{ url('rawat-inap/saveMedicalRecord') }}/" + id,
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                data: data,
                success: function() {
                    Toast.fire({ icon: 'success', title: 'Dokumen medis disimpan' });
                    loadMedicalRecords(id);
                    renderMedicalRecordFields();
                }
            });
        });

        function bukaRiwayatPindah(id) {
            $.ajax({
                url: "{{ url('rawat-inap/getPindahLog') }}/" + id,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(res) {
                    var rows = '';
                    res.data.forEach(d => rows += `<tr>
                        <td>${formatTglRWI(d.waktu_pindah)}</td>
                        <td>${d.kamar_asal?.nama ?? '-'}</td>
                        <td>${d.kamar_tujuan?.nama ?? '-'}</td>
                        <td>${d.catatan ?? '-'}</td>
                    </tr>`);
                    $('#riwayatPindahBody').html(rows ||
                        '<tr><td colspan="4" class="text-muted text-center">Belum pernah dipindah ruangan</td></tr>'
                    );
                    $('#modalRiwayatPindah').modal('show');
                }
            });
        }

        function formatRupiahRWI(v) {
            return 'Rp ' + Number(v).toLocaleString('id-ID');
        }

        function bukaPulangkan(id) {
            $('#pulangRawatInapId').val(id);
            $('#ringkasanPulang').val('');
            $.ajax({
                url: "{{ url('rawat-inap/getBilling') }}/" + id,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(res) {
                    var b = res.data;
                    var rincianKamarRows = (b.rincian_kamar || []).map(r =>
                        `<tr><td class="ps-4 text-muted">↳ ${r.kamar_nama} (${r.hari} hari × ${formatRupiahRWI(r.tarif_per_hari)})</td><td class="text-end text-muted">${formatRupiahRWI(r.subtotal)}</td></tr>`
                    ).join('');
                    var rows = `
                        <tr><td>Lama Rawat</td><td class="text-end">${b.jumlah_hari} hari</td></tr>
                        <tr><td>Biaya Registrasi</td><td class="text-end">${formatRupiahRWI(b.biaya_registrasi)}</td></tr>
                        <tr><td>Biaya Kamar</td><td class="text-end">${formatRupiahRWI(b.biaya_kamar)}</td></tr>
                        ${rincianKamarRows}
                        <tr><td>Tindakan</td><td class="text-end">${formatRupiahRWI(b.biaya_tindakan)}</td></tr>
                        <tr><td>Diagnosa</td><td class="text-end">${formatRupiahRWI(b.biaya_diagnosa)}</td></tr>
                        <tr><td>Lab</td><td class="text-end">${formatRupiahRWI(b.biaya_lab)}</td></tr>
                        <tr><td>Radiologi</td><td class="text-end">${formatRupiahRWI(b.biaya_radiologi)}</td></tr>
                        <tr><td>Resep</td><td class="text-end">${formatRupiahRWI(b.biaya_resep)}</td></tr>
                        <tr><td>Biaya Lain-lain</td><td class="text-end">${formatRupiahRWI(b.biaya_lain)}</td></tr>
                        <tr class="fw-bold"><td>Total Tagihan</td><td class="text-end">${formatRupiahRWI(b.total_tagihan)}</td></tr>
                        <tr><td>Sudah Dibayar</td><td class="text-end">${formatRupiahRWI(b.total_paid)}</td></tr>
                        <tr class="fw-bold text-danger"><td>Sisa</td><td class="text-end">${formatRupiahRWI(b.sisa)}</td></tr>
                    `;
                    $('#billingTable tbody').html(rows);
                    $('#modalPulangkan').modal('show');
                }
            });
        }

        $('#btnKonfirmasiPulang').on('click', function() {
            var id = $('#pulangRawatInapId').val();
            $.ajax({
                url: "{{ url('rawat-inap/discharge') }}/" + id,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    ringkasan_pulang: $('#ringkasanPulang').val()
                },
                success: function() {
                    Toast.fire({
                        icon: 'success',
                        title: 'Pasien dipulangkan'
                    });
                    $('#modalPulangkan').modal('hide');
                    loadAdmissionList();
                    loadRuanganList();
                },
                error: function(xhr) {
                    Toast.fire({
                        icon: 'error',
                        title: JSON.parse(xhr.responseText).error ?? 'Gagal memulangkan'
                    });
                }
            });
        });

        $(document).ready(function() {
            loadRuanganList();
            loadAdmissionList();
        });
    </script>
@endsection
