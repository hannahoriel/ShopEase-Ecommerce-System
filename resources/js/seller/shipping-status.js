document.addEventListener('DOMContentLoaded', function () {
    const configElement = document.getElementById('sellerShippingStatusConfig');
    const config = configElement ? JSON.parse(configElement.textContent) : {};
    const sidebar = document.getElementById('sellerSidebar');
    const page = document.getElementById('shipping-status-page');
    const tabs = document.querySelectorAll('.shipping-status-tab');
    const rows = Array.from(document.querySelectorAll('.shipping-row'));
    const searchInput = document.getElementById('shippingSearch');
    const noResults = document.getElementById('shippingNoResults');
    const showingCount = document.getElementById('shippingShowingCount');
    const totalEntriesCount = document.getElementById('shippingTotalEntriesCount');
    const previousPage = document.getElementById('shippingPreviousPage');
    const nextPage = document.getElementById('shippingNextPage');
    const pageButtons = document.querySelectorAll('#shipping-status-page .pagination-button[data-page]');

    let activeTab = 'all';

    function syncPageOffset() {
        if (!page) return;

        if (window.innerWidth <= 760) {
            page.style.marginLeft = '0px';
            return;
        }

        const width = sidebar ? sidebar.getBoundingClientRect().width : 288;
        page.style.marginLeft = width + 'px';
    }

    syncPageOffset();
    window.addEventListener('resize', syncPageOffset);

    if (sidebar && typeof ResizeObserver !== 'undefined') {
        const sidebarObserver = new ResizeObserver(function () {
            syncPageOffset();
        });

        sidebarObserver.observe(sidebar);
    }

    function filterShippingRows() {
        const query = (searchInput?.value || '').trim().toLowerCase();
        let visibleCount = 0;

        rows.forEach(function (row) {
            const status = row.dataset.status || '';
            const searchable = (row.dataset.search || '').toLowerCase();

            let statusMatches = true;

            if (activeTab === 'in-transit') {
                statusMatches = status === 'in-transit' || status === 'out-for-delivery';
            } else if (activeTab === 'delivered') {
                statusMatches = status === 'delivered';
            }

            const searchMatches = !query || searchable.includes(query);
            const shouldShow = statusMatches && searchMatches;

            row.classList.toggle('hidden', !shouldShow);
            if (shouldShow) visibleCount++;
        });

        if (showingCount) showingCount.textContent = visibleCount;
        if (totalEntriesCount) totalEntriesCount.textContent = rows.length;
        if (noResults) noResults.classList.toggle('hidden', visibleCount !== 0);
    }

    searchInput?.addEventListener('input', filterShippingRows);

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            tabs.forEach(function (item) {
                item.classList.remove('active');
            });

            tab.classList.add('active');
            activeTab = tab.dataset.tab || 'all';
            filterShippingRows();
        });
    });

    function setCurrentPage(targetPage) {
        pageButtons.forEach(function (button) {
            button.classList.toggle('current', Number(button.dataset.page) === targetPage);
        });

        if (previousPage) {
            previousPage.classList.toggle('disabled', targetPage === 1);
        }
    }

    pageButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            setCurrentPage(Number(button.dataset.page));
        });
    });

    previousPage?.addEventListener('click', function () {
        const current = document.querySelector('#shipping-status-page .pagination-button.current[data-page]');
        const currentPage = Number(current?.dataset.page || 1);
        setCurrentPage(Math.max(1, currentPage - 1));
    });

    nextPage?.addEventListener('click', function () {
        const current = document.querySelector('#shipping-status-page .pagination-button.current[data-page]');
        const currentPage = Number(current?.dataset.page || 1);
        setCurrentPage(Math.min(3, currentPage + 1));
    });

    const modal = document.getElementById('shippingDetailsModal');
    const modalOrderId = document.getElementById('shippingModalOrderId');
    const modalCustomer = document.getElementById('shippingModalCustomer');
    const modalPhone = document.getElementById('shippingModalPhone');
    const modalOrderDate = document.getElementById('shippingModalOrderDate');
    const modalOrderTime = document.getElementById('shippingModalOrderTime');
    const modalEstimated = document.getElementById('shippingModalEstimated');
    const modalTracking = document.getElementById('shippingModalTrackingNumber');
    const modalStatusLabel = document.getElementById('shippingModalStatusLabel');
    const modalStatusDot = document.getElementById('shippingModalStatusDot');
    const modalIconBox = document.getElementById('shippingModalIconBox');
    const modalIcon = document.getElementById('shippingModalOrderIcon');
    const trackingList = document.getElementById('shippingTrackingHistoryList');
    const progressTransit = document.querySelector('#modalProgressTransit img');
    const progressOut = document.querySelector('#modalProgressOut img');
    const progressDelivered = document.querySelector('#modalProgressDelivered img');
    const progressLine1 = document.getElementById('modalProgressLine1');
    const progressLine2 = document.getElementById('modalProgressLine2');
    const closeButton = document.getElementById('closeShippingModal');
    const advanceButton = document.getElementById('shippingAdvanceButton');

    let selectedRow = null;
    let selectedOrder = null;

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, (character) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
        })[character]);
    }

    function statusDisplay(status) {
        return ({
            new: 'Order Placed',
            pending: 'Order Placed',
            preparing: 'Preparing',
            to_ship: 'Ready to Ship',
            in_transit: 'In Transit',
            out_for_delivery: 'Out for Delivery',
            delivered: 'Delivered',
        })[status] || 'Shipping';
    }

    async function requestJson(url, options = {}) {
        const response = await fetch(url, {
            ...options,
            headers: {
                Accept: 'application/json',
                ...(options.body ? { 'Content-Type': 'application/json' } : {}),
                Authorization: `Bearer ${config.apiToken || ''}`,
                'X-Requested-With': 'XMLHttpRequest',
                ...(options.headers || {}),
            },
            credentials: 'same-origin',
        });
        const payload = await response.json();
        if (!response.ok) {
            throw new Error(payload.message || `Shipping request failed (${response.status}).`);
        }
        return payload;
    }

    function trackingIcon(type, active, green) {
        const stroke = active ? '#FFFFFF' : (green ? '#2A8B36' : '#185B8C');

        if (type === 'prepared') {
            return `
                <svg viewBox="0 0 24 24" fill="none" stroke="${stroke}" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 8h14v11H5z"></path>
                    <path d="M8 8V5h8v3"></path>
                    <path d="M9 12h6"></path>
                </svg>
            `;
        }

        if (type === 'ready') {
            return `
                <svg viewBox="0 0 24 24" fill="none" stroke="${stroke}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 6h14"></path>
                    <path d="M8 6v4"></path>
                    <path d="M16 6v4"></path>
                    <path d="M5 10h14v9H5z"></path>
                    <path d="M9 14h6"></path>
                </svg>
            `;
        }

        if (type === 'picked') {
            return `
                <svg viewBox="0 0 24 24" fill="none" stroke="${stroke}" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="5" y="8" width="14" height="11" rx="2"></rect>
                    <path d="M8 8V5h8v3"></path>
                </svg>
            `;
        }

        if (type === 'sorting') {
            return `
                <svg viewBox="0 0 24 24" fill="none" stroke="${stroke}" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="5" y="4" width="14" height="16" rx="2"></rect>
                    <path d="M8 8h8"></path>
                    <path d="M8 12h8"></path>
                    <path d="M8 16h5"></path>
                </svg>
            `;
        }

        if (type === 'transit') {
            return `
                <svg viewBox="0 0 24 24" fill="none" stroke="${stroke}" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 16h14"></path>
                    <path d="M14 7h4l3 4v5h-3"></path>
                    <circle cx="7" cy="17" r="2"></circle>
                    <circle cx="18" cy="17" r="2"></circle>
                </svg>
            `;
        }

        if (type === 'out') {
            return `
                <svg viewBox="0 0 24 24" fill="none" stroke="${stroke}" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 16h14"></path>
                    <path d="M14 7h4l3 4v5h-3"></path>
                    <circle cx="7" cy="17" r="2"></circle>
                    <circle cx="18" cy="17" r="2"></circle>
                </svg>
            `;
        }

        if (type === 'delivered') {
            return `
                <svg viewBox="0 0 24 24" fill="none" stroke="${stroke}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12l4 4L19 6"></path>
                </svg>
            `;
        }

        return '';
    }

    function renderTrackingHistory(order, row) {
        if (!trackingList) return;

        const events = [...(order.status_history || [])].reverse();
        trackingList.innerHTML = '';
        if (!events.length) {
            trackingList.textContent = 'No status history has been recorded yet.';
            return;
        }

        events.forEach(function (event, index) {
            const isLast = index === events.length - 1;
            const status = event.to_status;
            const type = status === 'new' || status === 'pending' || status === 'preparing' ? 'prepared'
                : status === 'to_ship' ? 'ready'
                    : status === 'in_transit' ? 'transit'
                        : status === 'out_for_delivery' ? 'out'
                            : status === 'delivered' ? 'delivered' : 'sorting';
            const changedAt = event.created_at ? new Date(event.created_at) : null;
            const date = changedAt && !Number.isNaN(changedAt.getTime())
                ? changedAt.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })
                : '—';
            const time = changedAt && !Number.isNaN(changedAt.getTime())
                ? changedAt.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' })
                : '';
            const item = document.createElement('div');
            item.className = 'shipping-history-item';

            const iconClass = isLast ? (status === 'delivered' ? 'active green-active' : 'active') : '';

            item.innerHTML = `
                <div class="shipping-history-icon ${iconClass}">
                    ${trackingIcon(type, isLast, status === 'delivered')}
                </div>

                <div class="shipping-history-time">
                    <span class="date">${escapeHtml(date)}</span>
                    <span class="time">${escapeHtml(time)}</span>
                </div>

                <div class="shipping-history-content">
                    <strong>${escapeHtml(statusDisplay(status))}</strong>
                    <p>${escapeHtml(event.notes || `Order status changed to ${statusDisplay(status)}.`)}</p>
                </div>
            `;

            trackingList.appendChild(item);
        });
    }

    function updateProgress(status) {
        if (!progressTransit || !progressOut || !progressDelivered) return;

        if (status === 'to_ship' || status === 'in_transit') {
            progressTransit.src = '/icons/seller/shipping-status/in-transit-blue.png';
            progressOut.src = '/icons/seller/shipping-status/out-for-delivery-gray.png';
            progressDelivered.src = '/icons/seller/shipping-status/delivered-gray.png';
            progressLine1?.classList.remove('completed');
            progressLine2?.classList.remove('completed');
            return;
        }

        if (status === 'out_for_delivery') {
            progressTransit.src = '/icons/seller/shipping-status/in-transit-check.png';
            progressOut.src = '/icons/seller/shipping-status/out-for-delivery.png';
            progressDelivered.src = '/icons/seller/shipping-status/delivered-gray.png';
            progressLine1?.classList.add('completed');
            progressLine2?.classList.remove('completed');
            return;
        }

        if (status === 'delivered') {
            progressTransit.src = '/icons/seller/shipping-status/delivered-check.png';
            progressOut.src = '/icons/seller/shipping-status/delivered-check.png';
            progressDelivered.src = '/icons/seller/shipping-status/delivered-check.png';
            progressLine1?.classList.add('completed');
            progressLine2?.classList.add('completed');
        }
    }

    async function openShippingModal(row) {
        if (!modal || !row) return;

        selectedRow = row;
        selectedOrder = null;
        if (advanceButton) advanceButton.hidden = true;

        const id = row.dataset.orderId;
        modalOrderId.textContent = `#${row.dataset.orderNumber}`;
        modalCustomer.textContent = row.dataset.customer;
        modalPhone.textContent = row.dataset.phone || '—';
        modalTracking.textContent = row.dataset.tracking || 'Pending';
        modalEstimated.textContent = row.dataset.estimated || 'Not scheduled';

        modal.classList.add('modal-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');

        try {
            const order = await requestJson(`${config.ordersUrl}/${id}`);
            if (selectedRow !== row) return;
            selectedOrder = order;
            const status = order.status;
            const shipment = order.shipment || {};
            const createdAt = order.created_at ? new Date(order.created_at) : null;
            const labels = {
                to_ship: ['blue-dot', 'blue-modal', 'in-transit.png'],
                in_transit: ['blue-dot', 'blue-modal', 'in-transit.png'],
                out_for_delivery: ['orange-dot', 'blue-modal', 'in-transit.png'],
                delivered: ['green-dot', 'green-modal', 'delivered.png'],
            };
            const [dotClass, boxClass, iconName] = labels[status] || labels.to_ship;

            modalOrderId.textContent = `#${order.order_number || `ORD-${order.id}`}`;
            modalCustomer.textContent = order.delivery_name || order.buyer?.name || 'Customer';
            modalPhone.textContent = order.delivery_phone || order.buyer?.contact_no || '—';
            document.getElementById('shippingModalAddress').textContent = order.delivery_address || '—';
            document.getElementById('shippingModalPaymentMethod').textContent = order.payment_method || 'COD';
            document.getElementById('shippingModalPaymentDescription').textContent =
                String(order.payment_method || 'COD').toLowerCase().includes('cash')
                    ? 'Payment upon Delivery'
                    : 'Payment details';
            document.getElementById('shippingModalNotes').textContent = order.customer_notes || 'No customer notes provided.';
            modalOrderDate.textContent = createdAt && !Number.isNaN(createdAt.getTime())
                ? createdAt.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })
                : '—';
            modalOrderTime.textContent = createdAt && !Number.isNaN(createdAt.getTime())
                ? createdAt.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' })
                : '';
            const estimatedDelivery = shipment.estimated_delivery
                ? String(shipment.estimated_delivery).slice(0, 10)
                : '';
            modalEstimated.textContent = estimatedDelivery
                ? new Date(`${estimatedDelivery}T00:00:00`).toLocaleDateString('en-US', {
                    month: 'long', day: 'numeric', year: 'numeric',
                })
                : 'Not scheduled';
            modalTracking.textContent = shipment.tracking_number || 'Pending';
            modalStatusLabel.textContent = statusDisplay(status);
            modalStatusDot.className = `modal-status-dot ${dotClass}`;
            modalIconBox.className = `shipping-modal-status-box ${boxClass}`;
            modalIcon.src = `/icons/seller/shipping-status/${iconName}`;
            modalIcon.alt = statusDisplay(status);
            updateProgress(status);
            renderTrackingHistory(order, row);

            const itemsList = document.getElementById('shippingModalItemsList');
            if (itemsList) {
                const items = order.items || [];
                const subtotal = items.reduce((sum, item) =>
                    sum + Number(item.unit_price || 0) * Number(item.quantity || 0), 0);
                const shippingFee = Number(shipment.shipping_fee || 0);
                const money = (amount) => `₱${amount.toLocaleString('en-PH', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2,
                })}`;
                itemsList.innerHTML = `
                    <div class="shipping-item-header"><span>Item</span><span>Price</span><span>Quantity</span><span>Subtotal</span></div>
                    ${items.length ? items.map((item) => `
                        <div class="shipping-item-row">
                            <div class="shipping-item-name">
                                <div class="shipping-item-image"><div class="modal-product-bag"></div></div>
                                <span>${escapeHtml(item.product_name || 'Product')}</span>
                            </div>
                            <span>${money(Number(item.unit_price || 0))}</span>
                            <span>${Number(item.quantity || 0)}</span>
                            <span>${money(Number(item.unit_price || 0) * Number(item.quantity || 0))}</span>
                        </div>`).join('') : '<p>No items were recorded for this order.</p>'}
                    <div class="shipping-modal-totals">
                        <div><span>Subtotal</span><strong>${money(subtotal)}</strong></div>
                        <div><span>Shipping Fee</span><strong>${money(shippingFee)}</strong></div>
                        <div><span>Total Amount</span><b>${money(Number(order.total || 0))}</b></div>
                    </div>`;
            }

            if (advanceButton) {
                const next = ({
                    to_ship: ['in_transit', 'Mark In Transit'],
                    in_transit: ['out_for_delivery', 'Mark Out for Delivery'],
                    out_for_delivery: ['delivered', 'Mark Delivered'],
                })[status];
                advanceButton.dataset.nextStatus = next?.[0] || '';
                advanceButton.hidden = !next;
                advanceButton.disabled = !next;
                if (next) advanceButton.textContent = next[1];
            }
        } catch (error) {
            trackingList.textContent = error.message;
            if (advanceButton) advanceButton.hidden = true;
        }
    }

    function closeShippingModal() {
        if (!modal) return;

        modal.classList.remove('modal-open');
        modal.setAttribute('aria-hidden', 'true');

        setTimeout(function () {
            document.body.classList.remove('overflow-hidden');
            selectedRow = null;
        }, 210);
    }

    rows.forEach(function (row) {
        row.setAttribute('tabindex', '0');
        row.setAttribute('role', 'button');

        row.addEventListener('click', function () {
            openShippingModal(row);
        });

        row.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                openShippingModal(row);
            }
        });
    });

    closeButton?.addEventListener('click', closeShippingModal);

    advanceButton?.addEventListener('click', async function () {
        if (!selectedRow || !advanceButton.dataset.nextStatus) return;

        const nextStatus = advanceButton.dataset.nextStatus;
        const originalLabel = advanceButton.textContent;
        advanceButton.disabled = true;
        advanceButton.textContent = 'Saving...';

        try {
            await requestJson(`${config.ordersUrl}/${selectedRow.dataset.orderId}/status`, {
                method: 'PATCH',
                body: JSON.stringify({
                    status: nextStatus,
                    notes: `Shipping status updated to ${statusDisplay(nextStatus)}.`,
                }),
            });
            window.location.reload();
        } catch (error) {
            window.alert(error.message);
            advanceButton.disabled = false;
            advanceButton.textContent = originalLabel;
        }
    });

    modal?.addEventListener('click', function (event) {
        if (event.target === modal) {
            closeShippingModal();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && modal?.classList.contains('modal-open')) {
            closeShippingModal();
        }
    });

    filterShippingRows();
});