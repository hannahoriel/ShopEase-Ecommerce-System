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

                    <p class="text-[26px] font-bold text-gray-900 leading-none">
                        236
                    </p>

                    <p class="text-[14px] text-gray-400 mt-1 whitespace-nowrap">
                        Pending Requests
                    </p>

                </div>

            </div>


            <!-- Bottom Text -->

            <p class="absolute left-[92px] bottom-5 text-[13px] text-green-600">
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

                            <p class="text-[26px] font-bold text-gray-900 leading-none">
                                236
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

                            <p class="text-[26px] font-bold text-gray-900 leading-none">
                                124
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
                        View archived users →
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
           flex flex-col justify-center
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

                    <p class="text-[26px] font-bold text-gray-900 leading-none">
                        378
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
            class="bg-white w-full max-w-5xl max-h-[80vh]
                   rounded-2xl shadow-xl overflow-hidden
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


            <!-- Approved Users Table -->

            <div class="overflow-auto max-h-[60vh]">

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
                                Date Approved
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-200">

                        <tr class="hover:bg-[#FFF9F7] transition">

                            <td class="px-6 py-4">

                                <div class="flex items-center gap-3">

                                    <div class="w-8 h-8 rounded-full bg-gray-200 shrink-0"></div>

                                    <span class="text-[13px] font-medium text-gray-800">
                                        Juan Dela Cruz
                                    </span>

                                </div>

                            </td>


                            <td class="px-6 py-4">

                                <div class="flex items-center gap-2">

                                    <img
                                        src="{{ asset('icons/admin/dashboard/body/buyer.png') }}"
                                        class="w-5 h-5 object-contain"
                                        alt="Buyer"
                                    >

                                    <span class="text-[13px]">
                                        Buyer
                                    </span>

                                </div>

                            </td>


                            <td class="px-6 py-4 text-[13px] text-gray-400">
                                juandelacruz@gmail.com
                            </td>


                            <td class="px-6 py-4 text-[13px]">
                                0917 123 4567
                            </td>


                            <td class="px-6 py-4 text-[13px]">
                                May 31, 2026
                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>


            <!-- Modal Footer -->

            <div
                class="px-6 py-4 border-t border-gray-200
                       flex items-center justify-between"
            >

                <p class="text-[13px] text-gray-400">
                    124 approved users
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
            class="bg-white w-full max-w-5xl max-h-[80vh]
                   rounded-2xl shadow-xl overflow-hidden
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


            <!-- Rejected Users Table -->

            <div class="overflow-auto max-h-[60vh]">

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
                                Date Rejected
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-200">

                        <!-- REJECTED USER 1 -->

                        <tr class="hover:bg-[#FFF9F7] transition">

                            <td class="px-6 py-4">

                                <div class="flex items-center gap-3">

                                    <div class="w-8 h-8 rounded-full bg-gray-200 shrink-0"></div>

                                    <span class="text-[13px] font-medium text-gray-800">
                                        Juan Dela Cruz
                                    </span>

                                </div>

                            </td>


                            <td class="px-6 py-4">

                                <div class="flex items-center gap-2">

                                    <img
                                        src="{{ asset('icons/admin/dashboard/body/seller.png') }}"
                                        class="w-5 h-5 object-contain"
                                        alt="Seller"
                                    >

                                    <span class="text-[13px]">
                                        Seller
                                    </span>

                                </div>

                            </td>


                            <td class="px-6 py-4 text-[13px] text-gray-400">
                                juandelacruz@gmail.com
                            </td>


                            <td class="px-6 py-4 text-[13px]">
                                0917 123 4567
                            </td>


                            <td class="px-6 py-4 text-[13px]">
                                May 31, 2026
                            </td>

                        </tr>


                        <!-- REJECTED USER 2 -->

                        <tr class="hover:bg-[#FFF9F7] transition">

                            <td class="px-6 py-4">

                                <div class="flex items-center gap-3">

                                    <div class="w-8 h-8 rounded-full bg-gray-200 shrink-0"></div>

                                    <span class="text-[13px] font-medium text-gray-800">
                                        Juan Dela Cruz
                                    </span>

                                </div>

                            </td>


                            <td class="px-6 py-4">

                                <div class="flex items-center gap-2">

                                    <img
                                        src="{{ asset('icons/admin/dashboard/body/buyer.png') }}"
                                        class="w-5 h-5 object-contain"
                                        alt="Buyer"
                                    >

                                    <span class="text-[13px]">
                                        Buyer
                                    </span>

                                </div>

                            </td>


                            <td class="px-6 py-4 text-[13px] text-gray-400">
                                juandelacruz@gmail.com
                            </td>


                            <td class="px-6 py-4 text-[13px]">
                                0917 123 4567
                            </td>


                            <td class="px-6 py-4 text-[13px]">
                                May 31, 2026
                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>


            <!-- Modal Footer -->

            <div
                class="px-6 py-4 border-t border-gray-200
                       flex items-center justify-between"
            >

                <p class="text-[13px] text-gray-400">
                    18 rejected users
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
                class="w-full min-w-[900px]"
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

                    <!-- ROW 1 -->

                    <tr
                        class="registration-row hover:bg-[#FFF9F7] transition"
                        data-name="Hannah Oriel"
                        data-email="juandelacruz@gmail.com"
                        data-phone="0917 123 4567"
                        data-type="seller"
                        data-status="pending"
                        data-date="2026-05-31"
                    >

                        <td class="px-5 py-3">

                            <div class="flex items-center gap-3">

                                <div class="w-8 h-8 rounded-full bg-gray-200 shrink-0"></div>

                                <span class="text-[13px] font-medium text-gray-800">
                                    Hannah Oriel
                                </span>

                            </div>

                        </td>


                        <td class="px-5 py-3">

                            <div class="flex items-center gap-2">

                                <img
                                    src="{{ asset('icons/admin/dashboard/body/seller.png') }}"
                                    class="w-5 h-5 object-contain"
                                    alt="Seller"
                                >

                                <span class="text-[13px] text-gray-800">
                                    Seller
                                </span>

                            </div>

                        </td>


                        <td class="px-5 py-3 text-[13px] text-gray-400">
                            juandelacruz@gmail.com
                        </td>


                        <td class="px-5 py-3 text-[13px] text-gray-800">
                            0917 123 4567
                        </td>


                        <td class="px-5 py-3">

                            <div class="text-[13px] text-gray-800">
                                May 31, 2026
                            </div>

                            <div class="text-[12px] text-gray-800">
                                10:30 AM
                            </div>

                        </td>


                        <td class="px-5 py-3">

                            <span
                                class="inline-flex items-center px-3 py-1
                                       rounded-full bg-[#FFE5D0]
                                       text-[#E87D22] text-[11px] font-medium"
                            >
                                Pending
                            </span>

                        </td>

                    </tr>


                    <!-- ROW 2 -->

                    <tr
                        class="registration-row hover:bg-[#FFF9F7] transition"
                        data-name="Juan Dela Cruz"
                        data-email="juandelacruz@gmail.com"
                        data-phone="0917 123 4567"
                        data-type="buyer"
                        data-status="pending"
                        data-date="2026-05-31"
                    >

                        <td class="px-5 py-3">

                            <div class="flex items-center gap-3">

                                <div class="w-8 h-8 rounded-full bg-gray-200"></div>

                                <span class="text-[13px] font-medium text-gray-800">
                                    Juan Dela Cruz
                                </span>

                            </div>

                        </td>


                        <td class="px-5 py-3">

                            <div class="flex items-center gap-2">

                                <img
                                    src="{{ asset('icons/admin/dashboard/body/buyer.png') }}"
                                    class="w-5 h-5 object-contain"
                                    alt="Buyer"
                                >

                                <span class="text-[13px] text-gray-800">
                                    Buyer
                                </span>

                            </div>

                        </td>


                        <td class="px-5 py-3 text-[13px] text-gray-400">
                            juandelacruz@gmail.com
                        </td>


                        <td class="px-5 py-3 text-[13px]">
                            0917 123 4567
                        </td>


                        <td class="px-5 py-3">

                            <div class="text-[13px]">
                                May 31, 2026
                            </div>

                            <div class="text-[12px]">
                                10:30 AM
                            </div>

                        </td>


                        <td class="px-5 py-3">

                            <span
                                class="inline-flex px-3 py-1 rounded-full
                                       bg-[#FFE5D0] text-[#E87D22]
                                       text-[11px] font-medium"
                            >
                                Pending
                            </span>

                        </td>

                    </tr>


                    <!-- ROW 3 -->

                    <tr
                        class="registration-row hover:bg-[#FFF9F7] transition"
                        data-name="Juan Dela Cruz"
                        data-email="juandelacruz@gmail.com"
                        data-phone="0917 123 4567"
                        data-type="buyer"
                        data-status="pending"
                        data-date="2026-05-31"
                    >

                        <td class="px-5 py-3">

                            <div class="flex items-center gap-3">

                                <div class="w-8 h-8 rounded-full bg-gray-200"></div>

                                <span class="text-[13px] font-medium">
                                    Juan Dela Cruz
                                </span>

                            </div>

                        </td>


                        <td class="px-5 py-3">

                            <div class="flex items-center gap-2">

                                <img
                                    src="{{ asset('icons/admin/dashboard/body/buyer.png') }}"
                                    class="w-5 h-5"
                                    alt="Buyer"
                                >

                                <span class="text-[13px]">
                                    Buyer
                                </span>

                            </div>

                        </td>


                        <td class="px-5 py-3 text-[13px] text-gray-400">
                            juandelacruz@gmail.com
                        </td>


                        <td class="px-5 py-3 text-[13px]">
                            0917 123 4567
                        </td>


                        <td class="px-5 py-3">

                            <div class="text-[13px]">
                                May 31, 2026
                            </div>

                            <div class="text-[12px]">
                                10:30 AM
                            </div>

                        </td>


                        <td class="px-5 py-3">

                            <span
                                class="inline-flex px-3 py-1 rounded-full
                                       bg-[#FFE5D0] text-[#E87D22]
                                       text-[11px] font-medium"
                            >
                                Pending
                            </span>

                        </td>

                    </tr>


                    <!-- ROW 4 -->

                    <tr
                        class="registration-row hover:bg-[#FFF9F7] transition"
                        data-name="Juan Dela Cruz"
                        data-email="juandelacruz@gmail.com"
                        data-phone="0917 123 4567"
                        data-type="buyer"
                        data-status="pending"
                        data-date="2026-05-31"
                    >

                        <td class="px-5 py-3">

                            <div class="flex items-center gap-3">

                                <div class="w-8 h-8 rounded-full bg-gray-200"></div>

                                <span class="text-[13px] font-medium">
                                    Juan Dela Cruz
                                </span>

                            </div>

                        </td>


                        <td class="px-5 py-3">

                            <div class="flex items-center gap-2">

                                <img
                                    src="{{ asset('icons/admin/dashboard/body/buyer.png') }}"
                                    class="w-5 h-5"
                                    alt="Buyer"
                                >

                                <span class="text-[13px]">
                                    Buyer
                                </span>

                            </div>

                        </td>


                        <td class="px-5 py-3 text-[13px] text-gray-400">
                            juandelacruz@gmail.com
                        </td>


                        <td class="px-5 py-3 text-[13px]">
                            0917 123 4567
                        </td>


                        <td class="px-5 py-3">

                            <div class="text-[13px]">
                                May 31, 2026
                            </div>

                            <div class="text-[12px]">
                                10:30 AM
                            </div>

                        </td>


                        <td class="px-5 py-3">

                            <span
                                class="inline-flex px-3 py-1 rounded-full
                                       bg-[#FFE5D0] text-[#E87D22]
                                       text-[11px] font-medium"
                            >
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
                Showing 4 out of 378 entries
            </p>


            <div class="flex items-center gap-2">

                <button
                    type="button"
                    id="registration-prev"
                    class="w-7 h-7 flex items-center justify-center
                           text-gray-700 hover:bg-gray-100
                           rounded transition"
                >
                    ‹
                </button>


                <button
                    type="button"
                    class="registration-page
                           w-7 h-7 flex items-center justify-center
                           rounded-md bg-[#FFD1C2] text-[#7B1B1B]
                           text-[12px] font-medium"
                    data-page="1"
                >
                    1
                </button>


                <button
                    type="button"
                    class="registration-page
                           w-7 h-7 flex items-center justify-center
                           rounded-md hover:bg-gray-100
                           text-[12px]"
                    data-page="2"
                >
                    2
                </button>


                <button
                    type="button"
                    class="registration-page
                           w-7 h-7 flex items-center justify-center
                           rounded-md hover:bg-gray-100
                           text-[12px]"
                    data-page="3"
                >
                    3
                </button>


                <button
                    type="button"
                    id="registration-next"
                    class="w-7 h-7 flex items-center justify-center
                           text-gray-700 hover:bg-gray-100
                           rounded transition"
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

                    <option value="10" selected>
                        Items per page: 10
                    </option>

                    <option value="20">
                        Items per page: 20
                    </option>

                    <option value="50">
                        Items per page: 50
                    </option>

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

    const statusFilter =
        document.getElementById('status-filter');

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
       TOTAL REGISTRATIONS
    ========================================================== */

    const totalRegistrations = 378;


    /* =========================================================
       FILTER FUNCTION
    ========================================================== */

    function filterRegistrations() {

        const search =
            searchInput
                ? searchInput.value.toLowerCase().trim()
                : '';

        const userType =
            userTypeFilter
                ? userTypeFilter.value
                : 'all';

        const status =
            statusFilter
                ? statusFilter.value
                : 'all';

        const selectedDate =
            dateFilter
                ? dateFilter.value
                : '';

        let visibleCount = 0;


        rows.forEach(row => {

            const name =
                (row.dataset.name || '').toLowerCase();

            const email =
                (row.dataset.email || '').toLowerCase();

            const phone =
                (row.dataset.phone || '').toLowerCase();

            const type =
                row.dataset.type || '';

            const rowStatus =
                row.dataset.status || '';

            const rowDate =
                row.dataset.date || '';


            const matchesSearch =
                search === '' ||
                name.includes(search) ||
                email.includes(search) ||
                phone.includes(search);


            const matchesType =
                userType === 'all' ||
                type === userType;


            const matchesStatus =
                status === 'all' ||
                rowStatus === status;


            const matchesDate =
                selectedDate === '' ||
                rowDate === selectedDate;


            if (
                matchesSearch &&
                matchesType &&
                matchesStatus &&
                matchesDate
            ) {

                row.classList.remove('hidden');

                visibleCount++;

            } else {

                row.classList.add('hidden');

            }

        });


        count.textContent =
            `Showing ${visibleCount} out of ${totalRegistrations} entries`;

    }


    /* =========================================================
       FILTER EVENTS
    ========================================================== */

    if (searchInput) {

        searchInput.addEventListener(
            'input',
            filterRegistrations
        );

    }


    if (userTypeFilter) {

        userTypeFilter.addEventListener(
            'change',
            filterRegistrations
        );

    }


    if (statusFilter) {

        statusFilter.addEventListener(
            'change',
            filterRegistrations
        );

    }


    if (dateFilter) {

        dateFilter.addEventListener(
            'change',
            filterRegistrations
        );

    }


    /* =========================================================
       DATE PICKER
    ========================================================== */

    const dateCalendarButton =
        document.getElementById('date-calendar-button');

    if (dateCalendarButton && dateFilter) {

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

                reloadButton.disabled = true;


                setTimeout(() => {

                    if (searchInput) {
                        searchInput.value = '';
                    }


                    if (userTypeFilter) {
                        userTypeFilter.value = 'all';
                    }


                    if (statusFilter) {
                        statusFilter.value = 'all';
                    }


                    if (dateFilter) {
                        dateFilter.value = '';
                    }


                    if (itemsPerPageSelect) {
                        itemsPerPageSelect.value = '10';
                    }


                    rows.forEach(row => {

                        row.classList.remove('hidden');

                    });


                    count.textContent =
                        'Showing 4 out of 378 entries';


                    if (reloadIcon) {

                        reloadIcon.classList.remove(
                            'animate-spin'
                        );

                    }

                    reloadButton.disabled = false;

                }, 700);

            }
        );

    }


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

    document.addEventListener(
        'keydown',
        function (event) {

            if (event.key !== 'Escape') {
                return;
            }


            if (
                approvedUsersModal &&
                !approvedUsersModal.classList.contains(
                    'hidden'
                )
            ) {

                closeApprovedModal();

            }


            if (
                rejectedUsersModal &&
                !rejectedUsersModal.classList.contains(
                    'hidden'
                )
            ) {

                closeRejectedModal();

            }

        }
    );


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
       INITIAL FILTER
    ========================================================== */

    filterRegistrations();

});

</script>

@endpush

@endsection