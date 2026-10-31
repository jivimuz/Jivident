<!DOCTYPE html>
<html lang="en">
<?php
use App\Models\Setting;

$company_name = Setting::where('parameter', 'company_name')->first()->value ?: 'JiviDent';
$company_city = Setting::where('parameter', 'company_city')->first()->value ?: '';
$company_address = Setting::where('parameter', 'company_address')->first()->value ?: '';
// Same resolution pattern as navbar.blade.php / layout/main.blade.php: a
// clinic's own uploaded logo if set, otherwise the product's default mark.
$logo = Setting::where('parameter', 'company_logo')->first()->value ?: url('/assets/logo.png');

// This landing page defaults to English (server-rendered, so it's what
// first-time visitors and crawlers see) with a client-side toggle back to
// the original Indonesian copy — scoped to this page only, not the rest of
// the app, which stays Indonesian everywhere else.
$pageTitleEn = 'JiviDent — All-in-One Clinic & Dental Practice Management System';
$pageDescriptionEn = "{$company_name} brings patient registration, queue, medical records, billing, pharmacy, payroll, and a patient portal together in one system — built for how clinics and dental practices in Indonesia actually work.";
$pageTitleId = 'JiviDent — Sistem Manajemen Klinik & Praktek Dokter Gigi All-in-One';
$pageDescriptionId = "{$company_name} menyatukan pendaftaran pasien, antrian, rekam medis, billing, farmasi, payroll, hingga portal pasien dalam satu sistem — dibangun untuk cara kerja klinik dan praktek dokter gigi di Indonesia.";

$pageTitle = $pageTitleEn;
$pageDescription = $pageDescriptionEn;
$canonicalUrl = url()->current();
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#2F49C7">

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="robots" content="index, follow">
    <meta name="author" content="{{ $company_name }}">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <link href="{{ url('') }}/assets/favicon.ico" rel="icon" type="image/x-icon">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ $logo }}">
    <meta property="og:locale" content="en_US">
    <meta property="og:locale:alternate" content="id_ID">
    <meta property="og:site_name" content="{{ $company_name }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image" content="{{ $logo }}">

    <!-- Structured data: lets search engines show this as software, not a generic page -->
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'SoftwareApplication',
            'name' => 'JiviDent',
            'description' => $pageDescription,
            'applicationCategory' => 'BusinessApplication',
            'applicationSubCategory' => 'Healthcare Practice Management',
            'operatingSystem' => 'Web',
            'url' => $canonicalUrl,
            'image' => $logo,
            'inLanguage' => 'en-US',
        ], JSON_UNESCAPED_SLASHES) !!}
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Lexend:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --paper: #FAFAF7;
            --paper-raised: #FFFFFF;
            --ink: #1D2230;
            --ink-soft: #5B6072;
            --line: #E5E2D8;
            --indigo: #2F49C7;
            --indigo-deep: #1C2E8C;
            --indigo-soft: #E9ECFB;
            --mint: #1F9D7C;
            --mint-soft: #E2F3EE;
            --amber: #CF8A2E;
            --amber-soft: #FBF0DC;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
        }

        html {
            scroll-behavior: smooth;
        }

        @media (prefers-reduced-motion: reduce) {
            html {
                scroll-behavior: auto;
            }
        }

        body {
            font-family: 'Lexend', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            background: var(--paper);
            color: var(--ink);
            -webkit-font-smoothing: antialiased;
            line-height: 1.55;
        }

        img {
            max-width: 100%;
        }

        a {
            color: inherit;
        }

        .wrap {
            max-width: 1120px;
            margin: 0 auto;
            padding: 0 24px;
        }

        .display {
            font-family: 'Space Grotesk', 'Lexend', sans-serif;
            letter-spacing: -0.01em;
        }

        :focus-visible {
            outline: 2px solid var(--indigo);
            outline-offset: 3px;
        }

        @media (prefers-reduced-motion: reduce) {
            * {
                animation: none !important;
                transition: none !important;
            }
        }

        /* Nav */
        .nav {
            position: sticky;
            top: 0;
            z-index: 20;
            background: rgba(250, 250, 247, 0.9);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid var(--line);
        }

        .nav .wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 14px;
            padding-bottom: 14px;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .nav-brand img {
            height: 32px;
            width: auto;
        }

        .nav-brand span {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 600;
            font-size: 18px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 28px;
        }

        .nav-links a:not(.btn) {
            font-size: 14px;
            font-weight: 500;
            color: var(--ink-soft);
            text-decoration: none;
        }

        .nav-links a:not(.btn):hover {
            color: var(--ink);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 22px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14.5px;
            text-decoration: none;
            border: 1px solid transparent;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--indigo);
            color: #fff;
        }

        .btn-primary:hover {
            background: var(--indigo-deep);
        }

        .btn-outline {
            background: transparent;
            border-color: var(--line);
            color: var(--ink);
        }

        .btn-outline:hover {
            border-color: var(--indigo);
            color: var(--indigo);
        }

        .nav-cta {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .lang-switch {
            display: flex;
            border: 1px solid var(--line);
            border-radius: 999px;
            padding: 3px;
            background: var(--paper-raised);
            flex-shrink: 0;
        }

        .lang-btn {
            border: none;
            background: transparent;
            padding: 6px 12px;
            border-radius: 999px;
            font-family: 'Lexend', sans-serif;
            font-size: 12.5px;
            font-weight: 600;
            color: var(--ink-soft);
            cursor: pointer;
        }

        .lang-btn.active {
            background: var(--indigo);
            color: #fff;
        }

        /* Hero */
        .hero {
            padding: 72px 0 56px;
            overflow: hidden;
        }

        .hero .wrap {
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            gap: 56px;
            align-items: center;
        }

        .hero-copy {
            animation: rise 0.6s ease both;
        }

        @keyframes rise {
            from {
                opacity: 0;
                transform: translateY(14px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero-copy h1 {
            font-size: 44px;
            line-height: 1.12;
            margin: 0 0 18px;
            font-weight: 600;
        }

        .hero-copy p.lead {
            font-size: 17px;
            color: var(--ink-soft);
            max-width: 46ch;
            margin: 0 0 28px;
        }

        .hero-ctas {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .hero-portal-note {
            margin-top: 18px;
            font-size: 13.5px;
            color: var(--ink-soft);
        }

        .hero-portal-note a {
            color: var(--indigo);
            font-weight: 600;
            text-decoration: none;
        }

        /* Patient-flow visual: a genuine sequence (daftar -> antri -> periksa ->
           bayar), so step numbering here documents real order instead of
           decorating generic content. */
        .flow-card {
            background: var(--paper-raised);
            border: 1px solid var(--line);
            border-radius: 20px;
            padding: 26px 22px;
        }

        .flow-card .flow-label {
            font-size: 13px;
            color: var(--ink-soft);
            margin: 0 0 18px;
        }

        .flow-steps {
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        .flow-step {
            display: flex;
            gap: 14px;
            position: relative;
            padding-bottom: 22px;
        }

        .flow-step:last-child {
            padding-bottom: 0;
        }

        .flow-step::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 34px;
            bottom: 0;
            width: 2px;
            background: var(--line);
        }

        .flow-step:last-child::before {
            display: none;
        }

        .flow-num {
            flex-shrink: 0;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--indigo-soft);
            color: var(--indigo-deep);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 600;
            font-size: 14px;
            z-index: 1;
        }

        .flow-step.active .flow-num {
            background: var(--indigo);
            color: #fff;
        }

        .flow-text h3 {
            margin: 3px 0 2px;
            font-size: 15.5px;
            font-weight: 600;
        }

        .flow-text p {
            margin: 0;
            font-size: 13.5px;
            color: var(--ink-soft);
        }

        /* Capability strip */
        .strip {
            border-top: 1px solid var(--line);
            border-bottom: 1px solid var(--line);
            background: var(--paper-raised);
            padding: 26px 0;
        }

        .strip .wrap {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .strip-item {
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }

        .strip-item .dot {
            flex-shrink: 0;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-top: 7px;
            background: var(--mint);
        }

        .strip-item p {
            margin: 0;
            font-size: 14px;
            color: var(--ink-soft);
        }

        .strip-item strong {
            color: var(--ink);
        }

        /* Demo video */
        .video-frame {
            position: relative;
            width: 100%;
            aspect-ratio: 16 / 9;
            border-radius: 18px;
            overflow: hidden;
            background: var(--ink);
            border: 1px solid var(--line);
        }

        .video-frame iframe {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }

        /* Section scaffolding */
        section.block {
            padding: 72px 0;
        }

        .section-head {
            max-width: 56ch;
            margin: 0 0 44px;
        }

        .section-head h2 {
            font-size: 30px;
            margin: 0 0 10px;
            font-weight: 600;
        }

        .section-head p {
            color: var(--ink-soft);
            font-size: 15.5px;
            margin: 0;
        }

        /* Alternating feature rows */
        .feature-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 48px;
            align-items: center;
            padding: 36px 0;
            border-bottom: 1px solid var(--line);
        }

        .feature-row:last-child {
            border-bottom: none;
        }

        .feature-row.reverse .feature-text {
            order: 2;
        }

        .feature-row.reverse .feature-visual {
            order: 1;
        }

        .feature-text h3 {
            font-size: 22px;
            margin: 0 0 10px;
            font-weight: 600;
        }

        .feature-text p {
            color: var(--ink-soft);
            font-size: 15px;
            margin: 0 0 14px;
            max-width: 42ch;
        }

        .feature-text ul {
            margin: 0;
            padding: 0;
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .feature-text li {
            font-size: 14px;
            color: var(--ink);
            padding-left: 18px;
            position: relative;
        }

        .feature-text li::before {
            content: '';
            position: absolute;
            left: 0;
            top: 8px;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--indigo);
        }

        .feature-visual {
            background: var(--indigo-soft);
            border-radius: 18px;
            padding: 28px;
            min-height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .feature-visual .metric {
            text-align: center;
        }

        .feature-visual .metric .num {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 40px;
            font-weight: 700;
            color: var(--indigo-deep);
            line-height: 1;
        }

        .feature-visual .metric .cap {
            font-size: 13.5px;
            color: var(--ink-soft);
            margin-top: 8px;
            max-width: 26ch;
        }

        .feature-visual.mint {
            background: var(--mint-soft);
        }

        .feature-visual.mint .num {
            color: #146B54;
        }

        .feature-visual.amber {
            background: var(--amber-soft);
        }

        .feature-visual.amber .num {
            color: #8C5B10;
        }

        /* Highlighted single feature: patient portal */
        .spotlight {
            background: linear-gradient(135deg, var(--indigo) 0%, var(--indigo-deep) 100%);
            border-radius: 24px;
            padding: 48px;
            color: #fff;
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 36px;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .spotlight::before {
            content: '';
            position: absolute;
            top: -60px;
            right: -60px;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
        }

        .spotlight h3 {
            font-size: 26px;
            margin: 0 0 12px;
            font-weight: 600;
            position: relative;
            z-index: 1;
        }

        .spotlight p {
            margin: 0 0 20px;
            opacity: 0.9;
            font-size: 15px;
            max-width: 42ch;
            position: relative;
            z-index: 1;
        }

        .spotlight .btn-outline {
            border-color: rgba(255, 255, 255, 0.4);
            color: #fff;
            position: relative;
            z-index: 1;
        }

        .spotlight .btn-outline:hover {
            border-color: #fff;
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
        }

        .spotlight-card {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 16px;
            padding: 20px;
            position: relative;
            z-index: 1;
        }

        .spotlight-card .row {
            display: flex;
            justify-content: space-between;
            padding: 9px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.14);
            font-size: 13.5px;
        }

        .spotlight-card .row:last-child {
            border-bottom: none;
        }

        .spotlight-card .row span:first-child {
            opacity: 0.75;
        }

        /* Quiet grid for remaining capabilities */
        .mini-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1px;
            background: var(--line);
            border: 1px solid var(--line);
            border-radius: 16px;
            overflow: hidden;
        }

        .mini-grid .mini {
            background: var(--paper-raised);
            padding: 24px;
        }

        .mini h4 {
            font-size: 15px;
            margin: 0 0 6px;
            font-weight: 600;
        }

        .mini p {
            font-size: 13.5px;
            color: var(--ink-soft);
            margin: 0;
        }

        /* CTA band */
        .cta-band {
            background: var(--ink);
            color: #fff;
            padding: 64px 0;
            text-align: center;
        }

        .cta-band h2 {
            font-size: 30px;
            margin: 0 0 12px;
            font-weight: 600;
        }

        .cta-band p {
            color: rgba(255, 255, 255, 0.72);
            margin: 0 0 28px;
            font-size: 15.5px;
        }

        .cta-band .btn-primary {
            background: #fff;
            color: var(--ink);
        }

        .cta-band .btn-primary:hover {
            background: var(--indigo-soft);
        }

        .cta-band .btn-outline {
            border-color: rgba(255, 255, 255, 0.3);
            color: #fff;
        }

        .cta-band .btn-outline:hover {
            border-color: #fff;
        }

        .cta-actions {
            display: flex;
            justify-content: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        /* Footer */
        footer {
            padding: 40px 0;
            border-top: 1px solid var(--line);
        }

        footer .wrap {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .footer-brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .footer-brand img {
            height: 24px;
        }

        .footer-brand div {
            font-size: 13px;
            color: var(--ink-soft);
        }

        .footer-brand strong {
            display: block;
            font-size: 14px;
            color: var(--ink);
            font-family: 'Space Grotesk', sans-serif;
        }

        footer nav {
            display: flex;
            gap: 20px;
        }

        footer nav a {
            font-size: 13.5px;
            color: var(--ink-soft);
            text-decoration: none;
        }

        footer nav a:hover {
            color: var(--indigo);
        }

        @media (max-width: 860px) {

            .hero .wrap,
            .feature-row,
            .spotlight {
                grid-template-columns: 1fr;
            }

            .feature-row.reverse .feature-text,
            .feature-row.reverse .feature-visual {
                order: initial;
            }

            .strip .wrap,
            .mini-grid {
                grid-template-columns: 1fr;
            }

            .nav-links {
                display: none;
            }

            .hero-copy h1 {
                font-size: 32px;
            }
        }
    </style>
</head>

<body>
    <nav class="nav">
        <div class="wrap">
            <a href="{{ url('/i') }}" class="nav-brand">
                <img src="{{ $logo }}" alt="Logo {{ $company_name }}">
            </a>
            <div class="nav-links">
                <a href="#fitur" data-en="Features" data-id="Fitur">Features</a>
                <a href="#layanan-lanjutan" data-en="Advanced Services" data-id="Layanan Lanjutan">Advanced
                    Services</a>
                <a href="{{ url('portal/login') }}" data-en="Patient Portal" data-id="Portal Pasien">Patient
                    Portal</a>
            </div>
            <div class="nav-cta">
                <div class="lang-switch" role="group" aria-label="Language / Bahasa">
                    <button type="button" class="lang-btn active" data-lang="en" aria-pressed="true">EN</button>
                    <button type="button" class="lang-btn" data-lang="id" aria-pressed="false">ID</button>
                </div>
                <a href="{{ route('login') }}" class="btn btn-primary" data-en="Log In to the App"
                    data-id="Masuk ke Aplikasi">Log In to the App</a>
            </div>
        </div>
    </nav>

    <header class="hero">
        <div class="wrap">
            <div class="hero-copy">
                <h1 class="display" data-en="One system, from check-in to cash in."
                    data-id="Satu sistem, dari pasien datang sampai kas masuk.">One system, from check-in to cash
                    in.</h1>
                <p class="lead"
                    data-en="{{ $company_name }} brings together registration, queue, medical records, pharmacy, billing, and payroll in one application — built for how clinics and dental practices in Indonesia actually work, not a generic template translated after the fact."
                    data-id="{{ $company_name }} menyatukan pendaftaran, antrian, rekam medis, farmasi, billing, hingga payroll dalam satu aplikasi — dibangun untuk cara kerja klinik dan praktek dokter gigi di Indonesia, bukan template generik yang diterjemahkan.">
                    {{ $company_name }} brings together registration, queue, medical records, pharmacy, billing, and
                    payroll in one application — built for how clinics and dental practices in Indonesia actually
                    work, not a generic template translated after the fact.</p>
                <div class="hero-ctas">
                    <a href="{{ route('login') }}" class="btn btn-primary" data-en="Log In to the App"
                        data-id="Masuk ke Aplikasi">Log In to the App</a>
                    <a href="#demo" class="btn btn-outline" data-en="Watch Demo (2 min)"
                        data-id="Tonton Demo (2 menit)">Watch Demo (2 min)</a>
                </div>
                <p class="hero-portal-note">
                    <span data-en="Looking to check your own history or book an appointment?"
                        data-id="Pasien ingin cek riwayat atau booking sendiri?">Looking to check your own history or
                        book an appointment?</span>
                    <a href="{{ url('portal/login') }}"><span data-en="Open the Patient Portal"
                            data-id="Masuk ke Portal Pasien">Open the Patient Portal</span></a>.
                </p>
            </div>

            <div class="flow-card" aria-label="Patient visit flow">
                <p class="flow-label display" data-en="One patient visit, start to finish"
                    data-id="Alur satu kunjungan pasien">One patient visit, start to finish</p>
                <div class="flow-steps">
                    <div class="flow-step active">
                        <div class="flow-num">1</div>
                        <div class="flow-text">
                            <h3 data-en="Check In" data-id="Daftar">Check In</h3>
                            <p data-en="Automatic medical record number, registration fee recorded instantly."
                                data-id="No. RM otomatis, biaya registrasi langsung tercatat.">Automatic medical
                                record number, registration fee recorded instantly.</p>
                        </div>
                    </div>
                    <div class="flow-step">
                        <div class="flow-num">2</div>
                        <div class="flow-text">
                            <h3 data-en="Queue" data-id="Antri">Queue</h3>
                            <p data-en="Public queue display, or triage-based queueing for the ER."
                                data-id="Papan antrian publik, atau antrian triase untuk IGD.">Public queue display,
                                or triage-based queueing for the ER.</p>
                        </div>
                    </div>
                    <div class="flow-step">
                        <div class="flow-num">3</div>
                        <div class="flow-text">
                            <h3 data-en="Examination" data-id="Periksa">Examination</h3>
                            <p data-en="Vitals, SOAPE notes, odontogram, ICD diagnosis, prescriptions."
                                data-id="Vital sign, SOAPE, odontogram, diagnosa ICD, resep.">Vitals, SOAPE notes,
                                odontogram, ICD diagnosis, prescriptions.</p>
                        </div>
                    </div>
                    <div class="flow-step">
                        <div class="flow-num">4</div>
                        <div class="flow-text">
                            <h3 data-en="Payment" data-id="Bayar">Payment</h3>
                            <p data-en="Bill calculated automatically, from procedures to prescriptions."
                                data-id="Tagihan terhitung otomatis dari tindakan sampai resep.">Bill calculated
                                automatically, from procedures to prescriptions.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="strip">
        <div class="wrap">
            <div class="strip-item">
                <span class="dot"></span>
                <p>
                    <strong><span data-en="FEFO stock tracking" data-id="Stok obat FEFO">FEFO stock
                            tracking</span></strong>
                    <span
                        data-en="— medicine closest to expiry goes out first, calculated automatically whenever a prescription is dispensed."
                        data-id="— yang lebih dulu kedaluwarsa, lebih dulu keluar, dihitung otomatis saat resep dipakai.">—
                        medicine closest to expiry goes out first, calculated automatically whenever a prescription
                        is dispensed.</span>
                </p>
            </div>
            <div class="strip-item">
                <span class="dot"></span>
                <p>
                    <strong><span data-en="BPJS & PPh21-ready payroll" data-id="Payroll siap BPJS & PPh21">BPJS &
                            PPh21-ready payroll</span></strong>
                    <span data-en="— built-in TER calculation (PMK 168/2023), not an Excel bolt-on."
                        data-id="— perhitungan TER (PMK 168/2023) bawaan, bukan tempelan Excel.">— built-in TER
                        calculation (PMK 168/2023), not an Excel bolt-on.</span>
                </p>
            </div>
            <div class="strip-item">
                <span class="dot"></span>
                <p>
                    <strong><span data-en="Multi-branch from day one" data-id="Multi-cabang dari awal">Multi-branch
                            from day one</span></strong>
                    <span
                        data-en="— each branch only ever sees its own data, not a feature bolted on later."
                        data-id="— tiap cabang hanya melihat datanya sendiri, bukan fitur tambahan belakangan.">—
                        each branch only ever sees its own data, not a feature bolted on later.</span>
                </p>
            </div>
        </div>
    </div>

    <section class="block" id="demo" style="padding-bottom:0">
        <div class="wrap">
            <div class="section-head">
                <h2 class="display" data-en="See it in action" data-id="Lihat alurnya langsung">See it in action</h2>
                <p data-en="Two minutes, from patient check-in to a finished bill."
                    data-id="Dua menit, dari pendaftaran pasien sampai tagihan selesai.">Two minutes, from patient
                    check-in to a finished bill.</p>
            </div>
            <div class="video-frame">
                <iframe src="https://www.youtube.com/embed/cBc9dCsd9mc" title="{{ $company_name }} Demo"
                    loading="lazy"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
            </div>
        </div>
    </section>

    <section class="block" id="fitur">
        <div class="wrap">
            <div class="section-head">
                <h2 class="display" data-en="One app for your entire clinic operation"
                    data-id="Satu aplikasi untuk seluruh operasional klinik">One app for your entire clinic
                    operation</h2>
                <p data-en="Every part is built around how clinics actually run day to day, not generic features forced to fit."
                    data-id="Tiap bagian dibangun berdasarkan cara kerja klinik sehari-hari, bukan fitur generik yang dipaksakan.">
                    Every part is built around how clinics actually run day to day, not generic features forced to
                    fit.</p>
            </div>

            <div class="feature-row">
                <div class="feature-text">
                    <h3 data-en="Registration & Medical Records" data-id="Pendaftaran & Rekam Medis">Registration &
                        Medical Records</h3>
                    <p data-en="From a brand-new patient to a complete visit history, all in one flow."
                        data-id="Dari pasien baru sampai riwayat kunjungan lengkap, semua dalam satu alur.">From a
                        brand-new patient to a complete visit history, all in one flow.</p>
                    <ul>
                        <li data-en="Vitals, SOAPE notes, and odontogram charts for adult & pediatric teeth"
                            data-id="Vital sign, catatan SOAPE, dan odontogram gigi dewasa & anak">Vitals, SOAPE
                            notes, and odontogram charts for adult & pediatric teeth</li>
                        <li data-en="ICD-9/ICD-10 diagnosis, prescriptions including compounded medicine"
                            data-id="Diagnosa ICD-9/ICD-10, resep obat termasuk racikan">ICD-9/ICD-10 diagnosis,
                            prescriptions including compounded medicine</li>
                        <li data-en="Informed consent & digital signatures for patients/doctors"
                            data-id="Informed consent & tanda tangan digital pasien/dokter">Informed consent &
                            digital signatures for patients/doctors</li>
                    </ul>
                </div>
                <div class="feature-visual">
                    <div class="metric">
                        <div class="num display">ICD-9/10</div>
                        <p class="cap"
                            data-en="Structured diagnosis, not free-text notes you can never find again"
                            data-id="Diagnosa terstruktur, bukan catatan bebas yang susah dicari lagi">Structured
                            diagnosis, not free-text notes you can never find again</p>
                    </div>
                </div>
            </div>

            <div class="feature-row reverse">
                <div class="feature-text">
                    <h3 data-en="Billing & Pharmacy" data-id="Billing & Farmasi">Billing & Pharmacy</h3>
                    <p data-en="Bills calculated automatically from procedures, diagnosis, lab, radiology, all the way to prescriptions."
                        data-id="Tagihan terhitung otomatis dari tindakan, diagnosa, lab, radiologi, sampai resep.">
                        Bills calculated automatically from procedures, diagnosis, lab, radiology, all the way to
                        prescriptions.</p>
                    <ul>
                        <li data-en="Pharmacy POS checkout with prices locked to the product master"
                            data-id="Kasir POS farmasi dengan harga terkunci ke master produk">Pharmacy POS checkout
                            with prices locked to the product master</li>
                        <li data-en="Supplier receive orders & stock, automatic FEFO stock deduction"
                            data-id="Receive order & stok supplier, potongan stok FEFO otomatis">Supplier receive
                            orders & stock, automatic FEFO stock deduction</li>
                        <li data-en="One ledger for every kind of transaction"
                            data-id="Ledger transaksi satu pintu untuk semua jenis pemasukan">One ledger for every
                            kind of transaction</li>
                    </ul>
                </div>
                <div class="feature-visual mint">
                    <div class="metric">
                        <div class="num display">FEFO</div>
                        <p class="cap"
                            data-en="Medicine closest to expiry gets used first — automatically, not checked one by one by hand"
                            data-id="Obat yang lebih dulu kedaluwarsa, lebih dulu dipakai — otomatis, bukan manual dicek satu-satu">
                            Medicine closest to expiry gets used first — automatically, not checked one by one by
                            hand</p>
                    </div>
                </div>
            </div>

            <div class="feature-row">
                <div class="feature-text">
                    <h3 data-en="Doctor Schedules & Booking" data-id="Jadwal Dokter & Booking">Doctor Schedules &
                        Booking</h3>
                    <p data-en="One practice room can be shared by several doctors at different times."
                        data-id="Satu ruang praktek bisa dipakai bergilir oleh beberapa dokter di jam yang berbeda.">
                        One practice room can be shared by several doctors at different times.</p>
                    <ul>
                        <li data-en="Booking slots generated automatically from a doctor's weekly schedule"
                            data-id="Slot booking digenerate otomatis dari jadwal mingguan dokter">Booking slots
                            generated automatically from a doctor's weekly schedule</li>
                        <li data-en="Patients can book their own appointment through the Patient Portal"
                            data-id="Pasien bisa booking sendiri lewat Portal Pasien">Patients can book their own
                            appointment through the Patient Portal</li>
                        <li data-en="Automatically catches it if two doctors' shifts clash in the same room"
                            data-id="Deteksi otomatis kalau dua dokter bertabrakan di ruang & jam yang sama">
                            Automatically catches it if two doctors' shifts clash in the same room</li>
                    </ul>
                </div>
                <div class="feature-visual amber">
                    <div class="metric">
                        <div class="num display" data-en="1 Room" data-id="1 Ruang">1 Room</div>
                        <p class="cap" data-en="Shared by several doctors without their schedules colliding"
                            data-id="Dipakai bergilir oleh beberapa dokter tanpa bertabrakan jadwal">Shared by
                            several doctors without their schedules colliding</p>
                    </div>
                </div>
            </div>

            <div class="feature-row reverse">
                <div class="feature-text">
                    <h3 data-en="HR & Payroll" data-id="Kepegawaian & Payroll">HR & Payroll</h3>
                    <p data-en="Work schedules, leave, and payroll in one place — not a separate spreadsheet."
                        data-id="Jadwal kerja, cuti, dan penggajian dalam satu tempat — bukan file Excel terpisah.">
                        Work schedules, leave, and payroll in one place — not a separate spreadsheet.</p>
                    <ul>
                        <li data-en="Employee leave requests & approvals" data-id="Pengajuan & approval cuti karyawan">
                            Employee leave requests & approvals</li>
                        <li data-en="Payroll with BPJS Kesehatan/Ketenagakerjaan and PPh21 (TER scheme)"
                            data-id="Payroll dengan BPJS Kesehatan/Ketenagakerjaan dan PPh21 (skema TER)">Payroll
                            with BPJS Kesehatan/Ketenagakerjaan and PPh21 (TER scheme)</li>
                        <li data-en="Employee work schedules per shift" data-id="Jadwal kerja karyawan per shift">
                            Employee work schedules per shift</li>
                    </ul>
                </div>
                <div class="feature-visual">
                    <div class="metric">
                        <div class="num display">TER</div>
                        <p class="cap"
                            data-en="PPh21's Tarif Efektif Rata-rata scheme (PMK 168/2023) built right in"
                            data-id="PPh21 skema Tarif Efektif Rata-rata (PMK 168/2023) sudah bawaan sistem">PPh21's
                            Tarif Efektif Rata-rata scheme (PMK 168/2023) built right in</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="block" id="layanan-lanjutan" style="padding-top:0">
        <div class="wrap">
            <div class="section-head">
                <h2 class="display" data-en="Ready for more than outpatient care"
                    data-id="Siap untuk layanan yang lebih luas">Ready for more than outpatient care</h2>
                <p data-en="Not just an outpatient clinic — supports small-to-mid hospital-style services too."
                    data-id="Bukan cuma klinik rawat jalan — mendukung layanan gaya rumah sakit skala kecil-menengah.">
                    Not just an outpatient clinic — supports small-to-mid hospital-style services too.</p>
            </div>

            <div class="mini-grid">
                <div class="mini">
                    <h4 data-en="Emergency Room" data-id="IGD">Emergency Room</h4>
                    <p data-en="Triage-based queue (Red/Yellow/Green/Black), not arrival order."
                        data-id="Antrian berbasis triase (Merah/Kuning/Hijau/Hitam), bukan urutan datang.">
                        Triage-based queue (Red/Yellow/Green/Black), not arrival order.</p>
                </div>
                <div class="mini">
                    <h4 data-en="Laboratory" data-id="Laboratorium">Laboratory</h4>
                    <p data-en="Ordered from the examination tab, result & price recorded when the order is placed."
                        data-id="Permintaan dari tab pemeriksaan, hasil & harga tercatat saat order dibuat.">Ordered
                        from the examination tab, result & price recorded when the order is placed.</p>
                </div>
                <div class="mini">
                    <h4 data-en="Radiology" data-id="Radiologi">Radiology</h4>
                    <p data-en="Requested → scheduled → completed workflow, with image results attached."
                        data-id="Alur diminta → terjadwal → selesai, lengkap dengan hasil gambar.">Requested →
                        scheduled → completed workflow, with image results attached.</p>
                </div>
                <div class="mini">
                    <h4 data-en="Inpatient Care" data-id="Rawat Inap">Inpatient Care</h4>
                    <p data-en="Billing accumulates automatically: daily room rate, procedures, diagnosis, prescriptions."
                        data-id="Billing terakumulasi otomatis: kamar per hari, tindakan, diagnosa, resep.">Billing
                        accumulates automatically: daily room rate, procedures, diagnosis, prescriptions.</p>
                </div>
                <div class="mini">
                    <h4 data-en="Multi-Branch" data-id="Multi-Cabang">Multi-Branch</h4>
                    <p>
                        <span data-en="Each branch (" data-id="Setiap cabang (">Each branch (</span><code>Klien</code><span
                            data-en=") only ever sees its own data." data-id=") hanya melihat data miliknya sendiri.">)
                            only ever sees its own data.</span>
                    </p>
                </div>
                <div class="mini">
                    <h4 data-en="Certificates & Letters" data-id="Surat Keterangan">Certificates & Letters</h4>
                    <p data-en="Print health/sick/referral certificates, even for people who aren't registered patients."
                        data-id="Cetak surat sehat/sakit/rujukan, termasuk untuk yang bukan pasien terdaftar.">Print
                        health/sick/referral certificates, even for people who aren't registered patients.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="block" style="padding-top:0">
        <div class="wrap">
            <div class="spotlight">
                <div>
                    <h3 class="display"
                        data-en="Patients shouldn't have to call the clinic for something they can see themselves."
                        data-id="Pasien tidak perlu telepon klinik untuk hal yang bisa mereka lihat sendiri.">Patients
                        shouldn't have to call the clinic for something they can see themselves.
                    </h3>
                    <p data-en="The Patient Portal gives direct access to visit history, prescriptions, bills, and booking a new doctor's appointment — no staff account, no extra app."
                        data-id="Portal Pasien memberi akses langsung ke riwayat kunjungan, resep, tagihan, dan booking jadwal dokter baru — tanpa akun staf, tanpa aplikasi tambahan.">
                        The Patient Portal gives direct access to visit history, prescriptions, bills, and booking a
                        new doctor's appointment — no staff account, no extra app.</p>
                    <a href="{{ url('portal/login') }}" class="btn btn-outline" data-en="Open the Patient Portal"
                        data-id="Buka Portal Pasien">Open the Patient Portal</a>
                </div>
                <div class="spotlight-card">
                    <div class="row">
                        <span data-en="Visit History" data-id="Riwayat Kunjungan">Visit History</span>
                        <span data-en="Available" data-id="Tersedia">Available</span>
                    </div>
                    <div class="row">
                        <span data-en="Prescriptions" data-id="Resep Obat">Prescriptions</span>
                        <span data-en="Available" data-id="Tersedia">Available</span>
                    </div>
                    <div class="row">
                        <span data-en="Bills" data-id="Tagihan">Bills</span>
                        <span data-en="Available" data-id="Tersedia">Available</span>
                    </div>
                    <div class="row">
                        <span data-en="Doctor Booking" data-id="Booking Dokter">Doctor Booking</span>
                        <span data-en="Available" data-id="Tersedia">Available</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-band">
        <div class="wrap">
            <h2 class="display" data-en="Start running your clinic from one place."
                data-id="Mulai kelola klinik dari satu tempat.">Start running your clinic from one place.</h2>
            <p data-en="Log in for your clinic team, or take a look at how it works first."
                data-id="Masuk ke aplikasi untuk tim klinik, atau lihat dulu bagaimana alurnya bekerja.">Log in for
                your clinic team, or take a look at how it works first.</p>
            <div class="cta-actions">
                <a href="{{ route('login') }}" class="btn btn-primary" data-en="Log In to the App"
                    data-id="Masuk ke Aplikasi">Log In to the App</a>
                <a href="#demo" class="btn btn-outline" data-en="Watch Demo" data-id="Tonton Demo">Watch Demo</a>
            </div>
        </div>
    </section>

    <footer>
        <div class="wrap">
            <div class="footer-brand">
                <img src="{{ $logo }}" alt="Logo {{ $company_name }}">
                <div>
                    <strong>{{ $company_name }}</strong>
                    @if ($company_address || $company_city)
                        {{ $company_address }}{{ $company_address && $company_city ? ', ' : '' }}{{ $company_city }}
                    @endif
                </div>
            </div>
            <nav>
                <a href="#fitur" data-en="Features" data-id="Fitur">Features</a>
                <a href="{{ url('portal/login') }}" data-en="Patient Portal" data-id="Portal Pasien">Patient
                    Portal</a>
                <a href="{{ route('login') }}" data-en="Log In" data-id="Masuk">Log In</a>
            </nav>
        </div>
        <div class="wrap" style="margin-top:20px">
            <p style="font-size:12.5px;color:var(--ink-soft);margin:0">© {{ date('Y') }} {{ $company_name }}.
                <span data-en="All rights reserved." data-id="Seluruh hak cipta dilindungi.">All rights
                    reserved.</span>
            </p>
        </div>
    </footer>

    <script>
        (function () {
            var STORAGE_KEY = 'jivident_landing_lang';
            var META = {
                en: { title: @json($pageTitleEn), description: @json($pageDescriptionEn) },
                id: { title: @json($pageTitleId), description: @json($pageDescriptionId) }
            };

            function applyLang(lang) {
                document.documentElement.setAttribute('lang', lang);

                document.querySelectorAll('[data-en]').forEach(function (el) {
                    var text = lang === 'id' ? el.getAttribute('data-id') : el.getAttribute('data-en');
                    if (text !== null) el.textContent = text;
                });

                document.querySelectorAll('.lang-btn').forEach(function (btn) {
                    var isActive = btn.getAttribute('data-lang') === lang;
                    btn.classList.toggle('active', isActive);
                    btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
                });

                if (META[lang]) {
                    document.title = META[lang].title;
                    var metaDescription = document.querySelector('meta[name="description"]');
                    if (metaDescription) metaDescription.setAttribute('content', META[lang].description);
                }

                try { localStorage.setItem(STORAGE_KEY, lang); } catch (e) {}
            }

            document.querySelectorAll('.lang-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    applyLang(btn.getAttribute('data-lang'));
                });
            });

            // Default is English (server-rendered); only switch on load if this
            // visitor had previously chosen Indonesian on this page before.
            var saved = null;
            try { saved = localStorage.getItem(STORAGE_KEY); } catch (e) {}
            if (saved === 'id') {
                applyLang('id');
            }
        })();
    </script>
</body>

</html>
