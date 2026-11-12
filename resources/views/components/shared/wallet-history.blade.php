<section class="sw-panel" id="sellerWalletHistory" aria-label="Wallet transaction history">
    <div class="sw-panel-head">
        <div>
            <h2>Transaction History</h2>
            <p>Search and filter your wallet ledger records.</p>
        </div>
        <span class="sw-history-count" id="walletResultCount">{{ $wallet['transactions']->total() }} records</span>
    </div>

    <form method="GET" action="{{ route($walletRoute) }}" class="sw-filters" id="walletFilterForm" data-wallet-url="{{ route($walletRoute) }}" data-wallet-api-url="{{ $walletApiUrl }}">
        <div class="sw-search-wrap">
            <input
                type="search"
                name="search"
                value="{{ $search }}"
                placeholder="Search order ref or transaction"
                autocomplete="off"
                class="sw-search-input"
            >
            <svg class="sw-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="11" cy="11" r="7"></circle>
                <path d="M20 20l-4-4"></path>
            </svg>
        </div>

        <div class="sw-filter-group">
            <label for="walletFrom">From</label>
            <input type="datetime-local" id="walletFrom" name="from" value="{{ $from }}" class="sw-filter-input">
        </div>

        <div class="sw-filter-group">
            <label for="walletTo">To</label>
            <input type="datetime-local" id="walletTo" name="to" value="{{ $to }}" class="sw-filter-input">
        </div>

        <div class="sw-filter-actions">
            <button type="submit" class="sw-apply-btn">Apply</button>
            <a href="{{ route($walletRoute) }}" class="sw-clear-btn">Clear</a>
        </div>
    </form>

    <div class="sw-type-tabs" aria-label="Wallet transaction types">
        @foreach ($typeLabels as $typeKey => $typeName)
            @php
                $query = array_filter([
                    'search' => $search !== '' ? $search : null,
                    'from' => $from !== '' ? $from : null,
                    'to' => $to !== '' ? $to : null,
                    'type' => $typeKey === 'all' ? null : $typeKey,
                ], fn ($value) => !is_null($value));
            @endphp
            <a
                href="{{ route($walletRoute, $query) }}"
                data-wallet-type="{{ $typeKey }}" class="sw-type-tab {{ $activeType === $typeKey || ($activeType === '' && $typeKey === 'all') ? 'active' : '' }}"
            >
                {{ $typeName }}
            </a>
        @endforeach
    </div>

    <p class="sw-loading-note" id="walletLiveStatus" aria-live="polite" role="status"></p>

    <div class="sw-table-wrap" id="walletResultsTable">
        <table class="sw-table">
            <thead>
                <tr>
                    <th>Transaction</th>
                    <th>Order Reference</th>
                    <th>Date &amp; Time</th>
                    <th>Status</th>
                    <th class="sw-right">Amount</th>
                </tr>
            </thead>
            <tbody>
            @forelse($wallet['transactions'] as $transaction)
                @php
                    $labels = [
                        'commission_income' => 'Order commission',
                        'platform_voucher' => 'ShopEase voucher deduction',
                        'commission_refund' => 'Refund commission deduction',
                        'order_income' => 'Completed order earnings',
                        'refund' => 'Buyer refund',
                        'return_shipping' => 'Return shipping',
                        'voucher_discount' => 'Seller voucher deduction',
                        'logistics_reimbursement' => 'Logistics reimbursement',
                    ];
                    $positive = (float)$transaction->amount >= 0;
                @endphp
                <tr>
                    <td>
                        <div class="sw-transaction">
                            <span class="sw-tr-icon {{ $positive ? 'sw-positive-bg' : 'sw-negative-bg' }}">{{ $positive ? '↗' : '↙' }}</span>
                            <span>
                                <strong>{{ $labels[$transaction->entry_type] ?? 'Wallet adjustment' }}</strong>
                                <small>{{ $transaction->description }}</small>
                            </span>
                        </div>
                    </td>
                    <td class="sw-order">{{ $transaction->reference ?? '—' }}</td>
                    <td>{{ $transaction->occurred_at ? \Carbon\Carbon::parse($transaction->occurred_at)->format('M d, Y · h:i A') : '—' }}</td>
                    <td><span class="sw-status">Recorded</span></td>
                    <td class="sw-right sw-money {{ $positive ? 'sw-positive' : 'sw-negative' }}">{{ $positive ? '+' : '−' }}₱{{ number_format(abs((float)$transaction->amount),2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="sw-empty">No matching transactions found. Try another search term or change the filters.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="sw-pagination-wrap" id="walletPagination">
        @if($wallet['transactions']->hasPages())
            <p class="sw-pagination-summary">Showing {{ $wallet['transactions']->firstItem() }}–{{ $wallet['transactions']->lastItem() }} of {{ $wallet['transactions']->total() }}</p>
            <div class="sw-pagination-pages" aria-label="Wallet pages">
                @if($wallet['transactions']->onFirstPage())
                    <span class="sw-page-link sw-disabled" aria-disabled="true">‹</span>
                @else
                    <a class="sw-page-link" href="{{ $wallet['transactions']->previousPageUrl() }}" aria-label="Previous page">‹</a>
                @endif
                @foreach($wallet['transactions']->getUrlRange(max(1, $wallet['transactions']->currentPage() - 2), min($wallet['transactions']->lastPage(), $wallet['transactions']->currentPage() + 2)) as $pageNumber => $url)
                    @if($wallet['transactions']->currentPage() === $pageNumber)
                        <span class="sw-page-link sw-current" aria-current="page">{{ $pageNumber }}</span>
                    @else
                        <a class="sw-page-link" href="{{ $url }}">{{ $pageNumber }}</a>
                    @endif
                @endforeach
                @if($wallet['transactions']->hasMorePages())
                    <a class="sw-page-link" href="{{ $wallet['transactions']->nextPageUrl() }}" aria-label="Next page">›</a>
                @else
                    <span class="sw-page-link sw-disabled" aria-disabled="true">›</span>
                @endif
            </div>
        @endif
    </div>
</section>
