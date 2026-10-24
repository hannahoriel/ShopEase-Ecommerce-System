<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Rider Deliveries — ShopEase</title>
    <style>
        :root{font-family:Arial,sans-serif;color:#211b19;background:#fbf8f6}body{margin:0}header{background:#7b1b1b;color:#fff;padding:18px 5vw;display:flex;justify-content:space-between}header a{color:#fff}main{max-width:960px;margin:30px auto;padding:0 18px}.muted{color:#716966;font-size:14px;line-height:1.5}.card{background:#fff;border:1px solid #eadfdb;border-radius:12px;padding:18px;margin:14px 0}.card-top{display:flex;justify-content:space-between;gap:12px}.card h2{font-size:18px;margin:0 0 8px}.details{white-space:pre-line;line-height:1.6;margin:12px 0}.tag{background:#f4e7e3;border-radius:99px;padding:5px 9px;font-size:12px;text-transform:capitalize;white-space:nowrap}button{padding:10px 14px;border:0;border-radius:7px;background:#7b1b1b;color:white;cursor:pointer}button:disabled{opacity:.55}.error{background:#fff0ef;color:#8b2424;padding:12px;border-radius:8px}.success{background:#eef8ef;color:#28632d;padding:12px;border-radius:8px}#message:empty{display:none}
    </style>
</head>
<body>
    <header><strong>ShopEase · Rider</strong><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Log out</button></form></header>
    <main>
        <h1>My deliveries</h1>
        <p class="muted">Delivery updates are shared with the buyer and seller as soon as you save them.</p>
        <div id="message" role="status"></div>
        <div id="deliveries"><p class="muted">Loading your assigned deliveries…</p></div>
    </main>
    <script>
        const apiToken = @json($apiToken);
        const deliveries = document.getElementById('deliveries');
        const message = document.getElementById('message');
        const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, character => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;',
        }[character]));
        async function loadDeliveries() {
            deliveries.innerHTML = '<p class="muted">Loading your assigned deliveries…</p>';
            try {
                const response = await fetch('/api/v1/rider/deliveries', {
                    headers: { Accept: 'application/json', Authorization: `Bearer ${apiToken}` },
                });
                const payload = await response.json();
                if (!response.ok) throw new Error(payload.message || `Unable to load deliveries (${response.status}).`);
                const orders = payload.data || [];
                if (!orders.length) {
                    deliveries.innerHTML = '<p class="muted">No deliveries have been assigned to you.</p>';
                    return;
                }
                deliveries.innerHTML = orders.map(order => {
                    const nextStatus = order.status === 'in_transit' ? 'out_for_delivery' : null;
                    const requiresProof = order.status === 'to_ship' || order.status === 'out_for_delivery';
                    const action = nextStatus
                        ? `<button type="button" data-order="${order.id}" data-status="${nextStatus}">Mark ${escapeHtml(nextStatus.replaceAll('_', ' '))}</button>`
                        : order.status === 'delivered'
                            ? '<p class="muted">Delivery completed.</p>'
                            : requiresProof
                                ? '<p class="muted">Use the rider mobile app to submit the required photo proof before advancing this delivery.</p>'
                                : '<p class="muted">No web status update is available for this delivery.</p>';
                    return `<article class="card"><div class="card-top"><div><h2>${escapeHtml(order.order_number)}</h2><span class="muted">${escapeHtml(order.seller?.store_name || 'ShopEase seller')}</span></div><span class="tag">${escapeHtml(order.status.replaceAll('_', ' '))}</span></div><p class="details"><strong>Deliver to:</strong> ${escapeHtml(order.delivery_name)} · ${escapeHtml(order.delivery_phone)}\n${escapeHtml(order.delivery_address)}\n<strong>Items:</strong> ${order.items.map(item => `${escapeHtml(item.product_name)} × ${Number(item.quantity)}`).join(', ')}\n<strong>Tracking:</strong> ${escapeHtml(order.shipment?.tracking_number || 'Not available')}</p>${action}</article>`;
                }).join('');
                deliveries.querySelectorAll('[data-order]').forEach(button => button.addEventListener('click', async () => {
                    button.disabled = true;
                    message.className = '';
                    message.textContent = '';
                    try {
                        const update = await fetch(`/api/v1/rider/deliveries/${button.dataset.order}/status`, {
                            method: 'PATCH',
                            headers: {
                                Accept: 'application/json',
                                'Content-Type': 'application/json',
                                Authorization: `Bearer ${apiToken}`,
                            },
                            body: JSON.stringify({ status: button.dataset.status }),
                        });
                        const payload = await update.json();
                        if (!update.ok) throw new Error(payload.message || `Update failed (${update.status}).`);
                        message.className = 'success';
                        message.textContent = `Delivery updated to ${button.dataset.status.replaceAll('_', ' ')}.`;
                        await loadDeliveries();
                    } catch (error) {
                        message.className = 'error';
                        message.textContent = error.message || 'Unable to update delivery.';
                        button.disabled = false;
                    }
                }));
            } catch (error) {
                deliveries.innerHTML = `<p class="error">${escapeHtml(error.message || 'Unable to load deliveries.')}</p>`;
            }
        }
        loadDeliveries();
    </script>
</body>
</html>
