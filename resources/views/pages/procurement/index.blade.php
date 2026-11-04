@extends('layout/main')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <h4 class="mb-1">Procurement & Warehouse</h4>
        <p class="text-muted mb-0">Permintaan barang, approval, dan purchase order lintas unit.</p>
    </div>
    <div class="alert alert-info">Receive Order tetap menjadi proses penerimaan dan pembaruan stok. Modul ini mengelola permintaan dan purchase order sebelum barang diterima.</div>
    <div class="row g-3">
        <div class="col-md-4"><div class="card"><div class="card-body"><h5>Permintaan Barang</h5><p class="text-muted mb-0">Draft, submit, approval, dan status ordered.</p></div></div></div>
        <div class="col-md-4"><div class="card"><div class="card-body"><h5>Purchase Order</h5><p class="text-muted mb-0">Supplier, nomor PO, dan tanggal pemesanan.</p></div></div></div>
        <div class="col-md-4"><div class="card"><div class="card-body"><h5>Warehouse</h5><p class="text-muted mb-0">Penerimaan tetap terhubung ke Receive Order existing.</p></div></div></div>
    </div>
</div>
@endsection
