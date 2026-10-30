@extends('public/portal/layout')

@section('style')
    <style>
        .pt-idcard {
            position: relative;
            overflow: hidden;
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 18px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dim) 55%, var(--sage) 130%);
            color: #fff;
        }

        .pt-idcard::before {
            content: '';
            position: absolute;
            top: -40px;
            right: -40px;
            width: 160px;
            height: 160px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .08);
        }

        .pt-idcard-label {
            font-size: 12px;
            opacity: .8;
            margin: 0 0 2px;
        }

        .pt-idcard-name {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 21px;
            font-weight: 600;
            margin: 0 0 14px;
            letter-spacing: -0.01em;
            position: relative;
            z-index: 1;
        }

        .pt-idcard-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            position: relative;
            z-index: 1;
        }

        .pt-idcard-grid div span {
            display: block;
        }

        .pt-idcard-grid .k {
            font-size: 11px;
            opacity: .75;
            margin-bottom: 2px;
        }

        .pt-idcard-grid .v {
            font-size: 13.5px;
            font-weight: 500;
        }

        .pt-quicklinks {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 14px;
        }

        .pt-quicklink {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px;
            background: var(--paper-raised);
            border: 1px solid var(--line);
            border-radius: 14px;
            text-decoration: none;
            color: var(--ink);
        }

        .pt-quicklink .ic {
            flex-shrink: 0;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pt-quicklink .ic svg {
            width: 18px;
            height: 18px;
        }

        .pt-quicklink span {
            font-size: 13.5px;
            font-weight: 600;
        }

        .ic-primary {
            background: var(--primary-soft);
            color: var(--primary);
        }

        .ic-sage {
            background: var(--sage-soft);
            color: var(--sage);
        }

        .ic-clay {
            background: var(--clay-soft);
            color: var(--clay);
        }
    </style>
@endsection

@section('content')
    <div class="pt-idcard">
        <p class="pt-idcard-label">Kartu Pasien</p>
        <p class="pt-idcard-name">{{ $patient->nama }}</p>
        <div class="pt-idcard-grid">
            <div>
                <span class="k">No. RM</span>
                <span class="v">{{ $patient->norm }}</span>
            </div>
            <div>
                <span class="k">Tanggal Lahir</span>
                <span
                    class="v">{{ $patient->tgllahir ? \Carbon\Carbon::parse($patient->tgllahir)->translatedFormat('d F Y') : '-' }}</span>
            </div>
            <div>
                <span class="k">No. HP</span>
                <span class="v">{{ $patient->nohp ?? '-' }}</span>
            </div>
        </div>
    </div>

    <div class="pt-quicklinks mt-4" style="margin-top: 20px">
        <a href="{{ url('portal/riwayat') }}" class="pt-quicklink">
            <span class="ic ic-primary">
                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                    stroke-linejoin="round">
                    <circle cx="10" cy="10" r="7" />
                    <path d="M10 6.5V10l3 2" />
                </svg>
            </span>
            <span>Riwayat</span>
        </a>
        <a href="{{ url('portal/resep') }}" class="pt-quicklink">
            <span class="ic ic-sage">
                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                    stroke-linejoin="round">
                    <g transform="rotate(45 10 10)">
                        <rect x="3" y="7" width="14" height="6" rx="3" />
                        <line x1="10" y1="7" x2="10" y2="13" />
                    </g>
                </svg>
            </span>
            <span>Resep</span>
        </a>
        <a href="{{ url('portal/tagihan') }}" class="pt-quicklink">
            <span class="ic ic-clay">
                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M5 3h10v14l-2-1.2L11 17l-2-1.2L7 17l-2-1.2V3Z" />
                    <line x1="7.5" y1="7" x2="12.5" y2="7" />
                    <line x1="7.5" y1="10" x2="12.5" y2="10" />
                </svg>
            </span>
            <span>Tagihan</span>
        </a>
        <a href="{{ url('portal/booking') }}" class="pt-quicklink">
            <span class="ic ic-primary">
                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                    stroke-linejoin="round">
                    <rect x="3" y="4.5" width="14" height="12" rx="2" />
                    <line x1="3" y1="8" x2="17" y2="8" />
                    <line x1="6.5" y1="3" x2="6.5" y2="6" />
                    <line x1="13.5" y1="3" x2="13.5" y2="6" />
                </svg>
            </span>
            <span>Booking</span>
        </a>
    </div>

    <div class="portal-card">
        <h3>Tentang Portal Ini</h3>
        <p class="portal-muted" style="margin:0;line-height:1.6">Semua riwayat kunjungan, resep obat, dan tagihan Anda
            tercatat otomatis di sini setelah setiap kunjungan. Ingin bertemu dokter lagi? Buat jadwal baru lewat menu
            Booking, tanpa perlu menelepon klinik.</p>
    </div>
@endsection
