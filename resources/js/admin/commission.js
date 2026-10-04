const commissionConfigElement =
    document.getElementById('commissionConfig');

const commissionConfig = commissionConfigElement
    ? JSON.parse(commissionConfigElement.textContent)
    : {
        rows: [],
        totalEntries: 0
    };

document.addEventListener('DOMContentLoaded', function () {

    /* =========================================================
       DATA
    ========================================================== */
    const commissionRows =
        commissionConfig.rows || [];

    const originalTotalEntries =
        Number(commissionConfig.totalEntries || 0);

    /* =========================================================
       ELEMENTS
    ========================================================== */
    const search =
        document.getElementById('commission-search');

    const fromWrap =
        document.getElementById('commission-from-wrap');

    const fromTrigger =
        document.getElementById('commission-from-trigger');

    const fromDate =
        document.getElementById('commission-from-date');

    const fromText =
        document.getElementById('commission-from-text');

    const toWrap =
        document.getElementById('commission-to-wrap');

    const toTrigger =
        document.getElementById('commission-to-trigger');

    const toDate =
        document.getElementById('commission-to-date');

    const toText =
        document.getElementById('commission-to-text');

    const reload =
        document.getElementById('commission-reload');

    const tbody =
        document.getElementById('commission-table-body');

    const empty =
        document.getElementById('commission-empty');

    const count =
        document.getElementById('commission-count');

    const prev =
        document.getElementById('commission-prev');

    const next =
        document.getElementById('commission-next');

    const pages =
        document.getElementById('commission-pages');

    const items =
        document.getElementById('commission-items');

    /* =========================================================
       STATE
    ========================================================== */
    let currentPage = 1;
    let itemsPerPage = Number(items.value);

    /* =========================================================
       FORMATTERS
    ========================================================== */
    function formatMoney(value) {
        return new Intl.NumberFormat('en-PH', {
            style: 'currency',
            currency: 'PHP',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(Number(value || 0));
    }

    function formatCompactAmount(value) {
        return `₱ ${new Intl.NumberFormat('en-PH', {
            maximumFractionDigits: 0
        }).format(Number(value || 0))}`;
    }

    function formatDateInput(value) {
        if (!value) {
            return '';
        }

        const [year, month, day] =
            value.split('-').map(Number);

        const date =
            new Date(
                year,
                month - 1,
                day
            );

        return new Intl.DateTimeFormat('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        }).format(date);
    }

    function normalize(value) {
        return String(value || '')
            .trim()
            .toLowerCase();
    }

    /* =========================================================
       DATE PICKER
    ========================================================== */
    function openDatePicker(input, wrap) {
        wrap.classList.add('is-open');

        try {
            if (typeof input.showPicker === 'function') {
                input.showPicker();
            } else {
                input.focus();
                input.click();
            }
        } catch (error) {
            input.focus();
            input.click();
        }
    }

    function syncDateLabel(
        input,
        label,
        emptyLabel
    ) {
        if (input.value) {
            label.textContent =
                formatDateInput(input.value);

            label.classList.add('has-value');
        } else {
            label.textContent =
                emptyLabel;

            label.classList.remove('has-value');
        }
    }

    fromTrigger.addEventListener(
        'click',
        function () {
            openDatePicker(
                fromDate,
                fromWrap
            );
        }
    );

    toTrigger.addEventListener(
        'click',
        function () {
            openDatePicker(
                toDate,
                toWrap
            );
        }
    );

    fromDate.addEventListener(
        'change',
        function () {
            fromWrap.classList.remove('is-open');

            syncDateLabel(
                fromDate,
                fromText,
                'Select From Date'
            );

            if (
                fromDate.value &&
                toDate.value &&
                fromDate.value > toDate.value
            ) {
                toDate.value =
                    fromDate.value;

                syncDateLabel(
                    toDate,
                    toText,
                    'Select To Date'
                );
            }

            currentPage = 1;
            render();
        }
    );

    toDate.addEventListener(
        'change',
        function () {
            toWrap.classList.remove('is-open');

            syncDateLabel(
                toDate,
                toText,
                'Select To Date'
            );

            if (
                fromDate.value &&
                toDate.value &&
                toDate.value < fromDate.value
            ) {
                fromDate.value =
                    toDate.value;

                syncDateLabel(
                    fromDate,
                    fromText,
                    'Select From Date'
                );
            }

            currentPage = 1;
            render();
        }
    );

    [fromDate, toDate].forEach(input => {
        input.addEventListener(
            'blur',
            function () {
                fromWrap.classList.remove('is-open');
                toWrap.classList.remove('is-open');
            }
        );
    });

    /* =========================================================
       FILTERING
    ========================================================== */
    function getFilteredRows() {
        const query =
            normalize(search.value);

        const start =
            fromDate.value || '';

        const end =
            toDate.value || '';

        return commissionRows.filter(row => {

            const searchable =
                normalize(
                    [
                        row.order_id,
                        row.seller,
                        row.seller_owner,
                        row.date_label,
                        row.time
                    ].join(' ')
                );

            const matchesSearch =
                !query ||
                searchable.includes(query);

            const matchesFrom =
                !start ||
                row.date >= start;

            const matchesTo =
                !end ||
                row.date <= end;

            return (
                matchesSearch &&
                matchesFrom &&
                matchesTo
            );
        });
    }

    /* =========================================================
       TABLE
    ========================================================== */
    function renderRows(filteredRows) {

        const startIndex =
            (currentPage - 1) * itemsPerPage;

        const pageRows =
            filteredRows.slice(
                startIndex,
                startIndex + itemsPerPage
            );

        tbody.innerHTML =
            pageRows.map(row => `
                <tr>
                    <td>
                        <div class="commission-date-cell">
                            ${row.date_label}<br>
                            ${row.time}
                        </div>
                    </td>

                    <td>
                        <span class="commission-order-id">
                            ${row.order_id}
                        </span>
                    </td>

                    <td>
                        <div class="commission-seller-cell">
                            <span class="commission-seller-avatar"></span>

                            <span class="commission-seller-copy">
                                <span class="commission-seller-name">
                                    ${row.seller}
                                </span>

                                <span class="commission-seller-owner">
                                    ${row.seller_owner}
                                </span>
                            </span>
                        </div>
                    </td>

                    <td>
                        <span class="commission-money">
                            ${formatCompactAmount(row.order_amount)}
                        </span>
                    </td>

                    <td>
                        <span class="commission-money">
                            ${formatMoney(row.commission)}
                        </span>
                    </td>
                </tr>
            `).join('');

        const hasRows =
            pageRows.length > 0;

        tbody.style.display =
            hasRows ? '' : 'none';

        empty.style.display =
            hasRows ? 'none' : 'block';
    }

    /* =========================================================
       PAGINATION
    ========================================================== */
    function getPageNumbers(totalPages) {

        if (totalPages <= 5) {
            return Array.from(
                { length: totalPages },
                (_, index) => index + 1
            );
        }

        if (currentPage <= 3) {
            return [1, 2, 3, 4, totalPages];
        }

        if (currentPage >= totalPages - 2) {
            return [
                1,
                totalPages - 3,
                totalPages - 2,
                totalPages - 1,
                totalPages
            ];
        }

        return [
            1,
            currentPage - 1,
            currentPage,
            currentPage + 1,
            totalPages
        ];
    }

    function renderPagination(filteredRows) {

        const totalPages =
            Math.max(
                1,
                Math.ceil(
                    filteredRows.length /
                    itemsPerPage
                )
            );

        if (currentPage > totalPages) {
            currentPage = totalPages;
        }

        prev.disabled =
            currentPage === 1;

        next.disabled =
            currentPage === totalPages;

        const pageNumbers =
            getPageNumbers(totalPages);

        pages.innerHTML = '';

        let previousPageNumber = 0;

        pageNumbers.forEach(pageNumber => {

            if (
                previousPageNumber &&
                pageNumber - previousPageNumber > 1
            ) {
                const ellipsis =
                    document.createElement('span');

                ellipsis.textContent = '…';

                ellipsis.className =
                    'px-1 text-[11px] text-[#8C8784]';

                pages.appendChild(
                    ellipsis
                );
            }

            const button =
                document.createElement('button');

            button.type = 'button';

            button.className =
                'commission-page-button' +
                (
                    pageNumber === currentPage
                        ? ' active'
                        : ''
                );

            button.textContent =
                pageNumber;

            button.addEventListener(
                'click',
                function () {
                    currentPage =
                        pageNumber;

                    render();
                }
            );

            pages.appendChild(
                button
            );

            previousPageNumber =
                pageNumber;
        });
    }

    function renderCount(filteredRows) {

        const visibleStart =
            filteredRows.length
                ? (
                    (currentPage - 1) *
                    itemsPerPage
                ) + 1
                : 0;

        const visibleEnd =
            Math.min(
                currentPage * itemsPerPage,
                filteredRows.length
            );

        const hasActiveFilter =
            Boolean(
                search.value.trim() ||
                fromDate.value ||
                toDate.value
            );

        const total =
            hasActiveFilter
                ? filteredRows.length
                : originalTotalEntries;

        const showing =
            filteredRows.length
                ? visibleEnd - visibleStart + 1
                : 0;

        count.textContent =
            `Showing ${showing} out of ${total.toLocaleString()} entries`;
    }

    /* =========================================================
       MAIN RENDER
    ========================================================== */
    function render() {

        const filteredRows =
            getFilteredRows();

        const totalPages =
            Math.max(
                1,
                Math.ceil(
                    filteredRows.length /
                    itemsPerPage
                )
            );

        if (currentPage > totalPages) {
            currentPage =
                totalPages;
        }

        renderRows(
            filteredRows
        );

        renderPagination(
            filteredRows
        );

        renderCount(
            filteredRows
        );
    }

    /* =========================================================
       EVENTS
    ========================================================== */
    search.addEventListener(
        'input',
        function () {
            currentPage = 1;
            render();
        }
    );

    items.addEventListener(
        'change',
        function () {
            itemsPerPage =
                Number(items.value);

            currentPage = 1;
            render();
        }
    );

    prev.addEventListener(
        'click',
        function () {
            if (currentPage <= 1) {
                return;
            }

            currentPage--;
            render();
        }
    );

    next.addEventListener(
        'click',
        function () {

            const filteredRows =
                getFilteredRows();

            const totalPages =
                Math.max(
                    1,
                    Math.ceil(
                        filteredRows.length /
                        itemsPerPage
                    )
                );

            if (currentPage >= totalPages) {
                return;
            }

            currentPage++;
            render();
        }
    );

    reload.addEventListener(
        'click',
        function () {

            search.value = '';

            fromDate.value = '';

            toDate.value = '';

            syncDateLabel(
                fromDate,
                fromText,
                'Select From Date'
            );

            syncDateLabel(
                toDate,
                toText,
                'Select To Date'
            );

            items.value = '7';
            itemsPerPage = 7;
            currentPage = 1;

            reload.classList.add(
                'is-spinning'
            );

            window.setTimeout(
                function () {
                    reload.classList.remove(
                        'is-spinning'
                    );
                },
                520
            );

            render();
        }
    );

    /* =========================================================
       INITIALIZE
    ========================================================== */
    syncDateLabel(
        fromDate,
        fromText,
        'Select From Date'
    );

    syncDateLabel(
        toDate,
        toText,
        'Select To Date'
    );

    render();
});