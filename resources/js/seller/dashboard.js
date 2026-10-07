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
                        '.sales-chart-container'
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

        const dashboardDataElement =
            document.getElementById('sellerDashboardData');

        const dashboardData =
            dashboardDataElement
                ? JSON.parse(dashboardDataElement.textContent || '{}')
                : {};

        const salesData =
            dashboardData.sales || {};


        /* =================================================
           SALES CHART
        ================================================== */

        const salesCanvas =
            document.getElementById(
                'salesChart'
            );


        let salesChart =
            null;


        function renderSalesChart(
            period
        ) {

            if (!salesCanvas) {
                return;
            }


            const data =
                salesData[period];


            const ctx =
                salesCanvas.getContext(
                    '2d'
                );


            if (salesChart) {

                salesChart.destroy();

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


            salesChart =
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


        renderSalesChart(
            'month'
        );


        /* =================================================
           SALES PERIOD
        ================================================== */

        const salesPeriod =
            document.getElementById(
                'salesPeriod'
            );


        if (salesPeriod) {

            salesPeriod.addEventListener(
                'change',
                function () {

                    renderSalesChart(
                        this.value
                    );

                }
            );

        }


        /* =================================================
           STATUS CHART
        ================================================== */

        const statusCanvas =
            document.getElementById(
                'statusChart'
            );


        const statusTotal =
            document.getElementById(
                'statusTotal'
            );


        let statusChart =
            null;


        const statusData =
            dashboardData.statuses || {};

        const statusKeys = [
            'new_orders',
            'preparing',
            'to_ship',
            'in_transit',
            'delivered'
        ];


        function renderStatusChart(
            period
        ) {

            if (!statusCanvas) {
                return;
            }


            const periodData =
                statusData[period] || {};

            const data =
                statusKeys.map(
                    (status) => Number(periodData[status] || 0)
                );


            const total =
                data.reduce(
                    function (
                        sum,
                        value
                    ) {

                        return sum + value;

                    },
                    0
                );


            if (statusTotal) {

                statusTotal.textContent =
                    total.toLocaleString();

            }

            document.querySelectorAll('.status-count[data-status]').forEach(
                function (element) {
                    element.textContent =
                        Number(periodData[element.dataset.status] || 0).toLocaleString();
                }
            );


            const ctx =
                statusCanvas.getContext(
                    '2d'
                );


            if (statusChart) {

                statusChart.destroy();

            }


            statusChart =
                new Chart(
                    ctx,
                    {

                        type:
                            'doughnut',

                        data: {

                            labels: [

                                'New Orders',
                                'Preparing',
                                'To Ship',
                                'In Transit',
                                'Delivered'

                            ],

                            datasets: [

                                {

                                    data:
                                        data,

                                    backgroundColor: [

                                        '#FF6B76',
                                        '#FF9F43',
                                        '#FFCA5C',
                                        '#79A6E8',
                                        '#6FC29A'

                                    ],

                                    borderWidth:
                                        0,

                                    hoverOffset:
                                        8

                                }

                            ]

                        },

                        options: {

                            responsive:
                                true,

                            maintainAspectRatio:
                                false,

                            cutout:
                                '70%',

                            animation: {

                                duration:
                                    900,

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
                                        '#FFFFFF',

                                    titleColor:
                                        '#161616',

                                    bodyColor:
                                        '#444444',

                                    borderColor:
                                        '#E7E1DE',

                                    borderWidth:
                                        1,

                                    padding:
                                        10,

                                    titleFont: {

                                        family:
                                            'Poppins',

                                        size:
                                            12,

                                        weight:
                                            '600'

                                    },

                                    bodyFont: {

                                        family:
                                            'Poppins',

                                        size:
                                            12

                                    }

                                }

                            }

                        }

                    }
                );

        }


        renderStatusChart(
            'week'
        );


        /* =================================================
           STATUS PERIOD
        ================================================== */

        const statusPeriod =
            document.getElementById(
                'statusPeriod'
            );


        if (statusPeriod) {

            statusPeriod.addEventListener(
                'change',
                function () {

                    renderStatusChart(
                        this.value
                    );

                }
            );

        }


        /* =================================================
           SELLER SIDEBAR SYNC
        ================================================== */

        const sellerSidebar =
            document.getElementById(
                'sellerSidebar'
            );


        const sellerDashboardPage =
            document.getElementById(
                'sellerDashboardPage'
            );


        const sellerMenuButton =
            document.getElementById(
                'sellerNavbarMenuButton'
            );


        function syncSellerSidebarState() {

            if (
                !sellerSidebar ||
                !sellerDashboardPage
            ) {

                return;
            }


            const collapsed =
                sellerSidebar.classList.contains(
                    'sidebar-collapsed'
                )
                ||
                sellerSidebar.classList.contains(
                    'seller-sidebar-collapsed'
                )
                ||
                sellerSidebar.classList.contains(
                    'collapsed'
                );


            sellerDashboardPage.classList.toggle(
                'sidebar-collapsed',
                collapsed
            );


            document.body.classList.toggle(
                'seller-sidebar-collapsed',
                collapsed
            );


            setTimeout(
                function () {

                    if (salesChart) {

                        salesChart.resize();

                    }


                    if (statusChart) {

                        statusChart.resize();

                    }

                },
                320
            );

        }


        /* =================================================
           WATCH SIDEBAR STATE
        ================================================== */

        if (sellerSidebar) {

            const sidebarObserver =
                new MutationObserver(
                    function () {

                        syncSellerSidebarState();

                    }
                );


            sidebarObserver.observe(
                sellerSidebar,
                {
                    attributes:
                        true,

                    attributeFilter: [
                        'class'
                    ]
                }
            );


            syncSellerSidebarState();

        }


        /* =================================================
           BURGER BUTTON
        ================================================== */

        if (sellerMenuButton) {

            sellerMenuButton.addEventListener(
                'click',
                function () {

                    setTimeout(
                        function () {

                            syncSellerSidebarState();

                        },
                        30
                    );

                }
            );

        }


        /* =================================================
           WINDOW RESIZE
        ================================================== */

        let resizeTimer =
            null;


        window.addEventListener(
            'resize',
            function () {

                clearTimeout(
                    resizeTimer
                );


                resizeTimer =
                    setTimeout(
                        function () {

                            if (salesChart) {

                                salesChart.resize();

                            }


                            if (statusChart) {

                                statusChart.resize();

                            }


                            syncSellerSidebarState();

                        },
                        100
                    );

            }
        );

    }
);