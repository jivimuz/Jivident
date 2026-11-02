<div id="biayaTambahanTab" class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom pb-3 pt-4 d-flex justify-content-between align-items-start">
        <div>
            <h5 class="card-title fw-bold text-primary mb-0">
                <i class="iconoir-coins me-2"></i>Biaya Tambahan
            </h5>
            <small class="text-muted">Tambahkan biaya di luar tindakan/diagnosa/lab/radiologi/resep langsung ke
                tagihan kunjungan ini — misalnya biaya operasi.</small>
        </div>
        @if (!$isLunas)
            <button type="button" class="btn btn-primary btn-sm" id="btnBukaBiayaTambahan">
                <i class="iconoir-plus-circle me-1"></i> Tambah Biaya
            </button>
        @endif
    </div>
    <div class="card-body p-4">
        @if ($isLunas)
            <div class="alert alert-secondary mb-3">
                Tagihan kunjungan ini sudah <strong>Lunas</strong> & ditutup — tidak bisa menambah atau menghapus
                biaya lagi.
            </div>
        @endif

        @if ($biayaList->isEmpty())
            <p class="text-muted text-center py-3 mb-0">Belum ada biaya tambahan untuk kunjungan ini.</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Nama Biaya</th>
                            <th>Jumlah</th>
                            <th>Keterangan</th>
                            <th>-</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($biayaList as $biaya)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($biaya->created_at)->translatedFormat('d M Y, H:i') }}</td>
                                <td>{{ $biaya->nama }}</td>
                                <td>{{ rupiah($biaya->jumlah) }}</td>
                                <td>{{ $biaya->keterangan ?? '-' }}</td>
                                <td>
                                    @if (!$isLunas)
                                        <button type="button" class="btn btn-xs btn-outline-danger rounded-pill btnHapusBiayaTambahan"
                                            data-id="{{ $biaya->id }}">
                                            <i class="iconoir-trash"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- Modal Tambah Biaya Tambahan -->
<div class="modal fade bt-modal" id="modalBiayaTambahan" data-bt-modal="true" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Biaya Tambahan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama Biaya</label>
                    <input type="text" class="form-control" id="btNama" maxlength="255"
                        placeholder="Contoh: Biaya Operasi" list="btPresetPemeriksaan">
                    <datalist id="btPresetPemeriksaan">
                        <option value="Biaya Operasi">
                        <option value="Biaya Administrasi">
                        <option value="Biaya Alat Medis Habis Pakai">
                        <option value="Biaya Konsultasi Tambahan">
                    </datalist>
                </div>
                <div class="mb-3">
                    <label class="form-label">Jumlah (Rp)</label>
                    <input type="number" class="form-control" id="btJumlah" min="1" step="1">
                </div>
                <div class="mb-0">
                    <label class="form-label">Keterangan (opsional)</label>
                    <textarea class="form-control" id="btKeterangan" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnKonfirmasiBiayaTambahan">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        // Sama persis dengan pola mount-modal tab Operasi/Rawat Inap — partial
        // ini diganti lewat `#tab-bawah.html(...)` tiap kali tab dibuka, jadi
        // modal harus dipindah ke <body> dan instance lama dibuang supaya
        // tidak menumpuk/gagal tampil tiap kali tab ini dibuka ulang.
        function btMountModal() {
            var modalBaru = document.querySelector('#tab-bawah .modal[data-bt-modal="true"]');

            document.querySelectorAll('body > .modal[data-bt-modal="true"]').forEach(function(modal) {
                if (modal === modalBaru) return;
                if (window.bootstrap?.Modal) window.bootstrap.Modal.getInstance(modal)?.dispose();
                modal.remove();
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open').css('padding-right', '');
            });

            if (modalBaru) document.body.appendChild(modalBaru);
        }

        btMountModal();

        function btShowModal(id) {
            var element = document.getElementById(id);
            if (!element) return;
            if (window.bootstrap?.Modal) {
                window.bootstrap.Modal.getOrCreateInstance(element).show();
                return;
            }
            if ($.fn.modal) $(element).modal('show');
        }

        function btHideModal(id) {
            var element = document.getElementById(id);
            if (!element) return;
            if (window.bootstrap?.Modal) {
                window.bootstrap.Modal.getOrCreateInstance(element).hide();
                return;
            }
            if ($.fn.modal) $(element).modal('hide');
        }

        $('#btnBukaBiayaTambahan').on('click', function() {
            $('#btNama,#btJumlah,#btKeterangan').val('');
            btShowModal('modalBiayaTambahan');
        });

        $('#btnKonfirmasiBiayaTambahan').on('click', function() {
            var payload = {
                nama: $('#btNama').val(),
                jumlah: $('#btJumlah').val(),
                keterangan: $('#btKeterangan').val(),
            };
            if (!payload.nama || !payload.jumlah) {
                return Toast.fire({
                    icon: 'warning',
                    title: 'Isi nama biaya dan jumlahnya dulu'
                });
            }

            $.ajax({
                url: "{{ url('pemeriksaan/pasien/' . $id . '/biayatambahan/save') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: payload,
                success: function() {
                    Toast.fire({
                        icon: 'success',
                        title: 'Biaya berhasil ditambahkan ke tagihan'
                    });
                    btHideModal('modalBiayaTambahan');
                    $('#biayatambahan').trigger('click');
                },
                error: function(xhr) {
                    Toast.fire({
                        icon: 'error',
                        title: xhr.responseJSON?.error ?? 'Gagal menyimpan biaya'
                    });
                }
            });
        });

        $('.btnHapusBiayaTambahan').on('click', function() {
            var biayaId = $(this).data('id');
            Swal.fire({
                title: 'Hapus biaya ini dari tagihan?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then(function(result) {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: "{{ url('pemeriksaan/pasien/' . $id . '/biayatambahan/delete') }}/" + biayaId,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function() {
                        Toast.fire({
                            icon: 'success',
                            title: 'Biaya dihapus'
                        });
                        $('#biayatambahan').trigger('click');
                    },
                    error: function(xhr) {
                        Toast.fire({
                            icon: 'error',
                            title: xhr.responseJSON?.error ?? 'Gagal menghapus biaya'
                        });
                    }
                });
            });
        });
    })();
</script>
