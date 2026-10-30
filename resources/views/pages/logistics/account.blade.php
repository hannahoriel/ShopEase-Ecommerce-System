@php
// Account details are loaded from the authenticated logistics account and pending requests.
$profile = is_array($accountProfile ?? null) ? $accountProfile : [];
$companyName = $profile['company_name'] ?? auth()->user()->name ?? 'Logistics Company';
$initials = collect(preg_split('/\s+/', trim($companyName)))->filter()->take(2)->map(fn ($part)=>strtoupper(substr($part,0,1)))->implode('') ?: 'LC';
$privacy = $accountPreferences['privacy_settings'] ?? ['public_company_contact'=>false,'share_delivery_updates'=>true];
$notifications = array_merge(['parcel_updates'=>true,'order_updates'=>true,'messages'=>true,'delivery_exceptions'=>true,'compliance'=>true,'email_alerts'=>true,'announcements'=>true,'promotions'=>false], $accountPreferences['notification_preferences'] ?? []);
@endphp
<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="csrf-token" content="{{ csrf_token() }}"><title>Company Account — ShopEase Logistics</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
@vite(['resources/css/app.css'])
<style>
/* Shared Logistics Dashboard layout CSS, embedded to avoid missing logistics-shell.css */

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
<style>
*{box-sizing:border-box}.la-page{font-family:Poppins,Arial,sans-serif;min-height:100vh;padding:115px 24px 48px;margin-left:270px;background:#fcf8f6;color:#282124;transition:margin-left .3s}.lg-collapsed .la-page{margin-left:94px}.la-layout{max-width:1450px;display:grid;grid-template-columns:228px minmax(0,1fr);gap:20px;margin:auto;align-items:start}.la-side,.la-panel{background:#fff;border:1px solid #f0e6e2;border-radius:15px;box-shadow:0 3px 15px #501c1909}.la-side{position:sticky;top:110px;padding:17px 13px}.la-menu-head{font-size:16px;font-weight:600;margin:6px 9px 15px}.la-nav{display:block;width:100%;text-align:left;text-decoration:none;border:0;border-left:3px solid transparent;border-radius:6px;background:none;color:#6d6870;font:500 12px Poppins;padding:13px 13px;cursor:pointer}.la-nav.active{background:#fff0f1;border-left-color:#8d2434;color:#8d2434;font-weight:600}.la-nav:hover{background:#fff7f6}.la-panel{padding:24px}.la-panel[hidden]{display:none!important}.la-panel h1{margin:0;font-size:23px}.la-desc{font-size:12px;color:#9c928f;margin:5px 0 23px}.la-cover{background:linear-gradient(115deg,#4f0a11,#9b4550 50%,#edc3b8);height:155px;border-radius:12px}.la-avatar-line{display:flex;align-items:center;gap:17px;margin:-34px 20px 25px;position:relative}.la-avatar{display:grid;place-items:center;background:#fff0ed;border:5px solid #fff;border-radius:50%;height:108px;width:108px;color:#86212c;font-weight:700;font-size:32px;box-shadow:0 4px 15px #280a0a18}.la-identity h2{font-size:20px;margin:22px 0 3px}.la-identity p{font-size:11px;color:#9e8f8a;margin:0}.la-note{border:1px solid #ecd6ce;background:#fff8f5;color:#7f5046;padding:13px 15px;border-radius:10px;font-size:12px;line-height:1.75;margin-bottom:20px}.la-group{grid-column:1/-1;font-size:14px;color:#69202d;margin:18px 0 1px;border-bottom:1px solid #f0e7e4;padding-bottom:10px}.la-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px 18px}.la-field{display:flex;flex-direction:column;gap:7px;font-size:11px;font-weight:500;color:#493c3a}.la-field input,.la-field select{border:1px solid #ddd6d4;border-radius:8px;height:42px;width:100%;padding:0 12px;font:400 12px Poppins;color:#302a2b;outline:none}.la-field input:focus{border-color:#944955;box-shadow:0 0 0 2px #9449551a}.la-actions{display:flex;gap:12px;justify-content:flex-end;margin-top:23px}.la-btn{background:#76202c;color:#fff;border:0;border-radius:8px;cursor:pointer;padding:12px 20px;font:600 12px Poppins}.la-btn:disabled{opacity:.4;cursor:not-allowed}.la-muted-btn{border:1px solid #d8cdca;border-radius:8px;background:white;padding:10px 15px;font:500 12px Poppins;cursor:pointer}.la-card{padding:20px;border:1px solid #eee4e1;border-radius:11px;margin:14px 0}.la-card h2{font-size:15px;margin:0 0 7px}.la-card p{font-size:12px;color:#8c807d;line-height:1.8}.la-toggle{display:flex;justify-content:space-between;gap:20px;align-items:center;border-bottom:1px solid #f2e9e6;padding:14px 0}.la-toggle:last-child{border:0}.la-toggle strong{display:block;font-size:12px}.la-toggle small{display:block;font-size:10px;color:#999;margin-top:4px}.la-toggle input{accent-color:#842638;width:19px;height:19px;cursor:pointer}.la-pending{padding:15px 18px;margin:15px 0;border-radius:10px;border:1px solid #eddbba;background:#fffaf1;font-size:12px}.la-pending strong{color:#916117}.la-pending ul{padding-left:18px}.la-pill{border-radius:50px;padding:4px 9px;font-size:10px;background:#fff0d7;color:#885c20}.la-policy{line-height:1.85;font-size:12px;color:#524848}.la-policy section{border-top:1px solid #eee4e0;padding:13px 0}.la-policy h2{color:#792431;font-size:15px}.la-flash{padding:12px 15px;background:#e9f5ea;color:#276a35;border-radius:9px;margin-bottom:15px;font-size:12px}.la-errors{background:#ffeded;color:#9b2934;padding:14px;border-radius:9px;font-size:12px;margin-bottom:15px}.la-help{font-size:11px;color:#988b86}.la-dialog-bg{position:fixed;inset:0;z-index:1000;background:#140a0ba8;display:grid;place-items:center;padding:20px}.la-dialog-bg[hidden]{display:none}.la-dialog{width:min(480px,100%);background:white;border-radius:17px;padding:26px}.la-dialog h2{font-size:18px}.la-dialog p{font-size:12px;color:#80716e;line-height:1.7}@media(max-width:1080px){.la-layout{grid-template-columns:1fr}.la-side{position:static}.la-menu{display:flex;flex-wrap:wrap}.la-nav{width:auto}.la-page{margin-left:0;padding:105px 14px 30px}.lg-collapsed .la-page{margin-left:0}}@media(max-width:600px){.la-grid{grid-template-columns:1fr}.la-panel{padding:17px}.la-avatar{width:85px;height:85px;font-size:24px}.la-identity h2{font-size:16px}.la-avatar-line{margin-left:5px}}

/* Company Account uses the same layout breakpoints as the Logistics Dashboard. */
@media (min-width:901px){body.lg-body .la-page{margin-left:260px}body.lg-body.lg-collapsed .la-page{margin-left:80px}}
@media (min-width:701px) and (max-width:900px){body.lg-body .la-page,body.lg-body.lg-collapsed .la-page{margin-left:80px;padding-top:115px}}
@media (max-width:700px){body.lg-body .la-page,body.lg-body.lg-collapsed .la-page{margin-left:0;padding-top:96px}body.lg-body .lg-sidebar{transform:translateX(-100%)}body.lg-body.lg-collapsed .lg-sidebar{transform:translateX(0)}}
</style><style>
/* Seller Account inspired content components; logistics shell remains unchanged. */
/* ShopEase Seller Account — extracted from original Blade; original rule order preserved */

        .la-page,.la-page *{box-sizing:border-box}
        .la-page{font-family:'Poppins',sans-serif;background:#fcf8f6;min-height:100vh;margin-left:270px;padding:120px 25px 44px 20px;color:#182033;transition:margin-left .25s ease}
        .la-page button,.la-page input,.la-page select{font-family:inherit}
        .sa-layout{display:grid;grid-template-columns:228px minmax(0,1fr);gap:20px;max-width:1640px;margin:0 auto;align-items:start}
        .sa-navigation,.sa-workspace{background:white;border:1px solid #f0ebe8;border-radius:15px;box-shadow:0 2px 16px rgba(58,36,28,.035)}
        .sa-navigation{padding:18px 14px;position:sticky;top:126px;min-height:580px}
        .sa-nav-heading{display:flex;align-items:center;gap:9px;padding:3px 9px 14px;font-size:15px;font-weight:600}
        .sa-nav-heading svg{width:19px;height:19px;color:#3274c8}
        .sa-nav-list{display:flex;flex-direction:column;gap:4px}
        .sa-nav-link{display:flex;align-items:center;min-height:40px;padding:0 14px;border-left:3px solid transparent;border-radius:6px;color:#586276;text-decoration:none;font-size:12px;font-weight:400;transition:.2s}
        .sa-nav-link:hover{background:#fff6f5;color:#9b1238}
        .sa-nav-link.active{background:#fcf0f2;color:#9d1239;border-left-color:#9d1239;font-weight:600}
        .sa-workspace{padding:18px 20px 20px;min-width:0}
        .sa-page-title{margin:0;font-size:23px;font-weight:600;letter-spacing:-.5px;line-height:1.25}
        .sa-intro{color:#8e96a4;font-size:12px;margin:3px 0 15px}
        .sa-cover{position:relative;height:165px;border-radius:11px;background:linear-gradient(110deg,#ded5cb 0%,#f6efeb 45%,#d8c9c1 100%);overflow:hidden}
        .sa-cover:after{content:'';position:absolute;inset:0;background:linear-gradient(90deg,rgba(133,107,89,.07),transparent 65%);pointer-events:none}
        .sa-cover img{height:100%;width:100%;object-fit:cover;display:block}
        .sa-cover-button{position:absolute;right:18px;top:16px;z-index:2}
        .sa-photo-button{display:inline-flex;align-items:center;gap:7px;background:#fff;border:1px solid #d6d9df;border-radius:7px;padding:9px 12px;font-size:11px;font-weight:500;color:#253044;box-shadow:0 2px 5px rgba(20,20,20,.035);cursor:pointer}
        .sa-photo-button:hover{border-color:#9da3aa;background:#fffafa}
        .sa-photo-button svg{width:15px;height:15px}
        .sa-shop-strip{position:relative;min-height:94px;display:flex;gap:20px;align-items:center;padding:7px 0 12px 164px}
        .sa-avatar{position:absolute;top:-56px;left:22px;width:124px;height:124px;background:#ffeaed;border:5px solid white;border-radius:50%;box-shadow:0 4px 15px rgba(0,0,0,.10);display:grid;place-items:center;overflow:visible}
        .sa-avatar>img{width:100%;height:100%;object-fit:cover;border-radius:50%}
        /* Force a perfect circular avatar even when dashboard/global CSS overrides img sizes. */
        #laPage #saAvatar{width:124px!important;height:124px!important;min-width:124px;min-height:124px;max-width:124px;max-height:124px;aspect-ratio:1/1;border-radius:50%!important;flex-shrink:0;box-sizing:border-box;}
        #laPage #saAvatar>img{display:block;width:100%!important;height:100%!important;min-width:0;min-height:0;aspect-ratio:1/1;object-fit:cover!important;object-position:center;border-radius:50%!important;}
        @media(max-width:760px){#laPage #saAvatar{width:98px!important;height:98px!important;min-width:98px;min-height:98px;max-width:98px;max-height:98px;}}
        .sa-avatar-fallback{font-size:48px;color:#a1153c;font-weight:600}
        .sa-avatar-camera{position:absolute;right:2px;bottom:8px;display:grid;place-items:center;width:28px;height:28px;border:3px solid white;border-radius:50%;background:#485467;color:white;cursor:pointer}
        .sa-avatar-camera svg{width:14px;height:14px}
        .sa-shop-name{font-size:20px;font-weight:600;line-height:1.25;margin:0;color:#182033}
        .sa-shop-caption{font-size:11px;color:#8b94a4;margin:3px 0 7px}
        .sa-change-avatar{padding:7px 10px;font-size:10px}
        .sa-form-stack{display:grid;gap:12px}
        .sa-section{border:1px solid #e5e8ed;border-radius:9px;padding:15px 18px 17px;background:#fff}
        .sa-section-heading{display:flex;align-items:flex-start;gap:11px;margin:0 0 14px}
        .sa-section-icon{width:26px;height:26px;display:grid;place-items:center;border-radius:8px;color:#3479cf;background:#f4f8ff;flex-shrink:0}
        .sa-section-icon.purple{background:#f7f4ff;color:#7050d4}.sa-section-icon.red{background:#fff0f2;color:#bd214a}
        .sa-section-icon svg{width:19px;height:19px}
        .sa-section-title{font-size:14px;line-height:1.25;font-weight:600;margin:0}
        .sa-section-subtitle{font-size:10px;color:#9199a6;margin:3px 0 0}
        .sa-fields{display:grid;grid-template-columns:repeat(12,minmax(0,1fr));column-gap:16px;row-gap:11px}
        .sa-field{grid-column:span 4;min-width:0}
        .sa-field.half{grid-column:span 6}.sa-field.quarter{grid-column:span 3}.sa-field.span8{grid-column:span 8}
        .sa-field label,.sa-field>label{display:block;font-size:11px;font-weight:500;color:#252c3a;margin:0 0 5px}
        .sa-required{color:#cb2549}
        .sa-control{display:block;width:100%;height:39px;border:1px solid #d8dbe1;border-radius:7px;background:#fff;color:#303b4e;padding:0 12px;outline:none;font-size:11px;font-weight:400;transition:border-color .15s,box-shadow .15s}
        .sa-control::placeholder{color:#a7aeb9;opacity:1}
        .sa-control:focus{border-color:#9ca3af;box-shadow:0 0 0 2px rgba(156,163,175,.15)}
        .sa-control:disabled,.sa-control[readonly]{background:#f2f2f2;color:#9097a2;cursor:not-allowed}
        .sa-phone{display:flex;width:100%;height:39px;border:1px solid #d8dbe1;border-radius:7px;overflow:hidden;background:#fff}
        .sa-phone:focus-within{border-color:#9ca3af;box-shadow:0 0 0 2px rgba(156,163,175,.15)}
        .sa-phone-prefix{padding:0 12px;display:flex;align-items:center;justify-content:center;border-right:1px solid #d8dbe1;font-size:11px;white-space:nowrap}
        .sa-phone input{border:0;outline:0;flex:1;min-width:0;padding:0 12px;font-size:11px}
        .sa-phone input::placeholder{color:#a7aeb9}
        .sa-upload{height:72px;border:1px dashed #d1d6df;border-radius:8px;display:flex;gap:11px;align-items:center;justify-content:center;text-align:left;background:#fff;cursor:pointer;padding:12px;transition:.2s}
        .sa-upload:hover,.sa-upload:focus-within{background:#fffafa;border-color:#b17a85}
        .sa-upload svg{color:#a0a8b4;width:26px;height:26px;flex:0 0 26px}
        .sa-upload strong{display:block;font-size:10px;font-weight:500;color:#7c8595;overflow-wrap:anywhere}
        .sa-upload small{display:block;font-size:9px;color:#aab1be;margin-top:3px}
        .sa-file-field{grid-column:span 6}
        .sa-multiselect{position:relative}
        .sa-category-box{display:flex;align-items:center;gap:5px;flex-wrap:wrap;min-height:72px;padding:8px 31px 8px 9px;border:1px solid #d8dbe1;border-radius:8px;cursor:pointer;position:relative}
        .sa-category-box:focus-within{border-color:#9ca3af;box-shadow:0 0 0 2px rgba(156,163,175,.15)}
        .sa-tag{display:inline-flex;align-items:center;gap:5px;border-radius:20px;background:#f7dfe4;color:#4b2734;padding:4px 8px;font-size:10px;line-height:1.4}
        .sa-tag button{padding:0;border:0;background:none;color:#8e3d55;cursor:pointer;font-size:13px;line-height:1}
        .sa-category-placeholder{color:#a7aeb9;font-size:11px}
        .sa-category-toggle{position:absolute;right:9px;top:50%;transform:translateY(-50%);border:0;background:none;color:#424b5b;cursor:pointer}
        .sa-category-menu{display:none;position:absolute;z-index:20;top:calc(100% + 5px);left:0;right:0;max-height:218px;overflow-y:auto;padding:6px;background:white;border:1px solid #d8dbe1;border-radius:9px;box-shadow:0 12px 24px rgba(30,20,20,.12)}
        .sa-category-menu.open{display:block}
        .sa-category-choice{width:100%;display:flex;align-items:center;gap:10px;padding:8px;border:0;border-radius:5px;background:transparent;text-align:left;color:#303b4e;font-size:11px;cursor:pointer}
        .sa-category-choice:hover{background:#fff3f4}
        .sa-category-choice.selected{background:#fcf0f2;color:#9d1239}
        .sa-business-row{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:20px}
        .sa-actions{display:flex;align-items:center;justify-content:flex-end;gap:11px;padding:7px 0 1px}
        .sa-action{height:39px;padding:0 22px;border:1px solid #a7b0be;border-radius:7px;background:#fff;font-size:11px;font-weight:500;color:#2b3545;cursor:pointer}
        .sa-action.primary{background:#9b1239;border-color:#9b1239;color:white;min-width:150px}
        .sa-action.primary:hover:not(:disabled){background:#800b2d}
        .sa-action.primary:disabled{background:#d9cbd0;border-color:#d9cbd0;color:#fff;cursor:not-allowed;box-shadow:none;opacity:.8}.sa-action:not(.primary):hover{background:#f8f9fa}
        .sa-status{display:none;padding:10px 12px;border-radius:8px;margin:0 0 12px;font-size:11px;background:#fff7e9;color:#906019;border:1px solid #f3dfbc}
        .sa-status.visible{display:block}.sa-error{font-size:10px;color:#b91c1c;margin-top:5px}
        @media(max-width:1260px){.sa-layout{grid-template-columns:185px minmax(0,1fr);gap:14px}.sa-navigation{padding:15px 10px}.sa-fields{column-gap:11px}.sa-section{padding:14px}.sa-business-row{gap:12px}}
        @media(max-width:1020px){.sa-layout{grid-template-columns:1fr}.sa-navigation{position:static;min-height:unset}.sa-nav-list{flex-direction:row;flex-wrap:wrap}.sa-nav-link{padding:0 12px}.sa-workspace{padding:16px}.sa-field{grid-column:span 6}.sa-field.quarter{grid-column:span 6}.sa-field.span8{grid-column:span 12}}
        @media(max-width:760px){.la-page{margin-left:0!important;padding:106px 12px 32px}.sa-cover{height:140px}.sa-shop-strip{padding-left:114px;gap:8px;min-height:83px}.sa-avatar{left:10px;top:-40px;width:98px;height:98px}.sa-shop-name{font-size:16px}.sa-section{padding:12px}.sa-business-row{grid-template-columns:1fr}}
        @media(max-width:530px){.sa-fields{grid-template-columns:repeat(2,minmax(0,1fr))}.sa-field,.sa-field.half,.sa-field.quarter,.sa-field.span8,.sa-file-field{grid-column:span 2}.sa-cover-button{right:7px;top:8px}.sa-photo-button{font-size:9px;padding:7px}.sa-shop-strip{padding-left:105px}.sa-shop-name{font-size:15px}.sa-navigation{padding:12px}.sa-nav-heading{padding-bottom:10px}.sa-nav-link{font-size:11px}.sa-actions{justify-content:stretch}.sa-action{flex:1;padding:0 8px}}

        /* Seller account documents, read-only approved categories, category requests */
        .sa-readonly-tags{display:flex;flex-wrap:wrap;align-items:center;gap:8px;min-height:37px;padding:5px 0}
        .sa-tag-static{padding:6px 11px;font-size:11px;font-weight:500;cursor:default}
        .sa-request-button{display:inline-flex;align-items:center;justify-content:center;min-height:36px;margin-top:12px;padding:0 14px;border:1px solid #9b1239;border-radius:8px;color:#9b1239;background:#fff;font-size:11px;font-weight:600;cursor:pointer}
        .sa-request-button:hover:not(:disabled){background:#fcf0f2}
        .sa-request-button:disabled{cursor:not-allowed;opacity:.58}
        .sa-request-hint{margin:7px 0 0;color:#8b94a4;font-size:10px;line-height:1.6}
        .sa-document-preview{margin-top:8px;padding:0;border:0;background:none;color:#9b1239;font-size:11px;font-weight:500;text-decoration:underline;cursor:pointer}
        .sa-document-preview[hidden],.sa-modal-overlay[hidden]{display:none!important}
        body.sa-modal-open{overflow:hidden}
        .sa-modal-overlay{position:fixed;inset:0;z-index:200;display:grid;place-items:center;padding:18px;background:rgba(18,22,30,.55);backdrop-filter:blur(3px)}
        .sa-modal-dialog{position:relative;width:min(650px,100%);max-height:calc(100vh - 36px);overflow:auto;padding:24px;background:white;border-radius:17px;box-shadow:0 20px 60px rgba(0,0,0,.2);font-family:Poppins,sans-serif}
        .sa-modal-dialog h2{margin:0 38px 6px 0;font-size:18px;font-weight:600;color:#182033}
        .sa-modal-description{margin:0 0 18px;color:#8b94a4;font-size:11px;line-height:1.6}
        .sa-modal-close{position:absolute;right:18px;top:15px;width:32px;height:32px;border:0;border-radius:50%;background:#f6f4f4;cursor:pointer;font-size:21px;color:#5c6570}
        .sa-preview-media{display:grid;place-items:center;min-height:160px;background:#f7f6f5;border-radius:10px;overflow:hidden}
        .sa-preview-media img{display:block;width:auto;max-width:100%;max-height:70vh;object-fit:contain}
        .sa-request-options{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:9px;max-height:290px;overflow:auto}
        .sa-request-choice{display:flex;align-items:center;gap:9px;padding:10px;border:1px solid #e5e3e3;border-radius:8px;font-size:11px;color:#32303a;cursor:pointer}
        .sa-request-choice:has(input:checked){background:#fff5f7;border-color:#eac6d0}
        .sa-request-choice input{accent-color:#9b1239;width:15px;height:15px;flex-shrink:0}
        .sa-request-reason{display:block;width:100%;min-height:92px;margin-top:7px;padding:11px 12px;border:1px solid #d8dbe1;border-radius:9px;resize:vertical;font:400 12px Poppins,sans-serif;outline:none}
        .sa-request-reason:focus{border-color:#9ca3af;box-shadow:0 0 0 2px rgba(156,163,175,.15)}
        .sa-modal-buttons{display:flex;justify-content:flex-end;gap:10px;margin-top:18px}
        .sa-modal-buttons button{padding:9px 15px;border-radius:8px;border:1px solid #d2d6dc;background:#fff;font:500 11px Poppins,sans-serif;cursor:pointer}
        .sa-modal-buttons button.primary{border-color:#9b1239;background:#9b1239;color:white}
        .sa-flash-success{background:#f0faf2;color:#286a40;border:1px solid #bde3c7}
        @media(max-width:530px){.sa-request-options{grid-template-columns:1fr}}

         /* Exact category colors used by ShopEase */
         .category-pet-supplies{background:#E7F5E9;color:#2F6B3A}
         .category-electronics-and-gadgets{background:#DDEBFF;color:#185FA3}
         .category-womens-apparel{background:#F9DFEA;color:#A12763}
         .category-mens-apparel{background:#E6E2F8;color:#5A4A9A}
         .category-kids-and-baby{background:#FFE5B8;color:#9A5B00}
         .category-home-and-garden{background:#EEF0D8;color:#596126}
         .category-sports-and-outdoors{background:#DDECF2;color:#23627A}
         .category-health-and-beauty{background:#FFE0DC;color:#A63B2C}
         .category-books-and-media{background:#E6E8F2;color:#3F4A68}
         .category-food-and-gourmet{background:#FFF0C7;color:#8A5A00}
         .category-automotive-motorcycle{background:#E3E3E3;color:#434343}
         .category-furniture-and-office-equipment{background:#EBDCCF;color:#795548}
         .category-jewelry-and-watches{background:#F8E2B8;color:#946B00}
         .category-office-and-school-supplies{background:#E2F0F7;color:#2B617D}
         .sa-field-label{display:block;font-size:11px;font-weight:500;color:#252c3a;margin-bottom:7px}
         .sa-document-list{display:grid;gap:8px}
         .sa-doc-tile{display:flex;align-items:center;gap:11px;width:100%;padding:7px;background:#fff;border:1px solid #e5e8ed;border-radius:9px;text-align:left;cursor:pointer;transition:background .2s,border-color .2s}
         .sa-doc-tile:hover{background:#fff8f8;border-color:#d9a7b3}
         .sa-doc-thumb{width:57px;height:53px;display:grid;place-items:center;overflow:hidden;border-radius:6px;background:#f7f6f6;flex-shrink:0}
         .sa-doc-thumb img{width:100%;height:100%;object-fit:cover}
         .sa-doc-name{flex:1;min-width:0;overflow-wrap:anywhere;font-size:11px;font-weight:500;color:#263246}
         .sa-doc-name small{display:block;font-size:10px;color:#88909c;margin-top:4px}
         .sa-doc-eye{color:#9b1239;font-size:17px;margin-right:8px}
         .sa-doc-empty{padding:16px 10px;border:1px dashed #d8dbe1;border-radius:8px;font-size:11px;color:#8b94a4}
         .sa-doc-actions{display:flex;flex-wrap:wrap;gap:8px}
         .sa-doc-action{margin-top:9px;font-weight:500;min-height:34px;font-size:10px}
         .sa-document-file{display:block;width:100%;margin-top:7px;border:1px dashed #d8dbe1;border-radius:8px;padding:12px;font-size:11px}
         .sa-inline-preview{display:none;max-height:170px;max-width:100%;margin-top:10px;object-fit:contain;border-radius:7px;cursor:zoom-in}
         .sa-cover:has(img[src]){cursor:zoom-in}
         .sa-avatar>img{cursor:zoom-in}

/* Account settings extension */
.sa-tab-panel[hidden],.sa-modal-overlay[hidden]{display:none!important}
.sa-global-flash{grid-column:1/-1;padding:13px 17px;border-radius:10px;font-size:12px;line-height:1.6}
.sa-global-success{background:#edf9f0;color:#246238;border:1px solid #b9dec2}.sa-global-error{background:#fff3e7;color:#985d21;border:1px solid #f2d1a7}
.sa-settings-card{border:1px solid #e8e8eb;border-radius:12px;padding:23px;margin-top:16px;background:#fff}
.sa-settings-top{display:flex;gap:13px;align-items:center;margin-bottom:23px}.sa-settings-emblem{width:42px;height:42px;display:grid;place-items:center;background:#fff1f3;border-radius:12px;font-size:19px}
.sa-settings-top h2,.sa-account-recovery h2{font-size:16px;font-weight:600;margin:0 0 3px}.sa-settings-top p,.sa-account-recovery p{font-size:11px;color:#8891a0;margin:0;line-height:1.7}
.sa-settings-form{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}.sa-settings-form.single{grid-template-columns:1fr}.sa-settings-form .sa-full{grid-column:1/-1;max-width:550px}.sa-settings-form label{display:block;font-size:12px;font-weight:500;margin-bottom:8px}.sa-settings-form small{display:block;font-size:10px;color:#8b94a4;margin-top:7px}
.sa-settings-actions{display:flex;justify-content:flex-end;margin:18px 0 3px}.sa-settings-primary{border:0;background:#9b1239;color:white;border-radius:8px;padding:12px 21px;font:500 11px Poppins,sans-serif;cursor:pointer;min-height:39px}.sa-settings-primary:hover:not(:disabled){background:#800b2d}.sa-settings-primary:disabled{background:#dccdd2;cursor:not-allowed}
.sa-account-recovery{display:flex;align-items:center;justify-content:space-between;gap:18px;background:#fffbfa}.sa-account-recovery div{max-width:600px}.sa-settings-outline{white-space:nowrap;background:#fff;color:#9b1239;border:1px solid #9b1239;border-radius:8px;padding:10px 14px;font:500 11px Poppins,sans-serif;cursor:pointer}.sa-settings-outline:hover{background:#fff1f4}
.sa-toggle-row{display:flex;justify-content:space-between;align-items:center;gap:20px;padding:15px 0;border-top:1px solid #f0eef0;cursor:pointer}.sa-toggle-row strong{display:block;font-size:12px;font-weight:500}.sa-toggle-row small{display:block;color:#8b94a4;font-size:10px;margin-top:3px}.sa-switch{position:relative;flex:0 0 41px;width:41px;height:23px}.sa-switch input{opacity:0;position:absolute;inset:0;width:100%;height:100%;cursor:pointer;z-index:2}.sa-switch-track{display:block;width:41px;height:23px;background:#d0d3d7;border-radius:25px;transition:.2s}.sa-switch-track:after{content:'';position:absolute;top:3px;left:3px;width:17px;height:17px;border-radius:50%;background:white;transition:.2s}.sa-switch input:checked+.sa-switch-track{background:#9b1239}.sa-switch input:checked+.sa-switch-track:after{transform:translateX(18px)}.sa-switch input:focus-visible+.sa-switch-track{outline:2px solid #777;outline-offset:3px}
.sa-policy-document{max-width:980px;margin:17px auto;padding:45px 53px;background:#fff;border:1px solid #e8e2df;box-shadow:0 8px 30px rgba(60,39,31,.06);border-radius:10px;color:#282d39}.sa-policy-header{text-align:center}.sa-policy-header h2{margin:12px 0 3px;font-size:26px;letter-spacing:-.7px}.sa-policy-header>p{font-size:11px;color:#858a95}.sa-policy-stamp{display:inline-block;border:1px solid #e5cbd0;color:#8b1e3b;padding:5px 11px;border-radius:5px;font-size:10px;letter-spacing:1px}.sa-policy-rule{height:2px;background:#9b1239;margin:25px 0}.sa-policy-note{padding:12px;border:1px solid #f0dfb7;background:#fffaf0;text-align:left;line-height:1.7}.sa-policy-index{display:grid;gap:10px;margin:32px 0 40px;padding:23px;background:#fbf8f7;border-radius:8px;font-size:12px}.sa-policy-index strong{font-size:13px;margin-bottom:2px}.sa-policy-index a{color:#6c3043;text-decoration:none}.sa-policy-index a:hover{text-decoration:underline}.sa-policy-document section{margin:0 0 30px;scroll-margin-top:125px}.sa-policy-document section h3{font-size:15px;border-bottom:1px solid #eae3e3;padding-bottom:10px;margin:0 0 12px}.sa-policy-document section p{font-size:12px;line-height:2;color:#4b5260;margin:0}.sa-policy-footer{border-top:1px solid #e7dfe0;padding-top:15px;display:flex;justify-content:space-between;color:#9ba0a8;font-size:10px}
.sa-security-dialog{max-width:490px;text-align:center}.sa-otp-icon{width:76px;height:76px;margin:0 auto 14px;background:#ffe1dc;border-radius:50%;display:grid;place-items:center}.sa-otp-icon img{height:40px;width:40px;object-fit:contain}.sa-otp-description{text-align:center}.sa-otp-digits{display:flex;justify-content:center;gap:9px;margin:26px 0}.sa-otp-digit{width:45px;height:54px;border:1px solid #b9bcc3;border-radius:9px;text-align:center;font:600 20px Poppins,sans-serif;outline:0}.sa-otp-digit:focus{border-color:#9b1239;box-shadow:0 0 0 2px #f8e2e9}.sa-otp-hint{font-size:11px;color:#777f8d;background:#fff1ee;padding:11px;border-radius:9px}.sa-otp-resend{border:0;background:none;color:#9b1239;margin:8px 0 16px;font:500 11px Poppins,sans-serif;cursor:pointer}.sa-otp-resend:disabled{opacity:.5;cursor:not-allowed}.sa-otp-verify{width:100%;margin-top:8px}.sa-security-message{color:#ae1b36;font-size:11px;margin:12px 0}.sa-security-dialog .sa-settings-form{text-align:left;margin-top:15px}
@media(max-width:760px){.sa-settings-form{grid-template-columns:1fr}.sa-settings-form .sa-full{grid-column:auto}.sa-account-recovery{flex-direction:column;align-items:flex-start}.sa-policy-document{padding:25px 18px}.sa-otp-digit{width:38px;height:50px}.sa-otp-digits{gap:6px}}

/* ShopEase account section PNG icons without colored backing */
.sa-section-icon,.sa-section-icon.purple,.sa-section-icon.red,.sa-settings-emblem{background:transparent!important;border-radius:0;display:flex;justify-content:center;align-items:center;width:28px;height:28px;padding:0;flex:0 0 28px}
.sa-section-icon img,.sa-settings-emblem img{display:block;width:24px;height:24px;object-fit:contain}
/* Email verification styling matched to the uploaded registration */

        .email-verification-modal {
            position:
                fixed;

            inset:
                0;

            background:
                rgba(
                    22,
                    12,
                    12,
                    0.55
                );

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            padding:
                20px;

            opacity:
                0;

            visibility:
                hidden;

            pointer-events:
                none;

            transition:
                opacity 0.2s ease,
                visibility 0.2s ease;

            z-index:
                10000;
        }


        .email-verification-modal.active {
            opacity:
                1;

            visibility:
                visible;

            pointer-events:
                auto;
        }


        .email-verification-card {
            width:
                100%;

            max-width:
                480px;

            background:
                #FFFFFF;

            border-radius:
                22px;

            padding:
                40px
                32px
                30px;

            text-align:
                center;

            box-shadow:
                0
                20px
                55px
                rgba(
                    0,
                    0,
                    0,
                    0.20
                );

            transform:
                scale(0.95);

            transition:
                transform 0.2s ease;
        }


        .email-verification-modal.active
        .email-verification-card {
            transform:
                scale(1);
        }


        .email-verification-icon-wrapper {
            width:
                88px;

            height:
                88px;

            margin:
                0
                auto
                20px;

            border-radius:
                50%;

            background:
                #FFE1DC;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;
        }


        .email-verification-icon {
            width:
                48px;

            height:
                48px;

            object-fit:
                contain;
        }


        .email-verification-title {
            margin:
                0;

            font-size:
                24px;

            line-height:
                1.2;

            font-weight:
                700;

            color:
                #000000;
        }


        .email-verification-subtitle {
            margin:
                8px
                0
                28px;

            font-size:
                16px;

            line-height:
                1.4;

            font-weight:
                400;

            color:
                #919191;
        }


        .email-code-container {
            display:
                flex;

            justify-content:
                center;

            gap:
                10px;

            margin-bottom:
                25px;
        }


        .email-code-input {
            width:
                52px;

            height:
                62px;

            border:
                1.5px solid
                #6D6D6D;

            border-radius:
                11px;

            background:
                #FFFFFF;

            text-align:
                center;

            font-family:
                'Poppins',
                sans-serif;

            font-size:
                23px;

            font-weight:
                600;

            color:
                #161616;

            outline:
                none;

            transition:
                border-color 0.2s ease,
                box-shadow 0.2s ease;
        }


        .email-code-input:focus {
            border-color:
                #7B1B1B;

            box-shadow:
                0
                0
                0
                3px
                rgba(
                    123,
                    27,
                    27,
                    0.08
                );
        }


        .email-spam-message {
            width:
                100%;

            min-height:
                48px;

            padding:
                10px
                12px;

            margin-bottom:
                22px;

            border-radius:
                11px;

            background:
                #FFE8E4;

            display:
                flex;

            align-items:
                center;

            text-align:
                left;

            color:
                #6E1717;

            font-size:
                13px;

            font-weight:
                400;
        }


        .email-spam-icon {
            width:
                19px;

            height:
                19px;

            margin-right:
                9px;

            flex-shrink:
                0;
        }


        .email-divider {
            display:
                flex;

            align-items:
                center;

            gap:
                14px;

            margin:
                0
                10px
                20px;
        }


        .email-divider-line {
            flex:
                1;

            height:
                1px;

            background:
                #BDBDBD;
        }


        .email-divider-text {
            font-size:
                17px;

            color:
                #929292;

            font-weight:
                400;
        }


        .email-resend-button {
            border:
                none;

            background:
                transparent;

            padding:
                0;

            margin-bottom:
                24px;

            display:
                inline-flex;

            align-items:
                center;

            justify-content:
                center;

            gap:
                6px;

            font-family:
                'Poppins',
                sans-serif;

            font-size:
                15px;

            font-weight:
                400;

            color:
                #A52A2A;

            cursor:
                pointer;
        }


        .email-resend-button:hover {
            color:
                #7B1B1B;
        }


        .email-resend-icon {
            width:
                18px;

            height:
                18px;
        }


        .email-verify-button {
            width:
                100%;

            height:
                48px;

            border:
                none;

            border-radius:
                24px;

            background:
                #A52A2A;

            color:
                #FFFFFF;

            font-family:
                'Poppins',
                sans-serif;

            font-size:
                16px;

            font-weight:
                600;

            cursor:
                pointer;

            transition:
                background 0.2s ease,
                transform 0.1s ease,
                box-shadow 0.2s ease;
        }


        .email-verify-button:hover {
            background:
                #8F211F;

            box-shadow:
                0
                5px
                14px
                rgba(
                    123,
                    27,
                    27,
                    0.18
                );
        }


        .email-verify-button:active {
            transform:
                scale(0.98);
        }


        .email-verify-button:disabled {
            opacity:
                0.6;

            cursor:
                not-allowed;

            box-shadow:
                none;
        }


        
.email-verification-card{transform:none!important;max-height:calc(100vh - 32px);overflow-y:auto}
.email-verification-card .email-verification-subtitle strong{color:#6E1717;font-weight:600}
.email-verification-card .sa-security-message{margin:8px 0;color:#a52a2a}
@media(max-width:530px){.email-verification-card{padding:30px 18px 24px}.email-code-container{gap:6px}.email-code-input{width:39px;height:52px;font-size:19px}.email-verification-title{font-size:21px}.email-verification-subtitle{font-size:13px}}

    
/* Unified account toast — matches Admin Registrations decision flash */
.account-decision-flash{position:fixed;right:24px;bottom:24px;z-index:100050;width:min(370px,calc(100vw - 32px));min-height:72px;display:flex;align-items:flex-start;gap:11px;padding:13px 14px;box-sizing:border-box;background:#fff;border:1px solid #E7E2DF;border-radius:14px;box-shadow:0 14px 34px rgba(42,20,15,.16);opacity:0;visibility:hidden;pointer-events:none;transform:translateY(10px);transition:opacity .08s ease,transform .08s ease,visibility .08s ease;font-family:Poppins,sans-serif}
.account-decision-flash.show{opacity:1;visibility:visible;pointer-events:auto;transform:translateY(0)}
.account-decision-flash.approved{border-color:#BFDDB8}
.account-decision-flash.rejected,.account-decision-flash.error{border-color:#E7A8AB}
.account-decision-flash .account-flash-body{display:flex;align-items:flex-start;gap:12px;width:100%;min-width:0}
.account-decision-flash .account-flash-icon{width:36px;height:36px;flex-shrink:0;border-radius:50%;display:grid;place-items:center;background:#DDF0D6;color:#28721B}
.account-decision-flash.error .account-flash-icon{background:#FFE3E5;color:#B3262E}
.account-decision-flash .account-flash-icon svg{width:20px;height:20px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.account-decision-flash .account-flash-copy{flex:1;min-width:0;padding-top:1px}
.account-decision-flash .account-flash-title{font-size:14px;font-weight:600;line-height:1.4;color:#111827;margin:0}
.account-decision-flash .account-flash-message{margin:4px 0 0;font-size:12px;line-height:20px;font-weight:400;color:#6b7280;overflow-wrap:anywhere}
.account-decision-flash .account-flash-close{flex:0 0 28px;width:28px;height:28px;display:grid;place-items:center;border:0;border-radius:999px;background:transparent;color:#9ca3af;cursor:pointer;transition:background .2s}
.account-decision-flash .account-flash-close:hover{background:#f3f4f6}
.account-decision-flash .account-flash-close svg{width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round}
@media(max-width:640px){.account-decision-flash{right:16px;bottom:16px}}



.sa-floating-flash{position:fixed;z-index:99999;top:90px;right:24px;max-width:390px;padding:14px 18px;border-radius:10px;font:500 12px Poppins,sans-serif;box-shadow:0 8px 25px #0002;display:none}.sa-floating-flash.success{display:block;color:#246238;background:#eef9f0;border:1px solid #b9dec2}.sa-floating-flash.error{display:block;color:#9a2f26;background:#fff2ec;border:1px solid #f0c8b8}

.la-page .sa-workspace{padding:19px 21px}.la-page .sa-settings-card{max-width:none}.la-page .sa-policy-document{max-width:none}
.la-page .sa-page-title{font-size:23px;font-weight:600}.la-page .sa-intro{font-size:12px;font-weight:400}.la-page .la-field{font-size:11px;font-weight:500}.la-page .la-field input{height:39px;font-size:11px;font-weight:400}
.la-page .la-panel{padding:19px 21px}.la-page .la-identity h2{font-size:20px;font-weight:600}.la-page .la-group{font-size:14px;font-weight:600}.la-page .la-btn{font-size:11px;font-weight:500}.la-page .la-muted-btn{font-size:11px}
.la-page .sa-settings-card{margin-bottom:16px}.la-page .sa-policy-document{margin-top:14px}
.la-photo-button svg{width:15px;height:15px;flex:0 0 15px}
.la-avatar-camera svg{width:14px;height:14px}
.la-cover img{width:100%;height:100%;display:block;object-fit:cover}.la-cover-button{position:absolute;top:16px;right:18px;z-index:2}.la-photo-button{display:inline-flex;align-items:center;gap:7px;background:#fff;border:1px solid #d6d9df;border-radius:7px;padding:9px 12px;font:500 11px Poppins,sans-serif;color:#253044;cursor:pointer}.la-avatar{position:relative;width:124px;height:124px;min-width:124px;border:5px solid white;overflow:visible}.la-avatar img{width:100%;height:100%;object-fit:cover;border-radius:50%}.la-avatar-camera{position:absolute;bottom:5px;right:2px;width:28px;height:28px;display:grid;place-items:center;border:3px solid #fff;border-radius:50%;background:#485467;color:white;cursor:pointer;font-size:15px}.la-avatar-line{margin:-48px 20px 25px}.la-identity{padding-top:45px}.la-identity h2{margin-top:0}.la-photo-hint{font:400 10px Poppins;color:#918a87;margin:8px 0 0}.la-photo-save{display:flex;gap:10px;align-items:center;justify-content:flex-end;margin:7px 0 20px}.la-photo-save [hidden]{display:none!important}.la-account-toast{padding:11px 15px;background:#eaf7ee;color:#2e7142;border:1px solid #c4e8cf;border-radius:9px;font-size:11px;margin-bottom:12px}.la-account-toast[hidden]{display:none}.la-photo-view{position:fixed;inset:0;background:#1d1317aa;display:grid;place-items:center;z-index:1900;padding:20px}.la-photo-view[hidden]{display:none}.la-photo-view-dialog{background:#fff;border-radius:15px;padding:18px;max-width:min(700px,100%);max-height:90vh;overflow:auto}.la-photo-view img{width:auto;height:auto;max-width:100%;max-height:70vh;display:block;margin:12px auto}.la-photo-view header{display:flex;justify-content:space-between;align-items:center;gap:10px}.la-photo-view button{border:0;background:#f8eeed;border-radius:50%;width:32px;height:32px;cursor:pointer}
.la-cover{position:relative;overflow:hidden;background:linear-gradient(110deg,#ded5cb 0%,#f6efeb 45%,#d8c9c1 100%);height:165px;border-radius:11px}
@media(max-width:700px){.la-cover{height:140px}.la-avatar{height:98px;width:98px;min-width:98px}.la-identity{padding-top:28px}.la-cover-button{top:8px;right:8px}}
</style></head><body class="lg-body">
@include('components.logistics.sidebar')
@include('components.logistics.navbar')
<main class="la-page" id="laPage"><div class="la-layout"><aside class="la-side"><div class="la-menu-head">Company Account</div><nav class="la-menu" aria-label="Company settings"><a href="#profile" class="la-nav active" data-section="profile">Profile</a><a href="#privacy" class="la-nav" data-section="privacy">Privacy Settings</a><a href="#notifications" class="la-nav" data-section="notifications">Notification Settings</a><a href="#policies" class="la-nav" data-section="policies">Platform Policies</a></nav></aside><div>
<div id="laPreviewPending" class="la-pending" hidden><strong>Company profile requests</strong><p>Requests are saved to your account for ShopEase administration to review.</p><ul id="laPreviewRequestList"></ul></div><section class="la-panel" data-content="profile"><h1>Company Account</h1><p class="la-desc">Review your registered logistics business information.</p><div class="la-cover" id="laCover"><img id="laCoverImage" src="{{ $accountCoverPhotoUrl ?? "" }}" alt="Company cover photo" @if(empty($accountCoverPhotoUrl)) hidden @endif><label class="la-cover-button la-photo-button" for="laCoverInput"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h3l2-2h6l2 2h3a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2Z"/><circle cx="12" cy="13" r="3"/></svg>Change Cover</label><input type="file" id="laCoverInput" accept="image/png,image/jpeg,image/webp" hidden></div><div class="la-avatar-line"><div class="la-avatar" id="laAvatar"><img id="laAvatarImage" src="{{ $accountAvatarUrl ?? "" }}" alt="Company profile photo" @if(empty($accountAvatarUrl)) hidden @endif><span id="laAvatarInitials">{{ $initials }}</span><label class="la-avatar-camera" for="laAvatarInput" title="Change profile photo" aria-label="Change profile photo"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h3l2-2h6l2 2h3a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2Z"/><circle cx="12" cy="13" r="3"/></svg></label><input type="file" id="laAvatarInput" accept="image/png,image/jpeg,image/webp" hidden></div><div class="la-identity"><h2>{{ $companyName }}</h2><p>ShopEase Logistics Partner · Company profile</p><label class="la-photo-button" for="laAvatarInput" style="margin-top:8px">Change Profile Photo</label></div></div><div id="laPhotoNotice" class="la-account-toast" hidden>Photo preview updated.</div><div class="la-photo-save"><span class="la-photo-hint">Profile and cover photos do not require admin approval.</span><button type="button" id="laPhotoSave" class="la-btn" hidden>Save Photos</button></div><div class="la-note">Changes to registered company information are sent to ShopEase administration for review before they take effect.</div><form method="POST" action="#" class="la-approval-form"><input type="hidden" name="section" value="profile"><div class="la-grid"><h3 class="la-group">Company Information</h3>
<label class="la-field"><span>Company / Logistics Business Name</span><input name="company_name" type="text" value="{{ old('company_name', $profile['company_name'] ?? '') }}" autocomplete="off" ></label>
<label class="la-field"><span>Registered Legal Business Name</span><input name="registered_business_name" type="text" value="{{ old('registered_business_name', $profile['registered_business_name'] ?? '') }}" autocomplete="off" ></label>
<label class="la-field"><span>Company Type</span><input name="company_type" type="text" value="{{ old('company_type', $profile['company_type'] ?? '') }}" autocomplete="off" ></label>
<label class="la-field"><span>SEC Registration No.</span><input name="sec_registration_number" type="text" value="{{ old('sec_registration_number', $profile['sec_registration_number'] ?? '') }}" autocomplete="off" ></label>
<label class="la-field"><span>TIN</span><input name="tin" type="text" value="{{ old('tin', $profile['tin'] ?? '') }}" autocomplete="off" ></label>
<label class="la-field"><span>Company Email</span><input name="company_email" type="email" value="{{ old('company_email', $profile['company_email'] ?? '') }}" autocomplete="off" ></label>
<label class="la-field"><span>Company Contact Number</span><input name="company_phone" type="tel" value="{{ old('company_phone', $profile['company_phone'] ?? '') }}" autocomplete="off" ></label>
<label class="la-field"><span>Company Website</span><input name="website" type="url" value="{{ old('website', $profile['website'] ?? '') }}" autocomplete="off" ></label>
<label class="la-field"><span>Number of Branches</span><input name="branch_count" type="number" value="{{ old('branch_count', $profile['branch_count'] ?? '') }}" autocomplete="off" min="0"></label>
<h3 class="la-group">Authorized Representative & Operations</h3>
<label class="la-field"><span>Years in Operation</span><input name="years_in_operation" type="number" value="{{ old('years_in_operation', $profile['years_in_operation'] ?? '') }}" autocomplete="off" min="0"></label>
<label class="la-field"><span>Authorized Representative</span><input name="representative_name" type="text" value="{{ old('representative_name', $profile['representative_name'] ?? '') }}" autocomplete="off" ></label>
<label class="la-field"><span>Position</span><input name="representative_position" type="text" value="{{ old('representative_position', $profile['representative_position'] ?? '') }}" autocomplete="off" ></label>
<label class="la-field"><span>Representative Email</span><input name="representative_email" type="email" value="{{ old('representative_email', $profile['representative_email'] ?? '') }}" autocomplete="off" ></label>
<label class="la-field"><span>Representative Contact</span><input name="representative_phone" type="tel" value="{{ old('representative_phone', $profile['representative_phone'] ?? '') }}" autocomplete="off" ></label>
<h3 class="la-group">Registered Company Address</h3>
<label class="la-field"><span>Region</span><input name="region" type="text" value="{{ old('region', $profile['region'] ?? '') }}" autocomplete="off" ></label>
<label class="la-field"><span>Province</span><input name="province" type="text" value="{{ old('province', $profile['province'] ?? '') }}" autocomplete="off" ></label>
<label class="la-field"><span>Municipality / City</span><input name="city" type="text" value="{{ old('city', $profile['city'] ?? '') }}" autocomplete="off" ></label>
<label class="la-field"><span>Barangay</span><input name="barangay" type="text" value="{{ old('barangay', $profile['barangay'] ?? '') }}" autocomplete="off" ></label>
<label class="la-field"><span>ZIP Code</span><input name="postal_code" type="text" value="{{ old('postal_code', $profile['postal_code'] ?? '') }}" autocomplete="off" ></label>
<label class="la-field"><span>Street</span><input name="street" type="text" value="{{ old('street', $profile['street'] ?? '') }}" autocomplete="off" ></label>
<label class="la-field"><span>House No.</span><input name="house_no" type="text" value="{{ old('house_no', $profile['house_no'] ?? '') }}" autocomplete="off" ></label>
<label class="la-field"><span>Building</span><input name="building" type="text" value="{{ old('building', $profile['building'] ?? '') }}" autocomplete="off" ></label>
<label class="la-field"><span>Subdivision</span><input name="subdivision" type="text" value="{{ old('subdivision', $profile['subdivision'] ?? '') }}" autocomplete="off" ></label>
</div><div class="la-actions"><button type="reset" class="la-muted-btn">Reset</button><button type="submit" class="la-btn" disabled>Request Approval</button></div></form></section>
            <div class="la-panel sa-workspace sa-tab-panel" data-content="privacy" hidden>
                <h1 class="sa-page-title">Privacy Settings</h1>
                <p class="sa-intro">Protect your logistics company account and manage your password securely.</p>
                <section class="sa-settings-card">
                    <div class="sa-settings-top"><span class="sa-settings-emblem"><img src="{{ asset('icons/seller/account/password.png') }}" alt="" aria-hidden="true"></span><div><h2>Change Password</h2><p>Use your current password and verify your registered email before saving.</p></div></div>
                    <form id="laChangePasswordForm" autocomplete="off">
                        <div class="sa-settings-form">
                            <div class="sa-full"><label for="saCurrentPassword">Current Password <span class="sa-required">*</span></label><input type="password" id="saCurrentPassword" name="current_password" autocomplete="current-password" class="sa-control" required></div>
                            <div><label for="saNewPassword">New Password <span class="sa-required">*</span></label><input type="password" id="saNewPassword" name="new_password" autocomplete="new-password" class="sa-control" minlength="8" required><small>At least 8 characters. Use a strong and unique password.</small></div>
                            <div><label for="saConfirmPassword">Confirm New Password <span class="sa-required">*</span></label><input type="password" id="saConfirmPassword" name="new_password_confirmation" autocomplete="new-password" class="sa-control" minlength="8" required></div>
                        </div>
                        <div class="sa-settings-actions"><button type="submit" class="sa-settings-primary" id="laChangePasswordButton" disabled>Verify Email and Change Password</button></div>
                    </form>
                </section>
                <section class="sa-settings-card sa-account-recovery"><div><h2>Forgot your password?</h2><p>Verify the email associated with your logistics company account, then create a new password. Your current password will not be required.</p></div><button type="button" class="sa-settings-outline" id="laForgotPasswordButton">Reset Password</button></section>
            </div>

            <div class="la-panel sa-workspace sa-tab-panel" data-content="notifications" hidden>
                <h1 class="sa-page-title">Notification Settings</h1>
                <p class="sa-intro">Choose which ShopEase notifications you want to receive.</p>
                <form id="laNotificationSettingsForm" action="#" method="POST">
                    @csrf
                    @php $saPreferences = $notifications ?? []; @endphp
                    <section class="sa-settings-card">
                        <div class="sa-settings-top"><span class="sa-settings-emblem"><img src="{{ asset('icons/seller/account/notification.png') }}" alt="" aria-hidden="true"></span><div><h2>Order & Shop Activity</h2><p>Stay updated on important actions affecting your logistics network.</p></div></div>
                        @foreach([ 'parcel_updates' => ['Parcel movements','Sorting center and delivery hub hand-offs'], 'order_updates' => ['Order updates','Shipping, delivery, cancellation and return updates'], 'messages' => ['Messages','New buyer and admin conversations'], 'delivery_exceptions' => ['Delivery exceptions','Failed deliveries, delays and escalation reports'], 'compliance' => ['Compliance & approval','Company requests, permits and compliance notices'] ] as $key => $copy)
                            <label class="sa-toggle-row"><span><strong>{{ $copy[0] }}</strong><small>{{ $copy[1] }}</small></span><span class="sa-switch"><input type="checkbox" name="preferences[{{ $key }}]" value="1" @checked((bool)($saPreferences[$key] ?? true))><span class="sa-switch-track"></span></span></label>
                        @endforeach
                    </section>
                    <section class="sa-settings-card"><div class="sa-settings-top"><span class="sa-settings-emblem"><img src="{{ asset('icons/seller/account/notification.png') }}" alt="" aria-hidden="true"></span><div><h2>Communication Preferences</h2><p>Decide which email notifications and updates matter to you.</p></div></div>
                        @foreach(['email_alerts' => ['Email notifications','Receive a copy of important account alerts by email'], 'announcements' => ['Platform announcements','Feature updates, maintenance and logistics announcements'], 'promotions' => ['Marketing & tips','Optional logistics operations growth guides and promotional tips']] as $key=>$copy)
                            <label class="sa-toggle-row"><span><strong>{{ $copy[0] }}</strong><small>{{ $copy[1] }}</small></span><span class="sa-switch"><input type="checkbox" name="preferences[{{ $key }}]" value="1" @checked((bool)($saPreferences[$key] ?? ($key !== 'promotions')))><span class="sa-switch-track"></span></span></label>
                        @endforeach
                    </section>
                    <div class="sa-settings-actions"><button type="submit" class="sa-settings-primary" id="laSaveNotifications" disabled>Save Preferences</button></div>
                </form>
            </div>

            <div class="la-panel sa-workspace sa-tab-panel" data-content="policies" hidden>
                <h1 class="sa-page-title">Platform Policies</h1>
                <p class="sa-intro">Logistics guidelines and platform expectations in a readable document format.</p>
                <article class="sa-policy-document">
                    <header class="sa-policy-header"><div class="sa-policy-stamp">ShopEase · LOGISTICS DOCUMENT</div><h2>Logistics Platform Policies</h2><p>Reference guide for logistics partners</p><div class="sa-policy-rule"></div><p class="sa-policy-note">Illustrative policy summary for UI purposes. Replace this text with your organization's approved and current policies before publishing.</p></header>
                    <nav class="sa-policy-index" aria-label="Document contents"><strong>Contents</strong><a href="#saPolicy1">01. Company responsibilities</a><a href="#saPolicy2">02. Hubs & service areas</a><a href="#saPolicy3">03. Parcel handling & deliveries</a><a href="#saPolicy4">04. Customer & seller communication</a><a href="#saPolicy5">05. Privacy & security</a><a href="#saPolicy6">06. Compliance & requests</a></nav>
                    <section id="saPolicy1"><h3>01. Company responsibilities</h3><p>Maintain accurate company and authorized representative information, required permits, and service-area coverage.</p></section>
                    <section id="saPolicy2"><h3>02. Hubs & service areas</h3><p>Maintain sorting centers, delivery hubs, and service area records accurately and follow safety and operational guidelines.</p></section>
                    <section id="saPolicy3"><h3>03. Parcel handling & deliveries</h3><p>Handle parcels with care, record hand-offs and status updates accurately, and report delivery delays or exceptions promptly.</p></section>
                    <section id="saPolicy4"><h3>04. Customer & seller communication</h3><p>Communicate professionally with buyers, sellers, riders, and ShopEase administrators on shipment and return matters.</p></section>
                    <section id="saPolicy5"><h3>05. Privacy & security</h3><p>Protect account credentials and customer and shipment information. Do not disclose sensitive buyer details outside authorized order processing and support workflows.</p></section>
                    <section id="saPolicy6"><h3>06. Compliance & requests</h3><p>Changes to registered logistics company details and verification documents require administrator approval; profile and cover photos are exempt.</p></section>
                    <footer class="sa-policy-footer">ShopEase Logistics Guidelines <span>End of document</span></footer>
                </article>
            </div>
</div></div></main>
<div class="la-photo-view" id="laPhotoView" hidden><div class="la-photo-view-dialog" role="dialog" aria-modal="true"><header><strong id="laPhotoTitle">Photo Preview</strong><button type="button" id="laPhotoClose" aria-label="Close">×</button></header><img id="laPhotoPreview" alt="Image preview"></div></div>
<div class="la-dialog-bg" id="laConfirm" hidden><div class="la-dialog" role="dialog" aria-modal="true"><h2>Request Admin Approval?</h2><p>This request will be saved and sent to ShopEase administration. Approved changes will update your registered company information.</p><div class="la-actions"><button class="la-muted-btn" type="button" id="laCancel">Cancel</button><button class="la-btn" type="button" id="laConfirmSend">Send Request</button></div></div></div>
<script>
document.addEventListener('DOMContentLoaded',()=>{
'use strict';
const $=id=>document.getElementById(id), csrf=document.querySelector('meta[name="csrf-token"]')?.content||'';
const api=async(url,method='GET',body=null)=>{const formData=body instanceof FormData,headers={Accept:'application/json','X-CSRF-TOKEN':csrf};if(body&&!formData)headers['Content-Type']='application/json';const response=await fetch(url,{method,credentials:'same-origin',headers,body:body?(formData?body:JSON.stringify(body)):undefined});const data=await response.json().catch(()=>({}));if(!response.ok)throw new Error(data.message||Object.values(data.errors||{}).flat()[0]||'Request failed.');return data;};
const notice=message=>{const el=$('laPhotoNotice');el.textContent=message;el.hidden=false;clearTimeout(notice.timer);notice.timer=setTimeout(()=>el.hidden=true,5000);};
const tabs=[...document.querySelectorAll('[data-section]')],panels=[...document.querySelectorAll('[data-content]')];
function show(tab){const key=['profile','privacy','notifications','policies'].includes(tab)?tab:'profile';tabs.forEach(x=>{x.classList.toggle('active',x.dataset.section===key);x.setAttribute('aria-current',x.dataset.section===key?'page':'false')});panels.forEach(x=>x.hidden=x.dataset.content!==key);}
tabs.forEach(x=>x.addEventListener('click',e=>{e.preventDefault();location.hash=x.dataset.section;show(x.dataset.section)}));addEventListener('hashchange',()=>show(location.hash.slice(1)));show(location.hash.slice(1));
const requests=@json($accountChangeRequests ?? []);
function showRequests(){const wrap=$('laPreviewPending'),list=$('laPreviewRequestList');list.replaceChildren();requests.slice().reverse().forEach(r=>{const li=document.createElement('li'),date=r.created_at?new Date(r.created_at).toLocaleString('en-PH'):'';li.textContent=(r.section==='profile'?'Company profile':'Account')+' · '+date+' · '+(r.status==='pending'?'Pending admin review':r.status);list.appendChild(li);});wrap.hidden=!requests.length;}
showRequests();
const dialog=$('laConfirm');let pendingChanges=null;
const closeDialog=()=>{dialog.hidden=true;pendingChanges=null;};
document.querySelectorAll('.la-approval-form').forEach(form=>{const inputs=[...form.querySelectorAll('input:not([type=hidden]),select,textarea')],baseline=new Map(inputs.map(x=>[x,x.value])),submit=form.querySelector('[type=submit]');const update=()=>submit.disabled=!inputs.some(x=>x.value!==baseline.get(x));form.addEventListener('input',update);form.addEventListener('submit',e=>{e.preventDefault();const changes={};inputs.forEach(x=>{if(x.value!==baseline.get(x))changes[x.name]=x.value;});if(!Object.keys(changes).length)return;pendingChanges=changes;dialog.hidden=false;$('laCancel').focus();});$('laCancel').addEventListener('click',closeDialog);});
$('laConfirmSend').addEventListener('click',async()=>{if(!pendingChanges)return;const button=$('laConfirmSend');button.disabled=true;try{const result=await api('/api/v1/logistics/account/profile-requests','POST',pendingChanges);requests.push(result.data);showRequests();document.querySelector('.la-approval-form')?.reset();document.querySelector('.la-approval-form')?.querySelector('[type=submit]')?.setAttribute('disabled','disabled');closeDialog();notice('Your company profile request was sent to administration.');}catch(error){notice(error.message);}finally{button.disabled=false;}});
dialog.addEventListener('click',e=>{if(e.target===dialog)closeDialog();});document.addEventListener('keydown',e=>{if(e.key==='Escape'&&!dialog.hidden)closeDialog();});
const photoState={};
function showPhoto(which,url){const img=$(which==='cover'?'laCoverImage':'laAvatarImage');img.src=url;img.hidden=false;if(which==='avatar')$('laAvatarInitials').hidden=true;}
[['laCoverInput','cover'],['laAvatarInput','avatar']].forEach(([id,which])=>$(id)?.addEventListener('change',()=>{const file=$(id).files?.[0];if(!file)return;if(!['image/jpeg','image/png','image/webp'].includes(file.type)||file.size>5*1024*1024){notice('Choose a JPG, PNG, or WebP image under 5 MB.');$(id).value='';return;}photoState[which]=file;showPhoto(which,URL.createObjectURL(file));$('laPhotoSave').hidden=false;}));
$('laPhotoSave')?.addEventListener('click',async()=>{const button=$('laPhotoSave');button.disabled=true;try{if(photoState.avatar){const data=new FormData();data.append('profile_photo',photoState.avatar);const result=await api('/api/v1/profile/photo','PUT',data);showPhoto('avatar',result.profile_photo_url);delete photoState.avatar;}if(photoState.cover){const data=new FormData();data.append('cover_photo',photoState.cover);const result=await api('/api/v1/logistics/account/cover-photo','POST',data);showPhoto('cover',result.cover_photo_url);delete photoState.cover;}button.hidden=true;notice('Company photos saved to your account.');}catch(error){notice(error.message);}finally{button.disabled=false;}});
const notificationForm=$('laNotificationSettingsForm');if(notificationForm){const checks=[...notificationForm.querySelectorAll('input[type=checkbox]')],button=$('laSaveNotifications'),baseline=checks.map(x=>x.checked),update=()=>button.disabled=!checks.some((x,i)=>x.checked!==baseline[i]);checks.forEach(x=>x.addEventListener('change',update));notificationForm.addEventListener('submit',async e=>{e.preventDefault();const preferences={};checks.forEach(x=>{const match=x.name.match(/^preferences\[([^\]]+)\]$/);if(match)preferences[match[1]]=x.checked;});button.disabled=true;try{await api('/api/v1/logistics/account/preferences','PUT',{notification_preferences:preferences});baseline.splice(0,baseline.length,...checks.map(x=>x.checked));update();notice('Notification preferences saved to your account.');}catch(error){notice(error.message);button.disabled=false;}});update();}
const passwordForm=$('laChangePasswordForm'),passwordButton=$('laChangePasswordButton');if(passwordForm){const fields=[...passwordForm.querySelectorAll('input')],update=()=>{passwordButton.disabled=!(fields.every(x=>x.value)&&fields[1].value.length>=8&&fields[1].value===fields[2].value)};fields.forEach(x=>x.addEventListener('input',update));passwordForm.addEventListener('submit',async e=>{e.preventDefault();passwordButton.disabled=true;try{const current_password=fields[0].value,send=await api('/api/v1/account/security/password/otp/send','POST',{current_password});notice(send.message);const code=window.prompt('Enter the 6-digit code sent to your registered email.');if(!code)return;const verified=await api('/api/v1/account/security/password/otp/verify','POST',{code});await api('/api/v1/account/security/password/change','POST',{current_password,verification_token:verified.verification_token,password:fields[1].value,password_confirmation:fields[2].value});passwordForm.reset();update();notice('Password changed successfully.');}catch(error){notice(error.message);}finally{update();}});update();}
$('laForgotPasswordButton')?.addEventListener('click',async()=>{const email=window.prompt('Enter your registered logistics account email.');if(!email)return;try{const sent=await api('/api/v1/auth/password/forgot/otp/send','POST',{email});notice(sent.message);const code=window.prompt('Enter the 6-digit code sent to your email.');if(!code)return;const verified=await api('/api/v1/auth/password/forgot/otp/verify','POST',{email,code});const password=window.prompt('Enter a new password (at least 8 characters).');if(!password)return;const confirmation=window.prompt('Confirm your new password.');if(!confirmation)return;await api('/api/v1/auth/password/forgot/reset','POST',{email,verification_token:verified.verification_token,password,password_confirmation:confirmation});notice('Password reset. Please sign in again.');}catch(error){notice(error.message);}});
$('lgSidebarToggle')?.addEventListener('click',()=>{document.body.classList.toggle('lg-collapsed');$('lgSidebarToggle').setAttribute('aria-expanded',String(!document.body.classList.contains('lg-collapsed')));});
$('lgProfileButton')?.addEventListener('click',()=>{const menu=$('lgProfileMenu');if(menu){menu.hidden=!menu.hidden;$('lgProfileButton').setAttribute('aria-expanded',String(!menu.hidden));}});
document.addEventListener('click',e=>{const menu=$('lgProfileMenu');if(menu&&!e.target.closest('.lg-profile-wrap'))menu.hidden=true;});
});
</script></body></html>
