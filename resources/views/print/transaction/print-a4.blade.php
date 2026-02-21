<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice {{ $no_transaction }}</title>
    <style>
        @page {
            size: A4;
            margin: 14mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #111;
        }

        .top {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: flex-start;
        }

        .brand h2 {
            margin: 0;
            font-size: 20px;
            letter-spacing: .2px;
        }

        .muted {
            color: #666;
        }

        .box {
            border: 1px solid #e7e7e7;
            border-radius: 12px;
            padding: 12px;
        }

        .meta {
            min-width: 300px;
        }

        .meta div {
            margin: 2px 0;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            border: 1px solid #ddd;
        }

        .badge-in {
            background: #eafff2;
            border-color: #b7f0cf;
            color: #0f7a3a;
        }

        .badge-out {
            background: #ffecec;
            border-color: #ffc2c2;
            color: #b42318;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
        }

        th,
        td {
            border-bottom: 1px solid #eee;
            padding: 10px 8px;
            text-align: left;
        }

        th {
            background: #fafafa;
            font-weight: 700;
        }

        .right {
            text-align: right;
        }

        .total-row td {
            border-top: 2px solid #111;
            border-bottom: none;
            padding-top: 12px;
        }

        .total {
            font-size: 14px;
            font-weight: 900;
        }

        .note {
            margin-top: 12px;
            line-height: 1.4;
        }

        .footer {
            margin-top: 18px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .sign {
            width: 220px;
            text-align: center;
        }

        .line {
            border-top: 1px solid #bbb;
            margin-top: 40px;
        }
    </style>
</head>

<body>
    @php
        $isIn = strtolower($transaction_type) === 'in';
        $typeLabel = $isIn ? 'Penerimaan Dana' : 'Pengeluaran Dana';
        $badgeClass = $isIn ? 'badge-in' : 'badge-out';
        $sign = $isIn ? '+' : '-';
    @endphp

    <div class="top">
        <div class="brand">
            <h2>{{ $company_name }}</h2>
            @if (!empty($company_address))
                <div class="muted">{{ $company_address }}</div>
            @endif
            @if (!empty($company_phone))
                <div class="muted">{{ $company_phone }}</div>
            @endif
            <div class="muted" style="margin-top:8px"><b>INVOICE</b></div>
        </div>

        <div class="box meta">
            <div><b>No Invoice:</b> {{ $no_transaction }}</div>
            <div><b>Tanggal:</b> {{ \Carbon\Carbon::parse($created_at)->translatedFormat('d F Y H:i') }}</div>
            <div><b>Tipe:</b> {{ $typeLabel }}</div>
            @if ($pasien)
                <div><b>Nama Pasien:</b> {{ $pasien }}</div>
            @endif

            <div><b>Petugas:</b> {{ $user_name ?? '-' }}</div>
        </div>
    </div>

    <div class="box note" style="margin-top:14px">
        <b>Keterangan</b>
        <div style="margin-top:6px">{{ $notes }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Deskripsi</th>
                <th class="right">Nominal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Transaksi {{ $typeLabel }}</td>
                <td class="right">{{ $sign }} Rp {{ number_format($amount, 0, ',', '.') }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td class="right total">TOTAL</td>
                <td class="right total">{{ $sign }} Rp {{ number_format($amount, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <div class="muted">
            *Invoice ini valid tanpa tanda tangan basah.
        </div>

        <div class="sign">
            <div class="muted">Tanda tangan</div>
            <div class="line"></div>
            <div>{{ $company_name }}</div>
        </div>
    </div>

    <script>
        window.onload = () => window.print();
        setTimeout(() => {
            window.close()
        }, 1000);
    </script>
</body>

</html>
