@extends('public/portal/layout')

@section('style')
    <style>
        .pt-timeline {
            position: relative;
            padding-left: 18px;
        }

        .pt-timeline::before {
            content: '';
            position: absolute;
            left: 4px;
            top: 8px;
            bottom: 8px;
            width: 2px;
            background: var(--line);
        }

        .pt-tl-item {
            position: relative;
            margin-bottom: 14px;
        }

        .pt-tl-item::before {
            content: '';
            position: absolute;
            left: -18px;
            top: 7px;
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: var(--primary);
            box-shadow: 0 0 0 3px var(--paper);
        }

        .pt-tl-date {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 600;
            font-size: 14.5px;
        }
    </style>
@endsection

@section('content')
    <div class="pt-section-head">
        <h2>Riwayat Kunjungan</h2>
    </div>

    @if ($visits->isEmpty())
        <div class="portal-card pt-empty">
            <svg width="34" height="34" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.4"
                stroke-linecap="round" stroke-linejoin="round">
                <circle cx="10" cy="10" r="7" />
                <path d="M10 6.5V10l3 2" />
            </svg>
            <p style="margin:0">Belum ada riwayat kunjungan.<br>Riwayat akan muncul di sini setelah kunjungan pertama
                Anda.</p>
        </div>
    @else
        <div class="pt-timeline">
            @foreach ($visits as $visit)
                <div class="pt-tl-item">
                    <div class="portal-card" style="margin-bottom:0">
                        <div class="portal-row" style="border-bottom:none;padding-bottom:4px">
                            <span class="pt-tl-date">{{ \Carbon\Carbon::parse($visit->tglregistrasi)->translatedFormat('d F Y, H:i') }}</span>
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
                </div>
            @endforeach
        </div>
    @endif
@endsection
