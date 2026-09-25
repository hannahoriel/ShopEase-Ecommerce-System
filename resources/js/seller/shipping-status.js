document.addEventListener('DOMContentLoaded', function () {
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
            }

            if (activeTab === 'delivered') {
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

    let selectedRow = null;

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

    function getTrackingHistory(row) {
        const status = row.dataset.status || 'in-transit';
        const events = [
            {
                type: 'prepared',
                date: row.dataset.preparedDate || row.dataset.orderDate || 'May 22, 2026',
                time: row.dataset.preparedTime || '11:45 AM',
                title: 'Order Prepared',
                description: 'The seller prepared the order for shipment.'
            },
            {
                type: 'ready',
                date: row.dataset.readyDate || row.dataset.orderDate || 'May 22, 2026',
                time: row.dataset.readyTime || '12:05 PM',
                title: 'Ready to Ship',
                description: 'The order is ready for courier pickup.'
            },
            {
                type: 'picked',
                date: row.dataset.pickedDate || 'May 23, 2026',
                time: row.dataset.pickedTime || '9:15 AM',
                title: 'Picked Up',
                description: 'Ease Express has picked up the parcel.'
            },
            {
                type: 'sorting',
                date: row.dataset.sorting1Date || 'May 23, 2026',
                time: row.dataset.sorting1Time || '2:10 PM',
                title: 'Arrived at Sta. Cruz Sorting Center',
                description: 'The parcel arrived at the sorting center.'
            },
            {
                type: 'sorting',
                date: row.dataset.sorting2Date || 'May 24, 2026',
                time: row.dataset.sorting2Time || '8:35 AM',
                title: 'Arrived at Lumban Sorting Center',
                description: 'The parcel was transferred to the next sorting center.'
            },
            {
                type: 'transit',
                date: row.dataset.transitDate || 'May 24, 2026',
                time: row.dataset.transitTime || '1:20 PM',
                title: 'In Transit',
                description: 'The parcel is currently on its way to the destination.'
            }
        ];

        if (status === 'out-for-delivery' || status === 'delivered') {
            events.push({
                type: 'out',
                date: row.dataset.outDate || 'May 25, 2026',
                time: row.dataset.outTime || '8:05 AM',
                title: 'Out for Delivery',
                description: 'The courier is on the way to deliver the parcel.'
            });
        }

        if (status === 'delivered') {
            events.push({
                type: 'delivered',
                date: row.dataset.deliveredDate || 'May 25, 2026',
                time: row.dataset.deliveredTime || '9:32 AM',
                title: 'Delivered',
                description: 'The order has been successfully delivered.'
            });
        }

        return events;
    }

    function renderTrackingHistory(row) {
        if (!trackingList) return;

        const events = getTrackingHistory(row);
        trackingList.innerHTML = '';

        events.forEach(function (event, index) {
            const isLast = index === events.length - 1;
            const isDelivered = row.dataset.status === 'delivered';
            const item = document.createElement('div');
            item.className = 'shipping-history-item';

            const iconClass = isLast ? (isDelivered ? 'active green-active' : 'active') : '';

            item.innerHTML = `
                <div class="shipping-history-icon ${iconClass}">
                    ${trackingIcon(event.type, isLast, isDelivered)}
                </div>

                <div class="shipping-history-time">
                    <span class="date">${event.date}</span>
                    <span class="time">${event.time}</span>
                </div>

                <div class="shipping-history-content">
                    <strong>${event.title}</strong>
                    <p>${event.description}</p>
                </div>
            `;

            trackingList.appendChild(item);
        });
    }

    function updateProgress(status) {
        if (!progressTransit || !progressOut || !progressDelivered) return;

        if (status === 'in-transit') {
            progressTransit.src = '/icons/seller/shipping-status/in-transit-blue.png';
            progressOut.src = '/icons/seller/shipping-status/out-for-delivery-gray.png';
            progressDelivered.src = '/icons/seller/shipping-status/delivered-gray.png';
            progressLine1?.classList.remove('completed');
            progressLine2?.classList.remove('completed');
            return;
        }

        if (status === 'out-for-delivery') {
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

    function openShippingModal(row) {
        if (!modal || !row) return;

        selectedRow = row;

        const status = row.dataset.status || 'in-transit';
        const orderId = row.dataset.orderId || '#ORD-2025';
        const customer = row.dataset.customer || 'Juan Dela Cruz';
        const phone = row.dataset.phone || '0917 123 4567';
        const orderDate = row.dataset.orderDate || 'May 22, 2026';
        const orderTime = row.dataset.orderTime || '10:34 AM';
        const estimated = row.dataset.estimated || 'May 25, 2026';
        const tracking = row.dataset.tracking || 'T23430583RHEFBW';

        modalOrderId.textContent = orderId;
        modalCustomer.textContent = customer;
        modalPhone.textContent = phone;
        modalOrderDate.textContent = orderDate;
        modalOrderTime.textContent = orderTime;
        modalEstimated.textContent = estimated;
        modalTracking.textContent = tracking;

        if (status === 'delivered') {
            modalStatusLabel.textContent = 'Delivered';
            modalStatusDot.className = 'modal-status-dot green-dot';
            modalIconBox.className = 'shipping-modal-status-box green-modal';
            modalIcon.src = '/icons/seller/shipping-status/delivered.png';
        } else if (status === 'out-for-delivery') {
            modalStatusLabel.textContent = 'Out for Delivery';
            modalStatusDot.className = 'modal-status-dot orange-dot';
            modalIconBox.className = 'shipping-modal-status-box blue-modal';
            modalIcon.src = '/icons/seller/shipping-status/in-transit.png';
        } else {
            modalStatusLabel.textContent = 'In Transit';
            modalStatusDot.className = 'modal-status-dot blue-dot';
            modalIconBox.className = 'shipping-modal-status-box blue-modal';
            modalIcon.src = '/icons/seller/shipping-status/in-transit.png';
        }

        modalIcon.alt = modalStatusLabel.textContent;
        updateProgress(status);
        renderTrackingHistory(row);
        modal.classList.add('modal-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
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
