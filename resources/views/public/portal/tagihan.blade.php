@extends('public/portal/layout')

@section('content')
    <div class="portal-card">
        <h3>Riwayat Tagihan</h3>
        @if ($tagihan->isEmpty())
            <p class="portal-muted">Belum ada riwayat tagihan.</p>
        @endif
    </div>

    @foreach ($tagihan as $row)
        <div class="portal-card">
            <div class="portal-row">
                <strong>{{ \Carbon\Carbon::parse($row->tglregistrasi)->translatedFormat('d F Y') }}</strong>
                <span class="portal-badge {{ $row->statuspembayaran === 'Lunas' ? 'lunas' : 'belum' }}">{{ $row->statuspembayaran }}</span>
            </div>
            <div class="portal-row">
                <span class="portal-muted">Total Tagihan</span>
                <span>Rp {{ number_format($row->total_tagihan, 0, ',', '.') }}</span>
            </div>
            <div class="portal-row">
                <span class="portal-muted">Sudah Dibayar</span>
                <span>Rp {{ number_format($row->total_paid, 0, ',', '.') }}</span>
            </div>
            <div class="portal-row">
                <span class="portal-muted">Sisa</span>
                <span><b>Rp {{ number_format($row->sisa, 0, ',', '.') }}</b></span>
            </div>
        </div>
    @endforeach
@endsection
