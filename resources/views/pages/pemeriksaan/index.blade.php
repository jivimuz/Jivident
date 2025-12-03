@extends('layout/main')

@section('style')
@endsection


@section('content')
    <div class="content-inner mt-4 py-0">
        <input type="text" value="<?= $id ?>" id="idF" hidden>
        <div class="row">
            <div class="col-md-12 mb-3">
                <div class="bg-white w-100 rounded-pill p-2 d-flex align-items-center justify-content-between shadow-sm">
                    <a href="javascript:history.back()"
                        class="btn btn-primary rounded-pill d-flex align-items-center justify-content-center"
                        style="width:45px; height:45px;">
                        <i class="iconoir-fast-arrow-left" style="font-size:20px;"></i>
                    </a>
                    <span class="text-primary fw-bold text-center flex-grow-1" style="font-size:24px;">
                        Pemeriksaan Pasien
                    </span>
                    @php
                        $statusColors = [
                            'Dalam Antrian' => 'text-warning',
                            'Di Periksa' => 'text-info',
                            'Farmasi' => 'text-purple', // custom class sendiri ya
                            'Selesai' => 'text-success',
                        ];
                    @endphp

                    <div class="fw-bold text-center  text-black" style="padding-right:10px">
                        Status : <span class="{{ $statusColors[$data->status] ?? 'text-dark' }}">{{ $data->status }}</span>
                    </div>
                    <!-- spacer biar teks tetap di tengah -->
                </div>
            </div>
            <div class="col-md-12">
                <div class="card equal-card">
                    <div class="card-header">
                        <h5>Menu Pemeriksaan</h5>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs tab-primary bg-primary p-1 justify-content-end" id="bg"
                            role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active" id="vitalsign" aria-selected="true"><i
                                        class="fa fa-heartbeat fa-fw"></i> Vital Sign</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" id="odontogram" aria-selected="false"><i
                                        class="fa fa-tooth pe-1 ps-1"></i>Odontogram</button>
                            </li>

                            <li class="nav-item">
                                <button class="nav-link" id="soape" aria-selected="false"><i
                                        class="fa fa-clipboard fa-fw"></i>SOAPE</button>
                            </li>

                            <li class="nav-item">
                                <button class="nav-link" id="tindakan" aria-selected="false"><i
                                        class="fa fa-list-ol fa-fw"></i>Tindakan </button>
                            </li>
                            {{-- <li class="nav-item">
                                <button class="nav-link" id="resepelektronik" aria-selected="false"><i
                                        class="fa fa-flask fa-fw"></i>Resep Obat</button>
                            </li> --}}
                        </ul>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="ticket-details row">
                <div class="col-md-3 ">
                    <div class="card">
                        <div class="card-body">
                            <div class="ticket-details-profile ">
                                <div class="ticket-profile mb-4 border-b">
                                    <div class="h-45 w-45 d-flex-center b-r-50 overflow-hidden text-bg-secondary me-3">
                                        <img alt="" class="img-fluid "
                                            src="{{ url('/assets/images/avtar/' . ($data->jeniskelamin == 'Perempuan' ? '1.png' : '6.png')) }}">
                                    </div>
                                    <div>
                                        <h6 class="mb-0">
                                            {{ strlen($data->nama) > 20 ? substr($data->nama, 0, 20) . '...' : $data->nama }}
                                        </h6>
                                        <p class="text-secondary">{{ $data->statuspasien }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="about-list pt-0 border-b">
                                <div>
                                    <span class="fw-medium">No Registrasi</span>
                                    <span class="float-end f-s-13 text-secondary">{{ $data->noregistrasi }}</span>
                                </div>
                                <div>
                                    <span class="fw-medium">No Rekam Medis</span>
                                    <span class="float-end f-s-13 text-secondary">{{ $data->norm }}</span>
                                </div>
                                <div>
                                    <span class="fw-medium">Jenis Kelamin</span>
                                    <span class="float-end f-s-13 text-secondary">{{ $data->jeniskelamin }}</span>
                                </div>
                                <div>
                                    <span class="fw-medium">Umur (thn)</span>
                                    <span class="float-end f-s-13 text-secondary">{{ countAge($data->tgllahir) }}</span>
                                </div>
                                <div>
                                    <span class="fw-medium">Pendidikan</span>
                                    <span class="float-end f-s-13 text-secondary">{{ $data->pendidikan ?? '-' }}</span>
                                </div>
                                <div>
                                    <span class="fw-medium">Pekerjaan</span>
                                    <span class="float-end f-s-13 text-secondary">{{ $data->pekerjaan ?? '-' }}</span>
                                </div>
                                <div>
                                    <span class="fw-medium">Alamat</span>

                                </div>
                                <div class=" f-s-13 text-secondary">
                                    {{ $data->alamat ?? '-' }}{{ $data->kelurahan ? ', ' . $data->kelurahan : '' }}{{ $data->kecamatan ? ', ' . $data->kecamatan : '' }}{{ $data->kotakabupaten ? ', ' . $data->kotakabupaten : '' }}{{ $data->provinsi ? ', ' . $data->provinsi : '' }}
                                </div>
                            </div>


                            <form action="<?= url('pemeriksaan/pasien/' . $id . '/updateKeluhan/' . $data->idp) ?>"
                                class="form-group" id="form-keluhan">
                                <label class="fw-medium">Keluhan</label>
                                <div class="input-group mb-3">
                                    <textarea name="keluhan" class="form-control bg-warning" placeholder="-" id="keluhan" readonly><?= $data->keluhan ?></textarea>
                                    @if ($data->status != 'Dalam Antrian' && $data->status != 'Selesai')
                                        <div class="input-group-text bg-white">
                                            <div id="div-keluhan-1">
                                                <a class="btn btn-sm" onclick="editNotes('keluhan')"><i
                                                        class="fa fa-pencil text-primary"></i></a>
                                            </div>
                                            <div id="div-keluhan-2" hidden>
                                                <a onclick="sendNotes('#form-keluhan','keluhan')" class="btn btn-sm"><i
                                                        class="fa fa-check text-success"></i></a><br>
                                                <a class="btn btn-sm" onclick="batalNotes('keluhan')"><i
                                                        class="fa fa-times text-danger"></i></a>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </form>
                            <form class="form-group" id="form-alergi"
                                action="<?= url('pemeriksaan/pasien/' . $id . '/updateAlergi/' . $data->idp) ?>">
                                <label class="fw-medium">Alergi</label>
                                <div class="input-group mb-3">
                                    <textarea name="alergi" class="form-control bg-warning" placeholder="-" id="alergi" readonly><?= $data->alergi ?></textarea>
                                    @if ($data->status != 'Dalam Antrian' && $data->status != 'Selesai')
                                        <div class="input-group-text bg-white">
                                            <div id="div-alergi-1">
                                                <a class="btn btn-sm" onclick="editNotes('alergi')"><i
                                                        class="fa fa-pencil text-primary"></i></a>
                                            </div>
                                            <div id="div-alergi-2" hidden>
                                                <a onclick="sendNotes('#form-alergi','alergi')" class="btn btn-sm"
                                                    id="btn_alergi"><i class="fa fa-check text-success"></i></a><br>
                                                <a class="btn btn-sm" onclick="batalNotes('alergi')"><i
                                                        class="fa fa-times text-danger"></i></a>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </form>
                            <form class="form-group" id="form-catatanpasien"
                                action="<?= url('pemeriksaan/pasien/' . $id . '/updateCatatan/' . $data->idp) ?>">
                                <label class="fw-medium">Catatan Pasien</label>
                                <div class="input-group mb-3">
                                    <textarea name="catatanpasien" class="form-control" placeholder="-" id="catatanpasien" readonly><?= $data->catatanpasien ?></textarea>
                                    @if ($data->status != 'Dalam Antrian' && $data->status != 'Selesai')
                                        <div class="input-group-text bg-white">
                                            <div id="div-catatanpasien-1">
                                                <a class="btn btn-sm" onclick="editNotes('catatanpasien')"><i
                                                        class="fa fa-pencil text-primary"></i></a>
                                            </div>
                                            <div id="div-catatanpasien-2" hidden>
                                                <a onclick="sendNotes('#form-catatanpasien', 'catatanpasien')"
                                                    class="btn btn-sm"><i class="fa fa-check text-success"></i></a><br>
                                                <a class="btn btn-sm" onclick="batalNotes('catatanpasien')"><i
                                                        class="fa fa-times text-danger"></i></a>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    @if ($data->status == 'Dalam Antrian')
                        <div id="startExamWrapper" class="mb-3 pt-5 text-center">
                            <button class="btn btn-outline-primary rounded-pill px-4 py-2" id="btnStartExam"
                                style="font-size: 16px;">
                                <svg width="25" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.4"
                                        d="M2 11.0786C2.05 13.4166 2.19 17.4156 2.21 17.8566C2.281 18.7996 2.642 19.7526 3.204 20.4246C3.986 21.3676 4.949 21.7886 6.292 21.7886C8.148 21.7986 10.194 21.7986 12.181 21.7986C14.176 21.7986 16.112 21.7986 17.747 21.7886C19.071 21.7886 20.064 21.3566 20.836 20.4246C21.398 19.7526 21.759 18.7896 21.81 17.8566C21.83 17.4856 21.93 13.1446 21.99 11.0786H2Z"
                                        fill="currentColor"></path>
                                    <path
                                        d="M11.2451 15.3843V16.6783C11.2451 17.0923 11.5811 17.4283 11.9951 17.4283C12.4091 17.4283 12.7451 17.0923 12.7451 16.6783V15.3843C12.7451 14.9703 12.4091 14.6343 11.9951 14.6343C11.5811 14.6343 11.2451 14.9703 11.2451 15.3843Z"
                                        fill="currentColor"></path>
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M10.211 14.5565C10.111 14.9195 9.762 15.1515 9.384 15.1015C6.833 14.7455 4.395 13.8405 2.337 12.4815C2.126 12.3435 2 12.1075 2 11.8555V8.38949C2 6.28949 3.712 4.58149 5.817 4.58149H7.784C7.972 3.12949 9.202 2.00049 10.704 2.00049H13.286C14.787 2.00049 16.018 3.12949 16.206 4.58149H18.183C20.282 4.58149 21.99 6.28949 21.99 8.38949V11.8555C21.99 12.1075 21.863 12.3425 21.654 12.4815C19.592 13.8465 17.144 14.7555 14.576 15.1105C14.541 15.1155 14.507 15.1175 14.473 15.1175C14.134 15.1175 13.831 14.8885 13.746 14.5525C13.544 13.7565 12.821 13.1995 11.99 13.1995C11.148 13.1995 10.433 13.7445 10.211 14.5565ZM13.286 3.50049H10.704C10.031 3.50049 9.469 3.96049 9.301 4.58149H14.688C14.52 3.96049 13.958 3.50049 13.286 3.50049Z"
                                        fill="currentColor"></path>
                                </svg> Mulai Pemeriksaan?
                            </button><br><br>
                            <b class="text-small">*tombol ini sekaligus memanggil antrian pasien</b>
                        </div>
                    @else
                        <div class="card">
                            <div class="card-body">
                                <div class="" id="tab-bawah">

                                    <i class="iconoir-sun-light loop"></i> Loading...

                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
@endsection



@section('script')
    <script src="{{ url('') }}/assets/js/3dmodel/three.js"></script>
    <script src="{{ url('') }}/assets/js/3dmodel/orbitControl.js"></script>
    <script src="{{ url('') }}/assets/js/3dmodel/loaderBin.js"></script>
    <script src="{{ url('') }}/assets/js/3dmodel/jspdf.js"></script>
    <script src="{{ url('') }}/assets/js/3dmodel/2dglobal.js"></script>
    <script>
        let tindakanInitial = []
        let tindakanList = [];
        var id = $('#idF').attr('value')
        // Toggle dropdowns
        $(document).ready(function() {
            $("#vitasign").addClass("active");
            const url = "<?= url('pemeriksaan/pasien') ?>/" + id + '/vitalsign';
            $.ajax({
                url: url,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(res) {
                    $("#tab-bawah").html(res);
                },
                error: function() {
                    $("#tab-bawah").html(
                        '<div class="p-3 text-danger">Gagal memuat konten.</div>');
                }
            });

        });
        // Toggle dropdowns

        $('.nav-link').on('click', function(e) {
            e.preventDefault();

            if ("{{ $data->status }}" == "Dalam Antrian") {
                return Toast.fire({
                    icon: "warning",
                    title: "silahkan mulai pemeriksaan!"
                });
            }
            const id = $('#idF').val();
            const tabId = $(this).attr('id');
            const url = "<?= url('pemeriksaan/pasien') ?>/" + id + '/' + tabId;

            $('.nav-link').removeClass('active');
            $('#' + tabId).addClass('active');
            $("#tab-bawah").html('<div class="p-3 text-center text-muted">Loading...</div>');

            $.ajax({
                url: url,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(res) {
                    $("#tab-bawah").html(res);
                },
                error: function() {
                    $("#tab-bawah").html(
                        '<div class="p-3 text-danger">Gagal memuat konten.</div>');
                }
            });

        });


        function editNotes(field) {
            $(`#div-${field}-1`).attr('hidden', true)
            $(`#div-${field}-2`).attr('hidden', false)
            $(`#${field}`).attr('readonly', false)
        }

        function batalNotes(field) {
            $(`#div-${field}-1`).attr('hidden', false)
            $(`#div-${field}-2`).attr('hidden', true)

            if (field == 'alergi') {
                $(`#${field}`).val('<?= $data->alergi ?>')
            } else if (field == 'catatanpasien') {
                $(`#${field}`).val('<?= $data->catatanpasien ?>')
            } else if (field == 'keluhan') {
                $(`#${field}`).val('<?= $data->keluhan ?>')
            }
            $(`#${field}`).attr('readonly', true)
        }

        function sendNotes(form, field) {
            $.ajax({
                url: $(form).attr('action'),
                method: 'POST',
                data: {
                    value: $('#' + field).val()
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    Toast.fire({
                        icon: "success",
                        title: "Data berhasil disimpan"
                    });
                    $(`#div-${field}-1`).attr('hidden', false)
                    $(`#div-${field}-2`).attr('hidden', true)
                    $(`#${field}`).attr('readonly', true)
                },
                error: function(xhr) {
                    Swal.close();
                    const msg = xhr.responseJSON?.error || 'Terjadi kesalahan.';
                    Toast.fire({
                        icon: "error",
                        title: msg
                    });
                },
            });
        };

        $('#btnStartExam').on('click', function() {
            // Simpan referensi tombol ke variabel
            var $btn = $(this);

            Swal.fire({
                title: "Anda yakin?",
                text: "Memulai pemeriksaan?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, Mulai!",
                // Opsi tambahan untuk UX yang lebih baik
                showLoaderOnConfirm: true, // Menampilkan loader pada tombol Swal
                allowOutsideClick: () => !Swal.isLoading() // Mencegah klik di luar saat loading
            }).then((result) => {
                if (result.isConfirmed) {

                    // 1. KUNCI TOMBOL UTAMA (Mencegah spam trigger)
                    $btn.prop('disabled', true);

                    // 2. TAMPILKAN LOADING DI SWAL (Mencegah spam tombol konfirmasi)
                    Swal.showLoading();

                    $.ajax({
                        url: "<?= url('pemeriksaan/pasien') ?>/" + id + "/updateStatus",
                        method: "POST",
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            // Jika sukses, tidak perlu enable tombol karena halaman akan reload
                            Toast.fire({
                                icon: 'success',
                                title: 'Pemeriksaan dimulai 🎉'
                            });
                            location.reload();
                        },
                        error: function() {
                            // 3. JIKA GAGAL: Buka kembali kunci tombol agar bisa dicoba lagi
                            $btn.prop('disabled', false);

                            // Sembunyikan loading Swal dan tampilkan error
                            Swal.hideLoading();

                            Toast.fire({
                                icon: 'error',
                                title: 'Gagal update status'
                            });
                        }
                    });
                }
            });
        });
    </script>
@endsection
