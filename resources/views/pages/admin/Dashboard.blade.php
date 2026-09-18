@extends('layouts.admin')
@section('page-title', 'Dashboard')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');

    #admin-content,
    #admin-content * {
        font-family: 'Poppins', sans-serif;
    }
</style>

<div
    id="admin-content"
    class="ml-60 pt-[95px] pl-5 pb-7 min-h-screen transition-all duration-300"
>

    <!-- Welcome -->
    <h2 class="text-[21px] font-semibold text-black mb-5">
        Welcome, Admin!
    </h2>


    <!-- ==================== STAT CARDS ==================== -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-5">

        @php
            $stats = [
                [
                    'icon' => 'pending.png',
                    'value' => '236',
                    'label' => 'Pending Registrations',
                    'change' => '12%'
                ],
                [
                    'icon' => 'active-users.png',
                    'value' => '1,245',
                    'label' => 'Active Users',
                    'change' => '8%'
                ],
                [
                    'icon' => 'active-sellers.png',
                    'value' => '352',
                    'label' => 'Active Sellers',
                    'change' => '5%'
                ],
                [
                    'icon' => 'total-commision.png',
                    'value' => '₱45,680.00',
                    'label' => 'Total Commission',
                    'change' => '5%'
                ],
            ];
        @endphp

        @foreach ($stats as $stat)

            <div class="dashboard-stat-card">

                <div class="dashboard-stat-main">

                    <!-- PNG ICON -->
                    <img
                        src="{{ asset('icons/admin/dashboard/body/' . $stat['icon']) }}"
                        alt="{{ $stat['label'] }}"
                        class="dashboard-stat-icon"
                    >

                    <!-- CONTENT -->
                    <div class="dashboard-stat-content">

                        <div class="dashboard-stat-number">
                            {{ $stat['value'] }}
                        </div>

                        <div class="dashboard-stat-label">
                            {{ $stat['label'] }}
                        </div>

                    </div>

                </div>

                <!-- GROWTH -->
                <div class="dashboard-stat-growth">

                    <span class="dashboard-growth-arrow">
                        ↑
                    </span>

                    <strong>
                        {{ $stat['change'] }}
                    </strong>

                    from yesterday

                </div>

            </div>

        @endforeach

    </div>


    <style>
        /* =========================================================
           DASHBOARD STAT CARDS
           Matches Seller Compliance stat-card structure/format.
        ========================================================== */

        .dashboard-stat-card {
            min-height: 108px;
            background: #FFFFFF;
            border: 1px solid #F0E9E6;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(42, 20, 15, 0.05);
            padding: 16px;
            box-sizing: border-box;
        }

        .dashboard-stat-main {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /*
         * The PNG contains its own visual background.
         * No extra icon wrapper is added.
         */
        .dashboard-stat-icon {
            width: 40px;
            height: 40px;
            flex: 0 0 40px;
            object-fit: contain;
            display: block;
        }

        .dashboard-stat-content {
            min-width: 0;
        }

        .dashboard-stat-number {
            font-size: 23px;
            line-height: 1;
            font-weight: 600;
            color: #17120F;
        }

        .dashboard-stat-label {
            margin-top: 5px;
            font-size: 13px;
            line-height: 1.15;
            font-weight: 400;
            color: #8C8784;
        }

        .dashboard-stat-growth {
            margin-top: 15px;
            font-size: 12px;
            line-height: 1.2;
            font-weight: 400;
            color: #8C8784;
            white-space: nowrap;
        }

        .dashboard-stat-growth strong {
            color: #11951B;
            font-weight: 500;
            margin-right: 2px;
        }

        .dashboard-growth-arrow {
            color: #11951B;
            font-size: 16px;
            vertical-align: -1px;
            margin-right: 2px;
        }

        @media (max-width: 640px) {
            .dashboard-stat-card {
                min-height: 104px;
            }
        }
    </style>

    <!-- ==================== PLATFORM OVERVIEW + SALES ==================== -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 mb-5">

        <!-- Platform Overview -->
        <div class="xl:col-span-2 bg-white rounded-2xl p-4 shadow-sm">

            <h3 class="text-[17px] font-semibold text-black mb-3">
                Platform Overview
            </h3>

            <div
    id="overview-chart-container"
    class="w-full opacity-0 translate-y-3 transition-all duration-700"
>
    <canvas id="overviewChart" height="250"></canvas>
</div>

        </div>


        <!-- Sales Summary -->
        <div class="bg-white rounded-2xl p-4 shadow-sm flex flex-col justify-between">

            <div>

                <h3 class="text-[17px] font-semibold text-black mb-3">
                    Sales Summary
                </h3>

                <div class="space-y-2.5 text-[13px]">

                    <div class="flex justify-between gap-4">
                        <span class="text-gray-400">
                            Gross Sales
                        </span>

                        <span class="font-medium text-right">
                            ₱145,560,000.00
                        </span>
                    </div>

                    <div class="flex justify-between gap-4">
                        <span class="text-gray-400">
                            Total Orders
                        </span>

                        <span class="font-medium">
                            1,256
                        </span>
                    </div>

                    <div class="flex justify-between gap-4">
                        <span class="text-gray-400">
                            Average Order Value
                        </span>

                        <span class="font-medium">
                            ₱145.00
                        </span>
                    </div>

                    <div class="flex justify-between gap-4">
                        <span class="text-gray-400">
                            Completed Orders
                        </span>

                        <span class="font-medium">
                            1,256
                        </span>
                    </div>

                    <div class="flex justify-between gap-4">
                        <span class="text-gray-400">
                            Return/Refund
                        </span>

                        <span class="font-medium">
                            25
                        </span>
                    </div>

                </div>

            </div>

            <button class="mt-3 bg-maroon-900 text-white rounded-full py-2.5 text-[13px] font-medium hover:bg-maroon-800 transition">
                View Full Report
            </button>

        </div>

    </div>


    <!-- ==================== BOTTOM CARDS ==================== -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">

        <!-- Complaints -->
        <div class="bg-white rounded-2xl p-4 shadow-sm">

            <div class="flex justify-between mb-3">

                <h3 class="text-[17px] font-semibold text-black">
                    Complaints and Dispute
                </h3>

                <a href="#" class="text-[12px] text-maroon-700 font-medium">
                    View all
                </a>

            </div>

            <div class="flex items-center gap-4">

                <div class="shrink-0">
                    <canvas
                        id="complaintsChart"
                        width="120"
                        height="120"
                    ></canvas>
                </div>

                <div class="space-y-2 text-[13px] flex-1">

                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-maroon-900"></span>
                        <span>Open</span>
                        <span class="ml-auto font-medium">20</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-red-500"></span>
                        <span>In Progress</span>
                        <span class="ml-auto font-medium">10</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-peach-dark"></span>
                        <span>Resolved</span>
                        <span class="ml-auto font-medium">45</span>
                    </div>

                </div>

            </div>

        </div>


        <!-- Pending Registrations -->
        <div class="bg-white rounded-2xl p-4 shadow-sm">

            <div class="flex justify-between mb-3">

                <h3 class="text-[17px] font-semibold text-black">
                    Pending Registrations
                </h3>

                <a href="#" class="text-[12px] text-maroon-700 font-medium">
                    View all
                </a>

            </div>

            <div class="space-y-4 text-[15px]">

                <div class="flex items-center gap-3">
                    <img
                        src="{{ asset('icons/admin/dashboard/body/seller.png') }}"
                        class="w-5 h-5"
                        alt=""
                    >

                    <span>Sellers</span>

                    <span class="ml-auto font-medium">
                        12
                    </span>
                </div>

                <div class="flex items-center gap-3">
                    <img
                        src="{{ asset('icons/admin/dashboard/body/courier.png') }}"
                        class="w-5 h-5"
                        alt=""
                    >

                    <span>Couriers</span>

                    <span class="ml-auto font-medium">
                        8
                    </span>
                </div>

                <div class="flex items-center gap-3">
                    <img
                        src="{{ asset('icons/admin/dashboard/body/buyer.png') }}"
                        class="w-5 h-5"
                        alt=""
                    >

                    <span>Buyers</span>

                    <span class="ml-auto font-medium">
                        15
                    </span>
                </div>

            </div>

        </div>


        <!-- Announcement -->
        <div class="bg-maroon-900 text-white rounded-2xl p-4 shadow-sm relative overflow-hidden">

            <!-- Decorative circles -->
            <div class="absolute -right-10 -top-10 w-32 h-32 rounded-full bg-peach-dark/10"></div>

            <div class="absolute -right-5 -bottom-12 w-28 h-28 rounded-full bg-peach-dark/10"></div>

            <!-- Decorative small dots -->
            <div class="absolute right-6 top-6 w-2 h-2 rounded-full bg-peach-dark/50"></div>

            <div class="absolute right-12 top-12 w-1.5 h-1.5 rounded-full bg-peach-dark/40"></div>


            <!-- Content -->
            <div class="relative z-10">

                <div class="flex items-center justify-between mb-3">

                    <p class="text-[13px] font-medium text-peach-dark uppercase tracking-wider">
                        Announcement
                    </p>

                    <span class="text-[13px] bg-white/10 px-3 py-1 rounded-full">
                        August
                    </span>

                </div>

                <h3 class="text-[19px] font-bold mb-2">
                    Augzu Sale 2026!
                </h3>

                <p class="text-[14px] text-white/75 leading-relaxed max-w-[230px]">
                    Abangan ang mga katangahan ngayong August
                </p>


                <!-- Bottom accent -->
                <div class="mt-4 flex items-center gap-2">

                    <span class="w-8 h-1 rounded-full bg-peach-dark"></span>

                    <span class="w-2 h-1 rounded-full bg-white/30"></span>

                    <span class="w-2 h-1 rounded-full bg-white/20"></span>

                </div>

            </div>

        </div>

    </div>

</div>


@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

Chart.defaults.font.family = 'Poppins, sans-serif';

new Chart(document.getElementById('overviewChart'), {

    type: 'line',

    data: {

        labels: [
            'May 1',
            'May 6',
            'May 11',
            'May 16',
            'May 21',
            'May 26',
            'May 31'
        ],

        datasets: [

            {
                label: 'Registrations',
                data: [80,120,90,150,110,140,300],

                borderColor: '#5c1414',
                backgroundColor: 'rgba(92,20,20,0.15)',

                fill: true,
                tension: 0.4,

                borderWidth: 2,

                pointRadius: 3,
                pointHoverRadius: 7,

                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#5c1414',
                pointBorderWidth: 2,

                pointHoverBorderWidth: 3
            },

            {
                label: 'Active Users',
                data: [60,90,70,180,130,160,380],

                borderColor: '#e63946',
                backgroundColor: 'rgba(230,57,70,0.10)',

                fill: true,
                tension: 0.4,

                borderWidth: 2,

                pointRadius: 3,
                pointHoverRadius: 7,

                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#e63946',
                pointBorderWidth: 2,

                pointHoverBorderWidth: 3
            },

            {
                label: 'Active Sellers',
                data: [100,150,120,200,160,190,350],

                borderColor: '#f3a341',
                backgroundColor: 'rgba(243,163,65,0.15)',

                fill: true,
                tension: 0.4,

                borderWidth: 2,

                pointRadius: 3,
                pointHoverRadius: 7,

                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#f3a341',
                pointBorderWidth: 2,

                pointHoverBorderWidth: 3
            }

        ]

    },

    options: {

        responsive: true,
        maintainAspectRatio: false,

        interaction: {
            mode: 'index',
            intersect: false
        },

        animation: {
    duration: 1600,
    easing: 'easeOutQuart',

    onComplete: function() {
        this.canvas.style.transition = 'transform 0.3s ease';
    }
},
        plugins: {

            legend: {

                position: 'top',
                align: 'start',

                labels: {

                    usePointStyle: true,
                    pointStyle: 'circle',

                    boxWidth: 8,
                    boxHeight: 8,

                    padding: 16,

                    font: {
                        size: 14
                    }
                }
            },

            tooltip: {

                enabled: true,

                backgroundColor: '#5c1414',

                titleColor: '#ffffff',
                bodyColor: '#ffffff',

                titleFont: {
                    size: 13,
                    weight: 'bold'
                },

                bodyFont: {
                    size: 13
                },

                padding: 12,

                cornerRadius: 10,

                displayColors: true,

                boxPadding: 4,

                callbacks: {

                    title: function(context) {
                        return context[0].label;
                    },

                    label: function(context) {
                        return ` ${context.dataset.label}: ${context.parsed.y}`;
                    }

                }
            }
        },

        scales: {

            y: {

                beginAtZero: true,

                grid: {
                    color: 'rgba(0,0,0,0.08)',
                    drawBorder: false
                },

                ticks: {
                    font: {
                        size: 13
                    },

                    color: '#6b7280'
                }
            },

            x: {

                grid: {
                    color: 'rgba(0,0,0,0.06)',
                    drawBorder: false
                },

                ticks: {

                    font: {
                        size: 13
                    },

                    color: '#6b7280'
                }
            }
        }
    }

});


new Chart(document.getElementById('complaintsChart'), {

    type: 'doughnut',

    data: {

        labels: [
            'Open',
            'In Progress',
            'Resolved'
        ],

        datasets: [{

            data: [20, 10, 45],

            backgroundColor: [
                '#5c1414',
                '#e63946',
                '#f3a98c'
            ],

            borderWidth: 0,

            hoverOffset: 8
        }]

    },

    options: {

        responsive: false,

        cutout: '70%',

        animation: {
            animateRotate: true,
            duration: 900,
            easing: 'easeOutQuart'
        },

        plugins: {

            legend: {
                display: false
            },

            tooltip: {

                enabled: true,

                backgroundColor: '#5c1414',

                titleColor: '#ffffff',
                bodyColor: '#ffffff',

                padding: 10,

                cornerRadius: 8,

                callbacks: {

                    label: function(context) {

                        const value = context.parsed;

                        return ` ${context.label}: ${value}`;
                    }

                }
            }
        }
    }

});
document.addEventListener('DOMContentLoaded', () => {

    setTimeout(() => {

        const overview =
            document.getElementById('overview-chart-container');

        if (overview) {
            overview.classList.remove(
                'opacity-0',
                'translate-y-3'
            );
        }

    }, 150);

});
</script>

@endpush

@endsection