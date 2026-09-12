{{-- =========================================================
     SELLER ORDER STATUS PAGE
     resources/views/pages/seller/order-status.blade.php

     Updated UX
     ---------------------------------------------------------
     - New Order -> Preparing
     - Preparing -> Schedule Shipment
     - Pickup date/time updates the To Ship row
     - Tracking History appears after preparation
     - Ready to Ship tracking appears after scheduling
     - ALL MODAL EXIT ANIMATIONS ARE FADE-OUT ONLY
     - No modal slide / scale / translate transitions
     - Modal scrollbar is hidden while remaining scrollable
========================================================= --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopEase - Order Status</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>

<body class="m-0 p-0 bg-[#FCF8F6] font-[Poppins,sans-serif] text-[#17120F]">

    {{-- EXISTING SELLER SIDEBAR --}}
    @include('components.seller.sidebar')

    {{-- EXISTING SELLER NAVBAR --}}
    @section('page-title', 'Order Status')
    @include('components.seller.navbar')

    {{-- =========================================================
         PAGE
    ========================================================== --}}
    <main id="order-status-page" class="min-h-screen bg-[#FCF8F6] pt-[104px] ml-[288px] transition-[margin] duration-100 ease-out">
        <div class="px-[36px] pt-[42px] pb-[22px]">

            {{-- PAGE HEADER --}}
            <div class="mb-[18px]">
                <h1 class="text-[28px] leading-tight font-semibold text-[#17120F]">Order Status</h1>
                <p class="mt-[3px] text-[19px] leading-tight text-[#999393]">View and manage new orders from your customers.</p>
            </div>

            {{-- =====================================================
                 ORDER CARD
            ====================================================== --}}
            <section class="overflow-hidden bg-white rounded-[12px] border border-[#F0E9E6] shadow-[0_2px_12px_rgba(42,20,15,0.04)]">

                {{-- TABS / SEARCH --}}
                <div class="flex items-center justify-between gap-4 min-h-[58px] border-b border-[#E8E2DF] px-[12px] pl-[14px]">
                    <div id="orderStatusTabs" class="flex items-center self-stretch shrink-0">
                        <button type="button" data-tab="all" class="order-status-tab active relative h-full px-[8px] mr-[48px] text-[14px] font-medium text-[#95908E] transition-colors duration-200 ease-out hover:text-[#6F6A68]">All Orders</button>
                        <button type="button" data-tab="new" class="order-status-tab relative h-full px-[8px] mr-[48px] text-[14px] font-medium text-[#95908E] transition-colors duration-200 ease-out hover:text-[#6F6A68]">New Orders</button>
                        <button type="button" data-tab="preparing" class="order-status-tab relative h-full px-[8px] mr-[48px] text-[14px] font-medium text-[#95908E] transition-colors duration-200 ease-out hover:text-[#6F6A68]">Preparing</button>
                        <button type="button" data-tab="to-ship" class="order-status-tab relative h-full px-[8px] text-[14px] font-medium text-[#95908E] transition-colors duration-200 ease-out hover:text-[#6F6A68]">To Ship</button>
                    </div>

                    <div class="relative w-[290px] shrink-0">
                        <input type="text" id="orderSearch" placeholder="Search order ID or customer name" autocomplete="off" class="w-full h-[35px] rounded-[8px] border border-[#D9D6D4] bg-white pl-[12px] pr-[38px] text-[12px] text-[#45403E] outline-none transition-all duration-200 placeholder:text-[#B2AFAD] focus:border-[#B8B0AC] focus:ring-[3px] focus:ring-[#7B1B1B]/5">
                        <svg class="absolute right-[10px] top-1/2 -translate-y-1/2 w-[18px] h-[18px] text-[#B7B3B1] pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="M20 20l-4-4"></path>
                        </svg>
                    </div>
                </div>

                {{-- TABLE HEADER --}}
                <div class="mx-[12px] mt-[18px] rounded-[10px] bg-[#FBE0DD] grid grid-cols-[1.55fr_1.55fr_2.05fr_1.05fr_1.05fr] items-center min-h-[50px] px-[20px] text-[12px] font-medium text-[#60100F]">
                    <div>Order ID</div>
                    <div>Customer</div>
                    <div>Items</div>
                    <div>Total Amount</div>
                    <div>Ordered At</div>
                </div>

                {{-- ORDER TABLE --}}
                <div id="orderStatusTable" class="mt-[4px]">

                    {{-- ROW 1 --}}
                    <article class="order-row grid grid-cols-[1.55fr_1.55fr_2.05fr_1.05fr_1.05fr] items-center min-h-[99px] px-[20px] border-b border-[#DDD9D7] transition-all duration-200 ease-out hover:bg-[#FFFBF9]" data-status="new" data-tab="new" data-search="#ORD-2026 Juan Dela Cruz 0917-123-4567">
                        <div class="flex items-center gap-[22px] min-w-0">
                            <div class="order-icon-box new"><img src="{{ asset('icons/seller/order-status/new-order.png') }}" alt="New Order"></div>
                            <div class="min-w-0">
                                <h3 class="order-id">#ORD-2025</h3>
                                <p class="order-status new-text"><span class="status-dot new-dot"></span>New Order</p>
                            </div>
                        </div>
                        <div class="min-w-0"><p class="customer-name">Juan Dela Cruz</p><p class="customer-phone">0917-123-4567</p></div>
                        <div class="flex items-center gap-[10px] min-w-0"><div class="mini-product"><div class="product-bag"></div></div><div class="mini-product"><div class="product-bag"></div></div><span class="more-items">+1 more</span></div>
                        <div><p class="amount">₱559.00</p><p class="payment">Payment: COD</p></div>
                        <div><p class="ordered-date">May 22, 2026</p><p class="ordered-time">10:34 AM</p></div>
                    </article>

                    {{-- ROW 2 --}}
                    <article class="order-row grid grid-cols-[1.55fr_1.55fr_2.05fr_1.05fr_1.05fr] items-center min-h-[99px] px-[20px] border-b border-[#DDD9D7] transition-all duration-200 ease-out hover:bg-[#FFFBF9]" data-status="preparing" data-tab="preparing" data-search="#ORD-2025 Juan Dela Cruz 0917-123-4567">
                        <div class="flex items-center gap-[22px] min-w-0">
                            <div class="order-icon-box preparing"><img src="{{ asset('icons/seller/order-status/processing.png') }}" alt="Preparing"></div>
                            <div class="min-w-0">
                                <h3 class="order-id">#ORD-2025</h3>
                                <p class="order-status preparing-text"><span class="status-dot preparing-dot"></span>Preparing</p>
                            </div>
                        </div>
                        <div class="min-w-0"><p class="customer-name">Juan Dela Cruz</p><p class="customer-phone">0917-123-4567</p></div>
                        <div class="flex items-center gap-[10px] min-w-0"><div class="mini-product"><div class="product-bag"></div></div><div class="mini-product"><div class="product-bag"></div></div><span class="more-items">+1 more</span></div>
                        <div><p class="amount">₱559.00</p><p class="payment">Payment: COD</p></div>
                        <div><p class="ordered-date">May 22, 2026</p><p class="ordered-time">10:34 AM</p></div>
                    </article>

                    {{-- ROW 3 --}}
                    <article class="order-row grid grid-cols-[1.55fr_1.55fr_2.05fr_1.05fr_1.05fr] items-center min-h-[99px] px-[20px] border-b border-[#DDD9D7] transition-all duration-200 ease-out hover:bg-[#FFFBF9]" data-status="to-ship" data-tab="to-ship" data-search="#ORD-2025 Juan Dela Cruz 0917-123-4567">
                        <div class="flex items-center gap-[22px] min-w-0">
                            <div class="order-icon-box ready"><img src="{{ asset('icons/seller/order-status/ready-to-ship.png') }}" alt="Ready to Ship"></div>
                            <div class="min-w-0">
                                <h3 class="order-id">#ORD-2025</h3>
                                <p class="order-status ready-text"><span class="status-dot ready-dot"></span>Ready to Ship</p>
                            </div>
                        </div>
                        <div class="min-w-0"><p class="customer-name">Juan Dela Cruz</p><p class="customer-phone">0917-123-4567</p></div>
                        <div class="flex items-center gap-[10px] min-w-0"><div class="mini-product"><div class="product-bag"></div></div><div class="mini-product"><div class="product-bag"></div></div><span class="more-items">+1 more</span></div>
                        <div><p class="amount">₱559.00</p><p class="payment">Payment: COD</p></div>
                        <div><p class="ordered-date">May 22, 2026</p><p class="ordered-time">10:34 AM</p></div>
                    </article>

                    {{-- ROW 4 --}}
                    <article class="order-row grid grid-cols-[1.55fr_1.55fr_2.05fr_1.05fr_1.05fr] items-center min-h-[99px] px-[20px] border-b border-[#DDD9D7] transition-all duration-200 ease-out hover:bg-[#FFFBF9]" data-status="new" data-tab="new" data-search="#ORD-2026 Maria Santos 0918-555-7777">
                        <div class="flex items-center gap-[22px] min-w-0">
                            <div class="order-icon-box new"><img src="{{ asset('icons/seller/order-status/new-order.png') }}" alt="New Order"></div>
                            <div class="min-w-0">
                                <h3 class="order-id">#ORD-2026</h3>
                                <p class="order-status new-text"><span class="status-dot new-dot"></span>New Order</p>
                            </div>
                        </div>
                        <div class="min-w-0"><p class="customer-name">Maria Santos</p><p class="customer-phone">0918-555-7777</p></div>
                        <div class="flex items-center gap-[10px] min-w-0"><div class="mini-product"><div class="product-bag"></div></div><div class="mini-product"><div class="product-bag"></div></div><span class="more-items">+1 more</span></div>
                        <div><p class="amount">₱559.00</p><p class="payment">Payment: COD</p></div>
                        <div><p class="ordered-date">May 23, 2026</p><p class="ordered-time">2:15 PM</p></div>
                    </article>

                    {{-- ROW 5 --}}
                    <article class="order-row grid grid-cols-[1.55fr_1.55fr_2.05fr_1.05fr_1.05fr] items-center min-h-[99px] px-[20px] border-b border-[#DDD9D7] transition-all duration-200 ease-out hover:bg-[#FFFBF9]" data-status="preparing" data-tab="preparing" data-search="#ORD-2027 Carlo Reyes 0919-222-8899">
                        <div class="flex items-center gap-[22px] min-w-0">
                            <div class="order-icon-box preparing"><img src="{{ asset('icons/seller/order-status/processing.png') }}" alt="Preparing"></div>
                            <div class="min-w-0">
                                <h3 class="order-id">#ORD-2027</h3>
                                <p class="order-status preparing-text"><span class="status-dot preparing-dot"></span>Preparing</p>
                            </div>
                        </div>
                        <div class="min-w-0"><p class="customer-name">Carlo Reyes</p><p class="customer-phone">0919-222-8899</p></div>
                        <div class="flex items-center gap-[10px] min-w-0"><div class="mini-product"><div class="product-bag"></div></div><div class="mini-product"><div class="product-bag"></div></div><span class="more-items">+1 more</span></div>
                        <div><p class="amount">₱559.00</p><p class="payment">Payment: COD</p></div>
                        <div><p class="ordered-date">May 24, 2026</p><p class="ordered-time">9:20 AM</p></div>
                    </article>

                    {{-- ROW 6 --}}
                    <article class="order-row grid grid-cols-[1.55fr_1.55fr_2.05fr_1.05fr_1.05fr] items-center min-h-[99px] px-[20px] transition-all duration-200 ease-out hover:bg-[#FFFBF9]" data-status="to-ship" data-tab="to-ship" data-search="#ORD-2028 Ana Garcia 0920-333-1212">
                        <div class="flex items-center gap-[22px] min-w-0">
                            <div class="order-icon-box ready"><img src="{{ asset('icons/seller/order-status/ready-to-ship.png') }}" alt="Ready to Ship"></div>
                            <div class="min-w-0">
                                <h3 class="order-id">#ORD-2028</h3>
                                <p class="order-status ready-text"><span class="status-dot ready-dot"></span>Ready to Ship</p>
                            </div>
                        </div>
                        <div class="min-w-0"><p class="customer-name">Ana Garcia</p><p class="customer-phone">0920-333-1212</p></div>
                        <div class="flex items-center gap-[10px] min-w-0"><div class="mini-product"><div class="product-bag"></div></div><div class="mini-product"><div class="product-bag"></div></div><span class="more-items">+1 more</span></div>
                        <div><p class="amount">₱559.00</p><p class="payment">Payment: COD</p></div>
                        <div><p class="ordered-date">May 24, 2026</p><p class="ordered-time">4:30 PM</p></div>
                    </article>
                </div>

                {{-- NO RESULTS --}}
                <div id="orderStatusNoResults" class="hidden px-[20px] py-[55px] text-center">
                    <p class="text-[15px] font-medium text-[#7D7774]">No orders found.</p>
                    <p class="mt-[4px] text-[12px] text-[#AAA6A4]">Try another search or status tab.</p>
                </div>

                {{-- PAGINATION --}}
                <div class="flex items-center justify-between min-h-[58px] px-[18px] border-t border-[#E4DFDD]">
                    <p class="text-[12px] text-[#8E8885]">Showing <span id="showingCount">6</span> out of 378 entries</p>
                    <div class="flex items-center gap-[3px]">
                        <button type="button" id="previousPage" class="pagination-button disabled" aria-label="Previous page">‹</button>
                        <button type="button" data-page="1" class="pagination-button current">1</button>
                        <button type="button" data-page="2" class="pagination-button">2</button>
                        <button type="button" data-page="3" class="pagination-button">3</button>
                        <button type="button" id="nextPage" class="pagination-button" aria-label="Next page">›</button>
                        <select id="itemsPerPage" class="ml-[10px] h-[31px] rounded-[7px] border border-[#ECC8C1] bg-[#FFF5F2] px-[8px] text-[11px] text-[#8C4B44] outline-none cursor-pointer">
                            <option value="7">Items per page: 7</option>
                            <option value="10">Items per page: 10</option>
                            <option value="20">Items per page: 20</option>
                        </select>
                    </div>
                </div>
            </section>
        </div>
    </main>

    {{-- =========================================================
         ORDER DETAILS MODAL
    ========================================================== --}}
    <div id="newOrderModal" class="ui-modal" aria-hidden="true">
        <div id="newOrderModalPanel" class="order-details-modal-panel" role="dialog" aria-modal="true" aria-labelledby="newOrderModalTitle">

            {{-- MODAL HEADER --}}
            <div class="modal-section modal-header-section">
                <h2 id="newOrderModalTitle" class="text-[19px] leading-none font-medium text-[#17120F]">Order Details</h2>
                <div class="flex items-center gap-[10px]">
                    <svg class="w-[30px] h-[30px]" viewBox="0 0 48 48" fill="none" aria-hidden="true">
                        <path d="M6 28h4V17h19v11h4" stroke="#D90000" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M29 20h7l6 7v7h-6" fill="#D90000" stroke="#D90000" stroke-width="3" stroke-linejoin="round"></path>
                        <circle cx="15" cy="35" r="4" fill="#D90000"></circle>
                        <circle cx="36" cy="35" r="4" fill="#D90000"></circle>
                        <path d="M4 35h7m16-7h10" stroke="#D90000" stroke-width="4" stroke-linecap="round"></path>
                    </svg>
                    <span class="text-[19px] leading-none font-bold text-[#17120F]">Ease Express</span>
                </div>
            </div>

            {{-- ORDER SUMMARY --}}
            <div class="modal-section modal-order-summary">
                <div class="flex items-center gap-[20px]">
                    <div id="orderModalIconBox" class="order-modal-status-box new">
                        <img id="orderModalStatusIcon" src="{{ asset('icons/seller/order-status/new-order.png') }}" alt="New Order" class="w-[58px] h-[58px] object-contain">
                    </div>
                    <div>
                        <p id="orderModalOrderId" class="text-[17px] leading-tight font-medium text-[#17120F]">#ORD-2025</p>
                        <p id="orderModalStatusText" class="mt-[7px] flex items-center gap-[8px] text-[12px] leading-tight new-text">
                            <span id="orderModalStatusDot" class="w-[9px] h-[9px] rounded-full bg-[#E21E1E] shrink-0"></span>
                            <span id="orderModalStatusLabel">New Order</span>
                        </p>
                    </div>
                </div>
                <div class="min-w-[190px]">
                    <p class="text-[13px] font-medium text-[#8A8582]">Ordered at</p>
                    <p id="orderModalOrderedDate" class="mt-[9px] text-[13px] text-[#17120F]">May 22, 2026</p>
                    <p id="orderModalOrderedTime" class="mt-[3px] text-[13px] text-[#17120F]">10:34 AM</p>
                </div>
            </div>

            {{-- CUSTOMER / PAYMENT --}}
            <div class="modal-section modal-customer-payment grid grid-cols-2 gap-[45px]">
                <div>
                    <h3 class="text-[16px] font-semibold text-[#17120F]">Customer Information</h3>
                    <div class="mt-[13px] space-y-[9px]">
                        <div class="flex items-center gap-[10px]"><svg class="w-[19px] h-[19px] shrink-0" viewBox="0 0 24 24" fill="none"><circle cx="8" cy="8" r="3" fill="#000"></circle><circle cx="16" cy="8" r="3" fill="#000"></circle><path d="M2 19c.8-3 3-5 6-5s5.2 2 6 5H2Z" fill="#000"></path><path d="M12 19c.7-2.5 2.5-4 5-4 2.7 0 4.5 1.5 5 4h-10Z" fill="#000"></path></svg><span class="text-[12px] text-[#17120F]">Juan Dela Cruz</span></div>
                        <div class="flex items-center gap-[10px]"><svg class="w-[19px] h-[19px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"></rect><path d="M3 7l9 7 9-7"></path></svg><span class="text-[12px] text-[#17120F]">juandelacruz@gmail.com</span></div>
                        <div class="flex items-center gap-[10px]"><svg class="w-[19px] h-[19px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7.5 4.5c.5 0 1 .3 1.3.8l1.7 3.1c.2.4.2 1-.1 1.4l-1.2 1.4a13 13 0 0 0 3.6 3.6l1.4-1.2c.4-.3 1-.4 1.4-.1l3.1 1.7c.5.3.8.8.8 1.3V19c0 .8-.7 1.5-1.5 1.5C10.6 20.5 3.5 13.4 3.5 4.5 3.5 3.7 4.2 3 5 3h2.5c0 .5 0 1 0 1.5Z"></path></svg><span class="text-[12px] text-[#17120F]">0917 123 4567</span></div>
                        <div class="flex items-center gap-[10px]"><svg class="w-[19px] h-[19px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s7-5.6 7-12a7 7 0 1 0-14 0c0 6.4 7 12 7 12Z"></path><circle cx="12" cy="9" r="2.3"></circle></svg><span class="text-[12px] text-[#17120F]">Calamba, Laguna</span></div>
                    </div>
                </div>
                <div>
                    <h3 class="text-[16px] font-semibold text-[#17120F]">Payment Method</h3>
                    <div class="mt-[18px] flex items-start gap-[15px]">
                        <svg class="w-[19px] h-[19px] mt-[1px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="6" width="18" height="13" rx="2"></rect><path d="M3 10h18"></path><path d="M7 14h4"></path></svg>
                        <div><p class="text-[14px] leading-tight text-[#17120F]">Payment: <span class="text-[#F07E23]">COD</span></p><p class="mt-[7px] text-[13px] leading-tight text-[#77716E]">Payment upon Delivery</p></div>
                    </div>
                </div>
            </div>

            {{-- ITEMS --}}
            <div class="modal-section modal-items-section">
                <h3 class="text-[15px] font-semibold text-[#17120F]">Items Ordered</h3>
                <div class="mt-[5px] h-[42px] rounded-[11px] bg-[#FBEDED] grid grid-cols-[2.25fr_1fr_1fr_1fr] items-center px-[24px] text-[12px] font-medium text-[#60100F]"><div>Item</div><div>Price</div><div>Quantity</div><div>Subtotal</div></div>
                <div class="modal-item-row"><div class="flex items-center gap-[16px]"><div class="modal-product-image"><div class="modal-product-bag"></div></div><span class="text-[14px] font-semibold text-[#17120F]">Wireless Bag</span></div><div class="text-[13px] text-[#17120F]">₱559.00</div><div class="text-[13px] text-[#17120F] text-center">1</div><div class="text-[13px] text-[#17120F]">₱559.00</div></div>
                <div class="modal-item-row"><div class="flex items-center gap-[16px]"><div class="modal-product-image"><div class="modal-product-bag"></div></div><span class="text-[14px] font-semibold text-[#17120F]">Wireless Bag</span></div><div class="text-[13px] text-[#17120F]">₱559.00</div><div class="text-[13px] text-[#17120F] text-center">1</div><div class="text-[13px] text-[#17120F]">₱559.00</div></div>
                <div class="flex justify-end pt-[10px]"><div class="w-[320px]"><div class="flex items-center justify-between"><span class="text-[13px] text-[#85807D]">Subtotal</span><span class="text-[13px] text-[#17120F]">₱559.00</span></div><div class="mt-[10px] flex items-center justify-between"><span class="text-[13px] text-[#85807D]">Shipping Fee</span><span class="text-[13px] text-[#17120F]">₱60.00</span></div><div class="mt-[12px] flex items-center justify-between"><span class="text-[13px] font-medium text-[#17120F]">Total Amount</span><span class="text-[20px] font-bold text-[#721313]">₱6,020.00</span></div></div></div>
            </div>

            {{-- CUSTOMER NOTES --}}
            <div class="modal-section modal-notes-section">
                <h3 class="text-[15px] font-semibold text-[#17120F]">Customer Notes</h3>
                <div class="mt-[8px] min-h-[42px] rounded-[10px] bg-[#ECECEC] px-[20px] py-[11px] flex items-center"><span class="text-[13px] text-[#77716E]">Please Handle with care. Thank you!</span></div>
            </div>

            {{-- =====================================================
                 TRACKING HISTORY
            ====================================================== --}}
            <section id="trackingHistorySection" class="tracking-history-section hidden">
                <h3 class="tracking-title">Tracking History</h3>
                <div id="trackingHistoryList" class="tracking-history-list"></div>
            </section>

            {{-- ACTION --}}
            <div class="modal-section modal-footer-section">
                <button type="button" id="orderModalActionButton" class="order-modal-action-button new-action"><span id="orderModalActionLabel">Prepare Order</span></button>
            </div>
        </div>
    </div>

    {{-- =========================================================
         SCHEDULE SHIPMENT MODAL
    ========================================================== --}}
    <div id="scheduleShipmentModal" class="ui-modal" aria-hidden="true">
        <div id="scheduleShipmentModalPanel" class="schedule-modal-panel" role="dialog" aria-modal="true" aria-labelledby="scheduleShipmentTitle">
            <div class="flex items-center justify-between px-[28px] py-[20px] border-b border-[#D9D5D3]">
                <div>
                    <h2 id="scheduleShipmentTitle" class="text-[19px] font-semibold text-[#17120F]">Schedule Shipment</h2>
                    <p class="mt-[4px] text-[12px] text-[#8A8582]">Choose when the courier should pick up this order.</p>
                </div>
                <button type="button" id="closeScheduleShipment" class="w-[34px] h-[34px] rounded-full flex items-center justify-center text-[#77716E] transition-colors duration-200 hover:bg-[#FFF2EE] hover:text-[#6A1616]" aria-label="Close schedule shipment modal">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M6 6l12 12"></path><path d="M18 6L6 18"></path></svg>
                </button>
            </div>

            <div class="px-[28px] py-[24px]">
                <div class="rounded-[14px] border border-[#E7E1DE] bg-[#FFFBF9] px-[18px] py-[15px]">
                    <div class="flex items-center justify-between gap-4">
                        <div><p class="text-[11px] text-[#8A8582]">Order</p><p id="scheduleOrderId" class="mt-[3px] text-[15px] font-semibold text-[#17120F]">#ORD-2025</p></div>
                        <div class="text-right"><p class="text-[11px] text-[#8A8582]">Status</p><p class="mt-[3px] inline-flex items-center gap-[7px] text-[12px] font-medium text-[#D98C00]"><span class="w-[8px] h-[8px] rounded-full bg-[#F2A100]"></span>Preparing</p></div>
                    </div>
                </div>

                <div class="mt-[20px]">
                    <label for="pickupDate" class="block mb-[7px] text-[13px] font-semibold text-[#2F2926]">Pickup Date</label>
                    <input id="pickupDate" type="date" class="schedule-input">
                </div>

                <div class="mt-[16px]">
                    <label for="pickupTime" class="block mb-[7px] text-[13px] font-semibold text-[#2F2926]">Pickup Time</label>
                    <input id="pickupTime" type="time" class="schedule-input">
                </div>

                <div class="mt-[18px] flex items-start gap-[10px] rounded-[11px] bg-[#FFF7DD] px-[14px] py-[12px]">
                    <svg class="w-4 h-4 mt-[1px] shrink-0 text-[#D99400]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 10v6"></path><path d="M12 7h.01"></path></svg>
                    <p class="text-[11px] leading-[1.5] text-[#876A18]">Make sure the pickup time gives your team enough time to prepare and hand over the order.</p>
                </div>
            </div>

            <div class="flex justify-end items-center gap-[10px] px-[28px] py-[18px] border-t border-[#ECE7E5]">
                <button type="button" id="cancelScheduleShipment" class="h-[42px] px-[18px] rounded-[9px] border border-[#D9D3D0] bg-white text-[13px] font-medium text-[#625D5A] transition-colors duration-200 hover:bg-[#FAF7F5]">Cancel</button>
                <button type="button" id="confirmScheduleShipment" class="h-[42px] px-[20px] rounded-[9px] bg-[#E6A21A] text-[13px] font-semibold text-white shadow-sm transition-all duration-200 hover:bg-[#D99105] active:scale-[0.98]">Schedule Pickup</button>
            </div>
        </div>
    </div>

    <style>
        /* =====================================================
           PAGE / TABLE
        ====================================================== */
        .order-status-tab::after{content:"";position:absolute;left:0;right:0;bottom:0;height:4px;border-radius:999px 999px 0 0;background:transparent;transition:background .2s ease}
        .order-status-tab.active{color:#9E241F}.order-status-tab.active::after{background:#9E241F}

        .order-icon-box{width:65px;height:65px;border-radius:10px;flex:0 0 65px;display:flex;align-items:center;justify-content:center;overflow:hidden}
        .order-icon-box img{width:65px;height:65px;display:block;object-fit:contain}
        .order-icon-box.new{background:#FDE6E9}.order-icon-box.preparing{background:#FFF0D9}.order-icon-box.ready{background:#DDF1D7}
        .order-id{font-size:16px;line-height:1.2;font-weight:500;color:#181514;margin:0;white-space:nowrap}
        .order-status{margin-top:7px;display:flex;align-items:center;gap:8px;font-size:12px;line-height:1.2}
        .status-dot{width:9px;height:9px;border-radius:999px;flex:0 0 9px}
        .new-text{color:#CF2525}.new-dot{background:#E21E1E}.preparing-text{color:#F59B0B}.preparing-dot{background:#FF9D00}.ready-text{color:#0F9C1E}.ready-dot{background:#08A11D}
        .customer-name{margin:0;font-size:14px;line-height:1.2;font-weight:500;color:#1D1917;white-space:nowrap}.customer-phone{margin:6px 0 0;font-size:13px;line-height:1.2;color:#77716E;white-space:nowrap}
        .amount{margin:0;font-size:14px;line-height:1.2;font-weight:500;color:#17120F;white-space:nowrap}.payment{margin:6px 0 0;font-size:11px;line-height:1.2;color:#F07E23;white-space:nowrap}
        .ordered-date{margin:0;font-size:13px;line-height:1.2;color:#17120F;white-space:nowrap}.ordered-time{margin:4px 0 0;font-size:12px;line-height:1.2;color:#17120F;white-space:nowrap}
        .mini-product{width:44px;height:44px;border:1px solid #DDDAD8;border-radius:10px;background:#F8F8F8;display:flex;align-items:center;justify-content:center;flex:0 0 44px;overflow:hidden}
        .product-bag{position:relative;width:29px;height:23px;background:#2A2A2A;border-radius:4px 4px 6px 6px;box-shadow:inset 0 -2px 0 rgba(0,0,0,.15)}
        .product-bag::before{content:"";position:absolute;left:6px;top:-5px;width:17px;height:8px;border:2px solid #2A2A2A;border-bottom:none;border-radius:9px 9px 0 0}.product-bag::after{content:"";position:absolute;left:4px;top:5px;width:21px;height:3px;border-radius:999px;background:#363636}.more-items{font-size:13px;color:#17120F;white-space:nowrap}
        .pagination-button{width:28px;height:28px;display:inline-flex;align-items:center;justify-content:center;border-radius:6px;font-size:11px;color:#615A57;transition:all .18s ease}.pagination-button:hover:not(.disabled){background:#FFF2EE;color:#8D211D}.pagination-button.current{background:#FFC8B9;color:#7B1B1B;font-weight:600}.pagination-button.disabled{opacity:.35;pointer-events:none}

        /* =====================================================
           MODAL SYSTEM
           FADE ONLY — NO TRANSFORM / SCALE / SLIDE
        ====================================================== */
        .ui-modal{
            position:fixed;
            inset:0;
            z-index:120;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:10px;
            background:rgba(0,0,0,.25);
            backdrop-filter:blur(2px);
            opacity:0;
            visibility:hidden;
            pointer-events:none;
            transition:opacity .20s ease-out, visibility 0s linear .20s;
        }
        .ui-modal.modal-open{
            opacity:1;
            visibility:visible;
            pointer-events:auto;
            transition:opacity .20s ease-out, visibility 0s linear 0s;
        }
        .ui-modal > *{
            transform:none !important;
            animation:none !important;
        }
        .order-details-modal-panel{
            position:relative;
            width:916px;
            max-width:calc(100vw - 20px);
            max-height:calc(100vh - 24px);
            overflow-y:auto;
            overflow-x:hidden;
            scrollbar-width:none;
            -ms-overflow-style:none;
            border-radius:28px;
            background:#fff;
            box-shadow:0 20px 60px rgba(35,14,11,.18);
            outline:none;
        }
        .order-details-modal-panel::-webkit-scrollbar{display:none;width:0;height:0}
        .schedule-modal-panel{
            position:relative;
            width:560px;
            max-width:calc(100vw - 32px);
            max-height:calc(100vh - 32px);
            overflow-y:auto;
            overflow-x:hidden;
            scrollbar-width:none;
            -ms-overflow-style:none;
            border-radius:24px;
            background:#fff;
            box-shadow:0 20px 60px rgba(35,14,11,.18);
        }
        .schedule-modal-panel::-webkit-scrollbar{display:none;width:0;height:0}

        .modal-section{flex:0 0 auto}
        .modal-header-section{height:72px;display:flex;align-items:center;justify-content:space-between;padding:0 35px;border-bottom:1px solid #D9D5D3}
        .modal-order-summary{min-height:108px;display:flex;align-items:center;justify-content:space-between;padding:0 30px;border-bottom:1px solid #D9D5D3}
        .order-modal-status-box{width:65px;height:65px;flex:0 0 65px;border-radius:10px;display:flex;align-items:center;justify-content:center;overflow:hidden}.order-modal-status-box.new{background:#FDE6E9}.order-modal-status-box.preparing{background:#FFF0D9}.order-modal-status-box.ready{background:#DDF1D7}
        .modal-customer-payment{padding:20px 35px 14px;min-height:170px}
        .modal-items-section{padding:0 35px 10px}
        .modal-item-row{height:66px;display:grid;grid-template-columns:2.25fr 1fr 1fr 1fr;align-items:center;padding:0 19px;border-bottom:1px solid #D9D5D3}.modal-product-image{width:48px;height:48px;flex:0 0 48px;border-radius:10px;border:1px solid #DDDAD8;background:#F8F8F8;display:flex;align-items:center;justify-content:center;overflow:hidden}
        .modal-product-bag{position:relative;width:34px;height:24px;background:#292929;border-radius:4px 4px 6px 6px;box-shadow:inset 0 -2px 0 rgba(0,0,0,.15)}.modal-product-bag::before{content:"";position:absolute;left:8px;top:-6px;width:18px;height:9px;border:2px solid #292929;border-bottom:none;border-radius:9px 9px 0 0}.modal-product-bag::after{content:"";position:absolute;left:4px;top:5px;width:26px;height:3px;border-radius:999px;background:#363636}
        .modal-notes-section{padding:12px 35px 12px}.modal-footer-section{padding:4px 35px 18px;display:flex;justify-content:flex-end}
        .order-modal-action-button{height:45px;padding:0 28px;border-radius:9px;color:white;font-size:15px;font-weight:600;box-shadow:0 2px 6px rgba(0,0,0,.06);transition:background-color .2s ease, transform .2s ease}.order-modal-action-button:hover{transform:translateY(-1px)}.order-modal-action-button:active{transform:scale(.98)}.order-modal-action-button.new-action{background:#9E241F}.order-modal-action-button.new-action:hover{background:#861D19}.order-modal-action-button.preparing-action{background:#E6A21A}.order-modal-action-button.preparing-action:hover{background:#D99105}.order-modal-action-button.ready-action{background:#A7A7A7;cursor:default}.order-modal-action-button.ready-action:hover{transform:none}

        /* =====================================================
           TRACKING HISTORY
        ====================================================== */
        .tracking-history-section{padding:16px 35px 10px}.tracking-history-section.hidden{display:none}.tracking-title{margin:0;font-size:16px;font-weight:600;color:#17120F}.tracking-history-list{margin-top:12px;position:relative;padding-bottom:2px}.tracking-history-list::before{content:"";position:absolute;left:15px;top:15px;bottom:17px;width:1px;background:#D8D8D8}
        .tracking-item{position:relative;display:grid;grid-template-columns:32px 95px minmax(0,1fr);column-gap:12px;min-height:56px}.tracking-icon-wrap{position:relative;z-index:2;width:30px;height:30px;border-radius:999px;background:#E8F1FB;display:flex;align-items:center;justify-content:center}.tracking-icon-wrap.active{background:#185B8C}.tracking-icon-wrap svg{width:16px;height:16px}.tracking-time{text-align:right;padding-top:1px;font-size:10px;line-height:1.35;color:#17120F}.tracking-time .date{display:block}.tracking-time .time{display:block}.tracking-content{padding-top:0}.tracking-content .event-title{font-size:11px;line-height:1.35;font-weight:600;color:#17120F}.tracking-content .event-description{margin-top:2px;font-size:10px;line-height:1.45;color:#45403E}
        .tracking-empty{padding:10px 0 4px;font-size:11px;color:#8A8582}

        .schedule-input{width:100%;height:46px;border-radius:10px;border:1px solid #D9D3D0;background:#fff;padding:0 13px;font-size:13px;color:#17120F;outline:none;transition:border-color .2s ease, box-shadow .2s ease}.schedule-input:focus{border-color:#B7B0AD;box-shadow:0 0 0 3px rgba(165,42,42,.05)}

        @media(max-width:900px){
            #order-status-page > div{padding-left:20px;padding-right:20px}
            .order-status-tab{margin-right:18px}
            #orderSearch{width:230px}
        }
        @media(max-width:760px){
            #order-status-page{margin-left:0}
            #order-status-page > div{padding-left:16px;padding-right:16px}
            section{overflow-x:auto}
            #orderStatusTabs,#orderSearch{flex:0 0 auto}
            .order-row,#order-status-page section > div.grid{min-width:1000px}
            .order-details-modal-panel{width:calc(100vw - 16px);max-width:calc(100vw - 16px);max-height:calc(100vh - 16px);border-radius:20px}
            .modal-header-section{padding:0 20px}.modal-order-summary{padding:0 20px}.modal-customer-payment{padding-left:20px;padding-right:20px;gap:25px}.modal-items-section{padding-left:20px;padding-right:20px}.modal-notes-section{padding-left:20px;padding-right:20px}.tracking-history-section{padding-left:20px;padding-right:20px}.modal-footer-section{padding-left:20px;padding-right:20px}
            .schedule-modal-panel{width:calc(100vw - 16px);max-width:calc(100vw - 16px);border-radius:20px}
        }
        @media(prefers-reduced-motion:reduce){.ui-modal,.order-modal-action-button,.schedule-input,.order-status-tab::after{transition:none!important}}
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.getElementById('sellerSidebar');
        const page = document.getElementById('order-status-page');
        const tabs = document.querySelectorAll('.order-status-tab');
        const rows = Array.from(document.querySelectorAll('.order-row'));
        const searchInput = document.getElementById('orderSearch');
        const noResults = document.getElementById('orderStatusNoResults');
        const showingCount = document.getElementById('showingCount');
        const previousPage = document.getElementById('previousPage');
        const nextPage = document.getElementById('nextPage');
        const pageButtons = document.querySelectorAll('.pagination-button[data-page]');

        let activeTab = 'all';

        /* ---------------------------------------------------------
           Sidebar-aware page offset
        --------------------------------------------------------- */
        function syncPageOffset() {
            if (!page) return;
            if (window.innerWidth <= 760) {
                page.style.marginLeft = '0px';
                return;
            }
            const sidebarRight = sidebar ? Math.max(0, sidebar.getBoundingClientRect().right) : 288;
            page.style.marginLeft = sidebarRight + 'px';
        }
        syncPageOffset();
        window.addEventListener('resize', syncPageOffset);
        if (sidebar && typeof ResizeObserver !== 'undefined') {
            const resizeObserver = new ResizeObserver(syncPageOffset);
            resizeObserver.observe(sidebar);
        }

        /* ---------------------------------------------------------
           Filter
        --------------------------------------------------------- */
        function filterOrders() {
            const query = (searchInput?.value || '').trim().toLowerCase();
            let visibleCount = 0;

            rows.forEach(function (row) {
                const rowSearch = (row.dataset.search || '').toLowerCase();
                const rowStatus = row.dataset.status || '';
                const visible =
                    (activeTab === 'all' || rowStatus === activeTab) &&
                    (!query || rowSearch.includes(query));

                row.classList.toggle('hidden', !visible);
                if (visible) visibleCount++;
            });

            if (showingCount) showingCount.textContent = visibleCount;
            if (noResults) noResults.classList.toggle('hidden', visibleCount !== 0);
        }

        searchInput?.addEventListener('input', filterOrders);
        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                tabs.forEach(item => item.classList.remove('active'));
                tab.classList.add('active');
                activeTab = tab.dataset.tab || 'all';
                filterOrders();
            });
        });

        /* ---------------------------------------------------------
           Pagination visual UX
        --------------------------------------------------------- */
        function setCurrentPage(targetPage) {
            pageButtons.forEach(function (item) {
                item.classList.toggle('current', Number(item.dataset.page) === targetPage);
            });
            if (previousPage) previousPage.classList.toggle('disabled', targetPage === 1);
        }
        pageButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                setCurrentPage(Number(button.dataset.page));
            });
        });
        previousPage?.addEventListener('click', function () {
            const current = document.querySelector('.pagination-button.current[data-page]');
            const target = Math.max(1, Number(current?.dataset.page || 1) - 1);
            setCurrentPage(target);
        });
        nextPage?.addEventListener('click', function () {
            const current = document.querySelector('.pagination-button.current[data-page]');
            const target = Math.min(3, Number(current?.dataset.page || 1) + 1);
            setCurrentPage(target);
        });

        /* =========================================================
           MODAL REFERENCES
        ========================================================== */
        const orderDetailsModal = document.getElementById('newOrderModal');
        const orderDetailsPanel = document.getElementById('newOrderModalPanel');
        const orderModalActionButton = document.getElementById('orderModalActionButton');
        const orderModalActionLabel = document.getElementById('orderModalActionLabel');
        const orderModalIconBox = document.getElementById('orderModalIconBox');
        const orderModalStatusIcon = document.getElementById('orderModalStatusIcon');
        const orderModalStatusText = document.getElementById('orderModalStatusText');
        const orderModalStatusDot = document.getElementById('orderModalStatusDot');
        const orderModalStatusLabel = document.getElementById('orderModalStatusLabel');
        const orderModalOrderId = document.getElementById('orderModalOrderId');
        const orderModalOrderedDate = document.getElementById('orderModalOrderedDate');
        const orderModalOrderedTime = document.getElementById('orderModalOrderedTime');
        const trackingHistorySection = document.getElementById('trackingHistorySection');
        const trackingHistoryList = document.getElementById('trackingHistoryList');

        const scheduleShipmentModal = document.getElementById('scheduleShipmentModal');
        const scheduleShipmentPanel = document.getElementById('scheduleShipmentModalPanel');
        const closeScheduleShipmentButton = document.getElementById('closeScheduleShipment');
        const cancelScheduleShipmentButton = document.getElementById('cancelScheduleShipment');
        const confirmScheduleShipmentButton = document.getElementById('confirmScheduleShipment');
        const pickupDateInput = document.getElementById('pickupDate');
        const pickupTimeInput = document.getElementById('pickupTime');
        const scheduleOrderId = document.getElementById('scheduleOrderId');

        let selectedOrderRow = null;
        let lastFocusedElement = null;
        let bodyLockCount = 0;

        /* ---------------------------------------------------------
           Body lock helper
        --------------------------------------------------------- */
        function lockBody() {
            bodyLockCount++;
            document.body.classList.add('overflow-hidden');
        }
        function unlockBody() {
            bodyLockCount = Math.max(0, bodyLockCount - 1);
            if (bodyLockCount === 0) document.body.classList.remove('overflow-hidden');
        }

        /* ---------------------------------------------------------
           Date / time helpers
        --------------------------------------------------------- */
        function pad(number) {
            return String(number).padStart(2, '0');
        }
        function toDateInputValue(date) {
            return date.getFullYear() + '-' + pad(date.getMonth() + 1) + '-' + pad(date.getDate());
        }
        function formatDateDisplay(value) {
            if (!value) return '';
            const date = new Date(value + 'T00:00:00');
            if (Number.isNaN(date.getTime())) return value;
            return date.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
        }
        function formatTimeDisplay(value) {
            if (!value) return '';
            const [hoursRaw, minutes] = value.split(':');
            const hours = Number(hoursRaw);
            if (Number.isNaN(hours)) return value;
            const suffix = hours >= 12 ? 'PM' : 'AM';
            const twelveHour = hours % 12 || 12;
            return twelveHour + ':' + minutes + ' ' + suffix;
        }
        function nowTrackingStamp() {
            const now = new Date();
            return {
                date: now.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }),
                time: now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' })
            };
        }

        /* ---------------------------------------------------------
           Tracking data
        --------------------------------------------------------- */
        function makeTrackingEvent(type, date, time) {
            return { type, date, time };
        }
        function getTrackingEvents(row) {
            try {
                return JSON.parse(row.dataset.trackingHistory || '[]');
            } catch (error) {
                return [];
            }
        }
        function setTrackingEvents(row, events) {
            row.dataset.trackingHistory = JSON.stringify(events);
        }
        function ensureInitialTracking(row) {
            const status = row.dataset.status;
            if (status === 'preparing' && !getTrackingEvents(row).length) {
                setTrackingEvents(row, [makeTrackingEvent('prepared', 'May 22, 2026', '11:45 PM')]);
            }
            if (status === 'to-ship' && !getTrackingEvents(row).length) {
                setTrackingEvents(row, [
                    makeTrackingEvent('prepared', 'May 22, 2026', '11:45 PM'),
                    makeTrackingEvent('ready', row.querySelector('.ordered-date')?.textContent?.trim() || 'May 22, 2026', row.querySelector('.ordered-time')?.textContent?.trim() || '11:45 PM')
                ]);
            }
        }
        rows.forEach(ensureInitialTracking);

        function trackingIcon(type, active) {
            const stroke = active ? '#FFFFFF' : '#185B8C';
            if (type === 'prepared') {
                return '<svg viewBox="0 0 24 24" fill="none" stroke="' + stroke + '" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M5 8h14v11H5z"></path><path d="M8 8V5h8v3"></path><path d="M9 12h6"></path></svg>';
            }
            if (type === 'ready') {
                return '<svg viewBox="0 0 24 24" fill="none" stroke="' + stroke + '" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 16h14"></path><path d="M14 7h4l3 4v5h-3"></path><circle cx="7" cy="17" r="2"></circle><circle cx="18" cy="17" r="2"></circle><path d="M3 8h8"></path></svg>';
            }
            return '<svg viewBox="0 0 24 24" fill="none" stroke="' + stroke + '" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle></svg>';
        }
        function renderTrackingHistory(row) {
            if (!trackingHistorySection || !trackingHistoryList) return;

            const events = getTrackingEvents(row);
            if (!events.length) {
                trackingHistorySection.classList.add('hidden');
                trackingHistoryList.innerHTML = '';
                return;
            }

            trackingHistorySection.classList.remove('hidden');
            trackingHistoryList.innerHTML = '';

            events.forEach(function (event, index) {
                const isActive = index === events.length - 1;
                const wrapper = document.createElement('div');
                wrapper.className = 'tracking-item';

                let title = 'Order Prepared';
                let description = 'You have prepared your order';
                let type = 'prepared';

                if (event.type === 'ready') {
                    title = 'Ready to Ship';
                    description = 'Your order is ready for courier pickup.';
                    type = 'ready';
                }

                wrapper.innerHTML =
                    '<div class="tracking-icon-wrap ' + (isActive ? 'active' : '') + '">' + trackingIcon(type, isActive) + '</div>' +
                    '<div class="tracking-time"><span class="date">' + event.date + '</span><span class="time">' + event.time + '</span></div>' +
                    '<div class="tracking-content"><div class="event-title">' + title + '</div><div class="event-description">' + description + '</div></div>';

                trackingHistoryList.appendChild(wrapper);
            });
        }

        /* ---------------------------------------------------------
           Modal state update
        --------------------------------------------------------- */
        function syncOrderModalFromRow(row) {
            const status = row.dataset.status || 'new';
            const isPreparing = status === 'preparing';
            const isReady = status === 'to-ship';

            if (orderModalOrderId) orderModalOrderId.textContent = row.querySelector('.order-id')?.textContent?.trim() || '#ORD-2025';
            if (orderModalOrderedDate) orderModalOrderedDate.textContent = row.querySelector('.ordered-date')?.textContent?.trim() || 'May 22, 2026';
            if (orderModalOrderedTime) orderModalOrderedTime.textContent = row.querySelector('.ordered-time')?.textContent?.trim() || '10:34 AM';

            let label = 'New Order';
            if (isPreparing) label = 'Preparing';
            if (isReady) label = 'Ready to Ship';

            if (orderModalStatusLabel) orderModalStatusLabel.textContent = label;
            if (orderModalStatusText) {
                orderModalStatusText.classList.remove('new-text', 'preparing-text', 'ready-text');
                orderModalStatusText.classList.add(isPreparing ? 'preparing-text' : (isReady ? 'ready-text' : 'new-text'));
            }
            if (orderModalStatusDot) {
                orderModalStatusDot.style.backgroundColor = isPreparing ? '#FF9D00' : (isReady ? '#08A11D' : '#E21E1E');
            }
            if (orderModalIconBox) {
                orderModalIconBox.classList.remove('new', 'preparing', 'ready');
                orderModalIconBox.classList.add(isPreparing ? 'preparing' : (isReady ? 'ready' : 'new'));
            }
            if (orderModalStatusIcon) {
                orderModalStatusIcon.src = isPreparing
                    ? "{{ asset('icons/seller/order-status/processing.png') }}"
                    : (isReady ? "{{ asset('icons/seller/order-status/ready-to-ship.png') }}" : "{{ asset('icons/seller/order-status/new-order.png') }}");
                orderModalStatusIcon.alt = label;
            }

            if (orderModalActionButton && orderModalActionLabel) {
                orderModalActionButton.classList.remove('new-action', 'preparing-action', 'ready-action');
                if (isPreparing) {
                    orderModalActionButton.classList.add('preparing-action');
                    orderModalActionLabel.textContent = 'Schedule Shipment';
                    orderModalActionButton.disabled = false;
                } else if (isReady) {
                    orderModalActionButton.classList.add('ready-action');
                    orderModalActionLabel.textContent = 'Ready to Ship';
                    orderModalActionButton.disabled = true;
                } else {
                    orderModalActionButton.classList.add('new-action');
                    orderModalActionLabel.textContent = 'Prepare Order';
                    orderModalActionButton.disabled = false;
                }
            }

            renderTrackingHistory(row);
        }

        /* ---------------------------------------------------------
           Fade-only modal open/close
        --------------------------------------------------------- */
        function openOrderDetailsModal(row) {
            if (!row || !orderDetailsModal || !orderDetailsPanel) return;
            selectedOrderRow = row;
            lastFocusedElement = document.activeElement;
            syncOrderModalFromRow(row);
            orderDetailsModal.classList.add('modal-open');
            orderDetailsModal.setAttribute('aria-hidden', 'false');
            lockBody();
        }

        function closeOrderDetailsModal(keepBodyLocked = false) {
            if (!orderDetailsModal) return;
            orderDetailsModal.classList.remove('modal-open');
            orderDetailsModal.setAttribute('aria-hidden', 'true');
            if (!keepBodyLocked) unlockBody();
            setTimeout(function () {
                if (!keepBodyLocked && lastFocusedElement && typeof lastFocusedElement.focus === 'function') {
                    lastFocusedElement.focus();
                }
            }, 210);
        }

        function openScheduleShipmentModal() {
            if (!scheduleShipmentModal || !scheduleShipmentPanel) return;
            if (selectedOrderRow && scheduleOrderId) {
                scheduleOrderId.textContent = selectedOrderRow.querySelector('.order-id')?.textContent?.trim() || '#ORD-2025';
            }

            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            if (pickupDateInput) {
                pickupDateInput.min = toDateInputValue(tomorrow);
                pickupDateInput.value = pickupDateInput.value || toDateInputValue(tomorrow);
            }
            if (pickupTimeInput) pickupTimeInput.value = pickupTimeInput.value || '10:00';

            scheduleShipmentModal.classList.add('modal-open');
            scheduleShipmentModal.setAttribute('aria-hidden', 'false');
            lockBody();
        }

        function closeScheduleShipmentModal() {
            if (!scheduleShipmentModal) return;
            scheduleShipmentModal.classList.remove('modal-open');
            scheduleShipmentModal.setAttribute('aria-hidden', 'true');
            unlockBody();
        }

        /* ---------------------------------------------------------
           Clickable rows
        --------------------------------------------------------- */
        rows.forEach(function (row) {
            row.addEventListener('click', function () {
                if (row.dataset.status === 'new' || row.dataset.status === 'preparing' || row.dataset.status === 'to-ship') {
                    openOrderDetailsModal(row);
                }
            });
            row.addEventListener('keydown', function (event) {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    openOrderDetailsModal(row);
                }
            });
            row.setAttribute('tabindex', '0');
        });

        /* ---------------------------------------------------------
           Order details backdrop
        --------------------------------------------------------- */
        orderDetailsModal?.addEventListener('click', function (event) {
            if (event.target === orderDetailsModal) closeOrderDetailsModal();
        });

        /* ---------------------------------------------------------
           Main action
        --------------------------------------------------------- */
        orderModalActionButton?.addEventListener('click', function () {
            if (!selectedOrderRow) return;

            const currentStatus = selectedOrderRow.dataset.status;

            /* NEW -> PREPARING: keep modal open and reveal Tracking History. */
            if (currentStatus === 'new') {
                orderModalActionButton.disabled = true;
                orderModalActionLabel.textContent = 'Preparing...';

                setTimeout(function () {
                    const stamp = nowTrackingStamp();

                    selectedOrderRow.dataset.status = 'preparing';
                    selectedOrderRow.dataset.tab = 'preparing';

                    const iconBox = selectedOrderRow.querySelector('.order-icon-box');
                    const icon = iconBox?.querySelector('img');
                    const status = selectedOrderRow.querySelector('.order-status');

                    iconBox?.classList.remove('new', 'ready');
                    iconBox?.classList.add('preparing');
                    if (icon) {
                        icon.src = "{{ asset('icons/seller/order-status/processing.png') }}";
                        icon.alt = 'Preparing';
                    }
                    if (status) {
                        status.className = 'order-status preparing-text';
                        status.innerHTML = '<span class="status-dot preparing-dot"></span>Preparing';
                    }

                    setTrackingEvents(selectedOrderRow, [makeTrackingEvent('prepared', stamp.date, stamp.time)]);

                    activeTab = 'preparing';
                    tabs.forEach(tab => tab.classList.toggle('active', tab.dataset.tab === 'preparing'));
                    filterOrders();
                    syncOrderModalFromRow(selectedOrderRow);
                }, 400);

                return;
            }

            /* PREPARING -> SCHEDULE SHIPMENT */
            if (currentStatus === 'preparing') {
                closeOrderDetailsModal(true);
                setTimeout(function () {
                    openScheduleShipmentModal();
                    /* body stays locked because the close above retained the lock */
                    unlockBody();
                }, 210);
            }
        });

        /* ---------------------------------------------------------
           Schedule modal controls
        --------------------------------------------------------- */
        closeScheduleShipmentButton?.addEventListener('click', closeScheduleShipmentModal);
        cancelScheduleShipmentButton?.addEventListener('click', closeScheduleShipmentModal);
        scheduleShipmentModal?.addEventListener('click', function (event) {
            if (event.target === scheduleShipmentModal) closeScheduleShipmentModal();
        });

        confirmScheduleShipmentButton?.addEventListener('click', function () {
            if (!selectedOrderRow) return;

            const pickupDate = pickupDateInput?.value || '';
            const pickupTime = pickupTimeInput?.value || '';
            if (!pickupDate || !pickupTime) {
                alert('Please select a pickup date and time.');
                return;
            }

            /*
             * IMPORTANT:
             * Capture the exact moment the seller clicks
             * "Schedule Pickup".
             *
             * This timestamp is ONLY for Tracking History
             * (Ready to Ship).
             *
             * The pickupDate / pickupTime selected in the
             * schedule modal remains separate and is used
             * ONLY for the To Ship table.
             */
            const scheduledStamp = nowTrackingStamp();

            confirmScheduleShipmentButton.disabled = true;
            confirmScheduleShipmentButton.textContent = 'Scheduling...';

            setTimeout(function () {
                const row = selectedOrderRow;
                const displayDate = formatDateDisplay(pickupDate);
                const displayTime = formatTimeDisplay(pickupTime);

                row.dataset.status = 'to-ship';
                row.dataset.tab = 'to-ship';
                row.dataset.pickupDate = pickupDate;
                row.dataset.pickupTime = pickupTime;

                const iconBox = row.querySelector('.order-icon-box');
                const icon = iconBox?.querySelector('img');
                const status = row.querySelector('.order-status');
                const dateElement = row.querySelector('.ordered-date');
                const timeElement = row.querySelector('.ordered-time');

                iconBox?.classList.remove('new', 'preparing');
                iconBox?.classList.add('ready');
                if (icon) {
                    icon.src = "{{ asset('icons/seller/order-status/ready-to-ship.png') }}";
                    icon.alt = 'Ready to Ship';
                }
                if (status) {
                    status.className = 'order-status ready-text';
                    status.innerHTML = '<span class="status-dot ready-dot"></span>Ready to Ship';
                }
                if (dateElement) dateElement.textContent = displayDate;
                if (timeElement) timeElement.textContent = displayTime;

                /*
                 * TRACKING HISTORY:
                 * Use the real timestamp of the Schedule Pickup action,
                 * NOT the future pickup schedule selected by the seller.
                 */
                const events = getTrackingEvents(row);
                events.push(
                    makeTrackingEvent(
                        'ready',
                        scheduledStamp.date,
                        scheduledStamp.time
                    )
                );
                setTrackingEvents(row, events);

                activeTab = 'to-ship';
                tabs.forEach(tab => tab.classList.toggle('active', tab.dataset.tab === 'to-ship'));
                filterOrders();

                /* Close schedule modal with fade only. */
                closeScheduleShipmentModal();

                /* Re-open order details after the schedule fade so seller can immediately see Ready to Ship tracking. */
                setTimeout(function () {
                    openOrderDetailsModal(row);
                    syncOrderModalFromRow(row);
                }, 220);

                confirmScheduleShipmentButton.disabled = false;
                confirmScheduleShipmentButton.textContent = 'Schedule Pickup';

            }, 400);
        });

        /* ---------------------------------------------------------
           Escape: close the top-most modal only
        --------------------------------------------------------- */
        document.addEventListener('keydown', function (event) {
            if (event.key !== 'Escape') return;

            if (scheduleShipmentModal?.classList.contains('modal-open')) {
                closeScheduleShipmentModal();
                return;
            }
            if (orderDetailsModal?.classList.contains('modal-open')) {
                closeOrderDetailsModal();
            }
        });

        /* ---------------------------------------------------------
           Initial filter
        --------------------------------------------------------- */
        filterOrders();
    });
    </script>
</body>
</html>
