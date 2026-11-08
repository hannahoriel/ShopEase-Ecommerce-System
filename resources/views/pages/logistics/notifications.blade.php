
{{-- =========================================================
    SHOPEASE — LOGISTICS NOTIFICATIONS
    resources/views/pages/logistics/logistics/notifications.blade.php

    Uses:
    - Logistics sidebar and navbar
    - Laravel database notifications
    - Poppins font
    - Responsive admin dashboard layout
    - No hardcoded notification records
========================================================= --}}

@php
    // Only the authenticated Logistics account's notifications are queried.
    // Each company sees only notifications addressed to its authenticated account.
    $pageName = 'Notifications';
    $companyUser = auth()->user();
    $activeFilter = request()->query('filter', 'all');
    if (!in_array($activeFilter, ['all', 'unread', 'read'], true)) $activeFilter = 'all';
    $searchQuery = trim((string) request()->query('search', ''));
    $selectedDate = request()->query('date', '');
    $selectedDate = is_string($selectedDate) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $selectedDate)
        && checkdate((int) substr($selectedDate, 5, 2), (int) substr($selectedDate, 8, 2), (int) substr($selectedDate, 0, 4))
        ? $selectedDate : '';

    // Filtering happens before pagination, on the server, with no cross-account access.
    $baseNotifications = $companyUser->notifications();
    $totalNotifications = (clone $baseNotifications)->count();
    $unreadNotifications = (clone $baseNotifications)->whereNull('read_at')->count();
    $readNotifications = $totalNotifications - $unreadNotifications;
    $notificationQuery = clone $baseNotifications;
    if ($activeFilter === 'unread') $notificationQuery->whereNull('read_at');
    if ($activeFilter === 'read') $notificationQuery->whereNotNull('read_at');
    if ($searchQuery !== '') $notificationQuery->where('data', 'like', '%' . addcslashes($searchQuery, '%_\\') . '%');
    if ($selectedDate !== '') $notificationQuery->whereDate('created_at', $selectedDate);
    $notificationItems = $notificationQuery->latest('created_at')->paginate(10)->withQueryString();

    $formatNotificationTime = function ($notification) {
        if (!$notification->created_at) return 'Date unavailable';
        return $notification->created_at->diffForHumans() . ' · ' .
            $notification->created_at->format('M d, Y \a\t h:i A');
    };
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>Logistics Notifications - ShopEase</title>

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >
    <style>
/* =========================================
           ADMIN NOTIFICATIONS — BASE
        ========================================= */

        .admin-notifications-page,
        .admin-notifications-page *,
        .admin-notifications-page *::before,
        .admin-notifications-page *::after {
            box-sizing: border-box;
        }

        .admin-notifications-page {
            min-height: 100vh;
            margin-left: 260px;
            padding-top: 125px;
            background: #FCF8F6;
            color: #17120F;
            font-family: 'Poppins', sans-serif;
            transition: margin-left 200ms ease;
        }

        .admin-notifications-page button,
        .admin-notifications-page input {
            font-family: 'Poppins', sans-serif;
        }

        .admin-notifications-content {
            width: 100%;
            padding: 24px 25px 48px 20px;
        }

        .sn-mark-all {
            height: 37px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 0 15px;
            border: 1px solid #9E241F;
            border-radius: 8px;
            background: #9E241F;
            color: #FFFFFF;
            font-size: 11px;
            font-weight: 500;
            cursor: pointer;
            transition: background .2s, transform .2s;
        }

        .sn-mark-all svg {
            width: 15px;
            height: 15px;
        }

        .sn-mark-all:hover:not(:disabled) {
            background: #841C19;
            transform: translateY(-1px);
        }

        .sn-mark-all:disabled {
            opacity: .45;
            cursor: not-allowed;
        }

        /* =========================================
           MAIN CARD
        ========================================= */

        .sn-main-card {
            overflow: hidden;
            background: #FFFFFF;
            border: 1px solid #F0E9E6;
            border-radius: 14px;
            box-shadow: 0 2px 12px rgba(42, 20, 15, .04);
        }

        .sn-card-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
            padding: 20px 22px 0;
        }

        /* =========================================
           SEARCH
        ========================================= */

        .sn-toolbar-mark-all {
            flex: 0 0 auto;
        }

        .sn-search-form {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            flex: 1 1 400px;
            max-width: 100%;
        }

        .sn-search-field {
            position: relative;
            width: 330px;
            flex: 0 0 330px;
            max-width: 100%;
        }

        .sn-date-input {
            width: 170px;
            height: 36px;
            padding: 0 11px;
            border: 1px solid #D9D6D4;
            border-radius: 8px;
            background: #FFFFFF;
            color: #76716E;
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            font-weight: 400;
            outline: none;
            cursor: pointer;
            color-scheme: light;
        }

        .sn-date-input:focus {
            border-color: #7B1B1B;
            box-shadow: 0 0 0 2px rgba(123, 27, 27, .10);
        }

        .sn-date-input::-webkit-datetime-edit,
        .sn-date-input::-webkit-datetime-edit-fields-wrapper,
        .sn-date-input::-webkit-datetime-edit-text,
        .sn-date-input::-webkit-datetime-edit-month-field,
        .sn-date-input::-webkit-datetime-edit-day-field,
        .sn-date-input::-webkit-datetime-edit-year-field {
            color: #76716E;
        }

        .sn-date-input::-webkit-calendar-picker-indicator {
            cursor: pointer;
            opacity: .6;
        }

        .sn-clear-date {
            color: #817A76;
            font-size: 11px;
            font-weight: 500;
            text-decoration: none;
        }

        .sn-clear-date:hover { color: #9E241F; }

        .sn-search-input {
            width: 100%;
            height: 36px;
            padding: 0 38px 0 12px;
            border: 1px solid #D9D6D4;
            border-radius: 8px;
            outline: none;
            background: #FFFFFF;
            color: #76716E;
            font-size: 13px;
            font-weight: 400;
            transition: border-color .2s, box-shadow .2s;
        }

        .sn-search-input::placeholder {
            color: #76716E;
            opacity: 1;
        }

        .sn-search-input:focus {
            border-color: #7B1B1B;
            box-shadow: 0 0 0 2px rgba(123, 27, 27, .10);
        }

        .sn-search-button {
            position: absolute;
            right: 3px;
            top: 3px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            padding: 0;
            border: 0;
            border-radius: 6px;
            background: transparent;
            color: #76716E;
            cursor: pointer;
        }

        .sn-search-button svg {
            width: 17px;
            height: 17px;
        }

        .sn-search-button:hover {
            background: #F6F3F2;
        }

        /* =========================================
           TABS
        ========================================= */

        .sn-tabs {
            display: flex;
            align-items: center;
            gap: 30px;
            margin-top: 13px;
            padding: 0 22px;
            border-bottom: 1px solid #EEE9E6;
            overflow-x: auto;
            scrollbar-width: none;
        }

        .sn-tabs::-webkit-scrollbar {
            display: none;
        }

        .sn-tab {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            min-height: 49px;
            color: #96908D;
            text-decoration: none;
            white-space: nowrap;
            font-size: 12px;
            font-weight: 400;
            transition: color .2s;
        }

        .sn-tab:hover {
            color: #9E241F;
        }

        .sn-tab.active {
            color: #9E241F;
            font-weight: 600;
        }

        .sn-tab.active::after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            bottom: -1px;
            height: 3px;
            border-radius: 4px 4px 0 0;
            background: #9E241F;
        }

        .sn-tab-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 20px;
            min-height: 20px;
            padding: 2px 6px;
            border-radius: 6px;
            background: #F5F2F0;
            color: #89817D;
            font-size: 10px;
            font-weight: 500;
        }

        .sn-tab.active .sn-tab-count {
            background: #FBE3DD;
            color: #9E241F;
        }

        /* =========================================
           NOTIFICATION ROW
        ========================================= */

        .sn-list {
            display: flex;
            flex-direction: column;
        }

        .sn-notification {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            padding: 19px 22px;
            border-bottom: 1px solid #F1ECEA;
            background: #FFFFFF;
            transition: background .18s;
        }

        .sn-notification:last-child {
            border-bottom: 0;
        }

        .sn-notification:hover {
            background: #FFFAF8;
        }

        .sn-notification.is-unread {
            background: #FFFAF7;
        }

        .sn-notification.is-unread:hover {
            background: #FFF4EF;
        }

        .sn-notification-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 43px;
            height: 43px;
            flex: 0 0 43px;
            border-radius: 11px;
            background: #FFF0EC;
            color: #9E241F;
        }

        .sn-notification-icon svg {
            width: 20px;
            height: 20px;
        }

        .sn-notification-icon.warning {
            background: #FFF1D9;
            color: #CA8123;
        }

        .sn-notification-icon.order {
            background: #EAF0FF;
            color: #547AC6;
        }

        .sn-notification-icon.success {
            background: #E5F2E4;
            color: #328C40;
        }

        .sn-notification-icon.message {
            background: #F2E8FF;
            color: #8654AD;
        }

        .sn-notification-body {
            flex: 1;
            min-width: 0;
        }

        .sn-notification-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 6px;
        }

        .sn-notification-title {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
            color: #25201E;
            font-size: 12px;
            line-height: 1.65;
            font-weight: 500;
            overflow-wrap: anywhere;
        }

        .sn-notification.is-unread .sn-notification-title {
            color: #17120F;
            font-weight: 600;
        }

        .sn-unread-dot {
            width: 7px;
            height: 7px;
            flex: 0 0 7px;
            border-radius: 50%;
            background: #A52A2A;
        }

        .sn-notification-time {
            color: #AAA3A0;
            font-size: 10px;
            line-height: 1.6;
            font-weight: 400;
        }

        .sn-notification-description {
            margin: 0;
            max-width: 850px;
            color: #817A76;
            font-size: 11px;
            line-height: 1.85;
            font-weight: 400;
            overflow-wrap: anywhere;
        }

        .sn-notification-footer {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            margin-top: 12px;
        }

        .sn-action {
            padding: 0;
            border: 0;
            background: transparent;
            color: #9E241F;
            font-size: 10px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: color .2s;
        }

        .sn-action:hover {
            color: #701713;
            text-decoration: underline;
        }

        .sn-action.muted {
            color: #8B8480;
        }

        .sn-action.muted:hover {
            color: #55504D;
        }

        .sn-read-label {
            color: #AAA39F;
            font-size: 10px;
            font-weight: 400;
        }

        .sn-notification-side {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 0 0 auto;
        }

        .sn-status-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 5px 10px;
            border-radius: 999px;
            background: #F2F0EF;
            color: #837D7A;
            font-size: 9px;
            font-weight: 500;
        }

        .sn-status-pill.unread {
            background: #FDE7E0;
            color: #9E241F;
        }

        /* =========================================
           EMPTY STATE
        ========================================= */

        .sn-empty-state {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            min-height: 320px;
            padding: 42px 20px;
            text-align: center;
        }

        .sn-empty-icon {
            display: grid;
            place-items: center;
            width: 68px;
            height: 68px;
            margin-bottom: 18px;
            border-radius: 18px;
            background: #FFF0EC;
            color: #A52A2A;
        }

        .sn-empty-icon svg {
            width: 30px;
            height: 30px;
        }

        .sn-empty-state h3 {
            margin: 0 0 8px;
            color: #292320;
            font-size: 15px;
            font-weight: 600;
        }

        .sn-empty-state p {
            max-width: 365px;
            margin: 0;
            color: #99918E;
            font-size: 11px;
            line-height: 1.9;
        }

        /* =========================================
           PAGINATION
        ========================================= */

        .sn-pagination {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 14px;
            min-height: 64px;
            padding: 14px 22px;
            border-top: 1px solid #F0EBE8;
        }

        .sn-pagination-info {
            color: #98908D;
            font-size: 11px;
        }

        .sn-pagination-buttons {
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .sn-page-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 29px;
            height: 29px;
            padding: 0 8px;
            border: 1px solid #EEE6E2;
            border-radius: 7px;
            background: #FFFFFF;
            color: #6E6561;
            font-size: 11px;
            font-weight: 500;
            text-decoration: none;
        }

        .sn-page-button:hover {
            background: #FFF2EE;
            border-color: #F1D4CD;
        }

        .sn-page-button.active {
            background: #9E241F;
            border-color: #9E241F;
            color: #FFFFFF;
        }

        .sn-page-button.disabled {
            opacity: .35;
            pointer-events: none;
        }

        /* =========================================
           FEEDBACK
        ========================================= */

        .sn-flash {
            display: flex;
            align-items: center;
            gap: 9px;
            margin-bottom: 16px;
            padding: 12px 16px;
            border: 1px solid #D4E9D2;
            border-radius: 9px;
            background: #F0F9EE;
            color: #347239;
            font-size: 11px;
            font-weight: 500;
        }

        /* =========================================
           RESPONSIVE
        ========================================= */

        /* Registrations search field expands on narrower admin layouts. */
        @media (max-width: 1279px) {
            .sn-search-field {
                width: 100%;
                flex: 1 1 100%;
            }
        }

        @media (max-width: 760px) {
            .admin-notifications-page {
                margin-left: 0 !important;
                padding-top: 104px;
            }

            .admin-notifications-content {
                padding: 20px 16px 36px;
            }

            .sn-card-toolbar {
                padding: 17px 16px 0;
            }

            .sn-tabs {
                padding: 0 16px;
                gap: 21px;
            }

            .sn-notification {
                padding: 17px 16px;
                gap: 11px;
            }

            .sn-notification-icon {
                width: 37px;
                height: 37px;
                flex-basis: 37px;
            }

            .sn-notification-icon svg {
                width: 18px;
                height: 18px;
            }

            .sn-pagination {
                padding: 15px 16px;
            }
        }

        @media (max-width: 520px) {
            .sn-search-form,
            .sn-search-field,
            .sn-date-input {
                width: 100%;
            }

            .sn-toolbar-mark-all,
            .sn-mark-all {
                width: 100%;
            }

            .sn-notification-side {
                display: none;
            }


        }
    
/* FINAL: same Seller Compliance content frame and 300ms sidebar movement. */
@media (min-width:901px){
 html body #admin-content.admin-notifications-page{box-sizing:border-box!important;width:auto!important;min-width:0!important;max-width:none!important;margin-left:260px!important;transition:margin-left 300ms ease!important}
 html body:has(#admin-sidebar.w-20) #admin-content.admin-notifications-page,
 html body:has(#admin-sidebar.collapsed) #admin-content.admin-notifications-page,
 html body.sidebar-collapsed #admin-content.admin-notifications-page{margin-left:80px!important}
 html body .admin-notifications-content{box-sizing:border-box;width:100%!important;max-width:none!important;padding-left:25px!important;padding-right:25px!important}
}
@media (min-width:761px) and (max-width:900px){html body #admin-content.admin-notifications-page{margin-left:80px!important;transition:margin-left 300ms ease!important}html body .admin-notifications-content{padding-left:24px!important;padding-right:24px!important}}
@media (max-width:760px){html body #admin-content.admin-notifications-page{margin-left:0!important;transition:margin-left 300ms ease!important}html body .admin-notifications-content{padding-left:16px!important;padding-right:16px!important}}

/* Logistics layout overrides; preserves Admin notifications styling. */
body.lg-body .admin-notifications-page{margin-left:260px;padding-top:106px;min-height:100vh;transition:margin-left .3s ease}
body.lg-body.lg-collapsed .admin-notifications-page{margin-left:80px}
@media(max-width:900px){body.lg-body .admin-notifications-page{margin-left:80px}}
@media(max-width:700px){body.lg-body .admin-notifications-page,body.lg-body.lg-collapsed .admin-notifications-page{margin-left:0;padding-top:85px}}
</style>
<style>


@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');
:root{--lg-maroon:#7B1B1B;--lg-dark:#52070B;--lg-peach:#FFE6DE;--lg-bg:#FCF8F6;--lg-line:#F0E9E6;--lg-ink:#17120F;--lg-muted:#8C8784}
*{box-sizing:border-box}body.lg-body{margin:0;background:var(--lg-bg);font-family:Poppins,sans-serif;color:var(--lg-ink)}button,input,select{font-family:inherit}button{cursor:pointer}a{text-decoration:none}body.lg-modal-open{overflow:hidden}
.lg-sidebar{position:fixed;left:0;top:0;bottom:0;width:260px;z-index:50;background:linear-gradient(155deg,#7B1B1B 0%,#52070B 80%);border-top-right-radius:58px;padding:20px 14px 18px;display:flex;flex-direction:column;transition:width .3s ease;box-shadow:4px 0 18px #52070b12;overflow:hidden;color:white}
.lg-sidebar-logo{height:74px;display:flex;align-items:center;justify-content:center;gap:7px;margin-bottom:20px;color:white;white-space:nowrap}.lg-sidebar-logo img{max-width:115px;max-height:53px;object-fit:contain}.lg-sidebar-logo>span{font-size:11px;letter-spacing:1.1px;font-weight:700}.lg-sidebar-logo small{display:block;font-size:8px;font-weight:400;letter-spacing:1.5px;opacity:.65}
.lg-sidebar-menu{overflow:auto;scrollbar-width:thin;flex:1}.lg-nav-category{margin:15px 13px 9px;font-size:10px;letter-spacing:1.4px;color:#f5c8c7;font-weight:600}.lg-nav-link{height:43px;width:100%;display:flex;align-items:center;gap:12px;padding:0 13px;border:0;background:none;color:#fff;border-radius:999px;font-size:12px;font-weight:500;text-align:left;margin:3px 0;white-space:nowrap;transition:background .2s}.lg-nav-link:hover{background:#ffffff1b}.lg-nav-link.active{background:#ffffff30}.lg-nav-unavailable{opacity:.64;cursor:default;user-select:none}.lg-nav-unavailable:hover{background:none}.lg-nav-link img{width:20px;height:20px;object-fit:contain;flex:0 0 20px}.lg-sidebar-bottom{border-top:1px solid #ffffff25;padding:14px 11px 3px;white-space:nowrap;font-size:11px}.lg-sidebar-bottom small{display:block;color:#ffc6bc;font-size:10px;margin-top:3px}
.lg-navbar{position:fixed;top:0;left:0;right:0;height:88px;background:var(--lg-peach);z-index:35;display:flex;align-items:center;justify-content:space-between;padding:0 26px 0 288px;transition:padding-left .3s ease;gap:15px}.lg-nav-left,.lg-nav-right{display:flex;align-items:center;gap:22px}.lg-nav-left h2{font-size:21px;font-weight:700;margin:0;white-space:nowrap}.lg-icon-button{border:0;background:transparent;display:grid;place-items:center;width:30px;height:34px;color:#52070B;flex-shrink:0}.lg-icon-button svg{width:24px;height:24px}.lg-icon-button img{width:24px;height:26px;object-fit:contain}.lg-navbar-caption{font-size:12px;color:#815d57}.lg-profile-wrap{position:relative}.lg-profile-btn{border:1px solid #ebe0dc;background:white;border-radius:10px;padding:6px 10px;display:flex;align-items:center;gap:9px;box-shadow:0 2px 8px #0000000b;color:#29211f;text-align:left;min-width:190px}.lg-profile-btn strong{display:block;font-size:12px;font-weight:600;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.lg-profile-btn small{font-size:10px;color:#a19b98}.lg-profile-chevron{margin-left:auto}.lg-profile-menu{position:absolute;right:0;top:53px;width:214px;border:1px solid var(--lg-line);box-shadow:0 12px 30px #0002;border-radius:13px;background:white;padding:8px;z-index:100}.lg-profile-menu[hidden]{display:none}.lg-profile-menu>span{display:block;font-size:10px;color:var(--lg-muted);padding:9px}.lg-profile-menu a,.lg-profile-menu button{display:block;width:100%;text-align:left;border:0;background:transparent;padding:10px;border-radius:7px;color:var(--lg-ink);font-size:12px}.lg-profile-menu a:hover,.lg-profile-menu button:hover{background:#FFF2EE}
.lg-main{margin-left:260px;padding:114px 25px 36px;min-height:100vh;transition:margin-left .3s ease;max-width:none}.lg-body.lg-collapsed .lg-sidebar{width:80px}.lg-body.lg-collapsed .lg-sidebar-logo>span,.lg-body.lg-collapsed .lg-nav-category,.lg-body.lg-collapsed .lg-nav-link span,.lg-body.lg-collapsed .lg-sidebar-bottom{display:none}.lg-body.lg-collapsed .lg-sidebar-logo img{max-width:60px}.lg-body.lg-collapsed .lg-nav-link{justify-content:center;padding:0}.lg-body.lg-collapsed .lg-main{margin-left:80px}.lg-body.lg-collapsed .lg-navbar{padding-left:108px}
.lg-welcome{display:flex;justify-content:space-between;align-items:center;gap:20px;margin-bottom:16px}.lg-overline{font-size:10px;letter-spacing:1.3px;color:#9b1239;font-weight:700;margin:0 0 5px}.lg-welcome h1{margin:0;font-weight:650;font-size:24px}.lg-welcome p:not(.lg-overline){font-size:12px;color:var(--lg-muted);line-height:1.7;margin:4px 0 0}.lg-welcome-actions{display:flex;align-items:center;gap:11px;flex-shrink:0}.lg-date{padding:11px 13px;background:white;border:1px solid var(--lg-line);border-radius:9px;color:#665b56;font-size:11px;white-space:nowrap}.lg-btn{border-radius:8px;padding:10px 15px;min-height:37px;font-weight:600;font-size:11px;display:inline-flex;justify-content:center;align-items:center;gap:7px;transition:background .2s,box-shadow .2s;white-space:nowrap}.lg-btn-primary{color:white;border:1px solid #7B1B1B;background:#7B1B1B}.lg-btn-primary:hover{background:#621515}.lg-btn-outline{color:#7B1B1B;background:white;border:1px solid #d9bfc0}.lg-btn-outline:hover{background:#fff6f5}
.lg-notice{display:flex;gap:10px;align-items:start;margin-bottom:19px;font-size:11px;line-height:1.7;color:#80572e;padding:11px 14px;border:1px solid #f3e4d5;background:#fff9f2;border-radius:10px}.lg-notice strong{font-weight:600}.lg-notice-dot{width:8px;height:8px;background:#cd9250;flex:0 0 8px;border-radius:50%;margin-top:5px}
.lg-stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:15px;margin-bottom:20px}.lg-stat{display:flex;align-items:center;gap:12px;padding:17px 16px;min-height:91px;background:white;border:1px solid var(--lg-line);border-radius:16px;box-shadow:0 2px 12px rgba(42,20,15,.05)}.lg-stat-icon{width:40px;height:40px;object-fit:contain;flex-shrink:0}.lg-stat strong{display:block;font-size:23px;line-height:1.2;font-weight:600;white-space:nowrap}.lg-stat span{display:block;font-size:11px;color:var(--lg-muted);margin-top:5px}
.lg-main-grid{display:grid;grid-template-columns:minmax(0,1.66fr) minmax(320px,1fr);gap:18px;align-items:start}.lg-side-stack{display:grid;gap:18px}.lg-panel{background:white;border:1px solid var(--lg-line);border-radius:16px;box-shadow:0 1px 4px #00000008;min-width:0;overflow:hidden}.lg-panel-header{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:19px 19px 15px}.lg-panel-header h2{font-size:15px;font-weight:600;margin:0}.lg-panel-header p{font-size:11px;line-height:1.6;color:var(--lg-muted);margin:4px 0 0}.lg-panel-icon{width:30px;height:30px;object-fit:contain}.lg-toolbar{display:flex;gap:9px;padding:0 19px 16px;flex-wrap:wrap}.lg-toolbar select,.lg-search-box{height:37px;background:white;border:1px solid #D9D6D4;border-radius:8px;font-size:11px;color:#68615e}.lg-toolbar select{padding:0 8px;max-width:170px}.lg-search-box{display:flex;gap:6px;align-items:center;padding:0 10px;flex:1;min-width:190px}.lg-search-box span{font-size:19px}.lg-search-box input{width:100%;outline:none;border:0;background:none;font-size:11px}.lg-table-scroll{overflow:auto}.lg-table{width:100%;border-collapse:collapse;min-width:625px}.lg-table th{font-weight:500;font-size:11px;color:var(--lg-muted);padding:13px 15px;white-space:nowrap;text-align:left;border-bottom:1px solid #E5E7EB}.lg-table td{font-size:11px;padding:14px 15px;border-bottom:1px solid #f0edef;color:var(--lg-ink);vertical-align:middle}.lg-table tr:hover td{background:#FFFAF8}.lg-table tr:last-child td{border-bottom:0}.lg-table td strong{display:block;font-size:11px;font-weight:600}.lg-table td small{display:block;color:#9c9692;font-size:10px;margin-top:4px}.lg-tag{display:inline-block;border-radius:20px;padding:5px 9px;font-size:10px;white-space:nowrap}.lg-tag.sorting{color:#9b1239;background:#fce9ee}.lg-tag.delivery{color:#765422;background:#fff0d9}.lg-tag.active{color:#2d7745;background:#e9f7ec}.lg-table-empty{text-align:center;padding:38px 14px!important;color:#8c8784!important}.lg-table-foot{display:flex;justify-content:space-between;color:var(--lg-muted);font-size:10px;padding:13px 18px;border-top:1px solid #ece9e7;gap:10px}
.lg-scan-form{display:grid;gap:8px;padding:0 19px 20px}.lg-scan-form label,.lg-field label{font-size:11px;font-weight:500;color:#2b2624}.lg-scan-form label span,.lg-field label span{color:#d32443}.lg-scan-form input,.lg-scan-form select,.lg-field input,.lg-field select{height:39px;border:1px solid #D9D6D4;border-radius:8px;background:#fff;padding:0 11px;font-size:11px;color:#302b29;min-width:0;outline:none}.lg-scan-form input:focus,.lg-scan-form select:focus,.lg-field input:focus,.lg-field select:focus{border-color:#9b1239;box-shadow:0 0 0 2px #9b123916}.lg-scan-form .lg-full{margin-top:8px;width:100%}.lg-help{font-size:10px;color:#9b9290;line-height:1.65;margin:6px 0 0}.lg-flow{padding-bottom:15px}.lg-flow-step{display:flex;align-items:flex-start;gap:13px;margin:0 19px;padding:13px 0;border-top:1px solid #f2ebeb}.lg-flow-step>span{display:grid;place-items:center;color:#9b1239;background:#fce9ee;border-radius:8px;width:30px;height:30px;flex:0 0 30px;font-size:11px;font-weight:700}.lg-flow-step b{font-size:11px;font-weight:600;display:block;margin-top:1px}.lg-flow-step small{font-size:10px;color:var(--lg-muted);display:block;line-height:1.6;margin-top:3px}
.lg-lower-grid{display:grid;grid-template-columns:minmax(0,1.66fr) minmax(320px,1fr);gap:18px;align-items:stretch;margin-top:18px}.lg-activity-table{min-width:580px}.lg-soft-pill{border-radius:20px;background:#fce9ee;color:#9b1239;padding:6px 10px;font-size:10px;white-space:nowrap}.lg-earnings-total{margin:0 19px 15px;background:#fff9f7;border:1px solid #f3e5e0;border-radius:12px;padding:15px}.lg-earnings-total small,.lg-earnings-total span{display:block;color:var(--lg-muted);font-size:10px}.lg-earnings-total strong{display:block;font-size:24px;margin:4px 0;color:#7B1B1B}.lg-earnings-row{display:flex;justify-content:space-between;gap:9px;border-bottom:1px solid #f3eded;padding:11px 0;margin:0 19px;font-size:11px}.lg-earnings-row span{color:var(--lg-muted)}.lg-earnings-row b{font-weight:600}.lg-lower-grid .lg-help{margin:12px 19px 17px}.lg-announcements{margin-top:18px;padding-bottom:15px}.lg-announcements>*:not(.lg-panel-header){margin:0 18px}
.lg-modal{position:fixed;inset:0;background:#1a0b0b88;display:grid;place-items:center;z-index:200;padding:15px}.lg-modal[hidden]{display:none}.lg-modal-card{background:white;border-radius:17px;max-width:660px;width:100%;max-height:95vh;overflow:auto;box-shadow:0 24px 70px #0003;padding:24px}.lg-modal-header{display:flex;justify-content:space-between;gap:20px;margin-bottom:23px}.lg-modal-header h2{font-size:19px;margin:0}.lg-modal-header p{color:var(--lg-muted);font-size:11px;margin:5px 0}.lg-close{background:#f8f1ef;border:0;border-radius:50%;width:32px;height:32px;font-size:23px;color:#625452}.lg-form-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}.lg-field{display:grid;gap:7px}.lg-field small{font-size:10px;color:#9b9390;line-height:1.5}.lg-field.lg-wide{grid-column:1/-1}.lg-form-error{font-size:11px;color:#b42323;min-height:15px}.lg-modal-actions{display:flex;justify-content:flex-end;gap:10px}.lg-toast{position:fixed;right:22px;bottom:20px;background:white;border:1px solid #d6babc;border-left:4px solid #7B1B1B;border-radius:10px;padding:14px 19px;font-size:12px;max-width:370px;z-index:250;box-shadow:0 12px 35px #0002}.lg-toast[hidden]{display:none}
@media(max-width:1260px){.lg-stats{grid-template-columns:repeat(2,minmax(0,1fr))}.lg-main-grid,.lg-lower-grid{grid-template-columns:1fr}.lg-side-stack{grid-template-columns:1fr 1fr}}
@media(max-width:900px){.lg-sidebar{width:80px}.lg-sidebar-logo>span,.lg-nav-category,.lg-nav-link span,.lg-sidebar-bottom{display:none}.lg-sidebar-logo img{max-width:60px}.lg-nav-link{justify-content:center;padding:0}.lg-navbar{padding-left:108px}.lg-main{margin-left:80px}.lg-body.lg-collapsed .lg-sidebar{width:260px;box-shadow:0 0 0 100vw #0003}.lg-body.lg-collapsed .lg-sidebar-logo>span,.lg-body.lg-collapsed .lg-nav-category,.lg-body.lg-collapsed .lg-nav-link span,.lg-body.lg-collapsed .lg-sidebar-bottom{display:block}.lg-body.lg-collapsed .lg-nav-link{justify-content:flex-start;padding:0 13px}.lg-body.lg-collapsed .lg-main{margin-left:80px}.lg-body.lg-collapsed .lg-navbar{padding-left:108px}}
@media(max-width:700px){.lg-sidebar{transform:translateX(-100%);width:260px}.lg-body.lg-collapsed .lg-sidebar{transform:translateX(0)}.lg-navbar,.lg-body.lg-collapsed .lg-navbar{padding:0 14px;height:75px}.lg-nav-left h2{font-size:16px}.lg-nav-right{gap:8px}.lg-navbar-caption,.lg-profile-btn small,.lg-profile-chevron{display:none}.lg-profile-btn{min-width:unset}.lg-profile-btn strong{font-size:10px;max-width:92px}.lg-main,.lg-body.lg-collapsed .lg-main{margin-left:0;padding:96px 13px 25px}.lg-welcome{align-items:flex-start;flex-direction:column}.lg-welcome-actions{width:100%;flex-wrap:wrap}.lg-side-stack{grid-template-columns:1fr}.lg-panel-header{align-items:flex-start;flex-wrap:wrap}.lg-form-grid{grid-template-columns:1fr}.lg-field.lg-wide{grid-column:auto}}
@media(max-width:440px){.lg-stats{grid-template-columns:1fr 1fr;gap:8px}.lg-stat{padding:12px 9px;gap:7px;min-height:85px}.lg-stat-icon{width:27px;height:27px}.lg-stat strong{font-size:17px}.lg-stat span{font-size:9px}.lg-toolbar>*{max-width:none!important;width:100%}.lg-date{display:none}}
@media(prefers-reduced-motion:reduce){.lg-sidebar,.lg-main,.lg-navbar,.lg-btn{transition:none}}


/* Merged logistics command-center layout */
.lg-main{max-width:1650px;margin-right:auto}.lg-dash-top{display:grid;grid-template-columns:minmax(0,1fr) 340px;gap:18px;align-items:start;margin:0 0 18px}.lg-dash-left{display:grid;gap:18px;min-width:0}.lg-tracker{padding:22px 23px}.lg-clock{color:var(--lg-dark);font-weight:700;font-size:17px;font-variant-numeric:tabular-nums;margin:0 0 4px}.lg-clock-sub{color:var(--lg-muted);font-size:11px;margin:0 0 17px}.lg-tracker-row{display:flex;gap:9px;align-items:center}.lg-track-input{height:43px;min-width:0;flex:1;border:1px solid #ded1cd;border-radius:9px;padding:0 14px;outline:none;font-size:12px}.lg-track-input:focus{border-color:var(--lg-maroon);box-shadow:0 0 0 3px #7b1b1b10}.lg-tracker-row button{height:43px}.lg-tracker-result{font-size:11px;color:#715e59;margin:11px 0 0;min-height:15px}.lg-attention-row{display:flex;align-items:center;gap:13px;padding:16px 18px;border:1px solid #eddcd6;border-radius:12px;background:#fff}.lg-attention-icon{height:36px;width:36px;border-radius:11px;display:grid;place-items:center;background:#ffece5;color:#8a2222;font-size:19px;flex:0 0 auto}.lg-attention-row b{font-size:12px}.lg-attention-row p{font-size:11px;color:var(--lg-muted);margin:3px 0 0}.lg-attention-row .lg-btn{margin-left:auto}.lg-section-title{font-size:15px;font-weight:650;margin:0 0 12px}.lg-kpi-grid{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:11px}.lg-kpi{background:#fff;border:1px solid var(--lg-line);border-radius:13px;padding:15px 12px;min-height:113px;display:flex;gap:10px;align-items:center}.lg-kpi .lg-kpi-icon{width:35px;height:35px;border-radius:10px;display:grid;place-items:center;flex:0 0 auto;font-size:16px;background:#fff0eb;color:#7B1B1B}.lg-kpi:nth-child(3n+2) .lg-kpi-icon{background:#eaf2fe;color:#306ca0}.lg-kpi:nth-child(3n) .lg-kpi-icon{background:#edf8f1;color:#2c8051}.lg-kpi span{font-size:10px;line-height:1.45;color:var(--lg-muted);display:block}.lg-kpi strong{display:block;margin-top:5px;font-size:21px;line-height:1.15}.lg-dash-middle{display:grid;grid-template-columns:minmax(0,1.5fr) minmax(300px,1fr);gap:18px;margin-top:18px}.lg-announcements{margin:0;min-height:345px;background:var(--lg-dark);color:#fff;border-color:var(--lg-dark);padding:7px 5px 20px}.lg-announcements .lg-panel-header h2{color:#ffd0be;font-size:13px;letter-spacing:.5px;text-transform:uppercase}.lg-announcements .lg-panel-header p{color:#f0c2b8}.lg-announcements .lg-help{color:#f4c5bc!important;font-size:13px;margin-top:18px!important}.lg-announcements article small{color:#f5b7a9}.lg-announcements article h3{font-size:18px;margin:10px 0}.lg-announcements article p{font-size:12px;line-height:1.8;color:#f6dcd5}.lg-announcements .lg-btn-outline{background:transparent;color:#fff;border-color:#ffffff77;margin-top:20px!important}.lg-network-panel{margin-top:0}.lg-dash-notice{font-size:11px;color:#8a7974;margin:0 0 16px}.lg-kpi-note{font-size:10px;color:#a29590;margin:10px 0 0}.lg-activity-table{min-width:580px}
@media(max-width:1450px){.lg-kpi-grid{grid-template-columns:repeat(3,minmax(0,1fr))}.lg-dash-top{grid-template-columns:minmax(0,1fr) 310px}}
@media(max-width:1100px){.lg-dash-top,.lg-dash-middle{grid-template-columns:1fr}.lg-announcements{min-height:200px}.lg-kpi-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}
@media(max-width:650px){.lg-kpi-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.lg-kpi{padding:12px 10px}.lg-attention-row{flex-wrap:wrap}.lg-attention-row .lg-btn{margin-left:0}.lg-tracker-row{flex-wrap:wrap}.lg-tracker-row button{width:100%}.lg-clock{font-size:15px}}


/* Minimal time label, subordinate to parcel tracking */
.lg-tracker .lg-clock{display:inline-flex;align-items:center;gap:6px;margin:0 0 14px;font-size:11px;font-weight:400;letter-spacing:0;color:#988a85;font-variant-numeric:tabular-nums}
.lg-tracker .lg-clock:before{content:"";width:5px;height:5px;border-radius:50%;background:#bd9991;flex-shrink:0}
/* Announcements: compact right-column carousel */
.lg-announcements{display:flex;flex-direction:column;min-height:310px;padding:8px 6px 14px}
.lg-announcements .lg-panel-header{padding-bottom:12px}
.lg-announcements .lg-announcement-slides{margin:0 18px!important;flex:1;display:flex;align-items:center;min-height:155px}
.lg-announcements .lg-announcement-slides article{width:100%;animation:lgAnnouncementIn .25s ease}
.lg-announcements .lg-announcement-slides article[hidden]{display:none}
.lg-announcements .lg-announcement-slides article small{font-size:10px;letter-spacing:.45px;text-transform:uppercase}
.lg-announcements .lg-announcement-slides article h3{font-size:18px;line-height:1.35;margin:12px 0}
.lg-announcements .lg-announcement-slides article p{font-size:11px;line-height:1.75;margin:0}
.lg-announcement-controls{display:flex;justify-content:space-between;align-items:center;gap:10px;padding:12px 18px 0;margin-top:auto;border-top:1px solid #ffffff21}
.lg-announcement-controls[hidden]{display:none}
.lg-announcement-dots,.lg-announcement-arrows{display:flex;align-items:center;gap:7px}
.lg-announcement-dot{height:5px;width:9px;min-width:9px;border:0;border-radius:20px;padding:0;background:#ffffff60;transition:width .2s,background .2s}
.lg-announcement-dot.active{width:26px;background:#ffdacd}
.lg-announcement-arrows button{height:29px;width:29px;border:1px solid #ffffff60;background:transparent;border-radius:50%;color:white;font-size:21px;line-height:1;display:grid;place-items:center}
.lg-announcement-arrows button:hover{background:#ffffff22}
@keyframes lgAnnouncementIn{from{opacity:.4;transform:translateY(4px)}to{opacity:1;transform:translateY(0)}}
@media(prefers-reduced-motion:reduce){.lg-announcements article{animation:none!important}}

/* Keep areas scrollable without visible scrollbar indicators. */
.lg-sidebar-menu,.lg-table-scroll{scrollbar-width:none;-ms-overflow-style:none}
.lg-sidebar-menu::-webkit-scrollbar,.lg-table-scroll::-webkit-scrollbar{display:none}
html,body.lg-body{overflow-x:hidden}
    .lg-facility-kpis{grid-template-columns:repeat(5,minmax(0,1fr));max-width:none}
    .lg-facility-kpis .lg-kpi{min-width:0;padding:14px 11px;gap:9px}
    .lg-facility-kpis .lg-kpi-icon{width:34px;height:34px;flex-shrink:0}
    @media(max-width:1350px){.lg-facility-kpis{grid-template-columns:repeat(3,minmax(0,1fr))}}
    @media(max-width:650px){.lg-facility-kpis{grid-template-columns:repeat(2,minmax(0,1fr))}}
    

</style>
</head>

<body class="lg-body">

    {{-- LOGISTICS COMPONENTS --}}

    @section('page-title', 'Notifications')

    @include('components.logistics.sidebar')
    @include('components.logistics.navbar')

    <main
        id="lg-notifications-content"
        class="admin-notifications-page"
    >
        <div class="admin-notifications-content">
            <p id="notifications-api-error" class="mb-4 text-sm text-red-700" role="alert" hidden></p>

            @if(session('success'))
                <div class="sn-flash" role="status">
                    <svg
                        width="16"
                        height="16"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="m5 12 4 4L19 6"/>
                    </svg>

                    {{ session('success') }}
                </div>
            @endif

            {{-- NOTIFICATIONS PANEL --}}

            <section class="sn-main-card">

                <div class="sn-card-toolbar">

                    {{-- SERVER-SIDE SEARCH --}}

                    <form
                        class="sn-search-form"
                        method="GET"
                        action="{{ route('logistics.notifications') }}"
                    >
                        <input
                            type="hidden"
                            name="filter"
                            value="{{ $activeFilter }}"
                        >

                        <div class="sn-search-field">
                            <input
                                class="sn-search-input"
                                type="search"
                                name="search"
                                value="{{ $searchQuery }}"
                                placeholder="Search notifications..."
                                aria-label="Search notifications"
                            >
                            <button type="submit" class="sn-search-button" aria-label="Search">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="7"/>
                                    <path d="m16 16 5 5"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Native date picker: exactly one calendar icon --}}
                        <input
                            id="snDateFilter"
                            class="sn-date-input"
                            type="date"
                            name="date"
                            value="{{ $selectedDate }}"
                            aria-label="Filter notifications by date"
                            title="Filter notifications by date"
                            
                        >

                        @if($selectedDate !== '')
                            <a class="sn-clear-date" href="{{ route('logistics.notifications', ['filter' => $activeFilter, 'search' => $searchQuery]) }}" aria-label="Clear date filter">Clear date</a>
                        @endif

                    </form>

                    <form method="POST" action="{{ route('logistics.notifications.read-all') }}" class="sn-toolbar-mark-all">
                        @csrf

                        <button class="sn-mark-all" type="submit" {{ $unreadNotifications === 0 ? 'disabled' : '' }}>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="m4 12 5 5L20 6"/>
                                <path d="m3 6 3 3"/>
                            </svg>
                            Mark All as Read
                        </button>
                    </form>
                </div>

                {{-- FILTER TABS --}}

                <nav
                    class="sn-tabs"
                    aria-label="Notification filters"
                >

                    <a
                        href="{{ route('logistics.notifications', ['filter' => 'all', 'search' => $searchQuery, 'date' => $selectedDate]) }}"
                        class="sn-tab {{ $activeFilter === 'all' ? 'active' : '' }}"
                        {{ $activeFilter === 'all' ? 'aria-current=page' : '' }}
                    >
                        All

                        <span class="sn-tab-count">
                            {{ $totalNotifications }}
                        </span>
                    </a>

                    <a
                        href="{{ route('logistics.notifications', ['filter' => 'unread', 'search' => $searchQuery, 'date' => $selectedDate]) }}"
                        class="sn-tab {{ $activeFilter === 'unread' ? 'active' : '' }}"
                        {{ $activeFilter === 'unread' ? 'aria-current=page' : '' }}
                    >
                        Unread

                        <span class="sn-tab-count">
                            {{ $unreadNotifications }}
                        </span>
                    </a>

                    <a
                        href="{{ route('logistics.notifications', ['filter' => 'read', 'search' => $searchQuery, 'date' => $selectedDate]) }}"
                        class="sn-tab {{ $activeFilter === 'read' ? 'active' : '' }}"
                        {{ $activeFilter === 'read' ? 'aria-current=page' : '' }}
                    >
                        Read

                        <span class="sn-tab-count">
                            {{ $readNotifications }}
                        </span>
                    </a>

                </nav>

                {{-- NOTIFICATION LIST --}}

                <div class="sn-list">

                    @forelse($notificationItems as $notification)

                        @php
                            $notificationData = is_array($notification->data)
                                ? $notification->data
                                : [];

                            $notificationTitle =
                                $notificationData['title']
                                ?? $notificationData['subject']
                                ?? 'ShopEase Notification';

                            $notificationDescription =
                                $notificationData['message']
                                ?? $notificationData['body']
                                ?? $notificationData['description']
                                ?? 'You have a new update regarding your ShopEase account.';

                            $notificationType = strtolower(
                                (string) (
                                    $notificationData['type']
                                    ?? $notificationData['category']
                                    ?? 'general'
                                )
                            );

                            $iconType = 'general';

                            if (
                                str_contains($notificationType, 'warning') ||
                                str_contains($notificationType, 'policy') ||
                                str_contains($notificationType, 'compliance') ||
                                str_contains($notificationType, 'removed')
                            ) {
                                $iconType = 'warning';
                            } elseif (
                                str_contains($notificationType, 'order') ||
                                str_contains($notificationType, 'shipping') ||
                                str_contains($notificationType, 'delivery')
                            ) {
                                $iconType = 'order';
                            } elseif (
                                str_contains($notificationType, 'success') ||
                                str_contains($notificationType, 'approved') ||
                                str_contains($notificationType, 'resolved')
                            ) {
                                $iconType = 'success';
                            } elseif (
                                str_contains($notificationType, 'message') ||
                                str_contains($notificationType, 'chat')
                            ) {
                                $iconType = 'message';
                            }

                            $isUnread = $notification->read_at === null;

                            /*
                             * Only allow relative internal paths.
                             * External redirects are intentionally disallowed.
                             */
                            $rawTarget = $notificationData['url']
                                ?? $notificationData['link']
                                ?? null;

                            $targetUrl = null;

                            if (
                                is_string($rawTarget) &&
                                str_starts_with($rawTarget, '/logistics/') &&
                                !str_starts_with($rawTarget, '//') &&
                                !str_contains($rawTarget, '\\') &&
                                !preg_match('/[\x00-\x1F\x7F]/', $rawTarget)
                            ) {
                                $targetUrl = $rawTarget;
                            }
                        @endphp

                        <article
                            class="sn-notification {{ $isUnread ? 'is-unread' : '' }}"
                        >

                            <div
                                class="sn-notification-icon {{ $iconType }}"
                                aria-hidden="true"
                            >

                                @if($iconType === 'warning')
                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <path d="m12 3 10 18H2L12 3Z"/>
                                        <path d="M12 9v5"/>
                                        <path d="M12 17h.01"/>
                                    </svg>

                                @elseif($iconType === 'order')
                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <path d="m3 7 9-4 9 4-9 4-9-4Z"/>
                                        <path d="M3 7v10l9 4 9-4V7"/>
                                        <path d="M12 11v10"/>
                                    </svg>

                                @elseif($iconType === 'success')
                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <circle cx="12" cy="12" r="9"/>
                                        <path d="m8 12 3 3 5-6"/>
                                    </svg>

                                @elseif($iconType === 'message')
                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <path d="M21 11.5a8.5 8.5 0 0 1-12 7.7L3 21l1.8-6A8.5 8.5 0 1 1 21 11.5Z"/>
                                    </svg>

                                @else
                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                                        <path d="M10 21h4"/>
                                    </svg>
                                @endif

                            </div>

                            <div class="sn-notification-body">

                                <div class="sn-notification-top">

                                    <h3 class="sn-notification-title">
                                        @if($isUnread)
                                            <span
                                                class="sn-unread-dot"
                                                aria-label="Unread"
                                            ></span>
                                        @endif

                                        {{ $notificationTitle }}
                                    </h3>

                                </div>

                                <p class="sn-notification-description">
                                    {{ $notificationDescription }}
                                </p>

                                <div class="sn-notification-footer">

                                    <span class="sn-notification-time">
                                        {{ $formatNotificationTime($notification) }}
                                    </span>

                                    @if($isUnread)

                                        <form
                                            method="POST"
                                            action="{{ route('logistics.notifications.read', $notification->id) }}"
                                        >
                                            @csrf
                    

                                            <button
                                                class="sn-action"
                                                type="submit"
                                            >
                                                Mark as Read
                                            </button>
                                        </form>

                                    @else

                                        <span class="sn-read-label">
                                            Already read
                                        </span>

                                    @endif

                                    @if($targetUrl)
                                        <a
                                            class="sn-action muted"
                                            href="{{ $targetUrl }}"
                                        >
                                            View Details →
                                        </a>
                                    @endif

                                </div>

                            </div>

                            <div class="sn-notification-side">

                                <span class="sn-status-pill {{ $isUnread ? 'unread' : '' }}">
                                    {{ $isUnread ? 'Unread' : 'Read' }}
                                </span>

                            </div>

                        </article>

                    @empty

                        <div class="sn-empty-state">

                            <div class="sn-empty-icon">
                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                                    <path d="M10 21h4"/>
                                </svg>
                            </div>

                            <h3>
                                @if($searchQuery !== '')
                                    No matching notifications
                                @elseif($activeFilter === 'unread')
                                    You're all caught up!
                                @elseif($activeFilter === 'read')
                                    No read notifications yet
                                @else
                                    No notifications yet
                                @endif
                            </h3>

                            <p>
                                @if($searchQuery !== '')
                                    Try searching with a different keyword.
                                @elseif($activeFilter === 'unread')
                                    You don't have any unread notifications.
                                @elseif($activeFilter === 'read')
                                    Notifications you've marked as read will appear here.
                                @else
                                    Your logistics notifications will appear here
                                    when there are new updates.
                                @endif
                            </p>

                        </div>

                    @endforelse

                </div>

                {{-- PAGINATION --}}

                @if($notificationItems->hasPages())

                    <div class="sn-pagination">

                        <span class="sn-pagination-info">
                            Showing
                            {{ $notificationItems->firstItem() }}
                            to
                            {{ $notificationItems->lastItem() }}
                            of
                            {{ $notificationItems->total() }}
                            notifications
                        </span>

                        <div class="sn-pagination-buttons">

                            <a
                                class="sn-page-button {{ $notificationItems->onFirstPage() ? 'disabled' : '' }}"
                                href="{{ $notificationItems->previousPageUrl() ?? '#' }}"
                                aria-label="Previous page"
                            >
                                ‹
                            </a>

                            @foreach(range(1, $notificationItems->lastPage()) as $page)
                                @if(
                                    $notificationItems->lastPage() <= 7 ||
                                    $page === 1 ||
                                    $page === $notificationItems->lastPage() ||
                                    abs($page - $notificationItems->currentPage()) <= 1
                                )
                                    <a
                                        class="sn-page-button {{ $page === $notificationItems->currentPage() ? 'active' : '' }}"
                                        href="{{ $notificationItems->url($page) }}"
                                        {{ $page === $notificationItems->currentPage() ? 'aria-current=page' : '' }}
                                    >
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach

                            <a
                                class="sn-page-button {{ !$notificationItems->hasMorePages() ? 'disabled' : '' }}"
                                href="{{ $notificationItems->nextPageUrl() ?? '#' }}"
                                aria-label="Next page"
                            >
                                ›
                            </a>

                        </div>

                    </div>

                @endif

            </section>

        </div>
    </main>
<script>
document.addEventListener('DOMContentLoaded',()=>{
  const date=document.getElementById('snDateFilter');
  date?.addEventListener('change',()=>{
    const form=date.form;
    if(form){if(form.requestSubmit) form.requestSubmit();else form.submit();}
  });
});
</script>
<script>
document.addEventListener('DOMContentLoaded',()=>{
 const toggle=document.getElementById('lgSidebarToggle');
 toggle?.addEventListener('click',()=>{const collapsed=document.body.classList.toggle('lg-collapsed');toggle.setAttribute('aria-expanded',String(!collapsed));});
 const profile=document.getElementById('lgProfileButton'),menu=document.getElementById('lgProfileMenu');
 profile?.addEventListener('click',()=>{menu.hidden=!menu.hidden;profile.setAttribute('aria-expanded',String(!menu.hidden));});
 document.addEventListener('click',e=>{if(menu&&!e.target.closest('.lg-profile-wrap')){menu.hidden=true;profile?.setAttribute('aria-expanded','false');}});
});
</script>
</body>
</html>