@extends('layout/main')

@section('content')
    <div class="content-inner mt-5 py-0">
        <div class="row">
            <div class="col-12">
                <div class="bg-white w-100 rounded-pill p-2 d-flex align-items-center justify-content-between shadow-sm">
                    <span class="text-primary fw-bold text-center flex-grow-1 fs-5 fs-md-4">
                        Daftar Rawat Inap
                    </span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0" data-iq-gsap="onStart" data-iq-opacity="0" data-iq-position-y="-40"
                    data-iq-duration=".6" data-iq-delay=".2" data-iq-trigger="scroll" data-iq-ease="none">
                    <div class="card-body p-4">
                        <div class="row mb-3">
                            <div class="col-md-4 mb-2">
                                <label class="fw-bold mb-1 text-muted" style="font-size: 13px;">Pasien</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white" style="cursor:pointer"
                                        onclick="rawatInapTable.ajax.reload()"><i class="iconoir-search"></i></span>
                                    <input type="text" id="search_pasien" class="form-control"
                                        placeholder="No. RM atau nama...">
                                </div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="fw-bold mb-1 text-muted" style="font-size: 13px;">Status</label>
                                <select class="form-select" id="filter_status">
                                    <option value="dirawat" selected>Sedang Dirawat</option>
                                    <option value="keluar">Riwayat (Sudah Keluar)</option>
                                </select>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="rawatinap-table" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Tgl Masuk</th>
                                        <th>No. RM</th>
                                        <th>Nama Pasien</th>
                                        <th>Kamar</th>
                                        <th>Dokter Penanggung Jawab</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data dimuat lewat AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        var rawatInapTable;

        function formatTglPasienRawatInap(v) {
            if (!v) return '-';
            var d = new Date(String(v).replace(' ', 'T'));
            if (isNaN(d.getTime())) return v;
            return d.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            }) + ' ' + d.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function loadRawatInapList() {
            rawatInapTable = $('#rawatinap-table').DataTable({
                destroy: true,
                responsive: true,
                searching: false,
                lengthChange: false,
                pageLength: 10,
                ajax: {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    url: "{{ url('pasien-rawat-inap/getList') }}",
                    type: 'POST',
                    data: function(d) {
                        d.pasienSearch = $('#search_pasien').val();
                        d.status = $('#filter_status').val();
                    }
                },
                columns: [{
                        data: 'tgl_masuk',
                        render: (data) => formatTglPasienRawatInap(data)
                    },
                    {
                        data: 'norm'
                    },
                    {
                        data: 'nama'
                    },
                    {
                        data: 'kamar_nama'
                    },
                    {
                        data: 'dokter_nama',
                        render: (data) => data ?? '-'
                    },
                    {
                        data: 'status',
                        className: 'text-center',
                        render: (data) => data === 'dirawat' ?
                            '<span class="badge bg-primary">Dirawat</span>' :
                            '<span class="badge bg-secondary">Keluar</span>'
                    },
                    {
                        data: 'fk_daftarpasien',
                        className: 'text-center',
                        sortable: false,
                        render: function(data) {
                            var url = `{{ url('pemeriksaan/pasien') }}/${btoa(btoa(data))}`;
                            return `<a class="btn btn-xs btn-outline-success rounded-pill" href="${url}">
                                <i class="iconoir-fast-arrow-right"></i> Periksa
                            </a>`;
                        }
                    },
                ]
            });
        }

        $('#search_pasien').on('keyup', function(e) {
            if (e.key === 'Enter') rawatInapTable.ajax.reload();
        });
        $('#filter_status').on('change', function() {
            rawatInapTable.ajax.reload();
        });

        $(document).ready(function() {
            loadRawatInapList();
        });
    </script>
@endsection
