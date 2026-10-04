const registrationsConfigElement =
    document.getElementById('registrationsConfig');

const registrationsConfig = registrationsConfigElement
    ? JSON.parse(registrationsConfigElement.textContent)
    : {
        counts: {},
        icons: {
            seller: '',
            buyer: '',
            rider: '',
            logistics: ''
        }
    };

document.addEventListener('DOMContentLoaded', function () {

    /* =========================================================
       ELEMENTS
    ========================================================== */

    const searchInput =
        document.getElementById('registration-search');

    const userTypeFilter =
        document.getElementById('user-type-filter');

    const dateFilter =
        document.getElementById('date-filter');

    const rows =
        document.querySelectorAll('.registration-row');

    const count =
        document.getElementById('registration-count');

    const reloadButton =
        document.getElementById('registration-reload');

    const reloadIcon =
        document.getElementById('registration-reload-icon');

    const itemsPerPageSelect =
        document.getElementById('items-per-page');


    /* =========================================================
       PAGINATION + FILTERING + LIVE REGISTRATION ARCHIVE
    ========================================================== */

    let allRows = Array.from(document.querySelectorAll('.registration-row'));
    const serverCounts = registrationsConfig.counts || {};

    allRows = allRows.filter(row => row.dataset.id);
    document.querySelectorAll('.registration-row:not([data-id])').forEach(row => row.remove());

    const approvedUsersBody = document.getElementById('approved-users-body');
    const rejectedUsersBody = document.getElementById('rejected-users-body');
    const pendingCountCard = document.getElementById('pending-count-card');
    const approvedCountCard = document.getElementById('approved-count-card');
    const rejectedCountCard = document.getElementById('rejected-count-card');
    const totalCountCard = document.getElementById('total-count-card');
    const approvedUsersCount = document.getElementById('approved-users-count');
    const rejectedUsersCount = document.getElementById('rejected-users-count');
    const approvedUsersSearch = document.getElementById('approved-users-search');
    const rejectedUsersSearch = document.getElementById('rejected-users-search');

    if (approvedUsersBody) {
        approvedUsersBody.innerHTML = '';
    }

    if (rejectedUsersBody) {
        rejectedUsersBody.innerHTML = '';
    }

    const totalRegistrations = serverCounts.total;

    let approvedCount = serverCounts.approved;
    let rejectedCount = serverCounts.rejected;

    let currentPage = 1;
    let itemsPerPage = 10;
    let filteredRows = [...allRows];
    let activeRegistrationRow = null;

    function formatCurrentDate() {
        return new Date().toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }

    function formatCategoryDisplay(category) {
        return String(category ?? '—')
            .replace(/Electronics\s+and\s+Gadgets/gi, 'Electronics & Gadgets')
            .replace(/Women\'s\s+Apparel/gi, "Women's Apparel")
            .replace(/Men\'s\s+Apparel/gi, "Men's Apparel")
            .replace(/Kids\s+and\s+Baby/gi, 'Kids & Baby')
            .replace(/Home\s+and\s+Garden/gi, 'Home & Garden')
            .replace(/Sports\s+and\s+Outdoors/gi, 'Sports & Outdoors')
            .replace(/Health\s+and\s+Beauty/gi, 'Health & Beauty')
            .replace(/Books\s+and\s+Media/gi, 'Books & Media')
            .replace(/Food\s+and\s+Gourmet/gi, 'Food & Gourmet')
            .replace(/Furniture\s+and\s+Office\s+Equipment/gi, 'Furniture & Office Equipment')
            .replace(/Office\s+and\s+School\s+Supplies/gi, 'Office & School Supplies');
    }

    function categoryBadgeClass(category) {
        const normalized = formatCategoryDisplay(category);

        const map = {
            'Pet Supplies': 'category-pet-supplies',
            'Electronics & Gadgets': 'category-electronics-and-gadgets',
            "Women's Apparel": 'category-womens-apparel',
            "Men's Apparel": 'category-mens-apparel',
            'Kids & Baby': 'category-kids-and-baby',
            'Home & Garden': 'category-home-and-garden',
            'Sports & Outdoors': 'category-sports-and-outdoors',
            'Health & Beauty': 'category-health-and-beauty',
            'Books & Media': 'category-books-and-media',
            'Food & Gourmet': 'category-food-and-gourmet',
            'Automotive & Motorcycle': 'category-automotive-motorcycle',
            'Furniture & Office Equipment': 'category-furniture-and-office-equipment',
            'Jewelry & Watches': 'category-jewelry-and-watches',
            'Office & School Supplies': 'category-office-and-school-supplies'
        };

        return map[normalized] || 'category-default';
    }

    function renderCategoryPills(categories) {
        let values = Array.isArray(categories) ? categories : [categories];

        values = values.filter(
            value => String(value ?? '').trim() !== ''
        );

        if (!values.length) {
            return '<span class="registration-category-pill category-default">—</span>';
        }

        return values.map(value => {
            const label = formatCategoryDisplay(value);

            return `
                <span class="registration-category-pill ${categoryBadgeClass(label)}">
                    ${escapeHtml(label)}
                </span>
            `;
        }).join(' ');
    }

    function initials(name) {
        return (name || '?')
            .split(' ')
            .filter(Boolean)
            .slice(0, 2)
            .map(part => part.charAt(0).toUpperCase())
            .join('');
    }

    function createArchiveRow(row, action) {
        const name = row.dataset.name || 'Unknown User';
        const email = row.dataset.email || '—';
        const phone = row.dataset.phone || '—';

        const type =
            row.dataset.type === 'seller'
                ? 'Seller'
                : 'Buyer';

        const icon =
            type === 'Seller'
                ? registrationsConfig.icons.seller
                : registrationsConfig.icons.buyer;

        const tr =
            document.createElement('tr');

        tr.className =
            'hover:bg-[#FFF9F7] transition';

        tr.dataset.sourceName =
            name;

        tr.dataset.action =
            action;

        tr.dataset.archiveTimestamp =
            String(Date.now());

        tr.innerHTML = `
            <td class="px-6 py-4">
                <div class="flex items-center gap-2.5">

                    <div
                        class="
                            w-7 h-7 rounded-full
                            bg-[#F6D8D2]
                            flex items-center justify-center
                            text-[10px] font-semibold
                            text-[#7B1B1B]
                            shrink-0
                        "
                    >
                        ${initials(name)}
                    </div>

                    <span class="text-[12px] font-medium text-gray-800">
                        ${name}
                    </span>

                </div>
            </td>

            <td class="px-6 py-4">

                <div class="flex items-center gap-1.5">

                    <img
                        src="${icon}"
                        class="w-[18px] h-[18px] object-contain"
                        alt="${type}"
                    >

                    <span class="text-[13px]">
                        ${type}
                    </span>

                </div>

            </td>

            <td class="px-6 py-4 text-[13px] text-gray-400">
                ${email}
            </td>

            <td class="px-6 py-4 text-[13px]">
                ${phone}
            </td>

            <td class="px-6 py-4 text-[13px]">
                ${formatCurrentDate()}
            </td>
        `;

        return tr;
    }

    function updateRegistrationStats() {

        if (pendingCountCard) {
            pendingCountCard.textContent =
                allRows.length;
        }

        if (approvedCountCard) {
            approvedCountCard.textContent =
                approvedCount;
        }

        if (rejectedCountCard) {
            rejectedCountCard.textContent =
                rejectedCount;
        }

        if (totalCountCard) {
            totalCountCard.textContent =
                totalRegistrations;
        }

        if (approvedUsersCount) {
            approvedUsersCount.textContent =
                `${approvedCount} approved users`;
        }

        if (rejectedUsersCount) {
            rejectedUsersCount.textContent =
                `${rejectedCount} rejected users`;
        }

    }

    function filterArchiveUsers(
        tbody,
        searchInput
    ) {

        if (
            !tbody ||
            !searchInput
        ) {
            return;
        }

        const query =
            searchInput.value
                .toLowerCase()
                .trim();

        const archiveRows =
            Array.from(
                tbody.querySelectorAll('tr')
            );

        archiveRows.forEach(row => {

            const rowText =
                row.textContent.toLowerCase();

            const matches =
                query === '' ||
                rowText.includes(query);

            row.classList.toggle(
                'hidden',
                !matches
            );

        });

    }

    function resetArchiveSearch(
        searchInput,
        tbody
    ) {

        if (searchInput) {
            searchInput.value = '';
        }

        if (tbody) {
            tbody
                .querySelectorAll('tr')
                .forEach(
                    row =>
                        row.classList.remove('hidden')
                );
        }

    }

    async function loadArchiveUsers(
        endpoint,
        tbody,
        action
    ) {

        if (!tbody) {
            return;
        }

        const response =
            await fetch(
                endpoint,
                {
                    headers: {
                        'Accept':
                            'application/json'
                    }
                }
            );

        if (!response.ok) {
            throw new Error(
                'Unable to load archived registrations.'
            );
        }

        const result =
            await response.json();

        const iconByType = {
            seller:
                registrationsConfig.icons.seller,

            buyer:
                registrationsConfig.icons.buyer,

            rider:
                registrationsConfig.icons.rider,

            logistics:
                registrationsConfig.icons.logistics
        };

        tbody.innerHTML =
            result.data.map(
                registration => {

                    const name =
                        registration.full_name ||
                        `${registration.first_name} ${registration.last_name}`;

                    const initials =
                        name
                            .split(' ')
                            .filter(Boolean)
                            .slice(0, 2)
                            .map(
                                part =>
                                    part[0].toUpperCase()
                            )
                            .join('');

                    const type =
                        registration.user_type || '';

                    const reviewedDate =
                        registration.reviewed_at
                            ? new Date(
                                registration.reviewed_at
                              ).toLocaleDateString(
                                'en-US',
                                {
                                    year:
                                        'numeric',

                                    month:
                                        'long',

                                    day:
                                        'numeric'
                                }
                              )
                            : '—';

                    return `
                        <tr class="hover:bg-[#FFF9F7] transition">

                            <td class="px-6 py-4">

                                <div class="flex items-center gap-2.5">

                                    <div
                                        class="
                                            w-7 h-7 rounded-full
                                            bg-[#F6D8D2]
                                            flex items-center justify-center
                                            text-[10px] font-semibold
                                            text-[#7B1B1B]
                                            shrink-0
                                        "
                                    >
                                        ${escapeHtml(initials)}
                                    </div>

                                    <span class="text-[12px] font-medium text-gray-800">
                                        ${escapeHtml(name)}
                                    </span>

                                </div>

                            </td>

                            <td class="px-6 py-4">

                                <div class="flex items-center gap-1.5">

                                    <img
                                        src="${iconByType[type] || iconByType.buyer}"
                                        class="w-[18px] h-[18px] object-contain"
                                        alt="${escapeHtml(type)}"
                                    >

                                    <span class="text-[13px]">
                                        ${escapeHtml(
                                            type.charAt(0).toUpperCase() +
                                            type.slice(1)
                                        )}
                                    </span>

                                </div>

                            </td>

                            <td class="px-6 py-4 text-[13px] text-gray-400">
                                ${escapeHtml(registration.email)}
                            </td>

                            <td class="px-6 py-4 text-[13px]">
                                ${escapeHtml(registration.phone)}
                            </td>

                            <td class="px-6 py-4 text-[13px]">
                                ${reviewedDate}
                            </td>

                        </tr>
                    `;
                }
            ).join('') ||
            `
                <tr>
                    <td
                        colspan="5"
                        class="px-6 py-10 text-center text-sm text-gray-400"
                    >
                        No ${action} users found.
                    </td>
                </tr>
            `;

        if (action === 'approved') {
            approvedCount =
                result.count;
        } else {
            rejectedCount =
                result.count;
        }

        updateRegistrationStats();

    }


    /* =========================================================
       APPROVED / REJECTED DATE SORTING
    ========================================================== */

    const archiveSortState = {
        'approved-users-body':
            'desc',

        'rejected-users-body':
            'desc'
    };

    function getArchiveRowDate(
        row
    ) {

        const dateCell =
            row.cells[4];

        if (!dateCell) {
            return 0;
        }

        const timestamp =
            Date.parse(
                dateCell.textContent.trim()
            );

        return Number.isNaN(timestamp)
            ? 0
            : timestamp;

    }

    function sortArchiveRows(
        tbodyId,
        direction
    ) {

        const tbody =
            document.getElementById(
                tbodyId
            );

        if (!tbody) {
            return;
        }

        const rows =
            Array.from(
                tbody.querySelectorAll('tr')
            );

        rows.sort(
            (
                a,
                b
            ) => {

                const dateA =
                    getArchiveRowDate(a);

                const dateB =
                    getArchiveRowDate(b);

                return direction === 'asc'
                    ? dateA - dateB
                    : dateB - dateA;

            }
        );

        rows.forEach(
            row =>
                tbody.appendChild(row)
        );

    }

    function updateArchiveSortArrow(
        button,
        direction
    ) {

        const arrow =
            button.querySelector(
                '.archive-sort-arrow'
            );

        if (arrow) {
            arrow.textContent =
                direction === 'asc'
                    ? '↑'
                    : '↓';
        }

    }

    document
        .querySelectorAll(
            '.archive-date-sort'
        )
        .forEach(
            button => {

                const targetId =
                    button.dataset.target;

                sortArchiveRows(
                    targetId,
                    archiveSortState[targetId]
                );

                button.addEventListener(
                    'click',
                    function () {

                        const currentDirection =
                            archiveSortState[targetId] ||
                            'desc';

                        const nextDirection =
                            currentDirection === 'desc'
                                ? 'asc'
                                : 'desc';

                        archiveSortState[targetId] =
                            nextDirection;

                        sortArchiveRows(
                            targetId,
                            nextDirection
                        );

                        updateArchiveSortArrow(
                            this,
                            nextDirection
                        );

                    }
                );

            }
        );

    if (approvedUsersSearch) {

        approvedUsersSearch.addEventListener(
            'input',
            function () {

                filterArchiveUsers(
                    approvedUsersBody,
                    approvedUsersSearch
                );

            }
        );

    }

    if (rejectedUsersSearch) {

        rejectedUsersSearch.addEventListener(
            'input',
            function () {

                filterArchiveUsers(
                    rejectedUsersBody,
                    rejectedUsersSearch
                );

            }
        );

    }

    function getFilters() {

        return {
            search:
                searchInput
                    ? searchInput.value
                        .toLowerCase()
                        .trim()
                    : '',

            userType:
                userTypeFilter
                    ? userTypeFilter.value
                    : 'all',

            date:
                dateFilter
                    ? dateFilter.value
                    : ''
        };

    }

    function matchesFilters(
        row,
        filters
    ) {

        const name =
            (row.dataset.name || '')
                .toLowerCase();

        const email =
            (row.dataset.email || '')
                .toLowerCase();

        const phone =
            (row.dataset.phone || '')
                .toLowerCase();

        const type =
            row.dataset.type || '';

        const rowDate =
            row.dataset.date || '';

        const matchesSearch =
            filters.search === '' ||
            name.includes(filters.search) ||
            email.includes(filters.search) ||
            phone.includes(filters.search);

        const matchesType =
            filters.userType === 'all' ||
            type === filters.userType;

        const matchesDate =
            filters.date === '' ||
            rowDate === filters.date;

        return (
            matchesSearch &&
            matchesType &&
            matchesDate
        );

    }

    function renderPagination() {

        const pagesContainer =
            document.getElementById(
                'registration-pages'
            );

        const prevButton =
            document.getElementById(
                'registration-prev'
            );

        const nextButton =
            document.getElementById(
                'registration-next'
            );

        const totalPages =
            Math.max(
                1,
                Math.ceil(
                    filteredRows.length /
                    itemsPerPage
                )
            );

        currentPage =
            Math.min(
                currentPage,
                totalPages
            );

        currentPage =
            Math.max(
                currentPage,
                1
            );

        if (pagesContainer) {

            pagesContainer.innerHTML =
                '';

            for (
                let page = 1;
                page <= totalPages;
                page++
            ) {

                const button =
                    document.createElement(
                        'button'
                    );

                button.type =
                    'button';

                button.textContent =
                    page;

                button.dataset.page =
                    page;

                button.className =
                    'registration-page w-7 h-7 flex items-center justify-center rounded-md text-[12px] transition ' +
                    (
                        page === currentPage
                            ? 'bg-[#FFD1C2] text-[#7B1B1B] font-medium'
                            : 'hover:bg-gray-100 text-gray-700'
                    );

                button.addEventListener(
                    'click',
                    function () {

                        currentPage =
                            Number(
                                this.dataset.page
                            );

                        renderTable();

                    }
                );

                pagesContainer
                    .appendChild(
                        button
                    );

            }

        }

        if (prevButton) {

            prevButton.disabled =
                currentPage <= 1;

            prevButton.classList.toggle(
                'opacity-40',
                currentPage <= 1
            );

            prevButton.classList.toggle(
                'cursor-not-allowed',
                currentPage <= 1
            );

        }

        if (nextButton) {

            nextButton.disabled =
                currentPage >= totalPages;

            nextButton.classList.toggle(
                'opacity-40',
                currentPage >= totalPages
            );

            nextButton.classList.toggle(
                'cursor-not-allowed',
                currentPage >= totalPages
            );

        }

    }

    function renderTable() {

        allRows.forEach(
            row =>
                row.classList.add('hidden')
        );

        const startIndex =
            (currentPage - 1) *
            itemsPerPage;

        const endIndex =
            startIndex +
            itemsPerPage;

        const pageRows =
            filteredRows.slice(
                startIndex,
                endIndex
            );

        pageRows.forEach(
            row =>
                row.classList.remove('hidden')
        );

        const visibleStart =
            filteredRows.length === 0
                ? 0
                : startIndex + 1;

        const visibleEnd =
            Math.min(
                endIndex,
                filteredRows.length
            );

        if (count) {

            count.textContent =
                `Showing ${visibleStart}–${visibleEnd} of ${filteredRows.length} entries`;

        }

        renderPagination();
        updateRegistrationStats();

    }

    function filterRegistrations(
        resetPage = true
    ) {

        const filters =
            getFilters();

        filteredRows =
            allRows.filter(
                row =>
                    matchesFilters(
                        row,
                        filters
                    )
            );

        if (resetPage) {
            currentPage = 1;
        }

        renderTable();

    }

    if (searchInput) {

        searchInput.addEventListener(
            'input',
            () =>
                filterRegistrations(true)
        );

    }

    if (userTypeFilter) {

        userTypeFilter.addEventListener(
            'change',
            () =>
                filterRegistrations(true)
        );

    }

    if (dateFilter) {

        dateFilter.addEventListener(
            'change',
            () =>
                filterRegistrations(true)
        );

    }

    const dateCalendarButton =
        document.getElementById(
            'date-calendar-button'
        );

    if (
        dateCalendarButton &&
        dateFilter
    ) {

        dateCalendarButton.addEventListener(
            'click',
            function () {

                if (
                    typeof dateFilter.showPicker ===
                    'function'
                ) {

                    dateFilter.showPicker();

                } else {

                    dateFilter.focus();
                    dateFilter.click();

                }

            }
        );

    }

    const prevButton =
        document.getElementById(
            'registration-prev'
        );

    const nextButton =
        document.getElementById(
            'registration-next'
        );

    if (prevButton) {

        prevButton.addEventListener(
            'click',
            function () {

                if (currentPage > 1) {

                    currentPage--;
                    renderTable();

                }

            }
        );

    }

    if (nextButton) {

        nextButton.addEventListener(
            'click',
            function () {

                const totalPages =
                    Math.max(
                        1,
                        Math.ceil(
                            filteredRows.length /
                            itemsPerPage
                        )
                    );

                if (
                    currentPage <
                    totalPages
                ) {

                    currentPage++;
                    renderTable();

                }

            }
        );

    }

    if (itemsPerPageSelect) {

        itemsPerPageSelect.addEventListener(
            'change',
            function () {

                itemsPerPage =
                    Number(
                        this.value
                    ) || 10;

                currentPage =
                    1;

                renderTable();

            }
        );

    }


    /* =========================================================
       RELOAD UX
    ========================================================== */

    if (reloadButton) {

        reloadButton.addEventListener(
            'click',
            function () {

                if (reloadIcon) {
                    reloadIcon.classList.add(
                        'animate-spin'
                    );
                }

                reloadButton.disabled =
                    true;

                setTimeout(
                    () => {

                        if (searchInput) {
                            searchInput.value =
                                '';
                        }

                        if (userTypeFilter) {
                            userTypeFilter.value =
                                'all';
                        }

                        if (dateFilter) {
                            dateFilter.value =
                                '';
                        }

                        if (itemsPerPageSelect) {
                            itemsPerPageSelect.value =
                                '10';
                        }

                        itemsPerPage =
                            10;

                        currentPage =
                            1;

                        filterRegistrations(
                            true
                        );

                        if (reloadIcon) {
                            reloadIcon.classList.remove(
                                'animate-spin'
                            );
                        }

                        reloadButton.disabled =
                            false;

                    },
                    500
                );

            }
        );

    }


    /* =========================================================
       REGISTRATION DETAILS MODALS
    ========================================================== */

    const sellerDetailsModal =
        document.getElementById(
            'seller-details-modal'
        );

    const buyerDetailsModal =
        document.getElementById(
            'buyer-details-modal'
        );

    const registrationImagePreviewModal =
        document.getElementById(
            'registration-image-preview-modal'
        );

    const registrationImagePreview =
        document.getElementById(
            'registration-image-preview'
        );

    const registrationImagePreviewTitle =
        document.getElementById(
            'registration-image-preview-title'
        );

    const registrationImagePreviewClose =
        document.getElementById(
            'registration-image-preview-close'
        );

    function openRegistrationModal(
        modal
    ) {

        if (!modal) {
            return;
        }

        modal.classList.remove(
            'hidden'
        );

        modal.classList.add(
            'flex'
        );

        modal.setAttribute(
            'aria-hidden',
            'false'
        );

        document.body.classList.add(
            'overflow-hidden'
        );

    }

    function openRegistrationImagePreview(
        imageUrl,
        imageTitle
    ) {

        if (
            !registrationImagePreviewModal ||
            !registrationImagePreview
        ) {
            return;
        }

        registrationImagePreview.src =
            imageUrl || '';

        registrationImagePreview.alt =
            imageTitle ||
            'Image preview';

        if (
            registrationImagePreviewTitle
        ) {

            registrationImagePreviewTitle.textContent =
                imageTitle ||
                'Image Preview';

        }

        registrationImagePreviewModal
            .classList
            .remove(
                'hidden'
            );

        registrationImagePreviewModal
            .classList
            .add(
                'flex'
            );

        registrationImagePreviewModal
            .setAttribute(
                'aria-hidden',
                'false'
            );

        document.body.classList.add(
            'overflow-hidden'
        );

    }

    function closeRegistrationImagePreview() {

        if (
            !registrationImagePreviewModal
        ) {
            return;
        }

        registrationImagePreviewModal
            .classList
            .add(
                'hidden'
            );

        registrationImagePreviewModal
            .classList
            .remove(
                'flex'
            );

        registrationImagePreviewModal
            .setAttribute(
                'aria-hidden',
                'true'
            );

        if (
            registrationImagePreview
        ) {

            registrationImagePreview.removeAttribute(
                'src'
            );

        }

        const anyRegistrationModalOpen =
            (
                sellerDetailsModal &&
                !sellerDetailsModal
                    .classList
                    .contains('hidden')
            ) ||
            (
                buyerDetailsModal &&
                !buyerDetailsModal
                    .classList
                    .contains('hidden')
            );

        if (
            !anyRegistrationModalOpen
        ) {

            document.body.classList.remove(
                'overflow-hidden'
            );

        }

    }

    function closeRegistrationModal(
        modal
    ) {

        if (!modal) {
            return;
        }

        modal.classList.add(
            'hidden'
        );

        modal.classList.remove(
            'flex'
        );

        modal.setAttribute(
            'aria-hidden',
            'true'
        );

        const anyRegistrationModalOpen =
            (
                sellerDetailsModal &&
                !sellerDetailsModal
                    .classList
                    .contains('hidden')
            ) ||
            (
                buyerDetailsModal &&
                !buyerDetailsModal
                    .classList
                    .contains('hidden')
            );

        if (
            !anyRegistrationModalOpen
        ) {

            document.body.classList.remove(
                'overflow-hidden'
            );

        }

    }

    function escapeHtml(
        value
    ) {

        return String(
            value ?? '—'
        ).replace(
            /[&<>'"]/g,
            character => ({
                '&':
                    '&amp;',

                '<':
                    '&lt;',

                '>':
                    '&gt;',

                "'":
                    '&#039;',

                '"':
                    '&quot;'

            }[character])
        );

    }

    function categoryBadgeClass(
        category
    ) {

        const normalizedCategory =
            String(
                category ?? ''
            )
                .replace(
                    /\band\b/gi,
                    '&'
                )
                .replace(
                    /\s{2,}/g,
                    ' '
                )
                .trim();

        const map = {
            'Pet Supplies':
                'category-pet-supplies',

            'Electronics & Gadgets':
                'category-electronics-and-gadgets',

            "Women's Apparel":
                'category-womens-apparel',

            "Women’s Apparel":
                'category-womens-apparel',

            "Men's Apparel":
                'category-mens-apparel',

            "Men’s Apparel":
                'category-mens-apparel',

            'Kids & Baby':
                'category-kids-and-baby',

            'Home & Garden':
                'category-home-and-garden',

            'Sports & Outdoors':
                'category-sports-and-outdoors',

            'Health & Beauty':
                'category-health-and-beauty',

            'Books & Media':
                'category-books-and-media',

            'Food & Gourmet':
                'category-food-and-gourmet',

            'Automotive & Motorcycle':
                'category-automotive-motorcycle',

            'Furniture & Office Equipment':
                'category-furniture-and-office-equipment',

            'Jewelry & Watches':
                'category-jewelry-and-watches',

            'Office & School Supplies':
                'category-office-and-school-supplies'
        };

        return (
            map[normalizedCategory] ||
            'category-default'
        );

    }

    function renderCategoryPills(
        categoryValue
    ) {

        let categories =
            categoryValue;

        if (
            typeof categories ===
            'string'
        ) {

            const trimmed =
                categories.trim();

            if (!trimmed) {

                categories =
                    [];

            } else if (
                trimmed.startsWith(
                    '['
                )
            ) {

                try {

                    const decoded =
                        JSON.parse(
                            trimmed
                        );

                    categories =
                        Array.isArray(
                            decoded
                        )
                            ? decoded
                            : [trimmed];

                } catch (
                    error
                ) {

                    categories =
                        [trimmed];

                }

            } else {

                categories =
                    trimmed
                        .split(',')
                        .map(
                            item =>
                                item.trim()
                        )
                        .filter(Boolean);

            }

        }

        if (
            !Array.isArray(
                categories
            )
        ) {

            categories =
                categories
                    ? [
                        String(
                            categories
                        )
                    ]
                    : [];

        }

        if (
            !categories.length
        ) {

            return `
                <span class="text-[15px] text-gray-400">
                    —
                </span>
            `;

        }

        return `
            <div class="flex flex-wrap items-center gap-2">

                ${
                    categories.map(
                        category => {

                            const safeCategory =
                                String(
                                    category
                                ).trim();

                            const displayCategory =
                                safeCategory.replace(
                                    /\band\b/gi,
                                    '&'
                                );

                            return `
                                <span
                                    class="
                                        registration-category-pill
                                        ${categoryBadgeClass(safeCategory)}
                                    "
                                >
                                    ${escapeHtml(displayCategory)}
                                </span>
                            `;

                        }
                    ).join('')
                }

            </div>
        `;

    }

    function renderRegistrationDetails(
        modal,
        registration
    ) {

        if (!modal) {
            return;
        }

        const body =
            modal.querySelector(
                '.registration-detail-modal > .px-7.pb-5'
            );

        const birthday =
            registration.birthdate
                ? new Date(
                    `${registration.birthdate}T00:00:00`
                  ).toLocaleDateString(
                    'en-US',
                    {
                        year:
                            'numeric',

                        month:
                            'long',

                        day:
                            'numeric'
                    }
                  )
                : '—';

        const age =
            registration.birthdate
                ? Math.floor(
                    (
                        Date.now() -
                        new Date(
                            registration.birthdate
                        ).getTime()
                    ) /
                    31557600000
                  )
                : '—';

        const validId =
            registration.valid_id_url
                ? `
                    <button
                        type="button"
                        class="
                            registration-image-trigger
                            group
                            block
                            w-full
                            text-left
                        "
                        data-image-url="${escapeHtml(registration.valid_id_url)}"
                        data-image-title="Uploaded Valid ID"
                        aria-label="View uploaded valid ID"
                    >

                        <div
                            class="
                                w-full
                                h-[360px]
                                rounded-xl
                                border
                                border-gray-200
                                bg-gray-50
                                overflow-hidden
                                flex
                                items-center
                                justify-center
                                transition
                                group-hover:border-[#A52A2A]
                                group-hover:shadow-md
                            "
                        >

                            <img
                                src="${escapeHtml(registration.valid_id_url)}"
                                alt="Uploaded valid ID"
                                class="
                                    w-full
                                    h-full
                                    object-contain
                                    bg-white
                                "
                            >

                        </div>

                        <p class="mt-2 text-[12px] text-gray-400">
                            Click the image to preview
                        </p>

                    </button>
                `
                : `
                    <div
                        class="
                            h-40
                            flex
                            items-center
                            justify-center
                            rounded-lg
                            border
                            border-dashed
                            border-gray-300
                            text-sm
                            text-gray-400
                        "
                    >
                        No valid ID uploaded
                    </div>
                `;

        const businessPermit =
            registration.business_permit_url
                ? `
                    <button
                        type="button"
                        class="
                            registration-image-trigger
                            group
                            block
                            w-full
                            text-left
                        "
                        data-image-url="${escapeHtml(registration.business_permit_url)}"
                        data-image-title="Business Permit"
                        aria-label="View business permit"
                    >

                        <div
                            class="
                                w-full
                                h-[360px]
                                rounded-xl
                                border
                                border-gray-200
                                bg-gray-50
                                overflow-hidden
                                flex
                                items-center
                                justify-center
                                transition
                                group-hover:border-[#A52A2A]
                                group-hover:shadow-md
                            "
                        >

                            <img
                                src="${escapeHtml(registration.business_permit_url)}"
                                alt="Business Permit"
                                class="
                                    w-full
                                    h-full
                                    object-contain
                                    bg-white
                                "
                            >

                        </div>

                        <p class="mt-2 text-[12px] text-gray-400">
                            Click the image to preview
                        </p>

                    </button>
                `
                : `
                    <span class="text-gray-400">
                        Business permit not provided
                    </span>
                `;

        if (body) {

            body.innerHTML = `
                <section>

                    <h4 class="text-[20px] font-semibold text-[#A52A2A] mb-6">
                        Personal Information
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-[1fr_300px] gap-8">

                        <div class="space-y-4 text-[15px]">

                            <div>
                                <span class="text-gray-400">Last Name</span>
                                <p class="font-medium">
                                    ${escapeHtml(registration.last_name)}
                                </p>
                            </div>

                            <div>
                                <span class="text-gray-400">First Name</span>
                                <p class="font-medium">
                                    ${escapeHtml(registration.first_name)}
                                </p>
                            </div>

                            <div>
                                <span class="text-gray-400">Middle Name</span>
                                <p class="font-medium">
                                    ${escapeHtml(registration.middle_name)}
                                </p>
                            </div>

                            <div>
                                <span class="text-gray-400">Sex</span>
                                <p class="font-medium">
                                    ${escapeHtml(registration.sex)}
                                </p>
                            </div>

                            <div>
                                <span class="text-gray-400">Birthday</span>
                                <p class="font-medium">
                                    ${birthday}
                                </p>
                            </div>

                            <div>
                                <span class="text-gray-400">Age</span>
                                <p class="font-medium">
                                    ${age}
                                </p>
                            </div>

                            <div>
                                <span class="text-gray-400">Email</span>
                                <p class="font-medium break-all">
                                    ${escapeHtml(registration.email)}
                                </p>
                            </div>

                            <div>
                                <span class="text-gray-400">Contact No.</span>
                                <p class="font-medium">
                                    ${escapeHtml(registration.phone)}
                                </p>
                            </div>

                        </div>

                        <div>

                            <p class="text-[15px] text-gray-400 mb-2">
                                Uploaded Valid ID
                            </p>

                            ${validId}

                        </div>

                    </div>

                </section>

                <div class="border-t border-gray-200 my-7"></div>

                <section>

                    <h4 class="text-[20px] font-semibold text-[#A52A2A] mb-6">
                        Address
                    </h4>

                    <div class="space-y-4 text-[15px]">

                        <div>
                            <span class="text-gray-400">Province</span>
                            <p class="font-medium">
                                ${escapeHtml(registration.province)}
                            </p>
                        </div>

                        <div>
                            <span class="text-gray-400">Municipality</span>
                            <p class="font-medium">
                                ${escapeHtml(registration.municipality)}
                            </p>
                        </div>

                        <div>
                            <span class="text-gray-400">Barangay</span>
                            <p class="font-medium">
                                ${escapeHtml(registration.barangay)}
                            </p>
                        </div>

                        <div>
                            <span class="text-gray-400">Street</span>
                            <p class="font-medium">
                                ${escapeHtml(registration.street)}
                            </p>
                        </div>

                        <div>
                            <span class="text-gray-400">House No.</span>
                            <p class="font-medium">
                                ${escapeHtml(registration.house_no)}
                            </p>
                        </div>

                        <div>
                            <span class="text-gray-400">Zip Code</span>
                            <p class="font-medium">
                                ${escapeHtml(registration.zip_code)}
                            </p>
                        </div>

                    </div>

                </section>

                ${
                    registration.user_type ===
                    'seller'
                        ? `
                            <div class="border-t border-gray-200 my-7"></div>

                            <section>

                                <h4 class="text-[20px] font-semibold text-[#A52A2A] mb-6">
                                    Business Information
                                </h4>

                                <div class="space-y-4 text-[15px]">

                                    <div>
                                        <span class="text-gray-400">
                                            Business Name
                                        </span>

                                        <p class="font-medium">
                                            ${escapeHtml(registration.business_name)}
                                        </p>
                                    </div>

                                    <div>
                                        <span class="text-gray-400">
                                            Category
                                        </span>

                                        <div class="mt-1">
                                            ${
                                                renderCategoryPills(
                                                    registration.business_categories ??
                                                    registration.business_category
                                                )
                                            }
                                        </div>
                                    </div>

                                    <div>
                                        <span class="text-gray-400">
                                            Business Permit
                                        </span>

                                        <div class="mt-1">
                                            ${businessPermit}
                                        </div>
                                    </div>

                                </div>

                            </section>
                        `
                        : ''
                }
            `;

        }

        const title =
            modal.querySelector(
                'h3'
            );

        if (title) {

            title.textContent =
                `${
                    registration.user_type ===
                    'seller'
                        ? 'Seller'
                        : 'Buyer'
                } Details`;

        }

    }

    async function loadRegistrationDetails(
        row,
        modal
    ) {

        if (!row.dataset.id) {
            return false;
        }

        try {

            const response =
                await fetch(
                    `/admin/registrations/${row.dataset.id}`,
                    {
                        headers: {
                            'Accept':
                                'application/json'
                        }
                    }
                );

            if (!response.ok) {

                throw new Error(
                    'Unable to load registration details.'
                );

            }

            renderRegistrationDetails(
                modal,
                await response.json()
            );

            return true;

        } catch (
            error
        ) {

            console.error(
                error
            );

            return false;

        }

    }

    document.addEventListener(
        'click',
        function (
            event
        ) {

            const imageTrigger =
                event.target.closest(
                    '.registration-image-trigger'
                );

            if (!imageTrigger) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();

            openRegistrationImagePreview(
                imageTrigger.dataset.imageUrl,
                imageTrigger.dataset.imageTitle
            );

        }
    );

    if (
        registrationImagePreviewClose
    ) {

        registrationImagePreviewClose.addEventListener(
            'click',
            function () {
                closeRegistrationImagePreview();
            }
        );

    }

    if (
        registrationImagePreviewModal
    ) {

        registrationImagePreviewModal.addEventListener(
            'click',
            function (
                event
            ) {

                if (
                    event.target ===
                    registrationImagePreviewModal
                ) {

                    closeRegistrationImagePreview();

                }

            }
        );

    }

    document.addEventListener(
        'keydown',
        function (
            event
        ) {

            if (
                event.key ===
                'Escape'
            ) {

                closeRegistrationImagePreview();

            }

        }
    );

    document
        .querySelectorAll(
            '.registration-row'
        )
        .forEach(
            row => {

                row.addEventListener(
                    'click',
                    async function () {

                        activeRegistrationRow =
                            this;

                        const type =
                            (
                                this.dataset.type ||
                                ''
                            ).toLowerCase();

                        if (
                            type ===
                            'seller'
                        ) {

                            if (
                                await loadRegistrationDetails(
                                    this,
                                    sellerDetailsModal
                                )
                            ) {

                                openRegistrationModal(
                                    sellerDetailsModal
                                );

                            }

                            return;

                        }

                        if (
                            type ===
                            'buyer'
                        ) {

                            if (
                                await loadRegistrationDetails(
                                    this,
                                    buyerDetailsModal
                                )
                            ) {

                                openRegistrationModal(
                                    buyerDetailsModal
                                );

                            }

                        }

                    }
                );

                row.setAttribute(
                    'role',
                    'button'
                );

                row.setAttribute(
                    'tabindex',
                    '0'
                );

                row.addEventListener(
                    'keydown',
                    function (
                        event
                    ) {

                        if (
                            event.key !==
                                'Enter' &&
                            event.key !==
                                ' '
                        ) {
                            return;
                        }

                        event.preventDefault();
                        this.click();

                    }
                );

            }
        );

    document
        .querySelectorAll(
            '.registration-modal-close'
        )
        .forEach(
            button => {

                button.addEventListener(
                    'click',
                    function () {

                        const modalId =
                            this.dataset.modal;

                        closeRegistrationModal(
                            document.getElementById(
                                modalId
                            )
                        );

                    }
                );

            }
        );

    [
        sellerDetailsModal,
        buyerDetailsModal
    ].forEach(
        modal => {

            if (!modal) {
                return;
            }

            modal.addEventListener(
                'click',
                function (
                    event
                ) {

                    if (
                        event.target ===
                        modal
                    ) {

                        closeRegistrationModal(
                            modal
                        );

                    }

                }
            );

        }
    );

    function moveRegistrationToArchive(
        row,
        action
    ) {

        if (
            !row ||
            !row.parentNode
        ) {
            return;
        }

        const archiveBody =
            action === 'approved'
                ? approvedUsersBody
                : rejectedUsersBody;

        if (!archiveBody) {
            return;
        }

        const archiveRow =
            createArchiveRow(
                row,
                action
            );

        archiveBody.insertBefore(
            archiveRow,
            archiveBody.firstChild
        );

        if (
            action ===
            'approved'
        ) {

            sortArchiveRows(
                'approved-users-body',
                archiveSortState['approved-users-body']
            );

            if (
                approvedUsersSearch
            ) {

                filterArchiveUsers(
                    approvedUsersBody,
                    approvedUsersSearch
                );

            }

            approvedCount++;

        } else {

            sortArchiveRows(
                'rejected-users-body',
                archiveSortState['rejected-users-body']
            );

            rejectedCount++;

            if (
                rejectedUsersSearch
            ) {

                filterArchiveUsers(
                    rejectedUsersBody,
                    rejectedUsersSearch
                );

            }

        }

        allRows =
            allRows.filter(
                item =>
                    item !== row
            );

        row.remove();

        activeRegistrationRow =
            null;

        filterRegistrations(
            true
        );

        updateRegistrationStats();

    }


    /* =========================================================
       APPROVE / REJECT REGISTRATION ACTIONS
    ========================================================== */

    const rejectRegistrationModal =
        document.getElementById(
            'reject-registration-modal'
        );

    const cancelRejectRegistration =
        document.getElementById(
            'cancel-reject-registration'
        );

    const confirmRejectRegistration =
        document.getElementById(
            'confirm-reject-registration'
        );

    const rejectAdditionalDetails =
        document.getElementById(
            'reject-additional-details'
        );

    const rejectDetailsCount =
        document.getElementById(
            'reject-details-count'
        );

    const rejectReasonError =
        document.getElementById(
            'reject-reason-error'
        );

    const registrationFlash =
        document.getElementById(
            'registration-flash'
        );

    const registrationFlashIcon =
        document.getElementById(
            'registration-flash-icon'
        );

    const registrationFlashTitle =
        document.getElementById(
            'registration-flash-title'
        );

    const registrationFlashMessage =
        document.getElementById(
            'registration-flash-message'
        );

    const registrationFlashClose =
        document.getElementById(
            'registration-flash-close'
        );

    let registrationFlashTimer =
        null;

    function getSelectedRejectReason() {

        const checked =
            document.querySelector(
                'input[name="reject_reason"]:checked'
            );

        return checked
            ? checked.value
            : '';

    }

    function resetRejectRegistrationForm() {

        document
            .querySelectorAll(
                'input[name="reject_reason"]'
            )
            .forEach(
                input => {

                    input.checked =
                        false;

                }
            );

        if (
            rejectAdditionalDetails
        ) {

            rejectAdditionalDetails.value =
                '';

        }

        if (
            rejectDetailsCount
        ) {

            rejectDetailsCount.textContent =
                '0/300';

        }

        if (
            rejectReasonError
        ) {

            rejectReasonError.classList.add(
                'hidden'
            );

        }

    }

    function openRejectRegistrationModal() {

        if (
            !rejectRegistrationModal
        ) {
            return;
        }

        resetRejectRegistrationForm();

        rejectRegistrationModal
            .classList
            .remove(
                'hidden'
            );

        rejectRegistrationModal
            .classList
            .add(
                'flex'
            );

        rejectRegistrationModal
            .setAttribute(
                'aria-hidden',
                'false'
            );

        document.body.classList.add(
            'overflow-hidden'
        );

    }

    function closeRejectRegistrationModal() {

        if (
            !rejectRegistrationModal
        ) {
            return;
        }

        rejectRegistrationModal
            .classList
            .add(
                'hidden'
            );

        rejectRegistrationModal
            .classList
            .remove(
                'flex'
            );

        rejectRegistrationModal
            .setAttribute(
                'aria-hidden',
                'true'
            );

        const anyRegistrationModalOpen =
            (
                sellerDetailsModal &&
                !sellerDetailsModal
                    .classList
                    .contains('hidden')
            ) ||
            (
                buyerDetailsModal &&
                !buyerDetailsModal
                    .classList
                    .contains('hidden')
            );

        if (
            !anyRegistrationModalOpen
        ) {

            document.body.classList.remove(
                'overflow-hidden'
            );

        }

    }

    function showRegistrationFlash(
        type,
        name
    ) {

        if (
            !registrationFlash
        ) {
            return;
        }

        const approved =
            type === 'approved';

        const rejected =
            type === 'rejected' ||
            type === 'reject';

        const isError =
            type === 'error';

        registrationFlash
            .classList
            .remove(
                'approved',
                'rejected',
                'error'
            );

        registrationFlash
            .classList
            .add(
                approved
                    ? 'approved'
                    : (
                        isError
                            ? 'error'
                            : 'rejected'
                    )
            );

        registrationFlashIcon.className =
            'w-9 h-9 rounded-full flex items-center justify-center shrink-0 ' +
            (
                approved
                    ? 'bg-[#DDF0D6]'
                    : 'bg-[#FFE3E5]'
            );

        registrationFlashIcon.innerHTML =
            approved
                ? `
                    <svg
                        class="w-5 h-5 text-[#28721B]"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="m5 12 4 4L19 6"
                        />
                    </svg>
                `
                : `
                    <svg
                        class="w-5 h-5 text-[#B3262E]"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 6l12 12M18 6 6 18"
                        />
                    </svg>
                `;

        if (
            isError
        ) {

            registrationFlashTitle.textContent =
                'Action Failed';

            registrationFlashMessage.textContent =
                name ||
                'Unable to update this registration.';

        } else {

            registrationFlashTitle.textContent =
                approved
                    ? 'Registration Approved'
                    : 'Registration Rejected';

            registrationFlashMessage.textContent =
                approved
                    ? `${name}'s registration has been approved. The user will be notified via email.`
                    : `${name}'s registration has been rejected. The user will be notified via email.`;

        }

        clearTimeout(
            registrationFlashTimer
        );

        registrationFlash
            .classList
            .add(
                'show'
            );

        registrationFlashTimer =
            setTimeout(
                () => {

                    registrationFlash
                        .classList
                        .remove(
                            'show'
                        );

                },
                3800
            );

    }

    async function submitRegistrationReview(
        row,
        action,
        payload = {}
    ) {

        if (
            !row.dataset.id
        ) {

            moveRegistrationToArchive(
                row,
                action
            );

            return true;

        }

        const csrfToken =
            document.querySelector(
                'meta[name="csrf-token"]'
            )?.content;

        const response =
            await fetch(
                `/admin/registrations/${row.dataset.id}/${action}`,
                {
                    method:
                        'POST',

                    headers: {
                        'Accept':
                            'application/json',

                        'Content-Type':
                            'application/json',

                        'X-CSRF-TOKEN':
                            csrfToken || ''
                    },

                    body:
                        JSON.stringify(
                            payload
                        )
                }
            );

        if (
            !response.ok
        ) {

            const error =
                await response
                    .json()
                    .catch(
                        () => ({})
                    );

            throw new Error(
                error.message ||
                `Unable to ${action} registration.`
            );

        }

        moveRegistrationToArchive(
            row,
            action
        );

        return true;

    }

    if (
        registrationFlashClose
    ) {

        registrationFlashClose.addEventListener(
            'click',
            () => {

                clearTimeout(
                    registrationFlashTimer
                );

                if (
                    registrationFlash
                ) {

                    registrationFlash
                        .classList
                        .remove(
                            'show'
                        );

                }

            }
        );

    }

    if (
        rejectAdditionalDetails &&
        rejectDetailsCount
    ) {

        rejectAdditionalDetails.addEventListener(
            'input',
            () => {

                rejectDetailsCount.textContent =
                    `${rejectAdditionalDetails.value.length}/300`;

            }
        );

    }

    if (
        cancelRejectRegistration
    ) {

        cancelRejectRegistration.addEventListener(
            'click',
            closeRejectRegistrationModal
        );

    }

    if (
        rejectRegistrationModal
    ) {

        rejectRegistrationModal.addEventListener(
            'click',
            function (
                event
            ) {

                if (
                    event.target ===
                    rejectRegistrationModal
                ) {

                    closeRejectRegistrationModal();

                }

            }
        );

    }

    if (
        confirmRejectRegistration
    ) {

        confirmRejectRegistration.addEventListener(
            'click',
            async function () {

                if (
                    !activeRegistrationRow
                ) {

                    closeRejectRegistrationModal();
                    return;

                }

                const reason =
                    getSelectedRejectReason();

                if (!reason) {

                    if (
                        rejectReasonError
                    ) {

                        rejectReasonError
                            .classList
                            .remove(
                                'hidden'
                            );

                    }

                    return;

                }

                const name =
                    activeRegistrationRow
                        .dataset
                        .name ||
                    'The applicant';

                showRegistrationFlash(
                    'rejected',
                    name
                );

                confirmRejectRegistration.disabled =
                    true;

                try {

                    await submitRegistrationReview(
                        activeRegistrationRow,
                        'reject',
                        {
                            reason,

                            details:
                                rejectAdditionalDetails
                                    ?.value ||
                                ''
                        }
                    );

                    closeRegistrationModal(
                        sellerDetailsModal
                    );

                    closeRegistrationModal(
                        buyerDetailsModal
                    );

                    closeRejectRegistrationModal();

                } catch (
                    error
                ) {

                    showRegistrationFlash(
                        'error',
                        error.message
                    );

                } finally {

                    confirmRejectRegistration.disabled =
                        false;

                }

            }
        );

    }

    document
        .querySelectorAll(
            '.registration-reject'
        )
        .forEach(
            button => {

                button.addEventListener(
                    'click',
                    async function () {

                        if (
                            !activeRegistrationRow
                        ) {
                            return;
                        }

                        openRejectRegistrationModal();

                    }
                );

            }
        );

    document
        .querySelectorAll(
            '.registration-approve'
        )
        .forEach(
            button => {

                button.addEventListener(
                    'click',
                    async function () {

                        if (
                            !activeRegistrationRow
                        ) {
                            return;
                        }

                        const name =
                            activeRegistrationRow
                                .dataset
                                .name ||
                            'The applicant';

                        const confirmed =
                            window.confirm(
                                `Are you sure you want to approve ${name}'s registration?`
                            );

                        if (
                            !confirmed
                        ) {
                            return;
                        }

                        showRegistrationFlash(
                            'approved',
                            name
                        );

                        button.disabled =
                            true;

                        try {

                            await submitRegistrationReview(
                                activeRegistrationRow,
                                'approve'
                            );

                            closeRegistrationModal(
                                sellerDetailsModal
                            );

                            closeRegistrationModal(
                                buyerDetailsModal
                            );

                        } catch (
                            error
                        ) {

                            showRegistrationFlash(
                                'error',
                                error.message
                            );

                        } finally {

                            button.disabled =
                                false;

                        }

                    }
                );

            }
        );


    /* =========================================================
       APPROVED USERS ARCHIVE MODAL
    ========================================================== */

    const approvedUsersButton =
        document.getElementById(
            'approved-users-button'
        );

    const approvedUsersModal =
        document.getElementById(
            'approved-users-modal'
        );

    const closeApprovedUsers =
        document.getElementById(
            'close-approved-users'
        );

    const closeApprovedUsersBottom =
        document.getElementById(
            'close-approved-users-bottom'
        );

    if (
        approvedUsersButton &&
        approvedUsersModal
    ) {

        approvedUsersButton.addEventListener(
            'click',
            async function () {

                approvedUsersModal
                    .classList
                    .remove(
                        'hidden'
                    );

                approvedUsersModal
                    .classList
                    .add(
                        'flex'
                    );

                resetArchiveSearch(
                    approvedUsersSearch,
                    approvedUsersBody
                );

                try {

                    await loadArchiveUsers(
                        '/admin/registrations/approved/list',
                        approvedUsersBody,
                        'approved'
                    );

                } catch (
                    error
                ) {

                    console.error(
                        error
                    );

                }

                document.body
                    .classList
                    .add(
                        'overflow-hidden'
                    );

            }
        );

    }

    function closeApprovedModal() {

        if (
            !approvedUsersModal
        ) {
            return;
        }

        approvedUsersModal
            .classList
            .add(
                'hidden'
            );

        approvedUsersModal
            .classList
            .remove(
                'flex'
            );

        document.body
            .classList
            .remove(
                'overflow-hidden'
            );

    }

    if (
        closeApprovedUsers
    ) {

        closeApprovedUsers.addEventListener(
            'click',
            closeApprovedModal
        );

    }

    if (
        closeApprovedUsersBottom
    ) {

        closeApprovedUsersBottom.addEventListener(
            'click',
            closeApprovedModal
        );

    }

    if (
        approvedUsersModal
    ) {

        approvedUsersModal.addEventListener(
            'click',
            function (
                event
            ) {

                if (
                    event.target ===
                    approvedUsersModal
                ) {

                    closeApprovedModal();

                }

            }
        );

    }


    /* =========================================================
       REJECTED USERS ARCHIVE MODAL
    ========================================================== */

    const rejectedUsersButton =
        document.getElementById(
            'rejected-users-button'
        );

    const rejectedUsersModal =
        document.getElementById(
            'rejected-users-modal'
        );

    const closeRejectedUsers =
        document.getElementById(
            'close-rejected-users'
        );

    const closeRejectedUsersBottom =
        document.getElementById(
            'close-rejected-users-bottom'
        );

    if (
        rejectedUsersButton &&
        rejectedUsersModal
    ) {

        rejectedUsersButton.addEventListener(
            'click',
            async function () {

                rejectedUsersModal
                    .classList
                    .remove(
                        'hidden'
                    );

                rejectedUsersModal
                    .classList
                    .add(
                        'flex'
                    );

                resetArchiveSearch(
                    rejectedUsersSearch,
                    rejectedUsersBody
                );

                try {

                    await loadArchiveUsers(
                        '/admin/registrations/rejected/list',
                        rejectedUsersBody,
                        'rejected'
                    );

                } catch (
                    error
                ) {

                    console.error(
                        error
                    );

                }

                document.body
                    .classList
                    .add(
                        'overflow-hidden'
                    );

            }
        );

    }

    function closeRejectedModal() {

        if (
            !rejectedUsersModal
        ) {
            return;
        }

        rejectedUsersModal
            .classList
            .add(
                'hidden'
            );

        rejectedUsersModal
            .classList
            .remove(
                'flex'
            );

        document.body
            .classList
            .remove(
                'overflow-hidden'
            );

    }

    if (
        closeRejectedUsers
    ) {

        closeRejectedUsers.addEventListener(
            'click',
            closeRejectedModal
        );

    }

    if (
        closeRejectedUsersBottom
    ) {

        closeRejectedUsersBottom.addEventListener(
            'click',
            closeRejectedModal
        );

    }

    if (
        rejectedUsersModal
    ) {

        rejectedUsersModal.addEventListener(
            'click',
            function (
                event
            ) {

                if (
                    event.target ===
                    rejectedUsersModal
                ) {

                    closeRejectedModal();

                }

            }
        );

    }


    /* =========================================================
       ESC TO CLOSE MODALS
    ========================================================== */

    document.addEventListener(
        'keydown',
        function (
            event
        ) {

            if (
                event.key !==
                'Escape'
            ) {
                return;
            }

            if (
                sellerDetailsModal &&
                !sellerDetailsModal
                    .classList
                    .contains(
                        'hidden'
                    )
            ) {

                closeRegistrationModal(
                    sellerDetailsModal
                );

            }

            if (
                buyerDetailsModal &&
                !buyerDetailsModal
                    .classList
                    .contains(
                        'hidden'
                    )
            ) {

                closeRegistrationModal(
                    buyerDetailsModal
                );

            }

            if (
                approvedUsersModal &&
                !approvedUsersModal
                    .classList
                    .contains(
                        'hidden'
                    )
            ) {

                closeApprovedModal();

            }

            if (
                rejectedUsersModal &&
                !rejectedUsersModal
                    .classList
                    .contains(
                        'hidden'
                    )
            ) {

                closeRejectedModal();

            }

            if (
                rejectRegistrationModal &&
                !rejectRegistrationModal
                    .classList
                    .contains(
                        'hidden'
                    )
            ) {

                closeRejectRegistrationModal();

            }

        }
    );


    /* =========================================================
       PAGE LOAD UX
    ========================================================== */

    const pageContent =
        document.getElementById(
            'admin-content'
        );

    if (pageContent) {

        pageContent.classList.add(
            'opacity-0',
            'translate-y-2'
        );

        requestAnimationFrame(
            () => {

                setTimeout(
                    () => {

                        pageContent.classList.remove(
                            'opacity-0',
                            'translate-y-2'
                        );

                    },
                    80
                );

            }
        );

    }


    /* =========================================================
       INITIAL RENDER
    ========================================================== */

    renderTable();

});