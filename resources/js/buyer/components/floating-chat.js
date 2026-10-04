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

            const conversations =
                Array.from(
                    root.querySelectorAll(
                        '.buyer-floating-chat-conversation'
                    )
                );

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

            const openConversation = (button) => {
                conversations.forEach(
                    item =>
                        item.classList.remove(
                            'is-active'
                        )
                );

                button.classList.add(
                    'is-active'
                );

                button
                    .querySelector(
                        '.buyer-floating-chat-unread-dot'
                    )
                    ?.remove();

                const name =
                    button.dataset.chatName ||
                    'Seller';

                if (activeName) {
                    activeName.textContent =
                        name;
                }

                if (emptyState) {
                    emptyState.hidden =
                        true;
                }

                if (activeConversation) {
                    activeConversation.hidden =
                        false;
                }

                if (badge) {
                    const remainingUnread =
                        root.querySelectorAll(
                            '.buyer-floating-chat-unread-dot'
                        ).length;

                    badge.textContent =
                        remainingUnread;

                    badge.hidden =
                        remainingUnread === 0;
                }
            };

            conversations.forEach(button => {
                button.addEventListener(
                    'click',
                    () =>
                        openConversation(
                            button
                        )
                );
            });

            search?.addEventListener(
                'input',
                () => {
                    const query =
                        search.value
                            .trim()
                            .toLowerCase();

                    let visible = 0;

                    conversations.forEach(
                        conversation => {
                            const name =
                                String(
                                    conversation.dataset.chatName ||
                                    ''
                                ).toLowerCase();

                            const match =
                                !query ||
                                name.includes(
                                    query
                                );

                            conversation.hidden =
                                !match;

                            if (match) {
                                visible++;
                            }
                        }
                    );

                    if (noResults) {
                        noResults.hidden =
                            visible > 0;
                    }

                    if (conversationList) {
                        conversationList.hidden =
                            visible === 0;
                    }
                }
            );

            composer?.addEventListener(
                'submit',
                event => {
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

                    const wrapper =
                        document.createElement(
                            'div'
                        );

                    wrapper.className =
                        'buyer-floating-chat-message is-buyer';

                    const bubble =
                        document.createElement(
                            'span'
                        );

                    bubble.textContent =
                        value;

                    const time =
                        document.createElement(
                            'time'
                        );

                    time.textContent =
                        new Intl.DateTimeFormat(
                            'en-US',
                            {
                                hour: 'numeric',
                                minute: '2-digit'
                            }
                        ).format(
                            new Date()
                        );

                    wrapper.append(
                        bubble,
                        time
                    );

                    messages.appendChild(
                        wrapper
                    );

                    messageInput.value =
                        '';

                    messages.scrollTop =
                        messages.scrollHeight;

                    messageInput.focus();
                }
            );

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

            /*
             * Start with the welcome state, like the reference.
             * Clicking a seller opens the conversation.
             */
            conversations.forEach(
                item =>
                    item.classList.remove(
                        'is-active'
                    )
            );

            if (activeConversation) {
                activeConversation.hidden =
                    true;
            }

            if (emptyState) {
                emptyState.hidden =
                    false;
            }
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