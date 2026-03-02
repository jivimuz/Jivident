<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Rujukan - {{ $patient->name ?? 'Pasien' }}</title>
    <style>
        @page {
            size: A5 portrait;
            margin: 10mm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
            line-height: 1.4;
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
            padding-bottom: 12px;
            margin-bottom: 15px;
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

        .tujuan-surat {
            margin-bottom: 15px;
            font-weight: bold;
        }

        .judul-surat {
            text-align: center;
            margin-bottom: 15px;
        }

        .judul-surat h3 {
            margin: 0;
            font-size: 15px;
            text-decoration: underline;
        }

        .isi-surat {
            text-align: justify;
            margin-bottom: 10px;
        }

        .table-identitas,
        .table-klinis {
            width: 100%;
            margin: 5px 0 15px 15px;
            font-size: 12px;
        }

        .table-identitas td,
        .table-klinis td {
            padding: 3px;
            vertical-align: top;
        }

        .footer-ttd {
            margin-top: 30px;
            float: right;
            text-align: center;
            width: 200px;
        }

        .footer-ttd p {
            margin: 0 0 50px 0;
        }

        .footer-ttd .nama-dokter {
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
            background-color: #0dcaf0;
            color: #000;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        .btn-print:hover {
            background-color: #31d2f2;
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
        <button class="btn-print" onclick="window.print()">Cetak Surat Rujukan</button>
        <button class="btn-print" style="background-color:#6c757d; color: #fff; margin-left:10px;"
            onclick="window.close()">Tutup</button>
    </div>

    <div class="print-container">
        <div class="kop-surat">
            <h2>KLINIK UTAMA SEHAT BERSAMA</h2>
            <p>Jl. Kesehatan No. 123, Kota Medika, Jawa Barat 40123<br>Telp: (022) 1234567 | Email:
                info@sehatbersama.com</p>
        </div>

        <div class="tujuan-surat">
            Kepada Yth,<br>
            Teman Sejawat Dokter Spesialis {{ $rujukan->spesialis_tujuan ?? 'Penyakit Dalam' }}<br>
            Di - {{ $rujukan->rs_tujuan ?? 'RSUD Kota Medika' }}
        </div>

        <div class="judul-surat">
            <h3>SURAT RUJUKAN MEDIS</h3>
        </div>

        <div class="isi-surat">
            Bersama surat ini, kami mohon pemeriksaan dan penanganan lebih lanjut terhadap pasien:
        </div>

        <table class="table-identitas">
            <tr>
                <td width="25%">Nama Lengkap</td>
                <td width="3%">:</td>
                <td><b>{{ $patient->name ?? 'Tn. Budi Santoso' }}</b></td>
            </tr>
            <tr>
                <td>Jenis Kelamin</td>
                <td>:</td>
                <td>{{ $patient->jenis_kelamin ?? 'Laki-laki' }}</td>
            </tr>
            <tr>
                <td>Umur</td>
                <td>:</td>
                <td>{{ $patient->age ?? '45' }} Tahun</td>
            </tr>
            <tr>
                <td>No. RM</td>
                <td>:</td>
                <td>{{ $patient->rm ?? '00-11-22-33' }}</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td>{{ $patient->alamat ?? 'Jl. Merpati No. 5, Kota Medika' }}</td>
            </tr>
        </table>

        <div class="isi-surat">
            Pada pemeriksaan kami hari ini, didapatkan keterangan klinis sebagai berikut:
        </div>

        <table class="table-klinis">
            <tr>
                <td width="25%">Keluhan Utama</td>
                <td width="3%">:</td>
                <td>{{ $rujukan->keluhan ?? 'Nyeri dada menjalar ke punggung sejak 2 hari yang lalu, disertai sesak napas.' }}
                </td>
            </tr>
            <tr>
                <td>Pemeriksaan Fisik</td>
                <td>:</td>
                <td>{{ $rujukan->pemeriksaan_fisik ?? 'TD: 160/100 mmHg, N: 110 x/m, S: 37 C, RR: 24 x/m.' }}</td>
            </tr>
            <tr>
                <td>Diagnosis Sementara</td>
                <td>:</td>
                <td><b>{{ $rujukan->diagnosis ?? 'Obs. Chest Pain e.c Susp. CAD' }}</b></td>
            </tr>
            <tr>
                <td>Terapi Diberikan</td>
                <td>:</td>
                <td>{{ $rujukan->terapi ?? 'Oksigen 3 LPM, Isosorbide Dinitrate 5mg (Sublingual).' }}</td>
            </tr>
        </table>

        <div class="isi-surat" style="margin-top: 15px;">
            Mohon penatalaksanaan lebih lanjut. Atas perhatian dan kerjasama sejawat, kami ucapkan terima kasih.
        </div>

        <div class="footer-ttd">
            <p>Kota Medika,
                {{ \Carbon\Carbon::parse($rujukan->tgl_surat ?? now())->translatedFormat('d F Y') }}<br>Hormat Kami,
            </p>
            <div class="nama-dokter">{{ $rujukan->doctor_name ?? 'dr. Sarah, Sp.PD' }}</div>
            <div style="font-size: 11px; margin-top: 2px;">SIP: {{ $rujukan->doctor_sip ?? '123.456.789' }}</div>
        </div>
    </div>
</body>

</html>
