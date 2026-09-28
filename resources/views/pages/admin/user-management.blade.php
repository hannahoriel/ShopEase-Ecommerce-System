@extends('layouts.admin')

@section('page-title', 'User Management')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');

    #admin-content,
    #admin-content * {
        font-family: 'Poppins', sans-serif;
    }

    .user-management-scrollbar {
        scrollbar-width: thin;
        scrollbar-color: #c9a39b #f8f4f3;
    }

    .user-management-scrollbar::-webkit-scrollbar {
        width: 9px;
    }

    .user-management-scrollbar::-webkit-scrollbar-track {
        background: #f8f4f3;
        border-radius: 999px;
    }

    .user-management-scrollbar::-webkit-scrollbar-thumb {
        background: #c9a39b;
        border-radius: 999px;
        border: 2px solid #f8f4f3;
    }

    /* Suspension duration: hide browser number spinners. */
    #suspension-duration-input::-webkit-outer-spin-button,
    #suspension-duration-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    #suspension-duration-input {
        -moz-appearance: textfield;
        appearance: textfield;
    }
</style>

<style>
/* Compact user-details modal: small enough for one-view on desktop,
   with only a minimal natural scroll on shorter screens. */
#user-management-modal > .user-management-scrollbar {
    width: min(920px, calc(100vw - 40px));
    max-width: 920px;
    max-height: calc(100vh - 100px);
    overflow-y: auto;
    overflow-x: hidden;
    border-radius: 20px;
    scrollbar-width: none;
    -ms-overflow-style: none;
}
#user-management-modal > .user-management-scrollbar::-webkit-scrollbar {
    display: none;
    width: 0;
    height: 0;
}
#user-management-modal > .user-management-scrollbar > .px-9.pt-6.pb-4 {
    padding: 14px 20px 10px !important;
}
#user-management-modal > .user-management-scrollbar > .px-7.pb-6 {
    padding: 12px 20px !important;
}
#user-management-modal .text-\[22px\] { font-size: 19px !important; }
#user-management-modal .text-\[17px\] { font-size: 13px !important; }
#user-management-modal .text-\[18px\] { font-size: 14px !important; }
#user-management-modal .text-\[15px\] { font-size: 13px !important; }
#user-management-modal .text-\[14px\] { font-size: 12px !important; }
#user-management-modal .gap-7 { gap: 12px !important; }
#user-management-modal .space-y-4 > :not([hidden]) ~ :not([hidden]) { margin-top: .55rem !important; }
#user-management-modal .my-7 { margin-top: 12px !important; margin-bottom: 12px !important; }
#user-management-modal .mb-6 { margin-bottom: 10px !important; }
#user-management-modal #user-modal-valid-id-preview {
    height: 128px !important;
    cursor: zoom-in;
}
#user-management-modal #user-modal-valid-id-link {
    display: none !important;
}
#user-management-modal #user-modal-business-permit-link {
    min-width: 0 !important;
    width: 100%;
    cursor: pointer;
    text-decoration: none;
}

/* Keep the dynamically-rendered action buttons visible. */
#user-management-modal #user-modal-actions {
    min-height: 52px;
    padding: 10px 20px 14px !important;
    display: flex !important;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
    background: #FFFFFF;
    border-top: 1px solid #E5E7EB;
}

#user-document-modal {
    display: none;
}
#user-document-modal.is-open {
    display: flex !important;
}
#user-document-modal .overflow-auto {
    scrollbar-width: none;
    -ms-overflow-style: none;
}
#user-document-modal .overflow-auto::-webkit-scrollbar {
    display: none;
    width: 0;
    height: 0;
}
</style>

<div id="admin-content" class="ml-60 pt-[110px] pl-5 pb-7 min-h-screen transition-all duration-300">

    <!-- ==================== STAT CARDS ==================== -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-5">

        <!-- Buyers -->
        <div class="user-stat-card">
            <div class="user-stat-main">
                <img
                    src="{{ asset('icons/admin/user-management/buyer.png') }}"
                    class="user-stat-icon"
                    alt="Buyers"
                >

                <div class="user-stat-content">
                    <p id="buyers-count-card" class="user-stat-number">328</p>
                    <p class="user-stat-label">Buyers</p>
                </div>
            </div>
        </div>

        <!-- Sellers -->
        <div class="user-stat-card">
            <div class="user-stat-main">
                <img
                    src="{{ asset('icons/admin/user-management/seller.png') }}"
                    class="user-stat-icon"
                    alt="Sellers"
                >

                <div class="user-stat-content">
                    <p id="sellers-count-card" class="user-stat-number">412</p>
                    <p class="user-stat-label">Sellers</p>
                </div>
            </div>
        </div>

        <!-- Suspended -->
        <div class="user-stat-card">
            <div class="user-stat-main">
                <img
                    src="{{ asset('icons/admin/user-management/suspended.png') }}"
                    class="user-stat-icon"
                    alt="Suspended"
                >

                <div class="user-stat-content">
                    <p id="suspended-count-card" class="user-stat-number">153</p>
                    <p class="user-stat-label">Suspended</p>
                </div>
            </div>
        </div>

        <!-- Total Users -->
        <div class="user-stat-card">
            <div class="user-stat-main">
                <img
                    src="{{ asset('icons/admin/user-management/total-users.png') }}"
                    class="user-stat-icon"
                    alt="Total Users"
                >

                <div class="user-stat-content">
                    <p id="total-users-count-card" class="user-stat-number">1245</p>
                    <p class="user-stat-label">Total Users</p>
                </div>
            </div>
        </div>

    </div>

    <style>
        /* =========================================================
           USER MANAGEMENT STAT CARDS
           Matches Dashboard typography, sizing, spacing, and format.
        ========================================================== */

        .user-stat-card {
            min-height: 90px;
            background: #FFFFFF;
            border: 1px solid #F0E9E6;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(42, 20, 15, 0.05);
            padding: 16px;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        .user-stat-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(42, 20, 15, 0.07);
        }

        .user-stat-main {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-stat-icon {
            width: 40px;
            height: 40px;
            flex: 0 0 40px;
            object-fit: contain;
            display: block;
        }

        .user-stat-content {
            min-width: 0;
        }

        .user-stat-number {
            font-size: 23px;
            line-height: 1;
            font-weight: 600;
            color: #17120F;
        }

        .user-stat-label {
            margin-top: 5px;
            font-size: 13px;
            line-height: 1.15;
            font-weight: 400;
            color: #8C8784;
            white-space: nowrap;
        }

        @media (max-width: 640px) {
            .user-stat-card {
                min-height: 104px;
            }
        }
    </style>

    <!-- FILTER BAR -->
    <div class="bg-white rounded-2xl shadow-sm border border-[#F0E9E6] p-4 mb-5">
        <div class="flex flex-col xl:flex-row items-stretch xl:items-center gap-2.5">

            <div class="relative w-full xl:w-[330px] xl:flex-none">
                <input
                    id="user-management-search"
                    type="text"
                    placeholder="Search name, email, and phone"
                    class="w-full h-[36px] rounded-lg border border-gray-300 bg-white pl-4 pr-11 text-[13px] text-gray-700 placeholder:text-gray-300 outline-none focus:border-[#7B1B1B] focus:ring-2 focus:ring-[#7B1B1B]/10 transition"
                >
                <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-[18px] h-[18px] text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"/>
                </svg>
            </div>

            <div class="relative">
                <select id="user-management-type" class="appearance-none w-full xl:w-[132px] h-[38px] rounded-lg border border-gray-300 bg-white px-3 pr-9 text-[13px] text-gray-700 outline-none cursor-pointer focus:border-[#7B1B1B] focus:ring-2 focus:ring-[#7B1B1B]/10">
                    <option value="all">All User Types</option>
                    <option value="buyer">Buyer</option>
                    <option value="seller">Seller</option>
                </select>
                <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"/>
                </svg>
            </div>

            <div class="relative">
                <select id="user-management-status" class="appearance-none w-full xl:w-[132px] h-[38px] rounded-lg border border-gray-300 bg-white px-3 pr-9 text-[13px] text-gray-700 outline-none cursor-pointer focus:border-[#7B1B1B] focus:ring-2 focus:ring-[#7B1B1B]/10">
                    <option value="all">All Status</option>
                    <option value="active">Active</option>
                    <option value="suspended">Suspended</option>
                    <option value="deactivated">Deactivated</option>
                </select>
                <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"/>
                </svg>
            </div>

            <div class="relative">
                <input
                    id="user-management-date"
                    type="date"
                    class="w-full xl:w-[195px] h-[38px] rounded-lg border border-gray-300 bg-white px-3 pr-10 text-[13px] text-gray-700 outline-none focus:border-[#7B1B1B] focus:ring-2 focus:ring-[#7B1B1B]/10 [&::-webkit-calendar-picker-indicator]:opacity-0 [&::-webkit-calendar-picker-indicator]:absolute [&::-webkit-calendar-picker-indicator]:right-0 [&::-webkit-calendar-picker-indicator]:w-10 [&::-webkit-calendar-picker-indicator]:h-full [&::-webkit-calendar-picker-indicator]:cursor-pointer"
                >
                <button type="button" id="user-management-date-button" class="absolute right-0 top-0 w-9 h-[36px] flex items-center justify-center text-gray-700 hover:text-[#7B1B1B]" aria-label="Open calendar">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 2v4m8-4v4M3 10h18M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"/>
                    </svg>
                </button>
            </div>

            <button id="user-management-reload" type="button" class="w-[40px] h-[38px] shrink-0 flex items-center justify-center rounded-lg text-gray-900 hover:bg-[#FFF0EC] active:scale-95 transition-all duration-200" title="Reset filters">
                <svg id="user-management-reload-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 11a8.1 8.1 0 0 0-15.5-2M4 5v4h4M4 13a8.1 8.1 0 0 0 15.5 2M20 19v-4h-4"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- USER TABLE -->
    <div class="bg-white rounded-2xl shadow-sm border border-[#F0E9E6] overflow-hidden">
        <div class="overflow-x-auto">
            <table id="user-management-table" class="w-full min-w-[980px]">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left px-4 py-3 text-[12px] font-medium text-gray-400">User</th>
                        <th class="text-left px-4 py-3 text-[13px] font-medium text-gray-400">User Type</th>
                        <th class="text-left px-4 py-3 text-[13px] font-medium text-gray-400">Email</th>
                        <th class="text-left px-4 py-3 text-[13px] font-medium text-gray-400">Phone</th>
                        <th class="text-left px-4 py-3 text-[13px] font-medium text-gray-400">Date Joined</th>
                        <th class="text-left px-4 py-3 text-[13px] font-medium text-gray-400">Status</th>
                    </tr>
                </thead>
                <tbody id="user-management-body" class="divide-y divide-gray-200"></tbody>
            </table>
        </div>

        <div class="flex flex-col md:flex-row items-center justify-between gap-3 px-4 py-3 border-t border-gray-200">
            <p id="user-management-count" class="text-[12px] text-gray-400">Showing 10 out of 30 users</p>

            <div class="flex items-center gap-2">
                <button id="user-management-prev" type="button" class="w-7 h-7 flex items-center justify-center rounded-md text-gray-700 hover:bg-gray-100 transition">‹</button>
                <div id="user-management-pages" class="flex items-center gap-2"></div>
                <button id="user-management-next" type="button" class="w-7 h-7 flex items-center justify-center rounded-md text-gray-700 hover:bg-gray-100 transition">›</button>

                <select id="user-management-items" class="ml-2 h-7 rounded-md border border-[#F0B9AC] bg-[#FFF5F1] px-2 text-[12px] text-gray-700 outline-none cursor-pointer">
                    <option value="10" selected>Items per page: 10</option>
                    <option value="20">Items per page: 20</option>
                    <option value="50">Items per page: 50</option>
                </select>
            </div>
        </div>
    </div>

    <!-- USER DETAILS MODAL -->
    <div
        id="user-management-modal"
        class="fixed inset-0 z-[120] hidden items-center justify-center bg-black/35 backdrop-blur-[2px] px-5 py-6"
        aria-hidden="true"
    >
        <div
            class="user-management-scrollbar relative bg-white w-full max-w-6xl max-h-[92vh] overflow-y-auto rounded-[28px] shadow-2xl"
            role="dialog"
            aria-modal="true"
            aria-labelledby="user-modal-title"
        >

            <!-- MODAL HEADER -->
            <div class="px-9 pt-6 pb-4">
                <div class="flex items-center justify-between">
                    <h3 id="user-modal-title" class="text-[22px] font-medium text-gray-900">
                        Profile
                    </h3>

                    <button
                        type="button"
                        id="close-user-management-modal"
                        class="w-8 h-8 flex items-center justify-center rounded-full text-gray-500 hover:bg-gray-100 transition"
                        aria-label="Close"
                    >
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6L6 18"/>
                        </svg>
                    </button>
                </div>

                <div class="mt-4 border-b border-gray-200"></div>
            </div>

            <!-- MODAL BODY -->
            <div class="px-7 pb-4">
                <div class="grid grid-cols-1 lg:grid-cols-[280px_minmax(0,1fr)] gap-5">

                    <!-- LEFT PROFILE SUMMARY -->
                    <aside class="px-1 lg:pr-2">
                        <div class="flex items-start gap-4">
                            <div class="w-[76px] h-[76px] rounded-full bg-[#D9D9D9] flex items-center justify-center shrink-0">
                                <svg class="w-10 h-10 text-black" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 12a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9Zm0 2c-4.42 0-8 2.24-8 5v2h16v-2c0-2.76-3.58-5-8-5Z"/>
                                </svg>
                            </div>

                            <div class="min-w-0 pt-1">
                                <h4 id="user-profile-name" class="text-[22px] font-medium text-gray-900 leading-tight">
                                    Juan Dela Cruz
                                </h4>

                                <div class="mt-2 flex flex-wrap items-center gap-2">
                                    <div id="user-profile-status"></div>

                                    <div class="flex items-center gap-2 text-[14px] text-gray-900">
                                        <img id="user-profile-type-icon"
                                             src="{{ asset('icons/admin/dashboard/body/seller.png') }}"
                                             class="w-[18px] h-[18px] object-contain"
                                             alt="Seller">
                                        <span id="user-profile-type">Seller</span>
                                    </div>
                                </div>

                                <!-- SELLER CATEGORIES -->
                                <div id="user-profile-categories" class="mt-3 flex flex-wrap gap-2"></div>
                            </div>
                        </div>

                        <div class="mt-7">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 shrink-0 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 3v3m10-3v3M4 9h16M6 5h12a2 2 0 0 1 2 2v12H4V7a2 2 0 0 1 2-2Z"/>
                                </svg>
                                <span class="text-[17px] font-medium text-gray-900">Date Joined</span>
                            </div>

                            <div class="mt-4 pl-0">
                                <p id="user-profile-date" class="text-[14px] text-gray-900">May 31, 2026</p>
                                <p id="user-profile-time" class="text-[14px] text-gray-900 mt-1">10:30 AM</p>
                            </div>
                        </div>
                    </aside>

                    <!-- RIGHT DETAILS -->
                    <section class="min-w-0">

                        <!-- PERSONAL INFORMATION -->
                        <section>
                            <div class="flex items-center gap-2 mb-6">
                                <svg class="w-6 h-6 text-[#A52A2A]" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M16 11a4 4 0 1 0-3.9-5A4 4 0 0 0 16 11ZM8 12a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm8 1c-2.7 0-5 1.35-5 3v1h10v-1c0-1.65-2.3-3-5-3ZM8 14c-2.2 0-4 1.1-4 2.5V18h8v-1.5C12 15.1 10.2 14 8 14Z"/>
                                </svg>
                                <h4 class="text-[18px] font-semibold text-[#A52A2A]">Personal Information</h4>
                            </div>

                            <div class="grid grid-cols-1 xl:grid-cols-[1fr_290px] gap-7">
                                <div class="space-y-4">
                                    <div class="grid grid-cols-[130px_minmax(0,1fr)] gap-4 items-center">
                                        <span class="text-[15px] text-gray-400">Last Name</span>
                                        <span id="user-modal-last-name" class="text-[15px] font-medium text-gray-900">Dela Cruz</span>
                                    </div>
                                    <div class="grid grid-cols-[130px_minmax(0,1fr)] gap-4 items-center">
                                        <span class="text-[15px] text-gray-400">First Name</span>
                                        <span id="user-modal-first-name" class="text-[15px] font-medium text-gray-900">Juan</span>
                                    </div>
                                    <div class="grid grid-cols-[130px_minmax(0,1fr)] gap-4 items-center">
                                        <span class="text-[15px] text-gray-400">Middle Name</span>
                                        <span id="user-modal-middle-name" class="text-[15px] font-medium text-gray-900">Amador</span>
                                    </div>
                                    <div class="grid grid-cols-[130px_minmax(0,1fr)] gap-4 items-center">
                                        <span class="text-[15px] text-gray-400">Sex</span>
                                        <span id="user-modal-sex" class="text-[15px] font-medium text-gray-900">Male</span>
                                    </div>
                                    <div class="grid grid-cols-[130px_minmax(0,1fr)] gap-4 items-center">
                                        <span class="text-[15px] text-gray-400">Birthday</span>
                                        <span id="user-modal-birthday" class="text-[15px] font-medium text-gray-900">November 7, 2006</span>
                                    </div>
                                    <div class="grid grid-cols-[130px_minmax(0,1fr)] gap-4 items-center">
                                        <span class="text-[15px] text-gray-400">Age</span>
                                        <span id="user-modal-age" class="text-[15px] font-medium text-gray-900">19</span>
                                    </div>
                                    <div class="grid grid-cols-[130px_minmax(0,1fr)] gap-4 items-center">
                                        <span class="text-[15px] text-gray-400">Email</span>
                                        <span id="user-modal-email" class="text-[15px] font-medium text-gray-900 break-all">juandelacruz@gmail.com</span>
                                    </div>
                                    <div class="grid grid-cols-[130px_minmax(0,1fr)] gap-4 items-center">
                                        <span class="text-[15px] text-gray-400">Contact No.</span>
                                        <span id="user-modal-phone" class="text-[15px] font-medium text-gray-900">0917 123 4567</span>
                                    </div>
                                </div>

                                <div>
                                    <p class="text-[15px] text-gray-400 mb-2">Valid ID</p>
                                    <div id="user-modal-valid-id-preview" class="w-full h-[178px] rounded-lg overflow-hidden border border-gray-200 bg-gray-50 relative shadow-sm">
                                        <img id="user-modal-valid-id" src="" alt="Valid ID" class="hidden w-full h-full object-contain bg-white">
                                        <div id="user-modal-valid-id-empty" class="absolute inset-0 flex items-center justify-center text-[13px] text-gray-400">Valid ID not provided</div>
                                        <a id="user-modal-valid-id-link" href="#" target="_blank" rel="noopener" class="hidden absolute bottom-2 right-2 rounded bg-white/90 px-2 py-1 text-[11px] font-medium text-[#A52A2A] shadow">Open document</a>
                                    </div>
                                    <div class="hidden">
                                        <div class="absolute top-3 left-4 text-[8px] font-semibold text-[#2d3550]">REPUBLIKA NG PILIPINAS</div>
                                        <div class="absolute top-6 left-4 text-[7px] text-[#2d3550]">PHILIPPINE IDENTIFICATION CARD</div>
                                        <div class="absolute left-4 top-[44px] w-[57px] h-[74px] rounded bg-gray-300 flex items-center justify-center overflow-hidden">
                                            <svg class="w-9 h-9 text-gray-500" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm0 2c-4.42 0-8 2.24-8 5v1h16v-1c0-2.76-3.58-5-8-5Z"/>
                                            </svg>
                                        </div>
                                        <div class="absolute left-[84px] top-[47px] text-[7px] text-[#4b5563]">Apelyido / Last Name</div>
                                        <div class="absolute left-[84px] top-[59px] text-[11px] font-bold text-[#111827]">DELA CRUZ</div>
                                        <div class="absolute left-[84px] top-[77px] text-[7px] text-[#4b5563]">Pangalan / First Name</div>
                                        <div class="absolute left-[84px] top-[89px] text-[11px] font-bold text-[#111827]">JUAN</div>
                                        <div class="absolute left-[84px] top-[107px] text-[7px] text-[#4b5563]">MIDDLE NAME</div>
                                        <div class="absolute left-[84px] top-[119px] text-[10px] font-semibold text-[#111827]">AMADOR</div>
                                        <div class="absolute left-[84px] bottom-3 text-[7px] text-[#4b5563]">Date of Birth: 07 NOV 2006</div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <div class="border-t border-gray-200 my-7"></div>

                        <!-- ADDRESS -->
                        <section>
                            <div class="flex items-center gap-2 mb-6">
                                <svg class="w-6 h-6 text-[#A52A2A]" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2a7 7 0 0 0-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 0 0-7-7Zm0 9.5A2.5 2.5 0 1 1 12 6a2.5 2.5 0 0 1 0 5.5Z"/>
                                </svg>
                                <h4 class="text-[18px] font-semibold text-[#A52A2A]">Address</h4>
                            </div>

                            <div class="space-y-4">
                                <div class="grid grid-cols-[130px_minmax(0,1fr)] gap-4">
                                    <span class="text-[15px] text-gray-400">Province</span>
                                    <span id="user-modal-province" class="text-[15px] font-medium text-gray-900">Laguna</span>
                                </div>
                                <div class="grid grid-cols-[130px_minmax(0,1fr)] gap-4">
                                    <span class="text-[15px] text-gray-400">Municipality</span>
                                    <span id="user-modal-municipality" class="text-[15px] font-medium text-gray-900">Calamba</span>
                                </div>
                                <div class="grid grid-cols-[130px_minmax(0,1fr)] gap-4">
                                    <span class="text-[15px] text-gray-400">Barangay</span>
                                    <span id="user-modal-barangay" class="text-[15px] font-medium text-gray-900">Masico</span>
                                </div>
                                <div class="grid grid-cols-[130px_minmax(0,1fr)] gap-4">
                                    <span class="text-[15px] text-gray-400">Street</span>
                                    <span id="user-modal-street" class="text-[15px] font-medium text-gray-900">Block 2 Lot 2, San Lorenzo St.</span>
                                </div>
                                <div class="grid grid-cols-[130px_minmax(0,1fr)] gap-4">
                                    <span class="text-[15px] text-gray-400">House No.</span>
                                    <span id="user-modal-house" class="text-[15px] font-medium text-gray-900">587</span>
                                </div>
                                <div class="grid grid-cols-[130px_minmax(0,1fr)] gap-4">
                                    <span class="text-[15px] text-gray-400">Zip Code</span>
                                    <span id="user-modal-zip" class="text-[15px] font-medium text-gray-900">4020</span>
                                </div>
                            </div>
                        </section>

                        <!-- SELLER-ONLY BUSINESS INFO -->
                        <section id="seller-business-section">
                            <div class="border-t border-gray-200 my-7"></div>

                            <div class="flex items-center gap-2 mb-6">
                                <svg class="w-6 h-6 text-[#A52A2A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 9h16v10H4zM7 9V6h10v3M9 13h6"/>
                                </svg>
                                <h4 class="text-[18px] font-semibold text-[#A52A2A]">Business Information</h4>
                            </div>

                            <div class="space-y-4">
                                <div class="grid grid-cols-[130px_minmax(0,1fr)] gap-4">
                                    <span class="text-[15px] text-gray-400">Business Name</span>
                                    <span id="user-modal-business-name" class="text-[15px] font-medium text-gray-900">Dela Cruz Online Boutique</span>
                                </div>

                                <div class="grid grid-cols-[130px_minmax(0,1fr)] gap-4">
                                    <span class="text-[15px] text-gray-400">Category</span>
                                    <span id="user-modal-business-category" class="text-[15px] font-medium text-gray-900">Fashion &amp; Apparel</span>
                                </div>

                                <div class="grid grid-cols-[130px_minmax(0,1fr)] gap-4 items-center">
                                    <span class="text-[15px] text-gray-400">Business Permit</span>

                                    <a id="user-modal-business-permit-link" href="#" target="_blank" rel="noopener" class="inline-flex items-center gap-2 w-fit min-w-[225px] rounded-lg border border-gray-300 px-3 py-2">
                                        <svg class="w-4 h-4 text-[#A52A2A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 3h8l4 4v14H7zM15 3v5h5M10 13h5m-5 4h5"/>
                                        </svg>
                                        <span id="user-modal-business-permit" class="text-[13px] text-gray-700">Business permit not provided</span>
                                    </a>
                                </div>
                            </div>
                        </section>

                        <!-- SUSPENSION INFO -->
                        <div id="user-suspension-info" class="hidden mt-7 rounded-lg bg-[#FFF7EE] border border-[#F1C7A3] px-4 py-3">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-[12px] text-gray-500">Suspension Duration</p>
                                    <p id="user-suspension-duration" class="text-[15px] font-semibold text-[#C86B00] mt-1">
                                        7 days remaining
                                    </p>
                                </div>
                            </div>
                        </div>

                    </section>
                </div>
            </div>

            <!-- MODAL ACTIONS -->
            <div
                id="user-modal-actions"
                class="px-5 py-3 flex justify-end items-center gap-2 border-t border-gray-200 bg-white"
            >
                <!-- Dynamically rendered based on status -->
            </div>
        </div>
    </div>

    <!-- DOCUMENT VIEWER MODAL -->
    <div id="user-document-modal" class="fixed inset-0 z-[180] hidden items-center justify-center bg-black/60 backdrop-blur-[2px] px-4 py-5" aria-hidden="true">
        <div class="relative bg-white w-full max-w-4xl h-[82vh] rounded-2xl shadow-2xl overflow-hidden" role="dialog" aria-modal="true" aria-labelledby="user-document-title">
            <div class="h-12 px-4 flex items-center justify-between border-b border-gray-200">
                <h3 id="user-document-title" class="text-[14px] font-semibold text-black">Document</h3>
                <button type="button" id="close-user-document-modal" class="w-8 h-8 flex items-center justify-center rounded-full text-gray-500 hover:bg-gray-100 hover:text-gray-800 transition" aria-label="Close document">
                    <svg class="w-[17px] h-[17px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6L6 18"/></svg>
                </button>
            </div>
            <div class="w-full h-[calc(82vh-48px)] bg-[#F7F7F7] flex items-center justify-center p-3 overflow-auto">
                <div id="user-document-empty" class="text-[13px] text-gray-400 hidden">Document not available.</div>
                <img id="user-document-image" src="" alt="Document preview" class="hidden max-w-full max-h-full object-contain rounded-lg shadow-sm">
                <iframe id="user-document-frame" title="Document preview" class="hidden w-full h-full border-0 rounded-lg bg-white"></iframe>
            </div>
        </div>
    </div>

    <!-- =========================================================
         SUSPEND ACCOUNT MODAL
    ========================================================== -->
    <div
        id="suspend-account-modal"
        class="fixed inset-0 z-[160] hidden items-center justify-center bg-black/35 backdrop-blur-[2px] px-5 py-6"
        aria-hidden="true"
    >
        <div
            class="relative bg-white w-full max-w-[610px] rounded-[28px] shadow-2xl"
            role="dialog"
            aria-modal="true"
            aria-labelledby="suspend-account-title"
        >
            <div class="px-8 pt-7 pb-6">
                <h3 id="suspend-account-title" class="text-[22px] font-medium text-gray-900">
                    Suspend Account
                </h3>

                <p class="mt-2 text-[16px] leading-7 text-gray-400">
                    Suspending an account will temporarily disable the user’s access. You can reactivate the account anytime.
                </p>

                <div class="mt-5">
                    <p class="text-[17px] font-medium text-gray-900">
                        Reason<span class="text-[#D41F1F]">*</span>
                    </p>

                    <div class="mt-2 space-y-2">
                        <label class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50/60 px-3 py-2.5 cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" name="suspend-reason" value="Violation of platform policies" class="w-[18px] h-[18px] accent-[#7B1B1B]">
                            <span class="text-[14px] text-gray-900">Violation of platform policies</span>
                        </label>

                        <label class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50/60 px-3 py-2.5 cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" name="suspend-reason" value="Inappropriate behavior" class="w-[18px] h-[18px] accent-[#7B1B1B]">
                            <span class="text-[14px] text-gray-900">Inappropriate behavior</span>
                        </label>

                        <label class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50/60 px-3 py-2.5 cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" name="suspend-reason" value="Listing of prohibited products" class="w-[18px] h-[18px] accent-[#7B1B1B]">
                            <span class="text-[14px] text-gray-900">Listing of prohibited products</span>
                        </label>

                        <label class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50/60 px-3 py-2.5 cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" name="suspend-reason" value="Fraudulent activity" class="w-[18px] h-[18px] accent-[#7B1B1B]">
                            <span class="text-[14px] text-gray-900">Fraudulent activity</span>
                        </label>

                        <label class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50/60 px-3 py-2.5 cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" name="suspend-reason" value="Multiple complaints from users" class="w-[18px] h-[18px] accent-[#7B1B1B]">
                            <span class="text-[14px] text-gray-900">Multiple complaints from users</span>
                        </label>

                        <label class="flex items-center gap-3 px-3 py-1.5 cursor-pointer">
                            <input type="radio" name="suspend-reason" value="Other (please specify)" class="w-[18px] h-[18px] accent-[#7B1B1B]">
                            <span class="text-[14px] text-gray-900">Other (please specify)</span>
                        </label>
                    </div>
                </div>

                <div class="mt-4">
                    <p class="text-[17px] font-medium text-gray-900">
                        Suspension Duration<span class="text-[#D41F1F]">*</span>
                    </p>

                    <div class="relative mt-2">
                        <input
                            id="suspension-duration-input"
                            type="number"
                            min="1"
                            step="1"
                            placeholder="Select number of days"
                            class="w-full h-[42px] rounded-lg border border-gray-200 bg-white px-4 pr-12 text-[14px] text-gray-700 placeholder:text-gray-400 outline-none focus:border-[#7B1B1B] focus:ring-2 focus:ring-[#7B1B1B]/10 transition"
                        >
                        <svg class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 3v3m10-3v3M4 9h16M6 5h12a2 2 0 0 1 2 2v12H4V7a2 2 0 0 1 2-2Z"/>
                        </svg>
                    </div>

                    <div class="mt-2 rounded-lg bg-[#FFF9D8] px-3 py-2 flex items-start gap-2">
                        <svg class="w-5 h-5 shrink-0 text-gray-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/>
                            <path d="M12 10v5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            <circle cx="12" cy="7" r="1" fill="currentColor"/>
                        </svg>
                        <p class="text-[13px] leading-4 text-gray-700">
                            After the selected number of days, the account will be automatically reactivated.
                        </p>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="flex items-center justify-between">
                        <p class="text-[17px] font-medium text-gray-900">Additional Details (Optional)</p>
                        <span id="suspend-details-count" class="text-[12px] text-gray-500">0/300</span>
                    </div>
                    <textarea
                        id="suspend-additional-details"
                        maxlength="300"
                        rows="2"
                        placeholder="Write additional details here..."
                        class="mt-2 w-full rounded-lg border border-gray-200 bg-white px-3 py-3 text-[14px] text-gray-700 placeholder:text-gray-400 resize-none outline-none focus:border-[#7B1B1B] focus:ring-2 focus:ring-[#7B1B1B]/10 transition"
                    ></textarea>
                </div>
            </div>

            <div class="px-8 pb-7 flex justify-end gap-2">
                <button
                    type="button"
                    id="cancel-suspend-account"
                    class="px-6 py-2.5 rounded-lg border border-gray-300 bg-white text-[#A52A2A] text-[14px] font-semibold hover:bg-gray-50 transition"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    id="confirm-suspend-account"
                    class="px-6 py-2.5 rounded-lg border border-[#D41F1F] bg-[#FFE0E0] text-[#AE0000] text-[14px] font-semibold hover:bg-[#FFD1D1] transition"
                >
                    Suspend
                </button>
            </div>
        </div>
    </div>

    <!-- =========================================================
         DEACTIVATE ACCOUNT MODAL
    ========================================================== -->
    <div
        id="deactivate-account-modal"
        class="fixed inset-0 z-[160] hidden items-center justify-center bg-black/35 backdrop-blur-[2px] px-5 py-6"
        aria-hidden="true"
    >
        <div
            class="relative bg-white w-full max-w-[610px] rounded-[28px] shadow-2xl"
            role="dialog"
            aria-modal="true"
            aria-labelledby="deactivate-account-title"
        >
            <div class="px-8 pt-7 pb-6">
                <h3 id="deactivate-account-title" class="text-[22px] font-medium text-gray-900">
                    Deactivate Account
                </h3>

                <p class="mt-2 text-[16px] leading-7 text-gray-400">
                    Deactivating an account will disable the user's access. The account can be reactivated anytime.
                </p>

                <div class="mt-5">
                    <p class="text-[17px] font-medium text-gray-900">
                        Reason<span class="text-[#D41F1F]">*</span>
                    </p>

                    <div class="mt-2 space-y-2">
                        <label class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50/60 px-3 py-2.5 cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" name="deactivate-reason" value="Severe violation of platform policies" class="w-[18px] h-[18px] accent-[#7B1B1B]">
                            <span class="text-[14px] text-gray-900">Severe violation of platform policies</span>
                        </label>

                        <label class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50/60 px-3 py-2.5 cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" name="deactivate-reason" value="Fraudulent activity" class="w-[18px] h-[18px] accent-[#7B1B1B]">
                            <span class="text-[14px] text-gray-900">Fraudulent activity</span>
                        </label>

                        <label class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50/60 px-3 py-2.5 cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" name="deactivate-reason" value="Abuse or harassment" class="w-[18px] h-[18px] accent-[#7B1B1B]">
                            <span class="text-[14px] text-gray-900">Abuse or harassment</span>
                        </label>

                        <label class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50/60 px-3 py-2.5 cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" name="deactivate-reason" value="Request by the user" class="w-[18px] h-[18px] accent-[#7B1B1B]">
                            <span class="text-[14px] text-gray-900">Request by the user</span>
                        </label>

                        <label class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50/60 px-3 py-2.5 cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" name="deactivate-reason" value="Other (please specify)" class="w-[18px] h-[18px] accent-[#7B1B1B]">
                            <span class="text-[14px] text-gray-900">Other (please specify)</span>
                        </label>
                    </div>
                </div>

                <div class="mt-5">
                    <div class="flex items-center justify-between">
                        <p class="text-[17px] font-medium text-gray-900">Additional Details (Optional)</p>
                        <span id="deactivate-details-count" class="text-[12px] text-gray-500">0/300</span>
                    </div>
                    <textarea
                        id="deactivate-additional-details"
                        maxlength="300"
                        rows="2"
                        placeholder="Write additional details here..."
                        class="mt-2 w-full rounded-lg border border-gray-200 bg-white px-3 py-3 text-[14px] text-gray-700 placeholder:text-gray-400 resize-none outline-none focus:border-[#7B1B1B] focus:ring-2 focus:ring-[#7B1B1B]/10 transition"
                    ></textarea>
                </div>
            </div>

            <div class="px-8 pb-7 flex justify-end gap-2">
                <button
                    type="button"
                    id="cancel-deactivate-account"
                    class="px-6 py-2.5 rounded-lg border border-gray-300 bg-white text-[#A52A2A] text-[14px] font-semibold hover:bg-gray-50 transition"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    id="confirm-deactivate-account"
                    class="px-6 py-2.5 rounded-lg border border-[#F08B4D] bg-[#FFF0E6] text-[#C96A00] text-[14px] font-semibold hover:bg-[#FFE6D8] transition"
                >
                    Deactivate
                </button>
            </div>
        </div>
    </div>

    <!-- =========================================================
         ACCOUNT STATUS FLASH MESSAGE
         Same lower-right feedback style used in Seller Compliance.
    ========================================================== -->
    <div
        id="account-action-flash"
        class="account-decision-flash"
        role="status"
        aria-live="polite"
        aria-atomic="true"
    >
        <div id="account-action-flash-icon" class="account-decision-flash-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12l4 4L19 6" />
            </svg>
        </div>

        <div class="account-decision-flash-copy">
            <strong id="account-action-flash-title">Account updated</strong>
            <span id="account-action-flash-message">The account status has been updated.</span>
        </div>

        <button
            type="button"
            id="close-account-action-flash"
            class="account-decision-flash-close"
            aria-label="Close notification"
        >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M6 6l12 12" />
                <path d="M18 6L6 18" />
            </svg>
        </button>
    </div>
</div>

<style>
    /* =========================================================
       DASHBOARD-MATCHED UI TYPOGRAPHY
    ========================================================== */

    #admin-content .text-gray-900,
    #admin-content .text-gray-800 {
        color: #17120F;
    }

    #admin-content .text-gray-400 {
        color: #8C8784;
    }

    #admin-content input,
    #admin-content select,
    #admin-content textarea,
    #admin-content button {
        font-family: 'Poppins', sans-serif;
    }
</style>

<style>
/* FINAL USER MANAGEMENT TABLE FONT SIZE */
#user-management-table tbody,
#user-management-table tbody tr,
#user-management-table tbody td,
#user-management-table tbody td * {
    font-size: 12px !important;
}

/* Compact search bar, matching Seller Compliance */
#user-management-search {
    width: 330px !important;
    max-width: 100%;
}

@media (max-width: 1279px) {
    #user-management-search {
        width: 100% !important;
    }
}

/* Compact Suspend Account modal */
#suspend-account-modal > div {
    width: min(540px, calc(100vw - 40px));
    max-width: 540px;
    border-radius: 22px;
}

#suspend-account-modal .px-8.pt-7.pb-6 {
    padding: 22px 24px 18px !important;
}

#suspend-account-modal .px-8.pb-7 {
    padding: 0 24px 22px !important;
}

#suspend-account-modal h3 {
    font-size: 20px !important;
}

#suspend-account-modal .text-\[16px\] {
    font-size: 13px !important;
    line-height: 1.5 !important;
}

#suspend-account-modal .text-\[17px\] {
    font-size: 13px !important;
}

#suspend-account-modal .text-\[14px\] {
    font-size: 12px !important;
}

#suspend-account-modal .text-\[13px\] {
    font-size: 12px !important;
}

#suspend-account-modal .text-\[12px\] {
    font-size: 11px !important;
}

#suspend-account-modal .mt-5 {
    margin-top: 14px !important;
}

#suspend-account-modal .mt-4 {
    margin-top: 12px !important;
}

#suspend-account-modal .gap-3 {
    gap: 10px !important;
}

#suspend-account-modal .space-y-2 > :not([hidden]) ~ :not([hidden]) {
    margin-top: 6px !important;
}

#suspend-account-modal label {
    padding-top: 8px !important;
    padding-bottom: 8px !important;
}

#suspend-account-modal #suspension-duration-input {
    height: 38px !important;
    font-size: 12px !important;
}

#suspend-account-modal textarea {
    min-height: 58px !important;
    padding-top: 9px !important;
    padding-bottom: 9px !important;
    font-size: 12px !important;
}

#suspend-account-modal .px-8.pb-7 button {
    padding: 8px 18px !important;
    font-size: 12px !important;
}

/* =========================================================
   ACCOUNT STATUS FLASH
   Matches the Seller Compliance lower-right moderation toast.
========================================================== */
.account-decision-flash {
    position: fixed;
    right: 24px;
    bottom: 24px;
    z-index: 220;
    width: min(370px, calc(100vw - 32px));
    min-height: 72px;
    display: flex;
    align-items: flex-start;
    gap: 11px;
    padding: 13px 14px;
    box-sizing: border-box;
    background: #FFFFFF;
    border: 1px solid #E7E2DF;
    border-radius: 14px;
    box-shadow: 0 14px 34px rgba(42, 20, 15, .16);
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transform: translateY(14px);
    transition: opacity .08s ease, transform .08s ease, visibility .08s ease;
}

.account-decision-flash.show {
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
    transform: translateY(0);
}

.account-decision-flash.activated {
    border-color: #BFDDB8;
}

.account-decision-flash.suspended {
    border-color: #E7A8AB;
}

.account-decision-flash.deactivated {
    border-color: #F0C776;
}

.account-decision-flash-icon {
    width: 36px;
    height: 36px;
    flex: 0 0 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: #E7F4E3;
    color: #28721B;
}

.account-decision-flash.suspended .account-decision-flash-icon {
    background: #FFE3E5;
    color: #B3262E;
}

.account-decision-flash.deactivated .account-decision-flash-icon {
    background: #FFF1D6;
    color: #A56500;
}

.account-decision-flash-icon svg {
    width: 19px;
    height: 19px;
}

.account-decision-flash-copy {
    min-width: 0;
    flex: 1 1 auto;
    display: grid;
    gap: 3px;
    padding-top: 1px;
}

.account-decision-flash-copy strong {
    font-size: 13px;
    line-height: 1.25;
    font-weight: 600;
    color: #17120F;
}

.account-decision-flash-copy span {
    font-size: 11px;
    line-height: 1.45;
    font-weight: 400;
    color: #77716E;
}

.account-decision-flash-close {
    width: 28px;
    height: 28px;
    flex: 0 0 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: -2px;
    border: 0;
    border-radius: 50%;
    background: transparent;
    color: #AAA4A1;
    cursor: pointer;
    transition: background .15s ease, color .15s ease;
}

.account-decision-flash-close:hover {
    background: #F6F2F0;
    color: #5D5754;
}

.account-decision-flash-close svg {
    width: 15px;
    height: 15px;
}

@media (max-width: 640px) {
    .account-decision-flash {
        right: 16px;
        bottom: 16px;
    }
}
</style>
@endsection


<style>
    /* =========================================================
       USER MANAGEMENT FILTER COLOR CONSISTENCY
       Matches the Seller Compliance filter/search styling.
    ========================================================== */

    #user-management-search,
    #user-management-type,
    #user-management-status,
    #user-management-date {
        border-color: #D9D6D4 !important;
        background-color: #FFFFFF !important;
        color: #76716E !important;
    }

    #user-management-search::placeholder {
        color: #76716E !important;
        opacity: 1;
    }

    #user-management-search + svg,
    #user-management-type + svg,
    #user-management-status + svg,
    #user-management-date-button {
        color: #76716E !important;
    }

    #user-management-date::-webkit-datetime-edit,
    #user-management-date::-webkit-datetime-edit-fields-wrapper,
    #user-management-date::-webkit-datetime-edit-text,
    #user-management-date::-webkit-datetime-edit-month-field,
    #user-management-date::-webkit-datetime-edit-day-field,
    #user-management-date::-webkit-datetime-edit-year-field {
        color: #76716E !important;
    }

    #user-management-search:focus,
    #user-management-type:focus,
    #user-management-status:focus,
    #user-management-date:focus {
        border-color: #7B1B1B !important;
        box-shadow: 0 0 0 2px rgba(123, 27, 27, 0.10) !important;
    }
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const users = @json($users);
    const counts = @json($counts);
    const statusUrl = @json(route('admin.user.management.status', ['user' => '__USER__']));
    const detailUrl = @json(route('admin.user.management.show', ['user' => '__USER__']));
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    /*
        {id:1,name:'Juan Dela Cruz',type:'seller',email:'juan.delacruz@gmail.com',phone:'0917 123 4567',date:'2026-05-31',dateLabel:'May 31, 2026',status:'active',suspensionDays:0},
        {id:2,name:'Maria Santos',type:'buyer',email:'maria.santos@gmail.com',phone:'0928 765 4321',date:'2026-05-31',dateLabel:'May 31, 2026',status:'active',suspensionDays:0},
        {id:3,name:'Jose Ramirez',type:'seller',email:'jose.ramirez@gmail.com',phone:'0906 555 7890',date:'2026-05-30',dateLabel:'May 30, 2026',status:'active',suspensionDays:0},
        {id:4,name:'Anna Reyes',type:'buyer',email:'anna.reyes@gmail.com',phone:'0915 888 1122',date:'2026-05-30',dateLabel:'May 30, 2026',status:'deactivated',suspensionDays:0},
        {id:5,name:'Liza Gomez',type:'seller',email:'liza.gomez@gmail.com',phone:'0932 444 6677',date:'2026-05-30',dateLabel:'May 30, 2026',status:'suspended',suspensionDays:7},
        {id:6,name:'Mark Dela Vega',type:'buyer',email:'mark.delavega@gmail.com',phone:'0910 333 2211',date:'2026-05-29',dateLabel:'May 29, 2026',status:'active',suspensionDays:0},
        {id:7,name:'Patricia Lim',type:'seller',email:'patricia.lim@gmail.com',phone:'0918 777 8899',date:'2026-05-29',dateLabel:'May 29, 2026',status:'active',suspensionDays:0},
        {id:8,name:'Carlo Mendoza',type:'buyer',email:'carlo.mendoza@gmail.com',phone:'0921 555 1199',date:'2026-05-28',dateLabel:'May 28, 2026',status:'active',suspensionDays:0},
        {id:9,name:'Sofia Navarro',type:'seller',email:'sofia.navarro@gmail.com',phone:'0916 222 3344',date:'2026-05-28',dateLabel:'May 28, 2026',status:'active',suspensionDays:0},
        {id:10,name:'Daniel Cruz',type:'buyer',email:'daniel.cruz@gmail.com',phone:'0927 888 7766',date:'2026-05-27',dateLabel:'May 27, 2026',status:'suspended',suspensionDays:7},
        {id:11,name:'Nicole Bautista',type:'seller',email:'nicole.bautista@gmail.com',phone:'0919 404 1515',date:'2026-05-27',dateLabel:'May 27, 2026',status:'active',suspensionDays:0},
        {id:12,name:'Miguel Torres',type:'buyer',email:'miguel.torres@gmail.com',phone:'0920 123 9090',date:'2026-05-26',dateLabel:'May 26, 2026',status:'active',suspensionDays:0},
        {id:13,name:'Rachel Aquino',type:'seller',email:'rachel.aquino@gmail.com',phone:'0917 678 2345',date:'2026-05-26',dateLabel:'May 26, 2026',status:'active',suspensionDays:0},
        {id:14,name:'Kevin Villanueva',type:'buyer',email:'kevin.villanueva@gmail.com',phone:'0908 444 1122',date:'2026-05-25',dateLabel:'May 25, 2026',status:'active',suspensionDays:0},
        {id:15,name:'Angela Flores',type:'seller',email:'angela.flores@gmail.com',phone:'0916 987 6543',date:'2026-05-25',dateLabel:'May 25, 2026',status:'active',suspensionDays:0},
        {id:16,name:'Patrick Tan',type:'buyer',email:'patrick.tan@gmail.com',phone:'0922 111 2233',date:'2026-05-24',dateLabel:'May 24, 2026',status:'deactivated',suspensionDays:0},
        {id:17,name:'Bea Garcia',type:'seller',email:'bea.garcia@gmail.com',phone:'0918 222 4455',date:'2026-05-24',dateLabel:'May 24, 2026',status:'active',suspensionDays:0},
        {id:18,name:'Rico Santiago',type:'buyer',email:'rico.santiago@gmail.com',phone:'0917 333 5577',date:'2026-05-23',dateLabel:'May 23, 2026',status:'active',suspensionDays:0},
        {id:19,name:'Jasmine Co',type:'seller',email:'jasmine.co@gmail.com',phone:'0925 555 7788',date:'2026-05-23',dateLabel:'May 23, 2026',status:'active',suspensionDays:0},
        {id:20,name:'Nathan Sy',type:'buyer',email:'nathan.sy@gmail.com',phone:'0919 666 8899',date:'2026-05-22',dateLabel:'May 22, 2026',status:'suspended',suspensionDays:7},
        {id:21,name:'Camille Rivera',type:'seller',email:'camille.rivera@gmail.com',phone:'0917 123 8800',date:'2026-05-22',dateLabel:'May 22, 2026',status:'active',suspensionDays:0},
        {id:22,name:'Andrei Castillo',type:'buyer',email:'andrei.castillo@gmail.com',phone:'0921 777 9911',date:'2026-05-21',dateLabel:'May 21, 2026',status:'active',suspensionDays:0},
        {id:23,name:'Mica Torres',type:'seller',email:'mica.torres@gmail.com',phone:'0918 345 6789',date:'2026-05-21',dateLabel:'May 21, 2026',status:'active',suspensionDays:0},
        {id:24,name:'Gabriel Ramos',type:'buyer',email:'gabriel.ramos@gmail.com',phone:'0915 555 1212',date:'2026-05-20',dateLabel:'May 20, 2026',status:'active',suspensionDays:0},
        {id:25,name:'Ella Fernandez',type:'seller',email:'ella.fernandez@gmail.com',phone:'0928 111 3434',date:'2026-05-20',dateLabel:'May 20, 2026',status:'active',suspensionDays:0},
        {id:26,name:'Luis Mercado',type:'buyer',email:'luis.mercado@gmail.com',phone:'0909 222 5656',date:'2026-05-19',dateLabel:'May 19, 2026',status:'deactivated',suspensionDays:0},
        {id:27,name:'Ivy Manalo',type:'seller',email:'ivy.manalo@gmail.com',phone:'0917 919 3434',date:'2026-05-19',dateLabel:'May 19, 2026',status:'active',suspensionDays:0},
        {id:28,name:'Paolo Mendoza',type:'buyer',email:'paolo.mendoza@gmail.com',phone:'0917 642 1305',date:'2026-05-18',dateLabel:'May 18, 2026',status:'active',suspensionDays:0},
        {id:29,name:'Diana Lopez',type:'seller',email:'diana.lopez@gmail.com',phone:'0927 345 6677',date:'2026-05-18',dateLabel:'May 18, 2026',status:'active',suspensionDays:0},
        {id:30,name:'Ramon Navarro',type:'buyer',email:'ramon.navarro@gmail.com',phone:'0908 517 4423',date:'2026-05-17',dateLabel:'May 17, 2026',status:'suspended',suspensionDays:7}
    ]; */

    const state = { page:1, itemsPerPage:10, filtered:[...users] };

    const tbody = document.getElementById('user-management-body');
    const search = document.getElementById('user-management-search');
    const typeFilter = document.getElementById('user-management-type');
    const statusFilter = document.getElementById('user-management-status');
    const dateFilter = document.getElementById('user-management-date');
    const countLabel = document.getElementById('user-management-count');
    const pagesWrap = document.getElementById('user-management-pages');
    const prev = document.getElementById('user-management-prev');
    const next = document.getElementById('user-management-next');
    const itemsSelect = document.getElementById('user-management-items');
    const reload = document.getElementById('user-management-reload');
    const reloadIcon = document.getElementById('user-management-reload-icon');
    const dateBtn = document.getElementById('user-management-date-button');

    const modal = document.getElementById('user-management-modal');
    const documentModal = document.getElementById('user-document-modal');
    const documentFrame = document.getElementById('user-document-frame');
    const documentImage = document.getElementById('user-document-image');
    const documentEmpty = document.getElementById('user-document-empty');
    const documentTitle = document.getElementById('user-document-title');
    const suspendModal = document.getElementById('suspend-account-modal');
    const deactivateModal = document.getElementById('deactivate-account-modal');
    const actionFlash = document.getElementById('account-action-flash');
    let selectedUser = null;
    let flashTimeout = null;

    function initials(name) {
        return name.split(' ').map(v => v[0]).slice(0,2).join('').toUpperCase();
    }

    function statusBadge(status) {
        const config = {
            active: ['Active', 'bg-[#DDF0D6]', 'text-[#28721B]'],
            suspended: ['Suspended', 'bg-[#FFD7D9]', 'text-[#B3262E]'],
            deactivated: ['Deactivated', 'bg-[#FFE5D0]', 'text-[#D16B12]']
        }[status];

        return `<span class="inline-flex items-center px-3 py-1 rounded-full ${config[1]} ${config[2]} text-[11px] font-medium">${config[0]}</span>`;
    }

    function typeIcon(type) {
    const icon = type === 'seller'
        ? 'seller.png'
        : 'buyer.png';

    const label = type === 'seller'
        ? 'Seller'
        : 'Buyer';

    const iconBase = @json(asset('icons/admin/dashboard/body'));

    return `
        <div class="flex items-center gap-2">
            <img
                src="${iconBase}/${icon}"
                class="w-[18px] h-[18px] object-contain"
                alt="${label}"
            >
            <span class="text-[12px] text-gray-800">
                ${label}
            </span>
        </div>
    `;
}

    function filterUsers() {
        const q = (search.value || '').toLowerCase().trim();
        const type = typeFilter.value;
        const status = statusFilter.value;
        const date = dateFilter.value;

        state.filtered = users.filter(user => {
            const matchSearch = !q || [user.name,user.email,user.phone].some(v => v.toLowerCase().includes(q));
            const matchType = type === 'all' || user.type === type;
            const matchStatus = status === 'all' || user.status === status;
            const matchDate = !date || user.date === date;
            return matchSearch && matchType && matchStatus && matchDate;
        });

        state.page = 1;
        render();
        updateCards();
    }

    function render() {
        const totalPages = Math.max(1, Math.ceil(state.filtered.length / state.itemsPerPage));
        if (state.page > totalPages) state.page = totalPages;

        const start = (state.page - 1) * state.itemsPerPage;
        const pageItems = state.filtered.slice(start, start + state.itemsPerPage);

        tbody.innerHTML = pageItems.map(user => `
            <tr class="user-management-row cursor-pointer hover:bg-[#FFF9F7] transition" data-id="${user.id}" tabindex="0" role="button">
                <td class="px-4 py-2.5">
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 rounded-full bg-[#F4D0CA] flex items-center justify-center text-[10px] font-semibold text-[#7B1B1B] shrink-0">${initials(user.name)}</div>
                        <span class="text-[12px] font-medium text-gray-800">${user.name}</span>
                    </div>
                </td>
                <td class="px-4 py-2.5">${typeIcon(user.type)}</td>
                <td class="px-4 py-2.5 text-[12px] text-gray-400">${user.email}</td>
                <td class="px-4 py-2.5 text-[12px] text-gray-800">${user.phone}</td>
                <td class="px-4 py-2.5"><div class="text-[13px] text-gray-800">${user.dateLabel}</div><div class="text-[11px] text-gray-400">10:30 AM</div></td>
                <td class="px-4 py-2.5">${statusBadge(user.status)}</td>
            </tr>
        `).join('');

        countLabel.textContent = `Showing ${Math.min(state.itemsPerPage, pageItems.length)} out of ${state.filtered.length} users`;
        renderPages(totalPages);
    }

    function renderPages(totalPages) {
        pagesWrap.innerHTML = '';
        const maxVisible = 5;
        let startPage = Math.max(1, state.page - 2);
        let endPage = Math.min(totalPages, startPage + maxVisible - 1);
        startPage = Math.max(1, endPage - maxVisible + 1);

        for (let page = startPage; page <= endPage; page++) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.textContent = page;
            btn.className = `w-7 h-7 flex items-center justify-center rounded-md text-[12px] transition ${page === state.page ? 'bg-[#FFD1C2] text-[#7B1B1B] font-semibold' : 'hover:bg-gray-100 text-gray-700'}`;
            btn.addEventListener('click', () => { state.page = page; render(); });
            pagesWrap.appendChild(btn);
        }

        prev.disabled = state.page === 1;
        next.disabled = state.page === totalPages;
        prev.classList.toggle('opacity-40', prev.disabled);
        next.classList.toggle('opacity-40', next.disabled);
    }

    function updateCards() {
        const total = users.length;
        const buyers = users.filter(u => u.type === 'buyer').length;
        const sellers = users.filter(u => u.type === 'seller').length;
        const suspended = users.filter(u => u.status === 'suspended').length;

        document.getElementById('buyers-count-card').textContent = counts.buyers;
        document.getElementById('sellers-count-card').textContent = counts.sellers;
        document.getElementById('suspended-count-card').textContent = counts.suspended;
        document.getElementById('total-users-count-card').textContent = counts.total.toLocaleString();
    }

    function renderModalActions(user) {
        const actions = document.getElementById('user-modal-actions');
        const suspensionInfo = document.getElementById('user-suspension-info');
        const suspensionDuration = document.getElementById('user-suspension-duration');

        actions.innerHTML = '';
        suspensionInfo.classList.add('hidden');

        if (user.status === 'active') {
            actions.innerHTML = `
                <button
                    type="button"
                    id="user-modal-suspend"
                    class="px-6 py-2 rounded-lg border border-[#D41F1F] bg-[#FFE0E0] text-[#AE0000] text-[13px] font-semibold hover:bg-[#FFD1D1] transition"
                >
                    Suspend
                </button>

                <button
                    type="button"
                    id="user-modal-deactivate"
                    class="px-6 py-2 rounded-lg border border-[#F08B4D] bg-[#FFF0E6] text-[#C96A00] text-[13px] font-semibold hover:bg-[#FFE6D8] transition"
                >
                    Deactivate
                </button>
            `;
        } else if (user.status === 'suspended') {
            suspensionInfo.classList.remove('hidden');
            const days = Number(user.suspensionDays || 7);
            suspensionDuration.textContent = `${days} ${days === 1 ? 'day' : 'days'} remaining`;

            actions.innerHTML = `
                <button
                    type="button"
                    id="user-modal-activate"
                    class="px-6 py-2 rounded-lg border border-[#79C56C] bg-[#DDF0D6] text-[#16710C] text-[13px] font-semibold hover:bg-[#D2EAC9] transition"
                >
                    Activate
                </button>
            `;
        } else {
            actions.innerHTML = `
                <button
                    type="button"
                    id="user-modal-activate"
                    class="px-6 py-2 rounded-lg border border-[#79C56C] bg-[#DDF0D6] text-[#16710C] text-[13px] font-semibold hover:bg-[#D2EAC9] transition"
                >
                    Activate
                </button>
            `;
        }

        const suspendBtn = document.getElementById('user-modal-suspend');
        const deactivateBtn = document.getElementById('user-modal-deactivate');
        const activateBtn = document.getElementById('user-modal-activate');

        if (suspendBtn) {
            suspendBtn.addEventListener('click', openSuspendAccountModal);
        }
        if (deactivateBtn) {
            deactivateBtn.addEventListener('click', openDeactivateAccountModal);
        }
        if (activateBtn) {
            activateBtn.addEventListener('click', () => updateSelectedStatus('active', true));
        }
    }

    function splitName(fullName) {
        const parts = fullName.trim().split(/\s+/);
        const lastName = parts.length >= 2 ? parts[parts.length - 1] : parts[0];
        const firstName = parts.length >= 2 ? parts.slice(0, -1).join(' ') : parts[0];

        return {
            firstName,
            lastName
        };
    }

    function clearRadioGroup(name) {
        document.querySelectorAll(`input[name="${name}"]`).forEach(input => {
            input.checked = false;
        });
    }

    function resetSuspendForm() {
        clearRadioGroup('suspend-reason');
        document.getElementById('suspension-duration-input').value = '';
        document.getElementById('suspend-additional-details').value = '';
        document.getElementById('suspend-details-count').textContent = '0/300';
    }

    function resetDeactivateForm() {
        clearRadioGroup('deactivate-reason');
        document.getElementById('deactivate-additional-details').value = '';
        document.getElementById('deactivate-details-count').textContent = '0/300';
    }

    function openSuspendAccountModal() {
        if (!selectedUser) return;
        resetSuspendForm();
        suspendModal.classList.remove('hidden');
        suspendModal.classList.add('flex');
        suspendModal.setAttribute('aria-hidden', 'false');
    }

    function closeSuspendAccountModal() {
        suspendModal.classList.add('hidden');
        suspendModal.classList.remove('flex');
        suspendModal.setAttribute('aria-hidden', 'true');
    }

    function openDeactivateAccountModal() {
        if (!selectedUser) return;
        resetDeactivateForm();
        deactivateModal.classList.remove('hidden');
        deactivateModal.classList.add('flex');
        deactivateModal.setAttribute('aria-hidden', 'false');
    }

    function closeDeactivateAccountModal() {
        deactivateModal.classList.add('hidden');
        deactivateModal.classList.remove('flex');
        deactivateModal.setAttribute('aria-hidden', 'true');
    }

    function closeActionFlash() {
        if (flashTimeout) {
            clearTimeout(flashTimeout);
            flashTimeout = null;
        }

        actionFlash?.classList.remove('show');
    }

    function showActionFlash(status, user) {
        if (!actionFlash) return;

        const userName = user?.name || 'User';
        const userEmail = user?.email || 'their registered email address';

        const config = {
            active: {
                className: 'activated',
                title: 'Account Activated',
                message: `${userName}'s account has been activated. A notification will be sent to ${userEmail}.`
            },
            suspended: {
                className: 'suspended',
                title: 'Account Suspended',
                message: `${userName}'s account has been suspended. A notification will be sent to ${userEmail}.`
            },
            deactivated: {
                className: 'deactivated',
                title: 'Account Deactivated',
                message: `${userName}'s account has been deactivated. A notification will be sent to ${userEmail}.`
            }
        }[status] || {
            className: 'activated',
            title: 'Account Updated',
            message: `${userName}'s account status has been updated. A notification will be sent to ${userEmail}.`
        };

        document.getElementById('account-action-flash-title').textContent = config.title;
        document.getElementById('account-action-flash-message').textContent = config.message;

        actionFlash.classList.remove('activated', 'suspended', 'deactivated');
        actionFlash.classList.add(config.className);

        if (flashTimeout) {
            clearTimeout(flashTimeout);
            flashTimeout = null;
        }

        // Immediate display on the same action click.
        actionFlash.classList.add('show');

        flashTimeout = setTimeout(closeActionFlash, 3800);
    }

    function getSelectedRadioValue(name) {
        const selected = document.querySelector(`input[name="${name}"]:checked`);
        return selected ? selected.value : '';
    }

    function renderUserCategories(value) {
        const categories = document.getElementById('user-profile-categories');
        categories.replaceChildren();

        const categoryList = Array.isArray(value)
            ? value
            : String(value || '').split(',');
        const categoryValues = categoryList.map(category => category.trim()).filter(Boolean);

        if (categoryValues.length === 0) {
            const emptyState = document.createElement('span');
            emptyState.className = 'text-[13px] text-gray-500';
            emptyState.textContent = 'Not provided';
            categories.appendChild(emptyState);
            return;
        }

        const categoryStyles = {
            'pet-supplies': { background: '#E7F5E9', color: '#2F6B3A' },
            'electronics-and-gadgets': { background: '#DDEBFF', color: '#185FA3' },
            'womens-apparel': { background: '#F9DFEA', color: '#A12763' },
            'mens-apparel': { background: '#E6E2F8', color: '#5A4A9A' },
            'kids-and-baby': { background: '#FFE5B8', color: '#9A5B00' },
            'home-and-garden': { background: '#DDF3E4', color: '#27704A' },
            'sports-and-outdoors': { background: '#DDECF2', color: '#23627A' },
            'health-and-beauty': { background: '#FFE0DC', color: '#A63B2C' },
            'books-and-media': { background: '#E6E8F2', color: '#3F4A68' },
            'food-and-gourmet': { background: '#FFF0C7', color: '#8A5A00' },
            'automotive-motorcycle': { background: '#E3E3E3', color: '#434343' },
            'furniture-and-office-equipment': { background: '#EBDCCF', color: '#795548' },
            'jewelry-and-watches': { background: '#F8E2B8', color: '#946B00' },
            'office-and-school-supplies': { background: '#E2F0F7', color: '#2B617D' },
            'default': { background: '#F1EFEE', color: '#6B6663' },
        };

        const slugifyCategory = (category) => String(category || '')
            .trim()
            .toLowerCase()
            .replace(/&/g, 'and')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');

        categoryValues.forEach((category) => {
            const badge = document.createElement('span');
            const style = categoryStyles[slugifyCategory(category)] || categoryStyles.default;

            badge.className = 'inline-flex items-center rounded-full px-3 py-1 text-[12px] font-medium';
            badge.style.backgroundColor = style.background;
            badge.style.color = style.color;
            badge.textContent = category;
            categories.appendChild(badge);
        });
    }

    async function loadUserDetails(user) {
        const response = await fetch(detailUrl.replace('__USER__', user.id), {
            headers: { 'Accept': 'application/json' },
            credentials: 'same-origin'
        });
        if (!response.ok || selectedUser?.id !== user.id) return;

        const details = (await response.json()).details || {};
        const fields = {
            'user-modal-last-name': details.last_name,
            'user-modal-first-name': details.first_name,
            'user-modal-middle-name': details.middle_initial,
            'user-modal-sex': details.sex,
            'user-modal-birthday': details.birthday,
            'user-modal-age': details.age,
            'user-modal-province': details.province,
            'user-modal-municipality': details.municipality,
            'user-modal-barangay': details.barangay,
            'user-modal-street': details.street,
            'user-modal-house': details.house_number,
            'user-modal-zip': details.zip_code,
            'user-modal-business-name': details.business_name,
            'user-modal-business-category': details.line_of_business,
        };

        Object.entries(fields).forEach(([id, value]) => {
            document.getElementById(id).textContent = value || 'Not provided';
        });
        renderUserCategories(details.line_of_business);

        const validId = document.getElementById('user-modal-valid-id');
        const validIdEmpty = document.getElementById('user-modal-valid-id-empty');
        const validIdLink = document.getElementById('user-modal-valid-id-link');
        validId.src = details.valid_id_url || '';
        validId.classList.toggle('hidden', !details.valid_id_url);
        validIdEmpty.classList.toggle('hidden', Boolean(details.valid_id_url));
        validIdLink.href = '#';
        validIdLink.dataset.url = details.valid_id_url || '';
        validIdLink.classList.add('hidden');

        const permitLink = document.getElementById('user-modal-business-permit-link');
        const permitName = document.getElementById('user-modal-business-permit');
        permitLink.href = '#';
        permitLink.dataset.url = details.business_permit_url || '';
        permitLink.classList.toggle('pointer-events-none', !details.business_permit_url);
        permitName.textContent = details.upload_business_permit
            ? details.upload_business_permit.split('/').pop()
            : 'Business permit not provided';
    }

    function openModal(user) {
        selectedUser = user;

        const isSeller = user.type === 'seller';

        document.getElementById('user-profile-name').textContent = user.name;
        document.getElementById('user-profile-type').textContent = isSeller ? 'Seller' : 'Buyer';
        document.getElementById('user-profile-type-icon').src = isSeller
            ? @json(asset('icons/admin/dashboard/body/seller.png'))
            : @json(asset('icons/admin/dashboard/body/buyer.png'));
        document.getElementById('user-profile-type-icon').alt = isSeller ? 'Seller' : 'Buyer';

        const profileStatus = document.getElementById('user-profile-status');
        profileStatus.innerHTML = statusBadge(user.status);

        document.getElementById('user-profile-date').textContent = user.dateLabel;
        document.getElementById('user-profile-time').textContent = user.timeLabel || '';

        document.getElementById('user-modal-last-name').textContent = 'Loading...';
        document.getElementById('user-modal-first-name').textContent = 'Loading...';
        document.getElementById('user-modal-middle-name').textContent = 'Loading...';
        document.getElementById('user-modal-sex').textContent = 'Loading...';
        document.getElementById('user-modal-birthday').textContent = 'Loading...';
        document.getElementById('user-modal-age').textContent = 'Loading...';
        document.getElementById('user-modal-email').textContent = user.email;
        document.getElementById('user-modal-phone').textContent = user.phone;

        ['user-modal-province', 'user-modal-municipality', 'user-modal-barangay',
            'user-modal-street', 'user-modal-house', 'user-modal-zip'].forEach(id => {
            document.getElementById(id).textContent = 'Loading...';
        });

        const categories = document.getElementById('user-profile-categories');
        const businessSection = document.getElementById('seller-business-section');

        if (isSeller) {
            categories.classList.remove('hidden');
            businessSection.classList.remove('hidden');
            renderUserCategories('');

            document.getElementById('user-modal-business-name').textContent = 'Loading...';
            document.getElementById('user-modal-business-category').textContent = 'Loading...';
            document.getElementById('user-modal-business-permit').textContent = 'Loading...';
        } else {
            categories.classList.add('hidden');
            businessSection.classList.add('hidden');
        }

        renderModalActions(user);

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
        loadUserDetails(user);
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('overflow-hidden');
        selectedUser = null;
    }

    async function updateSelectedStatus(newStatus, notifyUser = false, action = {}) {
        if (!selectedUser) return;

        const user = users.find(u => u.id === selectedUser.id);
        if (!user) return;

        const previousUserState = { ...user };
        const previousCounts = { ...counts };
        const previousStatus = user.status;

        // =========================================================
        // IMMEDIATE / OPTIMISTIC UI UPDATE
        // Status, buttons, table and flash all change on the same click.
        // =========================================================
        user.status = newStatus;

        if (newStatus === 'suspended') {
            user.suspensionDays = Number(action.duration || user.suspensionDays || 7);
        } else {
            user.suspensionDays = 0;
        }

        if (previousStatus !== newStatus) {
            if (previousStatus === 'suspended' && newStatus !== 'suspended') {
                counts.suspended = Math.max(0, Number(counts.suspended || 0) - 1);
            } else if (previousStatus !== 'suspended' && newStatus === 'suspended') {
                counts.suspended = Number(counts.suspended || 0) + 1;
            }
        }

        selectedUser = user;

        // Change the profile status immediately.
        document.getElementById('user-profile-status').innerHTML = statusBadge(user.status);

        // Change Suspend / Deactivate / Activate buttons immediately.
        renderModalActions(user);

        // Change the status shown in the table immediately.
        render();

        // Update the summary card immediately when suspension count changes.
        updateCards();

        // Show the color-coded flash in the same browser frame.
        if (notifyUser) {
            showActionFlash(newStatus, user);
        }

        try {
            const response = await fetch(statusUrl.replace('__USER__', user.id), {
                method: 'PATCH',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ status: newStatus, ...action })
            });

            if (!response.ok) {
                const error = await response.json().catch(() => ({}));

                // Revert the optimistic UI when the backend rejects the action.
                Object.assign(user, previousUserState);
                Object.keys(counts).forEach(key => delete counts[key]);
                Object.assign(counts, previousCounts);

                selectedUser = user;
                document.getElementById('user-profile-status').innerHTML = statusBadge(user.status);
                renderModalActions(user);
                render();
                updateCards();

                closeActionFlash();
                alert(error.message || 'Unable to update this account.');
                return;
            }

            const result = await response.json();

            // Sync with the authoritative backend result without showing another toast.
            Object.assign(user, result.user);
            Object.assign(counts, result.counts);

            selectedUser = user;
            document.getElementById('user-profile-status').innerHTML = statusBadge(user.status);
            renderModalActions(user);
            render();
            updateCards();

        } catch (error) {
            console.error(error);

            // Revert the optimistic UI if the request itself fails.
            Object.assign(user, previousUserState);
            Object.keys(counts).forEach(key => delete counts[key]);
            Object.assign(counts, previousCounts);

            selectedUser = user;
            document.getElementById('user-profile-status').innerHTML = statusBadge(user.status);
            renderModalActions(user);
            render();
            updateCards();

            closeActionFlash();
            alert('Something went wrong while updating the account.');
        }
    }

    search.addEventListener('input', filterUsers);
    typeFilter.addEventListener('change', filterUsers);
    statusFilter.addEventListener('change', filterUsers);
    dateFilter.addEventListener('change', filterUsers);
    itemsSelect.addEventListener('change', () => {
        state.itemsPerPage = parseInt(itemsSelect.value, 10);
        state.page = 1;
        render();
    });

    dateBtn.addEventListener('click', () => {
        if (typeof dateFilter.showPicker === 'function') dateFilter.showPicker();
        else dateFilter.click();
    });

    prev.addEventListener('click', () => {
        if (state.page > 1) { state.page--; render(); }
    });

    next.addEventListener('click', () => {
        const totalPages = Math.ceil(state.filtered.length / state.itemsPerPage);
        if (state.page < totalPages) { state.page++; render(); }
    });

    reload.addEventListener('click', () => {
        reloadIcon.classList.add('animate-spin');
        reload.disabled = true;
        setTimeout(() => {
            search.value = '';
            typeFilter.value = 'all';
            statusFilter.value = 'all';
            dateFilter.value = '';
            itemsSelect.value = '10';
            state.itemsPerPage = 10;
            state.page = 1;
            state.filtered = [...users];
            render();
            updateCards();
            reloadIcon.classList.remove('animate-spin');
            reload.disabled = false;
        }, 500);
    });

    tbody.addEventListener('click', function (event) {
        const row = event.target.closest('.user-management-row');
        if (!row) return;
        const user = users.find(u => u.id === Number(row.dataset.id));
        if (user) openModal(user);
    });

    tbody.addEventListener('keydown', function (event) {
        if (event.key !== 'Enter' && event.key !== ' ') return;
        const row = event.target.closest('.user-management-row');
        if (!row) return;
        event.preventDefault();
        const user = users.find(u => u.id === Number(row.dataset.id));
        if (user) openModal(user);
    });

    function openDocumentModal(url, title) {
        if (!url) return;
        documentTitle.textContent = title || 'Document';
        documentEmpty.classList.add('hidden');
        documentImage.classList.add('hidden');
        documentFrame.classList.add('hidden');
        documentImage.src = '';
        documentFrame.src = 'about:blank';
        const path = String(url).split('?')[0].toLowerCase();
        const isImage = /\.(jpg|jpeg|png|gif|webp|bmp|svg)$/i.test(path);
        if (isImage) {
            documentImage.src = url;
            documentImage.classList.remove('hidden');
        } else {
            documentFrame.src = url;
            documentFrame.classList.remove('hidden');
        }
        documentModal.classList.remove('hidden');
        documentModal.classList.add('flex', 'is-open');
        documentModal.setAttribute('aria-hidden', 'false');
    }

    function closeDocumentModal() {
        documentModal.classList.add('hidden');
        documentModal.classList.remove('flex', 'is-open');
        documentModal.setAttribute('aria-hidden', 'true');
        documentImage.src = '';
        documentFrame.src = 'about:blank';
    }

    document.getElementById('user-modal-valid-id-preview').addEventListener('click', function () {
        openDocumentModal(document.getElementById('user-modal-valid-id-link').dataset.url, 'Valid ID');
    });

    document.getElementById('user-modal-business-permit-link').addEventListener('click', function (event) {
        event.preventDefault();
        openDocumentModal(this.dataset.url, 'Business Permit');
    });

    document.getElementById('close-user-document-modal').addEventListener('click', closeDocumentModal);
    documentModal.addEventListener('click', function (event) {
        if (event.target === documentModal) closeDocumentModal();
    });

    document.getElementById('close-user-management-modal').addEventListener('click', closeModal);
    modal.addEventListener('click', event => {
        if (event.target === modal) closeModal();
    });

    document.getElementById('cancel-suspend-account').addEventListener('click', closeSuspendAccountModal);
    suspendModal.addEventListener('click', event => {
        if (event.target === suspendModal) closeSuspendAccountModal();
    });

    document.getElementById('cancel-deactivate-account').addEventListener('click', closeDeactivateAccountModal);
    deactivateModal.addEventListener('click', event => {
        if (event.target === deactivateModal) closeDeactivateAccountModal();
    });

    document.getElementById('close-account-action-flash').addEventListener('click', closeActionFlash);

    document.getElementById('suspend-additional-details').addEventListener('input', function () {
        document.getElementById('suspend-details-count').textContent = `${this.value.length}/300`;
    });

    document.getElementById('deactivate-additional-details').addEventListener('input', function () {
        document.getElementById('deactivate-details-count').textContent = `${this.value.length}/300`;
    });

    // Allow digits only for the suspension duration field.
    const suspensionDurationInput = document.getElementById('suspension-duration-input');

    suspensionDurationInput.addEventListener('keydown', function (event) {
        const allowedControlKeys = [
            'Backspace', 'Delete', 'Tab', 'Escape',
            'ArrowLeft', 'ArrowRight', 'Home', 'End'
        ];

        if (allowedControlKeys.includes(event.key) ||
            event.ctrlKey || event.metaKey) {
            return;
        }

        if (!/^[0-9]$/.test(event.key)) {
            event.preventDefault();
        }
    });

    suspensionDurationInput.addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '');
    });

    document.getElementById('confirm-suspend-account').addEventListener('click', function () {
        if (!selectedUser) return;

        const reason = getSelectedRadioValue('suspend-reason');
        const duration = Number(document.getElementById('suspension-duration-input').value);

        if (!reason) {
            alert('Please select a suspension reason.');
            return;
        }

        if (!Number.isInteger(duration) || duration < 1) {
            alert('Please enter a valid suspension duration in days.');
            return;
        }

        const user = users.find(u => u.id === selectedUser.id);
        if (!user) return;

        user.suspensionDays = duration;
        closeSuspendAccountModal();
        updateSelectedStatus('suspended', true, {
            reason,
            duration,
            details: document.getElementById('suspend-additional-details').value
        });
    });

    document.getElementById('confirm-deactivate-account').addEventListener('click', function () {
        if (!selectedUser) return;

        const reason = getSelectedRadioValue('deactivate-reason');
        if (!reason) {
            alert('Please select a deactivation reason.');
            return;
        }

        closeDeactivateAccountModal();
        updateSelectedStatus('deactivated', true, {
            reason,
            details: document.getElementById('deactivate-additional-details').value
        });
    });

    document.addEventListener('keydown', event => {
        if (event.key !== 'Escape') return;

        if (!suspendModal.classList.contains('hidden')) {
            closeSuspendAccountModal();
            return;
        }

        if (!deactivateModal.classList.contains('hidden')) {
            closeDeactivateAccountModal();
            return;
        }

        if (!documentModal.classList.contains('hidden')) { closeDocumentModal(); return; }

        if (!modal.classList.contains('hidden')) closeModal();
    });

    // Page-load animation
    const pageContent = document.getElementById('admin-content');
    pageContent.classList.add('opacity-0', 'translate-y-2');
    requestAnimationFrame(() => {
        setTimeout(() => pageContent.classList.remove('opacity-0', 'translate-y-2'), 70);
    });

    render();
    updateCards();
});
</script>
@endpush
