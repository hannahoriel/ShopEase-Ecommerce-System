
<!-- =========================================================
         REALISTIC FOOTER
    ========================================================== -->

    @vite([
    'resources/css/buyer/components/footer.css',
])

<footer class="landing-footer">

        <div class="footer-inner">

            <div class="footer-main">

                <div class="footer-brand">

                    <img
                        src="{{ asset('icons/logos/ShopEase.png') }}"
                        alt="ShopEase"
                    >

                    <p class="footer-brand-tagline">
                        Shop smarter, discover products you love,
                        and enjoy a smoother experience from checkout
                        to delivery.
                    </p>

                    <div class="footer-trust-row">

                        <span class="footer-trust-chip">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="4" y="10" width="16" height="10" rx="2" />
                                <path d="M8 10V7a4 4 0 0 1 8 0v3" />
                            </svg>
                            Secure Checkout
                        </span>

                        <span class="footer-trust-chip">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 7h11v10H3z" />
                                <path d="M14 10h4l3 3v4h-7z" />
                                <circle cx="7" cy="19" r="1.5" />
                                <circle cx="18" cy="19" r="1.5" />
                            </svg>
                            Order Tracking
                        </span>

                    </div>

                    <div class="footer-socials">

                        <a href="#" class="footer-social" aria-label="Facebook">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M14 8h3V5h-3c-2.8 0-4 1.8-4 4v2H7v3h3v5h3v-5h3l.5-3H13V9c0-.7.3-1 1-1z" />
                            </svg>
                        </a>

                        <a href="#" class="footer-social" aria-label="Instagram">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                <rect x="4" y="4" width="16" height="16" rx="4" />
                                <circle cx="12" cy="12" r="3.5" />
                                <circle cx="17.2" cy="6.8" r="1" fill="currentColor" stroke="none" />
                            </svg>
                        </a>

                        <a href="#" class="footer-social" aria-label="TikTok">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M15 4v9.2a4.8 4.8 0 1 1-4-4.7v2.8a2 2 0 1 0 1 1.9V4h3z" />
                            </svg>
                        </a>

                        <a href="#" class="footer-social" aria-label="YouTube">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M21 8.2a2.6 2.6 0 0 0-1.8-1.8C17.6 6 12 6 12 6s-5.6 0-7.2.4A2.6 2.6 0 0 0 3 8.2 27 27 0 0 0 2.6 12 27 27 0 0 0 3 15.8a2.6 2.6 0 0 0 1.8 1.8C6.4 18 12 18 12 18s5.6 0 7.2-.4a2.6 2.6 0 0 0 1.8-1.8 27 27 0 0 0 .4-3.8 27 27 0 0 0-.4-3.8z" />
                                <path d="M10 9.5l5 2.5-5 2.5z" fill="#3D080C" />
                            </svg>
                        </a>

                    </div>

                </div>


                <div class="footer-column">

                    <h3 class="footer-column-title">
                        Shop
                    </h3>

                    <div class="footer-links">
                        <a href="#guestShop" class="footer-link">All Products</a>
                        <a href="#guestShop" class="footer-link">Electronics &amp; Gadgets</a>
                        <a href="#guestShop" class="footer-link">Women's Apparel</a>
                        <a href="#guestShop" class="footer-link">Home &amp; Garden</a>
                        <a href="#guestShop" class="footer-link">Health &amp; Beauty</a>
                        <a href="#guestShop" class="footer-link">Great Deals</a>
                    </div>

                </div>


                <div class="footer-column">

                    <h3 class="footer-column-title">
                        Help &amp; Support
                    </h3>

                    <div class="footer-links">
                        <a href="#" class="footer-link">Order Tracking</a>
                        <a href="#" class="footer-link">Shipping Information</a>
                        <a href="#" class="footer-link">Returns &amp; Refunds</a>
                        <a href="#" class="footer-link">Contact Support</a>
                        <a href="#" class="footer-link">Frequently Asked Questions</a>
                    </div>

                </div>


                <div class="footer-column">

                    <h3 class="footer-column-title">
                        Sell on ShopEase
                    </h3>

                    <div class="footer-links">
                        <a href="#" class="footer-link">Become a Seller</a>
                        <a href="#" class="footer-link">Seller Guidelines</a>
                        <a href="#" class="footer-link">Seller Compliance</a>
                        <a href="#" class="footer-link">Seller Support</a>
                    </div>

                    <div style="margin-top:18px;">

                        <div class="footer-contact-item">
                            <svg class="footer-contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 5h16v14H4z" />
                                <path d="m4 7 8 6 8-6" />
                            </svg>
                            <span class="footer-contact-text">
                                support@shopease.com
                            </span>
                        </div>

                        <div class="footer-contact-item">
                            <svg class="footer-contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6.5 3.5h3l1.5 4-2 1.6a14 14 0 0 0 5.9 5.9l1.6-2 4 1.5v3c0 .8-.7 1.5-1.5 1.5C11.1 19 5 12.9 5 5c0-.8.7-1.5 1.5-1.5z" />
                            </svg>
                            <span class="footer-contact-text">
                                Customer Care · Mon–Sat
                            </span>
                        </div>

                    </div>

                </div>

            </div>


            <div class="footer-bottom">

                <p class="footer-text">
                    Shop Easier, Live Better. © {{ date('Y') }} ShopEase.
                </p>

                <div class="footer-legal">
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                    <a href="#">Cookie Policy</a>
                </div>

            </div>

        </div>

    </footer>
