@extends('public/portal/layout')

@section('style')
    <style>
        .pt-bill-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .pt-bill-date {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 600;
            font-size: 14.5px;
        }

        .pt-bill-sisa {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-top: 6px;
            padding-top: 10px;
            border-top: 1px solid var(--line);
        }

        .pt-bill-sisa .amount {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 18px;
            font-weight: 700;
        }

        .pt-bill-sisa .amount.due {
            color: var(--crimson);
        }

        .pt-bill-sisa .amount.clear {
            color: var(--sage);
        }
    </style>
@endsection

@section('content')
    <div class="pt-section-head">
        <h2>Riwayat Tagihan</h2>
    </div>

    @if ($tagihan->isEmpty())
        <div class="portal-card pt-empty">
            <svg width="34" height="34" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.4"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 3h10v14l-2-1.2L11 17l-2-1.2L7 17l-2-1.2V3Z" />
                <line x1="7.5" y1="7" x2="12.5" y2="7" />
                <line x1="7.5" y1="10" x2="12.5" y2="10" />
            </svg>
            <p style="margin:0">Belum ada riwayat tagihan.</p>
        </div>
    @endif

    @foreach ($tagihan as $row)
        <div class="portal-card">
            <div class="pt-bill-head">
                <span class="pt-bill-date">{{ \Carbon\Carbon::parse($row->tglregistrasi)->translatedFormat('d F Y') }}</span>
                <span class="portal-badge {{ $row->statuspembayaran === 'Lunas' ? 'lunas' : 'belum' }}">{{ $row->statuspembayaran }}</span>
            </div>
            <div class="portal-row">
                <span class="portal-muted">Total Tagihan</span>
                <span>Rp {{ number_format($row->total_tagihan, 0, ',', '.') }}</span>
            </div>
            <div class="portal-row" style="border-bottom:none">
                <span class="portal-muted">Sudah Dibayar</span>
                <span>Rp {{ number_format($row->total_paid, 0, ',', '.') }}</span>
            </div>
            <div class="pt-bill-sisa">
                <span class="portal-muted">Sisa</span>
                <span class="amount {{ $row->sisa > 0 ? 'due' : 'clear' }}">Rp {{ number_format($row->sisa, 0, ',', '.') }}</span>
            </div>
        </div>
    @endforeach
@endsection
