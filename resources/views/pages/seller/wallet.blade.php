<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>ShopEase | Seller Wallet</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/seller/wallet.css', 'resources/js/seller/wallet.js'])
</head>
<body>
    <x-seller.sidebar />
    <x-seller.navbar />

    @php
        $activeType = $filters['type'] ?? 'all';
        $search = $filters['search'] ?? '';
        $from = $filters['from'] ?? '';
        $to = $filters['to'] ?? '';
        $typeLabels = [
            'all' => 'All Transactions',
            'order_income' => 'Earnings',
            'refund' => 'Refunds',
            'return_shipping' => 'Shipping',
            'voucher_discount' => 'Vouchers',
        ];
    @endphp

    <div class="sw-page">
        <main class="sw-content">
            <div class="sw-hero">
                <div class="sw-hero-left">
                    <span class="sw-hero-label">Current Wallet Balance</span>
                    <div class="sw-amount">{{ $wallet['balance'] < 0 ? '−' : '' }}₱{{ number_format(abs($wallet['balance']), 2) }}</div>
                    <p>{{ $wallet['balance'] < 0 ? 'Negative balance means refunds or return-related costs are currently higher than recorded earnings.' : 'Your recorded balance is based on completed orders, refunds, return shipping charges, and redeemed seller vouchers.' }}</p>
                    <span class="sw-store">{{ $seller->store_name ?: ($seller->business_name ?: 'My Shop') }}</span>
                </div>
                <div class="sw-hero-mark" aria-hidden="true">
                    <svg viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="16" y="32" width="88" height="58" rx="18" stroke="rgba(255,255,255,0.95)" stroke-width="6"/>
                        <path d="M36 43V34C36 20.7452 46.7452 10 60 10C73.2548 10 84 20.7452 84 34V43" stroke="rgba(255,255,255,0.95)" stroke-width="6" stroke-linecap="round"/>
                        <path d="M74 61H104" stroke="rgba(255,255,255,0.92)" stroke-width="6" stroke-linecap="round"/>
                        <circle cx="58" cy="61" r="7" fill="rgba(255,255,255,0.95)"/>
                    </svg>
                </div>
            </div>

            <div class="sw-stats">
                <article class="sw-stat"><div class="sw-stat-label"><span class="sw-symbol sw-green">↗</span> Completed Order Earnings</div><strong>₱{{ number_format($wallet['income'],2) }}</strong><span class="sw-stat-help">Product amounts from completed orders</span></article>
                <article class="sw-stat"><div class="sw-stat-label"><span class="sw-symbol sw-red">↙</span> Buyer Refunds</div><strong>₱{{ number_format($wallet['refunds'],2) }}</strong><span class="sw-stat-help">Completed refund deductions</span></article>
                <article class="sw-stat"><div class="sw-stat-label"><span class="sw-symbol sw-orange">⇄</span> Return Shipping</div><strong>₱{{ number_format($wallet['shipping'],2) }}</strong><span class="sw-stat-help">₱50 per completed physical return</span></article>
                <article class="sw-stat"><div class="sw-stat-label"><span class="sw-symbol sw-purple">%</span> Seller Vouchers</div><strong>₱{{ number_format($wallet['vouchers'],2) }}</strong><span class="sw-stat-help">Redeemed seller-funded voucher costs</span></article>
            </div>

            <section class="sw-panel" id="sellerWalletHistory" aria-label="Wallet transaction history">
                <div class="sw-panel-head">
                    <div>
                        <h2>Transaction History</h2>
                        <p>Search and filter your wallet ledger records.</p>
                    </div>
                    <span class="sw-history-count" id="walletResultCount">{{ $wallet['transactions']->total() }} records</span>
                </div>

                <form method="GET" action="{{ route('seller.wallet.index') }}" class="sw-filters" id="walletFilterForm" data-wallet-url="{{ route('seller.wallet.index') }}" data-wallet-api-url="{{ route('seller.api.wallet.index') }}">
                    <div class="sw-search-wrap">
                        <input
                            type="text"
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
                        <a href="{{ route('seller.wallet.index') }}" class="sw-clear-btn">Clear</a>
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
                            href="{{ route('seller.wallet.index', $query) }}"
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
                                    'order_income' => 'Completed order earnings',
                                    'refund' => 'Buyer refund',
                                    'return_shipping' => 'Return shipping',
                                    'voucher_discount' => 'Seller voucher deduction',
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
        </main>
    </div>
</body>
</html>
