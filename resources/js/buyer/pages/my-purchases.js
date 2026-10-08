import QRCode from 'qrcode';

document.addEventListener('DOMContentLoaded', () => {
            const configElement = document.getElementById('buyerPurchasesConfig');
            const purchasesConfig = configElement ? JSON.parse(configElement.textContent) : {};
            const complaintModal = document.getElementById('buyerComplaintModal');
            const complaintForm = document.getElementById('buyerComplaintForm');
            const complaintError = document.getElementById('buyerComplaintError');
            const complaintSubmit = document.getElementById('buyerComplaintSubmit');
            const complaintOrderId = document.getElementById('buyerComplaintOrderId');
            const complaintOrderLabel = document.getElementById('buyerComplaintOrderLabel');
            const orderCancellationModal = document.getElementById('buyerOrderCancellationModal');
            const orderCancellationLabel = document.getElementById('buyerOrderCancellationLabel');
            const orderCancellationReason = document.getElementById('buyerOrderCancellationReason');
            const orderCancellationOtherWrap = document.getElementById('buyerOrderCancellationOtherWrap');
            const orderCancellationOther = document.getElementById('buyerOrderCancellationOther');
            const orderCancellationError = document.getElementById('buyerOrderCancellationError');
            const orderCancellationSubmit = document.getElementById('buyerOrderCancellationSubmit');
            let selectedCancellationButton = null;

            const closeComplaintModal = () => {
                complaintModal?.classList.add('hidden');
                complaintModal?.classList.remove('flex');
                complaintModal?.setAttribute('aria-hidden', 'true');
            };

            document.addEventListener('click', event => {
                const reportButton = event.target.closest('[data-buyer-complaint-order]');
                if (reportButton) {
                    complaintOrderId.value = reportButton.dataset.buyerComplaintOrder;
                    complaintOrderLabel.textContent = `Order ${reportButton.dataset.orderNumber || ''}`;
                    complaintError.hidden = true;
                    complaintForm.reset();
                    complaintOrderId.value = reportButton.dataset.buyerComplaintOrder;
                    complaintModal.classList.remove('hidden');
                    complaintModal.classList.add('flex');
                    complaintModal.setAttribute('aria-hidden', 'false');
                    document.getElementById('buyerComplaintType')?.focus();
                    return;
                }

                if (event.target.closest('#buyerComplaintClose, #buyerComplaintCancel') ||
                    event.target === complaintModal) {
                    closeComplaintModal();
                }
            });

            document.addEventListener('click', async event => {
                const cancelButton = event.target.closest('[data-buyer-cancel-order]');
                if (!cancelButton) return;

                selectedCancellationButton = cancelButton;
                orderCancellationLabel.textContent = `Order ${cancelButton.dataset.orderNumber || ''}`;
                orderCancellationReason.value = '';
                orderCancellationOther.value = '';
                orderCancellationOtherWrap.hidden = true;
                orderCancellationError.hidden = true;
                orderCancellationModal.classList.remove('hidden');
                orderCancellationModal.classList.add('flex');
                orderCancellationModal.setAttribute('aria-hidden', 'false');
                orderCancellationReason.focus();
                return;

                cancelButton.disabled = true;
                cancelButton.textContent = 'Cancelling…';

                try {
                    const response = await fetch(
                        `${purchasesConfig.ordersUrl || '/api/v1/buyer/orders'}/${encodeURIComponent(cancelButton.dataset.buyerCancelOrder)}/cancel`,
                        {
                            method: 'POST',
                            headers: {
                                Accept: 'application/json',
                                Authorization: `Bearer ${purchasesConfig.apiToken || ''}`,
                            },
                            credentials: 'same-origin',
                        }
                    );
                    const payload = await response.json();

                    if (!response.ok) {
                        throw new Error(payload.message || 'This order could not be cancelled.');
                    }

                    const orderCard = cancelButton.closest('[data-purchase-row]');
                    const [statusClass, statusLabel] = statusData('cancelled');
                    const status = orderCard?.querySelector('.purchase-status');

                    if (orderCard && status) {
                        orderCard.dataset.status = statusClass;
                        status.className = `purchase-status purchase-status--${statusClass}`;
                        status.textContent = statusLabel;
                    }

                    cancelButton.remove();
                    applyPurchaseFilters();
                    window.alert(payload.message || 'Order cancelled and stock restored.');
                } catch (error) {
                    window.alert(error.message);
                    cancelButton.disabled = false;
                    cancelButton.textContent = 'Cancel order';
                }
            });

            orderCancellationReason?.addEventListener('change', () => {
                const isOther = orderCancellationReason.value === 'other';
                orderCancellationOtherWrap.hidden = !isOther;
                if (!isOther) orderCancellationOther.value = '';
            });

            document.addEventListener('click', event => {
                if (event.target.closest('#buyerOrderCancellationClose, #buyerOrderCancellationKeep') ||
                    event.target === orderCancellationModal) {
                    orderCancellationModal.classList.add('hidden');
                    orderCancellationModal.classList.remove('flex');
                    orderCancellationModal.setAttribute('aria-hidden', 'true');
                    selectedCancellationButton = null;
                }
            });

            orderCancellationSubmit?.addEventListener('click', async () => {
                if (!selectedCancellationButton) return;

                const reason = orderCancellationReason.value;
                const otherReason = orderCancellationOther.value.trim();
                if (!reason || (reason === 'other' && !otherReason)) {
                    orderCancellationError.textContent = reason === 'other'
                        ? 'Please enter your cancellation reason.'
                        : 'Please select a cancellation reason.';
                    orderCancellationError.hidden = false;
                    return;
                }

                orderCancellationSubmit.disabled = true;
                orderCancellationSubmit.textContent = 'Submitting…';
                orderCancellationError.hidden = true;

                try {
                    const response = await fetch(
                        `${purchasesConfig.ordersUrl || '/api/v1/buyer/orders'}/${encodeURIComponent(selectedCancellationButton.dataset.buyerCancelOrder)}/cancel`,
                        {
                            method: 'POST',
                            headers: {
                                Accept: 'application/json',
                                'Content-Type': 'application/json',
                                Authorization: `Bearer ${purchasesConfig.apiToken || ''}`,
                            },
                            credentials: 'same-origin',
                            body: JSON.stringify({
                                reason,
                                ...(reason === 'other' ? { other_reason: otherReason } : {}),
                            }),
                        }
                    );
                    const payload = await response.json();
                    if (!response.ok) {
                        const validationMessage = Object.values(payload.errors || {}).flat()[0];
                        throw new Error(validationMessage || payload.message || 'This order could not be cancelled.');
                    }

                    const orderCard = selectedCancellationButton.closest('[data-purchase-row]');
                    if (orderCard && payload.data.status === 'cancelled') {
                        const statusClass = 'cancelled';
                        const statusLabel = 'Cancelled';
                        const status = orderCard.querySelector('.purchase-status');
                        orderCard.dataset.status = statusClass;
                        if (status) {
                            status.className = `purchase-status purchase-status--${statusClass}`;
                            status.textContent = statusLabel;
                        }
                        selectedCancellationButton.remove();
                        window.alert(payload.message || 'Order cancelled and stock restored.');
                    } else if (orderCard) {
                        selectedCancellationButton.remove();
                        let requestNote = orderCard.querySelector('[data-cancellation-request-note]');
                        if (!requestNote) {
                            requestNote = document.createElement('span');
                            requestNote.dataset.cancellationRequestNote = '';
                            requestNote.className = 'purchase-complaint-submitted';
                            orderCard.querySelector('.purchase-shop-order-meta')?.append(requestNote);
                        }
                        requestNote.textContent = 'Cancellation request pending seller review';
                        window.alert(payload.message || 'Cancellation request sent to seller.');
                    }

                    orderCancellationModal.classList.add('hidden');
                    orderCancellationModal.classList.remove('flex');
                    orderCancellationModal.setAttribute('aria-hidden', 'true');
                    selectedCancellationButton = null;
                    applyPurchaseFilters();
                } catch (error) {
                    orderCancellationError.textContent = error.message;
                    orderCancellationError.hidden = false;
                } finally {
                    orderCancellationSubmit.disabled = false;
                    orderCancellationSubmit.textContent = 'Submit cancellation';
                }
            });

            complaintForm?.addEventListener('submit', async event => {
                event.preventDefault();
                complaintError.hidden = true;
                complaintSubmit.disabled = true;
                complaintSubmit.textContent = 'Submitting…';

                try {
                    const response = await fetch(
                        `${purchasesConfig.complaintsUrl || '/api/v1/buyer/orders'}/${encodeURIComponent(complaintOrderId.value)}/complaints`,
                        {
                            method: 'POST',
                            headers: {
                                Accept: 'application/json',
                                'Content-Type': 'application/json',
                                Authorization: `Bearer ${purchasesConfig.apiToken || ''}`,
                            },
                            credentials: 'same-origin',
                            body: JSON.stringify({
                                type: document.getElementById('buyerComplaintType').value,
                                description: document.getElementById('buyerComplaintDescription').value,
                            }),
                        }
                    );
                    const payload = await response.json();

                    if (!response.ok) {
                        const validationMessage = Object.values(payload.errors || {}).flat()[0];
                        throw new Error(validationMessage || payload.message || 'Your complaint could not be submitted.');
                    }

                    const orderCard = Array.from(document.querySelectorAll('[data-purchase-row]'))
                        .find(card => card.querySelector(`[data-buyer-complaint-order="${CSS.escape(complaintOrderId.value)}"]`));

                    if (orderCard) {
                        orderCard.querySelector('[data-buyer-complaint-order]')?.remove();
                        const badge = document.createElement('span');
                        badge.className = 'purchase-complaint-submitted';
                        badge.textContent = `Complaint ${payload.data.reference} · Open`;
                        orderCard.querySelector('.purchase-shop-order-meta')?.append(badge);
                    }

                    window.alert(`${payload.message} Reference: ${payload.data.reference}`);
                    closeComplaintModal();
                } catch (error) {
                    complaintError.textContent = error.message;
                    complaintError.hidden = false;
                } finally {
                    complaintSubmit.disabled = false;
                    complaintSubmit.textContent = 'Submit complaint';
                }
            });

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
            let ordersRefreshBound = false;

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
                                            ${order.can_cancel
                                                ? `<button type="button" class="purchase-message-seller" data-buyer-cancel-order="${escapeHtml(order.id)}" data-order-number="${escapeHtml(order.order_number)}">Cancel order</button>`
                                                : ''}
                                            ${order.cancellation_request?.status === 'pending'
                                                ? '<span class="purchase-complaint-submitted" data-cancellation-request-note>Cancellation request pending seller review</span>'
                                                : ''}
                                            ${order.cancellation_request?.status === 'rejected'
                                                ? `<span class="purchase-complaint-submitted" data-cancellation-request-note>Seller declined cancellation: ${escapeHtml(order.cancellation_request.seller_other_reason || order.cancellation_request.seller_reason || 'Please contact the seller.')}</span>`
                                                : ''}
                                            ${order.complaint
                                                ? `<span class="purchase-complaint-submitted">Complaint ${escapeHtml(order.complaint.reference)} · ${escapeHtml(order.complaint.status.replace('-', ' '))}</span>`
                                                : `<button type="button" class="purchase-message-seller" data-buyer-complaint-order="${escapeHtml(order.id)}" data-order-number="${escapeHtml(order.order_number)}">Report a problem</button>`}
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
                    if (!ordersRefreshBound) {
                        window.addEventListener('focus', loadOrders);
                        window.setInterval(() => {
                            if (!document.hidden) loadOrders();
                        }, 30000);
                        ordersRefreshBound = true;
                    }
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
            tabs.find(tab => tab.dataset.purchaseTab === activeTab)?.click();
        });