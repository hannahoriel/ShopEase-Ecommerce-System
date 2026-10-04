document.addEventListener('DOMContentLoaded', () => {
    const tabs =
        Array.from(
            document.querySelectorAll('[data-message-tab]')
        );

    const threadList =
        document.getElementById('messagesThreadList');

    const emptyThreads =
        document.getElementById('messagesEmptyThreads');

    const searchInput =
        document.getElementById('messagesSearch');

    const chatPanel =
        document.querySelector('.messages-chat-panel');

    const chatBody =
        document.getElementById('messagesChatBody');

    const activeAvatar =
        document.getElementById('activeConversationAvatar');

    const activeTitle =
        document.getElementById('activeConversationTitle');

    const activeBadge =
        document.getElementById('activeConversationBadge');

    const activeSubtitle =
        document.getElementById('activeConversationSubtitle');

    const activeContextLabel =
        document.getElementById('activeContextLabel');

    const activeContextValue =
        document.getElementById('activeContextValue');

    const viewContextButton =
        document.getElementById('viewContextButton');

    const composerInput =
        document.getElementById('messageComposerInput');

    const sendButton =
        document.getElementById('sendMessageButton');

    const buyerTabCount =
        document.getElementById('buyerTabCount');

    const complaintTabCount =
        document.getElementById('complaintTabCount');


    const conversations = {
        buyers: [
            {
                id: 'buyer-1',
                type: 'buyers',
                title: 'Juan Dela Cruz',
                initials: 'JD',
                badge: 'Buyer',
                subtitle: 'Order #ORD-2028 · Online',
                contextLabel: 'Related Order',
                contextValue: '#ORD-2028 · Wireless Headphones',
                contextButton: 'View Order',
                preview: 'Is this still available in black?',
                time: '2m',
                unread: 2,
                messages: [
                    {
                        type: 'divider',
                        label: 'Today'
                    },
                    {
                        sender: 'buyer',
                        text: 'Hi! Is this still available in black?',
                        time: '10:22 AM'
                    },
                    {
                        sender: 'seller',
                        text: 'Hi Juan! Yes, the black variant is still available.',
                        time: '10:24 AM',
                        seen: true
                    },
                    {
                        sender: 'buyer',
                        text: 'Great. If I order today, when can you ship it?',
                        time: '10:25 AM'
                    },
                    {
                        sender: 'seller',
                        text: 'We can prepare it today and hand it over to the courier within 24 hours.',
                        time: '10:27 AM',
                        seen: true
                    },
                    {
                        sender: 'buyer',
                        text: 'Perfect, thank you!',
                        time: '10:29 AM'
                    }
                ]
            },
            {
                id: 'buyer-2',
                type: 'buyers',
                title: 'Maria Santos',
                initials: 'MS',
                badge: 'Buyer',
                subtitle: 'Order #ORD-2026 · Active 8m ago',
                contextLabel: 'Related Order',
                contextValue: '#ORD-2026 · Smart Watch Series 8',
                contextButton: 'View Order',
                preview: 'Thank you for the update!',
                time: '18m',
                unread: 0,
                messages: [
                    {
                        type: 'divider',
                        label: 'Today'
                    },
                    {
                        sender: 'seller',
                        text: 'Hi Maria, your order has already been packed and is ready for pickup.',
                        time: '9:41 AM',
                        seen: true
                    },
                    {
                        sender: 'buyer',
                        text: 'Thank you for the update!',
                        time: '9:45 AM'
                    }
                ]
            },
            {
                id: 'buyer-3',
                type: 'buyers',
                title: 'Carlo Reyes',
                initials: 'CR',
                badge: 'Buyer',
                subtitle: 'Order #ORD-2027 · Active 1h ago',
                contextLabel: 'Related Order',
                contextValue: '#ORD-2027 · Canvas Shoulder Bag',
                contextButton: 'View Order',
                preview: 'Can I change the delivery address?',
                time: '1h',
                unread: 1,
                messages: [
                    {
                        type: 'divider',
                        label: 'Today'
                    },
                    {
                        sender: 'buyer',
                        text: 'Hello, can I still change the delivery address for my order?',
                        time: '8:13 AM'
                    },
                    {
                        sender: 'seller',
                        text: 'Hi Carlo. If the parcel has not been handed to the courier yet, we can check what options are available.',
                        time: '8:17 AM',
                        seen: true
                    }
                ]
            }
        ],

        complaints: [
            {
                id: 'complaint-1',
                type: 'complaints',
                title: 'Complaint #CMP-2026-0148',
                initials: 'AD',
                badge: 'Admin',
                subtitle: 'Admin Support · Case in review',
                contextLabel: 'Complaint Reference',
                contextValue: '#CMP-2026-0148 · Order #ORD-2025',
                contextButton: 'View Complaint',
                preview: 'Admin requested additional proof.',
                time: '12m',
                unread: 1,
                messages: [
                    {
                        type: 'divider',
                        label: 'Today'
                    },
                    {
                        type: 'system',
                        text: 'This complaint conversation is between your seller account and ShopEase Admin only.'
                    },
                    {
                        sender: 'admin',
                        text: 'Hello Seller. We are reviewing Complaint #CMP-2026-0148 regarding Order #ORD-2025.',
                        time: '10:02 AM'
                    },
                    {
                        sender: 'admin',
                        text: 'Please provide a clear photo of the package before shipment and any courier handover proof available.',
                        time: '10:04 AM'
                    },
                    {
                        sender: 'seller',
                        text: 'Understood. I will send the requested proof here shortly.',
                        time: '10:08 AM',
                        seen: true
                    }
                ]
            },
            {
                id: 'complaint-2',
                type: 'complaints',
                title: 'Complaint #CMP-2026-0139',
                initials: 'AD',
                badge: 'Admin',
                subtitle: 'Admin Support · Waiting for seller response',
                contextLabel: 'Complaint Reference',
                contextValue: '#CMP-2026-0139 · Order #ORD-2018',
                contextButton: 'View Complaint',
                preview: 'Please confirm the item condition.',
                time: '2h',
                unread: 0,
                messages: [
                    {
                        type: 'divider',
                        label: 'Yesterday'
                    },
                    {
                        type: 'system',
                        text: 'Only ShopEase Admin can contact you inside complaint threads.'
                    },
                    {
                        sender: 'admin',
                        text: 'Please confirm whether the item was sealed and complete before courier pickup.',
                        time: '4:26 PM'
                    },
                    {
                        sender: 'seller',
                        text: 'Yes. The item was sealed, complete, and documented before pickup.',
                        time: '4:39 PM',
                        seen: true
                    }
                ]
            }
        ]
    };


    let activeTab = 'buyers';
    let activeConversationId = 'buyer-1';


    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }


    function getActiveConversations() {
        return conversations[activeTab] || [];
    }


    function getConversationById(id) {
        return [
            ...conversations.buyers,
            ...conversations.complaints
        ].find(conversation => conversation.id === id);
    }


    function renderThreads() {
        if (!threadList) {
            return;
        }

        const query =
            (searchInput?.value || '')
                .trim()
                .toLowerCase();

        const filtered =
            getActiveConversations().filter(conversation => {
                const haystack =
                    [
                        conversation.title,
                        conversation.preview,
                        conversation.subtitle,
                        conversation.contextValue
                    ]
                        .join(' ')
                        .toLowerCase();

                return !query || haystack.includes(query);
            });

        threadList.innerHTML =
            filtered.map(conversation => `
                <button
                    type="button"
                    class="
                        messages-thread
                        ${conversation.id === activeConversationId ? 'is-active' : ''}
                        ${conversation.unread ? 'is-unread' : ''}
                    "
                    data-conversation-id="${escapeHtml(conversation.id)}"
                    data-type="${escapeHtml(conversation.type)}"
                >
                    <span class="messages-thread-avatar">
                        ${escapeHtml(conversation.initials)}
                    </span>

                    <span class="messages-thread-copy">
                        <span class="messages-thread-topline">
                            <strong class="messages-thread-title">
                                ${escapeHtml(conversation.title)}
                            </strong>

                            <span class="messages-thread-type">
                                ${conversation.type === 'complaints' ? 'Admin' : 'Buyer'}
                            </span>
                        </span>

                        <span class="messages-thread-preview">
                            ${escapeHtml(conversation.preview)}
                        </span>
                    </span>

                    <span class="messages-thread-meta">
                        <time class="messages-thread-time">
                            ${escapeHtml(conversation.time)}
                        </time>

                        ${
                            conversation.unread
                                ? `<span class="messages-thread-unread">${conversation.unread}</span>`
                                : ''
                        }
                    </span>
                </button>
            `).join('');

        const hasResults = filtered.length > 0;

        threadList.hidden = !hasResults;

        if (emptyThreads) {
            emptyThreads.hidden = hasResults;
        }
    }


    function renderConversation(conversation) {
        if (!conversation || !chatBody) {
            return;
        }

        activeConversationId =
            conversation.id;

        conversation.unread = 0;

        chatPanel?.classList.toggle(
            'is-complaint',
            conversation.type === 'complaints'
        );

        if (activeAvatar) {
            activeAvatar.innerHTML =
                `<span>${escapeHtml(conversation.initials)}</span>`;
        }

        if (activeTitle) {
            activeTitle.textContent =
                conversation.title;
        }

        if (activeBadge) {
            activeBadge.textContent =
                conversation.badge;
        }

        if (activeSubtitle) {
            activeSubtitle.textContent =
                conversation.subtitle;
        }

        if (activeContextLabel) {
            activeContextLabel.textContent =
                conversation.contextLabel;
        }

        if (activeContextValue) {
            activeContextValue.textContent =
                conversation.contextValue;
        }

        if (viewContextButton) {
            viewContextButton.textContent =
                conversation.contextButton;
        }

        if (composerInput) {
            composerInput.placeholder =
                conversation.type === 'complaints'
                    ? 'Reply to ShopEase Admin...'
                    : 'Type a message...';
        }

        chatBody.innerHTML =
            conversation.messages.map(message => {
                if (message.type === 'divider') {
                    return `
                        <div class="messages-date-divider">
                            <span>${escapeHtml(message.label)}</span>
                        </div>
                    `;
                }

                if (message.type === 'system') {
                    return `
                        <div class="message-system-note">
                            ${escapeHtml(message.text)}
                        </div>
                    `;
                }

                const seller =
                    message.sender === 'seller';

                return `
                    <div class="message-row ${seller ? 'is-seller' : ''}">
                        <div class="message-bubble-wrap">
                            <div class="message-bubble">
                                ${escapeHtml(message.text)}
                            </div>

                            <div class="message-meta">
                                ${escapeHtml(message.time)}
                                ${
                                    seller && message.seen
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

        renderThreads();
        updateTabCounts();
    }


    function switchTab(tabName) {
        if (!conversations[tabName]) {
            return;
        }

        activeTab = tabName;

        tabs.forEach(tab => {
            const active =
                tab.dataset.messageTab === activeTab;

            tab.classList.toggle('is-active', active);
            tab.setAttribute(
                'aria-selected',
                active ? 'true' : 'false'
            );
        });

        const firstConversation =
            conversations[activeTab][0];

        if (firstConversation) {
            activeConversationId =
                firstConversation.id;

            renderConversation(
                firstConversation
            );
        } else {
            renderThreads();
        }
    }


    function updateTabCounts() {
        const buyerUnread =
            conversations.buyers.reduce(
                (sum, item) =>
                    sum + Number(item.unread || 0),
                0
            );

        const complaintUnread =
            conversations.complaints.reduce(
                (sum, item) =>
                    sum + Number(item.unread || 0),
                0
            );

        if (buyerTabCount) {
            buyerTabCount.textContent =
                buyerUnread || conversations.buyers.length;
        }

        if (complaintTabCount) {
            complaintTabCount.textContent =
                complaintUnread || conversations.complaints.length;
        }
    }


    function sendCurrentMessage() {
        const value =
            (composerInput?.value || '')
                .trim();

        if (!value) {
            composerInput?.focus();
            return;
        }

        const conversation =
            getConversationById(
                activeConversationId
            );

        if (!conversation) {
            return;
        }

        const now =
            new Intl.DateTimeFormat(
                'en-US',
                {
                    hour: 'numeric',
                    minute: '2-digit'
                }
            ).format(new Date());

        conversation.messages.push({
            sender: 'seller',
            text: value,
            time: now,
            seen: false
        });

        conversation.preview = value;
        conversation.time = 'Now';

        if (composerInput) {
            composerInput.value = '';
            composerInput.style.height = 'auto';
        }

        renderConversation(
            conversation
        );
    }


    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            switchTab(
                tab.dataset.messageTab
            );
        });
    });


    threadList?.addEventListener(
        'click',
        event => {
            const thread =
                event.target.closest(
                    '[data-conversation-id]'
                );

            if (!thread) {
                return;
            }

            const conversation =
                getConversationById(
                    thread.dataset.conversationId
                );

            renderConversation(
                conversation
            );
        }
    );


    searchInput?.addEventListener(
        'input',
        renderThreads
    );


    sendButton?.addEventListener(
        'click',
        sendCurrentMessage
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
                sendCurrentMessage();
            }
        }
    );


    composerInput?.addEventListener(
        'input',
        () => {
            composerInput.style.height = 'auto';
            composerInput.style.height =
                `${Math.min(composerInput.scrollHeight, 90)}px`;
        }
    );


    document
        .getElementById('emojiButton')
        ?.addEventListener(
            'click',
            () => {
                if (!composerInput) {
                    return;
                }

                const start =
                    composerInput.selectionStart ?? composerInput.value.length;

                const end =
                    composerInput.selectionEnd ?? composerInput.value.length;

                composerInput.value =
                    composerInput.value.slice(0, start)
                    + ' 🙂 '
                    + composerInput.value.slice(end);

                composerInput.focus();
            }
        );


    document
        .getElementById('attachFileButton')
        ?.addEventListener(
            'click',
            () => {
                alert('File attachment UI is ready for backend/file-upload integration.');
            }
        );


    document
        .getElementById('sendPhotoButton')
        ?.addEventListener(
            'click',
            () => {
                alert('Photo attachment UI is ready for backend/file-upload integration.');
            }
        );


    document
        .getElementById('newMessageButton')
        ?.addEventListener(
            'click',
            () => {
                alert(
                    activeTab === 'complaints'
                        ? 'Complaint threads are created through the complaint process and are handled with ShopEase Admin.'
                        : 'New buyer chats can be connected to your buyer messaging backend here.'
                );
            }
        );


    document
        .getElementById('chatSearchButton')
        ?.addEventListener(
            'click',
            () => {
                searchInput?.focus();
            }
        );


    viewContextButton?.addEventListener(
        'click',
        () => {
            const conversation =
                getConversationById(
                    activeConversationId
                );

            if (!conversation) {
                return;
            }

            alert(
                conversation.type === 'complaints'
                    ? `Open ${conversation.contextValue}`
                    : `Open ${conversation.contextValue}`
            );
        }
    );


    updateTabCounts();

    const initialConversation =
        getConversationById(
            activeConversationId
        );

    renderThreads();
    renderConversation(
        initialConversation
    );
});