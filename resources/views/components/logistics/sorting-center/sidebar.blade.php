{{-- Sorting Center sidebar: faithfully reuses Logistics sidebar styling/animations. Routes remain disabled until registered. --}}
@php
    $scMenuConfig = [
        ['Dashboard', 'dashboard.png', 'dashboard'],
        ['Courier Registrations', 'logistic-management.png', 'courier-registrations'],
        ['Delivery Hubs', 'logistic-management.png', 'delivery-hubs'],
        ['Scanners', 'reports.png', 'scanners'],
        ['Parcel Tracking', 'reports.png', 'parcel-tracking'],
        ['Parcel Disputes', 'complaints-disputes.png', 'parcel-disputes'],
        ['Vehicles and Drivers', 'logistic-management.png', 'vehicles-drivers'],
        ['Notifications', 'notifications.png', 'notifications'],
        ['Network Earnings', 'commission.png', 'network-earnings'],
        ['Messages', 'messages.png', 'messages'],
        ['Reports', 'reports.png', 'reports'],
        ['Account', 'account-management.png', 'account'],
    ];
    $lgNavItems = collect($scMenuConfig)->map(function($item) {
        [$label, $icon, $slug] = $item;
        $name = 'sorting-center.' . $slug;
        return ['label'=>$label,'icon'=>$icon,
                'href'=>\Illuminate\Support\Facades\Route::has($name) ? route($name) : '#',
                'active'=>request()->routeIs($name),
                'enabled'=>\Illuminate\Support\Facades\Route::has($name)];
    })->all();

@endphp
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
<aside id="lgSidebar" class="lg-sidebar" aria-label="Logistics navigation">
    <div class="lg-sidebar-content">
        <a class="lg-sidebar-logo" href="{{ \Illuminate\Support\Facades\Route::has('sorting-center.dashboard') ? route('sorting-center.dashboard') : '#' }}" aria-label="ShopEase Sorting Center Dashboard">
            <img src="{{ asset('icons/admin/dashboard/sidebar&navbar/shopease.png') }}" alt="ShopEase" onerror="this.style.display='none'">
        </a>
        <nav class="lg-sidebar-menu" aria-label="Logistics menu">
            @php
                $lgEntryIndex = 0;
                $lgGroups = [
                    ['heading' => 'OVERVIEW', 'labels' => ['Dashboard']],
                    ['heading' => 'OPERATIONS', 'labels' => ['Courier Registrations', 'Delivery Hubs', 'Scanners', 'Parcel Tracking', 'Parcel Disputes', 'Vehicles and Drivers']],
                    ['heading' => 'FINANCE & ACCOUNT', 'labels' => ['Notifications', 'Network Earnings', 'Messages', 'Reports', 'Account']],
                ];
            @endphp
            {{-- Plain PHP loops prevent Blade's inline array parsing from generating a stray endforeach. --}}
            <?php foreach ($lgGroups as $group): ?>
                <p class="lg-nav-category lg-admin-group-heading">{{ $group['heading'] }}</p>
                <?php foreach ($group['labels'] as $navLabel): ?>
                    <?php
                        $item = collect($lgNavItems)->firstWhere('label', $navLabel);
                        $lgEntryIndex++;
                        $lgAnimDelay = number_format(.05 + ($lgEntryIndex - 1) * .05, 2, '.', '');
                    ?>
                    @if($item && $item['enabled'])
                        <a class="lg-nav-link lg-admin-menu-item {{ $item['active'] ? 'active' : '' }}" href="{{ $item['href'] }}" title="{{ $item['label'] }}" @if($item['active']) aria-current="page" @endif style="animation-delay:{{ $lgAnimDelay }}s">
                            <span class="lg-admin-icon-wrap"><img src="{{ asset($item['label'] === 'Notifications' ? 'icons/seller/notifications.png' : 'icons/admin/dashboard/sidebar&navbar/' . $item['icon']) }}" alt=""></span>
                            <span class="lg-admin-label">{{ $item['label'] }}</span>
                        </a>
                    @elseif($item)
                        <span class="lg-nav-link lg-nav-unavailable lg-admin-menu-item" aria-disabled="true" title="Not available yet" style="animation-delay:{{ $lgAnimDelay }}s">
                            <span class="lg-admin-icon-wrap"><img src="{{ asset($item['label'] === 'Notifications' ? 'icons/seller/notifications.png' : 'icons/admin/dashboard/sidebar&navbar/' . $item['icon']) }}" alt=""></span>
                            <span class="lg-admin-label">{{ $item['label'] }}</span>
                        </span>
                    @endif
                <?php endforeach; ?>
            <?php endforeach; ?>
        </nav>
    </div>
    @if(\Illuminate\Support\Facades\Route::has('logout'))
        <form id="lgSidebarLogoutForm" class="lg-sidebar-footer" action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="lg-sidebar-logout lg-admin-logout-enter" title="Log Out">
                <span class="lg-admin-icon-wrap"><img src="{{ asset('icons/admin/dashboard/sidebar&navbar/log-out.png') }}" alt=""></span>
                <span class="lg-logout-label">Log Out</span>
            </button>
        </form>
    @endif
</aside>
<style>
/* Hover fix: reserve 5px of horizontal space for the 4px translation so the right pill curve remains visible.
   Entrance translation is moved to children to avoid a filled animation overriding :hover transforms. */
/* The original Admin sidebar uses a top-to-bottom maroon-900 → maroon-950 gradient,
   translucent maroon hover/active pills, and 300ms slide animation. Scoped to Logistics. */
#lgSidebar.lg-sidebar{
    background:linear-gradient(to bottom,#651015 0%,#360609 100%)!important;
    border-top-right-radius:64px!important;
    padding:24px 16px!important;
    display:flex!important;flex-direction:column!important;
    color:#fff!important;overflow:hidden!important;
    transition:width .3s ease,transform .3s ease!important;
}
#lgSidebar .lg-sidebar-content{flex:1 1 auto;min-height:0;display:flex;flex-direction:column}
#lgSidebar .lg-sidebar-logo{display:flex;justify-content:center;align-items:center;height:auto;min-height:72px;margin:-8px 0 28px;padding:0 4px;gap:0;white-space:nowrap;transition:all .5s ease}
#lgSidebar .lg-sidebar-logo img{height:64px;width:auto;max-width:100%;max-height:64px;object-fit:contain}
#lgSidebar .lg-sidebar-menu{display:flex;flex-direction:column;gap:2px;flex:1;min-height:0;overflow-y:auto;overflow-x:hidden;scrollbar-width:none;-ms-overflow-style:none}
#lgSidebar .lg-sidebar-menu::-webkit-scrollbar{display:none}
/* Restored grouped section labels, with the same muted light-maroon tone as the original Logistics sidebar. */
#lgSidebar .lg-admin-group-heading{display:block;margin:17px 12px 8px!important;color:#f5c8c7!important;font-size:10px!important;letter-spacing:1.4px!important;font-weight:600!important;line-height:1.4;white-space:nowrap;flex-shrink:0}
#lgSidebar .lg-admin-group-heading:first-child{margin-top:5px!important}
body.lg-collapsed #lgSidebar .lg-admin-group-heading{display:none}
@media (max-width:900px) and (min-width:701px){body:not(.lg-collapsed) #lgSidebar .lg-admin-group-heading{display:none}body.lg-collapsed #lgSidebar .lg-admin-group-heading{display:block}}
@media(max-width:700px){body.lg-collapsed #lgSidebar .lg-admin-group-heading{display:block}}
#lgSidebar .lg-nav-link{
    height:auto!important;min-height:40px;width:calc(100% - 5px);margin:0 5px 0 0!important;
    padding:10px 12px!important;display:flex;align-items:center;justify-content:flex-start;
    gap:10px!important;border-radius:999px!important;border:0!important;
    color:#fff!important;background:transparent;font-size:14px!important;font-weight:500!important;
    white-space:nowrap;text-align:left;line-height:1.45;
    transition:background-color .3s ease,transform .3s ease!important;will-change:transform;
}
#lgSidebar .lg-nav-link:not(.lg-nav-unavailable):not(.active):hover{background:rgba(112,25,31,.50)!important;transform:translateX(4px)!important;box-shadow:inset 0 0 0 1px rgba(255,255,255,.045)}
#lgSidebar .lg-nav-link.active{background:rgba(139,40,46,.60)!important;transform:none}
#lgSidebar .lg-nav-link.active:hover{background:rgba(151,45,53,.73)!important;transform:translateX(4px)!important}
#lgSidebar .lg-nav-unavailable{opacity:1!important;cursor:default!important;pointer-events:none}
#lgSidebar .lg-nav-unavailable:hover{background:transparent!important;transform:none!important}
#lgSidebar .lg-admin-icon-wrap{width:18px;height:18px;flex:0 0 18px;display:flex;justify-content:center;align-items:center;transition:all .3s ease}
#lgSidebar .lg-admin-icon-wrap img{width:18px;height:18px;object-fit:contain;display:block}
#lgSidebar .lg-admin-label{font-size:14px;font-weight:500;white-space:nowrap}
#lgSidebar .lg-sidebar-footer{width:100%;flex:0 0 auto;margin-top:auto!important;padding:0!important;border:0!important}
#lgSidebar .lg-sidebar-logout{width:100%;height:auto;min-height:42px;padding:10px 12px;border:1px solid rgba(255,255,255,.58)!important;background:transparent!important;border-radius:999px;color:white;display:flex;justify-content:center;align-items:center;gap:8px;font:500 14px Poppins,sans-serif;cursor:pointer;transition:background .3s ease,border-color .3s ease!important}
#lgSidebar .lg-sidebar-logout:hover{background:rgba(255,255,255,.08)!important;border-color:rgba(255,255,255,.82)!important}
#lgSidebar .lg-sidebar-logout .lg-admin-icon-wrap img{width:15px;height:15px}
@keyframes lgAdminMenuEnter{from{opacity:0}to{opacity:1}}
@keyframes lgAdminMenuContentEnter{from{opacity:0;transform:translateX(-20px)}to{opacity:1;transform:translateX(0)}}
@keyframes lgAdminLogoutEnter{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
#lgSidebar .lg-admin-menu-item{animation:lgAdminMenuEnter .35s ease-out both}
#lgSidebar .lg-admin-menu-item .lg-admin-icon-wrap,#lgSidebar .lg-admin-menu-item .lg-admin-label{animation:lgAdminMenuContentEnter .35s ease-out both;animation-delay:inherit}
#lgSidebar .lg-admin-logout-enter{opacity:0;animation:lgAdminLogoutEnter .45s ease-out .6s forwards}
/* Preserve existing Logistics Dashboard/Network Hubs navbar toggle without changing their scripts. */
body.lg-collapsed #lgSidebar .lg-sidebar-logo{min-height:72px}
body.lg-collapsed #lgSidebar .lg-sidebar-logo img{max-width:52px;height:auto}
body.lg-collapsed #lgSidebar .lg-nav-link{justify-content:center;padding:10px 0!important;width:100%;margin-right:0!important;transform:none!important}
body.lg-collapsed #lgSidebar .lg-admin-label{display:none}
body.lg-collapsed #lgSidebar .lg-sidebar-footer{padding:0!important}
body.lg-collapsed #lgSidebar .lg-sidebar-logout{width:44px;height:44px;margin:0 auto;padding:0}
body.lg-collapsed #lgSidebar .lg-logout-label{display:none}
@media (max-width:900px) and (min-width:701px){
    body:not(.lg-collapsed) #lgSidebar .lg-sidebar-logo img{max-width:52px;height:auto}
    body:not(.lg-collapsed) #lgSidebar .lg-nav-link{justify-content:center;padding:10px 0!important;width:100%;margin-right:0!important;transform:none!important}
    body:not(.lg-collapsed) #lgSidebar .lg-admin-label{display:none}
    body:not(.lg-collapsed) #lgSidebar .lg-sidebar-logout{width:44px;height:44px;margin:auto;padding:0}
    body:not(.lg-collapsed) #lgSidebar .lg-logout-label{display:none}
    body.lg-collapsed #lgSidebar .lg-admin-label,body.lg-collapsed #lgSidebar .lg-logout-label{display:inline}
    body.lg-collapsed #lgSidebar .lg-sidebar-logo img{max-width:100%;height:64px}
    body.lg-collapsed #lgSidebar .lg-nav-link{justify-content:flex-start;padding:10px 12px!important}
    body.lg-collapsed #lgSidebar .lg-sidebar-logout{width:100%;height:auto;padding:10px 12px}
}
@media(max-width:700px){
    body.lg-collapsed #lgSidebar .lg-admin-label,body.lg-collapsed #lgSidebar .lg-logout-label{display:inline}
    body.lg-collapsed #lgSidebar .lg-nav-link{justify-content:flex-start;padding:10px 12px!important}
    body.lg-collapsed #lgSidebar .lg-sidebar-logout{width:100%;height:auto;padding:10px 12px}
    body.lg-collapsed #lgSidebar .lg-sidebar-logo img{max-width:100%;height:64px}
}
@media(prefers-reduced-motion:reduce){#lgSidebar .lg-admin-menu-item,#lgSidebar .lg-admin-menu-item .lg-admin-icon-wrap,#lgSidebar .lg-admin-menu-item .lg-admin-label,#lgSidebar .lg-admin-logout-enter{opacity:1;animation:none!important;transform:none}#lgSidebar .lg-nav-link,#lgSidebar .lg-sidebar-logout{transition:none!important}}
</style>
<script>
document.addEventListener('DOMContentLoaded',function(){
    const form=document.getElementById('lgSidebarLogoutForm');
    if(!form)return;
    form.addEventListener('submit',function(){
        const button=form.querySelector('button[type="submit"]');
        if(!button)return;
        button.disabled=true;
        button.style.opacity='.7';
        const label=button.querySelector('.lg-logout-label');
        if(label)label.textContent='Logging Out...';
    });
});
</script>