<div id="operasiTab" class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom pb-3 pt-4">
        <h5 class="card-title fw-bold text-primary mb-0">
            <i class="iconoir-hospital-circle me-2"></i>Operasi
        </h5>
        <small class="text-muted">Jadwalkan operasi dari sini — kalender penuh semua jadwal ada di Monitor
            Operasi, dan manajemen ruangan ada di halaman Master &gt; Ruangan Operasi.</small>
    </div>
    <div class="card-body p-4">
        @php
            $statusLabel = [
                'dijadwalkan' => ['Dijadwalkan', 'bg-primary'],
                'berlangsung' => ['Berlangsung', 'bg-warning'],
                'selesai' => ['Selesai', 'bg-success'],
                'dibatalkan' => ['Dibatalkan', 'bg-secondary'],
            ];
            $isAktif = $jadwal && in_array($jadwal->status, ['dijadwalkan', 'berlangsung']);
        @endphp

        @if ($jadwal)
            <input type="hidden" id="opJadwalId" value="{{ $jadwal->id }}">
            <input type="hidden" id="opJadwalStatus" value="{{ $jadwal->status }}">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <span class="badge {{ $statusLabel[$jadwal->status][1] ?? 'bg-dark' }} mb-2">
                        {{ $statusLabel[$jadwal->status][0] ?? $jadwal->status }}
                    </span>
                    <h6 class="mb-0">{{ $jadwal->nama_operasi }}</h6>
                    <small class="text-muted">
                        {{ $jadwal->ruangan?->nama ?? '-' }} ·
                        {{ $jadwal->dokter?->employee?->fullname ?? '-' }}
                    </small>
                    <div class="text-muted small mt-1">
                        {{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('d M Y') }},
                        {{ substr((string) $jadwal->jam_mulai, 0, 5) }} - {{ substr((string) $jadwal->jam_selesai, 0, 5) }}
                    </div>
                    @if ($jadwal->catatan)
                        <div class="text-muted small mt-1">Catatan: {{ $jadwal->catatan }}</div>
                    @endif
                </div>
                @if ($isAktif)
                    <div class="d-flex flex-column gap-1">
                        @if ($jadwal->status === 'dijadwalkan')
                            <button type="button" class="btn btn-outline-warning btn-sm" id="btnOpBerlangsung">
                                Mulai Berlangsung
                            </button>
                        @endif
                        @if ($jadwal->status === 'berlangsung')
                            <button type="button" class="btn btn-outline-success btn-sm" id="btnOpSelesai">
                                Tandai Selesai
                            </button>
                        @endif
                        <button type="button" class="btn btn-outline-danger btn-sm" id="btnOpBatal">
                            Batalkan
                        </button>
                    </div>
                @endif
            </div>
        @else
            <div class="text-center py-3 mb-0">
                <p class="text-muted mb-3">Belum ada jadwal operasi untuk kunjungan ini.</p>
                <button type="button" class="btn btn-primary" id="btnBukaJadwalOperasi">
                    <i class="iconoir-plus-circle me-1"></i> Jadwalkan Operasi
                </button>
            </div>
        @endif

        @if ($jadwal && !$isAktif)
            <hr>
            <button type="button" class="btn btn-outline-primary btn-sm" id="btnBukaJadwalOperasi">
                <i class="iconoir-plus-circle me-1"></i> Jadwalkan Operasi Baru
            </button>
        @endif
    </div>
</div>

<!-- Modal Jadwalkan Operasi -->
<div class="modal fade op-modal" id="modalJadwalOperasi" data-op-modal="true" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Jadwalkan Operasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Ruangan Operasi</label>
                    <select class="form-control" id="opRuangan" style="width:100%" required></select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Dokter Bedah</label>
                    <select class="form-control" id="opDokter" style="width:100%" required></select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama/Jenis Operasi</label>
                    <input type="text" class="form-control" id="opNamaOperasi" maxlength="255"
                        placeholder="Contoh: Odontektomi gigi molar 3">
                </div>
                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label">Tanggal</label>
                        <input type="date" class="form-control" id="opTanggal">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jam Mulai</label>
                        <input type="time" class="form-control" id="opJamMulai">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jam Selesai</label>
                        <input type="time" class="form-control" id="opJamSelesai">
                    </div>
                </div>
                <div class="mb-0 mt-3">
                    <label class="form-label">Catatan (opsional)</label>
                    <textarea class="form-control" id="opCatatan" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnKonfirmasiJadwalOperasi">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        // Partial ini diganti lewat `#tab-bawah.html(...)` tiap kali tab
        // dibuka — modal harus dipindah ke <body> (posisi yang direkomendasikan
        // Bootstrap untuk komponen fixed/backdrop) dan instance lama dibuang,
        // sama persis dengan pola yang sudah dipakai tab Rawat Inap, atau
        // modal akan menumpuk/gagal tampil tiap kali tab ini dibuka ulang.
        function opMountModal() {
            var modalBaru = document.querySelector('#tab-bawah .modal[data-op-modal="true"]');

            document.querySelectorAll('body > .modal[data-op-modal="true"]').forEach(function(modal) {
                if (modal === modalBaru) return;
                if (window.bootstrap?.Modal) window.bootstrap.Modal.getInstance(modal)?.dispose();
                $(modal).find('select').each(function() {
                    if ($.fn.select2 && $(this).hasClass('select2-hidden-accessible')) $(this).select2('destroy');
                });
                modal.remove();
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open').css('padding-right', '');
            });

            if (modalBaru) document.body.appendChild(modalBaru);
        }

        opMountModal();

        function opShowModal(id) {
            var element = document.getElementById(id);
            if (!element) return;
            if (window.bootstrap?.Modal) {
                window.bootstrap.Modal.getOrCreateInstance(element).show();
                return;
            }
            if ($.fn.modal) $(element).modal('show');
        }

        function opHideModal(id) {
            var element = document.getElementById(id);
            if (!element) return;
            if (window.bootstrap?.Modal) {
                window.bootstrap.Modal.getOrCreateInstance(element).hide();
                return;
            }
            if ($.fn.modal) $(element).modal('hide');
        }

        $('#opRuangan').select2({
            ajax: {
                url: "{{ url('pemeriksaan/pasien/' . $id . '/operasi/ruanganList') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                processResults: function(res) {
                    return {
                        results: res.data.map(r => ({
                            id: r.id,
                            text: r.nama
                        }))
                    };
                }
            },
            placeholder: 'Pilih ruangan operasi',
            dropdownParent: $('#modalJadwalOperasi')
        });

        $('#opDokter').select2({
            ajax: {
                url: "{{ url('pemeriksaan/pasien/' . $id . '/operasi/dokterList') }}",
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
                            text: d.fullname
                        }))
                    };
                }
            },
            placeholder: 'Cari dokter bedah',
            dropdownParent: $('#modalJadwalOperasi')
        });

        $('#btnBukaJadwalOperasi').on('click', function() {
            $('#opRuangan,#opDokter').val(null).trigger('change');
            $('#opNamaOperasi,#opTanggal,#opJamMulai,#opJamSelesai,#opCatatan').val('');
            opShowModal('modalJadwalOperasi');
        });

        $('#btnKonfirmasiJadwalOperasi').on('click', function() {
            var payload = {
                fk_ruangan_operasi: $('#opRuangan').val(),
                fk_dokter: $('#opDokter').val(),
                nama_operasi: $('#opNamaOperasi').val(),
                tanggal: $('#opTanggal').val(),
                jam_mulai: $('#opJamMulai').val(),
                jam_selesai: $('#opJamSelesai').val(),
                catatan: $('#opCatatan').val(),
            };
            if (!payload.fk_ruangan_operasi || !payload.fk_dokter || !payload.nama_operasi || !payload
                .tanggal || !payload.jam_mulai || !payload.jam_selesai) {
                return Toast.fire({
                    icon: 'warning',
                    title: 'Lengkapi semua field wajib dulu'
                });
            }

            $.ajax({
                url: "{{ url('pemeriksaan/pasien/' . $id . '/operasi/save') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: payload,
                success: function() {
                    Toast.fire({
                        icon: 'success',
                        title: 'Operasi berhasil dijadwalkan'
                    });
                    opHideModal('modalJadwalOperasi');
                    $('#operasi').trigger('click');
                },
                error: function(xhr) {
                    Toast.fire({
                        icon: 'error',
                        title: xhr.responseJSON?.error ?? 'Gagal menjadwalkan operasi'
                    });
                }
            });
        });

        function opChangeStatus(status) {
            var jadwalId = $('#opJadwalId').val();
            $.ajax({
                url: "{{ url('pemeriksaan/pasien/' . $id . '/operasi/status') }}/" + jadwalId,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    status: status
                },
                success: function() {
                    Toast.fire({
                        icon: 'success',
                        title: 'Status operasi diperbarui'
                    });
                    $('#operasi').trigger('click');
                },
                error: function(xhr) {
                    Toast.fire({
                        icon: 'error',
                        title: xhr.responseJSON?.error ?? 'Gagal memperbarui status'
                    });
                }
            });
        }

        $('#btnOpBerlangsung').on('click', function() {
            opChangeStatus('berlangsung');
        });
        $('#btnOpSelesai').on('click', function() {
            opChangeStatus('selesai');
        });

        $('#btnOpBatal').on('click', function() {
            var jadwalId = $('#opJadwalId').val();
            Swal.fire({
                title: 'Batalkan jadwal operasi ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Batalkan',
                cancelButtonText: 'Tutup'
            }).then(function(result) {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: "{{ url('pemeriksaan/pasien/' . $id . '/operasi/cancel') }}/" + jadwalId,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function() {
                        Toast.fire({
                            icon: 'success',
                            title: 'Jadwal operasi dibatalkan'
                        });
                        $('#operasi').trigger('click');
                    },
                    error: function(xhr) {
                        Toast.fire({
                            icon: 'error',
                            title: xhr.responseJSON?.error ?? 'Gagal membatalkan'
                        });
                    }
                });
            });
        });
    })();
</script>
