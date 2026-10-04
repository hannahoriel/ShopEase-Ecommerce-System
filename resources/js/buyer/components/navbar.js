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
       SEARCH
    ================================================== */
    function runBuyerSearch() {
        const searchTerm = searchInput?.value?.trim();

        if (!searchTerm) {
            searchInput?.focus();
            return;
        }

        console.log('Buyer search:', searchTerm);
    }

    searchButton?.addEventListener('click', runBuyerSearch);

    searchInput?.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            runBuyerSearch();
        }
    });

    /* =================================================
       HEADER BUTTONS
    ================================================== */
    notificationButton?.addEventListener('click', function () {
        console.log('Buyer notifications opened.');
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