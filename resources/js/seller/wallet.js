/* Seller wallet filters use the authenticated JSON API and preserve the page's query URL. */
document.addEventListener('DOMContentLoaded', () => {
  const panel = document.getElementById('sellerWalletHistory');
  const form = document.getElementById('walletFilterForm');
  if (!panel || !form) return;

  const searchInput = form.querySelector('[name="search"]');
  const fromInput = form.querySelector('[name="from"]');
  const toInput = form.querySelector('[name="to"]');
  const status = document.getElementById('walletLiveStatus');
  const apiUrl = form.dataset.walletApiUrl;
  let selectedType = new URL(window.location.href).searchParams.get('type') || 'all';
  let controller = null;
  let debounceTimer = null;
  let lastRequest = 0;

  const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, char => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
  })[char]);
  const money = amount => `₱${Math.abs(Number(amount) || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
  const labels = {
    order_income: 'Completed order earnings',
    refund: 'Buyer refund',
    return_shipping: 'Return shipping',
    voucher_discount: 'Seller voucher deduction',
    logistics_reimbursement: 'Logistics reimbursement'
  };

  function makeUrl(page = 1) {
    const url = new URL(form.action || window.location.href, window.location.href);
    url.search = '';
    const term = searchInput.value.trim();
    if (term) url.searchParams.set('search', term);
    if (fromInput.value) url.searchParams.set('from', fromInput.value);
    if (toInput.value) url.searchParams.set('to', toInput.value);
    if (selectedType !== 'all') url.searchParams.set('type', selectedType);
    if (page > 1) url.searchParams.set('page', String(page));
    return url;
  }

  function makeApiUrl(page) {
    const url = new URL(apiUrl, window.location.href);
    const pageUrl = makeUrl(page);
    url.search = pageUrl.search;
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

  function renderTransactions(rows) {
    const tbody = panel.querySelector('#walletResultsTable tbody');
    if (!rows.length) {
      tbody.innerHTML = '<tr><td colspan="5" class="sw-empty">No matching transactions found. Try another search term or change the filters.</td></tr>';
      return;
    }
    tbody.innerHTML = rows.map(row => {
      const positive = Number(row.amount) >= 0;
      const date = row.occurred_at ? new Date(String(row.occurred_at).replace(' ', 'T')).toLocaleString('en-US', { month: 'short', day: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : '—';
      return `<tr>
        <td><div class="sw-transaction"><span class="sw-tr-icon ${positive ? 'sw-positive-bg' : 'sw-negative-bg'}">${positive ? '↗' : '↙'}</span><span><strong>${escapeHtml(labels[row.entry_type] || 'Wallet adjustment')}</strong><small>${escapeHtml(row.description)}</small></span></div></td>
        <td class="sw-order">${escapeHtml(row.reference || '—')}</td>
        <td>${escapeHtml(date)}</td><td><span class="sw-status">Recorded</span></td>
        <td class="sw-right sw-money ${positive ? 'sw-positive' : 'sw-negative'}">${positive ? '+' : '−'}${money(row.amount)}</td>
      </tr>`;
    }).join('');
  }

  function renderPagination(meta) {
    const container = panel.querySelector('#walletPagination');
    if (meta.last_page <= 1) {
      container.innerHTML = '';
      return;
    }
    const current = Number(meta.current_page);
    const pages = [];
    for (let page = Math.max(1, current - 2); page <= Math.min(meta.last_page, current + 2); page++) {
      pages.push(page === current
        ? `<span class="sw-page-link sw-current" aria-current="page">${page}</span>`
        : `<a class="sw-page-link" href="${escapeHtml(makeUrl(page).toString())}">${page}</a>`);
    }
    container.innerHTML = `<p class="sw-pagination-summary">Showing ${meta.from ?? 0}–${meta.to ?? 0} of ${meta.total}</p><div class="sw-pagination-pages" aria-label="Wallet pages">${current > 1 ? `<a class="sw-page-link" href="${escapeHtml(makeUrl(current - 1).toString())}" aria-label="Previous page">‹</a>` : '<span class="sw-page-link sw-disabled" aria-disabled="true">‹</span>'}${pages.join('')}${current < meta.last_page ? `<a class="sw-page-link" href="${escapeHtml(makeUrl(current + 1).toString())}" aria-label="Next page">›</a>` : '<span class="sw-page-link sw-disabled" aria-disabled="true">›</span>'}</div>`;
  }

  async function refresh(page = 1, push = false) {
    const requestNumber = ++lastRequest;
    if (controller) controller.abort();
    controller = new AbortController();
    panel.classList.add('sw-is-loading');
    status.textContent = 'Updating transactions…';
    try {
      const response = await fetch(makeApiUrl(page), {
        method: 'GET', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin', signal: controller.signal
      });
      if (!response.ok) throw new Error(response.status === 422 ? 'Check the selected date range.' : 'Could not load transactions.');
      const result = await response.json();
      if (requestNumber !== lastRequest) return;
      renderTransactions(result.transactions.data);
      renderPagination(result.transactions);
      document.getElementById('walletResultCount').textContent = `${result.transactions.total} records`;
      const url = makeUrl(result.transactions.current_page);
      window.history[push ? 'pushState' : 'replaceState']({ walletFilter: true }, '', url.pathname + url.search);
      status.textContent = '';
    } catch (error) {
      if (error.name !== 'AbortError') {
        status.textContent = error.message || 'Unable to update transactions. Please retry.';
        console.error('Wallet API:', error);
      }
    } finally {
      if (requestNumber === lastRequest) panel.classList.remove('sw-is-loading');
    }
  }

  function scheduleRefresh() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => refresh(1, true), 320);
  }

  searchInput?.addEventListener('input', scheduleRefresh);
  [fromInput, toInput].forEach(input => input?.addEventListener('change', () => {
    clearTimeout(debounceTimer);
    refresh(1, true);
  }));
  form.addEventListener('submit', event => { event.preventDefault(); clearTimeout(debounceTimer); refresh(1, true); });

  form.querySelector('.sw-clear-btn')?.addEventListener('click', event => {
    event.preventDefault();
    searchInput.value = '';
    fromInput.value = '';
    toInput.value = '';
    selectedType = 'all';
    setTabs();
    refresh(1, true);
  });

  panel.addEventListener('click', event => {
    const typeLink = event.target.closest('a[data-wallet-type]');
    if (typeLink) {
      event.preventDefault();
      selectedType = typeLink.dataset.walletType || 'all';
      setTabs();
      refresh(1, true);
      return;
    }
    const paginationLink = event.target.closest('#walletPagination a[href]');
    if (paginationLink) {
      event.preventDefault();
      const page = Number(new URL(paginationLink.href).searchParams.get('page') || 1);
      refresh(page, true);
    }
  });

  window.addEventListener('popstate', () => {
    const url = new URL(window.location.href);
    searchInput.value = url.searchParams.get('search') || '';
    fromInput.value = url.searchParams.get('from') || '';
    toInput.value = url.searchParams.get('to') || '';
    selectedType = url.searchParams.get('type') || 'all';
    setTabs();
    refresh(Number(url.searchParams.get('page') || 1));
  });
});
