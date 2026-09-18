@extends('layouts.admin')

@section('page-title', 'Platform Settings')

@section('content')

@php
    /*
    |--------------------------------------------------------------------------
    | DEMO / FALLBACK DATA
    |--------------------------------------------------------------------------
    | Replace these arrays later with controller data.
    */

    $announcements = $announcements ?? [
        [
            'title' => 'Scheduled Maintenance Notice',
            'description' => 'We will be conducting a system maintenance on June 2, 2026 from 12:00 AM to 4:00 AM. Some features may be unavailable...',
            'audience' => 'All Users',
            'status' => 'Published',
            'date' => 'August 8, 2026',
            'time' => '6:23 AM',
            'icon' => 'megaphone',
            'theme' => 'red',
        ],
        [
            'title' => 'New Feature: Order Tracking',
            'description' => 'You can now track your orders in real-time! Go to “My Orders” to check your delivery status anytime.',
            'audience' => 'All Users',
            'status' => 'Published',
            'date' => 'August 8, 2026',
            'time' => '6:23 AM',
            'icon' => 'box',
            'theme' => 'yellow',
        ],
        [
            'title' => 'Beware of Scammers',
            'description' => 'Shop safely. Do not share your OTP or account details with anyone. ShopEase will never ask for this information.',
            'audience' => 'All Users',
            'status' => 'Published',
            'date' => 'August 8, 2026',
            'time' => '6:23 AM',
            'icon' => 'shield',
            'theme' => 'blue',
        ],
        [
            'title' => 'Policy Update: Prohibited Items',
            'description' => 'We’ve updated our list of prohibited items. Please review the updated policy for more information.',
            'audience' => 'All Users',
            'status' => 'Published',
            'date' => 'August 8, 2026',
            'time' => '6:23 AM',
            'icon' => 'document',
            'theme' => 'purple',
        ],
    ];

    $policies = $policies ?? [
        [
            'title' => 'Prohibited and Restricted Items',
            'description' => 'We will be conducting a system maintenance on June 2, 2026 from 12:00 AM to 4:00 AM. Some features may be unavailable...',
            'audience' => 'All Users',
            'status' => 'Published',
            'date' => 'August 8, 2026',
            'time' => '6:23 AM',
            'icon' => 'shield',
            'theme' => 'red',
        ],
        [
            'title' => 'User Code of Conduct',
            'description' => 'You can now track your orders in real-time! Go to “My Orders” to check your delivery status anytime.',
            'audience' => 'All Users',
            'status' => 'Published',
            'date' => 'August 8, 2026',
            'time' => '6:23 AM',
            'icon' => 'document',
            'theme' => 'yellow',
        ],
        [
            'title' => 'Shipping and Delivery Policy',
            'description' => 'Shop safely. Do not share your OTP or account details with anyone. ShopEase will never ask for this information.',
            'audience' => 'All Users',
            'status' => 'Published',
            'date' => 'August 8, 2026',
            'time' => '6:23 AM',
            'icon' => 'truck',
            'theme' => 'blue',
        ],
        [
            'title' => 'Privacy Policy',
            'description' => 'We’ve updated our list of prohibited items. Please review the updated policy for more information.',
            'audience' => 'All Users',
            'status' => 'Published',
            'date' => 'August 8, 2026',
            'time' => '6:23 AM',
            'icon' => 'document',
            'theme' => 'purple',
        ],
    ];
@endphp

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

    #admin-content,
    #admin-content * {
        font-family: 'Poppins', sans-serif;
        box-sizing: border-box;
    }

    #admin-content {
        color: #17120F;
    }

    /* =========================================================
       PAGE TYPOGRAPHY
       Matches the compact sizing/weight hierarchy of Registrations.
    ========================================================== */
    .platform-page-title {
        margin: 0 0 24px;
        color: #17120F;
        font-size: 21px;
        line-height: 1.2;
        font-weight: 600;
    }

    .platform-toolbar {
        min-height: 50px;
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 16px;
        padding: 0 0 0 14px;
    }

    .platform-tabs {
        display: flex;
        align-items: flex-end;
        gap: 26px;
    }

    .platform-tab {
        position: relative;
        min-width: 150px;
        height: 46px;
        padding: 0 2px;
        border: 0;
        background: transparent;
        color: #8C8784;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        line-height: 1;
        font-weight: 600;
        cursor: pointer;
        transition: color .15s ease;
    }

    .platform-tab::after {
        content: '';
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        height: 2px;
        border-radius: 999px 999px 0 0;
        background: transparent;
    }

    .platform-tab.active {
        color: #17120F;
        font-weight: 600;
    }

    .platform-tab.active::after {
        background: #B3262E;
    }

    .platform-tab:hover {
        color: #5E5753;
    }

    .platform-create-button {
        height: 36px;
        margin: 0 0 7px;
        padding: 0 13px;
        border: 0;
        border-radius: 8px;
        background: #9E231F;
        color: #FFFFFF;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        font-size: 12px;
        line-height: 1;
        font-weight: 500;
        cursor: pointer;
        box-shadow: 0 1px 2px rgba(72, 22, 19, .08);
        transition:
            background .15s ease,
            transform .15s ease,
            box-shadow .15s ease;
        white-space: nowrap;
    }

    .platform-create-button:hover {
        background: #7B1B1B;
        box-shadow: 0 4px 10px rgba(123, 27, 27, .16);
    }

    .platform-create-button:active {
        transform: scale(.98);
    }

    .platform-create-button svg {
        width: 15px;
        height: 15px;
        flex: 0 0 15px;
    }

    /* =========================================================
       CONTENT BOARD
    ========================================================== */
    .platform-board {
        min-height: 650px;
        padding: 14px;
        border: 1px solid #F0E9E6;
        border-radius: 14px;
        background: #FFFFFF;
        box-shadow: 0 1px 3px rgba(0, 0, 0, .04);
    }

    .platform-panel {
        display: none;
    }

    .platform-panel.active {
        display: block;
    }

    .platform-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    /* =========================================================
       ANNOUNCEMENT / POLICY CARDS
       Same compact spacing + hover movement as Registrations.
    ========================================================== */
    .platform-card,
    .platform-empty-card {
        min-width: 0;
        min-height: 292px;
        border: 1px solid #F0E9E6;
        border-radius: 14px;
        background: #FFFFFF;
        box-shadow: 0 2px 12px rgba(42, 20, 15, .05);
        overflow: hidden;
    }

    .platform-card {
        display: flex;
        flex-direction: column;
        transition:
            transform .30s ease,
            box-shadow .30s ease,
            border-color .30s ease;
    }

    .platform-card:hover {
        transform: translateY(-4px);
        border-color: #E9A3A3;
        box-shadow:
            0 4px 6px -1px rgba(0, 0, 0, .10),
            0 2px 4px -2px rgba(0, 0, 0, .10);
    }

    .platform-card-main {
        flex: 1 1 auto;
        padding: 14px 14px 10px;
    }

    .platform-card-icon {
        width: 70px;
        height: 70px;
        margin: 0 auto 10px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform .30s ease;
    }

    .platform-card:hover .platform-card-icon {
        transform: translateY(-1px);
    }

    .platform-card-icon svg {
        width: 38px;
        height: 38px;
    }

    /* Uploaded announcement banner becomes a landscape cover photo. */
    .platform-card-cover {
        width: calc(100% + 28px);
        height: 118px;
        margin: -14px -14px 10px;
        overflow: hidden;
        background: #F3F1F0;
    }

    .platform-card-cover img {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
        object-position: center;
    }

    .theme-red {
        background: #FFD1D4;
        color: #C8121B;
    }

    .theme-yellow {
        background: #F8E8AE;
        color: #B56516;
    }

    .theme-blue {
        background: #CADDF8;
        color: #0C3F58;
    }

    .theme-purple {
        background: #D8C1F0;
        color: #48208D;
    }

    .platform-card-title {
        min-height: 36px;
        margin: 0 0 8px;
        color: #17120F;
        text-align: center;
        font-size: 13px;
        line-height: 1.3;
        font-weight: 600;
        display: flex;
        align-items: flex-start;
        justify-content: center;
    }

    .platform-card-description {
        margin: 0;
        color: #57514E;
        font-size: 12px;
        line-height: 1.45;
        font-weight: 400;
    }

    .platform-card-footer {
        border-top: 1px solid #E5E7EB;
        background: #FFFFFF;
    }

    .platform-card-audience {
        min-height: 32px;
        padding: 7px 12px;
        display: flex;
        align-items: center;
        gap: 6px;
        color: #8C8784;
        font-size: 11px;
        line-height: 1.2;
        font-weight: 400;
    }

    .platform-card-audience svg {
        width: 13px;
        height: 13px;
        color: #17120F;
        flex: 0 0 13px;
    }

    .platform-card-meta {
        min-height: 42px;
        padding: 6px 9px 7px 12px;
        display: grid;
        grid-template-columns: auto 1fr auto;
        gap: 7px;
        align-items: center;
    }

    .platform-status {
        min-width: 66px;
        height: 22px;
        padding: 0 9px;
        border-radius: 999px;
        background: #D9EBD8;
        color: #3D8736;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        line-height: 1;
        font-weight: 500;
    }

    .platform-card-date {
        color: #8C8784;
        text-align: right;
        font-size: 10px;
        line-height: 1.25;
        font-weight: 400;
    }

    .platform-more {
        width: 28px;
        height: 28px;
        padding: 0;
        border: 0;
        border-radius: 6px;
        background: transparent;
        color: #57514E;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition:
            background .15s ease,
            color .15s ease,
            transform .15s ease;
    }

    .platform-more:hover {
        background: #FFF0EC;
        color: #7B1B1B;
    }

    .platform-more:active {
        transform: scale(.95);
    }

    .platform-more svg {
        width: 17px;
        height: 17px;
    }

    .platform-more-wrap {
        position: relative;
        width: 28px;
        height: 28px;
    }

    .platform-action-menu {
        position: absolute;
        right: 0;
        bottom: 34px;
        z-index: 30;
        width: 112px;
        padding: 5px;
        border: 1px solid #E9E3E0;
        border-radius: 9px;
        background: #FFFFFF;
        box-shadow: 0 10px 24px rgba(42, 20, 15, .14);
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transform: translateY(5px);
        transition:
            opacity .14s ease,
            visibility .14s ease,
            transform .14s ease;
    }

    .platform-action-menu.is-open {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        transform: translateY(0);
    }

    .platform-action-item {
        width: 100%;
        min-height: 32px;
        padding: 0 9px;
        border: 0;
        border-radius: 6px;
        background: transparent;
        display: flex;
        align-items: center;
        gap: 8px;
        color: #57514E;
        font-size: 11px;
        line-height: 1;
        font-weight: 500;
        text-align: left;
        cursor: pointer;
        transition:
            background .15s ease,
            color .15s ease;
    }

    .platform-action-item:hover {
        background: #FFF7F4;
        color: #7B1B1B;
    }

    .platform-action-item.delete {
        color: #B3262E;
    }

    .platform-action-item.delete:hover {
        background: #FFF0F0;
        color: #971D25;
    }

    .platform-action-item svg {
        width: 14px;
        height: 14px;
        flex: 0 0 14px;
    }

    .platform-empty-card {
        min-height: 292px;
        opacity: .72;
    }

    @media (max-width: 1250px) {
        .platform-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 760px) {
        .platform-page-title {
            margin-bottom: 20px;
            font-size: 21px;
        }

        .platform-toolbar {
            padding-left: 0;
            align-items: stretch;
            flex-direction: column;
            gap: 10px;
        }

        .platform-tabs {
            gap: 8px;
            overflow-x: auto;
        }

        .platform-tab {
            min-width: 140px;
            height: 44px;
            font-size: 14px;
        }

        .platform-create-button {
            align-self: flex-end;
        }

        .platform-grid {
            grid-template-columns: 1fr;
        }

        .platform-board {
            min-height: 0;
        }

        .platform-empty-card {
            display: none;
        }
    }


    /* =========================================================
       CREATE ANNOUNCEMENT / ADD POLICY MODALS
       Compact versions based on the supplied references.
    ========================================================== */
    .platform-modal-backdrop {
        position: fixed;
        inset: 0;
        z-index: 220;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 18px;
        background: rgba(0, 0, 0, .34);
        backdrop-filter: blur(2px);
    }

    .platform-modal-backdrop.is-open {
        display: flex;
    }

    .platform-modal {
        width: min(860px, calc(100vw - 32px));
        max-height: calc(100vh - 28px);
        overflow-y: auto;
        border: 1px solid rgba(255, 255, 255, .9);
        border-radius: 22px;
        background: #FFFFFF;
        box-shadow: 0 24px 70px rgba(42, 20, 15, .24);
        scrollbar-width: none;
    }

    .platform-modal::-webkit-scrollbar {
        display: none;
    }

    .platform-policy-modal {
        width: min(720px, calc(100vw - 32px));
    }

    .platform-modal-inner {
        padding: 20px 22px 18px;
    }

    .platform-modal-heading {
        margin: 0;
        color: #17120F;
        font-size: 18px;
        line-height: 1.2;
        font-weight: 600;
    }

    .platform-modal-subtitle {
        margin: 6px 0 0;
        color: #9CA3AF;
        font-size: 12px;
        line-height: 1.45;
        font-weight: 400;
    }

    .announcement-modal-grid {
        margin-top: 17px;
        display: grid;
        grid-template-columns: 1.05fr .95fr;
        gap: 16px;
        align-items: start;
    }

    .announcement-modal-left,
    .announcement-modal-right {
        min-width: 0;
    }

    .platform-form-group {
        margin-bottom: 11px;
    }

    .platform-form-label {
        display: block;
        margin: 0 0 6px;
        color: #17120F;
        font-size: 11px;
        line-height: 1.2;
        font-weight: 500;
    }

    .platform-required {
        color: #D71920;
    }

    .platform-muted-inline {
        color: #9CA3AF;
        font-weight: 400;
    }

    .platform-option-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .platform-option-card {
        position: relative;
        min-height: 106px;
        padding: 11px 12px 10px;
        border: 1px solid #D9D6D4;
        border-radius: 12px;
        background: #FFFFFF;
        cursor: pointer;
        transition:
            border-color .15s ease,
            background .15s ease,
            box-shadow .15s ease;
    }

    .platform-option-card:hover {
        border-color: #D9A19F;
        background: #FFFBFA;
    }

    .platform-option-card.is-selected {
        border-color: #D71920;
        box-shadow: 0 0 0 1px rgba(215, 25, 32, .06);
    }

    .platform-option-card input {
        position: absolute;
        top: 11px;
        left: 11px;
        width: 16px;
        height: 16px;
        margin: 0;
        accent-color: #D71920;
        cursor: pointer;
    }

    .platform-option-icon {
        width: 46px;
        height: 46px;
        margin: 0 0 8px 24px;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .platform-option-icon svg {
        width: 23px;
        height: 23px;
    }

    .announcement-option-icon {
        background: #FFD4D7;
        color: #C8121B;
    }

    .policy-option-icon {
        background: #DCC7F3;
        color: #51229C;
    }

    .platform-option-title {
        margin: 0 0 4px 24px;
        color: #17120F;
        font-size: 13px;
        line-height: 1.25;
        font-weight: 600;
    }

    .platform-option-description {
        margin: 0 0 0 24px;
        color: #9CA3AF;
        font-size: 11px;
        line-height: 1.45;
        font-weight: 400;
    }

    .platform-input-wrap,
    .platform-textarea-wrap {
        position: relative;
    }

    .platform-input,
    .platform-select,
    .platform-textarea {
        width: 100%;
        border: 1px solid #D9D6D4;
        border-radius: 8px;
        background: #FFFFFF;
        outline: none;
        color: #57514E;
        font-size: 11px;
        line-height: 1.45;
        font-weight: 400;
        transition:
            border-color .15s ease,
            box-shadow .15s ease;
    }

    .platform-input,
    .platform-select {
        height: 36px;
        padding: 0 38px 0 10px;
    }

    .platform-select {
        padding-right: 34px;
        cursor: pointer;
    }

    .platform-textarea {
        min-height: 88px;
        padding: 9px 48px 22px 10px;
        resize: none;
    }

    .platform-textarea.policy-description {
        min-height: 82px;
    }

    .platform-textarea.policy-content {
        min-height: 90px;
    }

    .platform-input::placeholder,
    .platform-textarea::placeholder {
        color: #B4B0AE;
    }

    .platform-input:focus,
    .platform-select:focus,
    .platform-textarea:focus {
        border-color: #9E231F;
        box-shadow: 0 0 0 2px rgba(158, 35, 31, .08);
    }

    .platform-counter {
        position: absolute;
        right: 9px;
        bottom: 7px;
        color: #17120F;
        font-size: 10px;
        line-height: 1;
        font-weight: 400;
    }

    .platform-audience-row {
        display: flex;
        flex-wrap: wrap;
        gap: 18px;
        align-items: center;
    }

    .platform-radio-inline,
    .platform-checkbox-inline {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        color: #57514E;
        font-size: 11px;
        line-height: 1;
        font-weight: 400;
        cursor: pointer;
    }

    .platform-radio-inline input {
        width: 16px;
        height: 16px;
        margin: 0;
        accent-color: #D71920;
    }

    .platform-checkbox-inline input {
        width: 15px;
        height: 15px;
        margin: 0;
        accent-color: #9E231F;
    }

    .platform-datetime-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }

    /* Keep the native calendar / time icons close to the right edge. */
    .platform-datetime-row input[type="date"],
    .platform-datetime-row input[type="time"] {
        padding-left: 10px;
        padding-right: 8px;
    }

    .platform-datetime-row input[type="date"]::-webkit-calendar-picker-indicator,
    .platform-datetime-row input[type="time"]::-webkit-calendar-picker-indicator {
        margin-left: auto;
        margin-right: 0;
        padding: 0;
        cursor: pointer;
    }

    .announcement-schedule-fields {
        display: none;
        margin-top: 11px;
        padding-top: 11px;
        border-top: 1px solid #F0E9E6;
    }

    .announcement-schedule-fields.is-visible {
        display: block;
    }

    .platform-help-text {
        margin: 6px 0 0;
        color: #9CA3AF;
        font-size: 10px;
        line-height: 1.4;
        font-weight: 400;
    }

    .platform-status-grid .platform-option-card {
        min-height: 74px;
    }

    .platform-status-grid .platform-option-title {
        margin-top: 1px;
    }

    .platform-banner-box {
        min-height: 126px;
        padding: 14px;
        border: 1px solid #F2C6C4;
        border-radius: 12px;
        background: #FFF5F5;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        cursor: pointer;
        transition:
            border-color .15s ease,
            background .15s ease;
    }

    .platform-banner-box:hover,
    .platform-banner-box.is-dragover {
        border-color: #D97A76;
        background: #FFF0F0;
    }

    .platform-banner-icon {
        width: 36px;
        height: 36px;
        margin-bottom: 7px;
        border-radius: 999px;
        background: #FFD8DA;
        color: #B52B27;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .platform-banner-icon svg {
        width: 20px;
        height: 20px;
    }

    .platform-banner-title {
        margin: 0;
        color: #17120F;
        font-size: 11px;
        line-height: 1.3;
        font-weight: 600;
    }

    .platform-banner-help {
        margin: 4px 0 0;
        color: #9CA3AF;
        font-size: 10px;
        line-height: 1.4;
        font-weight: 400;
    }

    .platform-banner-file {
        margin-top: 6px;
        color: #7B1B1B;
        font-size: 10px;
        line-height: 1.3;
        font-weight: 500;
    }

    .announcement-preview-section {
        margin-top: 14px;
    }

    .announcement-preview-label {
        margin: 0 0 8px 2px;
        color: #17120F;
        font-size: 11px;
        line-height: 1.2;
        font-weight: 500;
    }

    /*
     * Realistic preview:
     * mirrors the actual announcement cards shown on the page.
     */
    .announcement-preview-card {
        min-height: 246px;
        border: 1px solid #F0E9E6;
        border-radius: 14px;
        background: #FFFFFF;
        box-shadow: 0 2px 12px rgba(42, 20, 15, .08);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .announcement-preview-main {
        min-width: 0;
        flex: 1 1 auto;
        padding: 14px 14px 10px;
        overflow: hidden;
    }

    .announcement-preview-top {
        display: block;
    }

    .announcement-preview-icon {
        width: 70px;
        height: 70px;
        margin: 0 auto 10px;
        border-radius: 10px;
        background: #FFD1D4;
        color: #C8121B;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .announcement-preview-icon svg {
        width: 38px;
        height: 38px;
        display: block;
    }

    .announcement-preview-icon.has-cover-photo {
        width: calc(100% + 28px);
        height: 118px;
        margin: -14px -14px 10px;
        border-radius: 13px 13px 0 0;
        overflow: hidden;
        background: #F3F1F0 !important;
        color: transparent !important;
        display: block;
    }

    .announcement-preview-icon.has-cover-photo img {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
        object-position: center;
    }

    .announcement-preview-title {
        min-height: 36px;
        margin: 0 0 8px;
        color: #17120F;
        text-align: center;
        font-size: 13px;
        line-height: 1.3;
        font-weight: 600;
        display: flex;
        align-items: flex-start;
        justify-content: center;
    }

    .announcement-preview-description {
        width: 100%;
        max-width: 100%;
        margin: 0;
        color: #57514E;
        text-align: center;
        font-size: 12px;
        line-height: 1.45;
        font-weight: 400;
        white-space: normal;
        overflow-wrap: anywhere;
        word-break: break-word;
    }

    .announcement-preview-footer {
        border-top: 1px solid #E5E7EB;
        background: #FFFFFF;
    }

    .announcement-preview-audience {
        min-height: 32px;
        padding: 7px 12px;
        display: flex;
        align-items: center;
        gap: 6px;
        color: #8C8784;
        font-size: 11px;
        line-height: 1.2;
        font-weight: 400;
    }

    .announcement-preview-audience svg {
        width: 13px;
        height: 13px;
        color: #17120F;
        flex: 0 0 13px;
    }

    .announcement-preview-meta {
        min-height: 42px;
        padding: 6px 9px 7px 12px;
        display: grid;
        grid-template-columns: auto 1fr auto;
        gap: 7px;
        align-items: center;
    }

    .announcement-preview-date {
        color: #8C8784;
        text-align: right;
        font-size: 10px;
        line-height: 1.25;
        font-weight: 400;
    }

    .announcement-preview-more {
        width: 28px;
        height: 28px;
        border: 0;
        border-radius: 6px;
        background: transparent;
        color: #57514E;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }

    .announcement-preview-more svg {
        width: 17px;
        height: 17px;
    }

    .platform-modal-actions {
        position: sticky;
        bottom: 0;
        z-index: 5;
        margin-top: 14px;
        padding-top: 12px;
        border-top: 1px solid #F0E9E6;
        background: #FFFFFF;
        display: flex;
        justify-content: flex-end;
        gap: 9px;
    }

    .platform-modal-button {
        min-width: 106px;
        height: 36px;
        padding: 0 20px;
        border-radius: 8px;
        font-size: 11px;
        line-height: 1;
        font-weight: 600;
        cursor: pointer;
        transition:
            background .15s ease,
            color .15s ease,
            transform .15s ease;
    }

    .platform-modal-button:active {
        transform: scale(.98);
    }

    .platform-modal-cancel {
        border: 1px solid #17120F;
        background: #FFFFFF;
        color: #17120F;
    }

    .platform-modal-cancel:hover {
        background: #F7F7F7;
    }

    .platform-modal-primary {
        border: 1px solid #9E231F;
        background: #9E231F;
        color: #FFFFFF;
    }

    .platform-modal-primary:hover {
        background: #7B1B1B;
    }

    /* Policy modal */
    .policy-modal-form {
        margin-top: 22px;
    }

    .policy-modal-two-col {
        display: grid;
        grid-template-columns: 1.15fr .95fr;
        gap: 14px;
    }

    .policy-modal-datetime {
        max-width: 405px;
    }

    @media (max-width: 900px) {
        .announcement-modal-grid {
            grid-template-columns: 1fr;
        }

        .announcement-preview-section {
            margin-top: 10px;
        }

        .platform-modal {
            width: min(680px, calc(100vw - 28px));
        }
    }

    @media (max-width: 560px) {
        .platform-modal-backdrop {
            padding: 10px;
        }

        .platform-modal,
        .platform-policy-modal {
            width: calc(100vw - 20px);
            max-height: calc(100vh - 20px);
            border-radius: 16px;
        }

        .platform-modal-inner {
            padding: 20px 16px 18px;
        }

        .platform-option-grid,
        .platform-datetime-row,
        .policy-modal-two-col {
            grid-template-columns: 1fr;
        }

        .platform-audience-row {
            gap: 12px;
        }

        .platform-modal-actions {
            flex-direction: column-reverse;
        }

        .platform-modal-button {
            width: 100%;
        }
    }

</style>

<div
    id="admin-content"
    class="ml-60 pt-[110px] pl-5 pb-7 min-h-screen transition-all duration-300"
>

    <div class="platform-toolbar">

        <div
            class="platform-tabs"
            role="tablist"
            aria-label="Platform Settings"
        >
            <button
                id="announcements-tab"
                type="button"
                class="platform-tab active"
                role="tab"
                aria-selected="true"
                aria-controls="announcements-panel"
                data-platform-tab="announcements"
            >
                Announcements
            </button>

            <button
                id="policies-tab"
                type="button"
                class="platform-tab"
                role="tab"
                aria-selected="false"
                aria-controls="policies-panel"
                data-platform-tab="policies"
            >
                Platform Policies
            </button>
        </div>

        <button
            id="platform-create-button"
            type="button"
            class="platform-create-button"
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
                    d="M12 5v14M5 12h14"
                />
            </svg>

            <span id="platform-create-label">
                Create Announcement
            </span>
        </button>

    </div>

    <section class="platform-board">

        <!-- =====================================================
             ANNOUNCEMENTS PANEL
        ====================================================== -->
        <div
            id="announcements-panel"
            class="platform-panel active"
            role="tabpanel"
            aria-labelledby="announcements-tab"
        >
            <div
                id="announcements-grid"
                class="platform-grid"
            >

                @foreach ($announcements as $item)
                    <article class="platform-card">

                        <div class="platform-card-main">

                            <div class="platform-card-icon theme-{{ $item['theme'] }}">

                                @if ($item['icon'] === 'megaphone')
                                    <svg viewBox="0 0 64 64" fill="currentColor" aria-hidden="true">
                                        <path d="M13 29v8c0 4.4 3.6 8 8 8h4l4 11h8l-4-12c6-1 13-4 21-10V20c-10 8-19 10-27 10h-6c-4.4 0-8 3.6-8 8Z"/>
                                        <path d="M47 22h5v10h-5z"/>
                                    </svg>
                                @elseif ($item['icon'] === 'box')
                                    <svg viewBox="0 0 64 64" fill="currentColor" aria-hidden="true">
                                        <path d="M11 20 32 9l21 11-21 11L11 20Zm3 7 15 8v20l-15-8V27Zm21 8 15-8v20l-15 8V35Z"/>
                                    </svg>
                                @elseif ($item['icon'] === 'shield')
                                    <svg viewBox="0 0 64 64" fill="currentColor" aria-hidden="true">
                                        <path d="M32 7 11 15v15c0 13 8 23 21 29 13-6 21-16 21-29V15L32 7Zm-2 43c-8-5-12-11-13-19V20l13-5v35Zm4 0V15l13 5v11c-1 8-5 14-13 19Z"/>
                                    </svg>
                                @else
                                    <svg viewBox="0 0 64 64" fill="currentColor" aria-hidden="true">
                                        <path d="M18 8h24l8 8v40H18V8Zm22 3v8h8l-8-8ZM25 28h18v4H25v-4Zm0 9h18v4H25v-4Zm0 9h12v4H25v-4Z"/>
                                    </svg>
                                @endif

                            </div>

                            <h3 class="platform-card-title">
                                {{ $item['title'] }}
                            </h3>

                            <p class="platform-card-description">
                                {{ $item['description'] }}
                            </p>

                        </div>

                        <div class="platform-card-footer">

                            <div class="platform-card-audience">
                                <svg
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                    aria-hidden="true"
                                >
                                    <circle cx="12" cy="12" r="9" stroke-width="1.8"/>
                                    <path d="M3 12h18M12 3c2.3 2.6 3.5 5.6 3.5 9S14.3 18.4 12 21c-2.3-2.6-3.5-5.6-3.5-9S9.7 5.6 12 3Z" stroke-width="1.6"/>
                                </svg>

                                <span>{{ $item['audience'] }}</span>
                            </div>

                            <div class="platform-card-meta">

                                <span class="platform-status">
                                    {{ $item['status'] }}
                                </span>

                                <div class="platform-card-date">
                                    <div>{{ $item['date'] }}</div>
                                    <div>{{ $item['time'] }}</div>
                                </div>

                                <button
                                    type="button"
                                    class="platform-more"
                                    aria-label="More announcement actions"
                                >
                                    <svg
                                        fill="currentColor"
                                        viewBox="0 0 24 24"
                                        aria-hidden="true"
                                    >
                                        <circle cx="12" cy="5" r="2"/>
                                        <circle cx="12" cy="12" r="2"/>
                                        <circle cx="12" cy="19" r="2"/>
                                    </svg>
                                </button>

                            </div>

                        </div>

                    </article>
                @endforeach

                @for ($i = count($announcements); $i < 8; $i++)
                    <div
                        class="platform-empty-card"
                        aria-hidden="true"
                    ></div>
                @endfor

            </div>
        </div>


        <!-- =====================================================
             PLATFORM POLICIES PANEL
        ====================================================== -->
        <div
            id="policies-panel"
            class="platform-panel"
            role="tabpanel"
            aria-labelledby="policies-tab"
        >
            <div class="platform-grid">

                @foreach ($policies as $item)
                    <article class="platform-card">

                        <div class="platform-card-main">

                            <div class="platform-card-icon theme-{{ $item['theme'] }}">

                                @if ($item['icon'] === 'shield')
                                    <svg viewBox="0 0 64 64" fill="currentColor" aria-hidden="true">
                                        <path d="M32 7 11 15v15c0 13 8 23 21 29 13-6 21-16 21-29V15L32 7Zm-2 43c-8-5-12-11-13-19V20l13-5v35Zm4 0V15l13 5v11c-1 8-5 14-13 19Z"/>
                                    </svg>
                                @elseif ($item['icon'] === 'truck')
                                    <svg viewBox="0 0 64 64" fill="currentColor" aria-hidden="true">
                                        <path d="M8 17h29v24H8V17Zm31 8h8l9 10v6H39V25Zm-24 20a6 6 0 1 1 0 12 6 6 0 0 1 0-12Zm32 0a6 6 0 1 1 0 12 6 6 0 0 1 0-12ZM39 29v9h12l-7-9h-5Z"/>
                                    </svg>
                                @else
                                    <svg viewBox="0 0 64 64" fill="currentColor" aria-hidden="true">
                                        <path d="M18 8h24l8 8v40H18V8Zm22 3v8h8l-8-8ZM25 28h18v4H25v-4Zm0 9h18v4H25v-4Zm0 9h12v4H25v-4Z"/>
                                    </svg>
                                @endif

                            </div>

                            <h3 class="platform-card-title">
                                {{ $item['title'] }}
                            </h3>

                            <p class="platform-card-description">
                                {{ $item['description'] }}
                            </p>

                        </div>

                        <div class="platform-card-footer">

                            <div class="platform-card-audience">
                                <svg
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                    aria-hidden="true"
                                >
                                    <circle cx="12" cy="12" r="9" stroke-width="1.8"/>
                                    <path d="M3 12h18M12 3c2.3 2.6 3.5 5.6 3.5 9S14.3 18.4 12 21c-2.3-2.6-3.5-5.6-3.5-9S9.7 5.6 12 3Z" stroke-width="1.6"/>
                                </svg>

                                <span>{{ $item['audience'] }}</span>
                            </div>

                            <div class="platform-card-meta">

                                <span class="platform-status">
                                    {{ $item['status'] }}
                                </span>

                                <div class="platform-card-date">
                                    <div>{{ $item['date'] }}</div>
                                    <div>{{ $item['time'] }}</div>
                                </div>

                                <button
                                    type="button"
                                    class="platform-more"
                                    aria-label="More policy actions"
                                >
                                    <svg
                                        fill="currentColor"
                                        viewBox="0 0 24 24"
                                        aria-hidden="true"
                                    >
                                        <circle cx="12" cy="5" r="2"/>
                                        <circle cx="12" cy="12" r="2"/>
                                        <circle cx="12" cy="19" r="2"/>
                                    </svg>
                                </button>

                            </div>

                        </div>

                    </article>
                @endforeach

                @for ($i = count($policies); $i < 8; $i++)
                    <div
                        class="platform-empty-card"
                        aria-hidden="true"
                    ></div>
                @endfor

            </div>
        </div>

    </section>


    <!-- =========================================================
         CREATE ANNOUNCEMENT MODAL
    ========================================================== -->
    <div
        id="create-announcement-modal"
        class="platform-modal-backdrop"
        aria-hidden="true"
    >
        <div
            class="platform-modal"
            role="dialog"
            aria-modal="true"
            aria-labelledby="create-announcement-title"
        >
            <div class="platform-modal-inner">

                <div>
                    <h2
                        id="create-announcement-title"
                        class="platform-modal-heading"
                    >
                        Create Announcement
                    </h2>

                    <p class="platform-modal-subtitle">
                        Post an announcement to keep all users informed.
                    </p>
                </div>


                <div class="announcement-modal-grid">

                    <!-- LEFT COLUMN -->
                    <div class="announcement-modal-left">

                        <div class="platform-form-group">
                            <label class="platform-form-label">
                                Announcement Type<span class="platform-required">*</span>
                            </label>

                            <div class="platform-option-grid">

                                <label
                                    class="platform-option-card is-selected"
                                    data-announcement-type-card
                                >
                                    <input
                                        type="radio"
                                        name="announcement_type"
                                        value="Announcement"
                                        checked
                                    >

                                    <span class="platform-option-icon announcement-option-icon">
                                        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                            <path d="M3 10v4a2 2 0 0 0 2 2h2l2 5h3l-2-5h1c3.9 0 7.2 1.1 10 3V5c-2.8 1.9-6.1 3-10 3H5a2 2 0 0 0-2 2Zm15-1.5v7c-2.2-1-4.5-1.6-7-1.8v-3.4c2.5-.2 4.8-.8 7-1.8Z"/>
                                        </svg>
                                    </span>

                                    <span class="platform-option-title">
                                        Announcement
                                    </span>

                                    <span class="platform-option-description">
                                        General updates, news, and important information.
                                    </span>
                                </label>


                                <label
                                    class="platform-option-card"
                                    data-announcement-type-card
                                >
                                    <input
                                        type="radio"
                                        name="announcement_type"
                                        value="Policy Update"
                                    >

                                    <span class="platform-option-icon policy-option-icon">
                                        <svg viewBox="0 0 64 64" fill="currentColor" aria-hidden="true">
                                            <path d="M18 8h24l8 8v40H18V8Zm22 3v8h8l-8-8ZM25 28h18v4H25v-4Zm0 9h18v4H25v-4Zm0 9h12v4H25v-4Z"/>
                                        </svg>
                                    </span>

                                    <span class="platform-option-title">
                                        Policy Update
                                    </span>

                                    <span class="platform-option-description">
                                        Updates to platform policies, rules, and guidelines.
                                    </span>
                                </label>

                            </div>
                        </div>


                        <div class="platform-form-group">
                            <label
                                for="announcement-title-input"
                                class="platform-form-label"
                            >
                                Title<span class="platform-required">*</span>
                            </label>

                            <div class="platform-input-wrap">
                                <input
                                    id="announcement-title-input"
                                    type="text"
                                    maxlength="80"
                                    class="platform-input"
                                    placeholder="Enter announcement title..."
                                >

                                <span
                                    id="announcement-title-count"
                                    class="platform-counter"
                                >
                                    0/80
                                </span>
                            </div>
                        </div>


                        <div class="platform-form-group">
                            <label
                                for="announcement-message-input"
                                class="platform-form-label"
                            >
                                Message / Description<span class="platform-required">*</span>
                            </label>

                            <div class="platform-textarea-wrap">
                                <textarea
                                    id="announcement-message-input"
                                    maxlength="500"
                                    class="platform-textarea"
                                    placeholder="Write your announcement here..."
                                ></textarea>

                                <span
                                    id="announcement-message-count"
                                    class="platform-counter"
                                >
                                    0/500
                                </span>
                            </div>
                        </div>


                        <div class="platform-form-group">
                            <label class="platform-form-label">
                                Audience<span class="platform-required">*</span>
                            </label>

                            <div class="platform-audience-row">
                                <label class="platform-radio-inline">
                                    <input
                                        type="radio"
                                        name="announcement_audience"
                                        value="All Users"
                                        checked
                                    >
                                    <span>All Users</span>
                                </label>

                                <label class="platform-radio-inline">
                                    <input
                                        type="radio"
                                        name="announcement_audience"
                                        value="Buyers"
                                    >
                                    <span>Buyers</span>
                                </label>

                                <label class="platform-radio-inline">
                                    <input
                                        type="radio"
                                        name="announcement_audience"
                                        value="Sellers"
                                    >
                                    <span>Sellers</span>
                                </label>

                                <label class="platform-radio-inline">
                                    <input
                                        type="radio"
                                        name="announcement_audience"
                                        value="Logistics"
                                    >
                                    <span>Logistics</span>
                                </label>
                            </div>
                        </div>


                        <div class="platform-form-group" style="margin-bottom:0;">
                            <label class="platform-form-label">
                                Status<span class="platform-required">*</span>
                            </label>

                            <div class="platform-option-grid platform-status-grid">

                                <label
                                    class="platform-option-card is-selected"
                                    data-announcement-status-card
                                >
                                    <input
                                        type="radio"
                                        name="announcement_status"
                                        value="Publish Now"
                                        checked
                                    >

                                    <span class="platform-option-title">
                                        Publish Now
                                    </span>

                                    <span class="platform-option-description">
                                        Make this announcement visible immediately.
                                    </span>
                                </label>


                                <label
                                    class="platform-option-card"
                                    data-announcement-status-card
                                >
                                    <input
                                        type="radio"
                                        name="announcement_status"
                                        value="Schedule"
                                    >

                                    <span class="platform-option-title">
                                        Schedule
                                    </span>

                                    <span class="platform-option-description">
                                        Schedule this announcement for a future date.
                                    </span>
                                </label>

                            </div>


                            <div
                                id="announcement-schedule-fields"
                                class="announcement-schedule-fields"
                            >
                                <label class="platform-form-label">
                                    Publish Date &amp; Time<span class="platform-required">*</span>
                                </label>

                                <div class="platform-datetime-row">
                                    <input
                                        id="announcement-date-input"
                                        type="date"
                                        class="platform-input"
                                    >

                                    <input
                                        id="announcement-time-input"
                                        type="time"
                                        class="platform-input"
                                    >
                                </div>

                                <p class="platform-help-text">
                                    Choose when this announcement will be published.
                                </p>
                            </div>
                        </div>

                    </div>


                    <!-- RIGHT COLUMN -->
                    <div class="announcement-modal-right">

                        <div class="platform-form-group">
                            <label class="platform-form-label">
                                Announcement Banner
                                <span class="platform-muted-inline">(Optional)</span>
                            </label>

                            <p class="platform-help-text" style="margin:0 0 8px;">
                                Upload a banner image to make your announcement more eye-catching.
                            </p>

                            <input
                                id="announcement-banner-input"
                                type="file"
                                accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                                hidden
                            >

                            <div
                                id="announcement-banner-drop"
                                class="platform-banner-box"
                                role="button"
                                tabindex="0"
                            >
                                <span class="platform-banner-icon">
                                    <svg
                                        fill="currentColor"
                                        viewBox="0 0 24 24"
                                        aria-hidden="true"
                                    >
                                        <path d="M4 5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v14H4V5Zm3 10h10l-3.2-4.1-2.5 3-1.8-2.1L7 15Zm2-7.5A1.5 1.5 0 1 0 9 4.5a1.5 1.5 0 0 0 0 3Z"/>
                                    </svg>
                                </span>

                                <p class="platform-banner-title">
                                    Click to upload or drag and drop
                                </p>

                                <p class="platform-banner-help">
                                    Recommended size: 1200 × 628 px (16:9)<br>
                                    JPG, PNG up to 5MB
                                </p>

                                <p
                                    id="announcement-banner-file"
                                    class="platform-banner-file"
                                ></p>
                            </div>
                        </div>


                        <div class="announcement-preview-section">
                            <p class="announcement-preview-label">
                                Preview
                            </p>

                            <div class="announcement-preview-card">

                                <div class="announcement-preview-main">

                                    <div class="announcement-preview-top">

                                        <div
                                            id="announcement-preview-icon"
                                            class="announcement-preview-icon"
                                        >
                                            <!-- Announcement = megaphone -->
                                            <svg
                                                viewBox="0 0 24 24"
                                                fill="currentColor"
                                                aria-hidden="true"
                                            >
                                                <path d="M3 10v4a2 2 0 0 0 2 2h2l2 5h3l-2-5h1c3.9 0 7.2 1.1 10 3V5c-2.8 1.9-6.1 3-10 3H5a2 2 0 0 0-2 2Zm15-1.5v7c-2.2-1-4.5-1.6-7-1.8v-3.4c2.5-.2 4.8-.8 7-1.8Z"/>
                                            </svg>
                                        </div>

                                        <h3
                                            id="announcement-preview-title"
                                            class="announcement-preview-title"
                                        ></h3>

                                    </div>

                                    <p
                                        id="announcement-preview-description"
                                        class="announcement-preview-description"
                                    ></p>

                                </div>


                                <div class="announcement-preview-footer">

                                    <div class="announcement-preview-audience">
                                        <svg
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                            aria-hidden="true"
                                        >
                                            <circle cx="12" cy="12" r="9" stroke-width="1.8"/>
                                            <path d="M3 12h18M12 3c2.3 2.6 3.5 5.6 3.5 9S14.3 18.4 12 21c-2.3-2.6-3.5-5.6-3.5-9S9.7 5.6 12 3Z" stroke-width="1.6"/>
                                        </svg>

                                        <span id="announcement-preview-audience"></span>
                                    </div>

                                    <div class="announcement-preview-meta">

                                        <span
                                            id="announcement-preview-status"
                                            class="platform-status"
                                        ></span>

                                        <div
                                            id="announcement-preview-date"
                                            class="announcement-preview-date"
                                        ></div>

                                        <button
                                            type="button"
                                            class="announcement-preview-more"
                                            aria-label="Preview more actions"
                                            tabindex="-1"
                                        >
                                            <svg
                                                fill="currentColor"
                                                viewBox="0 0 24 24"
                                                aria-hidden="true"
                                            >
                                                <circle cx="12" cy="5" r="2"/>
                                                <circle cx="12" cy="12" r="2"/>
                                                <circle cx="12" cy="19" r="2"/>
                                            </svg>
                                        </button>

                                    </div>

                                </div>

                            </div>
                        </div>

                    </div>

                </div>


                <div class="platform-modal-actions">
                    <button
                        id="cancel-announcement-modal"
                        type="button"
                        class="platform-modal-button platform-modal-cancel"
                    >
                        Cancel
                    </button>

                    <button
                        id="submit-announcement-modal"
                        type="button"
                        class="platform-modal-button platform-modal-primary"
                    >
                        Upload
                    </button>
                </div>

            </div>
        </div>
    </div>


    <!-- =========================================================
         ADD NEW POLICY MODAL
    ========================================================== -->
    <div
        id="add-policy-modal"
        class="platform-modal-backdrop"
        aria-hidden="true"
    >
        <div
            class="platform-modal platform-policy-modal"
            role="dialog"
            aria-modal="true"
            aria-labelledby="add-policy-title"
        >
            <div class="platform-modal-inner">

                <div>
                    <h2
                        id="add-policy-title"
                        class="platform-modal-heading"
                    >
                        Add New Policy
                    </h2>

                    <p class="platform-modal-subtitle">
                        Create a new policy to set guidelines and rules for the platform.
                    </p>
                </div>


                <div class="policy-modal-form">

                    <div class="policy-modal-two-col">

                        <div class="platform-form-group">
                            <label
                                for="policy-title-input"
                                class="platform-form-label"
                            >
                                Policy Title<span class="platform-required">*</span>
                            </label>

                            <div class="platform-input-wrap">
                                <input
                                    id="policy-title-input"
                                    type="text"
                                    maxlength="80"
                                    class="platform-input"
                                    placeholder="Enter policy title..."
                                >

                                <span
                                    id="policy-title-count"
                                    class="platform-counter"
                                >
                                    0/80
                                </span>
                            </div>
                        </div>


                        <div class="platform-form-group">
                            <label
                                for="policy-category-input"
                                class="platform-form-label"
                            >
                                Policy Category<span class="platform-required">*</span>
                            </label>

                            <select
                                id="policy-category-input"
                                class="platform-select"
                            >
                                <option value="">
                                    Select Category
                                </option>
                                <option>Prohibited &amp; Restricted Items</option>
                                <option>User Conduct</option>
                                <option>Shipping &amp; Delivery</option>
                                <option>Privacy &amp; Data</option>
                                <option>Buyer Policy</option>
                                <option>Seller Policy</option>
                                <option>Logistics Policy</option>
                            </select>
                        </div>

                    </div>


                    <div class="platform-form-group">
                        <label
                            for="policy-description-input"
                            class="platform-form-label"
                        >
                            Message / Description<span class="platform-required">*</span>
                        </label>

                        <div class="platform-textarea-wrap">
                            <textarea
                                id="policy-description-input"
                                maxlength="500"
                                class="platform-textarea policy-description"
                                placeholder="Enter a short description of the policy..."
                            ></textarea>

                            <span
                                id="policy-description-count"
                                class="platform-counter"
                            >
                                0/500
                            </span>
                        </div>
                    </div>


                    <div class="platform-form-group">
                        <label
                            for="policy-content-input"
                            class="platform-form-label"
                        >
                            Policy Content<span class="platform-required">*</span>
                        </label>

                        <div class="platform-textarea-wrap">
                            <textarea
                                id="policy-content-input"
                                maxlength="500"
                                class="platform-textarea policy-content"
                                placeholder="Write the full policy details here..."
                            ></textarea>

                            <span
                                id="policy-content-count"
                                class="platform-counter"
                            >
                                0/500
                            </span>
                        </div>
                    </div>


                    <div class="platform-form-group policy-modal-datetime">
                        <label class="platform-form-label">
                            Publish Date &amp; Time<span class="platform-required">*</span>
                        </label>

                        <div class="platform-datetime-row">
                            <input
                                id="policy-date-input"
                                type="date"
                                class="platform-input"
                            >

                            <input
                                id="policy-time-input"
                                type="time"
                                class="platform-input"
                            >
                        </div>

                        <p class="platform-help-text" style="margin-top:28px;">
                            Choose when this announcement will be published.
                        </p>
                    </div>

                </div>


                <div class="platform-modal-actions">
                    <button
                        id="cancel-policy-modal"
                        type="button"
                        class="platform-modal-button platform-modal-cancel"
                    >
                        Cancel
                    </button>

                    <button
                        id="submit-policy-modal"
                        type="button"
                        class="platform-modal-button platform-modal-primary"
                    >
                        Add
                    </button>
                </div>

            </div>
        </div>
    </div>


</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const tabs =
        Array.from(
            document.querySelectorAll(
                '[data-platform-tab]'
            )
        );

    const announcementsPanel =
        document.getElementById(
            'announcements-panel'
        );

    const announcementsGrid =
        document.getElementById(
            'announcements-grid'
        );

    const defaultAnnouncementData =
        @json($announcements);

    const policiesPanel =
        document.getElementById(
            'policies-panel'
        );

    const createButton =
        document.getElementById(
            'platform-create-button'
        );

    const createLabel =
        document.getElementById(
            'platform-create-label'
        );

    const announcementModal =
        document.getElementById(
            'create-announcement-modal'
        );

    const policyModal =
        document.getElementById(
            'add-policy-modal'
        );

    const cancelAnnouncement =
        document.getElementById(
            'cancel-announcement-modal'
        );

    const cancelPolicy =
        document.getElementById(
            'cancel-policy-modal'
        );

    const submitAnnouncement =
        document.getElementById(
            'submit-announcement-modal'
        );

    const submitPolicy =
        document.getElementById(
            'submit-policy-modal'
        );

    let activePlatformTab =
        'announcements';

    let editingAnnouncementId = null;
    let editingAnnouncementSource = null;

    let currentAnnouncementBannerDataUrl = '';
    let currentAnnouncementBannerName = '';


    /* =========================================================
       TAB SWITCHING
    ========================================================== */
    function switchPlatformTab(tabName) {
        activePlatformTab =
            tabName;

        tabs.forEach(tab => {
            const active =
                tab.dataset.platformTab ===
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

        announcementsPanel.classList.toggle(
            'active',
            tabName === 'announcements'
        );

        policiesPanel.classList.toggle(
            'active',
            tabName === 'policies'
        );

        createLabel.textContent =
            tabName === 'announcements'
                ? 'Create Announcement'
                : 'Add New Policy';
    }

    tabs.forEach(tab => {
        tab.addEventListener(
            'click',
            function () {
                switchPlatformTab(
                    this.dataset.platformTab
                );
            }
        );
    });


    /* =========================================================
       FORM RESET HELPERS
       Any unfinished admin input is cleared when a modal closes.
    ========================================================== */
    function resetAnnouncementModal() {
        const titleInput =
            document.getElementById(
                'announcement-title-input'
            );

        const messageInput =
            document.getElementById(
                'announcement-message-input'
            );

        const dateInput =
            document.getElementById(
                'announcement-date-input'
            );

        const timeInput =
            document.getElementById(
                'announcement-time-input'
            );

        const bannerInputElement =
            document.getElementById(
                'announcement-banner-input'
            );

        const bannerFileElement =
            document.getElementById(
                'announcement-banner-file'
            );

        if (titleInput) {
            titleInput.value = '';
        }

        if (messageInput) {
            messageInput.value = '';
        }

        if (dateInput) {
            dateInput.value = '';
            dateInput.disabled = false;
        }

        if (timeInput) {
            timeInput.value = '';
            timeInput.disabled = false;
        }

        if (bannerInputElement) {
            bannerInputElement.value = '';
        }

        if (bannerFileElement) {
            bannerFileElement.textContent = '';
        }

        currentAnnouncementBannerDataUrl = '';
        currentAnnouncementBannerName = '';

        document
            .querySelectorAll(
                'input[name="announcement_type"]'
            )
            .forEach((radio, index) => {
                radio.checked = index === 0;
            });

        document
            .querySelectorAll(
                'input[name="announcement_audience"]'
            )
            .forEach((radio, index) => {
                radio.checked = index === 0;
            });

        document
            .querySelectorAll(
                'input[name="announcement_status"]'
            )
            .forEach((radio, index) => {
                radio.checked = index === 0;
            });

        document
            .querySelectorAll(
                '[data-announcement-type-card]'
            )
            .forEach((card, index) => {
                card.classList.toggle(
                    'is-selected',
                    index === 0
                );
            });

        document
            .querySelectorAll(
                '[data-announcement-status-card]'
            )
            .forEach((card, index) => {
                card.classList.toggle(
                    'is-selected',
                    index === 0
                );
            });

        const titleCount =
            document.getElementById(
                'announcement-title-count'
            );

        const messageCount =
            document.getElementById(
                'announcement-message-count'
            );

        if (titleCount) {
            titleCount.textContent = '0/80';
        }

        if (messageCount) {
            messageCount.textContent = '0/500';
        }

        announcementPreviewTouched = false;

        if (
            typeof updateAnnouncementScheduleFields ===
            'function'
        ) {
            updateAnnouncementScheduleFields();
        }

        if (
            typeof renderAnnouncementPreview ===
            'function'
        ) {
            renderAnnouncementPreview();
        }

        setAnnouncementModalMode(
            'create'
        );
    }

    function resetPolicyModal() {
        const ids = [
            'policy-title-input',
            'policy-description-input',
            'policy-content-input',
            'policy-date-input',
            'policy-time-input'
        ];

        ids.forEach(id => {
            const element =
                document.getElementById(
                    id
                );

            if (element) {
                element.value = '';
            }
        });

        const category =
            document.getElementById(
                'policy-category-input'
            );

        if (category) {
            category.value = '';
        }

        const counts = {
            'policy-title-count': '0/80',
            'policy-description-count': '0/500',
            'policy-content-count': '0/500'
        };

        Object.entries(counts)
            .forEach(([id, value]) => {
                const element =
                    document.getElementById(
                        id
                    );

                if (element) {
                    element.textContent =
                        value;
                }
            });
    }


    /* =========================================================
       MODAL HELPERS
    ========================================================== */
    function openPlatformModal(
        modal,
        resetForm = true
    ) {
        if (!modal) {
            return;
        }

        if (
            resetForm &&
            modal === announcementModal
        ) {
            resetAnnouncementModal();
        }

        if (
            resetForm &&
            modal === policyModal
        ) {
            resetPolicyModal();
        }

        modal.classList.add(
            'is-open'
        );

        modal.setAttribute(
            'aria-hidden',
            'false'
        );

        document.body.classList.add(
            'overflow-hidden'
        );
    }

    function closePlatformModal(modal) {
        if (!modal) {
            return;
        }

        modal.classList.remove(
            'is-open'
        );

        modal.setAttribute(
            'aria-hidden',
            'true'
        );

        if (modal === announcementModal) {
            resetAnnouncementModal();

            editingAnnouncementId = null;
            editingAnnouncementSource = null;
        }

        if (modal === policyModal) {
            resetPolicyModal();
        }

        if (
            !announcementModal.classList.contains('is-open') &&
            !policyModal.classList.contains('is-open')
        ) {
            document.body.classList.remove(
                'overflow-hidden'
            );
        }
    }

    createButton.addEventListener(
        'click',
        function () {
            if (
                activePlatformTab ===
                'announcements'
            ) {
                editingAnnouncementId = null;
                editingAnnouncementSource = null;

                setAnnouncementModalMode(
                    'create'
                );

                openPlatformModal(
                    announcementModal
                );
            } else {
                openPlatformModal(
                    policyModal
                );
            }
        }
    );

    cancelAnnouncement.addEventListener(
        'click',
        function () {
            closePlatformModal(
                announcementModal
            );
        }
    );

    cancelPolicy.addEventListener(
        'click',
        function () {
            closePlatformModal(
                policyModal
            );
        }
    );

    announcementModal.addEventListener(
        'click',
        function (event) {
            if (
                event.target ===
                announcementModal
            ) {
                closePlatformModal(
                    announcementModal
                );
            }
        }
    );

    policyModal.addEventListener(
        'click',
        function (event) {
            if (
                event.target ===
                policyModal
            ) {
                closePlatformModal(
                    policyModal
                );
            }
        }
    );


    /* =========================================================
       OPTION CARD SELECTION
    ========================================================== */
    function bindOptionCards(
        selector,
        radioName
    ) {
        const cards =
            Array.from(
                document.querySelectorAll(
                    selector
                )
            );

        cards.forEach(card => {
            const radio =
                card.querySelector(
                    `input[name="${radioName}"]`
                );

            if (!radio) {
                return;
            }

            radio.addEventListener(
                'change',
                function () {
                    cards.forEach(item => {
                        item.classList.toggle(
                            'is-selected',
                            item === card
                        );
                    });
                }
            );
        });
    }

    bindOptionCards(
        '[data-announcement-type-card]',
        'announcement_type'
    );

    bindOptionCards(
        '[data-announcement-status-card]',
        'announcement_status'
    );

    document
        .querySelectorAll(
            'input[name="announcement_type"], input[name="announcement_status"]'
        )
        .forEach(radio => {
            radio.addEventListener(
                'change',
                function () {
                    announcementPreviewTouched = true;

                    if (
                        this.name ===
                        'announcement_status'
                    ) {
                        updateAnnouncementScheduleFields();
                    }

                    if (
                        typeof renderAnnouncementPreview ===
                        'function'
                    ) {
                        renderAnnouncementPreview();
                    }
                }
            );
        });


    /* =========================================================
       CHARACTER COUNTERS
    ========================================================== */
    function bindCounter(
        inputId,
        counterId,
        max
    ) {
        const input =
            document.getElementById(
                inputId
            );

        const counter =
            document.getElementById(
                counterId
            );

        if (!input || !counter) {
            return;
        }

        function renderCount() {
            counter.textContent =
                `${input.value.length}/${max}`;
        }

        input.addEventListener(
            'input',
            renderCount
        );

        renderCount();
    }

    bindCounter(
        'announcement-title-input',
        'announcement-title-count',
        80
    );

    bindCounter(
        'announcement-message-input',
        'announcement-message-count',
        500
    );

    bindCounter(
        'policy-title-input',
        'policy-title-count',
        80
    );

    bindCounter(
        'policy-description-input',
        'policy-description-count',
        500
    );

    bindCounter(
        'policy-content-input',
        'policy-content-count',
        500
    );


    /* =========================================================
       ANNOUNCEMENT PREVIEW
    ========================================================== */
    const announcementTitleInput =
        document.getElementById(
            'announcement-title-input'
        );

    const announcementMessageInput =
        document.getElementById(
            'announcement-message-input'
        );

    const announcementDateInput =
        document.getElementById(
            'announcement-date-input'
        );

    const announcementTimeInput =
        document.getElementById(
            'announcement-time-input'
        );

    const announcementScheduleFields =
        document.getElementById(
            'announcement-schedule-fields'
        );

    const previewTitle =
        document.getElementById(
            'announcement-preview-title'
        );

    const previewDescription =
        document.getElementById(
            'announcement-preview-description'
        );

    const previewAudience =
        document.getElementById(
            'announcement-preview-audience'
        );

    const previewDate =
        document.getElementById(
            'announcement-preview-date'
        );

    const previewIcon =
        document.getElementById(
            'announcement-preview-icon'
        );

    const previewStatus =
        document.getElementById(
            'announcement-preview-status'
        );

    let announcementPreviewTouched =
        false;

    function formatPreviewDate(value) {
        if (!value) {
            return '';
        }

        const parts =
            value.split('-');

        if (parts.length !== 3) {
            return value;
        }

        const date =
            new Date(
                Number(parts[0]),
                Number(parts[1]) - 1,
                Number(parts[2])
            );

        return new Intl.DateTimeFormat(
            'en-US',
            {
                month: 'long',
                day: 'numeric',
                year: 'numeric'
            }
        ).format(date);
    }

    function formatPreviewTime(value) {
        if (!value) {
            return '';
        }

        const parts =
            value.split(':');

        const date =
            new Date();

        date.setHours(
            Number(parts[0] || 0),
            Number(parts[1] || 0),
            0,
            0
        );

        return new Intl.DateTimeFormat(
            'en-US',
            {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            }
        ).format(date);
    }

    function updateAnnouncementScheduleFields() {
        const checkedStatus =
            document.querySelector(
                'input[name="announcement_status"]:checked'
            );

        const isScheduled =
            checkedStatus &&
            checkedStatus.value === 'Schedule';

        announcementScheduleFields.classList.toggle(
            'is-visible',
            Boolean(isScheduled)
        );

        announcementDateInput.required =
            Boolean(isScheduled);

        announcementTimeInput.required =
            Boolean(isScheduled);

        if (!isScheduled) {
            announcementDateInput.value = '';
            announcementTimeInput.value = '';
        }
    }

    function renderAnnouncementPreview() {
        previewTitle.textContent =
            announcementTitleInput.value.trim();

        previewDescription.textContent =
            announcementMessageInput.value.trim();

        const checkedAudience =
            document.querySelector(
                'input[name="announcement_audience"]:checked'
            );

        previewAudience.textContent =
            announcementPreviewTouched &&
            checkedAudience
                ? checkedAudience.value
                : '';

        const checkedType =
            document.querySelector(
                'input[name="announcement_type"]:checked'
            );

        const isPolicyUpdate =
            checkedType &&
            checkedType.value === 'Policy Update';

        if (currentAnnouncementBannerDataUrl) {
            previewIcon.classList.add(
                'has-cover-photo'
            );

            previewIcon.innerHTML = `
                <img
                    src="${currentAnnouncementBannerDataUrl}"
                    alt="Announcement cover photo"
                >
            `;
        } else {
            previewIcon.classList.remove(
                'has-cover-photo'
            );

            if (isPolicyUpdate) {
                previewIcon.style.background =
                    '#D8C1F0';

                previewIcon.style.color =
                    '#48208D';

                previewIcon.innerHTML = `
                    <svg
                        viewBox="0 0 64 64"
                        fill="currentColor"
                        aria-hidden="true"
                    >
                        <path d="M18 8h24l8 8v40H18V8Zm22 3v8h8l-8-8ZM25 28h18v4H25v-4Zm0 9h18v4H25v-4Zm0 9h12v4H25v-4Z"/>
                    </svg>
                `;
            } else {
                previewIcon.style.background =
                    '#FFD1D4';

                previewIcon.style.color =
                    '#C8121B';

                previewIcon.innerHTML = `
                    <svg
                        viewBox="0 0 24 24"
                        fill="currentColor"
                        aria-hidden="true"
                    >
                        <path d="M3 10v4a2 2 0 0 0 2 2h2l2 5h3l-2-5h1c3.9 0 7.2 1.1 10 3V5c-2.8 1.9-6.1 3-10 3H5a2 2 0 0 0-2 2Zm15-1.5v7c-2.2-1-4.5-1.6-7-1.8v-3.4c2.5-.2 4.8-.8 7-1.8Z"/>
                    </svg>
                `;
            }
        }

        const checkedStatus =
            document.querySelector(
                'input[name="announcement_status"]:checked'
            );

        if (!announcementPreviewTouched) {
            previewStatus.textContent = '';
            previewStatus.style.background = 'transparent';
            previewStatus.style.color = 'transparent';
        } else {
            previewStatus.textContent =
                checkedStatus &&
                checkedStatus.value === 'Schedule'
                    ? 'Scheduled'
                    : 'Published';

            if (
                checkedStatus &&
                checkedStatus.value === 'Schedule'
            ) {
                previewStatus.style.background =
                    '#FFF0C7';

                previewStatus.style.color =
                    '#8A5A00';
            } else {
                previewStatus.style.background =
                    '#D9EBD8';

                previewStatus.style.color =
                    '#3D8736';
            }
        }

        const previewDateText =
            formatPreviewDate(
                announcementDateInput.value
            );

        const previewTimeText =
            formatPreviewTime(
                announcementTimeInput.value
            );

        const shouldShowPreviewSchedule =
            announcementPreviewTouched &&
            checkedStatus &&
            checkedStatus.value === 'Schedule' &&
            (previewDateText || previewTimeText);

        previewDate.innerHTML =
            shouldShowPreviewSchedule
                ? `
                    <div>${previewDateText}</div>
                    <div>${previewTimeText}</div>
                `
                : '';
    }

    [
        announcementTitleInput,
        announcementMessageInput,
        announcementDateInput,
        announcementTimeInput
    ].forEach(input => {
        input.addEventListener(
            'input',
            function () {
                announcementPreviewTouched = true;
                renderAnnouncementPreview();
            }
        );

        input.addEventListener(
            'change',
            function () {
                announcementPreviewTouched = true;
                renderAnnouncementPreview();
            }
        );
    });

    document
        .querySelectorAll(
            'input[name="announcement_audience"]'
        )
        .forEach(radio => {
            radio.addEventListener(
                'change',
                function () {
                    announcementPreviewTouched = true;
                    renderAnnouncementPreview();
                }
            );
        });


    /* =========================================================
       BANNER UPLOAD
    ========================================================== */
    const bannerInput =
        document.getElementById(
            'announcement-banner-input'
        );

    const bannerDrop =
        document.getElementById(
            'announcement-banner-drop'
        );

    const bannerFile =
        document.getElementById(
            'announcement-banner-file'
        );

    function optimizeBannerImage(file) {
        return new Promise(
            (resolve, reject) => {
                const reader =
                    new FileReader();

                reader.onload =
                    function () {
                        const image =
                            new Image();

                        image.onload =
                            function () {
                                const targetWidth =
                                    1200;

                                const targetHeight =
                                    628;

                                const canvas =
                                    document.createElement(
                                        'canvas'
                                    );

                                canvas.width =
                                    targetWidth;

                                canvas.height =
                                    targetHeight;

                                const context =
                                    canvas.getContext(
                                        '2d'
                                    );

                                const sourceRatio =
                                    image.width /
                                    image.height;

                                const targetRatio =
                                    targetWidth /
                                    targetHeight;

                                let sourceX = 0;
                                let sourceY = 0;
                                let sourceWidth =
                                    image.width;
                                let sourceHeight =
                                    image.height;

                                if (
                                    sourceRatio >
                                    targetRatio
                                ) {
                                    sourceWidth =
                                        image.height *
                                        targetRatio;

                                    sourceX =
                                        (
                                            image.width -
                                            sourceWidth
                                        ) / 2;
                                } else {
                                    sourceHeight =
                                        image.width /
                                        targetRatio;

                                    sourceY =
                                        (
                                            image.height -
                                            sourceHeight
                                        ) / 2;
                                }

                                context.drawImage(
                                    image,
                                    sourceX,
                                    sourceY,
                                    sourceWidth,
                                    sourceHeight,
                                    0,
                                    0,
                                    targetWidth,
                                    targetHeight
                                );

                                resolve(
                                    canvas.toDataURL(
                                        'image/jpeg',
                                        0.78
                                    )
                                );
                            };

                        image.onerror =
                            reject;

                        image.src =
                            reader.result;
                    };

                reader.onerror =
                    reject;

                reader.readAsDataURL(
                    file
                );
            }
        );
    }

    async function handleBannerFile(file) {
        if (!file) {
            return;
        }

        const validTypes = [
            'image/jpeg',
            'image/png'
        ];

        const maxSize =
            5 * 1024 * 1024;

        if (
            !validTypes.includes(file.type)
        ) {
            bannerFile.textContent =
                'Please choose a JPG or PNG image.';

            return;
        }

        if (
            file.size > maxSize
        ) {
            bannerFile.textContent =
                'Image must be 5MB or smaller.';

            return;
        }

        bannerFile.textContent =
            'Preparing cover photo...';

        try {
            currentAnnouncementBannerDataUrl =
                await optimizeBannerImage(
                    file
                );

            currentAnnouncementBannerName =
                file.name;

            bannerFile.textContent =
                file.name;

            announcementPreviewTouched =
                true;

            renderAnnouncementPreview();
        } catch (error) {
            currentAnnouncementBannerDataUrl =
                '';

            currentAnnouncementBannerName =
                '';

            bannerFile.textContent =
                'Unable to preview this image.';
        }
    }

    bannerDrop.addEventListener(
        'click',
        function () {
            bannerInput.click();
        }
    );

    bannerDrop.addEventListener(
        'keydown',
        function (event) {
            if (
                event.key === 'Enter' ||
                event.key === ' '
            ) {
                event.preventDefault();
                bannerInput.click();
            }
        }
    );

    bannerInput.addEventListener(
        'change',
        function () {
            handleBannerFile(
                this.files[0]
            );
        }
    );

    ['dragenter', 'dragover']
        .forEach(eventName => {
            bannerDrop.addEventListener(
                eventName,
                function (event) {
                    event.preventDefault();
                    bannerDrop.classList.add(
                        'is-dragover'
                    );
                }
            );
        });

    ['dragleave', 'drop']
        .forEach(eventName => {
            bannerDrop.addEventListener(
                eventName,
                function (event) {
                    event.preventDefault();
                    bannerDrop.classList.remove(
                        'is-dragover'
                    );
                }
            );
        });

    bannerDrop.addEventListener(
        'drop',
        function (event) {
            const file =
                event.dataTransfer.files[0];

            handleBannerFile(
                file
            );
        }
    );


    /* =========================================================
       NEW ANNOUNCEMENT CARDS
       Front-end demo persistence until backend/database is connected.
    ========================================================== */
    const LOCAL_ANNOUNCEMENTS_KEY =
        'shopease_platform_announcements';

    const DEFAULT_ANNOUNCEMENT_OVERRIDES_KEY =
        'shopease_platform_default_announcement_overrides';

    const HIDDEN_DEFAULT_ANNOUNCEMENTS_KEY =
        'shopease_platform_hidden_default_announcements';

    function escapePlatformHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function getAnnouncementCardIcon(type) {
        if (type === 'Policy Update') {
            return `
                <div class="platform-card-icon theme-purple">
                    <svg
                        viewBox="0 0 64 64"
                        fill="currentColor"
                        aria-hidden="true"
                    >
                        <path d="M18 8h24l8 8v40H18V8Zm22 3v8h8l-8-8ZM25 28h18v4H25v-4Zm0 9h18v4H25v-4Zm0 9h12v4H25v-4Z"/>
                    </svg>
                </div>
            `;
        }

        return `
            <div class="platform-card-icon theme-red">
                <svg
                    viewBox="0 0 24 24"
                    fill="currentColor"
                    aria-hidden="true"
                >
                    <path d="M3 10v4a2 2 0 0 0 2 2h2l2 5h3l-2-5h1c3.9 0 7.2 1.1 10 3V5c-2.8 1.9-6.1 3-10 3H5a2 2 0 0 0-2 2Zm15-1.5v7c-2.2-1-4.5-1.6-7-1.8v-3.4c2.5-.2 4.8-.8 7-1.8Z"/>
                </svg>
            </div>
        `;
    }

    function getAnnouncementStatusStyle(status) {
        return status === 'Scheduled'
            ? 'background:#FFF0C7;color:#8A5A00;'
            : 'background:#D9EBD8;color:#3D8736;';
    }

    function getAnnouncementCardMedia(item) {
        if (item.bannerDataUrl) {
            return `
                <div class="platform-card-cover">
                    <img
                        src="${escapePlatformHtml(
                            item.bannerDataUrl
                        )}"
                        alt="${escapePlatformHtml(
                            item.title || 'Announcement'
                        )} cover photo"
                    >
                </div>
            `;
        }

        return getAnnouncementCardIcon(
            item.type
        );
    }

    function buildAnnouncementCard(item) {
        const safeId =
            escapePlatformHtml(item.id);

        const safeTitle =
            escapePlatformHtml(item.title);

        const safeDescription =
            escapePlatformHtml(item.description);

        const safeAudience =
            escapePlatformHtml(item.audience);

        const safeStatus =
            escapePlatformHtml(item.status);

        const safeDate =
            escapePlatformHtml(item.date);

        const safeTime =
            escapePlatformHtml(item.time);

        return `
            <article
                class="platform-card platform-card-dynamic"
                data-announcement-id="${safeId}"
            >
                <div class="platform-card-main">
                    ${getAnnouncementCardMedia(item)}

                    <h3 class="platform-card-title">
                        ${safeTitle}
                    </h3>

                    <p class="platform-card-description">
                        ${safeDescription}
                    </p>
                </div>

                <div class="platform-card-footer">
                    <div class="platform-card-audience">
                        <svg
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <circle cx="12" cy="12" r="9" stroke-width="1.8"/>
                            <path d="M3 12h18M12 3c2.3 2.6 3.5 5.6 3.5 9S14.3 18.4 12 21c-2.3-2.6-3.5-5.6-3.5-9S9.7 5.6 12 3Z" stroke-width="1.6"/>
                        </svg>

                        <span>${safeAudience}</span>
                    </div>

                    <div class="platform-card-meta">
                        <span
                            class="platform-status"
                            style="${getAnnouncementStatusStyle(item.status)}"
                        >
                            ${safeStatus}
                        </span>

                        <div class="platform-card-date">
                            <div>${safeDate}</div>
                            <div>${safeTime}</div>
                        </div>

                        <div class="platform-more-wrap">
                            <button
                                type="button"
                                class="platform-more js-announcement-more"
                                aria-label="More announcement actions"
                                aria-expanded="false"
                            >
                                <svg
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                    aria-hidden="true"
                                >
                                    <circle cx="12" cy="5" r="2"/>
                                    <circle cx="12" cy="12" r="2"/>
                                    <circle cx="12" cy="19" r="2"/>
                                </svg>
                            </button>

                            <div class="platform-action-menu">
                                <button
                                    type="button"
                                    class="platform-action-item"
                                    data-announcement-action="edit"
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
                                            stroke-width="1.8"
                                            d="M16.862 3.487a2.12 2.12 0 0 1 3 3L8.5 17.85 4 19l1.15-4.5 11.712-11.013Z"
                                        />
                                    </svg>
                                    <span>Edit</span>
                                </button>

                                <button
                                    type="button"
                                    class="platform-action-item delete"
                                    data-announcement-action="delete"
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
                                            stroke-width="1.8"
                                            d="M4 7h16M9 7V4h6v3m-8 0 1 13h8l1-13M10 11v5m4-5v5"
                                        />
                                    </svg>
                                    <span>Delete</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
        `;
    }

    function getStoredAnnouncements() {
        try {
            const stored =
                JSON.parse(
                    localStorage.getItem(
                        LOCAL_ANNOUNCEMENTS_KEY
                    ) || '[]'
                );

            return Array.isArray(stored)
                ? stored
                : [];
        } catch (error) {
            return [];
        }
    }

    function saveStoredAnnouncements(items) {
        try {
            localStorage.setItem(
                LOCAL_ANNOUNCEMENTS_KEY,
                JSON.stringify(items)
            );
        } catch (error) {
            // Front-end demo remains usable without localStorage.
        }
    }

    function getDefaultAnnouncementOverrides() {
        try {
            const value =
                JSON.parse(
                    localStorage.getItem(
                        DEFAULT_ANNOUNCEMENT_OVERRIDES_KEY
                    ) || '{}'
                );

            return (
                value &&
                typeof value === 'object' &&
                !Array.isArray(value)
            )
                ? value
                : {};
        } catch (error) {
            return {};
        }
    }

    function saveDefaultAnnouncementOverrides(value) {
        try {
            localStorage.setItem(
                DEFAULT_ANNOUNCEMENT_OVERRIDES_KEY,
                JSON.stringify(value)
            );
        } catch (error) {
            // Ignore storage failures in the front-end demo.
        }
    }

    function getHiddenDefaultAnnouncementIds() {
        try {
            const value =
                JSON.parse(
                    localStorage.getItem(
                        HIDDEN_DEFAULT_ANNOUNCEMENTS_KEY
                    ) || '[]'
                );

            return Array.isArray(value)
                ? value.map(String)
                : [];
        } catch (error) {
            return [];
        }
    }

    function saveHiddenDefaultAnnouncementIds(value) {
        try {
            localStorage.setItem(
                HIDDEN_DEFAULT_ANNOUNCEMENTS_KEY,
                JSON.stringify(value)
            );
        } catch (error) {
            // Ignore storage failures in the front-end demo.
        }
    }

    function normalizeDefaultAnnouncements() {
        const overrides =
            getDefaultAnnouncementOverrides();

        const hidden =
            new Set(
                getHiddenDefaultAnnouncementIds()
            );

        return defaultAnnouncementData
            .map((item, index) => {
                const id =
                    `default-${index}`;

                const base = {
                    ...item,
                    id,
                    source: 'default',
                    type:
                        item.icon === 'document'
                            ? 'Policy Update'
                            : 'Announcement',
                    rawDate: '',
                    rawTime: ''
                };

                return {
                    ...base,
                    ...(overrides[id] || {}),
                    id,
                    source: 'default'
                };
            })
            .filter(item =>
                !hidden.has(
                    String(item.id)
                )
            );
    }

    function getAllAnnouncements() {
        const stored =
            getStoredAnnouncements()
                .map(item => ({
                    ...item,
                    id: String(item.id),
                    source: 'local'
                }));

        return [
            ...stored,
            ...normalizeDefaultAnnouncements()
        ];
    }

    function renderAllAnnouncementCards() {
        if (!announcementsGrid) {
            return;
        }

        const items =
            getAllAnnouncements();

        announcementsGrid.innerHTML =
            items
                .map(buildAnnouncementCard)
                .join('');

        const placeholderCount =
            Math.max(
                0,
                8 - items.length
            );

        for (
            let index = 0;
            index < placeholderCount;
            index += 1
        ) {
            announcementsGrid.insertAdjacentHTML(
                'beforeend',
                `
                    <div
                        class="platform-empty-card"
                        aria-hidden="true"
                    ></div>
                `
            );
        }
    }

    function findAnnouncementById(id) {
        const stringId =
            String(id);

        return getAllAnnouncements()
            .find(item =>
                String(item.id) === stringId
            ) || null;
    }

    function closeAllAnnouncementMenus() {
        announcementsGrid
            ?.querySelectorAll(
                '.platform-action-menu.is-open'
            )
            .forEach(menu => {
                menu.classList.remove(
                    'is-open'
                );

                const trigger =
                    menu.parentElement?.querySelector(
                        '.js-announcement-more'
                    );

                trigger?.setAttribute(
                    'aria-expanded',
                    'false'
                );
            });
    }

    function formatCreatedAnnouncementDate(date) {
        return new Intl.DateTimeFormat(
            'en-US',
            {
                month: 'long',
                day: 'numeric',
                year: 'numeric'
            }
        ).format(date);
    }

    function formatCreatedAnnouncementTime(date) {
        return new Intl.DateTimeFormat(
            'en-US',
            {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            }
        ).format(date);
    }

    function setAnnouncementModalMode(mode) {
        const isEdit =
            mode === 'edit';

        const heading =
            document.getElementById(
                'create-announcement-title'
            );

        const subtitle =
            heading
                ?.parentElement
                ?.querySelector(
                    '.platform-modal-subtitle'
                );

        if (heading) {
            heading.textContent =
                isEdit
                    ? 'Edit Announcement'
                    : 'Create Announcement';
        }

        if (subtitle) {
            subtitle.textContent =
                isEdit
                    ? 'Update the announcement details below.'
                    : 'Post an announcement to keep all users informed.';
        }

        if (submitAnnouncement) {
            submitAnnouncement.textContent =
                isEdit
                    ? 'Save Changes'
                    : 'Upload';
        }
    }

    function setCheckedRadio(
        name,
        value
    ) {
        document
            .querySelectorAll(
                `input[name="${name}"]`
            )
            .forEach(radio => {
                radio.checked =
                    radio.value === value;
            });
    }

    function refreshAnnouncementChoiceCards() {
        document
            .querySelectorAll(
                '[data-announcement-type-card]'
            )
            .forEach(card => {
                const radio =
                    card.querySelector(
                        'input[name="announcement_type"]'
                    );

                card.classList.toggle(
                    'is-selected',
                    Boolean(radio?.checked)
                );
            });

        document
            .querySelectorAll(
                '[data-announcement-status-card]'
            )
            .forEach(card => {
                const radio =
                    card.querySelector(
                        'input[name="announcement_status"]'
                    );

                card.classList.toggle(
                    'is-selected',
                    Boolean(radio?.checked)
                );
            });
    }

    function parseDisplayDateToInput(value) {
        if (!value) {
            return '';
        }

        const parsed =
            new Date(value);

        if (
            Number.isNaN(
                parsed.getTime()
            )
        ) {
            return '';
        }

        const year =
            parsed.getFullYear();

        const month =
            String(
                parsed.getMonth() + 1
            ).padStart(2, '0');

        const day =
            String(
                parsed.getDate()
            ).padStart(2, '0');

        return `${year}-${month}-${day}`;
    }

    function parseDisplayTimeToInput(value) {
        if (!value) {
            return '';
        }

        const match =
            String(value)
                .trim()
                .match(
                    /^(\d{1,2}):(\d{2})\s*(AM|PM)$/i
                );

        if (!match) {
            return '';
        }

        let hour =
            Number(match[1]);

        const minute =
            match[2];

        const meridiem =
            match[3].toUpperCase();

        if (
            meridiem === 'PM' &&
            hour !== 12
        ) {
            hour += 12;
        }

        if (
            meridiem === 'AM' &&
            hour === 12
        ) {
            hour = 0;
        }

        return `${String(hour).padStart(2, '0')}:${minute}`;
    }

    function fillAnnouncementForm(item) {
        announcementTitleInput.value =
            item.title || '';

        announcementMessageInput.value =
            item.description || '';

        setCheckedRadio(
            'announcement_type',
            item.type || 'Announcement'
        );

        setCheckedRadio(
            'announcement_audience',
            item.audience || 'All Users'
        );

        setCheckedRadio(
            'announcement_status',
            item.status === 'Scheduled'
                ? 'Schedule'
                : 'Publish Now'
        );

        announcementDateInput.value =
            item.rawDate ||
            (
                item.status === 'Scheduled'
                    ? parseDisplayDateToInput(
                        item.date
                    )
                    : ''
            );

        announcementTimeInput.value =
            item.rawTime ||
            (
                item.status === 'Scheduled'
                    ? parseDisplayTimeToInput(
                        item.time
                    )
                    : ''
            );

        currentAnnouncementBannerDataUrl =
            item.bannerDataUrl || '';

        currentAnnouncementBannerName =
            item.bannerName || '';

        const bannerFileElement =
            document.getElementById(
                'announcement-banner-file'
            );

        if (bannerFileElement) {
            bannerFileElement.textContent =
                currentAnnouncementBannerName ||
                (
                    currentAnnouncementBannerDataUrl
                        ? 'Attached cover photo'
                        : ''
                );
        }

        const titleCount =
            document.getElementById(
                'announcement-title-count'
            );

        const messageCount =
            document.getElementById(
                'announcement-message-count'
            );

        if (titleCount) {
            titleCount.textContent =
                `${announcementTitleInput.value.length}/80`;
        }

        if (messageCount) {
            messageCount.textContent =
                `${announcementMessageInput.value.length}/500`;
        }

        refreshAnnouncementChoiceCards();

        announcementPreviewTouched =
            true;

        updateAnnouncementScheduleFields();
        renderAnnouncementPreview();
    }

    function openAnnouncementEditor(item) {
        if (!item) {
            return;
        }

        editingAnnouncementId =
            String(item.id);

        editingAnnouncementSource =
            item.source || 'local';

        resetAnnouncementModal();

        /*
         * resetAnnouncementModal restores create defaults,
         * so set edit state again before prefilling.
         */
        editingAnnouncementId =
            String(item.id);

        editingAnnouncementSource =
            item.source || 'local';

        setAnnouncementModalMode(
            'edit'
        );

        fillAnnouncementForm(
            item
        );

        openPlatformModal(
            announcementModal,
            false
        );
    }

    function collectAnnouncementFormData() {
        const type =
            document.querySelector(
                'input[name="announcement_type"]:checked'
            )?.value || 'Announcement';

        const audience =
            document.querySelector(
                'input[name="announcement_audience"]:checked'
            )?.value || 'All Users';

        const selectedStatus =
            document.querySelector(
                'input[name="announcement_status"]:checked'
            )?.value || 'Publish Now';

        const title =
            announcementTitleInput.value.trim();

        const description =
            announcementMessageInput.value.trim();

        if (!title || !description) {
            window.alert(
                'Please enter the announcement title and message.'
            );

            return null;
        }

        let status =
            'Published';

        let date =
            '';

        let time =
            '';

        if (selectedStatus === 'Schedule') {
            if (
                !announcementDateInput.value ||
                !announcementTimeInput.value
            ) {
                window.alert(
                    'Please select the publish date and time.'
                );

                return null;
            }

            status =
                'Scheduled';

            date =
                formatPreviewDate(
                    announcementDateInput.value
                );

            time =
                formatPreviewTime(
                    announcementTimeInput.value
                );
        } else {
            const now =
                new Date();

            date =
                formatCreatedAnnouncementDate(
                    now
                );

            time =
                formatCreatedAnnouncementTime(
                    now
                );
        }

        return {
            id:
                editingAnnouncementId ||
                String(Date.now()),

            source:
                editingAnnouncementSource ||
                'local',

            type,
            title,
            description,
            audience,
            status,
            date,
            time,

            rawDate:
                selectedStatus === 'Schedule'
                    ? announcementDateInput.value
                    : '',

            rawTime:
                selectedStatus === 'Schedule'
                    ? announcementTimeInput.value
                    : '',

            bannerDataUrl:
                currentAnnouncementBannerDataUrl,

            bannerName:
                currentAnnouncementBannerName
        };
    }


    function saveAnnouncementChanges(item) {
        const id =
            String(item.id);

        if (
            editingAnnouncementSource ===
            'default'
        ) {
            const overrides =
                getDefaultAnnouncementOverrides();

            overrides[id] = {
                ...item,
                id,
                source: 'default'
            };

            saveDefaultAnnouncementOverrides(
                overrides
            );
        } else {
            const stored =
                getStoredAnnouncements();

            const index =
                stored.findIndex(entry =>
                    String(entry.id) === id
                );

            const nextItem = {
                ...item,
                id,
                source: 'local'
            };

            if (index >= 0) {
                stored[index] =
                    nextItem;
            } else {
                stored.unshift(
                    nextItem
                );
            }

            saveStoredAnnouncements(
                stored
            );
        }
    }

    function deleteAnnouncement(item) {
        if (!item) {
            return;
        }

        const confirmed =
            window.confirm(
                `Delete "${item.title}"?`
            );

        if (!confirmed) {
            return;
        }

        const id =
            String(item.id);

        if (
            item.source === 'default'
        ) {
            const hidden =
                getHiddenDefaultAnnouncementIds();

            if (!hidden.includes(id)) {
                hidden.push(id);
            }

            saveHiddenDefaultAnnouncementIds(
                hidden
            );
        } else {
            const stored =
                getStoredAnnouncements()
                    .filter(entry =>
                        String(entry.id) !== id
                    );

            saveStoredAnnouncements(
                stored
            );
        }

        closeAllAnnouncementMenus();
        renderAllAnnouncementCards();
    }

    if (announcementsGrid) {
        announcementsGrid.addEventListener(
            'click',
            function (event) {
                const trigger =
                    event.target.closest(
                        '.js-announcement-more'
                    );

                if (trigger) {
                    event.stopPropagation();

                    const wrap =
                        trigger.closest(
                            '.platform-more-wrap'
                        );

                    const menu =
                        wrap?.querySelector(
                            '.platform-action-menu'
                        );

                    const alreadyOpen =
                        menu?.classList.contains(
                            'is-open'
                        );

                    closeAllAnnouncementMenus();

                    if (
                        menu &&
                        !alreadyOpen
                    ) {
                        menu.classList.add(
                            'is-open'
                        );

                        trigger.setAttribute(
                            'aria-expanded',
                            'true'
                        );
                    }

                    return;
                }

                const actionButton =
                    event.target.closest(
                        '[data-announcement-action]'
                    );

                if (!actionButton) {
                    return;
                }

                event.stopPropagation();

                const card =
                    actionButton.closest(
                        '[data-announcement-id]'
                    );

                const item =
                    findAnnouncementById(
                        card?.dataset
                            .announcementId
                    );

                if (!item) {
                    return;
                }

                const action =
                    actionButton.dataset
                        .announcementAction;

                closeAllAnnouncementMenus();

                if (action === 'edit') {
                    openAnnouncementEditor(
                        item
                    );

                    return;
                }

                if (action === 'delete') {
                    deleteAnnouncement(
                        item
                    );
                }
            }
        );
    }

    document.addEventListener(
        'click',
        function (event) {
            if (
                !event.target.closest(
                    '.platform-more-wrap'
                )
            ) {
                closeAllAnnouncementMenus();
            }
        }
    );


    /* =========================================================
       DEMO SUBMIT BUTTONS
       Front-end only until backend routes are connected.
    ========================================================== */
    submitAnnouncement.addEventListener(
        'click',
        function () {
            const announcement =
                collectAnnouncementFormData();

            if (!announcement) {
                return;
            }

            if (editingAnnouncementId) {
                saveAnnouncementChanges(
                    announcement
                );
            } else {
                const stored =
                    getStoredAnnouncements();

                stored.unshift({
                    ...announcement,
                    id: String(
                        announcement.id
                    ),
                    source: 'local'
                });

                saveStoredAnnouncements(
                    stored
                );
            }

            editingAnnouncementId = null;
            editingAnnouncementSource = null;

            renderAllAnnouncementCards();

            switchPlatformTab(
                'announcements'
            );

            closePlatformModal(
                announcementModal
            );
        }
    );

    submitPolicy.addEventListener(
        'click',
        function () {
            closePlatformModal(
                policyModal
            );
        }
    );


    /* =========================================================
       ESCAPE KEY
    ========================================================== */
    document.addEventListener(
        'keydown',
        function (event) {
            if (
                event.key !== 'Escape'
            ) {
                return;
            }

            if (
                announcementModal.classList.contains(
                    'is-open'
                )
            ) {
                closePlatformModal(
                    announcementModal
                );

                return;
            }

            if (
                policyModal.classList.contains(
                    'is-open'
                )
            ) {
                closePlatformModal(
                    policyModal
                );
            }
        }
    );


    switchPlatformTab(
        'announcements'
    );

    resetAnnouncementModal();
    resetPolicyModal();
    updateAnnouncementScheduleFields();
    renderAllAnnouncementCards();

});
</script>
@endpush

@endsection
