<!DOCTYPE html>
<html lang="id">
<?php
use App\Models\Setting;
$company_name = Setting::where('parameter', 'company_name')->first()->value ?? 'Klinik';

$hour = now()->hour;
$greeting = $hour < 11 ? 'pagi' : ($hour < 15 ? 'siang' : ($hour < 18 ? 'sore' : 'malam'));
$firstName = isset($patient->nama) ? trim(explode(' ', $patient->nama)[0]) : '';

$isHome = request()->is('portal');
$pageTitle = match (true) {
    request()->is('portal/riwayat') => 'Riwayat Kunjungan',
    request()->is('portal/resep') => 'Riwayat Resep',
    request()->is('portal/tagihan') => 'Riwayat Tagihan',
    request()->is('portal/booking*') => 'Booking Dokter',
    default => $company_name,
};
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
            --clay: #BD7A3C;
            --clay-soft: #F6E8D6;
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
            -webkit-font-smoothing: antialiased;
        }

        .pt-display {
            font-family: 'Space Grotesk', 'Lexend', sans-serif;
        }

        a {
            color: inherit;
        }

        button {
            font-family: inherit;
        }

        /* Header */
        .pt-header {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 16px 18px;
            background: var(--paper-raised);
            border-bottom: 1px solid var(--line);
            position: sticky;
            top: 0;
            z-index: 20;
        }

        .pt-header-info {
            flex: 1 1 auto;
            display: flex;
            flex-direction: column;
            gap: 1px;
            min-width: 0;
        }

        .pt-header-clinic {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 600;
            font-size: 16px;
            letter-spacing: -0.01em;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .pt-header-greeting {
            font-size: 12.5px;
            color: var(--ink-soft);
        }

        .pt-icon-btn {
            flex-shrink: 0;
            width: 40px;
            height: 40px;
            border-radius: 12px;
            border: 1px solid var(--line);
            background: var(--paper-raised);
            color: var(--ink-soft);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            padding: 0;
        }

        .pt-icon-btn:hover {
            background: var(--primary-soft);
            color: var(--primary);
            border-color: var(--primary-soft);
        }

        .pt-icon-btn--danger:hover {
            background: var(--crimson-soft);
            color: var(--crimson);
            border-color: var(--crimson-soft);
        }

        .pt-icon-btn:focus-visible {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }

        /* Main */
        main.portal-main {
            padding: 18px 16px calc(96px + env(safe-area-inset-bottom));
            max-width: 560px;
            margin: 0 auto;
        }

        .pt-section-head {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            margin: 4px 2px 12px;
        }

        .pt-section-head h2 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 17px;
            font-weight: 600;
            margin: 0;
            letter-spacing: -0.01em;
        }

        /* Cards & lists */
        .portal-card {
            background: var(--paper-raised);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 14px;
        }

        .portal-card h3 {
            margin: 0 0 10px;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 15px;
            font-weight: 600;
        }

        .portal-muted {
            color: var(--ink-soft);
            font-size: 12.5px;
        }

        .portal-badge {
            display: inline-block;
            padding: 3px 11px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
        }

        .portal-badge.lunas {
            background: var(--sage-soft);
            color: var(--sage);
        }

        .portal-badge.belum {
            background: var(--crimson-soft);
            color: var(--crimson);
        }

        .portal-btn {
            display: inline-block;
            padding: 10px 18px;
            border-radius: 12px;
            border: none;
            background: var(--primary);
            color: #fff;
            font-weight: 600;
            font-size: 13.5px;
            cursor: pointer;
            text-decoration: none;
            font-family: inherit;
        }

        .portal-btn:disabled {
            opacity: .5;
            cursor: not-allowed;
        }

        .portal-btn.outline {
            background: transparent;
            color: var(--crimson);
            border: 1px solid var(--crimson-soft);
        }

        .portal-row {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            padding: 8px 0;
            border-bottom: 1px solid var(--line);
            font-size: 13.5px;
        }

        .portal-row:last-child {
            border-bottom: none;
        }

        .pt-empty {
            text-align: center;
            padding: 30px 16px;
            color: var(--ink-soft);
            font-size: 13.5px;
        }

        .pt-empty svg {
            margin-bottom: 10px;
            color: var(--line);
        }

        /* Bottom nav */
        .pt-bottomnav {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            background: var(--paper-raised);
            border-top: 1px solid var(--line);
            padding: 8px 6px calc(8px + env(safe-area-inset-bottom));
            z-index: 30;
        }

        .pt-bottomnav a {
            flex: 1 1 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
            padding: 6px 2px;
            text-decoration: none;
            color: var(--ink-soft);
            font-size: 11px;
            font-weight: 500;
            border-radius: 10px;
        }

        .pt-bottomnav a svg {
            width: 21px;
            height: 21px;
        }

        .pt-bottomnav a.active {
            color: var(--primary);
            font-weight: 600;
        }

        .pt-bottomnav a.active span {
            position: relative;
        }

        @yield('style')
    </style>
</head>

<body>
    <header class="pt-header">
        @if (!$isHome)
            <a href="{{ url('portal') }}" class="pt-icon-btn" aria-label="Kembali ke Beranda" title="Kembali">
                <svg width="19" height="19" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12.5 4.5 6 10l6.5 5.5" />
                </svg>
            </a>
        @endif
        <div class="pt-header-info">
            @if ($isHome)
                <span class="pt-header-clinic">{{ $company_name }}</span>
                <span class="pt-header-greeting">Selamat {{ $greeting }}@if($firstName), {{ $firstName }}@endif</span>
            @else
                <span class="pt-header-clinic">{{ $pageTitle }}</span>
                <span class="pt-header-greeting">{{ $company_name }}</span>
            @endif
        </div>
        <form method="POST" action="{{ url('portal/logout') }}">
            @csrf
            <button type="submit" class="pt-icon-btn pt-icon-btn--danger" aria-label="Keluar" title="Keluar">
                <svg width="19" height="19" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M8 3H5a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h3" />
                    <path d="M13 14l4-4-4-4" />
                    <line x1="17" y1="10" x2="7.5" y2="10" />
                </svg>
            </button>
        </form>
    </header>

    <main class="portal-main">
        @yield('content')
    </main>

    <nav class="pt-bottomnav">
        <a href="{{ url('portal') }}" class="{{ request()->is('portal') ? 'active' : '' }}">
            <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                stroke-linejoin="round">
                <path d="M3 10.5 10 4l7 6.5" />
                <path d="M5 9.5V17a1 1 0 0 0 1 1h3v-4.5h2V18h3a1 1 0 0 0 1-1V9.5" />
            </svg>
            <span>Beranda</span>
        </a>
        <a href="{{ url('portal/riwayat') }}" class="{{ request()->is('portal/riwayat') ? 'active' : '' }}">
            <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                stroke-linejoin="round">
                <circle cx="10" cy="10" r="7" />
                <path d="M10 6.5V10l3 2" />
            </svg>
            <span>Riwayat</span>
        </a>
        <a href="{{ url('portal/resep') }}" class="{{ request()->is('portal/resep') ? 'active' : '' }}">
            <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                stroke-linejoin="round">
                <g transform="rotate(45 10 10)">
                    <rect x="3" y="7" width="14" height="6" rx="3" />
                    <line x1="10" y1="7" x2="10" y2="13" />
                </g>
            </svg>
            <span>Resep</span>
        </a>
        <a href="{{ url('portal/tagihan') }}" class="{{ request()->is('portal/tagihan') ? 'active' : '' }}">
            <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                stroke-linejoin="round">
                <path d="M5 3h10v14l-2-1.2L11 17l-2-1.2L7 17l-2-1.2V3Z" />
                <line x1="7.5" y1="7" x2="12.5" y2="7" />
                <line x1="7.5" y1="10" x2="12.5" y2="10" />
            </svg>
            <span>Tagihan</span>
        </a>
        <a href="{{ url('portal/booking') }}" class="{{ request()->is('portal/booking*') ? 'active' : '' }}">
            <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                stroke-linejoin="round">
                <rect x="3" y="4.5" width="14" height="12" rx="2" />
                <line x1="3" y1="8" x2="17" y2="8" />
                <line x1="6.5" y1="3" x2="6.5" y2="6" />
                <line x1="13.5" y1="3" x2="13.5" y2="6" />
            </svg>
            <span>Booking</span>
        </a>
    </nav>

    @yield('script')
</body>

</html>
