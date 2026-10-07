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


    let activeTab = 'logistics';
    let activeThreadId = null;
    let activeComplaintParty = 'buyer';
    const apiBase = '/admin/messages/api';
    const attachmentInput = document.getElementById('adminMessageAttachment');
    const photoInput = document.getElementById('adminMessagePhoto');
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


    function renderMessageBody(thread) {
        if (!chatBody || !thread) {
            return;
        }

        const messages =
            getActiveMessages(thread);

        chatBody.innerHTML =
            messages.map(message => {
                if (message.type === 'divider') {
                    return `
                        <div class="admin-message-date">
                            <span>${escapeHtml(message.label)}</span>
                        </div>
                    `;
                }

                if (message.type === 'system') {
                    return `
                        <div class="admin-system-note">
                            ${escapeHtml(message.text)}
                        </div>
                    `;
                }

                const isAdmin =
                    message.sender === 'admin';
                const attachment = message.attachment
                    ? `<a href="${escapeHtml(message.attachment.url)}" target="_blank" rel="noopener">${escapeHtml(message.attachment.name || 'Download attachment')}</a>`
                    : '';

                return `
                    <div class="admin-chat-message-row ${isAdmin ? 'is-admin' : ''}">
                        <div class="admin-chat-bubble-wrap">
                            <div class="admin-chat-bubble">
                                ${escapeHtml(message.text)}
                                ${attachment ? `<div class="admin-message-attachment">${attachment}</div>` : ''}
                            </div>

                            <div class="admin-message-meta">
                                ${escapeHtml(message.time)}
                                ${
                                    isAdmin && message.seen
                                        ? ' · Seen'
                                        : ''
                                }
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

        chatBody.scrollTop =
            chatBody.scrollHeight;
    }


    function renderHeader(thread) {
        if (!thread) {
            return;
        }

        const complaint =
            thread.type === 'complaints';

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
        const attachment = attachmentInput?.files?.[0] || photoInput?.files?.[0];
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
            alert('This complaint party does not have a conversation yet.');
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
            composerInput.value = '';
            composerInput.style.height = 'auto';
            if (attachmentInput) attachmentInput.value = '';
            if (photoInput) photoInput.value = '';
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
            const activeItems = conversations[activeTab];
            const preferred = allThreads().find(thread => thread.id === preferredId);
            const next = preferred || activeItems[0] || allThreads()[0] || null;
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
                    activeComplaintParty = next.parties?.buyer ? 'buyer' : 'seller';
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
            if (chatBody) chatBody.textContent = `Unable to load messages: ${error.message}`;
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
                activeComplaintParty =
                    'buyer';

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


    document
        .getElementById('adminChatSearchButton')
        ?.addEventListener(
            'click',
            () => {
                searchInput?.focus();
            }
        );


    document.getElementById('adminAttachButton')?.addEventListener('click', () => attachmentInput?.click());

    document.getElementById('adminPhotoButton')?.addEventListener('click', () => photoInput?.click());
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


    updateCounts();
    loadConversations();
});