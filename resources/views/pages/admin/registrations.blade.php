
<style>
    /* Hide the visual scrollbar inside registration detail modals while keeping them scrollable. */
    .registration-detail-modal {
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .registration-detail-modal::-webkit-scrollbar {
        display: none;
        width: 0;
        height: 0;
    }

    /* Make the entire Approved/Rejected modal content scroll as one unit. */
    .registration-archive-modal {
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .registration-archive-modal::-webkit-scrollbar {
        display: none;
        width: 0;
        height: 0;
    }
</style>

@extends('layouts.admin')

@section('page-title', 'Account Registrations')

@section('content')

<div
    id="admin-content"
    class="ml-72 pt-[128px] px-6 pb-8 min-h-screen transition-all duration-300"
>

    <!-- =========================================================
         PAGE HEADER
    ========================================================== -->

    <div class="mb-8">

        <h2 class="text-[26px] font-bold text-gray-900">
            Account Registrations
        </h2>

    </div>


    <!-- =========================================================
         REGISTRATION STAT CARDS
    ========================================================== -->

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">

        <!-- =====================================================
             PENDING
        ====================================================== -->

        <div
            class="bg-white rounded-xl p-5 shadow-sm border border-gray-100
                   min-h-[132px]
                   flex flex-col justify-between
                   transition-all duration-300
                   hover:-translate-y-1 hover:shadow-md"
        >

            <div class="flex items-center gap-4">

                <!-- Icon -->

                <div
                    class="w-14 h-14 shrink-0
                           flex items-center justify-center"
                >

                    <img
                        src="{{ asset('icons/admin/registrations/Pending Registrations.png') }}"
                        class="w-14 h-14 object-contain"
                        alt="Pending Registrations"
                    >

                </div>


                <!-- Content -->

                <div class="min-w-0">

                    <p id="pending-count-card" class="text-[26px] font-bold text-gray-900 leading-none">
                        18
                    </p>

                    <p class="text-[14px] text-gray-400 mt-1 whitespace-nowrap">
                        Pending Requests
                    </p>

                </div>

            </div>


            <!-- Bottom Text -->

            <p class="mt-3 ml-[72px] text-[13px] text-green-600">
                <span class="text-[18px] align-middle">↑</span>
                <span class="font-semibold">12%</span>
                <span class="text-gray-400"> from yesterday</span>
            </p>

        </div>


        <!-- =====================================================
             REJECTED USERS ARCHIVE
        ====================================================== -->

        <div>

            <button
                type="button"
                id="rejected-users-button"
                class="w-full bg-white rounded-xl p-5 shadow-sm border border-gray-100
       min-h-[132px]
       relative
       text-left
       flex flex-col justify-center
                       transition-all duration-300
                       hover:-translate-y-1 hover:shadow-md
                       hover:border-[#E9A3A3]
                       group"
            >

                <div class="flex items-center justify-between">

                    <div class="flex items-center gap-4">

                        <!-- Icon -->

                        <div
                            class="w-14 h-14 shrink-0
                                   flex items-center justify-center"
                        >

                            <img
                                src="{{ asset('icons/admin/registrations/Rejected Registrations.png') }}"
                                class="w-14 h-14 object-contain"
                                alt="Rejected Registrations"
                            >

                        </div>


                        <!-- Content -->

                        <div class="min-w-0">

                            <p id="rejected-count-card" class="text-[26px] font-bold text-gray-900 leading-none">
                                4
                            </p>

                            <p class="text-[14px] text-gray-400 mt-1 whitespace-nowrap">
                                Rejected Users
                            </p>

                        </div>

                    </div>


                    <!-- Arrow -->

                    <svg
                        class="w-5 h-5 shrink-0 text-gray-400
                               group-hover:text-[#7B1B1B]
                               group-hover:translate-x-1
                               transition-all duration-300"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="m9 5 7 7-7 7"
                        />

                    </svg>

                </div>


                <!-- Bottom Text -->

                <div class="mt-3 ml-[72px]">

                    <span class="text-[12px] text-[#7B1B1B] font-medium">
                        View rejected users →
                    </span>

                </div>

            </button>

        </div>


        <!-- =====================================================
             APPROVED USERS ARCHIVE
        ====================================================== -->

        <div>

            <button
                type="button"
                id="approved-users-button"
                class="w-full bg-white rounded-xl p-5 shadow-sm border border-gray-100
       min-h-[132px]
       relative
       text-left
       flex flex-col justify-center
                       transition-all duration-300
                       hover:-translate-y-1 hover:shadow-md
                       hover:border-[#E9A3A3]
                       group"
            >

                <div class="flex items-center justify-between">

                    <div class="flex items-center gap-4">

                        <!-- Icon -->

                        <div
                            class="w-14 h-14 shrink-0
                                   flex items-center justify-center"
                        >

                            <img
                                src="{{ asset('icons/admin/registrations/Approved Registrations.png') }}"
                                class="w-14 h-14 object-contain"
                                alt="Approved Registrations"
                            >

                        </div>


                        <!-- Content -->

                        <div class="min-w-0">

                            <p id="approved-count-card" class="text-[26px] font-bold text-gray-900 leading-none">
                                8
                            </p>

                            <p class="text-[14px] text-gray-400 mt-1 whitespace-nowrap">
                                Approved Users
                            </p>

                        </div>

                    </div>


                    <!-- Arrow -->

                    <svg
                        class="w-5 h-5 shrink-0 text-gray-400
                               group-hover:text-[#7B1B1B]
                               group-hover:translate-x-1
                               transition-all duration-300"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="m9 5 7 7-7 7"
                        />

                    </svg>

                </div>


                <!-- Bottom Text -->

                <div class="mt-3 ml-[72px]">

                    <span class="text-[12px] text-[#7B1B1B] font-medium">
                        View approved users →
                    </span>

                </div>

            </button>

        </div>


        <!-- =====================================================
             TOTAL REGISTRATIONS
        ====================================================== -->

        <div
            class="bg-white rounded-xl p-5 shadow-sm border border-gray-100
                   min-h-[132px]
                   relative
                   flex flex-col justify-start
                   transition-all duration-300
                   hover:-translate-y-1 hover:shadow-md"
        >

            <div class="flex items-center gap-4">

                <!-- Icon -->

                <div
                    class="w-14 h-14 shrink-0
                           flex items-center justify-center"
                >

                    <img
                        src="{{ asset('icons/admin/registrations/Total registrations.png') }}"
                        class="w-14 h-14 object-contain"
                        alt="Total Registrations"
                    >

                </div>


                <!-- Content -->

                <div class="min-w-0">

                    <p id="total-count-card" class="text-[26px] font-bold text-gray-900 leading-none">
                        42
                    </p>

                    <p class="text-[14px] text-gray-400 mt-1 whitespace-nowrap">
                        Total Registration
                    </p>

                </div>

            </div>

        </div>

    </div>


    <!-- =========================================================
         APPROVED USERS ARCHIVE MODAL
    ========================================================== -->

    <div
        id="approved-users-modal"
        class="fixed inset-0 z-[100] hidden items-center justify-center
               bg-black/30 backdrop-blur-sm px-5"
    >

        <div
            class="registration-archive-modal bg-white w-full max-w-5xl max-h-[85vh]
                   overflow-y-auto rounded-2xl shadow-xl
                   transform transition-all duration-300"
        >

            <!-- Modal Header -->

            <div
                class="flex items-center justify-between
                       px-6 py-5 border-b border-gray-200"
            >

                <div>

                    <h3 class="text-[21px] font-bold text-gray-900">
                        Approved Users
                    </h3>

                    <p class="text-[13px] text-gray-400 mt-1">
                        Archived approved registrations
                    </p>

                </div>


                <button
                    type="button"
                    id="close-approved-users"
                    class="w-9 h-9 flex items-center justify-center
                           rounded-full text-gray-500
                           hover:bg-gray-100 hover:text-gray-800
                           transition"
                >

                    <svg
                        class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 6l12 12M18 6L6 18"
                        />

                    </svg>

                </button>

            </div>


            <!-- Approved Users Search -->
            <div class="px-6 py-4 border-b border-gray-100 bg-[#FFFBF9]">
                <div class="relative max-w-md">
                    <input
                        type="text"
                        id="approved-users-search"
                        placeholder="Search approved users..."
                        class="w-full h-[40px] rounded-lg border border-gray-300 bg-white pl-10 pr-4 text-[13px] text-gray-700 placeholder:text-gray-300 outline-none focus:border-[#7B1B1B] focus:ring-2 focus:ring-[#7B1B1B]/10 transition"
                    >
                    <svg
                        class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z" />
                    </svg>
                </div>
            </div>


            <!-- Approved Users Table -->

            <div class="overflow-x-auto max-h-none">

                <table class="w-full min-w-[800px]">

                    <thead class="sticky top-0 bg-white">

                        <tr class="border-b border-gray-200">

                            <th class="text-left px-6 py-4 text-[13px] font-medium text-gray-400">
                                Applicant
                            </th>

                            <th class="text-left px-6 py-4 text-[13px] font-medium text-gray-400">
                                User Type
                            </th>

                            <th class="text-left px-6 py-4 text-[13px] font-medium text-gray-400">
                                Email
                            </th>

                            <th class="text-left px-6 py-4 text-[13px] font-medium text-gray-400">
                                Phone
                            </th>

                            <th class="text-left px-6 py-4 text-[13px] font-medium text-gray-400">
                                <button
                                    type="button"
                                    class="archive-date-sort inline-flex items-center gap-1.5 hover:text-[#7B1B1B] transition"
                                    data-target="approved-users-body"
                                    aria-label="Sort approved users by date"
                                >
                                    <span>Date Approved</span>
                                    <span class="archive-sort-arrow inline-flex items-center justify-center w-6 h-6 text-[17px] leading-none font-bold text-[#7B1B1B] rounded-md bg-[#FFF0EC] border border-[#F3C2B5]">↓</span>
                                </button>
                            </th>

                        </tr>

                    </thead>


                    <tbody id="approved-users-body" class="divide-y divide-gray-200">

                        <!-- APPROVED USER 1 -->
                        <tr class="hover:bg-[#FFF9F7] transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-[#F6D8D2] flex items-center justify-center text-[11px] font-semibold text-[#7B1B1B] shrink-0">JD</div>
                                    <span class="text-[13px] font-medium text-gray-800">Juan Dela Cruz</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <img src="{{ asset('icons/admin/dashboard/body/seller.png') }}" class="w-5 h-5 object-contain" alt="Seller">
                                    <span class="text-[13px]">Seller</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-[13px] text-gray-400">juan.delacruz@gmail.com</td>
                            <td class="px-6 py-4 text-[13px]">0917 123 4567</td>
                            <td class="px-6 py-4 text-[13px]">May 31, 2026</td>
                        </tr>

                        <!-- APPROVED USER 2 -->
                        <tr class="hover:bg-[#FFF9F7] transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-[#F4E0C5] flex items-center justify-center text-[11px] font-semibold text-[#8C5A11] shrink-0">MS</div>
                                    <span class="text-[13px] font-medium text-gray-800">Maria Santos</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <img src="{{ asset('icons/admin/dashboard/body/buyer.png') }}" class="w-5 h-5 object-contain" alt="Buyer">
                                    <span class="text-[13px]">Buyer</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-[13px] text-gray-400">maria.santos@gmail.com</td>
                            <td class="px-6 py-4 text-[13px]">0928 765 4321</td>
                            <td class="px-6 py-4 text-[13px]">May 30, 2026</td>
                        </tr>

                        <!-- APPROVED USER 3 -->
                        <tr class="hover:bg-[#FFF9F7] transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-[#DCEBD9] flex items-center justify-center text-[11px] font-semibold text-[#37662E] shrink-0">JR</div>
                                    <span class="text-[13px] font-medium text-gray-800">Jose Ramirez</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <img src="{{ asset('icons/admin/dashboard/body/buyer.png') }}" class="w-5 h-5 object-contain" alt="Buyer">
                                    <span class="text-[13px]">Buyer</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-[13px] text-gray-400">jose.ramirez@gmail.com</td>
                            <td class="px-6 py-4 text-[13px]">0906 555 7890</td>
                            <td class="px-6 py-4 text-[13px]">May 29, 2026</td>
                        </tr>

                        <!-- APPROVED USER 4 -->
                        <tr class="hover:bg-[#FFF9F7] transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-[#F5DAD8] flex items-center justify-center text-[11px] font-semibold text-[#8A2F28] shrink-0">AR</div>
                                    <span class="text-[13px] font-medium text-gray-800">Anna Reyes</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <img src="{{ asset('icons/admin/dashboard/body/seller.png') }}" class="w-5 h-5 object-contain" alt="Seller">
                                    <span class="text-[13px]">Seller</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-[13px] text-gray-400">anna.reyes@gmail.com</td>
                            <td class="px-6 py-4 text-[13px]">0915 888 1122</td>
                            <td class="px-6 py-4 text-[13px]">May 28, 2026</td>
                        </tr>

                        <!-- APPROVED USER 5 -->
                        <tr class="hover:bg-[#FFF9F7] transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-[#DFE8F4] flex items-center justify-center text-[11px] font-semibold text-[#35577A] shrink-0">PM</div>
                                    <span class="text-[13px] font-medium text-gray-800">Paolo Mendoza</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <img src="{{ asset('icons/admin/dashboard/body/buyer.png') }}" class="w-5 h-5 object-contain" alt="Buyer">
                                    <span class="text-[13px]">Buyer</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-[13px] text-gray-400">paolo.mendoza@gmail.com</td>
                            <td class="px-6 py-4 text-[13px]">0917 642 1305</td>
                            <td class="px-6 py-4 text-[13px]">May 27, 2026</td>
                        </tr>

                        <!-- APPROVED USER 6 -->
                        <tr class="hover:bg-[#FFF9F7] transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-[#E6DFF0] flex items-center justify-center text-[11px] font-semibold text-[#624A7A] shrink-0">LC</div>
                                    <span class="text-[13px] font-medium text-gray-800">Liza Cruz</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <img src="{{ asset('icons/admin/dashboard/body/seller.png') }}" class="w-5 h-5 object-contain" alt="Seller">
                                    <span class="text-[13px]">Seller</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-[13px] text-gray-400">liza.cruz@gmail.com</td>
                            <td class="px-6 py-4 text-[13px]">0932 444 6677</td>
                            <td class="px-6 py-4 text-[13px]">May 26, 2026</td>
                        </tr>

                        <!-- APPROVED USER 7 -->
                        <tr class="hover:bg-[#FFF9F7] transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-[#F0E4D5] flex items-center justify-center text-[11px] font-semibold text-[#765832] shrink-0">DV</div>
                                    <span class="text-[13px] font-medium text-gray-800">Mark Dela Vega</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <img src="{{ asset('icons/admin/dashboard/body/buyer.png') }}" class="w-5 h-5 object-contain" alt="Buyer">
                                    <span class="text-[13px]">Buyer</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-[13px] text-gray-400">mark.delavega@gmail.com</td>
                            <td class="px-6 py-4 text-[13px]">0910 333 2211</td>
                            <td class="px-6 py-4 text-[13px]">May 24, 2026</td>
                        </tr>

                        <!-- APPROVED USER 8 -->
                        <tr class="hover:bg-[#FFF9F7] transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-[#DDEFEA] flex items-center justify-center text-[11px] font-semibold text-[#2E6B5D] shrink-0">PL</div>
                                    <span class="text-[13px] font-medium text-gray-800">Patricia Lim</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <img src="{{ asset('icons/admin/dashboard/body/seller.png') }}" class="w-5 h-5 object-contain" alt="Seller">
                                    <span class="text-[13px]">Seller</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-[13px] text-gray-400">patricia.lim@gmail.com</td>
                            <td class="px-6 py-4 text-[13px]">0918 777 8899</td>
                            <td class="px-6 py-4 text-[13px]">May 23, 2026</td>
                        </tr>

                    </tbody>

                </table>

            </div>


            <!-- Modal Footer -->

            <div
                class="px-6 py-4 border-t border-gray-200
                       flex items-center justify-between"
            >

                <p id="approved-users-count" class="text-[13px] text-gray-400">
                    8 approved users
                </p>

                <button
                    type="button"
                    id="close-approved-users-bottom"
                    class="px-5 py-2 rounded-lg
                           bg-[#7B1B1B] text-white
                           text-[13px] font-medium
                           hover:bg-[#641515]
                           transition"
                >
                    Close
                </button>

            </div>

        </div>

    </div>


    <!-- =========================================================
         REJECTED USERS ARCHIVE MODAL
    ========================================================== -->

    <div
        id="rejected-users-modal"
        class="fixed inset-0 z-[100] hidden items-center justify-center
               bg-black/30 backdrop-blur-sm px-5"
    >

        <div
            class="registration-archive-modal bg-white w-full max-w-5xl max-h-[85vh]
                   overflow-y-auto rounded-2xl shadow-xl
                   transform transition-all duration-300"
        >

            <!-- Modal Header -->

            <div
                class="flex items-center justify-between
                       px-6 py-5 border-b border-gray-200"
            >

                <div>

                    <h3 class="text-[21px] font-bold text-gray-900">
                        Rejected Users
                    </h3>

                    <p class="text-[13px] text-gray-400 mt-1">
                        Archived rejected registrations
                    </p>

                </div>


                <button
                    type="button"
                    id="close-rejected-users"
                    class="w-9 h-9 flex items-center justify-center
                           rounded-full text-gray-500
                           hover:bg-gray-100 hover:text-gray-800
                           transition"
                >

                    <svg
                        class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 6l12 12M18 6L6 18"
                        />

                    </svg>

                </button>

            </div>


            <!-- Rejected Users Search -->
            <div class="px-6 py-4 border-b border-gray-100 bg-[#FFFBF9]">
                <div class="relative max-w-md">
                    <input
                        type="text"
                        id="rejected-users-search"
                        placeholder="Search rejected users..."
                        class="w-full h-[40px] rounded-lg border border-gray-300 bg-white pl-10 pr-4 text-[13px] text-gray-700 placeholder:text-gray-300 outline-none focus:border-[#7B1B1B] focus:ring-2 focus:ring-[#7B1B1B]/10 transition"
                    >
                    <svg
                        class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z" />
                    </svg>
                </div>
            </div>


            <!-- Rejected Users Table -->

            <div class="overflow-x-auto max-h-none">

                <table class="w-full min-w-[800px]">

                    <thead class="sticky top-0 bg-white">

                        <tr class="border-b border-gray-200">

                            <th class="text-left px-6 py-4 text-[13px] font-medium text-gray-400">
                                Applicant
                            </th>

                            <th class="text-left px-6 py-4 text-[13px] font-medium text-gray-400">
                                User Type
                            </th>

                            <th class="text-left px-6 py-4 text-[13px] font-medium text-gray-400">
                                Email
                            </th>

                            <th class="text-left px-6 py-4 text-[13px] font-medium text-gray-400">
                                Phone
                            </th>

                            <th class="text-left px-6 py-4 text-[13px] font-medium text-gray-400">
                                <button
                                    type="button"
                                    class="archive-date-sort inline-flex items-center gap-1.5 hover:text-[#7B1B1B] transition"
                                    data-target="rejected-users-body"
                                    aria-label="Sort rejected users by date"
                                >
                                    <span>Date Rejected</span>
                                    <span class="archive-sort-arrow inline-flex items-center justify-center w-6 h-6 text-[17px] leading-none font-bold text-[#7B1B1B] rounded-md bg-[#FFF0EC] border border-[#F3C2B5]">↓</span>
                                </button>
                            </th>

                        </tr>

                    </thead>


                    <tbody id="rejected-users-body" class="divide-y divide-gray-200">

                        <!-- REJECTED USER 1 -->
                        <tr class="hover:bg-[#FFF9F7] transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-[#F5D7D7] flex items-center justify-center text-[11px] font-semibold text-[#8B3030] shrink-0">KC</div>
                                    <span class="text-[13px] font-medium text-gray-800">Kevin Castillo</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <img src="{{ asset('icons/admin/dashboard/body/seller.png') }}" class="w-5 h-5 object-contain" alt="Seller">
                                    <span class="text-[13px]">Seller</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-[13px] text-gray-400">kevin.castillo@gmail.com</td>
                            <td class="px-6 py-4 text-[13px]">0917 245 6310</td>
                            <td class="px-6 py-4 text-[13px]">May 31, 2026</td>
                        </tr>

                        <!-- REJECTED USER 2 -->
                        <tr class="hover:bg-[#FFF9F7] transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-[#E8DDF0] flex items-center justify-center text-[11px] font-semibold text-[#69497A] shrink-0">JM</div>
                                    <span class="text-[13px] font-medium text-gray-800">Jessa Martinez</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <img src="{{ asset('icons/admin/dashboard/body/buyer.png') }}" class="w-5 h-5 object-contain" alt="Buyer">
                                    <span class="text-[13px]">Buyer</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-[13px] text-gray-400">jessa.martinez@gmail.com</td>
                            <td class="px-6 py-4 text-[13px]">0921 334 8062</td>
                            <td class="px-6 py-4 text-[13px]">May 29, 2026</td>
                        </tr>

                        <!-- REJECTED USER 3 -->
                        <tr class="hover:bg-[#FFF9F7] transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-[#F0E1CC] flex items-center justify-center text-[11px] font-semibold text-[#7B5D2D] shrink-0">RN</div>
                                    <span class="text-[13px] font-medium text-gray-800">Ramon Navarro</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <img src="{{ asset('icons/admin/dashboard/body/seller.png') }}" class="w-5 h-5 object-contain" alt="Seller">
                                    <span class="text-[13px]">Seller</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-[13px] text-gray-400">ramon.navarro@gmail.com</td>
                            <td class="px-6 py-4 text-[13px]">0908 517 4423</td>
                            <td class="px-6 py-4 text-[13px]">May 26, 2026</td>
                        </tr>

                        <!-- REJECTED USER 4 -->
                        <tr class="hover:bg-[#FFF9F7] transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-[#DDE8F2] flex items-center justify-center text-[11px] font-semibold text-[#45657D] shrink-0">SG</div>
                                    <span class="text-[13px] font-medium text-gray-800">Sofia Garcia</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <img src="{{ asset('icons/admin/dashboard/body/buyer.png') }}" class="w-5 h-5 object-contain" alt="Buyer">
                                    <span class="text-[13px]">Buyer</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-[13px] text-gray-400">sofia.garcia@gmail.com</td>
                            <td class="px-6 py-4 text-[13px]">0916 729 5504</td>
                            <td class="px-6 py-4 text-[13px]">May 24, 2026</td>
                        </tr>

                    </tbody>

                </table>

            </div>


            <!-- Modal Footer -->

            <div
                class="px-6 py-4 border-t border-gray-200
                       flex items-center justify-between"
            >

                <p id="rejected-users-count" class="text-[13px] text-gray-400">
                    4 rejected users
                </p>

                <button
                    type="button"
                    id="close-rejected-users-bottom"
                    class="px-5 py-2 rounded-lg
                           bg-[#7B1B1B] text-white
                           text-[13px] font-medium
                           hover:bg-[#641515]
                           transition"
                >
                    Close
                </button>

            </div>

        </div>

    </div>


    <!-- =========================================================
         SELLER DETAILS MODAL
    ========================================================== -->

    <div
        id="seller-details-modal"
        class="fixed inset-0 z-[120] hidden items-center justify-center
               bg-black/35 backdrop-blur-[2px] px-5 py-6"
        aria-hidden="true"
    >

        <div
            class="relative registration-detail-modal bg-white w-full max-w-3xl max-h-[92vh]
                   overflow-y-auto rounded-[24px] shadow-2xl
                   border border-white"
            role="dialog"
            aria-modal="true"
            aria-labelledby="seller-details-title"
        >

            <div class="px-7 pt-6 pb-3">

                <div class="flex items-center justify-between pb-4 border-b border-gray-200">

                    <h3
                        id="seller-details-title"
                        class="text-[23px] font-semibold text-gray-900"
                    >
                        Seller Details
                    </h3>

                    <button
                        type="button"
                        class="registration-modal-close w-9 h-9 flex items-center justify-center
                               rounded-full text-gray-500 hover:bg-gray-100 hover:text-gray-800 transition"
                        data-modal="seller-details-modal"
                        aria-label="Close seller details"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6L6 18" />
                        </svg>
                    </button>

                </div>

            </div>

            <div class="px-7 pb-5">

                <!-- Personal Information -->
                <section>

                    <div class="flex items-center gap-2 mb-6">
                        <svg class="w-6 h-6 text-[#A52A2A]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M16 11a4 4 0 1 0-3.9-5A4 4 0 0 0 16 11Zm-8 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm8 1c-2.7 0-5 1.35-5 3v1h10v-1c0-1.65-2.3-3-5-3ZM8 14c-2.2 0-4 1.1-4 2.5V18h8v-1.5C12 15.1 10.2 14 8 14Z"/>
                        </svg>
                        <h4 class="text-[20px] font-semibold text-[#A52A2A]">Personal Information</h4>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-[1fr_300px] gap-8">

                        <div class="space-y-5">
                            <div class="grid grid-cols-[155px_1fr] items-center gap-2">
                                <span class="text-[15px] text-gray-400">Last Name</span>
                                <span class="text-[15px] font-medium text-gray-900">Dela Cruz</span>
                            </div>
                            <div class="grid grid-cols-[155px_1fr] items-center gap-2">
                                <span class="text-[15px] text-gray-400">First Name</span>
                                <span class="text-[15px] font-medium text-gray-900">Juan</span>
                            </div>
                            <div class="grid grid-cols-[155px_1fr] items-center gap-2">
                                <span class="text-[15px] text-gray-400">Middle Name</span>
                                <span class="text-[15px] font-medium text-gray-900">Amador</span>
                            </div>
                            <div class="grid grid-cols-[155px_1fr] items-center gap-2">
                                <span class="text-[15px] text-gray-400">Sex</span>
                                <span class="text-[15px] font-medium text-gray-900">Male</span>
                            </div>
                            <div class="grid grid-cols-[155px_1fr] items-center gap-2">
                                <span class="text-[15px] text-gray-400">Birthday</span>
                                <span class="text-[15px] font-medium text-gray-900">November 7, 2006</span>
                            </div>
                            <div class="grid grid-cols-[155px_1fr] items-center gap-2">
                                <span class="text-[15px] text-gray-400">Age</span>
                                <span class="text-[15px] font-medium text-gray-900">19</span>
                            </div>
                            <div class="grid grid-cols-[155px_1fr] items-center gap-2">
                                <span class="text-[15px] text-gray-400">Email</span>
                                <span class="text-[15px] font-medium text-gray-900 break-all">juandelacruz@gmail.com</span>
                            </div>
                            <div class="grid grid-cols-[155px_1fr] items-center gap-2">
                                <span class="text-[15px] text-gray-400">Contact No.</span>
                                <span class="text-[15px] font-medium text-gray-900">0917 123 4567</span>
                            </div>
                        </div>

                        <div>
                            <p class="text-[15px] text-gray-400 mb-2">Valid ID</p>
                            <div class="w-full h-[180px] rounded-lg overflow-hidden border border-gray-200 bg-gradient-to-br from-[#f0e7d2] via-[#efe1c2] to-[#d6c49c] relative shadow-sm">
                                <div class="absolute top-3 left-4 text-[8px] font-semibold text-[#2d3550]">REPUBLIKA NG PILIPINAS</div>
                                <div class="absolute top-6 left-4 text-[7px] text-[#2d3550]">PHILIPPINE IDENTIFICATION CARD</div>
                                <div class="absolute left-4 top-[44px] w-[58px] h-[76px] rounded bg-gray-300 flex items-center justify-center overflow-hidden">
                                    <svg class="w-10 h-10 text-gray-500" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm0 2c-4.42 0-8 2.24-8 5v1h16v-1c0-2.76-3.58-5-8-5Z"/></svg>
                                </div>
                                <div class="absolute left-[86px] top-[47px] text-[8px] text-[#4b5563]">Apelyido / Last Name</div>
                                <div class="absolute left-[86px] top-[60px] text-[12px] font-bold text-[#111827]">DELA CRUZ</div>
                                <div class="absolute left-[86px] top-[78px] text-[8px] text-[#4b5563]">Pangalan / First Name</div>
                                <div class="absolute left-[86px] top-[91px] text-[12px] font-bold text-[#111827]">JUAN</div>
                                <div class="absolute left-[86px] top-[109px] text-[8px] text-[#4b5563]">MIDDLE NAME</div>
                                <div class="absolute left-[86px] top-[122px] text-[10px] font-semibold text-[#111827]">AMADOR</div>
                                <div class="absolute left-[86px] bottom-4 text-[8px] text-[#4b5563]">Date of Birth: 07 NOV 2006</div>
                            </div>
                        </div>

                    </div>

                </section>

                <div class="border-t border-gray-200 my-7"></div>

                <!-- Address -->
                <section>
                    <div class="flex items-center gap-2 mb-6">
                        <svg class="w-6 h-6 text-[#A52A2A]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2a7 7 0 0 0-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 0 0-7-7Zm0 9.5A2.5 2.5 0 1 1 12 6a2.5 2.5 0 0 1 0 5.5Z"/>
                        </svg>
                        <h4 class="text-[20px] font-semibold text-[#A52A2A]">Address</h4>
                    </div>

                    <div class="space-y-5">
                        <div class="grid grid-cols-[155px_1fr] gap-2"><span class="text-[15px] text-gray-400">Province</span><span class="text-[15px] font-medium">Laguna</span></div>
                        <div class="grid grid-cols-[155px_1fr] gap-2"><span class="text-[15px] text-gray-400">Municipality</span><span class="text-[15px] font-medium">Calamba</span></div>
                        <div class="grid grid-cols-[155px_1fr] gap-2"><span class="text-[15px] text-gray-400">Barangay</span><span class="text-[15px] font-medium">Masico</span></div>
                        <div class="grid grid-cols-[155px_1fr] gap-2"><span class="text-[15px] text-gray-400">Street</span><span class="text-[15px] font-medium">Block 2 Lot 2, San Lorenzo St.</span></div>
                        <div class="grid grid-cols-[155px_1fr] gap-2"><span class="text-[15px] text-gray-400">House No.</span><span class="text-[15px] font-medium">587</span></div>
                        <div class="grid grid-cols-[155px_1fr] gap-2"><span class="text-[15px] text-gray-400">Zip Code</span><span class="text-[15px] font-medium">4020</span></div>
                    </div>
                </section>

                <div class="border-t border-gray-200 my-7"></div>

                <!-- Business Information -->
                <section>
                    <div class="flex items-center gap-2 mb-6">
                        <svg class="w-6 h-6 text-[#A52A2A]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M4 7h16v13H4V7Zm3-4h10l2 2H5l2-2Zm-1 8v6h2v-6H6Zm4 0v6h2v-6h-2Zm4 0v6h2v-6h-2Z"/>
                        </svg>
                        <h4 class="text-[20px] font-semibold text-[#A52A2A]">Business Information</h4>
                    </div>

                    <div class="space-y-5">
                        <div class="grid grid-cols-[155px_1fr] gap-2"><span class="text-[15px] text-gray-400">Business Name</span><span class="text-[15px] font-medium">Dela Cruz Online Boutique</span></div>
                        <div class="grid grid-cols-[155px_1fr] gap-2"><span class="text-[15px] text-gray-400">Category</span><span class="text-[15px] font-medium">Fashion &amp; Apparel</span></div>
                        <div class="grid grid-cols-[155px_1fr] items-center gap-2">
                            <span class="text-[15px] text-gray-400">Business Permit</span>
                            <button type="button" class="inline-flex items-center gap-2 w-fit px-3 py-2 rounded-lg border border-gray-200 text-[13px] text-gray-700 hover:bg-[#FFF9F7] transition">
                                <svg class="w-4 h-4 text-[#A52A2A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 2h10v20H7zM9 7h6M9 11h6M9 15h4"/></svg>
                                business_permit.png
                            </button>
                        </div>
                    </div>
                </section>

            </div>

            <div class="px-7 py-5 border-t border-gray-200 flex items-center justify-end gap-4 bg-white">
                <button
                    type="button"
                    class="registration-reject px-9 py-2.5 rounded-lg bg-[#8F211F] text-white text-[14px] font-semibold hover:bg-[#741A18] transition"
                >
                    Reject
                </button>
                <button
                    type="button"
                    class="registration-approve px-9 py-2.5 rounded-lg bg-[#EA7779] text-white text-[14px] font-semibold hover:bg-[#D86567] transition"
                >
                    Approve
                </button>
            </div>

        </div>

    </div>


    <!-- =========================================================
         BUYER DETAILS MODAL
    ========================================================== -->

    <div
        id="buyer-details-modal"
        class="fixed inset-0 z-[120] hidden items-center justify-center
               bg-black/35 backdrop-blur-[2px] px-5 py-6"
        aria-hidden="true"
    >

        <div
            class="relative registration-detail-modal bg-white w-full max-w-3xl max-h-[92vh]
                   overflow-y-auto rounded-[24px] shadow-2xl
                   border border-white"
            role="dialog"
            aria-modal="true"
            aria-labelledby="buyer-details-title"
        >

            <div class="px-7 pt-6 pb-3">
                <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                    <h3 id="buyer-details-title" class="text-[23px] font-semibold text-gray-900">Buyer Details</h3>
                    <button
                        type="button"
                        class="registration-modal-close w-9 h-9 flex items-center justify-center rounded-full text-gray-500 hover:bg-gray-100 hover:text-gray-800 transition"
                        data-modal="buyer-details-modal"
                        aria-label="Close buyer details"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6L6 18" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="px-7 pb-5">

                <!-- Personal Information -->
                <section>
                    <div class="flex items-center gap-2 mb-6">
                        <svg class="w-6 h-6 text-[#A52A2A]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M16 11a4 4 0 1 0-3.9-5A4 4 0 0 0 16 11Zm-8 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm8 1c-2.7 0-5 1.35-5 3v1h10v-1c0-1.65-2.3-3-5-3ZM8 14c-2.2 0-4 1.1-4 2.5V18h8v-1.5C12 15.1 10.2 14 8 14Z"/>
                        </svg>
                        <h4 class="text-[20px] font-semibold text-[#A52A2A]">Personal Information</h4>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-[1fr_300px] gap-8">
                        <div class="space-y-5">
                            <div class="grid grid-cols-[155px_1fr] items-center gap-2"><span class="text-[15px] text-gray-400">Last Name</span><span class="text-[15px] font-medium">Dela Cruz</span></div>
                            <div class="grid grid-cols-[155px_1fr] items-center gap-2"><span class="text-[15px] text-gray-400">First Name</span><span class="text-[15px] font-medium">Juan</span></div>
                            <div class="grid grid-cols-[155px_1fr] items-center gap-2"><span class="text-[15px] text-gray-400">Middle Name</span><span class="text-[15px] font-medium">Amador</span></div>
                            <div class="grid grid-cols-[155px_1fr] items-center gap-2"><span class="text-[15px] text-gray-400">Sex</span><span class="text-[15px] font-medium">Male</span></div>
                            <div class="grid grid-cols-[155px_1fr] items-center gap-2"><span class="text-[15px] text-gray-400">Birthday</span><span class="text-[15px] font-medium">November 7, 2006</span></div>
                            <div class="grid grid-cols-[155px_1fr] items-center gap-2"><span class="text-[15px] text-gray-400">Age</span><span class="text-[15px] font-medium">19</span></div>
                            <div class="grid grid-cols-[155px_1fr] items-center gap-2"><span class="text-[15px] text-gray-400">Email</span><span class="text-[15px] font-medium break-all">juandelacruz@gmail.com</span></div>
                            <div class="grid grid-cols-[155px_1fr] items-center gap-2"><span class="text-[15px] text-gray-400">Contact No.</span><span class="text-[15px] font-medium">0917 123 4567</span></div>
                        </div>

                        <div>
                            <p class="text-[15px] text-gray-400 mb-2">Valid ID</p>
                            <div class="w-full h-[180px] rounded-lg overflow-hidden border border-gray-200 bg-gradient-to-br from-[#f0e7d2] via-[#efe1c2] to-[#d6c49c] relative shadow-sm">
                                <div class="absolute top-3 left-4 text-[8px] font-semibold text-[#2d3550]">REPUBLIKA NG PILIPINAS</div>
                                <div class="absolute top-6 left-4 text-[7px] text-[#2d3550]">PHILIPPINE IDENTIFICATION CARD</div>
                                <div class="absolute left-4 top-[44px] w-[58px] h-[76px] rounded bg-gray-300 flex items-center justify-center overflow-hidden">
                                    <svg class="w-10 h-10 text-gray-500" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm0 2c-4.42 0-8 2.24-8 5v1h16v-1c0-2.76-3.58-5-8-5Z"/></svg>
                                </div>
                                <div class="absolute left-[86px] top-[47px] text-[8px] text-[#4b5563]">Apelyido / Last Name</div>
                                <div class="absolute left-[86px] top-[60px] text-[12px] font-bold text-[#111827]">DELA CRUZ</div>
                                <div class="absolute left-[86px] top-[78px] text-[8px] text-[#4b5563]">Pangalan / First Name</div>
                                <div class="absolute left-[86px] top-[91px] text-[12px] font-bold text-[#111827]">JUAN</div>
                                <div class="absolute left-[86px] top-[109px] text-[8px] text-[#4b5563]">MIDDLE NAME</div>
                                <div class="absolute left-[86px] top-[122px] text-[10px] font-semibold text-[#111827]">AMADOR</div>
                                <div class="absolute left-[86px] bottom-4 text-[8px] text-[#4b5563]">Date of Birth: 07 NOV 2006</div>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="border-t border-gray-200 my-7"></div>

                <!-- Address -->
                <section>
                    <div class="flex items-center gap-2 mb-6">
                        <svg class="w-6 h-6 text-[#A52A2A]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2a7 7 0 0 0-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 0 0-7-7Zm0 9.5A2.5 2.5 0 1 1 12 6a2.5 2.5 0 0 1 0 5.5Z"/></svg>
                        <h4 class="text-[20px] font-semibold text-[#A52A2A]">Address</h4>
                    </div>
                    <div class="space-y-5">
                        <div class="grid grid-cols-[155px_1fr] gap-2"><span class="text-[15px] text-gray-400">Province</span><span class="text-[15px] font-medium">Laguna</span></div>
                        <div class="grid grid-cols-[155px_1fr] gap-2"><span class="text-[15px] text-gray-400">Municipality</span><span class="text-[15px] font-medium">Calamba</span></div>
                        <div class="grid grid-cols-[155px_1fr] gap-2"><span class="text-[15px] text-gray-400">Barangay</span><span class="text-[15px] font-medium">Masico</span></div>
                        <div class="grid grid-cols-[155px_1fr] gap-2"><span class="text-[15px] text-gray-400">Street</span><span class="text-[15px] font-medium">Block 2 Lot 2, San Lorenzo St.</span></div>
                        <div class="grid grid-cols-[155px_1fr] gap-2"><span class="text-[15px] text-gray-400">House No.</span><span class="text-[15px] font-medium">587</span></div>
                        <div class="grid grid-cols-[155px_1fr] gap-2"><span class="text-[15px] text-gray-400">Zip Code</span><span class="text-[15px] font-medium">4020</span></div>
                    </div>
                </section>

            </div>

            <div class="px-7 py-5 border-t border-gray-200 flex items-center justify-end gap-4 bg-white">
                <button type="button" class="registration-reject px-9 py-2.5 rounded-lg bg-[#8F211F] text-white text-[14px] font-semibold hover:bg-[#741A18] transition">Reject</button>
                <button type="button" class="registration-approve px-9 py-2.5 rounded-lg bg-[#EA7779] text-white text-[14px] font-semibold hover:bg-[#D86567] transition">Approve</button>
            </div>

        </div>

    </div>



    <!-- =========================================================
         REJECT REGISTRATION MODAL
    ========================================================== -->
    <div
        id="reject-registration-modal"
        class="fixed inset-0 z-[140] hidden items-center justify-center
               bg-black/35 backdrop-blur-[2px] px-5 py-6"
        aria-hidden="true"
    >
        <div
            class="relative bg-white w-full max-w-[610px] rounded-[28px] shadow-2xl
                   border border-white"
            role="dialog"
            aria-modal="true"
            aria-labelledby="reject-registration-title"
        >
            <div class="px-8 pt-7 pb-7">

                <h3
                    id="reject-registration-title"
                    class="text-[23px] font-medium text-gray-900"
                >
                    Reject Registration
                </h3>

                <p class="mt-2 text-[17px] leading-6 text-gray-400">
                    Please provide a reason for rejecting the registration.
                </p>

                <div class="mt-10">
                    <label class="block text-[17px] font-medium text-gray-900 mb-2">
                        Reason<span class="text-[#D62F2F]">*</span>
                    </label>

                    <div id="reject-reason-options" class="space-y-2">
                        <label class="flex items-center gap-3 min-h-[40px] px-3 rounded-lg border border-gray-200 bg-[#FCFCFC] cursor-pointer hover:bg-[#FFF9F7] transition">
                            <input type="radio" name="reject_reason" value="Incomplete or missing required documents." class="w-[18px] h-[18px] accent-[#8F211F]">
                            <span class="text-[15px] text-gray-900">Incomplete or missing required documents.</span>
                        </label>

                        <label class="flex items-center gap-3 min-h-[40px] px-3 rounded-lg border border-gray-200 bg-[#FCFCFC] cursor-pointer hover:bg-[#FFF9F7] transition">
                            <input type="radio" name="reject_reason" value="Invalid or unverifiable information." class="w-[18px] h-[18px] accent-[#8F211F]">
                            <span class="text-[15px] text-gray-900">Invalid or unverifiable information.</span>
                        </label>

                        <label class="flex items-center gap-3 min-h-[40px] px-3 rounded-lg border border-gray-200 bg-[#FCFCFC] cursor-pointer hover:bg-[#FFF9F7] transition">
                            <input type="radio" name="reject_reason" value="Does not meet platform requirements." class="w-[18px] h-[18px] accent-[#8F211F]">
                            <span class="text-[15px] text-gray-900">Does not meet platform requirements.</span>
                        </label>

                        <label class="flex items-center gap-3 min-h-[40px] px-3 rounded-lg border border-gray-200 bg-[#FCFCFC] cursor-pointer hover:bg-[#FFF9F7] transition">
                            <input type="radio" name="reject_reason" value="Prohibited or restricted business/category." class="w-[18px] h-[18px] accent-[#8F211F]">
                            <span class="text-[15px] text-gray-900">Prohibited or restricted business/category.</span>
                        </label>

                        <label class="flex items-center gap-3 min-h-[40px] px-3 rounded-lg border border-gray-200 bg-[#FCFCFC] cursor-pointer hover:bg-[#FFF9F7] transition">
                            <input type="radio" name="reject_reason" value="Duplicate account." class="w-[18px] h-[18px] accent-[#8F211F]">
                            <span class="text-[15px] text-gray-900">Duplicate account.</span>
                        </label>

                        <label class="flex items-center gap-3 min-h-[40px] px-3 rounded-lg cursor-pointer hover:bg-[#FFF9F7] transition">
                            <input type="radio" name="reject_reason" value="Other (please specify)" class="w-[18px] h-[18px] accent-[#8F211F]">
                            <span class="text-[15px] text-gray-900">Other (please specify)</span>
                        </label>
                    </div>

                    <p
                        id="reject-reason-error"
                        class="hidden mt-2 text-[12px] text-[#B3262E]"
                    >
                        Please select a reason before rejecting the registration.
                    </p>
                </div>

                <div class="mt-5">
                    <label class="block text-[17px] font-medium text-gray-900 mb-2">
                        Additional Details <span class="font-normal">(Optional)</span>
                    </label>

                    <div class="relative">
                        <textarea
                            id="reject-additional-details"
                            maxlength="300"
                            rows="3"
                            placeholder="Write additional details here..."
                            class="w-full h-[59px] resize-none rounded-lg border border-gray-200 bg-white
                                   px-3 py-2.5 pr-12 text-[15px] text-gray-700
                                   placeholder:text-gray-400 outline-none
                                   focus:border-[#8F211F] focus:ring-2 focus:ring-[#8F211F]/10 transition"
                        ></textarea>

                        <span
                            id="reject-details-count"
                            class="absolute right-3 bottom-2 text-[11px] text-gray-500"
                        >
                            0/300
                        </span>
                    </div>
                </div>
            </div>

            <div class="px-8 pb-7 flex items-center justify-end gap-3">
                <button
                    type="button"
                    id="cancel-reject-registration"
                    class="px-6 py-2.5 rounded-lg border border-gray-300 bg-white
                           text-[#8F211F] text-[14px] font-semibold
                           hover:bg-gray-50 transition"
                >
                    Cancel
                </button>

                <button
                    type="button"
                    id="confirm-reject-registration"
                    class="px-7 py-2.5 rounded-lg bg-[#8F211F] text-white
                           text-[14px] font-semibold hover:bg-[#741A18] transition"
                >
                    Reject
                </button>
            </div>
        </div>
    </div>

    <!-- =========================================================
         REGISTRATION FLASH MESSAGE
    ========================================================== -->
    <div
        id="registration-flash"
        class="fixed bottom-6 right-6 z-[180] hidden w-[370px] rounded-xl border
               bg-white shadow-xl px-4 py-4"
        role="status"
        aria-live="polite"
    >
        <div class="flex items-start gap-3">
            <div
                id="registration-flash-icon"
                class="w-9 h-9 rounded-full flex items-center justify-center shrink-0"
            ></div>

            <div class="min-w-0">
                <p id="registration-flash-title" class="text-[14px] font-semibold text-gray-900"></p>
                <p id="registration-flash-message" class="mt-1 text-[12px] leading-5 text-gray-500"></p>
            </div>

            <button
                type="button"
                id="registration-flash-close"
                class="ml-auto w-7 h-7 rounded-full flex items-center justify-center
                       text-gray-400 hover:bg-gray-100 transition shrink-0"
                aria-label="Close notification"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6 6 18"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- =========================================================
         FILTER BAR
    ========================================================== -->

    <div
        class="bg-white rounded-xl shadow-sm border border-gray-100
               p-4 mb-5"
    >

        <div class="flex flex-col xl:flex-row items-stretch xl:items-center gap-3">

            <!-- Search -->

            <div class="relative flex-1 min-w-0">

                <input
                    id="registration-search"
                    type="text"
                    placeholder="Search name, email, and phone"
                    class="w-full h-[38px] rounded-lg border border-gray-300
                           bg-white pl-4 pr-11 text-[13px] text-gray-700
                           placeholder:text-gray-300
                           outline-none
                           focus:border-maroon-700
                           focus:ring-2 focus:ring-maroon-700/10
                           transition"
                >

                <svg
                    class="absolute right-3 top-1/2 -translate-y-1/2
                           w-5 h-5 text-gray-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"
                    />

                </svg>

            </div>


            <!-- User Type -->

            <div class="relative">

                <select
                    id="user-type-filter"
                    class="appearance-none w-full xl:w-[138px]
                           h-[38px] rounded-lg border border-gray-300
                           bg-white px-3 pr-9 text-[13px] text-gray-700
                           outline-none cursor-pointer
                           focus:border-maroon-700
                           focus:ring-2 focus:ring-maroon-700/10"
                >

                    <option value="all">
                        All User Types
                    </option>

                    <option value="seller">
                        Seller
                    </option>

                    <option value="buyer">
                        Buyer
                    </option>

                </select>

                <svg
                    class="pointer-events-none absolute right-3 top-1/2
                           -translate-y-1/2 w-4 h-4 text-gray-600"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="m6 9 6 6 6-6"
                    />

                </svg>

            </div>


            <!-- Date -->

            <div class="relative">

                <input
                    id="date-filter"
                    type="date"
                    class="w-full xl:w-[205px]
                           h-[38px] rounded-lg border border-gray-300
                           bg-white px-3 pr-10 text-[13px] text-gray-700
                           outline-none
                           focus:border-maroon-700
                           focus:ring-2 focus:ring-maroon-700/10
                           [&::-webkit-calendar-picker-indicator]:opacity-0
                           [&::-webkit-calendar-picker-indicator]:absolute
                           [&::-webkit-calendar-picker-indicator]:right-0
                           [&::-webkit-calendar-picker-indicator]:w-10
                           [&::-webkit-calendar-picker-indicator]:h-full
                           [&::-webkit-calendar-picker-indicator]:cursor-pointer"
                >

                <button
                    type="button"
                    id="date-calendar-button"
                    class="absolute right-0 top-0
                           w-10 h-[38px]
                           flex items-center justify-center
                           text-gray-700
                           hover:text-[#7B1B1B]
                           cursor-pointer"
                    aria-label="Open calendar"
                >

                    <svg
                        class="w-4 h-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M8 2v4m8-4v4M3 10h18M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"
                        />

                    </svg>

                </button>

            </div>


            <!-- Reload -->

            <button
                id="registration-reload"
                type="button"
                class="w-[40px] h-[38px] shrink-0
                       flex items-center justify-center
                       rounded-lg text-gray-900
                       hover:bg-[#FFF0EC]
                       active:scale-95
                       transition-all duration-200"
                title="Reload"
            >

                <svg
                    id="registration-reload-icon"
                    class="w-6 h-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M20 11a8.1 8.1 0 0 0-15.5-2M4 5v4h4M4 13a8.1 8.1 0 0 0 15.5 2M20 19v-4h-4"
                    />

                </svg>

            </button>

        </div>

    </div>


    <!-- =========================================================
         REGISTRATIONS TABLE
    ========================================================== -->

    <div
        class="bg-white rounded-xl shadow-sm border border-gray-100
               overflow-hidden"
    >

        <div class="overflow-x-auto">

            <table
                id="registrations-table"
                class="w-full min-w-[780px]"
            >

                <thead>

                    <tr class="border-b border-gray-200">

                        <th class="text-left px-5 py-4 text-[13px] font-medium text-gray-400">
                            Applicant
                        </th>

                        <th class="text-left px-5 py-4 text-[13px] font-medium text-gray-400">
                            User Type
                        </th>

                        <th class="text-left px-5 py-4 text-[13px] font-medium text-gray-400">
                            Email
                        </th>

                        <th class="text-left px-5 py-4 text-[13px] font-medium text-gray-400">
                            Phone
                        </th>

                        <th class="text-left px-5 py-4 text-[13px] font-medium text-gray-400">
                            Date Registered
                        </th>

                        <th class="text-left px-5 py-4 text-[13px] font-medium text-gray-400">
                            Status
                        </th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-gray-200">

                    <tr
                        class="registration-row cursor-pointer hover:bg-[#FFF9F7] transition"
                        data-name="Juan Dela Cruz"
                        data-email="juandelacruz@gmail.com"
                        data-phone="0917 123 4567"
                        data-type="seller"
                        data-status="pending"
                        data-date="2026-05-31"
                        role="button"
                        tabindex="0"
                    >
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-200 shrink-0"></div>
                                <span class="text-[13px] font-medium text-gray-800">Juan Dela Cruz</span>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('icons/admin/dashboard/body/seller.png') }}" class="w-5 h-5 object-contain" alt="Seller">
                                <span class="text-[13px] text-gray-800">Seller</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-[13px] text-gray-400">juandelacruz@gmail.com</td>
                        <td class="px-5 py-3 text-[13px] text-gray-800">0917 123 4567</td>
                        <td class="px-5 py-3">
                            <div class="text-[13px] text-gray-800">May 31, 2026</div>
                            <div class="text-[12px] text-gray-800">10:30 AM</div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-[#FFE5D0] text-[#E87D22] text-[11px] font-medium border border-[#FFD1B8]">
                                Pending
                            </span>
                        </td>
                    </tr>
                    <tr
                        class="registration-row cursor-pointer hover:bg-[#FFF9F7] transition"
                        data-name="Maria Santos"
                        data-email="maria.santos@gmail.com"
                        data-phone="0928 765 4321"
                        data-type="buyer"
                        data-status="pending"
                        data-date="2026-05-31"
                        role="button"
                        tabindex="0"
                    >
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-200 shrink-0"></div>
                                <span class="text-[13px] font-medium text-gray-800">Maria Santos</span>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('icons/admin/dashboard/body/buyer.png') }}" class="w-5 h-5 object-contain" alt="Buyer">
                                <span class="text-[13px] text-gray-800">Buyer</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-[13px] text-gray-400">maria.santos@gmail.com</td>
                        <td class="px-5 py-3 text-[13px] text-gray-800">0928 765 4321</td>
                        <td class="px-5 py-3">
                            <div class="text-[13px] text-gray-800">May 31, 2026</div>
                            <div class="text-[12px] text-gray-800">09:15 AM</div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-[#FFE5D0] text-[#E87D22] text-[11px] font-medium border border-[#FFD1B8]">
                                Pending
                            </span>
                        </td>
                    </tr>
                    <tr
                        class="registration-row cursor-pointer hover:bg-[#FFF9F7] transition"
                        data-name="Jose Ramirez"
                        data-email="jose.ramirez@gmail.com"
                        data-phone="0906 555 7890"
                        data-type="seller"
                        data-status="pending"
                        data-date="2026-05-30"
                        role="button"
                        tabindex="0"
                    >
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-200 shrink-0"></div>
                                <span class="text-[13px] font-medium text-gray-800">Jose Ramirez</span>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('icons/admin/dashboard/body/seller.png') }}" class="w-5 h-5 object-contain" alt="Seller">
                                <span class="text-[13px] text-gray-800">Seller</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-[13px] text-gray-400">jose.ramirez@gmail.com</td>
                        <td class="px-5 py-3 text-[13px] text-gray-800">0906 555 7890</td>
                        <td class="px-5 py-3">
                            <div class="text-[13px] text-gray-800">May 30, 2026</div>
                            <div class="text-[12px] text-gray-800">08:45 PM</div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-[#FFE5D0] text-[#E87D22] text-[11px] font-medium border border-[#FFD1B8]">
                                Pending
                            </span>
                        </td>
                    </tr>
                    <tr
                        class="registration-row cursor-pointer hover:bg-[#FFF9F7] transition"
                        data-name="Anna Reyes"
                        data-email="anna.reyes@gmail.com"
                        data-phone="0915 888 1122"
                        data-type="buyer"
                        data-status="pending"
                        data-date="2026-05-30"
                        role="button"
                        tabindex="0"
                    >
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-200 shrink-0"></div>
                                <span class="text-[13px] font-medium text-gray-800">Anna Reyes</span>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('icons/admin/dashboard/body/buyer.png') }}" class="w-5 h-5 object-contain" alt="Buyer">
                                <span class="text-[13px] text-gray-800">Buyer</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-[13px] text-gray-400">anna.reyes@gmail.com</td>
                        <td class="px-5 py-3 text-[13px] text-gray-800">0915 888 1122</td>
                        <td class="px-5 py-3">
                            <div class="text-[13px] text-gray-800">May 30, 2026</div>
                            <div class="text-[12px] text-gray-800">06:20 PM</div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-[#FFE5D0] text-[#E87D22] text-[11px] font-medium border border-[#FFD1B8]">
                                Pending
                            </span>
                        </td>
                    </tr>
                    <tr
                        class="registration-row cursor-pointer hover:bg-[#FFF9F7] transition"
                        data-name="Liza Gomez"
                        data-email="liza.gomez@gmail.com"
                        data-phone="0932 444 6677"
                        data-type="seller"
                        data-status="pending"
                        data-date="2026-05-30"
                        role="button"
                        tabindex="0"
                    >
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-200 shrink-0"></div>
                                <span class="text-[13px] font-medium text-gray-800">Liza Gomez</span>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('icons/admin/dashboard/body/seller.png') }}" class="w-5 h-5 object-contain" alt="Seller">
                                <span class="text-[13px] text-gray-800">Seller</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-[13px] text-gray-400">liza.gomez@gmail.com</td>
                        <td class="px-5 py-3 text-[13px] text-gray-800">0932 444 6677</td>
                        <td class="px-5 py-3">
                            <div class="text-[13px] text-gray-800">May 30, 2026</div>
                            <div class="text-[12px] text-gray-800">04:10 PM</div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-[#FFE5D0] text-[#E87D22] text-[11px] font-medium border border-[#FFD1B8]">
                                Pending
                            </span>
                        </td>
                    </tr>
                    <tr
                        class="registration-row cursor-pointer hover:bg-[#FFF9F7] transition"
                        data-name="Mark Dela Vega"
                        data-email="mark.delavega@gmail.com"
                        data-phone="0910 333 2211"
                        data-type="buyer"
                        data-status="pending"
                        data-date="2026-05-29"
                        role="button"
                        tabindex="0"
                    >
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-200 shrink-0"></div>
                                <span class="text-[13px] font-medium text-gray-800">Mark Dela Vega</span>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('icons/admin/dashboard/body/buyer.png') }}" class="w-5 h-5 object-contain" alt="Buyer">
                                <span class="text-[13px] text-gray-800">Buyer</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-[13px] text-gray-400">mark.delavega@gmail.com</td>
                        <td class="px-5 py-3 text-[13px] text-gray-800">0910 333 2211</td>
                        <td class="px-5 py-3">
                            <div class="text-[13px] text-gray-800">May 29, 2026</div>
                            <div class="text-[12px] text-gray-800">03:35 PM</div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-[#FFE5D0] text-[#E87D22] text-[11px] font-medium border border-[#FFD1B8]">
                                Pending
                            </span>
                        </td>
                    </tr>
                    <tr
                        class="registration-row cursor-pointer hover:bg-[#FFF9F7] transition"
                        data-name="Patricia Lim"
                        data-email="patricia.lim@gmail.com"
                        data-phone="0918 777 8899"
                        data-type="seller"
                        data-status="pending"
                        data-date="2026-05-29"
                        role="button"
                        tabindex="0"
                    >
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-200 shrink-0"></div>
                                <span class="text-[13px] font-medium text-gray-800">Patricia Lim</span>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('icons/admin/dashboard/body/seller.png') }}" class="w-5 h-5 object-contain" alt="Seller">
                                <span class="text-[13px] text-gray-800">Seller</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-[13px] text-gray-400">patricia.lim@gmail.com</td>
                        <td class="px-5 py-3 text-[13px] text-gray-800">0918 777 8899</td>
                        <td class="px-5 py-3">
                            <div class="text-[13px] text-gray-800">May 29, 2026</div>
                            <div class="text-[12px] text-gray-800">11:05 AM</div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-[#FFE5D0] text-[#E87D22] text-[11px] font-medium border border-[#FFD1B8]">
                                Pending
                            </span>
                        </td>
                    </tr>
                    <tr
                        class="registration-row cursor-pointer hover:bg-[#FFF9F7] transition"
                        data-name="Carlo Mendoza"
                        data-email="carlo.mendoza@gmail.com"
                        data-phone="0921 555 1199"
                        data-type="buyer"
                        data-status="pending"
                        data-date="2026-05-28"
                        role="button"
                        tabindex="0"
                    >
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-200 shrink-0"></div>
                                <span class="text-[13px] font-medium text-gray-800">Carlo Mendoza</span>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('icons/admin/dashboard/body/buyer.png') }}" class="w-5 h-5 object-contain" alt="Buyer">
                                <span class="text-[13px] text-gray-800">Buyer</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-[13px] text-gray-400">carlo.mendoza@gmail.com</td>
                        <td class="px-5 py-3 text-[13px] text-gray-800">0921 555 1199</td>
                        <td class="px-5 py-3">
                            <div class="text-[13px] text-gray-800">May 28, 2026</div>
                            <div class="text-[12px] text-gray-800">04:25 PM</div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-[#FFE5D0] text-[#E87D22] text-[11px] font-medium border border-[#FFD1B8]">
                                Pending
                            </span>
                        </td>
                    </tr>
                    <tr
                        class="registration-row cursor-pointer hover:bg-[#FFF9F7] transition"
                        data-name="Sofia Navarro"
                        data-email="sofia.navarro@gmail.com"
                        data-phone="0916 222 3344"
                        data-type="seller"
                        data-status="pending"
                        data-date="2026-05-28"
                        role="button"
                        tabindex="0"
                    >
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-200 shrink-0"></div>
                                <span class="text-[13px] font-medium text-gray-800">Sofia Navarro</span>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('icons/admin/dashboard/body/seller.png') }}" class="w-5 h-5 object-contain" alt="Seller">
                                <span class="text-[13px] text-gray-800">Seller</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-[13px] text-gray-400">sofia.navarro@gmail.com</td>
                        <td class="px-5 py-3 text-[13px] text-gray-800">0916 222 3344</td>
                        <td class="px-5 py-3">
                            <div class="text-[13px] text-gray-800">May 28, 2026</div>
                            <div class="text-[12px] text-gray-800">01:40 PM</div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-[#FFE5D0] text-[#E87D22] text-[11px] font-medium border border-[#FFD1B8]">
                                Pending
                            </span>
                        </td>
                    </tr>
                    <tr
                        class="registration-row cursor-pointer hover:bg-[#FFF9F7] transition"
                        data-name="Daniel Cruz"
                        data-email="daniel.cruz@gmail.com"
                        data-phone="0927 888 7766"
                        data-type="buyer"
                        data-status="pending"
                        data-date="2026-05-27"
                        role="button"
                        tabindex="0"
                    >
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-200 shrink-0"></div>
                                <span class="text-[13px] font-medium text-gray-800">Daniel Cruz</span>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('icons/admin/dashboard/body/buyer.png') }}" class="w-5 h-5 object-contain" alt="Buyer">
                                <span class="text-[13px] text-gray-800">Buyer</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-[13px] text-gray-400">daniel.cruz@gmail.com</td>
                        <td class="px-5 py-3 text-[13px] text-gray-800">0927 888 7766</td>
                        <td class="px-5 py-3">
                            <div class="text-[13px] text-gray-800">May 27, 2026</div>
                            <div class="text-[12px] text-gray-800">10:12 AM</div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-[#FFE5D0] text-[#E87D22] text-[11px] font-medium border border-[#FFD1B8]">
                                Pending
                            </span>
                        </td>
                    </tr>
                    <tr
                        class="registration-row cursor-pointer hover:bg-[#FFF9F7] transition"
                        data-name="Nicole Bautista"
                        data-email="nicole.bautista@gmail.com"
                        data-phone="0919 404 1515"
                        data-type="seller"
                        data-status="pending"
                        data-date="2026-05-27"
                        role="button"
                        tabindex="0"
                    >
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-200 shrink-0"></div>
                                <span class="text-[13px] font-medium text-gray-800">Nicole Bautista</span>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('icons/admin/dashboard/body/seller.png') }}" class="w-5 h-5 object-contain" alt="Seller">
                                <span class="text-[13px] text-gray-800">Seller</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-[13px] text-gray-400">nicole.bautista@gmail.com</td>
                        <td class="px-5 py-3 text-[13px] text-gray-800">0919 404 1515</td>
                        <td class="px-5 py-3">
                            <div class="text-[13px] text-gray-800">May 27, 2026</div>
                            <div class="text-[12px] text-gray-800">09:05 AM</div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-[#FFE5D0] text-[#E87D22] text-[11px] font-medium border border-[#FFD1B8]">
                                Pending
                            </span>
                        </td>
                    </tr>
                    <tr
                        class="registration-row cursor-pointer hover:bg-[#FFF9F7] transition"
                        data-name="Miguel Torres"
                        data-email="miguel.torres@gmail.com"
                        data-phone="0920 123 9090"
                        data-type="buyer"
                        data-status="pending"
                        data-date="2026-05-26"
                        role="button"
                        tabindex="0"
                    >
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-200 shrink-0"></div>
                                <span class="text-[13px] font-medium text-gray-800">Miguel Torres</span>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('icons/admin/dashboard/body/buyer.png') }}" class="w-5 h-5 object-contain" alt="Buyer">
                                <span class="text-[13px] text-gray-800">Buyer</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-[13px] text-gray-400">miguel.torres@gmail.com</td>
                        <td class="px-5 py-3 text-[13px] text-gray-800">0920 123 9090</td>
                        <td class="px-5 py-3">
                            <div class="text-[13px] text-gray-800">May 26, 2026</div>
                            <div class="text-[12px] text-gray-800">05:20 PM</div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-[#FFE5D0] text-[#E87D22] text-[11px] font-medium border border-[#FFD1B8]">
                                Pending
                            </span>
                        </td>
                    </tr>
                    <tr
                        class="registration-row cursor-pointer hover:bg-[#FFF9F7] transition"
                        data-name="Rachel Aquino"
                        data-email="rachel.aquino@gmail.com"
                        data-phone="0917 678 2345"
                        data-type="seller"
                        data-status="pending"
                        data-date="2026-05-26"
                        role="button"
                        tabindex="0"
                    >
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-200 shrink-0"></div>
                                <span class="text-[13px] font-medium text-gray-800">Rachel Aquino</span>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('icons/admin/dashboard/body/seller.png') }}" class="w-5 h-5 object-contain" alt="Seller">
                                <span class="text-[13px] text-gray-800">Seller</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-[13px] text-gray-400">rachel.aquino@gmail.com</td>
                        <td class="px-5 py-3 text-[13px] text-gray-800">0917 678 2345</td>
                        <td class="px-5 py-3">
                            <div class="text-[13px] text-gray-800">May 26, 2026</div>
                            <div class="text-[12px] text-gray-800">02:50 PM</div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-[#FFE5D0] text-[#E87D22] text-[11px] font-medium border border-[#FFD1B8]">
                                Pending
                            </span>
                        </td>
                    </tr>
                    <tr
                        class="registration-row cursor-pointer hover:bg-[#FFF9F7] transition"
                        data-name="Kevin Villanueva"
                        data-email="kevin.villanueva@gmail.com"
                        data-phone="0908 444 1122"
                        data-type="buyer"
                        data-status="pending"
                        data-date="2026-05-25"
                        role="button"
                        tabindex="0"
                    >
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-200 shrink-0"></div>
                                <span class="text-[13px] font-medium text-gray-800">Kevin Villanueva</span>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('icons/admin/dashboard/body/buyer.png') }}" class="w-5 h-5 object-contain" alt="Buyer">
                                <span class="text-[13px] text-gray-800">Buyer</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-[13px] text-gray-400">kevin.villanueva@gmail.com</td>
                        <td class="px-5 py-3 text-[13px] text-gray-800">0908 444 1122</td>
                        <td class="px-5 py-3">
                            <div class="text-[13px] text-gray-800">May 25, 2026</div>
                            <div class="text-[12px] text-gray-800">11:30 AM</div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-[#FFE5D0] text-[#E87D22] text-[11px] font-medium border border-[#FFD1B8]">
                                Pending
                            </span>
                        </td>
                    </tr>
                    <tr
                        class="registration-row cursor-pointer hover:bg-[#FFF9F7] transition"
                        data-name="Angela Flores"
                        data-email="angela.flores@gmail.com"
                        data-phone="0916 987 6543"
                        data-type="seller"
                        data-status="pending"
                        data-date="2026-05-25"
                        role="button"
                        tabindex="0"
                    >
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-200 shrink-0"></div>
                                <span class="text-[13px] font-medium text-gray-800">Angela Flores</span>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('icons/admin/dashboard/body/seller.png') }}" class="w-5 h-5 object-contain" alt="Seller">
                                <span class="text-[13px] text-gray-800">Seller</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-[13px] text-gray-400">angela.flores@gmail.com</td>
                        <td class="px-5 py-3 text-[13px] text-gray-800">0916 987 6543</td>
                        <td class="px-5 py-3">
                            <div class="text-[13px] text-gray-800">May 25, 2026</div>
                            <div class="text-[12px] text-gray-800">10:05 AM</div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-[#FFE5D0] text-[#E87D22] text-[11px] font-medium border border-[#FFD1B8]">
                                Pending
                            </span>
                        </td>
                    </tr>
                    <tr
                        class="registration-row cursor-pointer hover:bg-[#FFF9F7] transition"
                        data-name="Patrick Tan"
                        data-email="patrick.tan@gmail.com"
                        data-phone="0922 111 2233"
                        data-type="buyer"
                        data-status="pending"
                        data-date="2026-05-24"
                        role="button"
                        tabindex="0"
                    >
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-200 shrink-0"></div>
                                <span class="text-[13px] font-medium text-gray-800">Patrick Tan</span>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('icons/admin/dashboard/body/buyer.png') }}" class="w-5 h-5 object-contain" alt="Buyer">
                                <span class="text-[13px] text-gray-800">Buyer</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-[13px] text-gray-400">patrick.tan@gmail.com</td>
                        <td class="px-5 py-3 text-[13px] text-gray-800">0922 111 2233</td>
                        <td class="px-5 py-3">
                            <div class="text-[13px] text-gray-800">May 24, 2026</div>
                            <div class="text-[12px] text-gray-800">03:18 PM</div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-[#FFE5D0] text-[#E87D22] text-[11px] font-medium border border-[#FFD1B8]">
                                Pending
                            </span>
                        </td>
                    </tr>
                    <tr
                        class="registration-row cursor-pointer hover:bg-[#FFF9F7] transition"
                        data-name="Bea Garcia"
                        data-email="bea.garcia@gmail.com"
                        data-phone="0918 222 4455"
                        data-type="seller"
                        data-status="pending"
                        data-date="2026-05-24"
                        role="button"
                        tabindex="0"
                    >
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-200 shrink-0"></div>
                                <span class="text-[13px] font-medium text-gray-800">Bea Garcia</span>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('icons/admin/dashboard/body/seller.png') }}" class="w-5 h-5 object-contain" alt="Seller">
                                <span class="text-[13px] text-gray-800">Seller</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-[13px] text-gray-400">bea.garcia@gmail.com</td>
                        <td class="px-5 py-3 text-[13px] text-gray-800">0918 222 4455</td>
                        <td class="px-5 py-3">
                            <div class="text-[13px] text-gray-800">May 24, 2026</div>
                            <div class="text-[12px] text-gray-800">01:25 PM</div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-[#FFE5D0] text-[#E87D22] text-[11px] font-medium border border-[#FFD1B8]">
                                Pending
                            </span>
                        </td>
                    </tr>
                    <tr
                        class="registration-row cursor-pointer hover:bg-[#FFF9F7] transition"
                        data-name="Rico Santiago"
                        data-email="rico.santiago@gmail.com"
                        data-phone="0917 333 5577"
                        data-type="buyer"
                        data-status="pending"
                        data-date="2026-05-23"
                        role="button"
                        tabindex="0"
                    >
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-200 shrink-0"></div>
                                <span class="text-[13px] font-medium text-gray-800">Rico Santiago</span>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('icons/admin/dashboard/body/buyer.png') }}" class="w-5 h-5 object-contain" alt="Buyer">
                                <span class="text-[13px] text-gray-800">Buyer</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-[13px] text-gray-400">rico.santiago@gmail.com</td>
                        <td class="px-5 py-3 text-[13px] text-gray-800">0917 333 5577</td>
                        <td class="px-5 py-3">
                            <div class="text-[13px] text-gray-800">May 23, 2026</div>
                            <div class="text-[12px] text-gray-800">04:42 PM</div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-[#FFE5D0] text-[#E87D22] text-[11px] font-medium border border-[#FFD1B8]">
                                Pending
                            </span>
                        </td>
                    </tr>
                    <tr
                        class="registration-row cursor-pointer hover:bg-[#FFF9F7] transition"
                        data-name="Jasmine Co"
                        data-email="jasmine.co@gmail.com"
                        data-phone="0925 555 7788"
                        data-type="seller"
                        data-status="pending"
                        data-date="2026-05-23"
                        role="button"
                        tabindex="0"
                    >
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-200 shrink-0"></div>
                                <span class="text-[13px] font-medium text-gray-800">Jasmine Co</span>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('icons/admin/dashboard/body/seller.png') }}" class="w-5 h-5 object-contain" alt="Seller">
                                <span class="text-[13px] text-gray-800">Seller</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-[13px] text-gray-400">jasmine.co@gmail.com</td>
                        <td class="px-5 py-3 text-[13px] text-gray-800">0925 555 7788</td>
                        <td class="px-5 py-3">
                            <div class="text-[13px] text-gray-800">May 23, 2026</div>
                            <div class="text-[12px] text-gray-800">12:15 PM</div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-[#FFE5D0] text-[#E87D22] text-[11px] font-medium border border-[#FFD1B8]">
                                Pending
                            </span>
                        </td>
                    </tr>
                    <tr
                        class="registration-row cursor-pointer hover:bg-[#FFF9F7] transition"
                        data-name="Nathan Sy"
                        data-email="nathan.sy@gmail.com"
                        data-phone="0919 666 8899"
                        data-type="buyer"
                        data-status="pending"
                        data-date="2026-05-22"
                        role="button"
                        tabindex="0"
                    >
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-200 shrink-0"></div>
                                <span class="text-[13px] font-medium text-gray-800">Nathan Sy</span>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('icons/admin/dashboard/body/buyer.png') }}" class="w-5 h-5 object-contain" alt="Buyer">
                                <span class="text-[13px] text-gray-800">Buyer</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-[13px] text-gray-400">nathan.sy@gmail.com</td>
                        <td class="px-5 py-3 text-[13px] text-gray-800">0919 666 8899</td>
                        <td class="px-5 py-3">
                            <div class="text-[13px] text-gray-800">May 22, 2026</div>
                            <div class="text-[12px] text-gray-800">09:55 AM</div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-[#FFE5D0] text-[#E87D22] text-[11px] font-medium border border-[#FFD1B8]">
                                Pending
                            </span>
                        </td>
                    </tr>
                    <tr
                        class="registration-row cursor-pointer hover:bg-[#FFF9F7] transition"
                        data-name="Camille Rivera"
                        data-email="camille.rivera@gmail.com"
                        data-phone="0917 123 8800"
                        data-type="seller"
                        data-status="pending"
                        data-date="2026-05-22"
                        role="button"
                        tabindex="0"
                    >
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-200 shrink-0"></div>
                                <span class="text-[13px] font-medium text-gray-800">Camille Rivera</span>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('icons/admin/dashboard/body/seller.png') }}" class="w-5 h-5 object-contain" alt="Seller">
                                <span class="text-[13px] text-gray-800">Seller</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-[13px] text-gray-400">camille.rivera@gmail.com</td>
                        <td class="px-5 py-3 text-[13px] text-gray-800">0917 123 8800</td>
                        <td class="px-5 py-3">
                            <div class="text-[13px] text-gray-800">May 22, 2026</div>
                            <div class="text-[12px] text-gray-800">08:40 AM</div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-[#FFE5D0] text-[#E87D22] text-[11px] font-medium border border-[#FFD1B8]">
                                Pending
                            </span>
                        </td>
                    </tr>
                    <tr
                        class="registration-row cursor-pointer hover:bg-[#FFF9F7] transition"
                        data-name="Andrei Castillo"
                        data-email="andrei.castillo@gmail.com"
                        data-phone="0921 777 9911"
                        data-type="buyer"
                        data-status="pending"
                        data-date="2026-05-21"
                        role="button"
                        tabindex="0"
                    >
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-200 shrink-0"></div>
                                <span class="text-[13px] font-medium text-gray-800">Andrei Castillo</span>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('icons/admin/dashboard/body/buyer.png') }}" class="w-5 h-5 object-contain" alt="Buyer">
                                <span class="text-[13px] text-gray-800">Buyer</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-[13px] text-gray-400">andrei.castillo@gmail.com</td>
                        <td class="px-5 py-3 text-[13px] text-gray-800">0921 777 9911</td>
                        <td class="px-5 py-3">
                            <div class="text-[13px] text-gray-800">May 21, 2026</div>
                            <div class="text-[12px] text-gray-800">05:30 PM</div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-[#FFE5D0] text-[#E87D22] text-[11px] font-medium border border-[#FFD1B8]">
                                Pending
                            </span>
                        </td>
                    </tr>
                    <tr
                        class="registration-row cursor-pointer hover:bg-[#FFF9F7] transition"
                        data-name="Mica Torres"
                        data-email="mica.torres@gmail.com"
                        data-phone="0918 345 6789"
                        data-type="seller"
                        data-status="pending"
                        data-date="2026-05-21"
                        role="button"
                        tabindex="0"
                    >
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-200 shrink-0"></div>
                                <span class="text-[13px] font-medium text-gray-800">Mica Torres</span>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('icons/admin/dashboard/body/seller.png') }}" class="w-5 h-5 object-contain" alt="Seller">
                                <span class="text-[13px] text-gray-800">Seller</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-[13px] text-gray-400">mica.torres@gmail.com</td>
                        <td class="px-5 py-3 text-[13px] text-gray-800">0918 345 6789</td>
                        <td class="px-5 py-3">
                            <div class="text-[13px] text-gray-800">May 21, 2026</div>
                            <div class="text-[12px] text-gray-800">02:14 PM</div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-[#FFE5D0] text-[#E87D22] text-[11px] font-medium border border-[#FFD1B8]">
                                Pending
                            </span>
                        </td>
                    </tr>
                    <tr
                        class="registration-row cursor-pointer hover:bg-[#FFF9F7] transition"
                        data-name="Gabriel Ramos"
                        data-email="gabriel.ramos@gmail.com"
                        data-phone="0915 555 1212"
                        data-type="buyer"
                        data-status="pending"
                        data-date="2026-05-20"
                        role="button"
                        tabindex="0"
                    >
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-200 shrink-0"></div>
                                <span class="text-[13px] font-medium text-gray-800">Gabriel Ramos</span>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('icons/admin/dashboard/body/buyer.png') }}" class="w-5 h-5 object-contain" alt="Buyer">
                                <span class="text-[13px] text-gray-800">Buyer</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-[13px] text-gray-400">gabriel.ramos@gmail.com</td>
                        <td class="px-5 py-3 text-[13px] text-gray-800">0915 555 1212</td>
                        <td class="px-5 py-3">
                            <div class="text-[13px] text-gray-800">May 20, 2026</div>
                            <div class="text-[12px] text-gray-800">03:47 PM</div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-[#FFE5D0] text-[#E87D22] text-[11px] font-medium border border-[#FFD1B8]">
                                Pending
                            </span>
                        </td>
                    </tr>
                    <tr
                        class="registration-row cursor-pointer hover:bg-[#FFF9F7] transition"
                        data-name="Ella Fernandez"
                        data-email="ella.fernandez@gmail.com"
                        data-phone="0928 111 3434"
                        data-type="seller"
                        data-status="pending"
                        data-date="2026-05-20"
                        role="button"
                        tabindex="0"
                    >
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-200 shrink-0"></div>
                                <span class="text-[13px] font-medium text-gray-800">Ella Fernandez</span>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('icons/admin/dashboard/body/seller.png') }}" class="w-5 h-5 object-contain" alt="Seller">
                                <span class="text-[13px] text-gray-800">Seller</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-[13px] text-gray-400">ella.fernandez@gmail.com</td>
                        <td class="px-5 py-3 text-[13px] text-gray-800">0928 111 3434</td>
                        <td class="px-5 py-3">
                            <div class="text-[13px] text-gray-800">May 20, 2026</div>
                            <div class="text-[12px] text-gray-800">11:06 AM</div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-[#FFE5D0] text-[#E87D22] text-[11px] font-medium border border-[#FFD1B8]">
                                Pending
                            </span>
                        </td>
                    </tr>
                    <tr
                        class="registration-row cursor-pointer hover:bg-[#FFF9F7] transition"
                        data-name="Luis Mercado"
                        data-email="luis.mercado@gmail.com"
                        data-phone="0909 222 5656"
                        data-type="buyer"
                        data-status="pending"
                        data-date="2026-05-19"
                        role="button"
                        tabindex="0"
                    >
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-200 shrink-0"></div>
                                <span class="text-[13px] font-medium text-gray-800">Luis Mercado</span>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('icons/admin/dashboard/body/buyer.png') }}" class="w-5 h-5 object-contain" alt="Buyer">
                                <span class="text-[13px] text-gray-800">Buyer</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-[13px] text-gray-400">luis.mercado@gmail.com</td>
                        <td class="px-5 py-3 text-[13px] text-gray-800">0909 222 5656</td>
                        <td class="px-5 py-3">
                            <div class="text-[13px] text-gray-800">May 19, 2026</div>
                            <div class="text-[12px] text-gray-800">10:45 AM</div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-[#FFE5D0] text-[#E87D22] text-[11px] font-medium border border-[#FFD1B8]">
                                Pending
                            </span>
                        </td>
                    </tr>
                    <tr
                        class="registration-row cursor-pointer hover:bg-[#FFF9F7] transition"
                        data-name="Trisha Ong"
                        data-email="trisha.ong@gmail.com"
                        data-phone="0917 888 3434"
                        data-type="seller"
                        data-status="pending"
                        data-date="2026-05-19"
                        role="button"
                        tabindex="0"
                    >
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-200 shrink-0"></div>
                                <span class="text-[13px] font-medium text-gray-800">Trisha Ong</span>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('icons/admin/dashboard/body/seller.png') }}" class="w-5 h-5 object-contain" alt="Seller">
                                <span class="text-[13px] text-gray-800">Seller</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-[13px] text-gray-400">trisha.ong@gmail.com</td>
                        <td class="px-5 py-3 text-[13px] text-gray-800">0917 888 3434</td>
                        <td class="px-5 py-3">
                            <div class="text-[13px] text-gray-800">May 19, 2026</div>
                            <div class="text-[12px] text-gray-800">09:32 AM</div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-[#FFE5D0] text-[#E87D22] text-[11px] font-medium border border-[#FFD1B8]">
                                Pending
                            </span>
                        </td>
                    </tr>
                    <tr
                        class="registration-row cursor-pointer hover:bg-[#FFF9F7] transition"
                        data-name="Enzo Manalo"
                        data-email="enzo.manalo@gmail.com"
                        data-phone="0922 444 7676"
                        data-type="buyer"
                        data-status="pending"
                        data-date="2026-05-18"
                        role="button"
                        tabindex="0"
                    >
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-200 shrink-0"></div>
                                <span class="text-[13px] font-medium text-gray-800">Enzo Manalo</span>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('icons/admin/dashboard/body/buyer.png') }}" class="w-5 h-5 object-contain" alt="Buyer">
                                <span class="text-[13px] text-gray-800">Buyer</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-[13px] text-gray-400">enzo.manalo@gmail.com</td>
                        <td class="px-5 py-3 text-[13px] text-gray-800">0922 444 7676</td>
                        <td class="px-5 py-3">
                            <div class="text-[13px] text-gray-800">May 18, 2026</div>
                            <div class="text-[12px] text-gray-800">06:05 PM</div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-[#FFE5D0] text-[#E87D22] text-[11px] font-medium border border-[#FFD1B8]">
                                Pending
                            </span>
                        </td>
                    </tr>
                    <tr
                        class="registration-row cursor-pointer hover:bg-[#FFF9F7] transition"
                        data-name="Claire Aquino"
                        data-email="claire.aquino@gmail.com"
                        data-phone="0916 222 9898"
                        data-type="seller"
                        data-status="pending"
                        data-date="2026-05-18"
                        role="button"
                        tabindex="0"
                    >
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-200 shrink-0"></div>
                                <span class="text-[13px] font-medium text-gray-800">Claire Aquino</span>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('icons/admin/dashboard/body/seller.png') }}" class="w-5 h-5 object-contain" alt="Seller">
                                <span class="text-[13px] text-gray-800">Seller</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-[13px] text-gray-400">claire.aquino@gmail.com</td>
                        <td class="px-5 py-3 text-[13px] text-gray-800">0916 222 9898</td>
                        <td class="px-5 py-3">
                            <div class="text-[13px] text-gray-800">May 18, 2026</div>
                            <div class="text-[12px] text-gray-800">02:21 PM</div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-[#FFE5D0] text-[#E87D22] text-[11px] font-medium border border-[#FFD1B8]">
                                Pending
                            </span>
                        </td>
                    </tr>
                    <tr
                        class="registration-row cursor-pointer hover:bg-[#FFF9F7] transition"
                        data-name="Adrian Cruz"
                        data-email="adrian.cruz@gmail.com"
                        data-phone="0918 333 6767"
                        data-type="buyer"
                        data-status="pending"
                        data-date="2026-05-17"
                        role="button"
                        tabindex="0"
                    >
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-200 shrink-0"></div>
                                <span class="text-[13px] font-medium text-gray-800">Adrian Cruz</span>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('icons/admin/dashboard/body/buyer.png') }}" class="w-5 h-5 object-contain" alt="Buyer">
                                <span class="text-[13px] text-gray-800">Buyer</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-[13px] text-gray-400">adrian.cruz@gmail.com</td>
                        <td class="px-5 py-3 text-[13px] text-gray-800">0918 333 6767</td>
                        <td class="px-5 py-3">
                            <div class="text-[13px] text-gray-800">May 17, 2026</div>
                            <div class="text-[12px] text-gray-800">01:10 PM</div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-[#FFE5D0] text-[#E87D22] text-[11px] font-medium border border-[#FFD1B8]">
                                Pending
                            </span>
                        </td>
                    </tr>
                </tbody>

            </table>

        </div>


        <!-- =====================================================
             TABLE FOOTER / PAGINATION
        ====================================================== -->

        <div
            class="flex flex-col md:flex-row items-center justify-between
                   gap-4 px-5 py-4 border-t border-gray-200"
        >

            <p
                id="registration-count"
                class="text-[13px] text-gray-400"
            >
                Showing 1–10 of 30 entries
            </p>


            <div class="flex items-center gap-2">

                <button
                    type="button"
                    id="registration-prev"
                    class="w-7 h-7 flex items-center justify-center
                           text-gray-700 hover:bg-gray-100
                           rounded transition disabled:opacity-30 disabled:cursor-not-allowed"
                    aria-label="Previous page"
                >
                    ‹
                </button>

                <div id="registration-pages" class="flex items-center gap-2"></div>

                <button
                    type="button"
                    id="registration-next"
                    class="w-7 h-7 flex items-center justify-center
                           text-gray-700 hover:bg-gray-100
                           rounded transition disabled:opacity-30 disabled:cursor-not-allowed"
                    aria-label="Next page"
                >
                    ›
                </button>

                <select
                    id="items-per-page"
                    class="ml-2 h-8 rounded-md
                           border border-[#F0B9AC]
                           bg-[#FFF5F1]
                           px-2 text-[12px] text-gray-700
                           outline-none cursor-pointer"
                >
                    <option value="10" selected>Items per page: 10</option>
                    <option value="20">Items per page: 20</option>
                    <option value="50">Items per page: 50</option>
                </select>

            </div>

        </div>

    </div>

</div>


@push('scripts')

<script>

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
    const initialPendingCount = allRows.length;

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

    const initialApprovedCount = approvedUsersBody
        ? approvedUsersBody.querySelectorAll('tr').length
        : 0;

    const initialRejectedCount = rejectedUsersBody
        ? rejectedUsersBody.querySelectorAll('tr').length
        : 0;

    const totalRegistrations =
        initialPendingCount + initialApprovedCount + initialRejectedCount;

    let approvedCount = initialApprovedCount;
    let rejectedCount = initialRejectedCount;

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
        const type = row.dataset.type === 'seller' ? 'Seller' : 'Buyer';
        const dateLabel = action === 'approved' ? 'Date Approved' : 'Date Rejected';
        const icon = type === 'Seller'
            ? "{{ asset('icons/admin/dashboard/body/seller.png') }}"
            : "{{ asset('icons/admin/dashboard/body/buyer.png') }}";

        const tr = document.createElement('tr');
        tr.className = 'hover:bg-[#FFF9F7] transition';
        tr.dataset.sourceName = name;
        tr.dataset.action = action;
        tr.dataset.archiveTimestamp = String(Date.now());

        tr.innerHTML = `
            <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-[#F6D8D2] flex items-center justify-center text-[11px] font-semibold text-[#7B1B1B] shrink-0">
                        ${initials(name)}
                    </div>
                    <span class="text-[13px] font-medium text-gray-800">${name}</span>
                </div>
            </td>
            <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                    <img src="${icon}" class="w-5 h-5 object-contain" alt="${type}">
                    <span class="text-[13px]">${type}</span>
                </div>
            </td>
            <td class="px-6 py-4 text-[13px] text-gray-400">${email}</td>
            <td class="px-6 py-4 text-[13px]">${phone}</td>
            <td class="px-6 py-4 text-[13px]">${formatCurrentDate()}</td>
        `;

        return tr;
    }

    function updateRegistrationStats() {
        if (pendingCountCard) {
            pendingCountCard.textContent = allRows.length;
        }

        if (approvedCountCard) {
            approvedCountCard.textContent = approvedCount;
        }

        if (rejectedCountCard) {
            rejectedCountCard.textContent = rejectedCount;
        }

        if (totalCountCard) {
            totalCountCard.textContent = totalRegistrations;
        }

        if (approvedUsersCount) {
            approvedUsersCount.textContent = `${approvedCount} approved users`;
        }

        if (rejectedUsersCount) {
            rejectedUsersCount.textContent = `${rejectedCount} rejected users`;
        }
    }

    function filterArchiveUsers(tbody, searchInput) {
        if (!tbody || !searchInput) {
            return;
        }

        const query = searchInput.value.toLowerCase().trim();
        const archiveRows = Array.from(tbody.querySelectorAll('tr'));

        archiveRows.forEach(row => {
            const rowText = row.textContent.toLowerCase();
            const matches = query === '' || rowText.includes(query);
            row.classList.toggle('hidden', !matches);
        });
    }

    function resetArchiveSearch(searchInput, tbody) {
        if (searchInput) {
            searchInput.value = '';
        }

        if (tbody) {
            tbody.querySelectorAll('tr').forEach(row => row.classList.remove('hidden'));
        }
    }

    /* =========================================================
       APPROVED / REJECTED DATE SORTING
    ========================================================== */

    const archiveSortState = {
        'approved-users-body': 'desc',
        'rejected-users-body': 'desc'
    };

    function getArchiveRowDate(row) {
        const dateCell = row.cells[4];

        if (!dateCell) {
            return 0;
        }

        const timestamp = Date.parse(dateCell.textContent.trim());

        return Number.isNaN(timestamp) ? 0 : timestamp;
    }

    function sortArchiveRows(tbodyId, direction) {
        const tbody = document.getElementById(tbodyId);

        if (!tbody) {
            return;
        }

        const rows = Array.from(tbody.querySelectorAll('tr'));

        rows.sort((a, b) => {
            const dateA = getArchiveRowDate(a);
            const dateB = getArchiveRowDate(b);

            return direction === 'asc'
                ? dateA - dateB
                : dateB - dateA;
        });

        rows.forEach(row => tbody.appendChild(row));
    }

    function updateArchiveSortArrow(button, direction) {
        const arrow = button.querySelector('.archive-sort-arrow');

        if (arrow) {
            arrow.textContent = direction === 'asc' ? '↑' : '↓';
        }
    }

    document.querySelectorAll('.archive-date-sort').forEach(button => {
        const targetId = button.dataset.target;

        // Default: newest date first.
        sortArchiveRows(targetId, archiveSortState[targetId]);

        button.addEventListener('click', function () {
            const currentDirection = archiveSortState[targetId] || 'desc';
            const nextDirection = currentDirection === 'desc' ? 'asc' : 'desc';

            archiveSortState[targetId] = nextDirection;
            sortArchiveRows(targetId, nextDirection);
            updateArchiveSortArrow(this, nextDirection);
        });
    });

    if (approvedUsersSearch) {
        approvedUsersSearch.addEventListener('input', function () {
            filterArchiveUsers(approvedUsersBody, approvedUsersSearch);
        });
    }

    if (rejectedUsersSearch) {
        rejectedUsersSearch.addEventListener('input', function () {
            filterArchiveUsers(rejectedUsersBody, rejectedUsersSearch);
        });
    }

    function getFilters() {
        return {
            search: searchInput ? searchInput.value.toLowerCase().trim() : '',
            userType: userTypeFilter ? userTypeFilter.value : 'all',
            date: dateFilter ? dateFilter.value : ''
        };
    }

    function matchesFilters(row, filters) {
        const name = (row.dataset.name || '').toLowerCase();
        const email = (row.dataset.email || '').toLowerCase();
        const phone = (row.dataset.phone || '').toLowerCase();
        const type = row.dataset.type || '';
        const rowDate = row.dataset.date || '';

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

        return matchesSearch && matchesType && matchesDate;
    }

    function renderPagination() {
        const pagesContainer = document.getElementById('registration-pages');
        const prevButton = document.getElementById('registration-prev');
        const nextButton = document.getElementById('registration-next');

        const totalPages = Math.max(1, Math.ceil(filteredRows.length / itemsPerPage));

        currentPage = Math.min(currentPage, totalPages);
        currentPage = Math.max(currentPage, 1);

        if (pagesContainer) {
            pagesContainer.innerHTML = '';

            for (let page = 1; page <= totalPages; page++) {
                const button = document.createElement('button');
                button.type = 'button';
                button.textContent = page;
                button.dataset.page = page;
                button.className =
                    'registration-page w-7 h-7 flex items-center justify-center rounded-md text-[12px] transition ' +
                    (page === currentPage
                        ? 'bg-[#FFD1C2] text-[#7B1B1B] font-medium'
                        : 'hover:bg-gray-100 text-gray-700');

                button.addEventListener('click', function () {
                    currentPage = Number(this.dataset.page);
                    renderTable();
                });

                pagesContainer.appendChild(button);
            }
        }

        if (prevButton) {
            prevButton.disabled = currentPage <= 1;
            prevButton.classList.toggle('opacity-40', currentPage <= 1);
            prevButton.classList.toggle('cursor-not-allowed', currentPage <= 1);
        }

        if (nextButton) {
            nextButton.disabled = currentPage >= totalPages;
            nextButton.classList.toggle('opacity-40', currentPage >= totalPages);
            nextButton.classList.toggle('cursor-not-allowed', currentPage >= totalPages);
        }
    }

    function renderTable() {
        allRows.forEach(row => row.classList.add('hidden'));

        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const pageRows = filteredRows.slice(startIndex, endIndex);

        pageRows.forEach(row => row.classList.remove('hidden'));

        const visibleStart = filteredRows.length === 0 ? 0 : startIndex + 1;
        const visibleEnd = Math.min(endIndex, filteredRows.length);

        if (count) {
            count.textContent =
                `Showing ${visibleStart}–${visibleEnd} of ${filteredRows.length} entries`;
        }

        renderPagination();
        updateRegistrationStats();
    }

    function filterRegistrations(resetPage = true) {
        const filters = getFilters();
        filteredRows = allRows.filter(row => matchesFilters(row, filters));

        if (resetPage) {
            currentPage = 1;
        }

        renderTable();
    }

    if (searchInput) {
        searchInput.addEventListener('input', () => filterRegistrations(true));
    }

    if (userTypeFilter) {
        userTypeFilter.addEventListener('change', () => filterRegistrations(true));
    }

    if (dateFilter) {
        dateFilter.addEventListener('change', () => filterRegistrations(true));
    }

    const dateCalendarButton = document.getElementById('date-calendar-button');

    if (dateCalendarButton && dateFilter) {
        dateCalendarButton.addEventListener('click', function () {
            if (typeof dateFilter.showPicker === 'function') {
                dateFilter.showPicker();
            } else {
                dateFilter.focus();
                dateFilter.click();
            }
        });
    }

    const prevButton = document.getElementById('registration-prev');
    const nextButton = document.getElementById('registration-next');

    if (prevButton) {
        prevButton.addEventListener('click', function () {
            if (currentPage > 1) {
                currentPage--;
                renderTable();
            }
        });
    }

    if (nextButton) {
        nextButton.addEventListener('click', function () {
            const totalPages = Math.max(1, Math.ceil(filteredRows.length / itemsPerPage));

            if (currentPage < totalPages) {
                currentPage++;
                renderTable();
            }
        });
    }

    if (itemsPerPageSelect) {
        itemsPerPageSelect.addEventListener('change', function () {
            itemsPerPage = Number(this.value) || 10;
            currentPage = 1;
            renderTable();
        });
    }

    /* =========================================================
       RELOAD UX
    ========================================================== */

    if (reloadButton) {
        reloadButton.addEventListener('click', function () {
            if (reloadIcon) {
                reloadIcon.classList.add('animate-spin');
            }

            reloadButton.disabled = true;

            setTimeout(() => {
                if (searchInput) searchInput.value = '';
                if (userTypeFilter) userTypeFilter.value = 'all';
                if (dateFilter) dateFilter.value = '';
                if (itemsPerPageSelect) itemsPerPageSelect.value = '10';

                itemsPerPage = 10;
                currentPage = 1;
                filterRegistrations(true);

                if (reloadIcon) {
                    reloadIcon.classList.remove('animate-spin');
                }

                reloadButton.disabled = false;
            }, 500);
        });
    }

    /* =========================================================
       REGISTRATION DETAILS MODALS
    ========================================================== */

    const sellerDetailsModal = document.getElementById('seller-details-modal');
    const buyerDetailsModal = document.getElementById('buyer-details-modal');

    function openRegistrationModal(modal) {

        if (!modal) {
            return;
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');

    }


    function closeRegistrationModal(modal) {

        if (!modal) {
            return;
        }

        modal.classList.add('hidden');
        modal.classList.remove('flex');
        modal.setAttribute('aria-hidden', 'true');

        const anyRegistrationModalOpen =
            (sellerDetailsModal && !sellerDetailsModal.classList.contains('hidden')) ||
            (buyerDetailsModal && !buyerDetailsModal.classList.contains('hidden'));

        if (!anyRegistrationModalOpen) {
            document.body.classList.remove('overflow-hidden');
        }

    }


    document.querySelectorAll('.registration-row').forEach(row => {

        row.addEventListener('click', function () {

            const type = (this.dataset.type || '').toLowerCase();

            if (type === 'seller') {
                openRegistrationModal(sellerDetailsModal);
                return;
            }

            if (type === 'buyer') {
                openRegistrationModal(buyerDetailsModal);
            }

        });

        row.setAttribute('role', 'button');
        row.setAttribute('tabindex', '0');

        row.addEventListener('keydown', function (event) {

            if (event.key !== 'Enter' && event.key !== ' ') {
                return;
            }

            event.preventDefault();
            this.click();

        });

    });


    document.querySelectorAll('.registration-modal-close').forEach(button => {

        button.addEventListener('click', function () {

            const modalId = this.dataset.modal;
            closeRegistrationModal(document.getElementById(modalId));

        });

    });


    [sellerDetailsModal, buyerDetailsModal].forEach(modal => {

        if (!modal) {
            return;
        }

        modal.addEventListener('click', function (event) {

            if (event.target === modal) {
                closeRegistrationModal(modal);
            }

        });

    });


    document.querySelectorAll('.registration-row').forEach(row => {

        row.addEventListener('click', function () {

            activeRegistrationRow = this;

            const type = (this.dataset.type || '').toLowerCase();

            if (type === 'seller') {
                openRegistrationModal(sellerDetailsModal);
                return;
            }

            if (type === 'buyer') {
                openRegistrationModal(buyerDetailsModal);
            }

        });

        row.setAttribute('role', 'button');
        row.setAttribute('tabindex', '0');

        row.addEventListener('keydown', function (event) {

            if (event.key !== 'Enter' && event.key !== ' ') {
                return;
            }

            event.preventDefault();
            this.click();

        });

    });


    document.querySelectorAll('.registration-modal-close').forEach(button => {

        button.addEventListener('click', function () {

            const modalId = this.dataset.modal;
            closeRegistrationModal(document.getElementById(modalId));

        });

    });


    [sellerDetailsModal, buyerDetailsModal].forEach(modal => {

        if (!modal) {
            return;
        }

        modal.addEventListener('click', function (event) {

            if (event.target === modal) {
                closeRegistrationModal(modal);
            }

        });

    });


    function moveRegistrationToArchive(row, action) {
        if (!row || !row.parentNode) {
            return;
        }

        const archiveBody = action === 'approved'
            ? approvedUsersBody
            : rejectedUsersBody;

        if (!archiveBody) {
            return;
        }

        const archiveRow = createArchiveRow(row, action);
        archiveBody.insertBefore(archiveRow, archiveBody.firstChild);

        if (action === 'approved') {
            sortArchiveRows('approved-users-body', archiveSortState['approved-users-body']);
            if (approvedUsersSearch) {
                filterArchiveUsers(approvedUsersBody, approvedUsersSearch);
            }
            approvedCount++;
        } else {
            sortArchiveRows('rejected-users-body', archiveSortState['rejected-users-body']);
            rejectedCount++;
            if (rejectedUsersSearch) {
                filterArchiveUsers(rejectedUsersBody, rejectedUsersSearch);
            }
        }

        allRows = allRows.filter(item => item !== row);
        row.remove();
        activeRegistrationRow = null;

        filterRegistrations(true);
        updateRegistrationStats();
    }


    /* =========================================================
       APPROVE / REJECT REGISTRATION ACTIONS
    ========================================================== */

    const rejectRegistrationModal =
        document.getElementById('reject-registration-modal');

    const cancelRejectRegistration =
        document.getElementById('cancel-reject-registration');

    const confirmRejectRegistration =
        document.getElementById('confirm-reject-registration');

    const rejectAdditionalDetails =
        document.getElementById('reject-additional-details');

    const rejectDetailsCount =
        document.getElementById('reject-details-count');

    const rejectReasonError =
        document.getElementById('reject-reason-error');

    const registrationFlash =
        document.getElementById('registration-flash');

    const registrationFlashIcon =
        document.getElementById('registration-flash-icon');

    const registrationFlashTitle =
        document.getElementById('registration-flash-title');

    const registrationFlashMessage =
        document.getElementById('registration-flash-message');

    const registrationFlashClose =
        document.getElementById('registration-flash-close');

    let registrationFlashTimer = null;

    function getSelectedRejectReason() {
        const checked =
            document.querySelector('input[name="reject_reason"]:checked');

        return checked ? checked.value : '';
    }

    function resetRejectRegistrationForm() {
        document
            .querySelectorAll('input[name="reject_reason"]')
            .forEach(input => {
                input.checked = false;
            });

        if (rejectAdditionalDetails) {
            rejectAdditionalDetails.value = '';
        }

        if (rejectDetailsCount) {
            rejectDetailsCount.textContent = '0/300';
        }

        if (rejectReasonError) {
            rejectReasonError.classList.add('hidden');
        }
    }

    function openRejectRegistrationModal() {
        if (!rejectRegistrationModal) {
            return;
        }

        resetRejectRegistrationForm();

        rejectRegistrationModal.classList.remove('hidden');
        rejectRegistrationModal.classList.add('flex');
        rejectRegistrationModal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
    }

    function closeRejectRegistrationModal() {
        if (!rejectRegistrationModal) {
            return;
        }

        rejectRegistrationModal.classList.add('hidden');
        rejectRegistrationModal.classList.remove('flex');
        rejectRegistrationModal.setAttribute('aria-hidden', 'true');

        const anyRegistrationModalOpen =
            (sellerDetailsModal && !sellerDetailsModal.classList.contains('hidden')) ||
            (buyerDetailsModal && !buyerDetailsModal.classList.contains('hidden'));

        if (!anyRegistrationModalOpen) {
            document.body.classList.remove('overflow-hidden');
        }
    }

    function showRegistrationFlash(type, name) {
        if (!registrationFlash) {
            return;
        }

        const approved = type === 'approved';

        registrationFlash.classList.remove(
            'border-[#B6D9AC]',
            'border-[#E6B4B4]'
        );

        registrationFlash.classList.add(
            approved ? 'border-[#B6D9AC]' : 'border-[#E6B4B4]'
        );

        registrationFlashIcon.className =
            'w-9 h-9 rounded-full flex items-center justify-center shrink-0 ' +
            (approved ? 'bg-[#DDF0D6]' : 'bg-[#FFE0E0]');

        registrationFlashIcon.innerHTML = approved
            ? `<svg class="w-5 h-5 text-[#28721B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 12 4 4L19 6"/>
               </svg>`
            : `<svg class="w-5 h-5 text-[#A52A2A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6 6 18"/>
               </svg>`;

        registrationFlashTitle.textContent =
            approved ? 'Registration Approved' : 'Registration Rejected';

        registrationFlashMessage.textContent =
            approved
                ? `${name}'s registration has been approved. The user has been notified via email.`
                : `${name}'s registration has been rejected. The user has been notified via email.`;

        registrationFlash.classList.remove('hidden');

        clearTimeout(registrationFlashTimer);
        registrationFlashTimer = setTimeout(() => {
            registrationFlash.classList.add('hidden');
        }, 4500);
    }

    if (registrationFlashClose) {
        registrationFlashClose.addEventListener('click', () => {
            clearTimeout(registrationFlashTimer);

            if (registrationFlash) {
                registrationFlash.classList.add('hidden');
            }
        });
    }

    if (rejectAdditionalDetails && rejectDetailsCount) {
        rejectAdditionalDetails.addEventListener('input', () => {
            rejectDetailsCount.textContent =
                `${rejectAdditionalDetails.value.length}/300`;
        });
    }

    if (cancelRejectRegistration) {
        cancelRejectRegistration.addEventListener(
            'click',
            closeRejectRegistrationModal
        );
    }

    if (rejectRegistrationModal) {
        rejectRegistrationModal.addEventListener('click', function (event) {

            if (event.target === rejectRegistrationModal) {
                closeRejectRegistrationModal();
            }

        });
    }

    if (confirmRejectRegistration) {
        confirmRejectRegistration.addEventListener('click', function () {

            if (!activeRegistrationRow) {
                closeRejectRegistrationModal();
                return;
            }

            const reason = getSelectedRejectReason();

            if (!reason) {
                if (rejectReasonError) {
                    rejectReasonError.classList.remove('hidden');
                }

                return;
            }

            const name =
                activeRegistrationRow.dataset.name ||
                'The applicant';

            moveRegistrationToArchive(
                activeRegistrationRow,
                'rejected'
            );

            closeRegistrationModal(sellerDetailsModal);
            closeRegistrationModal(buyerDetailsModal);
            closeRejectRegistrationModal();

            showRegistrationFlash('rejected', name);

        });
    }

    document.querySelectorAll('.registration-reject').forEach(button => {

        button.addEventListener('click', function () {

            if (!activeRegistrationRow) {
                return;
            }

            openRejectRegistrationModal();

        });

    });

    document.querySelectorAll('.registration-approve').forEach(button => {

        button.addEventListener('click', function () {

            if (!activeRegistrationRow) {
                return;
            }

            const name =
                activeRegistrationRow.dataset.name ||
                'The applicant';

            const confirmed = window.confirm(
                `Are you sure you want to approve ${name}'s registration?`
            );

            if (!confirmed) {
                return;
            }

            moveRegistrationToArchive(
                activeRegistrationRow,
                'approved'
            );

            closeRegistrationModal(sellerDetailsModal);
            closeRegistrationModal(buyerDetailsModal);

            showRegistrationFlash('approved', name);

        });

    });



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
            function () {

                approvedUsersModal.classList.remove(
                    'hidden'
                );

                approvedUsersModal.classList.add(
                    'flex'
                );

                resetArchiveSearch(approvedUsersSearch, approvedUsersBody);

                document.body.classList.add(
                    'overflow-hidden'
                );

            }
        );

    }


    function closeApprovedModal() {

        if (!approvedUsersModal) {
            return;
        }

        approvedUsersModal.classList.add(
            'hidden'
        );

        approvedUsersModal.classList.remove(
            'flex'
        );

        document.body.classList.remove(
            'overflow-hidden'
        );

    }


    if (closeApprovedUsers) {

        closeApprovedUsers.addEventListener(
            'click',
            closeApprovedModal
        );

    }


    if (closeApprovedUsersBottom) {

        closeApprovedUsersBottom.addEventListener(
            'click',
            closeApprovedModal
        );

    }


    if (approvedUsersModal) {

        approvedUsersModal.addEventListener(
            'click',
            function (event) {

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
            function () {

                rejectedUsersModal.classList.remove(
                    'hidden'
                );

                rejectedUsersModal.classList.add(
                    'flex'
                );

                resetArchiveSearch(rejectedUsersSearch, rejectedUsersBody);

                document.body.classList.add(
                    'overflow-hidden'
                );

            }
        );

    }


    function closeRejectedModal() {

        if (!rejectedUsersModal) {
            return;
        }

        rejectedUsersModal.classList.add(
            'hidden'
        );

        rejectedUsersModal.classList.remove(
            'flex'
        );

        document.body.classList.remove(
            'overflow-hidden'
        );

    }


    if (closeRejectedUsers) {

        closeRejectedUsers.addEventListener(
            'click',
            closeRejectedModal
        );

    }


    if (closeRejectedUsersBottom) {

        closeRejectedUsersBottom.addEventListener(
            'click',
            closeRejectedModal
        );

    }


    if (rejectedUsersModal) {

        rejectedUsersModal.addEventListener(
            'click',
            function (event) {

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

    document.addEventListener('keydown', function (event) {

        if (event.key !== 'Escape') {
            return;
        }

        if (
            sellerDetailsModal &&
            !sellerDetailsModal.classList.contains('hidden')
        ) {
            closeRegistrationModal(sellerDetailsModal);
        }

        if (
            buyerDetailsModal &&
            !buyerDetailsModal.classList.contains('hidden')
        ) {
            closeRegistrationModal(buyerDetailsModal);
        }

        if (
            approvedUsersModal &&
            !approvedUsersModal.classList.contains('hidden')
        ) {
            closeApprovedModal();
        }

        if (
            rejectedUsersModal &&
            !rejectedUsersModal.classList.contains('hidden')
        ) {
            closeRejectedModal();
        }

        if (
            rejectRegistrationModal &&
            !rejectRegistrationModal.classList.contains('hidden')
        ) {
            closeRejectRegistrationModal();
        }

    });

    /* =========================================================
       PAGE LOAD UX
    ========================================================== */

    const pageContent =
        document.getElementById('admin-content');


    if (pageContent) {

        pageContent.classList.add(
            'opacity-0',
            'translate-y-2'
        );

        requestAnimationFrame(() => {

            setTimeout(() => {

                pageContent.classList.remove(
                    'opacity-0',
                    'translate-y-2'
                );

            }, 80);

        });

    }


    /* =========================================================
       INITIAL RENDER
    ========================================================== */

    renderTable();

});

</script>

@endpush

@endsection