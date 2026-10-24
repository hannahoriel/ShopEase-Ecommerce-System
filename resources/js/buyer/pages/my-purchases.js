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

            // My Likes: shared browser-side favorites used by Dashboard/product heart buttons.
            const likesGrid = document.getElementById('buyerLikesGrid');
            const likesEmpty = document.getElementById('buyerLikesEmpty');
            const likesCount = document.getElementById('buyerLikesCount');
            const likesSidebarCount = document.getElementById('buyerLikesSidebarCount');
            const likesEscape = value => String(value ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
            const likesMoney = amount => '₱' + Number(amount || 0).toLocaleString('en-PH', {minimumFractionDigits:2,maximumFractionDigits:2});
            function renderBuyerLikes() {
                if (!likesGrid || !window.ShopEaseBuyerLikes) return;
                const liked = window.ShopEaseBuyerLikes.getAll();
                likesCount.textContent = `${liked.length} ${liked.length === 1 ? 'product' : 'products'}`;
                likesSidebarCount.textContent = liked.length > 99 ? '99+' : liked.length;
                likesSidebarCount.hidden = liked.length === 0;
                likesEmpty.hidden = liked.length > 0;
                likesGrid.hidden = liked.length === 0;
                likesGrid.innerHTML = liked.map(product => {
                    const url = product.url || ('/buyer/product?id=' + encodeURIComponent(product.id));
                    const image = product.image && /^(https?:\/\/|\/)(?!\/)/i.test(product.image)
                        ? `<img src="${likesEscape(product.image)}" alt="${likesEscape(product.name)}" loading="lazy">`
                        : '<span class="buyer-likes-image-fallback" aria-hidden="true">♡</span>';
                    return `<article class="buyer-like-card">
                        <div class="buyer-like-photo"><a href="${likesEscape(url)}" aria-label="View ${likesEscape(product.name)}">${image}</a>
                            <button class="buyer-like-remove" type="button" data-unlike-product="${likesEscape(product.id)}" aria-label="Remove ${likesEscape(product.name)} from likes" title="Unlike product">♥</button></div>
                        <div class="buyer-like-content"><span class="buyer-like-store">${likesEscape(product.seller || 'ShopEase Seller')}</span>
                            <a class="buyer-like-name" href="${likesEscape(url)}">${likesEscape(product.name)}</a>
                            <strong class="buyer-like-price">${likesMoney(product.price)}</strong>
                            <a class="buyer-like-view" href="${likesEscape(url)}">View Product <span aria-hidden="true">→</span></a>
                        </div></article>`;
                }).join('');
            }
            likesGrid?.addEventListener('click', event => {
                const unlike = event.target.closest('[data-unlike-product]');
                if (!unlike) return;
                window.ShopEaseBuyerLikes?.remove(unlike.dataset.unlikeProduct);
            });
            window.addEventListener('shopease:likes-changed', renderBuyerLikes);
            document.querySelector('[data-side-panel="likes"]')?.addEventListener('click', renderBuyerLikes);
            if (window.ShopEaseBuyerLikes) renderBuyerLikes();
            else window.addEventListener('shopease:likes-ready', renderBuyerLikes, {once:true});

            const sideTabs =
                Array.from(document.querySelectorAll('[data-side-panel]'));

            const sidePanels =
                Array.from(document.querySelectorAll('[data-panel-content]'));

            const settingsToggle = document.getElementById('buyerSettingsToggle');
            const settingsSubmenu = document.getElementById('buyerSettingsSubmenu');
            const settingsPanels = ['privacy-security', 'notification-settings', 'platform-policies'];
            const setSettingsOpen = open => {
                if (!settingsToggle || !settingsSubmenu) return;
                settingsToggle.setAttribute('aria-expanded', String(open));
                settingsSubmenu.hidden = !open;
            };
            settingsToggle?.addEventListener('click', () => setSettingsOpen(settingsSubmenu.hidden));
            const showSidePanel = (panelName) => {
                if (settingsPanels.includes(panelName)) setSettingsOpen(true);
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

            const notificationsList = document.getElementById('buyerNotificationsList');
            const notificationsError = document.getElementById('buyerNotificationsError');
            const markAllReadButton = document.getElementById('buyerMarkAllRead');
            const notificationBadge = document.getElementById('buyerNotificationBadge');
            const notificationsUrl = purchasesConfig.notificationsUrl || '/api/v1/buyer/notifications';
            let notifications = [];
            let unreadNotificationCount = 0;
            if (notificationsList) {
                notificationsList.innerHTML = '<p class="buyer-notifications-empty">Loading notifications…</p>';
            }

            const notificationHeaders = () => ({
                Accept: 'application/json',
                Authorization: `Bearer ${purchasesConfig.apiToken || ''}`,
                ...(document.querySelector('meta[name="csrf-token"]')?.content
                    ? { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                    : {}),
            });

            const escapeNotificationText = value => String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');

            const updateNotificationBadge = count => {
                unreadNotificationCount = count;
                if (!notificationBadge) return;
                notificationBadge.textContent = count > 99 ? '99+' : String(count);
                notificationBadge.style.display = count > 0 ? '' : 'none';
            };

            const relativeNotificationTime = value => {
                if (!value) return '';
                const seconds = Math.round((new Date(value).getTime() - Date.now()) / 1000);
                const units = [
                    ['year', 31536000],
                    ['month', 2592000],
                    ['week', 604800],
                    ['day', 86400],
                    ['hour', 3600],
                    ['minute', 60],
                ];
                const [unit, size] = units.find(([, unitSize]) => Math.abs(seconds) >= unitSize) || ['second', 1];
                return new Intl.RelativeTimeFormat('en', { numeric: 'auto' }).format(Math.round(seconds / size), unit);
            };

            const notificationIcon = type => {
                if (type === 'shipping') return 'buyer-notification-icon--shipping';
                if (type === 'delivered' || type === 'completed') return 'buyer-notification-icon--delivered';
                if (type === 'promotion') return 'buyer-notification-icon--promo';
                return 'buyer-notification-icon--order';
            };

            const renderNotifications = () => {
                if (!notificationsList) return;
                if (!notifications.length) {
                    notificationsList.innerHTML = '<p class="buyer-notifications-empty">You have no notifications yet.</p>';
                    return;
                }

                notificationsList.innerHTML = notifications.map(notification => `
                    <button type="button"
                        class="buyer-notification-item ${notification.read_at ? '' : 'is-unread'}"
                        data-buyer-notification="${escapeNotificationText(notification.id)}">
                        <span class="buyer-notification-icon ${notificationIcon(notification.type)}" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M5.5 8.5h13l-1 10h-11l-1-10Z" stroke="currentColor" stroke-width="1.65" stroke-linejoin="round"/>
                                <path d="M9 9V7a3 3 0 0 1 6 0v2" stroke="currentColor" stroke-width="1.65" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <span class="buyer-notification-copy">
                            <strong>${escapeNotificationText(notification.title)}</strong>
                            <span>${escapeNotificationText(notification.message)}</span>
                        </span>
                        <span class="buyer-notification-meta">
                            <time datetime="${escapeNotificationText(notification.created_at)}">${escapeNotificationText(relativeNotificationTime(notification.created_at))}</time>
                            ${notification.read_at ? '' : '<span class="buyer-notification-unread-dot" aria-label="Unread"></span>'}
                        </span>
                    </button>`).join('');
            };

            const loadNotifications = async () => {
                if (!notificationsList || !purchasesConfig.apiToken) return;
                notificationsError.hidden = true;
                try {
                    const response = await fetch(notificationsUrl, {
                        headers: notificationHeaders(),
                        credentials: 'same-origin',
                    });
                    if (!response.ok) throw new Error(`Notifications could not be loaded (${response.status}).`);
                    const payload = await response.json();
                    notifications = payload.data || [];
                    updateNotificationBadge(Number(payload.unread_count || 0));
                    renderNotifications();
                } catch (error) {
                    notificationsError.textContent = error.message;
                    notificationsError.hidden = false;
                }
            };

            notificationsList?.addEventListener('click', async event => {
                const notificationItem = event.target.closest('[data-buyer-notification]');
                if (!notificationItem || !notificationItem.classList.contains('is-unread')) return;
                const notificationId = notificationItem.dataset.buyerNotification;
                try {
                    const response = await fetch(`${notificationsUrl}/${encodeURIComponent(notificationId)}/read`, {
                        method: 'PATCH',
                        headers: notificationHeaders(),
                        credentials: 'same-origin',
                    });
                    if (!response.ok) throw new Error(`Notification could not be marked as read (${response.status}).`);
                    const notification = notifications.find(item => String(item.id) === notificationId);
                    if (notification) {
                        notification.read_at = new Date().toISOString();
                        updateNotificationBadge(Math.max(0, unreadNotificationCount - 1));
                    }
                    renderNotifications();
                } catch (error) {
                    notificationsError.textContent = error.message;
                    notificationsError.hidden = false;
                }
            });

            markAllReadButton?.addEventListener('click', async () => {
                markAllReadButton.disabled = true;
                notificationsError.hidden = true;
                try {
                    const response = await fetch(`${notificationsUrl}/read-all`, {
                        method: 'POST',
                        headers: notificationHeaders(),
                        credentials: 'same-origin',
                    });
                    if (!response.ok) throw new Error(`Notifications could not be marked as read (${response.status}).`);
                    const readAt = new Date().toISOString();
                    notifications.forEach(notification => {
                        notification.read_at = notification.read_at || readAt;
                    });
                    updateNotificationBadge(0);
                    renderNotifications();
                } catch (error) {
                    notificationsError.textContent = error.message;
                    notificationsError.hidden = false;
                } finally {
                    markAllReadButton.disabled = false;
                }
            });

            loadNotifications();

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
                            completed: ['completed', 'Completed'],
                            cancelled: ['cancelled', 'Cancelled'],
                            refunded: ['return-refund', 'Refunded'],
                            returned: ['return-refund', 'Returned'],
                            return_requested: ['return-refund', 'Return Requested'],
                            refund_requested: ['return-refund', 'Refund Requested'],
                            return_pending: ['return-refund', 'Return Pending'],
                            refund_pending: ['return-refund', 'Refund Pending'],
                            return_approved: ['return-refund', 'Return Approved'],
                            refund_approved: ['return-refund', 'Refund Approved'],
                        };
                        return states[status] || ['processing', 'Processing'];
                    };

                    function renderOrders(orders) {
                        if (!purchaseRows) return;
                        if (!orders.length) {
                            if (purchaseRows.querySelector('[data-demo-order]')) {
                                rows = Array.from(purchaseRows.querySelectorAll('[data-purchase-row]'));
                                applyPurchaseFilters();
                                return;
                            }
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
                            const deliveryDetails = order.shipment
                                ? `<span>${escapeHtml(order.shipment.estimated_delivery || 'Delivery in progress')}</span>${order.shipment.tracking_number ? `<small>Tracking ${escapeHtml(order.shipment.tracking_number)}</small>` : ''}`
                                : '—';
                            const moreItems = Math.max(0, order.items.length - 1);
                            return `
                                <article class="purchase-shop-card" data-purchase-row data-view-order-id="${escapeHtml(order.order_number)}" role="link" tabindex="0" aria-label="View order ${escapeHtml(order.order_number)}" data-status="${statusClass}" data-search="${escapeHtml(`${order.order_number} ${order.shop} ${order.items.map(item => item.product_name).join(' ')}`.toLowerCase())}">
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
                                        <div class="purchase-estimated-delivery">${deliveryDetails}</div>
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
                        if (emptyState && !purchaseRows.querySelector('[data-demo-order]')) {
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
                            if (purchaseRows.querySelector('[data-demo-order]')) {
                                applyPurchaseFilters();
                                return;
                            }
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
// Buyer account details are saved through the authenticated account API.
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('buyerProfileForm');
    if (!form) return;
    const get = id => document.getElementById(id);
    const save = get('bpRequestChanges'), discard = get('bpDiscard');
    const original = new Map([...form.querySelectorAll('input:not([type=file]),select,textarea')].filter(input => input.name).map(input => [input, input.value]));
    const files = [get('bpCoverInput'),get('bpAvatarInput')];
    const urls = [];
    const previews = [get('bpCoverPreview'),get('bpAvatarPreview')];
    const originalImage = previews.map(x=>({src:x.getAttribute('src'),hidden:x.hidden}));
    const fallback = get('bpAvatarFallback');
    const toast = message => {
        const element = get('bpProfileToast');
        element.textContent = message;
        element.hidden = false;
        clearTimeout(element._hideTimer);
        element._hideTimer = setTimeout(() => { element.hidden = true; }, 3900);
    };
    const isDirty = () =>
        [...original].some(([element, value]) => element.value !== value) ||
        files.some(input => input.files?.length > 0);
    const refresh = () => {
        save.disabled = !isDirty();
        discard.disabled = !isDirty();
    };
    const refreshAge = () => {
        const birthday = get('bp-birthday').value;
        const age = get('bp-age');
        if (!birthday) {
            age.value = '';
            return;
        }
        const birthDate = new Date(`${birthday}T00:00:00`);
        const today = new Date();
        let years = today.getFullYear() - birthDate.getFullYear();
        if (today.getMonth() < birthDate.getMonth() ||
            (today.getMonth() === birthDate.getMonth() && today.getDate() < birthDate.getDate())) {
            years--;
        }
        age.value = String(Math.max(0, years));
    };
    const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content || '';
    const requestHeaders = () => ({
        Accept: 'application/json',
        'X-CSRF-TOKEN': csrfToken(),
    });
    const setAccountPhoto = photoUrl => {
        document.querySelectorAll('.account-avatar').forEach(avatar => {
            let image = avatar.querySelector('img');
            if (!image) {
                image = document.createElement('img');
                image.alt = '';
                image.setAttribute('aria-hidden', 'true');
                image.style.cssText = 'position:absolute;inset:0;width:100%;height:100%;border-radius:inherit;object-fit:cover;';
                avatar.append(image);
            }
            image.src = photoUrl;
        });
    };

    form.addEventListener('input', event => {
        event.target.classList?.remove('bp-invalid');
        refreshAge();
        refresh();
    });
    form.addEventListener('change', refresh);
    files.forEach((input, index) => input.addEventListener('change', async () => {
        const file = input.files?.[0];
        if (!file) {
            refresh();
            return;
        }
        if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size > 5 * 1024 * 1024) {
            input.value = '';
            toast('Choose a JPG, PNG, or WebP image smaller than 5 MB.');
            refresh();
            return;
        }

        const previewUrl = URL.createObjectURL(file);
        urls.push(previewUrl);
        previews[index].src = previewUrl;
        previews[index].hidden = false;
        if (index === 1) fallback.hidden = true;

        if (index === 1) {
            try {
                const body = new FormData();
                body.append('profile_photo', file);
                const response = await fetch('/api/v1/profile/photo', {
                    method: 'PUT',
                    credentials: 'same-origin',
                    headers: requestHeaders(),
                    body,
                });
                const result = await response.json();
                if (!response.ok) {
                    throw new Error(result.message || 'Unable to update the profile photo.');
                }
                previews[index].src = result.profile_photo_url;
                originalImage[index] = {src: result.profile_photo_url, hidden: false};
                input.value = '';
                setAccountPhoto(result.profile_photo_url);
                toast('Profile photo updated successfully.');
            } catch (error) {
                input.value = '';
                previews[index].src = originalImage[index].src || '';
                previews[index].hidden = originalImage[index].hidden;
                fallback.hidden = !originalImage[index].hidden;
                toast(error.message || 'Unable to update the profile photo.');
            }
        }
        refresh();
    }));

    discard.addEventListener('click', () => {
        original.forEach((value, element) => {
            element.value = value;
            element.classList.remove('bp-invalid');
        });
        files.forEach(input => { input.value = ''; });
        previews.forEach((preview, index) => {
            if (originalImage[index].src) preview.setAttribute('src', originalImage[index].src);
            else preview.removeAttribute('src');
            preview.hidden = originalImage[index].hidden;
        });
        fallback.hidden = !originalImage[1].hidden;
        refreshAge();
        refresh();
        toast('Unsaved edits discarded.');
    });

    form.addEventListener('submit', async event => {
        event.preventDefault();
        if (!isDirty()) return;
        if (!form.reportValidity()) return;

        save.disabled = true;
        const body = new FormData(form);
        body.delete('profile_photo');
        try {
            const response = await fetch(form.dataset.updateUrl, {
                method: 'PUT',
                credentials: 'same-origin',
                headers: requestHeaders(),
                body,
            });
            const result = await response.json();
            if (!response.ok) {
                const validationError = Object.values(result.errors || {}).flat()[0];
                throw new Error(validationError || result.message || 'Unable to save your account details.');
            }

            original.clear();
            form.querySelectorAll('input:not([type=file]),select,textarea').forEach(input => {
                if (input.name) original.set(input, input.value);
            });
            files.forEach(input => { input.value = ''; });
            const displayName = result.user.name;
            document.querySelectorAll('.buyer-profile-copy strong, .bp-identity-copy h3').forEach(element => {
                element.textContent = displayName;
            });
            const nameParts = displayName.trim().split(/\s+/);
            const initials = nameParts.length > 1
                ? `${nameParts[0][0]}${nameParts[nameParts.length - 1][0]}`
                : (nameParts[0]?.[0] || 'B');
            document.querySelectorAll('.account-avatar').forEach(avatar => {
                avatar.setAttribute('aria-label', `${displayName} profile picture`);
                const fallbackInitials = avatar.querySelector('span[aria-hidden="true"]');
                if (fallbackInitials) fallbackInitials.textContent = initials.toUpperCase();
            });
            fallback.textContent = initials.toUpperCase();
            if (result.profile.cover_photo_url) {
                previews[0].src = result.profile.cover_photo_url;
                previews[0].hidden = false;
                originalImage[0] = {src: result.profile.cover_photo_url, hidden: false};
            }
            refreshAge();
            refresh();
            toast(result.message);
        } catch (error) {
            refresh();
            toast(error.message || 'Unable to save your account details.');
        }
    });

    window.addEventListener('beforeunload', () => urls.forEach(url => URL.revokeObjectURL(url)));
    refreshAge();
    refresh();
});

// Profile address: same searchable Province -> City/Municipality -> Barangay cascade as buyer registration.
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('buyerProfileForm');
    if (!form) return;
    const base = '/api/v1/locations';
    const names = ['province','municipality','barangay'];
    const pickers = {};
    const nameOf = x => x?.name || x?.prov_name || x?.city_name || x?.mun_name || x?.brgy_name || '';
    const codeOf = x => x?.code || x?.psgc_code || x?.prov_code || x?.mun_code || x?.city_code || x?.brgy_code || '';
    const norm = x => String(x || '').trim().toLocaleLowerCase();
    const originalNames = {};
    let provinces = [];
    let generation = 0;
    const notify = msg => {
        const el = document.getElementById('bpProfileToast');
        if (!el) return;
        el.textContent = msg; el.hidden = false;
        clearTimeout(el._locationTimer);
        el._locationTimer = setTimeout(() => el.hidden = true, 4200);
    };
    async function request(path) {
        const response = await fetch(`${base}/${path}`, {headers: {'Accept':'application/json'}, credentials:'same-origin'});
        if (!response.ok) throw new Error('Unable to load address options. Please try again.');
        const result = await response.json();
        return Array.isArray(result) ? result : Array.isArray(result.data) ? result.data : [];
    }
    function close(p) { p.wrap.classList.remove('is-open'); p.input.setAttribute('aria-expanded','false'); p.highlight=-1; }
    function closeAll(except) { Object.values(pickers).forEach(p => {if (p!==except) close(p);}); }
    function message(p, msg) {p.list.replaceChildren(); const node=document.createElement('div');node.className='bp-location-message';node.textContent=msg;p.list.append(node);}
    function render(p) {
        p.items=p.options.filter(x=>nameOf(x).toLowerCase().includes(p.input.value.trim().toLowerCase()));
        p.list.replaceChildren(); p.highlight=-1;
        if (!p.items.length) {message(p,'No matching result found.');return;}
        p.items.forEach((option,index)=>{
            const button=document.createElement('button');button.type='button';button.className='bp-location-option';
            button.setAttribute('role','option');button.textContent=nameOf(option);
            if(norm(p.hidden.value)===norm(nameOf(option)))button.classList.add('is-selected');
            button.addEventListener('mousedown',event=>{event.preventDefault(); select(p,option);});
            p.list.append(button);
        });
    }
    function open(p) {if(form.dataset.approvalPending==='true'||p.input.disabled)return; closeAll(p);p.wrap.classList.add('is-open');p.input.setAttribute('aria-expanded','true');render(p);}
    function reset(p, disabled=true) {
        p.options=[];p.items=[];p.input.value='';p.hidden.value='';p.input.disabled=disabled;
        p.input.placeholder= p.key==='municipality'?'Select province first':'Select municipality first';close(p);
        p.input.dispatchEvent(new Event('change',{bubbles:true}));
    }
    function populate(p, values) {p.options=values;p.input.disabled=form.dataset.approvalPending==='true';p.input.placeholder=`Type to search ${p.key}`;render(p);}
    async function cities(province,requestId) {
        reset(pickers.municipality,false);reset(pickers.barangay);
        pickers.municipality.input.disabled=true;
        const code=codeOf(province);if(!code)return;
        try {
            const values=await request(`provinces/${encodeURIComponent(code)}/cities${province.isRegion===true?'?is_region=1':''}`);
            if(requestId!==generation)return;
            populate(pickers.municipality,values);
        }catch(e){if(requestId===generation)notify(e.message);}
    }
    async function barangays(city,requestId) {
        reset(pickers.barangay);const code=codeOf(city);if(!code)return;
        try {const values=await request(`cities/${encodeURIComponent(code)}/barangays`);
            if(requestId!==generation)return;populate(pickers.barangay,values);
        }catch(e){if(requestId===generation)notify(e.message);}
    }
    function select(p,option) {
        p.input.value=nameOf(option);p.hidden.value=nameOf(option);close(p);
        p.input.dispatchEvent(new Event('change',{bubbles:true}));
        if(p.key==='province'){const id=++generation;cities(option,id);}
        if(p.key==='municipality'){const id=++generation;barangays(option,id);}
    }
    names.forEach(key=>{
        const wrap=document.querySelector(`[data-bp-location="${key}"]`);
        if(!wrap)return;
        const input=wrap.querySelector('.bp-location-search');const hidden=wrap.querySelector('input[type="hidden"]');
        const list=wrap.querySelector('.bp-location-options');
        const p={key,wrap,input,hidden,list,options:[],items:[],highlight:-1};pickers[key]=p;
        originalNames[key]=hidden.value;
        input.addEventListener('focus',()=>open(p));input.addEventListener('click',()=>open(p));
        input.addEventListener('input',()=>{if(form.dataset.approvalPending==='true')return;hidden.value=''; if(key==='province'){++generation;reset(pickers.municipality);reset(pickers.barangay);} if(key==='municipality'){++generation;reset(pickers.barangay);} open(p);});
        input.addEventListener('keydown',event=>{
            if(!p.wrap.classList.contains('is-open')&&['ArrowDown','ArrowUp'].includes(event.key))open(p);
            if(event.key==='Escape'){close(p);return;}
            if(event.key==='ArrowDown'||event.key==='ArrowUp'){
                event.preventDefault();p.highlight=Math.max(0,Math.min(p.items.length-1,p.highlight+(event.key==='ArrowDown'?1:-1)));
                [...p.list.querySelectorAll('button')].forEach((b,i)=>b.classList.toggle('is-highlighted',i===p.highlight));
            }
            if(event.key==='Enter'&&p.wrap.classList.contains('is-open')){event.preventDefault();if(p.items.length)select(p,p.items[Math.max(0,p.highlight)]);}
        });
        input.addEventListener('blur',()=>{setTimeout(()=>{close(p);if(!p.hidden.value){const matching=p.options.find(x=>norm(nameOf(x))===norm(p.input.value));if(matching)select(p,matching);else p.input.value='';}},110);});
    });
    if(names.some(key=>!pickers[key]))return;
    document.addEventListener('mousedown',event=>{if(!event.target.closest('.bp-location-picker'))closeAll();});
    async function hydrate(saved) {
        const id=++generation;
        try {
            provinces=await request('provinces');if(id!==generation)return;
            populate(pickers.province,provinces);
            const province=provinces.find(x=>norm(nameOf(x))===norm(saved.province));
            if(!province){if(saved.province)pickers.province.input.value=saved.province;return;}
            pickers.province.input.value=nameOf(province);pickers.province.hidden.value=nameOf(province);
            if(!saved.municipality)return;
            const citiesList=await request(`provinces/${encodeURIComponent(codeOf(province))}/cities${province.isRegion===true?'?is_region=1':''}`);
            if(id!==generation)return;
            populate(pickers.municipality,citiesList);
            const city=citiesList.find(x=>norm(nameOf(x))===norm(saved.municipality));
            if(!city){pickers.municipality.input.value=saved.municipality;return;}
            pickers.municipality.input.value=nameOf(city);pickers.municipality.hidden.value=nameOf(city);
            if(!saved.barangay)return;
            const brgys=await request(`cities/${encodeURIComponent(codeOf(city))}/barangays`);
            if(id!==generation)return;
            populate(pickers.barangay,brgys);
            const brgy=brgys.find(x=>norm(nameOf(x))===norm(saved.barangay));
            if(brgy){pickers.barangay.input.value=nameOf(brgy);pickers.barangay.hidden.value=nameOf(brgy);}
        }catch(e){if(id===generation)notify(e.message);}
    }
    // Restore the approved address fields when the user discards unsaved edits.
    document.getElementById('bpDiscard')?.addEventListener('click',()=>{
        generation++;
        names.forEach(key=>{pickers[key].input.value=originalNames[key];pickers[key].hidden.value=originalNames[key];});
        hydrate(originalNames);
    });
    // A typed string is not a valid address choice; only a selected API option counts.
    form.addEventListener('submit',e=>{
        const invalid=names.find(key=>!pickers[key].hidden.value || norm(pickers[key].input.value)!==norm(pickers[key].hidden.value));
        if(invalid){e.preventDefault();e.stopImmediatePropagation();pickers[invalid].input.classList.add('bp-invalid');pickers[invalid].input.focus();notify('Please select a valid province, municipality, and barangay from the suggestions.');}
    },true);
    hydrate(originalNames);
});
/* Buyer Complaints — temporary local demo, no backend writes. */
document.addEventListener('DOMContentLoaded', () => {
    const root = document.querySelector('.buyer-complaints-clone');
    if (!root) return;
    const get = id => document.getElementById(id);
    const safe = v => String(v ?? '—').replace(/[&<>"']/g, x => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[x]));
    const norm = x => String(x || '').toLowerCase().replaceAll('_','-');
    const title = x => ({open:'Open','in-progress':'In progress',resolved:'Resolved'})[norm(x)] || 'Open';
    const timeText = x => x ? new Date(x).toLocaleString('en-US',{month:'short',day:'numeric',year:'numeric',hour:'numeric',minute:'2-digit'}) : '—';
    const dateText = x => x ? new Date(x).toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'}) : '—';
    const setText = (id,v) => {if(get(id)) get(id).textContent=v??'—';};
    const mockImage = (label,accent) => 'data:image/svg+xml;charset=utf-8,'+encodeURIComponent(`<svg xmlns="http://www.w3.org/2000/svg" width="500" height="370" viewBox="0 0 500 370"><rect width="500" height="370" fill="#f5f0eb"/><rect x="48" y="34" width="404" height="302" rx="18" fill="#fff" stroke="#dccdc8" stroke-width="4"/><rect x="93" y="75" width="314" height="158" rx="12" fill="${accent}" opacity=".18"/><path d="M133 193l70-69 46 43 43-31 77 63" fill="none" stroke="${accent}" stroke-width="13" stroke-linecap="round" stroke-linejoin="round"/><circle cx="167" cy="115" r="16" fill="${accent}" opacity=".6"/><text x="250" y="270" fill="#643d40" font-family="Arial" font-size="21" font-weight="bold" text-anchor="middle">SAMPLE EVIDENCE</text><text x="250" y="299" fill="#8a7775" font-family="Arial" font-size="17" text-anchor="middle">${label}</text></svg>`);
    const categories=['Wrong Item','Damaged Item','Missing Item','Late Delivery','Defective Product','Incorrect Quantity'];
    const shops=['The Shop PH','Urban Finds Store','Home Haven PH','Trendline Market','Gadget Corner'];
    const statuses=['open','in-progress','resolved','open','resolved','in-progress','open','resolved','in-progress','open','resolved','open'];
    const cases=Array.from({length:12},(_,i)=>{
        const id=`CMP-DEMO-${String(i+1).padStart(3,'0')}`;
        const type=categories[i%categories.length];
        const shop=shops[i%shops.length];
        const when=new Date(2026,9,20-Math.floor(i*1.35),10+i%7,i*3%60).toISOString();
        return {id,order_id:`ORD-DEMO-${String(1801+i)}`,type,status:statuses[i],filed_at:when,updated_at:when,
          party1:'Sample Buyer',role1:'Buyer',party1Email:'demo.buyer@example.test',party2:shop,role2:'Seller',party2Email:'seller@example.test',
          description:({ 'Wrong Item':'The delivered item does not match the product variant selected during checkout.', 'Damaged Item':'The package arrived with visible damage and the product has scratches.', 'Missing Item':'One item listed in the paid order was not included in the parcel.', 'Late Delivery':'The order arrived significantly later than the estimated delivery date.', 'Defective Product':'The product does not function as described despite following setup instructions.', 'Incorrect Quantity':'The delivered quantity does not match the quantity on the receipt.'})[type],
          orderDate:when,paymentMethod:i%2?'GCash':'Cash on Delivery',paymentStatus:'Paid',shippingMethod:'Standard Delivery',orderStatus:statuses[i]==='resolved'?'Delivered':'Under review',orderTotal:`₱${(699+i*125).toLocaleString('en-PH')}.00`,
          productName:['Wireless Earbuds','Canvas Tote Bag','Ceramic Mug Set','Fitness Watch','Desk Lamp','Skin Care Set'][i%6],productVariant:'Standard / Assorted',productPrice:`₱${499+i*125}.00`,productQuantity:1,productCategory:'ShopEase Products',shopName:shop,
          productImage:mockImage('Product preview','#ad6267'),evidence:[mockImage(type,'#9e5860'),mockImage('Package photo','#6b859d')],
          updates:[{status:'open',actor:'ShopEase Support',timestamp:when},...(statuses[i]!=='open'?[{status:statuses[i],actor:'ShopEase Admin',timestamp:new Date(new Date(when).getTime()+86400000).toISOString()}]:[])]
        };
    });
    const state={status:'all',search:'',date:'',page:1,size:7,selected:null};
    const filtered=()=>cases.filter(c=>(state.status==='all'||c.status===state.status)&&(!state.date||c.filed_at.slice(0,10)===state.date)&&(!state.search||[c.id,c.order_id,c.type,c.party1,c.party2,c.description].join(' ').toLowerCase().includes(state.search)));
    const parties=c=>[{name:c.party1,role:'Buyer',email:c.party1Email},{name:c.party2,role:'Seller',email:c.party2Email}];
    const partyMarkup=p=>`<div class="complaint-party"><span class="complaint-avatar">${safe(p.name.charAt(0))}</span><span class="complaint-party-copy"><span class="complaint-party-name">${safe(p.name)}</span><span class="complaint-party-role">${safe(p.role)}</span></span></div>`;
    const badge=c=>`<span class="complaint-status ${c.status}">${title(c.status)}</span>`;
    function preview(c){
        get('complaints-workspace').classList.add('has-selection');get('complaints-detail-card').setAttribute('aria-hidden','false');
        setText('detail-id',c.id);setText('detail-date',timeText(c.filed_at));setText('detail-type',c.type);setText('detail-description',c.description);
        const status=get('detail-status');status.className='complaint-status '+c.status;status.textContent=title(c.status);
        get('detail-parties').innerHTML=parties(c).map(partyMarkup).join('');get('detail-evidence').innerHTML=evidenceMarkup(c,true);
    }
    function closePreview(){state.selected=null;get('complaints-workspace').classList.remove('has-selection');get('complaints-detail-card').setAttribute('aria-hidden','true');get('complaints-table-body').querySelectorAll('.active').forEach(el=>el.classList.remove('active'));}
    function render(){
        const data=filtered(),pageCount=Math.max(1,Math.ceil(data.length/state.size));state.page=Math.min(state.page,pageCount);
        const slice=data.slice((state.page-1)*state.size,state.page*state.size);
        get('complaints-table-body').innerHTML=slice.map(c=>`<tr class="complaint-row ${state.selected?.id===c.id?'active':''}" data-id="${safe(c.id)}" role="button" tabindex="0" aria-label="Preview complaint ${safe(c.id)}"><td><span class="complaint-id">${safe(c.id)}</span></td><td><div class="complaint-parties">${parties(c).map(partyMarkup).join('')}</div></td><td><span class="complaint-type">${safe(c.type)}</span></td><td>${badge(c)}</td><td><span class="complaint-date-cell"><span>${dateText(c.filed_at)}<br>${new Date(c.filed_at).toLocaleTimeString('en-US',{hour:'numeric',minute:'2-digit'})}</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 5 7 7-7 7"/></svg></span></td></tr>`).join('');
        get('complaints-empty').style.display=data.length?'none':'block';setText('complaints-count',`Showing ${slice.length} out of ${data.length} entries`);
        get('complaints-prev').disabled=state.page===1;get('complaints-next').disabled=state.page>=pageCount;
        get('complaints-page-numbers').innerHTML=Array.from({length:pageCount},(_,i)=>i+1).filter(n=>pageCount<=7||n===1||n===pageCount||Math.abs(n-state.page)<=1).map((n,i,arr)=>`${i&&n-arr[i-1]>1?'<span>…</span>':''}<button type="button" class="complaints-page-button ${n===state.page?'active':''}" data-page="${n}">${n}</button>`).join('');
        if(state.selected)preview(state.selected);
    }
    function evidenceMarkup(c,compact=false){return c.evidence.slice(0,compact?4:30).map((url,i)=>`<button type="button" class="sp-evidence-thumb" data-evidence-index="${i}" aria-label="View sample evidence ${i+1}"><img src="${safe(url)}" alt="Sample evidence ${i+1}" loading="lazy"></button>`).join('');}
    const selectCase=id=>{state.selected=cases.find(c=>c.id===id)||null;render();};
    const showLinkedComplaint = id => {
        const target = cases.find(c => String(c.id).toLowerCase() === String(id || '').toLowerCase());
        if (!target) return;
        // Open My Purchases > Complaints (not the full details modal).
        const side = document.querySelector('[data-side-panel="complaints"]');
        if (side) side.click();
        root.closest('[data-panel-content="complaints"]')?.removeAttribute('hidden');
        state.status = 'all'; state.search = ''; state.date = '';
        get('complaints-search').value = '';
        get('complaints-date-filter').value = '';
        root.querySelectorAll('.complaints-tab').forEach(tab => {
            const active = tab.dataset.tab === 'all';
            tab.classList.toggle('active', active);
            tab.setAttribute('aria-selected',String(active));
        });
        state.page = Math.floor(cases.findIndex(c => c.id === target.id) / state.size) + 1;
        selectCase(target.id);
        const url = new URL(window.location.href);
        url.searchParams.set('section','complaints');
        url.searchParams.set('complaint',target.id);
        window.history.replaceState(null,'',url.pathname+url.search+url.hash);
        root.scrollIntoView({behavior:'smooth',block:'start'});
    };
    document.addEventListener('buyer:view-complaint', e => showLinkedComplaint(e.detail?.id));
    const deepLinkedId = new URLSearchParams(window.location.search).get('complaint');
    if (deepLinkedId) showLinkedComplaint(deepLinkedId);
    get('complaints-table-body').addEventListener('click',e=>{const row=e.target.closest('.complaint-row');if(row)selectCase(row.dataset.id);});
    get('complaints-table-body').addEventListener('keydown',e=>{const row=e.target.closest('.complaint-row');if(row&&(e.key==='Enter'||e.key===' ')){e.preventDefault();selectCase(row.dataset.id);}});
    root.querySelectorAll('.complaints-tab').forEach(button=>button.addEventListener('click',()=>{state.status=button.dataset.tab;state.page=1;root.querySelectorAll('.complaints-tab').forEach(t=>{t.classList.toggle('active',t===button);t.setAttribute('aria-selected',String(t===button));});closePreview();render();}));
    get('complaints-search').addEventListener('input',e=>{state.search=e.target.value.trim().toLowerCase();state.page=1;closePreview();render();});
    get('complaints-date-filter').addEventListener('change',e=>{state.date=e.target.value;state.page=1;closePreview();render();});
    get('complaints-date-button').addEventListener('click',()=>{const input=get('complaints-date-filter');if(input.showPicker)input.showPicker();else input.focus();});
    get('complaints-reload').addEventListener('click',()=>{state.search='';state.date='';get('complaints-search').value='';get('complaints-date-filter').value='';root.querySelector('.complaints-tab[data-tab="all"]').click();get('complaints-reload').classList.add('is-spinning');setTimeout(()=>get('complaints-reload').classList.remove('is-spinning'),500);});
    get('complaints-items').addEventListener('change',e=>{state.size=Number(e.target.value)||7;state.page=1;closePreview();render();});
    get('complaints-prev').addEventListener('click',()=>{if(state.page>1){state.page--;render();}});
    get('complaints-next').addEventListener('click',()=>{if(state.page<Math.ceil(filtered().length/state.size)){state.page++;render();}});
    get('complaints-page-numbers').addEventListener('click',e=>{const b=e.target.closest('[data-page]');if(b){state.page=Number(b.dataset.page);render();}});
    get('complaints-detail-close').addEventListener('click',closePreview);
    const modal=get('complaints-details-modal');
    const rows=items=>items.map(([key,value])=>`<div class="complaints-modal-order-row"><span>${safe(key)}</span><span>${safe(value||'—')}</span></div>`).join('');
    const userIcon='<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="8" r="3.3"/><path d="M5.3 20c.1-4.1 2.6-6.3 6.7-6.3s6.6 2.2 6.7 6.3"/></svg>';
    const shopIcon='<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M3 9 5 4h14l2 5v2a3 3 0 0 1-6 0 3 3 0 0 1-6 0 3 3 0 0 1-6 0V9zM5 13v7h14v-7"/></svg>';
    function openModal(){
        const c=state.selected;if(!c)return;
        setText('modal-complaint-id',c.id);setText('modal-date-filed',timeText(c.filed_at));setText('modal-last-updated',timeText(c.updated_at));setText('modal-complaint-type',c.type);setText('modal-order-id-top',c.order_id);setText('modal-description-copy',c.description);
        const status=get('modal-complaint-status');status.className='complaints-modal-status '+c.status;status.textContent=title(c.status);
        get('modal-parties-list').innerHTML=parties(c).map(p=>`<div class="complaints-modal-party-row"><span class="complaints-modal-party-avatar">${p.role==='Buyer'?userIcon:shopIcon}</span><span class="complaints-modal-party-copy"><strong>${safe(p.name)}</strong><span>${safe(p.email)}</span></span><span class="complaints-modal-party-role">${p.role==='Buyer'?userIcon:shopIcon}${p.role}</span></div>`).join('');
        get('modal-order-information').innerHTML=rows([['Order ID',c.order_id],['Order Date',timeText(c.orderDate)],['Payment Method',c.paymentMethod],['Payment Status',c.paymentStatus],['Shipping Method',c.shippingMethod],['Order Status',c.orderStatus],['Order Total',c.orderTotal]]);
        get('modal-product-info').innerHTML=`<div class="complaints-modal-item-main"><img class="complaints-modal-product-image" src="${safe(c.productImage)}" alt="Demo ordered product"><div><div class="complaints-modal-product-name">${safe(c.productName)}</div><div class="complaints-modal-product-variant">${safe(c.productVariant)}</div><div class="complaints-modal-product-price"><strong>${safe(c.productPrice)}</strong><span>Qty: ${c.productQuantity}</span></div></div></div><div class="complaints-modal-item-extra"><div><span class="complaints-modal-item-extra-label">Category</span><span class="complaints-modal-category-pill">${safe(c.productCategory)}</span></div><div class="complaints-modal-shop-line">Shop: <strong>${safe(c.shopName)}</strong></div></div>`;
        get('modal-evidence-list').innerHTML=evidenceMarkup(c);
        get('complaints-modal-notes-list').innerHTML=c.updates.slice().reverse().map(update=>`<div class="complaints-modal-note"><span class="complaints-modal-note-avatar">${safe(update.actor[0])}</span><div class="complaints-modal-note-copy"><strong>${safe(update.actor)}</strong><span>${update.status==='open'?'Complaint opened.':`Complaint status changed to ${title(update.status)}.`}</span></div><span class="complaints-modal-note-time">${timeText(update.timestamp)}</span></div>`).join('');
        modal.hidden=false;modal.setAttribute('aria-hidden','false');document.body.classList.add('sp-modal-open');modal.querySelector('.complaints-modal-dialog').focus();
    }
    function closeModal(){modal.hidden=true;modal.setAttribute('aria-hidden','true');document.body.classList.remove('sp-modal-open');}
    get('complaints-view-details').addEventListener('click',openModal);
    ['complaints-modal-close','complaints-modal-done'].forEach(id=>get(id).addEventListener('click',closeModal));
    modal.addEventListener('click',e=>{if(e.target===modal)closeModal();});
    const viewer=get('sp-evidence-viewer');
    function closeEvidence(){viewer.hidden=true;viewer.setAttribute('aria-hidden','true');get('sp-evidence-image').removeAttribute('src');}
    document.addEventListener('click',e=>{const b=e.target.closest('.buyer-complaints-clone .sp-evidence-thumb, #complaints-details-modal .sp-evidence-thumb');if(!b||!state.selected)return;const i=Number(b.dataset.evidenceIndex);get('sp-evidence-image').src=state.selected.evidence[i];setText('sp-evidence-caption',`Sample evidence ${i+1} · ${state.selected.id}`);viewer.hidden=false;viewer.setAttribute('aria-hidden','false');});
    get('sp-evidence-close').addEventListener('click',closeEvidence);viewer.addEventListener('click',e=>{if(e.target===viewer)closeEvidence();});
    document.addEventListener('keydown',e=>{if(e.key==='Escape'){if(!viewer.hidden)closeEvidence();else if(!modal.hidden)closeModal();}});
    get('sp-contact-admin')?.addEventListener('click', () => {
        const complaint = state.selected;
        if (!complaint?.id) return;
        closeModal();
        // Open the EXISTING buyer floating chat, not a separate /buyer/messages route.
        // The floating chat renders a complaint-specific Admin demo conversation.
        if (typeof window.openBuyerComplaintSupportChat === 'function') {
            window.openBuyerComplaintSupportChat({id: complaint.id, type: complaint.type, status: complaint.status, filed_at: complaint.filed_at});
        } else {
            document.dispatchEvent(new CustomEvent('buyer:open-complaint-support', {
                detail: {id: complaint.id, type: complaint.type, status: complaint.status, filed_at: complaint.filed_at}
            }));
        }
    });
    render();
});


/* Buyer Vouchers & Promotions: sample UI catalog (no redemption backend). */
document.addEventListener('DOMContentLoaded', () => {
    const grid = document.getElementById('bvVoucherGrid');
    const overlay = document.getElementById('bvVoucherOverlay');
    if (!grid || !overlay) return;
    const offers = [
        { id:'WELCOME150', category:'order', tag:'Welcome Reward', discount:'₱150', headline:'₱150 OFF', title:'First Order Savings', purpose:'Save on your first eligible purchase.', expiry:'2026-11-30T23:59:59', starts:'2026-10-01', minSpend:'₱1,000', maxDiscount:'₱150', eligible:'First-time buyers · eligible products', limit:'1 use per buyer', shop:'Participating ShopEase stores', stacking:'Cannot combine with other vouchers', terms:['Valid only on a buyer’s first successfully placed order.','Minimum eligible item subtotal of ₱1,000, excluding delivery charges.','Cannot be exchanged for cash or applied after checkout.'] },
        { id:'FREESHIP80', category:'shipping', tag:'Shipping Discount', discount:'₱80', headline:'Up to ₱80 OFF', title:'Delivery Fee Saver', purpose:'Reduce delivery fees on eligible COD orders.', expiry:'2026-11-15T23:59:59', starts:'2026-10-20', minSpend:'₱499', maxDiscount:'₱80 shipping discount', eligible:'Orders with available shipping', limit:'2 uses per buyer', shop:'Participating delivery locations', stacking:'Cannot stack with another shipping voucher', terms:['Applies to delivery fee only, up to ₱80.','Any delivery fee above ₱80 is paid by the buyer through COD.','Delivery coverage and order eligibility depend on checkout availability.'] },
        { id:'PAYDAY20', category:'order', tag:'Payday Deal', discount:'20%', headline:'20% OFF', title:'Payday Checkout Treat', purpose:'Discount on eligible cart subtotals.', expiry:'2026-10-31T23:59:59', starts:'2026-10-23', minSpend:'₱1,500', maxDiscount:'₱250', eligible:'Selected products and stores', limit:'1 use per buyer', shop:'Participating ShopEase stores', stacking:'One order discount voucher per checkout', terms:['20% is applied to eligible item subtotal only.','Maximum total discount is ₱250 even if 20% exceeds the cap.','Offer is subject to remaining redemption allocation.'] },
        { id:'BEAUTY100', category:'store', tag:'Category Offer', discount:'₱100', headline:'₱100 OFF', title:'Beauty Essentials Deal', purpose:'Special discount for Health & Beauty purchases.', expiry:'2026-12-15T23:59:59', starts:'2026-10-15', minSpend:'₱799', maxDiscount:'₱100', eligible:'Health & Beauty category only', limit:'1 use per buyer', shop:'Participating Beauty stores', stacking:'Not stackable with category/order vouchers', terms:['Only eligible Health & Beauty items count toward minimum spend.','Other categories in the cart do not increase the qualifying subtotal.','Cancellation or return eligibility follows ShopEase order policies.'] },
        { id:'WEEKEND10', category:'order', tag:'Weekend Special', discount:'10%', headline:'10% OFF', title:'Weekend Cart Promo', purpose:'Take a little extra off your weekend haul.', expiry:'2026-11-08T23:59:59', starts:'2026-10-23', minSpend:'₱599', maxDiscount:'₱120', eligible:'Participating products', limit:'1 use per buyer', shop:'Participating ShopEase stores', stacking:'Cannot combine with other order vouchers', terms:['Valid only when the code is active at checkout.','10% off eligible subtotal, capped at ₱120.','All voucher use is subject to store and product restrictions.'] },
        { id:'HOME200', category:'store', tag:'Home & Living', discount:'₱200', headline:'₱200 OFF', title:'Home Refresh Reward', purpose:'Save on Home & Garden essentials.', expiry:'2026-12-31T23:59:59', starts:'2026-10-15', minSpend:'₱2,000', maxDiscount:'₱200', eligible:'Home & Garden category only', limit:'1 use per buyer', shop:'Participating Home & Garden stores', stacking:'Not stackable with category/order vouchers', terms:['Eligible Home & Garden items must total ₱2,000 or more.','Voucher cannot be converted to cash or used toward COD delivery charges.','Returns or order changes may affect eligibility.'] },
    ];
    const esc = v => String(v ?? '').replace(/[&<>"']/g, ch => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[ch]));
    const formatDate = value => new Date(value).toLocaleDateString('en-US', {month:'short',day:'numeric',year:'numeric'});
    const clockIcon='<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.7"/><path d="M12 7v5l3 2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>';
    const ticketIcon='<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 6h16v4a2 2 0 0 0 0 4v4H4v-4a2 2 0 0 0 0-4V6Z" stroke="currentColor" stroke-width="1.7"/><path d="M12 6v12" stroke="currentColor" stroke-width="1.7" stroke-dasharray="2 3"/></svg>';
    grid.innerHTML=offers.map(v=>`<button type="button" class="bv-voucher-card" data-voucher-id="${esc(v.id)}" data-category="${esc(v.category)}" aria-label="View details for ${esc(v.title)}"><span class="bv-voucher-left">${ticketIcon}<strong class="bv-discount">${esc(v.discount)}</strong><span class="bv-off-label">OFF</span></span><span class="bv-voucher-right"><span class="bv-voucher-tag">${esc(v.tag)}</span><strong class="bv-voucher-title">${esc(v.title)}</strong><span class="bv-voucher-purpose">${esc(v.purpose)}</span><span class="bv-voucher-expiry">${clockIcon} Expires ${esc(formatDate(v.expiry))}</span><span class="bv-voucher-bottom"><span>COD only · Min. ${esc(v.minSpend)}</span><span>View details →</span></span></span></button>`).join('');
    const count = document.getElementById('bvVoucherCount');
    if(count) count.textContent=`${offers.length} vouchers`;
    const modalText=(id,v)=>{const el=document.getElementById(id);if(el)el.textContent=v};
    const detailList = document.getElementById('bvModalDetails');
    const termList = document.getElementById('bvModalTerms');
    let previousFocus=null;
    const close=()=>{overlay.hidden=true;overlay.setAttribute('aria-hidden','true');document.body.classList.remove('bv-modal-open');previousFocus?.focus?.()};
    const open = offer => {
        previousFocus=document.activeElement;
        modalText('bvModalTitle',offer.title);modalText('bvModalValue',offer.headline);modalText('bvModalSubtitle',offer.purpose);modalText('bvModalDescription',`Use code ${offer.id} on a qualifying order. This is a sample voucher for interface testing only.`);
        const details=[['Voucher code',offer.id],['Discount value',offer.headline],['Minimum spend',offer.minSpend],['Maximum discount',offer.maxDiscount],['Valid from',formatDate(offer.starts)],['Expiry',`${formatDate(offer.expiry)} · 11:59 PM (PH time)`],['Eligible items / buyers',offer.eligible],['Participating stores',offer.shop],['Redemption limit',offer.limit],['Payment method','Cash on Delivery (COD)'],['Voucher stacking',offer.stacking],['Current status','Available · Demo only']];
        detailList.replaceChildren(...details.map(([name,value])=>{const box=document.createElement('div');box.className='bv-detail';const label=document.createElement('span');label.textContent=name;const strong=document.createElement('strong');strong.textContent=value;box.append(label,strong);return box}));
        const terms=[...offer.terms,'Voucher must be selected before the COD order is placed.','Voucher has no cash value and cannot be transferred or redeemed after placing an order.','Promotions may have usage quotas and may be cancelled or changed by ShopEase; no actual demo voucher is redeemable.'];
        termList.replaceChildren(...terms.map(t=>{const li=document.createElement('li');li.textContent=t;return li}));
        overlay.hidden=false;overlay.setAttribute('aria-hidden','false');document.querySelector('.bv-dialog')?.focus();
    };
    grid.addEventListener('click',e=>{const card=e.target.closest('[data-voucher-id]');if(!card)return;const offer=offers.find(x=>x.id===card.dataset.voucherId);if(offer)open(offer)});
    document.getElementById('bvModalClose')?.addEventListener('click',close);
    document.getElementById('bvModalDone')?.addEventListener('click',close);
    overlay.addEventListener('click',e=>{if(e.target===overlay)close()});
    document.addEventListener('keydown',e=>{if(!overlay.hidden && e.key==='Escape'){e.stopPropagation();close()}});
});


/* Buyer password changes require a verified email OTP. */
document.addEventListener('DOMContentLoaded', () => {
  const $ = id => document.getElementById(id);
  const passwordForm = $('buyerChangePasswordForm');
  if (!passwordForm) return;
  const current = $('buyerCurrentPassword'), next = $('buyerNewPassword'), confirm = $('buyerConfirmPassword');
  const changeBtn = $('buyerChangePasswordButton');
  const otpModal = $('buyerSecurityOtpModal'), resetModal = $('buyerForgotPasswordModal');
  const otpDigits = [...document.querySelectorAll('.buyer-otp-digit')];
  const otpError = $('buyerOtpError'), resetError = $('buyerResetError');
  const otpVerify = $('buyerVerifyOtp'), resend = $('buyerResendOtp');
  const deliveryMessage = $('buyerOtpDeliveryMessage');
  const toast = $('buyerSettingsToast');
  const endpoints = {
    send: '/api/v1/buyer/account/security/otp/send',
    verify: '/api/v1/buyer/account/security/otp/verify',
    change: '/api/v1/buyer/account/security/password/change',
    reset: '/api/v1/buyer/account/security/password/reset',
  };
  let flow = 'change';
  let verificationToken = '';
  let sending = false;
  let noticeTimer = null;
  function notify(message) { if (!toast) return; toast.textContent = message; toast.hidden = false; clearTimeout(noticeTimer); noticeTimer = setTimeout(() => { toast.hidden = true; }, 4500); }
  function refreshPasswordButton() {changeBtn.disabled = !(current.value.trim() && next.value.length >= 8 && next.value === confirm.value);}
  [current,next,confirm].forEach(input => input?.addEventListener('input',refreshPasswordButton));
  const open = modal => {modal.hidden=false; document.body.classList.add('buyer-security-dialog-open');};
  const close = modal => {modal.hidden=true; if (otpModal.hidden && resetModal.hidden) document.body.classList.remove('buyer-security-dialog-open');};
  const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content || '';
  async function request(url, data) {
    const response = await fetch(url, {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken(),
      },
      body: JSON.stringify(data),
    });
    const result = await response.json();
    if (!response.ok) {
      const validationError = Object.values(result.errors || {}).flat()[0];
      throw new Error(validationError || result.message || 'The security request failed.');
    }
    return result;
  }
  async function sendCode(purpose) {
    if (sending) return;
    sending = true;
    otpError.hidden = true;
    try {
      const result = await request(endpoints.send, {
        purpose,
        ...(purpose === 'change' ? {current_password: current.value} : {}),
      });
      flow = purpose;
      otpDigits.forEach(input => { input.value = ''; });
      deliveryMessage.textContent = result.message;
      deliveryMessage.hidden = false;
      open(otpModal);
      otpDigits[0]?.focus();
    } catch (error) {
      if (purpose === 'change' && error.message.toLowerCase().includes('current password')) {
        current.setCustomValidity(error.message);
        current.reportValidity();
        current.addEventListener('input', () => current.setCustomValidity(''), {once: true});
      } else {
        notify(error.message);
      }
    } finally {
      sending = false;
    }
  }
  passwordForm.addEventListener('submit', event => {
    event.preventDefault();
    if (!changeBtn.disabled) sendCode('change');
  });
  $('buyerForgotPasswordButton')?.addEventListener('click', () => sendCode('forgot'));
  $('buyerCloseOtp')?.addEventListener('click',()=>close(otpModal));
  $('buyerCloseForgot')?.addEventListener('click',()=>close(resetModal));
  [otpModal,resetModal].forEach(modal=>modal?.addEventListener('click',event=>{if (event.target===modal) close(modal);}));
  otpDigits.forEach((input,index)=>{
    input.addEventListener('input',()=>{input.value=input.value.replace(/\D/g,'').slice(-1);if(input.value)otpDigits[index+1]?.focus();});
    input.addEventListener('keydown',e=>{if(e.key==='Backspace'&&!input.value)otpDigits[index-1]?.focus();});
    input.addEventListener('paste',e=>{e.preventDefault();const code=(e.clipboardData?.getData('text')||'').replace(/\D/g,'').slice(0,6);[...code].forEach((v,i)=>otpDigits[i].value=v);otpDigits[Math.min(code.length,5)]?.focus();});
  });
  resend?.addEventListener('click', () => sendCode(flow));
  otpVerify?.addEventListener('click', async () => {
    const code = otpDigits.map(input => input.value).join('');
    if (!/^\d{6}$/.test(code)) {
      otpError.textContent = 'Please enter the six-digit verification code.';
      otpError.hidden = false;
      return;
    }
    otpVerify.disabled = true;
    otpError.hidden = true;
    try {
      const result = await request(endpoints.verify, {purpose: flow, code});
      verificationToken = result.verification_token;
      close(otpModal);
      if (flow === 'forgot') {
        open(resetModal);
        $('buyerResetNew')?.focus();
        return;
      }

      const changed = await request(endpoints.change, {
        current_password: current.value,
        new_password: next.value,
        new_password_confirmation: confirm.value,
        verification_token: verificationToken,
      });
      verificationToken = '';
      passwordForm.reset();
      refreshPasswordButton();
      notify(changed.message);
    } catch (error) {
      otpError.textContent = error.message;
      otpError.hidden = false;
      if (otpModal.hidden) open(otpModal);
    } finally {
      otpVerify.disabled = false;
    }
  });
  $('buyerForgotFinalForm')?.addEventListener('submit', async event => {
    event.preventDefault();
    const password = $('buyerResetNew').value;
    const confirmation = $('buyerResetConfirm').value;
    if (password.length < 8 || password !== confirmation) {
      resetError.textContent = 'Use at least 8 characters and enter matching passwords.';
      resetError.hidden = false;
      return;
    }
    const submit = event.currentTarget.querySelector('[type="submit"]');
    submit.disabled = true;
    resetError.hidden = true;
    try {
      const result = await request(endpoints.reset, {
        new_password: password,
        new_password_confirmation: confirmation,
        verification_token: verificationToken,
      });
      verificationToken = '';
      event.currentTarget.reset();
      close(resetModal);
      notify(result.message);
    } catch (error) {
      resetError.textContent = error.message;
      resetError.hidden = false;
    } finally {
      submit.disabled = false;
    }
  });
  document.addEventListener('keydown',event=>{if(event.key==='Escape'){if(!otpModal.hidden)close(otpModal);if(!resetModal.hidden)close(resetModal);}});
  const preferenceForm=$('buyerNotificationSettingsForm');
  const preferenceButton=$('buyerSaveNotificationSettings');
  if(preferenceForm&&preferenceButton){
    const inputs=[...preferenceForm.querySelectorAll('input[type="checkbox"]')];
    const savedKey='shopease-buyer-notification-preferences-demo-v1';
    try {const saved=JSON.parse(localStorage.getItem(savedKey)||'null');if(saved) inputs.forEach(i=>{if(typeof saved[i.name]==='boolean')i.checked=saved[i.name];});}catch(e){}
    let baseline=new Map(inputs.map(i=>[i,i.checked]));
    const refresh=()=>{preferenceButton.disabled=!inputs.some(i=>i.checked!==baseline.get(i));};
    inputs.forEach(i=>i.addEventListener('change',refresh));refresh();
    preferenceForm.addEventListener('submit',event=>{event.preventDefault();if(preferenceButton.disabled)return;
      const data=Object.fromEntries(inputs.map(i=>[i.name,i.checked]));
      try {localStorage.setItem(savedKey,JSON.stringify(data));}catch(e){}
      baseline=new Map(inputs.map(i=>[i,i.checked]));refresh();notify('Demo preferences saved in this browser only. No server settings were updated.');
    });
  }
});

/* Open the matching order detail page from either Blade demo cards or API-rendered cards.
   Controls inside a card retain their own behavior. */
document.addEventListener('click', (event) => {
    const card = event.target.closest('#purchaseRows [data-view-order-id]');
    if (!card || event.target.closest('a, button, input, select, textarea, label, [role="button"], [data-no-order-navigation]')) return;
    const orderId = card.dataset.viewOrderId;
    if (orderId) window.location.assign(`/buyer/view-order?order=${encodeURIComponent(orderId)}`);
});
document.addEventListener('keydown', (event) => {
    if (event.key !== 'Enter' && event.key !== ' ') return;
    const card = event.target.closest('#purchaseRows [data-view-order-id]');
    if (!card || event.target !== card) return;
    event.preventDefault();
    window.location.assign(`/buyer/view-order?order=${encodeURIComponent(card.dataset.viewOrderId)}`);
});