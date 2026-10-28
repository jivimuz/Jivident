@extends('public/portal/layout')

@section('content')
    <div class="portal-card">
        <h3>Halo, {{ $patient->nama }}</h3>
        <div class="portal-row">
            <span class="portal-muted">No. RM</span>
            <span>{{ $patient->norm }}</span>
        </div>
        <div class="portal-row">
            <span class="portal-muted">Tanggal Lahir</span>
            <span>{{ $patient->tgllahir ? \Carbon\Carbon::parse($patient->tgllahir)->translatedFormat('d F Y') : '-' }}</span>
        </div>
        <div class="portal-row">
            <span class="portal-muted">No. HP</span>
            <span>{{ $patient->nohp ?? '-' }}</span>
        </div>
    </div>

    <div class="portal-card">
        <h3>Menu</h3>
        <p class="portal-muted">Gunakan menu di atas untuk melihat riwayat kunjungan, resep obat, tagihan, atau
            membuat booking jadwal dokter baru.</p>
    </div>
@endsection
