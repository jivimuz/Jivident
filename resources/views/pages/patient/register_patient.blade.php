@extends('layout/main')
@section('style')
    <style>
    </style>
@endsection

@section('content')
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12 mb-3">
                <div class="bg-white w-100 rounded-pill p-2 d-flex align-items-center justify-content-between shadow-sm">
                    <a href="javascript:history.back()"
                        class="btn btn-primary rounded-pill d-flex align-items-center justify-content-center"
                        style="width:45px; height:45px;">
                        <i class="iconoir-fast-arrow-left" style="font-size:20px;"></i>
                    </a>
                    <span class="text-primary fw-bold text-center flex-grow-1" style="font-size:24px;">
                        Pendaftaran Pasien
                    </span>
                    <div style="width:45px;"></div> <!-- spacer biar teks tetap di tengah -->
                </div>
            </div>

            <div class="col-md-4">
                <div class="card" data-iq-gsap="onStart" data-iq-opacity="0" data-iq-position-y="-40"
                    data-iq-duration=".6" data-iq-delay=".4" data-iq-trigger="scroll" data-iq-ease="none">
                    <div class="card-header">
                        <div class="card-body px-30">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="card-title fw-600">{{ $pasien->nama }}</h4>
                                <!-- <i class="mdi mdi-bell-plus fs-30 text-success"></i>						 -->
                                <ul class="box-controls pull-right">
                                    <li><a class="box-btn-slide text-dark" href="#"></a></li>
                                    <li><a class="box-btn-fullscreen text-dark" href="#"></a></li>
                                </ul>
                            </div>
                            <h5><a class="card-text fw-500 fs-16 pb-20 text-primary">{{ $pasien->norm }}</a></h5>
                        </div>
                    </div>
                    <div class="card-body" data-iq-gsap="onStart" data-iq-opacity="0" data-iq-position-y="-40"
                        data-iq-duration=".6" data-iq-delay=".6" data-iq-trigger="scroll" data-iq-ease="none"
                        style="padding-left: 40px; padding-right:40px">
                        <div class="box-body px-35">
                            <div class="border-b">
                                <h5 class="fw-500 text-primary-light bb-2 pb-10 m-0">DETAILS</h5>
                            </div>
                            <div class="d-flex justify-content-between align-items-center pb-15 pt-30">
                                <p class="m-0  fw-600 text-fade">Umur</p>
                                <h6 class="m-0   text-primary">{{ countAge($pasien->tgllahir) }}</h6>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-15">
                                <p class="m-0  fw-600 text-fade">Gender</p>
                                <h6 class="m-0   text-primary">{{ $pasien->jeniskelamin }}</h6>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-15">
                                <p class="m-0  fw-600 text-fade">Tanggal Lahir</p>
                                <h6 class="m-0   text-primary">
                                    {{ date('d-m-Y', strtotime($pasien->tgllahir)) }}</h6>
                            </div>

                            <br>
                            <div class="border-b">
                                <h5 class="fw-500 text-primary-light bb-2 mt-20 pb-10 m-0">Alamat</h5>
                            </div>
                            <div class=" p-2 mt-2 text-center" style="border-radius: 20px">

                                <span class="me-20 fw-500 ">
                                    {{ $pasien->alamat ?? '-' }}{{ $pasien->kelurahan ? ', ' . $pasien->kelurahan : '' }}{{ $pasien->kecamatan ? ', ' . $pasien->kecamatan : '' }}{{ $pasien->kotakabupaten ? ', ' . $pasien->kotakabupaten : '' }}{{ $pasien->provinsi ? ', ' . $pasien->provinsi : '' }}
                                </span>
                            </div>

                            <hr>
                            @if (!empty($riwayat) && count($riwayat))
                                <br>
                                <div class="border-b">
                                    <h5 class="fw-500 text-primary-light bb-2 mt-20 pb-10 m-0">Riwayat</h5>
                                </div>

                                <div class="mt-2" style="max-height: 220px; overflow:auto;">
                                    <ul class="list-group list-group-flush">
                                        @foreach ($riwayat as $r)
                                            <li class="list-group-item d-flex align-items-center justify-content-between">
                                                <span class="fw-600 text-dark">
                                                    {{ data_get($r, 'name', '-') }}
                                                </span>
                                                <span class="badge bg-primary rounded-pill">{{ $loop->iteration }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card" data-iq-gsap="onStart" data-iq-opacity="0" data-iq-position-y="-40"
                    data-iq-duration=".6" data-iq-delay=".4" data-iq-trigger="scroll" data-iq-ease="none">
                    <div class="card-header">
                        <div class="float-end">
                            <h6 class="text-primary"> Biaya :
                                <span class="text-danger">{{ $registrasi->value ? rupiah($registrasi->value) : 0 }}</span>
                            </h6>
                        </div>
                        <h4 class="card-title">Data Pendaftaran</h4>
                        <small>Lengkapi data di bawah ini</small>
                    </div>

                    <div class="card-body" data-iq-gsap="onStart" data-iq-opacity="0" data-iq-position-y="-40"
                        data-iq-duration=".6" data-iq-delay=".6" data-iq-trigger="scroll" data-iq-ease="none"
                        style="padding-left: 40px; padding-right:40px">
                        <div class="alert alert-success d-none" id="success-msg"></div>
                        <div class="alert alert-danger d-none" id="error-msg"></div>
                        <form id="formPasien">
                            @csrf
                            <div class="row g-3 mt-2">
                                <div class="col-md-4">
                                    <label>Tanggal Registrasi:*</label>
                                    <input type="datetime-local" name="tglregistrasi" id="tglregistrasi"
                                        class="form-control form-control-lg" value="{{ WIB() }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label>Ruangan:*</label>
                                    <select name="ruanganfk" id="ruanganfk" class="form-select" required>
                                        <option value="" selected hidden>~ Pilih satu</option>
                                        @foreach ($ruangan as $i)
                                            <option value="{{ $i->id }}" doktername="{{ $i->dokter }}"
                                                dokterid="{{ $i->dokterid }}">
                                                {{ $i->name . ' || ' . $i->dokter }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                {{-- <div class="col-md-4">
                                    <label>Dokter:*</label>
                                    <select name="dokterid" id="dokterid" class="form-select" required>
                                    </select>
                                </div> --}}
                                <div class="col-md-4">
                                    <label>Jenis Pembayaran:*</label>
                                    <select name="jenispembayaran" id="jenispembayaran" class="form-select" required>
                                        <option value="UMUM">UMUM</option>
                                        <option value="BPJS">BPJS</option>
                                    </select>
                                </div>

                                <div class="col-md-12">
                                    <label>Keterangan / Keluhan:</label>
                                    <textarea type="text" name="keluhan" class="form-control" placeholder="Masukan keterangan (Opsional)"></textarea>
                                </div>
                            </div>
                            <div class="float-end">
                                <button type="submit" class="btn btn-outline-primary rounded-pill  mt-3"
                                    id="btnSubmit"><i class="ti ti-send"></i>
                                    Daftarkan</button>
                            </div>
                        </form>
                        <code>*Harga registrasi di atur pada menu setting</code>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        $(document).ready(function() {
            let isSubmitting = false;

            $('#formPasien').on('submit', function(e) {
                e.preventDefault();

                if (isSubmitting) return; // cegah spam klik
                isSubmitting = true;

                Swal.fire({
                    title: 'Menyimpan data...',
                    text: 'Tunggu sebentar ya',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                $('#btnSubmit').prop('disabled', true).text('Menyimpan...');

                $.ajax({
                    url: "{{ url('patient/register-patient/' . $id) }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(res) {
                        Swal.close();

                        // $('#formPasien')[0].reset();

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message || 'Pilih langkah selanjutnya',
                            showConfirmButton: true,
                            showDenyButton: true,
                            showCancelButton: true,
                            confirmButtonText: 'Langsung Periksa',
                            denyButtonText: 'Ke Ruang Tunggu',
                            cancelButtonText: 'Tutup'
                        }).then((result) => {
                            openPopup("{{ url('print/print-antrian') }}/" + btoa(btoa(
                                res
                                .data)))
                            if (result.isConfirmed) {
                                location.href =
                                    "{{ url('pemeriksaan/pasien') }}/" + btoa(btoa(res
                                        .data));
                            } else if (result.isDenied) {
                                location.href = "{{ url('ruang-tunggu') }}";
                            }
                        });

                    },
                    error: function(xhr) {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: xhr.responseJSON?.message || 'Gagal menyimpan data'
                        });
                    },
                    complete: function() {
                        $('#btnSubmit').prop('disabled', false).text('Simpan');
                        isSubmitting = false;
                    }
                });
            });
        });
    </script>
@endsection
