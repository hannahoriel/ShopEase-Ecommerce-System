<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#FBF8F6">
<title>Logistics Partners — ShopEase</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@vite(['resources/css/app.css', 'resources/css/logistics/logistics-landing.css', 'resources/css/logistics/logistics-hero.css', 'resources/js/logistics/logistics-landing.js', 'resources/js/logistics/logistics-hero.js'])

</head>
<body>
<div class="landing-page">
<header class="landing-navbar">
  <a href="{{ url('/') }}" class="landing-logo" aria-label="ShopEase home"><img src="{{ asset('icons/logos/shop-ease.png') }}" class="landing-logo-wordmark" alt="ShopEase"></a>
  <nav class="landing-nav-quick" aria-label="Page navigation"><a href="{{ url('/') }}">Home</a><a href="#opportunities">Why Partner</a></nav>
  <div class="landing-nav-actions">
    <!-- Logistics login opens the dedicated Blade modal. -->
    <button class="login-button" type="button" id="logisticsLogin" data-loading="off" aria-haspopup="dialog" aria-controls="logisticsLoginModal">Log In</button>
    <button class="signup-button" type="button" id="logisticsSignup" data-loading="off">Sign Up</button>
  </div>
</header>
<main>
@if (session('success'))
<div class="logistics-registration-flash-wrap" role="status" aria-live="polite" aria-atomic="true">
  <div class="logistics-registration-flash">
    <span class="logistics-registration-flash-icon" aria-hidden="true">✓</span>
    <div class="logistics-registration-flash-content">
      <strong>Registration submitted successfully</strong>
      <p>{{ session('success') }}</p>
    </div>
    <button type="button" class="logistics-registration-flash-close" aria-label="Dismiss registration notification" onclick="this.closest('.logistics-registration-flash-wrap').remove()">&times;</button>
  </div>
</div>
@endif
<section class="lx-hero" aria-labelledby="logisticsHeading" id="home">

  <canvas class="lx-net" id="lxNet" aria-hidden="true"></canvas>
  <div class="lx-aurora" aria-hidden="true"><i></i><i></i><i></i></div>
  <div class="lx-grid" aria-hidden="true"></div>
  <div class="lx-spot" id="lxSpot" aria-hidden="true"></div>

  <div class="lx-inner">

    <!-- ================= COPY ================= -->
    <div class="lx-copy" id="lxCopy">

      <div class="lx-chip">
        <span class="lx-pulse"></span>
        <span class="lx-chip-label">ShopEase Logistics. Built for</span>
        <span class="lx-rotor" aria-hidden="true">
          <span class="lx-rotor-track"><b>Couriers</b><b>Hubs</b><b>Riders</b><b>Fleets</b><b>Couriers</b></span>
        </span>
      </div>

      <h1 class="lx-title" id="logisticsHeading" aria-label="Deliver More, Grow Together.">
        <span class="lx-line" aria-hidden="true"><span class="lx-w" style="--i:0">Deliver</span> <span class="lx-w" style="--i:1">More,</span></span>
        <span class="lx-line lx-grad" aria-hidden="true"><span class="lx-w" style="--i:2">Grow</span> <span class="lx-w" style="--i:3">Together.</span></span>
      </h1>

      <p class="lx-desc">Partner with ShopEase and connect your logistics company to a growing marketplace. Let's make every delivery easier, together.</p>

      <div class="lx-actions">
        <a class="lx-btn lx-btn-primary lx-magnetic" id="heroSignup" href="#partnershipProcess">
          <span class="lx-btn-shine" aria-hidden="true"></span>
          <span class="lx-btn-text">How Partnership Works</span>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="M13 6l6 6-6 6"/></svg>
        </a>
        <a class="lx-btn lx-btn-ghost lx-magnetic" href="{{ url('/') }}#logisticsPartners">
          <span class="lx-btn-text">Explore ShopEase</span>
        </a>
      </div>

      <ul class="lx-trust">
        <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3 5 6v5c0 4.5 3 8.2 7 10 4-1.8 7-5.5 7-10V6l-7-3Z"/><path d="m9 12 2 2 4-4"/></svg>Company verification</li>
        <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h11v11H3zM14 10h4l3 4v3h-7z"/><circle cx="7.5" cy="18" r="2"/><circle cx="17.5" cy="18" r="2"/></svg>Seller to doorstep</li>
        <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s7-5.6 7-11a7 7 0 1 0-14 0c0 5.4 7 11 7 11Z"/><circle cx="12" cy="10" r="2.5"/></svg>Live parcel tracking</li>
      </ul>

      <p class="lx-note">For logistics companies interested in partnering with ShopEase.</p>
    </div>

    <!-- ================= LIVE NETWORK STAGE ================= -->
    <div class="lx-stage" id="lxStage" aria-hidden="true">
      <div class="lx-fit" id="lxFit">
        <div class="lx-scene" id="lxScene">

          <div class="lx-glow"></div>
          <div class="lx-orbit o1"><i></i><i></i></div>
          <div class="lx-orbit o2"><i></i></div>

          <!-- NETWORK MAP -->
          <div class="lx-card lx-map">
            <div class="lx-card-top">
              <span class="lx-dots"><i></i><i></i><i></i></span>
              <span class="lx-card-title">ShopEase Logistics · Live network</span>
              <span class="lx-live"><i></i>LIVE</span>
            </div>

            <svg class="lx-mapsvg" viewBox="0 0 380 190" fill="none">
              <defs>
                <pattern id="lxDots" width="14" height="14" patternUnits="userSpaceOnUse"><circle cx="1" cy="1" r=".9" fill="rgba(255,255,255,.13)"/></pattern>
                <linearGradient id="lxRg" x1="0" x2="1"><stop offset="0" stop-color="#FF876E"/><stop offset="1" stop-color="#FFD9CC"/></linearGradient>
              </defs>
              <rect width="380" height="190" rx="12" fill="url(#lxDots)"/>
              <g fill="rgba(255,255,255,.045)">
                <rect x="56" y="96" width="34" height="24" rx="5"/><rect x="150" y="112" width="40" height="28" rx="5"/>
                <rect x="268" y="66" width="36" height="26" rx="5"/><rect x="300" y="108" width="30" height="22" rx="5"/>
                <rect x="170" y="140" width="34" height="22" rx="5"/><rect x="84" y="24" width="38" height="26" rx="5"/>
              </g>

              <path d="M30 150 C 70 150, 90 70, 130 70 S 200 110, 240 110 S 320 30, 345 40" stroke="rgba(255,255,255,.15)" stroke-width="3" stroke-linecap="round" stroke-dasharray="2 7"/>
              <path d="M240 110 C 262 140, 282 152, 305 150" stroke="rgba(255,255,255,.15)" stroke-width="3" stroke-linecap="round" stroke-dasharray="2 7"/>
              <path d="M130 70 C 150 34, 195 24, 235 38" stroke="rgba(255,255,255,.15)" stroke-width="3" stroke-linecap="round" stroke-dasharray="2 7"/>

              <path id="lxR1" class="lx-fill" d="M30 150 C 70 150, 90 70, 130 70 S 200 110, 240 110 S 320 30, 345 40" stroke="url(#lxRg)" stroke-width="3.2" stroke-linecap="round"/>
              <path id="lxR2" class="lx-fill" d="M240 110 C 262 140, 282 152, 305 150" stroke="url(#lxRg)" stroke-width="3.2" stroke-linecap="round"/>
              <path id="lxR3" class="lx-fill" d="M130 70 C 150 34, 195 24, 235 38" stroke="url(#lxRg)" stroke-width="3.2" stroke-linecap="round"/>

              <g class="lx-hub" transform="translate(30 150)"><circle class="ring" r="6"/><circle r="5"/><text y="18">Seller</text></g>
              <g class="lx-hub" transform="translate(130 70)"><circle class="ring" r="6"/><circle r="5"/><text y="-11">Sorting Center</text></g>
              <g class="lx-hub" transform="translate(240 110)"><circle class="ring" r="6"/><circle r="5"/><text y="18">Delivery Hub</text></g>
              <g class="lx-hub" transform="translate(345 40)"><circle class="ring" r="6"/><circle r="5"/><text y="18">Buyer</text></g>
              <g class="lx-hub" transform="translate(305 150)"><circle class="ring" r="6"/><circle r="5"/><text y="18">Buyer</text></g>
              <g class="lx-hub" transform="translate(235 38)"><circle class="ring" r="6"/><circle r="5"/><text y="-11">Hub</text></g>

              <g class="lx-truck" data-route="lxR1" data-dur="9000" data-off="0"><rect x="-9" y="-5" width="14" height="10" rx="2.5"/><path d="M5 -3h4l3 3v3H5z"/></g>
              <g class="lx-truck" data-route="lxR2" data-dur="5200" data-off="1800"><rect x="-9" y="-5" width="14" height="10" rx="2.5"/><path d="M5 -3h4l3 3v3H5z"/></g>
              <g class="lx-truck" data-route="lxR3" data-dur="6800" data-off="900"><rect x="-9" y="-5" width="14" height="10" rx="2.5"/><path d="M5 -3h4l3 3v3H5z"/></g>
            </svg>

            <div class="lx-stats">
              <div><small>Active routes</small><strong id="lxRoutes" data-count="24">0</strong></div>
              <div><small>Parcels in transit</small><strong id="lxParcels" data-count="1284">0</strong></div>
              <div><small>Hubs online</small><strong id="lxHubs" data-count="12">0</strong></div>
            </div>
          </div>

          <!-- PARCEL SCAN -->
          <div class="lx-card lx-scan" id="lxScan">
            <div class="lx-mini-top"><span><i class="lx-dot"></i>Parcel scan</span><strong id="lxScanState">Scanning…</strong></div>
            <div class="lx-scan-body">
              <div class="lx-qr" id="lxQr"><i class="lx-scanline"></i></div>
              <div class="lx-scan-info">
                <b>#SE-20841</b>
                <small>Sorting Center</small>
                <span class="lx-scan-ok">✓ Received</span>
              </div>
            </div>
          </div>

          <!-- RIDER -->
          <div class="lx-card lx-rider">
            <div class="lx-mini-top"><span><i class="lx-dot"></i>Rider assigned</span><strong id="lxEta">ETA 12 min</strong></div>
            <div class="lx-rider-body">
              <span class="lx-avatar"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="5" cy="17" r="3"/><circle cx="19" cy="17" r="3"/><path d="m5 17 4-8h4l3 8h3M8 13h8l-2-4M13 9l2-3h3M7 7h3"/></svg></span>
              <div><b>Delivery Hub → Buyer</b><small id="lxRiderState">Out for delivery</small></div>
            </div>
            <div class="lx-bar"><i id="lxBarFill"></i></div>
          </div>

          <!-- TOAST -->
          <div class="lx-card lx-toast">
            <span class="lx-toast-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"/><path d="m4 7.5 8 4.5 8-4.5M12 12v9"/></svg></span>
            <div><b>Pickup ready</b><small>From seller to doorstep</small></div>
          </div>

          <!-- PARTNER STATUS -->
          <div class="lx-card lx-partner" id="lxPartner" data-step="0">
            <span class="lx-steps"><i></i><i></i><i></i></span>
            <span class="lx-partner-label" id="lxPartnerLabel">Application sent</span>
          </div>

        </div>
      </div>
    </div>

  </div>

  <a href="#opportunities" class="lx-scroll" aria-label="Scroll to explore">
    <span class="lx-mouse"><i></i></span>
    <span class="lx-scroll-text">Scroll to explore</span>
  </a>

</section>

<div class="lx-ticker" aria-hidden="true">
  <div class="lx-ticker-track">
    <span>Pickup</span><span>Sorting Center</span><span>Delivery Hub</span><span>Rider Dispatch</span><span>Parcel Scan</span><span>Live Tracking</span><span>Proof of Delivery</span><span>COD Settlement</span>
    <span>Pickup</span><span>Sorting Center</span><span>Delivery Hub</span><span>Rider Dispatch</span><span>Parcel Scan</span><span>Live Tracking</span><span>Proof of Delivery</span><span>COD Settlement</span>
  </div>
</div>

<section class="logistics-content" aria-label="ShopEase logistics partnership information">
  <section class="logistics-section" id="opportunities">
    <div class="logistics-shell">
      <div class="logistics-center logistics-reveal">
        <span class="logistics-eyebrow">Why partner with ShopEase?</span>
        <h2 class="logistics-heading">Move your business <span>forward.</span></h2>
        <p class="logistics-intro">ShopEase connects buyers and sellers through its marketplace. Logistics companies interested in supporting this experience can explore a partnership focused on bringing orders from sellers to customers.</p>
      </div>
      <div class="logistics-feature-grid">
        <article class="logistics-feature logistics-reveal"><div class="logistics-feature-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 16l5-5 4 4 7-8"/><path d="M15 7h5v5"/></svg></div><h3>Explore delivery opportunities</h3><p>Discover how your company's existing delivery operations could support orders placed through ShopEase, subject to partnership approval and operational arrangements.</p></article>
        <article class="logistics-feature logistics-reveal"><div class="logistics-feature-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="6" r="2.5"/><circle cx="18" cy="6" r="2.5"/><circle cx="12" cy="18" r="2.5"/><path d="M8 7.5l3 8M16 7.5l-3 8M8.5 6h7"/></svg></div><h3>Connect sellers and buyers</h3><p>Be part of a marketplace experience where dependable coordination between sellers, delivery teams, and customers matters.</p></article>
        <article class="logistics-feature logistics-reveal"><div class="logistics-feature-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 5 6v5c0 4.5 3 8.2 7 10 4-1.8 7-5.5 7-10V6l-7-3Z"/><path d="m9 12 2 2 4-4"/></svg></div><h3>Build a trusted partnership</h3><p>Learn the proposed application and verification process so both sides can assess whether the partnership is a good fit.</p></article>
      </div>
    </div>
  </section>
  <section class="logistics-section alt" id="partnershipProcess">
    <div class="logistics-shell logistics-process-grid">
      <div class="logistics-reveal"><span class="logistics-eyebrow">How it works</span><h2 class="logistics-heading">A clear path to <span>partnership.</span></h2><p class="logistics-intro">Interested companies can learn about ShopEase and prepare for the partnership process. Registration and actual logistics operations will depend on the routes, requirements, and integrations implemented in the system.</p></div>
      <div class="logistics-steps logistics-reveal">
        <article class="logistics-step"><span class="logistics-step-num">01</span><div><h3>Explore the opportunity</h3><p>Review what ShopEase is building and consider how the marketplace may align with your company's services and delivery coverage.</p></div></article>
        <article class="logistics-step"><span class="logistics-step-num">02</span><div><h3>Provide company information</h3><p>Register your company and submit the requested details and supporting documents for review.</p></div></article>
        <article class="logistics-step"><span class="logistics-step-num">03</span><div><h3>Company review</h3><p>Submitted information is reviewed before a company can be approved as a logistics partner. Application does not guarantee approval.</p></div></article>
        <article class="logistics-step"><span class="logistics-step-num">04</span><div><h3>Plan the next steps</h3><p>If approved, the logistics company and ShopEase can proceed with the applicable onboarding and operational arrangements.</p></div></article>
      </div>
    </div>
  </section>
  <section class="lx-cta" aria-labelledby="lxCtaTitle">
    <div class="lx-cta-glow" aria-hidden="true"></div>
    <div class="logistics-shell lx-cta-inner logistics-reveal">
      <div class="lx-cta-copy">
        <span class="lx-cta-eyebrow">Ready to partner?</span>
        <h2 id="lxCtaTitle">Let's move more <span>together.</span></h2>
        <p>Start your company application or log in to continue. Application does not guarantee approval.</p>
      </div>
      <div class="lx-cta-actions">
        <button type="button" class="lx-btn lx-btn-primary" id="lxCtaSignup"><span class="lx-btn-shine" aria-hidden="true"></span><span class="lx-btn-text">Start application</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="M13 6l6 6-6 6"/></svg></button>
        <button type="button" class="lx-btn lx-btn-ghost" id="lxCtaLogin"><span class="lx-btn-text">Log in</span></button>
      </div>
    </div>
  </section>
</section>
</main>

{{-- Modal contents come from resources/views/pages/logistics landing page/login.blade.php --}}
<div class="logistics-login-overlay" id="logisticsLoginModal" aria-hidden="true">
  <div class="logistics-login-dialog" role="dialog" aria-modal="true" aria-label="Logistics Company Login" tabindex="-1">
    <button type="button" class="logistics-login-close" id="logisticsLoginClose" aria-label="Close logistics login">&times;</button>
    @include('pages.logistics landing page.login')
  </div>
</div>
{{-- Expands just like the original ShopEase registration modal before full-screen navigation. --}}
<div class="logistics-signup-transition" id="logisticsSignupTransition" aria-hidden="true"><div class="logistics-signup-transition-card"><iframe class="logistics-signup-transition-iframe" id="logisticsSignupPreview" title="Logistics Company Registration" src="about:blank" tabindex="-1" aria-hidden="true"></iframe></div></div>
{{-- Successful logistics login expands the actual dashboard, matching ShopEase's auth-modal handoff. --}}
<div class="logistics-signup-transition logistics-dashboard-transition" id="logisticsDashboardTransition" aria-hidden="true">
  <div class="logistics-signup-transition-card">
    <iframe class="logistics-signup-transition-iframe" id="logisticsDashboardPreview" title="Logistics Dashboard" src="about:blank" tabindex="-1" aria-hidden="true"></iframe>
  </div>
</div>
{{-- Shared ShopEase footer: resources/views/components/landing page/footer.blade.php --}}
@include('components.landing page.footer')
</div>

</body>
</html>
