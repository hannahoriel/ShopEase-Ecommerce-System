{{-- =========================================================
     ADMIN MESSAGES PAGE
     resources/views/pages/admin/messages.blade.php

     Uses existing Admin Sidebar + Navbar
     Tabs:
     - Logistics
     - Complaints
     - Buyer
     - Seller

     Complaint threads:
     - Title is always Complaint ID
     - Buyer and Seller conversations are separated inside the case
========================================================= --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ShopEase - Admin Messages</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    @vite([
        'resources/css/app.css',
        'resources/css/admin/messages.css',
        'resources/js/admin/messages.js',
    ])
</head>

<body class="admin-messages-body">

    {{-- Existing Admin Sidebar --}}
    @include('components.admin.sidebar')

    {{-- Existing Admin Navbar --}}
    @section('page-title', 'Messages')
    @include('components.admin.navbar')

    <main id="admin-content" class="admin-messages-page">
        <div class="admin-messages-content">

            <section class="admin-messages-shell">

                {{-- =====================================================
                     LEFT: TABS + THREAD LIST
                ====================================================== --}}
                <aside class="admin-messages-sidebar">

                    <div class="admin-message-tabs" role="tablist" aria-label="Admin message categories">
                        <button
                            type="button"
                            class="admin-message-tab is-active"
                            data-admin-message-tab="logistics"
                            role="tab"
                            aria-selected="true"
                        >
                            Logistics
                            <span class="admin-tab-count" id="logisticsTabCount">2</span>
                        </button>

                        <button
                            type="button"
                            class="admin-message-tab"
                            data-admin-message-tab="complaints"
                            role="tab"
                            aria-selected="false"
                        >
                            Complaints
                            <span class="admin-tab-count" id="complaintsTabCount">3</span>
                        </button>

                        <button
                            type="button"
                            class="admin-message-tab"
                            data-admin-message-tab="buyers"
                            role="tab"
                            aria-selected="false"
                        >
                            Buyer
                            <span class="admin-tab-count" id="buyersTabCount">2</span>
                        </button>

                        <button
                            type="button"
                            class="admin-message-tab"
                            data-admin-message-tab="sellers"
                            role="tab"
                            aria-selected="false"
                        >
                            Seller
                            <span class="admin-tab-count" id="sellersTabCount">2</span>
                        </button>
                    </div>

                    <div class="admin-messages-search-wrap">
                        <label class="admin-messages-search">
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
                                id="adminMessagesSearch"
                                placeholder="Search messages"
                                autocomplete="off"
                            >
                        </label>
                    </div>

                    <div
                        class="admin-message-thread-list"
                        id="adminMessageThreadList"
                        aria-label="Admin conversations"
                    ></div>

                    <div
                        class="admin-empty-thread-list"
                        id="adminEmptyThreadList"
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
                     RIGHT: ACTIVE CHAT
                ====================================================== --}}
                <section class="admin-chat-panel">

                    <header class="admin-chat-header">
                        <div class="admin-chat-person">
                            <div
                                class="admin-chat-avatar"
                                id="adminActiveAvatar"
                                aria-hidden="true"
                            >
                                <span>EE</span>
                            </div>

                            <div class="admin-chat-heading-copy">
                                <div class="admin-chat-title-line">
                                    <h2 id="adminActiveTitle">Ease Express</h2>

                                    <span
                                        class="admin-chat-role-badge"
                                        id="adminActiveRoleBadge"
                                    >
                                        Logistics
                                    </span>
                                </div>

                                <p id="adminActiveSubtitle">
                                    Logistics Partner · Online
                                </p>
                            </div>
                        </div>

                        <div class="admin-chat-actions">
                            <button
                                type="button"
                                class="admin-chat-icon-button"
                                id="adminChatSearchButton"
                                aria-label="Search conversation"
                                title="Search conversation"
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
                                class="admin-chat-icon-button"
                                id="adminChatMoreButton"
                                aria-label="More options"
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

                    {{-- Complaint party switch: visible only on Complaints tab --}}
                    <div
                        class="complaint-party-switch-wrap"
                        id="complaintPartySwitchWrap"
                        hidden
                    >
                        <div class="complaint-case-summary">
                            <div class="complaint-case-summary-icon">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path
                                        d="M12 3 3.5 7v5.7c0 4.8 3.5 7.6 8.5 8.9 5-1.3 8.5-4.1 8.5-8.9V7L12 3Z"
                                        stroke="currentColor"
                                        stroke-width="1.7"
                                        stroke-linejoin="round"
                                    />
                                    <path
                                        d="M12 8v5M12 16.5v.1"
                                        stroke="currentColor"
                                        stroke-width="1.7"
                                        stroke-linecap="round"
                                    />
                                </svg>
                            </div>

                            <div class="complaint-case-summary-copy">
                                <span>Complaint Case</span>
                                <strong id="complaintCaseReference">Complaint #CMP-2026-0148</strong>
                            </div>

                            <span
                                class="complaint-case-status"
                                id="complaintCaseStatus"
                            >
                                Under Review
                            </span>
                        </div>

                        <div
                            class="complaint-party-switch"
                            role="tablist"
                            aria-label="Complaint party conversations"
                        >
                            <button
                                type="button"
                                class="complaint-party-tab is-active"
                                data-complaint-party="buyer"
                                role="tab"
                                aria-selected="true"
                            >
                                <span class="complaint-party-dot buyer"></span>
                                Buyer Conversation
                                <span class="complaint-party-unread" id="complaintBuyerUnread">1</span>
                            </button>

                            <button
                                type="button"
                                class="complaint-party-tab"
                                data-complaint-party="seller"
                                role="tab"
                                aria-selected="false"
                            >
                                <span class="complaint-party-dot seller"></span>
                                Seller Conversation
                                <span class="complaint-party-unread" id="complaintSellerUnread">1</span>
                            </button>
                        </div>
                    </div>

                    <div
                        class="admin-chat-context"
                        id="adminChatContext"
                    >
                        <div class="admin-chat-context-icon">
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

                        <div class="admin-chat-context-copy">
                            <span id="adminContextLabel">Reference</span>
                            <strong id="adminContextValue">Shipment #SHP-1098 · Ease Express</strong>
                        </div>

                        <button
                            type="button"
                            class="admin-view-context"
                            id="adminViewContextButton"
                        >
                            View Details
                        </button>
                    </div>

                    <div
                        class="admin-chat-body"
                        id="adminChatBody"
                        aria-live="polite"
                    ></div>

                    <footer class="admin-message-composer">
                        <div class="admin-composer-tools">
                            <button
                                type="button"
                                class="admin-composer-icon"
                                id="adminAttachButton"
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

                            <button
                                type="button"
                                class="admin-composer-icon"
                                id="adminPhotoButton"
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

                        <div class="admin-message-input-shell">
                            <textarea
                                id="adminMessageInput"
                                rows="1"
                                maxlength="1000"
                                placeholder="Type a message..."
                                aria-label="Message"
                            ></textarea>
                        </div>

                        <button
                            type="button"
                            class="admin-send-button"
                            id="adminSendMessageButton"
                            aria-label="Send message"
                        >
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path
                                    d="m21 3-8.2 18-2.1-7.7L3 11.2 21 3Z"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linejoin="round"
                                />
                                <path
                                    d="M10.7 13.3 21 3"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                />
                            </svg>
                        </button>
                    </footer>

                </section>

            </section>
        </div>
    </main>

</body>
</html>
