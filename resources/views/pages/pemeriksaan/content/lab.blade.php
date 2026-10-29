<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom pb-3 pt-4">
        <h5 class="card-title fw-bold text-primary mb-0">
            <i class="iconoir-test-tube me-2"></i>Permintaan Pemeriksaan Lab
        </h5>
        <small class="text-muted">Hasil diinput oleh petugas lab di halaman Laboratorium, bukan di sini.</small>
    </div>
    <div class="card-body p-4">
        <div class="row align-items-end mb-3">
            <div class="col-md-8">
                <label class="fw-bold mb-1" style="font-size: 13px;">Jenis Pemeriksaan</label>
                <select class="form-select" id="labJenis">
                    @foreach ($jenisOptions as $j)
                        <option value="{{ $j->id }}">{{ $j->nama }} — Rp {{ number_format($j->harga, 0, ',', '.') }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <button type="button" class="btn btn-primary w-100" id="btnMintaLab">
                    <i class="iconoir-plus-circle me-1"></i> Minta Pemeriksaan
                </button>
            </div>
        </div>

        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Tanggal Diminta</th>
                    <th>Pemeriksaan</th>
                    <th>Hasil</th>
                    <th>Nilai Rujukan</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="labOrderBody">
                @forelse ($orders as $o)
                    <tr>
                        <td>{{ $o->tgl_diminta }}</td>
                        <td>{{ $o->jenis_nama }}</td>
                        <td>
                            {{ $o->hasil ?? '-' }} {{ $o->satuan_snapshot }}
                            @if ($o->hasil_abnormal)
                                <span class="badge bg-danger">Abnormal</span>
                            @endif
                        </td>
                        <td>{{ $o->nilai_rujukan_snapshot ?? '-' }}</td>
                        <td>
                            <span class="badge {{ $o->status === 'selesai' ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ ucfirst($o->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-muted text-center">Belum ada permintaan lab untuk kunjungan ini</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    $('#btnMintaLab').on('click', function() {
        var jenisId = $('#labJenis').val();
        if (!jenisId) {
            return Toast.fire({
                icon: 'warning',
                title: 'Pilih jenis pemeriksaan dulu'
            });
        }
        $.ajax({
            url: "{{ url('pemeriksaan/pasien/' . $id . '/lab/save') }}",
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {
                fk_jenispemeriksaan: jenisId
            },
            success: function() {
                Toast.fire({
                    icon: 'success',
                    title: 'Permintaan lab terkirim'
                });
                $('#' + 'lab').trigger('click');
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
