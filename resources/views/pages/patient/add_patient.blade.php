@extends('layout/main')

@section('style')
    {{-- <style>
        .menu-list:hover {
            background-color: #B6E2FF;
        }

        .menu-list {
            width: 100%;
            height: 130px;
            vertical-align: middle;
        }

        .form-section {
            border-radius: 8px;
            padding: 20px;
            background: #fff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 25px;
        }

        .section-title {
            font-weight: 600;
            color: #0d6efd;
            margin-bottom: 15px;
        }

        .form-label {
            font-weight: 500;
        }
    </style> --}}
@endsection


@section('content')
    <div class="content-inner mt-4 py-0">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header  d-flex align-items-center justify-content-between">
                        <h5 class="mb-0"><i class="iconoir-user-plus me-2" style="font-size: 25px"></i>Formulir Data Pasien
                        </h5>
                    </div>

                    <div class="card-body" style="padding-left: 40px; padding-right:40px">
                        <form id="add-form">

                            {{-- ================== IDENTITAS PASIEN ================== --}}
                            <h6 class="fw-bold mb-3">Identitas Pasien</h6>
                            <div class="row g-3">
                                <div class="col-md-2">
                                    <label class="form-label">No BPJS</label>
                                    <div class="input-group">
                                        <input type="text" id="nobpjs" name="nobpjs" class="form-control"
                                            placeholder="No BPJS">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">NIK / No Dokumen <span class="text-danger">*</span></label>
                                    <input type="text" id="nik" name="nik" class="required form-control">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" id="nama" name="nama" class="required form-control">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Umur (thn) <span class="text-danger">*</span></label>
                                    <input type="text" id="umur" name="umur" class="form-control" readonly
                                        required>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">No HP Pasien <span class="text-danger">*</span></label>
                                    <input type="text" id="nohp" name="nohp" class="required form-control">
                                </div>
                            </div>

                            <div class="row g-3 mt-2">
                                <div class="col-md-2">
                                    <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="jeniskelaminfk" name="jeniskelaminfk">
                                        <option disabled selected>Pilih</option>
                                        @foreach ($jk as $i)
                                            <option value="{{ $i->id }}">{{ $i->jeniskelamin }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                                    <input type="text" name="tempatlahir" class="required form-control">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                    <input type="date" id="tgllahir" name="tgllahir" class="required form-control"
                                        max="{{ date('Y-m-d') }}">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Nama Ayah</label>
                                    <input type="text" name="namaayah" class="form-control">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Nama Ibu</label>
                                    <input type="text" name="namaibu" class="form-control">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Agama </label>
                                    <select class="form-control select2 " name="agamafk" id="agamafk">
                                        <option disabled selected>Pilih</option>
                                        @foreach ($agama as $ag)
                                            <option value="{{ $ag->id }}">{{ $ag->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Status Perkawinan</label>
                                    <select class="form-control select2" name="statusperkawinanfk" id="statusperkawinanfk">
                                        <option disabled selected>Pilih</option>
                                        @foreach ($statusperkawinan as $sp)
                                            <option value="{{ $sp->id }}">{{ $sp->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Golongan Darah</label>
                                    <select class="form-control select2" name="golongandarah" id="golongandarah">
                                        <option disabled selected>Pilih</option>
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                        <option value="O">O</option>
                                        <option value="AB">AB</option>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Pendidikan</label>
                                    <select class="form-control select2" name="pendidikanfk" id="pendidikanfk">
                                        <option disabled selected>Pilih</option>
                                        @foreach ($pendidikan as $pd)
                                            <option value="{{ $pd->id }}">{{ $pd->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Pekerjaan</label>
                                    <select class="form-control select2" name="pekerjaanfk" id="pekerjaanfk">
                                        <option disabled selected>Pilih</option>
                                        @foreach ($pekerjaan as $pk)
                                            <option value="{{ $pk->id }}">{{ $pk->pekerjaan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- ================== PENANGGUNG JAWAB ================== --}}
                            <hr class="my-4">
                            <h6 class="fw-bold mb-3">Penanggung Jawab</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Nama Penanggung Jawab <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control required" name="namapenanggungjawab">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Hubungan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control required" name="hubungandenganpasien">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">No HP Penanggung Jawab <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control required" name="nohppenanggungjawab">
                                </div>
                            </div>

                            {{-- ================== DATA TAMBAHAN ================== --}}
                            <hr class="my-4">
                            <h6 class="fw-bold mb-3">Data Tambahan</h6>
                            <div class="row g-3">
                                <div class="col-md-2">
                                    <label class="form-label">Nomor Asuransi</label>
                                    <input type="text" class="form-control" name="noasuransilain">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">No Telp Rumah</label>
                                    <input type="text" class="form-control" name="notelprumah">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Bahasa Dikuasai</label>
                                    <input type="text" class="form-control" name="bahasadikuasai">
                                </div>
                            </div>

                            {{-- ================== ALAMAT ================== --}}
                            <hr class="my-4">
                            <h6 class="fw-bold mb-3">Alamat</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                                    <textarea name="alamat" class="form-control required" rows="4"></textarea>
                                </div>

                                <div class="col-md-8 row">
                                    <div class="col-md-3">
                                        <label class="form-label">Kode POS</label>
                                        <input type="text" id="kodepos" name="kodepos" class="form-control">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">RT</label>
                                        <input type="text" id="rt" name="rt" class="form-control">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">RW</label>
                                        <input type="text" id="rw" name="rw" class="form-control">
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label">Kelurahan <span class="text-danger">*</span></label>
                                        <select class="form-control select2 required" id="kelurahanfk"
                                            name="kelurahanfk"></select>
                                        <input type="hidden" id="kelurahan" name="kelurahan">
                                        <input type="hidden" id="kecamatanfk" name="kecamatanfk">
                                        <input type="hidden" id="kotakabupatenfk" name="kotakabupatenfk">
                                        <input type="hidden" id="provinsifk" name="provinsifk">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Kecamatan</label>
                                        <input type="text" id="kecamatan" name="kecamatan" class="form-control"
                                            readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Kabupaten</label>
                                        <input type="text" id="kotakabupaten" name="kotakabupaten"
                                            class="form-control" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Provinsi</label>
                                        <input type="text" id="provinsi" name="provinsi" class="form-control"
                                            readonly>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Kebangsaan</label>
                                        <select class="form-control select2" name="kebangsaanfk" id="kebangsaanfk">
                                            <option disabled selected>Pilih</option>
                                            @foreach ($kebangsaan as $kb)
                                                <option value="{{ $kb->id }}">{{ $kb->kebangsaan }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Etnis</label>
                                        <select class="form-control select2" name="etnisfk" id="etnisfk">
                                            <option disabled selected>Pilih</option>
                                            @foreach ($etnis as $et)
                                                <option value="{{ $et->id }}">{{ $et->etnis }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Negara</label>
                                        <select class="form-control select2" name="negarafk" id="negarafk">
                                            <option disabled selected>Pilih</option>
                                            @foreach ($negara as $ng)
                                                <option value="{{ $ng->id }}">{{ $ng->negara }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 text-end">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="iconoir-send"></i> Simpan Data
                                </button>
                                <button type="reset" class="btn btn-light border px-4">Reset</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection



@section('script')
    <script>
        $(document).ready(function() {
            $('.select2').select2();
            $('#tgllahir').change(function() {
                const umur = Math.floor((new Date() - new Date($(this).val())) / 3.15576e+10);
                $('#umur').val(umur);
            });

            $('#kelurahanfk').select2({
                ajax: {
                    url: '{{ url('') }}/patient/searchAddress',
                    dataType: 'json',
                    delay: 250,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: params => ({
                        q: params.term
                    }),
                    processResults: data => ({
                        results: data.data.map(item => ({
                            id: item.id,
                            text: `${item.kelurahan}, ${item.kecamatan}, ${item.kotakabupaten}, ${item.provinsi}`,
                            ...item
                        }))
                    }),
                    cache: true
                },
                placeholder: 'Cari kelurahan...',
                minimumInputLength: 2
            });

            $('#kelurahanfk').on('select2:select', function(e) {
                const d = e.params.data;
                $('#provinsifk').val(d.provinsifk);
                $('#provinsi').val(d.provinsi);
                $('#kotakabupatenfk').val(d.kotakabupatenfk);
                $('#kotakabupaten').val(d.kotakabupaten);
                $('#kecamatanfk').val(d.kecamatanfk);
                $('#kecamatan').val(d.kecamatan);
                $('#kelurahan').val(d.kelurahan);
            });
        });

        $('#add-form').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const submitBtn = form.find('button[type="submit"]');
            let isValid = true;

            form.find('.required').each(function() {
                const input = $(this);
                if (!input.val()) {
                    input.addClass('is-invalid');
                    isValid = false;
                } else input.removeClass('is-invalid');
            });

            if (!isValid) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: 'Harap isi semua field yang wajib diisi!'
                });
                return;
            }

            // Disable tombol biar gak spam
            submitBtn.prop('disabled', true)
                .html('<i class="ti ti-loader spin me-2"></i> Menyimpan...');

            const formData = new FormData(this);

            Swal.fire({
                title: 'Menyimpan data...',
                text: 'Mohon tunggu sebentar',
                didOpen: () => Swal.showLoading(),
                allowOutsideClick: false,
            });

            $.ajax({
                url: "{{ url('patient/saveData') }}",
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.close();
                    form.trigger('reset');
                    form.find('.select2').val(null).trigger('change');
                    if (typeof getList === 'function') getList();
                    Toast.fire({
                        icon: "success",
                        title: "Data pasien berhasil disimpan"
                    });
                },
                error: function(xhr) {
                    Swal.close();
                    const msg = xhr.responseJSON?.error || 'Terjadi kesalahan.';
                    Toast.fire({
                        icon: "error",
                        title: msg
                    });
                },
                complete: function() {
                    submitBtn.prop('disabled', false)
                        .html('<i class="ti-save me-2"></i> Simpan Data');
                }
            });
        });
    </script>
@endsection
