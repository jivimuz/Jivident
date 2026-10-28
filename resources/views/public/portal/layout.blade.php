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
            background: #f4f6f9;
            margin: 0;
            color: #1f2937;
        }

        header.portal-header {
            background: #3a57e8;
            color: #fff;
            padding: 16px 20px;
        }

        header.portal-header h1 {
            margin: 0;
            font-size: 18px;
        }

        header.portal-header p {
            margin: 2px 0 0;
            font-size: 13px;
            opacity: .85;
        }

        nav.portal-nav {
            display: flex;
            overflow-x: auto;
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
        }

        nav.portal-nav a {
            flex: 0 0 auto;
            padding: 12px 16px;
            text-decoration: none;
            color: #4b5563;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
            border-bottom: 3px solid transparent;
        }

        nav.portal-nav a.active {
            color: #3a57e8;
            border-bottom-color: #3a57e8;
        }

        main.portal-main {
            padding: 16px;
            max-width: 720px;
            margin: 0 auto;
        }

        .portal-card {
            background: #fff;
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 14px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .06);
        }

        .portal-card h3 {
            margin: 0 0 10px;
            font-size: 15px;
        }

        .portal-muted {
            color: #6b7280;
            font-size: 12px;
        }

        .portal-badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
        }

        .portal-badge.lunas {
            background: #dcfce7;
            color: #166534;
        }

        .portal-badge.belum {
            background: #fee2e2;
            color: #991b1b;
        }

        .portal-btn {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 8px;
            border: none;
            background: #3a57e8;
            color: #fff;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
        }

        .portal-btn.outline {
            background: transparent;
            color: #dc2626;
            border: 1px solid #dc2626;
        }

        .portal-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13px;
        }

        .portal-row:last-child {
            border-bottom: none;
        }
    </style>
    @yield('style')
</head>

<body>
    <header class="portal-header">
        <h1>{{ $company_name }}</h1>
        <p>Portal Pasien — {{ $patient->nama ?? '' }} ({{ $patient->norm ?? '' }})</p>
    </header>

    <nav class="portal-nav">
        <a href="{{ url('portal') }}" class="{{ request()->is('portal') ? 'active' : '' }}">Beranda</a>
        <a href="{{ url('portal/riwayat') }}" class="{{ request()->is('portal/riwayat') ? 'active' : '' }}">Riwayat</a>
        <a href="{{ url('portal/resep') }}" class="{{ request()->is('portal/resep') ? 'active' : '' }}">Resep</a>
        <a href="{{ url('portal/tagihan') }}" class="{{ request()->is('portal/tagihan') ? 'active' : '' }}">Tagihan</a>
        <a href="{{ url('portal/booking') }}" class="{{ request()->is('portal/booking*') ? 'active' : '' }}">Booking</a>
    </nav>

    <main class="portal-main">
        @yield('content')

        <form method="POST" action="{{ url('portal/logout') }}" style="margin-top:10px">
            @csrf
            <button type="submit" class="portal-btn outline" style="width:100%">Keluar</button>
        </form>
    </main>

    @yield('script')
</body>

</html>
