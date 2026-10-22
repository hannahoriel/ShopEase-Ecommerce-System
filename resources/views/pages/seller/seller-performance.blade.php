{{-- resources/views/pages/seller/seller-performance.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ShopEase - Performance</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/seller/dashboard.css', 'resources/css/seller/seller-performance.css', 'resources/js/seller/seller-performance.js'])
</head>
<body>
    @section('page-title', 'Performance')
    {{-- Reuse the ACTUAL dashboard components so existing nav/sidebar hover/collapse remains identical. --}}
    <x-seller.sidebar />
    <x-seller.navbar />

    <div class="seller-dashboard-page seller-performance-page" id="sellerDashboardPage">
        <main class="seller-dashboard-content seller-performance-content" id="sellerDashboardContent">
            <section class="sp-top-grid" aria-label="Performance overview">
                <article class="sp-score-card">
                    <div class="sp-section-heading"><div><h2>Compliance Score</h2></div><span id="spComplianceStatus" class="sp-pill sp-pill-neutral">Not available</span></div>
                    <div class="sp-score-inner">
                        <div class="sp-gauge" id="spGauge" style="--score:0; --gauge-color:#7B1B1B" role="meter" aria-label="Compliance score" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                            <div class="sp-gauge-center"><strong id="spScore">—</strong><span>out of 100</span></div>
                        </div>
                        <div class="sp-score-copy"><strong id="spScoreHeading">Your account standing</strong><p id="spPolicyMessage">Your latest compliance details will appear here when available.</p><span class="sp-meta">Based on your shop's platform compliance records</span></div>
                    </div>
                </article>
                <div class="sp-metrics">
                    <article class="sp-metric sp-metric-link" data-navigate="policy" role="link" tabindex="0" aria-label="Go to policy records"><div class="sp-metric-icon"><img class="sp-metric-png" src="{{ asset('icons/admin/seller-compliance/warnings.png') }}" alt="Warnings issued"></div><div><strong id="spWarnings">—</strong><span>Warnings Issued</span></div></article>
                    <article class="sp-metric sp-metric-link" data-navigate="archived" role="link" tabindex="0" aria-label="Go to archived records"><div class="sp-metric-icon"><img class="sp-metric-png" src="{{ asset('icons/admin/registrations/Rejected Registrations.png') }}" alt="Products removed"></div><div><strong id="spRemoved">—</strong><span>Products Removed</span></div></article>
                    <article class="sp-metric sp-metric-link" data-navigate="active" role="link" tabindex="0" aria-label="Go to active records"><div class="sp-metric-icon"><img class="sp-metric-png" src="{{ asset('icons/admin/complaints-disputes/open.png') }}" alt="Active complaints"></div><div><strong id="spActiveCases">—</strong><span>Active Complaints</span></div></article>
                    <article class="sp-metric sp-metric-link" data-navigate="resolved" role="link" tabindex="0" aria-label="Go to resolved records"><div class="sp-metric-icon"><img class="sp-metric-png" src="{{ asset('icons/admin/complaints-disputes/resolved.png') }}" alt="Resolved complaints"></div><div><strong id="spResolved">—</strong><span>Resolved Complaints</span></div></article>
                </div>
            </section>

            <section class="sp-panel sp-notices">
                <div class="sp-section-heading"><div><h2>Compliance Notices</h2><p>Warnings and product removals recorded by the platform</p></div><span id="spNoticeCount" class="sp-count">0 records</span></div>
                <div class="sp-notice-list" id="spNoticeList"><div class="sp-empty">Loading compliance notices…</div></div>
            </section>


            <section class="sp-complaints" aria-label="Complaints and disputes">
              <div class="sp-section-heading sp-complaint-heading"><div><h2>Complaints &amp; Disputes</h2><p>Complaints filed involving your shop</p></div></div>
              <div class="complaints-filter-card"><div class="complaints-filter-row">
                <div class="complaints-search-wrap"><input id="complaints-search" class="complaints-search" type="search" placeholder="Search ID, name" aria-label="Search complaints"><svg class="complaints-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m16 16 5 5"/></svg></div>
                <div class="complaints-date-wrap"><input id="complaints-date-filter" class="complaints-date-input" type="date" aria-label="Complaint date"><button id="complaints-date-button" type="button" class="complaints-date-button" aria-label="Open calendar"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 2v4m8-4v4M3 10h18M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Z"/></svg></button></div>
                <button id="complaints-reload" type="button" class="complaints-reload" title="Reset filters" aria-label="Reset filters"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 11a8.1 8.1 0 0 0-15.5-2M4 5v4h4M4 13a8.1 8.1 0 0 0 15.5 2M20 19v-4h-4"/></svg></button>
              </div></div>
              <div id="complaints-workspace" class="complaints-workspace">
                <section class="complaints-table-card"><div class="complaints-tabs" role="tablist" aria-label="Complaint status">
                    <button class="complaints-tab active" type="button" data-tab="all" role="tab" aria-selected="true">All Complaints</button>
                    <button class="complaints-tab" type="button" data-tab="open" role="tab" aria-selected="false">Open</button>
                    <button class="complaints-tab" type="button" data-tab="in-progress" role="tab" aria-selected="false">In progress</button>
                    <button class="complaints-tab" type="button" data-tab="resolved" role="tab" aria-selected="false">Resolved</button>
                </div><div class="complaints-table-scroll"><table class="complaints-table"><thead><tr><th>Complaint ID</th><th>Parties</th><th>Type</th><th>Status</th><th>Date Filed</th></tr></thead><tbody id="complaints-table-body"></tbody></table><div id="complaints-empty" class="complaints-empty">No complaints match the selected filters.</div></div>
                <div class="complaints-table-footer"><p id="complaints-count" class="complaints-count">Showing 0 out of 0 entries</p><div id="complaints-pagination" class="complaints-pagination"><button type="button" id="complaints-prev" class="complaints-page-button" aria-label="Previous page">‹</button><div id="complaints-page-numbers" class="complaints-page-numbers"></div><button type="button" id="complaints-next" class="complaints-page-button" aria-label="Next page">›</button><select id="complaints-items" class="complaints-items" aria-label="Items per page"><option value="7">Items per page: 7</option><option value="10">Items per page: 10</option><option value="20">Items per page: 20</option></select></div></div></section>
                <aside id="complaints-detail-card" class="complaints-detail-card" aria-hidden="true">
                    <div class="complaints-detail-header"><div><div class="complaints-detail-eyebrow">Complaint ID</div><div id="detail-id" class="complaints-detail-id">—</div></div><div class="complaints-detail-header-right"><span id="detail-status" class="complaint-status open">Open</span><button id="complaints-detail-close" class="complaints-detail-close" aria-label="Close preview" type="button">×</button></div></div>
                    <div class="complaints-detail-body"><div class="complaints-detail-section"><h4 class="complaints-detail-label">Date Filed</h4><p id="detail-date" class="complaints-detail-text">—</p></div><div class="complaints-detail-section"><h4 class="complaints-detail-label">Complaint Type</h4><p id="detail-type" class="complaints-detail-text">—</p></div><div class="complaints-detail-section"><h4 class="complaints-detail-label">Description</h4><p id="detail-description" class="complaints-detail-text">—</p></div><div class="complaints-detail-section"><h4 class="complaints-detail-label">Parties Involved</h4><div id="detail-parties" class="complaints-parties-list"></div></div><div class="complaints-detail-section"><h4 class="complaints-detail-label">Supporting Evidence</h4><div id="detail-evidence" class="complaints-evidence"></div></div></div>
                    <div class="complaints-detail-actions"><button id="complaints-view-details" type="button" class="complaints-view-details">View Details</button></div>
                </aside>
              </div>
            </section><p id="spError" class="sp-error" role="alert" hidden></p>
        </main>
    </div>
    <div id="complaints-details-modal" class="sp-admin-modal-overlay" aria-hidden="true" hidden>
      <div class="complaints-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="complaints-modal-title" tabindex="-1">
        <div class="sp-modal-title-row"><h3 id="complaints-modal-title" class="complaints-modal-title">Complaint Details</h3><button id="complaints-modal-close" type="button" aria-label="Close complaint details" class="sp-modal-x">×</button></div>
        <div class="complaints-modal-grid complaints-modal-top-grid"><section class="complaints-modal-card complaints-modal-summary"><div class="complaints-modal-summary-main"><div class="complaints-modal-id-block"><span class="complaints-modal-label">Complaint ID</span><strong id="modal-complaint-id" class="complaints-modal-id">—</strong></div><span id="modal-complaint-status" class="complaints-modal-status open">—</span></div><div class="complaints-modal-meta"><div class="complaints-modal-meta-item"><span>Date Filed</span><strong id="modal-date-filed">—</strong></div><div class="complaints-modal-meta-item"><span>Last Updated</span><strong id="modal-last-updated">—</strong></div></div></section><section class="complaints-modal-card complaints-modal-type-order"><div><span class="complaints-modal-label">Complaint Type</span><div class="complaints-modal-type-value"><span id="modal-complaint-type">—</span></div></div><div><span class="complaints-modal-label">Order ID</span><strong id="modal-order-id-top" class="complaints-modal-order-id">—</strong></div></section></div>
        <div class="complaints-modal-grid"><section class="complaints-modal-card complaints-modal-description-card"><h4 class="complaints-modal-card-title">Description</h4><p id="modal-description-copy" class="complaints-modal-description">—</p></section><section class="complaints-modal-card complaints-modal-parties-card"><h4 class="complaints-modal-card-title">Parties Involved</h4><div id="modal-parties-list" class="complaints-modal-party-list"></div></section></div>
        <div class="complaints-modal-grid complaints-modal-middle-grid"><section class="complaints-modal-card complaints-modal-order-card"><h4 class="complaints-modal-card-title">Order Information</h4><div id="modal-order-information" class="complaints-modal-order-list"></div></section><section class="complaints-modal-card complaints-modal-item-card"><h4 class="complaints-modal-card-title">Ordered Item</h4><div id="modal-product-info"></div></section></div>
        <div class="complaints-modal-grid complaints-modal-bottom-grid"><section class="complaints-modal-card complaints-modal-evidence-card"><h4 class="complaints-modal-card-title">Supporting Evidence</h4><div id="modal-evidence-list" class="complaints-modal-evidence-list"></div></section><section class="complaints-modal-card sp-contact-admin-card" aria-labelledby="sp-contact-admin-heading">
                    <div class="sp-contact-admin-copy">
                        <h4 class="complaints-modal-card-title" id="sp-contact-admin-heading">Need assistance with this complaint?</h4>
                        <p>Contact ShopEase administration regarding your case.</p>
                    </div>
                    <button id="sp-contact-admin" class="sp-contact-admin-button" type="button">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7A8.4 8.4 0 0 1 4 11.5 8.5 8.5 0 0 1 8.7 3.9 8.4 8.4 0 0 1 12.5 3h.5a8.5 8.5 0 0 1 8 8v.5Z"/></svg>
                        Message Admin
                    </button>
                </section></div>
        <div class="complaints-modal-footer-grid"><section class="complaints-modal-card complaints-modal-notes-card"><h4 class="complaints-modal-card-title">Status Updates</h4><div id="complaints-modal-notes-list" class="complaints-modal-notes-list"></div></section><div class="sp-modal-bottom-actions"><p>Status updates are managed by ShopEase administration.</p><button id="complaints-modal-done" type="button" class="sp-modal-done">Close</button></div></div>
      </div>
    </div>

    {{-- Contact Admin: copy a complaint-specific message; no sending endpoint configured. --}}
    <div id="sp-contact-dialog" class="sp-contact-overlay" role="presentation" aria-hidden="true" hidden>
        <div class="sp-contact-dialog" role="dialog" aria-modal="true" aria-labelledby="sp-contact-title" tabindex="-1">
            <div class="sp-contact-header">
                <div><h3 id="sp-contact-title">Contact Admin</h3><p>Prepare a message about this complaint.</p></div>
                <button type="button" id="sp-contact-close" class="sp-contact-x" aria-label="Close Contact Admin dialog">&times;</button>
            </div>
            <label for="sp-contact-message" class="sp-contact-label">Message</label>
            <textarea id="sp-contact-message" rows="5"></textarea>
            <p class="sp-contact-help">Copying this message does not send it. Paste it into your ShopEase Messages page.</p>
            <div class="sp-contact-actions">
                <button type="button" id="sp-contact-cancel" class="sp-contact-cancel">Cancel</button>
                <button type="button" id="sp-contact-copy" class="sp-contact-copy">Copy Message</button>
            </div>
            <p id="sp-contact-feedback" role="status" aria-live="polite"></p>
        </div>
    </div>
    {{-- Click supporting evidence to preview a real attachment. --}}
    <div id="sp-evidence-viewer" class="sp-evidence-viewer" aria-hidden="true" hidden>
        <div class="sp-evidence-viewer-dialog" role="dialog" aria-modal="true" aria-label="Supporting evidence preview">
            <button id="sp-evidence-close" class="sp-evidence-viewer-close" type="button" aria-label="Close evidence preview">&times;</button>
            <img id="sp-evidence-image" alt="Supporting evidence">
            <p id="sp-evidence-caption">Supporting evidence</p>
        </div>
    </div>
    <script id="spBootstrap" type="application/json">{!! json_encode($performance ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
    <script id="spSettings" type="application/json">{!! json_encode(['endpoint' => route('seller.performance.data')], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
</body></html>
