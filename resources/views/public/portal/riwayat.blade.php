@extends('public/portal/layout')

@section('content')
    <div class="portal-card">
        <h3>Riwayat Kunjungan</h3>
        @if ($visits->isEmpty())
            <p class="portal-muted">Belum ada riwayat kunjungan.</p>
        @endif
    </div>

    @foreach ($visits as $visit)
        <div class="portal-card">
            <div class="portal-row">
                <strong>{{ \Carbon\Carbon::parse($visit->tglregistrasi)->translatedFormat('d F Y, H:i') }}</strong>
                <span class="portal-muted">{{ $visit->noregistrasi }}</span>
            </div>
            <div class="portal-row">
                <span class="portal-muted">Dokter</span>
                <span>{{ $visit->dokter_name ?? '-' }}</span>
            </div>
            <div class="portal-row">
                <span class="portal-muted">Keluhan</span>
                <span>{{ $visit->keluhan ?? '-' }}</span>
            </div>
            @if ($visit->diagnosa->isNotEmpty())
                <div class="portal-row">
                    <span class="portal-muted">Diagnosa</span>
                    <span>{{ $visit->diagnosa->implode(', ') }}</span>
                </div>
            @endif
            @if ($visit->tindakan->isNotEmpty())
                <div class="portal-row">
                    <span class="portal-muted">Tindakan</span>
                    <span>{{ $visit->tindakan->implode(', ') }}</span>
                </div>
            @endif
        </div>
    @endforeach
@endsection
