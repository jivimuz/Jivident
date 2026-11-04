@extends('layout/main')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <h4 class="mb-1">Mutu & Insiden</h4>
        <p class="text-muted mb-0">Pelaporan insiden, indikator mutu, dan antrean integrasi nasional.</p>
    </div>
    <div class="alert alert-info">SATUSEHAT, SISRUTE, dan RL masih berupa boundary antrean ter-audit. Pengiriman aktif membutuhkan credential dan mapping resmi.</div>
    <div class="row g-3">
        <div class="col-md-4"><div class="card"><div class="card-body"><h5>Insiden Keselamatan</h5><p class="text-muted mb-0">Near miss sampai kejadian sentinel.</p></div></div></div>
        <div class="col-md-4"><div class="card"><div class="card-body"><h5>Indikator Mutu</h5><p class="text-muted mb-0">Nilai, target, periode, dan catatan.</p></div></div></div>
        <div class="col-md-4"><div class="card"><div class="card-body"><h5>Integrasi Nasional</h5><p class="text-muted mb-0">Queue dan response log untuk adapter resmi.</p></div></div></div>
    </div>
</div>
@endsection
