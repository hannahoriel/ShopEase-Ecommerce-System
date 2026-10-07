import QRCode from 'qrcode';

document.addEventListener('DOMContentLoaded', () => {
            const configElement = document.getElementById('buyerPurchasesConfig');
            const purchasesConfig = configElement ? JSON.parse(configElement.textContent) : {};
            const sideTabs =
                Array.from(document.querySelectorAll('[data-side-panel]'));

            const sidePanels =
                Array.from(document.querySelectorAll('[data-panel-content]'));

            const showSidePanel = (panelName) => {
                sideTabs.forEach(tab => {
                    tab.classList.toggle(
                        'is-active',
                        tab.dataset.sidePanel === panelName
                    );
                });

                sidePanels.forEach(panel => {
                    const active =
                        panel.dataset.panelContent === panelName;

                    panel.hidden = !active;
                    panel.classList.toggle('is-active', active);
                });
            };

            sideTabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    showSidePanel(tab.dataset.sidePanel);
                });
            });

            const notificationItems =
                Array.from(document.querySelectorAll('.buyer-notification-item'));

            notificationItems.forEach(item => {
                item.addEventListener('click', () => {
                    item.classList.remove('is-unread');
                    item.querySelector('.buyer-notification-unread-dot')?.remove();
                });
            });

            document
                .getElementById('buyerMarkAllRead')
                ?.addEventListener('click', () => {
                    notificationItems.forEach(item => {
                        item.classList.remove('is-unread');
                        item.querySelector('.buyer-notification-unread-dot')?.remove();
                    });
                });

            const tabs =
                Array.from(
                    document.querySelectorAll('[data-purchase-tab]')
                );

            let rows =
                Array.from(
                    document.querySelectorAll('[data-purchase-row]')
                );

            const search =
                document.getElementById('purchaseSearch');

            const emptyState =
                document.getElementById('purchaseEmptyState');

            const purchaseRows =
                document.getElementById('purchaseRows');

            const initialTab =
                document.body?.dataset?.activePurchaseTab || 'all';

            const validTabs =
                tabs.map(tab => tab.dataset.purchaseTab);

            let activeTab =
                validTabs.includes(initialTab)
                    ? initialTab
                    : 'all';

            const applyPurchaseFilters = () => {
                const query =
                    (search?.value || '')
                        .trim()
                        .toLowerCase();

                let visibleCount = 0;

                rows.forEach(row => {
                    const rowStatus =
                        row.dataset.status || '';

                    const rowSearch =
                        row.dataset.search || '';

                    const tabMatch =
                        activeTab === 'all' ||
                        rowStatus === activeTab;

                    const searchMatch =
                        !query ||
                        rowSearch.includes(query);

                    const show =
                        tabMatch &&
                        searchMatch;

                    row.hidden = !show;

                    if (show) {
                        visibleCount++;
                    }
                });

                if (purchaseRows) {
                    purchaseRows.hidden =
                        visibleCount === 0;
                }

                if (emptyState) {
                    emptyState.hidden =
                        visibleCount > 0;
                }
            };

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    activeTab =
                        tab.dataset.purchaseTab || 'all';

                    tabs.forEach(item => {
                        const active =
                            item === tab;

                        item.classList.toggle(
                            'is-active',
                            active
                        );

                        item.setAttribute(
                            'aria-selected',
                            active ? 'true' : 'false'
                        );
                    });

                    applyPurchaseFilters();

                    const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, character => ({
                        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
                    })[character]);

                    const statusData = status => {
                        const states = {
                            pending: ['processing', 'Processing'],
                            new: ['processing', 'Processing'],
                            preparing: ['processing', 'Processing'],
                            to_ship: ['to-ship', 'To Ship'],
                            in_transit: ['in-transit', 'In Transit'],
                            out_for_delivery: ['out-for-delivery', 'Out for Delivery'],
                            delivered: ['delivered', 'Delivered'],
                            completed: ['delivered', 'Completed'],
                            cancelled: ['cancelled', 'Cancelled'],
                            refunded: ['cancelled', 'Refunded'],
                        };
                        return states[status] || ['processing', 'Processing'];
                    };

                    function renderOrders(orders) {
                        if (!purchaseRows) return;
                        if (!orders.length) {
                            purchaseRows.innerHTML = '';
                            rows = [];
                            applyPurchaseFilters();
                            if (emptyState) {
                                emptyState.querySelector('h3').textContent = 'No orders yet';
                                emptyState.querySelector('p').textContent = 'Your completed checkouts will appear here with their order QR codes.';
                            }
                            return;
                        }

                        purchaseRows.hidden = false;
                        purchaseRows.innerHTML = orders.map(order => {
                            const [statusClass, statusLabel] = statusData(order.status);
                            const firstItem = order.items[0] || {};
                            const variant = [firstItem.variation, firstItem.color, firstItem.size].filter(Boolean).join(' · ');
                            const placedAt = order.created_at
                                ? new Intl.DateTimeFormat('en-PH', { dateStyle: 'medium', timeStyle: 'short' }).format(new Date(order.created_at))
                                : '';
                            const moreItems = Math.max(0, order.items.length - 1);
                            return `
                                <article class="purchase-shop-card" data-purchase-row data-status="${statusClass}" data-search="${escapeHtml(`${order.order_number} ${order.shop} ${order.items.map(item => item.product_name).join(' ')}`.toLowerCase())}">
                                    <div class="purchase-shop-header">
                                        <div class="purchase-shop-heading">
                                            <span class="purchase-shop-icon" aria-hidden="true">▣</span>
                                            <strong>${escapeHtml(order.shop)}</strong>
                                        </div>
                                        <div class="purchase-shop-order-meta">
                                            <strong class="purchase-shop-order-id">Order No: ${escapeHtml(order.order_number)}</strong>
                                            <span class="purchase-shop-placed-at">Placed ${escapeHtml(placedAt)}</span>
                                            <button type="button" class="purchase-message-seller" data-buyer-chat-order="${escapeHtml(order.id)}">Message seller</button>
                                        </div>
                                    </div>
                                    <div class="purchase-row">
                                        <div class="purchase-product-cell">
                                            <div class="purchase-product-image purchase-product-placeholder" aria-hidden="true">ShopEase</div>
                                            <div class="purchase-product-copy">
                                                <strong>${escapeHtml(firstItem.product_name || 'Order items')}</strong>
                                                <small>${escapeHtml(variant || 'Standard')}</small>
                                                <div class="purchase-product-price"><span>${money(firstItem.unit_price || 0)}</span></div>
                                                ${moreItems ? `<div class="purchase-more-products">+${moreItems} more ${moreItems === 1 ? 'product' : 'products'} from this shop</div>` : ''}
                                            </div>
                                        </div>
                                        <div class="purchase-quantity">${firstItem.quantity || 0}</div>
                                        <div class="purchase-total">${money(order.total)}</div>
                                        <div class="purchase-estimated-delivery">—</div>
                                        <div class="purchase-status-cell"><span class="purchase-status purchase-status--${statusClass}">${escapeHtml(statusLabel)}</span></div>
                                    </div>
                                    <div class="purchase-order-qr">
                                        <canvas width="124" height="124" aria-label="QR code for order ${escapeHtml(order.order_number)}"></canvas>
                                        <span>Scan to verify order <strong>${escapeHtml(order.order_number)}</strong></span>
                                    </div>
                                </article>`;
                        }).join('');
                        rows = Array.from(purchaseRows.querySelectorAll('[data-purchase-row]'));
                        rows.forEach((row, index) => {
                            const canvas = row.querySelector('canvas');
                            QRCode.toCanvas(canvas, orders[index].qr_payload, {
                                width: 124,
                                margin: 1,
                                errorCorrectionLevel: 'M',
                            }).catch(error => {
                                const note = row.querySelector('.purchase-order-qr span');
                                if (note) note.textContent = `QR could not be generated: ${error.message}`;
                            });
                        });
                        applyPurchaseFilters();
                    }

                    async function loadOrders() {
                        if (!purchasesConfig.apiToken || !purchaseRows) return;
                        if (emptyState) {
                            emptyState.hidden = false;
                            emptyState.querySelector('h3').textContent = 'Loading orders…';
                            emptyState.querySelector('p').textContent = '';
                        }
                        try {
                            const response = await fetch(purchasesConfig.ordersUrl || '/api/v1/buyer/orders', {
                                headers: {
                                    Accept: 'application/json',
                                    Authorization: `Bearer ${purchasesConfig.apiToken}`,
                                },
                                credentials: 'same-origin',
                            });
                            if (!response.ok) throw new Error(`Orders could not be loaded (${response.status}).`);
                            const payload = await response.json();
                            renderOrders(payload.data || []);
                        } catch (error) {
                            if (emptyState) {
                                emptyState.hidden = false;
                                emptyState.querySelector('h3').textContent = 'Unable to load orders';
                                emptyState.querySelector('p').textContent = error.message;
                            }
                        }
                    }

                    function money(value) {
                        return `₱${Number(value || 0).toLocaleString('en-PH', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2,
                        })}`;
                    }

                    loadOrders();
                });
            });

            search?.addEventListener(
                'input',
                applyPurchaseFilters
            );

            tabs.forEach(tab => {
                const active =
                    tab.dataset.purchaseTab === activeTab;

                tab.classList.toggle('is-active', active);
                tab.setAttribute(
                    'aria-selected',
                    active ? 'true' : 'false'
                );
            });

            applyPurchaseFilters();
        });