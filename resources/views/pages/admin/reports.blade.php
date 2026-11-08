@extends('layouts.admin')
@section('page-title', 'Reports')
@section('content')
@vite(['resources/css/admin/reports.css', 'resources/js/admin/reports.js'])
<div id="admin-content" class="ar-page ml-60 pt-[110px] pl-5 pb-7 min-h-screen transition-all duration-300">
    <div class="ar-shell" id="admin-reports" data-url="{{ route('admin.reports') }}" data-api-url="{{ route('admin.api.reports.index') }}">
        <form class="ar-period" id="ar-period" action="{{ route('admin.reports') }}" method="get">
            <div class="ar-period-text"><span class="ar-period-icon">▦</span><div><strong>Reporting period</strong><small>Select a date range to update every section</small></div></div>
            <div class="ar-dates"><label>From <input type="date" name="from" value="{{ $from }}" max="{{ now()->toDateString() }}" required></label><span class="ar-date-dash">—</span><label>To <input type="date" name="to" value="{{ $to }}" max="{{ now()->toDateString() }}" required></label></div>
            <div class="ar-period-actions"><a class="ar-export" id="ar-export" href="{{ route('admin.reports.export', ['from' => $from, 'to' => $to]) }}" download>Download CSV</a><button type="button" class="ar-reset" id="ar-last30">Last 30 days</button><button class="ar-apply" type="submit">Apply dates</button></div>
        </form>
        <div id="ar-message" class="ar-message" hidden role="alert"></div>
        <div id="ar-report-content" aria-live="polite">
            <div class="ar-report-body">
    <div class="ar-section-caption">PERIOD SNAPSHOT <span>{{ $dateLabel }}</span></div>
    <div class="ar-kpis">
        <article class="ar-kpi"><div class="ar-kpi-head"><span>Completed merchandise value</span><span class="ar-kpi-icon">₱</span></div><strong>₱{{ number_format($kpis['sales'], 2) }}</strong><small>Completed orders, excluding buyer-paid shipping</small></article>
        <article class="ar-kpi"><div class="ar-kpi-head"><span>Earned platform commission</span><span class="ar-kpi-icon">%</span></div><strong>₱{{ number_format($kpis['commission'], 2) }}</strong><small>Commission entries earned within the range</small></article>
        <article class="ar-kpi"><div class="ar-kpi-head"><span>Buyer refunds issued</span><span class="ar-kpi-icon">↘</span></div><strong>₱{{ number_format($kpis['refunds'], 2) }}</strong><small>Refunded amounts processed within the range</small></article>
        <article class="ar-kpi"><div class="ar-kpi-head"><span>Completed orders</span><span class="ar-kpi-icon">✓</span></div><strong>{{ number_format($kpis['completed']) }}</strong><small>Orders in completed status for the period</small></article>
    </div>

    <div class="ar-two ar-main-charts">
        <section class="ar-card ar-chart-card ar-chart-fill">
            <div class="ar-card-head"><div><h2>Merchandise value trend</h2><p>Daily completed merchandise value for the selected period</p></div><span class="ar-muted-pill">PHP</span></div>
            <div class="ar-chart" id="ar-trend-chart" data-trend='@json($trend)'>
                <div class="ar-chart-empty">Preparing chart…</div>
            </div>
        </section>
        <section class="ar-card ar-cohort">
            <div class="ar-card-head"><div><h2>Order pipeline</h2><p>Current statuses of orders placed in this period</p></div><span class="ar-muted-pill">{{ number_format($orderCohort) }} placed</span></div>
            @forelse($ordersByStatus as $status => $count)
                <div class="ar-meter-row"><div><span>{{ ucwords(str_replace(['_', '-'], ' ', $status)) }}</span><strong>{{ number_format($count) }}</strong></div><div class="ar-meter"><i style="width:{{ $orderCohort ? round(($count / $orderCohort) * 100, 1) : 0 }}%"></i></div></div>
            @empty
                <p class="ar-empty">No orders placed in this period.</p>
            @endforelse
        </section>
    </div>

    <div class="ar-two">
        <section class="ar-card ar-table-card">
            <div class="ar-card-head"><div><h2>Seller contribution</h2><p>Top stores by completed merchandise value</p></div><span class="ar-muted-pill">Top 6</span></div>
            <div class="ar-table-scroll"><table class="ar-table"><thead><tr><th>Store</th><th>Completed orders</th><th class="ar-num">Merchandise value</th></tr></thead><tbody>
            @forelse($sellers as $seller)
                <tr><td class="ar-table-strong">{{ $seller->store_name ?: 'Store #' . $loop->iteration }}</td><td>{{ number_format($seller->orders) }}</td><td class="ar-num">₱{{ number_format($seller->sales,2) }}</td></tr>
            @empty
                <tr><td class="ar-table-empty" colspan="3">No completed seller orders in this period.</td></tr>
            @endforelse
            </tbody></table></div>
        </section>
        <section class="ar-card">
            <div class="ar-card-head"><div><h2>Registration activity</h2><p>Applications submitted during the date range</p></div><span class="ar-muted-pill">{{ number_format($registration['total']) }} submitted</span></div>
            <div class="ar-split-label">APPLICANT TYPE</div>
            <div class="ar-chips">
                @forelse(collect($registration['types'])->only(['buyer', 'seller', 'logistics']) as $type => $count)<span class="ar-chip"><b>{{ ucwords(str_replace('_', ' ', $type)) }}</b><strong>{{ number_format($count) }}</strong></span>@empty <span class="ar-empty">No applications in this period.</span> @endforelse
            </div>
            <div class="ar-divider"></div>
            <div class="ar-split-label">CURRENT REVIEW STATUS</div>
            <div class="ar-status-list">
                @forelse($registration['statuses'] as $status => $count)
                    <div><span><i class="ar-dot"></i>{{ ucwords(str_replace('_', ' ', $status)) }}</span><strong>{{ number_format($count) }}</strong></div>
                @empty <p class="ar-empty">No review records.</p> @endforelse
            </div>
        </section>
    </div>

    <div class="ar-three">
        <section class="ar-card ar-compact"><div class="ar-card-head"><div><h2>Customer issues</h2><p>Complaints opened in this period</p></div><span class="ar-muted-pill">{{ number_format($complaints['total']) }} cases</span></div>
            <div class="ar-status-list">
                @forelse($complaints['statuses'] as $status => $count)<div><span><i class="ar-dot"></i>{{ ucwords(str_replace('_',' ',$status)) }}</span><strong>{{ number_format($count) }}</strong></div>@empty <p class="ar-empty">No complaints recorded.</p>@endforelse
            </div>
        </section>
        <section class="ar-card ar-compact"><div class="ar-card-head"><div><h2>Returns & vouchers</h2><p>Returns requested and voucher usage</p></div></div>
            <div class="ar-mini-stat"><span>Returns &amp; refund cases</span><strong>{{ number_format($returns['total']) }}</strong></div>
            @foreach(collect($returns['types'])->only(['return_refund','refund']) as $type => $count)<div class="ar-sub-stat"><span>{{ $type === 'return_refund' ? 'Return & Refund' : ($type === 'refund' ? 'Refund Only' : ucwords(str_replace('_', ' ', $type ?: 'Unspecified'))) }}</span><span>{{ number_format($count) }}</span></div>@endforeach
            <div class="ar-divider"></div>
            <div class="ar-mini-stat"><span>Orders using seller vouchers</span><strong>{{ number_format($voucher['orders']) }}</strong></div>
            <div class="ar-sub-stat"><span>Redeemed discounts</span><strong>₱{{ number_format($voucher['amount'], 2) }}</strong></div>
        </section>
        <section class="ar-card ar-compact"><div class="ar-card-head"><div><h2>Delivery operations</h2><p>Shipments delivered within this period</p></div></div>
            <div class="ar-delivery-focus"><span>Delivered parcels</span><strong>{{ number_format($shipping['delivered']) }}</strong></div>
            <div class="ar-divider"></div>
            <div class="ar-delivery-focus ar-delivery-time"><span>Average pickup-to-delivery time</span><strong>{{ $shipping['average_hours'] !== null ? number_format($shipping['average_hours'], 1) . ' hrs' : '—' }}</strong></div>
            <p class="ar-card-foot">Calculated only from delivered shipments with both timestamps.</p>
        </section>
    </div>
    <div class="ar-two">
        <section class="ar-card">
            <div class="ar-card-head"><div><h2>Account growth</h2><p>New user accounts created in the reporting period</p></div><span class="ar-muted-pill">{{ number_format($accounts['created']) }} new accounts</span></div>
            <div class="ar-status-list">
                @forelse(collect($accounts['roles'])->only(['buyer', 'seller', 'logistics']) as $role => $count)
                    <div><span><i class="ar-dot"></i>{{ ucwords(str_replace('_', ' ', $role)) }}</span><strong>{{ number_format($count) }}</strong></div>
                @empty <p class="ar-empty">No accounts created in this period.</p> @endforelse
            </div>
        </section>
        <section class="ar-card">
            <div class="ar-card-head"><div><h2>Seller compliance</h2><p>New seller verification records in this period</p></div><span class="ar-muted-pill">{{ number_format($sellerCompliance['reviewed']) }} reviewed</span>
            </div>
            <div class="ar-status-list">
                <div><span><i class="ar-dot"></i>Approved sellers</span><strong>{{ number_format($sellerCompliance['approved']) }}</strong></div>
                <div><span><i class="ar-dot"></i>Pending verification</span><strong>{{ number_format($sellerCompliance['pending']) }}</strong></div>
                <div><span><i class="ar-dot"></i>Rejected sellers</span><strong>{{ number_format($sellerCompliance['rejected']) }}</strong></div>
            </div>
        </section>
    </div>
    <p class="ar-data-note">Data notes: Completed orders use their buyer-received date when present (otherwise the order's latest update). Registrations, complaints, and order pipeline are grouped by their creation date and show current statuses. Refunds use the refund completion date; commission uses its earned date. Figures are not duplicated across report sections. Current-state records may change after this report is viewed.</p>
</div>
        </div>
    </div>
</div>
@endsection
