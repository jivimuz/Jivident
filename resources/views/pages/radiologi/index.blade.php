@extends('layout/main')

@section('content')
    <div class="content-inner mt-5 py-0">
        <div class="row">
            <div class="col-md-5 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title" id="masterFormTitle">Tambah Jenis Pemeriksaan</h4>
                    </div>
                    <div class="card-body">
                        <input type="hidden" id="masterId">
                        <div class="mb-3">
                            <label class="form-label">Nama Pemeriksaan</label>
                            <input type="text" class="form-control" id="masterNama"
                                placeholder="Contoh: Rontgen Panoramic">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <input type="text" class="form-control" id="masterDeskripsi">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Harga</label>
                            <input type="number" class="form-control" id="masterHarga" min="0">
                        </div>
                        <button class="btn btn-success" id="saveMasterBtn">Simpan</button>
                        <button class="btn btn-outline-secondary" id="resetMasterBtn" style="display:none">Batal Edit</button>
                    </div>
                </div>
            </div>

            <div class="col-md-7 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Master Jenis Pemeriksaan Radiologi</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm" id="masterTable">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Deskripsi</th>
                                    <th>Harga</th>
                                    <th>-</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-12 mb-3">
                <div class="card">
                    <div class="card-header">
                        <div class="float-end">
                            <select class="form-control" id="worklistStatus" style="width:200px">
                                <option value="" selected>Diminta + Terjadwal</option>
                                <option value="diminta">Diminta</option>
                                <option value="terjadwal">Terjadwal</option>
                                <option value="selesai">Selesai</option>
                            </select>
                        </div>
                        <h4 class="card-title">Worklist Radiologi</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm" id="worklistTable">
                            <thead>
                                <tr>
                                    <th>Tgl Diminta</th>
                                    <th>Pasien</th>
                                    <th>Pemeriksaan</th>
                                    <th>Jadwal</th>
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
    </div>

    <!-- Modal Input Hasil -->
    <div class="modal fade" id="modalHasilRadiologi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Input Hasil Radiologi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formHasilRadiologi">
                    <div class="modal-body">
                        <input type="hidden" id="hasilOrderId">
                        <div class="mb-3">
                            <label class="form-label">Gambar Hasil (opsional)</label>
                            <input type="file" class="form-control" id="hasilGambar" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Interpretasi / Hasil</label>
                            <textarea class="form-control" id="hasilInterpretasi" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan Hasil</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function resetMasterForm() {
            $('#masterId').val('');
            $('#masterNama,#masterDeskripsi,#masterHarga').val('');
            $('#masterFormTitle').text('Tambah Jenis Pemeriksaan');
            $('#resetMasterBtn').hide();
        }
        $('#resetMasterBtn').on('click', resetMasterForm);

        function editMaster(id, nama, deskripsi, harga) {
            $('#masterId').val(id);
            $('#masterNama').val(nama);
            $('#masterDeskripsi').val(deskripsi);
            $('#masterHarga').val(harga);
            $('#masterFormTitle').text('Edit: ' + nama);
            $('#resetMasterBtn').show();
        }

        function loadMasterList() {
            $.ajax({
                url: "{{ url('radiologi/getMasterList') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(res) {
                    var rows = '';
                    res.data.forEach(d => rows += `<tr>
                        <td>${d.nama}</td>
                        <td>${d.deskripsi ?? '-'}</td>
                        <td>Rp ${Number(d.harga).toLocaleString('id-ID')}</td>
                        <td>
                            <a class="btn btn-xs btn-outline-primary" onclick="editMaster(${d.id}, '${d.nama}', '${d.deskripsi ?? ''}', ${d.harga})">Edit</a>
                            <a class="btn btn-xs btn-outline-danger" onclick="deleteMaster(${d.id})">Hapus</a>
                        </td>
                    </tr>`);
                    $('#masterTable tbody').html(rows || '<tr><td colspan="4" class="text-muted text-center">Belum ada data</td></tr>');
                }
            });
        }

        $('#saveMasterBtn').on('click', function() {
            $.ajax({
                url: "{{ url('radiologi/saveMaster') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    id: $('#masterId').val(),
                    nama: $('#masterNama').val(),
                    deskripsi: $('#masterDeskripsi').val(),
                    harga: $('#masterHarga').val(),
                },
                success: function() {
                    Toast.fire({
                        icon: 'success',
                        title: 'Tersimpan'
                    });
                    resetMasterForm();
                    loadMasterList();
                },
                error: function(xhr) {
                    Toast.fire({
                        icon: 'error',
                        title: Object.values(JSON.parse(xhr.responseText).errors ?? {}).flat().join(', ') || 'Gagal menyimpan'
                    });
                }
            });
        });

        function deleteMaster(id) {
            Swal.fire({
                title: 'Hapus jenis pemeriksaan ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('radiologi/deleteMaster') }}/" + id,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function() {
                            Toast.fire({
                                icon: 'success',
                                title: 'Terhapus'
                            });
                            loadMasterList();
                        }
                    });
                }
            });
        }

        function loadWorklist() {
            $.ajax({
                url: "{{ url('radiologi/getWorklist') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    status: $('#worklistStatus').val()
                },
                success: function(res) {
                    var rows = '';
                    res.data.forEach(function(d) {
                        var statusCls = d.status === 'selesai' ? 'bg-success' : (d.status === 'terjadwal' ?
                            'bg-info' : 'bg-warning text-dark');
                        var aksi = '-';
                        if (d.status === 'diminta') {
                            aksi =
                                `<a class="btn btn-xs btn-outline-info" onclick="jadwalkan(${d.id})">Jadwalkan</a>`;
                        } else if (d.status === 'terjadwal') {
                            aksi =
                                `<a class="btn btn-xs btn-outline-success" onclick="bukaModalHasil(${d.id})">Input Hasil</a>`;
                        }
                        rows += `<tr>
                            <td>${d.tgl_diminta}</td>
                            <td>${d.pasien_nama} <br><small class="text-muted">${d.pasien_norm}</small></td>
                            <td>${d.jenis_nama}<br><small class="text-muted">${d.catatan_klinis ?? ''}</small></td>
                            <td>${d.tgl_jadwal ?? '-'}</td>
                            <td><span class="badge ${statusCls}">${d.status}</span></td>
                            <td>${aksi}</td>
                        </tr>`;
                    });
                    $('#worklistTable tbody').html(rows || '<tr><td colspan="6" class="text-muted text-center">Tidak ada data</td></tr>');
                }
            });
        }

        function jadwalkan(id) {
            Swal.fire({
                title: 'Jadwalkan Pemeriksaan',
                input: 'datetime-local',
                showCancelButton: true,
                confirmButtonText: 'Simpan'
            }).then(function(result) {
                if (result.isConfirmed && result.value) {
                    $.ajax({
                        url: "{{ url('radiologi/jadwalkan') }}/" + id,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            tgl_jadwal: result.value
                        },
                        success: function() {
                            Toast.fire({
                                icon: 'success',
                                title: 'Dijadwalkan'
                            });
                            loadWorklist();
                        }
                    });
                }
            });
        }

        function bukaModalHasil(id) {
            $('#hasilOrderId').val(id);
            $('#hasilGambar').val('');
            $('#hasilInterpretasi').val('');
            $('#modalHasilRadiologi').modal('show');
        }

        $('#formHasilRadiologi').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData();
            formData.append('hasil_interpretasi', $('#hasilInterpretasi').val());
            if ($('#hasilGambar')[0].files[0]) {
                formData.append('gambar', $('#hasilGambar')[0].files[0]);
            }
            $.ajax({
                url: "{{ url('radiologi/saveHasil') }}/" + $('#hasilOrderId').val(),
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: formData,
                contentType: false,
                processData: false,
                success: function() {
                    Toast.fire({
                        icon: 'success',
                        title: 'Hasil tersimpan'
                    });
                    $('#modalHasilRadiologi').modal('hide');
                    loadWorklist();
                },
                error: function(xhr) {
                    Toast.fire({
                        icon: 'error',
                        title: JSON.parse(xhr.responseText).error ?? 'Gagal menyimpan hasil'
                    });
                }
            });
        });

        $('#worklistStatus').on('change', loadWorklist);

        $(document).ready(function() {
            loadMasterList();
            loadWorklist();
        });
    </script>
@endsection
