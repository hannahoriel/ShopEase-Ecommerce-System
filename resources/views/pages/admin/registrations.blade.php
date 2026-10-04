


@extends('layouts.admin')

@section('page-title', 'Account Registrations')

@section('content')

@vite('resources/css/admin/registrations.css')

<div
    id="admin-content"
    class="ml-60 pt-[110px] pl-5 pb-7 min-h-screen transition-all duration-300"
>
    <!-- =========================================================
         REGISTRATION STAT CARDS
    ========================================================== -->

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">

        <!-- =====================================================
             PENDING
        ====================================================== -->

        <div
            class="registration-stat-card
                   flex flex-col justify-between
                   transition-all duration-300
                   hover:-translate-y-1 hover:shadow-md"
        >

            <div class="flex items-center gap-2.5">

                <!-- Icon -->

                <div
                    class="shrink-0 flex items-center justify-center"
                >

                    <img
                        src="{{ asset('icons/admin/registrations/Pending Registrations.png') }}"
                        class="registration-stat-icon"
                        alt="Pending Registrations"
                    >

                </div>


                <!-- Content -->

                <div class="min-w-0">

                    <p id="pending-count-card" class="registration-stat-number">
                        {{ $counts['pending'] }}
                    </p>

                    <p class="registration-stat-label whitespace-nowrap">
                        Pending Requests
                    </p>

                </div>

            </div>


            <!-- Bottom Text -->

        </div>


        <!-- =====================================================
             REJECTED USERS ARCHIVE
        ====================================================== -->

        <div>

            <button
                type="button"
                id="rejected-users-button"
                class="registration-stat-card w-full
                       relative text-left
                       flex flex-col justify-center
                       transition-all duration-300
                       hover:-translate-y-1 hover:shadow-md
                       hover:border-[#E9A3A3]
                       group"
            >

                <div class="flex items-center justify-between">

                    <div class="flex items-center gap-2.5">

                        <!-- Icon -->

                        <div
                            class="shrink-0 flex items-center justify-center"
                        >

                            <img
                                src="{{ asset('icons/admin/registrations/Rejected Registrations.png') }}"
                                class="registration-stat-icon"
                                alt="Rejected Registrations"
                            >

                        </div>


                        <!-- Content -->

                        <div class="min-w-0">

                            <p id="rejected-count-card" class="registration-stat-number">
                                {{ $counts['rejected'] }}
                            </p>

                            <p class="registration-stat-label whitespace-nowrap">
                                Rejected Users
                            </p>

                        </div>

                    </div>


                    <!-- Arrow -->

                    <svg
                        class="w-[18px] h-[18px] shrink-0 text-gray-400
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

                <div class="mt-2 ml-[52px]">

                    <span class="registration-card-link">
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
                class="registration-stat-card w-full
                       relative text-left
                       flex flex-col justify-center
                       transition-all duration-300
                       hover:-translate-y-1 hover:shadow-md
                       hover:border-[#E9A3A3]
                       group"
            >

                <div class="flex items-center justify-between">

                    <div class="flex items-center gap-2.5">

                        <!-- Icon -->

                        <div
                            class="shrink-0 flex items-center justify-center"
                        >

                            <img
                                src="{{ asset('icons/admin/registrations/Approved Registrations.png') }}"
                                class="registration-stat-icon"
                                alt="Approved Registrations"
                            >

                        </div>


                        <!-- Content -->

                        <div class="min-w-0">

                            <p id="approved-count-card" class="registration-stat-number">
                                {{ $counts['approved'] }}
                            </p>

                            <p class="registration-stat-label whitespace-nowrap">
                                Approved Users
                            </p>

                        </div>

                    </div>


                    <!-- Arrow -->

                    <svg
                        class="w-[18px] h-[18px] shrink-0 text-gray-400
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

                <div class="mt-2 ml-[52px]">

                    <span class="registration-card-link">
                        View approved users →
                    </span>

                </div>

            </button>

        </div>


        <!-- =====================================================
             TOTAL REGISTRATIONS
        ====================================================== -->

        <div
            class="registration-stat-card relative
                   flex flex-col justify-start
                   transition-all duration-300
                   hover:-translate-y-1 hover:shadow-md"
        >

            <div class="flex items-center gap-2.5">

                <!-- Icon -->

                <div
                    class="shrink-0 flex items-center justify-center"
                >

                    <img
                        src="{{ asset('icons/admin/registrations/Total registrations.png') }}"
                        class="registration-stat-icon"
                        alt="Total Registrations"
                    >

                </div>


                <!-- Content -->

                <div class="min-w-0">

                    <p id="total-count-card" class="registration-stat-number">
                        {{ $counts['total'] }}
                    </p>

                    <p class="registration-stat-label whitespace-nowrap">
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
            class="registration-archive-modal bg-white w-full max-w-5xl max-h-[88vh]
                   overflow-y-auto rounded-2xl shadow-xl
                   transform transition-all duration-300"
        >

            <!-- Modal Header -->

            <div
                class="flex items-center justify-between
                       px-5 py-4 border-b border-gray-200"
            >

                <div>

                    <h3 class="text-[20px] font-bold text-gray-900">
                        Approved Users
                    </h3>

                    <p class="text-[13px] text-gray-400 mt-1">
                        Archived approved registrations
                    </p>

                </div>


                <button
                    type="button"
                    id="close-approved-users"
                    class="w-8 h-8 flex items-center justify-center
                           rounded-full text-gray-500
                           hover:bg-gray-100 hover:text-gray-800
                           transition"
                >

                    <svg
                        class="w-[18px] h-[18px]"
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
                    </tbody>

                </table>

            </div>


            <!-- Modal Footer -->

            <div
                class="px-6 py-4 border-t border-gray-200
                       flex items-center justify-between"
            >

                <p id="approved-users-count" class="text-[12px] text-gray-400">
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
            class="registration-archive-modal bg-white w-full max-w-5xl max-h-[88vh]
                   overflow-y-auto rounded-2xl shadow-xl
                   transform transition-all duration-300"
        >

            <!-- Modal Header -->

            <div
                class="flex items-center justify-between
                       px-5 py-4 border-b border-gray-200"
            >

                <div>

                    <h3 class="text-[20px] font-bold text-gray-900">
                        Rejected Users
                    </h3>

                    <p class="text-[13px] text-gray-400 mt-1">
                        Archived rejected registrations
                    </p>

                </div>


                <button
                    type="button"
                    id="close-rejected-users"
                    class="w-8 h-8 flex items-center justify-center
                           rounded-full text-gray-500
                           hover:bg-gray-100 hover:text-gray-800
                           transition"
                >

                    <svg
                        class="w-[18px] h-[18px]"
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
                    </tbody>

                </table>

            </div>


            <!-- Modal Footer -->

            <div
                class="px-6 py-4 border-t border-gray-200
                       flex items-center justify-between"
            >

                <p id="rejected-users-count" class="text-[12px] text-gray-400">
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
                   overflow-hidden rounded-[24px] shadow-2xl
                   border border-white"
            role="dialog"
            aria-modal="true"
            aria-labelledby="seller-details-title"
        >

            <div class="shrink-0 px-7 pt-6 pb-3">

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
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6L6 18" />
                        </svg>
                    </button>

                </div>

            </div>

            <div class="registration-detail-body flex-1 px-7 pb-5">

                <div class="flex items-center justify-center min-h-[220px] text-[13px] text-gray-400">
                    Loading registration details...
                </div>

            </div>

            <div class="shrink-0 px-7 py-5 border-t border-gray-200 flex items-center justify-end gap-4 bg-white">
                <button
                    type="button"
                    class="registration-reject px-9 py-2.5 rounded-lg border border-[#C92D32] bg-[#FFE6E6] text-[#A61B1B] text-[14px] font-semibold hover:bg-[#FFDADA] transition"
                >
                    Reject
                </button>
                <button
                    type="button"
                    class="registration-approve px-9 py-2.5 rounded-lg border border-[#4E9B46] bg-[#E7F4E3] text-[#28721B] text-[14px] font-semibold hover:bg-[#DCEFD7] transition"
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
                   overflow-hidden rounded-[24px] shadow-2xl
                   border border-white"
            role="dialog"
            aria-modal="true"
            aria-labelledby="buyer-details-title"
        >

            <div class="shrink-0 px-7 pt-6 pb-3">
                <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                    <h3 id="buyer-details-title" class="text-[23px] font-semibold text-gray-900">Buyer Details</h3>
                    <button
                        type="button"
                        class="registration-modal-close w-9 h-9 flex items-center justify-center rounded-full text-gray-500 hover:bg-gray-100 hover:text-gray-800 transition"
                        data-modal="buyer-details-modal"
                        aria-label="Close buyer details"
                    >
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6L6 18" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="registration-detail-body flex-1 px-7 pb-5">

                <div class="flex items-center justify-center min-h-[220px] text-[13px] text-gray-400">
                    Loading registration details...
                </div>

            </div>

            <div class="shrink-0 px-7 py-5 border-t border-gray-200 flex items-center justify-end gap-4 bg-white">
                <button type="button" class="registration-reject px-9 py-2.5 rounded-lg border border-[#C92D32] bg-[#FFE6E6] text-[#A61B1B] text-[14px] font-semibold hover:bg-[#FFDADA] transition">Reject</button>
                <button type="button" class="registration-approve px-9 py-2.5 rounded-lg border border-[#4E9B46] bg-[#E7F4E3] text-[#28721B] text-[14px] font-semibold hover:bg-[#DCEFD7] transition">Approve</button>
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
                    class="px-7 py-2.5 rounded-lg border border-[#C92D32] bg-[#FFE6E6] text-[#A61B1B]
                           text-[14px] font-semibold hover:bg-[#FFDADA] transition"
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
        class="registration-decision-flash"
        role="status"
        aria-live="polite"
        aria-atomic="true"
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
               p-3.5 mb-4"
    >

        <div class="flex flex-col xl:flex-row items-stretch xl:items-center gap-2.5">

            <!-- Search -->

            <div class="registration-search-wrap">

                <input
                    id="registration-search"
                    type="text"
                    placeholder="Search name, email, and phone"
                    class="w-full h-[36px] rounded-lg border border-gray-300
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
                    class="appearance-none w-full xl:w-[132px]
                           h-[36px] rounded-lg border border-gray-300
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

                    <option value="logistics">
                        Logistics
                    </option>

                    <option value="rider">
                        Rider
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
                    class="w-full xl:w-[195px]
                           h-[36px] rounded-lg border border-gray-300
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
                           w-9 h-[36px]
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
                    class="w-[18px] h-[18px]"
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

                        <th class="text-left px-4 py-3 text-[12px] font-medium text-gray-400">
                            Applicant
                        </th>

                        <th class="text-left px-4 py-3 text-[12px] font-medium text-gray-400">
                            User Type
                        </th>

                        <th class="text-left px-4 py-3 text-[12px] font-medium text-gray-400">
                            Email
                        </th>

                        <th class="text-left px-4 py-3 text-[12px] font-medium text-gray-400">
                            Phone
                        </th>

                        <th class="text-left px-4 py-3 text-[12px] font-medium text-gray-400">
                            Date Registered
                        </th>

                        <th class="text-left px-4 py-3 text-[12px] font-medium text-gray-400">
                            Status
                        </th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-gray-200">

                    @forelse($registrations as $registration)
                        <tr
                            class="registration-row cursor-pointer hover:bg-[#FFF9F7] transition"
                            data-id="{{ $registration->id }}"
                            data-name="{{ $registration->full_name }}"
                            data-email="{{ $registration->email }}"
                            data-phone="{{ $registration->phone }}"
                            data-type="{{ $registration->user_type }}"
                            data-status="{{ $registration->status }}"
                            data-date="{{ $registration->created_at->toDateString() }}"
                            role="button"
                            tabindex="0"
                        >
                            <td class="px-4 py-2.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-[#F6D8D2] flex items-center justify-center text-[10px] font-semibold text-[#7B1B1B] shrink-0">
                                        {{ collect(explode(' ', $registration->full_name))->filter()->map(fn ($part) => strtoupper(substr($part, 0, 1)))->take(2)->join('') }}
                                    </div>
                                    <span class="text-[12px] font-medium text-gray-800">{{ $registration->full_name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-2.5">
                                <div class="flex items-center gap-1.5">
                                    @if(strtolower($registration->user_type) === 'seller')
                                        <img src="{{ asset('icons/admin/dashboard/body/seller.png') }}" class="w-[18px] h-[18px] object-contain" alt="Seller">
                                    @elseif(strtolower($registration->user_type) === 'buyer')
                                        <img src="{{ asset('icons/admin/dashboard/body/buyer.png') }}" class="w-[18px] h-[18px] object-contain" alt="Buyer">
                                    @elseif(strtolower($registration->user_type) === 'logistics')
                                        <img src="{{ asset('icons/admin/dashboard/body/logistics.png') }}" class="w-[18px] h-[18px] object-contain" alt="Logistics">
                                    @elseif(strtolower($registration->user_type) === 'rider')
                                        <img src="{{ asset('icons/admin/dashboard/body/rider.png') }}" class="w-[18px] h-[18px] object-contain" alt="Rider">
                                    @endif
                                    <span class="text-[12px] text-gray-800">{{ ucfirst($registration->user_type) }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-2.5 text-[12px] text-gray-400">{{ $registration->email }}</td>
                            <td class="px-4 py-2.5 text-[12px] text-gray-800">{{ $registration->phone }}</td>
                            <td class="px-4 py-2.5 text-[12px] text-gray-800">{{ $registration->created_at->format('F j, Y g:i A') }}</td>
                            <td class="px-4 py-2.5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-[#FFE5D0] text-[#E87D22] text-[10px] font-medium border border-[#FFD1B8]">
                                    {{ ucfirst($registration->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-[13px] text-gray-400">
                                No registrations found.
                            </td>
                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>


        <!-- =====================================================
             TABLE FOOTER / PAGINATION
        ====================================================== -->

        <div
            class="flex flex-col md:flex-row items-center justify-between
                   gap-3 px-4 py-3 border-t border-gray-200"
        >

            <p
                id="registration-count"
                class="text-[12px] text-gray-400"
            >
                Showing 0–0 of 0 entries
            </p>


            <div class="flex items-center gap-1.5">

                <button
                    type="button"
                    id="registration-prev"
                    class="w-6 h-6 flex items-center justify-center
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
                    class="w-6 h-6 flex items-center justify-center
                           text-gray-700 hover:bg-gray-100
                           rounded transition disabled:opacity-30 disabled:cursor-not-allowed"
                    aria-label="Next page"
                >
                    ›
                </button>

                <select
                    id="items-per-page"
                    class="ml-2 h-7 rounded-md
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


<!-- =========================================================
     IMAGE PREVIEW MODAL
========================================================== -->

<div
    id="registration-image-preview-modal"
    class="fixed inset-0 z-[200] hidden items-center justify-center bg-black/75 backdrop-blur-[2px] px-5 py-6"
    aria-hidden="true"
>
    <div
        class="relative w-full max-w-6xl max-h-[92vh] rounded-2xl bg-white shadow-2xl overflow-hidden"
        role="dialog"
        aria-modal="true"
        aria-labelledby="registration-image-preview-title"
    >
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 bg-white">
            <h3 id="registration-image-preview-title" class="text-[18px] font-semibold text-gray-900">Image Preview</h3>

            <button
                type="button"
                id="registration-image-preview-close"
                class="w-8 h-8 flex items-center justify-center rounded-full text-gray-500 hover:bg-gray-100 hover:text-gray-800 transition"
                aria-label="Close image preview"
            >
                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6L6 18" />
                </svg>
            </button>
        </div>

        <div class="bg-[#F5F1EF] p-5 flex items-center justify-center max-h-[calc(92vh-74px)] overflow-auto">
            <img
                id="registration-image-preview"
                src=""
                alt="Image preview"
                class="max-w-full max-h-[calc(92vh-120px)] w-auto h-auto object-contain rounded-lg bg-white shadow-sm"
            >
        </div>
    </div>
</div>









@php
    $registrationsConfig = [
        'counts' => $counts,
        'icons' => [
            'seller' => asset('icons/admin/dashboard/body/seller.png'),
            'buyer' => asset('icons/admin/dashboard/body/buyer.png'),
            'rider' => asset('icons/admin/dashboard/body/rider.png'),
            'logistics' => asset('icons/admin/dashboard/body/logistics.png'),
        ],
    ];
@endphp

<script
    type="application/json"
    id="registrationsConfig"
>{!! json_encode($registrationsConfig, JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>

@push('scripts')

@vite('resources/js/admin/registrations.js')

@endpush

@endsection
