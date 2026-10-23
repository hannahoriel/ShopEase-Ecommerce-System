document.addEventListener('DOMContentLoaded', () => {
    const tabs =
        Array.from(
            document.querySelectorAll('[data-admin-message-tab]')
        );

    const partyTabs =
        Array.from(
            document.querySelectorAll('[data-complaint-party]')
        );

    const threadList =
        document.getElementById('adminMessageThreadList');

    const emptyThreadList =
        document.getElementById('adminEmptyThreadList');

    const searchInput =
        document.getElementById('adminMessagesSearch');

    const chatPanel =
        document.querySelector('.admin-chat-panel');

    const activeAvatar =
        document.getElementById('adminActiveAvatar');

    const activeTitle =
        document.getElementById('adminActiveTitle');

    const activeRoleBadge =
        document.getElementById('adminActiveRoleBadge');

    const activeSubtitle =
        document.getElementById('adminActiveSubtitle');

    const headerComplaintActions = document.getElementById('adminComplaintHeaderActions');
    const headerComplaintStatus = document.getElementById('adminHeaderComplaintStatus');
    const headerViewComplaint = document.getElementById('adminHeaderViewComplaint');

    const complaintPartySwitchWrap =
        document.getElementById('complaintPartySwitchWrap');

    const complaintCaseReference =
        document.getElementById('complaintCaseReference');

    const complaintCaseStatus =
        document.getElementById('complaintCaseStatus');

    const complaintBuyerUnread =
        document.getElementById('complaintBuyerUnread');

    const complaintSellerUnread =
        document.getElementById('complaintSellerUnread');

    const contextLabel =
        document.getElementById('adminContextLabel');

    const contextValue =
        document.getElementById('adminContextValue');

    const viewContextButton =
        document.getElementById('adminViewContextButton');

    const chatBody =
        document.getElementById('adminChatBody');

    const composerInput =
        document.getElementById('adminMessageInput');

    const sendButton =
        document.getElementById('adminSendMessageButton');


    const conversations = {
        logistics: [
            {
                id: 'logistics-1',
                type: 'logistics',
                title: 'Ease Express',
                initials: 'EE',
                role: 'Logistics',
                subtitle: 'Logistics Partner · Online',
                contextLabel: 'Shipment Reference',
                contextValue: '#SHP-1098 · 12 active deliveries',
                contextButton: 'View Logistics',
                preview: 'Pickup schedule has been confirmed.',
                time: '4m',
                unread: 2,
                messages: [
                    { type: 'divider', label: 'Today' },
                    {
                        sender: 'party',
                        text: 'Good morning Admin. Pickup schedule for Batch #SHP-1098 has been confirmed.',
                        time: '10:04 AM'
                    },
                    {
                        sender: 'admin',
                        text: 'Thank you. Please update the tracking status once the parcels are scanned.',
                        time: '10:06 AM',
                        seen: true
                    },
                    {
                        sender: 'party',
                        text: 'Will do. The first scan should reflect within the hour.',
                        time: '10:08 AM'
                    }
                ]
            },
            {
                id: 'logistics-2',
                type: 'logistics',
                title: 'SwiftGo Logistics',
                initials: 'SG',
                role: 'Logistics',
                subtitle: 'Logistics Partner · Active 16m ago',
                contextLabel: 'Shipment Reference',
                contextValue: '#SHP-1087 · Delayed route',
                contextButton: 'View Logistics',
                preview: 'We are checking the delayed route.',
                time: '16m',
                unread: 0,
                messages: [
                    { type: 'divider', label: 'Today' },
                    {
                        sender: 'party',
                        text: 'We are currently checking the delayed route for Shipment #SHP-1087.',
                        time: '9:45 AM'
                    },
                    {
                        sender: 'admin',
                        text: 'Please provide the updated ETA as soon as it is available.',
                        time: '9:48 AM',
                        seen: true
                    }
                ]
            }
        ],

        buyers: [
            {
                id: 'buyer-1',
                type: 'buyers',
                title: 'Juan Dela Cruz',
                initials: 'JD',
                role: 'Buyer',
                subtitle: 'Buyer Account · Online',
                contextLabel: 'Account / Order Reference',
                contextValue: 'Buyer #BUY-1042 · Order #ORD-2028',
                contextButton: 'View Buyer',
                preview: 'I need help with my order status.',
                time: '7m',
                unread: 1,
                messages: [
                    { type: 'divider', label: 'Today' },
                    {
                        sender: 'party',
                        text: 'Hello Admin, I need help checking the status of Order #ORD-2028.',
                        time: '10:14 AM'
                    },
                    {
                        sender: 'admin',
                        text: 'Hi Juan. I can see that the order is already in transit. I will also verify the latest courier scan for you.',
                        time: '10:17 AM',
                        seen: true
                    }
                ]
            },
            {
                id: 'buyer-2',
                type: 'buyers',
                title: 'Maria Santos',
                initials: 'MS',
                role: 'Buyer',
                subtitle: 'Buyer Account · Active 1h ago',
                contextLabel: 'Account Reference',
                contextValue: 'Buyer #BUY-1038',
                contextButton: 'View Buyer',
                preview: 'Thank you for assisting me.',
                time: '1h',
                unread: 0,
                messages: [
                    { type: 'divider', label: 'Today' },
                    {
                        sender: 'admin',
                        text: 'Your account verification concern has been resolved.',
                        time: '8:54 AM',
                        seen: true
                    },
                    {
                        sender: 'party',
                        text: 'Thank you for assisting me.',
                        time: '8:57 AM'
                    }
                ]
            }
        ],

        sellers: [
            {
                id: 'seller-1',
                type: 'sellers',
                title: 'HahaShop',
                initials: 'HS',
                role: 'Seller',
                subtitle: 'Seller Account · Online',
                contextLabel: 'Seller Reference',
                contextValue: 'Seller #SEL-2031 · Verified',
                contextButton: 'View Seller',
                preview: 'Can you review our compliance submission?',
                time: '9m',
                unread: 1,
                messages: [
                    { type: 'divider', label: 'Today' },
                    {
                        sender: 'party',
                        text: 'Hi Admin, can you review our latest compliance submission?',
                        time: '10:09 AM'
                    },
                    {
                        sender: 'admin',
                        text: 'Yes. The documents are already in the review queue. We will update the status after validation.',
                        time: '10:12 AM',
                        seen: true
                    }
                ]
            },
            {
                id: 'seller-2',
                type: 'sellers',
                title: 'Tech Haven',
                initials: 'TH',
                role: 'Seller',
                subtitle: 'Seller Account · Active 28m ago',
                contextLabel: 'Seller Reference',
                contextValue: 'Seller #SEL-2016 · Active',
                contextButton: 'View Seller',
                preview: 'The requested document was uploaded.',
                time: '28m',
                unread: 0,
                messages: [
                    { type: 'divider', label: 'Today' },
                    {
                        sender: 'party',
                        text: 'The requested business document has been uploaded.',
                        time: '9:31 AM'
                    },
                    {
                        sender: 'admin',
                        text: 'Received. Thank you.',
                        time: '9:34 AM',
                        seen: true
                    }
                ]
            }
        ],

        complaints: [
            {
                id: 'complaint-148',
                type: 'complaints',
                complaintId: 'Complaint #CMP-2026-0148',
                initials: 'C1',
                role: 'Complaint',
                subtitle: 'Buyer vs Seller · Under Review',
                status: 'Under Review',
                contextLabel: 'Complaint Reference',
                contextValue: '#CMP-2026-0148 · Order #ORD-2025',
                contextButton: 'View Complaint',
                preview: 'Buyer and seller statements received.',
                time: '12m',
                unread: 2,
                parties: {
                    buyer: {
                        name: 'Juan Dela Cruz',
                        initials: 'JD',
                        subtitle: 'Buyer · Complaint Party',
                        unread: 1,
                        messages: [
                            { type: 'divider', label: 'Today' },
                            {
                                type: 'system',
                                text: 'Private complaint conversation between ShopEase Admin and the buyer.'
                            },
                            {
                                sender: 'party',
                                text: 'The item I received was different from the variant I ordered.',
                                time: '9:51 AM'
                            },
                            {
                                sender: 'admin',
                                text: 'Thank you. Please send a photo of the item, packaging, and order label.',
                                time: '9:55 AM',
                                seen: true
                            },
                            {
                                sender: 'party',
                                text: 'I already uploaded the photos here.',
                                time: '10:01 AM'
                            }
                        ]
                    },
                    seller: {
                        name: 'Tech Haven',
                        initials: 'TH',
                        subtitle: 'Seller · Complaint Party',
                        unread: 1,
                        messages: [
                            { type: 'divider', label: 'Today' },
                            {
                                type: 'system',
                                text: 'Private complaint conversation between ShopEase Admin and the seller.'
                            },
                            {
                                sender: 'admin',
                                text: 'Complaint #CMP-2026-0148 was filed for Order #ORD-2025. Please provide your packing evidence.',
                                time: '10:05 AM',
                                seen: true
                            },
                            {
                                sender: 'party',
                                text: 'We have the packing photo and SKU scan. I will attach them now.',
                                time: '10:10 AM'
                            }
                        ]
                    }
                }
            },

            {
                id: 'complaint-139',
                type: 'complaints',
                complaintId: 'Complaint #CMP-2026-0139',
                initials: 'C2',
                role: 'Complaint',
                subtitle: 'Buyer vs Seller · Waiting for Seller',
                status: 'Waiting for Seller',
                contextLabel: 'Complaint Reference',
                contextValue: '#CMP-2026-0139 · Order #ORD-2018',
                contextButton: 'View Complaint',
                preview: 'Waiting for seller response.',
                time: '2h',
                unread: 1,
                parties: {
                    buyer: {
                        name: 'Maria Santos',
                        initials: 'MS',
                        subtitle: 'Buyer · Complaint Party',
                        unread: 0,
                        messages: [
                            { type: 'divider', label: 'Yesterday' },
                            {
                                sender: 'party',
                                text: 'The parcel arrived with a damaged outer box.',
                                time: '4:11 PM'
                            },
                            {
                                sender: 'admin',
                                text: 'We have recorded your statement. Please keep the packaging while the case is being reviewed.',
                                time: '4:18 PM',
                                seen: true
                            }
                        ]
                    },
                    seller: {
                        name: 'Gadget Hub',
                        initials: 'GH',
                        subtitle: 'Seller · Complaint Party',
                        unread: 1,
                        messages: [
                            { type: 'divider', label: 'Today' },
                            {
                                sender: 'admin',
                                text: 'Please provide the pre-shipment item and packaging photos for Complaint #CMP-2026-0139.',
                                time: '8:45 AM',
                                seen: true
                            }
                        ]
                    }
                }
            },

            {
                id: 'complaint-127',
                type: 'complaints',
                complaintId: 'Complaint #CMP-2026-0127',
                initials: 'C3',
                role: 'Complaint',
                subtitle: 'Buyer vs Seller · Resolved',
                status: 'Resolved',
                contextLabel: 'Complaint Reference',
                contextValue: '#CMP-2026-0127 · Order #ORD-1997',
                contextButton: 'View Complaint',
                preview: 'Case resolved and parties notified.',
                time: '1d',
                unread: 0,
                parties: {
                    buyer: {
                        name: 'Ana Garcia',
                        initials: 'AG',
                        subtitle: 'Buyer · Complaint Party',
                        unread: 0,
                        messages: [
                            { type: 'divider', label: 'Sep 30' },
                            {
                                sender: 'admin',
                                text: 'The refund has been approved and the case is now resolved.',
                                time: '3:12 PM',
                                seen: true
                            },
                            {
                                sender: 'party',
                                text: 'Thank you for the update.',
                                time: '3:16 PM'
                            }
                        ]
                    },
                    seller: {
                        name: 'Style Corner',
                        initials: 'SC',
                        subtitle: 'Seller · Complaint Party',
                        unread: 0,
                        messages: [
                            { type: 'divider', label: 'Sep 30' },
                            {
                                sender: 'admin',
                                text: 'The case has been resolved. The refund decision is now final.',
                                time: '3:13 PM',
                                seen: true
                            },
                            {
                                sender: 'party',
                                text: 'Acknowledged.',
                                time: '3:20 PM'
                            }
                        ]
                    }
                }
            }
        ]
    };


    // Complaint intake bridge from Complaints & Disputes.
    // These entries are browser-local case records, NOT confirmed backend chats.
    const COMPLAINT_INBOX_KEY = 'shopease.messages.complaints.v1';
    const PENDING_COMPLAINT_KEY = 'shopease.pendingComplaintMessage.v1';
    const SENT_COMPLAINT_KEY = 'shopease.complaintMessageSent.v1';
    const deepLink = new URLSearchParams(window.location.search);
    const requestedTab = deepLink.get('tab') || deepLink.get('category');
    const requestedComplaintId = deepLink.get('complaint') || '';
    const requestedConversationKey = deepLink.get('conversation') || '';
    const requestedRecipient = (deepLink.get('recipient') || '').toLowerCase();
    const initialComplaintsTab = requestedTab === 'complaints' || Boolean(requestedComplaintId);
    const localIntakeIds = new Set();

    function safeReadStorage(key, fallback) {
        try { return JSON.parse(localStorage.getItem(key) || 'null') ?? fallback; }
        catch (_) { return fallback; }
    }

    function normalizeRole(role) {
        return /seller/i.test(String(role || '')) ? 'seller' : 'buyer';
    }

    function formatCaseStatus(value) {
        const label = String(value || 'open').replace(/[-_]/g, ' ');
        return label.replace(/\b\w/g, c => c.toUpperCase());
    }

    function createLocalComplaintThread(records) {
        const record = records[0];
        const recordedAt = record.createdAt || record.timestamp || null;
        const complaintId = String(record.complaintId || '').trim();
        const cleanId = complaintId.replace(/^Complaint\s*#?\s*/i, '');
        const thread = {
            id: `local-complaint-${cleanId}`,
            type: 'complaints',
            localIntake: true,
            complaintId: `Complaint #${cleanId}`,
            rawComplaintId: cleanId,
            initials: 'CD',
            role: 'Complaint',
            subtitle: `Buyer vs Seller · ${formatCaseStatus(record.status)}`,
            status: formatCaseStatus(record.status),
            contextLabel: 'Complaint / Order Reference',
            contextValue: `${cleanId}${record.orderId ? ` · Order #${record.orderId}` : ''}`,
            contextButton: 'View Complaint',
            contextUrl: record.complaintDatabaseId ? `/admin/complaints-disputes?complaint=${encodeURIComponent(cleanId)}` : '/admin/complaints-disputes',
            preview: `${record.complaintType || 'Complaint'}${record.description ? ` · ${record.description}` : ''}`,
            time: 'New',
            unread: 0,
            parties: {}
        };
        for (const role of ['buyer', 'seller']) {
            const match = records.find(entry => normalizeRole(entry.recipientRole) === role);
            const name = match?.recipientName || (role === 'buyer' ? record.party1 : record.party2) || (role === 'buyer' ? 'Buyer' : 'Seller');
            thread.parties[role] = {
                name,
                email: match?.recipientEmail || '',
                initials: name.trim().split(/\s+/).slice(0, 2).map(s => s[0]).join('').toUpperCase(),
                subtitle: `${role === 'buyer' ? 'Buyer' : 'Seller'} · Complaint Party`,
                unread: 0,
                localIntake: true,
                messages: [{
                    type: 'system',
                    ...(recordedAt ? { timestamp: recordedAt } : {}),
                    text: match
                        ? `Complaint ${cleanId} is recorded for ${name}. This is a case record, not yet a sent message.`
                        : `No conversation with the ${role} has been started yet.`
                }]
            };
        }
        return thread;
    }

    function mergeLocalComplaintIntake() {
        const records = safeReadStorage(COMPLAINT_INBOX_KEY, []);
        if (!Array.isArray(records)) return;
        const grouped = new Map();
        records.filter(r => r && r.complaintId).forEach(record => {
            const id = String(record.complaintId).trim().replace(/^Complaint\s*#?\s*/i, '');
            if (!grouped.has(id)) grouped.set(id, []);
            grouped.get(id).push(record);
        });
        // Refresh local-only entries without duplicating actual API conversations.
        conversations.complaints = conversations.complaints.filter(t => !t.localIntake);
        localIntakeIds.clear();
        grouped.forEach((entries, id) => {
            const matchedServer = conversations.complaints.some(t =>
                String(t.complaintId || '').includes(id) ||
                String(t.rawComplaintId || '') === id
            );
            if (!matchedServer) {
                const thread = createLocalComplaintThread(entries);
                conversations.complaints.unshift(thread);
                localIntakeIds.add(thread.id);
            }
        });
    }

    function selectComplaintFromUrl() {
        if (!initialComplaintsTab) return null;
        const matching = conversations.complaints.find(thread => {
            const raw = String(thread.rawComplaintId || thread.complaintId || '')
                .replace(/^Complaint\s*#?\s*/i, '');
            return requestedComplaintId && raw === requestedComplaintId;
        });
        const pending = safeReadStorage(PENDING_COMPLAINT_KEY, {});
        const chosen = matching || conversations.complaints[0] || null;
        if (chosen) {
            const roleFromRecord = safeReadStorage(COMPLAINT_INBOX_KEY, []).find(r =>
                r.complaintId === requestedComplaintId &&
                (r.key === requestedConversationKey || String(r.recipientEmail || '').toLowerCase() === requestedRecipient)
            );
            const role = roleFromRecord?.recipientRole || (
                pending.complaintId === requestedComplaintId ? pending.recipientRole : ''
            );
            activeComplaintParty = normalizeRole(role);
            updateComplaintPartyTabs();
        }
        return chosen;
    }

    let activeTab = initialComplaintsTab ? 'complaints' : 'logistics';
    let activeThreadId = null;
    let activeComplaintParty = 'buyer';
    const apiBase = '/api/v1/admin/messages';
    const attachmentInput = document.getElementById('adminMessageAttachment');
    const newConversationDialog = document.getElementById('adminNewConversationDialog');
    const newConversationContact = document.getElementById('adminNewConversationContact');

    async function request(url, options = {}) {
        const headers = new Headers(options.headers || {});
        headers.set('Accept', 'application/json');
        headers.set('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]')?.content || '');
        const response = await fetch(url, {
            ...options,
            headers,
            credentials: 'same-origin'
        });
        const payload = await response.json().catch(() => ({}));
        if (!response.ok) {
            throw new Error(payload.message || Object.values(payload.errors || {}).flat()[0] || 'The request failed.');
        }
        return payload;
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }


    function allThreads() {
        return [
            ...conversations.logistics,
            ...conversations.buyers,
            ...conversations.sellers,
            ...conversations.complaints
        ];
    }


    function getThread(id) {
        return allThreads().find(
            thread => thread.id === id
        );
    }


    function currentTabThreads() {
        return conversations[activeTab] || [];
    }


    function renderThreadList() {
        if (!threadList) {
            return;
        }

        const query =
            (searchInput?.value || '')
                .trim()
                .toLowerCase();

        const filtered =
            currentTabThreads().filter(thread => {
                const title =
                    thread.type === 'complaints'
                        ? thread.complaintId
                        : thread.title;

                const haystack =
                    [
                        title,
                        thread.subtitle,
                        thread.preview,
                        thread.contextValue,
                        thread.status || ''
                    ]
                        .join(' ')
                        .toLowerCase();

                return !query || haystack.includes(query);
            });

        threadList.innerHTML =
            filtered.map(thread => {
                const complaint =
                    thread.type === 'complaints';

                const title =
                    complaint
                        ? thread.complaintId
                        : thread.title;

                return `
                    <button
                        type="button"
                        class="
                            admin-message-thread
                            ${thread.id === activeThreadId ? 'is-active' : ''}
                            ${thread.unread ? 'is-unread' : ''}
                        "
                        data-admin-thread-id="${escapeHtml(thread.id)}"
                        data-type="${escapeHtml(thread.type)}"
                    >
                        <span class="admin-thread-avatar">
                            ${escapeHtml(thread.initials)}
                        </span>

                        <span class="admin-thread-copy">
                            <span class="admin-thread-title-row">
                                <strong class="admin-thread-title">
                                    ${escapeHtml(title)}
                                </strong>

                                <span class="admin-thread-badge">
                                    ${complaint ? escapeHtml(thread.status) : escapeHtml(thread.role)}
                                </span>
                            </span>

                            <span class="admin-thread-preview">
                                ${escapeHtml(thread.preview)}
                            </span>

                            ${
                                complaint
                                    ? `
                                        <span class="admin-thread-secondary">
                                            <span class="admin-party-mini buyer">
                                                Buyer
                                            </span>
                                            <span>·</span>
                                            <span class="admin-party-mini seller">
                                                Seller
                                            </span>
                                        </span>
                                    `
                                    : ''
                            }
                        </span>

                        <span class="admin-thread-meta">
                            <time class="admin-thread-time">
                                ${escapeHtml(thread.time)}
                            </time>

                            ${
                                thread.unread
                                    ? `<span class="admin-thread-unread">${thread.unread}</span>`
                                    : ''
                            }
                        </span>
                    </button>
                `;
            }).join('');

        const hasResults =
            filtered.length > 0;

        threadList.hidden =
            !hasResults;

        if (emptyThreadList) {
            emptyThreadList.hidden =
                hasResults;
        }
    }


    function getActiveMessages(thread) {
        if (!thread) {
            return [];
        }

        if (thread.type === 'complaints') {
            return (
                thread.parties?.[activeComplaintParty]?.messages
                || []
            );
        }

        return thread.messages || [];
    }


    // Render a date marker at the beginning of a conversation and whenever
    // the calendar day changes. API timestamps take priority over demo labels.
    function messageCalendarDate(message) {
        const raw = message.created_at ?? message.createdAt ??
            message.sent_at ?? message.sentAt ?? message.timestamp ??
            message.datetime ?? message.date ?? message.sent_at_iso ??
            message.created_at_iso ?? message.createdAtIso ??
            message.meta?.created_at ?? message.meta?.timestamp;
        if (!raw) return null;
        const parsed = raw instanceof Date ? raw : new Date(raw);
        return Number.isNaN(parsed.getTime()) ? null : parsed;
    }

    function localDayKey(date) {
        return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
    }

    function dateSeparatorLabel(date) {
        const today = new Date();
        const todayStart = new Date(today.getFullYear(), today.getMonth(), today.getDate());
        const targetStart = new Date(date.getFullYear(), date.getMonth(), date.getDate());
        const dayDifference = Math.round((todayStart - targetStart) / 86400000);
        if (dayDifference === 0) return 'Today';
        if (dayDifference === 1) return 'Yesterday';
        return new Intl.DateTimeFormat('en-US', {
            month: 'long', day: 'numeric', year: 'numeric'
        }).format(date);
    }

    function renderMessageBody(thread) {
        if (!chatBody || !thread) return;

        const messages = getActiveMessages(thread);
        let lastDayKey = null;
        let pendingLegacyDivider = null;
        const parts = [];
        const appendDate = (label) => {
            parts.push(`<div class="admin-message-date" role="separator" aria-label="${escapeHtml(label)}"><span>${escapeHtml(label)}</span></div>`);
        };

        messages.forEach(message => {
            // Existing demo conversations already have manually authored date
            // labels. Keep these when the following messages lack real dates.
            if (message.type === 'divider') {
                pendingLegacyDivider = String(message.label || 'Today');
                return;
            }

            const date = messageCalendarDate(message);
            if (date) {
                const key = localDayKey(date);
                if (key !== lastDayKey) {
                    appendDate(dateSeparatorLabel(date));
                    lastDayKey = key;
                }
                pendingLegacyDivider = null;
            } else if (pendingLegacyDivider) {
                const legacyKey = `legacy:${pendingLegacyDivider}`;
                if (legacyKey !== lastDayKey) {
                    appendDate(pendingLegacyDivider);
                    lastDayKey = legacyKey;
                }
                pendingLegacyDivider = null;
            } else if (lastDayKey !== 'undated') {
                // No calendar date exists for this historical message. Show a
                // neutral divider instead of inventing Today or hiding all dates.
                appendDate('Earlier messages');
                lastDayKey = 'undated';
            }

            if (message.type === 'system') {
                parts.push(`<div class="admin-system-note">${escapeHtml(message.text)}</div>`);
                return;
            }

            const isAdmin = message.sender === 'admin';
            const attachment = message.attachment
                ? `<a href="${escapeHtml(message.attachment.url)}" target="_blank" rel="noopener">${escapeHtml(message.attachment.name || 'Download attachment')}</a>`
                : '';
            const time = message.time || (date
                ? new Intl.DateTimeFormat('en-US', {hour:'numeric',minute:'2-digit'}).format(date)
                : '');
            parts.push(`
                <div class="admin-chat-message-row ${isAdmin ? 'is-admin' : ''}">
                    <div class="admin-chat-bubble-wrap">
                        <div class="admin-chat-bubble">${escapeHtml(message.text ?? '')}${attachment ? `<div class="admin-message-attachment">${attachment}</div>` : ''}</div>
                        <div class="admin-message-meta">${escapeHtml(time)}${isAdmin && message.seen ? ' · Seen' : ''}</div>
                    </div>
                </div>
            `);
        });

        chatBody.innerHTML = parts.join('');
        chatBody.scrollTop = chatBody.scrollHeight;
    }


    function renderHeader(thread) {
        if (!thread) {
            return;
        }

        const complaint =
            thread.type === 'complaints';

        // Complaint-only actions replace Search and More in the chat header.
        if (headerComplaintActions) headerComplaintActions.hidden = !complaint;
        if (complaint && headerComplaintStatus) {
            const label = String(thread.status || 'Open');
            headerComplaintStatus.textContent = label;
            headerComplaintStatus.dataset.status = label.toLowerCase().replace(/\s+/g, '-');
        }

        chatPanel?.classList.toggle(
            'is-logistics',
            thread.type === 'logistics'
        );

        chatPanel?.classList.toggle(
            'is-complaint',
            complaint
        );

        chatPanel?.classList.toggle(
            'is-seller',
            thread.type === 'sellers'
        );

        complaintPartySwitchWrap.hidden =
            !complaint;

        if (complaint) {
            const party =
                thread.parties?.[activeComplaintParty];

            if (activeAvatar) {
                activeAvatar.innerHTML =
                    `<span>${escapeHtml(party?.initials || thread.initials)}</span>`;
            }

            if (activeTitle) {
                activeTitle.textContent =
                    thread.complaintId;
            }

            if (activeRoleBadge) {
                activeRoleBadge.textContent =
                    activeComplaintParty === 'buyer'
                        ? 'Buyer Conversation'
                        : 'Seller Conversation';
            }

            if (activeSubtitle) {
                activeSubtitle.textContent =
                    party
                        ? `${party.name} · ${party.subtitle}`
                        : thread.subtitle;
            }

            if (complaintCaseReference) {
                complaintCaseReference.textContent =
                    thread.complaintId;
            }

            if (complaintCaseStatus) {
                complaintCaseStatus.textContent =
                    thread.status;
            }

            if (complaintBuyerUnread) {
                const unread =
                    thread.parties?.buyer?.unread || 0;

                complaintBuyerUnread.textContent =
                    unread;

                complaintBuyerUnread.hidden =
                    unread === 0;
            }

            if (complaintSellerUnread) {
                const unread =
                    thread.parties?.seller?.unread || 0;

                complaintSellerUnread.textContent =
                    unread;

                complaintSellerUnread.hidden =
                    unread === 0;
            }

            if (composerInput) {
                composerInput.placeholder =
                    activeComplaintParty === 'buyer'
                        ? 'Reply to buyer...'
                        : 'Reply to seller...';
            }
        } else {
            if (activeAvatar) {
                activeAvatar.innerHTML =
                    `<span>${escapeHtml(thread.initials)}</span>`;
            }

            if (activeTitle) {
                activeTitle.textContent =
                    thread.title;
            }

            if (activeRoleBadge) {
                activeRoleBadge.textContent =
                    thread.role;
            }

            if (activeSubtitle) {
                activeSubtitle.textContent =
                    thread.subtitle;
            }

            if (composerInput) {
                composerInput.placeholder =
                    'Type a message...';
            }
        }

        if (contextLabel) {
            contextLabel.textContent =
                thread.contextLabel;
        }

        if (contextValue) {
            contextValue.textContent =
                thread.contextValue;
        }

        if (viewContextButton) {
            viewContextButton.textContent =
                thread.contextButton;
        }
    }


    function renderThread(thread) {
        if (!thread) {
            return;
        }

        activeThreadId =
            thread.id;

        thread.unread = 0;

        if (thread.type === 'complaints') {
            const party =
                thread.parties?.[activeComplaintParty];

            if (party) {
                party.unread = 0;
            }
        }

        renderHeader(thread);
        renderMessageBody(thread);
        renderThreadList();
        updateCounts();
    }


    function switchMainTab(tabName) {
        if (!conversations[tabName]) {
            return;
        }

        activeTab =
            tabName;

        tabs.forEach(tab => {
            const active =
                tab.dataset.adminMessageTab === activeTab;

            tab.classList.toggle(
                'is-active',
                active
            );

            tab.setAttribute(
                'aria-selected',
                active ? 'true' : 'false'
            );
        });

        if (activeTab === 'complaints') {
            activeComplaintParty =
                'buyer';

            updateComplaintPartyTabs();
        }

        loadConversations();
    }


    function updateComplaintPartyTabs() {
        partyTabs.forEach(tab => {
            const active =
                tab.dataset.complaintParty ===
                activeComplaintParty;

            tab.classList.toggle(
                'is-active',
                active
            );

            tab.setAttribute(
                'aria-selected',
                active ? 'true' : 'false'
            );
        });
    }


    function switchComplaintParty(partyName) {
        if (
            partyName !== 'buyer'
            &&
            partyName !== 'seller'
        ) {
            return;
        }

        activeComplaintParty =
            partyName;

        updateComplaintPartyTabs();

        const thread =
            getThread(activeThreadId);

        if (
            thread?.type !== 'complaints'
        ) {
            return;
        }

        const party =
            thread.parties?.[activeComplaintParty];

        if (party) {
            party.unread = 0;
        }

        renderHeader(thread);
        renderMessageBody(thread);
        renderThreadList();
        updateCounts();
        const conversationId = thread.parties?.[activeComplaintParty]?.conversationId;
        if (conversationId) {
            request(`${apiBase}/conversations/${conversationId}`)
                .then(() => loadConversations(thread.id))
                .catch(error => alert(error.message));
        }
    }


    function updateCounts() {
        const logisticsUnread =
            conversations.logistics.reduce(
                (sum, item) =>
                    sum + Number(item.unread || 0),
                0
            );

        const buyersUnread =
            conversations.buyers.reduce(
                (sum, item) =>
                    sum + Number(item.unread || 0),
                0
            );

        const sellersUnread =
            conversations.sellers.reduce(
                (sum, item) =>
                    sum + Number(item.unread || 0),
                0
            );

        const complaintsUnread =
            conversations.complaints.reduce(
                (sum, item) =>
                    sum
                    + Number(item.parties?.buyer?.unread || 0)
                    + Number(item.parties?.seller?.unread || 0),
                0
            );

        const values = {
            logisticsTabCount:
                logisticsUnread || conversations.logistics.length,

            complaintsTabCount:
                complaintsUnread || conversations.complaints.length,

            buyersTabCount:
                buyersUnread || conversations.buyers.length,

            sellersTabCount:
                sellersUnread || conversations.sellers.length
        };

        Object.entries(values).forEach(
            ([id, value]) => {
                const element =
                    document.getElementById(id);

                if (element) {
                    element.textContent =
                        value;
                }
            }
        );
    }


    async function sendMessage() {
        const text =
            (composerInput?.value || '')
                .trim();
        const attachment = attachmentInput?.files?.[0];
        if (!text && !attachment) {
            composerInput?.focus();
            return;
        }

        const thread =
            getThread(activeThreadId);

        if (!thread) {
            return;
        }

        const conversationId = thread.type === 'complaints'
            ? thread.parties?.[activeComplaintParty]?.conversationId
            : thread.id;
        if (!conversationId) {
            alert('Complaint recorded under Messages → Complaints. To send a real message, the complaint party needs a server-side conversation first.');
            return;
        }

        const formData = new FormData();
        formData.set('body', text);
        if (attachment) {
            formData.set('attachment', attachment);
        }
        try {
            const { data } = await request(
                `${apiBase}/conversations/${conversationId}/messages`,
                { method: 'POST', body: formData }
            );
            if (thread.type === 'complaints') {
                thread.parties[activeComplaintParty].messages.push(data);
                thread.parties[activeComplaintParty].unread = 0;
            } else {
                thread.messages.push(data);
            }
            thread.preview = text || attachment.name;
            thread.time = 'Now';
            if (thread.type === 'complaints') {
                const complaintId = String(thread.rawComplaintId || thread.complaintId || '').replace(/^Complaint\s*#?\s*/i, '');
                const recipientName = thread.parties?.[activeComplaintParty]?.name || activeComplaintParty;
                try {
                    localStorage.setItem(SENT_COMPLAINT_KEY, JSON.stringify({
                        complaintId, recipientName, sentAt: new Date().toISOString(),
                        id: `complaint-message-${data?.id || Date.now()}`
                    }));
                } catch (_) { /* Sending already succeeded; bridge is optional. */ }
            }
            composerInput.value = '';
            composerInput.style.height = 'auto';
            if (attachmentInput) attachmentInput.value = '';
            renderThread(thread);
        } catch (error) {
            alert(error.message);
        }
    }

    async function loadConversations(preferredId = null) {
        try {
            const { data } = await request(apiBase);
            Object.values(conversations).forEach(items => items.splice(0, items.length));
            data.forEach(thread => conversations[thread.type]?.push(thread));
            mergeLocalComplaintIntake();
            const activeItems = conversations[activeTab];
            const preferred = allThreads().find(thread => thread.id === preferredId);
            const urlMatch = selectComplaintFromUrl();
            const next = preferred || urlMatch || activeItems[0] || allThreads()[0] || null;
            if (next) {
                if (next.type !== activeTab) {
                    activeTab = next.type;
                    tabs.forEach(tab => {
                        const active = tab.dataset.adminMessageTab === activeTab;
                        tab.classList.toggle('is-active', active);
                        tab.setAttribute('aria-selected', active ? 'true' : 'false');
                    });
                }
                if (next.type === 'complaints') {
                    if (!initialComplaintsTab || !urlMatch) {
                        activeComplaintParty = next.parties?.buyer ? 'buyer' : 'seller';
                    }
                    updateComplaintPartyTabs();
                }
                renderThread(next);
            } else {
                activeThreadId = null;
                renderThreadList();
                updateCounts();
                if (chatBody) chatBody.textContent = 'No conversations yet. Start a conversation to contact a user.';
            }
        } catch (error) {
            // Network/API unavailable: only local complaint intake can be displayed.
            Object.values(conversations).forEach(items => items.splice(0, items.length));
            mergeLocalComplaintIntake();
            const fallback = selectComplaintFromUrl() || conversations.complaints[0];
            if (fallback) {
                activeTab = 'complaints';
                tabs.forEach(tab => {
                    const active = tab.dataset.adminMessageTab === 'complaints';
                    tab.classList.toggle('is-active', active);
                    tab.setAttribute('aria-selected', active ? 'true' : 'false');
                });
                renderThread(fallback);
            } else if (chatBody) {
                chatBody.textContent = `Unable to load messages: ${error.message}`;
                renderThreadList();
                updateCounts();
            }
        }
    }

    async function openNewConversation() {
        try {
            const { data } = await request(`${apiBase}/contacts?type=${encodeURIComponent(activeTab)}`);
            newConversationContact.replaceChildren();
            data.forEach(contact => {
                const option = document.createElement('option');
                option.value = JSON.stringify(contact);
                option.textContent = contact.label;
                newConversationContact.append(option);
            });
            if (data.length === 0) {
                alert('There are no available contacts in this category.');
                return;
            }
            newConversationDialog.showModal();
        } catch (error) {
            alert(error.message);
        }
    }


    tabs.forEach(tab => {
        tab.addEventListener(
            'click',
            () => {
                switchMainTab(
                    tab.dataset.adminMessageTab
                );
            }
        );
    });


    partyTabs.forEach(tab => {
        tab.addEventListener(
            'click',
            () => {
                switchComplaintParty(
                    tab.dataset.complaintParty
                );
            }
        );
    });


    threadList?.addEventListener(
        'click',
        event => {
            const item =
                event.target.closest(
                    '[data-admin-thread-id]'
                );

            if (!item) {
                return;
            }

            const thread =
                getThread(
                    item.dataset.adminThreadId
                );

            if (thread?.type === 'complaints') {
                activeComplaintParty = 'buyer';
                updateComplaintPartyTabs();
            }

            if (thread) {
                renderThread(thread);
                const conversationId = thread.type === 'complaints'
                    ? thread.parties?.[activeComplaintParty]?.conversationId
                    : thread.id;
                if (conversationId) {
                    request(`${apiBase}/conversations/${conversationId}`)
                        .then(() => loadConversations(thread.id))
                        .catch(error => alert(error.message));
                }
            }
        }
    );


    searchInput?.addEventListener(
        'input',
        renderThreadList
    );


    sendButton?.addEventListener(
        'click',
        sendMessage
    );


    composerInput?.addEventListener(
        'keydown',
        event => {
            if (
                event.key === 'Enter'
                &&
                !event.shiftKey
            ) {
                event.preventDefault();
                sendMessage();
            }
        }
    );


    composerInput?.addEventListener(
        'input',
        () => {
            composerInput.style.height =
                'auto';

            composerInput.style.height =
                `${Math.min(composerInput.scrollHeight, 90)}px`;
        }
    );




    document.getElementById('adminAttachButton')?.addEventListener('click', () => attachmentInput?.click());

    document.getElementById('adminNewConversationButton')?.addEventListener('click', openNewConversation);
    document.getElementById('adminCancelNewConversation')?.addEventListener('click', () => newConversationDialog.close());
    document.getElementById('adminNewConversationForm')?.addEventListener('submit', async event => {
        event.preventDefault();
        const contact = JSON.parse(newConversationContact.value);
        try {
            const { data: created } = await request(`${apiBase}/conversations`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    type: activeTab,
                    user_id: contact.user_id,
                    complaint_id: contact.complaint_id,
                    complaint_party: contact.complaint_party
                })
            });
            newConversationDialog.close();
            const threadId = activeTab === 'complaints'
                ? `complaint-${contact.complaint_id}`
                : created.id;
            await loadConversations(threadId);
        } catch (error) {
            alert(error.message);
        }
    });


    headerViewComplaint?.addEventListener('click', () => {
        const thread = getThread(activeThreadId);
        if (!thread || thread.type !== 'complaints') return;
        // Reuse the existing complaint-details destination when available.
        const rawId = String(thread.rawComplaintId || thread.complaintId || '')
            .replace(/^Complaint\s*#?\s*/i, '').trim();
        const destination = thread.contextUrl || '/admin/complaints-disputes';
        const url = new URL(destination, window.location.origin);
        if (rawId && !url.searchParams.has('complaint')) {
            url.searchParams.set('complaint', rawId);
        }
        window.location.assign(url.pathname + url.search);
    });

    viewContextButton?.addEventListener(
        'click',
        () => {
            const thread =
                getThread(activeThreadId);

            if (!thread) {
                return;
            }

            if (thread.contextUrl) {
                window.location.assign(thread.contextUrl);
            }
        }
    );


    tabs.forEach(tab => {
        const selected = tab.dataset.adminMessageTab === activeTab;
        tab.classList.toggle('is-active', selected);
        tab.setAttribute('aria-selected', selected ? 'true' : 'false');
    });
    updateCounts();
    loadConversations();
});