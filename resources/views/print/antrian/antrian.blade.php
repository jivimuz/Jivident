<html lang="id">
<?php
use App\Models\Setting;
$app_name = Setting::where('parameter', 'app_name')->first()->value ?: 'AppName';
?>

<head>
    <meta charset="UTF-8">
    <title>Tiket Antrian</title>
    <style type="text/css" media="print">
        @media print {
            @page {
                size: auto;
                margin: 0;
            }

            body {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .no-print {
                display: none !important;
            }
        }

        body {
            margin: 0;
            background: #fff;
            font-family: "Poppins", "Tahoma", sans-serif;
        }

        .ticket {
            width: 335px;
            /* thermal printer 80mm */
            margin: 0 auto;
            padding: 18px;
            text-align: center;
            border-radius: 0;
            box-sizing: border-box;
        }

        .clinic-name {
            font-size: 14pt;
            font-weight: 700;
            color: #007bff;
            margin-bottom: 2px;
            text-transform: uppercase;
        }

        .divider {
            border: none;
            border-top: 1.2px dashed #000;
            margin: 8px 0;
        }

        .time {
            font-size: 10pt;
            color: #444;
            margin-top: 2px;
        }

        .queue-label {
            font-size: 13pt;
            font-weight: 600;
            color: #111;
            margin-top: 8px;
        }

        .queue-number {
            font-size: 42pt;
            font-weight: 800;
            color: #000;
            margin: 6px 0 10px;
            letter-spacing: 1px;
        }

        .room {
            font-size: 13pt;
            font-weight: 600;
            color: #333;
            margin-bottom: 4px;
        }

        .doctor {
            font-size: 11pt;
            color: #555;
            font-style: italic;
            margin-bottom: 10px;
        }

        .note {
            font-size: 9pt;
            color: #444;
            margin-top: 6px;
        }

        .footer {
            border-top: 1.2px dashed #000;
            margin-top: 8px;
            padding-top: 6px;
            font-size: 9pt;
            color: #444;
        }

        .print-btn {
            margin-top: 12px;
            background: #007bff;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 11pt;
            cursor: pointer;
        }

        .print-btn:hover {
            background: #0056b3;
        }
    </style>
</head>

<body onload="window.print()">
    <div class="ticket">
        <div class="clinic-name">{{ $app_name }}</div>

        <hr class="divider" />

        <div class="time">{{ WIB() }}</div>

        <div class="queue-label">Nomor Antrian</div>
        <div class="queue-number">{{ $data->noantrian }}</div>

        <div class="room">{{ $data->ruangan }}</div>
        <div class="doctor">{{ $data->dokter }}</div>

        <hr class="divider" />

        <div class="note">Silakan menunggu, nomor Anda akan segera dipanggil.</div>

        <div class="footer">Terima kasih atas kunjungan Anda.</div>

        <button class="print-btn no-print" onclick="window.print()">🖨 Cetak Tiket</button>
    </div>
</body>

</html>
<script>
    setTimeout(() => {
        window.close()
    }, 1000);
</script>
