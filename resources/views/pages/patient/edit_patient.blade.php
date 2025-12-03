@extends('layout/main')

@section('style')
    {{-- Style bawaan --}}
@endsection

@section('content')
    <div class="content-inner mt-4 py-0">
        <div class="row">

            <div class="col-md-12 mb-3">
                <div class="bg-white w-100 rounded-pill p-2 d-flex align-items-center justify-content-between shadow-sm">
                    <a href="javascript:history.back()"
                        class="btn btn-primary rounded-pill d-flex align-items-center justify-content-center"
                        style="width:45px; height:45px;">
                        <i class="iconoir-fast-arrow-left" style="font-size:20px;"></i>
                    </a>
                    <span class="text-primary fw-bold text-center flex-grow-1" style="font-size:24px;">
                        Detail Pasien: {{ $data->nama }}
                    </span>
                    {{-- Tombol Edit di Header (Opsional, atau gunakan yang di bawah form) --}}
                    <div style="width:45px;"></div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0"><i class="iconoir-user me-2" style="font-size: 25px"></i>Data Pasien</h5>

                        {{-- Indikator Mode --}}
                        <span id="mode-badge" class="badge bg-secondary">Mode: Read Only</span>
                    </div>

                    <div class="card-body" style="padding-left: 40px; padding-right:40px">
                        <form id="update-form">
                            {{-- Hidden ID for Update --}}
                            <input type="hidden" name="id" value="{{ $data->id }}">

                            {{-- ================== IDENTITAS PASIEN ================== --}}
                            <h6 class="fw-bold mb-3">Identitas Pasien</h6>
                            <div class="row g-3">
                                <div class="col-md-2">
                                    <label class="form-label">No BPJS</label>
                                    <div class="input-group">
                                        <input type="text" id="nobpjs" name="nobpjs" class="form-control"
                                            placeholder="No BPJS" value="{{ $data->nobpjs }}" disabled>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">NIK / No Dokumen <span class="text-danger">*</span></label>
                                    <input type="text" id="nik" name="nik" class="required form-control"
                                        maxlength="16" value="{{ $data->nik }}" disabled>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" id="nama" name="nama" class="required form-control"
                                        maxlength="30" value="{{ $data->nama }}" disabled>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Umur (thn) <span class="text-danger">*</span></label>
                                    <input type="text" id="umur" name="umur" class="form-control" readonly
                                        required value="{{ $data->umur }}" disabled>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">No HP Pasien <span class="text-danger">*</span></label>
                                    <input type="text" id="nohp" name="nohp" class="required form-control"
                                        maxlength="16" value="{{ $data->nohp }}" disabled>
                                </div>
                            </div>

                            <div class="row g-3 mt-2">
                                <div class="col-md-2">
                                    <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="jeniskelaminfk" name="jeniskelaminfk" disabled>
                                        <option disabled selected>Pilih</option>
                                        @foreach ($jk as $i)
                                            <option value="{{ $i->id }}"
                                                {{ $data->jeniskelaminfk == $i->id ? 'selected' : '' }}>
                                                {{ $i->jeniskelamin }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                                    <input type="text" name="tempatlahir" class="required form-control" maxlength="30"
                                        value="{{ $data->tempatlahir }}" disabled>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                    <input type="date" id="tgllahir" name="tgllahir" class="required form-control"
                                        max="{{ date('Y-m-d') }}" maxlength="50" value="{{ $data->tgllahir }}" disabled>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Nama Ayah</label>
                                    <input type="text" name="namaayah" class="form-control" maxlength="30"
                                        value="{{ $data->namaayah }}" disabled>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Nama Ibu</label>
                                    <input type="text" name="namaibu" class="form-control" maxlength="30"
                                        value="{{ $data->namaibu }}" disabled>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Agama </label>
                                    <select class="form-control select2" name="agamafk" id="agamafk" disabled>
                                        <option disabled selected>Pilih</option>
                                        @foreach ($agama as $ag)
                                            <option value="{{ $ag->id }}"
                                                {{ $data->agamafk == $ag->id ? 'selected' : '' }}>
                                                {{ $ag->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Status Perkawinan</label>
                                    <select class="form-control select2" name="statusperkawinanfk"
                                        id="statusperkawinanfk" disabled>
                                        <option disabled selected>Pilih</option>
                                        @foreach ($statusperkawinan as $sp)
                                            <option value="{{ $sp->id }}"
                                                {{ $data->statusperkawinanfk == $sp->id ? 'selected' : '' }}>
                                                {{ $sp->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Golongan Darah</label>
                                    <select class="form-control select2" name="golongandarah" id="golongandarah"
                                        disabled>
                                        <option disabled>Pilih</option>
                                        <option value="A" {{ $data->golongandarah == 'A' ? 'selected' : '' }}>A
                                        </option>
                                        <option value="B" {{ $data->golongandarah == 'B' ? 'selected' : '' }}>B
                                        </option>
                                        <option value="O" {{ $data->golongandarah == 'O' ? 'selected' : '' }}>O
                                        </option>
                                        <option value="AB" {{ $data->golongandarah == 'AB' ? 'selected' : '' }}>AB
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Pendidikan</label>
                                    <select class="form-control select2" name="pendidikanfk" id="pendidikanfk" disabled>
                                        <option disabled selected>Pilih</option>
                                        @foreach ($pendidikan as $pd)
                                            <option value="{{ $pd->id }}"
                                                {{ $data->pendidikanfk == $pd->id ? 'selected' : '' }}>
                                                {{ $pd->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Pekerjaan</label>
                                    <select class="form-control select2" name="pekerjaanfk" id="pekerjaanfk" disabled>
                                        <option disabled selected>Pilih</option>
                                        @foreach ($pekerjaan as $pk)
                                            <option value="{{ $pk->id }}"
                                                {{ $data->pekerjaanfk == $pk->id ? 'selected' : '' }}>
                                                {{ $pk->pekerjaan }}
                                            </option>
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
                                    <input type="text" class="form-control required" name="namapenanggungjawab"
                                        maxlength="30" value="{{ $data->namapenanggungjawab }}" disabled>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Hubungan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control required" name="hubungandenganpasien"
                                        maxlength="30" value="{{ $data->hubungandenganpasien }}" disabled>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">No HP Penanggung Jawab <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control required" name="nohppenanggungjawab"
                                        maxlength="16" value="{{ $data->nohppenanggungjawab }}" disabled>
                                </div>
                            </div>

                            {{-- ================== DATA TAMBAHAN ================== --}}
                            <hr class="my-4">
                            <h6 class="fw-bold mb-3">Data Tambahan</h6>
                            <div class="row g-3">
                                <div class="col-md-2">
                                    <label class="form-label">Nomor Asuransi</label>
                                    <input type="text" class="form-control" name="noasuransilain" maxlength="30"
                                        value="{{ $data->noasuransilain }}" disabled>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">No Telp Rumah</label>
                                    <input type="text" class="form-control" name="notelprumah" maxlength="16"
                                        value="{{ $data->notelprumah }}" disabled>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Bahasa Dikuasai</label>
                                    <input type="text" class="form-control" name="bahasadikuasai" maxlength="30"
                                        value="{{ $data->bahasadikuasai }}" disabled>
                                </div>
                            </div>

                            {{-- ================== ALAMAT ================== --}}
                            <hr class="my-4">
                            <h6 class="fw-bold mb-3">Alamat</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                                    <textarea name="alamat" class="form-control required" rows="4" disabled>{{ $data->alamat }}</textarea>
                                </div>

                                <div class="col-md-8 row">
                                    <div class="col-md-3">
                                        <label class="form-label">Kode POS</label>
                                        <input type="text" id="kodepos" name="kodepos" class="form-control"
                                            maxlength="7" value="{{ $data->kodepos }}" disabled>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">RT</label>
                                        <input type="text" id="rt" name="rt" class="form-control"
                                            maxlength="3" value="{{ $data->rt }}" disabled>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">RW</label>
                                        <input type="text" id="rw" name="rw" class="form-control"
                                            maxlength="3" value="{{ $data->rw }}" disabled>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label">Kelurahan <span class="text-danger">*</span></label>
                                        <select class="form-control select2 required" id="kelurahanfk" name="kelurahanfk"
                                            disabled>
                                            @if ($data->kelurahanfk)
                                                {{-- Trik untuk Select2 AJAX agar menampilkan nilai awal --}}
                                                <option value="{{ $data->kelurahanfk }}" selected>
                                                    {{ $data->kelurahan }}, {{ $data->kecamatan }},
                                                    {{ $data->kotakabupaten }}, {{ $data->provinsi }}
                                                </option>
                                            @endif
                                        </select>
                                        {{-- Hidden inputs tetap diisi value-nya --}}
                                        <input type="hidden" id="kelurahan" name="kelurahan"
                                            value="{{ $data->kelurahan }}">
                                        <input type="hidden" id="kecamatanfk" name="kecamatanfk"
                                            value="{{ $data->kecamatanfk }}">
                                        <input type="hidden" id="kotakabupatenfk" name="kotakabupatenfk"
                                            value="{{ $data->kotakabupatenfk }}">
                                        <input type="hidden" id="provinsifk" name="provinsifk"
                                            value="{{ $data->provinsifk }}">
                                    </div>

                                    {{-- Kolom Readonly (Autofilled by System) --}}
                                    <div class="col-md-3">
                                        <label class="form-label">Kecamatan</label>
                                        <input type="text" id="kecamatan" name="kecamatan" class="form-control"
                                            maxlength="30" readonly value="{{ $data->kecamatan }}" disabled>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Kabupaten</label>
                                        <input type="text" id="kotakabupaten" name="kotakabupaten" maxlength="30"
                                            class="form-control" readonly value="{{ $data->kotakabupaten }}" disabled>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Provinsi</label>
                                        <input type="text" id="provinsi" name="provinsi" class="form-control"
                                            maxlength="30" readonly value="{{ $data->provinsi }}" disabled>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Kebangsaan</label>
                                        <select class="form-control select2" name="kebangsaanfk" id="kebangsaanfk"
                                            disabled>
                                            <option disabled selected>Pilih</option>
                                            @foreach ($kebangsaan as $kb)
                                                <option value="{{ $kb->id }}"
                                                    {{ $data->kebangsaanfk == $kb->id ? 'selected' : '' }}>
                                                    {{ $kb->kebangsaan }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Etnis</label>
                                        <select class="form-control select2" name="etnisfk" id="etnisfk" disabled>
                                            <option disabled selected>Pilih</option>
                                            @foreach ($etnis as $et)
                                                <option value="{{ $et->id }}"
                                                    {{ $data->etnisfk == $et->id ? 'selected' : '' }}>
                                                    {{ $et->etnis }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Negara</label>
                                        <select class="form-control select2" name="negarafk" id="negarafk" disabled>
                                            <option disabled selected>Pilih</option>
                                            @foreach ($negara as $ng)
                                                <option value="{{ $ng->id }}"
                                                    {{ $data->negarafk == $ng->id ? 'selected' : '' }}>
                                                    {{ $ng->negara }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 text-end">
                                {{-- Tombol Edit --}}
                                <button type="button" id="btn-edit" class="btn btn-warning px-4 text-white">
                                    <i class="iconoir-edit-pencil"></i> Edit Data
                                </button>

                                {{-- Grup Tombol Update (Hidden Awal) --}}
                                <div id="btn-update-group" class="d-none">
                                    <button type="button" id="btn-cancel" class="btn btn-light border px-4 me-2">
                                        Batal
                                    </button>
                                    <button type="submit" class="btn btn-success px-4">
                                        <i class="iconoir-floppy-disk"></i> Update Data
                                    </button>
                                </div>
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
            // Inisialisasi Select2 (akan ikut disabled jika select aslinya disabled)
            $('.select2').select2();

            // Hitung umur otomatis jika tgl lahir berubah
            $('#tgllahir').change(function() {
                const umur = Math.floor((new Date() - new Date($(this).val())) / 3.15576e+10);
                $('#umur').val(umur);
            });

            // Logic Tombol Edit
            $('#btn-edit').click(function() {
                // Aktifkan semua input form
                $('#update-form input, #update-form select, #update-form textarea').prop('disabled', false);

                // Kembalikan readonly untuk field yang memang harus readonly (Umur & Alamat Otomatis)
                $('#umur, #kecamatan, #kotakabupaten, #provinsi').prop('readonly', true);

                // Tampilan UI
                $(this).addClass('d-none'); // Sembunyikan tombol Edit
                $('#btn-update-group').removeClass('d-none'); // Munculkan tombol Update
                $('#mode-badge').removeClass('bg-secondary').addClass('bg-success').text('Mode: Edit');

                Toast.fire({
                    icon: "info",
                    title: "Mode Edit Diaktifkan"
                });
            });

            // Logic Tombol Batal (Reload Page)
            $('#btn-cancel').click(function() {
                location.reload();
            });

            // Select2 AJAX untuk Kelurahan
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

            // Auto-fill address fields on selection
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

        // Submit Logic (Update)
        $('#update-form').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const submitBtn = form.find('button[type="submit"]');
            let isValid = true;

            form.find('.required').each(function() {
                const input = $(this);
                // Validasi hanya berjalan jika field tidak disabled
                if (!input.prop('disabled') && !input.val()) {
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

            submitBtn.prop('disabled', true)
                .html('<i class="ti ti-loader spin me-2"></i> Mengupdate...');

            const formData = new FormData(this);

            Swal.fire({
                title: 'Mengupdate data...',
                text: 'Mohon tunggu sebentar',
                didOpen: () => Swal.showLoading(),
                allowOutsideClick: false,
            });

            $.ajax({
                // URL Update (Asumsi endpoint updateData menerima ID atau via POST body)
                url: "{{ url('patient/updateData') }}",
                method: 'POST', // Biasanya PUT/PATCH, tapi form-data lebih mudah via POST di Laravel
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.close();
                    Toast.fire({
                        icon: "success",
                        title: "Data pasien berhasil diupdate"
                    });
                    // Reload halaman untuk melihat perubahan
                    setTimeout(() => location.reload(), 1500);
                },
                error: function(xhr) {
                    Swal.close();
                    const msg = xhr.responseJSON?.error || 'Terjadi kesalahan saat update.';
                    Toast.fire({
                        icon: "error",
                        title: msg
                    });
                    submitBtn.prop('disabled', false)
                        .html('<i class="iconoir-floppy-disk me-2"></i> Update Data');
                }
            });
        });
    </script>
@endsection
