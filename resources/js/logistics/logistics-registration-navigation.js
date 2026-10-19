/**
 * ShopEase Logistics registration — client-side navigation for GET steps only.
 * This script intentionally does NOT submit or save forms. Only each page's
 * existing Continue button uses its Laravel POST route to save data.
 * Place in resources/js/logistics/logistics-registration-navigation.js; load via Laravel @vite.
 */
(() => {
  'use strict';
  if (window.__shopEaseLogisticsNavigationInstalled) return;
  window.__shopEaseLogisticsNavigationInstalled = true;

  const stepPaths = new Set([
    '/logistics/register/company',
    '/logistics/register/permits',
    '/logistics/register/review'
  ]);
  let navigating = false;

  function getStepUrl(href) {
    try {
      // Support older cached Blade that accidentally produced a literal route(...)
      const malformed = String(href).match(/logistics\.register\.(company|permits|review)/);
      if (malformed) return new URL('/logistics/register/' + malformed[1], location.origin);
      const url = new URL(href, location.href);
      if (url.origin !== location.origin || !stepPaths.has(url.pathname.replace(/\/$/, ''))) return null;
      return url;
    } catch (_) { return null; }
  }

  // Recover if the browser is already sitting at a malformed literal Blade URL.
  if (/%7B%7B|%7B|route\(/i.test(location.pathname)) {
    const recovered = getStepUrl(location.pathname);
    if (recovered) {
      location.replace(recovered.href);
      return;
    }
  }

  async function changeStep(url, updateHistory = true) {
    if (navigating) return;
    if (url.href === location.href && updateHistory) return;
    navigating = true;
    document.body.classList.add('logistics-route-loading');
    const controller = new AbortController();
    const timeout = setTimeout(() => controller.abort(), 15000);
    try {
      const response = await fetch(url.href, {
        method: 'GET',
        credentials: 'same-origin',
        headers: { 'Accept': 'text/html', 'X-Requested-With': 'XMLHttpRequest' },
        signal: controller.signal
      });
      if (!response.ok) throw new Error('Request failed: ' + response.status);
      const html = await response.text();
      const next = new DOMParser().parseFromString(html, 'text/html');
      if (!next.querySelector('.stepper-wrapper') || !next.querySelector('.registration-content')) {
        throw new Error('Response is not a logistics registration page');
      }

      // Use the CSS from the destination page, preserving the existing Vite and fonts.
      const newStyles = Array.from(next.head.querySelectorAll('style'));
      if (!newStyles.length) throw new Error('Registration styles missing');
      document.head.querySelectorAll('style').forEach(style => style.remove());
      newStyles.forEach(style => document.head.appendChild(style.cloneNode(true)));
      document.title = next.title || document.title;

      // Replace the entire visible page, not the browser document or its JS runtime.
      // File inputs and other unsaved edits are intentionally discarded here.
      document.body.innerHTML = next.body.innerHTML;
      if (updateHistory) history.pushState({ logisticsStep: true }, '', url.href);

      // Parsed script elements are inert after innerHTML; re-create them so every
      // step's original dropdowns, previews, validations and OTP UI initialize.
      for (const oldScript of Array.from(document.body.querySelectorAll('script'))) {
        if (oldScript.src) continue;
        const script = document.createElement('script');
        script.textContent = oldScript.textContent;
        oldScript.replaceWith(script);
      }
      window.scrollTo({ top: 0, behavior: 'instant' });
    } catch (error) {
      console.warn('Smooth registration navigation unavailable; opening the page normally.', error);
      // Fallback ensures navigation keeps working if a fetch or parsing error occurs.
      location.assign(url.href);
    } finally {
      clearTimeout(timeout);
      document.body.classList.remove('logistics-route-loading');
      navigating = false;
    }
  }

  document.addEventListener('click', event => {
    if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
    const link = event.target.closest('a[href]');
    if (!link || link.hasAttribute('download') || (link.target && link.target !== '_self')) return;
    const url = getStepUrl(link.href);
    if (!url) return;
    event.preventDefault();
    changeStep(url);
  });

  window.addEventListener('popstate', () => {
    const url = getStepUrl(location.href);
    if (url) changeStep(url, false);
  });
})();