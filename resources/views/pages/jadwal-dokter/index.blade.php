@extends('layout/main')

@section('content')
    <div class="content-inner mt-5 py-0">
        <div class="row">
            <div class="col-md-5 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Tambah Jadwal Praktek</h4>
                        <small>Jadwal mingguan — berulang tiap minggu sampai dihapus/nonaktifkan</small>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Dokter</label>
                            <select class="form-control select2" id="formDokter" style="width:100%"></select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ruang Praktek</label>
                            <select class="form-control" id="formRuangPraktek">
                                <option value="">- Pilih dokter dulu -</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hari</label>
                            <select class="form-control" id="formHari">
                                <option value="1">Senin</option>
                                <option value="2">Selasa</option>
                                <option value="3">Rabu</option>
                                <option value="4">Kamis</option>
                                <option value="5">Jumat</option>
                                <option value="6">Sabtu</option>
                                <option value="0">Minggu</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label">Jam Mulai</label>
                                <input type="time" class="form-control" id="formJamMulai">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Jam Selesai</label>
                                <input type="time" class="form-control" id="formJamSelesai">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Durasi per Slot (menit)</label>
                            <input type="number" class="form-control" id="formDurasi" value="30" min="5">
                        </div>
                        <button class="btn btn-success" id="saveJadwalBtn">Simpan</button>
                    </div>
                </div>
            </div>

            <div class="col-md-7 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Daftar Jadwal Praktek</h4>
                    </div>
                    <div class="card-body">
                        <table class="display app-data-table table-hover" id="jadwalTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Dokter</th>
                                    <th>Ruang</th>
                                    <th>Hari</th>
                                    <th>Jam</th>
                                    <th>Durasi Slot</th>
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
        var jadwalTable;

        $('#formDokter').select2({
            ajax: {
                url: "{{ url('jadwal-dokter/getDokterList') }}",
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
                            text: d.nik + ' | ' + d.fullname
                        }))
                    };
                }
            },
            placeholder: 'Cari nama/NIK dokter'
        });

        $('#formDokter').on('change', function() {
            var dokterId = $(this).val();
            if (!dokterId) return;
            $.get("{{ url('jadwal-dokter/getRuangPraktekByDokter') }}/" + dokterId, function(res) {
                var options = res.data.length ?
                    res.data.map(r => `<option value="${r.id}">${r.name}</option>`).join('') :
                    '<option value="">Dokter ini belum punya Ruang Praktek</option>';
                $('#formRuangPraktek').html(options);
            });
        });

        $('#saveJadwalBtn').on('click', function() {
            $.ajax({
                url: "{{ url('jadwal-dokter/store') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    fk_dokter: $('#formDokter').val(),
                    fk_ruangpraktek: $('#formRuangPraktek').val(),
                    hari: $('#formHari').val(),
                    jam_mulai: $('#formJamMulai').val(),
                    jam_selesai: $('#formJamSelesai').val(),
                    durasi_slot: $('#formDurasi').val(),
                },
                success: function() {
                    Toast.fire({
                        icon: 'success',
                        title: 'Jadwal disimpan'
                    });
                    jadwalTable.ajax.reload();
                },
                error: function(xhr) {
                    Toast.fire({
                        icon: 'error',
                        title: JSON.parse(xhr.responseText).error ?? JSON.parse(xhr.responseText).message ?? 'Gagal menyimpan'
                    });
                }
            });
        });

        function deleteJadwal(id) {
            Swal.fire({
                title: 'Hapus jadwal ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('jadwal-dokter/delete') }}/" + id,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function() {
                            Toast.fire({
                                icon: 'success',
                                title: 'Jadwal dihapus'
                            });
                            jadwalTable.ajax.reload();
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

        $(document).ready(function() {
            jadwalTable = $('#jadwalTable').DataTable({
                destroy: true,
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    url: "{{ url('jadwal-dokter/getList') }}",
                    type: "POST",
                },
                columns: [
                    { data: 'dokter' },
                    { data: 'ruang_praktek' },
                    { data: 'hari' },
                    { data: 'jam' },
                    { data: 'durasi_slot' },
                    {
                        data: 'id',
                        render: function(id) {
                            return `<a class="btn btn-xs btn-outline-danger" onclick="deleteJadwal(${id})">Hapus</a>`;
                        }
                    },
                ],
            });
        });
    </script>
@endsection
