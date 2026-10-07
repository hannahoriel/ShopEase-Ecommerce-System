@extends('layouts.admin')

@section('page-title', 'Complaints and Disputes')

@section('content')

@vite('resources/css/admin/complaints-disputes.css')



@php
    $statusLabels = [
        'open' => 'Open',
        'in-progress' => 'In progress',
        'resolved' => 'Resolved',
    ];
@endphp

<div
    id="admin-content"
    class="ml-60 pt-[110px] pl-5 pb-7 min-h-screen transition-all duration-300"
>
    <!-- ==================== STAT CARDS ==================== -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-5">

        <!-- Open -->
        <div class="complaint-stat-card">
            <div class="complaint-stat-main">
                <img
                    src="{{ asset('icons/admin/complaints-disputes/open.png') }}"
                    class="complaint-stat-icon"
                    alt="Open complaints"
                >

                <div class="complaint-stat-content">
                    <p id="complaint-stat-open" class="complaint-stat-number">{{ $complaintCounts['open'] }}</p>
                    <p class="complaint-stat-label">Open</p>
                </div>
            </div>

        </div>

        <!-- In progress -->
        <div class="complaint-stat-card">
            <div class="complaint-stat-main">
                <img
    src="{{ asset('icons/admin/complaints-disputes/in-progress.png') }}"
    class="complaint-stat-icon"
    alt="In progress complaints"
>

                <div class="complaint-stat-content">
                    <p id="complaint-stat-in-progress" class="complaint-stat-number">{{ $complaintCounts['in_progress'] }}</p>
                    <p class="complaint-stat-label">In progress</p>
                </div>
            </div>

        </div>

        <!-- Resolved -->
        <div class="complaint-stat-card">
            <div class="complaint-stat-main">
                <img
                    src="{{ asset('icons/admin/complaints-disputes/resolved.png') }}"
                    class="complaint-stat-icon"
                    alt="Resolved complaints"
                >

                <div class="complaint-stat-content">
                    <p id="complaint-stat-resolved" class="complaint-stat-number">{{ $complaintCounts['resolved'] }}</p>
                    <p class="complaint-stat-label">Resolved</p>
                </div>
            </div>

        </div>

        <!-- Total -->
        <div class="complaint-stat-card">
            <div class="complaint-stat-main">
                <img
                    src="{{ asset('icons/admin/complaints-disputes/total-complaints.png') }}"
                    class="complaint-stat-icon"
                    alt="Total complaints"
                >

                <div class="complaint-stat-content">
                    <p id="complaint-stat-total" class="complaint-stat-number">{{ $complaintCounts['total'] }}</p>
                    <p class="complaint-stat-label">Total Complaints</p>
                </div>
            </div>
        </div>

    </div>

    <!-- ==================== FILTER BAR ==================== -->
    <div class="complaints-filter-card">
        <div class="complaints-filter-row">

            <div class="complaints-search-wrap">
                <input
                    id="complaints-search"
                    type="text"
                    placeholder="Search ID, name"
                    class="complaints-search"
                >

                <svg
                    class="complaints-search-icon"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"
                    />
                </svg>
            </div>





            <div class="complaints-date-wrap">
                <input
                    id="complaints-date-filter"
                    type="date"
                    class="complaints-date-input"
                    aria-label="Complaint date"
                >

                <button
                    id="complaints-date-button"
                    type="button"
                    class="complaints-date-button"
                    aria-label="Open calendar"
                >
                    <svg
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M8 2v4m8-4v4M3 10h18M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"
                        />
                    </svg>
                </button>
            </div>

            <button
                id="complaints-reload"
                type="button"
                class="complaints-reload"
                title="Reset filters"
                aria-label="Reset filters"
            >
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M20 11a8.1 8.1 0 0 0-15.5-2M4 5v4h4M4 13a8.1 8.1 0 0 0 15.5 2M20 19v-4h-4"
                    />
                </svg>
            </button>

        </div>
    </div>

    <!-- ==================== TABLE + DETAILS ==================== -->
    <div id="complaints-workspace" class="complaints-workspace">

        <!-- LEFT TABLE -->
        <div class="complaints-table-card">

            <div class="complaints-tabs" role="tablist">
                <button type="button" class="complaints-tab active" data-tab="all">All Complaints</button>
                <button type="button" class="complaints-tab" data-tab="open">Open</button>
                <button type="button" class="complaints-tab" data-tab="in-progress">In progress</button>
                <button type="button" class="complaints-tab" data-tab="resolved">Resolved</button>
            </div>

            <div class="complaints-table-scroll">
                <table class="complaints-table">
                    <thead>
                        <tr>
                            <th>Complaint ID</th>
                            <th>Parties</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Date Filed</th>
                        </tr>
                    </thead>

                    <tbody id="complaints-table-body">
                        @foreach ($complaintRows as $index => $complaint)
                            <tr
                                class="complaint-row"
                                data-index="{{ $index }}"
                                data-database-id="{{ $complaint['databaseId'] }}"
                                data-id="{{ strtolower($complaint['id']) }}"
                                data-name="{{ strtolower($complaint['party1'] . ' ' . $complaint['party2']) }}"
                                data-type="{{ $complaint['type'] }}"
                                data-status="{{ $complaint['status'] }}"
                                tabindex="0"
                                role="button"
                            >
                                <td>
                                    <span class="complaint-id">{{ $complaint['id'] }}</span>
                                </td>

                                <td>
                                    <div class="complaint-parties">
                                        <div class="complaint-party">
                                            <span class="complaint-avatar"></span>

                                            <span class="complaint-party-copy">
                                                <span class="complaint-party-name">{{ $complaint['party1'] }}</span>
                                                <span class="complaint-party-role">{{ $complaint['role1'] }}</span>
                                            </span>
                                        </div>

                                        <div class="complaint-party">
                                            <span class="complaint-avatar"></span>

                                            <span class="complaint-party-copy">
                                                <span class="complaint-party-name">{{ $complaint['party2'] }}</span>
                                                <span class="complaint-party-role">{{ $complaint['role2'] }}</span>
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <span class="complaint-type">{{ $complaint['type'] }}</span>
                                </td>

                                <td>
                                    <span class="complaint-status {{ $complaint['status'] }}">
                                        {{ $statusLabels[$complaint['status']] }}
                                    </span>
                                </td>

                                <td>
                                    <div class="complaint-date-cell">
                                        <span>
                                            {{ $complaint['date'] }}<br>
                                            {{ $complaint['time'] }}
                                        </span>

                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/>
                                        </svg>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div id="complaints-empty" class="complaints-empty">
                    No complaints match the selected filters.
                </div>
            </div>

            <div class="complaints-table-footer">
                <p id="complaints-count" class="complaints-count">
                    Showing 0 out of 0 entries
                </p>

                <div class="complaints-pagination">
                    <button type="button" class="complaints-page-button" aria-label="Previous page">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 6-6 6 6 6"/>
                        </svg>
                    </button>

                    <button type="button" class="complaints-page-button active">1</button>
                    <button type="button" class="complaints-page-button">2</button>
                    <button type="button" class="complaints-page-button">3</button>

                    <button type="button" class="complaints-page-button" aria-label="Next page">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 6 6 6-6 6"/>
                        </svg>
                    </button>

                    <select id="complaints-items" class="complaints-items">
                        <option value="7" selected>Items per page: 7</option>
                        <option value="10">Items per page: 10</option>
                        <option value="20">Items per page: 20</option>
                    </select>
                </div>
            </div>

        </div>

        <!-- RIGHT DETAIL PANEL -->
        <aside id="complaints-detail-card" class="complaints-detail-card" aria-hidden="true">

            <div class="complaints-detail-header">
                <div>
                    <div class="complaints-detail-eyebrow">Complaint ID</div>
                    <div id="detail-id" class="complaints-detail-id">AS2026041</div>
                </div>

                <div class="complaints-detail-header-right">
                    <span id="detail-status" class="complaint-status open">Open</span>

                    <button
                        id="complaints-detail-close"
                        type="button"
                        class="complaints-detail-close"
                        aria-label="Close complaint details"
                        title="Close details"
                    >
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 6l12 12M18 6 6 18"
                            />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="complaints-detail-body">

                <div class="complaints-detail-section">
                    <h4 class="complaints-detail-label">Date Filed</h4>
                    <p id="detail-date" class="complaints-detail-text">May 31, 2026 10:30 AM</p>
                </div>

                <div class="complaints-detail-section">
                    <h4 class="complaints-detail-label">Complaint Type</h4>

                    <p class="complaints-detail-text complaints-type-line">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.8"
                                d="M6 3h8l4 4v14H6zM14 3v5h5M9 12h6M9 16h4"
                            />
                        </svg>

                        <span id="detail-type">Wrong Item</span>
                    </p>
                </div>

                <div class="complaints-detail-section">
                    <h4 class="complaints-detail-label">Description</h4>

                    <p id="detail-description" class="complaints-detail-text">
                        Mali-mali yung pinapadala ng seller. Grabe kayo lumaban kayo ng patas. Sayang pera.
                    </p>
                </div>

                <div class="complaints-detail-section">
                    <h4 class="complaints-detail-label">Parties Involved</h4>

                    <div class="complaints-parties-list">
                        <div class="complaints-party-detail">
                            <span class="complaint-avatar"></span>

                            <span class="complaints-party-detail-copy">
                                <span id="detail-party-1" class="complaints-party-detail-name">JunjunDura</span>
                                <span id="detail-role-1" class="complaints-party-detail-role">Buyer</span>
                            </span>

                            <img
                                src="{{ asset('icons/admin/complaints-disputes/message-icon.png') }}"
                                class="complaints-message-icon"
                                alt="Message"
                            >
                        </div>

                        <div class="complaints-party-detail">
                            <span class="complaint-avatar"></span>

                            <span class="complaints-party-detail-copy">
                                <span id="detail-party-2" class="complaints-party-detail-name">DelaCruzShop</span>
                                <span id="detail-role-2" class="complaints-party-detail-role">Seller</span>
                            </span>

                            <img
                                src="{{ asset('icons/admin/complaints-disputes/message-icon.png') }}"
                                class="complaints-message-icon"
                                alt="Message"
                            >
                        </div>
                    </div>
                </div>

                <div class="complaints-detail-section">
                    <h4 class="complaints-detail-label">Supporting Evidence</h4>

                    <div class="complaints-evidence">
                        <span class="complaints-evidence-item"></span>
                        <span class="complaints-evidence-item"></span>
                        <span class="complaints-evidence-item"></span>
                        <span class="complaints-evidence-item"></span>
                        <span class="complaints-evidence-item complaints-evidence-more">+2</span>
                    </div>
                </div>

            </div>

            <div class="complaints-detail-actions">
                <button
                    id="complaints-view-details"
                    type="button"
                    class="complaints-view-details"
                >
                    View Details
                </button>

                <div class="complaints-update-wrap">
                    <button
                        id="complaints-update-status"
                        type="button"
                        class="complaints-update-status"
                    >
                        Update Status

                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7 9 5 5 5-5"/>
                        </svg>
                    </button>

                    <div id="complaints-status-menu" class="complaints-status-menu">
                        <button type="button" class="complaints-status-option" data-status="open">
                            Open
                        </button>

                        <button type="button" class="complaints-status-option" data-status="in-progress">
                            In progress
                        </button>

                        <button type="button" class="complaints-status-option" data-status="resolved">
                            Resolved
                        </button>
                    </div>
                </div>
            </div>

        </aside>

    </div>

    <!-- ==================== VIEW DETAILS MODAL ==================== -->
    <div
        id="complaints-details-modal"
        class="fixed inset-0 z-[180] hidden items-center justify-center bg-black/35 backdrop-blur-[2px] px-4 py-4"
        aria-hidden="true"
    >
        <div
            class="complaints-modal-dialog"
            role="dialog"
            aria-modal="true"
            aria-labelledby="complaints-modal-title"
        >
            <h3 id="complaints-modal-title" class="complaints-modal-title">
                Complaint Details
            </h3>

            <!-- TOP SUMMARY -->
            <div class="complaints-modal-grid complaints-modal-top-grid">
                <section class="complaints-modal-card complaints-modal-summary">
                    <div class="complaints-modal-summary-main">
                        <div class="complaints-modal-id-block">
                            <span class="complaints-modal-label">Complaint ID</span>
                            <strong id="modal-complaint-id" class="complaints-modal-id">AS2026041</strong>
                        </div>

                        <span id="modal-complaint-status" class="complaints-modal-status open">
                            Open
                        </span>
                    </div>

                    <div class="complaints-modal-meta">
                        <div class="complaints-modal-meta-item">
                            <span>Date Filed</span>
                            <strong id="modal-date-filed">May 31, 2026&nbsp;&nbsp;10:30 AM</strong>
                        </div>

                        <div class="complaints-modal-meta-item">
                            <span>Last Updated</span>
                            <strong id="modal-last-updated">June 2, 2026&nbsp;&nbsp;10:50 AM</strong>
                        </div>
                    </div>
                </section>

                <section class="complaints-modal-card complaints-modal-type-order">
                    <div>
                        <span class="complaints-modal-label">Complaint Type</span>

                        <div class="complaints-modal-type-value">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 3h7l4 4v14H7zM14 3v5h5M10 13h5M10 17h4"/>
                            </svg>
                            <span id="modal-complaint-type">Wrong Item</span>
                        </div>
                    </div>

                    <div>
                        <span class="complaints-modal-label">Order ID</span>
                        <strong id="modal-order-id-top" class="complaints-modal-order-id">ORD-2089</strong>
                    </div>
                </section>
            </div>

            <!-- DESCRIPTION + PARTIES -->
            <div class="complaints-modal-grid mt-[10px]">
                <section class="complaints-modal-card complaints-modal-description-card">
                    <h4 class="complaints-modal-card-title">Description</h4>
                    <p id="modal-description-copy" class="complaints-modal-description">
                        Mali-mali yung pinapadala ng seller. Grabe kayo lumaban kayo ng patas. Sayang pera.
                    </p>
                </section>

                <section class="complaints-modal-card complaints-modal-parties-card">
                    <h4 class="complaints-modal-card-title">Parties Involved</h4>

                    <div class="complaints-modal-party-list">
                        <div class="complaints-modal-party-row">
                            <span class="complaints-modal-party-avatar"></span>

                            <div class="complaints-modal-party-copy">
                                <strong id="modal-party-1-name">JunjunDura</strong>
                                <span id="modal-party-1-email">junjun@gmail.com</span>
                            </div>

                            <div class="complaints-modal-party-role">
                                <img
                                    id="modal-party-1-role-icon"
                                    src="{{ asset('icons/admin/dashboard/body/buyer.png') }}"
                                    alt="Buyer"
                                >
                                <span id="modal-party-1-role">Buyer</span>
                            </div>

                            <button
                                type="button"
                                class="complaints-modal-message-button"
                                data-party-index="1"
                                aria-label="Message first party"
                            >
                                <img
                                    src="{{ asset('icons/admin/complaints-disputes/message-icon.png') }}"
                                    alt="Message"
                                >
                            </button>
                        </div>

                        <div class="complaints-modal-party-row">
                            <span class="complaints-modal-party-avatar"></span>

                            <div class="complaints-modal-party-copy">
                                <strong id="modal-party-2-name">DelaCruzShop</strong>
                                <span id="modal-party-2-email">juandelacruz@gmail.com</span>
                            </div>

                            <div class="complaints-modal-party-role">
                                <img
                                    id="modal-party-2-role-icon"
                                    src="{{ asset('icons/admin/dashboard/body/seller.png') }}"
                                    alt="Seller"
                                >
                                <span id="modal-party-2-role">Seller</span>
                            </div>

                            <button
                                type="button"
                                class="complaints-modal-message-button"
                                data-party-index="2"
                                aria-label="Message second party"
                            >
                                <img
                                    src="{{ asset('icons/admin/complaints-disputes/message-icon.png') }}"
                                    alt="Message"
                                >
                            </button>
                        </div>
                    </div>
                </section>
            </div>

            <!-- ORDER INFORMATION + ORDERED ITEM -->
            <div class="complaints-modal-grid complaints-modal-middle-grid mt-[10px]">
                <section class="complaints-modal-card complaints-modal-order-card">
                    <h4 class="complaints-modal-card-title">Order Information</h4>

                    <div class="complaints-modal-order-list">
                        <div class="complaints-modal-order-row">
                            <span>Order ID</span>
                            <span id="modal-order-id">ORD-2089</span>
                        </div>
                        <div class="complaints-modal-order-row">
                            <span>Order Date</span>
                            <span id="modal-order-date">May 28, 2026&nbsp;&nbsp;10:00 PM</span>
                        </div>
                        <div class="complaints-modal-order-row">
                            <span>Payment Method</span>
                            <span id="modal-payment-method">Cash On Delivery</span>
                        </div>
                        <div class="complaints-modal-order-row">
                            <span>Payment Status</span>
                            <span id="modal-payment-status">Paid</span>
                        </div>
                        <div class="complaints-modal-order-row">
                            <span>Shipping Method</span>
                            <span id="modal-shipping-method">J&amp;T Express</span>
                        </div>
                        <div class="complaints-modal-order-row">
                            <span>Order Status</span>
                            <span id="modal-order-status">Completed</span>
                        </div>
                        <div class="complaints-modal-order-row">
                            <span>Order Total</span>
                            <span id="modal-order-total">₱1,249.00</span>
                        </div>
                    </div>
                </section>

                <section class="complaints-modal-card complaints-modal-item-card">
                    <h4 class="complaints-modal-card-title">Ordered Item</h4>

                    <div class="complaints-modal-item-main">
                        <img
                            src="data:image/png;base64,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"
                            class="complaints-modal-product-image"
                            alt="Smart Watch Series X"
                        >

                        <div>
                            <p id="modal-product-name" class="complaints-modal-product-name">
                                Smart Watch Series X
                            </p>

                            <p id="modal-product-variant" class="complaints-modal-product-variant">
                                Black
                            </p>

                            <div class="complaints-modal-product-price">
                                <strong id="modal-product-price">₱1,249.00</strong>
                                <span id="modal-product-quantity">x1</span>
                            </div>
                        </div>
                    </div>

                    <div class="complaints-modal-item-extra">
                        <div>
                            <span class="complaints-modal-item-extra-label">Category</span>
                            <span id="modal-product-category" class="complaints-modal-category-pill">
                                Electronics &amp; Gadgets
                            </span>
                        </div>

                        <div class="complaints-modal-shop-line">
                            Shop:
                            <strong id="modal-shop-name">DelaCruzShop</strong>
                            <span id="modal-shop-owner" class="complaints-modal-shop-owner">Juan Dela Cruz</span>
                        </div>
                    </div>
                </section>
            </div>

            <!-- EVIDENCE + SELLER RESPONSE -->
            <div class="complaints-modal-grid complaints-modal-bottom-grid mt-[10px]">
                <section class="complaints-modal-card complaints-modal-evidence-card">
                    <h4 class="complaints-modal-card-title">Supporting Evidence</h4>

                    <div class="complaints-modal-evidence-list">
                        <span>No evidence attached.</span>
                    </div>
                </section>

                <section class="complaints-modal-card complaints-modal-response-card">
                    <h4 class="complaints-modal-card-title">Seller Response</h4>

                    <div class="complaints-modal-response-alert">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="12" cy="12" r="9" stroke-width="2"/>
                            <path d="M12 10v5M12 7h.01" stroke-width="2" stroke-linecap="round"/>
                        </svg>

                        <div class="complaints-modal-response-copy">
                            <strong id="modal-response-title">No seller response</strong>
                            <span id="modal-response-message">A seller response has not been recorded.</span>
                        </div>
                    </div>

                    <p id="modal-response-empty" class="complaints-modal-no-response">
                        No response recorded.
                    </p>
                </section>
            </div>

            <!-- NOTES + UPDATE STATUS -->
            <div class="complaints-modal-footer-grid mt-[10px]">
                <section class="complaints-modal-card complaints-modal-notes-card">
                    <h4 class="complaints-modal-card-title">Notes/Updates</h4>

                    <div
                        id="complaints-modal-notes-list"
                        class="complaints-modal-notes-list"
                        aria-live="polite"
                    >
                        <div class="complaints-modal-notes-empty">
                            No updates yet.
                        </div>
                    </div>
                </section>

                <div class="complaints-modal-actions-area">
                    <div class="complaints-modal-update-wrap">
                        <button
                            id="complaints-modal-update-status"
                            type="button"
                            class="complaints-modal-update-status"
                        >
                            Update Status

                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7 9 5 5 5-5"/>
                            </svg>
                        </button>

                        <div id="complaints-modal-status-menu" class="complaints-modal-status-menu">
                            <button type="button" class="complaints-modal-status-option" data-status="open">Open</button>
                            <button type="button" class="complaints-modal-status-option" data-status="in-progress">In progress</button>
                            <button type="button" class="complaints-modal-status-option" data-status="resolved">Resolved</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== STATUS FLASH ==================== -->
    <div
        id="complaints-flash"
        class="complaints-flash"
        role="status"
        aria-live="polite"
        aria-atomic="true"
    >
        <div class="flex items-start gap-3">

            <div
                id="complaints-flash-icon"
                class="w-9 h-9 rounded-full flex items-center justify-center shrink-0"
            ></div>

            <div class="min-w-0 flex-1">
                <p
                    id="complaints-flash-title"
                    class="text-[14px] font-semibold text-black"
                ></p>

                <p
                    id="complaints-flash-message"
                    class="mt-1 text-[12px] leading-5 text-gray-500"
                ></p>
            </div>

            <button
                id="complaints-flash-close"
                type="button"
                class="w-7 h-7 rounded-full flex items-center justify-center text-gray-400 hover:bg-gray-100 transition shrink-0"
                aria-label="Close notification"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6 6 18"/>
                </svg>
            </button>

        </div>
    </div>
</div>

@php
    $complaintsDisputesClientConfig = [
        'complaints' => $complaintRows,
        'icons' => [
            'buyer' => asset('icons/admin/dashboard/body/buyer.png'),
            'seller' => asset('icons/admin/dashboard/body/seller.png'),
        ],
    ];
@endphp

<script
    type="application/json"
    id="complaintsDisputesConfig"
>{!! json_encode($complaintsDisputesClientConfig, JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>

@endsection




@push('scripts')

@vite('resources/js/admin/complaints-disputes.js')

@endpush
