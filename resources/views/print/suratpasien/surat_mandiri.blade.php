<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $surat->jenis === 'sakit' ? 'Surat Izin Sakit' : ($surat->jenis === 'sehat' ? 'Surat Keterangan Sehat' : ($surat->jenis === 'rujukan' ? 'Surat Rujukan' : 'Surat Keterangan')) }} - {{ $surat->nama }}</title>
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

        .footer-ttd {
            margin-top: 40px;
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
            background-color: #3a57e8;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        .btn-print:hover {
            background-color: #2b45c2;
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
        <button class="btn-print" onclick="window.print()">Cetak Surat</button>
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

        @php
            $detail = $surat->detail ?? [];
            $nomor = strtoupper(substr($surat->jenis, 0, 3)) . '/' . date('m/Y', strtotime($surat->created_at)) . '/' . sprintf('%004s', $surat->id);
        @endphp

        @if ($surat->jenis === 'sakit')
            <div class="judul-surat">
                <h3>SURAT KETERANGAN SAKIT</h3>
                <p>No: {{ $nomor }}</p>
            </div>

            <div class="isi-surat">Yang bertanda tangan di bawah ini menerangkan bahwa:</div>

            <table class="table-identitas">
                <tr>
                    <td width="30%">Nama Lengkap</td>
                    <td width="3%">:</td>
                    <td><b>{{ $surat->nama }}</b></td>
                </tr>
                <tr>
                    <td>Umur</td>
                    <td>:</td>
                    <td>{{ $surat->umur ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Pekerjaan</td>
                    <td>:</td>
                    <td>{{ $surat->pekerjaan ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Alamat Lengkap</td>
                    <td>:</td>
                    <td>{{ $surat->alamat ?? '-' }}</td>
                </tr>
            </table>

            @php
                $lamaIstirahat = $detail['lama_istirahat'] ?? 3;
                $tglMulai = \Carbon\Carbon::parse($detail['tanggal_mulai'] ?? $surat->created_at);
                $tglSelesai = $tglMulai->copy()->addDays((int) $lamaIstirahat - 1);
            @endphp
            <div class="isi-surat">
                Berdasarkan keterangan yang bersangkutan, dinyatakan dalam keadaan sakit{{ ($detail['keterangan'] ?? null) ? ' ('.$detail['keterangan'].')' : '' }}
                dan memerlukan istirahat selama <b>{{ $lamaIstirahat }} hari</b>, terhitung mulai tanggal
                <b>{{ $tglMulai->translatedFormat('d F Y') }}</b> sampai dengan
                <b>{{ $tglSelesai->translatedFormat('d F Y') }}</b>.
            </div>
        @elseif($surat->jenis === 'sehat')
            <div class="judul-surat">
                <h3>SURAT KETERANGAN SEHAT</h3>
                <p>No: {{ $nomor }}</p>
            </div>

            <div class="isi-surat">Yang bertanda tangan di bawah ini menerangkan dengan sesungguhnya bahwa:</div>

            <table class="table-identitas">
                <tr>
                    <td width="30%">Nama Lengkap</td>
                    <td width="3%">:</td>
                    <td><b>{{ $surat->nama }}</b></td>
                </tr>
                <tr>
                    <td>Jenis Kelamin</td>
                    <td>:</td>
                    <td>{{ $surat->jenis_kelamin ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Umur</td>
                    <td>:</td>
                    <td>{{ $surat->umur ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Pekerjaan</td>
                    <td>:</td>
                    <td>{{ $surat->pekerjaan ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Alamat Lengkap</td>
                    <td>:</td>
                    <td>{{ $surat->alamat ?? '-' }}</td>
                </tr>
                @if ($surat->keperluan)
                    <tr>
                        <td>Keperluan</td>
                        <td>:</td>
                        <td>{{ $surat->keperluan }}</td>
                    </tr>
                @endif
            </table>

            <div class="isi-surat">
                {{ $detail['catatan'] ?? 'Dalam keadaan sehat jasmani dan rohani berdasarkan keterangan yang bersangkutan.' }}
            </div>
        @elseif($surat->jenis === 'rujukan')
            <div class="judul-surat">
                <h3>SURAT RUJUKAN</h3>
                <p>No: {{ $nomor }}</p>
            </div>

            <div class="isi-surat">
                Kepada Yth,<br>
                Teman Sejawat {{ ($detail['spesialis_tujuan'] ?? null) ? 'Dokter Spesialis '.$detail['spesialis_tujuan'] : '' }}<br>
                Di - {{ $detail['tujuan_rujukan'] ?? '-' }}
            </div>

            <div class="isi-surat">Bersama surat ini, kami mohon pemeriksaan dan penanganan lebih lanjut terhadap:
            </div>

            <table class="table-identitas">
                <tr>
                    <td width="25%">Nama Lengkap</td>
                    <td width="3%">:</td>
                    <td><b>{{ $surat->nama }}</b></td>
                </tr>
                <tr>
                    <td>Jenis Kelamin</td>
                    <td>:</td>
                    <td>{{ $surat->jenis_kelamin ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Umur</td>
                    <td>:</td>
                    <td>{{ $surat->umur ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>:</td>
                    <td>{{ $surat->alamat ?? '-' }}</td>
                </tr>
            </table>

            @if ($detail['alasan_rujukan'] ?? null)
                <div class="isi-surat">
                    <b>Alasan Rujukan:</b><br>{{ $detail['alasan_rujukan'] }}
                </div>
            @endif
        @else
            <div class="judul-surat">
                <h3>{{ strtoupper($detail['judul_surat'] ?? 'SURAT KETERANGAN') }}</h3>
                <p>No: {{ $nomor }}</p>
            </div>

            <table class="table-identitas">
                <tr>
                    <td width="30%">Nama Lengkap</td>
                    <td width="3%">:</td>
                    <td><b>{{ $surat->nama }}</b></td>
                </tr>
                @if ($surat->jenis_kelamin)
                    <tr>
                        <td>Jenis Kelamin</td>
                        <td>:</td>
                        <td>{{ $surat->jenis_kelamin }}</td>
                    </tr>
                @endif
                @if ($surat->umur)
                    <tr>
                        <td>Umur</td>
                        <td>:</td>
                        <td>{{ $surat->umur }}</td>
                    </tr>
                @endif
                @if ($surat->pekerjaan)
                    <tr>
                        <td>Pekerjaan</td>
                        <td>:</td>
                        <td>{{ $surat->pekerjaan }}</td>
                    </tr>
                @endif
                @if ($surat->alamat)
                    <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td>{{ $surat->alamat }}</td>
                    </tr>
                @endif
                @if ($surat->keperluan)
                    <tr>
                        <td>Keperluan</td>
                        <td>:</td>
                        <td>{{ $surat->keperluan }}</td>
                    </tr>
                @endif
            </table>

            <div class="isi-surat">{!! nl2br(e($detail['isi_surat'] ?? '-')) !!}</div>
        @endif

        <div class="isi-surat" style="margin-top: 20px;">
            Demikian surat keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.
        </div>

        <div class="footer-ttd">
            <p style="{{ ($surat->dokter->signature_path ?? null) ? 'margin-bottom:5px' : '' }}">
                {{ $company_city->value ?? '' }}, {{ \Carbon\Carbon::parse($surat->created_at)->translatedFormat('d F Y') }}<br>
                Dokter,</p>
            @if ($surat->dokter->signature_path ?? null)
                <img src="{{ request()->boolean('pdf') ? public_path($surat->dokter->signature_path) : url($surat->dokter->signature_path) }}"
                    style="max-height:55px;display:block;margin:0 auto 4px auto">
            @endif
            <div class="nama-dokter">{{ $surat->dokter->fullname ?? '-' }}</div>
            <div style="font-size: 11px; margin-top: 2px;">NIK/SIP: {{ $surat->dokter->nik ?? '-' }}</div>
        </div>
    </div>
</body>

</html>
