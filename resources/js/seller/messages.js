document.addEventListener('DOMContentLoaded', () => {
    const config = window.sellerMessagesConfig || {};
    const tabs = Array.from(document.querySelectorAll('[data-message-tab]'));
    const threadList = document.getElementById('messagesThreadList');
    const emptyThreads = document.getElementById('messagesEmptyThreads');
    const searchInput = document.getElementById('messagesSearch');
    const chatPanel = document.querySelector('.messages-chat-panel');
    const chatBody = document.getElementById('messagesChatBody');
    const activeAvatar = document.getElementById('activeConversationAvatar');
    const activeTitle = document.getElementById('activeConversationTitle');
    const activeBadge = document.getElementById('activeConversationBadge');
    const activeSubtitle = document.getElementById('activeConversationSubtitle');
    const activeContextLabel = document.getElementById('activeContextLabel');
    const activeContextValue = document.getElementById('activeContextValue');
    const viewContextButton = document.getElementById('viewContextButton');
    const composerInput = document.getElementById('messageComposerInput');
    const sendButton = document.getElementById('sendMessageButton');
    const fileInput = document.getElementById('messageFileInput');
    const mediaInput = document.getElementById('messageMediaInput');
    const status = document.getElementById('messagesStatus');
    const buyerTabCount = document.getElementById('buyerTabCount');
    const complaintTabCount = document.getElementById('complaintTabCount');
    const newConversationDialog = document.getElementById('newBuyerConversationDialog');
    const newConversationOrder = document.getElementById('newConversationOrder');
    const newConversationStatus = document.getElementById('newConversationStatus');
    const startConversationButton = document.getElementById('startNewConversationButton');

    const conversations = { buyers: [], complaints: [] };
    const counts = { buyers: 0, complaints: 0 };
    const unreadCounts = { buyers: 0, complaints: 0 };
    let activeTab = 'buyers';
    let activeConversationId = null;
    let searchTimer = null;
    let listRequestId = 0;
    let selectedAttachment = null;
    let statusTimer = null;
    const attachmentUrls = new Map();
    const maxAttachmentBytes = 15 * 1024 * 1024;
    const maxAttachmentMessage = 'You can only attach files up to 15 MB.';

    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function showStatus(element, message = '') {
        if (!element) return;
        element.textContent = message;
        element.hidden = !message;
    }

    function flashStatus(message) {
        showStatus(status, message);
        clearTimeout(statusTimer);
        statusTimer = window.setTimeout(() => showStatus(status), 6000);
    }

    function clearSelectedAttachment() {
        selectedAttachment = null;
        if (fileInput) fileInput.value = '';
        if (mediaInput) mediaInput.value = '';
    }

    async function apiFetch(path, options = {}) {
        const response = await fetch(`${config.apiUrl}${path}`, {
            credentials: 'same-origin',
            ...options,
            headers: {
                Accept: 'application/json',
                ...(options.body instanceof FormData ? {} : { 'Content-Type': 'application/json' }),
                Authorization: `Bearer ${config.apiToken || ''}`,
                'X-Requested-With': 'XMLHttpRequest',
                ...(options.headers || {}),
            },
        });

        const payload = await response.json().catch(() => ({}));
        if (!response.ok) {
            const validationMessage = Object.values(payload.errors || {}).flat()[0];
            throw new Error(validationMessage || payload.message || 'Unable to complete the messaging request.');
        }

        return payload;
    }

    function setCounts(payload) {
        Object.assign(counts, payload.counts || {});
        Object.assign(unreadCounts, payload.unread || {});
        buyerTabCount.textContent = unreadCounts.buyers || counts.buyers;
        complaintTabCount.textContent = unreadCounts.complaints || counts.complaints;
    }

    function renderThreads() {
        if (!threadList) return;

        const query = (searchInput?.value || '').trim().toLowerCase();
        const filtered = conversations[activeTab].filter(conversation => {
            const haystack = [
                conversation.title,
                conversation.preview,
                conversation.subtitle,
                conversation.context_value,
            ].join(' ').toLowerCase();
            return !query || haystack.includes(query);
        });

        threadList.innerHTML = filtered.map(conversation => `
            <button
                type="button"
                class="messages-thread ${String(conversation.id) === String(activeConversationId) ? 'is-active' : ''} ${conversation.unread ? 'is-unread' : ''}"
                data-conversation-id="${escapeHtml(conversation.id)}"
                data-type="${escapeHtml(conversation.type)}"
            >
                <span class="messages-thread-avatar">${escapeHtml(conversation.initials)}</span>
                <span class="messages-thread-copy">
                    <span class="messages-thread-topline">
                        <strong class="messages-thread-title">${escapeHtml(conversation.title)}</strong>
                        <span class="messages-thread-type">${escapeHtml(conversation.badge)}</span>
                    </span>
                    <span class="messages-thread-preview">${escapeHtml(conversation.preview)}</span>
                </span>
                <span class="messages-thread-meta">
                    <time class="messages-thread-time">${escapeHtml(conversation.time || '')}</time>
                    ${conversation.unread ? `<span class="messages-thread-unread">${escapeHtml(conversation.unread)}</span>` : ''}
                </span>
            </button>
        `).join('');

        threadList.hidden = filtered.length === 0;
        if (emptyThreads) emptyThreads.hidden = filtered.length > 0;
    }

    function formatDateLabel(dateValue) {
        const date = new Date(`${dateValue}T00:00:00`);
        const today = new Date();
        const yesterday = new Date();
        yesterday.setDate(today.getDate() - 1);
        const asDay = value => new Date(value.getFullYear(), value.getMonth(), value.getDate()).getTime();

        if (asDay(date) === asDay(today)) return 'Today';
        if (asDay(date) === asDay(yesterday)) return 'Yesterday';
        return date.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
    }

    function renderConversation(conversation) {
        if (!conversation || !chatBody) return;
        activeConversationId = conversation.id;
        chatPanel?.classList.toggle('is-complaint', conversation.type === 'complaints');
        activeAvatar.innerHTML = `<span>${escapeHtml(conversation.initials)}</span>`;
        activeTitle.textContent = conversation.title;
        activeBadge.textContent = conversation.badge;
        activeSubtitle.textContent = conversation.subtitle;
        activeContextLabel.textContent = conversation.context_label;
        activeContextValue.textContent = conversation.context_value;
        viewContextButton.textContent = conversation.context_button;
        composerInput.placeholder = conversation.type === 'complaints'
            ? 'Reply to ShopEase Admin...'
            : 'Type a message...';
        composerInput.disabled = false;
        sendButton.disabled = false;

        let previousDate = null;
        const messageMarkup = (conversation.messages || []).map(message => {
            const date = message.date || '';
            const divider = date && date !== previousDate
                ? `<div class="messages-date-divider"><span>${escapeHtml(formatDateLabel(date))}</span></div>`
                : '';
            previousDate = date || previousDate;
            const isSeller = message.sender === 'seller';
            const attachment = message.attachment;
            let attachmentMarkup = '';
            if (attachment) {
                const attachmentUrl = escapeHtml(attachment.url);
                const attachmentName = escapeHtml(attachment.name);
                if (attachment.mime_type?.startsWith('image/')) {
                    attachmentMarkup = `<img class="message-attachment-image" data-attachment-url="${attachmentUrl}" alt="${attachmentName}" loading="lazy">`;
                } else if (attachment.mime_type?.startsWith('video/')) {
                    attachmentMarkup = `<video class="message-attachment-video" data-attachment-url="${attachmentUrl}" controls preload="metadata" aria-label="${attachmentName}"></video>`;
                } else {
                    attachmentMarkup = `<span class="message-attachment-file">${attachmentName}</span>`;
                }
                attachmentMarkup += `<button type="button" class="message-attachment-download" data-download-url="${attachmentUrl}" data-download-name="${attachmentName}">Download</button>`;
            }

            return `${divider}
                <div class="message-row ${isSeller ? 'is-seller' : ''}">
                    <div class="message-bubble-wrap">
                        ${message.text ? `<div class="message-bubble">${escapeHtml(message.text)}</div>` : ''}
                        ${attachmentMarkup}
                        <div class="message-meta">
                            ${escapeHtml(message.time || '')}
                            ${isSeller && message.seen ? ' · Seen' : ''}
                        </div>
                    </div>
                </div>`;
        }).join('');

        const systemNote = conversation.type === 'complaints'
            ? '<div class="message-system-note">This complaint conversation is between your seller account and ShopEase Admin only.</div>'
            : '';
        chatBody.innerHTML = `${systemNote}${messageMarkup}`;
        if (!messageMarkup && !systemNote) {
            chatBody.innerHTML = '<div class="messages-system-empty">Send a message to start this conversation.</div>';
        }

        chatBody.scrollTop = chatBody.scrollHeight;
        hydrateAttachmentPreviews();
        renderThreads();
    }

    async function attachmentObjectUrl(url) {
        if (attachmentUrls.has(url)) return attachmentUrls.get(url);
        const response = await fetch(url, {
            credentials: 'same-origin',
            headers: {
                Authorization: `Bearer ${config.apiToken || ''}`,
                'X-Requested-With': 'XMLHttpRequest',
            },
        });
        if (!response.ok) throw new Error('Unable to load this attachment.');
        const objectUrl = URL.createObjectURL(await response.blob());
        attachmentUrls.set(url, objectUrl);
        return objectUrl;
    }

    function hydrateAttachmentPreviews() {
        chatBody?.querySelectorAll('[data-attachment-url]').forEach(element => {
            attachmentObjectUrl(element.dataset.attachmentUrl)
                .then(objectUrl => {
                    element.src = objectUrl;
                })
                .catch(error => flashStatus(error.message));
        });
    }

    async function downloadAttachment(button) {
        try {
            const url = `${button.dataset.downloadUrl}?download=1`;
            const response = await fetch(url, {
                credentials: 'same-origin',
                headers: {
                    Authorization: `Bearer ${config.apiToken || ''}`,
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
            if (!response.ok) throw new Error('Unable to download this attachment.');
            const objectUrl = URL.createObjectURL(await response.blob());
            const link = document.createElement('a');
            link.href = objectUrl;
            link.download = button.dataset.downloadName || 'attachment';
            link.click();
            URL.revokeObjectURL(objectUrl);
        } catch (error) {
            flashStatus(error.message);
        }
    }

    function clearConversation() {
        activeConversationId = null;
        activeTitle.textContent = 'Select a conversation';
        activeBadge.textContent = 'Messages';
        activeSubtitle.textContent = '';
        activeAvatar.innerHTML = '<span></span>';
        activeContextLabel.textContent = 'Conversation';
        activeContextValue.textContent = 'Select a thread to view its details.';
        viewContextButton.textContent = 'View Order';
        chatBody.innerHTML = '<div class="messages-system-empty">Choose a conversation to view its messages.</div>';
        composerInput.disabled = true;
        sendButton.disabled = true;
        chatPanel?.classList.remove('is-complaint');
        renderThreads();
    }

    async function loadThreads({ selectFirst = true } = {}) {
        const requestId = ++listRequestId;
        showStatus(status);
        const search = searchInput?.value.trim() || '';
        const query = new URLSearchParams({ type: activeTab });
        if (search) query.set('search', search);

        try {
            const payload = await apiFetch(`?${query.toString()}`);
            if (requestId !== listRequestId) return;
            conversations[activeTab] = payload.data || [];
            setCounts(payload);
            renderThreads();

            const current = conversations[activeTab].find(item => String(item.id) === String(activeConversationId));
            if (current) {
                await openConversation(current.id);
            } else if (selectFirst && conversations[activeTab].length) {
                await openConversation(conversations[activeTab][0].id);
            } else if (!conversations[activeTab].length) {
                clearConversation();
            }
        } catch (error) {
            showStatus(status, error.message);
        }
    }

    async function openConversation(id) {
        showStatus(status);
        try {
            const payload = await apiFetch(`/conversations/${encodeURIComponent(id)}`);
            const conversation = payload.data;
            if (String(activeConversationId) !== String(conversation.id)) {
                clearSelectedAttachment();
            }
            const existingIndex = conversations[conversation.type].findIndex(item => String(item.id) === String(conversation.id));
            if (existingIndex === -1) {
                conversations[conversation.type].unshift(conversation);
            } else {
                conversations[conversation.type][existingIndex] = conversation;
            }
            activeTab = conversation.type;
            renderConversation(conversation);
            await refreshUnreadCounts();
        } catch (error) {
            showStatus(status, error.message);
        }
    }

    async function refreshUnreadCounts() {
        try {
            const query = new URLSearchParams({ type: activeTab });
            const payload = await apiFetch(`?${query.toString()}`);
            setCounts(payload);
            const items = payload.data || [];
            conversations[activeTab] = items.map(item => {
                if (String(item.id) === String(activeConversationId)) {
                    const active = conversations[activeTab].find(existing => String(existing.id) === String(item.id));
                    return active ? { ...item, messages: active.messages } : item;
                }
                return item;
            });
            renderThreads();
        } catch (error) {
            showStatus(status, error.message);
        }
    }

    async function sendCurrentMessage() {
        const body = composerInput.value.trim();
        if (!body && !selectedAttachment) {
            composerInput.focus();
            return;
        }
        if (!activeConversationId) return;

        sendButton.disabled = true;
        showStatus(status);
        try {
            const formData = new FormData();
            if (body) formData.append('body', body);
            if (selectedAttachment) formData.append('attachment', selectedAttachment);
            await apiFetch(`/conversations/${encodeURIComponent(activeConversationId)}/messages`, {
                method: 'POST',
                body: formData,
            });
            composerInput.value = '';
            composerInput.style.height = 'auto';
            selectedAttachment = null;
            if (fileInput) fileInput.value = '';
            if (mediaInput) mediaInput.value = '';
            await openConversation(activeConversationId);
            await loadThreads({ selectFirst: false });
        } catch (error) {
            flashStatus(error.message.includes('15 MB') ? maxAttachmentMessage : error.message);
        } finally {
            sendButton.disabled = false;
        }
    }

    async function openNewConversationDialog() {
        if (activeTab === 'complaints') {
            window.alert('Complaint conversations are created from ShopEase complaint cases.');
            return;
        }

        showStatus(newConversationStatus);
        newConversationOrder.innerHTML = '<option value="">Loading buyer orders...</option>';
        newConversationDialog.showModal();
        try {
            const payload = await apiFetch('/contacts');
            const contacts = payload.data || [];
            newConversationOrder.innerHTML = contacts.length
                ? `<option value="">Choose a buyer order</option>${contacts.map(contact => `
                    <option value="${escapeHtml(contact.order_id)}">
                        ${escapeHtml(contact.buyer_name)} · #${escapeHtml(contact.order_number)}${contact.products ? ` · ${escapeHtml(contact.products)}` : ''}
                    </option>
                `).join('')}`
                : '<option value="">No buyer orders are available</option>';
            startConversationButton.disabled = contacts.length === 0;
            if (!contacts.length) showStatus(newConversationStatus, 'Buyer conversations can only be started from an existing buyer order.');
        } catch (error) {
            showStatus(newConversationStatus, error.message);
        }
    }

    async function createConversation() {
        const orderId = newConversationOrder.value;
        if (!orderId) {
            showStatus(newConversationStatus, 'Choose a buyer order first.');
            return;
        }
        startConversationButton.disabled = true;
        showStatus(newConversationStatus);
        try {
            const payload = await apiFetch('/conversations', {
                method: 'POST',
                body: JSON.stringify({ order_id: Number(orderId) }),
            });
            newConversationDialog.close();
            activeTab = 'buyers';
            tabs.forEach(tab => {
                const selected = tab.dataset.messageTab === activeTab;
                tab.classList.toggle('is-active', selected);
                tab.setAttribute('aria-selected', selected ? 'true' : 'false');
            });
            await loadThreads({ selectFirst: false });
            await openConversation(payload.data.id);
        } catch (error) {
            showStatus(newConversationStatus, error.message);
        } finally {
            startConversationButton.disabled = false;
        }
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            activeTab = tab.dataset.messageTab;
            activeConversationId = null;
            clearSelectedAttachment();
            tabs.forEach(item => {
                const selected = item === tab;
                item.classList.toggle('is-active', selected);
                item.setAttribute('aria-selected', selected ? 'true' : 'false');
            });
            loadThreads();
        });
    });

    threadList?.addEventListener('click', event => {
        const thread = event.target.closest('[data-conversation-id]');
        if (thread) openConversation(thread.dataset.conversationId);
    });

    searchInput?.addEventListener('input', () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => loadThreads({ selectFirst: false }), 250);
    });

    sendButton?.addEventListener('click', sendCurrentMessage);
    composerInput?.addEventListener('keydown', event => {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            sendCurrentMessage();
        }
    });
    composerInput?.addEventListener('input', () => {
        composerInput.style.height = 'auto';
        composerInput.style.height = `${Math.min(composerInput.scrollHeight, 90)}px`;
    });

    document.getElementById('emojiButton')?.addEventListener('click', () => {
        if (!composerInput) return;
        const start = composerInput.selectionStart ?? composerInput.value.length;
        const end = composerInput.selectionEnd ?? composerInput.value.length;
        composerInput.value = `${composerInput.value.slice(0, start)} 🙂 ${composerInput.value.slice(end)}`;
        composerInput.focus();
    });

    document.getElementById('attachFileButton')?.addEventListener('click', () => fileInput?.click());
    document.getElementById('sendPhotoButton')?.addEventListener('click', () => mediaInput?.click());
    [fileInput, mediaInput].forEach(input => input?.addEventListener('change', () => {
        const file = input.files?.[0];
        if (!file) return;
        if (file.size > maxAttachmentBytes) {
            selectedAttachment = null;
            input.value = '';
            flashStatus(maxAttachmentMessage);
            return;
        }
        selectedAttachment = file;
        showStatus(status, `${file.name} is ready to attach. Maximum size: 15 MB.`);
    }));
    chatBody?.addEventListener('click', event => {
        const button = event.target.closest('[data-download-url]');
        if (button) downloadAttachment(button);
    });
    document.getElementById('newMessageButton')?.addEventListener('click', openNewConversationDialog);
    document.getElementById('chatSearchButton')?.addEventListener('click', () => searchInput?.focus());
    document.getElementById('closeNewConversationButton')?.addEventListener('click', () => newConversationDialog.close());
    document.getElementById('cancelNewConversationButton')?.addEventListener('click', () => newConversationDialog.close());
    startConversationButton?.addEventListener('click', createConversation);

    viewContextButton?.addEventListener('click', () => {
        const conversation = conversations[activeTab].find(item => String(item.id) === String(activeConversationId));
        if (conversation) window.alert(conversation.context_value);
    });

    loadThreads();
    window.setInterval(() => {
        if (activeConversationId) openConversation(activeConversationId);
        else loadThreads({ selectFirst: false });
    }, 15000);
    window.addEventListener('beforeunload', () => {
        attachmentUrls.forEach(url => URL.revokeObjectURL(url));
        attachmentUrls.clear();
    });
});
