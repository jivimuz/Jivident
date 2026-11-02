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
$pageDescriptionEn = "{$company_name} is a free clinic and dental practice management system in Indonesia for patient registration, queues, medical records, billing, pharmacy, payroll, appointments, inpatient care, reports, and patient portals.";
$pageTitleId = 'JiviDent — Sistem Manajemen Klinik & Praktik Dokter Gigi Gratis';
$pageDescriptionId = "{$company_name} adalah sistem manajemen klinik dan praktik dokter gigi gratis di Indonesia untuk pendaftaran pasien, antrian, rekam medis, billing, farmasi, payroll, appointment, rawat inap, laporan, dan portal pasien.";
$seoKeywords = 'sistem manajemen klinik gratis, software klinik Indonesia, aplikasi klinik gigi, sistem informasi klinik, aplikasi rekam medis, aplikasi billing klinik, software apotek, aplikasi rawat inap, payroll klinik, portal pasien';

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
    <meta name="keywords" content="{{ $seoKeywords }}">
    <meta name="robots" content="index, follow">
    <meta name="author" content="{{ $company_name }}">
    <meta name="creator" content="Jivi Muzaqi Guntur">
    <meta name="publisher" content="JiviDent">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <link href="{{ url('') }}/assets/favicon.ico" rel="icon" type="image/x-icon">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ $logo }}">
    <meta property="og:locale" content="id_ID">
    <meta property="og:locale:alternate" content="en_US">
    <meta property="og:site_name" content="{{ $company_name }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image" content="{{ $logo }}">

    <!-- Structured data: software, organization, website, and FAQ signals -->
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'SoftwareApplication',
                    '@id' => $canonicalUrl . '#software',
                    'name' => 'JiviDent',
                    'description' => $pageDescriptionId,
                    'applicationCategory' => 'BusinessApplication',
                    'applicationSubCategory' => 'Healthcare Practice Management',
                    'operatingSystem' => 'Web',
                    'url' => $canonicalUrl,
                    'image' => $logo,
                    'inLanguage' => ['id-ID', 'en-US'],
                    'isAccessibleForFree' => true,
                    'offers' => [
                        '@type' => 'Offer',
                        'price' => '0',
                        'priceCurrency' => 'IDR',
                        'description' => 'Sistem inti gratis. Pengembangan custom dan migrasi database tersedia dengan penawaran terpisah.',
                    ],
                    'creator' => ['@id' => $canonicalUrl . '#owner'],
                ],
                [
                    '@type' => 'Person',
                    '@id' => $canonicalUrl . '#owner',
                    'name' => 'Jivi Muzaqi Guntur',
                    'email' => 'mailto:jivirasga@gmail.com',
                    'jobTitle' => 'Owner JiviDent',
                    'url' => $canonicalUrl . '#kontak',
                ],
                [
                    '@type' => 'Organization',
                    '@id' => $canonicalUrl . '#organization',
                    'name' => 'JiviDent',
                    'url' => $canonicalUrl,
                    'logo' => $logo,
                    'email' => 'mailto:jivirasga@gmail.com',
                    'founder' => ['@id' => $canonicalUrl . '#owner'],
                    'contactPoint' => [
                        '@type' => 'ContactPoint',
                        'contactType' => 'customer support',
                        'email' => 'mailto:jivirasga@gmail.com',
                        'availableLanguage' => ['Indonesian', 'English'],
                    ],
                ],
                [
                    '@type' => 'WebSite',
                    '@id' => $canonicalUrl . '#website',
                    'name' => 'JiviDent',
                    'url' => $canonicalUrl,
                    'description' => $pageDescriptionId,
                    'inLanguage' => ['id-ID', 'en-US'],
                    'publisher' => ['@id' => $canonicalUrl . '#organization'],
                ],
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => [
                [
                    '@type' => 'Question',
                    'name' => 'Apakah JiviDent gratis?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Ya. Sistem inti JiviDent gratis. Biaya hanya berlaku untuk pengembangan custom atau bantuan migrasi database.',
                    ],
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Fitur apa saja yang tersedia di JiviDent?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'JiviDent mencakup pendaftaran pasien, antrian, rekam medis, billing, farmasi, POS, stok, jadwal dokter, appointment, rawat inap, payroll, laporan, monitor publik, dan portal pasien.',
                    ],
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Bagaimana cara bertanya tentang JiviDent?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Hubungi owner JiviDent, Jivi Muzaqi Guntur, melalui email jivirasga@gmail.com.',
                    ],
                ],
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
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

        .faq-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
        }

        .faq-card {
            padding: 24px;
            border: 1px solid #E1E5EF;
            border-radius: 17px;
            background: #fff;
            box-shadow: var(--shadow-soft);
        }

        .faq-card h3 {
            margin: 0 0 9px;
            color: var(--ink);
            font-size: 16px;
            line-height: 1.35;
        }

        .faq-card p {
            margin: 0;
            color: var(--ink-soft);
            font-size: 13.5px;
            line-height: 1.65;
        }

        .faq-card a {
            color: var(--indigo);
            font-weight: 700;
        }

        .preview-carousel {
            position: relative;
        }

        .preview-grid {
            display: flex;
            gap: 22px;
            overflow-x: auto;
            overscroll-behavior-x: contain;
            padding: 8px 4px 24px;
            scroll-snap-type: x mandatory;
            scrollbar-width: thin;
            scrollbar-color: #B8C2EE transparent;
        }

        .preview-grid::-webkit-scrollbar {
            height: 7px;
        }

        .preview-grid::-webkit-scrollbar-track {
            background: transparent;
        }

        .preview-grid::-webkit-scrollbar-thumb {
            border-radius: 999px;
            background: #B8C2EE;
        }

        .preview-card {
            flex: 0 0 min(350px, 82vw);
            scroll-snap-align: start;
        }

        .carousel-controls {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin: -35px 0 18px;
        }

        .carousel-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border: 1px solid #D7DDEE;
            border-radius: 50%;
            color: var(--indigo-deep);
            background: #fff;
            box-shadow: 0 8px 18px rgba(29, 42, 91, .08);
            cursor: pointer;
            font-size: 20px;
            line-height: 1;
            transition: transform .2s ease, background .2s ease, color .2s ease;
        }

        .carousel-button:hover {
            color: #fff;
            background: var(--indigo);
            transform: translateY(-2px);
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

        .owner-bar {
            padding: 9px 0;
            color: #E8ECFF;
            background: #101832;
            font-size: 12.5px;
        }

        .owner-bar .wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            text-align: center;
        }

        .owner-bar strong {
            color: #fff;
        }

        .owner-bar a {
            color: #AEB9FF;
            font-weight: 700;
            text-decoration: none;
        }

        .owner-bar a:hover {
            color: #fff;
            text-decoration: underline;
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
            font-size: clamp(42px, 5.2vw, 68px);
            line-height: 1.12;
            margin: 0 0 18px;
            font-weight: 600;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin: 0 0 18px;
            padding: 7px 12px;
            border: 1px solid #BCE4D8;
            border-radius: 999px;
            color: #14785F;
            background: var(--mint-soft);
            font-size: 12.5px;
            font-weight: 700;
        }

        .eyebrow::before {
            content: '';
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--mint);
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

        /* Real product previews */
        .preview-grid {
            display: flex;
            flex-wrap: nowrap;
            gap: 22px;
            overflow-x: auto;
            overflow-y: hidden;
            scroll-snap-type: x mandatory;
        }

        .preview-card {
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 16px;
            background: var(--paper-raised);
            box-shadow: 0 8px 24px rgba(29, 34, 48, 0.06);
        }

        .preview-card img {
            display: block;
            width: 100%;
            aspect-ratio: 16 / 10;
            object-fit: cover;
            object-position: top;
            border-bottom: 1px solid var(--line);
        }

        .preview-card-body {
            padding: 16px 17px 18px;
        }

        .preview-card h3 {
            margin: 0 0 5px;
            font-size: 16px;
        }

        .preview-card p {
            margin: 0;
            color: var(--ink-soft);
            font-size: 13px;
            line-height: 1.5;
        }

        .preview-card a {
            display: inline-block;
            margin-top: 11px;
            color: var(--indigo);
            font-size: 12.5px;
            font-weight: 700;
            text-decoration: none;
        }

        .preview-card a:hover {
            text-decoration: underline;
        }

        .pricing-card {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 36px;
            align-items: center;
            padding: 34px;
            border: 1px solid #BCE4D8;
            border-radius: 20px;
            background: linear-gradient(135deg, var(--mint-soft), #fff 62%);
        }

        .pricing-card h2 {
            margin: 0 0 9px;
            font-size: 30px;
        }

        .pricing-card p {
            max-width: 57ch;
            margin: 0;
            color: var(--ink-soft);
            font-size: 15px;
        }

        .pricing-list {
            display: grid;
            gap: 9px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .pricing-list li {
            font-size: 14px;
            font-weight: 600;
        }

        .pricing-list li::before {
            content: '✓';
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            margin-right: 8px;
            border-radius: 50%;
            color: #fff;
            background: var(--mint);
            font-size: 12px;
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

        .footer-contact {
            margin-top: 5px;
            color: var(--ink-soft);
            font-size: 12.5px;
        }

        .footer-contact a {
            color: var(--indigo);
            font-weight: 600;
            text-decoration: none;
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
            .mini-grid,
            .pricing-card,
            .faq-grid {
                grid-template-columns: 1fr;
            }

            .nav-links {
                display: none;
            }

            .hero-copy h1 {
                font-size: 32px;
            }
        }

        /* Premium presentation layer */
        :root {
            --paper: #F7F8FC;
            --paper-raised: #FFFFFF;
            --ink: #11182D;
            --ink-soft: #66708A;
            --line: #E5E8F0;
            --indigo: #5368E8;
            --indigo-deep: #3648B9;
            --indigo-soft: #EEF0FF;
            --mint: #25B58E;
            --mint-soft: #E7F8F3;
            --shadow-soft: 0 16px 40px rgba(29, 42, 91, .08);
            --shadow-card: 0 22px 65px rgba(21, 31, 75, .12);
        }

        body {
            background:
                radial-gradient(circle at 8% 12%, rgba(83, 104, 232, .07), transparent 25rem),
                var(--paper);
        }

        .wrap {
            max-width: 1180px;
        }

        .nav {
            background: rgba(247, 248, 252, .82);
            border-bottom-color: rgba(229, 232, 240, .82);
            box-shadow: 0 5px 24px rgba(19, 29, 67, .04);
        }

        .nav .wrap {
            min-height: 72px;
        }

        .nav-brand img {
            height: 36px;
        }

        .nav-links {
            gap: 24px;
        }

        .nav-links a:not(.btn) {
            position: relative;
            font-size: 13px;
            letter-spacing: -.01em;
        }

        .nav-links a:not(.btn)::after {
            position: absolute;
            right: 0;
            bottom: -8px;
            left: 0;
            height: 2px;
            content: '';
            background: var(--indigo);
            transform: scaleX(0);
            transition: transform .2s ease;
        }

        .nav-links a:not(.btn):hover::after {
            transform: scaleX(1);
        }

        .btn {
            min-height: 46px;
            padding: 12px 21px;
            border-radius: 12px;
            box-shadow: 0 7px 16px rgba(29, 42, 91, .08);
            transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 22px rgba(29, 42, 91, .14);
        }

        .btn-primary {
            background: linear-gradient(135deg, #6478F2, #4054D0);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #7183F5, #3648B9);
        }

        .btn-outline {
            border-color: #D8DDEB;
            background: rgba(255, 255, 255, .72);
        }

        .lang-switch {
            border-color: #DCE1ED;
            background: rgba(255, 255, 255, .75);
        }

        .hero {
            position: relative;
            isolation: isolate;
            padding: 110px 0 104px;
            color: #fff;
            background:
                radial-gradient(circle at 84% 18%, rgba(112, 132, 255, .38), transparent 23rem),
                radial-gradient(circle at 20% 100%, rgba(37, 181, 142, .14), transparent 28rem),
                linear-gradient(120deg, #101832 0%, #1B2854 56%, #263C8C 100%);
        }

        .hero::after {
            position: absolute;
            right: 7%;
            bottom: -170px;
            z-index: -1;
            width: 420px;
            height: 420px;
            content: '';
            border: 1px solid rgba(255, 255, 255, .08);
            border-radius: 50%;
            box-shadow: 0 0 0 42px rgba(255, 255, 255, .025), 0 0 0 84px rgba(255, 255, 255, .018);
        }

        .hero .wrap {
            gap: 76px;
        }

        .hero-copy h1 {
            max-width: 760px;
            margin-bottom: 24px;
            color: #fff;
            font-size: clamp(46px, 5.2vw, 72px);
            letter-spacing: -.045em;
        }

        .hero-copy p.lead {
            max-width: 52ch;
            color: rgba(255, 255, 255, .72);
            font-size: 17px;
        }

        .hero-portal-note {
            color: rgba(255, 255, 255, .56);
        }

        .hero-portal-note a {
            color: #AEB9FF;
        }

        .eyebrow {
            border-color: rgba(112, 232, 199, .3);
            color: #8DE4C7;
            background: rgba(37, 181, 142, .12);
            letter-spacing: .01em;
            text-transform: uppercase;
        }

        .flow-card {
            position: relative;
            border-color: rgba(255, 255, 255, .4);
            border-radius: 24px;
            padding: 30px 28px;
            background: rgba(255, 255, 255, .96);
            box-shadow: var(--shadow-card);
        }

        .flow-card::before {
            position: absolute;
            top: 18px;
            right: 22px;
            padding: 5px 9px;
            content: 'LIVE WORKFLOW';
            border: 1px solid #D7F1E9;
            border-radius: 999px;
            color: #19866D;
            background: #F0FBF7;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: .1em;
        }

        .flow-card .flow-label {
            max-width: 16ch;
            margin-bottom: 28px;
            color: var(--ink);
            font-size: 16px;
            line-height: 1.25;
        }

        .flow-step {
            padding-bottom: 25px;
        }

        .flow-step::before {
            background: #DDE2F0;
        }

        .flow-num {
            width: 34px;
            height: 34px;
            border: 4px solid #fff;
            background: #EFF1FF;
            box-shadow: 0 2px 8px rgba(53, 73, 185, .14);
        }

        .flow-step.active .flow-num {
            background: var(--indigo);
            box-shadow: 0 5px 14px rgba(83, 104, 232, .34);
        }

        .flow-text h3 {
            color: var(--ink);
        }

        .flow-text p {
            color: var(--ink-soft);
        }

        .strip {
            position: relative;
            z-index: 1;
            border: 0;
            background: #fff;
            box-shadow: 0 12px 30px rgba(25, 38, 84, .06);
        }

        .strip .wrap {
            padding-top: 30px;
            padding-bottom: 30px;
        }

        .strip-item {
            align-items: flex-start;
            padding: 5px 14px;
            border-right: 1px solid var(--line);
        }

        .strip-item:last-child {
            border-right: 0;
        }

        .strip-item .dot {
            box-shadow: 0 0 0 5px rgba(37, 181, 142, .12);
        }

        section.block {
            padding: 112px 0;
        }

        .section-head {
            max-width: 65ch;
            margin-bottom: 50px;
        }

        .section-head h2 {
            color: var(--ink);
            font-size: clamp(31px, 3.2vw, 44px);
            letter-spacing: -.04em;
            line-height: 1.1;
        }

        .section-head p {
            font-size: 16px;
            line-height: 1.7;
        }

        .video-frame {
            border: 8px solid #fff;
            border-radius: 24px;
            box-shadow: var(--shadow-card);
        }

        .preview-grid {
            gap: 22px;
        }

        .preview-card {
            border-color: #E0E4EE;
            border-radius: 18px;
            box-shadow: var(--shadow-soft);
            transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
        }

        .preview-card:hover {
            border-color: #BFC8F2;
            box-shadow: 0 24px 48px rgba(32, 48, 113, .15);
            transform: translateY(-7px);
        }

        .preview-card img {
            aspect-ratio: 16 / 9;
            transition: transform .35s ease;
        }

        .preview-card:hover img {
            transform: scale(1.035);
        }

        .preview-card-body {
            padding: 19px 19px 21px;
        }

        .preview-card h3 {
            color: var(--ink);
            font-size: 16px;
            letter-spacing: -.02em;
        }

        .preview-card p {
            min-height: 40px;
            line-height: 1.6;
        }

        .pricing-card {
            position: relative;
            overflow: hidden;
            min-height: 260px;
            padding: 48px;
            border-color: #BBDDD3;
            border-radius: 26px;
            background:
                radial-gradient(circle at 90% 20%, rgba(37, 181, 142, .22), transparent 16rem),
                linear-gradient(135deg, #E9FAF5, #fff 68%);
            box-shadow: var(--shadow-soft);
        }

        .pricing-card::after {
            position: absolute;
            right: -80px;
            bottom: -145px;
            width: 290px;
            height: 290px;
            content: '';
            border: 1px solid rgba(37, 181, 142, .2);
            border-radius: 50%;
            box-shadow: 0 0 0 35px rgba(37, 181, 142, .06);
        }

        .pricing-card > * {
            position: relative;
            z-index: 1;
        }

        .pricing-card h2 {
            font-size: clamp(31px, 3.3vw, 44px);
            letter-spacing: -.04em;
        }

        .pricing-card p {
            line-height: 1.7;
        }

        .pricing-list {
            gap: 14px;
        }

        .pricing-list li {
            display: flex;
            align-items: center;
            font-size: 14px;
        }

        .feature-row {
            gap: 76px;
            padding: 54px 0;
        }

        .feature-text h3 {
            font-size: 27px;
            letter-spacing: -.035em;
        }

        .feature-text p {
            line-height: 1.7;
        }

        .feature-text li::before {
            width: 8px;
            height: 8px;
            background: var(--mint);
            box-shadow: 0 0 0 4px rgba(37, 181, 142, .12);
        }

        .feature-visual {
            min-height: 250px;
            border: 1px solid rgba(83, 104, 232, .1);
            border-radius: 24px;
            background:
                radial-gradient(circle at 75% 20%, rgba(255, 255, 255, .85), transparent 11rem),
                linear-gradient(135deg, #EEF0FF, #E3E7FF);
            box-shadow: var(--shadow-soft);
        }

        .feature-visual.mint {
            background: linear-gradient(135deg, #E8FAF5, #D9F1EA);
        }

        .feature-visual.amber {
            background: linear-gradient(135deg, #FFF5E3, #F9E8C8);
        }

        .feature-visual .metric .num {
            color: var(--indigo-deep);
            font-size: 48px;
            letter-spacing: -.06em;
        }

        .feature-visual .metric .cap {
            max-width: 25ch;
            line-height: 1.6;
        }

        .mini-grid {
            gap: 16px;
        }

        .mini-grid .mini {
            border: 1px solid #E1E5EF;
            border-radius: 16px;
            background: rgba(255, 255, 255, .72);
            box-shadow: 0 8px 22px rgba(30, 45, 93, .04);
            transition: transform .2s ease, border-color .2s ease;
        }

        .mini-grid .mini:hover {
            border-color: #BFC8F2;
            transform: translateY(-4px);
        }

        .spotlight {
            border-radius: 26px;
            box-shadow: var(--shadow-card);
        }

        .cta-band {
            position: relative;
            overflow: hidden;
            padding: 112px 0;
            background:
                radial-gradient(circle at 10% 40%, rgba(112, 132, 255, .25), transparent 22rem),
                linear-gradient(120deg, #101832, #263C8C);
        }

        .cta-band::after {
            position: absolute;
            top: -190px;
            right: -50px;
            width: 430px;
            height: 430px;
            content: '';
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 50%;
            box-shadow: 0 0 0 38px rgba(255, 255, 255, .035), 0 0 0 76px rgba(255, 255, 255, .025);
        }

        .cta-band h2 {
            position: relative;
            z-index: 1;
            font-size: clamp(32px, 4vw, 52px);
            letter-spacing: -.045em;
        }

        .cta-band p,
        .cta-actions {
            position: relative;
            z-index: 1;
        }

        footer {
            background: #fff;
        }

        @media (max-width: 860px) {
            .nav .wrap {
                min-height: 64px;
            }

            .owner-bar .wrap {
                flex-wrap: wrap;
                gap: 3px 7px;
            }

            .nav-cta .btn {
                padding-right: 14px;
                padding-left: 14px;
                font-size: 12px;
            }

            .hero {
                padding: 76px 0 72px;
            }

            .hero .wrap {
                gap: 42px;
            }

            .hero-copy h1 {
                font-size: clamp(42px, 11vw, 58px);
            }

            .strip-item {
                border-right: 0;
                border-bottom: 1px solid var(--line);
                padding: 13px 0;
            }

            .strip-item:last-child {
                border-bottom: 0;
            }

            section.block {
                padding: 78px 0;
            }

            .pricing-card {
                padding: 32px 26px;
            }
        }

        @media (max-width: 520px) {
            .wrap {
                padding-right: 18px;
                padding-left: 18px;
            }

            .nav .wrap {
                gap: 10px;
            }

            .nav-brand img {
                height: 30px;
            }

            .lang-switch {
                display: none;
            }

            .nav-cta .btn {
                min-height: 40px;
            }

            .hero-copy h1 {
                font-size: 42px;
            }

            .hero-ctas .btn {
                width: 100%;
            }

            .flow-card {
                padding: 25px 20px;
            }

            .section-head {
                margin-bottom: 32px;
            }

            .carousel-controls {
                margin-top: -22px;
            }

            .video-frame {
                border-width: 4px;
                border-radius: 17px;
            }

            .pricing-card {
                border-radius: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="owner-bar" id="kontak">
        <div class="wrap">
            <span data-en="Questions or want to discuss your clinic setup?"
                data-id="Mau tanya atau diskusi tentang kebutuhan klinik?">Mau tanya atau diskusi tentang kebutuhan
                klinik?</span>
            <strong>Jivi Muzaqi Guntur</strong>
            <a href="mailto:jivirasga@gmail.com">jivirasga@gmail.com</a>
        </div>
    </div>
    <nav class="nav">
        <div class="wrap">
            <a href="{{ url('/i') }}" class="nav-brand">
                <img src="{{ $logo }}" alt="Logo {{ $company_name }}">
            </a>
            <div class="nav-links">
                <a href="#preview" data-en="Product preview" data-id="Preview produk">Product preview</a>
                <a href="#fitur" data-en="Features" data-id="Fitur">Features</a>
                <a href="#layanan-lanjutan" data-en="Advanced Services" data-id="Layanan Lanjutan">Advanced
                    Services</a>
                <a href="#harga" data-en="Pricing" data-id="Harga">Pricing</a>
                <a href="#kontak" data-en="Contact owner" data-id="Kontak owner">Contact owner</a>
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
                <p class="eyebrow" data-en="Free core clinic system"
                    data-id="Sistem inti klinik gratis">Free core clinic system</p>
                <h1 class="display" data-en="Free clinic system, from check-in to cash in."
                    data-id="Sistem klinik gratis, dari pasien datang sampai kas masuk.">Free clinic system, from
                    check-in to cash in.</h1>
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

    <section class="block" id="preview">
        <div class="wrap">
            <div class="section-head">
                <h2 class="display" data-en="Preview the system before you commit"
                    data-id="Lihat preview sistemnya sebelum mulai">Preview the system before you commit</h2>
                <p data-en="These are real screens from the application — not stock illustrations. Open the demo, then explore the modules that matter to your team."
                    data-id="Ini adalah tampilan nyata dari aplikasi — bukan ilustrasi stok. Tonton demo, lalu lihat modul yang paling relevan untuk tim Anda.">
                    These are real screens from the application — not stock illustrations. Open the demo, then
                    explore the modules that matter to your team.</p>
            </div>
            <div class="carousel-controls" aria-label="Preview navigation">
                <button type="button" class="carousel-button" data-carousel-direction="-1"
                    aria-label="Previous preview">‹</button>
                <button type="button" class="carousel-button" data-carousel-direction="1"
                    aria-label="Next preview">›</button>
            </div>
            <div class="preview-carousel">
                <div class="preview-grid" id="feature-preview-carousel" tabindex="0"
                    aria-label="Feature previews, swipe horizontally">
                <article class="preview-card">
                    <img src="{{ asset('assets/images/landing-preview/dashboard.png') }}" alt="Dashboard JiviDent" loading="lazy">
                    <div class="preview-card-body">
                        <h3 data-en="Dashboard & daily control" data-id="Dashboard & kontrol harian">Dashboard & daily control</h3>
                        <p data-en="See your clinic's operational picture at a glance." data-id="Lihat kondisi operasional klinik dalam satu layar.">See your clinic's operational picture at a glance.</p>
                    </div>
                </article>
                <article class="preview-card">
                    <img src="{{ asset('assets/images/landing-preview/pendaftaran.png') }}" alt="Pendaftaran pasien" loading="lazy">
                    <div class="preview-card-body">
                        <h3 data-en="Patient registration" data-id="Pendaftaran pasien">Patient registration</h3>
                        <p data-en="New and returning patients, medical record number, doctor, room, and visit type." data-id="Pasien baru/lama, No. RM, dokter, ruang, dan jenis kunjungan.">New and returning patients, medical record number, doctor, room, and visit type.</p>
                    </div>
                </article>
                <article class="preview-card">
                    <img src="{{ asset('assets/images/landing-preview/ruang-tunggu.png') }}" alt="Ruang tunggu dan antrian" loading="lazy">
                    <div class="preview-card-body">
                        <h3 data-en="Waiting room & queue" data-id="Ruang tunggu & antrian">Waiting room & queue</h3>
                        <p data-en="Keep the front desk and examination flow moving." data-id="Jaga alur meja depan dan pemeriksaan tetap lancar.">Keep the front desk and examination flow moving.</p>
                    </div>
                </article>
                <article class="preview-card">
                    <img src="{{ asset('assets/images/landing-preview/billing.png') }}" alt="Billing dan pembayaran" loading="lazy">
                    <div class="preview-card-body">
                        <h3 data-en="Billing & payments" data-id="Billing & pembayaran">Billing & payments</h3>
                        <p data-en="Procedures, diagnosis, lab, radiology, and prescriptions flow into one bill." data-id="Tindakan, diagnosa, lab, radiologi, dan resep masuk ke satu tagihan.">Procedures, diagnosis, lab, radiology, and prescriptions flow into one bill.</p>
                    </div>
                </article>
                <article class="preview-card">
                    <img src="{{ asset('assets/images/landing-preview/pos.png') }}" alt="POS Apotek" loading="lazy">
                    <div class="preview-card-body">
                        <h3 data-en="Pharmacy POS" data-id="POS Apotek">Pharmacy POS</h3>
                        <p data-en="Fast checkout with product-master pricing, discounts, and stock deduction." data-id="Kasir cepat dengan harga master, diskon, dan potongan stok otomatis.">Fast checkout with product-master pricing, discounts, and stock deduction.</p>
                    </div>
                </article>
                <article class="preview-card">
                    <img src="{{ asset('assets/images/landing-preview/product.png') }}" alt="Master produk dan stok" loading="lazy">
                    <div class="preview-card-body">
                        <h3 data-en="Products & inventory" data-id="Produk & persediaan">Products & inventory</h3>
                        <p data-en="Manage catalog, suppliers, receive orders, lots, expiry, and stock opname." data-id="Kelola katalog, supplier, penerimaan, lot, kedaluwarsa, dan stok opname.">Manage catalog, suppliers, receive orders, lots, expiry, and stock opname.</p>
                    </div>
                </article>
                <article class="preview-card">
                    <img src="{{ asset('assets/images/landing-preview/jadwal-dokter.png') }}" alt="Jadwal dokter" loading="lazy">
                    <div class="preview-card-body">
                        <h3 data-en="Doctor schedules & booking" data-id="Jadwal dokter & booking">Doctor schedules & booking</h3>
                        <p data-en="Generate bookable slots and prevent room or doctor clashes." data-id="Buat slot booking dan cegah benturan ruang atau dokter.">Generate bookable slots and prevent room or doctor clashes.</p>
                    </div>
                </article>
                <article class="preview-card">
                    <img src="{{ asset('assets/images/landing-preview/rawat-inap.png') }}" alt="Rawat inap" loading="lazy">
                    <div class="preview-card-body">
                        <h3 data-en="Inpatient care" data-id="Rawat inap">Inpatient care</h3>
                        <p data-en="Bed management, admission, transfer, discharge, and accumulated billing." data-id="Kelola kamar, admisi, pindah kamar, pulang, dan billing terakumulasi.">Bed management, admission, transfer, discharge, and accumulated billing.</p>
                    </div>
                </article>
                <article class="preview-card">
                    <img src="{{ asset('assets/images/landing-preview/report.png') }}" alt="Laporan klinik dan keuangan" loading="lazy">
                    <div class="preview-card-body">
                        <h3 data-en="Clinical & financial reports" data-id="Laporan klinis & keuangan">Clinical & financial reports</h3>
                        <p data-en="Turn visit, payment, and operational data into useful reports." data-id="Ubah data kunjungan, pembayaran, dan operasional menjadi laporan.">Turn visit, payment, and operational data into useful reports.</p>
                    </div>
                </article>
                <article class="preview-card">
                    <img src="{{ asset('assets/images/landing-preview/employees.png') }}" alt="Manajemen karyawan dan payroll" loading="lazy">
                    <div class="preview-card-body">
                        <h3 data-en="Employees, shifts & payroll" data-id="Karyawan, jadwal & payroll">Employees, shifts & payroll</h3>
                        <p data-en="Employee records, contracts, leave, attendance schedules, BPJS, and PPh21 TER." data-id="Data karyawan, kontrak, cuti, jadwal kerja, BPJS, dan PPh21 TER.">Employee records, contracts, leave, attendance schedules, BPJS, and PPh21 TER.</p>
                    </div>
                </article>
                <article class="preview-card">
                    <img src="{{ asset('assets/images/landing-preview/monitor-antrian.png') }}" alt="Monitor antrian publik" loading="lazy">
                    <div class="preview-card-body">
                        <h3 data-en="Public queue monitor" data-id="Monitor antrian publik">Public queue monitor</h3>
                        <p data-en="A clear screen for patients and staff, including IGD triage priority." data-id="Layar jelas untuk pasien dan staf, termasuk prioritas triase IGD.">A clear screen for patients and staff, including IGD triage priority.</p>
                    </div>
                </article>
                <article class="preview-card">
                    <img src="{{ asset('assets/images/landing-preview/monitor-ruangan.png') }}" alt="Monitor ruangan rawat inap" loading="lazy">
                    <div class="preview-card-body">
                        <h3 data-en="Room occupancy monitor" data-id="Monitor ruangan">Room occupancy monitor</h3>
                        <p data-en="Show occupied beds and patient details on a public display." data-id="Tampilkan kamar terisi dan detail pasien di layar publik.">Show occupied beds and patient details on a public display.</p>
                    </div>
                </article>
                <article class="preview-card">
                    <img src="{{ asset('assets/images/landing-preview/portal-login.png') }}" alt="Portal pasien" loading="lazy">
                    <div class="preview-card-body">
                        <h3 data-en="Patient Portal" data-id="Portal Pasien">Patient Portal</h3>
                        <p data-en="Patients can access visit history, prescriptions, bills, and doctor booking." data-id="Pasien dapat melihat riwayat, resep, tagihan, dan booking dokter.">Patients can access visit history, prescriptions, bills, and doctor booking.</p>
                        <a href="{{ url('portal/login') }}" data-en="Open the portal →" data-id="Buka portal →">Open the portal →</a>
                    </div>
                </article>
                </div>
            </div>
        </div>
    </section>

    <section class="block" id="harga">
        <div class="wrap">
            <div class="pricing-card">
                <div>
                    <p class="eyebrow" data-en="Transparent pricing" data-id="Harga transparan">Transparent pricing</p>
                    <h2 class="display" data-en="The core system is free."
                        data-id="Sistem intinya gratis.">The core system is free.</h2>
                    <p data-en="Use the clinic management features without a monthly subscription. You only pay when you need custom development or help migrating your existing database."
                        data-id="Gunakan fitur manajemen klinik tanpa langganan bulanan. Biaya hanya berlaku jika Anda membutuhkan pengembangan custom atau bantuan migrasi database lama.">
                        Use the clinic management features without a monthly subscription. You only pay when you need a
                        custom development or help migrating your existing database.
                    </p>
                </div>
                <ul class="pricing-list">
                    <li data-en="Core clinic system: free" data-id="Sistem inti klinik: gratis">Core clinic system: free</li>
                    <li data-en="Custom features: quoted separately" data-id="Fitur custom: penawaran terpisah">Custom features: quoted separately</li>
                    <li data-en="Database migration: quoted separately" data-id="Migrasi database: penawaran terpisah">Database migration: quoted separately</li>
                    <li data-en="No surprise monthly fee" data-id="Tanpa biaya bulanan tersembunyi">No surprise monthly fee</li>
                </ul>
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
                <div class="mini">
                    <h4 data-en="Operating Room Scheduling" data-id="Jadwal Operasi">Operating Room Scheduling</h4>
                    <p data-en="Schedule procedures from the patient's examination page, with room and doctor conflict checks."
                        data-id="Jadwalkan tindakan dari halaman pemeriksaan pasien, lengkap dengan cek benturan ruang dan dokter.">
                        Schedule procedures from the patient's examination page, with room and doctor conflict checks.</p>
                </div>
                <div class="mini">
                    <h4 data-en="Extra Charges & Consent" data-id="Biaya Tambahan & Consent">Extra Charges & Consent</h4>
                    <p data-en="Add visit-linked charges and preserve signed consent wording as a historical snapshot."
                        data-id="Tambahkan biaya ke kunjungan dan simpan isi consent yang ditandatangani sebagai snapshot historis.">
                        Add visit-linked charges and preserve signed consent wording as a historical snapshot.</p>
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

    <section class="block" id="faq" style="padding-top:0">
        <div class="wrap">
            <div class="section-head">
                <h2 class="display" data-en="Frequently asked questions"
                    data-id="Pertanyaan yang sering ditanyakan">Frequently asked questions</h2>
                <p data-en="Still comparing clinic management systems? Here are the essentials before you start."
                    data-id="Masih membandingkan sistem manajemen klinik? Ini hal penting sebelum mulai.">
                    Still comparing clinic management systems? Here are the essentials before you start.</p>
            </div>
            <div class="faq-grid">
                <article class="faq-card">
                    <h3 data-en="Is JiviDent really free?" data-id="Apakah JiviDent benar-benar gratis?">Is JiviDent really free?</h3>
                    <p data-en="Yes. The core system is free. You only pay if you request custom development or database migration assistance."
                        data-id="Ya. Sistem inti gratis. Biaya hanya berlaku jika membutuhkan pengembangan custom atau bantuan migrasi database.">
                        Yes. The core system is free. You only pay if you request custom development or database migration assistance.</p>
                </article>
                <article class="faq-card">
                    <h3 data-en="What does the system cover?" data-id="Apa saja yang tersedia?">What does the system cover?</h3>
                    <p data-en="Registration, queues, medical records, billing, pharmacy, POS, inventory, appointments, inpatient care, payroll, reports, monitors, and the patient portal."
                        data-id="Pendaftaran, antrian, rekam medis, billing, farmasi, POS, stok, appointment, rawat inap, payroll, laporan, monitor, dan portal pasien.">
                        Registration, queues, medical records, billing, pharmacy, POS, inventory, appointments, inpatient care, payroll, reports, monitors, and the patient portal.</p>
                </article>
                <article class="faq-card">
                    <h3 data-en="Want to ask the owner directly?" data-id="Ingin tanya langsung ke owner?">Want to ask the owner directly?</h3>
                    <p data-en="Contact Jivi Muzaqi Guntur at "
                        data-id="Hubungi Jivi Muzaqi Guntur di ">Contact Jivi Muzaqi Guntur at
                        <a href="mailto:jivirasga@gmail.com">jivirasga@gmail.com</a>.</p>
                </article>
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
                    <div class="footer-contact">
                        <span data-en="Owner: Jivi Muzaqi Guntur · "
                            data-id="Owner: Jivi Muzaqi Guntur · ">Owner: Jivi Muzaqi Guntur · </span>
                        <a href="mailto:jivirasga@gmail.com">jivirasga@gmail.com</a>
                    </div>
                </div>
            </div>
            <nav>
                <a href="#fitur" data-en="Features" data-id="Fitur">Features</a>
                <a href="#faq" data-en="FAQ" data-id="FAQ">FAQ</a>
                <a href="#kontak" data-en="Contact" data-id="Kontak">Contact</a>
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

            var previewCarousel = document.getElementById('feature-preview-carousel');
            if (previewCarousel) {
                document.querySelectorAll('[data-carousel-direction]').forEach(function (button) {
                    button.addEventListener('click', function () {
                        var direction = Number(button.getAttribute('data-carousel-direction'));
                        previewCarousel.scrollBy({
                            left: direction * Math.min(previewCarousel.clientWidth * .82, 380),
                            behavior: 'smooth'
                        });
                    });
                });

                previewCarousel.addEventListener('keydown', function (event) {
                    if (event.key === 'ArrowLeft' || event.key === 'ArrowRight') {
                        event.preventDefault();
                        previewCarousel.scrollBy({
                            left: (event.key === 'ArrowRight' ? 1 : -1) * Math.min(previewCarousel.clientWidth * .82, 380),
                            behavior: 'smooth'
                        });
                    }
                });
            }

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
