<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Struk {{ $no_transaction }}</title>
    <style>
        @page {
            margin: 6mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #111;
        }

        .paper {
            width: 80mm;
        }

        /* ganti ke 58mm kalau printer 58 */
        .center {
            text-align: center;
        }

        .row {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .muted {
            color: #666;
        }

        .hr {
            border-top: 1px dashed #999;
            margin: 8px 0;
        }

        .title {
            font-size: 14px;
            font-weight: 800;
            letter-spacing: .2px;
        }

        .small {
            font-size: 11px;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 700;
            border: 1px solid #ddd;
        }

        /* .badge-in {
            background: #eafff2;
            border-color: #b7f0cf;
            color: #0f7a3a;
        }

        .badge-out {
            background: #ffecec;
            border-color: #ffc2c2;
            color: #b42318;
        } */

        .amount {
            font-size: 16px;
            font-weight: 900;
            letter-spacing: .2px;
        }

        .amount-in {
            color: #0f7a3a;
        }

        .amount-out {
            color: #b42318;
        }

        .block {
            padding: 8px 0;
        }

        .note {
            margin-top: 4px;
            line-height: 1.3;
        }
    </style>
</head>

<body>
    @php
        $isIn = strtolower($transaction_type) === 'in';
        $typeLabel = $isIn ? 'Penerimaan Dana' : 'Pengeluaran Dana';
        $badgeClass = $isIn ? 'badge-in' : 'badge-out';
        $sign = '';
        // $sign = $isIn ? '+' : '-';
        $amtClass = $isIn ? 'amount-in' : 'amount-out';
    @endphp

    <div class="paper">
        <div class="center">
            <div class="title">{{ $company_name }}</div>
            @if (!empty($company_address))
                <div class="muted small">{{ $company_address }}</div>
            @endif
            @if (!empty($company_phone))
                <div class="muted small">{{ $company_phone }}</div>
            @endif
        </div>

        <div class="hr"></div>

        <div class="block">
            <div class="row">
                <div class="muted">No</div>
                <div><b>{{ $no_transaction }}</b></div>
            </div>
            <div class="row">
                <div class="muted">Tanggal</div>
                <div>{{ \Carbon\Carbon::parse($created_at)->format('d/m/Y H:i') }}</div>
            </div>
            <div class="row">
                <div class="muted">Tipe</div>
                <div>{{ $typeLabel }}</div>
            </div>
            @if ($pasien)
                <div class="row">
                    <div class="muted">Nama Pasien</div>
                    <div>{{ $pasien ?? '-' }}</div>
                </div>
            @endif
            <div class="row">
                <div class="muted">Petugas</div>
                <div>{{ $user_name ?? '-' }}</div>
            </div>
        </div>

        <div class="hr"></div>

        <div class="block">
            <div class="muted small">Keterangan</div>
            <div class="note"><b>{{ $notes }}</b></div>
        </div>

        <div class="hr"></div>

        <div class="block">
            <div class="row">
                <div class="muted">Total</div>
                <div class="amount {{ $amtClass }}">{{ $sign }} Rp
                    {{ number_format($amount, 0, ',', '.') }}
                </div>
            </div>
        </div>

        <div class="hr"></div>

        <div class="center muted small" style="margin-top:10px">
            {{-- Terima kasih 🙏<br> --}}
            <span class="small">*Struk ini valid tanpa tanda tangan</span>
        </div>
    </div>
    <script>
        window.onload = () => window.print();
        setTimeout(() => {
            window.close()
        }, 1000);
    </script>
</body>
</
