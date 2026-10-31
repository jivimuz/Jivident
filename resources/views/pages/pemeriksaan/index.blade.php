@extends('layout/main')

@section('style')
    <style>
        .clinical-note {
            background-color: rgba(var(--primary), .08) !important;
            border-color: rgba(var(--primary), .28) !important;
            color: rgba(var(--primary-dark), 1);
        }

        .clinical-note:focus {
            background-color: rgba(var(--primary), .12) !important;
            border-color: rgba(var(--primary), .75) !important;
            box-shadow: 0 0 0 .2rem rgba(var(--primary), .15);
        }

        .service-info-table th {
            color: rgba(var(--secondary-dark), 1);
            font-weight: 500;
            width: 45%;
        }

        .service-info-table td,
        .service-info-table th {
            padding: .35rem 0;
            vertical-align: top;
        }
    </style>
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
                    <div>

                        @php
                            $statusColors = [
                                'Dalam Antrian' => 'text-warning',
                                'Di Periksa' => 'text-info',
                                'Farmasi' => 'text-purple', // custom class sendiri ya
                                'Payment' => 'text-danger', // custom class sendiri ya
                                'Selesai' => 'text-success',
                            ];
                        @endphp

                        <div class="fw-bold text-center  text-black" style="padding-right:10px">
                            Status : <span
                                class="{{ $statusColors[$data->status] ?? 'text-dark' }}">{{ $data->status }}</span>
                        </div>

                    </div>
                    <!-- spacer biar teks tetap di tengah -->
                </div>
            </div>
            <div class="col-md-12">
                <div class="card equal-card">
                    <div class="card-header">
                        @if ($data->status == 'Di Periksa')
                            <div class="float-end">
                                <a id="btnfinishExam"
                                    class="btn btn-success rounded-pill d-flex align-items-center justify-content-center"
                                    style="">
                                    <svg width="18" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path opacity="0.4"
                                            d="M12.0865 22C11.9627 22 11.8388 21.9716 11.7271 21.9137L8.12599 20.0496C7.10415 19.5201 6.30481 18.9259 5.68063 18.2336C4.31449 16.7195 3.5544 14.776 3.54232 12.7599L3.50004 6.12426C3.495 5.35842 3.98931 4.67103 4.72826 4.41215L11.3405 2.10679C11.7331 1.96656 12.1711 1.9646 12.5707 2.09992L19.2081 4.32684C19.9511 4.57493 20.4535 5.25742 20.4575 6.02228L20.4998 12.6628C20.5129 14.676 19.779 16.6274 18.434 18.1581C17.8168 18.8602 17.0245 19.4632 16.0128 20.0025L12.4439 21.9088C12.3331 21.9686 12.2103 21.999 12.0865 22Z"
                                            fill="currentColor"></path>
                                        <path
                                            d="M11.3194 14.3209C11.1261 14.3219 10.9328 14.2523 10.7838 14.1091L8.86695 12.2656C8.57097 11.9793 8.56795 11.5145 8.86091 11.2262C9.15387 10.9369 9.63207 10.934 9.92906 11.2193L11.3083 12.5451L14.6758 9.22479C14.9698 8.93552 15.448 8.93258 15.744 9.21793C16.041 9.50426 16.044 9.97004 15.751 10.2574L11.8519 14.1022C11.7049 14.2474 11.5127 14.3199 11.3194 14.3209Z"
                                            fill="currentColor"></path>
                                    </svg> Selesaikan
                                </a>
                            </div>
                        @elseif ($data->status != 'Dalam Antrian' && $data->status != 'Di Periksa')
                            <div class="float-end">
                                <a id="btnBackExam"
                                    class="btn btn-success rounded-pill d-flex align-items-center justify-content-center">
                                    <svg width="18" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path opacity="0.4" fill-rule="evenodd" clip-rule="evenodd"
                                            d="M9.18824 3.74722C9.18824 3.33438 8.84724 3 8.42724 3H8.42624L6.79724 3.00098C4.60624 3.00294 2.82324 4.75331 2.82324 6.90279V8.76201C2.82324 9.17386 3.16424 9.50923 3.58424 9.50923C4.00424 9.50923 4.34624 9.17386 4.34624 8.76201V6.90279C4.34624 5.57604 5.44624 4.4964 6.79824 4.49444L8.42724 4.49345C8.84824 4.49345 9.18824 4.15907 9.18824 3.74722ZM17.1931 3.00029H15.6001C15.1801 3.00029 14.8391 3.33468 14.8391 3.74751C14.8391 4.15936 15.1801 4.49277 15.6001 4.49277H17.1931C18.5501 4.49277 19.6541 5.57535 19.6541 6.90603V8.7623C19.6541 9.17415 19.9951 9.50952 20.4151 9.50952C20.8361 9.50952 21.1761 9.17415 21.1761 8.7623V6.90603C21.1761 4.75165 19.3901 3.00029 17.1931 3.00029ZM9.23804 6.74266H14.762C15.367 6.74266 15.948 6.98094 16.371 7.40554C16.797 7.83407 17.033 8.40968 17.032 9.00883V10.2542C17.027 10.4003 16.908 10.5189 16.759 10.5229H7.23904C7.09104 10.518 6.97204 10.3993 6.96904 10.2542V9.00883C6.95804 7.76837 7.97404 6.75541 9.23804 6.74266Z"
                                            fill="currentColor"></path>
                                        <path
                                            d="M22.239 12.0413H1.762C1.342 12.0413 1 12.3756 1 12.7885C1 13.2003 1.342 13.5337 1.762 13.5337H2.823V17.0972C2.823 19.2467 4.607 20.9971 6.798 20.999L8.427 21C8.848 21 9.188 20.6656 9.189 20.2528C9.189 19.841 8.848 19.5066 8.428 19.5066L6.8 19.5056C5.447 19.5036 4.346 18.424 4.346 17.0972V13.5337H6.969V14.5251C6.959 15.7656 7.974 16.7795 9.238 16.7913H14.762C16.027 16.7795 17.042 15.7656 17.032 14.5251V13.5337H19.655V17.0933C19.655 18.425 18.551 19.5066 17.194 19.5066H15.601C15.18 19.5066 14.839 19.841 14.839 20.2528C14.839 20.6656 15.18 21 15.601 21H17.194C19.39 21 21.177 19.2487 21.177 17.0933V13.5337H22.239C22.659 13.5337 23 13.2003 23 12.7885C23 12.3756 22.659 12.0413 22.239 12.0413Z"
                                            fill="currentColor"></path>
                                    </svg> Pemeriksaan Ulang?
                                </a>
                            </div>
                        @endif
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
                                <button class="nav-link" id="diagnosa" aria-selected="true"><i
                                        class="fa fa-stethoscope fa-fw"></i> Diagnosa</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" id="consent" aria-selected="false"><i
                                        class="fa fa-file-signature fa-fw"></i>Persetujuan</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" id="soape" aria-selected="false"><i
                                        class="fa fa-clipboard fa-fw"></i>SOAPE</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" id="odontogram" aria-selected="false"><i
                                        class="fa fa-tooth pe-1 ps-1"></i>Odontogram</button>
                            </li>



                            <li class="nav-item">
                                <button class="nav-link" id="tindakan" aria-selected="false"><i
                                        class="fa fa-list-ol fa-fw"></i>Tindakan </button>
                            </li>


                            <li class="nav-item">
                                <button class="nav-link" id="lab" aria-selected="false"><i
                                        class="fa fa-flask fa-fw"></i>Lab</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" id="radiologi" aria-selected="false"><i
                                        class="fa fa-x-ray fa-fw"></i>Radiologi</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" id="rawatinap" aria-selected="false"><i
                                        class="iconoir-bed fa-fw"></i>Rawat Inap</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" id="resep" aria-selected="false"><i
                                        class="fa fa-flask fa-fw"></i>Resep Obat</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" id="cetaksurat" aria-selected="false"><i
                                        class="fa fa-folder fa-fw"></i>Cetak Surat</button>
                            </li>
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
                                    <div class="mb-2">
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

                            <div class="pt-3 border-b pb-2">
                                <h6 class="fw-medium mb-2">Informasi Layanan</h6>
                                <table class="table table-sm table-borderless service-info-table mb-0">
                                    <tbody>
                                        <tr>
                                            <th>Jenis Layanan</th>
                                            <td>
                                                @if ($admission)
                                                    <span class="badge bg-primary">Rawat Inap</span>
                                                @else
                                                    <span class="badge bg-secondary">Rawat Jalan</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @if ($admission)
                                            <tr>
                                                <th>Ruangan</th>
                                                <td>{{ $admission->kamar?->ruangan?->nama ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Ranjang</th>
                                                <td>{{ $admission->kamar?->nomor_ranjang ?? ($admission->kamar?->nama ?? '-') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Masuk Rawat Inap</th>
                                                <td>{{ $admission->tgl_masuk?->translatedFormat('d M Y, H:i') ?? '-' }}
                                                </td>
                                            </tr>
                                        @else
                                            <tr>
                                                <th>Ruang Pemeriksaan</th>
                                                <td>{{ $data->ruangan ?? '-' }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                            <form action="<?= url('pemeriksaan/pasien/' . $id . '/updateKeluhan/' . $data->idp) ?>"
                                class="form-group" id="form-keluhan">
                                <label class="fw-medium">Keluhan</label>
                                <div class="input-group mb-3">
                                    <textarea name="keluhan" class="form-control clinical-note" placeholder="-" id="keluhan" readonly><?= $data->keluhan ?></textarea>
                                    @if ($data->status == 'Di Periksa')
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
                                    <textarea name="alergi" class="form-control clinical-note" placeholder="-" id="alergi" readonly><?= $data->alergi ?></textarea>
                                    @if ($data->status == 'Di Periksa')
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
                                    @if ($data->status == 'Di Periksa')
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
        // State Management
        // var, not let: Turbo re-runs this script on every visit to this page
        var cart = [];
        var rCounter = 1;
        var ishold = false;
        var tindakanInitial = []
        var tindakanList = [];
        var id = $('#idF').attr('value')
        // Toggle dropdowns
        $(document).ready(function() {
            $("#vitasign").addClass("active");
            const url = "<?= url('pemeriksaan/pasien') ?>/" + id + '/vitalsign';
            ishold = true;
            $.ajax({
                url: url,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(res) {
                    $("#tab-bawah").html(res);
                    ishold = false;
                },
                error: function() {
                    $("#tab-bawah").html(
                        '<div class="p-3 text-danger">Gagal memuat konten.</div>');
                    ishold = false;
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
            if (ishold) {
                return Toast.fire({
                    icon: "warning",
                    title: "Sedang memuat..\nmohon tunggu sebentar!"
                });
            }
            ishold = true
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
                    ishold = false
                },
                error: function() {
                    $("#tab-bawah").html(
                        '<div class="p-3 text-danger">Gagal memuat konten.</div>');
                    ishold = false
                },
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
            if (ishold) {
                return Toast.fire({
                    icon: "warning",
                    title: "Sedang memuat..\nmohon tunggu sebentar!"
                });
            }
            ishold = true

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
                        data: {
                            f: 0
                        },
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
                            ishold = false

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
        $('#btnfinishExam').on('click', function() {
            // Simpan referensi tombol ke variabel
            var $btn = $(this);
            if (ishold) {
                return Toast.fire({
                    icon: "warning",
                    title: "Sedang memuat..\nmohon tunggu sebentar!"
                });
            }
            ishold = true
            Swal.fire({
                title: "Anda yakin?",
                text: "Selesaikan pemeriksaan?",
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
                        data: {
                            f: 1
                        },
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            // Jika sukses, tidak perlu enable tombol karena halaman akan reload
                            Toast.fire({
                                icon: 'success',
                                title: res.admisi_dipulangkan ?
                                    'Pemeriksaan diselesaikan & pasien rawat inap dipulangkan 🎉' :
                                    'Pemeriksaan diselesaikan 🎉.. '
                            });
                            location.reload();
                            // history.back()
                        },
                        error: function() {
                            // 3. JIKA GAGAL: Buka kembali kunci tombol agar bisa dicoba lagi
                            $btn.prop('disabled', false);
                            ishold = false

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

        $('#btnBackExam').on('click', function() {
            // Simpan referensi tombol ke variabel
            var $btn = $(this);
            if (ishold) {
                return Toast.fire({
                    icon: "warning",
                    title: "Sedang memuat..\nmohon tunggu sebentar!"
                });
            }
            ishold = true

            Swal.fire({
                title: "Anda yakin?",
                text: "Memulai pemeriksaan Ulang?",
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
                        data: {
                            f: 2
                        },
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            // Jika sukses, tidak perlu enable tombol karena halaman akan reload
                            Toast.fire({
                                icon: 'success',
                                title: 'Pemeriksaan ulang dimulai 🎉'
                            });
                            location.reload();
                        },
                        error: function() {
                            // 3. JIKA GAGAL: Buka kembali kunci tombol agar bisa dicoba lagi
                            $btn.prop('disabled', false);
                            ishold = false

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

        function selectProduct() {
            $('.select-product').each(function() {
                if (!$(this).hasClass('rendered')) {
                    $(this).select2({
                        ajax: {
                            url: "<?= url('pemeriksaan/getProductList') ?>",
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            delay: 250,
                            datatype: 'json',

                            data: function(params) {
                                return {
                                    q: params.term
                                }
                            },
                            processResults: function(response) {
                                var option = [];
                                option.push({
                                    text: 'Pilih',
                                    id: '',
                                    name: '',
                                })
                                for (data of response.data) {
                                    option.push({
                                        text: data.is_narkotika ?
                                            `${data.name} (${data.strength} ${data.satuan_name}) - NARKOTIKA` :
                                            `${data.name} (${data.strength} ${data.satuan_name})`,
                                        id: data.id,
                                        satuan: data.unit_name,
                                        stok: data.qty,
                                        harga: data.harga,

                                    })
                                }
                                return {
                                    results: option
                                };
                            },

                        },
                        placeholder: "Input Name for search",
                    })
                    $(this).addClass('rendered')
                }
            })
        }

        function selectPenggunaan() {
            $('.select-penggunaan').each(function() {
                if (!$(this).hasClass('rendered')) {
                    $(this).select2({
                        ajax: {
                            url: "<?= url('pemeriksaan/getPenggunaanList') ?>",
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            delay: 250,
                            datatype: 'json',
                            data: function(params) {
                                return {
                                    q: params.term
                                }
                            },
                            processResults: function(response) {
                                var option = [];
                                option.push({
                                    text: 'Pilih',
                                    id: '',
                                    name: '',
                                })
                                for (data of response.data) {
                                    option.push({
                                        text: data.name,
                                        id: data.id,
                                    })
                                }
                                return {
                                    results: option
                                };
                            },

                        },
                        placeholder: "Input Name for search",
                    })
                    $(this).addClass('rendered')
                }
            })
        }
    </script>
@endsection
