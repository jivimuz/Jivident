<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Struk {{ $sale->no_sale }}</title>
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
        }

        .small {
            font-size: 11px;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        table.items td {
            padding: 2px 0;
            vertical-align: top;
        }

        .amount {
            font-size: 15px;
            font-weight: 900;
        }
    </style>
</head>

<body>
    <div class="paper">
        <div class="center">
            <div class="title">{{ $companyName->value ?? '' }}</div>
            @if (!empty($companyAddress->value))
                <div class="muted small">{{ $companyAddress->value }}</div>
            @endif
            @if (!empty($companyPhone->value))
                <div class="muted small">{{ $companyPhone->value }}</div>
            @endif
        </div>

        <div class="hr"></div>

        <div class="row">
            <div class="muted">No</div>
            <div><b>{{ $sale->no_sale }}</b></div>
        </div>
        <div class="row">
            <div class="muted">Tanggal</div>
            <div>{{ $sale->created_at->format('d/m/Y H:i') }}</div>
        </div>
        <div class="row">
            <div class="muted">Kasir</div>
            <div>{{ $sale->userhandler->display_name ?? '-' }}</div>
        </div>
        @if ($sale->buyer_name)
            <div class="row">
                <div class="muted">Pembeli</div>
                <div>{{ $sale->buyer_name }}</div>
            </div>
        @endif
        @if ($sale->status === 'voided')
            <div class="row">
                <div class="muted">Status</div>
                <div><b>VOID</b></div>
            </div>
        @endif

        <div class="hr"></div>

        <table class="items">
            @foreach ($sale->items as $item)
                <tr>
                    <td colspan="2">{{ $item->product_name }}</td>
                </tr>
                <tr>
                    <td class="muted small">{{ $item->qty }} x Rp
                        {{ number_format($item->price, 0, ',', '.') }}
                        @if ($item->discount > 0)
                            (disc Rp {{ number_format($item->discount, 0, ',', '.') }})
                        @endif
                    </td>
                    <td class="text-end" style="text-align:right">Rp
                        {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </table>

        <div class="hr"></div>

        <div class="row">
            <div class="muted">Subtotal</div>
            <div>Rp {{ number_format($sale->total_gross, 0, ',', '.') }}</div>
        </div>
        @if ($sale->total_discount > 0)
            <div class="row">
                <div class="muted">Diskon</div>
                <div>- Rp {{ number_format($sale->total_discount, 0, ',', '.') }}</div>
            </div>
        @endif
        <div class="row">
            <div class="muted">Total</div>
            <div class="amount">Rp {{ number_format($sale->total_net, 0, ',', '.') }}</div>
        </div>
        <div class="row">
            <div class="muted">Bayar</div>
            <div>{{ $sale->payment->title ?? '-' }}</div>
        </div>

        <div class="hr"></div>

        <div class="center muted small" style="margin-top:10px">
            Terima kasih<br>
            <span class="small">*Struk ini valid tanpa tanda tangan</span>
        </div>
    </div>
    @unless (request()->boolean('pdf'))
        <script>
            window.onload = () => window.print();
        </script>
    @endunless
</body>

</html>
