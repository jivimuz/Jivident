@extends('layout/main')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <h4 class="mb-1">Klaim BPJS</h4>
        <p class="text-muted mb-0">Worklist klaim per kunjungan. Integrasi API BPJS dapat dihubungkan setelah credential tersedia.</p>
    </div>
    <div class="alert alert-info">Status klaim: draft, submitted, verified, pending, disputed, rejected, dan paid.</div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Klaim</h5>
            <button type="button" class="btn btn-primary" id="reloadClaims">Muat ulang</button>
        </div>
        <div class="card-body">
            <table class="table" id="bpjsClaimTable">
                <thead><tr><th>Pasien</th><th>No. SEP</th><th>No. Klaim</th><th>Tanggal</th><th>Nominal</th><th>Status</th></tr></thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(function () {
    function loadClaims() {
        $.post('{{ url('bpjs-claims/getList') }}', {_token: '{{ csrf_token() }}'}, function (response) {
            var rows = response.data.data.map(function (claim) {
                var patient = claim.patient ? claim.patient.nama + ' (' + claim.patient.norm + ')' : '-';
                return '<tr><td>' + patient + '</td><td>' + (claim.sep_no || '-') + '</td><td>' + (claim.claim_no || '-') + '</td><td>' + (claim.service_date || '-') + '</td><td>' + claim.amount + '</td><td>' + claim.status + '</td></tr>';
            });
            $('#bpjsClaimTable tbody').html(rows.join(''));
        });
    }
    $('#reloadClaims').on('click', loadClaims);
    loadClaims();
});
</script>
@endsection
