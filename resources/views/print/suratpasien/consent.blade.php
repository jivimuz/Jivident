<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Persetujuan Tindakan - {{ $consent->pasien_nama ?? 'Pasien' }}</title>
    <style>
        @page {
            size: A5 portrait;
            margin: 10mm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 13px;
            color: #000;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
            line-height: 1.5;
        }

        .print-container {
            background-color: #fff;
            width: 148mm;
            min-height: 210mm;
            margin: 20px auto;
            padding: 20px 25px;
            box-sizing: border-box;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .kop-surat {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .kop-surat h2 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
            font-family: Arial, sans-serif;
        }

        .kop-surat p {
            margin: 3px 0;
            font-size: 11px;
            font-family: Arial, sans-serif;
        }

        .judul-surat {
            text-align: center;
            margin-bottom: 20px;
        }

        .judul-surat h3 {
            margin: 0;
            font-size: 15px;
            text-decoration: underline;
        }

        .isi-surat {
            text-align: justify;
            margin-bottom: 15px;
        }

        .table-identitas {
            width: 100%;
            margin: 10px 0 20px 20px;
            font-size: 13px;
        }

        .table-identitas td {
            padding: 4px;
            vertical-align: top;
        }

        .footer-ttd {
            margin-top: 30px;
            text-align: center;
            width: 220px;
            margin-left: auto;
        }

        .footer-ttd p {
            margin: 0 0 4px 0;
        }

        .footer-ttd .nama-penandatangan {
            font-weight: bold;
            text-decoration: underline;
        }

        .btn-print-wrapper {
            text-align: center;
            margin: 20px 0;
            font-family: Arial, sans-serif;
        }

        .btn-print {
            padding: 8px 16px;
            background-color: #198754;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        @media print {
            body {
                background-color: #fff;
            }

            .print-container {
                margin: 0;
                box-shadow: none;
                width: 100%;
                border: none;
                padding: 0;
            }

            .btn-print-wrapper {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="btn-print-wrapper">
        <button class="btn-print" onclick="window.print()">Cetak</button>
        <a class="btn-print" style="background-color:#0d6efd; margin-left:10px; text-decoration:none; display:inline-block;"
            href="{{ request()->fullUrlWithQuery(['pdf' => 1]) }}">Unduh PDF</a>
        <button class="btn-print" style="background-color:#6c757d; margin-left:10px;"
            onclick="window.close()">Tutup</button>
    </div>

    <div class="print-container">
        <div class="kop-surat">
            <h2>{{ $company_name->value ?? '' }}</h2>
            <p>{{ $company_address->value ?? '' }}</p>
        </div>

        <div class="judul-surat">
            <h3>{{ ($consent->tipe ?? '') === 'tindakan' ? 'PERSETUJUAN TINDAKAN MEDIS' : 'PERSETUJUAN UMUM PELAYANAN' }}
            </h3>
            <p style="margin:0;font-size:12px">(Informed Consent)</p>
        </div>

        <table class="table-identitas">
            <tr>
                <td width="35%">Nama Pasien</td>
                <td width="3%">:</td>
                <td><b>{{ $consent->pasien_nama ?? '-' }}</b></td>
            </tr>
            <tr>
                <td>No. RM</td>
                <td>:</td>
                <td>{{ $consent->pasien_norm ?? '-' }}</td>
            </tr>
            @if (($consent->tipe ?? '') === 'tindakan')
                <tr>
                    <td>Tindakan</td>
                    <td>:</td>
                    <td><b>{{ $consent->tindakan_nama ?? '-' }}</b></td>
                </tr>
            @endif
        </table>

        <div class="isi-surat">
            {{ $consent->isi_consent ?? '-' }}
        </div>

        <div class="footer-ttd">
            <p>{{ $company_city->value ?? '' }}, {{ \Carbon\Carbon::parse($consent->created_at ?? now())->translatedFormat('d F Y, H:i') }} WIB</p>
            @if ($consent->signature_path ?? null)
                <img src="{{ request()->boolean('pdf') ? public_path($consent->signature_path) : url($consent->signature_path) }}"
                    style="max-height:60px;display:block;margin:0 auto 4px auto">
            @endif
            <div class="nama-penandatangan">{{ $consent->ditandatangani_oleh ?? '-' }}</div>
            <div style="font-size: 11px;">({{ $consent->hubungan_dengan_pasien ?? '-' }})</div>
        </div>
    </div>
</body>

</html>
