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
                        Daftar Pasien Dalam Antrian
                    </span>
                    <div style="width:45px;"></div> <!-- spacer biar teks tetap di tengah -->
                </div>
            </div>
            <div class="col-md-12">
                <div class="card" data-iq-gsap="onStart" data-iq-opacity="0" data-iq-position-y="-40" data-iq-duration=".6"
                    data-iq-delay=".2" data-iq-trigger="scroll" data-iq-ease="none">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="global-search" class="form-label"><strong>Pasien </strong></label>
                                    <input type="search" class="form-control form-control-lg" id="global-search"
                                        maxlength="50" placeholder="Ketik NIK, Nama, No RM, No HP..."
                                        onchange="this.value = this.value.replace(/[^0-9a-zA-Z\s]/g, '');">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="ruanganfk" class="form-label"><strong>Ruang Praktek </strong></label>
                                <select name="ruanganfk" id="ruanganfk" class="form-select">
                                    <option value="" selected>Semua</option>
                                    @foreach ($ruangan as $i)
                                        <option value="{{ $i->id }}" doktername="{{ $i->dokter }}"
                                            dokterid="{{ $i->dokterid }}">
                                            {{ $i->name . ' || ' . $i->dokter }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tglregistrasi" class="form-label"><strong>Tgl Registrasi
                                            (Rangable)</strong></label>
                                    <input class="form-control form-control-lg picker-range flatpickr-input bg-white"
                                        id="tglregistrasi" value="{{ WIB() }}" max="{{ WIB() }}"
                                        placeholder="YYYY-MM-DD to YYYY-MM-DD">
                                </div>
                            </div>
                            <div class="col-md-1">
                                <a onclick="getList()" class="btn b-sm btn-outline-primary mt-4">
                                    <i class="iconoir-search" style="font-size: 15px"></i>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [AKHIR] Card untuk Filter NIK -->

    <div class="row">
        <div class="col-md-12">
            <div class="card" data-iq-gsap="onStart" data-iq-opacity="0" data-iq-position-y="-40" data-iq-duration=".6"
                data-iq-delay=".4" data-iq-trigger="scroll" data-iq-ease="none">
                <div class="card-header">
                    <div class="float-end">

                        <a class="btn btn-sm btn-outline-info rounded-pill mt-2 ml-2" href="{{ url('patient') }}">
                            <svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M11.997 15.1746C7.684 15.1746 4 15.8546 4 18.5746C4 21.2956 7.661 21.9996 11.997 21.9996C16.31 21.9996 19.994 21.3206 19.994 18.5996C19.994 15.8786 16.334 15.1746 11.997 15.1746Z"
                                    fill="currentColor"></path>
                                <path opacity="0.4"
                                    d="M11.9971 12.5838C14.9351 12.5838 17.2891 10.2288 17.2891 7.29176C17.2891 4.35476 14.9351 1.99976 11.9971 1.99976C9.06008 1.99976 6.70508 4.35476 6.70508 7.29176C6.70508 10.2288 9.06008 12.5838 11.9971 12.5838Z"
                                    fill="currentColor"></path>
                            </svg> Tambah Pasien
                        </a>
                    </div>
                    <h4 class="card-title">Daftar Ruang Tunggu Pasien </h4>
                    <small>Pasien Dalam Ruang Tunggu</small>
                </div>
                <div class="card-body" data-iq-gsap="onStart" data-iq-opacity="0" data-iq-position-y="-40"
                    data-iq-duration=".6" data-iq-delay=".6" data-iq-trigger="scroll" data-iq-ease="none"
                    style="padding-left: 40px; padding-right:40px">

                    <div class="app-datatable-default overflow-auto">
                        <table class="display app-data-table default-data-table table-hover" id="main-table">
                            <thead>
                                <tr>
                                    {{-- <th>No</th> --}}
                                    <th>No Antrian</th>
                                    <th>Tgl Registrasi</th>
                                    <th>Ruangan</th>
                                    <th>Dokter Tujuan</th>
                                    <th>No RM</th>
                                    <th>No Registrasi</th>
                                    <th>Nama Pasien</th>
                                    <th>Lama/Baru</th>
                                    <th>Status</th>
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
@endsection

@section('script')
    <script>
        var mainTable; // [UBAH] Definisikan di luar agar bisa diakses
        let isLoading = false;

        $(document).ready(function() {
            // [UBAH] Panggil getList() dan simpan instance tabelnya
            mainTable = getList();

        });

        function getList() {
            if (isLoading) {
                return Toast.fire({
                    icon: "warning",
                    title: "Harap sabar... masih proses..."
                });
            }
            let noD = 1;
            isLoading = true
            const columns = [{
                    data: "noantrian"
                },
                {
                    data: "tglregistrasi",
                    render: function(data, b, c) {
                        if (!data) return '-';
                        const date = new Date(data);

                        if (isNaN(date.getTime())) {
                            return '-'; // Tampilkan strip jika tanggal tidak valid
                        }
                        return date.toLocaleString('id-ID', {
                            timeZone: 'Asia/Jakarta', // Sesuaikan timezone jika perlu
                            year: 'numeric',
                            day: '2-digit',
                            month: 'short',
                            hour: '2-digit',
                            minute: '2-digit',
                        });
                    },
                },

                {
                    data: "ruangan"
                },
                {
                    data: "dokter"
                },
                {
                    data: "norm"
                },
                {
                    data: "noregistrasi"
                },
                {
                    data: "nama"
                },

                {
                    data: "statuspasien"
                },
                {
                    data: "status"
                },
                {
                    data: "id",
                    render: function(data, b, c) {
                        let urlReg = `{{ url('pemeriksaan/pasien/') }}/${btoa(btoa(c.id))}`;
                        let a = '';
                        a += `<a class="btn btn-xs btn-outline-success rounded-pill mt-1 " style="margin-right:5px" href="${urlReg}">
                                 <i class="iconoir-fast-arrow-right" style="font-size:15px; padding-top:2px; "></i> <br/> Periksa
                                </a>`;
                        // let urlAntrian = `{{ url('print/print-antrian') }}/${btoa(btoa(c.id))}`;
                        // a += `<a class="btn btn-xs btn-outline-warning rounded-pill mt-1 " style="margin-right:5px" onclick="openPopup('${urlAntrian}')">
                    //          <i class="iconoir-printing-page" style="font-size:15px; padding-top:2px; "></i> <br/> Antrian
                    //         </a>`;
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
                    url: "{{ url('ruang-tunggu/getList') }}",
                    type: "POST",
                    // [UBAH] Ubah 'data' menjadi fungsi
                    data: {
                        pasienSearch: $('#global-search').val(),
                        ruanganfk: $('#ruanganfk').val(),
                        tglregistrasi: $('#tglregistrasi').val(),
                    }

                },
                columns: columns,
                initComplete: function(data) {
                    isLoading = false
                },
                // initComplete: function(data) {}
            });

            // [UBAH] Kembalikan instance tabel
            return tabled;
        }
    </script>
@endsection
