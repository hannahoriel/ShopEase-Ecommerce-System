document.addEventListener('DOMContentLoaded', () => {
  const reveals = document.querySelectorAll('.logistics-reveal');
  if ('IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    const observer = new IntersectionObserver(entries => entries.forEach(entry => {
      if(entry.isIntersecting){entry.target.classList.add('is-visible');observer.unobserve(entry.target);}
    }), {threshold:.09});
    reveals.forEach(element => observer.observe(element));
  } else reveals.forEach(element => element.classList.add('is-visible'));
  const visual = document.getElementById('visualCard');
  const area = document.querySelector('.hero-visual');
  const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (!visual || !area || reduced) return;
  const desktop = window.matchMedia('(min-width: 1051px)');
  let frame = null;
  area.addEventListener('pointermove', (event) => {
    if (!desktop.matches) return;
    const bounds = area.getBoundingClientRect();
    const x = (event.clientX - bounds.left) / bounds.width - .5;
    const y = (event.clientY - bounds.top) / bounds.height - .5;
    if (frame) cancelAnimationFrame(frame);
    frame = requestAnimationFrame(() => {visual.style.transform = `rotateY(${x*7}deg) rotateX(${-y*7}deg)`;});
  });
  area.addEventListener('pointerleave', () => {
    if (frame) cancelAnimationFrame(frame);
    visual.style.transform = '';
  });
});

// Logistics company login modal (the included Blade is server-rendered).
(() => {
  const modal = document.getElementById('logisticsLoginModal');
  const openButton = document.getElementById('logisticsLogin');
  const closeButton = document.getElementById('logisticsLoginClose');
  const dialog = modal?.querySelector('.logistics-login-dialog');
  if (!modal || !openButton || !closeButton || !dialog) return;
  const signupButton = document.getElementById('logisticsSignup');
  const transitionLayer = document.getElementById('logisticsSignupTransition');
  const registrationUrl = '/logistics/register/company';
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  let signupTransitionStarted = false;
  const registrationPreview = document.getElementById('logisticsSignupPreview');
  function goToRegistration(fromOpenLogin) {
    if (signupTransitionStarted) return;
    signupTransitionStarted = true;
    document.body.classList.add('logistics-modal-open');
    // Keep the existing login modal in its exact centered position. The
    // replacement is positioned above it, not inside its scrolling dialog.
    if (transitionLayer && registrationPreview) {
      transitionLayer.setAttribute('aria-busy', 'true');
      registrationPreview.src = registrationUrl;
      let expanded = false;
      const expand = () => {
        if (expanded) return;
        expanded = true;
        transitionLayer.classList.add('is-active');
        transitionLayer.setAttribute('aria-hidden', 'false');
        requestAnimationFrame(() => requestAnimationFrame(() => {
          transitionLayer.classList.add('is-expanded');
          // Exact ShopEase 430ms handoff after expanding.
          setTimeout(() => window.location.assign(registrationUrl), prefersReducedMotion ? 30 : 430);
        }));
      };
      // Let actual registration page draw before expanding: no blank white card.
      registrationPreview.addEventListener('load', expand, {once:true});
      setTimeout(() => {
        if (!expanded) window.location.assign(registrationUrl);
      }, 5000); // navigate rather than expand a blank iframe if loading fails
    } else {
      window.location.assign(registrationUrl);
    }
  }
  signupButton?.addEventListener('click', () => goToRegistration(false));
  modal.addEventListener('click', (event) => {
    const link = event.target.closest('[data-logistics-signup]');
    if (!link || !modal.contains(link)) return;
    event.preventDefault();
    goToRegistration(true);
  });
  let previousFocus = null;
  function openLogin() {
    previousFocus = document.activeElement;
    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden','false');
    document.body.classList.add('logistics-modal-open');
    window.initLogisticsLogin?.(modal);
    const email = modal.querySelector('input[type="email"]');
    (email || closeButton).focus({preventScroll:true});
  }
  function closeLogin() {
    if (signupTransitionStarted) return;
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden','true');
    document.body.classList.remove('logistics-modal-open');
    (previousFocus?.isConnected ? previousFocus : openButton).focus({preventScroll:true});
  }
  openButton.addEventListener('click',openLogin);
  closeButton.addEventListener('click',closeLogin);
  modal.addEventListener('pointerdown',(event) => {if(event.target===modal) closeLogin();});
  document.addEventListener('keydown',(event) => {
    if(!modal.classList.contains('is-open')) return;
    if(event.key==='Escape') {event.preventDefault();closeLogin();return;}
    if(event.key!=='Tab') return;
    const focusable = Array.from(dialog.querySelectorAll('a[href],button:not([disabled]),input:not([disabled]),select:not([disabled]),textarea:not([disabled]),[tabindex]:not([tabindex="-1"])')).filter(el=>el.getClientRects().length && getComputedStyle(el).visibility!=='hidden');
    if(!focusable.length){event.preventDefault();closeButton.focus();return;}
    const first=focusable[0],last=focusable[focusable.length-1];
    if(event.shiftKey && document.activeElement===first){event.preventDefault();last.focus();}
    else if(!event.shiftKey && document.activeElement===last){event.preventDefault();first.focus();}
  });
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

// Successful-login expansion: use the SAME overlay/card/iframe CSS as signup.
// A rejected login never calls this function.
(() => {
  const layer = document.getElementById('logisticsDashboardTransition');
  const preview = document.getElementById('logisticsDashboardPreview');
  if (!layer || !preview) return;
  let started = false;
  window.startLogisticsDashboardTransition = (url) => {
    if (started) return;
    const destination = new URL(url, location.href);
    if (destination.origin !== location.origin || !/^\/logistics\/dashboard\/?$/.test(destination.pathname)) return;
    started = true;
    const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;
    let expanded = false;
    const expand = () => {
      if (expanded) return;
      expanded = true;
      layer.classList.add('is-active');
      layer.setAttribute('aria-hidden', 'false');
      document.body.classList.add('logistics-modal-open');
      requestAnimationFrame(() => requestAnimationFrame(() => {
        layer.classList.add('is-expanded');
        setTimeout(() => location.assign(destination.href), reduced ? 30 : 430);
      }));
    };
    preview.addEventListener('load', expand, {once:true});
    preview.src = destination.href;
    // If preview is blocked, still navigate; authentication has already succeeded.
    setTimeout(() => { if (!expanded) location.assign(destination.href); }, 3000);
  };
})();