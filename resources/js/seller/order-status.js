import QRCode from 'qrcode';

document.addEventListener('DOMContentLoaded', function () {
    const configElement = document.getElementById('sellerOrderStatusConfig');
    const config = configElement ? JSON.parse(configElement.textContent) : {};
    const sidebar = document.getElementById('sellerSidebar');
    const page = document.getElementById('order-status-page');
    const tabs = document.querySelectorAll('.order-status-tab');
    const cancellationSubtabs = document.getElementById('cancellationSubtabs');
    const cancellationTabs = document.querySelectorAll('.cancellation-subtab');
    const orderTable = document.getElementById('orderStatusTable');
    let rows = [];
    const searchInput = document.getElementById('orderSearch');
    const noResults = document.getElementById('orderStatusNoResults');
    const flashContainer = document.getElementById('orderStatusFlashContainer');
    const showingCount = document.getElementById('showingCount');
    const totalEntriesCount = document.getElementById('totalEntriesCount');
    const previousPage = document.getElementById('previousPage');
    const nextPage = document.getElementById('nextPage');
    const pageButtons = document.querySelectorAll('.pagination-button[data-page]');

    let activeTab = 'all';
    let activeCancellationTab = 'cancel-pending';
    let currentPage = 1;
    let itemsPerPage = Number(document.getElementById('itemsPerPage')?.value || 7);

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, (character) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
        })[character]);
    }

    // Matches the Registrations flash card: icon, title, message and close button.
    let orderFlashTimer = null;
    function showOrderFlash(message, type = 'success', title = '') {
        if (!flashContainer) return;
        clearTimeout(orderFlashTimer);
        const positive = type !== 'error' && type !== 'rejected';
        const flashType = positive ? 'approved' : (type === 'error' ? 'error' : 'rejected');
        const heading = title || (type === 'error' ? 'Action Failed' : (positive ? 'Action Successful' : 'Request Rejected'));
        const iconMarkup = positive
            ? '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 12 4 4L19 6"/></svg>'
            : '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6 6 18"/></svg>';
        flashContainer.className = `order-status-flash-container registration-decision-flash ${flashType}`;
        flashContainer.innerHTML = `
            <div class="order-status-flash-icon ${positive ? 'positive' : 'negative'}">${iconMarkup}</div>
            <div class="order-status-flash-content">
                <p class="order-status-flash-title">${escapeHtml(heading)}</p>
                <p class="order-status-flash-message">${escapeHtml(message)}</p>
            </div>
            <button type="button" class="order-status-flash-close" aria-label="Close notification">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6 6 18"/></svg>
            </button>`;
        const hide = () => flashContainer.classList.remove('show');
        flashContainer.querySelector('.order-status-flash-close')?.addEventListener('click', () => {
            clearTimeout(orderFlashTimer);
            hide();
        });
        flashContainer.classList.add('show');
        orderFlashTimer = setTimeout(hide, 3800);
    }

    function productImageUrl(item) {
        const photos = item.product?.photos;
        const photo = Array.isArray(photos)
            ? photos.find((value) => typeof value === 'string' && value.trim())
            : null;
        if (!photo) return null;
        if (/^(data:|https?:\/\/)/i.test(photo)) return photo;

        const path = photo.replace(/^\/+/, '').replace(/^storage\//, '');
        return path ? `/storage/${path.split('/').map(encodeURIComponent).join('/')}` : null;
    }

    function normalizeOrderStatus(order) {
        const rawStatus = String(order?.status || '')
            .trim()
            .toLowerCase()
            .replace(/\s+/g, '_');

        const cancellationStatus = String(
            order?.cancellation_status ??
            order?.cancellation_request_status ??
            order?.cancel_request_status ??
            order?.cancel_status ??
            ''
        )
            .trim()
            .toLowerCase()
            .replace(/\s+/g, '_');

        if (['cancelled', 'canceled'].includes(rawStatus)) {
            return 'cancelled';
        }

        if (
            [
                'cancellation_rejected',
                'cancel_rejected',
                'rejected_cancellation',
                'cancellation_request_rejected'
            ].includes(rawStatus)
            ||
            [
                'rejected',
                'declined',
                'denied'
            ].includes(cancellationStatus)
        ) {
            return 'cancel-rejected';
        }

        if (
            [
                'cancellation_pending',
                'pending_cancellation',
                'cancel_requested',
                'cancellation_requested'
            ].includes(rawStatus)
            ||
            [
                'pending',
                'requested',
                'request_pending',
                'for_approval'
            ].includes(cancellationStatus)
        ) {
            return 'cancel-pending';
        }

        if (rawStatus === 'pending') {
            return 'new';
        }

        if (rawStatus === 'to_ship') {
            return 'to-ship';
        }

        return rawStatus.replaceAll('_', '-');
    }

    function renderOrderRows(orders) {
        if (!orderTable) return;

        if (!Array.isArray(orders)) {
            rows =
                Array.from(
                    orderTable.querySelectorAll(
                        '.order-row'
                    )
                );

            return;
        }

        const excludedOrderStatuses = new Set([
            'in_transit',
            'in-transit',
            'out_for_delivery',
            'out-for-delivery',
            'delivered',
        ]);

        orders = orders.filter((order) => {
            const raw = String(order?.status || '').trim().toLowerCase();
            return !excludedOrderStatuses.has(raw);
        });

        const statusInfo = {
            new: { label: 'New Order', style: 'new', icon: 'new-order.png' },
            preparing: { label: 'Preparing', style: 'preparing', icon: 'processing.png' },
            'to-ship': { label: 'Ready to Ship', style: 'ready', icon: 'ready-to-ship.png' },
            cancelled: { label: 'Cancelled', style: 'cancelled', icon: 'new-order.png' },
            'cancel-pending': { label: 'Cancellation Pending', style: 'cancel-pending', icon: 'new-order.png' },
            'cancel-rejected': { label: 'Cancellation Rejected', style: 'cancel-rejected', icon: 'new-order.png' },
        };
        const money = (value) => `₱${Number(value || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 })}`;

        orderTable.innerHTML = orders.map((order) => {
            const normalizedStatus = normalizeOrderStatus(order);
            const status = statusInfo[normalizedStatus] || {
                label: String(normalizedStatus || 'unknown').replaceAll('-', ' '),
                style: 'new',
                icon: 'new-order.png',
            };
            const items = order.items || [];
            const itemNames = items.map((item) => item.product_name);
            const createdAt = order.created_at ? new Date(order.created_at) : null;
            const pickupDate = order.pickup_date_display || order.pickup_date || '';
            const pickupTime = order.pickup_time_display || order.pickup_time || '';
            const cancellationActor = normalizedStatus === 'cancelled' ? order.cancellation_actor : '';
            const pickupWasScheduled = (order.status_history || []).some((record) =>
                ['to_ship', 'ready_to_ship', 'shipment_scheduled'].includes(String(record.to_status || '').toLowerCase())
            );
            const orderNumber = order.order_number || `ORD-${order.id}`;
            const customer = order.delivery_name || order.buyer?.name || 'Customer';
            const phone = order.delivery_phone || order.buyer?.contact_no || '';
            const date = pickupDate && normalizedStatus === 'to-ship'
                ? formatDateDisplay(pickupDate)
                : (createdAt && !Number.isNaN(createdAt.getTime())
                ? new Intl.DateTimeFormat('en-US', { month: 'short', day: '2-digit', year: 'numeric' }).format(createdAt)
                : '');
            const time = pickupTime && normalizedStatus === 'to-ship'
                ? formatTimeDisplay(pickupTime)
                : (createdAt && !Number.isNaN(createdAt.getTime())
                ? new Intl.DateTimeFormat('en-US', { hour: 'numeric', minute: '2-digit' }).format(createdAt)
                : '');
            const itemPreview = items.slice(0, 2).map((item) =>
                `<div class="mini-product" title="${escapeHtml(item.product_name)}">${productImageUrl(item)
                    ? `<img src="${escapeHtml(productImageUrl(item))}" alt="${escapeHtml(item.product_name)}">`
                    : '<div class="product-bag"></div>'}</div>`
            ).join('');
            const moreItems = items.length > 2
                ? `<span class="more-items">+${items.length - 2} more</span>`
                : (items.length === 1 ? `<span class="text-[12px] text-[#625D5A] truncate">${escapeHtml(items[0].product_name)}</span>` : '');

            return `
                <article class="order-row grid grid-cols-[1.55fr_1.55fr_2.05fr_1.05fr_1.05fr] items-center min-h-[99px] px-[20px] border-b border-[#DDD9D7] transition-all duration-200 ease-out hover:bg-[#FFFBF9]"
                    data-order-id="${Number(order.id)}"
                    data-status="${escapeHtml(normalizedStatus)}"
                    data-tab="${escapeHtml(normalizedStatus)}"
                    data-pickup-date="${escapeHtml(pickupDate)}"
                    data-pickup-time="${escapeHtml(pickupTime)}"
                    data-cancellation-actor="${escapeHtml(cancellationActor)}"
                    data-cancellation-request-id="${escapeHtml(order.latest_cancellation_request?.id || '')}"
                    data-pickup-was-scheduled="${pickupWasScheduled ? 'true' : 'false'}"
                    data-cancellation-requested-at="${escapeHtml(order.cancellation_requested_at || order.cancellation_request_created_at || order.cancel_requested_at || '')}"
                    data-cancelled-at="${escapeHtml(order.cancelled_at || order.canceled_at || '')}"
                    data-cancellation-rejected-at="${escapeHtml(order.cancellation_rejected_at || order.cancel_rejected_at || '')}"
                    data-status-history="${escapeHtml(JSON.stringify(order.status_history || []))}"
                    data-prepared-at="${escapeHtml(order.prepared_at || order.preparing_at || '')}"
                    data-shipment-scheduled-at="${escapeHtml(order.shipment_scheduled_at || order.pickup_scheduled_at || order.scheduled_at || '')}"
                    data-ordered-at="${escapeHtml(order.created_at || '')}"
                    data-hardcoded-cancellation="${order.is_hardcoded_cancellation_request ? 'true' : 'false'}"
                    data-search="${escapeHtml(`${orderNumber} ${customer} ${phone} ${itemNames.join(' ')}`)}"
                    tabindex="0"
                    role="button"
                    aria-label="Open order ${escapeHtml(orderNumber)} details">
                    <div class="flex items-center gap-[22px] min-w-0">
                        <div class="order-icon-box ${status.style}"><img src="/icons/seller/order-status/${status.icon}" alt="${escapeHtml(status.label)}"></div>
                        <div class="min-w-0">
                            <h3 class="order-id">#${escapeHtml(orderNumber)}</h3>
                            <p class="order-status ${status.style}-text"><span class="status-dot ${status.style}-dot"></span>${escapeHtml(status.label)}</p>
                            ${cancellationActor ? `<p class="order-cancellation-detail mt-1 text-[10px] text-[#8A8582]">${pickupWasScheduled ? 'Pickup cancelled · ' : ''}Cancelled by ${escapeHtml(cancellationActor.charAt(0).toUpperCase() + cancellationActor.slice(1))}</p>` : ''}
                        </div>
                    </div>
                    <div class="min-w-0"><p class="customer-name">${escapeHtml(customer)}</p><p class="customer-phone">${escapeHtml(phone)}</p></div>
                    <div class="flex items-center gap-[10px] min-w-0">${itemPreview}${moreItems || (items.length === 0 ? '<span class="text-[12px] text-[#8A8582]">No item details</span>' : '')}</div>
                    <div><p class="amount">${money(order.total)}</p><p class="payment">Payment: ${escapeHtml(order.payment_method || 'Cash on Delivery')}</p></div>
                    <div><p class="ordered-date">${escapeHtml(date)}</p><p class="ordered-time">${escapeHtml(time)}</p></div>
                </article>`;
        }).join('');

        rows = Array.from(orderTable.querySelectorAll('.order-row'));
    }

    /*
     * Laravel may serialize $orders either as:
     *   1) a plain array, or
     *   2) a paginator/resource object containing a `data` array.
     *
     * Calling renderOrderRows() with the paginator object causes
     * `orders.map is not a function`, which stops ALL JS on this page.
     * That is why both the tabs and Order Details modal appeared dead.
     */
    const initialOrders =
        Array.isArray(
            config.orders
        )
            ? config.orders
            : (
                Array.isArray(
                    config.orders?.data
                )
                    ? config.orders.data
                    : null
            );

    const hardcodedCancellationRequest = {
        id: 999001,
        order_number: 'ORD-CAN-0001',
        status: 'cancel_requested',
        cancellation_status: 'pending',
        is_hardcoded_cancellation_request: true,
        delivery_name: 'Maria Santos',
        delivery_phone: '0918-555-7777',
        delivery_address: 'Brgy. San Roque, Santa Cruz, Laguna',
        payment_method: 'Cash on Delivery',
        total: 1249,
        created_at: '2026-10-11T10:30:00+08:00',
        buyer: {
            name: 'Maria Santos',
            email: 'maria.santos@example.com',
            contact_no: '0918-555-7777'
        },
        items: [
            {
                product_name: 'Classic Oversized Shirt',
                variation: 'Classic',
                color: 'Black',
                size: 'Medium',
                unit_price: 1249,
                quantity: 1,
                product: {
                    photos: []
                }
            }
        ],
        cancellation_reason:
            'Changed my mind and would like to cancel before shipment.',
        cancellation_requested_at:
            '2026-10-11T18:42:00+08:00'
    };

    const hardcodedOrderMap =
        new Map([
            [
                String(
                    hardcodedCancellationRequest.id
                ),
                hardcodedCancellationRequest
            ]
        ]);

    if (
        initialOrders !==
        null
    ) {
        const demoAlreadyExists =
            initialOrders.some(
                function (order) {
                    return (
                        String(
                            order?.id
                        ) ===
                        String(
                            hardcodedCancellationRequest.id
                        )
                    );
                }
            );

        renderOrderRows(
            demoAlreadyExists
                ? initialOrders
                : [
                    ...initialOrders,
                    hardcodedCancellationRequest
                ]
        );
    } else {
        /*
         * If no usable JSON collection was supplied, keep the Blade-rendered
         * rows instead of wiping the table.
         */
        rows =
            Array.from(
                orderTable?.querySelectorAll(
                    '.order-row'
                ) ||
                []
            );
    }

    rows.forEach(
        function (row) {
            row.setAttribute(
                'tabindex',
                row.getAttribute(
                    'tabindex'
                ) ||
                '0'
            );

            row.setAttribute(
                'role',
                row.getAttribute(
                    'role'
                ) ||
                'button'
            );
        }
    );

    function syncPageOffset() {
        if (!page) return;

        if (window.innerWidth <= 760) {
            page.style.marginLeft = '0px';
            return;
        }

        const sidebarRight = sidebar
            ? Math.max(0, sidebar.getBoundingClientRect().right)
            : 288;

        page.style.marginLeft = sidebarRight + 'px';
    }

    syncPageOffset();

    window.addEventListener(
        'resize',
        syncPageOffset
    );

    if (
        sidebar &&
        typeof ResizeObserver !== 'undefined'
    ) {
        const resizeObserver =
            new ResizeObserver(
                syncPageOffset
            );

        resizeObserver.observe(
            sidebar
        );
    }

    function syncCancellationSubtabs() {
        if (!cancellationSubtabs) return;

        cancellationSubtabs.classList.toggle(
            'hidden',
            activeTab !== 'cancellations'
        );
    }

    function rowMatchesActiveFilter(row, query) {
        const rowSearch = (row.dataset.search || '').toLowerCase();
        const rowStatus = row.dataset.status || '';

        let statusMatches = false;

        if (activeTab === 'all') {
            statusMatches = true;
        } else if (activeTab === 'cancellations') {
            statusMatches =
                rowStatus === activeCancellationTab;
        } else {
            statusMatches =
                rowStatus === activeTab;
        }

        return (
            statusMatches &&
            (
                !query ||
                rowSearch.includes(query)
            )
        );
    }

    function updatePaginationControls(totalFiltered) {
        const totalPages =
            Math.max(
                1,
                Math.ceil(
                    totalFiltered /
                    itemsPerPage
                )
            );

        currentPage =
            Math.min(
                currentPage,
                totalPages
            );

        pageButtons.forEach(
            function (button, index) {
                const pageNumber =
                    index + 1;

                button.dataset.page =
                    String(pageNumber);

                button.textContent =
                    String(pageNumber);

                button.classList.toggle(
                    'hidden',
                    pageNumber > totalPages
                );

                button.classList.toggle(
                    'current',
                    pageNumber === currentPage
                );
            }
        );

        previousPage?.classList.toggle(
            'disabled',
            currentPage <= 1
        );

        nextPage?.classList.toggle(
            'disabled',
            currentPage >= totalPages
        );

        return totalPages;
    }

    function filterOrders() {
        const query =
            (searchInput?.value || '')
                .trim()
                .toLowerCase();

        const matchingRows =
            rows.filter(
                function (row) {
                    return rowMatchesActiveFilter(
                        row,
                        query
                    );
                }
            );

        const totalFiltered =
            matchingRows.length;

        const totalPages =
            updatePaginationControls(
                totalFiltered
            );

        const pageStart =
            (currentPage - 1) *
            itemsPerPage;

        const pageEnd =
            pageStart +
            itemsPerPage;

        rows.forEach(
            function (row) {
                row.classList.add(
                    'hidden'
                );
            }
        );

        matchingRows
            .slice(
                pageStart,
                pageEnd
            )
            .forEach(
                function (row) {
                    row.classList.remove(
                        'hidden'
                    );
                }
            );

        if (showingCount) {
            showingCount.textContent =
                totalFiltered === 0
                    ? '0'
                    : String(
                        Math.min(
                            pageEnd,
                            totalFiltered
                        ) -
                        pageStart
                    );
        }

        if (totalEntriesCount) {
            totalEntriesCount.textContent =
                String(
                    totalFiltered
                );
        }

        if (noResults) {
            noResults.classList.toggle(
                'hidden',
                totalFiltered !== 0
            );

            const title =
                noResults.querySelector(
                    'p:first-child'
                );

            const subtitle =
                noResults.querySelector(
                    'p:last-child'
                );

            if (title && subtitle) {
                if (activeTab === 'cancellations') {
                    if (
                        activeCancellationTab ===
                        'cancel-pending'
                    ) {
                        title.textContent =
                            'No pending cancellation requests.';

                        subtitle.textContent =
                            query
                                ? 'Try another search keyword.'
                                : 'Buyer cancellation requests will appear here.';
                    } else if (
                        activeCancellationTab ===
                        'cancel-rejected'
                    ) {
                        title.textContent =
                            'No rejected cancellation requests.';

                        subtitle.textContent =
                            query
                                ? 'Try another search keyword.'
                                : 'Rejected cancellation requests will appear here.';
                    } else {
                        title.textContent =
                            'No cancelled orders found.';

                        subtitle.textContent =
                            query
                                ? 'Try another search keyword.'
                                : 'Cancelled orders will appear here.';
                    }
                } else {
                    title.textContent =
                        'No orders found.';

                    subtitle.textContent =
                        'Try another search or status tab.';
                }
            }
        }

        syncCancellationSubtabs();

        return totalPages;
    }

    searchInput?.addEventListener(
        'input',
        function () {
            currentPage = 1;
            filterOrders();
        }
    );

    tabs.forEach(
        function (tab) {
            tab.addEventListener(
                'click',
                function () {
                    tabs.forEach(
                        item =>
                            item.classList.remove(
                                'active'
                            )
                    );

                    tab.classList.add(
                        'active'
                    );

                    activeTab =
                        tab.dataset.tab ||
                        'all';

                    currentPage = 1;

                    if (
                        activeTab ===
                        'cancellations'
                    ) {
                        cancellationTabs.forEach(
                            function (item) {
                                item.classList.toggle(
                                    'active',
                                    item.dataset.cancellationTab ===
                                        activeCancellationTab
                                );
                            }
                        );
                    }

                    filterOrders();
                }
            );
        }
    );

    cancellationTabs.forEach(
        function (tab) {
            tab.addEventListener(
                'click',
                function () {
                    cancellationTabs.forEach(
                        item =>
                            item.classList.remove(
                                'active'
                            )
                    );

                    tab.classList.add(
                        'active'
                    );

                    activeCancellationTab =
                        tab.dataset.cancellationTab ||
                        'cancelled';

                    activeTab =
                        'cancellations';

                    tabs.forEach(
                        item =>
                            item.classList.toggle(
                                'active',
                                item.dataset.tab ===
                                    'cancellations'
                            )
                    );

                    currentPage = 1;
                    filterOrders();
                }
            );
        }
    );

    function setCurrentPage(
        targetPage
    ) {
        currentPage =
            Math.max(
                1,
                Number(
                    targetPage ||
                    1
                )
            );

        filterOrders();
    }

    pageButtons.forEach(
        function (button) {
            button.addEventListener(
                'click',
                function () {
                    if (
                        button.classList.contains(
                            'hidden'
                        )
                    ) {
                        return;
                    }

                    setCurrentPage(
                        Number(
                            button.dataset.page ||
                            1
                        )
                    );
                }
            );
        }
    );

    previousPage?.addEventListener(
        'click',
        function () {
            if (
                previousPage.classList.contains(
                    'disabled'
                )
            ) {
                return;
            }

            setCurrentPage(
                currentPage - 1
            );
        }
    );

    nextPage?.addEventListener(
        'click',
        function () {
            if (
                nextPage.classList.contains(
                    'disabled'
                )
            ) {
                return;
            }

            setCurrentPage(
                currentPage + 1
            );
        }
    );

    document.getElementById(
        'itemsPerPage'
    )?.addEventListener(
        'change',
        function (event) {
            itemsPerPage =
                Math.max(
                    1,
                    Number(
                        event.target.value ||
                        7
                    )
                );

            currentPage = 1;
            filterOrders();
        }
    );


    const orderDetailsModal =
        document.getElementById(
            'newOrderModal'
        );

    const orderDetailsPanel =
        document.getElementById(
            'newOrderModalPanel'
        );

    const orderModalActionButton =
        document.getElementById(
            'orderModalActionButton'
        );

    const orderModalActionLabel =
        document.getElementById(
            'orderModalActionLabel'
        );
    const printWaybillButton = document.getElementById('printOrderWaybillButton');

    const acceptCancellationRequestButton =
        document.getElementById(
            'acceptCancellationRequestButton'
        );

    const rejectCancellationRequestButton =
        document.getElementById(
            'rejectCancellationRequestButton'
        );

    const rejectCancellationRequestModal =
        document.getElementById(
            'rejectCancellationRequestModal'
        );

    const cancellationRejectionOtherReason =
        document.getElementById(
            'cancellationRejectionOtherReason'
        );

    const cancellationRejectionError =
        document.getElementById(
            'cancellationRejectionError'
        );

    const confirmRejectCancellationRequest =
        document.getElementById(
            'confirmRejectCancellationRequest'
        );

    const cancellationRejectionCounter =
        document.getElementById(
            'cancellationRejectionCounter'
        );

    const trackingHistoryTitle =
        document.getElementById(
            'trackingHistoryTitle'
        );

    const orderModalIconBox =
        document.getElementById(
            'orderModalIconBox'
        );

    const orderModalStatusIcon =
        document.getElementById(
            'orderModalStatusIcon'
        );

    const orderModalStatusText =
        document.getElementById(
            'orderModalStatusText'
        );

    const orderModalStatusDot =
        document.getElementById(
            'orderModalStatusDot'
        );

    const orderModalStatusLabel =
        document.getElementById(
            'orderModalStatusLabel'
        );

    const orderModalOrderId =
        document.getElementById(
            'orderModalOrderId'
        );

    const orderModalOrderedDate =
        document.getElementById(
            'orderModalOrderedDate'
        );

    const orderModalOrderedTime =
        document.getElementById(
            'orderModalOrderedTime'
        );
    function formatCurrency(value) {
        return `₱${Number(value || 0).toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        })}`;
    }

    function waybillDocument(order, shipment, scanUrl) {
        const items = order.items || [];
        const itemRows = items.map((item) => `
            <tr>
                <td>${escapeHtml(item.product_name || item.product?.name || 'Product')}${[item.variation, item.color, item.size].filter(Boolean).length
                    ? `<small>${escapeHtml([item.variation, item.color, item.size].filter(Boolean).join(' · '))}</small>`
                    : ''}</td>
                <td class="qty">${Number(item.quantity || 0)}</td>
            </tr>`).join('');
        const customerName = order.delivery_name || order.buyer?.name || 'Customer';
        const buyerContact = order.delivery_phone || order.buyer?.contact_no || 'Not provided';
        const sellerName = order.seller?.store_name || 'ShopEase Seller';
        const sellerAddress = [
            order.seller?.house_number,
            order.seller?.street,
            order.seller?.barangay,
            order.seller?.municipality,
            order.seller?.province,
        ].filter((part) => typeof part === 'string' && part.trim()).join(', ') || 'Seller address not provided';
        const address = order.delivery_address || '—';
        const trackingNumber = shipment.tracking_number || order.order_number || `ORD-${order.id}`;
        const paymentMethod = order.payment_method || 'Cash on Delivery';
        const isCod = /cash|cod/i.test(paymentMethod);
        const amountLabel = isCod ? 'COLLECT' : 'ORDER TOTAL';
        const routeHint = (order.delivery_address || '').split(',').slice(-2).join(',').trim() || 'PHILIPPINES';

        return `<!DOCTYPE html>
            <html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Waybill ${escapeHtml(trackingNumber)}</title>
            <style>
                @page { size: 100mm 150mm; margin: 0; }
                * { box-sizing: border-box; }
                body { margin: 0; color: #111; background: #fff; font: 10px Arial, Helvetica, sans-serif; }
                .label { width: 100mm; min-height: 150mm; margin: 0 auto; border: 1px solid #111; }
                .topbar { display: flex; justify-content: space-between; align-items: center; padding: 3mm; border-bottom: 1px solid #111; }
                .brand { font-size: 18px; font-weight: 900; letter-spacing: -.6px; }
                .courier { text-align: right; font-size: 12px; font-weight: 800; }
                .service { margin-top: 2px; font-size: 8px; font-weight: 700; letter-spacing: 1px; }
                .route { display: grid; grid-template-columns: 1fr auto; align-items: center; padding: 2.5mm 3mm; border-bottom: 1px solid #111; background: #f1f1f1; }
                .route-label { font-size: 8px; font-weight: 700; text-transform: uppercase; }
                .route-code { font-size: 15px; font-weight: 900; text-transform: uppercase; }
                .order-ref { font-size: 8px; font-weight: 700; text-align: right; }
                section { padding: 2.5mm 3mm; border-bottom: 1px solid #111; }
                .section-title { margin-bottom: 1.5mm; font-size: 8px; font-weight: 800; letter-spacing: .6px; text-transform: uppercase; }
                .recipient-line { display: flex; justify-content: space-between; align-items: flex-start; gap: 4px; }
                .recipient { font-size: 16px; font-weight: 900; }
                .phone { white-space: nowrap; font-size: 12px; font-weight: 800; }
                .buyer-contact { margin-top: 1mm; font-size: 9px; }
                .buyer-contact strong { text-transform: uppercase; font-size: 8px; }
                .address { margin-top: 1.5mm; line-height: 1.35; overflow-wrap: anywhere; }
                .tracking-block { display: grid; grid-template-columns: 1fr 27mm; gap: 3mm; align-items: center; }
                .tracking { margin: 1mm 0; font-size: 16px; font-weight: 900; letter-spacing: .4px; overflow-wrap: anywhere; }
                .qr { width: 27mm; height: 27mm; padding: 1mm; border: 1px solid #111; }
                .qr img { display: block; width: 100%; height: 100%; }
                .scan-caption { margin-top: 1mm; font-size: 7px; font-weight: 700; text-align: center; }
                .from { display: flex; justify-content: space-between; gap: 5px; }
                .from-name { font-size: 11px; font-weight: 800; }
                .payment { display: flex; justify-content: space-between; align-items: center; }
                .payment-badge { padding: 1.5mm 2mm; border: 1px solid #111; font-size: 10px; font-weight: 900; }
                .amount { font-size: 18px; font-weight: 900; text-align: right; white-space: nowrap; }
                .amount-caption { font-size: 7px; font-weight: 800; text-align: right; }
                table { width: 100%; border-collapse: collapse; }
                td { padding: 1.2mm 0; border-top: 1px solid #bbb; vertical-align: top; }
                td small { display: block; margin-top: 1px; color: #444; font-size: 8px; }
                .qty { width: 12mm; text-align: right; font-weight: 800; }
                .footer { padding: 2mm 3mm; font-size: 7px; text-align: center; }
                @media screen { body { padding: 16px; background: #e8e8e8; } .label { box-shadow: 0 2px 12px #0002; } }
                @media print { .label { break-inside: avoid; } }
            </style></head><body>
            <main class="label">
                <div class="topbar"><div class="brand">ShopEase</div><div class="courier">${escapeHtml(shipment.courier || 'Ease Express')}<div class="service">STANDARD DELIVERY</div></div></div>
                <div class="route"><div><div class="route-label">Destination</div><div class="route-code">${escapeHtml(routeHint)}</div></div><div class="order-ref">ORDER<br>${escapeHtml(order.order_number || `ORD-${order.id}`)}</div></div>
                <section><div class="section-title">Buyer</div><div class="recipient">${escapeHtml(customerName)}</div><div class="buyer-contact"><strong>Contact number:</strong> ${escapeHtml(buyerContact)}</div><div class="address">${escapeHtml(address)}</div></section>
                <section class="tracking-block"><div><div class="section-title">Tracking number</div><div class="tracking">${escapeHtml(trackingNumber)}</div><div class="section-title">Parcel tracking QR</div></div><div><div class="qr"><img src="${escapeHtml(scanUrl)}" alt="Scannable parcel tracking QR code"></div><div class="scan-caption">SCAN FOR PARCEL UPDATES</div></div></section>
                <section class="from"><div><div class="section-title">Seller</div><div class="from-name">${escapeHtml(sellerName)}</div><div class="address">${escapeHtml(sellerAddress)}</div></div><div style="text-align:right"><div class="section-title">Payment</div><div class="payment-badge">${isCod ? 'COD' : 'PAID'}</div></div></section>
                <section class="payment"><div><div class="section-title">Parcel contents</div><table><tbody>${itemRows || '<tr><td>Order parcel</td><td class="qty">1</td></tr>'}</tbody></table></div><div><div class="amount-caption">${amountLabel}</div><div class="amount">${formatCurrency(order.total)}</div></div></section>
                <div class="footer">Handle with care · Keep this waybill attached to the parcel</div>
            </main></body></html>`;
    }

    async function printWaybill() {
        if (!selectedOrderRow || !printWaybillButton) return;
        const printWindow = window.open('', '_blank');
        if (!printWindow) {
            showOrderFlash(
                'Please allow pop-ups to print the waybill.',
                'error'
            );
            return;
        }

        printWaybillButton.disabled = true;
        printWaybillButton.textContent = 'Preparing Waybill…';
        printWindow.document.write('<!doctype html><title>Preparing waybill</title><p style="font:16px Arial;padding:24px">Preparing your waybill…</p>');

        try {
            const response = await fetch(`${config.ordersUrl}/${selectedOrderRow.dataset.orderId}/waybill`, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    Authorization: `Bearer ${config.apiToken || ''}`,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });
            const result = await response.json();
            if (!response.ok) throw new Error(result.message || `Unable to create waybill (${response.status}).`);

            const qrImage = await QRCode.toDataURL(result.scan_url, {
                errorCorrectionLevel: 'H',
                margin: 2,
                width: 320,
            });
            printWindow.document.open();
            printWindow.document.write(waybillDocument(result.order, result.shipment, qrImage));
            printWindow.document.close();
            printWindow.addEventListener('load', () => {
                printWindow.focus();
                printWindow.print();
                showOrderFlash(
                    'Waybill is ready to print.',
                    'success'
                );
            }, { once: true });
        } catch (error) {
            printWindow.close();
            alert(error.message);
        } finally {
            printWaybillButton.disabled = false;
            printWaybillButton.textContent = 'Print Waybill';
        }
    }

    printWaybillButton?.addEventListener('click', printWaybill);

    function populateHardcodedCancellationDetails(
        row
    ) {
        const order =
            hardcodedOrderMap.get(
                String(
                    row?.dataset?.orderId ||
                    ''
                )
            );

        if (!order) {
            return false;
        }

        const buyer =
            order.buyer ||
            {};

        if (order.cancellation_requested_at && !row.dataset.cancellationRequestedAt) {
            row.dataset.cancellationRequestedAt = order.cancellation_requested_at;
        }

        if (order.cancelled_at) {
            row.dataset.cancelledAt = order.cancelled_at;
        }

        if (order.cancellation_rejected_at) {
            row.dataset.cancellationRejectedAt = order.cancellation_rejected_at;
        }

        if (order.rejection_reason) {
            row.dataset.rejectionReason = order.rejection_reason;
        }

        const setText =
            function (
                id,
                value
            ) {
                const element =
                    document.getElementById(
                        id
                    );

                if (element) {
                    element.textContent =
                        value ||
                        '—';
                }
            };

        setText(
            'orderModalCustomerName',
            order.delivery_name ||
            buyer.name
        );

        setText(
            'orderModalCustomerEmail',
            buyer.email
        );

        setText(
            'orderModalCustomerPhone',
            order.delivery_phone ||
            buyer.contact_no
        );

        setText(
            'orderModalCustomerAddress',
            order.delivery_address
        );

        setText(
            'orderModalPaymentMethod',
            order.payment_method
        );

        setText(
            'orderModalPaymentDescription',
            'Payment upon Delivery'
        );

        const items =
            order.items ||
            [];

        const subtotal =
            items.reduce(
                function (
                    sum,
                    item
                ) {
                    return (
                        sum +
                        (
                            Number(
                                item.unit_price ||
                                0
                            ) *
                            Number(
                                item.quantity ||
                                0
                            )
                        )
                    );
                },
                0
            );

        const itemsSection =
            document.querySelector(
                '.modal-items-section'
            );

        if (itemsSection) {
            itemsSection.innerHTML = `
                <h3 class="text-[15px] font-semibold text-[#17120F]">Items Ordered</h3>
                <div class="mt-[5px] h-[42px] rounded-[11px] bg-[#FBEDED] grid grid-cols-[2.25fr_1fr_1fr_1fr] items-center px-[24px] text-[12px] font-medium text-[#60100F]">
                    <div>Item</div><div>Price</div><div>Quantity</div><div>Subtotal</div>
                </div>

                ${items.map((item) => {
                    const itemSubtotal =
                        Number(
                            item.unit_price ||
                            0
                        ) *
                        Number(
                            item.quantity ||
                            0
                        );

                    const options =
                        [
                            item.variation,
                            item.color,
                            item.size
                        ]
                        .filter(Boolean)
                        .join(' · ');

                    return `
                        <div class="modal-item-row">
                            <div class="flex items-center gap-[16px]">
                                <div class="modal-product-image">
                                    <div class="modal-product-bag"></div>
                                </div>
                                <span class="text-[14px] font-semibold text-[#17120F]">
                                    ${escapeHtml(item.product_name)}
                                    ${options ? `<small class="block font-normal">${escapeHtml(options)}</small>` : ''}
                                </span>
                            </div>

                            <div class="text-[13px] text-[#17120F]">
                                ${formatCurrency(item.unit_price)}
                            </div>

                            <div class="text-[13px] text-[#17120F] text-center">
                                ${Number(item.quantity)}
                            </div>

                            <div class="text-[13px] text-[#17120F]">
                                ${formatCurrency(itemSubtotal)}
                            </div>
                        </div>
                    `;
                }).join('')}

                <div class="flex justify-end pt-[10px]">
                    <div class="w-[320px]">
                        <div class="flex items-center justify-between">
                            <span class="text-[13px] text-[#85807D]">Subtotal</span>
                            <span class="text-[13px] text-[#17120F]">${formatCurrency(subtotal)}</span>
                        </div>

                        <div class="mt-[10px] flex items-center justify-between">
                            <span class="text-[13px] text-[#85807D]">Shipping Fee</span>
                            <span class="text-[13px] text-[#17120F]">${formatCurrency(0)}</span>
                        </div>

                        <div class="mt-[12px] flex items-center justify-between">
                            <span class="text-[13px] font-medium text-[#17120F]">Total Amount</span>
                            <span class="text-[20px] font-bold text-[#721313]">${formatCurrency(order.total)}</span>
                        </div>
                    </div>
                </div>
            `;
        }

        const notesSection =
            document.querySelector(
                '.modal-notes-section'
            );

        if (notesSection) {
            notesSection.hidden =
                false;

            notesSection.innerHTML = `
                <h3 class="text-[15px] font-semibold text-[#17120F]">
                    Cancellation Request
                </h3>

                <div class="mt-[8px] rounded-[10px] bg-[#FFF5E6] px-[20px] py-[13px]">
                    <span class="text-[13px] leading-[1.5] text-[#6B5143]">
                        ${escapeHtml(order.cancellation_reason)}
                    </span>
                </div>

                ${
                    order.rejection_reason
                        ? `
                            <div class="mt-[10px] rounded-[10px] bg-[#F4F4F5] px-[20px] py-[13px]">
                                <span class="block text-[11px] font-medium uppercase tracking-[.04em] text-[#8A8582]">
                                    Rejection Reason
                                </span>
                                <span class="mt-[4px] block text-[13px] leading-[1.5] text-[#5B5552]">
                                    ${escapeHtml(order.rejection_reason)}
                                </span>
                            </div>
                        `
                        : ''
                }
            `;
        }

        return true;
    }


    async function loadOrderDetails(row) {
        const response = await fetch(`${config.ordersUrl}/${row.dataset.orderId}`, {
            headers: {
                Accept: 'application/json',
                Authorization: `Bearer ${config.apiToken || ''}`,
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        });
        if (!response.ok) throw new Error(`Unable to load order details (${response.status}).`);

        const order = await response.json();
        // Details endpoint may contain cancellation timestamps missing from list data.
        const cancellationFields = {
            cancellationRequestedAt: order.cancellation_requested_at || order.cancellation_request_created_at || order.cancel_requested_at,
            cancelledAt: order.cancelled_at || order.canceled_at,
            cancellationRejectedAt: order.cancellation_rejected_at || order.cancel_rejected_at,
            rejectionReason: order.rejection_reason,
            preparedAt: order.prepared_at || order.preparing_at,
            shipmentScheduledAt: order.shipment_scheduled_at || order.pickup_scheduled_at || order.scheduled_at,
        };
        Object.entries(cancellationFields).forEach(([key, value]) => {
            if (value) row.dataset[key] = value;
        });
        const buyer = order.buyer || {};
        const items = order.items || [];
        const subtotal = items.reduce((sum, item) =>
            sum + (Number(item.unit_price) * Number(item.quantity)), 0
        );
        const setText = (id, value) => {
            const element = document.getElementById(id);
            if (element) element.textContent = value || '—';
        };
        const createdAt = order.created_at ? new Date(order.created_at) : null;

        setText('orderModalCustomerName', order.delivery_name || buyer.name);
        setText('orderModalCustomerEmail', buyer.email);
        setText('orderModalCustomerPhone', order.delivery_phone || buyer.contact_no);
        setText('orderModalCustomerAddress', order.delivery_address);
        setText('orderModalPaymentMethod', order.payment_method || 'Cash on Delivery');
        setText(
            'orderModalPaymentDescription',
            (order.payment_method || '').toLowerCase().includes('cash') ? 'Payment upon Delivery' : 'Payment details',
        );
        if (createdAt && !Number.isNaN(createdAt.getTime())) {
            setText('orderModalOrderedDate', new Intl.DateTimeFormat('en-US', {
                month: 'long', day: 'numeric', year: 'numeric',
            }).format(createdAt));
            setText('orderModalOrderedTime', new Intl.DateTimeFormat('en-US', {
                hour: 'numeric', minute: '2-digit',
            }).format(createdAt));
        }

        updateModalShipmentSchedule(row, order);

        const itemsSection = document.querySelector('.modal-items-section');
        if (itemsSection) {
            itemsSection.innerHTML = `
                <h3 class="text-[15px] font-semibold text-[#17120F]">Items Ordered</h3>
                <div class="mt-[5px] h-[42px] rounded-[11px] bg-[#FBEDED] grid grid-cols-[2.25fr_1fr_1fr_1fr] items-center px-[24px] text-[12px] font-medium text-[#60100F]">
                    <div>Item</div><div>Price</div><div>Quantity</div><div>Subtotal</div>
                </div>
                ${items.length ? items.map((item) => {
                    const itemSubtotal = Number(item.unit_price) * Number(item.quantity);
                    const options = [item.variation, item.color, item.size].filter(Boolean).join(' · ');
                    return `
                        <div class="modal-item-row">
                            <div class="flex items-center gap-[16px]">
                                <div class="modal-product-image">${productImageUrl(item)
                                    ? `<img src="${escapeHtml(productImageUrl(item))}" alt="${escapeHtml(item.product_name)}">`
                                    : '<div class="modal-product-bag"></div>'}</div>
                                <span class="text-[14px] font-semibold text-[#17120F]">
                                    ${escapeHtml(item.product_name)}${options ? `<small class="block font-normal">${escapeHtml(options)}</small>` : ''}
                                </span>
                            </div>
                            <div class="text-[13px] text-[#17120F]">${formatCurrency(item.unit_price)}</div>
                            <div class="text-[13px] text-[#17120F] text-center">${Number(item.quantity)}</div>
                            <div class="text-[13px] text-[#17120F]">${formatCurrency(itemSubtotal)}</div>
                        </div>`;
                }).join('') : '<p class="py-4 text-[13px] text-[#77716E]">No item details are saved for this order.</p>'}
                <div class="flex justify-end pt-[10px]"><div class="w-[320px]">
                    <div class="flex items-center justify-between"><span class="text-[13px] text-[#85807D]">Subtotal</span><span class="text-[13px] text-[#17120F]">${formatCurrency(subtotal)}</span></div>
                    <div class="mt-[10px] flex items-center justify-between"><span class="text-[13px] text-[#85807D]">Shipping Fee</span><span class="text-[13px] text-[#17120F]">${formatCurrency(0)}</span></div>
                    <div class="mt-[12px] flex items-center justify-between"><span class="text-[13px] font-medium text-[#17120F]">Total Amount</span><span class="text-[20px] font-bold text-[#721313]">${formatCurrency(order.total)}</span></div>
                </div></div>`;
        }

        const notesSection = document.querySelector('.modal-notes-section');
        if (notesSection) notesSection.hidden = true;

        if (Array.isArray(order.status_history)) {
            row.dataset.statusHistory = JSON.stringify(order.status_history);
        }
        // Cancellation modals show the same entire order timeline.
        renderTrackingHistory(row);
    }

    async function saveOrderStatus(orderId, payload, method = 'PATCH') {
        const response = await fetch(
            method === 'POST'
                ? `${config.ordersUrl}/${orderId}/schedule`
                : `${config.ordersUrl}/${orderId}/status`,
            {
                method,
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    Authorization: `Bearer ${config.apiToken || ''}`,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                body: JSON.stringify(payload),
            },
        );

        let result = {};
        try {
            result = await response.json();
        } catch {
            // Surface the HTTP status if the API did not return JSON.
        }

        if (!response.ok) {
            const validationMessage = Object.values(result.errors || {}).flat()[0];
            throw new Error(validationMessage || result.message || `Could not save order changes (${response.status}).`);
        }

        return result;
    }

    const trackingHistorySection =
        document.getElementById(
            'trackingHistorySection'
        );

    const trackingHistoryList =
        document.getElementById(
            'trackingHistoryList'
        );

    const scheduleShipmentModal =
        document.getElementById(
            'scheduleShipmentModal'
        );

    const scheduleShipmentPanel =
        document.getElementById(
            'scheduleShipmentModalPanel'
        );

    const closeScheduleShipmentButton =
        document.getElementById(
            'closeScheduleShipment'
        );

    const cancelScheduleShipmentButton =
        document.getElementById(
            'cancelScheduleShipment'
        );

    const confirmScheduleShipmentButton =
        document.getElementById(
            'confirmScheduleShipment'
        );

    const pickupDateInput =
        document.getElementById(
            'pickupDate'
        );

    const pickupTimeInput =
        document.getElementById(
            'pickupTime'
        );

    const scheduleOrderId =
        document.getElementById(
            'scheduleOrderId'
        );

    let selectedOrderRow = null;
    let lastFocusedElement = null;
    let bodyLockCount = 0;

    function lockBody() {
        bodyLockCount++;

        document.body.classList.add(
            'overflow-hidden'
        );
    }

    function unlockBody() {
        bodyLockCount =
            Math.max(
                0,
                bodyLockCount - 1
            );

        if (
            bodyLockCount === 0
        ) {
            document.body.classList.remove(
                'overflow-hidden'
            );
        }
    }

    function pad(
        number
    ) {
        return String(
            number
        ).padStart(
            2,
            '0'
        );
    }

    function toDateInputValue(
        date
    ) {
        return (
            date.getFullYear() +
            '-' +
            pad(
                date.getMonth() + 1
            ) +
            '-' +
            pad(
                date.getDate()
            )
        );
    }

    function formatDateDisplay(
        value
    ) {
        if (!value) {
            return '';
        }

        const date =
            new Date(
                value +
                'T00:00:00'
            );

        if (
            Number.isNaN(
                date.getTime()
            )
        ) {
            return value;
        }

        return date.toLocaleDateString(
            'en-US',
            {
                month:
                    'long',
                day:
                    'numeric',
                year:
                    'numeric'
            }
        );
    }

    function formatTimeDisplay(
        value
    ) {
        if (!value) {
            return '';
        }
        // The API can already return a formatted 12-hour pickup time.
        if (/\b(?:AM|PM)\b/i.test(String(value))) return String(value);

        const [
            hoursRaw,
            minutes
        ] =
            value.split(
                ':'
            );

        const hours =
            Number(
                hoursRaw
            );

        if (
            Number.isNaN(
                hours
            )
        ) {
            return value;
        }

        const suffix =
            hours >= 12
                ? 'PM'
                : 'AM';

        const twelveHour =
            hours % 12 || 12;

        return (
            twelveHour +
            ':' +
            minutes +
            ' ' +
            suffix
        );
    }

    function nowTrackingStamp() {
        const now =
            new Date();

        return {
            date:
                now.toLocaleDateString(
                    'en-US',
                    {
                        month:
                            'short',
                        day:
                            'numeric',
                        year:
                            'numeric'
                    }
                ),

            time:
                now.toLocaleTimeString(
                    'en-US',
                    {
                        hour:
                            'numeric',
                        minute:
                            '2-digit'
                    }
                )
        };
    }

    function makeTrackingEvent(
        type,
        date,
        time
    ) {
        return {
            type,
            date,
            time
        };
    }

    function cancellationStampFromDate(value) {
        if (!value) return null;

        const date = new Date(value);
        if (Number.isNaN(date.getTime())) return null;

        return {
            date: new Intl.DateTimeFormat('en-US', {
                month: 'short',
                day: '2-digit',
                year: 'numeric',
            }).format(date),
            time: new Intl.DateTimeFormat('en-US', {
                hour: 'numeric',
                minute: '2-digit',
            }).format(date),
        };
    }


    function getCancellationEvents(row) {
        const events = [];

        const requested = cancellationStampFromDate(row.dataset.cancellationRequestedAt);
        if (requested) {
            events.push({
                type: 'cancel-requested',
                title: 'Cancellation Requested',
                description: 'The buyer submitted a cancellation request.',
                ...requested,
            });
        }

        const cancelled = cancellationStampFromDate(row.dataset.cancelledAt);
        if (cancelled) {
            const actor = row.dataset.cancellationActor;
            events.push({
                type: 'cancelled',
                title: 'Order Cancelled',
                description: `${row.dataset.pickupWasScheduled === 'true' ? 'The pickup schedule was cancelled. ' : ''}Order cancelled by ${actor || 'the buyer or seller'}.`,
                ...cancelled,
            });
        }

        const rejected = cancellationStampFromDate(row.dataset.cancellationRejectedAt);
        if (rejected) {
            events.push({
                type: 'cancel-rejected',
                title: 'Cancellation Rejected',
                description: row.dataset.rejectionReason
                    ? `Reason: ${row.dataset.rejectionReason}`
                    : 'The cancellation request was rejected.',
                ...rejected,
            });
        }

        return events;
    }


    function getTrackingEvents(
        row
    ) {
        try {
            return JSON.parse(
                row.dataset
                    .trackingHistory ||
                '[]'
            );
        } catch (
            error
        ) {
            return [];
        }
    }

    function setTrackingEvents(
        row,
        events
    ) {
        row.dataset
            .trackingHistory =
            JSON.stringify(
                events
            );
    }

    function trackingIcon(
        type,
        active
    ) {
        const stroke =
            active
                ? '#FFFFFF'
                : '#185B8C';

        if (
            type ===
            'prepared'
        ) {
            return (
                '<svg viewBox="0 0 24 24" fill="none" stroke="' +
                stroke +
                '" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">' +
                '<path d="M5 8h14v11H5z"></path>' +
                '<path d="M8 8V5h8v3"></path>' +
                '<path d="M9 12h6"></path>' +
                '</svg>'
            );
        }

        if (
            type === 'cancel-requested' ||
            type === 'cancelled' ||
            type === 'cancel-rejected'
        ) {
            return (
                '<svg viewBox="0 0 24 24" fill="none" stroke="' +
                stroke +
                '" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">' +
                '<circle cx="12" cy="12" r="9"></circle>' +
                (
                    type === 'cancelled'
                        ? '<path d="m8 12 2.5 2.5L16 9"></path>'
                        : (
                            type === 'cancel-rejected'
                                ? '<path d="m9 9 6 6"></path><path d="m15 9-6 6"></path>'
                                : '<path d="M12 7v5"></path><path d="M12 16h.01"></path>'
                        )
                ) +
                '</svg>'
            );
        }

        if (
            type ===
            'ready'
        ) {
            return (
                '<svg viewBox="0 0 24 24" fill="none" stroke="' +
                stroke +
                '" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">' +
                '<path d="M3 16h14"></path>' +
                '<path d="M14 7h4l3 4v5h-3"></path>' +
                '<circle cx="7" cy="17" r="2"></circle>' +
                '<circle cx="18" cy="17" r="2"></circle>' +
                '<path d="M3 8h8"></path>' +
                '</svg>'
            );
        }

        return (
            '<svg viewBox="0 0 24 24" fill="none" stroke="' +
            stroke +
            '" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">' +
            '<circle cx="12" cy="12" r="9"></circle>' +
            '</svg>'
        );
    }

    function renderTrackingHistory(row) {
        if (!trackingHistorySection || !trackingHistoryList || !row) return;

        // Combine the original fulfillment history and cancellation actions.
        // A cancelled order must NOT lose its earlier preparation/shipment entries.
        const events = [];
        const addDatedEvent = (type, value, description = '') => {
            const stamp = cancellationStampFromDate(value);
            if (stamp) events.push({ type, ...stamp, description, timestamp: new Date(value).getTime() });
        };
        addDatedEvent('placed', row.dataset.orderedAt);
        addDatedEvent('prepared', row.dataset.preparedAt);
        addDatedEvent('ready', row.dataset.shipmentScheduledAt);

        let statusHistory = [];
        try {
            const parsed = JSON.parse(row.dataset.statusHistory || '[]');
            statusHistory = Array.isArray(parsed) ? parsed : [];
        } catch { /* Missing/invalid optional API history */ }

        statusHistory.forEach((record) => {
            const status = String(record.to_status || record.new_status || record.status || '').toLowerCase().replace(/-/g, '_');
            const type = ['pending', 'new'].includes(status) ? 'placed'
                : status === 'preparing' ? 'prepared'
                : ['to_ship', 'ready_to_ship', 'shipment_scheduled'].includes(status) ? 'ready'
                : ['cancel_requested', 'cancellation_pending'].includes(status) ? 'cancel-requested'
                : ['cancelled', 'canceled'].includes(status) ? 'cancelled'
                : ['cancellation_rejected', 'cancel_rejected'].includes(status) ? 'cancel-rejected' : null;
            if (!type) return;
            const cancellationDescription = type === 'cancelled'
                ? `${row.dataset.pickupWasScheduled === 'true' ? 'The pickup schedule was cancelled. ' : ''}Order cancelled by ${row.dataset.cancellationActor || 'the buyer or seller'}.`
                : '';
            addDatedEvent(type, record.created_at || record.timestamp || record.changed_at || record.updated_at, cancellationDescription);
        });

        getTrackingEvents(row).forEach((event) => {
            const timestamp = Number.isFinite(Number(event.timestamp)) && event.timestamp
                ? Number(event.timestamp) : Date.parse(`${event.date || ''} ${event.time || ''}`);
            events.push({ ...event, timestamp: Number.isFinite(timestamp) ? timestamp : null });
        });
        getCancellationEvents(row).forEach((event) => {
            const timestamp = Date.parse(`${event.date || ''} ${event.time || ''}`);
            events.push({ ...event, timestamp: Number.isFinite(timestamp) ? timestamp : null });
        });

        // Same event may appear in both a status-history API response and local state.
        // Deduplicate near-identical entries (a minute resolution for UI timestamps).
        const unique = new Map();
        events.forEach((event) => {
            const timestamp = Number.isFinite(event.timestamp) ? event.timestamp : null;
            const key = `${event.type}|${timestamp !== null ? Math.floor(timestamp / 60000) : `${event.date}|${event.time}`}`;
            const previous = unique.get(key);
            if (!previous || (event.description && !previous.description)) unique.set(key, event);
        });
        const chronological = [...unique.values()].sort((a, b) => {
            if (a.timestamp == null) return 1;
            if (b.timestamp == null) return -1;
            return a.timestamp - b.timestamp;
        });

        if (trackingHistoryTitle) trackingHistoryTitle.textContent = 'Tracking History';
        trackingHistorySection.classList.toggle('hidden', !chronological.length);
        trackingHistoryList.replaceChildren();
        const titles = {
            placed: ['Order Placed', 'The buyer placed the order.'],
            prepared: ['Order Preparing', 'The seller started preparing the order.'],
            ready: ['Shipment Scheduled', 'Pickup was scheduled for the order.'],
            'cancel-requested': ['Cancellation Requested', 'The buyer requested cancellation.'],
            cancelled: ['Cancellation Approved', 'The seller approved the cancellation request.'],
            'cancel-rejected': ['Cancellation Rejected', 'The seller rejected the cancellation request.'],
        };
        chronological.forEach((event, index) => {
            const type = event.type || 'placed';
            const isActive = index === chronological.length - 1;
            const title = event.title || titles[type]?.[0] || 'Order Update';
            const description = event.description || titles[type]?.[1] || '';
            const wrapper = document.createElement('div');
            wrapper.className = 'tracking-item';
            wrapper.innerHTML =
                '<div class="tracking-icon-wrap ' + (isActive ? 'active ' : '') +
                'cancellation-' + escapeHtml(type) + '">' + trackingIcon(type, isActive) + '</div>' +
                '<div class="tracking-time"><span class="date">' + escapeHtml(event.date || '') +
                '</span><span class="time">' + escapeHtml(event.time || '') + '</span></div>' +
                '<div class="tracking-content"><div class="event-title">' + escapeHtml(title) +
                '</div><div class="event-description">' + escapeHtml(description) + '</div></div>';
            trackingHistoryList.appendChild(wrapper);
        });
    }

    function updateModalShipmentSchedule(row, order = null) {
        const section = document.getElementById('orderModalShipmentSchedule');
        if (!section) return;
        const date = order?.pickup_date_display || order?.pickup_date || row?.dataset?.pickupDate || '';
        const time = order?.pickup_time_display || order?.pickup_time || row?.dataset?.pickupTime || '';
        const hasSchedule = Boolean(date && time);
        section.classList.toggle('hidden', !hasSchedule);
        if (!hasSchedule) return;
        const dateLabel = document.getElementById('orderModalShipmentDate');
        const timeLabel = document.getElementById('orderModalShipmentTime');
        if (dateLabel) dateLabel.textContent = formatDateDisplay(date);
        if (timeLabel) timeLabel.textContent = formatTimeDisplay(time);
    }

    function syncOrderModalFromRow(
        row
    ) {
        const status =
            row.dataset.status ||
            'new';

        const isPreparing =
            status ===
            'preparing';

        const isReady =
            status ===
            'to-ship';

        const isNewOrder =
            status === 'new' || status === 'pending';

        const isCancelled =
            status === 'cancelled';

        const isCancellationPending =
            status === 'cancel-pending';

        const isCancellationRejected =
            status === 'cancel-rejected';

        if (printWaybillButton) {
            printWaybillButton.hidden =
                !isNewOrder;
        }


        if (acceptCancellationRequestButton) {
            acceptCancellationRequestButton.hidden =
                !isCancellationPending;
        }

        if (rejectCancellationRequestButton) {
            rejectCancellationRequestButton.hidden =
                !isCancellationPending;
        }

        if (orderModalActionButton) {
            orderModalActionButton.hidden =
                isCancellationPending;
        }

        if (
            orderModalOrderId
        ) {
            orderModalOrderId.textContent =
                row.querySelector(
                    '.order-id'
                )?.textContent
                    ?.trim() ||
                '#ORD-2025';
        }

        if (
            orderModalOrderedDate
        ) {
            orderModalOrderedDate.textContent =
                row.querySelector(
                    '.ordered-date'
                )?.textContent
                    ?.trim() ||
                'May 22, 2026';
        }

        if (
            orderModalOrderedTime
        ) {
            orderModalOrderedTime.textContent =
                row.querySelector(
                    '.ordered-time'
                )?.textContent
                    ?.trim() ||
                '10:34 AM';
        }

        // Shipment pickup must never replace the actual Ordered at timestamp.
        const placedAt = row.dataset.orderedAt;
        if (placedAt) {
            const ordered = cancellationStampFromDate(placedAt);
            if (ordered) {
                if (orderModalOrderedDate) orderModalOrderedDate.textContent = ordered.date;
                if (orderModalOrderedTime) orderModalOrderedTime.textContent = ordered.time;
            }
        }
        updateModalShipmentSchedule(row);

        let label =
            'New Order';

        if (
            isPreparing
        ) {
            label =
                'Preparing';
        }

        if (
            isReady
        ) {
            label =
                'Ready to Ship';
        }

        if (
            isCancelled
        ) {
            label =
                'Cancelled';
        }

        if (
            isCancellationPending
        ) {
            label =
                'Cancellation Pending';
        }

        if (
            isCancellationRejected
        ) {
            label =
                'Cancellation Rejected';
        }

        if (
            orderModalStatusLabel
        ) {
            orderModalStatusLabel.textContent =
                label;
        }

        if (
            orderModalStatusText
        ) {
            orderModalStatusText
                .classList
                .remove(
                    'new-text',
                    'preparing-text',
                    'ready-text',
                    'cancelled-text',
                    'cancel-pending-text',
                    'cancel-rejected-text'
                );

            orderModalStatusText
                .classList
                .add(
                    isCancelled
                        ? 'cancelled-text'
                        : (
                            isCancellationRejected
                                ? 'cancel-rejected-text'
                                : (
                                    isCancellationPending
                                        ? 'cancel-pending-text'
                                        : (
                                    isPreparing
                                        ? 'preparing-text'
                                        : (
                                            isReady
                                                ? 'ready-text'
                                                : 'new-text'
                                        )
                                    )
                                )
                        )
                );
        }

        if (
            orderModalStatusDot
        ) {
            orderModalStatusDot.style.backgroundColor =
                isCancelled
                    ? '#9CA3AF'
                    : (
                        isCancellationRejected
                            ? '#A55B5B'
                            : (
                                isCancellationPending
                                    ? '#D97706'
                                    : (
                                isPreparing
                                    ? '#FF9D00'
                                    : (
                                        isReady
                                            ? '#08A11D'
                                            : '#E21E1E'
                                    )
                                )
                            )
                    );
        }

        if (
            orderModalIconBox
        ) {
            orderModalIconBox
                .classList
                .remove(
                    'new',
                    'preparing',
                    'ready'
                );

            orderModalIconBox
                .classList
                .add(
                    isPreparing
                        ? 'preparing'
                        : (
                            isReady
                                ? 'ready'
                                : 'new'
                        )
                );
        }

        if (
            orderModalStatusIcon
        ) {
            orderModalStatusIcon.src =
                isPreparing
                    ? '/icons/seller/order-status/processing.png'
                    : (
                        isReady
                            ? '/icons/seller/order-status/ready-to-ship.png'
                            : '/icons/seller/order-status/new-order.png'
                    );

            orderModalStatusIcon.alt =
                label;
        }

        if (
            orderModalActionButton &&
            orderModalActionLabel
        ) {
            orderModalActionButton
                .classList
                .remove(
                    'new-action',
                    'preparing-action',
                    'ready-action'
                );

            if (
                isCancelled ||
                isCancellationPending ||
                isCancellationRejected
            ) {
                orderModalActionButton
                    .classList
                    .add(
                        'ready-action'
                    );

                orderModalActionLabel.textContent =
                    isCancellationPending
                        ? 'Cancellation Pending'
                        : (
                            isCancellationRejected
                                ? 'Cancellation Rejected'
                                : 'Cancelled'
                        );

                orderModalActionButton.disabled =
                    true;
            } else if (
                isPreparing
            ) {
                orderModalActionButton
                    .classList
                    .add(
                        'preparing-action'
                    );

                orderModalActionLabel.textContent =
                    'Schedule Shipment';

                orderModalActionButton.disabled =
                    false;
            } else if (
                isReady
            ) {
                orderModalActionButton
                    .classList
                    .add(
                        'ready-action'
                    );

                orderModalActionLabel.textContent =
                    'Ready to Ship';

                orderModalActionButton.disabled =
                    true;
            } else {
                orderModalActionButton
                    .classList
                    .add(
                        'new-action'
                    );

                orderModalActionLabel.textContent =
                    'Prepare Order';

                orderModalActionButton.disabled =
                    false;
            }
        }

        renderTrackingHistory(
            row
        );
    }

    function openOrderDetailsModal(
        row
    ) {
        if (
            !row ||
            !orderDetailsModal ||
            !orderDetailsPanel
        ) {
            return;
        }

        selectedOrderRow =
            row;

        lastFocusedElement =
            document.activeElement;

        const itemsSection = document.querySelector('.modal-items-section');
        if (itemsSection) {
            itemsSection.innerHTML = '<p class="py-4 text-[13px] text-[#77716E]">Loading order items…</p>';
        }
        [
            'orderModalCustomerName',
            'orderModalCustomerEmail',
            'orderModalCustomerPhone',
            'orderModalCustomerAddress',
            'orderModalPaymentMethod',
            'orderModalPaymentDescription',
        ].forEach((id) => {
            const element = document.getElementById(id);
            if (element) element.textContent = 'Loading…';
        });

        syncOrderModalFromRow(
            row
        );

        orderDetailsModal
            .classList
            .add(
                'modal-open'
            );

        orderDetailsModal
            .setAttribute(
                'aria-hidden',
                'false'
            );

        lockBody();

        if (
            row.dataset.hardcodedCancellation ===
            'true'
        ) {
            populateHardcodedCancellationDetails(
                row
            );

            return;
        }

        loadOrderDetails(row).catch((error) => {
            const items = document.getElementById('orderModalItems');
            if (items) items.innerHTML = `<p class="py-4 text-[13px] text-red-700">${escapeHtml(error.message)}</p>`;
        });
    }

    function closeOrderDetailsModal(
        keepBodyLocked = false
    ) {
        if (
            !orderDetailsModal
        ) {
            return;
        }

        orderDetailsModal
            .classList
            .remove(
                'modal-open'
            );

        orderDetailsModal
            .setAttribute(
                'aria-hidden',
                'true'
            );

        if (
            !keepBodyLocked
        ) {
            unlockBody();
        }

        setTimeout(
            function () {
                if (
                    !keepBodyLocked &&
                    lastFocusedElement &&
                    typeof lastFocusedElement.focus ===
                        'function'
                ) {
                    lastFocusedElement
                        .focus();
                }
            },
            210
        );
    }

    // Approving a buyer cancellation must be a single atomic server operation.
    async function approveBuyerCancellationRequest(row) {
        if (!row?.dataset.cancellationRequestId) {
            throw new Error('The buyer cancellation request could not be found. Refresh the order and try again.');
        }
        const response = await fetch(
            `${config.ordersUrl}/${row.dataset.orderId}/cancellation-requests/${row.dataset.cancellationRequestId}/approve`,
            {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                Authorization: `Bearer ${config.apiToken || ''}`,
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            },
        );
        let result = {};
        try { result = await response.json(); } catch { /* HTTP fallback below */ }
        if (!response.ok) {
            const message = Object.values(result.errors || {}).flat()[0];
            throw new Error(message || result.message || `Cancellation approval failed (${response.status}).`);
        }
        const pickupWasScheduled = Boolean(row.dataset.pickupDate) || row.dataset.pickupWasScheduled === 'true';
        row.dataset.pickupDate = '';
        row.dataset.pickupTime = '';
        row.dataset.cancellationActor = 'buyer';
        row.dataset.pickupWasScheduled = pickupWasScheduled ? 'true' : 'false';
        const cancellationHistory = (result.status_history || []).find((record) => record.to_status === 'cancelled');
        if (cancellationHistory?.created_at) row.dataset.cancelledAt = cancellationHistory.created_at;
        if (Array.isArray(result.status_history)) row.dataset.statusHistory = JSON.stringify(result.status_history);
        return true;
    }

    function setCancellationDecisionStatus(
        row,
        status,
        rejectionReason = ''
    ) {
        if (!row) {
            return;
        }

        row.dataset.status =
            status;

        row.dataset.tab =
            status;

        row.dataset.rejectionReason =
            rejectionReason;

        const rowStatus =
            row.querySelector(
                '.order-status'
            );

        if (rowStatus) {
            if (
                status ===
                'cancelled'
            ) {
                rowStatus.className =
                    'order-status cancelled-text';

                rowStatus.innerHTML =
                    '<span class="status-dot cancelled-dot"></span>Cancelled';

                let cancellationDetail = row.querySelector('.order-cancellation-detail');
                if (!cancellationDetail) {
                    cancellationDetail = document.createElement('p');
                    cancellationDetail.className = 'order-cancellation-detail mt-1 text-[10px] text-[#8A8582]';
                    rowStatus.insertAdjacentElement('afterend', cancellationDetail);
                }
                const actor = row.dataset.cancellationActor || 'buyer';
                cancellationDetail.textContent = `${row.dataset.pickupWasScheduled === 'true' ? 'Pickup cancelled · ' : ''}Cancelled by ${actor.charAt(0).toUpperCase() + actor.slice(1)}`;
            } else if (
                status ===
                'cancel-rejected'
            ) {
                rowStatus.className =
                    'order-status cancel-rejected-text';

                rowStatus.innerHTML =
                    '<span class="status-dot cancel-rejected-dot"></span>Cancellation Rejected';
            }
        }
    }


    acceptCancellationRequestButton?.addEventListener(
        'click',
        async function () {
            if (
                !selectedOrderRow ||
                selectedOrderRow.dataset.status !==
                    'cancel-pending'
            ) {
                return;
            }

            acceptCancellationRequestButton.disabled = true;
            try {
                // Demo cancellation requests have no persisted server shipment.
                if (selectedOrderRow.dataset.hardcodedCancellation !== 'true') {
                    await approveBuyerCancellationRequest(selectedOrderRow);
                }
            } catch (error) {
                showOrderFlash(error.message || 'Could not approve cancellation.', 'error');
                acceptCancellationRequestButton.disabled = false;
                return;
            }
            acceptCancellationRequestButton.disabled = false;

            const acceptedAt =
                new Date().toISOString();

            selectedOrderRow.dataset.cancelledAt =
                acceptedAt;

            const hardcodedOrder =
                hardcodedOrderMap.get(
                    String(
                        selectedOrderRow.dataset.orderId ||
                        ''
                    )
                );

            if (hardcodedOrder) {
                hardcodedOrder.status = 'cancelled';
                hardcodedOrder.cancellation_status = 'accepted';
                hardcodedOrder.cancelled_at = acceptedAt;
            }

            setCancellationDecisionStatus(
                selectedOrderRow,
                'cancelled'
            );

            activeTab =
                'cancellations';

            activeCancellationTab =
                'cancelled';

            currentPage =
                1;

            tabs.forEach(
                function (tab) {
                    tab.classList.toggle(
                        'active',
                        tab.dataset.tab ===
                            'cancellations'
                    );
                }
            );

            cancellationTabs.forEach(
                function (tab) {
                    tab.classList.toggle(
                        'active',
                        tab.dataset.cancellationTab ===
                            'cancelled'
                    );
                }
            );

            closeOrderDetailsModal();
            filterOrders();

            showOrderFlash(
                'Cancellation approved. Any scheduled shipment has been cancelled.',
                'success',
                'Cancellation Approved'
            );
        }
    );


    function openRejectCancellationRequestModal() {
        if (
            !selectedOrderRow ||
            !rejectCancellationRequestModal
        ) {
            return;
        }

        closeOrderDetailsModal(
            true
        );

        document.querySelectorAll(
            'input[name="cancellation_rejection_reason"]'
        ).forEach((radio) => {
            radio.checked = false;
        });

        if (cancellationRejectionOtherReason) {
            cancellationRejectionOtherReason.value = '';
        }

        if (cancellationRejectionCounter) {
            cancellationRejectionCounter.textContent = '0/300';
        }

        if (cancellationRejectionError) {
            cancellationRejectionError.hidden =
                true;

            cancellationRejectionError.textContent =
                '';
        }

        rejectCancellationRequestModal.classList.add(
            'modal-open'
        );

        rejectCancellationRequestModal.setAttribute(
            'aria-hidden',
            'false'
        );

        lockBody();

        cancellationRejectionReason?.focus();
    }


    function closeRejectCancellationRequestModal(
        reopenDetails = false
    ) {
        if (
            !rejectCancellationRequestModal
        ) {
            return;
        }

        rejectCancellationRequestModal.classList.remove(
            'modal-open'
        );

        rejectCancellationRequestModal.setAttribute(
            'aria-hidden',
            'true'
        );

        if (
            reopenDetails &&
            selectedOrderRow
        ) {
            openOrderDetailsModal(
                selectedOrderRow
            );

            return;
        }

        unlockBody();
    }


    rejectCancellationRequestButton?.addEventListener(
        'click',
        openRejectCancellationRequestModal
    );

    cancellationRejectionOtherReason?.addEventListener(
        'input',
        function () {
            if (cancellationRejectionCounter) {
                cancellationRejectionCounter.textContent =
                    `${cancellationRejectionOtherReason.value.length}/300`;
            }
        }
    );


    document
        .getElementById(
            'closeRejectCancellationRequest'
        )
        ?.addEventListener(
            'click',
            function () {
                closeRejectCancellationRequestModal(
                    true
                );
            }
        );


    document
        .getElementById(
            'cancelRejectCancellationRequest'
        )
        ?.addEventListener(
            'click',
            function () {
                closeRejectCancellationRequestModal(
                    true
                );
            }
        );


    rejectCancellationRequestModal?.addEventListener(
        'click',
        function (event) {
            if (
                event.target ===
                rejectCancellationRequestModal
            ) {
                closeRejectCancellationRequestModal(
                    true
                );
            }
        }
    );


    confirmRejectCancellationRequest?.addEventListener(
        'click',
        function () {
            if (
                !selectedOrderRow
            ) {
                return;
            }

            const reason =
                document.querySelector(
                    'input[name="cancellation_rejection_reason"]:checked'
                )?.value ||
                '';

            const otherReason =
                cancellationRejectionOtherReason?.value
                    ?.trim() ||
                '';

            if (
                !reason ||
                (
                    reason ===
                        'Other' &&
                    !otherReason
                )
            ) {
                if (cancellationRejectionError) {
                    cancellationRejectionError.textContent =
                        reason ===
                            'Other'
                            ? 'Please enter the rejection reason.'
                            : 'Please select a rejection reason.';

                    cancellationRejectionError.hidden =
                        false;
                }

                return;
            }

            const finalReason =
                reason ===
                    'Other'
                    ? otherReason
                    : reason;

            const decisionTime =
                new Date().toISOString();

            selectedOrderRow.dataset.cancellationRejectedAt =
                decisionTime;

            setCancellationDecisionStatus(
                selectedOrderRow,
                'cancel-rejected',
                finalReason
            );

            const hardcodedOrder =
                hardcodedOrderMap.get(
                    String(
                        selectedOrderRow.dataset.orderId ||
                        ''
                    )
                );

            if (hardcodedOrder) {
                hardcodedOrder.status =
                    'cancellation_rejected';

                hardcodedOrder.cancellation_status =
                    'rejected';

                hardcodedOrder.rejection_reason =
                    finalReason;

                hardcodedOrder.cancellation_rejected_at =
                    decisionTime;
            }

            activeTab =
                'cancellations';

            activeCancellationTab =
                'cancel-rejected';

            currentPage =
                1;

            tabs.forEach(
                function (tab) {
                    tab.classList.toggle(
                        'active',
                        tab.dataset.tab ===
                            'cancellations'
                    );
                }
            );

            cancellationTabs.forEach(
                function (tab) {
                    tab.classList.toggle(
                        'active',
                        tab.dataset.cancellationTab ===
                            'cancel-rejected'
                    );
                }
            );

            closeRejectCancellationRequestModal(
                false
            );

            filterOrders();

            showOrderFlash(
                'The buyer cancellation request has been rejected.',
                'rejected',
                'Cancellation Rejected'
            );
        }
    );


    function openScheduleShipmentModal() {
        if (
            !scheduleShipmentModal ||
            !scheduleShipmentPanel
        ) {
            return;
        }

        if (
            selectedOrderRow &&
            scheduleOrderId
        ) {
            scheduleOrderId.textContent =
                selectedOrderRow.querySelector(
                    '.order-id'
                )?.textContent
                    ?.trim() ||
                '#ORD-2025';
        }

        const tomorrow =
            new Date();

        tomorrow.setDate(
            tomorrow.getDate() +
            1
        );

        if (
            pickupDateInput
        ) {
            pickupDateInput.min =
                toDateInputValue(
                    tomorrow
                );

            pickupDateInput.value =
                pickupDateInput.value ||
                toDateInputValue(
                    tomorrow
                );
        }

        if (
            pickupTimeInput
        ) {
            pickupTimeInput.value =
                pickupTimeInput.value ||
                '10:00';
        }

        scheduleShipmentModal
            .classList
            .add(
                'modal-open'
            );

        scheduleShipmentModal
            .setAttribute(
                'aria-hidden',
                'false'
            );

        lockBody();
    }

    function closeScheduleShipmentModal() {
        if (
            !scheduleShipmentModal
        ) {
            return;
        }

        scheduleShipmentModal
            .classList
            .remove(
                'modal-open'
            );

        scheduleShipmentModal
            .setAttribute(
                'aria-hidden',
                'true'
            );

        unlockBody();
    }

    /*
     * Event delegation keeps the Order Details modal functional even when
     * renderOrderRows() replaces all table rows.
     */
    orderTable?.addEventListener(
        'click',
        function (event) {
            const target =
                event.target instanceof Element
                    ? event.target
                    : event.target?.parentElement;

            const row =
                target?.closest(
                    '.order-row'
                );

            if (
                !row ||
                !orderTable.contains(row)
            ) {
                return;
            }

            /*
             * Keep future row controls usable without accidentally opening
             * the details modal.
             */
            if (
                target?.closest(
                    'button, a, input, select, textarea'
                )
            ) {
                return;
            }

            openOrderDetailsModal(
                row
            );
        }
    );

    orderTable?.addEventListener(
        'keydown',
        function (event) {
            if (
                event.key !== 'Enter' &&
                event.key !== ' '
            ) {
                return;
            }

            const target =
                event.target instanceof Element
                    ? event.target
                    : event.target?.parentElement;

            const row =
                target?.closest(
                    '.order-row'
                );

            if (
                !row ||
                !orderTable.contains(row)
            ) {
                return;
            }

            event.preventDefault();

            openOrderDetailsModal(
                row
            );
        }
    );

    orderDetailsModal?.addEventListener(
        'click',
        function (
            event
        ) {
            if (
                event.target ===
                orderDetailsModal
            ) {
                closeOrderDetailsModal();
            }
        }
    );

    orderModalActionButton?.addEventListener(
        'click',
        async function () {
            if (
                !selectedOrderRow
            ) {
                return;
            }

            const currentStatus =
                selectedOrderRow
                    .dataset
                    .status;

            if (
                currentStatus ===
                'new'
            ) {
                orderModalActionButton.disabled =
                    true;

                orderModalActionLabel.textContent =
                    'Preparing...';

                try {
                    await saveOrderStatus(
                        selectedOrderRow.dataset.orderId,
                        { status: 'preparing', notes: 'Seller started preparing the order.' },
                    );

                        const stamp =
                            nowTrackingStamp();

                        selectedOrderRow.dataset.status =
                            'preparing';

                        selectedOrderRow.dataset.tab =
                            'preparing';

                        const iconBox =
                            selectedOrderRow.querySelector(
                                '.order-icon-box'
                            );

                        const icon =
                            iconBox?.querySelector(
                                'img'
                            );

                        const status =
                            selectedOrderRow.querySelector(
                                '.order-status'
                            );

                        iconBox?.classList.remove(
                            'new',
                            'ready'
                        );

                        iconBox?.classList.add(
                            'preparing'
                        );

                        if (
                            icon
                        ) {
                            icon.src =
                                '/icons/seller/order-status/processing.png';

                            icon.alt =
                                'Preparing';
                        }

                        if (
                            status
                        ) {
                            status.className =
                                'order-status preparing-text';

                            status.innerHTML =
                                '<span class="status-dot preparing-dot"></span>Preparing';
                        }

                        selectedOrderRow.dataset.preparedAt = new Date().toISOString();
                        setTrackingEvents(
                            selectedOrderRow,
                            [...getTrackingEvents(selectedOrderRow),
                                makeTrackingEvent('prepared', stamp.date, stamp.time)]
                        );

                        activeTab =
                            'preparing';

                        tabs.forEach(
                            tab =>
                                tab.classList.toggle(
                                    'active',
                                    tab.dataset.tab ===
                                        'preparing'
                                )
                        );

                        filterOrders();

                        syncOrderModalFromRow(
                            selectedOrderRow
                        );

                        showOrderFlash(
                            'Order has been moved to Preparing.',
                            'success',
                            'Order Preparing'
                        );
                } catch (error) {
                    showOrderFlash(
                        error.message || 'Could not prepare the order.',
                        'error'
                    );
                    orderModalActionButton.disabled = false;
                    orderModalActionLabel.textContent = 'Prepare Order';
                }

                return;
            }

            if (
                currentStatus ===
                'preparing'
            ) {
                closeOrderDetailsModal(
                    true
                );

                setTimeout(
                    function () {
                        openScheduleShipmentModal();

                        unlockBody();
                    },
                    210
                );
            }
        }
    );

    closeScheduleShipmentButton?.addEventListener(
        'click',
        closeScheduleShipmentModal
    );

    cancelScheduleShipmentButton?.addEventListener(
        'click',
        closeScheduleShipmentModal
    );

    scheduleShipmentModal?.addEventListener(
        'click',
        function (
            event
        ) {
            if (
                event.target ===
                scheduleShipmentModal
            ) {
                closeScheduleShipmentModal();
            }
        }
    );

    confirmScheduleShipmentButton?.addEventListener(
        'click',
        async function () {
            if (
                !selectedOrderRow
            ) {
                return;
            }

            const pickupDate =
                pickupDateInput?.value ||
                '';

            const pickupTime =
                pickupTimeInput?.value ||
                '';

            if (
                !pickupDate ||
                !pickupTime
            ) {
                showOrderFlash(
                    'Please select a pickup date and time.',
                    'error'
                );

                return;
            }

            confirmScheduleShipmentButton.disabled =
                true;

            confirmScheduleShipmentButton.textContent =
                'Scheduling...';

            try {
                    await saveOrderStatus(
                        selectedOrderRow.dataset.orderId,
                        {
                            pickup_date: pickupDate,
                            pickup_time: pickupTime,
                            notes: 'Seller scheduled shipment pickup.',
                        },
                        'POST',
                    );
                    const scheduledStamp =
                        nowTrackingStamp();
                    selectedOrderRow.dataset.shipmentScheduledAt = new Date().toISOString();
                    const row =
                        selectedOrderRow;

                    const displayDate =
                        formatDateDisplay(
                            pickupDate
                        );

                    const displayTime =
                        formatTimeDisplay(
                            pickupTime
                        );

                    row.dataset.status =
                        'to-ship';

                    row.dataset.tab =
                        'to-ship';

                    row.dataset.pickupDate =
                        pickupDate;

                    row.dataset.pickupTime =
                        pickupTime;

                    updateModalShipmentSchedule(row);

                    const iconBox =
                        row.querySelector(
                            '.order-icon-box'
                        );

                    const icon =
                        iconBox?.querySelector(
                            'img'
                        );

                    const status =
                        row.querySelector(
                            '.order-status'
                        );

                    const dateElement =
                        row.querySelector(
                            '.ordered-date'
                        );

                    const timeElement =
                        row.querySelector(
                            '.ordered-time'
                        );

                    iconBox?.classList.remove(
                        'new',
                        'preparing'
                    );

                    iconBox?.classList.add(
                        'ready'
                    );

                    if (
                        icon
                    ) {
                        icon.src =
                            '/icons/seller/order-status/ready-to-ship.png';

                        icon.alt =
                            'Ready to Ship';
                    }

                    if (
                        status
                    ) {
                        status.className =
                            'order-status ready-text';

                        status.innerHTML =
                            '<span class="status-dot ready-dot"></span>Ready to Ship';
                    }

                    if (
                        dateElement
                    ) {
                        dateElement.textContent =
                            displayDate;
                    }

                    if (
                        timeElement
                    ) {
                        timeElement.textContent =
                            displayTime;
                    }

                    const events =
                        getTrackingEvents(
                            row
                        );

                    events.push(
                        makeTrackingEvent(
                            'ready',
                            scheduledStamp.date,
                            scheduledStamp.time
                        )
                    );

                    setTrackingEvents(
                        row,
                        events
                    );

                    activeTab =
                        'to-ship';

                    tabs.forEach(
                        tab =>
                            tab.classList.toggle(
                                'active',
                                tab.dataset.tab ===
                                    'to-ship'
                            )
                    );

                    filterOrders();

                    closeScheduleShipmentModal();

                    setTimeout(
                        function () {
                            openOrderDetailsModal(
                                row
                            );

                            syncOrderModalFromRow(
                                row
                            );
                        },
                        220
                    );

                    confirmScheduleShipmentButton.disabled =
                        false;

                    confirmScheduleShipmentButton.textContent =
                        'Schedule Pickup';

                    showOrderFlash(
                        'Shipment pickup has been scheduled successfully.',
                        'success',
                        'Shipment Scheduled'
                    );
            } catch (error) {
                showOrderFlash(
                    error.message || 'Could not schedule shipment pickup.',
                    'error'
                );
                confirmScheduleShipmentButton.disabled = false;
                confirmScheduleShipmentButton.textContent = 'Schedule Pickup';
            }
        }
    );

    document.addEventListener(
        'keydown',
        function (
            event
        ) {
            if (
                event.key !==
                'Escape'
            ) {
                return;
            }


            if (
                scheduleShipmentModal
                    ?.classList
                    .contains(
                        'modal-open'
                    )
            ) {
                closeScheduleShipmentModal();

                return;
            }

            if (
                orderDetailsModal
                    ?.classList
                    .contains(
                        'modal-open'
                    )
            ) {
                closeOrderDetailsModal();
            }
        }
    );

    filterOrders();
});