<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#64171d">
    <title>@yield('pageTitle') | ShopEase</title>
    <style>
        :root {
            color-scheme: light;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: #30272a;
            background: #f7f5f4;
            font-synthesis: none;
            text-rendering: optimizeLegibility;
            line-height: 1.7;
            scroll-behavior: smooth;
            --legal-ink: #30272a;
            --legal-muted: #74686a;
            --legal-maroon: #64171d;
            --legal-accent: #a34a4e;
            --legal-border: #e9e2e0;
            --legal-paper: #fff;
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-width: 320px;
            margin: 0;
            color: var(--legal-ink);
            background: #f7f5f4;
        }

        a {
            color: #7a2027;
            text-underline-offset: 3px;
        }

        a:hover {
            color: #a13d44;
        }

        a:focus-visible {
            outline: 3px solid #e6a079;
            outline-offset: 3px;
            border-radius: 4px;
        }

        .skip-link {
            position: absolute;
            z-index: 5;
            top: 10px;
            left: 10px;
            padding: 9px 14px;
            transform: translateY(-160%);
            color: #fff;
            background: var(--legal-maroon);
            border-radius: 8px;
        }

        .skip-link:focus {
            transform: translateY(0);
        }

        .site-header {
            background: #fff;
            border-bottom: 1px solid var(--legal-border);
        }

        .site-nav {
            display: flex;
            width: min(1160px, calc(100% - 48px));
            min-height: 76px;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            margin: 0 auto;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 11px;
            color: var(--legal-ink);
            font-size: 1.05rem;
            font-weight: 750;
            letter-spacing: -.025em;
            text-decoration: none;
        }

        .brand:hover {
            color: var(--legal-maroon);
        }

        .brand-mark {
            display: grid;
            width: 38px;
            height: 38px;
            place-items: center;
            color: #fff;
            background: var(--legal-maroon);
            border-radius: 12px;
            font-size: .79rem;
            letter-spacing: .04em;
        }

        .brand-name {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }

        .brand-name small {
            margin-top: 4px;
            color: var(--legal-muted);
            font-size: .62rem;
            font-weight: 700;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .header-links {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .header-links a {
            padding: 9px 12px;
            color: #55494c;
            border-radius: 8px;
            font-size: .88rem;
            font-weight: 600;
            text-decoration: none;
        }

        .header-links a:hover {
            color: var(--legal-maroon);
            background: #f8f2f1;
        }

        .header-links .home-link {
            margin-left: 5px;
            padding: 10px 15px;
            color: #fff;
            background: var(--legal-maroon);
        }

        .header-links .home-link:hover {
            color: #fff;
            background: #7b2229;
        }

        .document-hero {
            position: relative;
            overflow: hidden;
            color: #fff;
            background: linear-gradient(118deg, #541117 0%, #741e25 58%, #873139 100%);
        }

        .document-hero::after {
            position: absolute;
            top: -190px;
            right: max(-100px, calc((100% - 1320px) / 2));
            width: 470px;
            height: 470px;
            border: 1px solid rgb(255 255 255 / 12%);
            border-radius: 50%;
            box-shadow: 0 0 0 52px rgb(255 255 255 / 4%), 0 0 0 104px rgb(255 255 255 / 3%);
            content: "";
            pointer-events: none;
        }

        .hero-inner {
            position: relative;
            z-index: 1;
            width: min(1160px, calc(100% - 48px));
            margin: 0 auto;
            padding: 51px 0 43px;
        }

        .breadcrumb {
            margin: 0 0 17px;
            color: rgb(255 255 255 / 72%);
            font-size: .82rem;
            font-weight: 550;
        }

        .legal-back {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            margin-bottom: 15px;
            color: #fff;
            font-size: .88rem;
            font-weight: 650;
            text-decoration: none;
        }

        .legal-back:hover {
            color: #ffe8df;
            text-decoration: underline;
        }

        .document-type {
            display: inline-flex;
            align-items: center;
            min-height: 27px;
            margin-bottom: 12px;
            padding: 3px 10px;
            color: #ffe8df;
            background: rgb(255 255 255 / 10%);
            border: 1px solid rgb(255 255 255 / 22%);
            border-radius: 999px;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .hero-inner h1 {
            max-width: 780px;
            margin: 0;
            color: #fff;
            font-size: clamp(2rem, 4vw, 3.2rem);
            font-weight: 750;
            letter-spacing: -.045em;
            line-height: 1.12;
        }

        .hero-summary {
            max-width: 720px;
            margin: 16px 0 25px;
            color: rgb(255 255 255 / 82%);
            font-size: 1.02rem;
            line-height: 1.65;
        }

        .legal-shell {
            display: grid;
            width: min(1160px, calc(100% - 48px));
            grid-template-columns: 238px minmax(0, 1fr);
            align-items: start;
            gap: 28px;
            margin: 32px auto 58px;
        }

        .legal-sidebar {
            position: sticky;
            top: 22px;
            padding: 19px 16px;
            background: #fff;
            border: 1px solid var(--legal-border);
            border-radius: 14px;
            box-shadow: 0 5px 18px rgb(47 28 31 / 3%);
        }

        .sidebar-heading {
            margin: 0 0 12px;
            color: #403438;
            font-size: .83rem;
            font-weight: 750;
            letter-spacing: .035em;
            text-transform: uppercase;
        }

        .document-toc {
            display: grid;
            gap: 3px;
        }

        .document-toc a {
            display: block;
            padding: 8px 9px;
            color: #5e5355;
            border-radius: 7px;
            font-size: .82rem;
            line-height: 1.4;
            text-decoration: none;
        }

        .document-toc a:hover {
            color: var(--legal-maroon);
            background: #f8f2f1;
        }

        .sidebar-divider {
            height: 1px;
            margin: 15px 0 13px;
            background: var(--legal-border);
        }

        .other-documents {
            display: grid;
            gap: 8px;
        }

        .other-documents a {
            font-size: .82rem;
            font-weight: 650;
        }

        .legal-paper {
            min-width: 0;
            padding: 34px 42px;
            background: var(--legal-paper);
            border: 1px solid var(--legal-border);
            border-radius: 14px;
            box-shadow: 0 8px 28px rgb(47 28 31 / 4%);
        }

        .document-notice {
            margin: 0 0 28px;
            padding: 16px 18px;
            color: #594448;
            background: #fbf3ef;
            border: 1px solid #f0dfd7;
            border-left: 4px solid #bd7568;
            border-radius: 8px;
            font-size: .91rem;
        }

        .document-notice strong {
            color: #5d2027;
        }

        .legal-progress {
            margin: -12px 0 6px;
            color: var(--legal-muted);
            font-size: .85rem;
            font-weight: 650;
        }

        .legal-paper section {
            scroll-margin-top: 24px;
            margin: 0;
            padding: 25px 0 24px;
            border-bottom: 1px solid #eee8e6;
        }

        .legal-paper section:first-of-type {
            padding-top: 0;
        }

        .legal-paper section:last-of-type {
            padding-bottom: 0;
            border-bottom: 0;
        }

        .legal-paper h2 {
            margin: 0 0 13px;
            color: #4c171d;
            font-size: 1.23rem;
            font-weight: 740;
            letter-spacing: -.018em;
            line-height: 1.4;
        }

        .legal-paper h3 {
            margin: 22px 0 8px;
            color: #4c171d;
            font-size: 1.03rem;
            line-height: 1.45;
        }

        .legal-paper p {
            margin: 0 0 13px;
            color: #51474a;
            font-size: .96rem;
        }

        .legal-paper p:last-child {
            margin-bottom: 0;
        }

        .legal-paper ul,
        .legal-paper ol {
            margin: 0;
            padding-left: 22px;
            color: #51474a;
        }

        .legal-paper li {
            margin: 0 0 10px;
            padding-left: 3px;
            font-size: .95rem;
        }

        .legal-paper li:last-child {
            margin-bottom: 0;
        }

        .legal-paper li::marker {
            color: #9c454b;
        }

        .section-acknowledgment {
            display: flex;
            align-items: flex-start;
            gap: 11px;
            margin-top: 17px;
            padding: 13px 15px;
            color: #4c3b3f;
            background: #fbf8f7;
            border: 1px solid #e8ddda;
            border-radius: 9px;
            cursor: pointer;
            font-size: .9rem;
            line-height: 1.5;
        }

        .section-acknowledgment:hover {
            background: #f8f1ef;
            border-color: #d7b8b2;
        }

        .section-acknowledgment input {
            width: 18px;
            height: 18px;
            flex: 0 0 auto;
            margin: 2px 0 0;
            accent-color: var(--legal-maroon);
        }

        .section-acknowledgment input:focus-visible {
            outline: 3px solid #e6a079;
            outline-offset: 3px;
        }

        .legal-paper strong {
            color: #3d3033;
        }

        .document-end {
            margin-top: 26px;
            padding-top: 17px;
            color: var(--legal-muted);
            border-top: 1px solid #eee8e6;
            font-size: .87rem;
        }

        .site-footer {
            display: flex;
            width: min(1160px, calc(100% - 48px));
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin: 0 auto;
            padding: 20px 0 28px;
            color: var(--legal-muted);
            border-top: 1px solid #e6dedc;
            font-size: .82rem;
        }

        .footer-links {
            display: flex;
            flex-wrap: wrap;
            gap: 18px;
        }

        .footer-links a {
            color: #67595d;
            font-weight: 600;
            text-decoration: none;
        }

        @media (max-width: 900px) {
            .legal-shell {
                grid-template-columns: 1fr;
                gap: 16px;
                margin-top: 22px;
            }

            .legal-sidebar {
                position: static;
            }

            .document-toc {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 600px) {
            .site-nav,
            .hero-inner,
            .legal-shell,
            .site-footer {
                width: min(100% - 32px, 1160px);
            }

            .site-nav {
                min-height: 66px;
                gap: 10px;
            }

            .header-links {
                gap: 0;
            }

            .header-links a {
                padding: 8px;
                font-size: .76rem;
            }

            .header-links .home-link {
                display: none;
            }

            .hero-inner {
                padding: 35px 0 30px;
            }

            .hero-inner h1 {
                font-size: clamp(1.85rem, 8vw, 2.45rem);
            }

            .hero-summary {
                margin: 12px 0 18px;
                font-size: .93rem;
            }

            .legal-paper {
                padding: 24px 20px;
                border-radius: 11px;
            }

            .document-toc {
                grid-template-columns: 1fr;
            }

            .legal-paper h2 {
                font-size: 1.12rem;
            }

            .legal-paper p,
            .legal-paper li {
                font-size: .92rem;
            }

            .site-footer {
                align-items: flex-start;
                flex-direction: column;
                gap: 8px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            :root {
                scroll-behavior: auto;
            }
        }
    </style>
    @vite(['resources/js/legal-consent.js'])
</head>
<body>
<a class="skip-link" href="#document-content">Skip to document content</a>

<header class="site-header">
    <nav class="site-nav" aria-label="Main navigation">
        <a class="brand" href="{{ route('landing.page') }}">
            <span class="brand-mark" aria-hidden="true">SE</span>
            <span class="brand-name">ShopEase<small>Legal center</small></span>
        </a>
        <div class="header-links">
            <a href="@yield('termsUrl', route('terms-and-conditions'))">Terms &amp; Conditions</a>
            <a href="@yield('privacyUrl', route('privacy-policy'))">Privacy Policy</a>
            <a class="home-link" href="{{ route('landing.page') }}">Back to ShopEase</a>
        </div>
    </nav>
</header>

<section class="document-hero" aria-labelledby="document-title">
    <div class="hero-inner">
        <a class="legal-back" href="{{ $legalReturnUrl }}">
            <span aria-hidden="true">&larr;</span>
            {{ $hasRegistrationReturn ? 'Back to registration' : 'Back to ShopEase' }}
        </a>
        <p class="breadcrumb">ShopEase <span aria-hidden="true">/</span> Legal center</p>
        <span class="document-type">@yield('documentType')</span>
        <h1 id="document-title">@yield('heading')</h1>
        <p class="hero-summary">@yield('summary')</p>
    </div>
</section>

<main class="legal-shell" id="document-content">
    <aside class="legal-sidebar" aria-label="Document navigation">
        <h2 class="sidebar-heading">In this document</h2>
        <nav class="document-toc" aria-label="Table of contents">
            @yield('toc')
        </nav>
        <div class="sidebar-divider"></div>
        <h2 class="sidebar-heading">Related documents</h2>
        <div class="other-documents">
            <a href="@yield('termsUrl', route('terms-and-conditions'))">Terms &amp; Conditions</a>
            <a href="@yield('privacyUrl', route('privacy-policy'))">Privacy Policy</a>
        </div>
    </aside>

    <article
        class="legal-paper"
        data-legal-consent-page
        data-legal-document="@yield('documentKey')"
        data-legal-role="@yield('legalRole')"
        data-legal-version="@yield('documentVersion')"
    >
        @yield('content')
    </article>
</main>

<footer class="site-footer">
    <span>ShopEase Legal Center</span>
    <div class="footer-links">
        <a href="@yield('termsUrl', route('terms-and-conditions'))">Terms &amp; Conditions</a>
        <a href="@yield('privacyUrl', route('privacy-policy'))">Privacy Policy</a>
        <a href="mailto:support@shopease.com">Contact support</a>
    </div>
</footer>
</body>
</html>
