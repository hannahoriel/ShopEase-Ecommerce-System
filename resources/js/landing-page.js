document.addEventListener(
    'DOMContentLoaded',
    function () {


        /* =====================================================
           HERO VISUAL MOTION
        ====================================================== */

        const heroVisual =
            document.querySelector(
                '.hero-visual'
            );


        const visualCard =
            document.getElementById(
                'visualCard'
            );


        if (
            heroVisual &&
            visualCard
        ) {

            const supportsDesktopMotion =
                window.matchMedia(
                    '(min-width: 1051px)'
                ).matches;


            let targetMouseX = 0;

            let targetMouseY = 0;

            let mouseX = 0;

            let mouseY = 0;


            if (
                supportsDesktopMotion
            ) {

                heroVisual.addEventListener(
                    'pointermove',
                    function (event) {

                        const rect =
                            heroVisual.getBoundingClientRect();


                        const x =
                            event.clientX -
                            rect.left -
                            rect.width /
                            2;


                        const y =
                            event.clientY -
                            rect.top -
                            rect.height /
                            2;


                        const normalizedX =
                            Math.max(
                                -1,
                                Math.min(
                                    1,
                                    x /
                                    (
                                        rect.width /
                                        2
                                    )
                                )
                            );


                        const normalizedY =
                            Math.max(
                                -1,
                                Math.min(
                                    1,
                                    y /
                                    (
                                        rect.height /
                                        2
                                    )
                                )
                            );


                        targetMouseX =
                            normalizedX *
                            7;


                        targetMouseY =
                            normalizedY *
                            -5;

                    }
                );


                heroVisual.addEventListener(
                    'pointerleave',
                    function () {

                        targetMouseX = 0;

                        targetMouseY = 0;

                    }
                );

            }


            const startTime =
                performance.now();


            function animateVisual(
                currentTime
            ) {

                const elapsed =
                    (
                        currentTime -
                        startTime
                    ) /
                    1000;


                mouseX +=
                    (
                        targetMouseX -
                        mouseX
                    )
                    *
                    0.055;


                mouseY +=
                    (
                        targetMouseY -
                        mouseY
                    )
                    *
                    0.055;


                const waveA =
                    Math.sin(
                        elapsed *
                        1.05
                    );


                const waveB =
                    Math.sin(
                        elapsed *
                        0.67
                        +
                        1.25
                    );


                const waveC =
                    Math.cos(
                        elapsed *
                        0.88
                        -
                        0.55
                    );


                const baseX =
                    (
                        waveA *
                        7
                    )
                    +
                    (
                        waveB *
                        2.5
                    );


                const baseY =
                    waveC *
                    2.8;


                const rotateY =
                    (
                        waveA *
                        3.8
                    )
                    +
                    (
                        waveB *
                        1.2
                    )
                    +
                    (
                        mouseX *
                        0.32
                    );


                const rotateX =
                    (
                        waveC *
                        1.4
                    )
                    +
                    (
                        mouseY *
                        0.24
                    );


                const skewX =
                    (
                        waveA *
                        1.9
                    )
                    +
                    (
                        waveB *
                        0.9
                    )
                    +
                    (
                        mouseX *
                        0.16
                    );


                const scaleX =
                    1 +
                    (
                        waveB *
                        0.010
                    );


                const scaleY =
                    1 -
                    (
                        waveC *
                        0.004
                    );


                visualCard.style.transform =
                    `
                    perspective(1200px)
                    translate3d(
                        ${baseX + mouseX}px,
                        ${baseY + mouseY}px,
                        0
                    )
                    rotateY(
                        ${rotateY}deg
                    )
                    rotateX(
                        ${rotateX}deg
                    )
                    skewX(
                        ${skewX}deg
                    )
                    scaleX(
                        ${scaleX}
                    )
                    scaleY(
                        ${scaleY}
                    )
                    `;


                requestAnimationFrame(
                    animateVisual
                );

            }


            requestAnimationFrame(
                animateVisual
            );

        }



        /* =====================================================
           START SHOPPING
           SMOOTH SCROLL
        ====================================================== */

        const startShoppingButton =
            document.getElementById(
                'startShoppingButton'
            );


        const guestShopSection =
            document.getElementById(
                'guestShop'
            );


        if (
            startShoppingButton &&
            guestShopSection
        ) {

            startShoppingButton.addEventListener(
                'click',
                function (event) {

                    event.preventDefault();


                    guestShopSection.scrollIntoView(
                        {
                            behavior:
                                'smooth',

                            block:
                                'start'
                        }
                    );

                }
            );

        }



        /* =====================================================
           GUEST SEARCH
        ====================================================== */

        const searchInput =
            document.getElementById(
                'guestSearch'
            );


        const productCards =
            document.querySelectorAll(
                '.guest-product-card'
            );


        const noResults =
            document.getElementById(
                'guestNoResults'
            );


        if (
            searchInput &&
            productCards.length
        ) {

            searchInput.addEventListener(
                'input',
                function () {

                    const searchValue =
                        this.value
                            .trim()
                            .toLowerCase();


                    let visibleProducts =
                        0;


                    productCards.forEach(
                        function (card) {

                            const name =
                                (
                                    card.dataset.name ||
                                    ''
                                ).toLowerCase();


                            const category =
                                (
                                    card.dataset.category ||
                                    ''
                                ).toLowerCase();


                            const matches =
                                name.includes(
                                    searchValue
                                )
                                ||
                                category.includes(
                                    searchValue
                                );


                            if (matches) {

                                card.style.display =
                                    '';

                                visibleProducts++;

                            } else {

                                card.style.display =
                                    'none';

                            }

                        }
                    );


                    if (noResults) {

                        noResults.style.display =
                            visibleProducts === 0
                                ? 'block'
                                : 'none';

                    }

                }
            );

        }



        /* =====================================================
           WHY SHOPEASE — SCROLL REVEAL
        ====================================================== */

        const easeRevealTargets =
            document.querySelectorAll(
                '#whyShopease .ease-heading, #whyShopease .ease-card, #whyShopease .ease-tracking'
            );


        if (
            easeRevealTargets.length &&
            'IntersectionObserver' in window
        ) {

            const easeObserver =
                new IntersectionObserver(
                    function (entries) {

                        entries.forEach(
                            function (entry, index) {

                                if (
                                    entry.isIntersecting
                                ) {

                                    const delay =
                                        entry.target.classList.contains(
                                            'ease-card'
                                        )
                                            ? Array.from(
                                                  document.querySelectorAll(
                                                      '#whyShopease .ease-card'
                                                  )
                                              ).indexOf(entry.target) * 90
                                            : 0;


                                    setTimeout(
                                        function () {

                                            entry.target.classList.add(
                                                'in-view'
                                            );

                                        },
                                        delay
                                    );


                                    easeObserver.unobserve(
                                        entry.target
                                    );

                                }

                            }
                        );

                    },
                    {
                        threshold: 0.18,
                        rootMargin: '0px 0px -60px 0px'
                    }
                );


            easeRevealTargets.forEach(
                function (target) {

                    easeObserver.observe(
                        target
                    );

                }
            );

        } else {

            easeRevealTargets.forEach(
                function (target) {

                    target.classList.add(
                        'in-view'
                    );

                }
            );

        }



        /* =====================================================
           GENERAL SECTION SCROLL REVEAL
           Guest Shop → Product Cards → Why ShopEase → Footer
        ====================================================== */

        const scrollRevealTargets =
            document.querySelectorAll(
                '#guestShop .guest-shop-heading, ' +
                '#guestShop .guest-search-wrapper, ' +
                '#guestShop .guest-product-card, ' +
                '#whyShopease .ease-heading, ' +
                '#whyShopease .ease-card'
            );

        function prepareScrollReveal() {
            scrollRevealTargets.forEach(function (element) {
                element.classList.add('scroll-reveal');

                if (
                    element.classList.contains('guest-product-card') ||
                    element.classList.contains('ease-card')
                ) {
                    const parent = element.parentElement;
                    const siblingCards = parent
                        ? Array.from(parent.children).filter(function (child) {
                            return (
                                child.classList.contains('guest-product-card') ||
                                child.classList.contains('ease-card')
                            );
                        })
                        : [];

                    const cardIndex = siblingCards.indexOf(element);

                    if (cardIndex >= 0) {
                        element.style.transitionDelay = (cardIndex * 90) + 'ms';
                    }
                }
            });
        }

        prepareScrollReveal();

        if (
            scrollRevealTargets.length &&
            'IntersectionObserver' in window
        ) {
            const scrollRevealObserver =
                new IntersectionObserver(
                    function (entries) {
                        entries.forEach(function (entry) {
                            if (entry.isIntersecting) {
                                entry.target.classList.add('is-visible');
                            } else {
                                entry.target.classList.remove('is-visible');
                            }
                        });
                    },
                    {
                        threshold: 0.12,
                        rootMargin: '0px 0px -55px 0px'
                    }
                );

            scrollRevealTargets.forEach(function (target) {
                scrollRevealObserver.observe(target);
            });
        } else {
            scrollRevealTargets.forEach(function (target) {
                target.classList.add('is-visible');
            });
        }


        /* =====================================================
           LIVE PARCEL TRACKING ANIMATION
           The progress line is measured against the actual dot
           positions, so it ends exactly at the Delivered dot.
           The Delivered dot lights up only when the line reaches it.
        ====================================================== */

        const parcelRoute =
            document.querySelector(
                '.ease-parcel-route'
            );

        const parcelLine =
            parcelRoute
                ? parcelRoute.querySelector(
                    '.ease-parcel-line'
                )
                : null;

        const parcelProgressLine =
            parcelRoute
                ? parcelRoute.querySelector(
                    '.ease-parcel-progress-line'
                )
                : null;

        const parcelSteps =
            parcelRoute
                ? Array.from(
                    parcelRoute.querySelectorAll(
                        '.ease-parcel-step'
                    )
                )
                : [];

        const deliveredStep =
            parcelSteps.length
                ? parcelSteps[parcelSteps.length - 1]
                : null;


        let trackerStart = 0;
        let trackerEnd = 0;
        let trackerDistance = 0;
        let trackerResizeObserver = null;
        let trackerAnimationFrame = null;
        let trackerStartedAt = performance.now();
        const trackerDuration = 6000;


        function updateParcelLineGeometry() {

            if (
                !parcelRoute ||
                !parcelLine ||
                !parcelProgressLine ||
                !parcelSteps.length
            ) {
                return;
            }

            const firstDot =
                parcelSteps[0]
                    .querySelector(
                        '.ease-parcel-dot'
                    );

            const lastDot =
                deliveredStep
                    ? deliveredStep.querySelector(
                        '.ease-parcel-dot'
                    )
                    : null;

            if (!firstDot || !lastDot) {
                return;
            }

            const routeRect =
                parcelRoute.getBoundingClientRect();

            const firstRect =
                firstDot.getBoundingClientRect();

            const lastRect =
                lastDot.getBoundingClientRect();

            trackerStart =
                (
                    firstRect.top +
                    firstRect.height / 2
                ) -
                routeRect.top;

            trackerEnd =
                (
                    lastRect.top +
                    lastRect.height / 2
                ) -
                routeRect.top;

            trackerDistance =
                Math.max(
                    0,
                    trackerEnd - trackerStart
                );

            parcelLine.style.top =
                trackerStart + 'px';

            parcelLine.style.height =
                trackerDistance + 'px';

            parcelProgressLine.style.top =
                trackerStart + 'px';

            parcelProgressLine.style.height =
                '0px';

        }


        function easeInOutCubic(t) {

            return t < 0.5
                ? 4 * t * t * t
                : 1 -
                    Math.pow(
                        -2 * t + 2,
                        3
                    ) /
                    2;

        }


        function animateParcelTracking(
            currentTime
        ) {

            if (
                !parcelProgressLine ||
                !deliveredStep ||
                trackerDistance <= 0
            ) {
                return;
            }

            const elapsed =
                currentTime -
                trackerStartedAt;

            const loopedTime =
                elapsed %
                trackerDuration;

            const rawProgress =
                loopedTime /
                trackerDuration;

            const progress =
                easeInOutCubic(
                    rawProgress
                );

            const currentHeight =
                trackerDistance *
                progress;

            parcelProgressLine.style.height =
                currentHeight + 'px';

            /*
             * Delivered activates at the exact moment
             * the animated line reaches its endpoint.
             */
            const deliveredReached =
                progress >= 0.995;

            const alreadyReached =
                deliveredStep.classList.contains(
                    'delivered-reached'
                );

            if (
                deliveredReached &&
                !alreadyReached
            ) {

                deliveredStep.classList.add(
                    'delivered-reached'
                );

            } else if (
                !deliveredReached &&
                alreadyReached
            ) {

                deliveredStep.classList.remove(
                    'delivered-reached'
                );

            }

            trackerAnimationFrame =
                requestAnimationFrame(
                    animateParcelTracking
                );

        }


        if (
            parcelRoute &&
            parcelProgressLine &&
            deliveredStep
        ) {

            updateParcelLineGeometry();

            trackerStartedAt =
                performance.now();

            trackerAnimationFrame =
                requestAnimationFrame(
                    animateParcelTracking
                );

            window.addEventListener(
                'resize',
                function () {

                    updateParcelLineGeometry();

                },
                { passive: true }
            );

            if (
                'ResizeObserver' in window
            ) {

                trackerResizeObserver =
                    new ResizeObserver(
                        updateParcelLineGeometry
                    );

                trackerResizeObserver.observe(
                    parcelRoute
                );

            }

            window.addEventListener(
                'load',
                updateParcelLineGeometry,
                { once: true }
            );

        }


        /* =====================================================
           AUTH MODAL
        ====================================================== */

        const loginModal =
            document.getElementById(
                'loginModal'
            );


        const loginModalIframe =
            document.getElementById(
                'loginModalIframe'
            );


        const openLoginModal =
            document.getElementById(
                'openLoginModal'
            );


        const openSignupModal =
            document.getElementById(
                'openSignupModal'
            );


        const closeLoginModal =
            document.getElementById(
                'closeLoginModal'
            );


        const loginModalOverlay =
            document.getElementById(
                'loginModalOverlay'
            );



        /* =====================================================
           AUTH URLS
        ====================================================== */

        const loginUrl = document.body.dataset.loginUrl;


        const registerUrl = document.body.dataset.registerUrl;



        /* =====================================================
           CHECK AUTH PAGE
        ====================================================== */

        function isAllowedAuthPage(
            url
        ) {

            try {

                const current =
                    new URL(
                        url,
                        window.location.origin
                    );


                const login =
                    new URL(
                        loginUrl,
                        window.location.origin
                    );


                const register =
                    new URL(
                        registerUrl,
                        window.location.origin
                    );


                const currentPath =
                    current.pathname;


                const loginPath =
                    login.pathname;


                const registerPath =
                    register.pathname;


                return (
                    currentPath === loginPath
                    ||
                    currentPath === registerPath
                );

            } catch (
                error
            ) {

                return false;

            }

        }



        /*
         * Prevent duplicate full-screen handoffs.
         */
        let authTransitionStarted = false;


        /*
         * Keep the iframe from reacting to its initial
         * about:blank load or an intentional modal close.
         */
        let authModalOpening = false;



        /* =====================================================
           OPEN AUTH MODAL
        ====================================================== */

        function openAuthModal(
            authType
        ) {

            if (
                !loginModal ||
                !loginModalIframe
            ) {

                return;

            }


            const targetUrl =
                authType === 'signup'
                    ? registerUrl
                    : loginUrl;


            authTransitionStarted =
                false;


            authModalOpening =
                true;


            loginModal.classList.remove(
                'is-leaving'
            );


            loginModalIframe.src =
                targetUrl;


            loginModal.classList.add(
                'show'
            );


            loginModal.setAttribute(
                'aria-hidden',
                'false'
            );


            loginModal.setAttribute(
                'aria-busy',
                'true'
            );


            document.body.style.overflow =
                'hidden';


            /*
             * Give the browser one paint cycle so the
             * hidden → visible transition is rendered cleanly.
             */
            requestAnimationFrame(
                function () {

                    requestAnimationFrame(
                        function () {

                            authModalOpening =
                                false;

                        }
                    );

                }
            );

        }



        /* =====================================================
           CLOSE AUTH MODAL
        ====================================================== */

        function closeAuthModal() {

            if (
                !loginModal
            ) {

                return;

            }


            authTransitionStarted =
                false;


            authModalOpening =
                false;


            loginModal.classList.remove(
                'is-leaving'
            );


            loginModal.classList.remove(
                'show'
            );


            loginModal.setAttribute(
                'aria-hidden',
                'true'
            );


            loginModal.setAttribute(
                'aria-busy',
                'false'
            );


            document.body.style.overflow =
                '';


            /*
             * Reset the iframe only after the close
             * animation has completed.
             */
            if (
                loginModalIframe
            ) {

                setTimeout(
                    function () {

                        if (
                            !loginModal.classList.contains(
                                'show'
                            )
                        ) {

                            loginModalIframe.src =
                                'about:blank';

                        }

                    },
                    380
                );

            }

        }



        /* =====================================================
           SMOOTH FULL-SCREEN HANDOFF
        ====================================================== */

        function transitionToAuthDestination(
            destinationUrl
        ) {

            if (
                !loginModal ||
                !destinationUrl ||
                authTransitionStarted
            ) {

                return;

            }


            /*
             * Never redirect to blank pages.
             */
            if (
                destinationUrl ===
                'about:blank'
            ) {

                return;

            }


            authTransitionStarted =
                true;


            authModalOpening =
                false;


            loginModal.classList.add(
                'is-leaving'
            );


            loginModal.setAttribute(
                'aria-busy',
                'true'
            );


            /*
             * Keep the current page frozen while
             * the modal expands into the viewport.
             */
            document.body.style.overflow =
                'hidden';


            /*
             * Allow CSS transition to finish,
             * then let Laravel's destination page
             * take over the parent window.
             */
            setTimeout(
                function () {

                    window.location.assign(
                        destinationUrl
                    );

                },
                430
            );

        }



        /* =====================================================
           LOGIN BUTTON
        ====================================================== */

        if (
            openLoginModal
        ) {

            openLoginModal.addEventListener(
                'click',
                function () {

                    openAuthModal(
                        'login'
                    );

                }
            );

        }



        /* =====================================================
           SIGNUP BUTTON
        ====================================================== */

        if (
            openSignupModal
        ) {

            openSignupModal.addEventListener(
                'click',
                function () {

                    openAuthModal(
                        'signup'
                    );

                }
            );

        }



        /* =====================================================
           CLOSE BUTTON
        ====================================================== */

        if (
            closeLoginModal
        ) {

            closeLoginModal.addEventListener(
                'click',
                function () {

                    if (
                        authTransitionStarted
                    ) {

                        return;

                    }

                    closeAuthModal();

                }
            );

        }



        /* =====================================================
           CLICK OUTSIDE
        ====================================================== */

        if (
            loginModalOverlay
        ) {

            loginModalOverlay.addEventListener(
                'click',
                function () {

                    if (
                        authTransitionStarted
                    ) {

                        return;

                    }

                    closeAuthModal();

                }
            );

        }



        /* =====================================================
           IMPORTANT:
           DETECT WHEN IFRAME LEAVES AUTH PAGE
        ====================================================== */

        if (
            loginModalIframe
        ) {

            loginModalIframe.addEventListener(
                'load',
                function () {

                    /*
                     * Ignore iframe loads when the modal
                     * is not currently visible.
                     */
                    if (
                        !loginModal.classList.contains(
                            'show'
                        )
                    ) {

                        return;

                    }


                    /*
                     * Ignore the first paint while the
                     * modal is still opening.
                     */
                    if (
                        authModalOpening
                    ) {

                        return;

                    }


                    try {

                        const iframeUrl =
                            loginModalIframe
                                .contentWindow
                                .location
                                .href;


                        /*
                         * Ignore blank iframe states.
                         */
                        if (
                            !iframeUrl ||
                            iframeUrl ===
                                'about:blank'
                        ) {

                            return;

                        }


                        /*
                         * Login and signup pages are
                         * intentionally kept inside the modal.
                         */
                        if (
                            isAllowedAuthPage(
                                iframeUrl
                            )
                        ) {

                            loginModal.setAttribute(
                                'aria-busy',
                                'false'
                            );

                            return;

                        }


                        /*
                         * Once Laravel moves outside the
                         * authentication pages (for example:
                         *
                         * /buyer/register
                         * /seller/register
                         * /dashboard
                         * /buyer/dashboard
                         * /seller/dashboard
                         * etc.
                         *
                         * smoothly expand the modal to full
                         * screen before handing control to
                         * the parent page.
                         */
                        transitionToAuthDestination(
                            iframeUrl
                        );

                    } catch (
                        error
                    ) {

                        /*
                         * Ignore iframe access errors.
                         * Same-origin Laravel pages should normally
                         * be accessible here.
                         */

                    }

                }
            );

        }



        /* =====================================================
           ESC KEY
        ====================================================== */

        document.addEventListener(
            'keydown',
            function (event) {

                if (
                    event.key ===
                    'Escape'
                ) {

                    if (
                        authTransitionStarted
                    ) {

                        return;

                    }

                    closeAuthModal();

                }

            }
        );

    }
);

(function(){
 const modal=document.getElementById('mobileInfoModal');
 if(!modal)return;
 const title=document.getElementById('mobileInfoTitle');
 const body=document.getElementById('mobileInfoBody');
 const close=document.getElementById('mobileInfoClose');
 const okay=document.getElementById('mobileInfoOkay');
 let previousFocus=null;
 function hide(){modal.hidden=true;document.body.style.overflow='';if(previousFocus)previousFocus.focus();}
 document.querySelectorAll('[data-app-info]').forEach(button=>button.addEventListener('click',()=>{
   const type=button.dataset.appInfo;
   title.textContent=type==='rider'?'Rider Applications on Mobile':type==='seller'?'ShopEase Seller Mobile':'ShopEase App Download';
   body.textContent=type==='rider'?'Rider applications will be accessible through the ShopEase mobile app. The app download will be linked here once released.':type==='seller'?'The seller mobile experience will be connected once the official ShopEase mobile app is released.':'The ShopEase app download is not available yet. This button is ready for the official app link when the mobile release is published.';
   previousFocus=button;modal.hidden=false;document.body.style.overflow='hidden';close.focus();
 }));
 close.addEventListener('click',hide);okay.addEventListener('click',hide);
 modal.addEventListener('click',e=>{if(e.target===modal)hide()});
 document.addEventListener('keydown',e=>{if(e.key==='Escape'&&!modal.hidden)hide();});
})();

(function () {
  'use strict';
  const ids = ['guestShop', 'whyShopease', 'logisticsPartners', 'mobileApp'];
  const links = [...document.querySelectorAll('[data-landing-jump]')];
  const targets = {
    guestShop: document.querySelector('#guestShop .guest-shop-heading'),
    whyShopease: document.querySelector('#whyShopease .ease-heading'),
    logisticsPartners: document.querySelector('#logisticsPartners .partner-copy'),
    mobileApp: document.querySelector('#mobileApp .mobile-copy')
  };
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  const offset = () => window.innerWidth <= 860 ? 154 : 112;
  let lockSelectionUntil = 0;

  function markCurrent(id) {
    links.forEach(link => {
      const selected = link.dataset.landingJump === id;
      link.classList.toggle('is-current', selected);
      if (selected) link.setAttribute('aria-current','location');
      else link.removeAttribute('aria-current');
    });
  }

  function targetY(id) {
    const element = targets[id] || document.getElementById(id);
    if (!element) return null;
    return Math.max(0, window.scrollY + element.getBoundingClientRect().top - offset());
  }

  function jumpTo(id, updateHistory, animate) {
    const y = targetY(id);
    if (y === null) return;
    lockSelectionUntil = Date.now() + (animate ? 1200 : 120);
    markCurrent(id);
    window.scrollTo({top:y,behavior:animate && !prefersReducedMotion.matches ? 'smooth' : 'instant'});
    if (updateHistory && window.history.replaceState) {
      window.history.replaceState(null,'','#' + id);
    }
  }

  links.forEach(link => link.addEventListener('click', e => {
    const id = link.dataset.landingJump;
    if (!targets[id]) return;
    e.preventDefault();
    jumpTo(id, true, true);
  }));

  function updateActiveByScroll() {
    if (Date.now() < lockSelectionUntil) return;
    const referenceLine = offset() + 35;
    let current = null;
    ids.forEach(id => {
      const target = targets[id];
      if (target && target.getBoundingClientRect().top <= referenceLine) current = id;
    });
    markCurrent(current);
  }
  let frameQueued = false;
  window.addEventListener('scroll', () => {
    if (frameQueued) return;
    frameQueued = true;
    window.requestAnimationFrame(() => {frameQueued = false;updateActiveByScroll();});
  }, {passive:true});
  window.addEventListener('resize', updateActiveByScroll, {passive:true});
  window.addEventListener('load', () => {
    const hash = decodeURIComponent(location.hash.slice(1));
    if (ids.includes(hash)) jumpTo(hash, false, false);
    else updateActiveByScroll();
  }, {once:true});

  // Reversible reveal: matches the earlier info cards and works in BOTH directions.
  // Deliberately do not unobserve: users should see a fade-out as content leaves
  // either edge of the viewport, then a fade-in when they scroll back.
  const revealElements = [...document.querySelectorAll('#logisticsPartners .ux-reveal, #mobileApp .ux-reveal')];
  if ('IntersectionObserver' in window && !prefersReducedMotion.matches) {
    document.documentElement.classList.add('landing-ux-js');
    const observer = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        entry.target.classList.toggle('in-view', entry.isIntersecting);
      });
    }, {threshold: 0.08, rootMargin: '-7% 0px -9% 0px'});
    revealElements.forEach((el, index) => {
      const localIndex = [...el.parentElement.children].indexOf(el);
      el.style.setProperty('--reveal-delay', `${Math.min(Math.max(localIndex,0),3)*75}ms`);
      observer.observe(el);
    });
  } else {
    revealElements.forEach(el => el.classList.add('in-view'));
  }

  // Purely visual ShopEase app preview, not a real shopping checkout.
  // Pause the sequence when its phone is offscreen to avoid needless work.
  const appPreview = document.querySelector('.se-app-demo');
  if (appPreview) {
    const captions = ['01 / 04 · Discover products','02 / 04 · Review your cart','03 / 04 · Secure checkout','04 / 04 · Track your order'];
    const caption = appPreview.querySelector('[data-demo-caption]');
    let step = 0, timer = null;
    const advance = () => {
      step = (step + 1) % 4;
      appPreview.dataset.demoStage = String(step);
      if (caption) caption.textContent = captions[step];
    };
    const start = () => {
      if (timer || prefersReducedMotion.matches) return;
      timer = window.setInterval(advance, 2750);
    };
    const stop = () => { if (timer) window.clearInterval(timer); timer = null; };
    if ('IntersectionObserver' in window) {
      const demoObserver = new IntersectionObserver(entries => {
        entries.forEach(entry => entry.isIntersecting ? start() : stop());
      }, {threshold: 0.12});
      demoObserver.observe(appPreview);
    } else start();
    document.addEventListener('visibilitychange', () => {
      if (document.hidden) stop();
      else if (appPreview.getBoundingClientRect().top < innerHeight && appPreview.getBoundingClientRect().bottom > 0) start();
    });
  }


})();


/* ShopEase: Main Landing <-> Logistics Landing transition. */
(() => {
  const TRANSITION_KEY = 'shopease:landing-switch';
  const DURATION = 250;
  const currentPath = window.location.pathname.replace(/\/+$/, '') || '/';
  const currentIsMain = currentPath === '/';
  const currentIsLogistics = currentPath === '/logistics/landing';
  if (!currentIsMain && !currentIsLogistics) return;
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

  // Exit is handled on the source page; entry animates only after a matching switch.
  let entering = false;
  try {
    const value = window.sessionStorage.getItem(TRANSITION_KEY);
    if (value) {
      const state = JSON.parse(value);
      window.sessionStorage.removeItem(TRANSITION_KEY);
      if (state && state.to === currentPath && Date.now() - state.when < 10000) entering = true;
    }
  } catch (_) { /* Storage may be disabled; navigation still works. */ }

  if (entering && !reducedMotion.matches) {
    document.body.classList.add('shopease-landing-enter');
    // Allow the browser to paint its initial transition state before animating.
    requestAnimationFrame(() => requestAnimationFrame(() => {
      document.body.classList.add('shopease-landing-entered');
    }));
    window.setTimeout(() => {
      document.body.classList.remove('shopease-landing-enter', 'shopease-landing-entered');
    }, 650);
  }

  let switching = false;
  document.addEventListener('click', (event) => {
    if (event.defaultPrevented || event.button !== 0 || event.ctrlKey || event.metaKey || event.altKey || event.shiftKey) return;
    const link = event.target instanceof Element ? event.target.closest('a[href]') : null;
    if (!link || link.hasAttribute('download') || (link.target && link.target !== '_self')) return;
    let url;
    try { url = new URL(link.href, location.href); } catch (_) { return; }
    if (url.origin !== location.origin) return;
    const destination = url.pathname.replace(/\/+$/, '') || '/';
    const isLandingSwitch = (currentIsMain && destination === '/logistics/landing') ||
      (currentIsLogistics && destination === '/');
    if (!isLandingSwitch) return;
    event.preventDefault();
    if (switching) return;
    switching = true;
    try { sessionStorage.setItem(TRANSITION_KEY, JSON.stringify({to: destination, when: Date.now()})); } catch (_) {}
    if (reducedMotion.matches) { location.assign(url.href); return; }
    document.body.classList.add('shopease-landing-leaving');
    window.setTimeout(() => location.assign(url.href), DURATION);
  });
  // Back-forward-cache pages should never remain faded out.
  window.addEventListener('pageshow', () => {
    document.body.classList.remove('shopease-landing-leaving');
    switching = false;
  });
})();