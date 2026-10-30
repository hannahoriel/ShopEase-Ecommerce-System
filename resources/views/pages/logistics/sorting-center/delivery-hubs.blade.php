@php
    $pageName = 'Delivery Hubs';
    // Pass $sortingCenterId from a scoped controller once accounts are connected.
    $sortingCenterId = $sortingCenterId ?? null;
    $deliveryHubs = $deliveryHubs ?? null;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Delivery Hubs — ShopEase Sorting Center</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>

@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');
:root{--lg-maroon:#7B1B1B;--lg-dark:#52070B;--lg-peach:#FFE6DE;--lg-bg:#FCF8F6;--lg-line:#F0E9E6;--lg-ink:#17120F;--lg-muted:#8C8784}
*{box-sizing:border-box}body.lg-body{margin:0;background:var(--lg-bg);font-family:Poppins,sans-serif;color:var(--lg-ink)}button,input,select{font-family:inherit}button{cursor:pointer}a{text-decoration:none}body.lg-modal-open{overflow:hidden}
.lg-sidebar{position:fixed;left:0;top:0;bottom:0;width:260px;z-index:50;background:linear-gradient(155deg,#7B1B1B 0%,#52070B 80%);border-top-right-radius:58px;padding:20px 14px 18px;display:flex;flex-direction:column;transition:width .3s ease;box-shadow:4px 0 18px #52070b12;overflow:hidden;color:white}
.lg-sidebar-logo{height:74px;display:flex;align-items:center;justify-content:center;gap:7px;margin-bottom:20px;color:white;white-space:nowrap}.lg-sidebar-logo img{max-width:115px;max-height:53px;object-fit:contain}.lg-sidebar-logo>span{font-size:11px;letter-spacing:1.1px;font-weight:700}.lg-sidebar-logo small{display:block;font-size:8px;font-weight:400;letter-spacing:1.5px;opacity:.65}
.lg-sidebar-menu{overflow:auto;scrollbar-width:thin;flex:1}.lg-nav-category{margin:15px 13px 9px;font-size:10px;letter-spacing:1.4px;color:#f5c8c7;font-weight:600}.lg-nav-link{height:43px;width:100%;display:flex;align-items:center;gap:12px;padding:0 13px;border:0;background:none;color:#fff;border-radius:999px;font-size:12px;font-weight:500;text-align:left;margin:3px 0;white-space:nowrap;transition:background .2s}.lg-nav-link:hover{background:#ffffff1b}.lg-nav-link.active{background:#ffffff30}.lg-nav-link img{width:20px;height:20px;object-fit:contain;flex:0 0 20px}.lg-sidebar-bottom{border-top:1px solid #ffffff25;padding:14px 11px 3px;white-space:nowrap;font-size:11px}.lg-sidebar-bottom small{display:block;color:#ffc6bc;font-size:10px;margin-top:3px}
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

    
.lg-sidebar-menu{scrollbar-width:none;-ms-overflow-style:none}.lg-sidebar-menu::-webkit-scrollbar{display:none}.lg-nav-unavailable{opacity:.64;cursor:default;user-select:none}.lg-nav-unavailable:hover{background:none}
.lg-page-wrap{max-width:1250px;margin:0 auto}.lg-pagehead{display:flex;align-items:center;justify-content:space-between;gap:20px;margin:5px 0 19px}.lg-pagehead h1{font-size:24px;margin:0 0 5px}.lg-pagehead p{font-size:12px;color:var(--lg-muted);margin:0;max-width:580px;line-height:1.7}
.hb-filters{display:flex;gap:9px;margin:16px 0 14px}.hb-pill{padding:8px 15px;background:white;border:1px solid #eadfdb;border-radius:99px;color:#726a66;font-size:12px}.hb-pill.active{background:#7b1b1b;color:white;border-color:#7b1b1b}.hb-pill small{background:#f8ecea;border-radius:20px;padding:2px 7px;color:#7b1b1b;margin-left:4px}.hb-toolbar{display:flex;justify-content:space-between;align-items:center;gap:14px;margin-bottom:16px}.hb-toolbar input{background:white;width:min(360px,100%);height:40px;border:1px solid #e5dcd8;border-radius:9px;padding:0 14px;font-size:12px}.hb-count{color:#938b88;font-size:12px}.hb-panel{border-radius:13px;border:1px solid #ece1dc;background:white;overflow:auto}.hb-table{width:100%;border-collapse:collapse;min-width:730px}.hb-table th{text-align:left;font-size:11px;color:#7d7470;background:#fcf9f7;padding:14px 18px;border-bottom:1px solid #efe6e1;font-weight:600}.hb-table td{font-size:12px;color:#302826;padding:16px 18px;border-bottom:1px solid #f2eae7}.hb-table td small{color:#918681;display:block;margin-top:3px}.hb-table tr:last-child td{border-bottom:0}.hb-status{display:inline-flex;align-items:center;gap:5px;padding:5px 11px;border-radius:20px;background:#eaf7ed;color:#297b45;font-size:11px}.hb-status:before{content:'';width:6px;height:6px;border-radius:50%;background:currentColor}.hb-status.inactive{background:#f3f0f0;color:#767073}
.hb-modal{position:fixed;inset:0;z-index:250;background:#18090a91;display:flex;align-items:center;justify-content:center;padding:18px}.hb-modal[hidden]{display:none}.hb-dialog{background:white;border-radius:17px;width:min(760px,100%);max-height:94vh;overflow:auto;box-shadow:0 20px 80px #0003}.hb-dialog-head{position:sticky;top:0;z-index:4;background:white;display:flex;justify-content:space-between;align-items:start;gap:20px;padding:23px 27px;border-bottom:1px solid #f0e5e1}.hb-dialog-head h2{font-size:20px;margin:0}.hb-dialog-head p{color:#908581;font-size:12px;margin:5px 0 0}.hb-close{border:0;border-radius:50%;width:34px;height:34px;background:#f6efec;font-size:20px;color:#745f59;cursor:pointer}.hb-form{padding:22px 27px 26px}.hb-section{font-size:13px;color:#66131a;margin:6px 0 15px;padding-bottom:8px;border-bottom:1px solid #f1e9e4}.hb-fields{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:15px;margin-bottom:20px}.hb-field{display:flex;flex-direction:column;gap:6px;min-width:0;position:relative}.hb-field.wide{grid-column:1/-1}.hb-field label{font-size:12px;font-weight:600}.hb-field label span{color:#d03d3d}.hb-field input{width:100%;height:42px;background:#fff;border:1px solid #ded5d1;border-radius:8px;padding:0 12px;font-size:12px;outline:none}.hb-field input:focus{border-color:#a9a9a9;box-shadow:0 0 0 3px rgba(120,120,120,.10)}.hb-field input:disabled{background:#f8f6f5}.hb-field .hb-hint{font-size:10px;color:#9a8e88}.hb-options{position:absolute;z-index:20;top:100%;left:0;right:0;max-height:190px;overflow:auto;background:#fff;box-shadow:0 12px 26px #0002;border:1px solid #e4d4cc;border-radius:8px;margin-top:3px}.hb-options[hidden]{display:none}.hb-option{display:block;width:100%;padding:10px 12px;background:white;border:0;text-align:left;font-size:12px;cursor:pointer}.hb-option:hover,.hb-option:focus{background:#fff0ec}.hb-error{color:#b42323;font-size:11px;min-height:0}.hb-actions{border-top:1px solid #f0e5e1;padding-top:18px;display:flex;gap:10px;justify-content:flex-end}.hb-message{padding:11px 15px;border-radius:9px;background:#fff6f1;color:#8b4b30;font-size:12px;margin-bottom:16px}.hb-message[hidden]{display:none}.hb-success{background:#edf8f0;color:#28653a}.hb-preview{padding:12px;background:#fff6f4;border-radius:9px;border:1px solid #f1dad4;font-size:12px;margin-bottom:15px}.hb-preview[hidden]{display:none}.hb-preview code{font-family:monospace;word-break:break-all}.hb-password-actions{display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-top:8px}.hb-password-actions button{border:1px solid #dec7c1;border-radius:7px;background:white;padding:6px 10px;font-size:11px}.hb-danger{color:#9b2f2f}
@media(max-width:700px){.lg-pagehead{align-items:flex-start;flex-direction:column}.hb-dialog-head{padding:19px}.hb-form{padding:19px}.hb-fields{grid-template-columns:1fr}.hb-field.wide{grid-column:auto}.hb-toolbar{align-items:flex-start}.hb-toolbar input{max-width:75%}}
/* Neutral and clearly non-editable generated credential */
#hbGeneratedPassword{background:#f1f2f4!important;border:1px solid #d5d7db!important;color:#70757d!important;cursor:default;max-width:330px;font-family:ui-monospace,SFMono-Regular,Consolas,monospace;font-size:12px;letter-spacing:.35px}
#hbGeneratedPassword:focus{border-color:#adb1b8!important;box-shadow:0 0 0 3px rgba(120,120,120,.10)!important;outline:none}
.hb-password-actions button:focus-visible{outline:2px solid #9ca3af;outline-offset:2px}

/* Sorting center / delivery hub details */
.hb-open-row{cursor:pointer}.hb-open-row:hover td,.hb-open-row:focus-visible td{background:#fff5f1}.hb-open-row:focus-visible{outline:2px solid #9c8080;outline-offset:-2px}
.hb-details-dialog{width:min(920px,100%)}.hb-tab-bar{display:flex;gap:7px;padding:14px 22px 0;border-bottom:1px solid #eee3df;flex-wrap:wrap}.hb-tab{border:0;border-radius:8px 8px 0 0;background:transparent;color:#82726e;font-size:12px;font-weight:600;padding:11px 16px}.hb-tab.active{background:#fce9e5;color:#681b1b}.hb-subtabs{padding:0 0 0;margin-bottom:18px}.hb-detail-section{padding:20px 25px 28px}.hb-info-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}.hb-info-item{padding:3px 0 9px;background:transparent;border:0;border-radius:0;min-width:0}.hb-info-item small{display:block;color:#958b87;font-size:12px;margin-bottom:5px}.hb-info-item strong{display:block;font-size:14px;font-weight:500;line-height:1.55;word-break:break-word}.hb-hubs-head{display:flex;align-items:center;justify-content:space-between;gap:14px;margin-bottom:19px}.hb-hubs-head h3{font-size:16px;margin:0 0 4px}.hb-hubs-head p{margin:0;font-size:11px;color:#918681}.hb-empty{padding:10px 0 19px;font-size:13px}.hb-empty p{color:#928581;font-size:11px}.hb-empty-row{text-align:center;color:#9c9390;padding:30px!important}.hb-readonly,#hubPassword{background:#f1f2f4!important;border:1px solid #d5d7db!important;color:#70757d!important;cursor:default;max-width:330px}.hb-readonly:focus,#hubPassword:focus{border-color:#aab0b7!important;box-shadow:0 0 0 3px #88888816!important}
@media(max-width:700px){.hb-info-grid{grid-template-columns:1fr}.hb-hubs-head{align-items:flex-start;flex-direction:column}.hb-tab-bar{padding-left:10px}.hb-detail-section{padding:14px}}

/* Inner information tabs: flat underline navigation, not primary tab pills. */
.hb-subtabs{gap:28px;padding:0;margin:0 0 24px;border-bottom:1px solid #e7e1df}
.hb-subtabs .hb-tab{position:relative;border-radius:0;background:transparent!important;color:#958f8c;padding:12px 1px 14px;font-size:13px;font-weight:600}
.hb-subtabs .hb-tab.active{color:#7b1b1b;background:transparent!important}
.hb-subtabs .hb-tab.active:after{content:"";height:3px;background:#7b1b1b;border-radius:4px 4px 0 0;position:absolute;bottom:-1px;left:0;right:0}
.hb-subtabs .hb-tab:hover{color:#7b1b1b}
.hb-info-grid{gap:22px 34px;padding:4px 0 10px}
/* Ensure the information grid is fully hidden when the Vehicles subtab is selected. */
#hbInfoDetails[hidden], #hbInfoVehicles[hidden], #hbDetailsInfo[hidden], #hbDetailsHubs[hidden] { display:none !important; }
/* Vehicles: image and basic details only; cards are not clickable. */
.hb-vehicles-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:17px;padding:5px 0}
.hb-vehicle-card{display:flex;gap:15px;align-items:center;border:1px solid #eee5e1;background:#fff;border-radius:12px;padding:15px;min-width:0}
.hb-vehicle-photo{width:140px;height:112px;flex:0 0 140px;border-radius:9px;object-fit:cover;background:#f6f2f0}
.hb-vehicle-copy{min-width:0}.hb-vehicle-copy h4{margin:0 0 9px;font-size:14px;font-weight:600;color:#342a28}
.hb-vehicle-meta{display:grid;grid-template-columns:1fr;gap:5px;font-size:11px;color:#807874}
.hb-vehicle-meta b{font-weight:500;color:#302827;margin-left:6px}
.hb-vehicle-empty{grid-column:1/-1;display:flex;align-items:center;gap:17px;padding:25px;background:#fbf8f7;border:1px dashed #e5d9d5;border-radius:12px;color:#827873}
.hb-vehicle-empty svg{width:58px;height:58px;flex:0 0 58px;color:#ad9690}
.hb-vehicle-empty strong{font-size:13px;color:#4e4140}.hb-vehicle-empty p{font-size:12px;margin:5px 0 0}
@media(max-width:790px){.hb-vehicles-grid{grid-template-columns:1fr}}
@media(max-width:460px){.hb-vehicle-card{align-items:flex-start;flex-direction:column}.hb-vehicle-photo{width:100%;height:165px;flex:auto}.hb-subtabs{gap:20px}}

/* Profile-style details: clean label/value pairs instead of boxes */
.hb-profile-layout{display:grid;grid-template-columns:215px minmax(0,1fr);gap:30px;align-items:start}
.hb-profile-summary{display:flex;align-items:flex-start;flex-direction:column;gap:7px;min-width:0;padding-right:20px;border-right:1px solid #eee5e1}
.hb-profile-avatar{width:64px;height:64px;background:#f2e5e2;border-radius:50%;display:grid;place-items:center;color:#751b1b;font-weight:700;font-size:21px;margin-bottom:7px}
.hb-profile-summary>strong{font-size:14px;overflow-wrap:anywhere}.hb-profile-summary>small{font-size:11px;color:#958b87}.hb-profile-contact{display:grid;gap:5px;width:100%;margin-top:17px;overflow-wrap:anywhere}
.hb-profile-contact span{font-size:10px;color:#988e8a}.hb-profile-contact b{font-size:11px;font-weight:500;margin-bottom:9px}
.hb-profile-content{min-width:0}.hb-profile-section{margin-bottom:25px}.hb-profile-section h3{color:#902424;font-size:13px;font-weight:650;padding:0 0 11px;border-bottom:1px solid #eee7e3;margin:0 0 16px}
.hb-profile-section h3 span{margin-right:5px}.hb-profile-list{display:grid;grid-template-columns:1fr 1fr;gap:13px 23px}.hb-profile-row{min-width:0;display:grid;gap:4px;align-content:start}
.hb-profile-row>span{font-size:11px;color:#99908c}.hb-profile-row>b{font-size:12px;line-height:1.6;font-weight:500;overflow-wrap:anywhere}.hb-profile-row .hb-status{font-weight:500}
.hb-status{display:inline-flex;align-items:center;gap:5px;padding:5px 11px;border-radius:100px;background:#e9f7ed;color:#2e7d46;font-size:11px!important;width:max-content;font-weight:550!important}
.hb-status.inactive{background:#f2eded;color:#776e70}.hb-status:before{content:'';height:6px;width:6px;border-radius:100%;background:currentColor}
.hb-vehicle-meta .hb-status{margin-left:6px}
@media(max-width:720px){.hb-profile-layout{grid-template-columns:1fr;gap:18px}.hb-profile-summary{border-right:0;border-bottom:1px solid #eee5e1;padding:0 0 15px}.hb-profile-list{grid-template-columns:1fr 1fr}}
@media(max-width:430px){.hb-profile-list{grid-template-columns:1fr}}

/* FIX: The profile is one full-width layout, not a cell in the legacy two-column info grid. */
#hbInfoDetails:not([hidden]) { display:block !important; width:100%; min-width:0; padding:0; }
#hbInfoDetails > .hb-profile-layout { width:100%; min-width:0; }
.hb-details-dialog { width:min(1080px, calc(100vw - 40px)); max-width:1080px; }
.hb-details-dialog .hb-detail-section { padding:24px 32px 34px; min-width:0; }
.hb-details-dialog .hb-profile-layout { grid-template-columns:minmax(215px,255px) minmax(0,1fr); gap:34px; }
.hb-details-dialog .hb-profile-summary { padding-right:28px; }
.hb-details-dialog .hb-profile-content { width:100%; min-width:0; }
.hb-details-dialog .hb-profile-list { grid-template-columns:repeat(2,minmax(0,1fr)); column-gap:36px; row-gap:18px; }
.hb-details-dialog .hb-profile-row { min-width:0; }
.hb-details-dialog .hb-profile-row>b { overflow-wrap:break-word; word-break:normal; }
@media(max-width:850px){
  .hb-details-dialog .hb-profile-layout { grid-template-columns:minmax(175px,210px) minmax(0,1fr); gap:22px; }
  .hb-details-dialog .hb-detail-section { padding:20px 23px 26px; }
  .hb-details-dialog .hb-profile-list { column-gap:20px; }
}
@media(max-width:640px){
  .hb-details-dialog { width:calc(100vw - 20px); }
  .hb-details-dialog .hb-profile-layout { grid-template-columns:minmax(0,1fr); gap:18px; }
  .hb-details-dialog .hb-profile-summary { border-right:0; border-bottom:1px solid #eee5e1; padding:0 0 18px; }
  .hb-details-dialog .hb-profile-list { grid-template-columns:repeat(2,minmax(0,1fr)); }
}
@media(max-width:430px){.hb-details-dialog .hb-profile-list {grid-template-columns:minmax(0,1fr)} }

/* Sorting-center actions and reversible archive */
.hb-detail-actions{display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin:20px 26px 0;padding:16px 0 20px;border-top:1px solid #f0e5e1;justify-content:flex-end}
.hb-action{border:1px solid #e8d9d4;background:#fff;border-radius:8px;color:#6c5250;font-size:12px;font-weight:600;padding:9px 16px;cursor:pointer}
.hb-action:hover{background:#fff5f1}.hb-action.primary{background:#7b1b1b;color:#fff;border-color:#7b1b1b}.hb-action.danger{color:#a43a3a;border-color:#eccaca}.hb-action.danger:hover{background:#fff0ef}
.hb-archive-fab{position:fixed;bottom:25px;right:25px;z-index:65;width:50px;height:50px;border-radius:50%;border:1px solid #eed7d2;background:#fff;color:#7b1b1b;box-shadow:0 8px 26px #52070b20;display:grid;place-items:center;cursor:pointer}
.hb-hub-header-actions{display:flex;align-items:center;justify-content:flex-end;gap:10px;flex-wrap:wrap;margin-left:auto}
.hb-archive-fab.hb-hub-fab{position:relative;bottom:auto;right:auto;z-index:auto;width:39px;height:39px;flex:none}.hb-hubs-head{gap:12px;flex-wrap:wrap}.hb-hub-fab svg{width:18px;height:18px}.hb-archive-fab:hover{background:#fff2ef;transform:translateY(-2px)}.hb-archive-fab svg{height:21px;width:21px}.hb-archive-count{position:absolute;top:-5px;right:-5px;background:#7b1b1b;color:white;border-radius:12px;font-size:10px;min-width:20px;padding:3px 5px;line-height:14px}
.hb-archive-list{padding:18px 25px 26px;display:grid;gap:10px}.hb-archive-item{border:1px solid #eee4df;border-radius:11px;padding:13px 15px;display:flex;align-items:center;justify-content:space-between;gap:15px}.hb-archive-item strong{font-size:13px}.hb-archive-item small{display:block;color:#998c85;margin-top:4px;font-size:11px}.hb-archive-empty{text-align:center;color:#948984;font-size:13px;padding:30px 10px}.hb-archive-note{font-size:11px;color:#8d807b;padding:0 25px 20px}.hb-edit-fields{padding:20px 26px 24px}.hb-edit-fields .hb-fields{margin-top:12px}.hb-edit-fields .hb-section{margin-top:20px}
@media(max-width:650px){.hb-detail-actions{margin:16px 16px 0;padding:14px 0 16px}.hb-archive-item{align-items:flex-start;flex-direction:column}.hb-archive-fab{right:14px;bottom:14px}}

/* Shared Admin/Seller-style action flash: lower-right card */
.hb-flash-stack{position:fixed;right:24px;bottom:24px;top:auto;z-index:10000;width:min(370px,calc(100vw - 32px));pointer-events:none}
.hb-flash{box-sizing:border-box;pointer-events:auto;display:flex;align-items:flex-start;gap:11px;min-height:72px;background:#fff;border:1px solid #BFDDB8;border-radius:14px;padding:13px 14px;box-shadow:0 14px 34px rgba(42,20,15,.16);color:#111827;opacity:1;animation:hb-flash-in .16s ease-out;font-family:Poppins,sans-serif}
.hb-flash.is-error{border-color:#E7A8AB}.hb-flash.is-info{border-color:#E7E2DF}
.hb-flash-icon{width:36px;height:36px;flex:0 0 36px;border-radius:9999px;display:flex;align-items:center;justify-content:center;background:#DDF0D6;color:#28721B}
.hb-flash.is-error .hb-flash-icon{background:#FFE3E5;color:#B3262E}
.hb-flash.is-info .hb-flash-icon{background:#F3F4F6;color:#6B7280}
.hb-flash-icon svg{width:20px;height:20px}
.hb-flash-copy{flex:1;min-width:0;overflow-wrap:anywhere}
.hb-flash-title{font-family:Poppins,sans-serif;font-size:14px;line-height:1.3;font-weight:600;color:#111827;margin:0}
.hb-flash-text{font-family:Poppins,sans-serif;font-size:12px;line-height:20px;color:#6B7280;margin:4px 0 0;overflow-wrap:anywhere}
.hb-flash-close{width:28px;height:28px;flex:0 0 28px;margin-left:auto;display:flex;align-items:center;justify-content:center;border:0;border-radius:9999px;background:transparent;color:#9CA3AF;cursor:pointer}
.hb-flash-close:hover{background:#F3F4F6;color:#57514E}.hb-flash-close svg{width:16px;height:16px}
@keyframes hb-flash-in{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
@media(max-width:640px){.hb-flash-stack{right:16px;bottom:16px}}
@media(prefers-reduced-motion:reduce){.hb-flash{animation:none}}

/* Direct message actions in Sorting Center and Delivery Hub details */
.hb-dialog-head-actions{display:flex;align-items:center;gap:9px;flex-shrink:0}
.hb-message-link{width:36px;height:36px;display:inline-flex;align-items:center;justify-content:center;border:1px solid #ecd8d1;background:#fff8f5;border-radius:10px;transition:background .25s ease,transform .25s ease,border-color .25s ease}
.hb-message-link:hover{background:#ffeae1;border-color:#d7b2aa;transform:translateY(-2px)}
.hb-message-link img{display:block;width:19px;height:19px;object-fit:contain}
.hb-message-link:focus-visible{outline:2px solid #7b1b1b;outline-offset:3px}
/* Message shortcut is aligned immediately beside the facility name, not the close button. */
.hb-title-with-message{display:flex;align-items:center;gap:11px;min-width:0;flex-wrap:wrap}
.hb-title-with-message h2{min-width:0;max-width:calc(100% - 48px);overflow-wrap:anywhere}
.hb-title-with-message .hb-message-link{width:32px;height:32px;flex:0 0 32px;border-radius:9px}
.hb-title-with-message .hb-message-link img{width:17px;height:17px}

/* Plain send.png shortcut beside the actual facility name (not the modal title). */
.hb-profile-name-row{display:flex;align-items:center;gap:8px;max-width:100%;flex-wrap:wrap}
.hb-profile-name-row>strong{font-size:14px;line-height:1.5;overflow-wrap:anywhere;min-width:0}
.hb-profile-message{display:inline-flex;align-items:center;justify-content:center;flex:0 0 auto;padding:2px!important;width:auto!important;height:auto!important;min-width:22px;min-height:22px;border:0!important;background:transparent!important;border-radius:0!important;box-shadow:none!important;text-decoration:none;transition:opacity .2s ease,transform .2s ease}
.hb-profile-message:hover{opacity:.72;transform:translateY(-1px);background:transparent!important}
.hb-profile-message img{display:block;width:19px;height:19px;object-fit:contain}
.hb-profile-message:focus-visible{outline:2px solid #75171f;outline-offset:2px}

/* Sorting Center is view-only: keep modal design, remove mutation controls. */
.sc-summary{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:13px;margin:0 0 19px}
.sc-summary-card{background:#fff;border:1px solid var(--lg-line);border-radius:13px;padding:17px 19px;display:flex;align-items:center;justify-content:space-between;gap:10px}
.sc-summary-card small{display:block;color:var(--lg-muted);font-size:11px;margin-bottom:5px}.sc-summary-card strong{font-size:23px;font-weight:650}
.sc-readonly{display:inline-flex;align-items:center;gap:6px;background:#fff6f1;border:1px solid #f3e1d8;border-radius:99px;padding:7px 12px;font-size:11px;color:#8a5e50;font-weight:600}
.sc-hub-table .hb-table{min-width:860px}.sc-hub-table .hb-table tr[data-hub-id]{cursor:pointer}.sc-hub-table .hb-table tr[data-hub-id]:hover td{background:#fff7f3}
.sc-detail-note{padding:12px 25px 0;font-size:11px;color:#918681}
.sc-muted-empty{padding:44px 16px;text-align:center;color:#948984;font-size:12px}
.sc-archive-filter{font-size:12px;color:#766c67}
@media(max-width:780px){.sc-summary{grid-template-columns:1fr 1fr}.sc-summary-card{padding:14px}.sc-summary-card strong{font-size:21px}}
@media(max-width:520px){.sc-summary{grid-template-columns:1fr}.hb-toolbar{flex-wrap:wrap}.hb-toolbar input{max-width:100%;width:100%}}
</style>
</head>
<body class="lg-body">
@include('components.logistics.sorting-center.sidebar')
@include('components.logistics.sorting-center.navbar')
<main class="lg-main" id="lgMain"><div class="lg-page-wrap">
  <div class="hb-filters" role="group" aria-label="Filter delivery hubs"><button type="button" class="hb-pill active" data-sc-filter="all">All <small id="scCountAll">0</small></button><button type="button" class="hb-pill" data-sc-filter="active">Active <small id="scCountActive">0</small></button><button type="button" class="hb-pill" data-sc-filter="inactive">Inactive <small id="scCountInactive">0</small></button></div>
  <div class="hb-toolbar"><input id="scSearch" type="search" aria-label="Search delivery hubs" placeholder="⌕  Search hub, municipality or manager"><span id="scResultCount" class="hb-count">0 hubs</span></div>
  <section class="hb-panel sc-hub-table" aria-label="Delivery hubs list"><table class="hb-table"><thead><tr><th>Delivery Hub</th><th>Municipality</th><th>Manager</th><th>Riders</th><th>Couriers (Truck Drivers)</th><th>Vehicles</th><th>Status</th></tr></thead><tbody id="scHubRows"><tr><td colspan="7" class="hb-empty-row">No delivery hubs available.</td></tr></tbody></table></section>
  <p class="lg-help" id="scDataNote" style="margin-top:13px">Hubs are managed by your logistics company. You can view their details here but cannot create, edit, archive, or delete them.</p>
</div></main>
<div class="hb-modal" id="scHubModal" hidden><section class="hb-dialog hb-details-dialog" role="dialog" aria-modal="true" aria-labelledby="scHubTitle"><div class="hb-dialog-head"><div><h2 id="scHubTitle">Delivery Hub</h2><p id="scHubSubtitle"></p></div><button type="button" class="hb-close" id="scHubClose" aria-label="Close">×</button></div><div class="hb-detail-section" id="scHubProfile"></div><div class="hb-detail-actions"><button type="button" class="hb-action" id="scHubCloseAction">Close</button></div></section></div>
<script>
document.addEventListener('DOMContentLoaded', () => {
  'use strict';
  const $ = id => document.getElementById(id);
  const KEY = 'shopease-logistics-network-hubs-ui-v1';
  const centerId = @json($sortingCenterId);
  const serverHubs = @json($deliveryHubs);
  const esc = value => String(value ?? '').replace(/[&<>"']/g, ch => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[ch]));
  const count = value => {const n=Number(value);return Number.isFinite(n)&&n>=0?Math.floor(n):0;};
  const status = hub => String(hub.status || 'active').toLowerCase() === 'inactive' ? 'inactive' : 'active';
  const pill = value => `<span class="hb-status ${value === 'inactive' ? 'inactive' : ''}">${value === 'inactive' ? 'Inactive' : 'Active'}</span>`;
  let filter='all', hubs=[], selected=null;

  function loadHubs(){
    // With backend data, use ONLY the hubs authorized and supplied by the controller.
    if(Array.isArray(serverHubs)){
      hubs=serverHubs.map(h=>({...h,center_name:h.center_name||h.sorting_center_name||'',province:h.province||''}));
      $('scDataNote').textContent='Delivery hubs assigned to your sorting center. Only your logistics company can manage hub records.';
      return;
    }
    // Browser prototype: read the SAME storage as Logistics > Network Hubs.
    // Until a sorting center identity is supplied, don't expose every company's hubs.
    let centers=[];
    try{const data=JSON.parse(localStorage.getItem(KEY)||'[]'); if(Array.isArray(data))centers=data;}catch{}
    let permitted=[];
    if(centerId!==null && centerId!==undefined && String(centerId).trim()!==''){
      permitted=centers.filter(c=>String(c.id)===String(centerId));
    } else if(centers.length===1){
      // Single-center demo only. Real access must be enforced by the backend.
      permitted=centers;
    }
    hubs=permitted.flatMap(c=>(Array.isArray(c.hubs)?c.hubs:[]).map(h=>({...h,center_name:c.branch_name||'',province:c.province||'',center_id:c.id})));
    $('scDataNote').textContent=permitted.length?'Viewing browser-saved delivery hubs. Only the logistics company can add or edit hubs.':'No sorting center is linked yet. Once the logistics company assigns hubs to your center, they will appear here.';
  }
  function profileSection(title, symbol, pairs){
    return `<section class="hb-profile-section"><h3><span>${symbol}</span> ${esc(title)}</h3><div class="hb-profile-list">${pairs.map(([label,value])=>`<div class="hb-profile-row"><span>${esc(label)}</span><b>${esc(value === null || value === undefined || value === '' ? '—' : value)}</b></div>`).join('')}</div></section>`;
  }
  function render(){
    const q=$('scSearch').value.trim().toLocaleLowerCase();
    const shown=hubs.filter(h=>(filter==='all'||status(h)===filter)&&[h.hub_name,h.name,h.city,h.municipality,h.manager_name,h.manager_email,h.province].join(' ').toLocaleLowerCase().includes(q));
    $('scCountAll').textContent=hubs.length;
    $('scCountActive').textContent=hubs.filter(h=>status(h)==='active').length;
    $('scCountInactive').textContent=hubs.filter(h=>status(h)==='inactive').length;
    $('scResultCount').textContent=`${shown.length} of ${hubs.length} hubs`;
    $('scHubRows').innerHTML=shown.map((h)=>`<tr data-hub-id="${esc(h.id)}" class="hb-open-row" tabindex="0" role="button" aria-label="View ${esc(h.hub_name||h.name||'delivery hub')}"><td><strong>${esc(h.hub_name||h.name||'Unnamed delivery hub')}</strong><small>${esc([h.street,h.barangay].filter(Boolean).join(', '))}</small></td><td>${esc(h.city||h.municipality||'—')}</td><td><strong>${esc(h.manager_name||'—')}</strong><small>${esc(h.manager_email||'')}</small></td><td>${count(h.riders_count)}</td><td>${count(h.couriers_count??h.drivers_count)}</td><td>${Array.isArray(h.vehicles)?h.vehicles.length:count(h.vehicles_count??h.vehicle_count)}</td><td>${pill(status(h))}</td></tr>`).join('')||'<tr><td class="hb-empty-row" colspan="7">No delivery hubs match your search.</td></tr>';
  }
  function openDetails(id){
    const h=hubs.find(x=>String(x.id)===String(id));if(!h)return;selected=h;
    $('scHubTitle').textContent=h.hub_name||h.name||'Delivery Hub';
    $('scHubSubtitle').textContent=[h.city||h.municipality,h.province].filter(Boolean).join(', ');
    $('scHubProfile').innerHTML=`<div class="hb-profile-layout"><aside class="hb-profile-summary"><div class="hb-profile-avatar" aria-hidden="true">DH</div><div class="hb-profile-name-row"><strong>${esc(h.hub_name||h.name||'Delivery Hub')}</strong></div><small>${esc([h.city||h.municipality,h.province].filter(Boolean).join(' · '))}</small>${pill(status(h))}<div class="hb-profile-contact"><span>Hub login</span><b>${esc(h.login_email||'—')}</b><span>Parent center</span><b>${esc(h.center_name||'—')}</b></div></aside><div class="hb-profile-content">${profileSection('Delivery Hub Information','▣',[['Hub name',h.hub_name||h.name],['Parent sorting center',h.center_name],['Municipality',h.city||h.municipality],['Riders',String(count(h.riders_count))],['Couriers (Truck Drivers)',String(count(h.couriers_count??h.drivers_count))],['Login email',h.login_email]])}${profileSection('Office Address','⌖',[['Province',h.province],['Municipality',h.city||h.municipality],['Barangay',h.barangay],['Street',h.street],['House / Unit',h.house_no],['Building',h.building],['Subdivision',h.subdivision],['ZIP Code',h.postal_code]])}${profileSection('Manager Details','♟',[['Manager name',h.manager_name],['Manager email',h.manager_email],['Phone number',h.manager_phone]])}</div></div>`;
    $('scHubModal').hidden=false;document.body.classList.add('lg-modal-open');$('scHubClose').focus();
  }
  function closeDetails(){ $('scHubModal').hidden=true;document.body.classList.remove('lg-modal-open'); }
  $('scHubRows').addEventListener('click',event=>{const row=event.target.closest('[data-hub-id]');if(row)openDetails(row.dataset.hubId)});
  $('scHubRows').addEventListener('keydown',event=>{const row=event.target.closest('[data-hub-id]');if(row&&(event.key==='Enter'||event.key===' ')){event.preventDefault();openDetails(row.dataset.hubId)}});
  $('scHubClose').addEventListener('click',closeDetails);
  $('scHubCloseAction').addEventListener('click',closeDetails);
  $('scHubModal').addEventListener('mousedown',event=>{if(event.target===$('scHubModal'))closeDetails()});
  document.addEventListener('keydown',event=>{if(event.key==='Escape'&&!$('scHubModal').hidden)closeDetails()});
  $('scSearch').addEventListener('input',render);
  document.querySelectorAll('[data-sc-filter]').forEach(btn=>btn.addEventListener('click',()=>{filter=btn.dataset.scFilter;document.querySelectorAll('[data-sc-filter]').forEach(x=>x.classList.toggle('active',x===btn));render()}));
  window.addEventListener('storage',e=>{if(e.key===KEY){loadHubs();render()}});
  window.addEventListener('pageshow',()=>{loadHubs();render()});
  loadHubs();render();
});
</script>
</body>
</html>
