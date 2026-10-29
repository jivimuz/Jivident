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
                            <input type="text" class="form-control" id="masterNama" placeholder="Contoh: Hemoglobin">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Satuan</label>
                            <input type="text" class="form-control" id="masterSatuan" placeholder="Contoh: g/dL">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nilai Rujukan Normal</label>
                            <input type="text" class="form-control" id="masterNilaiRujukan" placeholder="Contoh: 12-16">
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
                        <h4 class="card-title">Master Jenis Pemeriksaan Lab</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm" id="masterTable">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Satuan</th>
                                    <th>Nilai Rujukan</th>
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
                            <select class="form-control" id="worklistStatus" style="width:160px">
                                <option value="diminta" selected>Belum Dikerjakan</option>
                                <option value="selesai">Sudah Selesai</option>
                            </select>
                        </div>
                        <h4 class="card-title">Worklist Pemeriksaan Lab</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm" id="worklistTable">
                            <thead>
                                <tr>
                                    <th>Tgl Diminta</th>
                                    <th>Pasien</th>
                                    <th>Pemeriksaan</th>
                                    <th>Hasil</th>
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
@endsection

@section('script')
    <script>
        function resetMasterForm() {
            $('#masterId').val('');
            $('#masterNama,#masterSatuan,#masterNilaiRujukan,#masterHarga').val('');
            $('#masterFormTitle').text('Tambah Jenis Pemeriksaan');
            $('#resetMasterBtn').hide();
        }
        $('#resetMasterBtn').on('click', resetMasterForm);

        function editMaster(id, nama, satuan, nilaiRujukan, harga) {
            $('#masterId').val(id);
            $('#masterNama').val(nama);
            $('#masterSatuan').val(satuan);
            $('#masterNilaiRujukan').val(nilaiRujukan);
            $('#masterHarga').val(harga);
            $('#masterFormTitle').text('Edit: ' + nama);
            $('#resetMasterBtn').show();
        }

        function loadMasterList() {
            $.ajax({
                url: "{{ url('lab/getMasterList') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(res) {
                    var rows = '';
                    res.data.forEach(d => rows += `<tr>
                        <td>${d.nama}</td>
                        <td>${d.satuan ?? '-'}</td>
                        <td>${d.nilai_rujukan ?? '-'}</td>
                        <td>Rp ${Number(d.harga).toLocaleString('id-ID')}</td>
                        <td>
                            <a class="btn btn-xs btn-outline-primary" onclick="editMaster(${d.id}, '${d.nama}', '${d.satuan ?? ''}', '${d.nilai_rujukan ?? ''}', ${d.harga})">Edit</a>
                            <a class="btn btn-xs btn-outline-danger" onclick="deleteMaster(${d.id})">Hapus</a>
                        </td>
                    </tr>`);
                    $('#masterTable tbody').html(rows || '<tr><td colspan="5" class="text-muted text-center">Belum ada data</td></tr>');
                }
            });
        }

        $('#saveMasterBtn').on('click', function() {
            $.ajax({
                url: "{{ url('lab/saveMaster') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    id: $('#masterId').val(),
                    nama: $('#masterNama').val(),
                    satuan: $('#masterSatuan').val(),
                    nilai_rujukan: $('#masterNilaiRujukan').val(),
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
                        url: "{{ url('lab/deleteMaster') }}/" + id,
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
                url: "{{ url('lab/getWorklist') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    status: $('#worklistStatus').val()
                },
                success: function(res) {
                    var rows = '';
                    res.data.forEach(d => rows += `<tr>
                        <td>${d.tgl_diminta}</td>
                        <td>${d.pasien_nama} <br><small class="text-muted">${d.pasien_norm}</small></td>
                        <td>${d.jenis_nama}</td>
                        <td>${d.hasil ?? '-'} ${d.satuan_snapshot ?? ''} ${d.hasil_abnormal ? '<span class="badge bg-danger">Abnormal</span>' : ''}</td>
                        <td>${d.status === 'diminta' ? `<a class="btn btn-xs btn-outline-success" onclick="inputHasil(${d.id})">Input Hasil</a>` : '-'}</td>
                    </tr>`);
                    $('#worklistTable tbody').html(rows || '<tr><td colspan="5" class="text-muted text-center">Tidak ada data</td></tr>');
                }
            });
        }

        function inputHasil(id) {
            Swal.fire({
                title: 'Input Hasil Lab',
                html: `
                    <input type="text" id="swalHasil" class="swal2-input" placeholder="Nilai hasil">
                    <label style="display:block;margin-top:8px"><input type="checkbox" id="swalAbnormal"> Tandai abnormal</label>
                `,
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                preConfirm: function() {
                    return {
                        hasil: document.getElementById('swalHasil').value,
                        hasil_abnormal: document.getElementById('swalAbnormal').checked
                    };
                }
            }).then(function(result) {
                if (result.isConfirmed) {
                    if (!result.value.hasil) {
                        return Toast.fire({
                            icon: 'warning',
                            title: 'Isi nilai hasil dulu'
                        });
                    }
                    $.ajax({
                        url: "{{ url('lab/saveHasil') }}/" + id,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: result.value,
                        success: function() {
                            Toast.fire({
                                icon: 'success',
                                title: 'Hasil tersimpan'
                            });
                            loadWorklist();
                        }
                    });
                }
            });
        }

        $('#worklistStatus').on('change', loadWorklist);

        $(document).ready(function() {
            loadMasterList();
            loadWorklist();
        });
    </script>
@endsection
