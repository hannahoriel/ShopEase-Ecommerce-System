@extends('layouts.admin')

@section('page-title', 'Logistics Management')

@section('content')

@php
    /*
    |--------------------------------------------------------------------------
    | DEMO / FALLBACK DATA
    |--------------------------------------------------------------------------
    | Replace this later with controller data.
    */
    $logisticsCompanies = $logisticsCompanies ?? [
        [
            'company' => 'J&T Express',
            'branches' => 24,
            'riders' => 154,

            'contact_number' => '123456789',
            'company_email' => 'j&texpress@gmail.com',
            'office_address' => 'kung saan ka masaya',
            'description' => 'J&T Express provides parcel pickup, sorting, transportation, and last-mile delivery services for businesses and customers.',
            'dti_permit' => '',
            'business_permit' => '',
            'partnership_since' => 2019,
            'account_created_date' => 'May 26, 2026',
            'account_created_time' => '6:45 PM',
            'logo' => '',

            'branches_data' => [
                [
                    'name' => 'Main Hub',
                    'address' => 'Purok 2, Masico, Pila, Laguna. Building 206',
                    'contact_person' => 'Maria Santos',
                    'phone' => '0945-789-1028',
                    'riders' => 25,
                ],
                [
                    'name' => 'Sorting Hub',
                    'address' => 'Purok 2, Masico, Pila, Laguna. Building 206',
                    'contact_person' => 'Maria Santos',
                    'phone' => '0945-789-1028',
                    'riders' => 25,
                ],
                [
                    'name' => 'Delivery Hub',
                    'address' => 'Purok 2, Masico, Pila, Laguna. Building 206',
                    'contact_person' => 'Maria Santos',
                    'phone' => '0945-789-1028',
                    'riders' => 25,
                ],
                [
                    'name' => 'Laguna Center',
                    'address' => 'Purok 2, Masico, Pila, Laguna. Building 206',
                    'contact_person' => 'Maria Santos',
                    'phone' => '0945-789-1028',
                    'riders' => 25,
                ],
            ],
        ],
    ];

    $pendingLogisticsRequests = $pendingLogisticsRequests ?? [
        [
            'company' => 'Flash Express',
            'email' => 'flash.express@gmail.com',
            'phone' => '0917-245-8812',
            'contact_person' => 'Andrea Cruz',
            'registered_date' => '2026-05-31',
            'office_address' => 'Bldg. 2, Southwoods Business Park, Biñan, Laguna',
            'description' => 'Flash Express provides nationwide parcel pickup, sorting, and last-mile delivery services for online sellers and customers.',
            'registered_display' => 'May 31, 2026 10:30 AM',
            'logo' => '',
            'dti_permit' => '',
            'business_permit' => '',
        ],
        [
            'company' => 'Ninja Van',
            'email' => 'ninjavan.ph@gmail.com',
            'phone' => '0918-662-1405',
            'contact_person' => 'Paolo Reyes',
            'registered_date' => '2026-05-30',
            'office_address' => 'Sta. Rosa Business District, Sta. Rosa, Laguna',
            'description' => 'Ninja Van offers technology-enabled parcel delivery, fulfillment, and logistics support for e-commerce businesses.',
            'registered_display' => 'May 30, 2026 3:45 PM',
            'logo' => '',
            'dti_permit' => '',
            'business_permit' => '',
        ],
        [
            'company' => 'GoGo Xpress',
            'email' => 'gogoxpress@gmail.com',
            'phone' => '0920-841-7701',
            'contact_person' => 'Camille Santos',
            'registered_date' => '2026-05-29',
            'office_address' => 'National Highway, Calamba, Laguna',
            'description' => 'GoGo Xpress provides parcel delivery, cash-on-delivery support, and logistics services for small businesses and online merchants.',
            'registered_display' => 'May 29, 2026 1:15 PM',
            'logo' => '',
            'dti_permit' => '',
            'business_permit' => '',
        ],
        [
            'company' => 'LBC Express',
            'email' => 'lbc.logistics@gmail.com',
            'phone' => '0919-321-5558',
            'contact_person' => 'Daniel Garcia',
            'registered_date' => '2026-05-28',
            'office_address' => 'Poblacion, Sta. Cruz, Laguna',
            'description' => 'LBC Express provides domestic courier, cargo, remittance, and logistics services through a nationwide branch network.',
            'registered_display' => 'May 28, 2026 11:05 AM',
            'logo' => '',
            'dti_permit' => '',
            'business_permit' => '',
        ],
        [
            'company' => '2GO Logistics',
            'email' => '2go.logistics@gmail.com',
            'phone' => '0921-776-2400',
            'contact_person' => 'Mika Flores',
            'registered_date' => '2026-05-27',
            'office_address' => 'Port Area Logistics Center, Manila',
            'description' => '2GO Logistics provides freight forwarding, distribution, warehousing, and transport solutions for businesses.',
            'registered_display' => 'May 27, 2026 9:40 AM',
            'logo' => '',
            'dti_permit' => '',
            'business_permit' => '',
        ],
        [
            'company' => 'Entrego',
            'email' => 'entrego.ph@gmail.com',
            'phone' => '0916-908-4473',
            'contact_person' => 'Joshua Lim',
            'registered_date' => '2026-05-26',
            'office_address' => 'Cabuyao Logistics Hub, Cabuyao, Laguna',
            'description' => 'Entrego provides parcel management, fulfillment, and last-mile delivery solutions for businesses and e-commerce platforms.',
            'registered_display' => 'May 26, 2026 5:20 PM',
            'logo' => '',
            'dti_permit' => '',
            'business_permit' => '',
        ],
        [
            'company' => 'XDE Logistics',
            'email' => 'xde.logistics@gmail.com',
            'phone' => '0922-667-9094',
            'contact_person' => 'Sofia Mendoza',
            'registered_date' => '2026-05-25',
            'office_address' => 'Industrial Park, Calamba, Laguna',
            'description' => 'XDE Logistics provides cargo handling, trucking, distribution, and end-to-end logistics support for commercial clients.',
            'registered_display' => 'May 25, 2026 2:10 PM',
            'logo' => '',
            'dti_permit' => '',
            'business_permit' => '',
        ],
    ];

    $rejectedLogisticsArchive = $rejectedLogisticsArchive ?? [
        [
            'company' => 'PrimeTrack Logistics',
            'email' => 'primetrack.logistics@gmail.com',
            'phone' => '0917-801-2431',
            'contact_person' => 'Carlo Mendoza',
            'rejected_display' => 'May 24, 2026',
            'rejection_reason' => 'Incomplete or missing business permits.',
            'rejection_details' => '',
        ],
        [
            'company' => 'CargoLink Express',
            'email' => 'cargolink.express@gmail.com',
            'phone' => '0918-445-9072',
            'contact_person' => 'Mia Torres',
            'rejected_display' => 'May 21, 2026',
            'rejection_reason' => 'Company information is inconsistent or cannot be verified.',
            'rejection_details' => '',
        ],
        [
            'company' => 'RapidMove Logistics',
            'email' => 'rapidmove.logistics@gmail.com',
            'phone' => '0920-338-6147',
            'contact_person' => 'Jerome Castillo',
            'rejected_display' => 'May 18, 2026',
            'rejection_reason' => 'Does not meet ShopEase logistics partnership requirements.',
            'rejection_details' => '',
        ],
    ];

    $logisticsStats = $logisticsStats ?? [
        'pending_requests' => count($pendingLogisticsRequests),
        'accepted_logistics' => 12,
        'rejected_logistics' => count($rejectedLogisticsArchive),
        'total_companies' => 12,
    ];

    $pendingLogisticsTotalEntries = $pendingLogisticsTotalEntries ?? count($pendingLogisticsRequests);
    $logisticsTotalEntries = $logisticsTotalEntries ?? 378;
@endphp

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');

    html,
    body,
    body *,
    button,
    input,
    textarea,
    select,
    option {
        font-family: 'Poppins', sans-serif !important;
    }

    #admin-content {
        color: #17120F;
    }

    /* =========================================================
       STAT CARD
       Matches the sizing and typography used by the other pages.
    ========================================================== */
    /* =========================================================
       LOGISTICS STAT CARDS
       Exact sizing/spacing/typography behavior of Registrations.
    ========================================================== */
    .logistics-stat-card {
        min-height: 108px;
        background: #FFFFFF;
        border: 1px solid #F0E9E6;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(42, 20, 15, 0.05);
        padding: 16px;
        box-sizing: border-box;
    }

    .logistics-stat-icon {
        width: 40px;
        height: 40px;
        flex: 0 0 40px;
        object-fit: contain;
        display: block;
    }

    .logistics-stat-number {
        margin: 0;
        font-size: 23px;
        line-height: 1;
        font-weight: 600;
        color: #17120F;
    }

    .logistics-stat-label {
        margin: 5px 0 0;
        font-size: 13px;
        line-height: 1.15;
        font-weight: 400;
        color: #8C8784;
    }

    .logistics-card-link {
        font-size: 12px;
        line-height: 1.2;
        font-weight: 500;
        color: #7B1B1B;
    }

    /* Same archive-modal scrolling behavior used by Registrations. */
    .logistics-archive-modal {
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .logistics-archive-modal::-webkit-scrollbar {
        display: none;
        width: 0;
        height: 0;
    }


    /* =========================================================
       MAIN CONTENT TABS
    ========================================================== */
    .logistics-main-tabs-card {
        width: 100%;
        margin-bottom: 16px;
        background: #FFFFFF;
        border: 1px solid #F0E9E6;
        border-radius: 14px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, .04);
        overflow: hidden;
    }

    .logistics-main-tabs {
        min-height: 56px;
        padding: 0 20px;
        display: flex;
        align-items: flex-end;
        gap: 32px;
        border-bottom: 1px solid #E5E7EB;
    }

    .logistics-main-tab {
        position: relative;
        height: 56px;
        padding: 0 2px;
        border: 0;
        background: transparent;
        color: #8C8784;
        font-size: 13px;
        line-height: 1;
        font-weight: 400;
        cursor: pointer;
        transition: color .15s ease;
    }

    .logistics-main-tab:hover {
        color: #5E5753;
    }

    .logistics-main-tab.active {
        color: #17120F;
        font-weight: 500;
    }

    .logistics-main-tab.active::after {
        content: '';
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        height: 2px;
        border-radius: 999px 999px 0 0;
        background: #B3262E;
    }

    .logistics-main-panel {
        display: none;
    }

    .logistics-main-panel.active {
        display: block;
    }

    /* =========================================================
       PENDING REQUEST FILTERS
       Registrations-style compact controls.
    ========================================================== */
    .pending-logistics-filter-card {
        min-height: 68px;
        margin-bottom: 16px;
        padding: 14px;
        display: flex;
        align-items: center;
        gap: 12px;
        background: #FFFFFF;
        border: 1px solid #F0E9E6;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, .04);
        box-sizing: border-box;
    }

    .pending-logistics-search-wrap {
        position: relative;
        width: 420px;
        flex: 0 0 420px;
    }

    .pending-logistics-search {
        width: 100%;
        height: 36px;
        padding: 0 40px 0 12px;
        border: 1px solid #D9D6D4;
        border-radius: 8px;
        background: #FFFFFF;
        outline: none;
        color: #76716E;
        font-size: 13px;
        font-weight: 400;
        box-sizing: border-box;
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    .pending-logistics-search::placeholder {
        color: #B9B5B3;
        opacity: 1;
    }

    .pending-logistics-search:focus {
        border-color: #9CA3AF;
        box-shadow: 0 0 0 2px rgba(156, 163, 175, .14);
    }

    .pending-logistics-search-icon {
        position: absolute;
        right: 12px;
        top: 50%;
        width: 18px;
        height: 18px;
        transform: translateY(-50%);
        color: #8C8784;
        pointer-events: none;
    }

    .pending-date-wrap {
        position: relative;
        width: 244px;
        flex: 0 0 244px;
    }

    .pending-date-input {
        width: 100%;
        height: 38px;
        padding: 0 12px;
        border: 1px solid #D9D6D4;
        border-radius: 8px;
        background: #FFFFFF;
        outline: none;
        color: #76716E;
        font-size: 13px;
        font-weight: 400;
        box-sizing: border-box;
        cursor: pointer;
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    .pending-date-input:focus {
        border-color: #9CA3AF;
        box-shadow: 0 0 0 2px rgba(156, 163, 175, .14);
    }

    .pending-reload {
        width: 40px;
        height: 38px;
        flex: 0 0 40px;
        border: 0;
        border-radius: 8px;
        background: transparent;
        color: #17120F;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background .15s ease, transform .15s ease;
    }

    .pending-reload:hover {
        background: #FFF0EC;
    }

    .pending-reload:active {
        transform: scale(.95);
    }

    .pending-reload svg {
        width: 22px;
        height: 22px;
    }

    .pending-reload.spinning svg {
        animation: logistics-spin .55s linear;
    }

    @keyframes logistics-spin {
        to { transform: rotate(360deg); }
    }

    /* =========================================================
       PENDING REQUEST TABLE
       Table text follows Registrations: 13px headers / 12px body.
    ========================================================== */
    .pending-logistics-table-card {
        width: 100%;
        display: flex;
        flex-direction: column;
        background: #FFFFFF;
        border: 1px solid #F0E9E6;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, .04);
        overflow: hidden;
    }

    .pending-logistics-table-scroll {
        overflow-x: auto;
        flex: 0 0 auto;
    }

    .pending-logistics-table {
        width: 100%;
        min-width: 900px;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .pending-logistics-table thead tr {
        border-bottom: 1px solid #E5E7EB;
    }

    .pending-logistics-table th {
        padding: 14px 16px;
        text-align: left;
        color: #9CA3AF;
        font-size: 13px;
        line-height: 1.2;
        font-weight: 500;
        white-space: nowrap;
    }

    .pending-logistics-table th:nth-child(1) { width: 24%; }
    .pending-logistics-table th:nth-child(2) { width: 23%; }
    .pending-logistics-table th:nth-child(3) { width: 17%; }
    .pending-logistics-table th:nth-child(4) { width: 18%; }
    .pending-logistics-table th:nth-child(5) { width: 18%; }

    .pending-logistics-table tbody tr {
        border-bottom: 1px solid #E5E7EB;
        transition: background .15s ease;
    }

    .pending-logistics-table tbody tr:hover {
        background: #FFF9F7;
    }

    .pending-logistics-row {
        cursor: pointer;
    }

    .pending-logistics-row:focus-visible {
        outline: 2px solid rgba(123, 27, 27, .18);
        outline-offset: -2px;
    }

    .pending-logistics-table td {
        padding: 10px 16px;
        vertical-align: middle;
        color: #17120F;
        font-size: 12px;
        line-height: 1.3;
        font-weight: 400;
    }

    .pending-company-cell {
        min-width: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .pending-company-avatar {
        width: 30px;
        height: 30px;
        flex: 0 0 30px;
        border-radius: 50%;
        overflow: hidden;
        background: #D9D9D9;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .pending-company-avatar img {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
    }

    .pending-company-name {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        color: #17120F;
        font-size: 12px;
        font-weight: 600;
    }

    .pending-email {
        color: #9CA3AF;
        font-size: 12px;
    }

    .pending-table-empty {
        display: none;
        padding: 42px 16px;
        text-align: center;
        color: #9CA3AF;
        font-size: 12px;
    }

    .pending-table-footer {
        min-height: 54px;
        padding: 10px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        border-top: 1px solid #E5E7EB;
    }

    .pending-count {
        margin: 0;
        color: #9CA3AF;
        font-size: 12px;
        font-weight: 400;
    }

    .pending-pagination {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .pending-page-button {
        width: 28px;
        height: 28px;
        border: 0;
        border-radius: 6px;
        background: transparent;
        color: #374151;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 400;
        cursor: pointer;
        transition: background .15s ease, color .15s ease;
    }

    .pending-page-button:hover:not(:disabled) {
        background: #F3F4F6;
    }

    .pending-page-button.active {
        background: #FFD1C2;
        color: #7B1B1B;
        font-weight: 600;
    }

    .pending-page-button:disabled {
        opacity: .35;
        cursor: default;
    }

    .pending-items {
        height: 28px;
        margin-left: 4px;
        border: 1px solid #F0B9AC;
        border-radius: 6px;
        background: #FFF5F1;
        padding: 0 28px 0 10px;
        outline: none;
        color: #374151;
        font-size: 12px;
        font-weight: 400;
        cursor: pointer;
    }

    /* =========================================================
       ACTION BAR
    ========================================================== */
    .logistics-action-card {
        min-height: 68px;
        margin-bottom: 18px;
        padding: 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        background: #FFFFFF;
        border: 1px solid #F0E9E6;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, .04);
    }

    .logistics-search-wrap {
        position: relative;
        width: 420px;
        flex: 0 0 420px;
    }

    .logistics-search {
        width: 100%;
        height: 36px;
        border: 1px solid #D9D6D4;
        border-radius: 8px;
        background: #FFFFFF;
        padding: 0 42px 0 13px;
        outline: none;
        color: #76716E;
        font-size: 13px;
        font-weight: 400;
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    .logistics-search::placeholder {
        color: #B9B5B3;
        opacity: 1;
    }

    .logistics-search:focus {
        border-color: #7B1B1B;
        box-shadow: 0 0 0 2px rgba(123, 27, 27, .10);
    }

    .logistics-search-icon {
        position: absolute;
        right: 12px;
        top: 50%;
        width: 18px;
        height: 18px;
        transform: translateY(-50%);
        color: #C1BDBB;
        pointer-events: none;
    }


    /* =========================================================
       TABLE
    ========================================================== */
    .logistics-table-card {
        width: 100%;
        display: flex;
        flex-direction: column;
        background: #FFFFFF;
        border: 1px solid #F0E9E6;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, .04);
        overflow: hidden;
    }

    .logistics-table-scroll {
        overflow-x: auto;
        flex: 0 0 auto;
    }

    .logistics-table {
        width: 100%;
        min-width: 820px;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .logistics-table thead tr {
        border-bottom: 1px solid #E5E7EB;
    }

    .logistics-table th {
        padding: 12px 16px;
        text-align: left;
        color: #8C8784;
        font-size: 12px;
        line-height: 1.2;
        font-weight: 500;
        white-space: nowrap;
    }

    .logistics-table th:nth-child(1) { width: 46%; }
    .logistics-table th:nth-child(2) { width: 27%; }
    .logistics-table th:nth-child(3) { width: 27%; }

    .logistics-table tbody tr {
        border-bottom: 1px solid #E5E7EB;
        transition: background .15s ease;
    }

    .logistics-table tbody tr:hover {
        background: #FFF9F7;
    }

    .logistics-table td {
        padding: 12px 16px;
        vertical-align: middle;
        color: #17120F;
        font-size: 12px;
        line-height: 1.25;
        font-weight: 400;
    }

    .logistics-company-cell {
        min-width: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .logistics-company-avatar {
        width: 30px;
        height: 30px;
        flex: 0 0 30px;
        border-radius: 50%;
        background: #D9D9D9;
        overflow: hidden;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .logistics-company-avatar img {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
        border-radius: inherit;
    }

    .logistics-company-name {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        color: #17120F;
        font-size: 12px;
        font-weight: 600;
    }

    .logistics-company-row {
        cursor: pointer;
    }

    .logistics-company-row:focus-visible {
        outline: 2px solid rgba(123, 27, 27, .22);
        outline-offset: -2px;
    }


    .logistics-number {
        color: #17120F;
        font-size: 12px;
        font-weight: 500;
    }

    .logistics-empty {
        display: none;
        padding: 42px 16px;
        text-align: center;
        color: #8C8784;
        font-size: 12px;
    }

    /* =========================================================
       TABLE FOOTER / PAGINATION
    ========================================================== */
    .logistics-table-footer {
        min-height: 58px;
        padding: 10px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        border-top: 1px solid #E5E7EB;
    }

    .logistics-count {
        margin: 0;
        color: #8C8784;
        font-size: 12px;
        font-weight: 400;
    }

    .logistics-pagination {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .logistics-page-button {
        width: 28px;
        height: 28px;
        border: 0;
        border-radius: 6px;
        background: transparent;
        color: #374151;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 400;
        cursor: pointer;
        transition: background .15s ease, color .15s ease;
    }

    .logistics-page-button:hover:not(:disabled) {
        background: #F3F4F6;
    }

    .logistics-page-button.active {
        background: #FFD1C2;
        color: #7B1B1B;
        font-weight: 600;
    }

    .logistics-page-button:disabled {
        opacity: .35;
        cursor: default;
    }

    .logistics-page-button svg {
        width: 14px;
        height: 14px;
    }

    .logistics-items {
        height: 28px;
        margin-left: 4px;
        border: 1px solid #F0B9AC;
        border-radius: 6px;
        background: #FFF5F1;
        padding: 0 28px 0 10px;
        outline: none;
        color: #374151;
        font-size: 12px;
        font-weight: 400;
        cursor: pointer;
    }


    /* =========================================================
       AUTO-HEIGHT TABLE CARDS
       Table ends immediately after the current rows + pagination.
    ========================================================== */
    .pending-logistics-table-card,
    .logistics-table-card {
        min-height: 0;
        height: auto;
    }

    /* =========================================================
       PENDING LOGISTICS REVIEW MODAL
       Read-only review layout based on the supplied reference.
    ========================================================== */
    .pending-review-backdrop {
        position: fixed;
        inset: 0;
        z-index: 195;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 18px;
        background: rgba(0, 0, 0, .36);
        backdrop-filter: blur(2px);
    }

    .pending-review-backdrop.is-open {
        display: flex;
    }

    .pending-review-dialog {
        width: min(920px, calc(100vw - 36px));
        max-height: calc(100vh - 36px);
        overflow-y: auto;
        background: #FFFFFF;
        border-radius: 24px;
        box-shadow: 0 24px 70px rgba(42, 20, 15, .20);
        padding: 26px 28px 28px;
        box-sizing: border-box;
        scrollbar-width: none;
    }

    .pending-review-dialog::-webkit-scrollbar {
        display: none;
    }

    .pending-review-title {
        margin: 0 0 18px 8px;
        color: #17120F;
        font-size: 19px;
        line-height: 1.2;
        font-weight: 500;
    }

    .pending-review-columns {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }

    .pending-review-panel {
        min-width: 0;
        min-height: 470px;
        border: 1px solid #D9D6D4;
        border-radius: 13px;
        background: #FFFFFF;
        padding: 18px 16px;
        box-sizing: border-box;
    }

    .pending-review-panel-title {
        margin: 0 0 20px 1px;
        display: flex;
        align-items: center;
        gap: 9px;
        color: #9E231F;
        font-size: 14px;
        line-height: 1.2;
        font-weight: 600;
    }

    .pending-review-panel-title img {
        width: 20px;
        height: 20px;
        flex: 0 0 20px;
        object-fit: contain;
    }

    .pending-review-info-list {
        display: grid;
        gap: 14px;
    }

    .pending-review-info-item {
        min-width: 0;
        display: grid;
        grid-template-columns: 132px minmax(0, 1fr);
        gap: 16px;
        align-items: start;
    }

    .pending-review-info-label {
        display: block;
        margin: 0;
        color: #9B9693;
        font-size: 11px;
        line-height: 1.45;
        font-weight: 400;
    }

    .pending-review-info-value {
        min-width: 0;
        width: auto;
        min-height: 0;
        padding: 0;
        border: 0;
        border-radius: 0;
        background: transparent;
        color: #17120F;
        font-size: 12px;
        line-height: 1.45;
        font-weight: 500;
        overflow-wrap: anywhere;
        box-sizing: border-box;
        display: block;
    }

    .pending-review-info-value.address {
        min-height: 0;
        display: block;
    }

    .pending-review-additional-title {
        margin: 18px 0 10px 2px;
        padding-top: 15px;
        border-top: 1px solid #F0E9E6;
        color: #9E231F;
        font-size: 13px;
        line-height: 1.2;
        font-weight: 600;
    }

    .pending-review-logo-box {
        width: 100%;
        height: 74px;
        border: 1px dashed #D7D2CF;
        border-radius: 10px;
        background: #FCFCFC;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        box-sizing: border-box;
    }

    .pending-review-logo-box img {
        width: 100%;
        height: 100%;
        display: none;
        object-fit: contain;
        padding: 6px;
        box-sizing: border-box;
    }

    .pending-review-logo-box.has-image img {
        display: block;
    }

    .pending-review-logo-placeholder {
        display: flex;
        align-items: center;
        gap: 9px;
        color: #AAA6A4;
        font-size: 11px;
        font-weight: 400;
    }

    .pending-review-logo-box.has-image .pending-review-logo-placeholder {
        display: none;
    }

    .pending-review-logo-placeholder svg {
        width: 22px;
        height: 22px;
        flex: 0 0 22px;
    }

    .pending-review-permits-title {
        margin: 18px 0 10px 2px;
        padding-top: 15px;
        border-top: 1px solid #F0E9E6;
        color: #9E231F;
        font-size: 13px;
        line-height: 1.2;
        font-weight: 600;
    }

    .pending-review-permits-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .pending-review-permit-item {
        min-width: 0;
    }

    .pending-review-permit-label {
        display: block;
        margin: 0 0 7px;
        color: #17120F;
        font-size: 11px;
        line-height: 1.2;
        font-weight: 500;
    }

    .pending-review-permit-button {
        position: relative;
        width: 100%;
        height: 138px;
        padding: 0;
        border: 1px solid #E4DFDC;
        border-radius: 10px;
        background: #FAFAFA;
        overflow: hidden;
        cursor: zoom-in;
        box-sizing: border-box;
        transition:
            border-color .15s ease,
            box-shadow .15s ease,
            transform .15s ease;
    }

    .pending-review-permit-button:hover {
        border-color: #C9C1BD;
        box-shadow: 0 3px 10px rgba(42, 20, 15, .08);
        transform: translateY(-1px);
    }

    .pending-review-permit-button img {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
        background: #F7F7F7;
    }

    .pending-review-permit-zoom-hint {
        position: absolute;
        right: 8px;
        bottom: 8px;
        padding: 5px 8px;
        border-radius: 999px;
        background: rgba(255, 255, 255, .92);
        color: #6B625E;
        font-size: 9px;
        line-height: 1;
        font-weight: 500;
        box-shadow: 0 1px 5px rgba(0, 0, 0, .08);
        pointer-events: none;
    }

    /* Permit image viewer */
    .permit-zoom-backdrop {
        position: fixed;
        inset: 0;
        z-index: 240;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 24px;
        background: rgba(0, 0, 0, .72);
        backdrop-filter: blur(3px);
    }

    .permit-zoom-backdrop.is-open {
        display: flex;
    }

    .permit-zoom-dialog {
        width: min(900px, calc(100vw - 48px));
        max-height: calc(100vh - 48px);
        padding: 14px;
        border-radius: 16px;
        background: #FFFFFF;
        box-shadow: 0 24px 70px rgba(0, 0, 0, .28);
        box-sizing: border-box;
    }

    .permit-zoom-header {
        min-height: 38px;
        padding: 0 4px 10px 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .permit-zoom-title {
        margin: 0;
        color: #17120F;
        font-size: 13px;
        line-height: 1.2;
        font-weight: 600;
    }

    .permit-zoom-close {
        width: 32px;
        height: 32px;
        flex: 0 0 32px;
        border: 0;
        border-radius: 8px;
        background: transparent;
        color: #5E5753;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        line-height: 1;
        cursor: pointer;
    }

    .permit-zoom-close:hover {
        background: #F4F1F0;
    }

    .permit-zoom-image-wrap {
        max-height: calc(100vh - 130px);
        overflow: auto;
        border-radius: 10px;
        background: #F4F4F4;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .permit-zoom-image {
        max-width: 100%;
        max-height: calc(100vh - 130px);
        display: block;
        object-fit: contain;
    }

    .pending-review-description-note {
        margin: -4px 0 16px;
        min-height: 58px;
        border-radius: 8px;
        background: #EDF5FF;
        padding: 10px 12px;
        display: flex;
        align-items: flex-start;
        gap: 9px;
        box-sizing: border-box;
    }

    .pending-review-description-note svg {
        width: 18px;
        height: 18px;
        flex: 0 0 18px;
        margin-top: 1px;
        color: #145CA8;
    }

    .pending-review-description-note p {
        margin: 0;
        color: #45403E;
        font-size: 10px;
        line-height: 1.4;
        font-weight: 400;
    }

    .pending-review-description-box {
        min-height: 260px;
        border: 1px solid #E0DDDB;
        border-radius: 10px;
        background: #FAFAFA;
        padding: 14px;
        color: #57514E;
        font-size: 12px;
        line-height: 1.6;
        font-weight: 400;
        white-space: pre-wrap;
        overflow-wrap: anywhere;
        box-sizing: border-box;
    }

    .pending-review-meta {
        margin-top: 18px;
        padding-top: 14px;
        border-top: 1px solid #F0E9E6;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .pending-review-meta-item span {
        display: block;
    }

    .pending-review-meta-label {
        margin-bottom: 4px;
        color: #9B9693;
        font-size: 10px;
        font-weight: 400;
    }

    .pending-review-meta-value {
        color: #17120F;
        font-size: 11px;
        font-weight: 500;
    }

    .pending-review-status-item {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: flex-start;
    }

    .pending-review-status-item .pending-review-meta-label {
        width: auto;
        margin-bottom: 8px;
        text-align: left;
    }

    /* More specific than `.pending-review-meta-item span { display:block; }`
       so the text stays perfectly centered inside the pill. */
    .pending-review-meta-item .pending-review-status-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: auto;
        min-width: 88px;
        height: 30px;
        min-height: 30px;
        padding: 0 14px;
        margin: 0;
        border: 0;
        border-radius: 999px;
        background: #FFE5D0;
        color: #E87D22;
        font-size: 12px;
        line-height: 30px;
        font-weight: 500;
        white-space: nowrap;
        box-sizing: border-box;
        vertical-align: middle;
    }

    .pending-review-actions {
        margin-top: 22px;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
    }

    .pending-review-close,
    .pending-review-reject,
    .pending-review-approve {
        min-width: 116px;
        height: 40px;
        padding: 0 22px;
        border-radius: 8px;
        font-size: 12px;
        line-height: 1;
        font-weight: 600;
        cursor: pointer;
        box-sizing: border-box;
        transition:
            background .15s ease,
            border-color .15s ease,
            transform .15s ease,
            opacity .15s ease;
    }

    .pending-review-close {
        border: 1px solid #17120F;
        background: #FFFFFF;
        color: #17120F;
    }

    .pending-review-close:hover {
        background: #F7F7F7;
    }

    /* Same red / green treatment used by Account Registrations. */
    .pending-review-reject {
        border: 1px solid #C92D32;
        background: #FFE6E6;
        color: #A61B1B;
    }

    .pending-review-reject:hover {
        background: #FFDADA;
    }

    .pending-review-approve {
        border: 1px solid #4E9B46;
        background: #E7F4E3;
        color: #28721B;
    }

    .pending-review-approve:hover {
        background: #DCEFD7;
    }

    .pending-review-close:active,
    .pending-review-reject:active,
    .pending-review-approve:active {
        transform: scale(.98);
    }

    .pending-review-reject:disabled,
    .pending-review-approve:disabled {
        opacity: .55;
        cursor: not-allowed;
    }

    /* =========================================================
       LOGISTICS ACTION FLASH
       Same lower-right decision flash style as Registrations.
    ========================================================== */
    .logistics-decision-flash {
        position: fixed;
        right: 24px;
        bottom: 24px;
        z-index: 260;
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
        transform: translateY(10px);
        transition:
            opacity .08s ease,
            transform .08s ease,
            visibility .08s ease;
    }

    .logistics-decision-flash.show {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        transform: translateY(0);
    }

    .logistics-decision-flash.approved {
        border-color: #BFDDB8;
    }

    .logistics-decision-flash.rejected,
    .logistics-decision-flash.error {
        border-color: #E7A8AB;
    }

    .logistics-flash-content {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        width: 100%;
    }

    .logistics-flash-icon {
        width: 36px;
        height: 36px;
        flex: 0 0 36px;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .logistics-flash-icon svg {
        width: 20px;
        height: 20px;
    }

    .logistics-flash-copy {
        min-width: 0;
        flex: 1 1 auto;
    }

    .logistics-flash-title {
        margin: 0;
        color: #17120F;
        font-size: 14px;
        line-height: 1.3;
        font-weight: 600;
    }

    .logistics-flash-message {
        margin: 4px 0 0;
        color: #8C8784;
        font-size: 12px;
        line-height: 1.55;
        font-weight: 400;
    }

    .logistics-flash-close {
        width: 28px;
        height: 28px;
        flex: 0 0 28px;
        margin-left: auto;
        border: 0;
        border-radius: 999px;
        background: transparent;
        color: #9CA3AF;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background .15s ease, color .15s ease;
    }

    .logistics-flash-close:hover {
        background: #F3F4F6;
        color: #57514E;
    }

    .logistics-flash-close svg {
        width: 16px;
        height: 16px;
    }

    @media (max-width: 640px) {
        .logistics-decision-flash {
            right: 16px;
            bottom: 16px;
        }
    }

    /* =========================================================
       REJECT LOGISTICS REQUEST MODAL
       Mirrors the selection-based rejection flow in Registrations.
    ========================================================== */
    .logistics-reject-backdrop {
        position: fixed;
        inset: 0;
        z-index: 230;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 24px;
        background: rgba(0, 0, 0, .35);
        backdrop-filter: blur(2px);
    }

    .logistics-reject-backdrop.is-open {
        display: flex;
    }

    .logistics-reject-dialog {
        width: min(520px, calc(100vw - 40px));
        max-height: calc(100vh - 48px);
        overflow-y: auto;
        border: 1px solid #FFFFFF;
        border-radius: 28px;
        background: #FFFFFF;
        box-shadow: 0 24px 70px rgba(42, 20, 15, .24);
        box-sizing: border-box;
        scrollbar-width: none;
    }

    .logistics-reject-dialog::-webkit-scrollbar {
        display: none;
    }

    .logistics-reject-body {
        padding: 22px 24px 18px;
    }

    .logistics-reject-title {
        margin: 0;
        color: #17120F;
        font-size: 19px;
        line-height: 1.2;
        font-weight: 500;
    }

    .logistics-reject-subtitle {
        margin: 6px 0 0;
        color: #9CA3AF;
        font-size: 12px;
        line-height: 1.5;
        font-weight: 400;
    }

    .logistics-reject-reason-section {
        margin-top: 20px;
    }

    .logistics-reject-label {
        display: block;
        margin: 0 0 7px;
        color: #17120F;
        font-size: 12px;
        line-height: 1.2;
        font-weight: 500;
    }

    .logistics-reject-required {
        color: #D62F2F;
    }

    .logistics-reject-options {
        display: grid;
        gap: 6px;
    }

    .logistics-reject-option {
        min-height: 36px;
        padding: 7px 10px;
        display: flex;
        align-items: center;
        gap: 11px;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        background: #FCFCFC;
        color: #17120F;
        cursor: pointer;
        transition:
            background .15s ease,
            border-color .15s ease;
    }

    .logistics-reject-option:hover {
        background: #FFF9F7;
        border-color: #E0D7D3;
    }

    .logistics-reject-option input {
        width: 15px;
        height: 15px;
        flex: 0 0 15px;
        margin: 0;
        accent-color: #8F211F;
        cursor: pointer;
    }

    .logistics-reject-option span {
        color: #17120F;
        font-size: 11px;
        line-height: 1.35;
        font-weight: 400;
    }

    .logistics-reject-error {
        display: none;
        margin: 7px 0 0;
        color: #B3262E;
        font-size: 11px;
        line-height: 1.35;
        font-weight: 400;
    }

    .logistics-reject-error.show {
        display: block;
    }

    .logistics-reject-details-section {
        margin-top: 14px;
    }

    .logistics-reject-textarea-wrap {
        position: relative;
    }

    .logistics-reject-textarea {
        width: 100%;
        height: 60px;
        resize: none;
        padding: 10px 48px 10px 12px;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        background: #FFFFFF;
        outline: none;
        color: #57514E;
        font-size: 12px;
        line-height: 1.45;
        font-weight: 400;
        box-sizing: border-box;
        transition:
            border-color .15s ease,
            box-shadow .15s ease;
    }

    .logistics-reject-textarea::placeholder {
        color: #B9B5B3;
    }

    .logistics-reject-textarea:focus {
        border-color: #8F211F;
        box-shadow: 0 0 0 2px rgba(143, 33, 31, .08);
    }

    .logistics-reject-character-count {
        position: absolute;
        right: 10px;
        bottom: 8px;
        color: #8C8784;
        font-size: 10px;
        line-height: 1;
        font-weight: 400;
    }

    .logistics-reject-actions {
        padding: 0 24px 22px;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
    }

    .logistics-reject-cancel,
    .logistics-reject-confirm {
        height: 36px;
        padding: 0 20px;
        border-radius: 8px;
        font-size: 12px;
        line-height: 1;
        font-weight: 600;
        cursor: pointer;
        transition:
            background .15s ease,
            transform .15s ease,
            opacity .15s ease;
    }

    .logistics-reject-cancel {
        border: 1px solid #D1D5DB;
        background: #FFFFFF;
        color: #8F211F;
    }

    .logistics-reject-cancel:hover {
        background: #F9FAFB;
    }

    .logistics-reject-confirm {
        border: 1px solid #C92D32;
        background: #FFE6E6;
        color: #A61B1B;
    }

    .logistics-reject-confirm:hover {
        background: #FFDADA;
    }

    .logistics-reject-cancel:active,
    .logistics-reject-confirm:active {
        transform: scale(.98);
    }

    @media (max-width: 820px) {
        .pending-review-dialog {
            width: min(680px, calc(100vw - 24px));
            padding: 22px 18px 22px;
        }

        .pending-review-columns {
            grid-template-columns: 1fr;
        }

        .pending-review-panel {
            min-height: auto;
        }

        .pending-review-info-item {
            grid-template-columns: 120px minmax(0, 1fr);
        }
    }

    @media (max-width: 520px) {
        .pending-review-info-item {
            grid-template-columns: 1fr;
            gap: 3px;
        }

        .pending-review-permits-grid {
            grid-template-columns: 1fr;
        }

        .pending-review-actions {
            flex-wrap: wrap;
        }

        .pending-review-close,
        .pending-review-reject,
        .pending-review-approve {
            flex: 1 1 100%;
            width: 100%;
        }

        .logistics-reject-body {
            padding: 24px 20px 20px;
        }

        .logistics-reject-actions {
            padding: 0 20px 24px;
        }

        .permit-zoom-backdrop {
            padding: 12px;
        }

        .permit-zoom-dialog {
            width: calc(100vw - 24px);
        }
    }

    /* =========================================================
       COMPANY DETAILS MODAL
    ========================================================== */
    .logistics-details-backdrop {
        position: fixed;
        inset: 0;
        z-index: 190;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 14px;
        background: rgba(0, 0, 0, .36);
        backdrop-filter: blur(2px);
    }

    .logistics-details-backdrop.is-open {
        display: flex;
    }

    .logistics-details-dialog {
        width: min(900px, calc(100vw - 28px));
        height: min(720px, calc(100vh - 28px));
        min-height: 560px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        background: #FFFFFF;
        border-radius: 28px;
        box-shadow: 0 24px 70px rgba(42, 20, 15, .20);
    }

    .logistics-details-header {
        min-height: 66px;
        padding: 0 34px;
        display: flex;
        align-items: center;
        border-bottom: 1px solid #E5E0DE;
        box-sizing: border-box;
    }

    .logistics-details-title {
        margin: 0;
        color: #17120F;
        font-size: 18px;
        line-height: 1.2;
        font-weight: 500;
    }

    .logistics-details-body {
        min-height: 0;
        flex: 1 1 auto;
        padding: 24px 34px 0;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        box-sizing: border-box;
    }

    .logistics-details-company-head {
        min-height: 96px;
        display: flex;
        align-items: flex-start;
        gap: 18px;
    }

    .logistics-details-avatar {
        width: 76px;
        height: 76px;
        flex: 0 0 76px;
        border-radius: 50%;
        overflow: hidden;
        background: #D9D9D9;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .logistics-details-avatar img {
        width: 100%;
        height: 100%;
        display: none;
        object-fit: cover;
    }

    .logistics-details-avatar.has-image img {
        display: block;
    }

    .logistics-details-company-copy {
        min-width: 0;
        margin-top: 20px;
    }

    .logistics-details-company-name {
        margin: 0;
        color: #050505;
        font-size: 20px;
        line-height: 1.2;
        font-weight: 700;
    }

    .logistics-details-company-partnership {
        margin: 6px 0 0;
        color: #9B9693;
        font-size: 11px;
        line-height: 1.35;
        font-weight: 400;
    }

    .logistics-details-tabs {
        height: 40px;
        display: flex;
        align-items: flex-end;
        gap: 0;
        padding-left: 0;
    }

    .logistics-details-tab {
        height: 40px;
        min-width: 180px;
        padding: 0 22px;
        border: 0;
        border-radius: 20px 20px 0 0;
        background: transparent;
        color: #111111;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        line-height: 1;
        font-weight: 600;
        cursor: pointer;
        transition: background .14s ease, color .14s ease;
    }

    .logistics-details-tab:hover {
        background: #FFF9F7;
    }

    .logistics-details-tab.active {
        background: #FFF7F5;
        color: #111111;
    }

    .logistics-details-content-shell {
        min-height: 0;
        flex: 1 1 auto;
        border-radius: 0 18px 18px 18px;
        background: #FFF7F5;
        padding: 30px 22px 16px;
        overflow: auto;
        box-sizing: border-box;
        scrollbar-width: thin;
        scrollbar-color: #D6CFCC transparent;
    }

    .logistics-details-content-shell::-webkit-scrollbar {
        width: 6px;
    }

    .logistics-details-content-shell::-webkit-scrollbar-thumb {
        background: #D6CFCC;
        border-radius: 999px;
    }

    .logistics-details-panel {
        display: none;
    }

    .logistics-details-panel.active {
        display: block;
    }

    /* ---------- Company Details tab ---------- */
    .logistics-company-info-grid {
        display: grid;
        grid-template-columns: 1.12fr .88fr;
        gap: 28px;
        align-items: start;
    }

    .logistics-company-info-left {
        min-width: 0;
    }

    .logistics-company-info-right {
        min-height: 135px;
        border-left: 2px solid #DDD8D6;
        padding-left: 24px;
        display: flex;
        align-items: flex-start;
        box-sizing: border-box;
    }

    .logistics-detail-section-title {
        margin: 0 0 14px;
        color: #9E231F;
        font-size: 15px;
        line-height: 1.2;
        font-weight: 600;
    }

    .logistics-detail-list {
        display: grid;
        grid-template-columns: 160px minmax(0, 1fr);
        column-gap: 14px;
        row-gap: 14px;
    }

    .logistics-detail-label {
        color: #9B9693;
        font-size: 12px;
        line-height: 1.35;
        font-weight: 400;
    }

    .logistics-detail-value {
        min-width: 0;
        color: #17120F;
        font-size: 12px;
        line-height: 1.35;
        font-weight: 500;
        overflow-wrap: anywhere;
    }

    .logistics-details-extra {
        margin-top: 28px;
        padding-top: 22px;
        border-top: 1px solid #E8E1DE;
    }

    .logistics-details-description {
        margin-bottom: 24px;
    }

    .logistics-details-description-text {
        margin: 0;
        color: #4F4946;
        font-size: 12px;
        line-height: 1.65;
        font-weight: 400;
        white-space: pre-wrap;
        overflow-wrap: anywhere;
    }

    .logistics-details-permits-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .logistics-details-permit-item {
        min-width: 0;
    }

    .logistics-details-permit-label {
        display: block;
        margin: 0 0 7px;
        color: #17120F;
        font-size: 11px;
        line-height: 1.2;
        font-weight: 500;
    }

    .logistics-details-permit-button {
        position: relative;
        width: 100%;
        height: 150px;
        padding: 0;
        border: 1px solid #E4DFDC;
        border-radius: 10px;
        background: #FFFFFF;
        overflow: hidden;
        cursor: zoom-in;
        box-sizing: border-box;
        transition:
            border-color .15s ease,
            box-shadow .15s ease,
            transform .15s ease;
    }

    .logistics-details-permit-button:hover {
        border-color: #C9C1BD;
        box-shadow: 0 3px 10px rgba(42, 20, 15, .08);
        transform: translateY(-1px);
    }

    .logistics-details-permit-button img {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
        background: #F7F7F7;
    }

    .logistics-details-permit-zoom-hint {
        position: absolute;
        right: 8px;
        bottom: 8px;
        padding: 5px 8px;
        border-radius: 999px;
        background: rgba(255, 255, 255, .92);
        color: #6B625E;
        font-size: 9px;
        line-height: 1;
        font-weight: 500;
        box-shadow: 0 1px 5px rgba(0, 0, 0, .08);
        pointer-events: none;
    }

    .logistics-created-block {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-top: 26px;
    }

    .logistics-created-icon {
        width: 20px;
        height: 20px;
        flex: 0 0 20px;
        color: #17120F;
    }

    .logistics-created-copy strong {
        display: block;
        color: #17120F;
        font-size: 12px;
        line-height: 1.25;
        font-weight: 500;
    }

    .logistics-created-date-line {
        margin-top: 4px;
        display: flex;
        align-items: center;
        gap: 14px;
        color: #9B9693;
        font-size: 11px;
        line-height: 1.2;
        font-weight: 400;
    }

    /* ---------- Branches tab ---------- */
    .logistics-branches-table-wrap {
        min-height: 320px;
        border-radius: 11px;
        background: #FFFFFF;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(42, 20, 15, .05);
    }

    .logistics-branches-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .logistics-branches-table thead {
        background: #FCE8E8;
    }

    .logistics-branches-table th {
        padding: 12px 22px;
        text-align: left;
        color: #6E1519;
        font-size: 13px;
        line-height: 1.2;
        font-weight: 500;
        white-space: nowrap;
    }

    .logistics-branches-table th:nth-child(1) { width: 23%; }
    .logistics-branches-table th:nth-child(2) { width: 24%; }
    .logistics-branches-table th:nth-child(3) { width: 19%; }
    .logistics-branches-table th:nth-child(4) { width: 21%; }
    .logistics-branches-table th:nth-child(5) { width: 13%; }

    .logistics-branches-table td {
        padding: 9px 22px;
        border-bottom: 1px solid #DAD6D4;
        vertical-align: middle;
        color: #17120F;
        font-size: 13px;
        line-height: 1.35;
        font-weight: 400;
    }

    .logistics-branches-table td:first-child,
    .logistics-branches-table td:nth-child(3) {
        font-weight: 600;
    }

    .logistics-branches-empty {
        padding: 48px 20px;
        text-align: center;
        color: #9B9693;
        font-size: 12px;
    }

    .logistics-details-footer {
        min-height: 82px;
        padding: 14px 28px 22px;
        display: flex;
        align-items: flex-end;
        justify-content: flex-end;
        box-sizing: border-box;
    }

    .logistics-details-close {
        width: 116px;
        height: 40px;
        border: 1px solid #17120F;
        border-radius: 8px;
        background: #FFFFFF;
        color: #17120F;
        font-size: 14px;
        line-height: 1;
        font-weight: 600;
        cursor: pointer;
        transition: background .15s ease, transform .15s ease;
    }

    .logistics-details-close:hover {
        background: #F7F7F7;
    }

    .logistics-details-close:active {
        transform: scale(.98);
    }

    /* =========================================================
       RESPONSIVE
    ========================================================== */
    @media (max-width: 1200px) {
.pending-logistics-filter-card {
            flex-wrap: wrap;
        }

        .pending-logistics-search-wrap {
            width: min(100%, 420px);
            flex: 1 1 420px;
        }
    }

    @media (max-width: 1024px) {
        .logistics-action-card {
            align-items: stretch;
            flex-direction: column;
        }

        .logistics-search-wrap {
            width: 100%;
            flex: 1 1 auto;
        }

        .logistics-add-button {
            align-self: flex-end;
        }
    }

    @media (max-width: 640px) {
.logistics-main-tabs {
            gap: 20px;
            overflow-x: auto;
        }

        .pending-logistics-filter-card {
            align-items: stretch;
            flex-direction: column;
        }

        .pending-logistics-search-wrap,
        .pending-date-wrap {
            width: 100%;
            flex: 1 1 auto;
        }

        .pending-reload {
            align-self: flex-end;
        }

        .pending-table-footer {
            align-items: flex-start;
            flex-direction: column;
        }

        .pending-pagination {
            width: 100%;
            flex-wrap: wrap;
        }

        .logistics-stat-card {
            min-height: 82px;
        }

        .logistics-table-footer {
            align-items: flex-start;
            flex-direction: column;
        }

        .logistics-pagination {
            width: 100%;
            flex-wrap: wrap;
        }
    }

    @media (max-width: 900px) {
        .logistics-details-dialog {
            width: min(700px, calc(100vw - 24px));
            height: min(720px, calc(100vh - 24px));
        }

        .logistics-details-header {
            padding: 0 24px;
        }

        .logistics-details-body {
            padding: 24px 24px 0;
        }

        .logistics-company-info-grid {
            grid-template-columns: 1fr;
            gap: 26px;
        }

        .logistics-company-info-right {
            min-height: auto;
            border-left: 0;
            border-top: 1px solid #DDD8D6;
            padding: 22px 0 0;
        }

        .logistics-created-block {
            margin-top: 0;
        }

        .logistics-branches-table {
            min-width: 760px;
        }
    }

    @media (max-width: 560px) {
        .logistics-details-dialog {
            border-radius: 18px;
        }

        .logistics-details-header {
            min-height: 66px;
            padding: 0 18px;
        }

        .logistics-details-body {
            padding: 20px 16px 0;
        }

        .logistics-details-company-head {
            min-height: 100px;
            gap: 16px;
        }

        .logistics-details-avatar {
            width: 72px;
            height: 72px;
            flex-basis: 72px;
        }

        .logistics-details-company-copy {
            margin-top: 16px;
        }

        .logistics-details-company-name {
            font-size: 19px;
        }

        .logistics-details-company-partnership {
            font-size: 10px;
        }

        .logistics-details-tabs {
            overflow-x: auto;
        }

        .logistics-details-tab {
            min-width: 170px;
            font-size: 15px;
        }

        .logistics-details-content-shell {
            padding: 26px 16px 16px;
        }

        .logistics-detail-list {
            grid-template-columns: 1fr;
            row-gap: 5px;
        }

        .logistics-detail-value {
            margin-bottom: 10px;
        }

        .logistics-details-permits-grid {
            grid-template-columns: 1fr;
        }

        .logistics-details-permit-button {
            height: 170px;
        }

        .logistics-details-footer {
            min-height: 84px;
            padding: 16px 18px 20px;
        }
    }

</style>

<div
    id="admin-content"
    class="ml-60 pt-[110px] pl-5 pb-7 min-h-screen transition-all duration-300"
>
    <!-- =====================================================
         LOGISTICS STAT CARDS
         Matches Account Registrations card layout exactly.
    ====================================================== -->

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">

        <!-- =====================================================
             PENDING REQUESTS
        ====================================================== -->

        <div
            class="logistics-stat-card
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
                        src="{{ asset('icons/admin/complaints-disputes/in-progress.png') }}"
                        class="logistics-stat-icon"
                        alt="Pending Requests"
                    >

                </div>


                <!-- Content -->

                <div class="min-w-0">

                    <p
                        id="pending-logistics-stat-count"
                        class="logistics-stat-number"
                    >
                        {{ number_format($logisticsStats['pending_requests']) }}
                    </p>

                    <p class="logistics-stat-label whitespace-nowrap">
                        Pending Requests
                    </p>

                </div>

            </div>

        </div>


        <!-- =====================================================
             ACCEPTED LOGISTICS
        ====================================================== -->

        <div
            class="logistics-stat-card
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
                        src="{{ asset('icons/admin/registrations/Approved Registrations.png') }}"
                        class="logistics-stat-icon"
                        alt="Accepted Logistics"
                    >

                </div>


                <!-- Content -->

                <div class="min-w-0">

                    <p
                        id="accepted-logistics-stat-count"
                        class="logistics-stat-number"
                    >
                        {{ number_format($logisticsStats['accepted_logistics']) }}
                    </p>

                    <p class="logistics-stat-label whitespace-nowrap">
                        Accepted Logistics
                    </p>

                </div>

            </div>

        </div>


        <!-- =====================================================
             REJECTED LOGISTICS ARCHIVE
        ====================================================== -->

        <div>

            <button
                type="button"
                id="rejected-logistics-button"
                class="logistics-stat-card w-full
                       relative text-left
                       flex flex-col justify-center
                       transition-all duration-300
                       hover:-translate-y-1 hover:shadow-md
                       hover:border-[#E9A3A3]
                       group"
                aria-label="View rejected logistics"
            >

                <div class="flex items-center justify-between">

                    <div class="flex items-center gap-2.5">

                        <!-- Icon -->

                        <div
                            class="shrink-0 flex items-center justify-center"
                        >

                            <img
                                src="{{ asset('icons/admin/registrations/Rejected Registrations.png') }}"
                                class="logistics-stat-icon"
                                alt="Rejected Logistics"
                            >

                        </div>


                        <!-- Content -->

                        <div class="min-w-0">

                            <p
                                id="rejected-logistics-stat-count"
                                class="logistics-stat-number"
                            >
                                {{ number_format($logisticsStats['rejected_logistics']) }}
                            </p>

                            <p class="logistics-stat-label whitespace-nowrap">
                                Rejected Logistics
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

                    <span class="logistics-card-link">
                        View rejected users →
                    </span>

                </div>

            </button>

        </div>


        <!-- =====================================================
             TOTAL LOGISTICS
        ====================================================== -->

        <div
            class="logistics-stat-card relative
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
                        src="{{ asset('icons/admin/logistics/logistics.png') }}"
                        class="logistics-stat-icon"
                        alt="Total Logistics"
                    >

                </div>


                <!-- Content -->

                <div class="min-w-0">

                    <p
                        id="total-logistics-stat-count"
                        class="logistics-stat-number"
                    >
                        {{ number_format($logisticsStats['total_companies']) }}
                    </p>

                    <p class="logistics-stat-label whitespace-nowrap">
                        Total Logistics
                    </p>

                </div>

            </div>

        </div>

    </div>


    <!-- =====================================================
         MAIN TABS
    ====================================================== -->
    <section class="logistics-main-tabs-card">
        <div
            class="logistics-main-tabs"
            role="tablist"
            aria-label="Logistics management tabs"
        >
            <button
                id="pending-requests-main-tab"
                type="button"
                class="logistics-main-tab active"
                role="tab"
                aria-selected="true"
                data-main-tab="pending"
            >
                Pending Requests
            </button>

            <button
                id="logistics-main-tab"
                type="button"
                class="logistics-main-tab"
                role="tab"
                aria-selected="false"
                data-main-tab="logistics"
            >
                Logistics
            </button>
        </div>
    </section>

    <!-- =====================================================
         PENDING REQUESTS TAB
    ====================================================== -->
    <section
        id="pending-requests-panel"
        class="logistics-main-panel active"
        aria-labelledby="pending-requests-main-tab"
    >
        <!-- Filters -->
        <div class="pending-logistics-filter-card">

            <div class="pending-logistics-search-wrap">
                <input
                    id="pending-logistics-search"
                    type="text"
                    class="pending-logistics-search"
                    placeholder="Search company, email, phone, contact person"
                    autocomplete="off"
                >

                <svg
                    class="pending-logistics-search-icon"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"
                    />
                </svg>
            </div>

            <div class="pending-date-wrap">
                <input
                    id="pending-logistics-date"
                    type="date"
                    class="pending-date-input"
                    aria-label="Filter pending requests by registration date"
                >
            </div>

            <button
                id="pending-logistics-reload"
                type="button"
                class="pending-reload"
                title="Reload"
                aria-label="Reset pending request filters"
            >
                <svg
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    aria-hidden="true"
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

        <!-- Pending Table -->
        <section class="pending-logistics-table-card">
            <div class="pending-logistics-table-scroll">
                <table
                    id="pending-logistics-table"
                    class="pending-logistics-table"
                >
                    <thead>
                        <tr>
                            <th>Company Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Contact Person</th>
                            <th>Date Registered</th>
                        </tr>
                    </thead>

                    <tbody id="pending-logistics-body"></tbody>
                </table>

                <div
                    id="pending-logistics-empty"
                    class="pending-table-empty"
                >
                    No pending logistics requests found.
                </div>
            </div>

            <div class="pending-table-footer">
                <p
                    id="pending-logistics-count"
                    class="pending-count"
                >
                    Showing 0 out of {{ number_format($pendingLogisticsTotalEntries) }} entries
                </p>

                <div class="pending-pagination">
                    <button
                        id="pending-logistics-prev"
                        type="button"
                        class="pending-page-button"
                        aria-label="Previous page"
                    >
                        ‹
                    </button>

                    <div
                        id="pending-logistics-pages"
                        class="flex items-center gap-2"
                    ></div>

                    <button
                        id="pending-logistics-next"
                        type="button"
                        class="pending-page-button"
                        aria-label="Next page"
                    >
                        ›
                    </button>

                    <select
                        id="pending-logistics-items"
                        class="pending-items"
                    >
                        <option value="10" selected>Items per page: 10</option>
                        <option value="20">Items per page: 20</option>
                        <option value="50">Items per page: 50</option>
                    </select>
                </div>
            </div>
        </section>
    </section>

    <!-- =====================================================
         LOGISTICS TAB — CURRENT TABLE RETAINED
    ====================================================== -->
    <section
        id="logistics-panel"
        class="logistics-main-panel"
        aria-labelledby="logistics-main-tab"
    >
        <section class="logistics-action-card">
            <div class="logistics-search-wrap">
                <input
                    id="logistics-search"
                    type="text"
                    class="logistics-search"
                    placeholder="Search company"
                    autocomplete="off"
                >

                <svg
                    class="logistics-search-icon"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"
                    />
                </svg>
            </div>
        </section>

        <section class="logistics-table-card">
            <div class="logistics-table-scroll">
                <table class="logistics-table">
                    <thead>
                        <tr>
                            <th>Company Name</th>
                            <th>Number of Branches</th>
                            <th>Number of Riders</th>
                        </tr>
                    </thead>

                    <tbody id="logistics-table-body"></tbody>
                </table>

                <div
                    id="logistics-empty"
                    class="logistics-empty"
                >
                    No logistics company matches your search.
                </div>
            </div>

            <div class="logistics-table-footer">
                <p
                    id="logistics-count"
                    class="logistics-count"
                >
                    Showing 1 out of {{ number_format($logisticsTotalEntries) }} entries
                </p>

                <div class="logistics-pagination">
                    <button
                        id="logistics-prev"
                        type="button"
                        class="logistics-page-button"
                        aria-label="Previous page"
                    >
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="m15 6-6 6 6 6"
                            />
                        </svg>
                    </button>

                    <div
                        id="logistics-pages"
                        class="flex items-center gap-2"
                    ></div>

                    <button
                        id="logistics-next"
                        type="button"
                        class="logistics-page-button"
                        aria-label="Next page"
                    >
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="m9 6 6 6-6 6"
                            />
                        </svg>
                    </button>

                    <select
                        id="logistics-items"
                        class="logistics-items"
                    >
                        <option value="7" selected>Items per page: 7</option>
                        <option value="10">Items per page: 10</option>
                        <option value="20">Items per page: 20</option>
                    </select>
                </div>
            </div>
        </section>
    </section>
</div>


<!-- =========================================================
     REJECTED LOGISTICS ARCHIVE MODAL
     Styled to match the Rejected Users modal in Registrations.
========================================================== -->
<div
    id="rejected-logistics-modal"
    class="fixed inset-0 z-[180] hidden items-center justify-center
           bg-black/30 backdrop-blur-sm px-5"
    aria-hidden="true"
>
    <div
        class="logistics-archive-modal bg-white w-full max-w-5xl max-h-[88vh]
               overflow-y-auto rounded-2xl shadow-xl
               transform transition-all duration-300"
        role="dialog"
        aria-modal="true"
        aria-labelledby="rejected-logistics-modal-title"
    >
        <!-- Modal Header -->
        <div
            class="flex items-center justify-between
                   px-5 py-4 border-b border-gray-200"
        >
            <div>
                <h3
                    id="rejected-logistics-modal-title"
                    class="text-[20px] font-bold text-gray-900"
                >
                    Rejected Logistics
                </h3>

                <p class="text-[13px] text-gray-400 mt-1">
                    Archived rejected logistics requests
                </p>
            </div>

            <button
                type="button"
                id="close-rejected-logistics"
                class="w-8 h-8 flex items-center justify-center
                       rounded-full text-gray-500
                       hover:bg-gray-100 hover:text-gray-800
                       transition"
                aria-label="Close rejected logistics"
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
                        d="M6 6l12 12M18 6 6 18"
                    />
                </svg>
            </button>
        </div>

        <!-- Rejected Logistics Search -->
        <div class="px-6 py-4 border-b border-gray-100 bg-[#FFFBF9]">
            <div class="relative max-w-md">
                <input
                    type="text"
                    id="rejected-logistics-search"
                    placeholder="Search rejected logistics..."
                    class="w-full h-[40px] rounded-lg border border-gray-300
                           bg-white pl-10 pr-4 text-[13px] text-gray-700
                           placeholder:text-gray-300 outline-none
                           focus:border-[#7B1B1B]
                           focus:ring-2 focus:ring-[#7B1B1B]/10
                           transition"
                >

                <svg
                    class="pointer-events-none absolute left-3 top-1/2
                           -translate-y-1/2 w-4 h-4 text-gray-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"
                    />
                </svg>
            </div>
        </div>

        <!-- Rejected Logistics Table -->
        <div class="overflow-x-auto max-h-none">
            <table class="w-full min-w-[900px]">
                <thead class="sticky top-0 bg-white">
                    <tr class="border-b border-gray-200">
                        <th class="text-left px-6 py-4 text-[13px] font-medium text-gray-400">
                            Company
                        </th>

                        <th class="text-left px-6 py-4 text-[13px] font-medium text-gray-400">
                            Email
                        </th>

                        <th class="text-left px-6 py-4 text-[13px] font-medium text-gray-400">
                            Phone
                        </th>

                        <th class="text-left px-6 py-4 text-[13px] font-medium text-gray-400">
                            Contact Person
                        </th>

                        <th class="text-left px-6 py-4 text-[13px] font-medium text-gray-400">
                            <button
                                type="button"
                                id="rejected-logistics-date-sort"
                                class="inline-flex items-center gap-1.5
                                       hover:text-[#7B1B1B] transition"
                                aria-label="Sort rejected logistics by date"
                            >
                                <span>Date Rejected</span>

                                <span
                                    id="rejected-logistics-sort-arrow"
                                    class="inline-flex items-center justify-center
                                           w-6 h-6 text-[17px] leading-none
                                           font-bold text-[#7B1B1B]
                                           rounded-md bg-[#FFF0EC]
                                           border border-[#F3C2B5]"
                                >
                                    ↓
                                </span>
                            </button>
                        </th>
                    </tr>
                </thead>

                <tbody
                    id="rejected-logistics-body"
                    class="divide-y divide-gray-200"
                ></tbody>
            </table>
        </div>

        <!-- Modal Footer -->
        <div
            class="px-6 py-4 border-t border-gray-200
                   flex items-center justify-between"
        >
            <p
                id="rejected-logistics-count"
                class="text-[12px] text-gray-400"
            ></p>

            <button
                type="button"
                id="close-rejected-logistics-bottom"
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
     PENDING LOGISTICS REVIEW MODAL
========================================================== -->
<div
    id="pending-review-modal"
    class="pending-review-backdrop"
    aria-hidden="true"
>
    <div
        class="pending-review-dialog"
        role="dialog"
        aria-modal="true"
        aria-labelledby="pending-review-title"
    >
        <h2
            id="pending-review-title"
            class="pending-review-title"
        >
            Logistics Application Details
        </h2>

        <div class="pending-review-columns">

            <!-- LEFT: READ-ONLY COMPANY INFORMATION -->
            <section class="pending-review-panel">
                <h3 class="pending-review-panel-title">
                    <img
                        src="{{ asset('icons/admin/logistics/truck.png') }}"
                        alt=""
                        aria-hidden="true"
                    >
                    Company Information
                </h3>

                <div class="pending-review-info-list">
                    <div class="pending-review-info-item">
                        <span class="pending-review-info-label">
                            Company Name
                        </span>
                        <div
                            id="pending-review-company"
                            class="pending-review-info-value"
                        >
                            —
                        </div>
                    </div>

                    <div class="pending-review-info-item">
                        <span class="pending-review-info-label">
                            Contact Number
                        </span>
                        <div
                            id="pending-review-phone"
                            class="pending-review-info-value"
                        >
                            —
                        </div>
                    </div>

                    <div class="pending-review-info-item">
                        <span class="pending-review-info-label">
                            Email Address
                        </span>
                        <div
                            id="pending-review-email"
                            class="pending-review-info-value"
                        >
                            —
                        </div>
                    </div>

                    <div class="pending-review-info-item">
                        <span class="pending-review-info-label">
                            Contact Person
                        </span>
                        <div
                            id="pending-review-contact-person"
                            class="pending-review-info-value"
                        >
                            —
                        </div>
                    </div>

                    <div class="pending-review-info-item">
                        <span class="pending-review-info-label">
                            Office Address
                        </span>
                        <div
                            id="pending-review-address"
                            class="pending-review-info-value address"
                        >
                            —
                        </div>
                    </div>
                </div>

                <h4 class="pending-review-additional-title">
                    Additional Details
                </h4>

                <span class="pending-review-info-label">
                    Company Logo
                </span>

                <div
                    id="pending-review-logo-box"
                    class="pending-review-logo-box"
                >
                    <div class="pending-review-logo-placeholder">
                        <svg
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <rect
                                x="3"
                                y="4"
                                width="18"
                                height="16"
                                rx="2"
                                stroke-width="1.8"
                            />
                            <circle
                                cx="8.5"
                                cy="9"
                                r="1.5"
                                stroke-width="1.8"
                            />
                            <path
                                d="m5 17 4.2-4.2 3.2 3.2 2.1-2.1L19 18"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>

                        <span>No company logo uploaded</span>
                    </div>

                    <img
                        id="pending-review-logo"
                        src=""
                        alt="Company logo"
                    >
                </div>

                <h4 class="pending-review-permits-title">
                    Permits
                </h4>

                <div class="pending-review-permits-grid">
                    <div class="pending-review-permit-item">
                        <span class="pending-review-permit-label">
                            DTI Permit
                        </span>

                        <button
                            id="pending-review-dti-button"
                            type="button"
                            class="pending-review-permit-button"
                            aria-label="View DTI Permit"
                        >
                            <img
                                id="pending-review-dti-permit"
                                src=""
                                alt="DTI Permit preview"
                            >

                            <span class="pending-review-permit-zoom-hint">
                                Click to view
                            </span>
                        </button>
                    </div>

                    <div class="pending-review-permit-item">
                        <span class="pending-review-permit-label">
                            Business Permit
                        </span>

                        <button
                            id="pending-review-business-button"
                            type="button"
                            class="pending-review-permit-button"
                            aria-label="View Business Permit"
                        >
                            <img
                                id="pending-review-business-permit"
                                src=""
                                alt="Business Permit preview"
                            >

                            <span class="pending-review-permit-zoom-hint">
                                Click to view
                            </span>
                        </button>
                    </div>
                </div>
            </section>

            <!-- RIGHT: LOGISTICS DESCRIPTION -->
            <section class="pending-review-panel">
                <h3 class="pending-review-panel-title">
                    Logistics Description
                </h3>

                <div class="pending-review-description-note">
                    <svg
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <circle
                            cx="12"
                            cy="12"
                            r="9"
                            stroke-width="2"
                        />
                        <path
                            d="M12 10v6M12 7h.01"
                            stroke-width="2"
                            stroke-linecap="round"
                        />
                    </svg>

                    <p>
                        Review the logistics company's submitted description
                        and company information before processing the request.
                    </p>
                </div>

                <span class="pending-review-info-label">
                    Description
                </span>

                <div
                    id="pending-review-description"
                    class="pending-review-description-box"
                >
                    —
                </div>

                <div class="pending-review-meta">
                    <div class="pending-review-meta-item">
                        <span class="pending-review-meta-label">
                            Date Registered
                        </span>
                        <span
                            id="pending-review-date"
                            class="pending-review-meta-value"
                        >
                            —
                        </span>
                    </div>

                    <div class="pending-review-meta-item pending-review-status-item">
                        <span class="pending-review-meta-label">
                            Request Status
                        </span>
                        <span class="pending-review-status-pill">
                            Pending
                        </span>
                    </div>
                </div>
            </section>

        </div>

        <div class="pending-review-actions">
            <button
                id="pending-review-close"
                type="button"
                class="pending-review-close"
            >
                Close
            </button>

            <button
                id="pending-review-reject"
                type="button"
                class="pending-review-reject"
            >
                Reject
            </button>

            <button
                id="pending-review-approve"
                type="button"
                class="pending-review-approve"
            >
                Approve
            </button>
        </div>
    </div>
</div>


<!-- =========================================================
     LOGISTICS DECISION FLASH MESSAGE
========================================================== -->
<div
    id="logistics-flash"
    class="logistics-decision-flash"
    role="status"
    aria-live="polite"
    aria-atomic="true"
>
    <div class="logistics-flash-content">

        <div
            id="logistics-flash-icon"
            class="logistics-flash-icon"
        ></div>

        <div class="logistics-flash-copy">
            <p
                id="logistics-flash-title"
                class="logistics-flash-title"
            ></p>

            <p
                id="logistics-flash-message"
                class="logistics-flash-message"
            ></p>
        </div>

        <button
            id="logistics-flash-close"
            type="button"
            class="logistics-flash-close"
            aria-label="Close notification"
        >
            <svg
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                aria-hidden="true"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M6 6l12 12M18 6 6 18"
                />
            </svg>
        </button>

    </div>
</div>


<!-- =========================================================
     REJECT LOGISTICS REQUEST MODAL
========================================================== -->
<div
    id="logistics-reject-modal"
    class="logistics-reject-backdrop"
    aria-hidden="true"
>
    <div
        class="logistics-reject-dialog"
        role="dialog"
        aria-modal="true"
        aria-labelledby="logistics-reject-title"
    >
        <div class="logistics-reject-body">
            <h3
                id="logistics-reject-title"
                class="logistics-reject-title"
            >
                Reject Logistics Request
            </h3>

            <p class="logistics-reject-subtitle">
                Please select the reason for rejecting this logistics company request.
            </p>

            <div class="logistics-reject-reason-section">
                <label class="logistics-reject-label">
                    Reason<span class="logistics-reject-required">*</span>
                </label>

                <div
                    id="logistics-reject-reason-options"
                    class="logistics-reject-options"
                >
                    <label class="logistics-reject-option">
                        <input
                            type="radio"
                            name="logistics_reject_reason"
                            value="Incomplete or missing business permits."
                        >
                        <span>
                            Incomplete or missing business permits.
                        </span>
                    </label>

                    <label class="logistics-reject-option">
                        <input
                            type="radio"
                            name="logistics_reject_reason"
                            value="DTI or Business Permit is invalid, expired, or unverifiable."
                        >
                        <span>
                            DTI or Business Permit is invalid, expired, or unverifiable.
                        </span>
                    </label>

                    <label class="logistics-reject-option">
                        <input
                            type="radio"
                            name="logistics_reject_reason"
                            value="Company information is inconsistent or cannot be verified."
                        >
                        <span>
                            Company information is inconsistent or cannot be verified.
                        </span>
                    </label>

                    <label class="logistics-reject-option">
                        <input
                            type="radio"
                            name="logistics_reject_reason"
                            value="Does not meet ShopEase logistics partnership requirements."
                        >
                        <span>
                            Does not meet ShopEase logistics partnership requirements.
                        </span>
                    </label>

                    <label class="logistics-reject-option">
                        <input
                            type="radio"
                            name="logistics_reject_reason"
                            value="Insufficient delivery coverage or operational capacity."
                        >
                        <span>
                            Insufficient delivery coverage or operational capacity.
                        </span>
                    </label>

                    <label class="logistics-reject-option">
                        <input
                            type="radio"
                            name="logistics_reject_reason"
                            value="Duplicate or existing logistics partner account."
                        >
                        <span>
                            Duplicate or existing logistics partner account.
                        </span>
                    </label>

                    <label class="logistics-reject-option">
                        <input
                            type="radio"
                            name="logistics_reject_reason"
                            value="Other"
                        >
                        <span>
                            Other
                        </span>
                    </label>
                </div>

                <p
                    id="logistics-reject-reason-error"
                    class="logistics-reject-error"
                >
                    Please select a reason before rejecting the request.
                </p>
            </div>

            <div class="logistics-reject-details-section">
                <label
                    for="logistics-reject-additional-details"
                    class="logistics-reject-label"
                >
                    Additional Details
                    <span style="font-weight:400;">(Optional)</span>
                </label>

                <div class="logistics-reject-textarea-wrap">
                    <textarea
                        id="logistics-reject-additional-details"
                        class="logistics-reject-textarea"
                        maxlength="300"
                        placeholder="Write additional details here..."
                    ></textarea>

                    <span
                        id="logistics-reject-details-count"
                        class="logistics-reject-character-count"
                    >
                        0/300
                    </span>
                </div>
            </div>
        </div>

        <div class="logistics-reject-actions">
            <button
                id="cancel-logistics-reject"
                type="button"
                class="logistics-reject-cancel"
            >
                Cancel
            </button>

            <button
                id="confirm-logistics-reject"
                type="button"
                class="logistics-reject-confirm"
            >
                Reject
            </button>
        </div>
    </div>
</div>


<!-- =========================================================
     PERMIT IMAGE ZOOM MODAL
========================================================== -->
<div
    id="permit-zoom-modal"
    class="permit-zoom-backdrop"
    aria-hidden="true"
>
    <div
        class="permit-zoom-dialog"
        role="dialog"
        aria-modal="true"
        aria-labelledby="permit-zoom-title"
    >
        <div class="permit-zoom-header">
            <h3
                id="permit-zoom-title"
                class="permit-zoom-title"
            >
                Permit Preview
            </h3>

            <button
                id="permit-zoom-close"
                type="button"
                class="permit-zoom-close"
                aria-label="Close permit preview"
            >
                ×
            </button>
        </div>

        <div class="permit-zoom-image-wrap">
            <img
                id="permit-zoom-image"
                class="permit-zoom-image"
                src=""
                alt="Permit preview"
            >
        </div>
    </div>
</div>


<!-- =========================================================
     COMPANY DETAILS MODAL
========================================================== -->
<div
    id="logistics-details-modal"
    class="logistics-details-backdrop"
    aria-hidden="true"
>
    <div
        class="logistics-details-dialog"
        role="dialog"
        aria-modal="true"
        aria-labelledby="logistics-details-title"
    >
        <header class="logistics-details-header">
            <h2
                id="logistics-details-title"
                class="logistics-details-title"
            >
                Company Details
            </h2>
        </header>

        <div class="logistics-details-body">

            <div class="logistics-details-company-head">
                <div
                    id="logistics-details-avatar"
                    class="logistics-details-avatar"
                >
                    <img
                        id="logistics-details-avatar-image"
                        src=""
                        alt="Company logo"
                    >
                </div>

                <div class="logistics-details-company-copy">
                    <h3
                        id="logistics-details-company-name"
                        class="logistics-details-company-name"
                    >
                        J&T Express
                    </h3>

                    <p
                        id="logistics-details-company-partnership"
                        class="logistics-details-company-partnership"
                    >
                        In partnership with ShopEase since 2019
                    </p>
                </div>
            </div>

            <div
                class="logistics-details-tabs"
                role="tablist"
                aria-label="Company details tabs"
            >
                <button
                    id="logistics-details-company-tab"
                    type="button"
                    class="logistics-details-tab active"
                    role="tab"
                    aria-selected="true"
                    aria-controls="logistics-company-details-panel"
                    data-details-tab="company"
                >
                    Company Details
                </button>

                <button
                    id="logistics-details-branches-tab"
                    type="button"
                    class="logistics-details-tab"
                    role="tab"
                    aria-selected="false"
                    aria-controls="logistics-branches-panel"
                    data-details-tab="branches"
                >
                    Branches
                </button>
            </div>

            <div class="logistics-details-content-shell">

                <!-- COMPANY DETAILS TAB -->
                <section
                    id="logistics-company-details-panel"
                    class="logistics-details-panel active"
                    role="tabpanel"
                    aria-labelledby="logistics-details-company-tab"
                >
                    <div class="logistics-company-info-grid">

                        <div class="logistics-company-info-left">

                            <h4 class="logistics-detail-section-title">
                                Company Information
                            </h4>

                            <div class="logistics-detail-list">
                                <span class="logistics-detail-label">
                                    Company Name
                                </span>
                                <span
                                    id="details-company-name"
                                    class="logistics-detail-value"
                                >
                                    J&T Express
                                </span>

                                <span class="logistics-detail-label">
                                    Contact Number
                                </span>
                                <span
                                    id="details-contact-number"
                                    class="logistics-detail-value"
                                >
                                    123456789
                                </span>

                                <span class="logistics-detail-label">
                                    Email Address
                                </span>
                                <span
                                    id="details-company-email"
                                    class="logistics-detail-value"
                                >
                                    j&texpress@gmail.com
                                </span>

                                <span class="logistics-detail-label">
                                    Office Address
                                </span>
                                <span
                                    id="details-office-address"
                                    class="logistics-detail-value"
                                >
                                    kung saan ka masaya
                                </span>
                            </div>

</div>

                        <div class="logistics-company-info-right">
                            <div class="logistics-created-block">
                                <svg
                                    class="logistics-created-icon"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                    aria-hidden="true"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M8 2v3m8-3v3M4 9h16M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"
                                    />
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 13h2v2H9z"
                                    />
                                </svg>

                                <div class="logistics-created-copy">
                                    <strong>Account Created</strong>

                                    <div class="logistics-created-date-line">
                                        <span id="details-created-date">
                                            May 26, 2026
                                        </span>

                                        <span id="details-created-time">
                                            6:45 PM
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="logistics-details-extra">

                        <div class="logistics-details-description">
                            <h4 class="logistics-detail-section-title">
                                Logistics Description
                            </h4>

                            <p
                                id="details-company-description"
                                class="logistics-details-description-text"
                            >
                                —
                            </p>
                        </div>

                        <div class="logistics-details-permits">
                            <h4 class="logistics-detail-section-title">
                                Permits
                            </h4>

                            <div class="logistics-details-permits-grid">

                                <div class="logistics-details-permit-item">
                                    <span class="logistics-details-permit-label">
                                        DTI Permit
                                    </span>

                                    <button
                                        id="details-dti-permit-button"
                                        type="button"
                                        class="logistics-details-permit-button"
                                        aria-label="View DTI Permit"
                                    >
                                        <img
                                            id="details-dti-permit"
                                            src=""
                                            alt="DTI Permit preview"
                                        >

                                        <span class="logistics-details-permit-zoom-hint">
                                            Click to view
                                        </span>
                                    </button>
                                </div>

                                <div class="logistics-details-permit-item">
                                    <span class="logistics-details-permit-label">
                                        Business Permit
                                    </span>

                                    <button
                                        id="details-business-permit-button"
                                        type="button"
                                        class="logistics-details-permit-button"
                                        aria-label="View Business Permit"
                                    >
                                        <img
                                            id="details-business-permit"
                                            src=""
                                            alt="Business Permit preview"
                                        >

                                        <span class="logistics-details-permit-zoom-hint">
                                            Click to view
                                        </span>
                                    </button>
                                </div>

                            </div>
                        </div>

                    </div>
                </section>

                <!-- BRANCHES TAB -->
                <section
                    id="logistics-branches-panel"
                    class="logistics-details-panel"
                    role="tabpanel"
                    aria-labelledby="logistics-details-branches-tab"
                >
                    <div class="logistics-branches-table-wrap">
                        <div style="overflow-x:auto;">
                            <table class="logistics-branches-table">
                                <thead>
                                    <tr>
                                        <th>Branch Name</th>
                                        <th>Address</th>
                                        <th>Contact Person</th>
                                        <th>Phone Number</th>
                                        <th>Riders</th>
                                    </tr>
                                </thead>

                                <tbody id="logistics-branches-body"></tbody>
                            </table>
                        </div>

                        <div
                            id="logistics-branches-empty"
                            class="logistics-branches-empty"
                            hidden
                        >
                            No branch information available yet.
                        </div>
                    </div>
                </section>

            </div>
        </div>

        <footer class="logistics-details-footer">
            <button
                id="close-logistics-details"
                type="button"
                class="logistics-details-close"
            >
                Close
            </button>
        </footer>
    </div>
</div>


@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* =========================================================
       DATA
    ========================================================== */
    const pendingLogisticsRequests =
        @json($pendingLogisticsRequests);

    let pendingOriginalTotalEntries =
        Number(@json($pendingLogisticsTotalEntries));

    const logisticsCompanies =
        @json($logisticsCompanies);

    const rejectedLogisticsArchive =
        @json($rejectedLogisticsArchive);

    let originalTotalEntries =
        Number(@json($logisticsTotalEntries));

    const pendingLogisticsStatCount =
        document.getElementById('pending-logistics-stat-count');

    const acceptedLogisticsStatCount =
        document.getElementById('accepted-logistics-stat-count');

    const rejectedLogisticsStatCount =
        document.getElementById('rejected-logistics-stat-count');

    const totalLogisticsStatCount =
        document.getElementById('total-logistics-stat-count');

    const rejectedLogisticsButton =
        document.getElementById('rejected-logistics-button');

    const rejectedLogisticsModal =
        document.getElementById('rejected-logistics-modal');

    const closeRejectedLogistics =
        document.getElementById('close-rejected-logistics');

    const closeRejectedLogisticsBottom =
        document.getElementById('close-rejected-logistics-bottom');

    const rejectedLogisticsSearch =
        document.getElementById('rejected-logistics-search');

    const rejectedLogisticsBody =
        document.getElementById('rejected-logistics-body');

    const rejectedLogisticsCount =
        document.getElementById('rejected-logistics-count');

    const rejectedLogisticsDateSort =
        document.getElementById('rejected-logistics-date-sort');

    const rejectedLogisticsSortArrow =
        document.getElementById('rejected-logistics-sort-arrow');

    let rejectedLogisticsSortDirection =
        'desc';

    let pendingStatValue =
        Number(@json($logisticsStats['pending_requests']));

    let acceptedStatValue =
        Number(@json($logisticsStats['accepted_logistics']));

    let rejectedStatValue =
        Number(@json($logisticsStats['rejected_logistics']));

    let totalStatValue =
        Number(@json($logisticsStats['total_companies']));

    function renderLogisticsStats() {
        if (pendingLogisticsStatCount) {
            pendingLogisticsStatCount.textContent =
                pendingStatValue.toLocaleString();
        }

        if (acceptedLogisticsStatCount) {
            acceptedLogisticsStatCount.textContent =
                acceptedStatValue.toLocaleString();
        }

        if (rejectedLogisticsStatCount) {
            rejectedLogisticsStatCount.textContent =
                rejectedStatValue.toLocaleString();
        }

        if (totalLogisticsStatCount) {
            totalLogisticsStatCount.textContent =
                totalStatValue.toLocaleString();
        }
    }

    /* =========================================================
       REJECTED LOGISTICS ARCHIVE
       Mirrors the search / date-sort / modal behavior in Registrations.
    ========================================================== */
    function getRejectedLogisticsDate(entry) {
        const parsed =
            Date.parse(
                entry.rejected_display || ''
            );

        return Number.isNaN(parsed)
            ? 0
            : parsed;
    }

    function getFilteredRejectedLogistics() {
        const query =
            normalize(
                rejectedLogisticsSearch
                    ? rejectedLogisticsSearch.value
                    : ''
            );

        const rows =
            rejectedLogisticsArchive.filter(entry => {
                if (!query) {
                    return true;
                }

                return [
                    entry.company,
                    entry.email,
                    entry.phone,
                    entry.contact_person,
                    entry.rejected_display
                ].some(value =>
                    normalize(value).includes(query)
                );
            });

        rows.sort((a, b) => {
            const dateA =
                getRejectedLogisticsDate(a);

            const dateB =
                getRejectedLogisticsDate(b);

            return rejectedLogisticsSortDirection === 'asc'
                ? dateA - dateB
                : dateB - dateA;
        });

        return rows;
    }

    function rejectedCompanyInitials(name) {
        return String(name || '')
            .split(' ')
            .filter(Boolean)
            .slice(0, 2)
            .map(part =>
                part.charAt(0).toUpperCase()
            )
            .join('');
    }

    function renderRejectedLogisticsArchive() {
        if (
            !rejectedLogisticsBody ||
            !rejectedLogisticsCount
        ) {
            return;
        }

        const rows =
            getFilteredRejectedLogistics();

        if (!rows.length) {
            rejectedLogisticsBody.innerHTML = `
                <tr>
                    <td
                        colspan="5"
                        class="px-6 py-10 text-center
                               text-[12px] text-gray-400"
                    >
                        No rejected logistics found.
                    </td>
                </tr>
            `;
        } else {
            rejectedLogisticsBody.innerHTML =
                rows.map(entry => `
                    <tr class="hover:bg-[#FFF9F7] transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2.5">
                                <div
                                    class="w-7 h-7 rounded-full
                                           bg-[#F6D8D2]
                                           flex items-center justify-center
                                           text-[10px] font-semibold
                                           text-[#7B1B1B] shrink-0"
                                >
                                    ${escapeHtml(
                                        rejectedCompanyInitials(
                                            entry.company
                                        )
                                    )}
                                </div>

                                <span
                                    class="text-[12px] font-medium text-gray-800"
                                >
                                    ${escapeHtml(entry.company || '—')}
                                </span>
                            </div>
                        </td>

                        <td class="px-6 py-4 text-[12px] text-gray-400">
                            ${escapeHtml(entry.email || '—')}
                        </td>

                        <td class="px-6 py-4 text-[12px]">
                            ${escapeHtml(entry.phone || '—')}
                        </td>

                        <td class="px-6 py-4 text-[12px]">
                            ${escapeHtml(entry.contact_person || '—')}
                        </td>

                        <td class="px-6 py-4 text-[12px]">
                            ${escapeHtml(entry.rejected_display || '—')}
                        </td>
                    </tr>
                `).join('');
        }

        rejectedLogisticsCount.textContent =
            `${rejectedLogisticsArchive.length} rejected logistics`;
    }

    function openRejectedLogisticsModal() {
        if (!rejectedLogisticsModal) {
            return;
        }

        if (rejectedLogisticsSearch) {
            rejectedLogisticsSearch.value = '';
        }

        rejectedLogisticsSortDirection =
            'desc';

        if (rejectedLogisticsSortArrow) {
            rejectedLogisticsSortArrow.textContent =
                '↓';
        }

        renderRejectedLogisticsArchive();

        rejectedLogisticsModal.classList.remove(
            'hidden'
        );

        rejectedLogisticsModal.classList.add(
            'flex'
        );

        rejectedLogisticsModal.setAttribute(
            'aria-hidden',
            'false'
        );

        document.body.classList.add(
            'overflow-hidden'
        );
    }

    function closeRejectedLogisticsModal() {
        if (!rejectedLogisticsModal) {
            return;
        }

        rejectedLogisticsModal.classList.add(
            'hidden'
        );

        rejectedLogisticsModal.classList.remove(
            'flex'
        );

        rejectedLogisticsModal.setAttribute(
            'aria-hidden',
            'true'
        );

        document.body.classList.remove(
            'overflow-hidden'
        );
    }

    /* =========================================================
       MAIN TABS
    ========================================================== */
    const mainTabs =
        Array.from(
            document.querySelectorAll('[data-main-tab]')
        );

    const pendingPanel =
        document.getElementById('pending-requests-panel');

    const logisticsPanel =
        document.getElementById('logistics-panel');

    function switchMainTab(tabName) {
        mainTabs.forEach(tab => {
            const active =
                tab.dataset.mainTab === tabName;

            tab.classList.toggle(
                'active',
                active
            );

            tab.setAttribute(
                'aria-selected',
                active ? 'true' : 'false'
            );
        });

        pendingPanel.classList.toggle(
            'active',
            tabName === 'pending'
        );

        logisticsPanel.classList.toggle(
            'active',
            tabName === 'logistics'
        );
    }

    mainTabs.forEach(tab => {
        tab.addEventListener(
            'click',
            function () {
                switchMainTab(
                    tab.dataset.mainTab
                );
            }
        );
    });

    /* =========================================================
       HELPERS
    ========================================================== */
    function normalize(value) {
        return String(value || '')
            .trim()
            .toLowerCase();
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function getPageNumbers(currentPage, totalPages) {
        if (totalPages <= 5) {
            return Array.from(
                { length: totalPages },
                (_, index) => index + 1
            );
        }

        if (currentPage <= 3) {
            return [1, 2, 3, 4, totalPages];
        }

        if (currentPage >= totalPages - 2) {
            return [
                1,
                totalPages - 3,
                totalPages - 2,
                totalPages - 1,
                totalPages
            ];
        }

        return [
            1,
            currentPage - 1,
            currentPage,
            currentPage + 1,
            totalPages
        ];
    }

    /* =========================================================
       PENDING REQUESTS — ELEMENTS / STATE
    ========================================================== */
    const pendingSearch =
        document.getElementById('pending-logistics-search');

    const pendingDate =
        document.getElementById('pending-logistics-date');

    const pendingReload =
        document.getElementById('pending-logistics-reload');

    const pendingBody =
        document.getElementById('pending-logistics-body');

    const pendingEmpty =
        document.getElementById('pending-logistics-empty');

    const pendingCount =
        document.getElementById('pending-logistics-count');

    const pendingPrev =
        document.getElementById('pending-logistics-prev');

    const pendingNext =
        document.getElementById('pending-logistics-next');

    const pendingPages =
        document.getElementById('pending-logistics-pages');

    const pendingItems =
        document.getElementById('pending-logistics-items');

    let pendingCurrentPage = 1;
    let pendingItemsPerPage =
        Number(pendingItems.value);

    let activePendingRequestIndex = null;

    const pendingReviewModal =
        document.getElementById('pending-review-modal');

    const pendingReviewClose =
        document.getElementById('pending-review-close');

    const pendingReviewReject =
        document.getElementById('pending-review-reject');

    const pendingReviewApprove =
        document.getElementById('pending-review-approve');

    const logisticsRejectModal =
        document.getElementById('logistics-reject-modal');

    const cancelLogisticsReject =
        document.getElementById('cancel-logistics-reject');

    const confirmLogisticsReject =
        document.getElementById('confirm-logistics-reject');

    const logisticsRejectReasonError =
        document.getElementById('logistics-reject-reason-error');

    const logisticsRejectAdditionalDetails =
        document.getElementById('logistics-reject-additional-details');

    const logisticsRejectDetailsCount =
        document.getElementById('logistics-reject-details-count');

    const logisticsFlash =
        document.getElementById('logistics-flash');

    const logisticsFlashIcon =
        document.getElementById('logistics-flash-icon');

    const logisticsFlashTitle =
        document.getElementById('logistics-flash-title');

    const logisticsFlashMessage =
        document.getElementById('logistics-flash-message');

    const logisticsFlashClose =
        document.getElementById('logistics-flash-close');

    let logisticsFlashTimer = null;

    const pendingReviewCompany =
        document.getElementById('pending-review-company');

    const pendingReviewPhone =
        document.getElementById('pending-review-phone');

    const pendingReviewEmail =
        document.getElementById('pending-review-email');

    const pendingReviewContactPerson =
        document.getElementById('pending-review-contact-person');

    const pendingReviewAddress =
        document.getElementById('pending-review-address');

    const pendingReviewDescription =
        document.getElementById('pending-review-description');

    const pendingReviewDate =
        document.getElementById('pending-review-date');

    const pendingReviewLogoBox =
        document.getElementById('pending-review-logo-box');

    const pendingReviewLogo =
        document.getElementById('pending-review-logo');

    const pendingReviewDtiButton =
        document.getElementById('pending-review-dti-button');

    const pendingReviewDtiPermit =
        document.getElementById('pending-review-dti-permit');

    const pendingReviewBusinessButton =
        document.getElementById('pending-review-business-button');

    const pendingReviewBusinessPermit =
        document.getElementById('pending-review-business-permit');

    const permitZoomModal =
        document.getElementById('permit-zoom-modal');

    const permitZoomTitle =
        document.getElementById('permit-zoom-title');

    const permitZoomImage =
        document.getElementById('permit-zoom-image');

    const permitZoomClose =
        document.getElementById('permit-zoom-close');

    function getFilteredPendingRows() {
        const query =
            normalize(pendingSearch.value);

        const selectedDate =
            pendingDate.value;

        return pendingLogisticsRequests.filter(request => {
            const searchMatch =
                !query ||
                normalize(
                    [
                        request.company,
                        request.email,
                        request.phone,
                        request.contact_person
                    ].join(' ')
                ).includes(query);

            const dateMatch =
                !selectedDate ||
                request.registered_date === selectedDate;

            return searchMatch && dateMatch;
        });
    }

    function renderPendingRows(filteredRows) {
        const start =
            (pendingCurrentPage - 1) *
            pendingItemsPerPage;

        const pageRows =
            filteredRows.slice(
                start,
                start + pendingItemsPerPage
            );

        pendingBody.innerHTML =
            pageRows.map(request => {
                const requestIndex =
                    pendingLogisticsRequests.indexOf(request);

                return `
                <tr
                    class="pending-logistics-row"
                    data-pending-index="${requestIndex}"
                    tabindex="0"
                    role="button"
                    aria-label="Review ${escapeHtml(request.company)} logistics application"
                >
                    <td>
                        <div class="pending-company-cell">
                            <span class="pending-company-avatar">
                                ${
                                    request.logo
                                        ? `<img src="${escapeHtml(request.logo)}" alt="${escapeHtml(request.company)} logo">`
                                        : ''
                                }
                            </span>

                            <span class="pending-company-name">
                                ${escapeHtml(request.company)}
                            </span>
                        </div>
                    </td>

                    <td>
                        <span class="pending-email">
                            ${escapeHtml(request.email)}
                        </span>
                    </td>

                    <td>
                        ${escapeHtml(request.phone)}
                    </td>

                    <td>
                        ${escapeHtml(request.contact_person)}
                    </td>

                    <td>
                        ${escapeHtml(request.registered_display)}
                    </td>
                </tr>
            `;
            }).join('');

        const hasRows =
            pageRows.length > 0;

        pendingBody.style.display =
            hasRows ? '' : 'none';

        pendingEmpty.style.display =
            hasRows ? 'none' : 'block';
    }

    function renderPendingPagination(filteredRows) {
        const totalPages =
            Math.max(
                1,
                Math.ceil(
                    filteredRows.length /
                    pendingItemsPerPage
                )
            );

        if (pendingCurrentPage > totalPages) {
            pendingCurrentPage = totalPages;
        }

        pendingPrev.disabled =
            pendingCurrentPage === 1;

        pendingNext.disabled =
            pendingCurrentPage === totalPages;

        pendingPages.innerHTML = '';

        let previousPage = 0;

        getPageNumbers(
            pendingCurrentPage,
            totalPages
        ).forEach(pageNumber => {

            if (
                previousPage &&
                pageNumber - previousPage > 1
            ) {
                const ellipsis =
                    document.createElement('span');

                ellipsis.textContent = '…';
                ellipsis.className =
                    'px-1 text-[11px] text-[#9CA3AF]';

                pendingPages.appendChild(
                    ellipsis
                );
            }

            const button =
                document.createElement('button');

            button.type = 'button';
            button.className =
                'pending-page-button' +
                (
                    pageNumber === pendingCurrentPage
                        ? ' active'
                        : ''
                );

            button.textContent =
                pageNumber;

            button.addEventListener(
                'click',
                function () {
                    pendingCurrentPage =
                        pageNumber;

                    renderPending();
                }
            );

            pendingPages.appendChild(
                button
            );

            previousPage =
                pageNumber;
        });
    }

    function renderPendingCount(filteredRows) {
        const start =
            (pendingCurrentPage - 1) *
            pendingItemsPerPage;

        const visible =
            filteredRows.slice(
                start,
                start + pendingItemsPerPage
            ).length;

        const hasFilters =
            Boolean(
                pendingSearch.value.trim() ||
                pendingDate.value
            );

        const total =
            hasFilters
                ? filteredRows.length
                : pendingOriginalTotalEntries;

        pendingCount.textContent =
            `Showing ${visible} out of ${total.toLocaleString()} entries`;
    }

    function makePermitPlaceholder(title, companyName) {
        const safeTitle =
            String(title || 'Permit');

        const safeCompany =
            String(companyName || 'Logistics Company');

        const svg = `
            <svg xmlns="http://www.w3.org/2000/svg" width="720" height="460" viewBox="0 0 720 460">
                <rect width="720" height="460" fill="#f8f5f2"/>
                <rect x="34" y="34" width="652" height="392" rx="8" fill="#ffffff" stroke="#cfc7c2" stroke-width="2"/>
                <rect x="70" y="76" width="580" height="56" rx="6" fill="#fff0eb"/>
                <text x="360" y="111" text-anchor="middle" font-family="Poppins, Arial, sans-serif" font-size="25" font-weight="600" fill="#8f241f">${safeTitle}</text>
                <text x="360" y="166" text-anchor="middle" font-family="Poppins, Arial, sans-serif" font-size="18" font-weight="600" fill="#231f1d">${safeCompany}</text>
                <line x1="95" y1="205" x2="625" y2="205" stroke="#ddd7d3" stroke-width="2"/>
                <line x1="95" y1="244" x2="530" y2="244" stroke="#e5e0dc" stroke-width="12" stroke-linecap="round"/>
                <line x1="95" y1="279" x2="580" y2="279" stroke="#e5e0dc" stroke-width="12" stroke-linecap="round"/>
                <line x1="95" y1="314" x2="470" y2="314" stroke="#e5e0dc" stroke-width="12" stroke-linecap="round"/>
                <circle cx="558" cy="348" r="42" fill="#fff5f1" stroke="#bd5148" stroke-width="3"/>
                <text x="558" y="354" text-anchor="middle" font-family="Arial, sans-serif" font-size="14" font-weight="700" fill="#9e231f">PERMIT</text>
                <text x="95" y="384" font-family="Poppins, Arial, sans-serif" font-size="13" fill="#938a85">Attached document preview</text>
            </svg>
        `;

        return (
            'data:image/svg+xml;charset=UTF-8,' +
            encodeURIComponent(svg)
        );
    }

    function openPermitZoom(title, imageSrc) {
        if (!imageSrc) {
            return;
        }

        permitZoomTitle.textContent =
            title;

        permitZoomImage.src =
            imageSrc;

        permitZoomImage.alt =
            `${title} enlarged preview`;

        permitZoomModal.classList.add(
            'is-open'
        );

        permitZoomModal.setAttribute(
            'aria-hidden',
            'false'
        );
    }

    function closePermitZoom() {
        permitZoomModal.classList.remove(
            'is-open'
        );

        permitZoomModal.setAttribute(
            'aria-hidden',
            'true'
        );

        permitZoomImage.removeAttribute(
            'src'
        );
    }

    function populatePendingReview(request) {
        if (!request) {
            return;
        }

        pendingReviewCompany.textContent =
            request.company || '—';

        pendingReviewPhone.textContent =
            request.phone || '—';

        pendingReviewEmail.textContent =
            request.email || '—';

        pendingReviewContactPerson.textContent =
            request.contact_person || '—';

        pendingReviewAddress.textContent =
            request.office_address || '—';

        pendingReviewDescription.textContent =
            request.description ||
            'No logistics description was provided.';

        pendingReviewDate.textContent =
            request.registered_display || '—';

        const dtiPermitSource =
            request.dti_permit ||
            makePermitPlaceholder(
                'DTI Permit',
                request.company
            );

        const businessPermitSource =
            request.business_permit ||
            makePermitPlaceholder(
                'Business Permit',
                request.company
            );

        pendingReviewDtiPermit.src =
            dtiPermitSource;

        pendingReviewDtiPermit.dataset.fullSrc =
            dtiPermitSource;

        pendingReviewBusinessPermit.src =
            businessPermitSource;

        pendingReviewBusinessPermit.dataset.fullSrc =
            businessPermitSource;

        if (request.logo) {
            pendingReviewLogo.src =
                request.logo;

            pendingReviewLogo.alt =
                `${request.company || 'Company'} logo`;

            pendingReviewLogoBox.classList.add(
                'has-image'
            );
        } else {
            pendingReviewLogo.removeAttribute(
                'src'
            );

            pendingReviewLogo.alt =
                'Company logo';

            pendingReviewLogoBox.classList.remove(
                'has-image'
            );
        }
    }

    function openPendingReview(requestIndex) {
        const numericIndex =
            Number(requestIndex);

        const request =
            pendingLogisticsRequests[
                numericIndex
            ];

        if (!request) {
            return;
        }

        activePendingRequestIndex =
            numericIndex;

        populatePendingReview(
            request
        );

        pendingReviewModal.classList.add(
            'is-open'
        );

        pendingReviewModal.setAttribute(
            'aria-hidden',
            'false'
        );

        document.body.classList.add(
            'overflow-hidden'
        );
    }

    function closePendingReview() {
        pendingReviewModal.classList.remove(
            'is-open'
        );

        pendingReviewModal.setAttribute(
            'aria-hidden',
            'true'
        );

        activePendingRequestIndex = null;

        if (
            !logisticsRejectModal.classList.contains('is-open') &&
            !permitZoomModal.classList.contains('is-open')
        ) {
            document.body.classList.remove(
                'overflow-hidden'
            );
        }
    }

    function showLogisticsFlash(type, companyName) {
        if (!logisticsFlash) {
            return;
        }

        const approved =
            type === 'approved';

        const isError =
            type === 'error';

        logisticsFlash.classList.remove(
            'approved',
            'rejected',
            'error'
        );

        logisticsFlash.classList.add(
            approved
                ? 'approved'
                : (isError ? 'error' : 'rejected')
        );

        logisticsFlashIcon.style.background =
            approved
                ? '#DDF0D6'
                : '#FFE3E5';

        logisticsFlashIcon.innerHTML =
            approved
                ? `
                    <svg
                        fill="none"
                        stroke="#28721B"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="m5 12 4 4L19 6"
                        />
                    </svg>
                `
                : `
                    <svg
                        fill="none"
                        stroke="#B3262E"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 6l12 12M18 6 6 18"
                        />
                    </svg>
                `;

        if (isError) {
            logisticsFlashTitle.textContent =
                'Action Failed';

            logisticsFlashMessage.textContent =
                companyName ||
                'Unable to update this logistics request.';
        } else {
            logisticsFlashTitle.textContent =
                approved
                    ? 'Logistics Request Approved'
                    : 'Logistics Request Rejected';

            logisticsFlashMessage.textContent =
                approved
                    ? `${companyName}'s logistics request has been approved. The company will be notified via email.`
                    : `${companyName}'s logistics request has been rejected. The company will be notified via email.`;
        }

        clearTimeout(
            logisticsFlashTimer
        );

        logisticsFlash.classList.add(
            'show'
        );

        logisticsFlashTimer =
            window.setTimeout(
                function () {
                    logisticsFlash.classList.remove(
                        'show'
                    );
                },
                3800
            );
    }

    function resetLogisticsRejectForm() {
        document
            .querySelectorAll(
                'input[name="logistics_reject_reason"]'
            )
            .forEach(input => {
                input.checked = false;
            });

        logisticsRejectAdditionalDetails.value = '';
        logisticsRejectDetailsCount.textContent = '0/300';
        logisticsRejectReasonError.classList.remove('show');
    }

    function getSelectedLogisticsRejectReason() {
        const checked =
            document.querySelector(
                'input[name="logistics_reject_reason"]:checked'
            );

        return checked
            ? checked.value
            : '';
    }

    function openLogisticsRejectModal() {
        if (
            activePendingRequestIndex === null ||
            !pendingLogisticsRequests[
                activePendingRequestIndex
            ]
        ) {
            return;
        }

        resetLogisticsRejectForm();

        logisticsRejectModal.classList.add(
            'is-open'
        );

        logisticsRejectModal.setAttribute(
            'aria-hidden',
            'false'
        );

        document.body.classList.add(
            'overflow-hidden'
        );
    }

    function closeLogisticsRejectModal() {
        logisticsRejectModal.classList.remove(
            'is-open'
        );

        logisticsRejectModal.setAttribute(
            'aria-hidden',
            'true'
        );

        if (
            !pendingReviewModal.classList.contains('is-open') &&
            !permitZoomModal.classList.contains('is-open')
        ) {
            document.body.classList.remove(
                'overflow-hidden'
            );
        }
    }

    function formatDecisionDate() {
        return new Intl.DateTimeFormat(
            'en-US',
            {
                month: 'short',
                day: 'numeric',
                year: 'numeric'
            }
        ).format(new Date());
    }

    function formatDecisionTime() {
        return new Intl.DateTimeFormat(
            'en-US',
            {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            }
        ).format(new Date());
    }

    function approveActivePendingRequest() {
        if (
            activePendingRequestIndex === null
        ) {
            return;
        }

        const request =
            pendingLogisticsRequests[
                activePendingRequestIndex
            ];

        if (!request) {
            return;
        }

        const confirmed =
            window.confirm(
                `Are you sure you want to approve ${request.company}'s logistics request?`
            );

        if (!confirmed) {
            return;
        }

        /*
         * Match Registrations: show the decision flash immediately
         * after the admin confirms the action.
         */
        showLogisticsFlash(
            'approved',
            request.company
        );

        /*
         * Front-end demo behavior.
         * Replace with the real approval endpoint when backend review
         * routes are connected.
         */
        const approvedCompany = {
            company:
                request.company,

            branches:
                0,

            riders:
                0,

            contact_number:
                request.phone || '—',

            company_email:
                request.email || '—',

            office_address:
                request.office_address || '—',

            description:
                request.description || '',

            dti_permit:
                request.dti_permit || '',

            business_permit:
                request.business_permit || '',

            partnership_since:
                new Date().getFullYear(),

            account_created_date:
                formatDecisionDate(),

            account_created_time:
                formatDecisionTime(),

            logo:
                request.logo || '',

            branches_data:
                []
        };

        pendingLogisticsRequests.splice(
            activePendingRequestIndex,
            1
        );

        logisticsCompanies.unshift(
            approvedCompany
        );

        pendingOriginalTotalEntries =
            Math.max(
                0,
                pendingOriginalTotalEntries - 1
            );

        originalTotalEntries += 1;

        pendingStatValue =
            Math.max(
                0,
                pendingStatValue - 1
            );

        acceptedStatValue += 1;
        totalStatValue += 1;

        closePendingReview();

        pendingCurrentPage = 1;
        currentPage = 1;

        renderPending();
        render();
        renderLogisticsStats();
    }

    function rejectActivePendingRequest() {
        if (
            activePendingRequestIndex === null
        ) {
            return;
        }

        const request =
            pendingLogisticsRequests[
                activePendingRequestIndex
            ];

        if (!request) {
            return;
        }

        const reason =
            getSelectedLogisticsRejectReason();

        if (!reason) {
            logisticsRejectReasonError.classList.add(
                'show'
            );

            return;
        }

        /*
         * Same immediate rejection flash behavior as Registrations.
         */
        showLogisticsFlash(
            'rejected',
            request.company
        );

        /*
         * Keep the selected rejection data on the object before it
         * leaves the pending list. This can later be sent to the API.
         */
        request.rejection_reason =
            reason;

        request.rejection_details =
            logisticsRejectAdditionalDetails.value.trim();

        request.rejected_at =
            `${formatDecisionDate()} ${formatDecisionTime()}`;

        rejectedLogisticsArchive.unshift({
            company:
                request.company || '—',

            email:
                request.email || '—',

            phone:
                request.phone || '—',

            contact_person:
                request.contact_person || '—',

            rejected_display:
                formatDecisionDate(),

            rejection_reason:
                request.rejection_reason,

            rejection_details:
                request.rejection_details
        });

        pendingLogisticsRequests.splice(
            activePendingRequestIndex,
            1
        );

        pendingOriginalTotalEntries =
            Math.max(
                0,
                pendingOriginalTotalEntries - 1
            );

        pendingStatValue =
            Math.max(
                0,
                pendingStatValue - 1
            );

        rejectedStatValue += 1;

        closeLogisticsRejectModal();
        closePendingReview();

        pendingCurrentPage = 1;

        renderPending();
        renderLogisticsStats();
        renderRejectedLogisticsArchive();
    }

    function renderPending() {
        const filteredRows =
            getFilteredPendingRows();

        const totalPages =
            Math.max(
                1,
                Math.ceil(
                    filteredRows.length /
                    pendingItemsPerPage
                )
            );

        if (pendingCurrentPage > totalPages) {
            pendingCurrentPage =
                totalPages;
        }

        renderPendingRows(
            filteredRows
        );

        renderPendingPagination(
            filteredRows
        );

        renderPendingCount(
            filteredRows
        );
    }

    pendingSearch.addEventListener(
        'input',
        function () {
            pendingCurrentPage = 1;
            renderPending();
        }
    );

    pendingDate.addEventListener(
        'change',
        function () {
            pendingCurrentPage = 1;
            renderPending();
        }
    );

    pendingItems.addEventListener(
        'change',
        function () {
            pendingItemsPerPage =
                Number(pendingItems.value);

            pendingCurrentPage = 1;
            renderPending();
        }
    );

    pendingPrev.addEventListener(
        'click',
        function () {
            if (pendingCurrentPage <= 1) {
                return;
            }

            pendingCurrentPage--;
            renderPending();
        }
    );

    pendingNext.addEventListener(
        'click',
        function () {
            const filteredRows =
                getFilteredPendingRows();

            const totalPages =
                Math.max(
                    1,
                    Math.ceil(
                        filteredRows.length /
                        pendingItemsPerPage
                    )
                );

            if (
                pendingCurrentPage >=
                totalPages
            ) {
                return;
            }

            pendingCurrentPage++;
            renderPending();
        }
    );

    pendingReload.addEventListener(
        'click',
        function () {
            pendingSearch.value = '';
            pendingDate.value = '';
            pendingCurrentPage = 1;

            pendingReload.classList.add(
                'spinning'
            );

            window.setTimeout(
                function () {
                    pendingReload.classList.remove(
                        'spinning'
                    );
                },
                550
            );

            renderPending();
        }
    );

    pendingBody.addEventListener(
        'click',
        function (event) {
            const row =
                event.target.closest(
                    '.pending-logistics-row'
                );

            if (!row) {
                return;
            }

            openPendingReview(
                row.dataset.pendingIndex
            );
        }
    );

    pendingBody.addEventListener(
        'keydown',
        function (event) {
            if (
                event.key !== 'Enter' &&
                event.key !== ' '
            ) {
                return;
            }

            const row =
                event.target.closest(
                    '.pending-logistics-row'
                );

            if (!row) {
                return;
            }

            event.preventDefault();

            openPendingReview(
                row.dataset.pendingIndex
            );
        }
    );

    if (logisticsFlashClose) {
        logisticsFlashClose.addEventListener(
            'click',
            function () {
                clearTimeout(
                    logisticsFlashTimer
                );

                logisticsFlash.classList.remove(
                    'show'
                );
            }
        );
    }

    if (rejectedLogisticsButton) {
        rejectedLogisticsButton.addEventListener(
            'click',
            openRejectedLogisticsModal
        );
    }

    if (closeRejectedLogistics) {
        closeRejectedLogistics.addEventListener(
            'click',
            closeRejectedLogisticsModal
        );
    }

    if (closeRejectedLogisticsBottom) {
        closeRejectedLogisticsBottom.addEventListener(
            'click',
            closeRejectedLogisticsModal
        );
    }

    if (rejectedLogisticsModal) {
        rejectedLogisticsModal.addEventListener(
            'click',
            function (event) {
                if (
                    event.target ===
                    rejectedLogisticsModal
                ) {
                    closeRejectedLogisticsModal();
                }
            }
        );
    }

    if (rejectedLogisticsSearch) {
        rejectedLogisticsSearch.addEventListener(
            'input',
            renderRejectedLogisticsArchive
        );
    }

    if (rejectedLogisticsDateSort) {
        rejectedLogisticsDateSort.addEventListener(
            'click',
            function () {
                rejectedLogisticsSortDirection =
                    rejectedLogisticsSortDirection === 'desc'
                        ? 'asc'
                        : 'desc';

                if (rejectedLogisticsSortArrow) {
                    rejectedLogisticsSortArrow.textContent =
                        rejectedLogisticsSortDirection === 'asc'
                            ? '↑'
                            : '↓';
                }

                renderRejectedLogisticsArchive();
            }
        );
    }

    pendingReviewClose.addEventListener(
        'click',
        closePendingReview
    );

    pendingReviewReject.addEventListener(
        'click',
        openLogisticsRejectModal
    );

    pendingReviewApprove.addEventListener(
        'click',
        approveActivePendingRequest
    );

    cancelLogisticsReject.addEventListener(
        'click',
        closeLogisticsRejectModal
    );

    confirmLogisticsReject.addEventListener(
        'click',
        rejectActivePendingRequest
    );

    logisticsRejectAdditionalDetails.addEventListener(
        'input',
        function () {
            logisticsRejectDetailsCount.textContent =
                `${logisticsRejectAdditionalDetails.value.length}/300`;
        }
    );

    document
        .querySelectorAll(
            'input[name="logistics_reject_reason"]'
        )
        .forEach(input => {
            input.addEventListener(
                'change',
                function () {
                    logisticsRejectReasonError.classList.remove(
                        'show'
                    );
                }
            );
        });

    logisticsRejectModal.addEventListener(
        'click',
        function (event) {
            if (
                event.target ===
                logisticsRejectModal
            ) {
                closeLogisticsRejectModal();
            }
        }
    );

    pendingReviewDtiButton.addEventListener(
        'click',
        function () {
            openPermitZoom(
                'DTI Permit',
                pendingReviewDtiPermit.dataset.fullSrc ||
                pendingReviewDtiPermit.src
            );
        }
    );

    pendingReviewBusinessButton.addEventListener(
        'click',
        function () {
            openPermitZoom(
                'Business Permit',
                pendingReviewBusinessPermit.dataset.fullSrc ||
                pendingReviewBusinessPermit.src
            );
        }
    );

    permitZoomClose.addEventListener(
        'click',
        closePermitZoom
    );

    permitZoomModal.addEventListener(
        'click',
        function (event) {
            if (
                event.target ===
                permitZoomModal
            ) {
                closePermitZoom();
            }
        }
    );

    pendingReviewModal.addEventListener(
        'click',
        function (event) {
            if (
                event.target ===
                pendingReviewModal
            ) {
                closePendingReview();
            }
        }
    );

    /* =========================================================
       LOGISTICS TAB — CURRENT TABLE
    ========================================================== */
    const search =
        document.getElementById('logistics-search');

    const tbody =
        document.getElementById('logistics-table-body');

    const empty =
        document.getElementById('logistics-empty');

    const count =
        document.getElementById('logistics-count');

    const prev =
        document.getElementById('logistics-prev');

    const next =
        document.getElementById('logistics-next');

    const pages =
        document.getElementById('logistics-pages');

    const items =
        document.getElementById('logistics-items');

    let currentPage = 1;
    let itemsPerPage =
        Number(items.value);

    function getFilteredRows() {
        const query =
            normalize(search.value);

        if (!query) {
            return logisticsCompanies;
        }

        return logisticsCompanies.filter(company =>
            normalize(
                [
                    company.company,
                    company.company_email,
                    company.branches,
                    company.riders
                ].join(' ')
            ).includes(query)
        );
    }

    function renderRows(filteredRows) {
        const startIndex =
            (currentPage - 1) *
            itemsPerPage;

        const pageRows =
            filteredRows.slice(
                startIndex,
                startIndex + itemsPerPage
            );

        tbody.innerHTML =
            pageRows.map(company => {
                const companyIndex =
                    logisticsCompanies.indexOf(company);

                return `
                    <tr
                        class="logistics-company-row"
                        data-company-index="${companyIndex}"
                        tabindex="0"
                        role="button"
                        aria-label="View ${escapeHtml(company.company)} company details"
                    >
                        <td>
                            <div class="logistics-company-cell">
                                <span class="logistics-company-avatar">
                                    ${
                                        company.logo
                                            ? `<img src="${escapeHtml(company.logo)}" alt="${escapeHtml(company.company)} logo">`
                                            : ''
                                    }
                                </span>

                                <span class="logistics-company-name">
                                    ${escapeHtml(company.company)}
                                </span>
                            </div>
                        </td>

                        <td>
                            <span class="logistics-number">
                                ${escapeHtml(company.branches)}
                            </span>
                        </td>

                        <td>
                            <span class="logistics-number">
                                ${escapeHtml(company.riders)}
                            </span>
                        </td>
                    </tr>
                `;
            }).join('');

        const hasRows =
            pageRows.length > 0;

        tbody.style.display =
            hasRows ? '' : 'none';

        empty.style.display =
            hasRows ? 'none' : 'block';
    }

    function renderPagination(filteredRows) {
        const totalPages =
            Math.max(
                1,
                Math.ceil(
                    filteredRows.length /
                    itemsPerPage
                )
            );

        if (currentPage > totalPages) {
            currentPage = totalPages;
        }

        prev.disabled =
            currentPage === 1;

        next.disabled =
            currentPage === totalPages;

        pages.innerHTML = '';

        let previousPageNumber = 0;

        getPageNumbers(
            currentPage,
            totalPages
        ).forEach(pageNumber => {

            if (
                previousPageNumber &&
                pageNumber -
                previousPageNumber > 1
            ) {
                const ellipsis =
                    document.createElement('span');

                ellipsis.textContent = '…';
                ellipsis.className =
                    'px-1 text-[11px] text-[#8C8784]';

                pages.appendChild(
                    ellipsis
                );
            }

            const button =
                document.createElement('button');

            button.type = 'button';
            button.className =
                'logistics-page-button' +
                (
                    pageNumber === currentPage
                        ? ' active'
                        : ''
                );

            button.textContent =
                pageNumber;

            button.addEventListener(
                'click',
                function () {
                    currentPage =
                        pageNumber;

                    render();
                }
            );

            pages.appendChild(
                button
            );

            previousPageNumber =
                pageNumber;
        });
    }

    function renderCount(filteredRows) {
        const startIndex =
            (currentPage - 1) *
            itemsPerPage;

        const visibleRows =
            filteredRows.slice(
                startIndex,
                startIndex + itemsPerPage
            ).length;

        const hasSearch =
            Boolean(
                search.value.trim()
            );

        const total =
            hasSearch
                ? filteredRows.length
                : originalTotalEntries;

        count.textContent =
            `Showing ${visibleRows} out of ${total.toLocaleString()} entries`;
    }

    function render() {
        const filteredRows =
            getFilteredRows();

        const totalPages =
            Math.max(
                1,
                Math.ceil(
                    filteredRows.length /
                    itemsPerPage
                )
            );

        if (currentPage > totalPages) {
            currentPage = totalPages;
        }

        renderRows(
            filteredRows
        );

        renderPagination(
            filteredRows
        );

        renderCount(
            filteredRows
        );
    }

    search.addEventListener(
        'input',
        function () {
            currentPage = 1;
            render();
        }
    );

    items.addEventListener(
        'change',
        function () {
            itemsPerPage =
                Number(items.value);

            currentPage = 1;
            render();
        }
    );

    prev.addEventListener(
        'click',
        function () {
            if (currentPage <= 1) {
                return;
            }

            currentPage--;
            render();
        }
    );

    next.addEventListener(
        'click',
        function () {
            const filteredRows =
                getFilteredRows();

            const totalPages =
                Math.max(
                    1,
                    Math.ceil(
                        filteredRows.length /
                        itemsPerPage
                    )
                );

            if (
                currentPage >=
                totalPages
            ) {
                return;
            }

            currentPage++;
            render();
        }
    );

    /* =========================================================
       CURRENT COMPANY DETAILS MODAL
    ========================================================== */
    const detailsModal =
        document.getElementById('logistics-details-modal');

    const detailsCloseButton =
        document.getElementById('close-logistics-details');

    const detailsTabs =
        Array.from(
            document.querySelectorAll('[data-details-tab]')
        );

    const companyDetailsPanel =
        document.getElementById('logistics-company-details-panel');

    const branchesPanel =
        document.getElementById('logistics-branches-panel');

    const detailsAvatar =
        document.getElementById('logistics-details-avatar');

    const detailsAvatarImage =
        document.getElementById('logistics-details-avatar-image');

    const detailsCompanyHeading =
        document.getElementById('logistics-details-company-name');

    const detailsCompanyPartnership =
        document.getElementById('logistics-details-company-partnership');

    const detailsCompanyName =
        document.getElementById('details-company-name');

    const detailsContactNumber =
        document.getElementById('details-contact-number');

    const detailsCompanyEmail =
        document.getElementById('details-company-email');

    const detailsOfficeAddress =
        document.getElementById('details-office-address');

    const detailsCompanyDescription =
        document.getElementById('details-company-description');

    const detailsDtiPermitButton =
        document.getElementById('details-dti-permit-button');

    const detailsDtiPermit =
        document.getElementById('details-dti-permit');

    const detailsBusinessPermitButton =
        document.getElementById('details-business-permit-button');

    const detailsBusinessPermit =
        document.getElementById('details-business-permit');

    const detailsCreatedDate =
        document.getElementById('details-created-date');

    const detailsCreatedTime =
        document.getElementById('details-created-time');

    const branchesBody =
        document.getElementById('logistics-branches-body');

    const branchesEmpty =
        document.getElementById('logistics-branches-empty');

    function switchDetailsTab(tabName) {
        detailsTabs.forEach(tab => {
            const active =
                tab.dataset.detailsTab ===
                tabName;

            tab.classList.toggle(
                'active',
                active
            );

            tab.setAttribute(
                'aria-selected',
                active ? 'true' : 'false'
            );
        });

        companyDetailsPanel.classList.toggle(
            'active',
            tabName === 'company'
        );

        branchesPanel.classList.toggle(
            'active',
            tabName === 'branches'
        );
    }

    function renderCompanyBranches(company) {
        const branches =
            Array.isArray(company?.branches_data)
                ? company.branches_data
                : [];

        if (!branches.length) {
            branchesBody.innerHTML = '';
            branchesEmpty.hidden = false;
            return;
        }

        branchesEmpty.hidden = true;

        branchesBody.innerHTML =
            branches.map(branch => `
                <tr>
                    <td>${escapeHtml(branch.name || '—')}</td>
                    <td>${escapeHtml(branch.address || '—')}</td>
                    <td>${escapeHtml(branch.contact_person || '—')}</td>
                    <td>${escapeHtml(branch.phone || '—')}</td>
                    <td>${escapeHtml(branch.riders ?? '—')}</td>
                </tr>
            `).join('');
    }

    function populateCompanyDetails(company) {
        if (!company) {
            return;
        }

        const companyName =
            company.company ||
            'Logistics Company';

        detailsCompanyHeading.textContent =
            companyName;

        detailsCompanyPartnership.textContent =
            `In partnership with ShopEase since ${company.partnership_since || 2019}`;

        detailsCompanyName.textContent =
            companyName;

        detailsContactNumber.textContent =
            company.contact_number || '—';

        detailsCompanyEmail.textContent =
            company.company_email || '—';

        detailsOfficeAddress.textContent =
            company.office_address || '—';

        detailsCompanyDescription.textContent =
            company.description ||
            'No logistics description was provided.';

        const companyDtiPermitSource =
            company.dti_permit ||
            makePermitPlaceholder(
                'DTI Permit',
                companyName
            );

        const companyBusinessPermitSource =
            company.business_permit ||
            makePermitPlaceholder(
                'Business Permit',
                companyName
            );

        detailsDtiPermit.src =
            companyDtiPermitSource;

        detailsDtiPermit.dataset.fullSrc =
            companyDtiPermitSource;

        detailsBusinessPermit.src =
            companyBusinessPermitSource;

        detailsBusinessPermit.dataset.fullSrc =
            companyBusinessPermitSource;

        detailsCreatedDate.textContent =
            company.account_created_date ||
            'May 26, 2026';

        detailsCreatedTime.textContent =
            company.account_created_time ||
            '6:45 PM';

        if (company.logo) {
            detailsAvatarImage.src =
                company.logo;

            detailsAvatarImage.alt =
                `${companyName} logo`;

            detailsAvatar.classList.add(
                'has-image'
            );
        } else {
            detailsAvatarImage.removeAttribute(
                'src'
            );

            detailsAvatarImage.alt =
                'Company logo';

            detailsAvatar.classList.remove(
                'has-image'
            );
        }

        renderCompanyBranches(
            company
        );
    }

    function openCompanyDetails(companyIndex) {
        const company =
            logisticsCompanies[
                Number(companyIndex)
            ];

        if (!company) {
            return;
        }

        populateCompanyDetails(
            company
        );

        switchDetailsTab(
            'company'
        );

        detailsModal.classList.add(
            'is-open'
        );

        detailsModal.setAttribute(
            'aria-hidden',
            'false'
        );

        document.body.classList.add(
            'overflow-hidden'
        );
    }

    function closeCompanyDetails() {
        detailsModal.classList.remove(
            'is-open'
        );

        detailsModal.setAttribute(
            'aria-hidden',
            'true'
        );

        document.body.classList.remove(
            'overflow-hidden'
        );
    }

    detailsTabs.forEach(tab => {
        tab.addEventListener(
            'click',
            function () {
                switchDetailsTab(
                    tab.dataset.detailsTab
                );
            }
        );
    });

    detailsCloseButton.addEventListener(
        'click',
        closeCompanyDetails
    );

    detailsDtiPermitButton.addEventListener(
        'click',
        function () {
            openPermitZoom(
                'DTI Permit',
                detailsDtiPermit.dataset.fullSrc ||
                detailsDtiPermit.src
            );
        }
    );

    detailsBusinessPermitButton.addEventListener(
        'click',
        function () {
            openPermitZoom(
                'Business Permit',
                detailsBusinessPermit.dataset.fullSrc ||
                detailsBusinessPermit.src
            );
        }
    );

    detailsModal.addEventListener(
        'click',
        function (event) {
            if (
                event.target ===
                detailsModal
            ) {
                closeCompanyDetails();
            }
        }
    );

    tbody.addEventListener(
        'click',
        function (event) {
            const row =
                event.target.closest(
                    '.logistics-company-row'
                );

            if (!row) {
                return;
            }

            openCompanyDetails(
                row.dataset.companyIndex
            );
        }
    );

    tbody.addEventListener(
        'keydown',
        function (event) {
            if (
                event.key !== 'Enter' &&
                event.key !== ' '
            ) {
                return;
            }

            const row =
                event.target.closest(
                    '.logistics-company-row'
                );

            if (!row) {
                return;
            }

            event.preventDefault();

            openCompanyDetails(
                row.dataset.companyIndex
            );
        }
    );

    window.addEventListener(
        'keydown',
        function (event) {
            if (event.key !== 'Escape') {
                return;
            }

            if (
                permitZoomModal.classList.contains(
                    'is-open'
                )
            ) {
                closePermitZoom();
                return;
            }

            if (
                logisticsRejectModal.classList.contains(
                    'is-open'
                )
            ) {
                closeLogisticsRejectModal();
                return;
            }

            if (
                rejectedLogisticsModal &&
                !rejectedLogisticsModal.classList.contains(
                    'hidden'
                )
            ) {
                closeRejectedLogisticsModal();
                return;
            }

            if (
                pendingReviewModal.classList.contains(
                    'is-open'
                )
            ) {
                closePendingReview();
                return;
            }

            if (
                detailsModal.classList.contains(
                    'is-open'
                )
            ) {
                closeCompanyDetails();
            }
        }
    );

    /* =========================================================
       INITIAL RENDER
    ========================================================== */
    switchMainTab('pending');
    renderPending();
    render();
    renderLogisticsStats();
    renderRejectedLogisticsArchive();
});
</script>
@endpush
