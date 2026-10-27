<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Resep - {{ $prescription->no_resep ?? 'RSP-000' }}</title>
    <style>
        /* CSS Khusus Print & Layout Kertas */
        @page {
            size: A5 portrait;
            /* Ukuran standar kertas resep */
            margin: 10mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
            /* Terlihat abu-abu di layar, putih di kertas */
        }

        .print-container {
            background-color: #fff;
            width: 148mm;
            /* Lebar A5 */
            min-height: 210mm;
            /* Tinggi A5 */
            margin: 20px auto;
            padding: 15px 20px;
            box-sizing: border-box;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        /* Header Klinik/RS */
        .kop-surat {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .kop-surat h2 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .kop-surat p {
            margin: 2px 0;
            font-size: 11px;
        }

        /* Info Dokter & Pasien */
        .info-header {
            width: 100%;
            margin-bottom: 15px;
            font-size: 12px;
        }

        .info-header td {
            vertical-align: top;
            padding: 2px;
        }

        .patient-box {
            border: 1px solid #000;
            padding: 8px;
            margin-bottom: 15px;
        }

        /* Simbol R/ dan List Obat */
        .r-symbol {
            font-family: "Times New Roman", Times, serif;
            font-size: 24px;
            font-weight: bold;
            font-style: italic;
            float: left;
            width: 30px;
            margin-top: -5px;
        }

        .r-block {
            margin-bottom: 15px;
            clear: both;
            overflow: hidden;
        }

        .r-content {
            margin-left: 35px;
            font-size: 13px;
            line-height: 1.4;
        }

        .r-signa {
            font-style: italic;
            margin-top: 3px;
        }

        .r-line {
            border-bottom: 1px dashed #ccc;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        /* Footer & Tanda Tangan */
        .footer-ttd {
            margin-top: 40px;
            text-align: right;
        }

        .footer-ttd p {
            margin: 0 0 50px 0;
        }

        .footer-ttd .nama-dokter {
            font-weight: bold;
            text-decoration: underline;
        }

        /* Tombol Print (Hanya tampil di layar) */
        .btn-print-wrapper {
            text-align: center;
            margin: 20px 0;
        }

        .btn-print {
            padding: 10px 20px;
            background-color: #3a57e8;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
        }

        .btn-print:hover {
            background-color: #2b45c2;
        }

        /* Hilangkan elemen yang tidak perlu saat dicetak */
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
        <button class="btn-print" onclick="window.print()"><i class="iconoir-printer"></i> Cetak Resep Sekarang</button>
        <a class="btn-print" style="background-color:#0d6efd; margin-left:10px; text-decoration:none; display:inline-block;"
            href="{{ request()->fullUrlWithQuery(['pdf' => 1]) }}">Unduh PDF</a>
        <button class="btn-print" style="background-color:#6c757d; margin-left:10px;"
            onclick="window.close()">Tutup</button>
    </div>

    <div class="print-container">
        <!-- KOP SURAT -->
        <div class="kop-surat">
            <h2>{{ $company_name->value ?? '' }}</h2>
            <p>{{ $company_address->value ?? '' }}</p>
        </div>

        <!-- INFO DOKTER & TANGGAL -->
        <table class="info-header">
            <tr>
                <td width="15%">Dokter</td>
                <td width="2%">:</td>
                <td width="43%"><b>{{ $patient->dokter_name ?? '-' }}</b></td>
                <td width="15%">Tanggal</td>
                <td width="2%">:</td>
                <td width="23%">
                    {{ \Carbon\Carbon::parse($patient->tgldiperiksa ?? now())->translatedFormat('d F Y') }}
                </td>
            </tr>
            <tr>
                <td>Ruangan</td>
                <td>:</td>
                <td>{{ $patient->ruangan ?? 'Poli Umum' }}</td>
                <td colspan="3">
            </tr>
        </table>

        <hr style="border-top: 1px solid #000; margin-bottom: 20px;">

        <!-- DAFTAR OBAT (R/) -->
        <div style="min-height: 150px;">
            @if (isset($data) && count($data) > 0)
                @foreach ($data as $item)
                    <div class="r-block">
                        <div class="r-symbol">R/<span style="font-size: 10px">{{ $item['r_num'] }}</span></div>
                        <div class="r-content">

                            <!-- JIKA OBAT NON-RACIKAN -->
                            @if ($item['type'] == 'non-racik')
                                <strong>{{ $item['obat_name'] }}</strong>
                                <span style="float: right;">Qty. {{ $item['qty'] }}</span>
                                <div class="r-signa">{{ $item['signa'] }}</div>

                                <!-- JIKA OBAT RACIKAN -->
                            @elseif($item['type'] == 'racik')
                                <!-- Komposisi Racikan -->
                                @if (!empty($item['ingredients']))
                                    @foreach ($item['ingredients'] as $ing)
                                        <div>{{ $ing['name'] ?? '-' }} &mdash; {{ $ing['qty'] ?? 0 }}</div>
                                    @endforeach
                                @endif

                                <div style="margin-top: 5px;">
                                    <em>m.f. {{ $item['bentuk_sediaan'] ?? '-' }}</em>
                                    <span style="float: right;">Qty. {{ $item['qty_buat'] ?? 0 }}</span>
                                </div>
                                <div class="r-signa">{{ $item['signa'] }}</div>
                            @endif

                        </div>
                    </div>
                @endforeach
            @else
                <div style="text-align: center; color: #999; margin-top: 50px;">
                    <i>- Resep Kosong -</i>
                </div>
            @endif
        </div>

        <!-- GARIS PENUTUP RESEP -->
        <div class="r-line"></div>

        <!-- INFO PASIEN DI BAWAH (Sesuai standar penulisan resep) -->
        <div class="patient-box">
            <table width="100%" style="font-size: 12px;">
                <tr>
                    <td width="15%">Pro (Nama)</td>
                    <td width="2%">:</td>
                    <td><b>{{ $patient->nama ?? '-' }}</b></td>
                </tr>
                <tr>
                    <td>Umur</td>
                    <td>:</td>
                    <td>{{ $patient->tgllahir ? countAge($patient->tgllahir) : '-' }} Tahun</td>
                </tr>
                <tr>
                    <td>No. RM</td>
                    <td>:</td>
                    <td>{{ $patient->norm ?? '00-12-34-56' }}</td>
                </tr>
            </table>
        </div>

        <!-- TANDA TANGAN -->
        <div class="footer-ttd">
            <p>Ttd / Paraf Dokter,</p>
            <div class="nama-dokter">{{ $patient->dokter_name ?? '-' }}</div>
            <div style="font-size: 10px; margin-top: 2px;">SIP: {{ $patient->dokter_nik ?? '-' }}</div>
        </div>

        <div style="text-align: center; font-style: italic; font-size: 11px; margin-top: 30px;">
            "Semoga Lekas Sembuh"
        </div>

    </div>

    <script>
        // Opsional: Otomatis memicu dialog print saat halaman dimuat
        window.onload = function() {
            // window.print();
        }
    </script>
</body>

</html>
