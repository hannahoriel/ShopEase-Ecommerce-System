<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Courier Dashboard — ShopEase</title>
    <style>
        :root{font-family:Arial,sans-serif;color:#211b19;background:#fbf8f6}body{margin:0}header{background:#7b1b1b;color:#fff;padding:18px 5vw;display:flex;justify-content:space-between;align-items:center}header form{margin:0}main{max-width:1050px;margin:30px auto;padding:0 18px}.muted{color:#716966;font-size:14px;line-height:1.5}.grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px}.card{background:#fff;border:1px solid #eadfdb;border-radius:12px;padding:18px;margin:14px 0}.metric{font-size:28px;font-weight:700;color:#7b1b1b}.trip h2{font-size:18px;margin:0 0 7px}.trip-top{display:flex;justify-content:space-between;gap:12px}.tag{background:#f4e7e3;border-radius:99px;padding:5px 9px;font-size:12px;text-transform:capitalize;white-space:nowrap}.error{background:#fff0ef;color:#8b2424;padding:12px;border-radius:8px}button{padding:10px 14px;border:0;border-radius:7px;background:#fff;color:#7b1b1b;cursor:pointer}@media(max-width:700px){.grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
    </style>
</head>
<body>
    <header><strong>ShopEase · Courier</strong><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Log out</button></form></header>
    <main>
        <h1>Courier Dashboard</h1>
        <p class="muted">View your assigned linehaul trips and parcel manifests.</p>
        <div id="error"></div>
        <section class="grid" aria-label="Trip summary">
            <article class="card"><div class="metric" id="activeTrips">—</div><div class="muted">Active trips</div></article>
            <article class="card"><div class="metric" id="inTruck">—</div><div class="muted">Parcels in truck</div></article>
            <article class="card"><div class="metric" id="outbound">—</div><div class="muted">Outbound parcels</div></article>
            <article class="card"><div class="metric" id="returns">—</div><div class="muted">Return parcels</div></article>
        </section>
        <h2>My active trips</h2>
        <section id="trips"><p class="muted">Loading assigned trips…</p></section>
    </main>
    <script>
        const token = @json($apiToken);
        const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));
        const request = async path => {
            const response = await fetch(path, {headers:{Accept:'application/json',Authorization:`Bearer ${token}`}});
            const body = await response.json();
            if (!response.ok) throw new Error(body.message || `Request failed (${response.status})`);
            return body.data;
        };
        async function load() {
            try {
                const [dashboard, manifests] = await Promise.all([request('/api/v1/courier/dashboard'), request('/api/v1/courier/trips?scope=active')]);
                const summary = dashboard.summary || {};
                document.getElementById('activeTrips').textContent = summary.active_trips ?? 0;
                document.getElementById('inTruck').textContent = summary.in_truck ?? 0;
                document.getElementById('outbound').textContent = summary.outbound_parcels ?? 0;
                document.getElementById('returns').textContent = summary.return_parcels ?? 0;
                const rows = Array.isArray(manifests) ? manifests : [];
                document.getElementById('trips').innerHTML = rows.length ? rows.map(trip => `<article class="card trip"><div class="trip-top"><div><h2>${escapeHtml(trip.manifest_number || `Trip #${trip.id}`)}</h2><div class="muted">${escapeHtml(trip.origin?.name || trip.origin?.facility_name || 'Origin')} → ${escapeHtml(trip.destination?.name || trip.destination?.facility_name || 'Destination')}</div></div><span class="tag">${escapeHtml(trip.status || 'assigned')}</span></div><p class="muted">${Number(trip.expected_parcels ?? 0)} parcels · ${escapeHtml(trip.scheduled_at || 'Schedule pending')}</p></article>`).join('') : '<p class="muted">No active trips are assigned to you.</p>';
            } catch (error) {
                document.getElementById('error').innerHTML = `<p class="error">${escapeHtml(error.message)}</p>`;
                document.getElementById('trips').innerHTML = '';
            }
        }
        load();
    </script>
</body>
</html>
