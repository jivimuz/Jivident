<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: #111;
        }

        h3 {
            margin-top: 20px;
            margin-bottom: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: left;
        }

        th {
            background: #f5f5f5;
        }

        .muted {
            color: #777;
        }
    </style>
</head>

<body>
    <p>Berikut ringkasan operasional yang perlu perhatian:</p>

    @if ($licenseExpiryDate)
        <h3>⚠️ Lisensi Aplikasi</h3>
        <p>Lisensi akan berakhir pada <b>{{ $licenseExpiryDate }}</b>. Segera perpanjang sebelum tanggal ini.</p>
    @endif

    <h3>Produk Stok Habis ({{ count($outOfStockProducts) }})</h3>
    @if (count($outOfStockProducts) > 0)
        <table>
            <thead>
                <tr>
                    <th>Produk</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($outOfStockProducts as $product)
                    <tr>
                        <td>{{ $product }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="muted">Tidak ada produk yang stoknya habis.</p>
    @endif

    <h3>Produk Mendekati Expired ({{ $expiringLots->count() }})</h3>
    @if ($expiringLots->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Tanggal Expired</th>
                    <th>Sisa Stok</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($expiringLots as $lot)
                    <tr>
                        <td>{{ $lot->product_name }}</td>
                        <td>{{ $lot->expired_date }}</td>
                        <td>{{ $lot->available_qty }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="muted">Tidak ada produk yang mendekati tanggal expired.</p>
    @endif

    <p class="muted">Email ini dikirim otomatis oleh Jivident.</p>
</body>

</html>
