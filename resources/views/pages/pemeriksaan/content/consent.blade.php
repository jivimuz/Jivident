<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom pb-3 pt-4">
        <h5 class="card-title fw-bold text-primary mb-0">
            <i class="iconoir-page-edit me-2"></i>Formulir Persetujuan Tindakan (Informed Consent)
        </h5>
        <small class="text-muted">Tanda tangan elektronik sederhana (bukan tersertifikasi) — gambar + timestamp,
            bukan pengganti tanda tangan basah yang tersertifikasi secara hukum.</small>
    </div>
    <div class="card-body p-4">
        <div class="mb-3">
            <label class="fw-bold mb-1" style="font-size: 13px;">Jenis Persetujuan</label>
            <select class="form-select" id="consentTipe">
                <option value="general">Persetujuan Umum Pelayanan (General Consent)</option>
                @foreach ($tindakanOptions as $t)
                    <option value="tindakan" data-tindakan-id="{{ $t->id }}" data-tindakan-nama="{{ $t->name }}">
                        Tindakan: {{ $t->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3 p-3 bg-light rounded border">
            <small class="fw-bold">Teks Persetujuan:</small>
            <p class="mb-0" id="consentTextPreview" style="font-size: 13px;"></p>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="fw-bold mb-1" style="font-size: 13px;">Ditandatangani Oleh</label>
                <input type="text" class="form-control" id="consentDitandatanganiOleh"
                    value="{{ $status->nama ?? '' }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="fw-bold mb-1" style="font-size: 13px;">Hubungan dengan Pasien</label>
                <select class="form-select" id="consentHubungan">
                    <option value="Pasien Sendiri" selected>Pasien Sendiri</option>
                    <option value="Orang Tua">Orang Tua</option>
                    <option value="Wali">Wali</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>
        </div>

        <div class="mb-2">
            <label class="fw-bold mb-1" style="font-size: 13px;">Tanda Tangan</label>
            <canvas id="consentSignatureCanvas" width="500" height="150"
                style="border:1px dashed #999;border-radius:6px;width:100%;touch-action:none;cursor:crosshair;background:#fff"></canvas>
        </div>
        <div class="d-flex justify-content-between mb-3">
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearConsentCanvas()">Bersihkan</button>
            <button type="button" class="btn btn-success rounded-pill px-4 fw-bold" id="btnSimpanConsent">
                <i class="iconoir-check-circle me-1"></i> Simpan Persetujuan
            </button>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom pb-3 pt-4">
        <h5 class="card-title fw-bold mb-0">Riwayat Persetujuan Pasien Ini</h5>
        <small class="text-muted">Mencakup semua kunjungan pasien ini, bukan cuma kunjungan sekarang.</small>
    </div>
    <div class="card-body p-4">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Ditandatangani Oleh</th>
                    <th>Hubungan</th>
                    <th>-</th>
                </tr>
            </thead>
            <tbody id="consentHistoryBody"></tbody>
        </table>
    </div>
</div>

<script>
    var idDaftarPasienConsent = "{{ $id }}";
    var consentCanvas, consentCtx, consentDrawing = false,
        consentHasDrawn = false;

    // Teks di sini HARUS sama persis dengan PemeriksaanController::CONSENT_TEXT_GENERAL
    // dan template tindakan di PemeriksaanController::saveConsent — ini preview sebelum
    // tanda tangan, teks yang benar-benar disimpan dibuat ulang di server.
    const CONSENT_TEXT_GENERAL = 'Saya yang bertanda tangan di bawah ini menyatakan telah menerima penjelasan mengenai prosedur pelayanan kesehatan gigi di klinik ini, termasuk pemeriksaan, tindakan, dan pengobatan yang mungkin diperlukan. Saya menyetujui untuk menerima pelayanan tersebut dan memahami bahwa saya dapat bertanya atau menolak tindakan tertentu kapan saja.';

    function consentTindakanText(nama) {
        return `Saya yang bertanda tangan di bawah ini menyatakan telah menerima penjelasan mengenai tindakan "${nama}" yang akan dilakukan, termasuk tujuan, prosedur, risiko, dan alternatif yang tersedia. Saya menyetujui untuk dilakukan tindakan tersebut.`;
    }

    function updateConsentPreview() {
        var opt = $('#consentTipe option:selected');
        if (opt.val() === 'tindakan') {
            $('#consentTextPreview').text(consentTindakanText(opt.data('tindakan-nama')));
        } else {
            $('#consentTextPreview').text(CONSENT_TEXT_GENERAL);
        }
    }

    function initConsentCanvas() {
        consentCanvas = document.getElementById('consentSignatureCanvas');
        consentCtx = consentCanvas.getContext('2d');
        consentCtx.lineWidth = 2;
        consentCtx.lineCap = 'round';
        consentCtx.strokeStyle = '#000';

        function getPos(e) {
            var rect = consentCanvas.getBoundingClientRect();
            var scaleX = consentCanvas.width / rect.width;
            var scaleY = consentCanvas.height / rect.height;
            return {
                x: (e.clientX - rect.left) * scaleX,
                y: (e.clientY - rect.top) * scaleY
            };
        }
        consentCanvas.onpointerdown = function(e) {
            consentDrawing = true;
            var pos = getPos(e);
            consentCtx.beginPath();
            consentCtx.moveTo(pos.x, pos.y);
        };
        consentCanvas.onpointermove = function(e) {
            if (!consentDrawing) return;
            var pos = getPos(e);
            consentCtx.lineTo(pos.x, pos.y);
            consentCtx.stroke();
            consentHasDrawn = true;
        };
        consentCanvas.onpointerup = function() {
            consentDrawing = false;
        };
        consentCanvas.onpointerleave = function() {
            consentDrawing = false;
        };
    }

    function clearConsentCanvas() {
        consentCtx.clearRect(0, 0, consentCanvas.width, consentCanvas.height);
        consentHasDrawn = false;
    }

    function loadConsentHistory() {
        $.ajax({
            url: "{{ url('pemeriksaan/pasien/' . $id . '/consent/getList') }}",
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(res) {
                var rows = '';
                res.data.forEach(d => rows += `<tr>
                    <td>${d.created_at}</td>
                    <td>${d.tipe === 'tindakan' ? 'Tindakan: ' + d.tindakan_nama : 'Umum'}</td>
                    <td>${d.ditandatangani_oleh}</td>
                    <td>${d.hubungan_dengan_pasien}</td>
                    <td><a class="btn btn-xs btn-outline-primary" href="{{ url('print/print-consent/') }}/${btoa(btoa(d.id))}" target="_blank">Lihat</a></td>
                </tr>`);
                $('#consentHistoryBody').html(rows || '<tr><td colspan="5" class="text-muted text-center">Belum ada persetujuan tercatat</td></tr>');
            }
        });
    }

    $('#consentTipe').on('change', updateConsentPreview);

    $('#btnSimpanConsent').on('click', function() {
        if (!consentHasDrawn) {
            return Toast.fire({
                icon: 'warning',
                title: 'Minta pasien/penanda tangan menggambar tanda tangan dulu'
            });
        }
        var opt = $('#consentTipe option:selected');
        var payload = {
            tipe: opt.val(),
            tindakan_id: opt.data('tindakan-id') || null,
            ditandatangani_oleh: $('#consentDitandatanganiOleh').val(),
            hubungan_dengan_pasien: $('#consentHubungan').val(),
            signature: consentCanvas.toDataURL('image/png'),
        };

        $.ajax({
            url: "{{ url('pemeriksaan/pasien/' . $id . '/consent/save') }}",
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: payload,
            success: function() {
                Toast.fire({
                    icon: 'success',
                    title: 'Persetujuan tersimpan'
                });
                clearConsentCanvas();
                loadConsentHistory();
            },
            error: function(xhr) {
                var msg = 'Gagal menyimpan';
                try {
                    var body = JSON.parse(xhr.responseText);
                    msg = body.error || Object.values(body.errors ?? {}).flat().join(', ') || msg;
                } catch (e) {}
                Toast.fire({
                    icon: 'error',
                    title: msg
                });
            }
        });
    });

    initConsentCanvas();
    updateConsentPreview();
    loadConsentHistory();
</script>
