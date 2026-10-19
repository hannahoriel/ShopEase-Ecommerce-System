{{-- ShopEase footer component: markup + all footer CSS in one Blade file. --}}
<style>
/* ShopEase original footer, separated from landing-page.blade.php */
:root{--peach:#FF876E;--maroon-dark:#52070B;}
        .landing-footer {
            width: 100%;
            margin: 0;
            padding: 30px 5.5% 14px;

            display: block;

            background:
                linear-gradient(145deg, #3D080C 0%, #52070B 52%, #650E14 100%);

            color: rgba(255,255,255,0.70);
            border-top: 1px solid rgba(255,255,255,0.07);
        }


        .footer-inner {
            width: min(1360px, 100%);
            margin: 0 auto;
        }


        .footer-main {
            display: grid;
            grid-template-columns: 1.45fr repeat(3, 1fr);
            gap: 32px;
            padding-bottom: 26px;
            align-items: start;
        }


        .footer-brand {
            display: block;
            min-width: 0;
        }


        .footer-brand img {
            width: 170px;
            max-width: 100%;
            height: auto;
            display: block;
            object-fit: contain;
            margin: 0 0 15px;
            transform: translateY(-0px);
        }


        .footer-brand-tagline {
            max-width: 290px;
            margin: 0;
            font-size: 12px;
            line-height: 1.75;
            color: rgba(255,255,255,0.62);
        }


        .footer-trust-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 12px;
        }


        .footer-trust-chip {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 10px;
            border: 1px solid rgba(255,255,255,0.13);
            border-radius: 999px;
            background: rgba(255,255,255,0.045);
            color: rgba(255,255,255,0.68);
            font-size: 9px;
            font-weight: 600;
        }


        .footer-trust-chip svg {
            width: 12px;
            height: 12px;
            color: var(--peach);
            flex-shrink: 0;
        }


        .footer-column {
            min-width: 0;
        }


        .footer-column > div[style*="margin-top:18px"] {
            margin-top: 12px !important;
        }


        .footer-column-title {
            margin: 0 0 12px;
            font-size: 12px;
            font-weight: 700;
            color: #FFFFFF;
            letter-spacing: 0.15px;
        }


        .footer-links {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }


        .footer-link {
            width: fit-content;
            font-size: 11px;
            color: rgba(255,255,255,0.58);
            transition: color 0.2s ease, transform 0.2s ease;
        }


        .footer-link:hover {
            color: var(--peach);
            transform: translateX(2px);
        }


        .footer-contact-item {
            display: flex;
            gap: 9px;
            align-items: flex-start;
            margin-bottom: 8px;
        }


        .footer-contact-icon {
            width: 14px;
            height: 14px;
            flex: 0 0 14px;
            color: var(--peach);
            margin-top: 2px;
        }


        .footer-contact-text {
            font-size: 10.5px;
            line-height: 1.55;
            color: rgba(255,255,255,0.58);
        }


        .footer-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding-top: 12px;
            border-top: 1px solid rgba(255,255,255,0.10);
        }


        .footer-text {
            margin: 0;
            font-size: 9.5px;
            line-height: 1.6;
            color: rgba(255,255,255,0.48);
        }


        .footer-legal {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }


        .footer-legal a {
            font-size: 9px;
            color: rgba(255,255,255,0.45);
            transition: color 0.2s ease;
        }


        .footer-legal a:hover {
            color: #FFFFFF;
        }


        .footer-socials {
            display: flex;
            align-items: center;
            gap: 7px;
            margin-top: 12px;
        }


        .footer-social {
            width: 31px;
            height: 31px;
            border: 1px solid rgba(255,255,255,0.22);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FFFFFF;
            background: rgba(255,255,255,0.025);
            transition: all 0.25s ease;
        }


        .footer-social:hover {
            background: var(--peach);
            border-color: var(--peach);
            color: var(--maroon-dark);
            transform: translateY(-2px);
        }


        .footer-social svg {
            width: 13px;
            height: 13px;
        }




@media(max-width:850px){
 .footer-main{grid-template-columns:repeat(2,minmax(0,1fr));gap:26px;}
 .footer-brand{grid-column:1/-1;}
}
@media(max-width:600px){
 .landing-footer{width:100%;padding:33px 18px 20px;overflow:visible;}
 .footer-main{grid-template-columns:repeat(2,minmax(0,1fr));gap:24px 15px;}
 .footer-brand{grid-column:1/-1;}
 .footer-brand img{width:144px;height:auto;margin:0 0 12px;max-width:100%;}
 .footer-brand-tagline{max-width:100%;font-size:11px;}
 .footer-column-title{font-size:12px;}
 .footer-link{font-size:10px;}
 .footer-trust-row{gap:7px;}
 .footer-trust-chip{font-size:9px;padding:7px;}
 .footer-bottom{flex-direction:column;align-items:flex-start;gap:12px;}
}
@media(max-width:375px){.footer-main{gap:21px 11px;}}


/* Additional original landing-page footer responsive styles */
@media (max-width: 760px) {
.landing-footer {
                padding: 36px 18px 14px;
            }
.footer-main {
                grid-template-columns: 1fr 1fr;
                gap: 24px 18px;
                padding-bottom: 22px;
            }
.footer-brand {
                grid-column: 1 / -1;
            }
.footer-brand img {
                width: 115px;
                margin-top: 0;
                margin-bottom: 15px;
                transform: none;
            }
.footer-brand-tagline {
                max-width: 430px;
                font-size: 11px;
            }
.footer-column-title {
                margin-bottom: 13px;
                font-size: 11px;
            }
.footer-link {
                font-size: 10px;
            }
.footer-contact-text {
                font-size: 9.5px;
            }
.footer-bottom {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
}
.landing-footer {
            opacity: 1 !important;
            transform: none !important;
            transition: none !important;
        }
@media(max-width:600px){
.footer-main{grid-template-columns:repeat(2,minmax(0,1fr));gap:25px 14px}
.footer-column{min-width:0}
.footer-brand{grid-column:1/-1}
}
.footer-brand img {width:clamp(120px,12vw,170px);height:auto;max-width:100%;
    margin:0 0 15px;position:static;transform:none;object-fit:contain;object-position:left center;}
.footer-brand {min-width:0;}
.footer-main,.footer-inner,.footer-column {min-width:0;}
.footer-link {overflow-wrap:anywhere;}
@media (max-width:1150px) {
.footer-main {grid-template-columns:1.3fr repeat(3,minmax(0,1fr));gap:20px;}
}
@media (max-width:850px) {
.footer-main {grid-template-columns:repeat(2,minmax(0,1fr));gap:26px;}
.footer-brand {grid-column:1/-1;}
}
@media (max-width:600px) {
.landing-footer {width:100%;padding:33px 18px 20px;overflow:visible;}
.footer-main {grid-template-columns:repeat(2,minmax(0,1fr));gap:24px 15px;}
.footer-brand {grid-column:1/-1;}
.footer-brand img {width:144px;height:auto;margin:0 0 12px;max-width:100%;}
.footer-brand-tagline {max-width:100%;font-size:11px;}
.footer-column-title {font-size:12px;}
.footer-link {font-size:10px;}
.footer-trust-row {gap:7px;}
.footer-trust-chip {font-size:9px;padding:7px;}
.footer-bottom {flex-direction:column;align-items:flex-start;gap:12px;}
}
@media (max-width:375px) {
.footer-main {gap:21px 11px;}
}
</style>
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
                        <a href="{{ url('/logistics/partner') }}" class="footer-link">Logistics Partnership</a>
                        <a href="#mobileApp" class="footer-link">Download Mobile App</a>
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
