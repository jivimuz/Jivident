@extends('public/portal/layout')

@section('style')
    <style>
        .pt-resep-date {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 600;
            font-size: 14.5px;
        }

        .pt-resep-item {
            padding: 10px 0;
            border-bottom: 1px solid var(--line);
        }

        .pt-resep-item:last-child {
            border-bottom: none;
        }

        .pt-resep-item .row {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            font-size: 13.5px;
        }

        .pt-resep-item .name {
            font-weight: 500;
        }

        .pt-resep-qty {
            display: inline-block;
            padding: 2px 9px;
            border-radius: 999px;
            background: var(--sage-soft);
            color: var(--sage);
            font-size: 11.5px;
            font-weight: 600;
            white-space: nowrap;
        }

        .pt-resep-signa {
            margin-top: 3px;
            font-size: 12px;
            color: var(--ink-soft);
        }
    </style>
@endsection

@section('content')
    <div class="pt-section-head">
        <h2>Riwayat Resep Obat</h2>
    </div>

    @if ($reseps->isEmpty())
        <div class="portal-card pt-empty">
            <svg width="34" height="34" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.4"
                stroke-linecap="round" stroke-linejoin="round">
                <g transform="rotate(45 10 10)">
                    <rect x="3" y="7" width="14" height="6" rx="3" />
                    <line x1="10" y1="7" x2="10" y2="13" />
                </g>
            </svg>
            <p style="margin:0">Belum ada riwayat resep.<br>Resep dari dokter akan tercatat di sini.</p>
        </div>
    @endif

    @foreach ($reseps as $daftarpasienId => $items)
        <div class="portal-card">
            <div class="pt-resep-date" style="margin-bottom:6px">
                {{ \Carbon\Carbon::parse($items->first()->tglregistrasi)->translatedFormat('d F Y') }}
            </div>
            @foreach ($items as $item)
                <div class="pt-resep-item">
                    <div class="row">
                        <span class="name">{{ $item->type === 'racik' ? ($item->nama_racikan ?? 'Racikan') : $item->obat_name }}</span>
                        <span class="pt-resep-qty">{{ $item->qty }} {{ $item->unit ?? '' }}</span>
                    </div>
                    @if ($item->signa)
                        <div class="pt-resep-signa">{{ $item->signa }}</div>
                    @endif
                </div>
            @endforeach
        </div>
    @endforeach
@endsection
