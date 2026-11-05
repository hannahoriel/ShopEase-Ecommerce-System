@php
    // Logistics Reports prototype. Demonstration data only; no orders or finance rows are inserted.
    // Each sample parcel has one parent sorting center and one child delivery hub.
    // Revenue is based on a flat ₱50 shipping fee per parcel; no returns or registrations.
    $demoCenters = [
        ['name' => 'Laguna Sorting Center', 'hubs' => ['Santa Cruz Delivery Hub', 'Cavite Delivery Hub']],
        ['name' => 'Cebu Sorting Center', 'hubs' => ['Cebu Operations Hub', 'Mandaue Delivery Hub']],
        ['name' => 'Batangas Sorting Center', 'hubs' => ['Lipa Delivery Hub', 'Tanauan Delivery Hub']],
    ];
    $demoParcels = [];
    $nowManila = \Carbon\Carbon::now('Asia/Manila');
    $startDemo = $nowManila->copy()->subDays(90)->startOfDay();
    $idSequence = 0;
    for ($date = $startDemo->copy(); $date->lte($nowManila); $date->addDay()) {
        $parcelCount = 8 + (($date->day * 3 + $date->month * 5) % 10);
        for ($index = 0; $index < $parcelCount; $index++) {
            $centerIndex = ($idSequence + $date->day) % count($demoCenters);
            $center = $demoCenters[$centerIndex];
            $hub = $center['hubs'][($idSequence + $index) % count($center['hubs'])];
            $stateKey = ($idSequence * 7 + $date->day) % 20;
            $status = $stateKey < 14 ? 'delivered' : ($stateKey < 17 ? 'in_transit' : ($stateKey < 19 ? 'at_hub' : 'exception'));
            $exception = $status === 'exception' ? (($idSequence % 2) ? 'damaged' : 'lost') : null;
            $durationHours = 14 + (($idSequence * 11) % 74);
            $targetHours = 48;
            $demoParcels[] = [
                'date' => $date->format('Y-m-d'),
                'ref' => 'SE-' . (200000 + $idSequence),
                'center' => $center['name'],
                'hub' => $hub,
                'status' => $status,
                'issue' => $exception,
                'hours' => $durationHours,
                'on_time' => $status === 'delivered' && $durationHours <= $targetHours,
                'fee' => 50,
            ];
            $idSequence++;
        }
    }
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Logistics Reports — ShopEase</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/logistics/reports.css', 'resources/js/logistics/reports.js'])
</head>
<body class="lg-body">
    <x-logistics.sidebar />
    <x-logistics.navbar />
    <main class="lg-reports-main ar-page">
        <div class="ar-shell" id="logistics-reports">
            <section class="ar-period" aria-label="Reports date filters">
                <div class="ar-period-text">
                    <span class="ar-period-icon" aria-hidden="true">
                        <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 3v4M17 3v4M3 10h18"/></svg>
                    </span>
                    <div><strong>Reporting period</strong><small>Select a date range to update all report sections</small></div>
                </div>
                <div class="ar-dates">
                    <label for="lr-from">From <input type="date" id="lr-from" max="{{ $nowManila->toDateString() }}"></label>
                    <span class="ar-date-dash">—</span>
                    <label for="lr-to">To <input type="date" id="lr-to" max="{{ $nowManila->toDateString() }}"></label>
                </div>
                <div class="ar-period-actions">
                    <button type="button" class="ar-reset-icon" id="lr-reset" aria-label="Reset report filters to current month" title="Reset to current month">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 7v5h-5"/><path d="M4 17v-5h5"/><path d="M5.8 9A7.5 7.5 0 0 1 19.5 7.5L20 12M4 12l.5 4.5A7.5 7.5 0 0 0 18.2 15"/></svg>
                    </button>
                </div>
            </section>
            <p class="ar-date-error" id="lr-error" role="alert"></p>

            <div class="ar-section-caption">NETWORK PERFORMANCE <span id="lr-period-label">Current month</span></div>
            <div class="ar-kpis" aria-live="polite">
                <article class="ar-kpi"><div class="ar-kpi-head"><span>Parcels processed</span><span class="ar-kpi-icon">▣</span></div><strong id="lr-parcels">—</strong><small>Unique parcels recorded across the network</small></article>
                <article class="ar-kpi"><div class="ar-kpi-head"><span>Delivered parcels</span><span class="ar-kpi-icon">✓</span></div><strong id="lr-delivered">—</strong><small>Parcels marked delivered in the selected period</small></article>
                <article class="ar-kpi"><div class="ar-kpi-head"><span>On-time delivery rate</span><span class="ar-kpi-icon">◷</span></div><strong id="lr-on-time">—</strong><small>Delivered within the 48-hour example target</small></article>
                <article class="ar-kpi"><div class="ar-kpi-head"><span>Company shipping fee income</span><span class="ar-kpi-icon">₱</span></div><strong id="lr-shipping">—</strong><small>Delivered parcels × ₱50 (company income preview)</small></article>
            </div>

            <div class="ar-two ar-main-charts">
                <section class="ar-card ar-chart-card ar-chart-fill">
                    <div class="ar-card-head"><div><h2>Parcel volume trend</h2><p>Parcel records by date within your selected period</p></div><span class="ar-muted-pill">Parcels</span></div>
                    <div class="ar-chart" id="lr-volume-chart" aria-label="Daily parcel volume chart"><p class="ar-chart-empty">Preparing chart…</p></div>
                </section>
                <section class="ar-card ar-cohort">
                    <div class="ar-card-head"><div><h2>Parcel movement</h2><p>Current simulated parcel stages from the selected period</p></div><span class="ar-muted-pill" id="lr-status-total">— records</span></div>
                    <div id="lr-status-bars" class="ar-status-grid"></div>
                    <p class="ar-note">Each parcel belongs to one status. Counts are not added again under sorting centers or delivery hubs.</p>
                </section>
            </div>

            <div class="ar-two">
                <section class="ar-card ar-table-card">
                    <div class="ar-card-head"><div><h2>Sorting center throughput</h2><p>Parcel volume assigned to each sorting center</p></div><span class="ar-muted-pill">Network facilities</span></div>
                    <div class="ar-table-scroll"><table class="ar-table"><thead><tr><th>Sorting center</th><th>Parcels</th><th>Delivered</th><th class="ar-num">Share of volume</th></tr></thead><tbody id="lr-center-rows"></tbody></table></div>
                </section>
                <section class="ar-card">
                    <div class="ar-card-head"><div><h2>Delivery hub breakdown</h2><p>Hubs remain grouped under their parent sorting center</p></div><span class="ar-muted-pill">Parent → Child</span></div>
                    <div class="ar-hub-tree ar-hub-scroll" id="lr-hub-tree"></div>
                </section>
            </div>

            <div class="ar-three">
                <section class="ar-card ar-compact">
                    <div class="ar-card-head"><div><h2>Delivery timeliness</h2><p>Time taken for successfully delivered parcels</p></div><span class="ar-muted-pill">48h example target</span></div>
                    <div class="ar-status-list" id="lr-timeliness"></div>
                    <p class="ar-card-foot">Illustrative SLA target, not a committed ShopEase delivery guarantee.</p>
                </section>
                <section class="ar-card ar-compact">
                    <div class="ar-card-head"><div><h2>Parcel Disputes</h2><p>Cases requiring network investigation</p></div><span class="ar-muted-pill" id="lr-issue-total">— cases</span></div>
                    <div class="ar-status-list" id="lr-issues"></div>
                    <p class="ar-card-foot">Lost and damaged parcel reports, separate from buyer returns and refunds.</p>
                </section>
                <section class="ar-card ar-compact">
                    <div class="ar-card-head"><div><h2>Transit efficiency</h2><p>Delivery duration among delivered parcels</p></div><span class="ar-muted-pill">Hours</span></div>
                    <div class="ar-delivery-focus"><span>Average delivery duration</span><strong id="lr-average-hours">—</strong></div>
                    <div class="ar-divider"></div>
                    <div class="ar-summary-pair"><span>Fastest delivery</span><strong id="lr-fastest">—</strong></div>
                    <div class="ar-summary-pair"><span>Longest delivery</span><strong id="lr-longest">—</strong></div>
                </section>
            </div>

            <div class="ar-two">
                <section class="ar-card ar-table-card">
                    <div class="ar-card-head"><div><h2>Shipping fee collections by route</h2><p>Company-owned fees attributed to sorting-center parcel routes, not center payouts</p></div><span class="ar-muted-pill">₱50 / parcel</span></div>
                    <div class="ar-table-scroll"><table class="ar-table"><thead><tr><th>Sorting center</th><th>Delivered parcels</th><th class="ar-num">Company fee income</th></tr></thead><tbody id="lr-revenue-rows"></tbody></table></div>
                </section>
                <section class="ar-card">
                    <div class="ar-card-head"><div><h2>Operational interpretation</h2><p>Use this section to prioritize network improvements</p></div><span class="ar-muted-pill">Period summary</span></div>
                    <div id="lr-insights" class="ar-status-list"></div>
                </section>
            </div>
            <p class="ar-report-footer">Sample data preview only. Sorting center and hub names, statuses, durations and figures here are illustrative and are not read from your ShopEase database. There is no registration reporting or return/refund accounting. All shipping-fee income is assigned to the logistics company; sorting centers and delivery hubs have no separate earnings wallets.</p>
        </div>
    </main>
    <script id="lr-demo-data" type="application/json">@json(['parcels' => $demoParcels, 'centers' => $demoCenters, 'today' => $nowManila->toDateString()])</script>
</body>
</html>
