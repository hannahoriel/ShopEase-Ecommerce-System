@extends('layouts.admin')
@section('page-title', 'Dashboard')

@section('content')

<div
    id="admin-content"
    class="ml-72 pt-[128px] px-6 pb-8 min-h-screen transition-all duration-300"
>

    <!-- Welcome -->
    <h2 class="text-[22px] font-bold text-gray-800 mb-6">
        Welcome, Admin!
    </h2>


    <!-- ==================== STAT CARDS ==================== -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">

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

        <div class="bg-white rounded-2xl p-5 shadow-sm">

            <div class="flex items-center gap-4">

                <!-- ICON -->
                <img
                    src="{{ asset('icons/admin/dashboard/body/' . $stat['icon']) }}"
                    class="w-12 h-12 object-contain shrink-0"
                    alt=""
                >

                <!-- TEXT -->
                <div>
                    <p class="text-[25px] font-bold text-gray-800 leading-tight">
                        {{ $stat['value'] }}
                    </p>

                    <p class="text-[15px] text-gray-400">
                        {{ $stat['label'] }}
                    </p>

                    <p class="text-[13px] text-green-600 mt-1">
                        ↑ {{ $stat['change'] }} from yesterday
                    </p>
                </div>

            </div>

        </div>

        @endforeach

    </div>


    <!-- ==================== PLATFORM OVERVIEW + SALES ==================== -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5 mb-6">

        <!-- Platform Overview -->
        <div class="xl:col-span-2 bg-white rounded-2xl p-5 shadow-sm">

            <h3 class="text-[18px] font-bold text-gray-800 mb-4">
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
        <div class="bg-white rounded-2xl p-5 shadow-sm flex flex-col justify-between">

            <div>

                <h3 class="text-[18px] font-bold text-gray-800 mb-4">
                    Sales Summary
                </h3>

                <div class="space-y-3 text-[15px]">

                    <div class="flex justify-between gap-4">
                        <span class="text-gray-400">
                            Gross Sales
                        </span>

                        <span class="font-semibold text-right">
                            ₱145,560,000.00
                        </span>
                    </div>

                    <div class="flex justify-between gap-4">
                        <span class="text-gray-400">
                            Total Orders
                        </span>

                        <span class="font-semibold">
                            1,256
                        </span>
                    </div>

                    <div class="flex justify-between gap-4">
                        <span class="text-gray-400">
                            Average Order Value
                        </span>

                        <span class="font-semibold">
                            ₱145.00
                        </span>
                    </div>

                    <div class="flex justify-between gap-4">
                        <span class="text-gray-400">
                            Completed Orders
                        </span>

                        <span class="font-semibold">
                            1,256
                        </span>
                    </div>

                    <div class="flex justify-between gap-4">
                        <span class="text-gray-400">
                            Return/Refund
                        </span>

                        <span class="font-semibold">
                            25
                        </span>
                    </div>

                </div>

            </div>

            <button class="mt-4 bg-maroon-900 text-white rounded-full py-3 text-[15px] font-medium hover:bg-maroon-800 transition">
                View Full Report
            </button>

        </div>

    </div>


    <!-- ==================== BOTTOM CARDS ==================== -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

        <!-- Complaints -->
        <div class="bg-white rounded-2xl p-5 shadow-sm">

            <div class="flex justify-between mb-4">

                <h3 class="text-[18px] font-bold text-gray-800">
                    Complaints and Dispute
                </h3>

                <a href="#" class="text-[13px] text-maroon-700 font-medium">
                    View all
                </a>

            </div>

            <div class="flex items-center gap-6">

                <div class="shrink-0">
                    <canvas
                        id="complaintsChart"
                        width="120"
                        height="120"
                    ></canvas>
                </div>

                <div class="space-y-2 text-[15px] flex-1">

                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-maroon-900"></span>
                        <span>Open</span>
                        <span class="ml-auto font-semibold">20</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-red-500"></span>
                        <span>In Progress</span>
                        <span class="ml-auto font-semibold">10</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-peach-dark"></span>
                        <span>Resolved</span>
                        <span class="ml-auto font-semibold">45</span>
                    </div>

                </div>

            </div>

        </div>


        <!-- Pending Registrations -->
        <div class="bg-white rounded-2xl p-5 shadow-sm">

            <div class="flex justify-between mb-4">

                <h3 class="text-[18px] font-bold text-gray-800">
                    Pending Registrations
                </h3>

                <a href="#" class="text-[13px] text-maroon-700 font-medium">
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

                    <span class="ml-auto font-semibold">
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

                    <span class="ml-auto font-semibold">
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

                    <span class="ml-auto font-semibold">
                        15
                    </span>
                </div>

            </div>

        </div>


        <!-- Announcement -->
        <div class="bg-maroon-900 text-white rounded-2xl p-5 shadow-sm relative overflow-hidden">

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

                <h3 class="text-[21px] font-bold mb-2">
                    Augzu Sale 2026!
                </h3>

                <p class="text-[15px] text-white/75 leading-relaxed max-w-[230px]">
                    Abangan ang mga katangahan ngayong August
                </p>


                <!-- Bottom accent -->
                <div class="mt-5 flex items-center gap-2">

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