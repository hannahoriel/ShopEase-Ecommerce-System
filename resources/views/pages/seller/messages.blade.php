{{-- =========================================================
     SELLER MESSAGES PAGE
     resources/views/pages/seller/messages.blade.php
========================================================= --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>ShopEase - Seller Messages</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    @vite([
        'resources/css/app.css',
        'resources/css/seller/messages.css',
        'resources/js/seller/messages.js',
    ])
</head>

@section('page-title', 'Messages')

<body class="seller-messages-body">

    {{-- Shared Seller Sidebar --}}
    <x-seller.sidebar />

    {{-- Shared Seller Navbar --}}
    <x-seller.navbar />

    <main id="sellerMessagesPage" class="seller-messages-page">
        <div class="seller-messages-content">

            <section class="messages-shell">

                {{-- =====================================================
                     LEFT: CONVERSATION LIST
                ====================================================== --}}
                <aside class="messages-sidebar-panel">

                    <div class="messages-tabs" role="tablist" aria-label="Message categories">
                        <button
                            type="button"
                            class="messages-tab is-active"
                            data-message-tab="buyers"
                            role="tab"
                            aria-selected="true"
                        >
                            Buyers
                            <span class="messages-tab-count" id="buyerTabCount">0</span>
                        </button>

                        <button
                            type="button"
                            class="messages-tab"
                            data-message-tab="complaints"
                            role="tab"
                            aria-selected="false"
                        >
                            Complaints
                            <span class="messages-tab-count" id="complaintTabCount">0</span>
                        </button>
                    </div>

                    <div class="messages-search-wrap">
                        <label class="messages-search">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle
                                    cx="11"
                                    cy="11"
                                    r="6.5"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                />
                                <path
                                    d="m16 16 4 4"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                />
                            </svg>

                            <input
                                type="search"
                                id="messagesSearch"
                                placeholder="Search conversations"
                                autocomplete="off"
                            >
                        </label>
                    </div>

                    <div
                        class="messages-thread-list"
                        id="messagesThreadList"
                        aria-label="Conversation list"
                    ></div>

                    <div
                        id="messagesEmptyThreads"
                        class="messages-empty-threads"
                        hidden
                    >
                        <svg viewBox="0 0 48 48" fill="none" aria-hidden="true">
                            <path
                                d="M8 10h32v23H20L10 40v-7H8V10Z"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M15 18h18M15 24h13"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                            />
                        </svg>
                        <strong>No conversations found</strong>
                        <span>Try another search keyword.</span>
                    </div>

                </aside>

                {{-- =====================================================
                     RIGHT: ACTIVE CONVERSATION
                ====================================================== --}}
                <section class="messages-chat-panel is-no-chat-selected" id="messagesChatPanel">

                    <header class="messages-chat-header">
                        <div class="messages-chat-person">
                            <div
                                class="messages-chat-avatar"
                                id="activeConversationAvatar"
                                aria-hidden="true"
                            >
                                <span></span>
                            </div>

                            <div class="messages-chat-heading-copy">
                                <div class="messages-chat-title-row">
                                    <h2 id="activeConversationTitle">Select a conversation</h2>
                                    <span
                                        class="messages-conversation-badge"
                                        id="activeConversationBadge"
                                    >
                                        Messages
                                    </span>
                                </div>

                                <p id="activeConversationSubtitle">
                                </p>
                            </div>
                        </div>

                        <div class="messages-chat-header-actions">
                            <button
                                type="button"
                                class="messages-icon-button"
                                id="chatSearchButton"
                                aria-label="Search in conversation"
                                title="Search in conversation"
                            >
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <circle
                                        cx="11"
                                        cy="11"
                                        r="6.5"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    />
                                    <path
                                        d="m16 16 4 4"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                    />
                                </svg>
                            </button>

                            <button
                                type="button"
                                class="messages-icon-button"
                                id="chatMoreButton"
                                aria-label="More conversation options"
                                title="More"
                            >
                                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <circle cx="5" cy="12" r="1.7"/>
                                    <circle cx="12" cy="12" r="1.7"/>
                                    <circle cx="19" cy="12" r="1.7"/>
                                </svg>
                            </button>
                        </div>
                    </header>

                    <div
                        class="messages-chat-context"
                        id="activeConversationContext"
                    >
                        <div class="messages-chat-context-icon">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path
                                    d="M4 7h16l-1 12H5L4 7Z"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                    stroke-linejoin="round"
                                />
                                <path
                                    d="M8 9V6a4 4 0 0 1 8 0v3"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                    stroke-linecap="round"
                                />
                            </svg>
                        </div>

                        <div class="messages-chat-context-copy">
                            <span id="activeContextLabel">Conversation</span>
                            <strong id="activeContextValue">Select a thread to view its details.</strong>
                        </div>

                        <button
                            type="button"
                            class="messages-view-context"
                            id="viewContextButton"
                        >
                            View Order
                        </button>
                    </div>

                    <div
                        class="messages-chat-body"
                        id="messagesChatBody"
                        aria-live="polite"
                    >
                        <div
                            class="messages-no-selected-chat"
                            id="messagesNoSelectedChat"
                        >
                            <strong>No selected chat</strong>
                            <span>Selected chat will appear here.</span>
                        </div>
                    </div>

                    <p id="messagesStatus" class="messages-status" role="status" aria-live="polite" hidden></p>

                    <div
                        class="messages-typing"
                        id="messagesTyping"
                        hidden
                    >
                        <span></span>
                        <span></span>
                        <span></span>
                        <small id="messagesTypingLabel">Buyer is typing</small>
                    </div>

                    <footer class="messages-composer">
                        <div class="messages-composer-actions">
                            <button
                                type="button"
                                class="messages-composer-icon"
                                id="attachFileButton"
                                aria-label="Attach file"
                                title="Attach file"
                            >
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path
                                        d="m9 12 6.6-6.6a4 4 0 0 1 5.7 5.7L11.2 21.2a6 6 0 0 1-8.5-8.5L13 2.4"
                                        stroke="currentColor"
                                        stroke-width="1.7"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                </svg>
                            </button>

                            <input
                                type="file"
                                id="messageFileInput"
                                class="messages-hidden-file-input"
                                aria-label="Choose a file attachment"
                            >
                            <input
                                type="file"
                                id="messageMediaInput"
                                class="messages-hidden-file-input"
                                accept="image/*,video/*"
                                aria-label="Choose a photo or video attachment"
                            >

                            <button
                                type="button"
                                class="messages-composer-icon"
                                id="sendPhotoButton"
                                aria-label="Send photo"
                                title="Send photo"
                            >
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <rect
                                        x="3"
                                        y="5"
                                        width="18"
                                        height="14"
                                        rx="2"
                                        stroke="currentColor"
                                        stroke-width="1.7"
                                    />
                                    <circle
                                        cx="9"
                                        cy="10"
                                        r="2"
                                        stroke="currentColor"
                                        stroke-width="1.7"
                                    />
                                    <path
                                        d="m5 17 4.5-4 3.2 2.8 2.8-2.4L19 17"
                                        stroke="currentColor"
                                        stroke-width="1.7"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                </svg>
                            </button>
                        </div>

                        <div class="messages-input-shell">
                            <textarea
                                id="messageComposerInput"
                                rows="1"
                                maxlength="1000"
                                placeholder="Type a message..."
                                aria-label="Message"
                            ></textarea>

                            <button
                                type="button"
                                class="messages-emoji-button"
                                id="emojiButton"
                                aria-label="Add emoji"
                                title="Emoji"
                            >
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <circle
                                        cx="12"
                                        cy="12"
                                        r="9"
                                        stroke="currentColor"
                                        stroke-width="1.7"
                                    />
                                    <circle cx="9" cy="10" r="1" fill="currentColor"/>
                                    <circle cx="15" cy="10" r="1" fill="currentColor"/>
                                    <path
                                        d="M8.5 14.2c1 1.3 2.1 1.9 3.5 1.9s2.5-.6 3.5-1.9"
                                        stroke="currentColor"
                                        stroke-width="1.7"
                                        stroke-linecap="round"
                                    />
                                </svg>
                            </button>
                        </div>

                        <button
                            type="button"
                            class="messages-send-button"
                            id="sendMessageButton"
                            aria-label="Send message"
                        >
                            <img
                                src="{{ asset('icons/buyer/send.png') }}"
                                alt=""
                                class="messages-send-icon"
                                aria-hidden="true"
                            >
                        </button>
                    </footer>

                </section>
            </section>

            <dialog class="messages-new-dialog" id="newBuyerConversationDialog">
                <form method="dialog" class="messages-new-dialog-form" id="newBuyerConversationForm">
                    <div class="messages-new-dialog-heading">
                        <div>
                            <h2>Start a buyer conversation</h2>
                            <p>Choose one of your buyer's orders to provide context.</p>
                        </div>
                        <button type="button" class="messages-icon-button" id="closeNewConversationButton" aria-label="Close dialog">×</button>
                    </div>

                    <label for="newConversationOrder">Buyer order</label>
                    <select id="newConversationOrder" required>
                        <option value="">Loading buyer orders...</option>
                    </select>
                    <p id="newConversationStatus" class="messages-status" role="status" aria-live="polite" hidden></p>

                    <div class="messages-new-dialog-actions">
                        <button type="button" class="messages-dialog-cancel" id="cancelNewConversationButton">Cancel</button>
                        <button type="button" class="messages-dialog-submit" id="startNewConversationButton">Open conversation</button>
                    </div>
                </form>
            </dialog>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const panel = document.getElementById('messagesChatPanel');
            const title = document.getElementById('activeConversationTitle');

            if (!panel || !title) {
                return;
            }

            const syncSelectedChatState = function () {
                const text = (title.textContent || '').trim().toLowerCase();

                const hasSelectedChat =
                    text !== '' &&
                    text !== 'select a conversation' &&
                    text !== 'no selected chat';

                panel.classList.toggle(
                    'is-no-chat-selected',
                    !hasSelectedChat
                );
            };

            syncSelectedChatState();

            new MutationObserver(syncSelectedChatState).observe(
                title,
                {
                    childList: true,
                    characterData: true,
                    subtree: true
                }
            );
        });
    </script>

    <script>
        window.sellerMessagesConfig = {!! json_encode([
            'apiUrl' => '/api/v1/seller/messages',
            'apiToken' => $apiToken,
        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!};
    </script>
</body>
</html>
