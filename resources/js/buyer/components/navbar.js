document.addEventListener('DOMContentLoaded', function () {

    /* =================================================
       ELEMENTS
    ================================================== */
    const allCategoriesButton =
        document.getElementById('buyerAllCategoriesButton');

    const allCategoriesText =
        document.getElementById('allCategoriesText');

    const categoriesDropdown =
        document.getElementById('buyerCategoriesDropdown');

    const searchButton =
        document.getElementById('buyerSearchButton');

    const searchInput =
        document.getElementById('buyerSearchInput');

    const accountButton =
        document.getElementById('buyerAccountButton');

    const accountDropdown =
        document.getElementById('buyerAccountDropdown');

    const notificationButton =
        document.getElementById('buyerNotificationButton');

    const cartBadge =
        document.getElementById('buyerCartBadge');

    function updateCartBadge(count) {
        if (!cartBadge) return;

        const quantity = Math.max(0, Number(count) || 0);
        cartBadge.textContent = quantity > 99 ? '99+' : String(quantity);
        cartBadge.style.display = quantity > 0 ? '' : 'none';
    }

    window.addEventListener('buyer:cart-updated', function (event) {
        updateCartBadge(event.detail?.count);
    });

    // Keep count accurate after navigation/back-forward or updates on another tab.
    async function refreshCartBadge() {
        if (!cartBadge) return;
        try {
            const response = await fetch('/api/v1/buyer/cart', { credentials: 'same-origin', headers: { Accept: 'application/json' } });
            if (!response.ok) return;
            const payload = await response.json();
            const items = Array.isArray(payload) ? payload : (payload.items || payload.data?.items || payload.data || []);
            if (Array.isArray(items)) updateCartBadge(items.reduce((sum, item) => sum + Number(item.quantity || 0), 0));
        } catch (_) { /* server-rendered badge remains as fallback */ }
    }
    refreshCartBadge();
    window.addEventListener('pageshow', refreshCartBadge);
    window.addEventListener('focus', refreshCartBadge);

    /* =================================================
       CATEGORY DROPDOWN
    ================================================== */
    function positionCategoriesDropdown() {
        if (!allCategoriesText || !categoriesDropdown) {
            return;
        }

        const textRect = allCategoriesText.getBoundingClientRect();

        categoriesDropdown.style.left = `${Math.round(textRect.left)}px`;
        categoriesDropdown.style.top = `${Math.round(textRect.bottom + 1)}px`;
    }

    function toggleCategoriesDropdown() {
        if (!categoriesDropdown) {
            return;
        }

        positionCategoriesDropdown();
        categoriesDropdown.classList.toggle('hidden');
    }

    allCategoriesButton?.addEventListener('click', function (event) {
        event.stopPropagation();
        positionCategoriesDropdown();
        toggleCategoriesDropdown();
    });

    window.addEventListener('resize', function () {
        if (categoriesDropdown && !categoriesDropdown.classList.contains('hidden')) {
            positionCategoriesDropdown();
        }
    });

    /* =================================================
       CLICK OUTSIDE DROPDOWN
    ================================================== */
    document.addEventListener('click', function (event) {
        if (
            categoriesDropdown &&
            !categoriesDropdown.contains(event.target) &&
            !allCategoriesButton?.contains(event.target)
        ) {
            categoriesDropdown.classList.add('hidden');
        }

        if (
            accountDropdown &&
            !accountDropdown.contains(event.target) &&
            !accountButton?.contains(event.target)
        ) {
            accountDropdown.classList.add('hidden');
            accountButton?.classList.remove('is-open');
            accountButton?.setAttribute('aria-expanded', 'false');
        }
    });

    /* =================================================
       SEARCH + LIVE SUGGESTIONS
    ================================================== */
    const suggestionPanel = document.getElementById('buyerSearchSuggestions');
    let suggestionItems = [];
    let activeSuggestion = -1;
    let searchDebounce = null;
    let latestRequest = null;
    let latestQuery = '';

    function searchUrl(term) {
        return '/buyer/search?q=' + encodeURIComponent(term);
    }

    function closeSuggestions() {
        if (!suggestionPanel) return;
        suggestionPanel.hidden = true;
        searchInput?.setAttribute('aria-expanded', 'false');
        searchInput?.removeAttribute('aria-activedescendant');
        activeSuggestion = -1;
    }

    function runBuyerSearch(term = searchInput?.value?.trim()) {
        if (!term) {
            searchInput?.focus();
            return;
        }
        closeSuggestions();
        window.location.href = searchUrl(term);
    }

    function drawSuggestions(values) {
        if (!suggestionPanel || !searchInput) return;
        suggestionPanel.replaceChildren();
        suggestionItems = values.filter(v => typeof v === 'string' && v.trim()).slice(0, 6);
        activeSuggestion = -1;

        if (suggestionItems.length === 0) {
            closeSuggestions();
            return;
        }

        const heading = document.createElement('div');
        heading.className = 'buyer-search-suggestion-heading';
        heading.textContent = 'Suggested products';
        suggestionPanel.appendChild(heading);

        suggestionItems.forEach((value, index) => {
            const option = document.createElement('button');
            option.type = 'button';
            option.id = 'buyerSearchSuggestion-' + index;
            option.className = 'buyer-search-suggestion';
            option.setAttribute('role', 'option');
            option.setAttribute('aria-selected', 'false');

            const icon = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            icon.setAttribute('width', '16');
            icon.setAttribute('height', '16');
            icon.setAttribute('viewBox', '0 0 24 24');
            icon.setAttribute('fill', 'none');
            icon.setAttribute('stroke', 'currentColor');
            icon.setAttribute('stroke-width', '2');
            const circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
            circle.setAttribute('cx', '11');circle.setAttribute('cy', '11');circle.setAttribute('r', '7');
            const line = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            line.setAttribute('d', 'm20 20-4-4');
            icon.append(circle, line);
            const text = document.createElement('span');
            text.className = 'buyer-search-suggestion-text';
            text.textContent = value;
            option.append(icon, text);

            // mousedown happens before input blur: navigation remains reliable.
            option.addEventListener('mousedown', event => event.preventDefault());
            option.addEventListener('click', () => runBuyerSearch(value));
            suggestionPanel.appendChild(option);
        });
        suggestionPanel.hidden = false;
        searchInput.setAttribute('aria-expanded', 'true');
    }

    function setActiveSuggestion(index) {
        const buttons = [...(suggestionPanel?.querySelectorAll('[role="option"]') || [])];
        if (!buttons.length) return;
        activeSuggestion = (index + buttons.length) % buttons.length;
        buttons.forEach((button, i) => {
            const selected = i === activeSuggestion;
            button.classList.toggle('is-active', selected);
            button.setAttribute('aria-selected', String(selected));
            if (selected) button.scrollIntoView({ block: 'nearest' });
        });
        searchInput?.setAttribute('aria-activedescendant', buttons[activeSuggestion].id);
    }

    async function fetchSuggestions() {
        const query = searchInput?.value?.trim() || '';
        if (query.length < 2) { closeSuggestions(); return; }
        latestRequest?.abort();
        const controller = new AbortController();
        latestRequest = controller;
        latestQuery = query;
        try {
            const response = await fetch('/buyer/search/suggestions?q=' + encodeURIComponent(query), {
                signal: controller.signal,
                credentials: 'same-origin',
                headers: { Accept: 'application/json' }
            });
            if (!response.ok) throw new Error('Suggestions unavailable');
            const payload = await response.json();
            if (controller.signal.aborted || searchInput?.value?.trim() !== latestQuery) return;
            drawSuggestions(Array.isArray(payload.suggestions) ? payload.suggestions : []);
        } catch (error) {
            if (error.name !== 'AbortError') closeSuggestions();
        }
    }

    searchInput?.addEventListener('input', () => {
        window.clearTimeout(searchDebounce);
        latestRequest?.abort();
        closeSuggestions();
        if (searchInput.value.trim().length < 2) return;
        searchDebounce = window.setTimeout(fetchSuggestions, 300);
    });

    searchInput?.addEventListener('focus', () => {
        if (searchInput.value.trim().length >= 2) {
            window.clearTimeout(searchDebounce);
            searchDebounce = window.setTimeout(fetchSuggestions, 300);
        }
    });

    searchInput?.addEventListener('keydown', event => {
        if (event.key === 'ArrowDown' && !suggestionPanel?.hidden && suggestionItems.length) {
            event.preventDefault(); setActiveSuggestion(activeSuggestion + 1);
        } else if (event.key === 'ArrowUp' && !suggestionPanel?.hidden && suggestionItems.length) {
            event.preventDefault(); setActiveSuggestion(activeSuggestion - 1);
        } else if (event.key === 'Enter') {
            event.preventDefault();
            runBuyerSearch(activeSuggestion >= 0 && !suggestionPanel?.hidden
                ? suggestionItems[activeSuggestion] : searchInput.value.trim());
        } else if (event.key === 'Escape') {
            closeSuggestions();
        }
    });

    searchButton?.addEventListener('click', () => runBuyerSearch());
    document.addEventListener('click', event => {
        if (!searchInput?.contains(event.target) &&
            !suggestionPanel?.contains(event.target) &&
            !searchButton?.contains(event.target)) closeSuggestions();
    });
    searchInput?.addEventListener('blur', () => {
        window.setTimeout(() => {
            if (!suggestionPanel?.contains(document.activeElement)) closeSuggestions();
        }, 150);
    });

    /* =================================================
       HEADER BUTTONS
    ================================================== */
    notificationButton?.addEventListener('click', function () {
        window.location.href = '/buyer/my-purchases?section=notifications';
    });

    accountButton?.addEventListener('click', function (event) {
        event.stopPropagation();

        if (!accountDropdown) {
            return;
        }

        const isOpen = !accountDropdown.classList.contains('hidden');

        accountDropdown.classList.toggle('hidden', isOpen);
        accountButton.classList.toggle('is-open', !isOpen);
        accountButton.setAttribute(
            'aria-expanded',
            isOpen ? 'false' : 'true'
        );
    });

    /* =================================================
       ESCAPE
    ================================================== */
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            categoriesDropdown?.classList.add('hidden');
            accountDropdown?.classList.add('hidden');
            accountButton?.classList.remove('is-open');
            accountButton?.setAttribute('aria-expanded', 'false');
        }
    });
});
