(() => {
        const initBuyerFloatingChat = () => {
            const root =
                document.getElementById('buyerFloatingChat');

            if (
                !root ||
                root.dataset.initialized === 'true'
            ) {
                return;
            }

            root.dataset.initialized = 'true';

            const toggle =
                document.getElementById(
                    'buyerFloatingChatToggle'
                );

            const panel =
                document.getElementById(
                    'buyerFloatingChatPanel'
                );

            const minimize =
                document.getElementById(
                    'buyerFloatingChatMinimize'
                );

            const search =
                document.getElementById(
                    'buyerFloatingChatSearch'
                );

            const conversationList =
                document.getElementById(
                    'buyerFloatingChatConversationList'
                );

            const noResults =
                document.getElementById(
                    'buyerFloatingChatNoResults'
                );

            const emptyState =
                document.getElementById(
                    'buyerFloatingChatEmptyState'
                );

            const activeConversation =
                document.getElementById(
                    'buyerFloatingChatActiveConversation'
                );

            const activeName =
                document.getElementById(
                    'buyerFloatingChatActiveName'
                );

            const composer =
                document.getElementById(
                    'buyerFloatingChatComposer'
                );

            const messageInput =
                document.getElementById(
                    'buyerFloatingChatMessageInput'
                );

            const messages =
                document.getElementById(
                    'buyerFloatingChatMessages'
                );

            const badge =
                document.getElementById(
                    'buyerFloatingChatBadge'
                );

            const status = document.getElementById('buyerFloatingChatStatus');
            const config = window.buyerMessagesConfig || {};
            let conversations = [];
            let activeConversationId = null;
            // Demo-only Admin complaint conversations, separate from the existing seller API.
            const demoKey = 'shopease-buyer-complaint-chat-demo-v1';
            const chatPrefix = 'admin-complaint:';
            const cleanComplaintId = value => {
                let id = String(value ?? '').trim();
                // Older demo chats mistakenly prepended the conversation prefix more than once.
                while (id.toLowerCase().startsWith(chatPrefix)) id = id.slice(chatPrefix.length).trim();
                return id;
            };
            const isComplaintChat = id => String(id || '').startsWith(chatPrefix);
            const escapeText = v => String(v ?? '').replace(/[&<>"']/g, ch => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[ch]));
            let demoChats = {};
            try {
                const stored = JSON.parse(localStorage.getItem(demoKey) || '{}') || {};
                // Consolidate all legacy duplicated keys into one chat per real complaint ID.
                Object.entries(stored).forEach(([key, chat]) => {
                    if (!chat || typeof chat !== 'object') return;
                    const complaintId = cleanComplaintId(chat.complaintId || chat.id || key);
                    if (!complaintId) return;
                    const id = `${chatPrefix}${complaintId}`;
                    const existing = demoChats[id];
                    const incoming = Array.isArray(chat.messages) ? chat.messages : [];
                    if (!existing) {
                        demoChats[id] = {
                            ...chat, id, complaintId,
                            messages: []
                        };
                    } else {
                        if (!existing.type && chat.type) existing.type = chat.type;
                        if ((!existing.status || existing.status === 'open') && chat.status) existing.status = chat.status;
                        if (!existing.filedAt && chat.filedAt) existing.filedAt = chat.filedAt;
                    }
                    const target = demoChats[id];
                    const seen = new Set(target.messages.map(m => JSON.stringify([m.sender,m.text,m.createdAt || m.created_at || m.timestamp || '',m.time || ''])));
                    incoming.forEach(message => {
                        if (!message || typeof message !== 'object') return;
                        // Repair bad complaint IDs inside previously generated demo greeting messages.
                        const normalized = {...message};
                        if (typeof normalized.text === 'string') {
                            normalized.text = normalized.text.replace(/(Conversation for complaint )(?:(?:admin-complaint:)+)/gi, '$1');
                        }
                        const identity = JSON.stringify([normalized.sender,normalized.text,normalized.createdAt || normalized.created_at || normalized.timestamp || '',normalized.time || '']);
                        if (!seen.has(identity)) { target.messages.push(normalized); seen.add(identity); }
                    });
                });
                localStorage.setItem(demoKey, JSON.stringify(demoChats));
            } catch (_) { demoChats = {}; }
            const saveDemo = () => { try { localStorage.setItem(demoKey, JSON.stringify(demoChats)); } catch (_) {} };
            const localConversations = () => Object.values(demoChats).map(c => ({
                id: c.id, title: 'ShopEase Admin', preview: `${c.complaintId} · ${c.type || 'Complaint'}`,
                unread: 0, complaintId: c.complaintId, demo: true
            }));
            const showComplaintContext = conversation => {
                const target = document.getElementById('buyerFloatingChatComplaintContext');
                if (!target) return;
                if (!conversation?.complaintId) { target.hidden = true; target.textContent = ''; return; }
                target.hidden = false;
                const norm = String(conversation.status || 'open').toLowerCase().replaceAll('_','-');
                document.getElementById('buyerChatComplaintId').textContent = `Complaint ${conversation.complaintId}`;
                document.getElementById('buyerChatComplaintType').textContent = conversation.type || 'Complaint';
                const statusEl = document.getElementById('buyerChatComplaintStatus');
                statusEl.className = `buyer-chat-complaint-status is-${['open','in-progress','resolved'].includes(norm) ? norm : 'open'}`;
                statusEl.textContent = ({open:'Open','in-progress':'In Progress',resolved:'Resolved'})[norm] || 'Open';
            };
            const openComplaintDemo = complaint => {
                const complaintId = cleanComplaintId(complaint?.complaintId || complaint?.id || '');
                if (!complaintId) return;
                const id = `${chatPrefix}${complaintId}`;
                if (!demoChats[id]) demoChats[id] = {
                    id, complaintId, type: String(complaint.type || ''), status: String(complaint.status || 'open'), filedAt: complaint.filed_at || new Date().toISOString(), messages: [
                        {sender:'admin', text:`ShopEase Support · Conversation for complaint ${complaintId}. This is a demo chat; no messages are sent to an administrator.`, time:'', createdAt: complaint.filed_at || new Date().toISOString()}
                    ]
                };
                if (complaint.type) demoChats[id].type = String(complaint.type);
                if (complaint.status) demoChats[id].status = String(complaint.status);
                if (complaint.filed_at) demoChats[id].filedAt = complaint.filed_at;
                saveDemo();
                activeConversationId = id;
                expand();
                showStatus('');
                if (activeName) activeName.textContent = 'ShopEase Admin';
                if (activeConversation) activeConversation.hidden = false;
                if (emptyState) emptyState.hidden = true;
                showComplaintContext(demoChats[id]);
                renderMessages(demoChats[id]);
                renderConversationList();
                messageInput?.focus();
            };
            window.openBuyerComplaintSupportChat = openComplaintDemo;
            document.addEventListener('buyer:open-complaint-support', event => openComplaintDemo(event.detail || {}));

            const apiFetch = async (path, options = {}) => {
                const response = await fetch(`${config.apiUrl}${path}`, {
                    credentials: 'same-origin',
                    ...options,
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        Authorization: `Bearer ${config.apiToken || ''}`,
                        'X-Requested-With': 'XMLHttpRequest',
                        ...(options.headers || {}),
                    },
                });
                const payload = await response.json().catch(() => ({}));
                if (!response.ok) {
                    const error = Object.values(payload.errors || {}).flat()[0];
                    throw new Error(error || payload.message || 'Unable to load messages.');
                }
                return payload;
            };

            const showStatus = message => {
                if (!status) return;
                status.textContent = message;
                status.hidden = !message;
            };

            const expand = () => {
                root.dataset.state = 'expanded';

                toggle?.setAttribute(
                    'aria-expanded',
                    'true'
                );

                panel?.setAttribute(
                    'aria-hidden',
                    'false'
                );

                window.setTimeout(
                    () => {
                        search?.focus();
                    },
                    180
                );
            };

            const collapse = () => {
                root.dataset.state = 'collapsed';

                toggle?.setAttribute(
                    'aria-expanded',
                    'false'
                );

                panel?.setAttribute(
                    'aria-hidden',
                    'true'
                );
            };

            toggle?.addEventListener(
                'click',
                expand
            );

            minimize?.addEventListener(
                'click',
                collapse
            );

            const renderConversationList = () => {
                if (!conversationList) return;
                const query = search?.value.trim().toLowerCase() || '';
                const allConversations = [...localConversations(), ...conversations.filter(c => !isComplaintChat(c.id))];
                const filtered = allConversations.filter(conversation =>
                    conversation.title.toLowerCase().includes(query)
                    || (conversation.preview || '').toLowerCase().includes(query)
                );
                conversationList.replaceChildren();
                filtered.forEach(conversation => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'buyer-floating-chat-conversation';
                    if (String(conversation.id) === String(activeConversationId)) {
                        button.classList.add('is-active');
                    }
                    button.dataset.conversationId = conversation.id;
                    button.dataset.chatName = conversation.title;
                    if (conversation.demo) {
                        const avatar = document.createElement('span');
                        avatar.className = 'buyer-floating-chat-admin-avatar';
                        avatar.textContent = 'SA';
                        button.append(avatar);
                    }
                    const copy = document.createElement('span');
                    copy.className = 'buyer-floating-chat-conversation-copy';
                    const title = document.createElement('strong');
                    title.textContent = conversation.title;
                    const preview = document.createElement('span');
                    preview.textContent = conversation.preview || 'Start a conversation';
                    copy.append(title, preview);
                    button.append(copy);
                    if (conversation.unread > 0) {
                        const unread = document.createElement('span');
                        unread.className = 'buyer-floating-chat-unread-dot';
                        button.append(unread);
                    }
                    conversationList.append(button);
                });
                conversationList.hidden = filtered.length === 0;
                if (noResults) noResults.hidden = filtered.length !== 0 || allConversations.length === 0;
                if (emptyState) emptyState.hidden = activeConversationId !== null;
                if (badge) {
                    const unreadCount = conversations.reduce((sum, item) => sum + Number(item.unread || 0), 0);
                    badge.textContent = unreadCount;
                    badge.hidden = unreadCount === 0;
                }
            };

            const renderMessages = conversation => {
                if (!messages) return;
                messages.replaceChildren();
                let lastDay = '';
                const entries = conversation.messages || [];
                entries.forEach((message, index) => {
                    const timestamp = message.createdAt || message.created_at || message.timestamp ||
                        (conversation.complaintId ? conversation.filedAt : '');
                    const dt = timestamp ? new Date(timestamp) : null;
                    const validDate = dt && !Number.isNaN(dt.getTime());
                    const dayKey = validDate ? `${dt.getFullYear()}-${dt.getMonth()}-${dt.getDate()}` : '';
                    if (dayKey && dayKey !== lastDay) {
                        const divider = document.createElement('div');
                        divider.className = 'buyer-chat-date-divider';
                        const pill = document.createElement('span');
                        const today = new Date();
                        const yesterday = new Date(today.getFullYear(),today.getMonth(),today.getDate()-1);
                        const same = x => x.getFullYear() === dt.getFullYear() && x.getMonth() === dt.getMonth() && x.getDate() === dt.getDate();
                        pill.textContent = same(today) ? 'Today' : same(yesterday) ? 'Yesterday' :
                            dt.toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'});
                        divider.append(pill);
                        messages.append(divider);
                        lastDay = dayKey;
                    }
                    const wrapper = document.createElement('div');
                    wrapper.className = `buyer-floating-chat-message is-${message.sender === 'buyer' ? 'buyer' : 'seller'}`;
                    const bubble = document.createElement('span');
                    bubble.textContent = message.text || '';
                    const time = document.createElement('time');
                    time.textContent = message.time || (validDate ? dt.toLocaleTimeString('en-US',{hour:'numeric',minute:'2-digit'}) : '');
                    wrapper.append(bubble, time);
                    messages.append(wrapper);
                });
                messages.scrollTop = messages.scrollHeight;
            };

            document.getElementById('buyerChatViewComplaint')?.addEventListener('click', () => {
                const chat = demoChats[activeConversationId];
                if (!chat?.complaintId) return;
                collapse();
                // Works on the My Purchases page without a reload; other buyer pages navigate there.
                const detail = {id:chat.complaintId};
                if (document.querySelector('[data-panel-content="complaints"]')) {
                    document.dispatchEvent(new CustomEvent('buyer:view-complaint', {detail}));
                } else {
                    const url = new URL('/buyer/my-purchases', window.location.origin);
                    url.searchParams.set('section','complaints');
                    url.searchParams.set('complaint',chat.complaintId);
                    window.location.assign(url.pathname+url.search);
                }
            });

            const openConversation = async conversationId => {
                if (isComplaintChat(conversationId) && demoChats[conversationId]) {
                    openComplaintDemo({...demoChats[conversationId], complaintId: demoChats[conversationId].complaintId});
                    return;
                }
                try {
                    const payload = await apiFetch(`/conversations/${encodeURIComponent(conversationId)}`);
                    const conversation = payload.data;
                    activeConversationId = conversation.id;
                    if (activeName) activeName.textContent = conversation.title;
                    showComplaintContext(null);
                    if (activeConversation) activeConversation.hidden = false;
                    if (emptyState) emptyState.hidden = true;
                    renderMessages(conversation);
                    await loadConversations();
                } catch (error) {
                    showStatus(error.message);
                }
            };

            const loadConversations = async () => {
                try {
                    const payload = await apiFetch('');
                    conversations = payload.data || [];
                    renderConversationList();
                } catch (error) {
                    showStatus(error.message);
                }
            };

            search?.addEventListener(
                'input',
                () => {
                    const query =
                        search.value
                            .trim()
                            .toLowerCase();

                    renderConversationList();
                }
            );

            conversationList?.addEventListener('click', event => {
                const button = event.target.closest('[data-conversation-id]');
                if (button) openConversation(button.dataset.conversationId);
            });

            composer?.addEventListener(
                'submit',
                async event => {
                    event.preventDefault();

                    const value =
                        messageInput?.value
                            .trim();

                    if (
                        !value ||
                        !messages
                    ) {
                        return;
                    }

                    if (!activeConversationId) return;
                    if (isComplaintChat(activeConversationId)) {
                        const chat = demoChats[activeConversationId];
                        if (!chat) return;
                        chat.messages.push({sender:'buyer', text:value, time:new Date().toLocaleTimeString('en-US',{hour:'numeric',minute:'2-digit'}), createdAt:new Date().toISOString()});
                        saveDemo();
                        messageInput.value = '';
                        renderMessages(chat);
                        messageInput.focus();
                        showStatus('Demo only — this message was not sent to an administrator.');
                        return;
                    }
                    showStatus('');
                    try {
                        await apiFetch(`/conversations/${encodeURIComponent(activeConversationId)}/messages`, {
                            method: 'POST',
                            body: JSON.stringify({ body: value }),
                        });
                        messageInput.value = '';
                        await openConversation(activeConversationId);
                        messageInput.focus();
                    } catch (error) {
                        showStatus(error.message);
                    }
                }
            );

            window.openBuyerSellerChat = async (contextId, contextType = 'product') => {
                expand();
                showStatus('');
                try {
                    const payload = await apiFetch('/conversations', {
                        method: 'POST',
                        body: JSON.stringify(contextType === 'order'
                            ? { order_id: Number(contextId) }
                            : { product_id: Number(contextId) }),
                    });
                    await loadConversations();
                    await openConversation(payload.data.id);
                } catch (error) {
                    showStatus(error.message);
                }
            };

            document.addEventListener(
                'pointerdown',
                event => {
                    if (
                        root.dataset.state === 'expanded' &&
                        !root.contains(event.target)
                    ) {
                        collapse();
                    }
                }
            );

            document.addEventListener(
                'keydown',
                event => {
                    if (
                        event.key === 'Escape' &&
                        root.dataset.state ===
                            'expanded'
                    ) {
                        collapse();
                    }
                }
            );

            if (activeConversation) activeConversation.hidden = true;
            if (emptyState) emptyState.hidden = false;
            conversationList?.replaceChildren();
            renderConversationList();
            loadConversations();
            // Supports previously generated URLs from the Complaints Contact Support button.
            const query = new URLSearchParams(window.location.search);
            const complaintFromUrl = query.get('complaint');
            if (complaintFromUrl && (query.get('tab') === 'admin' || query.get('source') === 'buyer-complaints')) {
                openComplaintDemo({id:complaintFromUrl});
            }

            if (config.productId) {
                document.querySelector('.chat-now-button')?.addEventListener('click', () => {
                    window.openBuyerSellerChat(config.productId);
                });
            }
            document.addEventListener('click', event => {
                const button = event.target.closest('[data-buyer-chat-order]');
                if (button) {
                    window.openBuyerSellerChat(button.dataset.buyerChatOrder, 'order');
                }
            });
        };

        if (
            document.readyState ===
            'loading'
        ) {
            document.addEventListener(
                'DOMContentLoaded',
                initBuyerFloatingChat,
                { once: true }
            );
        } else {
            initBuyerFloatingChat();
        }
    })();