<!DOCTYPE html>
<html lang="id">
<?php
use App\Models\Setting;
$company_name = Setting::where('parameter', 'company_name')->first()->value ?? 'Klinik';
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Pasien - {{ $company_name }}</title>
    <link href="{{ url('') }}/assets/favicon.ico" rel="icon" type="image/x-icon">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            background: #3a57e8;
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .login-card {
            background: #fff;
            border-radius: 12px;
            padding: 28px 24px;
            width: 100%;
            max-width: 380px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .2);
        }

        .login-card h1 {
            font-size: 18px;
            margin: 0 0 4px;
            text-align: center;
            color: #1f2937;
        }

        .login-card p.subtitle {
            text-align: center;
            color: #6b7280;
            font-size: 13px;
            margin: 0 0 20px;
        }

        label {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            display: block;
            margin-bottom: 4px;
        }

        input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 14px;
        }

        button {
            width: 100%;
            padding: 11px;
            border: none;
            border-radius: 8px;
            background: #3a57e8;
            color: #fff;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
        }

        .error-box {
            background: #fee2e2;
            color: #991b1b;
            padding: 10px 12px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 14px;
        }

        .help-text {
            text-align: center;
            font-size: 11px;
            color: #9ca3af;
            margin-top: 16px;
        }
    </style>
</head>

<body>
    <div class="login-card">
        <h1>{{ $company_name }}</h1>
        <p class="subtitle">Portal Pasien — Lihat riwayat kunjungan, resep, tagihan, dan booking jadwal dokter.</p>

        @if ($errors->any())
            <div class="error-box">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ url('portal/login') }}">
            @csrf
            <label for="norm">No. Rekam Medis (RM)</label>
            <input type="text" name="norm" id="norm" value="{{ old('norm') }}" placeholder="Contoh: JD0010001" required>

            <label for="tanggal_lahir">Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required>

            <button type="submit">Masuk</button>
        </form>

        <p class="help-text">No. RM bisa dilihat di kartu pasien Anda. Hubungi klinik kalau lupa.</p>
    </div>
</body>

</html>
