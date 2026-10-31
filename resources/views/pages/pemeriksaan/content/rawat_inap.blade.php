<style>
    #rawatInapTab .bed-grid,
    .rwi-modal .bed-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    #rawatInapTab .bed-slot,
    .rwi-modal .bed-slot {
        width: 46px;
        height: 46px;
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: 600;
        border: 2px solid transparent;
        padding: 0;
    }

    #rawatInapTab .bed-slot i,
    .rwi-modal .bed-slot i {
        font-size: 15px;
    }

    #rawatInapTab .bed-kosong,
    .rwi-modal .bed-kosong {
        background: #e8f8ee;
        color: #1f9d55;
        border-color: #bfe8cf;
        cursor: pointer;
    }

    #rawatInapTab .bed-terisi,
    .rwi-modal .bed-terisi {
        background: #fdeceb;
        color: #d9453d;
        border-color: #f6c9c6;
        opacity: .5;
        cursor: not-allowed;
    }

    #rawatInapTab .bed-slot.bed-selected,
    .rwi-modal .bed-slot.bed-selected {
        background: #3a57e8;
        color: #fff;
        border-color: #3a57e8;
    }

    #rawatInapTab .lantai-filter .btn,
    .rwi-modal .lantai-filter .btn {
        margin-right: 4px;
        margin-bottom: 4px;
    }

    .rwi-modal .modal-body {
        min-height: 260px;
    }

    .rwi-modal .select2-container {
        width: 100% !important;
    }

    .rwi-modal .rwi-room-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .rwi-modal .rwi-room-card {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 10px;
    }

    @media (max-width: 575.98px) {
        .rwi-modal .rwi-room-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div id="rawatInapTab" class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom pb-3 pt-4">
        <h5 class="card-title fw-bold text-primary mb-0">
            <i class="iconoir-bed me-2"></i>Rawat Inap
        </h5>
        <small class="text-muted">Admisi &amp; pindah ruangan dari sini — manajemen kamar/ruangan tetap di halaman
            Rawat Inap terpisah.</small>
    </div>
    <div class="card-body p-4">
        @if ($admission && $admission->status === 'dirawat')
            <input type="hidden" id="rwiAdmissionId" value="{{ $admission->id }}">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <span class="badge bg-primary mb-2">Sedang Dirawat</span>
                    <h6 class="mb-0" id="rwiKamarNama">{{ $admission->kamar->nama ?? '-' }}</h6>
                    <small class="text-muted">
                        {{ $admission->kamar?->ruangan?->kelas }}
                        {{ $admission->kamar?->ruangan?->lantai ? '· Lantai ' . $admission->kamar->ruangan->lantai : '' }}
                    </small>
                    <div class="text-muted small mt-1">
                        Masuk: {{ \Carbon\Carbon::parse($admission->tgl_masuk)->translatedFormat('d M Y, H:i') }}
                    </div>
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm" id="btnBukaPindahRuangan">
                    <i class="iconoir-arrow-separate me-1"></i> Pindah Ruangan
                </button>
            </div>
        @else
            <div class="text-center py-3 mb-3">
                <p class="text-muted mb-3">
                    @if ($admission)
                        Pasien ini sudah keluar dari rawat inap sebelumnya pada kunjungan ini. Admisi lagi kalau
                        perlu dirawat ulang.
                    @else
                        Pasien ini belum rawat inap pada kunjungan ini.
                    @endif
                </p>
                <button type="button" class="btn btn-primary" id="btnBukaAdmisi">
                    <i class="iconoir-plus-circle me-1"></i> Admisi ke Rawat Inap
                </button>
            </div>
        @endif

        @if ($admission)
            <hr>
            <h6 class="fw-bold mb-2" style="font-size:13px">Riwayat Pindah Ruangan</h6>
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Dari</th>
                        <th>Ke</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pindahLog as $p)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($p->waktu_pindah)->translatedFormat('d M Y, H:i') }}</td>
                            <td>{{ $p->kamarAsal->nama ?? '-' }}</td>
                            <td>{{ $p->kamarTujuan->nama ?? '-' }}</td>
                            <td>{{ $p->catatan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-muted text-center">Belum pernah dipindah ruangan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @endif
    </div>
</div>

<!-- Modal Admisi Rawat Inap -->
<div class="modal fade rwi-modal" id="modalAdmisiRawatInap" data-rwi-modal="true" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Admisi ke Rawat Inap</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="rwiKamarAdmisi">
                <label class="form-label">Pilih Ranjang</label>
                <div class="lantai-filter mb-2" id="rwiAdmisiLantaiFilter"></div>
                <div id="rwiAdmisiGrid" class="mb-2">Memuat ranjang...</div>
                <div id="rwiAdmisiPager" class="mb-3"></div>
                <div class="mb-3">
                    <label class="form-label">Dokter Penanggung Jawab</label>
                    <select class="form-select" id="rwiAdmisiDokter" style="width:100%"></select>
                </div>
                <div class="mb-0">
                    <label class="form-label">Catatan Masuk (opsional)</label>
                    <textarea class="form-control" id="rwiAdmisiCatatan" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnKonfirmasiAdmisi" disabled>Admisi</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pindah Ruangan -->
<div class="modal fade rwi-modal" id="modalPindahRuangan" data-rwi-modal="true" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pindah Ruangan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="rwiKamarTujuan">
                <div class="lantai-filter mb-2" id="rwiPindahLantaiFilter"></div>
                <div id="rwiPindahGrid" class="mb-2">Memuat ranjang...</div>
                <div id="rwiPindahPager" class="mb-3"></div>
                <div class="mb-0">
                    <label class="form-label">Catatan (opsional)</label>
                    <textarea class="form-control" id="rwiPindahCatatan" rows="2"
                        placeholder="Contoh: permintaan pasien pindah ke VIP"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnKonfirmasiPindah" disabled>Pindahkan</button>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        var currentKamarId = {{ $admission && $admission->status === 'dirawat' ? $admission->fk_kamar : 'null' }};

        // Partial tab ini diganti dengan `#tab-bawah.html(...)`. Modal yang
        // ditinggal di dalam card dapat tertutup/terpotong oleh parent dan
        // instance lama akan menumpuk setiap tab dibuka. Bersihkan instance
        // lama lalu pindahkan dua modal yang baru ke body, posisi yang memang
        // direkomendasikan Bootstrap untuk komponen fixed/backdrop.
        function rwiMountModals() {
            var modalsBaru = Array.from(document.querySelectorAll('#tab-bawah .modal[data-rwi-modal="true"]'));
            var keep = new Set(modalsBaru);
            var removedModalLama = false;

            document.querySelectorAll('body > .modal[data-rwi-modal="true"]').forEach(function(modal) {
                if (keep.has(modal)) return;
                removedModalLama = true;
                if (window.bootstrap?.Modal) window.bootstrap.Modal.getInstance(modal)?.dispose();
                $(modal).find('select').each(function() {
                    if ($.fn.select2 && $(this).hasClass('select2-hidden-accessible')) $(this).select2('destroy');
                });
                modal.remove();
            });

            modalsBaru.forEach(function(modal) {
                document.body.appendChild(modal);
            });
            if (removedModalLama) {
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open').css('padding-right', '');
            }
        }

        rwiMountModals();

        // Bootstrap yang dipakai aplikasi adalah v5. API jQuery `$('#id').modal()`
        // tidak selalu tersedia, terutama setelah halaman ditukar oleh Turbo.
        // Pakai API Bootstrap v5 langsung supaya modal tetap dapat dibuka dari
        // partial yang dimuat melalui AJAX.
        function rwiShowModal(id) {
            var element = document.getElementById(id);
            if (!element) return;
            if (window.bootstrap?.Modal) {
                window.bootstrap.Modal.getOrCreateInstance(element).show();
                return;
            }
            if ($.fn.modal) $(element).modal('show');
        }

        function rwiHideModal(id) {
            var element = document.getElementById(id);
            if (!element) return;
            if (window.bootstrap?.Modal) {
                window.bootstrap.Modal.getOrCreateInstance(element).hide();
                return;
            }
            if ($.fn.modal) $(element).modal('hide');
        }

        // Satu fungsi grid generik dipakai dua modal (admisi & pindah) — beda
        // cuma di id input hidden tujuan, selector grid, dan apakah ranjang
        // yang sedang ditempati pasien ini sendiri perlu ditandai (cuma
        // relevan untuk pindah, bukan admisi baru).
        function rwiBedSquare(bed, targetInputId, gridSelector, highlightCurrent) {
            var kosong = bed.status === 'kosong';
            var cls = kosong ? 'bed-kosong' : 'bed-terisi';
            var isCurrent = highlightCurrent && Number(bed.id) === Number(currentKamarId);
            var title = kosong ? ('Ranjang ' + bed.nomor_ranjang + ' - Kosong') :
                (isCurrent ? 'Ranjang saat ini' : ('Terisi: ' + (bed.pasien_nama ?? '-')));
            return `<button type="button" class="bed-slot ${cls}" data-bed-id="${bed.id}" title="${title}" ${kosong ? '' : 'disabled'}>
                <i class="iconoir-bed"></i><span>${bed.nomor_ranjang ?? ''}</span>
            </button>`;
        }

        function rwiRenderLantaiFilter(filterContainerId, ruanganCache, lantaiAktif, onPilih) {
            var lantaiList = [...new Set(ruanganCache.map(r => r.lantai).filter(Boolean))];
            if (lantaiList.length < 2) {
                $('#' + filterContainerId).html('');
                return;
            }
            var html = `<button type="button" class="btn btn-sm ${lantaiAktif === null ? 'btn-primary' : 'btn-outline-secondary'}" onclick="${onPilih}(null)">Semua Lantai</button>`;
            lantaiList.sort().forEach(function(l) {
                html +=
                    `<button type="button" class="btn btn-sm ${lantaiAktif === l ? 'btn-primary' : 'btn-outline-secondary'}" onclick="${onPilih}('${l}')">Lantai ${l}</button>`;
            });
            $('#' + filterContainerId).html(html);
        }

        var RWI_ROOMS_PER_PAGE = 6;

        // Dipaging per-ruangan (bukan per-ranjang) — kalau ruangannya banyak,
        // menampilkan semua kartu ruangan sekaligus bikin modal jadi sangat
        // panjang untuk di-scroll.
        function rwiRenderPager(pagerSelector, page, totalPages, onGotoPage) {
            if (totalPages <= 1) {
                $(pagerSelector).html('');
                return;
            }
            var html = `<div class="d-flex justify-content-center align-items-center gap-2">
                <button type="button" class="btn btn-xs btn-outline-secondary" data-rwi-page="${page - 1}" ${page <= 1 ? 'disabled' : ''}><i class="iconoir-nav-arrow-left"></i></button>
                <span class="small text-muted">Halaman ${page} / ${totalPages}</span>
                <button type="button" class="btn btn-xs btn-outline-secondary" data-rwi-page="${page + 1}" ${page >= totalPages ? 'disabled' : ''}><i class="iconoir-nav-arrow-right"></i></button>
            </div>`;
            $(pagerSelector).html(html);
            $(pagerSelector).off('click.rwiPager', '[data-rwi-page]').on('click.rwiPager', '[data-rwi-page]',
                function() {
                    onGotoPage(Number($(this).data('rwi-page')));
                });
        }

        function rwiRenderGrid(gridSelector, pagerSelector, ruanganCache, lantaiAktif, page, targetInputId,
            highlightCurrent, onGotoPage) {
            var ruanganTampil = ruanganCache.filter(r => lantaiAktif === null || r.lantai === lantaiAktif);
            if (!ruanganTampil.length) {
                $(gridSelector).html('<p class="text-muted mb-0">Tidak ada ruangan untuk lantai ini.</p>');
                $(pagerSelector).html('');
                return;
            }
            var totalPages = Math.ceil(ruanganTampil.length / RWI_ROOMS_PER_PAGE);
            if (page > totalPages) page = totalPages;
            if (page < 1) page = 1;
            var ruanganHalamanIni = ruanganTampil.slice((page - 1) * RWI_ROOMS_PER_PAGE, page *
                RWI_ROOMS_PER_PAGE);
            var html = '<div class="rwi-room-grid">';
            ruanganHalamanIni.forEach(function(r) {
                html += `<div class="rwi-room-card">
                    <div class="small fw-bold mb-1">${r.nama}${r.kelas ? ' (' + r.kelas + ')' : ''}</div>
                    <div class="bed-grid">${r.beds.map(b => rwiBedSquare(b, targetInputId, gridSelector, highlightCurrent)).join('')}</div>
                </div>`;
            });
            html += '</div>';
            var $grid = $(gridSelector);
            $grid.html(html);
            // Jangan memakai inline onclick: partial ini dimuat ulang lewat
            // AJAX/Turbo sehingga fungsi global bisa hilang atau tertimpa.
            // Handler dipasang ulang setiap grid dirender dan membawa state
            // modal yang benar melalui closure.
            $grid.off('click.rwiBed', '.bed-kosong[data-bed-id]').on('click.rwiBed',
                '.bed-kosong[data-bed-id]', function() {
                    rwiPilihBed(Number($(this).data('bed-id')), targetInputId, gridSelector);
                });
            rwiRenderPager(pagerSelector, page, totalPages, onGotoPage);
        }

        function rwiPilihBed(bedId, targetInputId, gridSelector) {
            $('#' + targetInputId).val(bedId);
            $(gridSelector + ' .bed-slot').removeClass('bed-selected');
            $(gridSelector + ' .bed-slot[data-bed-id="' + bedId + '"]').addClass('bed-selected');
            $(targetInputId === 'rwiKamarAdmisi' ? '#btnKonfirmasiAdmisi' : '#btnKonfirmasiPindah')
                .prop('disabled', false);
        }

        function loadRuanganUntuk(callback) {
            $.ajax({
                url: "{{ url('pemeriksaan/pasien/' . $id . '/rawatinap/ruanganList') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: callback,
                error: function(xhr) {
                    Toast.fire({
                        icon: 'error',
                        title: xhr.responseJSON?.error ?? 'Gagal memuat data ruangan'
                    });
                }
            });
        }

        // ------- Pindah Ruangan -------
        var rwiPindahCache = [];
        var rwiPindahLantaiAktif = null;
        var rwiPindahPage = 1;

        window.rwiFilterLantaiPindah = function(lantai) {
            rwiPindahLantaiAktif = lantai;
            rwiPindahPage = 1;
            rwiRenderLantaiFilter('rwiPindahLantaiFilter', rwiPindahCache, rwiPindahLantaiAktif,
                'window.rwiFilterLantaiPindah');
            rwiRenderGrid('#rwiPindahGrid', '#rwiPindahPager', rwiPindahCache, rwiPindahLantaiAktif, rwiPindahPage,
                'rwiKamarTujuan', true, window.rwiGotoPindahPage);
        };

        window.rwiGotoPindahPage = function(p) {
            rwiPindahPage = p;
            rwiRenderGrid('#rwiPindahGrid', '#rwiPindahPager', rwiPindahCache, rwiPindahLantaiAktif, rwiPindahPage,
                'rwiKamarTujuan', true, window.rwiGotoPindahPage);
        };

        $('#btnBukaPindahRuangan').on('click', function() {
            $('#rwiKamarTujuan').val('');
            $('#rwiPindahCatatan').val('');
            $('#btnKonfirmasiPindah').prop('disabled', true);
            rwiPindahLantaiAktif = null;
            rwiPindahPage = 1;
            loadRuanganUntuk(function(res) {
                rwiPindahCache = res.data;
                rwiRenderLantaiFilter('rwiPindahLantaiFilter', rwiPindahCache, rwiPindahLantaiAktif,
                    'window.rwiFilterLantaiPindah');
                rwiRenderGrid('#rwiPindahGrid', '#rwiPindahPager', rwiPindahCache, rwiPindahLantaiAktif,
                    rwiPindahPage, 'rwiKamarTujuan', true, window.rwiGotoPindahPage);
                rwiShowModal('modalPindahRuangan');
            });
        });

        $('#btnKonfirmasiPindah').on('click', function() {
            var kamarTujuan = $('#rwiKamarTujuan').val();
            if (!kamarTujuan) return;
            $.ajax({
                url: "{{ url('pemeriksaan/pasien/' . $id . '/rawatinap/pindah') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    fk_kamar_tujuan: kamarTujuan,
                    catatan: $('#rwiPindahCatatan').val(),
                },
                success: function() {
                    Toast.fire({
                        icon: 'success',
                        title: 'Pasien dipindahkan ruangan'
                    });
                    rwiHideModal('modalPindahRuangan');
                    $('#' + 'rawatinap').trigger('click');
                },
                error: function(xhr) {
                    Toast.fire({
                        icon: 'error',
                        title: xhr.responseJSON?.error ?? 'Gagal memindahkan ruangan'
                    });
                }
            });
        });

        // ------- Admisi -------
        var rwiAdmisiCache = [];
        var rwiAdmisiLantaiAktif = null;
        var rwiAdmisiPage = 1;

        window.rwiFilterLantaiAdmisi = function(lantai) {
            rwiAdmisiLantaiAktif = lantai;
            rwiAdmisiPage = 1;
            rwiRenderLantaiFilter('rwiAdmisiLantaiFilter', rwiAdmisiCache, rwiAdmisiLantaiAktif,
                'window.rwiFilterLantaiAdmisi');
            rwiRenderGrid('#rwiAdmisiGrid', '#rwiAdmisiPager', rwiAdmisiCache, rwiAdmisiLantaiAktif, rwiAdmisiPage,
                'rwiKamarAdmisi', false, window.rwiGotoAdmisiPage);
        };

        window.rwiGotoAdmisiPage = function(p) {
            rwiAdmisiPage = p;
            rwiRenderGrid('#rwiAdmisiGrid', '#rwiAdmisiPager', rwiAdmisiCache, rwiAdmisiLantaiAktif, rwiAdmisiPage,
                'rwiKamarAdmisi', false, window.rwiGotoAdmisiPage);
        };

        $('#rwiAdmisiDokter').select2({
            ajax: {
                url: "{{ url('pemeriksaan/pasien/' . $id . '/rawatinap/dokterList') }}",
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
            placeholder: 'Cari dokter (opsional)',
            allowClear: true,
            dropdownParent: $('#modalAdmisiRawatInap')
        });

        $('#btnBukaAdmisi').on('click', function() {
            $('#rwiKamarAdmisi').val('');
            $('#rwiAdmisiCatatan').val('');
            $('#rwiAdmisiDokter').val(null).trigger('change');
            $('#btnKonfirmasiAdmisi').prop('disabled', true);
            rwiAdmisiLantaiAktif = null;
            rwiAdmisiPage = 1;
            loadRuanganUntuk(function(res) {
                rwiAdmisiCache = res.data;
                rwiRenderLantaiFilter('rwiAdmisiLantaiFilter', rwiAdmisiCache, rwiAdmisiLantaiAktif,
                    'window.rwiFilterLantaiAdmisi');
                rwiRenderGrid('#rwiAdmisiGrid', '#rwiAdmisiPager', rwiAdmisiCache, rwiAdmisiLantaiAktif,
                    rwiAdmisiPage, 'rwiKamarAdmisi', false, window.rwiGotoAdmisiPage);
                rwiShowModal('modalAdmisiRawatInap');
            });
        });

        $('#btnKonfirmasiAdmisi').on('click', function() {
            var kamar = $('#rwiKamarAdmisi').val();
            if (!kamar) return;
            $.ajax({
                url: "{{ url('pemeriksaan/pasien/' . $id . '/rawatinap/admit') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    fk_kamar: kamar,
                    fk_dokter_penanggungjawab: $('#rwiAdmisiDokter').val(),
                    catatan_masuk: $('#rwiAdmisiCatatan').val(),
                },
                success: function() {
                    Toast.fire({
                        icon: 'success',
                        title: 'Pasien diadmisi ke rawat inap'
                    });
                    rwiHideModal('modalAdmisiRawatInap');
                    $('#' + 'rawatinap').trigger('click');
                },
                error: function(xhr) {
                    Toast.fire({
                        icon: 'error',
                        title: xhr.responseJSON?.error ?? 'Gagal mengadmisi pasien'
                    });
                }
            });
        });
    })();
</script>
