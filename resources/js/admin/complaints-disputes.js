const complaintsConfigElement =
    document.getElementById('complaintsDisputesConfig');

const complaintsConfig = complaintsConfigElement
    ? JSON.parse(complaintsConfigElement.textContent)
    : {
        complaints: [],
        icons: {
            buyer: '',
            seller: ''
        }
    };

document.addEventListener('DOMContentLoaded', function () {
    const complaints = complaintsConfig.complaints || [];

    const statusLabels = {
        open: 'Open',
        'in-progress': 'In progress',
        resolved: 'Resolved'
    };

    const rows = Array.from(document.querySelectorAll('.complaint-row'));
    const tabs = Array.from(document.querySelectorAll('.complaints-tab'));

    const search = document.getElementById('complaints-search');
    const dateFilter = document.getElementById('complaints-date-filter');
    const dateButton = document.getElementById('complaints-date-button');
    const reload = document.getElementById('complaints-reload');

    const count = document.getElementById('complaints-count');
    const empty = document.getElementById('complaints-empty');
    const statOpen = document.getElementById('complaint-stat-open');
    const statInProgress = document.getElementById('complaint-stat-in-progress');
    const statResolved = document.getElementById('complaint-stat-resolved');
    const statTotal = document.getElementById('complaint-stat-total');

    const workspace = document.getElementById('complaints-workspace');
    const detailCard = document.getElementById('complaints-detail-card');
    const detailClose = document.getElementById('complaints-detail-close');

    const detailId = document.getElementById('detail-id');
    const detailStatus = document.getElementById('detail-status');
    const detailDate = document.getElementById('detail-date');
    const detailType = document.getElementById('detail-type');
    const detailDescription = document.getElementById('detail-description');
    const detailParty1 = document.getElementById('detail-party-1');
    const detailRole1 = document.getElementById('detail-role-1');
    const detailParty2 = document.getElementById('detail-party-2');
    const detailRole2 = document.getElementById('detail-role-2');

    const updateStatus = document.getElementById('complaints-update-status');
    const statusMenu = document.getElementById('complaints-status-menu');

    const viewDetails = document.getElementById('complaints-view-details');
    const detailsModal = document.getElementById('complaints-details-modal');

    const modalComplaintId = document.getElementById('modal-complaint-id');
    const modalComplaintStatus = document.getElementById('modal-complaint-status');
    const modalDateFiled = document.getElementById('modal-date-filed');
    const modalLastUpdated = document.getElementById('modal-last-updated');
    const modalComplaintType = document.getElementById('modal-complaint-type');
    const modalOrderIdTop = document.getElementById('modal-order-id-top');
    const modalDescription = document.getElementById('modal-description-copy');
    const modalParty1Name = document.getElementById('modal-party-1-name');
    const modalParty1Email = document.getElementById('modal-party-1-email');
    const modalParty1Role = document.getElementById('modal-party-1-role');
    const modalParty1RoleIcon = document.getElementById('modal-party-1-role-icon');

    const modalParty2Name = document.getElementById('modal-party-2-name');
    const modalParty2Email = document.getElementById('modal-party-2-email');
    const modalParty2Role = document.getElementById('modal-party-2-role');
    const modalParty2RoleIcon = document.getElementById('modal-party-2-role-icon');

    const buyerRoleIconUrl =
        complaintsConfig.icons.buyer || '';

    const sellerRoleIconUrl =
        complaintsConfig.icons.seller || '';

    const modalOrderId = document.getElementById('modal-order-id');
    const modalOrderDate = document.getElementById('modal-order-date');
    const modalPaymentMethod = document.getElementById('modal-payment-method');
    const modalPaymentStatus = document.getElementById('modal-payment-status');
    const modalShippingMethod = document.getElementById('modal-shipping-method');
    const modalOrderStatus = document.getElementById('modal-order-status');
    const modalOrderTotal = document.getElementById('modal-order-total');
    const modalProductName = document.getElementById('modal-product-name');
    const modalProductVariant = document.getElementById('modal-product-variant');
    const modalProductPrice = document.getElementById('modal-product-price');
    const modalProductQuantity = document.getElementById('modal-product-quantity');
    const modalProductCategory = document.getElementById('modal-product-category');
    const modalShopName = document.getElementById('modal-shop-name');
    const modalShopOwner = document.getElementById('modal-shop-owner');
    const modalUpdateStatus = document.getElementById('complaints-modal-update-status');
    const modalStatusMenu = document.getElementById('complaints-modal-status-menu');
    const modalNotesList = document.getElementById('complaints-modal-notes-list');
    const modalMessageButtons = Array.from(
        document.querySelectorAll('.complaints-modal-message-button')
    );

    /*
     * Complaint tracking storage.
     *
     * The status history already works now.
     *
     * FUTURE MESSAGE PAGE:
     * After a message is ACTUALLY sent successfully, that page only needs
     * to write a success payload to MESSAGE_SENT_STORAGE_KEY. This page
     * will consume it and add "You messaged <user>." to Notes/Updates.
     */
    const COMPLAINT_UPDATES_STORAGE_KEY =
        'shopease.complaintUpdates.v1';

    const PENDING_MESSAGE_STORAGE_KEY =
        'shopease.pendingComplaintMessage.v1';

    const MESSAGE_SENT_STORAGE_KEY =
        'shopease.complaintMessageSent.v1';

    const flash = document.getElementById('complaints-flash');
    const flashIcon = document.getElementById('complaints-flash-icon');
    const flashTitle = document.getElementById('complaints-flash-title');
    const flashMessage = document.getElementById('complaints-flash-message');
    const flashClose = document.getElementById('complaints-flash-close');

    let activeTab = 'all';
    let activeIndex = null;
    let flashTimer = null;

    function statusClass(status) {
        if (status === 'in-progress') return 'in-progress';
        if (status === 'resolved') return 'resolved';
        return 'open';
    }

    function safeJsonParse(value, fallback) {
        try {
            return JSON.parse(value);
        } catch (error) {
            return fallback;
        }
    }

    function readComplaintUpdatesStore() {
        try {
            return safeJsonParse(
                localStorage.getItem(COMPLAINT_UPDATES_STORAGE_KEY),
                {}
            ) || {};
        } catch (error) {
            return {};
        }
    }

    function writeComplaintUpdatesStore(store) {
        try {
            localStorage.setItem(
                COMPLAINT_UPDATES_STORAGE_KEY,
                JSON.stringify(store)
            );
        } catch (error) {
            /*
             * Tracking still works for the current page even when
             * browser storage is unavailable.
             */
        }
    }

    function escapeTrackingHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function formatTrackingDateTime(value) {
        const date =
            value instanceof Date
                ? value
                : new Date(value);

        if (Number.isNaN(date.getTime())) {
            return '';
        }

        return new Intl.DateTimeFormat('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        }).format(date);
    }

    function getComplaintStoredUpdates(complaintId) {
        const store =
            readComplaintUpdatesStore();

        const updates =
            Array.isArray(store[complaintId])
                ? store[complaintId]
                : [];

        return updates
            .filter(update => update && update.id && update.message)
            .sort((a, b) => {
                return new Date(b.timestamp).getTime() -
                    new Date(a.timestamp).getTime();
            });
    }

    function getComplaintUpdates(complaint) {
        const serverUpdates =
            Array.isArray(complaint?.updates)
                ? complaint.updates
                : [];

        const storedUpdates =
            getComplaintStoredUpdates(complaint?.id);

        const merged =
            [...serverUpdates, ...storedUpdates];

        const seen =
            new Set();

        return merged
            .filter(update => {
                if (!update || !update.id || seen.has(update.id)) {
                    return false;
                }

                seen.add(update.id);
                return true;
            })
            .sort((a, b) => {
                return new Date(b.timestamp).getTime() -
                    new Date(a.timestamp).getTime();
            });
    }

    function getComplaintLastUpdated(complaint) {
        const latest =
            getComplaintUpdates(complaint)[0];

        if (latest?.timestamp) {
            return formatTrackingDateTime(
                latest.timestamp
            );
        }

        if (complaint?.lastUpdated) {
            return complaint.lastUpdated;
        }

        return `${complaint?.date || ''} ${complaint?.time || ''}`.trim();
    }

    function renderComplaintUpdates(complaint) {
        if (!modalNotesList) {
            return;
        }

        const updates =
            getComplaintUpdates(complaint);

        if (!updates.length) {
            modalNotesList.innerHTML = `
                <div class="complaints-modal-notes-empty">
                    No updates yet.
                </div>
            `;
            return;
        }

        modalNotesList.innerHTML =
            updates.map(update => {
                const actor =
                    escapeTrackingHtml(
                        update.actor || 'Admin'
                    );

                const message =
                    escapeTrackingHtml(
                        update.message
                    );

                const time =
                    escapeTrackingHtml(
                        formatTrackingDateTime(
                            update.timestamp
                        )
                    );

                const initial =
                    escapeTrackingHtml(
                        (update.actor || 'Admin')
                            .trim()
                            .charAt(0)
                            .toUpperCase() || 'A'
                    );

                return `
                    <div class="complaints-modal-note" data-update-id="${escapeTrackingHtml(update.id)}">
                        <span class="complaints-modal-note-avatar">${initial}</span>

                        <div class="complaints-modal-note-copy">
                            <strong>${actor}</strong>
                            <span>${message}</span>
                        </div>

                        <span class="complaints-modal-note-time">${time}</span>
                    </div>
                `;
            }).join('');
    }

    function persistComplaintUpdate(complaintId, update) {
        const store =
            readComplaintUpdatesStore();

        const current =
            Array.isArray(store[complaintId])
                ? store[complaintId]
                : [];

        if (
            current.some(item =>
                item?.id === update.id
            )
        ) {
            return;
        }

        store[complaintId] =
            [update, ...current].slice(0, 100);

        writeComplaintUpdatesStore(store);
    }

    function createComplaintUpdate(
        complaint,
        {
            type,
            message,
            actor = 'Admin',
            timestamp = new Date().toISOString(),
            meta = {}
        }
    ) {
        if (!complaint?.id || !message) {
            return null;
        }

        const update = {
            id: `${complaint.id}-${type}-${Date.now()}-${Math.random().toString(36).slice(2, 8)}`,
            complaintId: complaint.id,
            type,
            message,
            actor,
            timestamp,
            meta
        };

        persistComplaintUpdate(
            complaint.id,
            update
        );

        complaint.lastUpdated =
            formatTrackingDateTime(timestamp);

        renderComplaintUpdates(
            complaint
        );

        return update;
    }

    function syncStatusUi(complaint, newStatus, row) {
        complaint.status =
            newStatus;

        row.dataset.status =
            newStatus;

        const rowStatus =
            row.querySelector('.complaint-status');

        rowStatus.className =
            `complaint-status ${statusClass(newStatus)}`;

        rowStatus.textContent =
            statusLabels[newStatus];

        detailStatus.className =
            `complaint-status ${statusClass(newStatus)}`;

        detailStatus.textContent =
            statusLabels[newStatus];

        if (statOpen) {
            statOpen.textContent = complaints.filter(item => item.status === 'open').length;
            statInProgress.textContent = complaints.filter(item => item.status === 'in-progress').length;
            statResolved.textContent = complaints.filter(item => item.status === 'resolved').length;
            statTotal.textContent = complaints.length;
        }
    }

    async function applyComplaintStatusChange(newStatus) {
        const complaint =
            complaints[activeIndex];

        const row =
            rows.find(item =>
                Number(item.dataset.index) === activeIndex
            );

        if (!complaint || !row) {
            return;
        }

        const previousStatus =
            complaint.status;

        if (previousStatus === newStatus) {
            statusMenu.classList.remove('show');
            modalStatusMenu.classList.remove('show');
            return;
        }

        const csrfToken =
            document.querySelector('meta[name="csrf-token"]')?.content || '';

        try {
            const response = await fetch(
                `/admin/complaints-disputes/${complaint.databaseId}/status`,
                {
                    method: 'PATCH',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ status: newStatus }),
                }
            );
            const payload = await response.json();

            if (!response.ok) {
                throw new Error(payload.message || 'Complaint status could not be updated.');
            }

            if (payload.data.update) {
                complaint.updates = [
                    payload.data.update,
                    ...(complaint.updates || []),
                ];
            }
        } catch (error) {
            window.alert(error.message);
            return;
        }

        syncStatusUi(
            complaint,
            newStatus,
            row
        );

        complaint.lastUpdated = new Date().toISOString();
        renderComplaintUpdates(complaint);

        syncComplaintModal(
            complaint
        );

        statusMenu.classList.remove('show');
        modalStatusMenu.classList.remove('show');

        showStatusFlash(
            newStatus,
            complaint
        );

        filterRows();
    }

    function storePendingMessageContext(complaint, partyIndex) {
        if (!complaint) {
            return null;
        }

        const modalData =
            getComplaintModalData(
                complaint,
                activeIndex
            );

        const isFirstParty =
            Number(partyIndex) === 1;

        const context = {
            complaintId: complaint.id,
            recipientName:
                isFirstParty
                    ? complaint.party1
                    : complaint.party2,
            recipientRole:
                isFirstParty
                    ? complaint.role1
                    : complaint.role2,
            recipientEmail:
                isFirstParty
                    ? modalData.party1Email
                    : modalData.party2Email,
            createdAt: new Date().toISOString()
        };

        try {
            localStorage.setItem(
                PENDING_MESSAGE_STORAGE_KEY,
                JSON.stringify(context)
            );
        } catch (error) {
        }

        return context;
    }

    function recordMessageSentUpdate(payload = {}) {
        const pending =
            safeJsonParse(
                (() => {
                    try {
                        return localStorage.getItem(
                            PENDING_MESSAGE_STORAGE_KEY
                        );
                    } catch (error) {
                        return null;
                    }
                })(),
                {}
            ) || {};

        const complaintId =
            payload.complaintId ||
            pending.complaintId;

        const recipientName =
            payload.recipientName ||
            pending.recipientName ||
            'the user';

        const timestamp =
            payload.timestamp ||
            payload.sentAt ||
            new Date().toISOString();

        if (!complaintId) {
            return;
        }

        const complaint =
            complaints.find(item =>
                item.id === complaintId
            );

        const update = {
            id:
                payload.id ||
                `${complaintId}-message-${Date.now()}-${Math.random().toString(36).slice(2, 8)}`,
            complaintId,
            type: 'message',
            message: `You messaged ${recipientName}.`,
            actor: payload.actor || 'Admin',
            timestamp,
            meta: {
                recipientName
            }
        };

        persistComplaintUpdate(
            complaintId,
            update
        );

        if (complaint) {
            complaint.lastUpdated =
                formatTrackingDateTime(timestamp);

            if (
                activeIndex !== null &&
                complaints[activeIndex]?.id === complaintId
            ) {
                syncComplaintModal(
                    complaint
                );
            }
        }

        try {
            localStorage.removeItem(
                MESSAGE_SENT_STORAGE_KEY
            );
        } catch (error) {
        }
    }

    function consumeMessageSentBridge(rawPayload = null) {
        let payload =
            rawPayload;

        if (!payload) {
            try {
                payload =
                    safeJsonParse(
                        localStorage.getItem(
                            MESSAGE_SENT_STORAGE_KEY
                        ),
                        null
                    );
            } catch (error) {
                payload = null;
            }
        }

        if (!payload) {
            return;
        }

        recordMessageSentUpdate(
            payload
        );
    }

    window.ShopEaseComplaintTracking = {
        updatesStorageKey:
            COMPLAINT_UPDATES_STORAGE_KEY,

        pendingMessageStorageKey:
            PENDING_MESSAGE_STORAGE_KEY,

        messageSentStorageKey:
            MESSAGE_SENT_STORAGE_KEY,

        recordMessageSent(payload = {}) {
            const eventPayload = {
                ...payload,
                sentAt:
                    payload.sentAt ||
                    new Date().toISOString(),
                nonce:
                    payload.nonce ||
                    Date.now()
            };

            try {
                localStorage.setItem(
                    MESSAGE_SENT_STORAGE_KEY,
                    JSON.stringify(eventPayload)
                );
            } catch (error) {
                recordMessageSentUpdate(
                    eventPayload
                );
                return;
            }

            consumeMessageSentBridge(
                eventPayload
            );
        }
    };

    function getComplaintModalData(complaint) {
        return {
            orderId: complaint.orderId || '—',
            lastUpdated: getComplaintLastUpdated(complaint),
            orderDate: complaint.orderDate || '—',
            paymentMethod: complaint.paymentMethod || '—',
            paymentStatus: complaint.paymentStatus || '—',
            shippingMethod: complaint.shippingMethod || '—',
            orderStatus: complaint.orderStatus || '—',
            orderTotal: complaint.orderTotal || '—',
            productName: complaint.productName || '—',
            productVariant: complaint.productVariant || '—',
            productPrice: complaint.productPrice || '—',
            productQuantity: complaint.productQuantity || '—',
            productCategory: complaint.productCategory || '—',
            shopName: complaint.shopName || complaint.party2 || '—',
            shopOwner: complaint.shopOwner || '—',
            party1Email: complaint.party1Email || '—',
            party2Email: complaint.party2Email || '—',
        };
    }

    function syncModalPartyRoleIcon(image, role) {
        if (!image) {
            return;
        }

        const normalizedRole =
            String(role || '').trim().toLowerCase();

        if (normalizedRole === 'buyer') {
            image.src = buyerRoleIconUrl;
            image.alt = 'Buyer';
            image.hidden = false;
            return;
        }

        if (normalizedRole === 'seller') {
            image.src = sellerRoleIconUrl;
            image.alt = 'Seller';
            image.hidden = false;
            return;
        }

        image.hidden = true;
        image.removeAttribute('src');
        image.alt = '';
    }

    function syncComplaintModal(complaint) {
        if (!complaint) {
            return;
        }

        const modalData =
            getComplaintModalData(
                complaint,
                activeIndex
            );

        modalComplaintId.textContent =
            complaint.id;

        modalComplaintStatus.className =
            `complaints-modal-status ${statusClass(complaint.status)}`;

        modalComplaintStatus.textContent =
            statusLabels[complaint.status] || 'Open';

        modalDateFiled.textContent =
            `${complaint.date}  ${complaint.time}`;

        modalLastUpdated.textContent =
            modalData.lastUpdated;

        modalComplaintType.textContent =
            complaint.type;

        modalOrderIdTop.textContent =
            modalData.orderId;

        modalDescription.textContent =
            complaint.description;

        modalParty1Name.textContent =
            complaint.party1;

        modalParty1Email.textContent =
            modalData.party1Email;

        modalParty1Role.textContent =
            complaint.role1;

        syncModalPartyRoleIcon(
            modalParty1RoleIcon,
            complaint.role1
        );

        modalParty2Name.textContent =
            complaint.party2;

        modalParty2Email.textContent =
            modalData.party2Email;

        modalParty2Role.textContent =
            complaint.role2;

        syncModalPartyRoleIcon(
            modalParty2RoleIcon,
            complaint.role2
        );

        modalOrderId.textContent =
            modalData.orderId;

        modalOrderDate.textContent =
            modalData.orderDate;

        modalPaymentMethod.textContent =
            modalData.paymentMethod;

        modalPaymentStatus.textContent =
            modalData.paymentStatus;

        modalShippingMethod.textContent =
            modalData.shippingMethod;

        modalOrderStatus.textContent =
            modalData.orderStatus;

        modalOrderTotal.textContent =
            modalData.orderTotal;

        modalProductName.textContent =
            modalData.productName;

        modalProductVariant.textContent =
            modalData.productVariant;

        modalProductPrice.textContent =
            modalData.productPrice;

        modalProductQuantity.textContent =
            modalData.productQuantity;

        modalProductCategory.textContent =
            modalData.productCategory;

        modalShopName.textContent =
            modalData.shopName;

        modalShopOwner.textContent =
            modalData.shopOwner;

        renderComplaintUpdates(
            complaint
        );
    }

    function updateDetails(index) {
        const complaint = complaints[index];

        if (!complaint) {
            return;
        }

        activeIndex = Number(index);

        workspace.classList.add('has-selection');
        detailCard.setAttribute('aria-hidden', 'false');

        rows.forEach(row => {
            row.classList.toggle(
                'active',
                Number(row.dataset.index) === activeIndex
            );
        });

        detailId.textContent = complaint.id;

        detailStatus.className =
            `complaint-status ${statusClass(complaint.status)}`;

        detailStatus.textContent =
            statusLabels[complaint.status] || 'Open';

        detailDate.textContent =
            `${complaint.date} ${complaint.time}`;

        detailType.textContent =
            complaint.type;

        detailDescription.textContent =
            complaint.description;

        detailParty1.textContent =
            complaint.party1;

        detailRole1.textContent =
            complaint.role1;

        detailParty2.textContent =
            complaint.party2;

        detailRole2.textContent =
            complaint.role2;

        syncComplaintModal(complaint);
    }

    function closeComplaintDetails() {
        activeIndex = null;

        workspace.classList.remove('has-selection');
        detailCard.setAttribute('aria-hidden', 'true');

        rows.forEach(row => {
            row.classList.remove('active');
        });

        statusMenu.classList.remove('show');
    }

    function toIsoDate(dateLabel) {
        if (!dateLabel) {
            return '';
        }

        const parsed = new Date(`${dateLabel} 00:00:00`);

        if (Number.isNaN(parsed.getTime())) {
            return '';
        }

        const year = parsed.getFullYear();
        const month = String(parsed.getMonth() + 1).padStart(2, '0');
        const day = String(parsed.getDate()).padStart(2, '0');

        return `${year}-${month}-${day}`;
    }

    function filterRows() {
        const query =
            (search.value || '').trim().toLowerCase();

        const selectedDate =
            dateFilter.value;

        let visible = 0;

        rows.forEach(row => {
            const complaint =
                complaints[Number(row.dataset.index)];

            if (!complaint) {
                return;
            }

            const haystack =
                `${complaint.id} ${complaint.party1} ${complaint.party2}`
                    .toLowerCase();

            const matchesSearch =
                !query ||
                haystack.includes(query);

            const matchesTab =
                activeTab === 'all' ||
                complaint.status === activeTab;

            const complaintDate =
                toIsoDate(complaint.date);

            const matchesDate =
                !selectedDate ||
                complaintDate === selectedDate;

            const show =
                matchesSearch &&
                matchesTab &&
                matchesDate;

            row.style.display =
                show ? '' : 'none';

            if (show) {
                visible++;
            }
        });

        empty.style.display =
            visible ? 'none' : 'block';

        count.textContent =
            `Showing ${visible} out of ${rows.length} entries`;

        if (activeIndex !== null) {
            const activeRow = rows.find(
                row => Number(row.dataset.index) === activeIndex
            );

            if (!activeRow || activeRow.style.display === 'none') {
                closeComplaintDetails();
            }
        }
    }

    function closeFlash() {
        if (flashTimer) {
            clearTimeout(flashTimer);
            flashTimer = null;
        }

        flash.classList.remove('show');
    }

    function showStatusFlash(status, complaint) {
        const config = {
            open: {
                title: 'Complaint Reopened',
                message: `${complaint.id} has been marked as Open.`,
                iconBg: 'bg-[#FFE3E5]',
                iconColor: '#B3262E',
                icon: `
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="8.5" stroke-width="2"/>
                        <path d="M12 8v5M12 16h.01" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                `
            },

            'in-progress': {
                title: 'Complaint In Progress',
                message: `${complaint.id} is now under review.`,
                iconBg: 'bg-[#FFF0E0]',
                iconColor: '#C96A00',
                icon: `
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="8.5" stroke-width="2"/>
                        <path d="M12 7.5v5h4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                `
            },

            resolved: {
                title: 'Complaint Resolved',
                message: `${complaint.id} has been marked as Resolved.`,
                iconBg: 'bg-[#DDF0D6]',
                iconColor: '#28721B',
                icon: `
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="m5 12 4 4L19 6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                `
            }
        }[status];

        if (!config) {
            return;
        }

        flash.classList.remove(
            'open',
            'in-progress',
            'resolved'
        );

        flash.classList.add(status);

        flashIcon.className =
            `w-9 h-9 rounded-full flex items-center justify-center shrink-0 ${config.iconBg}`;

        flashIcon.style.color =
            config.iconColor;

        flashIcon.innerHTML =
            config.icon;

        flashTitle.textContent =
            config.title;

        flashMessage.textContent =
            config.message;

        if (flashTimer) {
            clearTimeout(flashTimer);
        }

        flash.classList.add('show');

        flashTimer =
            setTimeout(closeFlash, 3800);
    }

    rows.forEach(row => {
        row.addEventListener('click', function () {
            updateDetails(
                Number(row.dataset.index)
            );
        });

        row.addEventListener('keydown', function (event) {
            if (
                event.key !== 'Enter' &&
                event.key !== ' '
            ) {
                return;
            }

            event.preventDefault();

            updateDetails(
                Number(row.dataset.index)
            );
        });
    });

    detailClose.addEventListener('click', function (event) {
        event.stopPropagation();
        closeComplaintDetails();
    });

    tabs.forEach(tab => {
        tab.addEventListener('click', function () {
            tabs.forEach(item => {
                item.classList.remove('active');
            });

            tab.classList.add('active');

            activeTab =
                tab.dataset.tab;

            filterRows();
        });
    });

    search.addEventListener(
        'input',
        filterRows
    );

    dateFilter.addEventListener(
        'change',
        filterRows
    );

    dateButton.addEventListener('click', function () {
        try {
            if (typeof dateFilter.showPicker === 'function') {
                dateFilter.showPicker();
            } else {
                dateFilter.focus();
                dateFilter.click();
            }
        } catch (error) {
            dateFilter.focus();
            dateFilter.click();
        }
    });

    reload.addEventListener('click', function () {
        reload.classList.add('is-spinning');

        search.value = '';
        dateFilter.value = '';

        tabs.forEach(item => {
            item.classList.toggle(
                'active',
                item.dataset.tab === 'all'
            );
        });

        activeTab = 'all';

        filterRows();

        setTimeout(() => {
            reload.classList.remove('is-spinning');
        }, 500);
    });

    updateStatus.addEventListener('click', function (event) {
        event.stopPropagation();

        statusMenu.classList.toggle('show');
    });

    document
        .querySelectorAll('.complaints-status-option')
        .forEach(option => {

            option.addEventListener('click', function () {
                applyComplaintStatusChange(
                    option.dataset.status
                );
            });
        });

    document.addEventListener('click', function (event) {
        if (!event.target.closest('.complaints-update-wrap')) {
            statusMenu.classList.remove('show');
        }

        if (!event.target.closest('.complaints-modal-update-wrap')) {
            modalStatusMenu.classList.remove('show');
        }
    });

    viewDetails.addEventListener('click', function () {
        const complaint =
            complaints[activeIndex];

        if (!complaint) {
            return;
        }

        syncComplaintModal(complaint);

        detailsModal.classList.remove('hidden');
        detailsModal.classList.add('flex', 'is-open');
        detailsModal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
    });

    function closeDetailsModal() {
        modalStatusMenu.classList.remove('show');

        detailsModal.classList.add('hidden');
        detailsModal.classList.remove('flex', 'is-open');

        detailsModal.setAttribute(
            'aria-hidden',
            'true'
        );

        document.body.classList.remove(
            'overflow-hidden'
        );
    }

    detailsModal.addEventListener('click', function (event) {
        if (event.target === detailsModal) {
            closeDetailsModal();
        }
    });

    modalMessageButtons.forEach(button => {
        button.addEventListener('click', function () {
            const complaint =
                complaints[activeIndex];

            if (!complaint) {
                return;
            }

            const context =
                storePendingMessageContext(
                    complaint,
                    button.dataset.partyIndex
                );

            window.dispatchEvent(
                new CustomEvent(
                    'shopease:complaint-message-requested',
                    {
                        detail: context
                    }
                )
            );
        });
    });

    modalUpdateStatus.addEventListener('click', function (event) {
        event.stopPropagation();

        modalStatusMenu.classList.toggle('show');
    });

    document
        .querySelectorAll('.complaints-modal-status-option')
        .forEach(option => {
            option.addEventListener('click', function () {
                applyComplaintStatusChange(
                    option.dataset.status
                );
            });
        });

    window.addEventListener(
        'shopease:complaint-message-sent',
        function (event) {
            recordMessageSentUpdate(
                event.detail || {}
            );
        }
    );

    window.addEventListener(
        'storage',
        function (event) {
            if (
                event.key !== MESSAGE_SENT_STORAGE_KEY ||
                !event.newValue
            ) {
                return;
            }

            consumeMessageSentBridge(
                safeJsonParse(
                    event.newValue,
                    null
                )
            );
        }
    );

    consumeMessageSentBridge();

    flashClose.addEventListener(
        'click',
        closeFlash
    );

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') {
            return;
        }

        statusMenu.classList.remove('show');
        modalStatusMenu.classList.remove('show');

        if (!detailsModal.classList.contains('hidden')) {
            closeDetailsModal();
            return;
        }

        if (workspace.classList.contains('has-selection')) {
            closeComplaintDetails();
        }
    });

    const pageContent =
        document.getElementById('admin-content');

    pageContent.classList.add(
        'opacity-0',
        'translate-y-2'
    );

    requestAnimationFrame(() => {
        setTimeout(() => {
            pageContent.classList.remove(
                'opacity-0',
                'translate-y-2'
            );
        }, 70);
    });

    closeComplaintDetails();
    filterRows();
});