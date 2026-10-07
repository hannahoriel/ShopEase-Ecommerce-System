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
                const filtered = conversations.filter(conversation =>
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
                if (noResults) noResults.hidden = filtered.length !== 0 || conversations.length === 0;
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
                (conversation.messages || []).forEach(message => {
                    const wrapper = document.createElement('div');
                    wrapper.className = `buyer-floating-chat-message is-${message.sender}`;
                    const bubble = document.createElement('span');
                    bubble.textContent = message.text || '';
                    const time = document.createElement('time');
                    time.textContent = message.time || '';
                    wrapper.append(bubble, time);
                    messages.append(wrapper);
                });
                messages.scrollTop = messages.scrollHeight;
            };

            const openConversation = async conversationId => {
                try {
                    const payload = await apiFetch(`/conversations/${encodeURIComponent(conversationId)}`);
                    const conversation = payload.data;
                    activeConversationId = conversation.id;
                    if (activeName) activeName.textContent = conversation.title;
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
            loadConversations();

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