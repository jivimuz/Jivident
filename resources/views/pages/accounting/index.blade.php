@extends('layout/main')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Akuntansi</h4>
            <p class="text-muted mb-0">Chart of accounts dan jurnal umum cabang.</p>
        </div>
    </div>
    <div class="alert alert-info">Gunakan endpoint jurnal untuk mencatat transaksi berpasangan. Total debit dan kredit wajib seimbang.</div>
    <div class="card">
        <div class="card-header"><h5 class="mb-0">Chart of Accounts</h5></div>
        <div class="card-body">
            <table class="table" id="accountTable">
                <thead><tr><th>Kode</th><th>Nama</th><th>Tipe</th><th>Status</th></tr></thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(function () {
    $.post('{{ url('accounting/accounts') }}', {_token: '{{ csrf_token() }}'}, function (response) {
        var rows = response.data.map(function (account) {
            return '<tr><td>' + account.code + '</td><td>' + account.name + '</td><td>' + account.type + '</td><td>' + (account.is_active ? 'Aktif' : 'Nonaktif') + '</td></tr>';
        });
        $('#accountTable tbody').html(rows.join(''));
    });
});
</script>
@endsection
