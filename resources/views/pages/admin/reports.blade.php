@extends('layouts.admin')
@section('page-title', 'Reports')
@section('content')
@php
    // Preview values are exclusively for this Blade template; the controller and database remain unchanged.
    $preview = request()->query('preview', '1') !== '0';
    if ($preview) {
        $samples = [54620,68240,71350,59800,95270,110400,73360,98850,120500,101900,86900,96520,113600,133250];
        $startDay = \Carbon\Carbon::parse($from);
        $endDay = \Carbon\Carbon::parse($to);
        $days = max(1, (int)$startDay->diffInDays($endDay) + 1);
        $trend = [];
        $bucketCount = min(14, $days);
        for ($i=0; $i<$bucketCount; $i++) {
            $day = $startDay->copy()->addDays((int)round($i*($days-1)/max(1,$bucketCount-1)));
            $trend[] = [
                'date' => $day->toDateString(),
                'label' => $day->format('M d'),
                'orders' => [13,16,19,14,22,25,18,21,26,23,20,24,28,31][$i],
                'sales' => $samples[$i],
            ];
        }
        $demo = [
            'kpis' => [
                'sales' => 1284560.50,
                'commission' => 128456.05,
                'refunds' => 37250.00,
                'completed' => 284,
            ],
            'ordersByStatus' => [
                'new' => 21,
                'preparing' => 34,
                'to_ship' => 27,
                'shipped' => 18,
                'out_for_delivery' => 16,
                'completed' => 284,
                'cancelled' => 12,
            ],
            'orderCohort' => 412,
            'trend' => $trend,
            'sellers' => collect([
                (object) ['store_name' => 'Luna Finds PH', 'orders' => 58, 'sales' => 274800.00],
                (object) ['store_name' => 'Urban Essentials', 'orders' => 46, 'sales' => 216450.50],
                (object) ['store_name' => 'Everyday Tech', 'orders' => 39, 'sales' => 193700.00],
                (object) ['store_name' => 'Casa Living', 'orders' => 32, 'sales' => 146250.00],
                (object) ['store_name' => 'Style Street', 'orders' => 29, 'sales' => 118360.00],
                (object) ['store_name' => 'Fresh Basket', 'orders' => 22, 'sales' => 83600.00],
            ]),
            'registration' => [
                'total' => 64,
                'types' => [
                    'buyer' => 21, 'seller' => 34, 'logistics' => 9,
                ],
                'statuses' => [
                    'pending' => 12, 'approved' => 43, 'rejected' => 9,
                ],
            ],
            'complaints' => [
                'total' => 28,
                'statuses' => ['pending' => 6, 'under_review' => 8, 'resolved' => 12, 'rejected' => 2],
            ],
            'returns' => [
                'total' => 41,
                'types' => ['return_refund' => 25, 'refund' => 16],
            ],
            'shipping' => [
                'delivered' => 267,
                'average_hours' => 42.6,
            ],
            'voucher' => [
                'orders' => 73,
                'amount' => 18450.00,
            ],
            'accounts' => [
                'created' => 186,
                'roles' => [
                    'buyer' => 143, 'seller' => 34, 'logistics' => 9,
                ],
            ],
            'sellerCompliance' => [
                'reviewed' => 43,
                'approved' => 34,
                'pending' => 8,
                'rejected' => 9,
            ],
        ];
        foreach ($demo as $key=>$value) { $$key = $value; }
    }
@endphp
@vite(['resources/css/admin/reports.css', 'resources/js/admin/reports.js'])
<div id="admin-content" class="ar-page ml-60 pt-[110px] pl-5 pb-7 min-h-screen transition-all duration-300">
    <div class="ar-shell" id="admin-reports" data-url="{{ route('admin.reports') }}" data-preview="{{ $preview ? '1' : '0' }}">
        <form class="ar-period" id="ar-period" action="{{ route('admin.reports') }}" method="get">
            <div class="ar-period-text"><span class="ar-period-icon">▦</span><div><strong>Reporting period</strong><small>Select a date range to update every section</small></div></div>
            <div class="ar-dates"><label>From <input type="date" name="from" value="{{ $from }}" max="{{ now()->toDateString() }}" required></label><span class="ar-date-dash">—</span><label>To <input type="date" name="to" value="{{ $to }}" max="{{ now()->toDateString() }}" required></label></div>
            <div class="ar-period-actions"><button type="button" class="ar-reset" id="ar-last30">Last 30 days</button><button class="ar-apply" type="submit">Apply dates</button></div>
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
            <div class="ar-card-head"><div><h2>Seller compliance</h2><p>Seller verification and document review activity</p></div>
                @if($preview)<span class="ar-muted-pill">{{ number_format($sellerCompliance['reviewed']) }} reviewed</span>@endif
            </div>
            @if($preview)
                <div class="ar-status-list">
                    <div><span><i class="ar-dot"></i>Approved seller reviews</span><strong>{{ number_format($sellerCompliance['approved']) }}</strong></div>
                    <div><span><i class="ar-dot"></i>Pending seller verification</span><strong>{{ number_format($sellerCompliance['pending']) }}</strong></div>
                    <div><span><i class="ar-dot"></i>Rejected seller reviews</span><strong>{{ number_format($sellerCompliance['rejected']) }}</strong></div>
                </div>
                <p class="ar-card-foot">Example seller verification counts for interface preview only.</p>
            @else
                <p class="ar-empty">Seller verification breakdown is not connected to the current Reports backend yet.</p>
            @endif
        </section>
    </div>
    <p class="ar-data-note">@if($preview)<strong>Sample report:</strong> The sample figures are intentionally illustrative and are not calculated from the selected date range. The date range changes the preview chart labels, not the mock transaction totals. @endif Data notes: Completed orders use their buyer-received date when present (otherwise the order's latest update). Registrations, complaints, and order pipeline are grouped by their creation date and show current statuses. Refunds use the refund completion date; commission uses its earned date. Figures are not duplicated across report sections. Current-state records may change after this report is viewed.</p>
</div>
        </div>
    </div>
</div>
@endsection