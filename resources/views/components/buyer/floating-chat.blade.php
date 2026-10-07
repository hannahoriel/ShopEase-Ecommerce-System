{{-- =========================================================
     ShopEase Buyer Floating Chat
     Suggested path:
     resources/views/components/buyer/floating-chat.blade.php

     Reusable on:
     - Buyer Dashboard
     - Cart
     - Product page
     - Other buyer pages

     Usage:
     @include('components.buyer.floating-chat')
========================================================= --}}


{{-- Floating Chat Assets --}}
@vite([
    'resources/css/buyer/components/floating-chat.css',
    'resources/js/buyer/components/floating-chat.js',
])

<div
    id="buyerFloatingChat"
    class="buyer-floating-chat"
    data-state="collapsed"
>
    {{-- COLLAPSED BUTTON --}}
    <button
        type="button"
        id="buyerFloatingChatToggle"
        class="buyer-floating-chat-toggle"
        aria-expanded="false"
        aria-controls="buyerFloatingChatPanel"
    >
        <span class="buyer-floating-chat-toggle-icon" aria-hidden="true">
            <img
                src="{{ asset('icons/buyer/chats-maroon.png') }}"
                alt=""
                class="buyer-floating-chat-toggle-icon-image"
            >
        </span>

        <span class="buyer-floating-chat-toggle-text">
            Chat
        </span>

        <span
            id="buyerFloatingChatBadge"
            class="buyer-floating-chat-badge"
        >
            2
        </span>
    </button>

    {{-- EXPANDED FLOATING PANEL --}}
    <section
        id="buyerFloatingChatPanel"
        class="buyer-floating-chat-panel"
        aria-label="ShopEase Chat"
        aria-hidden="true"
    >
        {{-- HEADER --}}
        <header class="buyer-floating-chat-header">
            <div class="buyer-floating-chat-heading">
                <span class="buyer-floating-chat-heading-icon" aria-hidden="true">
                    <img
                        src="{{ asset('icons/buyer/chats-maroon.png') }}"
                        alt=""
                        class="buyer-floating-chat-heading-icon-image"
                    >
                </span>

                <div>
                    <h2>Chat</h2>
                    <span>ShopEase Messages</span>
                </div>
            </div>

            <div class="buyer-floating-chat-header-actions">
                <button
                    type="button"
                    id="buyerFloatingChatMinimize"
                    class="buyer-floating-chat-icon-button"
                    aria-label="Minimize chat"
                >
                    <svg viewBox="0 0 24 24" fill="none">
                        <path
                            d="m7 10 5 5 5-5"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>
                </button>
            </div>
        </header>

        {{-- BODY --}}
        <div class="buyer-floating-chat-body">

            {{-- LEFT: CONVERSATION LIST --}}
            <aside class="buyer-floating-chat-sidebar">
                <div class="buyer-floating-chat-search-row">
                    <label class="buyer-floating-chat-search">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <circle
                                cx="11"
                                cy="11"
                                r="6.5"
                                stroke="currentColor"
                                stroke-width="1.7"
                            />
                            <path
                                d="m16 16 4 4"
                                stroke="currentColor"
                                stroke-width="1.7"
                                stroke-linecap="round"
                            />
                        </svg>

                        <input
                            type="search"
                            id="buyerFloatingChatSearch"
                            placeholder="Search name"
                            autocomplete="off"
                        >
                    </label>

                    <button
                        type="button"
                        id="buyerFloatingChatFilter"
                        class="buyer-floating-chat-filter"
                        aria-expanded="false"
                    >
                        All

                        <svg viewBox="0 0 24 24" fill="none">
                            <path
                                d="m7 10 5 5 5-5"
                                stroke="currentColor"
                                stroke-width="1.7"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>
                    </button>
                </div>

                <div
                    id="buyerFloatingChatConversationList"
                    class="buyer-floating-chat-conversations"
                >
                    <button
                        type="button"
                        class="buyer-floating-chat-conversation is-active"
                        data-chat-name="The Shop PH"
                    >
                        <span class="buyer-floating-chat-avatar">
                            <svg viewBox="0 0 48 48" aria-hidden="true">
                                <circle cx="24" cy="24" r="24" fill="#E3ECDF"/>
                                <circle cx="24" cy="18" r="8" fill="#D59E7A"/>
                                <path d="M13 38Q15 28 24 28T35 38" fill="#5F7462"/>
                                <path d="M16 15Q20 7 28 11Q33 12 34 18Q29 14 24 15Q20 16 16 15Z" fill="#3A2D2A"/>
                            </svg>
                        </span>

                        <span class="buyer-floating-chat-conversation-copy">
                            <strong>The Shop PH</strong>
                            <span>Your order is ready to ship.</span>
                        </span>

                        <span class="buyer-floating-chat-conversation-meta">
                            <time>2m</time>
                            <span class="buyer-floating-chat-unread-dot"></span>
                        </span>
                    </button>

                    <button
                        type="button"
                        class="buyer-floating-chat-conversation"
                        data-chat-name="Tech Haven"
                    >
                        <span class="buyer-floating-chat-avatar">
                            <svg viewBox="0 0 48 48" aria-hidden="true">
                                <circle cx="24" cy="24" r="24" fill="#DFE9F2"/>
                                <circle cx="24" cy="18" r="8" fill="#F0BF96"/>
                                <path d="M12 39Q15 28 24 28T36 39" fill="#374956"/>
                                <path d="M16 14Q21 5 30 10Q34 12 35 18Q30 14 25 14Q21 14 16 14Z" fill="#1E2630"/>
                            </svg>
                        </span>

                        <span class="buyer-floating-chat-conversation-copy">
                            <strong>Tech Haven</strong>
                            <span>Thank you for your purchase!</span>
                        </span>

                        <span class="buyer-floating-chat-conversation-meta">
                            <time>1h</time>
                        </span>
                    </button>

                    <button
                        type="button"
                        class="buyer-floating-chat-conversation"
                        data-chat-name="Mia Boutique"
                    >
                        <span class="buyer-floating-chat-avatar">
                            <svg viewBox="0 0 48 48" aria-hidden="true">
                                <circle cx="24" cy="24" r="24" fill="#F1E1D5"/>
                                <circle cx="24" cy="18" r="8" fill="#BD825D"/>
                                <path d="M12 39Q15 28 24 28T36 39" fill="#7B5E4F"/>
                                <path d="M15 17Q15 8 24 8Q33 8 34 17Q29 12 24 13Q19 12 15 17Z" fill="#4D3030"/>
                            </svg>
                        </span>

                        <span class="buyer-floating-chat-conversation-copy">
                            <strong>Mia Boutique</strong>
                            <span>Your item has been packed.</span>
                        </span>

                        <span class="buyer-floating-chat-conversation-meta">
                            <time>3h</time>
                        </span>
                    </button>
                </div>

                <div
                    id="buyerFloatingChatNoResults"
                    class="buyer-floating-chat-no-results"
                    hidden
                >
                    No Conversation Found
                </div>
            </aside>

            {{-- RIGHT: ACTIVE CHAT --}}
            <section class="buyer-floating-chat-message-area">

                <div
                    id="buyerFloatingChatEmptyState"
                    class="buyer-floating-chat-empty-state"
                >
                    <div class="buyer-floating-chat-empty-art" aria-hidden="true">
                        <svg viewBox="0 0 190 145" fill="none">
                            <rect
                                x="45"
                                y="23"
                                width="94"
                                height="74"
                                rx="8"
                                stroke="#B8B8B8"
                                stroke-width="6"
                            />

                            <rect
                                x="60"
                                y="40"
                                width="55"
                                height="28"
                                rx="4"
                                fill="#7A2630"
                            />

                            <path
                                d="M69 50h35M69 57h23"
                                stroke="#FFFFFF"
                                stroke-width="3"
                                stroke-linecap="round"
                            />

                            <path
                                d="M37 105h112"
                                stroke="#767676"
                                stroke-width="4"
                                stroke-linecap="round"
                            />

                            <path
                                d="M61 98h66l-8 8H69l-8-8Z"
                                fill="#777777"
                            />

                            <path
                                d="M131 61h37a8 8 0 0 1 8 8v18a8 8 0 0 1-8 8h-25l-13 8 4-11a8 8 0 0 1-3-6V69a8 8 0 0 1 8-8Z"
                                fill="#FF876E"
                            />

                            <circle cx="145" cy="78" r="3" fill="#FFFFFF"/>
                            <circle cx="155" cy="78" r="3" fill="#FFFFFF"/>
                            <circle cx="165" cy="78" r="3" fill="#FFFFFF"/>
                        </svg>
                    </div>

                    <h3>Welcome to ShopEase Chat</h3>

                    <p>
                        Start chatting with our sellers now!
                    </p>
                </div>

                <div
                    id="buyerFloatingChatActiveConversation"
                    class="buyer-floating-chat-active-conversation"
                    hidden
                >
                    <div class="buyer-floating-chat-active-header">
                        <span class="buyer-floating-chat-active-avatar">
                            <svg viewBox="0 0 48 48" aria-hidden="true">
                                <circle cx="24" cy="24" r="24" fill="#E3ECDF"/>
                                <circle cx="24" cy="18" r="8" fill="#D59E7A"/>
                                <path d="M13 38Q15 28 24 28T35 38" fill="#5F7462"/>
                                <path d="M16 15Q20 7 28 11Q33 12 34 18Q29 14 24 15Q20 16 16 15Z" fill="#3A2D2A"/>
                            </svg>
                        </span>

                        <div>
                            <strong id="buyerFloatingChatActiveName">
                                The Shop PH
                            </strong>
                            <span>Active now</span>
                        </div>
                    </div>

                    <div
                        id="buyerFloatingChatMessages"
                        class="buyer-floating-chat-messages"
                    >
                    </div>

                    <p id="buyerFloatingChatStatus" role="status" aria-live="polite" hidden></p>

                    <form
                        id="buyerFloatingChatComposer"
                        class="buyer-floating-chat-composer"
                    >
                        <button
                            type="button"
                            class="buyer-floating-chat-composer-icon"
                            aria-label="Attach file"
                        >
                            <svg viewBox="0 0 24 24" fill="none">
                                <path
                                    d="m8 12 6-6a4 4 0 0 1 6 6l-8 8a6 6 0 0 1-9-9l8-8"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>
                        </button>

                        <input
                            type="text"
                            id="buyerFloatingChatMessageInput"
                            placeholder="Type a message..."
                            autocomplete="off"
                        >

                        <button
                            type="submit"
                            class="buyer-floating-chat-send"
                            aria-label="Send message"
                        >
                            <img
                                src="{{ asset('icons/buyer/send.png') }}"
                                alt=""
                                class="buyer-floating-chat-send-image"
                            >
                        </button>
                    </form>
                </div>

                <script>
                    window.buyerMessagesConfig = {!! json_encode([
                        'apiUrl' => '/api/v1/buyer/messages',
                        'apiToken' => $apiToken ?? '',
                        'productId' => request()->integer('id') ?: null,
                    ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!};
                </script>
            </section>
        </div>
    </section>
</div>
