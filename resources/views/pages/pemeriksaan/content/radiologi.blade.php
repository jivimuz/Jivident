<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom pb-3 pt-4">
        <h5 class="card-title fw-bold text-primary mb-0">
            <i class="iconoir-xray-view me-2"></i>Permintaan Pemeriksaan Radiologi
        </h5>
        <small class="text-muted">Penjadwalan dan hasil diproses di halaman Radiologi, bukan di sini.</small>
    </div>
    <div class="card-body p-4">
        <div class="mb-3">
            <label class="fw-bold mb-1" style="font-size: 13px;">Jenis Pemeriksaan</label>
            <select class="form-select" id="radJenis">
                @foreach ($jenisOptions as $j)
                    <option value="{{ $j->id }}">{{ $j->nama }} — Rp {{ number_format($j->harga, 0, ',', '.') }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="fw-bold mb-1" style="font-size: 13px;">Catatan Klinis / Alasan Rujukan <span
                    class="text-danger">*</span></label>
            <textarea class="form-control" id="radCatatan" rows="2" placeholder="Contoh: Nyeri gigi geraham bawah, curiga impaksi"></textarea>
        </div>
        <button type="button" class="btn btn-primary" id="btnMintaRad">
            <i class="iconoir-plus-circle me-1"></i> Minta Pemeriksaan
        </button>

        <table class="table table-sm mt-4">
            <thead>
                <tr>
                    <th>Tanggal Diminta</th>
                    <th>Pemeriksaan</th>
                    <th>Status</th>
                    <th>Interpretasi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $o)
                    <tr>
                        <td>{{ $o->tgl_diminta }}</td>
                        <td>{{ $o->jenis_nama }}</td>
                        <td>
                            <span class="badge {{ $o->status === 'selesai' ? 'bg-success' : ($o->status === 'terjadwal' ? 'bg-info' : 'bg-warning text-dark') }}">
                                {{ ucfirst($o->status) }}
                            </span>
                        </td>
                        <td>{{ $o->hasil_interpretasi ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-muted text-center">Belum ada permintaan radiologi untuk kunjungan
                            ini</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    $('#btnMintaRad').on('click', function() {
        var jenisId = $('#radJenis').val();
        var catatan = $('#radCatatan').val();
        if (!jenisId || !catatan) {
            return Toast.fire({
                icon: 'warning',
                title: 'Pilih jenis pemeriksaan dan isi catatan klinis'
            });
        }
        $.ajax({
            url: "{{ url('pemeriksaan/pasien/' . $id . '/radiologi/save') }}",
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {
                fk_jenispemeriksaan: jenisId,
                catatan_klinis: catatan
            },
            success: function() {
                Toast.fire({
                    icon: 'success',
                    title: 'Permintaan radiologi terkirim'
                });
                $('#radiologi').trigger('click');
            },
            error: function(xhr) {
                Toast.fire({
                    icon: 'error',
                    title: JSON.parse(xhr.responseText).error ?? 'Gagal mengirim permintaan'
                });
            }
        });
    });
</script>
