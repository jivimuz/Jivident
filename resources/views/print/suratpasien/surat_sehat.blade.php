<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keterangan Sehat - {{ $pasien->name ?? 'Pasien' }}</title>
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

        .judul-surat p {
            margin: 0;
            font-size: 12px;
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

        .pemeriksaan-box {
            border: 1px solid #000;
            padding: 10px 15px;
            margin-bottom: 20px;
        }

        .table-fisik {
            width: 100%;
            font-size: 12px;
        }

        .table-fisik td {
            padding: 4px 0;
        }

        .footer-ttd {
            margin-top: 30px;
            float: right;
            text-align: center;
            width: 200px;
        }

        .footer-ttd p {
            margin: 0 0 60px 0;
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
            background-color: #198754;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        .btn-print:hover {
            background-color: #157347;
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
        <button class="btn-print" onclick="window.print()">Cetak Surat Sehat</button>
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
            <h3>SURAT KETERANGAN SEHAT</h3>
            <p>No: {{ 'SKSE/' . date('m/Y') . '/' . sprintf('%004s', base64_decode(base64_decode($id))) }}</p>
        </div>

        <div class="isi-surat">
            Yang bertanda tangan di bawah ini Dokter Pemeriksa Klinik Utama Sehat Bersama, menerangkan dengan
            sesungguhnya bahwa:
        </div>

        <table class="table-identitas">
            <tr>
                <td width="30%">Nama Lengkap</td>
                <td width="3%">:</td>
                <td><b>{{ $pasien->nama ?? '-' }}</b></td>
            </tr>
            <tr>
                <td>Tempat, Tgl Lahir</td>
                <td>:</td>
                <td>{{ $pasien->tempatlahir ?? '-' }},
                    {{ \Carbon\Carbon::parse($pasien->tgllahir ?? now())->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td>Jenis Kelamin</td>
                <td>:</td>
                <td>{{ $pasien->jeniskelamin ?? '-' }}</td>
            </tr>
            <tr>
                <td>Alamat Lengkap</td>
                <td>:</td>
                <td>{{ $pasien->alamat ?? '-' }}</td>
            </tr>
        </table>

        <div class="pemeriksaan-box">
            <b>Hasil Pemeriksaan Fisik:</b>
            <table class="table-fisik" style="margin-top: 8px;">
                <tr>
                    <td width="25%">Berat Badan</td>
                    <td width="3%">:</td>
                    <td width="22%">{{ $data->bb ?? '-' }} kg</td>
                    <td width="25%">Tekanan Darah</td>
                    <td width="3%">:</td>
                    <td width="22%">{{ $data->td ?? '-' }} mmHg</td>
                </tr>
                <tr>
                    <td>Tinggi Badan</td>
                    <td>:</td>
                    <td>{{ $data->tb ?? '-' }} cm</td>
                    <td>Golongan Darah</td>
                    <td>:</td>
                    <td>{{ $data->goldar ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Buta Warna</td>
                    <td>:</td>
                    <td>{{ $data->buta_warna ?? '-' }}</td>
                    <td>Visus Mata</td>
                    <td>:</td>
                    <td>{{ $data->visus ?? '-' }}</td>
                </tr>
            </table>
        </div>

        <div class="isi-surat">
            Berdasarkan pemeriksaan fisik pada hari ini, yang bersangkutan dinyatakan <b>DALAM KEADAAN SEHAT</b> dan
            tidak menderita kelainan/penyakit menular.<br><br>
            Surat keterangan ini dipergunakan untuk keperluan: <br>
            <b><i>"{{ $data->keperluan ?? '-' }}"</i></b>
        </div>

        <div class="isi-surat">Demikian surat keterangan ini diberikan untuk dapat dipergunakan sebagaimana mestinya.
        </div>

        <div class="footer-ttd">
            <p>{{ $company_city->value ?? '' }},
                {{ $tglmulai }}<br>Dokter
                Pemeriksa,</p>
            <div class="nama-dokter">{{ $pasien->dokter_name ?? '-' }}</div>
            <div style="font-size: 11px; margin-top: 2px;">NIK/SIP: {{ $pasien->dokter_nik ?? '123.456.789' }}
            </div>
        </div>
    </div>
</body>

</html>
