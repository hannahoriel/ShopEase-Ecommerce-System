/* =========================================================
   MOVED FROM reports.blade.php — INLINE SCRIPT 1
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const reportsPage =
            document.getElementById(
                'sellerReportsPage'
            );

        const refreshButton =
            document.getElementById(
                'refreshReports'
            );


        /* =================================================
           SIDEBAR UX
           Mirrors seller dashboard content shift
        ================================================== */

        function syncSidebarState(
            collapsed
        ) {

            if (!reportsPage) {
                return;
            }

            reportsPage.classList.toggle(
                'sidebar-collapsed',
                Boolean(
                    collapsed
                )
            );
        }


        document.body.addEventListener(
            'seller-sidebar-state-changed',
            function (event) {

                syncSidebarState(
                    event.detail
                        ?.collapsed
                );
            }
        );


        const sidebar =
            document.getElementById(
                'sellerSidebar'
            );


        if (
            sidebar
            && reportsPage
        ) {

            syncSidebarState(
                sidebar.classList.contains(
                    'seller-sidebar-collapsed'
                )
            );


            const observer =
                new MutationObserver(
                    function () {

                        syncSidebarState(
                            sidebar.classList.contains(
                                'seller-sidebar-collapsed'
                            )
                        );
                    }
                );


            observer.observe(
                sidebar,
                {
                    attributes:
                        true,

                    attributeFilter:
                        [
                            'class'
                        ],
                }
            );
        }


        /* =================================================
           REFRESH UX
        ================================================== */

        refreshButton
            ?.addEventListener(
                'click',
                function () {

                    refreshButton.animate(
                        [
                            {
                                transform:
                                    'rotate(0deg)',
                            },

                            {
                                transform:
                                    'rotate(360deg)',
                            },
                        ],
                        {
                            duration:
                                420,

                            easing:
                                'ease',
                        }
                    );
                }
            );


        /* =================================================
           PAGINATION VISUAL UX
        ================================================== */

        document
            .querySelectorAll(
                '.report-page-button[data-page]'
            )
            .forEach(
                function (button) {

                    button.addEventListener(
                        'click',
                        function () {

                            document
                                .querySelectorAll(
                                    '.report-page-button[data-page]'
                                )
                                .forEach(
                                    function (item) {

                                        item.classList.remove(
                                            'active'
                                        );
                                    }
                                );


                            button.classList.add(
                                'active'
                            );
                        }
                    );
                }
            );

    }
);


/* =========================================================
   MOVED FROM reports.blade.php — INLINE SCRIPT 2
========================================================= */

Chart.defaults.font.family =
    'Poppins, sans-serif';

Chart.defaults.font.size =
    13;

Chart.defaults.color =
    '#6B7280';


document.addEventListener(
    'DOMContentLoaded',
    function () {

        setTimeout(
            function () {

                const salesChartContainer =
                    document.querySelector(
                        '.report-sales-chart-container'
                    );

                if (salesChartContainer) {

                    salesChartContainer.classList.add(
                        'admin-visible'
                    );

                }

            },
            150
        );


        /* =================================================
           SALES DATA
        ================================================== */

        const reportDataElement =
            document.getElementById('sellerReportsData');

        const reportConfigElement =
            document.getElementById('sellerReportsConfig');

        let reportData =
            reportDataElement
                ? JSON.parse(reportDataElement.textContent || '{}')
                : {};

        const reportConfig =
            reportConfigElement
                ? JSON.parse(reportConfigElement.textContent || '{}')
                : {};

        let reportSalesData = {};


        /* =================================================
           SALES CHART
        ================================================== */

        const salesCanvas =
            document.getElementById(
                'reportSalesChart'
            );


        let reportSalesChart =
            null;


        const reportStartDate =
            document.getElementById(
                'reportStartDate'
            );


        const reportEndDate =
            document.getElementById(
                'reportEndDate'
            );


        const reportRefreshButton =
            document.getElementById(
                'refreshReports'
            );


        const reportOrderRows =
            Array.from(
                document.querySelectorAll(
                    '.order-summary-table tbody tr'
                )
            );


        const reportOrderCount =
            document.querySelector(
                '.order-summary-count'
            );


        const originalOrderCountText =
            reportOrderCount
                ?.textContent
                ?.trim() ||
            '';


        function parseReportDate(
            value
        ) {

            if (!value) {
                return null;
            }


            const parsed =
                new Date(
                    value +
                    'T00:00:00'
                );


            return Number.isNaN(
                parsed.getTime()
            )
                ? null
                : parsed;

        }


        function getSelectedReportRange() {

            return {

                start:
                    parseReportDate(
                        reportStartDate
                            ?.value
                    ),

                end:
                    parseReportDate(
                        reportEndDate
                            ?.value
                    ),

            };

        }


        function dateIsInsideReportRange(
            date
        ) {

            const range =
                getSelectedReportRange();


            if (
                range.start &&
                date <
                    range.start
            ) {

                return false;

            }


            if (
                range.end &&
                date >
                    range.end
            ) {

                return false;

            }


            return true;

        }


        function validateReportRange() {

            if (
                !reportStartDate ||
                !reportEndDate
            ) {

                return true;

            }

            reportEndDate.setCustomValidity('');

            if (
                reportStartDate.value &&
                reportEndDate.value &&
                reportStartDate.value > reportEndDate.value
            ) {
                reportEndDate.setCustomValidity(
                    'End date must be on or after the start date.'
                );
                reportEndDate.reportValidity();

                return false;
            }

            return true;
        }

            function formatReportCurrency(value) {
                return '₱' + Number(value || 0).toLocaleString('en-PH', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2,
                });
            }

            function updateReportChange(elementId, change) {
                const element = document.getElementById(elementId);
                if (!element) return;

                const arrows = { up: '↑', down: '↓', flat: '→' };
                element.textContent = (arrows[change?.direction] || '→') + (change?.value || '0%');
                element.style.color = change?.direction === 'down'
                    ? '#B42318'
                    : change?.direction === 'up'
                        ? '#11951B'
                        : '#777777';
            }

            function renderReportData() {
                const stats = reportData.stats || {};
                const performance = reportData.performance || {};
                const orders = reportData.orders || {};

                document.getElementById('reportTotalSales').textContent =
                    formatReportCurrency(stats.total_sales);
                document.getElementById('reportTotalOrders').textContent =
                    Number(stats.total_orders || 0).toLocaleString();
                document.getElementById('reportTotalProfit').textContent =
                    formatReportCurrency(stats.total_profit);
                document.getElementById('reportGrossProfitMargin').textContent =
                    Number(stats.gross_profit_margin || 0).toFixed(1) + '%';
                updateReportChange('reportTotalSalesChange', stats.changes?.total_sales);
                updateReportChange('reportTotalOrdersChange', stats.changes?.total_orders);
                updateReportChange('reportTotalProfitChange', stats.changes?.total_profit);
                updateReportChange('reportGrossProfitMarginChange', stats.changes?.gross_profit_margin);
                document.querySelectorAll('.report-comparison-label').forEach((element) => {
                    element.textContent = stats.comparison_label || 'from yesterday';
                });

                document.getElementById('reportPerformanceOrders').textContent =
                    Number(performance.total_orders || 0).toLocaleString();
                document.getElementById('reportPerformanceSales').textContent =
                    formatReportCurrency(performance.gross_sales);
                document.getElementById('reportPerformanceCommission').textContent =
                    '-' + formatReportCurrency(performance.admin_commission);
                document.getElementById('reportPerformanceProfit').textContent =
                    formatReportCurrency(performance.profit);

                const orderRows = document.getElementById('reportOrderRows');
                orderRows.replaceChildren();
                if (!orders.data?.length) {
                    const row = document.createElement('tr');
                    row.className = 'report-empty-row';

                    const empty = document.createElement('td');
                    empty.colSpan = 6;

                    const state = document.createElement('div');
                    state.className = 'reports-empty-state order-summary-empty-state';

                    const title = document.createElement('strong');
                    title.textContent = 'No completed orders found.';

                    const subtitle = document.createElement('span');
                    subtitle.textContent = 'Completed orders will appear here for the selected period.';

                    state.append(title, subtitle);
                    empty.append(state);
                    row.append(empty);
                    orderRows.append(row);
                } else {
                    orders.data.forEach((order) => {
                        const row = document.createElement('tr');
                        const date = order.date
                            ? new Date(order.date + 'T00:00:00').toLocaleDateString('en-US', {
                                month: 'short',
                                day: '2-digit',
                                year: 'numeric',
                            })
                            : '—';

                        [
                            { value: '#' + order.id, className: 'order-id-cell' },
                            { value: date },
                            { value: order.payment_method || '—' },
                            { value: formatReportCurrency(order.sales) },
                            { value: formatReportCurrency(order.commission) },
                            { value: formatReportCurrency(order.profit) },
                        ].forEach((cell) => {
                            const td = document.createElement('td');
                            td.textContent = cell.value;
                            if (cell.className) td.className = cell.className;
                            row.append(td);
                        });
                        orderRows.append(row);
                    });
                }

                const topProducts = document.getElementById('reportTopProducts');
                topProducts.replaceChildren();
                if (!reportData.top_products?.length) {
                    const empty = document.createElement('div');
                    empty.className = 'reports-empty-state top-products-empty-state';

                    const title = document.createElement('strong');
                    title.textContent = 'No product sales found.';

                    const subtitle = document.createElement('span');
                    subtitle.textContent = 'Product sales will appear here for the selected period.';

                    empty.append(title, subtitle);
                    topProducts.append(empty);
                } else {
                    reportData.top_products.forEach((product) => {
                        const row = document.createElement('div');
                        row.className = 'top-product-row';
                        const info = document.createElement('div');
                        info.className = 'top-product-info';
                        const image = document.createElement('div');
                        image.className = 'top-product-image';
                        if (product.photo) {
                            const img = document.createElement('img');
                            img.src = product.photo;
                            img.alt = '';
                            image.append(img);
                        } else {
                            image.textContent = (product.name || '?').slice(0, 1).toUpperCase();
                        }
                        const name = document.createElement('span');
                        name.className = 'top-product-name';
                        name.textContent = product.name;
                        info.append(image, name);
                        const quantity = document.createElement('div');
                        quantity.className = 'top-product-qty';
                        quantity.textContent = Number(product.quantity || 0).toLocaleString();
                        row.append(info, quantity);
                        topProducts.append(row);
                    });
                }

                const count = document.getElementById('reportOrderCount');
                count.textContent = orders.total
                    ? `Showing ${orders.from}–${orders.to} out of ${orders.total} entries`
                    : 'Showing 0 out of 0 entries';

                const start = reportStartDate.value;
                const end = reportEndDate.value;
                const title = document.getElementById('reportOrderSummaryTitle');
                title.textContent = start && end
                    ? `Order Summary (${start} - ${end})`
                    : start
                        ? `Order Summary (From ${start})`
                        : end
                            ? `Order Summary (Through ${end})`
                            : 'Order Summary (All dates)';

                renderReportPagination(orders);
                reportSalesData[reportData.filters?.period || 'month'] = reportData.sales_chart;
                renderReportSalesChart(reportData.filters?.period || 'month');
            }

            function renderReportPagination(orders) {
                const pages = document.getElementById('reportPaginationPages');
                pages.replaceChildren();
                const current = Number(orders.current_page || 1);
                const last = Number(orders.last_page || 1);
                const first = Math.max(1, current - 2);
                const final = Math.min(last, current + 2);

                for (let page = first; page <= final; page += 1) {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'report-page-button' + (page === current ? ' active' : '');
                    button.dataset.page = String(page);
                    button.textContent = String(page);
                    pages.append(button);
                }

                document.querySelector('[data-page-direction="previous"]').disabled = current <= 1;
                document.querySelector('[data-page-direction="next"]').disabled = current >= last;
                document.getElementById('reportItemsPerPage').value = String(orders.per_page || 7);
            }

            async function loadReportData(page = 1) {
                if (!validateReportRange()) return;

                const params = new URLSearchParams({
                    period: document.getElementById('reportSalesPeriod').value,
                    page: String(page),
                    per_page: document.getElementById('reportItemsPerPage').value,
                });
                if (reportStartDate.value) params.set('start_date', reportStartDate.value);
                if (reportEndDate.value) params.set('end_date', reportEndDate.value);

                const feedback = document.getElementById('reportFeedback');
                feedback.textContent = '';
                try {
                    const response = await fetch(`${reportConfig.dataUrl}?${params}`, {
                        credentials: 'same-origin',
                        headers: { Accept: 'application/json' },
                    });
                    const result = await response.json();
                    if (!response.ok) {
                        throw new Error(result.message || 'Unable to load report data.');
                    }

                    reportData = result;
                    renderReportData();
                } catch (error) {
                    feedback.textContent = error.message || 'Unable to load report data.';
                }
            }
        function filterReportSeriesByDate(
            data
        ) {

            if (
                !data?.dates ||
                !Array.isArray(
                    data.dates
                )
            ) {

                return data;

            }


            const filtered = {

                labels: [],
                previous: [],
                current: [],

            };


            data.dates.forEach(
                function (
                    rawDate,
                    index
                ) {

                    const date =
                        parseReportDate(
                            rawDate
                        );


                    if (
                        !date ||
                        !dateIsInsideReportRange(
                            date
                        )
                    ) {

                        return;

                    }


                    filtered.labels.push(
                        data.labels[
                            index
                        ]
                    );


                    filtered.previous.push(
                        data.previous[
                            index
                        ]
                    );


                    filtered.current.push(
                        data.current[
                            index
                        ]
                    );

                }
            );


            return filtered;

        }


        function filterOrderSummaryByDate() {

            if (
                !reportOrderRows.length
            ) {

                return;
            }


            const hasActiveDateFilter =
                Boolean(
                    reportStartDate
                        ?.value ||
                    reportEndDate
                        ?.value
                );


            let visibleRows =
                0;


            reportOrderRows.forEach(
                function (
                    row
                ) {

                    const dateText =
                        row.children[
                            1
                        ]
                            ?.textContent
                            ?.trim();


                    const rowDate =
                        dateText
                            ? new Date(
                                dateText
                              )
                            : null;


                    const isValidDate =
                        rowDate &&
                        !Number.isNaN(
                            rowDate.getTime()
                        );


                    const visible =
                        !hasActiveDateFilter ||
                        (
                            isValidDate &&
                            dateIsInsideReportRange(
                                rowDate
                            )
                        );


                    row.style.display =
                        visible
                            ? ''
                            : 'none';


                    if (visible) {

                        visibleRows +=
                            1;

                    }

                }
            );


            if (!reportOrderCount) {
                return;
            }


            if (
                !hasActiveDateFilter
            ) {

                reportOrderCount.textContent =
                    originalOrderCountText;

                return;

            }


            reportOrderCount.textContent =
                'Showing ' +
                visibleRows +
                ' out of ' +
                reportOrderRows.length +
                ' matching entries';

        }


        function applyReportDateFilter() {

            loadReportData(1);

        }


        function renderReportSalesChart(
            period
        ) {

            if (!salesCanvas) {
                return;
            }


            const sourceData =
                reportSalesData[period];


            if (!sourceData) {
                return;
            }


            const data =
                filterReportSeriesByDate(
                    sourceData
                );


            const ctx =
                salesCanvas.getContext(
                    '2d'
                );


            if (reportSalesChart) {

                reportSalesChart.destroy();

            }


            const previousGradient =
                ctx.createLinearGradient(
                    0,
                    0,
                    0,
                    260
                );


            previousGradient.addColorStop(
                0,
                'rgba(93, 11, 17, 0.42)'
            );


            previousGradient.addColorStop(
                1,
                'rgba(93, 11, 17, 0)'
            );


            const currentGradient =
                ctx.createLinearGradient(
                    0,
                    0,
                    0,
                    260
                );


            currentGradient.addColorStop(
                0,
                'rgba(224, 0, 0, 0.48)'
            );


            currentGradient.addColorStop(
                1,
                'rgba(224, 0, 0, 0)'
            );


            reportSalesChart =
                new Chart(
                    ctx,
                    {

                        type:
                            'line',


                        data: {

                            labels:
                                data.labels,


                            datasets: [

                                {

                                    label:
                                        'Previous Period',

                                    data:
                                        data.previous,

                                    borderColor:
                                        '#5D0B11',

                                    backgroundColor:
                                        previousGradient,

                                    borderWidth:
                                        2,

                                    fill:
                                        true,

                                    spanGaps:
                                        true,

                                    tension:
                                        0.4,

                                    pointRadius:
                                        3,

                                    pointHoverRadius:
                                        7,

                                    pointBackgroundColor:
                                        '#FFFFFF',

                                    pointBorderColor:
                                        '#5D0B11',

                                    pointBorderWidth:
                                        2,

                                    pointHoverBorderWidth:
                                        3

                                },


                                {

                                    label:
                                        'This Period',

                                    data:
                                        data.current,

                                    borderColor:
                                        '#E00000',

                                    backgroundColor:
                                        currentGradient,

                                    borderWidth:
                                        2,

                                    fill:
                                        true,

                                    spanGaps:
                                        true,

                                    tension:
                                        0.4,

                                    pointRadius:
                                        3,

                                    pointHoverRadius:
                                        7,

                                    pointBackgroundColor:
                                        '#FFFFFF',

                                    pointBorderColor:
                                        '#E00000',

                                    pointBorderWidth:
                                        2,

                                    pointHoverBorderWidth:
                                        3

                                }

                            ]

                        },


                        options: {

                            responsive:
                                true,

                            maintainAspectRatio:
                                false,


                            interaction: {

                                intersect:
                                    false,

                                mode:
                                    'index'

                            },


                            animation: {

                                duration:
                                    1600,

                                easing:
                                    'easeOutQuart'

                            },


                            plugins: {

                                legend: {

                                    display:
                                        false

                                },


                                tooltip: {

                                    backgroundColor:
                                        '#5C1414',

                                    titleColor:
                                        '#FFFFFF',

                                    bodyColor:
                                        '#FFFFFF',

                                    padding:
                                        12,

                                    cornerRadius:
                                        10,

                                    displayColors:
                                        true,

                                    boxPadding:
                                        4,


                                    titleFont: {

                                        family:
                                            'Poppins',

                                        size:
                                            13,

                                        weight:
                                            'bold'

                                    },


                                    bodyFont: {

                                        family:
                                            'Poppins',

                                        size:
                                            13

                                    },


                                    callbacks: {

                                        label:
                                            function (
                                                context
                                            ) {

                                                return (
                                                    ' ₱' +
                                                    Number(
                                                        context.parsed.y
                                                    ).toLocaleString(
                                                        'en-PH',
                                                        {
                                                            minimumFractionDigits:
                                                                2
                                                        }
                                                    )
                                                );

                                            }

                                    }

                                }

                            },


                            scales: {

                                y: {

                                    beginAtZero:
                                        true,


                                    border: {

                                        display:
                                            false

                                    },


                                    grid: {

                                        color:
                                            'rgba(0,0,0,0.05)',

                                        drawTicks:
                                            false

                                    },


                                    ticks: {

                                        color:
                                            '#777777',

                                        maxTicksLimit:
                                            6,


                                        font: {

                                            family:
                                                'Poppins',

                                            size:
                                                13

                                        },


                                        padding:
                                            7,


                                        callback:
                                            function (
                                                value,
                                                index,
                                                ticks
                                            ) {

                                                const amount =
                                                    Number(value);

                                                if (!Number.isFinite(amount)) {

                                                    return '';
                                                }

                                                const highestTick =
                                                    Number(ticks[ticks.length - 1]?.value || 0);

                                                if (Math.abs(highestTick) >= 1000) {

                                                    return '₱' + (amount / 1000).toLocaleString('en-PH', {
                                                        minimumFractionDigits: 1,
                                                        maximumFractionDigits: 1
                                                    }) + 'k';
                                                }

                                                return '₱' + amount.toLocaleString('en-PH', {
                                                    maximumFractionDigits: 2
                                                });

                                            }

                                    }

                                },


                                x: {

                                    border: {

                                        display:
                                            false

                                    },


                                    grid: {

                                        display:
                                            false

                                    },


                                    ticks: {

                                        color:
                                            '#777777',


                                        font: {

                                            family:
                                                'Poppins',

                                            size:
                                                13

                                        },


                                        maxRotation:
                                            0

                                    }

                                }

                            }

                        }

                    }
                );

        }


        renderReportSalesChart(
            reportData.filters?.period || 'month'
        );


        /* =================================================
           SALES PERIOD
        ================================================== */

        const salesPeriod =
            document.getElementById(
                'reportSalesPeriod'
            );


        if (salesPeriod) {

            salesPeriod.addEventListener(
                'change',
                function () {

                    loadReportData(1);

                }
            );

        }

        document.getElementById('reportItemsPerPage')
            ?.addEventListener('change', function () {
                loadReportData(1);
            });

        document.querySelector('.order-summary-pagination')
            ?.addEventListener('click', function (event) {
                const button = event.target.closest('.report-page-button');
                if (!button || button.disabled) return;

                const current = Number(reportData.orders?.current_page || 1);
                const page = button.dataset.page
                    ? Number(button.dataset.page)
                    : button.dataset.pageDirection === 'previous'
                        ? current - 1
                        : current + 1;
                loadReportData(page);
            });


        /*
         * Date range filter.
         * Only behavior is added; existing UI remains unchanged.
         */
        reportStartDate
            ?.addEventListener(
                'change',
                applyReportDateFilter
            );


        reportEndDate
            ?.addEventListener(
                'change',
                applyReportDateFilter
            );


        reportRefreshButton?.addEventListener('click', applyReportDateFilter);

        reportSalesData[reportData.filters?.period || 'month'] = reportData.sales_chart;
        renderReportData();


        /* =================================================
           RESPONSIVE / SIDEBAR RESIZE
        ================================================== */

        function resizeReportSalesChart() {

            if (!reportSalesChart) {
                return;
            }


            window.setTimeout(
                function () {

                    reportSalesChart.resize();

                },
                330
            );

        }


        document.body.addEventListener(
            'seller-sidebar-state-changed',
            resizeReportSalesChart
        );


        window.addEventListener(
            'resize',
            resizeReportSalesChart
        );

    }
);