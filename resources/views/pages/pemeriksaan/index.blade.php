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
                    <div style="width:45px;"></div> <!-- spacer biar teks tetap di tengah -->
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
                            <li class="nav-item">
                                <button class="nav-link" id="resepelektronik" aria-selected="false"><i
                                        class="fa fa-flask fa-fw"></i>Resep Obat</button>
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
                                    <div class="input-group-text bg-white">
                                        <div id="div-keluhan-1">
                                            <a class="btn btn-sm" onclick="editNotes('keluhan')"><i
                                                    class="fa fa-pencil text-primary"></i></a>
                                        </div>
                                        <div id="div-keluhan-2" hidden>
                                            <a onclick="sendNotes('#form-keluhan','keluhan')" class="btn btn-sm"><i
                                                    class="fa fa-check text-success"></i></a>
                                            <a class="btn btn-sm" onclick="batalNotes('keluhan')"><i
                                                    class="fa fa-times text-danger"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <form class="form-group" id="form-alergi"
                                action="<?= url('pemeriksaan/pasien/' . $id . '/updateAlergi/' . $data->idp) ?>">
                                <label class="fw-medium">Alergi</label>
                                <div class="input-group mb-3">
                                    <textarea name="alergi" class="form-control bg-warning" placeholder="-" id="alergi" readonly><?= $data->alergi ?></textarea>
                                    <div class="input-group-text bg-white">
                                        <div id="div-alergi-1">
                                            <a class="btn btn-sm" onclick="editNotes('alergi')"><i
                                                    class="fa fa-pencil text-primary"></i></a>
                                        </div>
                                        <div id="div-alergi-2" hidden>
                                            <a onclick="sendNotes('#form-alergi','alergi')" class="btn btn-sm"
                                                id="btn_alergi"><i class="fa fa-check text-success"></i></a>
                                            <a class="btn btn-sm" onclick="batalNotes('alergi')"><i
                                                    class="fa fa-times text-danger"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <form class="form-group" id="form-catatanpasien"
                                action="<?= url('pemeriksaan/pasien/' . $id . '/updateCatatan/' . $data->idp) ?>">
                                <label class="fw-medium">Catatan Pasien</label>
                                <div class="input-group mb-3">
                                    <textarea name="catatanpasien" class="form-control" placeholder="-" id="catatanpasien" readonly><?= $data->catatanpasien ?></textarea>
                                    <div class="input-group-text bg-white">
                                        <div id="div-catatanpasien-1">
                                            <a class="btn btn-sm" onclick="editNotes('catatanpasien')"><i
                                                    class="fa fa-pencil text-primary"></i></a>
                                        </div>
                                        <div id="div-catatanpasien-2" hidden>
                                            <a onclick="sendNotes('#form-catatanpasien', 'catatanpasien')"
                                                class="btn btn-sm"><i class="fa fa-check text-success"></i></a>
                                            <a class="btn btn-sm" onclick="batalNotes('catatanpasien')"><i
                                                    class="fa fa-times text-danger"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-body">
                            <div class="" id="tab-bawah">

                                <i class="iconoir-sun-light loop"></i> Loading...

                            </div>
                        </div>
                    </div>
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
    </script>
@endsection
