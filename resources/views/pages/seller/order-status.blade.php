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
    - Modal content remains scrollable on smaller screens
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

    @vite(['resources/css/app.css', 'resources/css/seller/order-status.css', 'resources/js/seller/order-status.js'])
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
                    <p class="text-[12px] text-[#8E8885]">Showing <span id="showingCount">0</span> out of <span id="totalEntriesCount">0</span> entries</p>
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


</body>
</html>
