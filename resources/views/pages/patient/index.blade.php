@extends('layout/main')
@section('style')
    <style>
    </style>
@endsection

@section('content')
    <div class="content-inner mt-5 py-0">

        <!-- Card untuk Filter NIK -->
        <div class="row">

            <div class="col-md-12 mb-3">
                <div class="bg-white w-100 rounded-pill p-2 d-flex align-items-center justify-content-between shadow-sm">
                    {{-- <a href="javascript:history.back()"
                        class="btn btn-primary rounded-pill d-flex align-items-center justify-content-center"
                        style="width:45px; height:45px;">
                        <i class="iconoir-fast-arrow-left" style="font-size:20px;"></i>
                    </a> --}}
                    <span class="text-primary fw-bold text-center flex-grow-1" style="font-size:24px;">
                        Data Pasien
                    </span>
                    <div style="width:45px;"></div> <!-- spacer biar teks tetap di tengah -->
                </div>
            </div>
            <div class="col-md-12">
                <div class="card" data-iq-gsap="onStart" data-iq-opacity="0" data-iq-position-y="-40" data-iq-duration=".6"
                    data-iq-delay=".2" data-iq-trigger="scroll" data-iq-ease="none">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="global-search" class="form-label"><strong>Cari Pasien
                                            (Global)</strong></label>
                                    <div class="input-group">
                                        <input type="search" class="form-control b-r-left" id="global-search"
                                            maxlength="50" placeholder="Ketik NIK, Nama, No RM, No HP..."
                                            onchange="this.value = this.value.replace(/[^0-9a-zA-Z\s]/g, '');">
                                        <a onclick="getList()" class="btn b-r-right btn-outline-primary ">
                                            <i class="iconoir-search" style="font-size: 15px"></i>
                                        </a>
                                        <button class="btn  btn-outline-info b-r-right" id="clear-global-search"
                                            style="display: none;">
                                            <svg width="16" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg" stroke="currentColor" class="me-1">
                                                <path
                                                    d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C14.3857 3 16.5291 4.09381 18 5.61765"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M21 3V8H16" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>
                                            Tampilkan Semua Pasien
                                        </button>
                                    </div>
                                    <!-- [UBAH] Ganti label dan id -->
                                    <small>Ketik NIK atau Nama Pasien untuk Cek Data Pasien </small>
                                    <!-- [UBAH] Ganti id, placeholder, dan hapus batasan NIK -->

                                </div>

                            </div>
                            {{-- <div class="col-md-2 d-flex align-items-center">
                                <div class="form-group mt-2"> <!-- mt-4 agar sejajar dengan input box -->
                                    <a onclick="getList()" class="btn btn-sm btn-outline-primary rounded-pill">
                                        <i class="iconoir-search" style="font-size: 15px"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-center">
                                <div class="form-group mt-2"> <!-- mt-4 agar sejajar dengan input box -->
                                    <!-- [UBAH] Ganti id tombol -->
                                    <button class="btn btn-sm btn-outline-info rounded-pill" id="clear-global-search"
                                        style="display: none;">
                                        <svg width="16" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg" stroke="currentColor" class="me-1">
                                            <path
                                                d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C14.3857 3 16.5291 4.09381 18 5.61765"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M21 3V8H16" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                        Tampilkan Semua Pasien
                                    </button>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [AKHIR] Card untuk Filter NIK -->

        <div class="row">
            <div class="col-md-12">
                <div class="card" data-iq-gsap="onStart" data-iq-opacity="0" data-iq-position-y="-40"
                    data-iq-duration=".6" data-iq-delay=".4" data-iq-trigger="scroll" data-iq-ease="none">
                    <div class="card-header">
                        <div class="float-end">
                            <a class="btn btn-sm btn-outline-primary rounded-pill mt-2 ml-2"
                                href="{{ url('patient/add_patient') }}">
                                <svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.4"
                                        d="M21.101 9.58786H19.8979V8.41162C19.8979 7.90945 19.4952 7.5 18.999 7.5C18.5038 7.5 18.1 7.90945 18.1 8.41162V9.58786H16.899C16.4027 9.58786 16 9.99731 16 10.4995C16 11.0016 16.4027 11.4111 16.899 11.4111H18.1V12.5884C18.1 13.0906 18.5038 13.5 18.999 13.5C19.4952 13.5 19.8979 13.0906 19.8979 12.5884V11.4111H21.101C21.5962 11.4111 22 11.0016 22 10.4995C22 9.99731 21.5962 9.58786 21.101 9.58786Z"
                                        fill="currentColor"></path>
                                    <path
                                        d="M9.5 15.0156C5.45422 15.0156 2 15.6625 2 18.2467C2 20.83 5.4332 21.5001 9.5 21.5001C13.5448 21.5001 17 20.8533 17 18.269C17 15.6848 13.5668 15.0156 9.5 15.0156Z"
                                        fill="currentColor"></path>
                                    <path opacity="0.4"
                                        d="M9.50023 12.5542C12.2548 12.5542 14.4629 10.3177 14.4629 7.52761C14.4629 4.73754 12.2548 2.5 9.50023 2.5C6.74566 2.5 4.5376 4.73754 4.5376 7.52761C4.5376 10.3177 6.74566 12.5542 9.50023 12.5542Z"
                                        fill="currentColor"></path>
                                </svg> Tambah Pasien Baru
                            </a>
                        </div>
                        <h4 class="card-title">Daftar Pasien</h4>
                        <small>List of Patient</small>
                    </div>
                    <div class="card-body" data-iq-gsap="onStart" data-iq-opacity="0" data-iq-position-y="-40"
                        data-iq-duration=".6" data-iq-delay=".6" data-iq-trigger="scroll" data-iq-ease="none"
                        style="padding-left: 40px; padding-right:40px">

                        <div class="app-datatable-default overflow-auto">
                            <table class="display app-data-table default-data-table table-hover" id="main-table">
                                <thead>
                                    <tr>
                                        {{-- <th>No</th> --}}
                                        <th>No</th>
                                        <th>No RM</th>
                                        <th>NIK</th>
                                        <th>Nama Pasien</th>
                                        <th>No HP</th>
                                        <th>Tgl Lahir</th>
                                        <th>-</th>
                                    </tr>
                                </thead>
                                <tbody>

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
        var mainTable; // [UBAH] Definisikan di luar agar bisa diakses
        let isLoading = false;

        $(document).ready(function() {
            getList();

            // [UBAH] Ganti event listener ke #global-search
            $('#global-search').on('change', function() {
                // 'draw()' akan memicu ulang ajax call dengan parameter baru
                getList();
            });

            // [UBAH] Ganti click handler ke #clear-global-search
            $('#clear-global-search').on('click', function() {

                $('#global-search').val(''); // Kosongkan input
                getList();
            });
        });

        function getList() {
            if (isLoading) {
                Toast.fire({
                    icon: "warning",
                    title: "Harap sabar... masih proses..."
                });
            }
            let noD = 1;
            isLoading = true
            const columns = [{
                    data: null, // karena ini cuma nomor urut
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                    orderable: false, // [TAMBAH] nonaktifkan sorting untuk nomor
                    searchable: false, // [TAMBAH] nonaktifkan search untuk nomor
                    width: '10',
                },
                {
                    data: "norm",
                    render: function(data, b, c) {
                        var a = `<a>`
                        a +=
                            `<small class=" rounded-pill bg-success badge-md" style="padding: 4px;font-size:11px">${data}</small>`
                        a += `</a>`
                        return a
                    },
                    className: 'text-center'
                },
                {
                    data: "nik",
                }, {
                    data: "nama",
                },


                {
                    data: "nohp",
                    render: function(data, b, c) {

                        return data ?? '-'
                    },
                },
                {
                    data: "tgllahir",
                    render: function(data, b, c) {
                        if (!data) return '-';

                        // Convert ke jam Indonesia (WIB = UTC+7)
                        // Perhatian: Ini mengasumsikan data dari server adalah UTC
                        // Jika data sudah dalam zona waktu lain, logikanya mungkin perlu disesuaikan
                        const date = new Date(data);

                        // Cek jika tanggal valid
                        if (isNaN(date.getTime())) {
                            return '-'; // Tampilkan strip jika tanggal tidak valid
                        }

                        // Format tanggal (contoh: 07 Nov 2025)
                        // Dihilangkan jam agar lebih ringkas untuk Tgl Lahir
                        return date.toLocaleString('id-ID', {
                            timeZone: 'Asia/Jakarta', // Sesuaikan timezone jika perlu
                            year: 'numeric',
                            day: '2-digit',
                            month: 'short',
                        });
                    },
                },

                {
                    data: "id",
                    render: function(data, b, c) {
                        let urlReg = `{{ url('patient/register-patient') }}/${btoa(btoa(c.id))}`;
                        let urlEd = `{{ url('patient/edit_patient') }}/${btoa(btoa(c.id))}`;
                        let a = '';
                        a += `<a class="btn btn-xs btn-outline-success rounded-pill mt-1 " style="margin-right:5px" href="${urlReg}">
                                 <i class="iconoir-fast-arrow-right" style="font-size:15px; padding-top:2px; "></i>
                                </a>`;
                        a += `<a class="btn btn-xs btn-outline-primary rounded-pill mt-1 " style="margin-right:5px"  href="${urlEd}">
                                <i class="iconoir-page-search" style="font-size:15px"></i>
                                </a>`;

                        a += `<a class="btn btn-xs btn-outline-danger rounded-pill mt-1 " style="margin-right:5px"  href="#">
                                <i class="iconoir-trash" style="font-size:15px;padding-top:2px;"></i>
                                </a>`;
                        return a

                    },
                    className: 'text-center',
                    orderable: false, // [TAMBAH] nonaktifkan sorting untuk tombol aksi
                    searchable: false // [TAMBAH] nonaktifkan search untuk tombol aksi
                },
            ]

            // [UBAH] Hapus `var tabled =` karena sudah didefinisikan di atas
            // dan return instance datatable-nya
            var tabled = $('#main-table').DataTable({
                searching: false, // Biarkan 'searching' global, tapi kita akan filter via server
                destroy: true,
                lengthChange: false,
                responsive: true,
                // pageLength: 2,
                processing: true,
                serverSide: true,
                ajax: {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    url: "{{ url('patient/getList') }}",
                    type: "POST",
                    // [UBAH] Ubah 'data' menjadi fungsi
                    data: function(d) {
                        // 'd' adalah data default DataTables
                        // [UBAH] Ganti 'nik_search' menjadi 'search.value' (parameter global search DataTables)
                        d.search.value = $('#global-search').val();
                        // 'd' sekarang akan berisi: {draw: ..., start: ..., length: ..., search: {value: '...'}, ...}
                        return d;
                    }

                },
                columns: columns,
                initComplete: function(data) {
                    isLoading = false
                },
                // [BARU] Mengganti pesan default jika tabel kosong
                language: {
                    "zeroRecords": "Tidak ada data pasien yang ditemukan",
                    "infoEmpty": "Tidak ada data tersedia",
                    "processing": "Sedang memuat..."
                },

                // [BARU] Callback untuk Tampilkan/Sembunyikan Tombol Clear
                drawCallback: function(settings) {
                    var api = this.api();
                    var info = api.page.info();
                    // [UBAH] Ganti ke 'global-search'
                    var globalSearchValue = $('#global-search').val();

                    // Tampilkan tombol HANYA jika:
                    // 1. Input search tidak kosong
                    // 2. Hasil pencarian (recordsDisplay) adalah 0
                    if (globalSearchValue.length > 0 && info.recordsDisplay === 0) {
                        // [UBAH] Ganti ke 'clear-global-search'
                        $('#clear-global-search').show();
                    } else {
                        // [UBAH] Ganti ke 'clear-global-search'
                        $('#clear-global-search').hide();
                    }
                }
                // initComplete: function(data) {}
            });

            // [UBAH] Kembalikan instance tabel
            return tabled;
        }
    </script>
@endsection
