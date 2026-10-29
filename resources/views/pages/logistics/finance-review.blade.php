<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Rider Finance Review — ShopEase Logistics</title>
    <style>
        :root{font-family:Arial,sans-serif;color:#211b19;background:#fbf8f6}body{margin:0}header{background:#7b1b1b;color:#fff;padding:18px 5vw;display:flex;justify-content:space-between;align-items:center}header a{color:#fff}main{max-width:1100px;margin:30px auto;padding:0 18px}h1{margin-bottom:6px}.muted{color:#716966;font-size:14px;line-height:1.5}.panel{background:#fff;border:1px solid #eadfdb;border-radius:12px;padding:18px;margin:18px 0;overflow:auto}.tabs{display:flex;gap:8px;margin-bottom:16px}button,select{padding:9px 12px;border:1px solid #ddd;border-radius:7px;background:#fff;color:#332b28}button{cursor:pointer}button.primary,.tab.active{background:#7b1b1b;border-color:#7b1b1b;color:#fff}button:disabled{opacity:.5;cursor:not-allowed}.error{background:#fff0ef;color:#8b2424;padding:12px;border-radius:8px}.success{background:#eef8ef;color:#28632d;padding:12px;border-radius:8px}#message:empty{display:none}table{width:100%;border-collapse:collapse;margin-top:14px;min-width:700px}th,td{text-align:left;border-bottom:1px solid #eee;padding:11px 8px;font-size:13px;vertical-align:top}th{font-size:11px;color:#716966;text-transform:uppercase}.actions{display:flex;gap:6px;flex-wrap:wrap}.tag{display:inline-block;background:#f4e7e3;border-radius:99px;padding:4px 8px;font-size:11px;text-transform:capitalize}
    </style>
</head>
<body>
    <header>
        <strong>ShopEase · Rider Finance Review</strong>
        <nav><a href="{{ route('logistics.dashboard') }}">Logistics dashboard</a></nav>
    </header>
    <main>
        <h1>Rider remittances</h1>
        <p class="muted">Review submitted COD remittances. Verified and rejected decisions are recorded for the assigned rider.</p>
        <div id="message" role="status"></div>
        <section class="panel">
            <div class="tabs">
                <button class="tab active" data-tab="remittance" type="button">COD remittances</button>
                <button class="tab" data-tab="ticket" type="button">Support tickets</button>
                <button class="tab" data-tab="action" type="button">Delivery requests</button>
            </div>
            <div id="remittancePanel">
                <label for="statusFilter">Status:</label>
                <select id="statusFilter">
                    <option value="">All</option>
                    <option value="pending">Pending</option>
                    <option value="verified">Verified</option>
                    <option value="rejected">Rejected</option>
                </select>
                <button class="primary" id="reloadRemittances" type="button">Refresh</button>
                <div id="remittances"><p class="muted">Loading rider remittances…</p></div>
            </div>
            <div id="ticketPanel" hidden>
                <button class="primary" id="reloadTickets" type="button">Refresh</button>
                <div id="tickets"><p class="muted">Loading rider tickets…</p></div>
            </div>
            <div id="actionPanel" hidden>
                <button class="primary" id="reloadActions" type="button">Refresh</button>
                <div id="actions"><p class="muted">Loading delivery requests…</p></div>
            </div>
        </section>
    </main>
    <script>
        const apiToken = @json($apiToken);
        const remittances = document.getElementById('remittances');
        const message = document.getElementById('message');
        const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, character => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;',
        }[character]));
        const requestHeaders = {
            Accept: 'application/json',
            Authorization: 'Bearer ' + apiToken,
        };

        async function loadRemittances() {
            remittances.innerHTML = '<p class="muted">Loading rider remittances…</p>';
            const status = document.getElementById('statusFilter').value;
            const query = status ? `?status=${encodeURIComponent(status)}` : '';
            try {
                const response = await fetch(`/api/v1/logistics/rider-remittances${query}`, {
                    headers: requestHeaders,
                });
                const payload = await response.json();
                if (!response.ok) throw new Error(payload.message || `Unable to load remittances (${response.status}).`);
                const rows = payload.data || [];
                if (!rows.length) {
                    remittances.innerHTML = '<p class="muted">No remittances found for this status.</p>';
                    return;
                }
                remittances.innerHTML = `<table><thead><tr><th>Rider</th><th>Amount</th><th>Method / reference</th><th>Submitted</th><th>Status</th><th>Review</th></tr></thead><tbody>${rows.map(row => {
                    const receipt = row.receipt_url
                        ? `<a href="${escapeHtml(row.receipt_url)}" target="_blank" rel="noopener">View receipt</a>`
                        : 'No receipt attached';
                    const review = row.status === 'pending'
                        ? `<div class="actions"><button class="primary" data-id="${escapeHtml(row.id)}" data-status="verified">Verify</button><button data-id="${escapeHtml(row.id)}" data-status="rejected">Reject</button></div>`
                        : escapeHtml(row.review_notes || 'Reviewed');
                    return `<tr><td>${escapeHtml(row.rider_name || 'Rider')}</td><td>₱${escapeHtml(row.amount)}</td><td>${escapeHtml(row.method)}<br>${escapeHtml(row.reference_number || '')}<br>${receipt}</td><td>${escapeHtml(row.created_at || '')}</td><td><span class="tag">${escapeHtml(row.status)}</span></td><td>${review}</td></tr>`;
                }).join('')}</tbody></table>`;
                remittances.querySelectorAll('[data-id]').forEach(button => button.addEventListener('click', () => reviewRemittance(button)));
            } catch (error) {
                remittances.innerHTML = `<p class="error">${escapeHtml(error.message || 'Unable to load rider remittances.')}</p>`;
            }
        }

        async function reviewRemittance(button) {
            button.disabled = true;
            message.className = '';
            message.textContent = '';
            const reviewNotes = window.prompt('Optional review notes:') || '';
            try {
                const response = await fetch(`/api/v1/logistics/rider-remittances/${encodeURIComponent(button.dataset.id)}/review`, {
                    method: 'PATCH',
                    headers: { ...requestHeaders, 'Content-Type': 'application/json' },
                    body: JSON.stringify({ status: button.dataset.status, review_notes: reviewNotes }),
                });
                const payload = await response.json();
                if (!response.ok) throw new Error(payload.message || `Review failed (${response.status}).`);
                message.className = 'success';
                message.textContent = `Remittance ${button.dataset.status}.`;
                await loadRemittances();
            } catch (error) {
                message.className = 'error';
                message.textContent = error.message || 'Unable to review remittance.';
                button.disabled = false;
            }
        }

        async function loadTickets() {
            const target = document.getElementById('tickets');
            target.innerHTML = '<p class="muted">Loading rider tickets…</p>';
            try {
                const response = await fetch('/api/v1/logistics/rider-support-tickets', { headers: requestHeaders });
                const payload = await response.json();
                if (!response.ok) throw new Error(payload.message || `Unable to load tickets (${response.status}).`);
                const rows = payload.data || [];
                target.innerHTML = rows.length
                    ? `<table><thead><tr><th>Rider / order</th><th>Ticket / conversation</th><th>Priority</th><th>Status</th><th>Review</th></tr></thead><tbody>${rows.map(row => {
                        const messages = (row.messages || []).map(item => `<p><strong>${escapeHtml(item.sender_name || 'User')}:</strong> ${escapeHtml(item.body)}</p>`).join('');
                        return `<tr><td>${escapeHtml(row.rider_name)}<br>${escapeHtml(row.order_number || '')}</td><td><strong>${escapeHtml(row.subject)}</strong><br>${escapeHtml(row.description)}<div>${messages}</div><textarea data-ticket-reply="${escapeHtml(row.id)}" maxlength="2000" placeholder="Reply to rider"></textarea><button data-reply-ticket-id="${escapeHtml(row.id)}">Send reply</button></td><td>${escapeHtml(row.priority)}</td><td><span class="tag">${escapeHtml(row.status)}</span></td><td><select data-ticket-status="${escapeHtml(row.id)}"><option value="in_progress">In progress</option><option value="resolved">Resolved</option><option value="closed">Closed</option></select><button data-ticket-id="${escapeHtml(row.id)}">Save</button></td></tr>`;
                    }).join('')}</tbody></table>`
                    : '<p class="muted">No rider support tickets found.</p>';
                target.querySelectorAll('[data-ticket-id]').forEach(button => button.addEventListener('click', () => reviewTicket(button)));
                target.querySelectorAll('[data-reply-ticket-id]').forEach(button => button.addEventListener('click', () => replyTicket(button)));
            } catch (error) {
                target.innerHTML = `<p class="error">${escapeHtml(error.message || 'Unable to load tickets.')}</p>`;
            }
        }

        async function replyTicket(button) {
            const id = button.dataset.replyTicketId;
            const input = document.querySelector(`[data-ticket-reply="${CSS.escape(id)}"]`);
            const body = input.value.trim();
            if (!body) return;
            button.disabled = true;
            try {
                const response = await fetch(`/api/v1/logistics/rider-support-tickets/${encodeURIComponent(id)}/messages`, {
                    method: 'POST',
                    headers: { ...requestHeaders, 'Content-Type': 'application/json' },
                    body: JSON.stringify({ body }),
                });
                const payload = await response.json();
                if (!response.ok) throw new Error(payload.message || `Reply failed (${response.status}).`);
                message.className = 'success';
                message.textContent = 'Reply sent to rider.';
                await loadTickets();
            } catch (error) {
                message.className = 'error';
                message.textContent = error.message || 'Unable to send reply.';
                button.disabled = false;
            }
        }

        async function reviewTicket(button) {
            const id = button.dataset.ticketId;
            const status = document.querySelector(`[data-ticket-status="${CSS.escape(id)}"]`).value;
            const resolution = status === 'resolved' || status === 'closed'
                ? window.prompt('Required resolution note:')
                : window.prompt('Optional note:');
            if (resolution === null) return;
            button.disabled = true;
            try {
                const response = await fetch(`/api/v1/logistics/rider-support-tickets/${encodeURIComponent(id)}/review`, {
                    method: 'PATCH',
                    headers: { ...requestHeaders, 'Content-Type': 'application/json' },
                    body: JSON.stringify({ status, resolution }),
                });
                const payload = await response.json();
                if (!response.ok) throw new Error(payload.message || `Ticket review failed (${response.status}).`);
                message.className = 'success';
                message.textContent = 'Ticket status updated.';
                await loadTickets();
            } catch (error) {
                message.className = 'error';
                message.textContent = error.message || 'Unable to update ticket.';
                button.disabled = false;
            }
        }

        async function loadActions() {
            const target = document.getElementById('actions');
            target.innerHTML = '<p class="muted">Loading delivery requests…</p>';
            try {
                const response = await fetch('/api/v1/logistics/rider-delivery-actions?status=pending', { headers: requestHeaders });
                const payload = await response.json();
                if (!response.ok) throw new Error(payload.message || `Unable to load delivery requests (${response.status}).`);
                const rows = payload.data || [];
                target.innerHTML = rows.length
                    ? `<table><thead><tr><th>Rider / order</th><th>Request</th><th>Reason / notes</th><th>Schedule</th><th>Review</th></tr></thead><tbody>${rows.map(row => `<tr><td>${escapeHtml(row.rider_name)}<br>${escapeHtml(row.order_number || '')}</td><td>${escapeHtml(row.action)}</td><td>${escapeHtml(row.reason)}<br>${escapeHtml(row.notes || '')}</td><td>${escapeHtml(row.reschedule_date || '')} ${escapeHtml(row.reschedule_slot || '')}</td><td><div class="actions"><button class="primary" data-action-id="${escapeHtml(row.id)}" data-status="approved">Approve</button><button data-action-id="${escapeHtml(row.id)}" data-status="rejected">Reject</button></div></td></tr>`).join('')}</tbody></table>`
                    : '<p class="muted">No pending delivery requests.</p>';
                target.querySelectorAll('[data-action-id]').forEach(button => button.addEventListener('click', () => reviewAction(button)));
            } catch (error) {
                target.innerHTML = `<p class="error">${escapeHtml(error.message || 'Unable to load delivery requests.')}</p>`;
            }
        }

        async function reviewAction(button) {
            button.disabled = true;
            try {
                const response = await fetch(`/api/v1/logistics/rider-delivery-actions/${encodeURIComponent(button.dataset.actionId)}/review`, {
                    method: 'PATCH',
                    headers: { ...requestHeaders, 'Content-Type': 'application/json' },
                    body: JSON.stringify({ status: button.dataset.status }),
                });
                const payload = await response.json();
                if (!response.ok) throw new Error(payload.message || `Request review failed (${response.status}).`);
                message.className = 'success';
                message.textContent = `Delivery request ${button.dataset.status}.`;
                await loadActions();
            } catch (error) {
                message.className = 'error';
                message.textContent = error.message || 'Unable to review delivery request.';
                button.disabled = false;
            }
        }

        function showTab(tab) {
            document.querySelectorAll('.tab').forEach(button => button.classList.toggle('active', button.dataset.tab === tab));
            document.getElementById('remittancePanel').hidden = tab !== 'remittance';
            document.getElementById('ticketPanel').hidden = tab !== 'ticket';
            document.getElementById('actionPanel').hidden = tab !== 'action';
            if (tab === 'ticket') loadTickets();
            if (tab === 'action') loadActions();
        }

        document.querySelectorAll('.tab').forEach(button => button.addEventListener('click', () => showTab(button.dataset.tab)));
        document.getElementById('reloadRemittances').addEventListener('click', loadRemittances);
        document.getElementById('reloadTickets').addEventListener('click', loadTickets);
        document.getElementById('reloadActions').addEventListener('click', loadActions);
        document.getElementById('statusFilter').addEventListener('change', loadRemittances);
        loadRemittances();
    </script>
</body>
</html>
