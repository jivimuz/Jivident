<!DOCTYPE html>
<html lang="id">
<?php
use App\Models\Setting;
$company_name = Setting::where('parameter', 'company_name')->first()->value ?? 'Klinik';
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#2F49C7">
    <title>Portal Pasien - {{ $company_name }}</title>
    <link href="{{ url('') }}/assets/favicon.ico" rel="icon" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Lexend:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --paper: #F6F4EF;
            --paper-raised: #FFFFFF;
            --ink: #23262B;
            --ink-soft: #6B6F76;
            --line: #E7E2D8;
            --primary: #2F49C7;
            --primary-dim: #4A5FD1;
            --primary-soft: #E8EAFB;
            --sage: #4C7A67;
            --sage-soft: #E3EEE8;
            --crimson: #AE3F3C;
            --crimson-soft: #F5E1E0;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
        }

        body {
            font-family: 'Lexend', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            background: var(--paper);
            color: var(--ink);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            position: relative;
            overflow-x: hidden;
        }

        body::before,
        body::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            filter: blur(2px);
            z-index: 0;
        }

        body::before {
            width: 320px;
            height: 320px;
            background: var(--primary-soft);
            top: -120px;
            right: -100px;
        }

        body::after {
            width: 220px;
            height: 220px;
            background: var(--sage-soft);
            bottom: -80px;
            left: -70px;
        }

        .login-card {
            position: relative;
            z-index: 1;
            background: var(--paper-raised);
            border: 1px solid var(--line);
            border-radius: 20px;
            padding: 30px 26px 26px;
            width: 100%;
            max-width: 380px;
        }

        .login-mark {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: var(--primary);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }

        .login-card h1 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 19px;
            font-weight: 600;
            margin: 0 0 4px;
            text-align: center;
            letter-spacing: -0.01em;
        }

        .login-card p.subtitle {
            text-align: center;
            color: var(--ink-soft);
            font-size: 13px;
            line-height: 1.5;
            margin: 0 0 22px;
        }

        label {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--ink);
            display: block;
            margin-bottom: 5px;
        }

        input {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid var(--line);
            border-radius: 10px;
            font-size: 14px;
            margin-bottom: 15px;
            background: var(--paper);
            color: var(--ink);
            font-family: inherit;
        }

        input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-soft);
            background: var(--paper-raised);
        }

        button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 10px;
            background: var(--primary);
            color: #fff;
            font-weight: 600;
            font-size: 14.5px;
            cursor: pointer;
            font-family: inherit;
        }

        button:hover {
            background: var(--primary-dim);
        }

        button:focus-visible {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }

        .error-box {
            background: var(--crimson-soft);
            color: var(--crimson);
            padding: 10px 12px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 15px;
        }

        .help-text {
            text-align: center;
            font-size: 11.5px;
            color: var(--ink-soft);
            margin-top: 18px;
            line-height: 1.5;
        }
    </style>
</head>

<body>
    <div class="login-card">
        <div class="login-mark">
            <svg width="22" height="22" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M10 2.5 3 5.5v4c0 5 3 7.5 7 8 4-.5 7-3 7-8v-4L10 2.5Z" />
                <path d="M10 7v6M7 10h6" />
            </svg>
        </div>
        <h1>{{ $company_name }}</h1>
        <p class="subtitle">Portal pasien — lihat riwayat kunjungan, resep, tagihan, dan atur jadwal dokter Anda.</p>

        @if ($errors->any())
            <div class="error-box">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ url('portal/login') }}">
            @csrf
            <label for="norm">No. Rekam Medis (RM)</label>
            <input type="text" name="norm" id="norm" value="{{ old('norm') }}" placeholder="Contoh: JD0010001" required>

            <label for="tanggal_lahir">Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required>

            <button type="submit">Masuk ke Portal</button>
        </form>

        <p class="help-text">No. RM bisa dilihat di kartu pasien Anda.<br>Hubungi klinik kalau lupa.</p>
    </div>
</body>

</html>
