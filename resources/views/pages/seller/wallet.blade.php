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
            'logistics_reimbursement' => 'Logistics Reimbursements',
        ];
    @endphp

    <div class="sw-page">
        <main class="sw-content">
            <div class="sw-hero">
                <div class="sw-hero-left">
                    <span class="sw-hero-label">Current Wallet Balance</span>
                    <div class="sw-amount">{{ $wallet['balance'] < 0 ? '−' : '' }}₱{{ number_format(abs($wallet['balance']), 2) }}</div>
                    <p>{{ $wallet['balance'] < 0 ? 'Negative balance means refunds or return-related costs are currently higher than recorded earnings.' : 'Your recorded balance includes completed orders, logistics reimbursements, refunds, return shipping charges, and redeemed seller vouchers.' }}</p>
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
                <article class="sw-stat"><div class="sw-stat-label"><span class="sw-symbol sw-green">↗</span> Logistics Reimbursements</div><strong>₱{{ number_format($wallet['reimbursements'],2) }}</strong><span class="sw-stat-help">Lost or damaged parcel compensation</span></article>
                <article class="sw-stat"><div class="sw-stat-label"><span class="sw-symbol sw-red">↙</span> Buyer Refunds</div><strong>₱{{ number_format($wallet['refunds'],2) }}</strong><span class="sw-stat-help">Completed refund deductions</span></article>
                <article class="sw-stat"><div class="sw-stat-label"><span class="sw-symbol sw-orange">⇄</span> Return Shipping</div><strong>₱{{ number_format($wallet['shipping'],2) }}</strong><span class="sw-stat-help">₱50 per completed physical return</span></article>
                <article class="sw-stat"><div class="sw-stat-label"><span class="sw-symbol sw-purple">%</span> Seller Vouchers</div><strong>₱{{ number_format($wallet['vouchers'],2) }}</strong><span class="sw-stat-help">Redeemed seller-funded voucher costs</span></article>
            </div>

            @include('components.shared.wallet-history', ['walletRoute' => 'seller.wallet.index', 'walletApiUrl' => route('seller.api.wallet.index')])
        </main>
    </div>
</body>
</html>
