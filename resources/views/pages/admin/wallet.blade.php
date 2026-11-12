@extends('layouts.admin')

@section('title', 'ShopEase | Admin Wallet')
@section('page-title', 'ShopEase Wallet')

@section('content')
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap">
@vite(['resources/css/seller/wallet.css', 'resources/js/seller/wallet.js'])

@php
    $activeType = $filters['type'] ?? 'all';
    $search = $filters['search'] ?? '';
    $from = $filters['from'] ?? '';
    $to = $filters['to'] ?? '';
    $typeLabels = [
        'all' => 'All Transactions',
        'commission_income' => 'Commission',
        'platform_voucher' => 'Vouchers',
        'commission_refund' => 'Refund Deductions',
    ];
@endphp

<style>
    #admin-content.sw-page, #admin-content.sw-page :is(input, select, button) { font-family: Poppins, Arial, sans-serif; }
</style>

<div id="admin-content" class="sw-page">
    <div class="sw-content">
        <section class="sw-hero" aria-label="ShopEase Wallet balance">
            <div class="sw-hero-left">
                <span class="sw-hero-label">Recorded Wallet Balance</span>
                <div class="sw-amount">{{ $wallet['balance'] < 0 ? '−' : '' }}₱{{ number_format(abs($wallet['balance']), 2) }}</div>
                <p>Earned order commissions less ShopEase voucher costs and commission deductions for completed refunds.</p>
                <span class="sw-store">ShopEase Admin</span>
            </div>
                <div class="sw-hero-mark" aria-hidden="true">
                    <svg viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="16" y="32" width="88" height="58" rx="18" stroke="rgba(255,255,255,0.95)" stroke-width="6"/>
                        <path d="M36 43V34C36 20.7452 46.7452 10 60 10C73.2548 10 84 20.7452 84 34V43" stroke="rgba(255,255,255,0.95)" stroke-width="6" stroke-linecap="round"/>
                        <path d="M74 61H104" stroke="rgba(255,255,255,0.92)" stroke-width="6" stroke-linecap="round"/>
                        <circle cx="58" cy="61" r="7" fill="rgba(255,255,255,0.95)"/>
                    </svg>
                </div>
        </section>

        <div class="sw-stats" style="grid-template-columns: repeat(auto-fit, minmax(min(100%, 210px), 1fr))">
            <article class="sw-stat">
                <div class="sw-stat-label"><span class="sw-symbol sw-green">↗</span> Earned Commission</div>
                <strong>₱{{ number_format($wallet['income'], 2) }}</strong>
                <span class="sw-stat-help">Recorded order commission credits</span>
            </article>
            <article class="sw-stat">
                <div class="sw-stat-label"><span class="sw-symbol sw-purple">%</span> ShopEase Vouchers</div>
                <strong>₱{{ number_format($wallet['vouchers'], 2) }}</strong>
                <span class="sw-stat-help">ShopEase-funded discounts on completed orders</span>
            </article>
            <article class="sw-stat">
                <div class="sw-stat-label"><span class="sw-symbol sw-red">↙</span> Refund Deductions</div>
                <strong>₱{{ number_format($wallet['refunds'], 2) }}</strong>
                <span class="sw-stat-help">ShopEase commission share of completed refunds</span>
            </article>
        </div>

        @include('components.shared.wallet-history', ['walletRoute' => 'admin.wallet.index', 'walletApiUrl' => route('admin.wallet.index')])
    </div>
</div>
@endsection
