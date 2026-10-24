<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Deliveries & Riders — ShopEase</title>
    <style>
        :root{font-family:Arial,sans-serif;color:#211b19;background:#fbf8f6}body{margin:0}header{background:#7b1b1b;color:#fff;padding:18px 5vw;display:flex;justify-content:space-between;align-items:center}header a{color:white}main{max-width:1200px;margin:30px auto;padding:0 20px}.top{display:flex;justify-content:space-between;align-items:center;gap:12px}.top p{color:#746b67}.panels{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-top:22px}.panel{background:#fff;border:1px solid #eee2dd;border-radius:12px;padding:20px;min-width:0}.panel h2{font-size:18px;margin:0 0 14px}.row{border-top:1px solid #eee;padding:14px 0;display:flex;justify-content:space-between;gap:14px;align-items:center}.row:first-of-type{border-top:0}.muted{color:#756d69;font-size:13px;line-height:1.5}.controls{display:flex;gap:8px;align-items:center;flex-wrap:wrap}select,button{padding:9px 10px;border:1px solid #d7c9c4;border-radius:7px;background:#fff;font:inherit}button{background:#7b1b1b;color:white;border-color:#7b1b1b;cursor:pointer}button:disabled{opacity:.5;cursor:not-allowed}.notice{padding:12px;border-radius:8px;background:#f4eeeb;color:#5e5551}.error{background:#fff0ef;color:#8b2424}.success{background:#eef8ef;color:#28632d}#notice{margin-top:16px}.tag{display:inline-block;background:#f5e9e6;padding:4px 8px;border-radius:99px;font-size:12px}@media(max-width:800px){.panels{grid-template-columns:1fr}.top{align-items:flex-start;flex-direction:column}.row{align-items:flex-start;flex-direction:column}}
    </style>
</head>
<body>
    <header>
        <strong>ShopEase · Logistics Operations</strong>
        <nav><a href="{{ route('logistics.dashboard') }}">Dashboard</a> &nbsp;·&nbsp;
            <form method="POST" action="{{ route('logout') }}" style="display:inline">@csrf<button type="submit">Log out</button></form>
        </nav>
    </header>
    <main>
        <div class="top">
            <div><h1>Deliveries & riders</h1><p>Assign approved riders and track delivery progress shared with buyers and sellers.</p></div>
            <button id="refresh" type="button">Refresh</button>
        </div>
        <div id="notice" class="notice" role="status" hidden></div>
        <div class="panels">
            <section class="panel" aria-labelledby="available-title">
                <h2 id="available-title">Available for pickup</h2>
                <div id="available"><p class="muted">Loading available deliveries…</p></div>
            </section>
            <section class="panel" aria-labelledby="assigned-title">
                <h2 id="assigned-title">Assigned deliveries</h2>
                <div id="assigned"><p class="muted">Loading assigned deliveries…</p></div>
            </section>
            <section class="panel" aria-labelledby="riders-title">
                <h2 id="riders-title">Rider roster</h2>
                <div id="riders"><p class="muted">Loading riders…</p></div>
            </section>
            <section class="panel" aria-labelledby="branches-title">
                <h2 id="branches-title">Your branches</h2>
                <div id="branches"><p class="muted">Loading branches…</p></div>
            </section>
        </div>
    </main>
    <script>
        const apiToken = @json($apiToken);
        const apiRoot = '/api/v1/logistics';
        const notice = document.getElementById('notice');
        const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, character => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;',
        }[character]));
        async function request(path, options = {}) {
            const response = await fetch(`${apiRoot}${path}`, {
                ...options,
                headers: {
                    Accept: 'application/json',
                    Authorization: `Bearer ${apiToken}`,
                    ...(options.body ? { 'Content-Type': 'application/json' } : {}),
                    ...options.headers,
                },
            });
            const payload = await response.json();
            if (!response.ok) {
                const message = payload.message || Object.values(payload.errors || {}).flat().join(' ') || `Request failed (${response.status}).`;
                throw new Error(message);
            }
            return payload;
        }
        function showNotice(message, isError = false) {
            notice.textContent = message;
            notice.className = `notice ${isError ? 'error' : 'success'}`;
            notice.hidden = false;
        }
        async function load() {
            notice.hidden = true;
            for (const id of ['available', 'assigned', 'riders', 'branches']) {
                document.getElementById(id).innerHTML = '<p class="muted">Loading…</p>';
            }
            try {
                const [availablePayload, assignedPayload, riderPayload, branchPayload] = await Promise.all([
                    request('/available-deliveries'),
                    request('/deliveries'),
                    request('/riders'),
                    request('/branches'),
                ]);
                const available = availablePayload.data || [];
                const assigned = assignedPayload.data || [];
                const riders = riderPayload.data || [];
                const branches = branchPayload.data || [];
                const availableRiders = riders.filter(rider => rider.logistics_branch_id);
                document.getElementById('available').innerHTML = available.length
                    ? available.map(order => `<article class="row"><div><strong>${escapeHtml(order.order_number)}</strong><div class="muted">${escapeHtml(order.seller?.store_name || 'Seller')} · ${escapeHtml(order.shipment?.tracking_number || '')}</div></div><div class="controls"><select aria-label="Select rider for ${escapeHtml(order.order_number)}" id="rider-${order.id}"><option value="">Select rider</option>${availableRiders.map(rider => `<option value="${rider.id}">${escapeHtml(rider.name)} · ${escapeHtml(rider.branch)}</option>`).join('')}</select><button type="button" data-assign="${order.id}" ${availableRiders.length ? '' : 'disabled'}>Assign</button></div></article>`).join('')
                    : '<p class="muted">No deliveries are currently waiting for pickup.</p>';
                document.getElementById('assigned').innerHTML = assigned.length
                    ? assigned.map(order => `<article class="row"><div><strong>${escapeHtml(order.order_number)}</strong><div class="muted">${escapeHtml(order.buyer?.name || order.delivery_name || 'Buyer')} · ${escapeHtml(order.shipment?.rider?.user?.name || 'Rider')}</div></div><span class="tag">${escapeHtml(order.status.replaceAll('_', ' '))}</span></article>`).join('')
                    : '<p class="muted">No deliveries have been assigned to your branches.</p>';
                document.getElementById('riders').innerHTML = riders.length
                    ? riders.map(rider => `<article class="row"><div><strong>${escapeHtml(rider.name)}</strong><div class="muted">${escapeHtml(rider.vehicle)} · ${escapeHtml(rider.plate_number)} · ${escapeHtml(rider.contact_no)}</div></div><div class="controls"><select aria-label="Branch for ${escapeHtml(rider.name)}" id="branch-${rider.id}"><option value="">Unassigned</option>${branches.map(branch => `<option value="${branch.id}" ${Number(rider.logistics_branch_id) === Number(branch.id) ? 'selected' : ''}>${escapeHtml(branch.name)}</option>`).join('')}</select><button type="button" data-branch="${rider.id}">Save</button></div></article>`).join('')
                    : '<p class="muted">No approved riders are available to add.</p>';
                document.getElementById('branches').innerHTML = branches.length
                    ? branches.map(branch => `<article class="row"><div><strong>${escapeHtml(branch.name)}</strong><div class="muted">${escapeHtml(branch.address)}</div></div></article>`).join('')
                    : '<p class="muted">Ask an administrator to create a logistics branch before assigning riders.</p>';
                document.querySelectorAll('[data-assign]').forEach(button => button.addEventListener('click', async () => {
                    const riderId = document.getElementById(`rider-${button.dataset.assign}`).value;
                    if (!riderId) return showNotice('Select a rider who belongs to one of your branches.', true);
                    button.disabled = true;
                    try {
                        await request(`/deliveries/${button.dataset.assign}/assign`, {
                            method: 'POST', body: JSON.stringify({ rider_id: Number(riderId) }),
                        });
                        showNotice('Delivery assigned successfully.');
                        await load();
                    } catch (error) {
                        showNotice(error.message, true);
                        button.disabled = false;
                    }
                }));
                document.querySelectorAll('[data-branch]').forEach(button => button.addEventListener('click', async () => {
                    const branchId = document.getElementById(`branch-${button.dataset.branch}`).value;
                    button.disabled = true;
                    try {
                        await request(`/riders/${button.dataset.branch}/branch`, {
                            method: 'PATCH',
                            body: JSON.stringify({ logistics_branch_id: branchId ? Number(branchId) : null }),
                        });
                        showNotice('Rider branch updated.');
                        await load();
                    } catch (error) {
                        showNotice(error.message, true);
                        button.disabled = false;
                    }
                }));
            } catch (error) {
                showNotice(error.message || 'Unable to load logistics data.', true);
                for (const id of ['available', 'assigned', 'riders', 'branches']) {
                    document.getElementById(id).innerHTML = '<p class="muted">Data could not be loaded. Refresh and try again.</p>';
                }
            }
        }
        document.getElementById('refresh').addEventListener('click', load);
        load();
    </script>
</body>
</html>
