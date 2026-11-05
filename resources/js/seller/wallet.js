/* ShopEase seller wallet — fetch filtered table data without full-page navigation. */
document.addEventListener('DOMContentLoaded', () => {
  const panel = document.getElementById('sellerWalletHistory');
  const form = document.getElementById('walletFilterForm');
  if (!panel || !form) return;

  const searchInput = form.querySelector('[name="search"]');
  const fromInput = form.querySelector('[name="from"]');
  const toInput = form.querySelector('[name="to"]');
  const status = document.getElementById('walletLiveStatus');
  let selectedType = new URL(window.location.href).searchParams.get('type') || 'all';
  let controller = null;
  let debounceTimer = null;
  let lastRequest = 0;

  function makeUrl(page = null) {
    const url = new URL(form.action || window.location.href, window.location.href);
    url.search = '';
    const term = searchInput.value.trim();
    if (term) url.searchParams.set('search', term);
    if (fromInput.value) url.searchParams.set('from', fromInput.value);
    if (toInput.value) url.searchParams.set('to', toInput.value);
    if (selectedType !== 'all') url.searchParams.set('type', selectedType);
    if (page && page !== '1') url.searchParams.set('page', page);
    return url;
  }

  function setTabs() {
    panel.querySelectorAll('[data-wallet-type]').forEach(link => {
      const active = link.dataset.walletType === selectedType;
      link.classList.toggle('active', active);
      if (active) link.setAttribute('aria-current', 'true');
      else link.removeAttribute('aria-current');
    });
  }

  async function refresh(url, push = false) {
    const requestNumber = ++lastRequest;
    if (controller) controller.abort();
    controller = new AbortController();
    panel.classList.add('sw-is-loading');
    status.textContent = 'Updating transactions…';
    try {
      const response = await fetch(url.toString(), {
        method: 'GET',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
        credentials: 'same-origin',
        signal: controller.signal
      });
      if (!response.ok) throw new Error('Could not load transactions.');
      const html = await response.text();
      if (requestNumber !== lastRequest) return;
      const doc = new DOMParser().parseFromString(html, 'text/html');
      const freshPanel = doc.getElementById('sellerWalletHistory');
      if (!freshPanel) throw new Error('Transaction results were not returned.');
      for (const id of ['walletResultCount', 'walletResultsTable', 'walletPagination']) {
        const oldEl = document.getElementById(id);
        const freshEl = freshPanel.querySelector('#' + id);
        if (!oldEl || !freshEl) throw new Error('Missing wallet result element: ' + id);
        oldEl.replaceWith(freshEl);
      }
      window.history[push ? 'pushState' : 'replaceState']({ walletFilter: true }, '', url.pathname + url.search);
      status.textContent = '';
    } catch (error) {
      if (error.name !== 'AbortError') {
        status.textContent = 'Unable to update transactions. Please retry.';
        console.error('Wallet filters:', error);
      }
    } finally {
      if (requestNumber === lastRequest) panel.classList.remove('sw-is-loading');
    }
  }

  function scheduleRefresh() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => refresh(makeUrl()), 320);
  }

  searchInput?.addEventListener('input', scheduleRefresh);
  [fromInput, toInput].forEach(input => input?.addEventListener('change', () => {
    clearTimeout(debounceTimer);
    refresh(makeUrl());
  }));

  form.addEventListener('submit', e => {
    e.preventDefault();
    clearTimeout(debounceTimer);
    refresh(makeUrl());
  });

  form.querySelector('.sw-clear-btn')?.addEventListener('click', e => {
    e.preventDefault();
    clearTimeout(debounceTimer);
    searchInput.value = '';
    fromInput.value = '';
    toInput.value = '';
    selectedType = 'all';
    setTabs();
    refresh(makeUrl());
  });

  panel.addEventListener('click', e => {
    const typeLink = e.target.closest('a[data-wallet-type]');
    if (typeLink) {
      e.preventDefault();
      selectedType = typeLink.dataset.walletType || 'all';
      clearTimeout(debounceTimer);
      setTabs();
      refresh(makeUrl());
      return;
    }
    const paginationLink = e.target.closest('#walletPagination a[href]');
    if (paginationLink) {
      e.preventDefault();
      clearTimeout(debounceTimer);
      const url = new URL(paginationLink.href, window.location.href);
      // Keep the currently selected filters, change only the requested page.
      refresh(makeUrl(url.searchParams.get('page') || '1'));
    }
  });

  window.addEventListener('popstate', () => {
    const url = new URL(window.location.href);
    searchInput.value = url.searchParams.get('search') || '';
    fromInput.value = url.searchParams.get('from') || '';
    toInput.value = url.searchParams.get('to') || '';
    selectedType = url.searchParams.get('type') || 'all';
    setTabs();
    clearTimeout(debounceTimer);
    refresh(url);
  });
});
