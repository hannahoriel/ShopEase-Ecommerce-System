/* resources/js/seller/seller-performance.js */
document.addEventListener('DOMContentLoaded', () => {
    const $ = (id) => document.getElementById(id);
    const bootstrap = JSON.parse($('spBootstrap')?.textContent || '{}');
    const settings = JSON.parse($('spSettings')?.textContent || '{}');
    // Real server data only. No sample complaints, notices, or evidence.
    const INVENTORY_PATH = '/seller/inventory';
    const state = { data: bootstrap && typeof bootstrap === 'object' ? bootstrap : {}, search: '', status: 'all', date: '', page: 1, size: 7, selected: null };
    const htmlEscape = (v) => String(v ?? '—').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    const text = (id, v) => { if ($(id))
        $(id).textContent = v ?? '—'; };
    const date = (value) => { if (!value)
        return '—'; const d = new Date(value); return Number.isNaN(d.valueOf()) ? String(value) : d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }); };
    const label = (value) => ({ open: 'Open', 'in-progress': 'In Progress', resolved: 'Resolved', compliant: 'Compliant', warning: 'Warning Issued', 'under-review': 'Under Review', suspended: 'Suspended' })[value] || String(value || 'Not available').replace(/[-_]/g, ' ');
    const norm = (value) => String(value ?? '').toLowerCase().trim().replace(/_/g, '-');
    const statusTone = (value) => ({ compliant: 'green', resolved: 'green', open: 'orange', 'in-progress': 'orange', warning: 'orange', 'under-review': 'orange', suspended: 'red' })[norm(value)] || 'neutral';
    const safeNoticeAttr = value => String(value ?? '').replace(/[&<>"']/g, ch => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[ch]));
    function navigateInventory(tab, productId = '', productName = '') {
        const url = new URL(INVENTORY_PATH, window.location.origin);
        url.searchParams.set('tab', tab);
        if (productId !== null && String(productId).trim()) url.searchParams.set('product', String(productId).trim());
        if (productName && !String(productId ?? '').trim()) url.searchParams.set('product_name', String(productName));
        window.location.assign(url.pathname + url.search);
    }
    function chooseComplaintsTab(name) {
        const target = document.querySelector(`.complaints-tab[data-tab="${name}"]`);
        if (!target) return;
        target.click();
        document.querySelector('.sp-complaints')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    function metricNavigate(metric) {
        switch (metric.dataset.navigate) {
            case 'policy': navigateInventory('policy'); break;
            case 'archived': navigateInventory('archived'); break;
            case 'active': chooseComplaintsTab('open'); break;
            case 'resolved': chooseComplaintsTab('resolved'); break;
            case 'returns': window.location.assign('/seller/returns-refunds'); break;
        }
    }
    document.querySelectorAll('.sp-metric-link').forEach(metric => {
        metric.addEventListener('click', () => metricNavigate(metric));
        metric.addEventListener('keydown', event => {
            if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); metricNavigate(metric); }
        });
    });
    $('spNoticeList')?.addEventListener('click', event => {
        const notice = event.target.closest('.sp-notice-link');
        if (notice) navigateInventory(notice.dataset.noticeStatus === 'removed' ? 'archived' : 'policy', notice.dataset.productId, notice.dataset.productName);
    });
    $('spNoticeList')?.addEventListener('keydown', event => {
        const notice = event.target.closest('.sp-notice-link');
        if (notice && (event.key === 'Enter' || event.key === ' ')) {
            event.preventDefault();
            navigateInventory(notice.dataset.noticeStatus === 'removed' ? 'archived' : 'policy', notice.dataset.productId, notice.dataset.productName);
        }
    });
    // Compliance notices are enforcement records, not the seller's own archives.
    // For a removal, require an explicit admin actor/flag from the backend.
    // Unknown attribution must NOT be assumed to be an admin removal.
    const isAdminRemoval = notice => {
        const actor = norm(notice.archive_actor ?? notice.archiveActor ?? notice.archived_by_role ?? notice.archivedByRole ?? notice.actor_role ?? notice.actorRole ?? notice.removed_by_role ?? notice.removedByRole ?? notice.archived_by ?? notice.archivedBy ?? '');
        const flag = notice.archived_by_admin ?? notice.archivedByAdmin ?? notice.removed_by_admin ?? notice.removedByAdmin;
        if (flag === true || flag === 1 || flag === '1') return true;
        if (flag === false || flag === 0 || flag === '0') return false;
        return ['admin', 'administrator', 'superadmin', 'super-admin', 'platform-admin'].includes(actor);
    };
    const complianceNotices = data => (Array.isArray(data?.notices) ? data.notices : [])
        .filter(notice => {
            const status = norm(notice.status);
            if (['violation-confirmed','seller-at-fault','guilty'].includes(status) && ['complaint','return-refund','return_refund','refund'].includes(norm(notice.source || notice.type || notice.category))) return true;
            if (status === 'warning') return true;
            if (['removed', 'archived', 'product-removed'].includes(status)) return isAdminRemoval(notice);
            return false;
        });
    function render() {
        const d = state.data || {}, compliance = d.compliance || {}, complaints = Array.isArray(d.complaints) ? d.complaints : [], notices = complianceNotices(d);
        const score = compliance.score === null || compliance.score === undefined || compliance.score === '' ? null : Math.max(0, Math.min(100, Number(compliance.score)));
        text('spScore', Number.isFinite(score) ? `${Math.round(score)}%` : '—');
        const gauge = $('spGauge');
        const normalizedScore = Number.isFinite(score) ? score : 0;
        gauge.style.setProperty('--score', normalizedScore);
        const complianceKey = norm(compliance.status);
        const sellerComplianceColor = complianceKey === 'compliant' ? '#13951A'
            : complianceKey === 'suspended' ? '#CF1616' : '#FF9D22';
        gauge.style.setProperty('--gauge-color', sellerComplianceColor);
        gauge.classList.toggle('sp-gauge-unavailable', !Number.isFinite(score));
        gauge.setAttribute('aria-valuenow', Math.round(normalizedScore));
        const pill = $('spComplianceStatus');
        pill.className = `sp-pill sp-pill-${['compliant', 'warning', 'under-review', 'suspended'].includes(norm(compliance.status)) ? norm(compliance.status) : 'neutral'}`;
        pill.textContent = compliance.label || label(norm(compliance.status));
        text('spScoreHeading', compliance.store_name || 'Your shop compliance');
        text('spPolicyMessage', compliance.policy || 'Your compliance status and policy message will appear once provided by your account records.');
        text('spWarnings', notices.filter(n => norm(n.status) === 'warning').length);
        text('spRemoved', notices.filter(n => ['removed','archived','product-removed'].includes(norm(n.status))).length);
        text('spActiveCases', complaints.filter(c => ['open', 'in-progress'].includes(norm(c.status))).length);
        text('spResolved', complaints.filter(c => norm(c.status) === 'resolved').length);
        const rrCount = d.return_refund_count ?? d.returnRefundCount ?? (Array.isArray(d.return_refunds) ? d.return_refunds.length : null);
        text('spReturnRefunds', rrCount === null ? '—' : rrCount);
        text('spNoticeCount', `${notices.length} ${notices.length === 1 ? 'record' : 'records'}`);
        $('spNoticeList').innerHTML = notices.length ? notices.map(n => {
            const confirmed = ['violation-confirmed','seller-at-fault','guilty'].includes(norm(n.status));
            const removed = !confirmed && norm(n.status) !== 'warning';
            const kind = confirmed ? 'Seller Violation Confirmed' : removed ? 'Product Removed' : 'Warning Issued';
            const source = confirmed ? norm(n.source || n.type || n.category).replace(/[-_]/g, ' ') : 'Product';
            return `<article class="sp-notice ${confirmed ? '' : 'sp-notice-link'}" ${confirmed ? '' : `data-notice-status="${removed ? 'removed':'warning'}" data-product-id="${safeNoticeAttr(n.product_id ?? n.productId ?? n.id ?? '')}" data-product-name="${safeNoticeAttr(n.product_name ?? '')}" role="link" tabindex="0"`}><div class="sp-notice-icon ${removed ? 'removed' : 'warning'}">${removed ? '×' : '!'}</div><div class="sp-notice-content"><strong>${htmlEscape(confirmed ? `${source} · ${kind}` : `${n.product_name || 'Product notice'} · ${kind}`)}</strong><p>${htmlEscape(n.reason || 'No reason provided')}${n.details ? ` — ${htmlEscape(n.details)}` : ''}</p></div><time class="sp-notice-date">${htmlEscape(date(n.date))}</time></article>`;
        }).join('') : '<div class="sp-empty">No confirmed compliance notices on record.</div>';
        renderTable();
    }
    const safe = htmlEscape;
    const blank = v => (v === undefined || v === null || v === '') ? '—' : String(v);
    const dateTime = v => { if (!v)
        return '—'; const d = new Date(v); return Number.isNaN(d.valueOf()) ? String(v) : d.toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit' }); };
    const statusName = status => ({ open: 'Open', 'in-progress': 'In progress', resolved: 'Resolved' })[norm(status)] || 'Open';
    const roleNames = c => [
        { name: c.party1 || c.complainant || 'Buyer', role: c.role1 || 'Buyer', email: c.party1Email || c.buyer_email || '' },
        { name: c.party2 || state.data?.compliance?.store_name || 'Your Shop', role: c.role2 || 'Seller', email: c.party2Email || c.seller_email || '' }
    ];
    const evidenceList = c => Array.isArray(c.evidence) ? c.evidence : Array.isArray(c.attachments) ? c.attachments : [];
    const imgUrl = item => typeof item === 'string' ? item : item?.url || item?.path || '';
    const trustedImageUrl = v => { const s = String(v || ''); return (/^(https?:\/\/|\/)(?!\/)/i.test(s) || false) ? s : ''; };
    const partHtml = party => `<div class="complaint-party"><span class="complaint-avatar">${safe((party.name || '?').slice(0, 1).toUpperCase())}</span><span class="complaint-party-copy"><span class="complaint-party-name">${safe(party.name)}</span><span class="complaint-party-role">${safe(party.role)}</span></span></div>`;
    const statusHtml = status => `<span class="complaint-status ${safe(norm(status))}">${safe(statusName(status))}</span>`;
    const complaints = () => Array.isArray(state.data?.complaints) ? state.data.complaints : [];
    function filtered() {
        const q = state.search.toLowerCase();
        return complaints().filter(c => (state.status === 'all' || norm(c.status) === state.status) &&
            (!state.date || String(c.filed_at || c.date || '').slice(0, 10) === state.date || date(c.filed_at || c.date) === date(state.date)) &&
            (!q || [c.id, c.type, c.order_id, c.party1, c.party2, c.complainant, c.description].some(v => String(v || '').toLowerCase().includes(q))));
    }
    function renderTable() {
        const all = filtered(), total = Math.max(1, Math.ceil(all.length / state.size));
        state.page = Math.min(state.page, total);
        const pageItems = all.slice((state.page - 1) * state.size, state.page * state.size);
        $('complaints-table-body').innerHTML = pageItems.map(c => { const people = roleNames(c); return `<tr class="complaint-row ${String(state.selected?.id) === String(c.id) ? 'active' : ''}" data-id="${safe(c.id)}" role="button" tabindex="0" aria-label="Preview complaint ${safe(c.id)}"><td><span class="complaint-id">${safe(c.id)}</span></td><td><div class="complaint-parties">${people.map(partHtml).join('')}</div></td><td><span class="complaint-type">${safe(blank(c.type))}</span></td><td>${statusHtml(c.status)}</td><td><span class="complaint-date-cell"><span>${safe(date(c.filed_at || c.date))}<br>${safe(c.time || (c.filed_at && !Number.isNaN(new Date(c.filed_at).valueOf()) ? new Date(c.filed_at).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' }) : ''))}</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 5 7 7-7 7"/></svg></span></td></tr>`; }).join('');
        $('complaints-empty').style.display = all.length ? 'none' : 'block';
        $('complaints-count').textContent = `Showing ${pageItems.length} out of ${all.length} entries`;
        $('complaints-prev').disabled = state.page === 1;
        $('complaints-next').disabled = state.page === total;
        $('complaints-page-numbers').innerHTML = Array.from({ length: total }, (_, index) => index + 1).filter(n => total <= 7 || n === 1 || n === total || Math.abs(n - state.page) <= 1).map((n, index, arr) => `${index && n - arr[index - 1] > 1 ? '<span>…</span>' : ''}<button type="button" class="complaints-page-button ${n === state.page ? 'active' : ''}" data-page="${n}">${n}</button>`).join('');
        if (state.selected) {
            const newest = complaints().find(c => String(c.id) === String(state.selected.id));
            if (newest) {
                state.selected = newest;
                renderPreview(newest);
            }
            else
                closePreview();
        }
    }
    function evidenceMarkup(c, preview = false) { const pics = evidenceList(c).map(imgUrl).filter(trustedImageUrl); if (!pics.length)
        return '<span>No evidence attached.</span>'; return pics.slice(0, preview ? 4 : 30).map((url, i) => `<button type="button" class="sp-evidence-thumb" data-evidence-index="${i}" aria-label="Enlarge supporting evidence ${i + 1}"><img src="${safe(url)}" alt="Supporting evidence ${i + 1}" loading="lazy"></button>`).join('') + (preview && pics.length > 4 ? `<span>+${pics.length - 4}</span>` : ''); }
    function renderPreview(c) {
        $('complaints-workspace').classList.add('has-selection');
        $('complaints-detail-card').setAttribute('aria-hidden', 'false');
        text('detail-id', c.id);
        text('detail-date', dateTime(c.filed_at || c.date));
        text('detail-type', blank(c.type));
        text('detail-description', blank(c.description));
        $('detail-status').className = `complaint-status ${norm(c.status)}`;
        $('detail-status').textContent = statusName(c.status);
        $('detail-parties').innerHTML = roleNames(c).map(partHtml).join('');
        $('detail-evidence').innerHTML = evidenceMarkup(c, true);
    }
    function selectCase(id) { const c = complaints().find(x => String(x.id) === String(id)); if (!c)
        return; state.selected = c; renderTable(); }
    // Deep link from Seller Messages: select complaint row and open the RIGHT preview.
    // Uses a URL query (not the large View Details modal).
    const linkedComplaintId = new URLSearchParams(window.location.search).get('complaint');
    let complaintDeepLinkHandled = false;
    function openLinkedComplaintPreview() {
        if (!linkedComplaintId || complaintDeepLinkHandled) return;
        const normalizeId = value => String(value ?? '').replace(/^Complaint\s*#?\s*/i, '').trim().toLowerCase();
        const target = complaints().find(c => normalizeId(c.id) === normalizeId(linkedComplaintId));
        if (!target) return; // May arrive from async API response; try again after load.
        complaintDeepLinkHandled = true;
        state.search = '';
        state.date = '';
        state.status = 'all';
        $('complaints-search').value = '';
        $('complaints-date-filter').value = '';
        document.querySelectorAll('.complaints-tab').forEach(tab => {
            const active = tab.dataset.tab === 'all';
            tab.classList.toggle('active', active);
            tab.setAttribute('aria-selected', String(active));
        });
        const index = filtered().findIndex(c => normalizeId(c.id) === normalizeId(target.id));
        state.page = Math.floor(Math.max(0, index) / state.size) + 1;
        selectCase(target.id);
        requestAnimationFrame(() => {
            const section = document.querySelector('.sp-complaints');
            section?.scrollIntoView({ behavior: 'auto', block: 'start' });
            const row = [...document.querySelectorAll('#complaints-table-body .complaint-row')]
                .find(el => normalizeId(el.dataset.id) === normalizeId(target.id));
            row?.setAttribute('aria-selected', 'true');
        });
    }
    function closePreview() { state.selected = null; $('complaints-workspace').classList.remove('has-selection'); $('complaints-detail-card').setAttribute('aria-hidden', 'true'); $('complaints-table-body').querySelectorAll('.complaint-row.active').forEach(el => el.classList.remove('active')); }
    function sectionRows(fields) { return fields.map(([key, value]) => `<div class="complaints-modal-order-row"><span>${safe(key)}</span><span>${safe(blank(value))}</span></div>`).join(''); }
    function statusUpdateMessage(status) {
        return ({
            open: 'Complaint opened.',
            'in-progress': 'Complaint status changed to In progress.',
            resolved: 'Complaint status changed to Resolved.'
        })[status] || 'Complaint status updated.';
    }

    function getStatusUpdates(complaint) {
        const history = Array.isArray(complaint.statusHistory) && complaint.statusHistory.length
            ? complaint.statusHistory : (Array.isArray(complaint.updates) ? complaint.updates : []);
        const statuses = new Set(['open', 'in-progress', 'resolved']);
        const items = history.map(event => {
            if (!event || typeof event !== 'object') return null;
            let status = norm(event.status || event.new_status || event.newStatus || '');
            const eventType = norm(event.type || '');
            if (status === 'in progress') status = 'in-progress';
            if (!statuses.has(status) && (eventType === 'status' || eventType === 'status-change')) {
                const match = String(event.message || '').toLowerCase()
                    .match(/(?:changed to|status(?: is| updated to)?|marked as)\s+(in[ -]progress|open|resolved)/);
                if (match) status = norm(match[1]).replace(/\s+/g, '-');
            }
            if (!statuses.has(status)) return null;
            return {
                status,
                actor: event.actor || 'Admin',
                timestamp: event.timestamp || event.created_at || event.date || ''
            };
        }).filter(Boolean);

        // Real data without a status history: show only the known current status.
        // We deliberately do not invent earlier workflow events.
        if (!items.length && statuses.has(norm(complaint.status))) {
            items.push({
                status: norm(complaint.status),
                actor: 'ShopEase Support',
                timestamp: complaint.updated_at || complaint.filed_at || ''
            });
        }
        return items.sort((a, b) => Date.parse(b.timestamp || 0) - Date.parse(a.timestamp || 0));
    }

    function openModal() {
        const c = state.selected;
        if (!c)
            return;
        const people = roleNames(c);
        text('modal-complaint-id', c.id);
        text('modal-date-filed', dateTime(c.filed_at || c.date));
        text('modal-last-updated', dateTime(c.updated_at || c.lastUpdated || c.filed_at));
        text('modal-complaint-type', blank(c.type));
        text('modal-order-id-top', blank(c.order_id || c.orderId));
        text('modal-description-copy', blank(c.description));
        $('modal-complaint-status').className = `complaints-modal-status ${norm(c.status)}`;
        $('modal-complaint-status').textContent = statusName(c.status);
        const userIcon = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><circle cx="12" cy="8" r="3.3"/><path d="M5.3 20c.1-4.1 2.6-6.3 6.7-6.3s6.6 2.2 6.7 6.3"/></svg>`;
        const storeIcon = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M3 9 5 4h14l2 5v2a3 3 0 0 1-6 0 3 3 0 0 1-6 0 3 3 0 0 1-6 0V9zM5 13v7h14v-7"/></svg>`;
        $('modal-parties-list').innerHTML = people.map(p => `
            <div class="complaints-modal-party-row">
                <span class="complaints-modal-party-avatar" aria-hidden="true">${userIcon}</span>
                <span class="complaints-modal-party-copy">
                    <strong title="${safe(p.name)}">${safe(p.name)}</strong>
                    <span title="${safe(p.email || '')}">${safe(p.email || 'No email provided')}</span>
                </span>
                <span class="complaints-modal-party-role">${norm(p.role) === 'seller' ? storeIcon : userIcon}${safe(p.role)}</span>
            </div>`).join('');
        $('modal-order-information').innerHTML = sectionRows([['Order ID', c.order_id || c.orderId], ['Order Date', dateTime(c.orderDate || c.order_date)], ['Payment Method', 'Cash on Delivery'], ['Payment Status', c.paymentStatus || c.payment_status], ['Shipping Method', c.shippingMethod || c.shipping_method], ['Order Status', c.orderStatus || c.order_status], ['Order Total', c.orderTotal || c.order_total]]);
        const product = c.product_name || c.productName;
        const img = trustedImageUrl(c.product_image || c.productImage || '');
        $('modal-product-info').innerHTML = `
            <div class="complaints-modal-item-main">
                ${img ? `<img class="complaints-modal-product-image" src="${safe(img)}" alt="Ordered item">` : '<div class="complaints-modal-product-image"></div>'}
                <div>
                    <div class="complaints-modal-product-name">${safe(blank(product))}</div>
                    <div class="complaints-modal-product-variant">${safe(blank(c.productVariant || c.product_variant))}</div>
                    <div class="complaints-modal-product-price"><strong>${safe(blank(c.productPrice || c.product_price))}</strong><span>Qty: ${safe(blank(c.productQuantity || c.product_quantity))}</span></div>
                </div>
            </div>
            <div class="complaints-modal-item-extra">
                <div><span class="complaints-modal-item-extra-label">Category</span><span class="complaints-modal-category-pill">${safe(blank(c.productCategory || c.product_category))}</span></div>
                <div class="complaints-modal-shop-line">Shop: <strong>${safe(blank(c.shopName || c.shop_name || state.data?.compliance?.store_name))}</strong></div>
            </div>`;
        $('modal-evidence-list').innerHTML = evidenceMarkup(c);
        const statusUpdates = getStatusUpdates(c);
        $('complaints-modal-notes-list').innerHTML = statusUpdates.length
            ? statusUpdates.map(u => `
                <div class="complaints-modal-note">
                    <span class="complaints-modal-note-avatar">${safe(String(u.actor || 'A').charAt(0).toUpperCase())}</span>
                    <div class="complaints-modal-note-copy">
                        <strong>${safe(u.actor || 'Admin')}</strong>
                        <span>${safe(statusUpdateMessage(u.status))}</span>
                    </div>
                    <span class="complaints-modal-note-time">${safe(dateTime(u.timestamp))}</span>
                </div>`).join('')
            : '<div class="complaints-modal-notes-empty">No status updates recorded yet.</div>';
        $('complaints-details-modal').hidden = false;
        $('complaints-details-modal').setAttribute('aria-hidden', 'false');
        document.body.classList.add('sp-modal-open');
        $('complaints-details-modal').querySelector('.complaints-modal-dialog').focus();
    }
    function closeModal() { $('complaints-details-modal').hidden = true; $('complaints-details-modal').setAttribute('aria-hidden', 'true'); document.body.classList.remove('sp-modal-open'); }
    $('complaints-table-body').addEventListener('click', e => { const row = e.target.closest('.complaint-row'); if (row)
        selectCase(row.dataset.id); });
    $('complaints-table-body').addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') {
        const row = e.target.closest('.complaint-row');
        if (row) {
            e.preventDefault();
            selectCase(row.dataset.id);
        }
    } });
    document.querySelectorAll('.complaints-tab').forEach(tab => tab.addEventListener('click', () => { state.status = tab.dataset.tab; state.page = 1; document.querySelectorAll('.complaints-tab').forEach(t => { const active = t === tab; t.classList.toggle('active', active); t.setAttribute('aria-selected', String(active)); }); closePreview(); renderTable(); }));
    $('complaints-search').addEventListener('input', e => { state.search = e.target.value.trim(); state.page = 1; closePreview(); renderTable(); });
    $('complaints-date-filter').addEventListener('change', e => { state.date = e.target.value; state.page = 1; closePreview(); renderTable(); });
    $('complaints-date-button').addEventListener('click', () => { if (typeof $('complaints-date-filter').showPicker === 'function')
        $('complaints-date-filter').showPicker();
    else
        $('complaints-date-filter').focus(); });
    $('complaints-reload').addEventListener('click', () => { state.search = ''; state.date = ''; state.status = 'all'; state.page = 1; $('complaints-search').value = ''; $('complaints-date-filter').value = ''; document.querySelector('[data-tab="all"]').click(); $('complaints-reload').classList.add('is-spinning'); setTimeout(() => $('complaints-reload').classList.remove('is-spinning'), 500); });
    $('complaints-items').addEventListener('change', e => { state.size = Number(e.target.value) || 7; state.page = 1; closePreview(); renderTable(); });
    $('complaints-prev').addEventListener('click', () => { if (state.page > 1) {
        state.page--;
        renderTable();
    } });
    $('complaints-next').addEventListener('click', () => { if (state.page < Math.ceil(filtered().length / state.size)) {
        state.page++;
        renderTable();
    } });
    $('complaints-page-numbers').addEventListener('click', e => { const btn = e.target.closest('[data-page]'); if (btn) {
        state.page = Number(btn.dataset.page);
        renderTable();
    } });
    $('complaints-detail-close').addEventListener('click', closePreview);
    $('complaints-view-details').addEventListener('click', openModal);
    $('complaints-modal-close').addEventListener('click', closeModal);
    $('complaints-modal-done').addEventListener('click', closeModal);
    /* Contact Admin: compose and copy only; does not send. */
    const contactOverlay = $('sp-contact-dialog');
    let contactReturnFocus = null;
    function closeContact() {
        if (contactOverlay.hidden) return;
        contactOverlay.hidden = true;
        contactOverlay.setAttribute('aria-hidden', 'true');
        if (contactReturnFocus && contactReturnFocus.isConnected) contactReturnFocus.focus();
    }
    function openContact() {
        const c = state.selected;
        if (!c) return;
        contactReturnFocus = $('sp-contact-admin');
        $('sp-contact-message').value =
            `Hello ShopEase Admin,\n\nI would like assistance with complaint ${c.id} ` +
            `(${c.type || 'Customer complaint'}) regarding order ${c.order_id || c.orderId || 'N/A'}. ` +
            `The current case status is ${statusName(c.status)}.\n\nThank you.`;
        $('sp-contact-feedback').textContent = '';
        contactOverlay.hidden = false;
        contactOverlay.setAttribute('aria-hidden', 'false');
        $('sp-contact-message').focus();
    }
    // Navigate directly to Seller Messages > Complaints (no copy-only dialog).
    $('sp-contact-admin').addEventListener('click', () => {
        const c = state.selected;
        if (!c) return;
        const params = new URLSearchParams({ tab: 'complaints', complaint: String(c.id), source: 'performance' });
        window.location.assign('/seller/messages?' + params.toString());
    });
    $('sp-contact-close').addEventListener('click', closeContact);
    $('sp-contact-cancel').addEventListener('click', closeContact);
    contactOverlay.addEventListener('click', event => {
        if (event.target === contactOverlay) closeContact();
    });
    $('sp-contact-copy').addEventListener('click', async () => {
        const value = $('sp-contact-message').value;
        if (!value.trim()) {
            $('sp-contact-feedback').textContent = 'Please write a message first.';
            return;
        }
        let copied = false;
        try {
            if (navigator.clipboard?.writeText) {
                await navigator.clipboard.writeText(value);
                copied = true;
            }
        } catch (_) { /* Fall back to selection-based copying. */ }
        if (!copied) {
            const textarea = $('sp-contact-message');
            textarea.focus();
            textarea.select();
            try { copied = document.execCommand('copy'); } catch (_) { /* Copy unsupported. */ }
        }
        $('sp-contact-feedback').textContent = copied
            ? 'Message copied! Paste it into ShopEase Messages to contact the admin.'
            : 'Please select and copy the message manually.';
    });
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && !contactOverlay.hidden) {
            event.stopPropagation();
            closeContact();
        }
    });

    $('complaints-details-modal').addEventListener('click', e => { if (e.target === $('complaints-details-modal'))
        closeModal(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape' && $('sp-contact-dialog').hidden && !$('complaints-details-modal').hidden)
        closeModal(); });
    // Existing dashboard navbar component owns its styling. Only update its visible heading.
    const titleEl = document.querySelector('#navbar-left h1, .seller-navbar-title, .seller-navbar .page-title, .navbar-page-title');
    if (titleEl)
        titleEl.textContent = 'Performance';
    async function load() { try {
        const r = await fetch(settings.endpoint, { headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' });
        if (!r.ok)
            throw new Error(`Could not load performance (${r.status})`);
        state.data = await r.json();
        render();
        openLinkedComplaintPreview();
    }
    catch (error) {
        text('spError', error.message);
        $('spError').hidden = false;
    } }
    // Supporting evidence image preview.
    function openEvidence(imageIndex) {
        const selected = state.selected;
        if (!selected)
            return;
        const image = evidenceList(selected).map(imgUrl).filter(trustedImageUrl)[imageIndex];
        if (!image)
            return;
        $('sp-evidence-image').src = image;
        $('sp-evidence-caption').textContent = `Supporting evidence ${imageIndex + 1} · ${selected.id}`;
        $('sp-evidence-viewer').hidden = false;
        $('sp-evidence-viewer').setAttribute('aria-hidden', 'false');
    }
    function closeEvidence() {
        $('sp-evidence-viewer').hidden = true;
        $('sp-evidence-viewer').setAttribute('aria-hidden', 'true');
        $('sp-evidence-image').removeAttribute('src');
    }
    document.addEventListener('click', event => {
        const thumbnail = event.target.closest('.sp-evidence-thumb');
        if (thumbnail)
            openEvidence(Number(thumbnail.dataset.evidenceIndex));
    });
    $('sp-evidence-close').addEventListener('click', closeEvidence);
    $('sp-evidence-viewer').addEventListener('click', e => {
        if (e.target === $('sp-evidence-viewer'))
            closeEvidence();
    });
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && !$('sp-evidence-viewer').hidden)
            closeEvidence();
    });
    render();
    openLinkedComplaintPreview();
    if (settings.endpoint) load();
});