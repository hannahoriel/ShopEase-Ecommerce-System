/**
 * Read-only Admin Commission order details.
 * The modal markup and classes are copied from the Seller Shipping Status Order Details modal.
 */
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('commissionDetailsModal');
    const panel = document.getElementById('commissionDetailsModalPanel');
    const table = document.getElementById('commission-table-body');
    const closeButton = document.getElementById('closeCommissionModal');
    const configElement = document.getElementById('commissionConfig');
    if (!modal || !table || !configElement) return;

    const config = JSON.parse(configElement.textContent || '{}');
    let requestNumber = 0;
    let previousFocus = null;
    const byId = id => document.getElementById(id);
    const text = (id, value) => { const el = byId(id); if (el) el.textContent = value ?? '—'; };
    const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, c => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    })[c]);
    const money = value => '₱' + Number(value || 0).toLocaleString('en-PH', {
        minimumFractionDigits: 2, maximumFractionDigits: 2
    });
    const date = value => {
        if (!value) return '—';
        const d = new Date(value);
        return Number.isNaN(d.getTime()) ? '—' : d.toLocaleDateString('en-US', {
            month: 'long', day: 'numeric', year: 'numeric'
        });
    };
    const time = value => {
        if (!value) return '';
        const d = new Date(value);
        return Number.isNaN(d.getTime()) ? '' : d.toLocaleTimeString('en-US', {
            hour: 'numeric', minute: '2-digit'
        });
    };
    const statusLabel = status => ({
        to_ship: 'To Ship', in_transit: 'In Transit', out_for_delivery: 'Out for Delivery',
        delivered: 'Delivered', completed: 'Completed', refunded: 'Refunded',
        cancelled: 'Cancelled'
    }[status] || String(status || 'Order Recorded').replaceAll('_', ' '));

    function show() {
        previousFocus = document.activeElement;
        modal.classList.add('modal-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
        closeButton?.focus();
    }
    function close() {
        requestNumber++;
        modal.classList.remove('modal-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('overflow-hidden');
        previousFocus?.focus?.();
    }

    function updateProgress(status) {
        const steps = [byId('modalProgressTransit'), byId('modalProgressOut'), byId('modalProgressDelivered')];
        const lines = [byId('modalProgressLine1'), byId('modalProgressLine2')];
        const images = steps.map(step => step?.querySelector('img'));
        const path = '/icons/seller/shipping-status/';
        const level = ({ in_transit: 0, out_for_delivery: 1, delivered: 2, completed: 2 })[status] ?? -1;
        steps.forEach((el, i) => {
            if (!el) return;
            el.classList.toggle('active', i <= level);
            el.classList.toggle('completed', i < level);
        });
        lines.forEach((el, i) => el?.classList.toggle('active', i < level));
        const icons = level >= 2
            ? ['delivered-check.png','delivered-check.png','delivered-check.png']
            : level === 1
                ? ['in-transit-check.png','out-for-delivery.png','delivered-gray.png']
                : ['in-transit-blue.png','out-for-delivery-gray.png','delivered-gray.png'];
        images.forEach((img, i) => { if (img) img.src = path + icons[i]; });
    }

    function render(data) {
        const shipment = data.shipment || {};
        const commission = data.commission || {};
        const created = data.ordered_at;
        const status = String(data.status || '').toLowerCase();

        text('shippingModalOrderId', '#' + String(data.order_number || `ORD-${data.order_id}`));
        text('shippingModalStatusLabel', statusLabel(status));
        text('shippingModalOrderDate', date(created));
        text('shippingModalOrderTime', time(created));
        text('shippingModalEstimated', date(shipment.estimated_delivery));
        text('shippingModalTrackingNumber', shipment.tracking_number || 'Pending');
        text('shippingModalCurrentLocation', shipment.current_location || 'Not scanned yet');
        text('shippingModalCustomer', data.delivery_name || 'Customer');
        text('shippingModalPhone', data.delivery_phone || '—');
        text('shippingModalAddress', data.delivery_address || '—');
        text('shippingModalPaymentMethod', data.payment_method || '—');
        text('shippingModalPaymentDescription', String(data.payment_method || '').toLowerCase().includes('cash')
            ? 'Payment upon Delivery' : 'Order payment method');
        text('shippingModalNotes', data.customer_notes || 'No customer notes provided.');

        const icon = byId('shippingModalOrderIcon');
        const iconBox = byId('shippingModalIconBox');
        const dot = byId('shippingModalStatusDot');
        const delivered = ['delivered', 'completed'].includes(status);
        if (icon) icon.src = '/icons/seller/shipping-status/' + (delivered ? 'delivered.png' : 'in-transit.png');
        if (iconBox) iconBox.className = 'shipping-modal-status-box ' + (delivered ? 'green-modal' : 'blue-modal');
        if (dot) dot.className = 'modal-status-dot ' + (delivered ? 'green-dot' : 'blue-dot');
        updateProgress(status);

        const items = Array.isArray(data.items) ? data.items : [];
        const subtotal = items.reduce((sum, item) => sum + Number(item.unit_price || 0) * Number(item.quantity || 0), 0);
        const list = byId('shippingModalItemsList');
        if (list) list.innerHTML = `
            <div class="shipping-item-header"><span>Item</span><span>Price</span><span>Quantity</span><span>Subtotal</span></div>
            ${items.length ? items.map(item => `
                <div class="shipping-item-row">
                    <div class="shipping-item-name">
                        <div class="shipping-item-image"><div class="modal-product-bag"></div></div>
                        <span>${escapeHtml(item.name || 'Product')}${item.variation ? `<small style="display:block;font-size:10px;color:#928482">${escapeHtml(item.variation)}</small>` : ''}</span>
                    </div>
                    <span>${money(item.unit_price)}</span><span>${Number(item.quantity || 0)}</span>
                    <span>${money(Number(item.unit_price || 0) * Number(item.quantity || 0))}</span>
                </div>`).join('') : '<p style="padding:15px 0">No items recorded for this order.</p>'}
            <div class="shipping-modal-totals">
                <div><span>Subtotal</span><strong>${money(subtotal)}</strong></div>
                <div><span>Shipping Fee</span><strong>${money(data.shipping_fee)}</strong></div>
                ${Number(data.voucher_discount || 0) > 0 ? `<div><span>Voucher Discount</span><strong>−${money(data.voucher_discount)}</strong></div>` : ''}
                <div><span>Total Amount</span><b>${money(data.total)}</b></div>
            </div>`;

        text('commissionDetailOrderAmount', money(commission.order_amount));
        text('commissionDetailRate', Number(commission.rate || 0).toLocaleString('en-PH', {maximumFractionDigits: 2}) + '%');
        text('commissionDetailEarned', money(commission.amount));
        text('commissionDetailSellerNet', money(commission.seller_after_commission));

        const history = byId('shippingTrackingHistoryList');
        const scans = Array.isArray(shipment.scans) ? shipment.scans : [];
        if (history) {
            history.innerHTML = scans.length
                ? scans.map(scan => `<div class="shipping-history-item">
                    <div class="shipping-history-icon active" aria-hidden="true">✓</div>
                    <div class="shipping-history-time"><span class="date">${escapeHtml(date(scan.scanned_at))}</span><span class="time">${escapeHtml(time(scan.scanned_at))}</span></div>
                    <div class="shipping-history-content"><strong>${escapeHtml(statusLabel(scan.status))}</strong><p>${escapeHtml(scan.location || 'Parcel update')}</p></div>
                </div>`).join('')
                : '<p style="padding:10px 0;font-size:12px;color:#938581">No tracking scans have been recorded.</p>';
        }
    }

    async function openRow(row) {
        const id = Number(row.dataset.commissionId || 0);
        if (!id || !config.detailsUrl) return;
        const token = ++requestNumber;
        show();
        text('shippingModalOrderId', 'Loading order…');
        text('shippingModalStatusLabel', 'Loading');
        text('shippingModalCustomer', '—');
        text('commissionDetailEarned', '—');
        const list = byId('shippingModalItemsList');
        if (list) list.textContent = 'Loading order details…';
        try {
            const response = await fetch(config.detailsUrl.replace(/\/$/, '') + '/' + encodeURIComponent(String(id)), {
                credentials: 'same-origin', headers: { Accept: 'application/json' }
            });
            if (!response.ok) throw new Error(response.status === 404 ? 'Order information was not found.' : 'Unable to load order details.');
            const data = await response.json();
            if (token !== requestNumber) return;
            render(data);
        } catch (error) {
            if (token === requestNumber && list) list.textContent = error.message || 'Unable to load order details.';
        }
    }

    table.addEventListener('click', event => {
        const row = event.target.closest('tr[data-commission-id]');
        if (row) openRow(row);
    });
    table.addEventListener('keydown', event => {
        if (event.key !== 'Enter' && event.key !== ' ') return;
        const row = event.target.closest('tr[data-commission-id]');
        if (row) { event.preventDefault(); openRow(row); }
    });
    closeButton?.addEventListener('click', close);
    modal.addEventListener('click', event => { if (event.target === modal) close(); });
    document.addEventListener('keydown', event => {
        if (!modal.classList.contains('modal-open')) return;
        if (event.key === 'Escape') close();
        if (event.key === 'Tab' && panel) {
            // Keep keyboard focus inside the open dialog.
            const nodes = [...panel.querySelectorAll('button:not([disabled]),a[href],input:not([disabled])')];
            if (!nodes.length) return;
            const first = nodes[0], last = nodes[nodes.length - 1];
            if (event.shiftKey && document.activeElement === first) {event.preventDefault();last.focus();}
            else if (!event.shiftKey && document.activeElement === last) {event.preventDefault();first.focus();}
        }
    });
});
