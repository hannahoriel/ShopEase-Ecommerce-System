import QRCode from 'qrcode';

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
            const pickupDate = order.pickup_date_display || '';
            const pickupTime = order.pickup_time_display || '';
            const orderNumber = order.order_number || `ORD-${order.id}`;
            const customer = order.delivery_name || order.buyer?.name || 'Customer';
            const phone = order.delivery_phone || order.buyer?.contact_no || '';
            const date = pickupDate && ['to_ship', 'to-ship'].includes(order.status)
                ? formatDateDisplay(pickupDate)
                : (createdAt && !Number.isNaN(createdAt.getTime())
                ? new Intl.DateTimeFormat('en-US', { month: 'short', day: '2-digit', year: 'numeric' }).format(createdAt)
                : '');
            const time = pickupTime && ['to_ship', 'to-ship'].includes(order.status)
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
                    data-status="${escapeHtml(order.status === 'pending' ? 'new' : (order.status === 'to_ship' ? 'to-ship' : order.status))}"
                    data-tab="${escapeHtml(order.status === 'pending' ? 'new' : (order.status === 'to_ship' ? 'to-ship' : order.status))}"
                    data-pickup-date="${escapeHtml(pickupDate)}"
                    data-pickup-time="${escapeHtml(pickupTime)}"
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
    const printWaybillButton = document.getElementById('printOrderWaybillButton');

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
            alert('Please allow pop-ups to print the waybill.');
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
            const statusHistoryOrder = {
                new: [],
                pending: [],
                preparing: ['preparing'],
                to_ship: ['preparing', 'to_ship'],
                in_transit: ['preparing', 'to_ship'],
                out_for_delivery: ['preparing', 'to_ship'],
                delivered: ['preparing', 'to_ship'],
            };
            const eligibleStatuses = statusHistoryOrder[order.status] || [];
            const history = [...order.status_history]
                .filter((event) => eligibleStatuses.includes(event.to_status))
                .reverse();
            const events = history
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

        const isNewOrder =
            status === 'new' || status === 'pending';
        if (printWaybillButton) printWaybillButton.hidden = !isNewOrder;

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
                } catch (error) {
                    alert(error.message);
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
                alert(
                    'Please select a pickup date and time.'
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
            } catch (error) {
                alert(error.message);
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