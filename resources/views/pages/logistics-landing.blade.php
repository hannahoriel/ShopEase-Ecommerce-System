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
@vite(['resources/css/app.css', 'resources/css/logistics/logistics-landing.css', 'resources/js/logistics/logistics-landing.js'])

</head>
<body>
<div class="landing-page">
<header class="landing-navbar">
  <a href="{{ url('/') }}" class="landing-logo" aria-label="ShopEase home"><img src="{{ asset('icons/logos/shop-ease.png') }}" class="landing-logo-wordmark" alt="ShopEase"></a>
  <nav class="landing-nav-quick" aria-label="Page navigation"><a href="{{ url('/') }}">Home</a><a href="#opportunities">Why Partner</a></nav>
  <div class="landing-nav-actions">
    <!-- Logistics login opens the dedicated Blade modal. -->
    <button class="login-button" type="button" id="logisticsLogin" aria-haspopup="dialog" aria-controls="logisticsLoginModal">Log In</button>
    <button class="signup-button" type="button" id="logisticsSignup">Sign Up</button>
  </div>
</header>
<main>
@if (session('success'))
<div role="status" style="max-width:1100px;margin:20px auto 0;padding:16px 20px;border:1px solid #b7dfc0;border-radius:12px;background:#effaf1;color:#205b2c;font-weight:600;">{{ session('success') }}</div>
@endif
<section class="hero" aria-labelledby="logisticsHeading" id="home">
<div class="hero-bg-circle one"></div><div class="hero-bg-circle two"></div><div class="hero-bg-circle three"></div>
<div class="hero-copy">
  <h1 class="hero-title" id="logisticsHeading">Deliver More,<br><span>Grow Together.</span></h1>
  <p class="hero-description">Partner with ShopEase and connect your logistics company to a growing marketplace. Let's make every delivery easier, together.</p>
  <div class="hero-actions"><a class="hero-primary" id="heroSignup" href="#partnershipProcess">How Partnership Works <span aria-hidden="true">↗</span></a><a class="hero-secondary" href="{{ url('/') }}#logisticsPartners">Explore ShopEase ↗</a></div>
  <p class="logistics-note">For logistics companies interested in partnering with ShopEase.</p>
</div>
<div class="hero-visual">
  <div class="visual-card" id="visualCard">
    <div class="visual-circle one"></div><div class="visual-circle two"></div>
    <span class="logistics-stamp">SHOPEASE LOGISTICS</span>
    <div class="delivery-graphic"><svg class="truck-svg" viewBox="0 0 360 184" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Illustration of a delivery truck"><ellipse cx="181" cy="164" rx="151" ry="15" fill="#30080D" fill-opacity=".29"/><rect x="23" y="65" width="191" height="85" rx="15" fill="#FFF1EA"/><rect x="32" y="74" width="173" height="67" rx="9" fill="#FFD8C9"/><rect x="50" y="88" width="80" height="8" rx="4" fill="#A52A2A"/><rect x="50" y="102" width="117" height="5" rx="2.5" fill="#C97467"/><rect x="50" y="112" width="100" height="5" rx="2.5" fill="#C97467"/><path d="M214 95H272L322 132V150H214V95Z" fill="#F8AF96"/><path d="M274 102V132H305L274 102Z" fill="#FFEDE6"/><path d="M238 106H261V132H238V106Z" fill="#FFEDE6"/><circle cx="91" cy="150" r="23" fill="#4C131B"/><circle cx="91" cy="150" r="11" fill="#FFF1EA"/><circle cx="271" cy="150" r="23" fill="#4C131B"/><circle cx="271" cy="150" r="11" fill="#FFF1EA"/><path d="M22 139H321" stroke="#FFCFBF" stroke-width="7" stroke-linecap="round"/></svg></div>
    <div class="logistics-glass top">📦 Pickup ready<small>From seller to doorstep</small></div>
    <div class="logistics-glass bottom">✓ Delivery made simple<small>Powered by partnership</small></div>
  </div>
</div>
</section>

<section class="logistics-content" aria-label="ShopEase logistics partnership information">
  <section class="logistics-section" id="opportunities">
    <div class="logistics-shell">
      <div class="logistics-center logistics-reveal">
        <span class="logistics-eyebrow">Why partner with ShopEase?</span>
        <h2 class="logistics-heading">Move your business <span>forward.</span></h2>
        <p class="logistics-intro">ShopEase connects buyers and sellers through its marketplace. Logistics companies interested in supporting this experience can explore a partnership focused on bringing orders from sellers to customers.</p>
      </div>
      <div class="logistics-feature-grid">
        <article class="logistics-feature logistics-reveal"><div class="logistics-feature-icon" aria-hidden="true">↗</div><h3>Explore delivery opportunities</h3><p>Discover how your company's existing delivery operations could support orders placed through ShopEase, subject to partnership approval and operational arrangements.</p></article>
        <article class="logistics-feature logistics-reveal"><div class="logistics-feature-icon" aria-hidden="true">▣</div><h3>Connect sellers and buyers</h3><p>Be part of a marketplace experience where dependable coordination between sellers, delivery teams, and customers matters.</p></article>
        <article class="logistics-feature logistics-reveal"><div class="logistics-feature-icon" aria-hidden="true">✓</div><h3>Build a trusted partnership</h3><p>Learn the proposed application and verification process so both sides can assess whether the partnership is a good fit.</p></article>
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
{{-- Shared ShopEase footer: resources/views/components/landing page/footer.blade.php --}}
@include('components.landing page.footer')
</div>

</body>
</html>
