<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Radiologi - {{ $radiologi->pasien_nama ?? 'Pasien' }}</title>
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
            box-shadow: 0 0 10px rgba(0, 0, 0, .1);
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

        .judul-surat {
            text-align: center;
            margin-bottom: 20px;
        }

        .judul-surat h3 {
            margin: 0;
            font-size: 15px;
            text-decoration: underline;
        }

        .table-identitas {
            width: 100%;
            margin: 10px 0 20px 0;
            font-size: 13px;
        }

        .table-identitas td {
            padding: 4px;
            vertical-align: top;
        }

        .gambar-box {
            text-align: center;
            margin: 15px 0;
        }

        .gambar-box img {
            max-width: 100%;
            max-height: 300px;
            border: 1px solid #ccc;
        }

        .hasil-box {
            border: 1px solid #000;
            padding: 10px 15px;
            margin-top: 10px;
        }

        .footer-ttd {
            margin-top: 25px;
            text-align: right;
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
    </div>

    <div class="print-container">
        <div class="kop-surat">
            <h2>{{ $company_name->value ?? '' }}</h2>
            <p>{{ $company_address->value ?? '' }}</p>
        </div>

        <div class="judul-surat">
            <h3>LAPORAN HASIL RADIOLOGI</h3>
        </div>

        <table class="table-identitas">
            <tr>
                <td width="30%">Nama Pasien</td>
                <td width="3%">:</td>
                <td><b>{{ $radiologi->pasien_nama ?? '-' }}</b></td>
            </tr>
            <tr>
                <td>No. RM</td>
                <td>:</td>
                <td>{{ $radiologi->pasien_norm ?? '-' }}</td>
            </tr>
            <tr>
                <td>Jenis Pemeriksaan</td>
                <td>:</td>
                <td><b>{{ $radiologi->jenis_nama ?? '-' }}</b></td>
            </tr>
            <tr>
                <td>Catatan Klinis</td>
                <td>:</td>
                <td>{{ $radiologi->catatan_klinis ?? '-' }}</td>
            </tr>
            <tr>
                <td>Tanggal Pemeriksaan</td>
                <td>:</td>
                <td>{{ $radiologi->tgl_jadwal ? \Carbon\Carbon::parse($radiologi->tgl_jadwal)->translatedFormat('d F Y') : '-' }}</td>
            </tr>
        </table>

        @if ($radiologi->gambar_path ?? null)
            <div class="gambar-box">
                <img src="{{ request()->boolean('pdf') ? \Illuminate\Support\Facades\Storage::disk('public')->path($radiologi->gambar_path) : \Illuminate\Support\Facades\Storage::url($radiologi->gambar_path) }}">
            </div>
        @endif

        <div class="hasil-box">
            <b>Hasil / Interpretasi:</b>
            <p style="text-align: justify;">{{ $radiologi->hasil_interpretasi ?? '-' }}</p>
        </div>

        <div class="footer-ttd">
            <p>{{ $company_city->value ?? '' }}, {{ \Carbon\Carbon::parse($radiologi->tgl_hasil ?? now())->translatedFormat('d F Y') }}<br>
                Dokter Radiologi</p>
        </div>
    </div>
</body>

</html>
