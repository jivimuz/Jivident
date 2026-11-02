@extends('layout/main')

@section('content')
    <div class="content-inner mt-5 py-0">
        <div class="row">
            <div class="col-md-12 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Verifikasi &amp; Serahkan Resep</h4>
                        <small class="text-muted">Resep yang sudah ditulis dokter di kunjungan pasien, menunggu
                            diverifikasi dan diserahkan oleh farmasi. Menyerahkan resep akan otomatis memotong stok
                            obat terkait.</small>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm" id="worklistTable">
                            <thead>
                                <tr>
                                    <th>Tgl Registrasi</th>
                                    <th>No. RM</th>
                                    <th>Pasien</th>
                                    <th>Jumlah R/</th>
                                    <th>Total Harga</th>
                                    <th>-</th>
                                </tr>
                            </thead>
                            <tbody id="worklistBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDetailResep">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Detail Resep</h5>
                    <a onclick="closeModal('modalDetailResep')" class="btn text-lg">x</a>
                </div>
                <div class="modal-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>R/</th>
                                <th>Obat / Racikan</th>
                                <th>Qty</th>
                                <th>Stok Sistem</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="detailResepBody"></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" onclick="closeModal('modalDetailResep')">Batal</button>
                    <button class="btn btn-success" id="btnKonfirmasiDispense">
                        <i class="iconoir-check-circle"></i> Verifikasi &amp; Serahkan
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            var activeDaftarPasienId = null;

            function loadWorklist() {
                $.ajax({
                    url: "{{ url('verifikasi-resep/getWorklist') }}",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        var rows = '';
                        res.data.forEach(function(d) {
                            rows += `<tr>
                                <td>${new Date(d.tglregistrasi).toLocaleDateString('id-ID')}</td>
                                <td>${d.pasien_norm}</td>
                                <td>${d.pasien_nama}</td>
                                <td>${d.jumlah_item}</td>
                                <td>${formatRupiah(d.total_harga)}</td>
                                <td>
                                    <button type="button" class="btn btn-xs btn-outline-primary rounded-pill" onclick="openDetailResep(${d.daftarpasien_id})">Verifikasi</button>
                                </td>
                            </tr>`;
                        });
                        $('#worklistBody').html(rows ||
                            '<tr><td colspan="6" class="text-muted text-center">Tidak ada resep yang menunggu diverifikasi</td></tr>'
                        );
                    }
                });
            }

            window.openDetailResep = function(daftarPasienId) {
                activeDaftarPasienId = daftarPasienId;

                $.ajax({
                    url: "{{ url('verifikasi-resep/detail') }}/" + daftarPasienId,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        var rows = '';
                        res.data.forEach(function(item) {
                            if (item.type === 'racik') {
                                rows += `<tr>
                                    <td>R/${item.r_num}</td>
                                    <td><strong>Racikan: ${item.nama}</strong> (${item.qty} ${item.satuan ?? ''})</td>
                                    <td colspan="2"></td>
                                    <td class="text-end">${formatRupiah(item.subtotal)}</td>
                                </tr>`;
                                item.ingredients.forEach(function(ing) {
                                    var low = ing.qty > ing.stok_sistem;
                                    rows += `<tr>
                                        <td></td>
                                        <td class="ps-4">&mdash; ${ing.product_name}</td>
                                        <td>${ing.qty}</td>
                                        <td class="${low ? 'text-danger fw-bold' : ''}">${ing.stok_sistem}</td>
                                        <td></td>
                                    </tr>`;
                                });
                            } else {
                                var low = item.stok_sistem !== null && item.qty > item.stok_sistem;
                                rows += `<tr>
                                    <td>R/${item.r_num}</td>
                                    <td>${item.nama}</td>
                                    <td>${item.qty} ${item.satuan ?? ''}</td>
                                    <td class="${low ? 'text-danger fw-bold' : ''}">${item.stok_sistem ?? '-'}</td>
                                    <td class="text-end">${formatRupiah(item.subtotal)}</td>
                                </tr>`;
                            }
                        });
                        $('#detailResepBody').html(rows ||
                            '<tr><td colspan="5" class="text-muted text-center">Tidak ada resep</td></tr>'
                        );
                        showModal('modalDetailResep');
                    },
                    error: function(xhr) {
                        Toast.fire({
                            icon: 'error',
                            title: xhr.responseJSON?.error ?? 'Gagal memuat detail resep'
                        });
                    }
                });
            };

            $('#btnKonfirmasiDispense').on('click', function() {
                if (!activeDaftarPasienId) return;

                Swal.fire({
                    title: 'Serahkan resep ini?',
                    text: 'Stok obat terkait akan otomatis dipotong dan resep ini tidak bisa diubah lagi oleh dokter.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Serahkan'
                }).then(function(result) {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: "{{ url('verifikasi-resep/dispense') }}/" + activeDaftarPasienId,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function() {
                            Toast.fire({
                                icon: 'success',
                                title: 'Resep berhasil diverifikasi & diserahkan'
                            });
                            closeModal('modalDetailResep');
                            loadWorklist();
                        },
                        error: function(xhr) {
                            Toast.fire({
                                icon: 'error',
                                title: xhr.responseJSON?.error ?? 'Gagal memproses resep'
                            });
                        }
                    });
                });
            });

            loadWorklist();
        });
    </script>
@endsection
