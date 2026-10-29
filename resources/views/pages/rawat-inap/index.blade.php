@extends('layout/main')

@section('content')
    <div class="content-inner mt-5 py-0">
        <div class="row">
            <div class="col-md-5 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title" id="kamarFormTitle">Tambah Kamar/Bed</h4>
                    </div>
                    <div class="card-body">
                        <input type="hidden" id="kamarId">
                        <div class="mb-3">
                            <label class="form-label">Nama Kamar/Bed</label>
                            <input type="text" class="form-control" id="kamarNama" placeholder="Contoh: Kamar A - Bed 1">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kelas</label>
                            <input type="text" class="form-control" id="kamarKelas" placeholder="Contoh: VIP, Kelas I">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tarif per Hari</label>
                            <input type="number" class="form-control" id="kamarTarif" min="0">
                        </div>
                        <button class="btn btn-success" id="saveKamarBtn">Simpan</button>
                        <button class="btn btn-outline-secondary" id="resetKamarBtn" style="display:none">Batal Edit</button>
                    </div>
                </div>

                <div class="card mt-3">
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
                            <label class="form-label">Kamar/Bed</label>
                            <select class="form-control select2" id="admitKamar" style="width:100%"></select>
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
                </div>
            </div>

            <div class="col-md-7 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Master Kamar/Bed</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm" id="kamarTable">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Kelas</th>
                                    <th>Tarif/Hari</th>
                                    <th>Status</th>
                                    <th>-</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

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
                        <div class="col-12 mt-2">
                            <label class="form-label">Catatan</label>
                            <textarea class="form-control" id="catatanIsi" rows="2"></textarea>
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
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody id="catatanBody"></tbody>
                    </table>
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
@endsection

@section('script')
    <script>
        function resetKamarForm() {
            $('#kamarId').val('');
            $('#kamarNama,#kamarKelas,#kamarTarif').val('');
            $('#kamarFormTitle').text('Tambah Kamar/Bed');
            $('#resetKamarBtn').hide();
        }
        $('#resetKamarBtn').on('click', resetKamarForm);

        function editKamar(id, nama, kelas, tarif) {
            $('#kamarId').val(id);
            $('#kamarNama').val(nama);
            $('#kamarKelas').val(kelas);
            $('#kamarTarif').val(tarif);
            $('#kamarFormTitle').text('Edit: ' + nama);
            $('#resetKamarBtn').show();
        }

        function loadKamarList() {
            $.ajax({
                url: "{{ url('rawat-inap/getKamarList') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(res) {
                    var rows = '';
                    res.data.forEach(d => rows += `<tr>
                        <td>${d.nama}</td>
                        <td>${d.kelas ?? '-'}</td>
                        <td>Rp ${Number(d.tarif_per_hari).toLocaleString('id-ID')}</td>
                        <td><span class="badge ${d.status === 'kosong' ? 'bg-success' : 'bg-secondary'}">${d.status}</span></td>
                        <td>
                            <a class="btn btn-xs btn-outline-primary" onclick="editKamar(${d.id}, '${d.nama}', '${d.kelas ?? ''}', ${d.tarif_per_hari})">Edit</a>
                            <a class="btn btn-xs btn-outline-danger" onclick="deleteKamar(${d.id})">Hapus</a>
                        </td>
                    </tr>`);
                    $('#kamarTable tbody').html(rows || '<tr><td colspan="5" class="text-muted text-center">Belum ada data</td></tr>');
                }
            });
        }

        $('#saveKamarBtn').on('click', function() {
            $.ajax({
                url: "{{ url('rawat-inap/saveKamar') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    id: $('#kamarId').val(),
                    nama: $('#kamarNama').val(),
                    kelas: $('#kamarKelas').val(),
                    tarif_per_hari: $('#kamarTarif').val(),
                },
                success: function() {
                    Toast.fire({
                        icon: 'success',
                        title: 'Tersimpan'
                    });
                    resetKamarForm();
                    loadKamarList();
                    loadAvailableKamar();
                }
            });
        });

        function deleteKamar(id) {
            Swal.fire({
                title: 'Hapus kamar/bed ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('rawat-inap/deleteKamar') }}/" + id,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function() {
                            Toast.fire({
                                icon: 'success',
                                title: 'Terhapus'
                            });
                            loadKamarList();
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

        function loadAvailableKamar() {
            $.ajax({
                url: "{{ url('rawat-inap/getAvailableKamar') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(res) {
                    var options = '<option></option>';
                    res.data.forEach(k => options += `<option value="${k.id}">${k.nama} (Rp ${Number(k.tarif_per_hari).toLocaleString('id-ID')}/hari)</option>`);
                    $('#admitKamar').html(options).trigger('change');
                }
            });
        }

        $('#admitBtn').on('click', function() {
            if (!$('#admitDaftarPasien').val() || !$('#admitKamar').val()) {
                return Toast.fire({
                    icon: 'warning',
                    title: 'Pilih kunjungan pasien dan kamar dulu'
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
                    $('#admitDaftarPasien,#admitKamar,#admitDokter').val(null).trigger('change');
                    $('#admitCatatan').val('');
                    loadAvailableKamar();
                    loadKamarList();
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
                            `<a class="btn btn-xs btn-outline-info" onclick="bukaCatatan(${d.id})">Catatan</a>
                             <a class="btn btn-xs btn-outline-danger" onclick="bukaPulangkan(${d.id})">Pulangkan</a>` :
                            '-';
                        rows += `<tr>
                            <td>${d.pasien_nama}<br><small class="text-muted">${d.pasien_norm}</small></td>
                            <td>${d.kamar_nama}</td>
                            <td>${d.dokter_nama ?? '-'}</td>
                            <td>${d.tgl_masuk}</td>
                            <td>${aksi}</td>
                        </tr>`;
                    });
                    $('#admissionTable tbody').html(rows || '<tr><td colspan="5" class="text-muted text-center">Tidak ada data</td></tr>');
                }
            });
        }
        $('#admissionStatus').on('change', loadAdmissionList);

        function bukaCatatan(id) {
            $('#catatanRawatInapId').val(id);
            $('#catatanSuhu,#catatanNadi,#catatanPernapasan,#catatanTensi,#catatanIsi').val('');
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
                        <td>S:${d.suhu ?? '-'} N:${d.nadi ?? '-'} RR:${d.pernapasan ?? '-'} TD:${d.tensi ?? '-'}</td>
                        <td>${d.catatan}</td>
                    </tr>`);
                    $('#catatanBody').html(rows || '<tr><td colspan="3" class="text-muted text-center">Belum ada catatan</td></tr>');
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
                    catatan: $('#catatanIsi').val(),
                },
                success: function() {
                    Toast.fire({
                        icon: 'success',
                        title: 'Catatan ditambahkan'
                    });
                    $('#catatanSuhu,#catatanNadi,#catatanPernapasan,#catatanTensi,#catatanIsi').val('');
                    loadCatatanList(id);
                }
            });
        });

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
                    var rows = `
                        <tr><td>Lama Rawat</td><td class="text-end">${b.jumlah_hari} hari</td></tr>
                        <tr><td>Biaya Registrasi</td><td class="text-end">${formatRupiahRWI(b.biaya_registrasi)}</td></tr>
                        <tr><td>Biaya Kamar</td><td class="text-end">${formatRupiahRWI(b.biaya_kamar)}</td></tr>
                        <tr><td>Tindakan</td><td class="text-end">${formatRupiahRWI(b.biaya_tindakan)}</td></tr>
                        <tr><td>Diagnosa</td><td class="text-end">${formatRupiahRWI(b.biaya_diagnosa)}</td></tr>
                        <tr><td>Lab</td><td class="text-end">${formatRupiahRWI(b.biaya_lab)}</td></tr>
                        <tr><td>Radiologi</td><td class="text-end">${formatRupiahRWI(b.biaya_radiologi)}</td></tr>
                        <tr><td>Resep</td><td class="text-end">${formatRupiahRWI(b.biaya_resep)}</td></tr>
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
                    loadKamarList();
                    loadAvailableKamar();
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
            loadKamarList();
            loadAvailableKamar();
            loadAdmissionList();
        });
    </script>
@endsection
