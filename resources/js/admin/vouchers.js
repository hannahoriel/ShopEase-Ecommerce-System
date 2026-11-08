document.addEventListener('DOMContentLoaded', () => {
    const $ = id => document.getElementById(id);
    const app = $('admin-content');
    if (!app || !$('avGrid')) return;
    const canManage = app.dataset.canManage === '1';
    const token = JSON.parse($('adminVoucherConfig').textContent).apiToken;
    const endpoint = '/api/v1/admin/vouchers';
    const form = $('svVoucherForm');
    const createModal = $('svCreateOverlay');
    const detailModal = $('svDetailsOverlay');
    const esc = value => String(value ?? '').replace(/[&<>"']/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[char]));
    const money = value => '₱' + Number(value || 0).toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2});
    const date = value => new Date(value).toLocaleString('en-PH', {month:'short',day:'numeric',year:'numeric',hour:'numeric',minute:'2-digit'});
    const discount = voucher => voucher.discountType === 'percent' ? `${voucher.discountValue}%` : money(voucher.discountValue);
    const status = voucher => !voucher.enabled ? 'paused' : voucher.claimed >= voucher.quantity ? 'exhausted' : Date.now() < new Date(voucher.starts) ? 'upcoming' : Date.now() > new Date(voucher.ends) ? 'expired' : 'active';
    const statusLabel = value => ({active:'Active',upcoming:'Upcoming',paused:'Paused',expired:'Expired',exhausted:'Fully Claimed'})[value];
    let vouchers = [], selected = null, lastFocus = null, busy = false;
    const message = (id, text) => { $(id).textContent = text; $(id).hidden = !text; };

    async function request(url, options = {}) {
        const response = await fetch(url, {...options, credentials:'same-origin', headers:{
            Accept:'application/json', 'Content-Type':'application/json',
            'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With':'XMLHttpRequest', ...(token ? {Authorization:`Bearer ${token}`} : {}),
        }});
        const payload = await response.json().catch(() => ({}));
        if (!response.ok) throw new Error(Object.values(payload.errors || {}).flat()[0] || payload.message || 'Unable to save vouchers. Please try again.');
        return payload;
    }

    function render() {
        const query = $('avSearch').value.trim().toLowerCase(), filter = $('avFilter').value;
        const filtered = vouchers.filter(v => `${v.id} ${v.title}`.toLowerCase().includes(query) && (filter === 'all' || status(v) === filter));
        $('avGrid').innerHTML = filtered.map(v => `<article class="sv-voucher admin" role="button" tabindex="0" data-code="${esc(v.id)}" aria-label="View ${esc(v.title)}"><div class="sv-voucher-rail"><img src="/icons/seller/sidebar%26navbar/vouchers-promotions.png" width="25" height="25" alt=""><strong>${esc(discount(v))}</strong><small>OFF</small></div><div class="sv-voucher-copy"><div class="sv-voucher-top"><span class="sv-pill ${status(v)}">${statusLabel(status(v))}</span><span class="sv-origin">ShopEase Admin</span></div><h3>${esc(v.title)}</h3><span class="sv-code">${esc(v.id)}</span><p>${esc(v.purpose)}</p><div class="sv-remaining">${Math.max(0,v.quantity-v.claimed)} of ${v.quantity} available · Min. ${money(v.minimumSpend)}</div><div class="sv-voucher-bottom"><span>Valid until ${esc(date(v.ends))}</span>${canManage ? `<button type="button" class="sv-pause-button ${v.enabled?'':'is-paused'}" data-toggle="${v.recordId}">${v.enabled?'Pause':'Publish'}</button>` : ''}</div></div></article>`).join('');
        $('avEmpty').hidden = !!filtered.length;
        $('avTotal').textContent = vouchers.length;
        $('avActive').textContent = vouchers.filter(v => status(v) === 'active').length;
        $('avUpcoming').textContent = vouchers.filter(v => status(v) === 'upcoming').length;
        $('avAllocation').textContent = vouchers.reduce((sum,v) => sum + Number(v.quantity),0).toLocaleString('en-PH');
        document.querySelectorAll('[data-status]').forEach(button => {
            const active = button.dataset.status === filter;
            button.classList.toggle('active', active);
            button.setAttribute('aria-selected', String(active));
        });
    }

    function open(modal) {
        lastFocus = document.activeElement;
        modal.hidden = false;
        modal.setAttribute('aria-hidden','false');
        document.body.style.overflow = 'hidden';
        modal.querySelector('.sv-x').focus();
    }
    function close(modal) {
        if (busy) return;
        modal.hidden = true;
        modal.setAttribute('aria-hidden','true');
        document.body.style.overflow = '';
        lastFocus?.focus();
    }
    [createModal, detailModal].forEach(modal => modal.addEventListener('click', event => {
        if (event.target === modal || event.target.closest('[data-close]')) close(modal);
    }));
    document.addEventListener('keydown', event => {
        const modal = [createModal, detailModal].find(element => !element.hidden);
        if (!modal) return;
        if (event.key === 'Escape') close(modal);
        if (event.key === 'Tab') {
            const controls = [...modal.querySelectorAll('button, input, select, textarea, [tabindex="0"]')].filter(el => !el.disabled && el.getClientRects().length);
            const first = controls[0], last = controls.at(-1);
            if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
            else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
        }
    });

    function details(voucher) {
        selected = voucher;
        $('svDetailsTitle').textContent = voucher.title;
        $('svDetailsValue').textContent = `${discount(voucher)} OFF`;
        $('svDetailsSubtitle').textContent = 'ShopEase platform-issued voucher';
        $('svDetailsStatus').textContent = statusLabel(status(voucher));
        $('svDetailsPurpose').textContent = voucher.purpose;
        const fields = [['Voucher Code',voucher.id],['Discount',discount(voucher)],['Minimum Spend',money(voucher.minimumSpend)],['Maximum Discount',money(voucher.maximumDiscount)],['Valid From',date(voucher.starts)],['Expires',date(voucher.ends)],['Eligible Items',voucher.eligibleItems],['Eligible Buyers',voucher.eligibleBuyers],['Total Allocation',voucher.quantity],['Already Claimed',voucher.claimed],['Remaining Allocation',Math.max(0,voucher.quantity-voucher.claimed)],['Uses Per Buyer',voucher.perBuyer],['Voucher Stacking',voucher.stacking],['Scope',voucher.scope],['Payment Method','Cash on Delivery (COD)'],['Status',statusLabel(status(voucher))]];
        $('svDetailsGrid').innerHTML = fields.map(([label,value]) => `<div class="sv-detail"><small>${esc(label)}</small><strong>${esc(value)}</strong></div>`).join('');
        $('svDetailsTerms').innerHTML = [voucher.terms || 'Discount applies to the eligible merchandise subtotal.', 'Valid for one qualifying shop order per checkout, subject to remaining allocation and per-buyer limits.', 'Cannot be exchanged for cash or applied retroactively.'].map(text => `<li>${esc(text)}</li>`).join('');
        $('svDetailsToggle').hidden = !canManage;
        $('svDetailsToggle').textContent = voucher.enabled ? 'Pause Voucher' : 'Publish Voucher';
        open(detailModal);
    }

    async function toggle(voucher, button) {
        if (!canManage || busy) return;
        busy = true; button.disabled = true;
        message('avError',''); message('avSuccess','');
        const finish = window.ShopEaseLoading?.start(button);
        try {
            const payload = await request(`${endpoint}/${voucher.recordId}`, {method:'PATCH',body:JSON.stringify({enabled:!voucher.enabled})});
            Object.assign(voucher,payload.data);
            render();
            message('avSuccess', `${voucher.id} ${voucher.enabled?'published':'paused'} successfully.`);
            busy = false;
            if (!detailModal.hidden) close(detailModal);
        } catch (error) {
            message('avError',error.message);
            busy = false;
            if (!detailModal.hidden) close(detailModal);
        } finally { busy = false; button.disabled = false; finish?.(); }
    }
    $('svDetailsToggle').addEventListener('click', event => toggle(selected,event.currentTarget));
    $('avGrid').addEventListener('click', event => {
        const button = event.target.closest('[data-toggle]');
        if (button) return void toggle(vouchers.find(v => String(v.recordId) === button.dataset.toggle), button);
        const card = event.target.closest('[data-code]');
        if (card) details(vouchers.find(v => v.id === card.dataset.code));
    });
    $('avGrid').addEventListener('keydown', event => {
        if (event.target.matches('[data-code]') && ['Enter',' '].includes(event.key)) {
            event.preventDefault(); details(vouchers.find(v => v.id === event.target.dataset.code));
        }
    });
    $('avSearch').addEventListener('input',render);
    $('avFilter').addEventListener('change',render);
    document.querySelectorAll('[data-status]').forEach(button => button.addEventListener('click', () => {
        $('avFilter').value = button.dataset.status; render();
    }));
    $('svCreate').hidden = !canManage;
    $('svCreate').addEventListener('click', () => {
        if (!canManage) return;
        form.reset(); message('svFormError','');
        const local = time => new Date(time - new Date(time).getTimezoneOffset()*60000).toISOString().slice(0,16);
        form.elements.starts.value = local(Date.now()+60000);
        form.elements.ends.value = local(Date.now()+30*86400000);
        open(createModal);
    });
    form.elements.code.addEventListener('input', event => {
        event.target.value = event.target.value.toUpperCase().replace(/[^A-Z0-9_-]/g,'');
    });
    form.addEventListener('submit', async event => {
        event.preventDefault();
        if (!canManage || busy || !form.reportValidity()) return;
        message('svFormError','');
        const values = Object.fromEntries(new FormData(form));
        const button = form.querySelector('[type="submit"]');
        busy = true; button.disabled = true;
        const finish = window.ShopEaseLoading?.start(button);
        try {
            const payload = await request(endpoint, {method:'POST',body:JSON.stringify({...values,enabled:values.enabled === 'true'})});
            vouchers.unshift(payload.data);
            $('avSearch').value = ''; $('avFilter').value = 'all';
            render(); busy = false; close(createModal);
            message('avSuccess', `${payload.data.id} created successfully.`);
        } catch (error) { message('svFormError',error.message); }
        finally { busy = false; button.disabled = false; finish?.(); }
    });
    request(endpoint).then(payload => { vouchers = payload.data || []; render(); })
        .catch(error => message('avError',error.message))
        .finally(() => { $('avLoading').hidden = true; });
});
