@extends('layouts.admin')

@section('page-title', 'User Management')

@section('content')

@vite('resources/css/admin/user-management.css')




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
                            <div id="user-profile-avatar" class="w-[76px] h-[76px] rounded-full bg-[#FFE6DE] text-[#650B12] flex items-center justify-center shrink-0 overflow-hidden text-[22px] font-semibold" aria-label="User profile photo">
                                <span id="user-profile-initials">U</span>
                                <img id="user-profile-photo" class="hidden w-full h-full object-cover" alt="">
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

    <style>
/* Isolated User Management change-request UI: does not modify existing account modals. */
.um-change-backdrop{position:fixed;inset:0;z-index:230;display:flex;align-items:center;justify-content:center;padding:18px;background:rgba(15,7,9,.55);backdrop-filter:blur(2px)}.um-change-backdrop[hidden],.um-change-flash[hidden]{display:none!important}.um-change-dialog{background:#fff;border-radius:22px;width:min(770px,100%);max-height:92dvh;overflow-y:auto;box-shadow:0 24px 65px #0003;font-family:Poppins,sans-serif}.um-change-header{display:flex;justify-content:space-between;gap:15px;padding:24px 27px 18px;border-bottom:1px solid #eee5e1}.um-change-header h2{font-size:19px;font-weight:650;margin:0;color:#2b2223}.um-change-header p{font-size:12px;color:#928782;margin:7px 0 0}.um-change-x{height:32px;width:32px;border-radius:50%;background:#f5f1ef;color:#756b68;border:0;flex-shrink:0;font-size:24px;line-height:1;cursor:pointer}.um-change-content{padding:20px 27px}.um-change-notice{background:#fff9f1;border:1px solid #f2e5cd;color:#80592d;font-size:11px;line-height:1.7;border-radius:9px;padding:11px 13px;margin:0 0 16px}.um-change-table-wrap{overflow:auto}.um-change-table{width:100%;border-collapse:collapse;min-width:420px}.um-change-table th{padding:12px;text-align:left;background:#faf7f6;color:#7f7672;font-size:11px;font-weight:600;border-bottom:1px solid #ece5e2}.um-change-table td{padding:14px 12px;font-size:12px;color:#3b3331;border-bottom:1px solid #f1eae7;overflow-wrap:anywhere;vertical-align:top}.um-change-table td:last-child{color:#247245;font-weight:600}.um-change-meta{font-size:11px;color:#958884;margin:17px 0 0}.um-change-actions{display:flex;justify-content:flex-end;gap:10px;flex-wrap:wrap;padding:18px 27px 23px;border-top:1px solid #eee5e1}.um-btn{border-radius:9px;padding:10px 17px;font:600 12px Poppins,sans-serif;border:1px solid #ded5d2;cursor:pointer}.um-btn-neutral{background:#fff;color:#655b58}.um-btn-reject{background:#ffe8e8;color:#9f2429;border-color:#edc1c3}.um-btn-approve{background:#e8f5e9;color:#277b41;border-color:#b9ddc0}.um-change-badge{border:1px solid #efcb9b;background:#fff5e5;color:#93601d;padding:5px 9px;border-radius:999px;font:600 10px Poppins,sans-serif;cursor:pointer;white-space:nowrap;margin-top:5px;display:inline-flex;align-items:center;gap:6px}.um-change-badge:before{content:'';width:6px;height:6px;background:#d98f2b;border-radius:50%}.um-change-badge:hover{background:#ffeccf}.um-reason-dialog{max-width:570px}.um-reason-label{display:block;font-size:13px;font-weight:600;margin:0 0 9px}.um-reason-label span,.um-reason-error{color:#b42d32}.um-reason-label small{font-weight:400;color:#918681}.um-reason-options{display:grid;gap:7px;margin-bottom:15px}.um-reason-options label{display:flex;align-items:center;gap:10px;border:1px solid #ebe3df;border-radius:9px;background:#fdfcfb;padding:10px 12px;font-size:12px;cursor:pointer}.um-reason-options input{accent-color:#7b1b1b;flex-shrink:0}.um-reason-error{font-size:11px;margin:0 0 12px}#um-reason-notes{display:block;width:100%;resize:vertical;border:1px solid #ddd4d0;border-radius:9px;padding:11px;font:12px Poppins,sans-serif}#um-reason-notes:focus{outline:none;border-color:#a18a87;box-shadow:0 0 0 3px #77777714}.um-reason-counter{text-align:right;color:#938984;font-size:10px;margin-top:5px}.um-change-flash{position:fixed;z-index:280;right:24px;bottom:24px;max-width:min(380px,calc(100vw - 30px));background:#fff;border:1px solid #cce2c8;border-radius:13px;box-shadow:0 12px 30px #0002;padding:13px 14px;display:flex;gap:10px;align-items:flex-start;font-family:Poppins,sans-serif}.um-change-flash.is-error{border-color:#edc2c3}.um-flash-icon{height:30px;width:30px;flex:0 0 30px;border-radius:50%;display:grid;place-items:center;background:#e2f5dd;color:#277844}.um-change-flash.is-error .um-flash-icon{background:#ffe5e5;color:#aa3034}.um-change-flash strong{font-size:12px}.um-change-flash p{font-size:11px;color:#857b76;margin:4px 0 0}.um-change-flash>button{background:none;border:0;margin-left:auto;font-size:20px;color:#999;cursor:pointer}@media(max-width:540px){.um-change-content{padding:15px}.um-change-header,.um-change-actions{padding-left:17px;padding-right:17px}}
</style>

    <!-- Account Information Change Requests (UI demo only) -->
    <div id="um-change-modal" class="um-change-backdrop" hidden role="presentation">
      <section class="um-change-dialog" role="dialog" aria-modal="true" aria-labelledby="um-change-title">
        <header class="um-change-header"><div><h2 id="um-change-title">Account Information Change Request</h2><p id="um-change-subtitle">Review the user's proposed updates.</p></div><button type="button" class="um-change-x" id="um-change-close" aria-label="Close">&times;</button></header>
        <div class="um-change-content"><p class="um-change-notice">The original account information remains unchanged until a request is approved. This is a UI-only preview.</p><div class="um-change-table-wrap"><table class="um-change-table"><thead><tr><th>Information</th><th>Current</th><th>Requested</th></tr></thead><tbody id="um-change-fields"></tbody></table></div><p id="um-change-meta" class="um-change-meta"></p></div>
        <footer class="um-change-actions"><button type="button" class="um-btn um-btn-neutral" id="um-change-cancel">Close</button><button type="button" class="um-btn um-btn-reject" id="um-change-reject">Disapprove</button><button type="button" class="um-btn um-btn-approve" id="um-change-approve">Approve</button></footer>
      </section>
    </div>
    <div id="um-reason-modal" class="um-change-backdrop" hidden role="presentation">
      <section class="um-change-dialog um-reason-dialog" role="dialog" aria-modal="true" aria-labelledby="um-reason-title">
        <header class="um-change-header"><div><h2 id="um-reason-title">Disapprove Information Change</h2><p>Please select a reason before rejecting the request.</p></div><button type="button" class="um-change-x" id="um-reason-close" aria-label="Close">&times;</button></header>
        <div class="um-change-content"><label class="um-reason-label">Reason <span>*</span></label><div id="um-reason-options" class="um-reason-options">
          <label><input type="radio" name="um_disapprove_reason" value="Submitted information could not be verified."> Submitted information could not be verified.</label>
          <label><input type="radio" name="um_disapprove_reason" value="Incomplete or inconsistent information."> Incomplete or inconsistent information.</label>
          <label><input type="radio" name="um_disapprove_reason" value="Requested changes conflict with existing account records."> Requested changes conflict with existing account records.</label>
          <label><input type="radio" name="um_disapprove_reason" value="Additional supporting documents are required."> Additional supporting documents are required.</label>
          <label><input type="radio" name="um_disapprove_reason" value="Other"> Other (please specify)</label>
        </div><p id="um-reason-error" class="um-reason-error" hidden>Select a reason before continuing.</p><label class="um-reason-label" for="um-reason-notes">Additional Details <small>(optional; required for Other)</small></label><textarea id="um-reason-notes" rows="3" maxlength="300" placeholder="Explain the reason..."></textarea><div class="um-reason-counter"><span id="um-reason-count">0</span>/300</div></div>
        <footer class="um-change-actions"><button class="um-btn um-btn-neutral" type="button" id="um-reason-cancel">Cancel</button><button class="um-btn um-btn-reject" type="button" id="um-reason-confirm">Confirm Disapproval</button></footer>
      </section>
    </div>
    <div id="um-change-flash" class="um-change-flash" hidden role="status" aria-live="polite"><span id="um-change-flash-icon" class="um-flash-icon">✓</span><div><strong id="um-change-flash-title"></strong><p id="um-change-flash-text"></p></div><button type="button" id="um-change-flash-close" aria-label="Close notification">&times;</button></div>

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




@endsection




@php
    $userManagementClientConfig = [
        'users' => $users,
        'counts' => $counts,
        'listUrl' => url('/api/v1/admin/users'),
        'statusUrl' => url('/api/v1/admin/users/__USER__/status'),
        'detailUrl' => url('/api/v1/admin/users/__USER__'),
        'iconBase' => asset('icons/admin/dashboard/body'),
        'icons' => [
            'seller' => asset('icons/admin/dashboard/body/seller.png'),
            'buyer' => asset('icons/admin/dashboard/body/buyer.png'),
        ],
    ];
@endphp

<script
    type="application/json"
    id="userManagementConfig"
>{!! json_encode($userManagementClientConfig, JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>

@push('scripts')

@vite('resources/js/admin/user-management.js')

@endpush