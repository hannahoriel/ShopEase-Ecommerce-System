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

        const reportSalesData = {

            month: {

                dates: [
                    '2026-05-01',
                    '2026-05-06',
                    '2026-05-11',
                    '2026-05-16',
                    '2026-05-21',
                    '2026-05-26',
                    '2026-05-31'
                ],

                labels: [
                    'May 1',
                    'May 6',
                    'May 11',
                    'May 16',
                    'May 21',
                    'May 26',
                    'May 31'
                ],

                previous: [
                    2400,
                    5600,
                    2300,
                    5500,
                    4200,
                    5800,
                    14000
                ],

                current: [
                    4700,
                    8900,
                    5100,
                    13200,
                    7900,
                    11800,
                    21600
                ]

            },


            week: {

                dates: [
                    '2026-05-25',
                    '2026-05-26',
                    '2026-05-27',
                    '2026-05-28',
                    '2026-05-29',
                    '2026-05-30',
                    '2026-05-31'
                ],

                labels: [
                    'Mon',
                    'Tue',
                    'Wed',
                    'Thu',
                    'Fri',
                    'Sat',
                    'Sun'
                ],

                previous: [
                    1800,
                    2500,
                    2100,
                    2900,
                    3200,
                    4100,
                    3900
                ],

                current: [
                    2600,
                    3400,
                    2950,
                    4300,
                    4700,
                    5900,
                    5200
                ]

            },


            year: {

                dates: [
                    '2026-01-01',
                    '2026-03-01',
                    '2026-05-01',
                    '2026-07-01',
                    '2026-09-01',
                    '2026-11-01'
                ],

                labels: [
                    'Jan',
                    'Mar',
                    'May',
                    'Jul',
                    'Sep',
                    'Nov'
                ],

                previous: [
                    12000,
                    15000,
                    18000,
                    21000,
                    23000,
                    27000
                ],

                current: [
                    15000,
                    19000,
                    24000,
                    26000,
                    30000,
                    35000
                ]

            }

        };


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


            reportEndDate.setCustomValidity(
                ''
            );


            if (
                reportStartDate.value &&
                reportEndDate.value &&
                reportStartDate.value >
                    reportEndDate.value
            ) {

                reportEndDate.setCustomValidity(
                    'End date must be on or after the start date.'
                );


                reportEndDate.reportValidity();


                return false;

            }


            return true;

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

            if (
                !validateReportRange()
            ) {

                return;

            }


            filterOrderSummaryByDate();


            renderReportSalesChart(
                document.getElementById(
                    'reportSalesPeriod'
                )
                    ?.value ||
                'month'
            );

        }


        function renderReportSalesChart(
            period
        ) {

            if (!salesCanvas) {
                return;
            }


            const sourceData =
                reportSalesData[
                    period
                ];


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
                                                value
                                            ) {

                                                if (
                                                    value === 0
                                                ) {

                                                    return '0';

                                                }


                                                return (
                                                    '₱' +
                                                    (
                                                        value /
                                                        1000
                                                    ) +
                                                    'k'
                                                );

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
            'month'
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

                    renderReportSalesChart(
                        this.value
                    );

                }
            );

        }


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


        reportRefreshButton
            ?.addEventListener(
                'click',
                applyReportDateFilter
            );


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