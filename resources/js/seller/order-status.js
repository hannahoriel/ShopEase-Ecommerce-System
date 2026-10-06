document.addEventListener('DOMContentLoaded', function () {
    const configElement = document.getElementById('sellerOrderStatusConfig');
    const config = configElement ? JSON.parse(configElement.textContent) : {};
    const sidebar = document.getElementById('sellerSidebar');
    const page = document.getElementById('order-status-page');
    const tabs = document.querySelectorAll('.order-status-tab');
    const orderTable = document.getElementById('orderStatusTable');
    let rows = [];
    const searchInput = document.getElementById('orderSearch');
    const noResults = document.getElementById('orderStatusNoResults');
    const showingCount = document.getElementById('showingCount');
    const totalEntriesCount = document.getElementById('totalEntriesCount');
    const previousPage = document.getElementById('previousPage');
    const nextPage = document.getElementById('nextPage');
    const pageButtons = document.querySelectorAll('.pagination-button[data-page]');

    let activeTab = 'all';

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, (character) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
        })[character]);
    }

    function renderOrderRows(orders) {
        if (!orderTable) return;

        const statusInfo = {
            new: { label: 'New Order', style: 'new', icon: 'new-order.png' },
            pending: { label: 'New Order', style: 'new', icon: 'new-order.png' },
            preparing: { label: 'Preparing', style: 'preparing', icon: 'processing.png' },
            to_ship: { label: 'Ready to Ship', style: 'ready', icon: 'ready-to-ship.png' },
            'to-ship': { label: 'Ready to Ship', style: 'ready', icon: 'ready-to-ship.png' },
        };
        const money = (value) => `₱${Number(value || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 })}`;

        orderTable.innerHTML = orders.map((order) => {
            const status = statusInfo[order.status] || {
                label: String(order.status || 'unknown').replaceAll('_', ' '),
                style: 'new',
                icon: 'new-order.png',
            };
            const items = order.items || [];
            const itemNames = items.map((item) => item.product_name);
            const createdAt = order.created_at ? new Date(order.created_at) : null;
            const orderNumber = order.order_number || `ORD-${order.id}`;
            const customer = order.delivery_name || order.buyer?.name || 'Customer';
            const phone = order.delivery_phone || order.buyer?.contact_no || '';
            const date = createdAt && !Number.isNaN(createdAt.getTime())
                ? new Intl.DateTimeFormat('en-US', { month: 'short', day: '2-digit', year: 'numeric' }).format(createdAt)
                : '';
            const time = createdAt && !Number.isNaN(createdAt.getTime())
                ? new Intl.DateTimeFormat('en-US', { hour: 'numeric', minute: '2-digit' }).format(createdAt)
                : '';
            const itemPreview = items.slice(0, 2).map((item) =>
                `<div class="mini-product" title="${escapeHtml(item.product_name)}"><div class="product-bag"></div></div>`
            ).join('');
            const moreItems = items.length > 2
                ? `<span class="more-items">+${items.length - 2} more</span>`
                : (items.length === 1 ? `<span class="text-[12px] text-[#625D5A] truncate">${escapeHtml(items[0].product_name)}</span>` : '');

            return `
                <article class="order-row grid grid-cols-[1.55fr_1.55fr_2.05fr_1.05fr_1.05fr] items-center min-h-[99px] px-[20px] border-b border-[#DDD9D7] transition-all duration-200 ease-out hover:bg-[#FFFBF9]"
                    data-order-id="${Number(order.id)}"
                    data-status="${escapeHtml(order.status === 'pending' ? 'new' : (order.status === 'to_ship' ? 'to-ship' : order.status))}"
                    data-tab="${escapeHtml(order.status === 'pending' ? 'new' : (order.status === 'to_ship' ? 'to-ship' : order.status))}"
                    data-pickup-date="${escapeHtml(order.pickup_date || '')}"
                    data-pickup-time="${escapeHtml(order.pickup_time || '')}"
                    data-search="${escapeHtml(`${orderNumber} ${customer} ${phone} ${itemNames.join(' ')}`)}">
                    <div class="flex items-center gap-[22px] min-w-0">
                        <div class="order-icon-box ${status.style}"><img src="/icons/seller/order-status/${status.icon}" alt="${escapeHtml(status.label)}"></div>
                        <div class="min-w-0">
                            <h3 class="order-id">#${escapeHtml(orderNumber)}</h3>
                            <p class="order-status ${status.style}-text"><span class="status-dot ${status.style}-dot"></span>${escapeHtml(status.label)}</p>
                        </div>
                    </div>
                    <div class="min-w-0"><p class="customer-name">${escapeHtml(customer)}</p><p class="customer-phone">${escapeHtml(phone)}</p></div>
                    <div class="flex items-center gap-[10px] min-w-0">${itemPreview}${moreItems || (items.length === 0 ? '<span class="text-[12px] text-[#8A8582]">No item details</span>' : '')}</div>
                    <div><p class="amount">${money(order.total)}</p><p class="payment">Payment: ${escapeHtml(order.payment_method || 'Cash on Delivery')}</p></div>
                    <div><p class="ordered-date">${escapeHtml(date)}</p><p class="ordered-time">${escapeHtml(time)}</p></div>
                </article>`;
        }).join('');

        rows = Array.from(orderTable.querySelectorAll('.order-row'));
        rows.forEach(ensureInitialTracking);
    }

    renderOrderRows(config.orders || []);

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

    function filterOrders() {
        const query =
            (searchInput?.value || '')
                .trim()
                .toLowerCase();

        let visibleCount = 0;

        rows.forEach(
            function (row) {
                const rowSearch =
                    (row.dataset.search || '')
                        .toLowerCase();

                const rowStatus =
                    row.dataset.status || '';

                const visible =
                    (
                        activeTab === 'all' ||
                        rowStatus === activeTab
                    ) &&
                    (
                        !query ||
                        rowSearch.includes(query)
                    );

                row.classList.toggle(
                    'hidden',
                    !visible
                );

                if (visible) {
                    visibleCount++;
                }
            }
        );

        if (showingCount) {
            showingCount.textContent =
                visibleCount;
        }

        if (totalEntriesCount) {
            totalEntriesCount.textContent =
                rows.length;
        }

        if (noResults) {
            noResults.classList.toggle(
                'hidden',
                visibleCount !== 0
            );
        }
    }

    searchInput?.addEventListener(
        'input',
        filterOrders
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
                        tab.dataset.tab || 'all';

                    filterOrders();
                }
            );
        }
    );

    function setCurrentPage(
        targetPage
    ) {
        pageButtons.forEach(
            function (item) {
                item.classList.toggle(
                    'current',
                    Number(
                        item.dataset.page
                    ) === targetPage
                );
            }
        );

        if (previousPage) {
            previousPage.classList.toggle(
                'disabled',
                targetPage === 1
            );
        }
    }

    pageButtons.forEach(
        function (button) {
            button.addEventListener(
                'click',
                function () {
                    setCurrentPage(
                        Number(
                            button.dataset.page
                        )
                    );
                }
            );
        }
    );

    previousPage?.addEventListener(
        'click',
        function () {
            const current =
                document.querySelector(
                    '.pagination-button.current[data-page]'
                );

            const target =
                Math.max(
                    1,
                    Number(
                        current?.dataset.page ||
                        1
                    ) - 1
                );

            setCurrentPage(
                target
            );
        }
    );

    nextPage?.addEventListener(
        'click',
        function () {
            const current =
                document.querySelector(
                    '.pagination-button.current[data-page]'
                );

            const target =
                Math.min(
                    3,
                    Number(
                        current?.dataset.page ||
                        1
                    ) + 1
                );

            setCurrentPage(
                target
            );
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
                                <div class="modal-product-image"><div class="modal-product-bag"></div></div>
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
            const history = [...order.status_history].reverse();
            const events = history
                .filter((event) => ['preparing', 'to_ship'].includes(event.to_status))
                .map((event) => {
                    const eventDate = event.created_at ? new Date(event.created_at) : null;
                    return {
                        type: event.to_status === 'to_ship' ? 'ready' : 'prepared',
                        date: eventDate && !Number.isNaN(eventDate.getTime())
                            ? new Intl.DateTimeFormat('en-US', { month: 'short', day: 'numeric', year: 'numeric' }).format(eventDate)
                            : '',
                        time: eventDate && !Number.isNaN(eventDate.getTime())
                            ? new Intl.DateTimeFormat('en-US', { hour: 'numeric', minute: '2-digit' }).format(eventDate)
                            : '',
                    };
                });
            setTrackingEvents(row, events);
            renderTrackingHistory(row);
        }
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

    function ensureInitialTracking(
        row
    ) {
        const status =
            row.dataset.status;

        if (
            status ===
                'preparing' &&
            !getTrackingEvents(
                row
            ).length
        ) {
            setTrackingEvents(
                row,
                [
                    makeTrackingEvent(
                        'prepared',
                        'May 22, 2026',
                        '11:45 PM'
                    )
                ]
            );
        }

        if (
            status ===
                'to-ship' &&
            !getTrackingEvents(
                row
            ).length
        ) {
            setTrackingEvents(
                row,
                [
                    makeTrackingEvent(
                        'prepared',
                        'May 22, 2026',
                        '11:45 PM'
                    ),

                    makeTrackingEvent(
                        'ready',

                        row.querySelector(
                            '.ordered-date'
                        )?.textContent
                            ?.trim() ||
                            'May 22, 2026',

                        row.querySelector(
                            '.ordered-time'
                        )?.textContent
                            ?.trim() ||
                            '11:45 PM'
                    )
                ]
            );
        }
    }

    rows.forEach(
        ensureInitialTracking
    );

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

    function renderTrackingHistory(
        row
    ) {
        if (
            !trackingHistorySection ||
            !trackingHistoryList
        ) {
            return;
        }

        const events =
            getTrackingEvents(
                row
            );

        if (
            !events.length
        ) {
            trackingHistorySection
                .classList
                .add(
                    'hidden'
                );

            trackingHistoryList
                .innerHTML =
                '';

            return;
        }

        trackingHistorySection
            .classList
            .remove(
                'hidden'
            );

        trackingHistoryList
            .innerHTML =
            '';

        events.forEach(
            function (
                event,
                index
            ) {
                const isActive =
                    index ===
                    events.length -
                        1;

                const wrapper =
                    document.createElement(
                        'div'
                    );

                wrapper.className =
                    'tracking-item';

                let title =
                    'Order Prepared';

                let description =
                    'You have prepared your order';

                let type =
                    'prepared';

                if (
                    event.type ===
                    'ready'
                ) {
                    title =
                        'Ready to Ship';

                    description =
                        'Your order is ready for courier pickup.';

                    type =
                        'ready';
                }

                wrapper.innerHTML =
                    '<div class="tracking-icon-wrap ' +
                    (
                        isActive
                            ? 'active'
                            : ''
                    ) +
                    '">' +
                    trackingIcon(
                        type,
                        isActive
                    ) +
                    '</div>' +

                    '<div class="tracking-time">' +
                    '<span class="date">' +
                    event.date +
                    '</span>' +
                    '<span class="time">' +
                    event.time +
                    '</span>' +
                    '</div>' +

                    '<div class="tracking-content">' +
                    '<div class="event-title">' +
                    title +
                    '</div>' +
                    '<div class="event-description">' +
                    description +
                    '</div>' +
                    '</div>';

                trackingHistoryList
                    .appendChild(
                        wrapper
                    );
            }
        );
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
                    'ready-text'
                );

            orderModalStatusText
                .classList
                .add(
                    isPreparing
                        ? 'preparing-text'
                        : (
                            isReady
                                ? 'ready-text'
                                : 'new-text'
                        )
                );
        }

        if (
            orderModalStatusDot
        ) {
            orderModalStatusDot.style.backgroundColor =
                isPreparing
                    ? '#FF9D00'
                    : (
                        isReady
                            ? '#08A11D'
                            : '#E21E1E'
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

    rows.forEach(
        function (row) {
            row.addEventListener(
                'click',
                function () {
                    if (
                        row.dataset.status ===
                            'new' ||
                        row.dataset.status ===
                            'preparing' ||
                        row.dataset.status ===
                            'to-ship'
                    ) {
                        openOrderDetailsModal(
                            row
                        );
                    }
                }
            );

            row.addEventListener(
                'keydown',
                function (
                    event
                ) {
                    if (
                        event.key ===
                            'Enter' ||
                        event.key ===
                            ' '
                    ) {
                        event.preventDefault();

                        openOrderDetailsModal(
                            row
                        );
                    }
                }
            );

            row.setAttribute(
                'tabindex',
                '0'
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
        function () {
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

                setTimeout(
                    function () {
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

                        setTrackingEvents(
                            selectedOrderRow,
                            [
                                makeTrackingEvent(
                                    'prepared',
                                    stamp.date,
                                    stamp.time
                                )
                            ]
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
                    },
                    400
                );

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
        function () {
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
                alert(
                    'Please select a pickup date and time.'
                );

                return;
            }

            const scheduledStamp =
                nowTrackingStamp();

            confirmScheduleShipmentButton.disabled =
                true;

            confirmScheduleShipmentButton.textContent =
                'Scheduling...';

            setTimeout(
                function () {
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
                },
                400
            );
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