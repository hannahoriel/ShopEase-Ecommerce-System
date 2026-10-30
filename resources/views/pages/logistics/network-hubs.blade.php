@php($pageName = 'Network Hubs')
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}"><title>Network Hubs — ShopEase</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
</style></head><body class="lg-body">
@include('components.logistics.sidebar')
@include('components.logistics.navbar')
<main class="lg-main" id="lgMain"><div class="lg-page-wrap">
<div class="lg-pagehead"><div><h1>Sorting Centers</h1><p>Manage your provincial sorting centers. Each province can have one main sorting center with its own office and manager.</p></div><button class="lg-btn lg-btn-primary" id="hbOpen">+ Create Sorting Center</button></div>
<div class="hb-filters" role="group" aria-label="Filter sorting centers"><button class="hb-pill active" data-filter="all">All <small id="hbCountAll">0</small></button><button class="hb-pill" data-filter="active">Active <small id="hbCountActive">0</small></button><button class="hb-pill" data-filter="inactive">Inactive <small id="hbCountInactive">0</small></button></div>
<div class="hb-toolbar"><input id="hbSearch" type="search" aria-label="Search sorting centers" placeholder="⌕  Search center, province or manager"><span id="hbResultCount" class="hb-count">0 centers</span></div>
<div class="hb-panel"><table class="hb-table"><thead><tr><th>Sorting Center</th><th>Province</th><th>Manager</th><th>Delivery Hubs</th><th>Drivers</th><th>Vehicles</th><th>Status</th></tr></thead><tbody id="hbRows"><tr><td colspan="7" style="text-align:center;color:#999;padding:35px">No sorting centers yet.</td></tr></tbody></table></div>
</div></main>
<div class="hb-modal" id="hbModal" hidden><section class="hb-dialog" role="dialog" aria-modal="true" aria-labelledby="hbDialogTitle"><div class="hb-dialog-head"><div><h2 id="hbDialogTitle">Create Sorting Center</h2><p>Set up the sorting center's office address, manager and login details.</p></div><button class="hb-close" type="button" id="hbClose" aria-label="Close">×</button></div>
<form class="hb-form" id="hbForm" novalidate><div id="hbFormMessage" class="hb-message" hidden></div>
<h3 class="hb-section">Sorting Center Information</h3><div class="hb-fields">
<div class="hb-field"><label for="hbName">Branch / Sorting Center Name <span>*</span></label><input id="hbName" name="branch_name" required maxlength="120" placeholder="e.g. Laguna Main Sorting Center"></div>
<div class="hb-field"><label for="hbProvinceSearch">Province <span>*</span></label><input id="hbProvinceSearch" autocomplete="off" placeholder="Search province..." role="combobox" aria-autocomplete="list" aria-expanded="false" required><input type="hidden" name="province" id="hbProvince"><div class="hb-options" id="hbProvinceOptions" hidden></div><small class="hb-hint">Select a valid province from the search results.</small></div></div>
<h3 class="hb-section">Office Address</h3><div class="hb-fields">
<div class="hb-field"><label for="hbCitySearch">City / Municipality <span>*</span></label><input id="hbCitySearch" autocomplete="off" placeholder="Select province first" role="combobox" aria-autocomplete="list" aria-expanded="false" disabled required><input type="hidden" name="city" id="hbCity"><div class="hb-options" id="hbCityOptions" hidden></div></div>
<div class="hb-field"><label for="hbBarangaySearch">Barangay <span>*</span></label><input id="hbBarangaySearch" autocomplete="off" placeholder="Select city first" role="combobox" aria-autocomplete="list" aria-expanded="false" disabled required><input type="hidden" name="barangay" id="hbBarangay"><div class="hb-options" id="hbBarangayOptions" hidden></div></div>
<div class="hb-field"><label for="hbZip">ZIP / Postal Code <span>*</span></label><input id="hbZip" name="postal_code" required inputmode="numeric" maxlength="4" pattern="[0-9]{4}" placeholder="4009"></div>
<div class="hb-field"><label for="hbHouse">House / Unit / Lot No. (Optional)</label><input id="hbHouse" name="house_no" maxlength="80" placeholder="House / lot number"></div>
<div class="hb-field"><label for="hbStreet">Street <span>*</span></label><input id="hbStreet" name="street" maxlength="150" required placeholder="Street name"></div>
<div class="hb-field"><label for="hbBuilding">Building / Floor (Optional)</label><input id="hbBuilding" name="building" maxlength="150" placeholder="Building / floor"></div>
<div class="hb-field wide"><label for="hbSubdivision">Subdivision / Village (Optional)</label><input id="hbSubdivision" name="subdivision" maxlength="150" placeholder="Subdivision / village"></div></div>
<h3 class="hb-section">Manager Details</h3><div class="hb-fields"><div class="hb-field"><label for="hbManager">Manager Name <span>*</span></label><input id="hbManager" name="manager_name" required maxlength="120" placeholder="Full name"></div><div class="hb-field"><label for="hbManagerEmail">Manager Email <span>*</span></label><input id="hbManagerEmail" name="manager_email" type="email" required placeholder="manager@example.com"></div><div class="hb-field"><label for="hbManagerPhone">Manager Phone <span>*</span></label><input id="hbManagerPhone" name="manager_phone" type="tel" required pattern="(09[0-9]{9}|\+639[0-9]{9})" placeholder="09XXXXXXXXX"></div></div>
<h3 class="hb-section">Sorting Center Login Credentials</h3><div class="hb-fields"><div class="hb-field wide"><label for="hbLoginEmail">Branch Login Email <span>*</span></label><input id="hbLoginEmail" name="login_email" type="email" autocomplete="off" required placeholder="branch@example.com"><small class="hb-hint">Contact email is saved with the center. This form does not create a separate branch login.</small></div>
<div class="hb-field wide" id="hbPasswordInfo"><label>Auto-generated Password</label><small class="hb-hint">A secure login password will be generated when this sorting center is created and sent to the Branch Login Email above.</small></div></div>
<div class="hb-actions"><button type="button" class="lg-btn lg-btn-outline" id="hbCancel">Cancel</button><button type="submit" class="lg-btn lg-btn-primary">Create Sorting Center</button></div></form></section></div>

<div class="hb-modal" id="hbDetailsModal" hidden>
 <section class="hb-dialog hb-details-dialog" role="dialog" aria-modal="true" aria-labelledby="hbDetailsTitle">
  <div class="hb-dialog-head"><div><h2 id="hbDetailsTitle">Sorting Center</h2><p id="hbDetailsSubtitle">View sorting center details and associated delivery hubs.</p></div><div class="hb-dialog-head-actions"><button type="button" class="hb-close" id="hbDetailsClose" aria-label="Close">×</button></div></div>
  <div class="hb-tab-bar" role="tablist" aria-label="Sorting center sections"><button type="button" class="hb-tab active" data-detail-tab="info">Sorting Center Info</button><button type="button" class="hb-tab" data-detail-tab="hubs">Delivery Hubs</button></div>
  <div id="hbDetailsInfo" class="hb-detail-section">
   <div class="hb-tab-bar hb-subtabs" role="tablist" aria-label="Sorting center information"><button type="button" class="hb-tab active" data-info-tab="details">Information</button><button type="button" class="hb-tab" data-info-tab="vehicles">Vehicles</button></div>
   <div id="hbInfoDetails" class="hb-info-grid"></div>
   <div id="hbInfoVehicles" hidden><p class="hb-hint" style="margin:0 0 15px;color:#988e8a;font-size:11px">Registered vehicles assigned to this sorting center.</p><div class="hb-vehicles-grid" id="hbVehiclesGrid" aria-live="polite"></div></div>
  </div>
  <div id="hbDetailsHubs" class="hb-detail-section" hidden><div class="hb-hubs-head"><div><h3>Delivery Hubs</h3><p>Municipal delivery hubs under this sorting center.</p></div><div class="hb-hub-header-actions"><button type="button" class="lg-btn lg-btn-primary" id="hbAddHubOpen">+ Add Delivery Hub</button><button type="button" class="hb-archive-fab hb-hub-fab" id="hbHubArchiveOpen" title="Archived delivery hubs" aria-label="Open delivery hub archive"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 5h18M6 5l1 15h10l1-15M9 5V3h6v2M10 10v6M14 10v6"/></svg><span class="hb-archive-count" id="hbHubArchiveCount" hidden>0</span></button></div></div><div class="hb-panel"><table class="hb-table"><thead><tr><th>Delivery Hub</th><th>Municipality</th><th>Manager</th><th>Riders</th><th>Couriers (Truck Drivers)</th><th>Vehicles</th><th>Status</th></tr></thead><tbody id="hbHubRows"><tr><td class="hb-empty-row" colspan="7">No delivery hubs yet.</td></tr></tbody></table></div></div>
  <div class="hb-detail-actions"><button type="button" class="hb-action primary" id="hbEditCenter">Edit</button><button type="button" class="hb-action danger" id="hbArchiveCenter">Delete</button><button type="button" class="hb-action" id="hbDetailsCloseAction">Close</button></div>
 </section>
</div>
<div class="hb-modal" id="hbHubDetailsModal" hidden><section class="hb-dialog hb-details-dialog" role="dialog" aria-modal="true" aria-labelledby="hbHubDetailsTitle"><div class="hb-dialog-head"><div><h2 id="hbHubDetailsTitle">Delivery Hub</h2><p id="hbHubDetailsSubtitle"></p></div><div class="hb-dialog-head-actions"><button type="button" class="hb-close" id="hbHubDetailsClose" aria-label="Close">×</button></div></div><div class="hb-detail-section" id="hbHubProfile"></div><div class="hb-detail-actions"><button type="button" class="hb-action primary" id="hbEditHub">Edit</button><button type="button" class="hb-action danger" id="hbArchiveHub">Delete</button><button type="button" class="hb-action" id="hbHubCloseAction">Close</button></div></section></div>
<div class="hb-modal" id="hbAddHubModal" hidden>
 <section class="hb-dialog" role="dialog" aria-modal="true" aria-labelledby="hbAddHubTitle"><div class="hb-dialog-head"><div><h2 id="hbAddHubTitle">Add Delivery Hub</h2><p id="hbAddHubSubtitle">A hub under the selected sorting center.</p></div><button type="button" class="hb-close" id="hbAddHubClose" aria-label="Close">×</button></div>
 <form id="hbAddHubForm" class="hb-form" novalidate>
 <p id="hbAddHubError" class="hb-message" hidden role="alert"></p>
 <h3 class="hb-section">Delivery Hub Information</h3><div class="hb-fields">
 <div class="hb-field"><label for="hubName">Delivery Hub Name <span>*</span></label><input id="hubName" name="hub_name" required maxlength="120" placeholder="e.g. Pila Delivery Hub"></div>
 <div class="hb-field"><label for="hubCitySearch">City / Municipality <span>*</span></label><input id="hubCitySearch" role="combobox" aria-expanded="false" autocomplete="off" required placeholder="Search a municipality..."><input type="hidden" name="city" id="hubCity"><div id="hubCityOptions" class="hb-options" hidden></div><small class="hb-hint">Options limited to the parent sorting center's province.</small></div></div>
 <h3 class="hb-section">Office Address</h3><div class="hb-fields">
 <div class="hb-field"><label for="hubProvince">Province</label><input id="hubProvince" name="province" readonly class="hb-readonly"></div>
 <div class="hb-field"><label for="hubBarangaySearch">Barangay <span>*</span></label><input id="hubBarangaySearch" role="combobox" aria-expanded="false" autocomplete="off" required disabled placeholder="Select a municipality first"><input type="hidden" name="barangay" id="hubBarangay"><div id="hubBarangayOptions" class="hb-options" hidden></div></div>
 <div class="hb-field"><label for="hubZip">ZIP / Postal Code <span>*</span></label><input id="hubZip" name="postal_code" required inputmode="numeric" pattern="[0-9]{4}" maxlength="4" placeholder="4000"></div>
 <div class="hb-field"><label for="hubHouse">House / Unit / Lot No. (Optional)</label><input id="hubHouse" name="house_no" maxlength="80" placeholder="Unit / lot number"></div>
 <div class="hb-field"><label for="hubStreet">Street <span>*</span></label><input id="hubStreet" name="street" required maxlength="150" placeholder="Street name"></div>
 <div class="hb-field"><label for="hubBuilding">Building / Floor (Optional)</label><input id="hubBuilding" name="building" maxlength="150" placeholder="Building / floor"></div>
 <div class="hb-field wide"><label for="hubSubdivision">Subdivision / Village (Optional)</label><input id="hubSubdivision" name="subdivision" maxlength="150" placeholder="Subdivision / village"></div></div>
 <h3 class="hb-section">Manager Details</h3><div class="hb-fields"><div class="hb-field"><label for="hubManager">Manager Name <span>*</span></label><input id="hubManager" name="manager_name" required maxlength="120" placeholder="Full name"></div><div class="hb-field"><label for="hubManagerEmail">Manager Email <span>*</span></label><input id="hubManagerEmail" name="manager_email" type="email" required placeholder="manager@example.com"></div><div class="hb-field"><label for="hubManagerPhone">Manager Phone <span>*</span></label><input id="hubManagerPhone" name="manager_phone" required type="tel" pattern="(09[0-9]{9}|\+639[0-9]{9})" placeholder="09XXXXXXXXX"></div></div>
 <h3 class="hb-section">Delivery Hub Login Credentials</h3><div class="hb-fields"><div class="hb-field wide"><label for="hubLoginEmail">Hub Login Email <span>*</span></label><input id="hubLoginEmail" name="login_email" type="email" required autocomplete="off" placeholder="hub@example.com"></div><div class="hb-field wide" id="hubPasswordInfo"><label>Auto-generated Password</label><small class="hb-hint">A secure 8-character login password with lowercase and uppercase letters, a number, and a special character will be generated when this delivery hub is created and sent to the Hub Login Email above.</small></div></div>
 <div class="hb-actions"><button type="button" class="lg-btn lg-btn-outline" id="hubCancel">Cancel</button><button type="submit" class="lg-btn lg-btn-primary">Add Delivery Hub</button></div>
 </form></section>
</div>


<div class="hb-modal" id="hbHubArchiveModal" hidden><section class="hb-dialog" role="dialog" aria-modal="true" aria-labelledby="hbHubArchiveTitle"><div class="hb-dialog-head"><div><h2 id="hbHubArchiveTitle">Archived Delivery Hubs</h2><p>Restore delivery hubs previously removed from this sorting center.</p></div><button type="button" class="hb-close" id="hbHubArchiveClose" aria-label="Close archive">×</button></div><div class="hb-archive-list" id="hbHubArchiveList"></div></section></div>
<div class="hb-modal" id="hbHubDeleteReasonModal" hidden><section class="hb-dialog" role="dialog" aria-modal="true" aria-labelledby="hbHubDeleteReasonTitle" style="max-width:520px"><div class="hb-dialog-head"><div><h2 id="hbHubDeleteReasonTitle">Archive Delivery Hub</h2><p id="hbHubDeleteReasonSubtitle">Choose a reason before archiving this hub.</p></div><button type="button" class="hb-close" id="hbHubDeleteReasonX" aria-label="Close">×</button></div><div style="padding:22px 26px 12px"><label for="hbHubDeleteReason" style="font-size:12px;font-weight:600;display:block;margin-bottom:9px">Reason <span style="color:#bd232b">*</span></label><select id="hbHubDeleteReason" style="display:block;width:100%;height:42px;padding:0 12px;border:1px solid #d9cfcb;border-radius:9px;background:#fff;font:12px Poppins,sans-serif"><option value="">Select reason</option><option>Delivery hub no longer operational</option><option>Duplicate delivery hub record</option><option>Operations transferred to another facility</option><option>Incorrect hub registration details</option><option>Temporary suspension of hub operations</option><option value="Other">Other</option></select><label for="hbHubDeleteDetails" style="font-size:12px;display:block;margin-top:15px">Additional Details <span style="color:#999">(optional, required for Other)</span></label><textarea id="hbHubDeleteDetails" maxlength="500" rows="3" placeholder="Explain the reason…" style="width:100%;box-sizing:border-box;margin-top:7px;border:1px solid #d9cfcb;border-radius:9px;padding:11px;font:12px Poppins,sans-serif;resize:vertical"></textarea><div id="hbHubDeleteError" role="alert" style="min-height:20px;padding-top:6px;color:#bd232b;font-size:11px"></div></div><div class="hb-detail-actions" style="justify-content:flex-end;margin-top:0;padding:16px 26px"><button type="button" class="hb-action" id="hbHubDeleteCancel">Cancel</button><button type="button" class="hb-action danger" id="hbHubDeleteConfirm">Archive Hub</button></div></section></div>
<!-- Archive is a reversible soft-delete; keeps hubs and center data together. -->
<button type="button" class="hb-archive-fab" id="hbArchiveOpen" title="Archived sorting centers" aria-label="Open sorting center archive"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 5h18M6 5l1 15h10l1-15M9 5V3h6v2M10 10v6M14 10v6"/></svg><span class="hb-archive-count" id="hbArchiveCount" hidden>0</span></button>
<div class="hb-modal" id="hbArchiveModal" hidden><section class="hb-dialog" role="dialog" aria-modal="true" aria-labelledby="hbArchiveTitle"><div class="hb-dialog-head"><div><h2 id="hbArchiveTitle">Archived Sorting Centers</h2><p>Deleted sorting centers are kept here until restored, including their delivery hubs.</p></div><button type="button" class="hb-close" id="hbArchiveClose" aria-label="Close archive">×</button></div><div class="hb-archive-list" id="hbArchiveList"></div></section></div>
<div id="hbFlashStack" class="hb-flash-stack" role="region" aria-label="Network Hubs notifications" aria-live="polite"></div>
<script id="networkHubsConfig" type="application/json">{!! json_encode(['apiToken' => $networkHubsApiToken ?? '', 'apiUrl' => url('/api/v1/logistics/network-hubs')]) !!}</script>
<script>document.addEventListener('DOMContentLoaded',()=>{
'use strict';
const $=id=>document.getElementById(id), form=$('hbForm'), modal=$('hbModal');
function flash(text,type='success',title=''){
  const stack=$('hbFlashStack');if(!stack)return;
  // Match the Admin Logistics + Seller Order Status flash card component.
  stack.replaceChildren();
  const el=document.createElement('div');
  el.className='hb-flash'+(type==='error'?' is-error':type==='info'?' is-info':'');
  el.setAttribute('role',type==='error'?'alert':'status');
  const icon=document.createElement('span');icon.className='hb-flash-icon';
  icon.innerHTML=type==='error'
    ? '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6 6 18"/></svg>'
    : type==='info'
      ? '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke-width="2"/><path stroke-linecap="round" stroke-width="2" d="M12 11v5M12 7h.01"/></svg>'
      : '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 12 4 4L19 6"/></svg>';
  const content=document.createElement('div');content.className='hb-flash-copy';
  const heading=document.createElement('p');heading.className='hb-flash-title';
  heading.textContent=title||(type==='error'?'Action Failed':type==='info'?'Notice':/delivery hub.*restor/i.test(text)?'Delivery Hub Restored':/restor/i.test(text)?'Sorting Center Restored':/delivery hub.*archiv/i.test(text)?'Delivery Hub Archived':/archiv/i.test(text)?'Sorting Center Archived':/delivery hub.*creat/i.test(text)?'Delivery Hub Created':/sorting center.*creat/i.test(text)?'Sorting Center Created':/updat|edit/i.test(text)?'Changes Saved':'Action Successful');
  const message=document.createElement('p');message.className='hb-flash-text';message.textContent=text;
  content.append(heading,message);
  const close=document.createElement('button');close.type='button';close.className='hb-flash-close';close.setAttribute('aria-label','Close notification');
  close.innerHTML='<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6 6 18"/></svg>';
  el.append(icon,content,close);stack.append(el);
  if(flash.timer)clearTimeout(flash.timer);
  close.addEventListener('click',()=>{clearTimeout(flash.timer);el.remove();});
  flash.timer=setTimeout(()=>el.remove(),3800);
}

const networkConfig=JSON.parse($('networkHubsConfig').textContent);
const networkApi=async(path,options={})=>{
 const response=await fetch(`${networkConfig.apiUrl}${path}`,{
  ...options,credentials:'same-origin',
  headers:{Accept:'application/json',Authorization:`Bearer ${networkConfig.apiToken}`,'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]')?.content||'',...(options.headers||{})}
 });
 const result=await response.json();
 if(!response.ok){const validation=Object.values(result.errors||{}).flat()[0];throw Error(validation||result.message||`Request failed (${response.status}).`)}
 return result;
};
// Delivery-hub update/archive/restore endpoints are siblings of /network-hubs, not children.
const networkApiHub=async(path,options={})=>{
 const base=networkConfig.apiUrl.replace(/\/network-hubs\/?$/, '');
 const response=await fetch(`${base}${path}`,{
  ...options,credentials:'same-origin',
  headers:{Accept:'application/json',Authorization:`Bearer ${networkConfig.apiToken}`,'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]')?.content||'',...(options.headers||{})}
 });
 const result=await response.json();
 if(!response.ok){const validation=Object.values(result.errors||{}).flat()[0];throw Error(validation||result.message||`Request failed (${response.status}).`)}
 return result;
};
let records=[],archived=[],hubArchive=[];
const loadCenters=async()=>{const result=await networkApi('');records=result.data||[];draw();return records};
const esc=v=>String(v??'').replace(/[&<>"']/g,x=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[x]));
let centerEditId=null,hubEditId=null,selectedHubId=null;let activeFilter='all';function draw(){const q=$('hbSearch').value.trim().toLowerCase();const shown=records.filter(r=>(activeFilter==='all'||r.status===activeFilter)&&[r.branch_name,r.province,r.manager_name].some(v=>String(v||'').toLowerCase().includes(q)));
$('hbCountAll').textContent=records.length;$('hbCountActive').textContent=records.filter(r=>r.status==='active').length;$('hbCountInactive').textContent=records.filter(r=>r.status==='inactive').length;$('hbResultCount').textContent=shown.length+' '+(shown.length===1?'center':'centers');
$('hbRows').innerHTML=shown.map(r=>`<tr class="hb-open-row" data-center-id="${esc(r.id)}" tabindex="0" role="button" aria-label="View ${esc(r.branch_name)}"><td><strong>${esc(r.branch_name)}</strong><small>${esc([r.street,r.barangay,r.city].filter(Boolean).join(', '))}</small></td><td>${esc(r.province)}</td><td><strong>${esc(r.manager_name)}</strong><small>${esc(r.manager_email)}</small></td><td>${Number(r.delivery_hubs_count||0)}</td><td>${Number(r.drivers_count||0)}</td><td>${Number(r.vehicles_count||0)}</td><td><span class="hb-status ${r.status==='inactive'?'inactive':''}">${r.status==='inactive'?'Inactive':'Active'}</span></td></tr>`).join('')||`<tr><td colspan="7" style="text-align:center;padding:35px;color:#98918c">${records.length?'No sorting centers match your search.':'No sorting centers yet.'}</td></tr>`;
}
$('hbSearch').addEventListener('input',draw);document.querySelectorAll('[data-filter]').forEach(b=>b.addEventListener('click',()=>{activeFilter=b.dataset.filter;document.querySelectorAll('[data-filter]').forEach(x=>x.classList.toggle('active',x===b));draw()}));
function open(skipLocations=false){centerEditId=null;$('hbDialogTitle').textContent='Create Sorting Center';form.querySelector('[type=submit]').textContent='Create Sorting Center';$('hbProvinceSearch').disabled=false;$('hbPasswordInfo').hidden=false;form.reset();resetLocation('province','Search province...');resetLocation('city','Select province first');resetLocation('barangay','Select city first');$('hbFormMessage').hidden=true;modal.hidden=false;document.body.classList.add('lg-modal-open');if(!skipLocations)initProvinces();$('hbName').focus()}
function close(){modal.hidden=true;const wasEdit=centerEditId!==null;centerEditId=null;if(wasEdit&&selectedCenter())detailsModal.hidden=false;else document.body.classList.remove('lg-modal-open')}
$('hbOpen').addEventListener('click',()=>open());$('hbClose').addEventListener('click',close);$('hbCancel').addEventListener('click',close);modal.addEventListener('mousedown',e=>{if(e.target===modal)close()});document.addEventListener('keydown',e=>{if(e.key==='Escape')close()});
$('lgSidebarToggle')?.addEventListener('click',()=>document.body.classList.toggle('lg-collapsed'));
$('lgProfileButton')?.addEventListener('click',()=>{const m=$('lgProfileMenu');m.hidden=!m.hidden});document.addEventListener('click',e=>{if(!e.target.closest('.lg-profile-wrap')&&$('lgProfileMenu'))$('lgProfileMenu').hidden=true});
$('lgNotifyButton')?.addEventListener('click',()=>alert('No live notifications yet.'));
$('lgChangeProfilePhoto')?.addEventListener('click',()=>$('lgProfilePhotoInput')?.click());
$('lgProfilePhotoInput')?.addEventListener('change',async event=>{
 const file=event.target.files?.[0];if(!file)return;
 if(!['image/jpeg','image/png','image/webp'].includes(file.type)||file.size>5*1024*1024){alert('Choose JPG, PNG or WebP under 5 MB.');event.target.value='';return}
 const data=new FormData();data.append('profile_photo',file);
 try{const response=await fetch('/api/v1/profile/photo',{method:'PUT',credentials:'same-origin',headers:{Accept:'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]')?.content||''},body:data});
 const result=await response.json();if(!response.ok)throw Error(result.message||'Unable to update photo.');
 const avatar=document.querySelector('#lgProfileButton .account-avatar');if(avatar){let image=avatar.querySelector('img');if(!image){image=document.createElement('img');image.alt='';image.style.cssText='position:absolute;inset:0;width:100%;height:100%;border-radius:inherit;object-fit:cover';avatar.append(image)}image.src=result.profile_photo_url;}alert('Profile photo updated.');
 }catch(err){alert(err.message||'Unable to update profile photo.')}finally{event.target.value=''}
});

const objName=o=>String(o?.name||o?.prov_name||o?.city_name||o?.mun_name||o?.brgy_name||'').trim();
const objCode=o=>String(o?.code||o?.psgc_code||o?.prov_code||o?.city_code||o?.mun_code||o?.brgy_code||'').trim();
const isLocation=o=>Boolean(objName(o)&&objCode(o));
async function fetchLocations(suffix){const res=await fetch('/api/v1/locations/'+suffix,{headers:{Accept:'application/json'}});if(!res.ok)throw Error('Could not load locations ('+res.status+')');const json=await res.json(),locations=Array.isArray(json)?json:json?.data;if(!Array.isArray(locations))throw Error('Invalid location response');return locations.filter(isLocation)}
const locationState={};for(const kind of ['province','city','barangay']){const text=$('hb'+kind[0].toUpperCase()+kind.slice(1)+'Search'),hidden=$('hb'+kind[0].toUpperCase()+kind.slice(1)),list=$('hb'+kind[0].toUpperCase()+kind.slice(1)+'Options');locationState[kind]={text,hidden,list,items:[],selected:null,version:0};
text.addEventListener('focus',()=>renderOptions(kind));text.addEventListener('input',()=>{hidden.value='';locationState[kind].selected=null;renderOptions(kind)});text.addEventListener('keydown',e=>{if(e.key==='Escape'){list.hidden=true;return}if(e.key==='Enter'&&!list.hidden){const first=list.querySelector('button');if(first){e.preventDefault();first.click()}}});text.addEventListener('blur',()=>setTimeout(()=>{text.value=hidden.value;list.hidden=true;text.setAttribute('aria-expanded','false')},150));}
function resetLocation(kind,placeholder){const c=locationState[kind];c.items=[];c.selected=null;c.version++;c.text.value='';c.hidden.value='';c.text.placeholder=placeholder;c.text.disabled=true;c.list.hidden=true;c.text.setAttribute('aria-expanded','false')}
function renderOptions(kind){const c=locationState[kind];if(c.text.disabled)return;const q=c.text.value.trim().toLowerCase();const options=c.items.filter(o=>isLocation(o)&&objName(o).toLowerCase().includes(q)).slice(0,100);c.list.innerHTML='';if(!options.length){const empty=document.createElement('div');empty.className='hb-option';empty.textContent='No matching locations';c.list.append(empty)}for(const o of options){const b=document.createElement('button');b.type='button';b.className='hb-option';b.textContent=objName(o);b.addEventListener('mousedown',e=>e.preventDefault());b.addEventListener('click',()=>choose(kind,o));c.list.append(b)}c.list.hidden=false;c.text.setAttribute('aria-expanded','true')}
async function choose(kind,o){if(!isLocation(o)){message('The selected location is missing a name or code. Please choose another result.');return}const c=locationState[kind];c.selected=o;c.hidden.value=objName(o);c.text.value=objName(o);c.list.hidden=true;c.text.setAttribute('aria-expanded','false');if(kind==='province'){resetLocation('city','Loading cities...');resetLocation('barangay','Select city first');const version=locationState.city.version;try{const list=await fetchLocations('provinces/'+encodeURIComponent(objCode(o))+'/cities'+(o.isRegion===true?'?is_region=1':''));if(version!==locationState.city.version)return;locationState.city.items=list;locationState.city.text.disabled=false;locationState.city.text.placeholder='Search city or municipality...'}catch(e){locationState.city.text.placeholder='Locations unavailable';message('City/municipality lookup failed. Check the locations API and try selecting the province again.')}}if(kind==='city'){resetLocation('barangay','Loading barangays...');const version=locationState.barangay.version;try{const list=await fetchLocations('cities/'+encodeURIComponent(objCode(o))+'/barangays');if(version!==locationState.barangay.version)return;locationState.barangay.items=list;locationState.barangay.text.disabled=false;locationState.barangay.text.placeholder='Search barangay...'}catch(e){locationState.barangay.text.placeholder='Locations unavailable';message('Barangay lookup failed. Check the locations API and try selecting the city again.')}}}
async function initProvinces(){const c=locationState.province;c.text.placeholder='Loading provinces...';try{c.items=await fetchLocations('provinces');if(modal.hidden)return;c.text.disabled=false;c.text.placeholder='Type or select a province...'}catch(e){c.text.placeholder='Unable to load provinces';message('Province lookup unavailable; check /api/v1/locations/provinces.')}}
function message(msg){const m=$('hbFormMessage');m.textContent=msg;m.hidden=false;flash(msg,'error')}
form.addEventListener('submit',async e=>{e.preventDefault();$('hbFormMessage').hidden=true;const values=Object.fromEntries(new FormData(form).entries());for(const id of ['hbName','hbZip','hbStreet','hbManager','hbManagerEmail','hbManagerPhone','hbLoginEmail']){const input=$(id);if(!input.checkValidity()){input.reportValidity();return}}
for(const kind of ['province','city','barangay'])if(!(centerEditId!==null&&kind==='province')&&(!locationState[kind].selected||!locationState[kind].hidden.value)){locationState[kind].text.focus();message('Select a valid '+kind+' from the dropdown.');return}
const editing=centerEditId!==null,submit=form.querySelector('[type=submit]');submit.disabled=true;
try{await networkApi(editing?'/'+encodeURIComponent(centerEditId):'',{method:editing?'PUT':'POST',body:JSON.stringify(values)});await loadCenters();if(editing){const entry=selectedCenter();if(entry){$('hbDetailsTitle').textContent=entry.branch_name;$('hbDetailsSubtitle').textContent=entry.province+' · Sorting center details';renderCenterInfo()}}close();flash(editing?'Sorting center updated successfully.':'Sorting center created successfully.')}
catch(error){message(error.message)}finally{submit.disabled=false}
});

// Sorting center details: only the selected center and its child hubs are shown.
let selectedCenterId=null;
const detailsModal=$('hbDetailsModal'),hubModal=$('hbAddHubModal');
function selectedCenter(){return records.find(r=>String(r.id)===String(selectedCenterId))}
function showDetailTab(tab){$('hbHubArchiveOpen').hidden=tab!=='hubs';if(tab==='hubs')updateHubArchiveCount();document.querySelectorAll('[data-detail-tab]').forEach(b=>b.classList.toggle('active',b.dataset.detailTab===tab));$('hbDetailsInfo').hidden=tab!=='info';$('hbDetailsHubs').hidden=tab!=='hubs';if(tab==='hubs')renderHubRows()}
function showInfoTab(tab){document.querySelectorAll('[data-info-tab]').forEach(b=>b.classList.toggle('active',b.dataset.infoTab===tab));$('hbInfoDetails').hidden=tab!=='details';$('hbInfoVehicles').hidden=tab!=='vehicles';if(tab==='vehicles')renderCenterVehicles()}
function pill(status){const inactive=String(status||'active').toLowerCase()==='inactive';return `<span class="hb-status ${inactive?'inactive':''}">${inactive?'Inactive':'Active'}</span>`}
function infoRows(items){return `<div class="hb-profile-list">${items.map(([label,val,raw])=>`<div class="hb-profile-row"><span>${esc(label)}</span><b>${raw?val:esc(val??'—')}</b></div>`).join('')}</div>`}
function profileSection(title,icon,items){return `<section class="hb-profile-section"><h3><span aria-hidden="true">${icon}</span> ${esc(title)}</h3>${infoRows(items)}</section>`}
function renderCenterInfo(){const c=selectedCenter();if(!c)return;

 $('hbInfoDetails').innerHTML=`<div class="hb-profile-layout"><aside class="hb-profile-summary"><div class="hb-profile-avatar" aria-hidden="true">SC</div><div class="hb-profile-name-row"><strong>${esc(c.branch_name)}</strong><a class="hb-profile-message" href="${'/logistics/messages' + '?tab=sorting&center=' + encodeURIComponent(String(c.id))}" aria-label="Message ${esc(c.branch_name)}" title="Message this sorting center"><img src="{{ asset('icons/buyer/send.png') }}" alt=""></a></div><small>${esc(c.city||'')} · ${esc(c.province||'')}</small>${pill(c.status)}<div class="hb-profile-contact"><span>Office email</span><b>${esc(c.login_email||'—')}</b><span>Center type</span><b>Provincial Sorting Center</b></div></aside><div class="hb-profile-content">${profileSection('Sorting Center Information','▣',[['Branch name',c.branch_name],['Province',c.province],['City / Municipality',c.city],['Login email',c.login_email]])}${profileSection('Office Address','⌖',[['Province',c.province],['Municipality',c.city],['Barangay',c.barangay],['Street',c.street],['House / Unit',c.house_no],['Building',c.building],['Subdivision',c.subdivision],['ZIP Code',c.postal_code]])}${profileSection('Manager Details','♟',[['Manager name',c.manager_name],['Manager email',c.manager_email],['Phone number',c.manager_phone]])}</div></div>`;
}

// Display actual vehicles when provided by the selected center. No sample vehicle records are invented.
function renderCenterVehicles(){
 const center=selectedCenter();const target=$('hbVehiclesGrid');if(!center||!target)return;
 const vehicles=Array.isArray(center.vehicles)?center.vehicles:[];
 if(!vehicles.length){target.innerHTML=`<div class="hb-vehicle-empty"><svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="6" y="22" width="35" height="25" rx="3"/><path d="M41 30h9l8 10v7H41z"/><circle cx="18" cy="49" r="5" fill="#fbf8f7"/><circle cx="48" cy="49" r="5" fill="#fbf8f7"/><path d="M12 29h23"/></svg><div><strong>No vehicles registered yet</strong><p>Vehicle photos and their basic details will appear here when registered.</p></div></div>`;return}
 const fallback=`<svg class="hb-vehicle-photo" viewBox="0 0 160 112" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"><rect width="160" height="112" rx="10" fill="#f5eeeb"/><rect x="24" y="40" width="75" height="39" rx="5" fill="#bfa6a0"/><path d="M99 52h19l18 18v9H99z" fill="#98716a"/><circle cx="47" cy="81" r="11" fill="#5b4c49"/><circle cx="47" cy="81" r="5" fill="#f5eeeb"/><circle cx="118" cy="81" r="11" fill="#5b4c49"/><circle cx="118" cy="81" r="5" fill="#f5eeeb"/></svg>`;
 target.innerHTML=vehicles.map(v=>{
  // Only app-relative images under /storage/ or /images/ are shown. Otherwise use a safe placeholder.
  const photo=String(v.photo_url||v.image_url||'');
  const picture=/^\/(?:storage|images)\/[a-zA-Z0-9_./-]+$/.test(photo)?`<img class="hb-vehicle-photo" src="${esc(photo)}" alt="${esc(v.name||v.model||'Vehicle')}" loading="lazy">`:fallback;
  return `<article class="hb-vehicle-card">${picture}<div class="hb-vehicle-copy"><h4>${esc(v.name||v.model||v.vehicle_name||'Registered vehicle')}</h4><div class="hb-vehicle-meta"><span>Plate number <b>${esc(v.plate_number||'—')}</b></span><span>Type <b>${esc(v.type||v.vehicle_type||'—')}</b></span><span>Model <b>${esc(v.model||'—')}</b></span><span>Status ${pill(v.status)}</span></div></div></article>`;
 }).join('');
}
function renderHubRows(){const c=selectedCenter();if(!c)return;const hubs=Array.isArray(c.hubs)?c.hubs:[];
 $('hbHubRows').innerHTML=hubs.map(h=>`<tr class="hb-open-row" data-hub-id="${esc(h.id)}" tabindex="0" role="button" aria-label="View ${esc(h.hub_name)}"><td><strong>${esc(h.hub_name)}</strong><small>${esc([h.street,h.barangay].filter(Boolean).join(', '))}</small></td><td>${esc(h.city)}</td><td><strong>${esc(h.manager_name)}</strong><small>${esc(h.manager_email)}</small></td><td>${Number(h.riders_count||0)}</td><td>${Number(h.couriers_count||0)}</td><td>${Number(h.vehicles_count||0)}</td><td>${pill(h.status)}</td></tr>`).join('')||'<tr><td colspan="7" class="hb-empty-row">No delivery hubs yet.</td></tr>';
}
function openHubDetails(id){selectedHubId=String(id);const c=selectedCenter();const h=(c?.hubs||[]).find(x=>String(x.id)===String(id));if(!h)return;

 $('hbHubDetailsTitle').textContent=h.hub_name;
 $('hbHubDetailsSubtitle').textContent=h.city+', '+c.province;
 $('hbHubProfile').innerHTML=`<div class="hb-profile-layout"><aside class="hb-profile-summary"><div class="hb-profile-avatar" aria-hidden="true">DH</div><div class="hb-profile-name-row"><strong>${esc(h.hub_name)}</strong><a class="hb-profile-message" href="${'/logistics/messages' + '?tab=delivery&center=' + encodeURIComponent(String(c.id)) + '&hub=' + encodeURIComponent(String(h.id))}" aria-label="Message ${esc(h.hub_name)}" title="Message this delivery hub"><img src="{{ asset('icons/buyer/send.png') }}" alt=""></a></div><small>${esc(h.city)} · ${esc(c.province)}</small>${pill(h.status)}<div class="hb-profile-contact"><span>Hub login</span><b>${esc(h.login_email||'—')}</b><span>Parent center</span><b>${esc(c.branch_name)}</b></div></aside><div class="hb-profile-content">${profileSection('Delivery Hub Information','▣',[['Hub name',h.hub_name],['Parent sorting center',c.branch_name],['Municipality',h.city],['Riders',String(h.riders_count||0)],['Couriers (Truck Drivers)',String(h.couriers_count||0)],['Login email',h.login_email]])}${profileSection('Office Address','⌖',[['Province',c.province],['Municipality',h.city],['Barangay',h.barangay],['Street',h.street],['House / Unit',h.house_no],['Building',h.building],['Subdivision',h.subdivision],['ZIP Code',h.postal_code]])}${profileSection('Manager Details','♟',[['Manager name',h.manager_name],['Manager email',h.manager_email],['Phone number',h.manager_phone]])}</div></div>`;
 $('hbHubDetailsModal').hidden=false;detailsModal.hidden=true;document.body.classList.add('lg-modal-open');
}
function closeHubDetails(){$('hbHubDetailsModal').hidden=true;detailsModal.hidden=false;showDetailTab('hubs')}
$('hbHubRows').addEventListener('click',e=>{const row=e.target.closest('[data-hub-id]');if(row)openHubDetails(row.dataset.hubId)});
$('hbHubRows').addEventListener('keydown',e=>{if((e.key==='Enter'||e.key===' ')&&e.target.closest('[data-hub-id]')){e.preventDefault();openHubDetails(e.target.closest('[data-hub-id]').dataset.hubId)}});
$('hbHubDetailsClose').addEventListener('click',closeHubDetails);$('hbHubCloseAction').addEventListener('click',closeHubDetails);
$('hbHubDetailsModal').addEventListener('click',e=>{if(e.target===$('hbHubDetailsModal'))closeHubDetails()});
function openDetails(id){selectedCenterId=id;const c=selectedCenter();if(!c)return;$('hbDetailsTitle').textContent=c.branch_name;$('hbDetailsSubtitle').textContent=c.province+' · Sorting center details';renderCenterInfo();showInfoTab('details');showDetailTab('info');detailsModal.hidden=false;document.body.classList.add('lg-modal-open')}
function closeDetails(){detailsModal.hidden=true;document.body.classList.remove('lg-modal-open')}
$('hbRows').addEventListener('click',e=>{const tr=e.target.closest('[data-center-id]');if(tr)openDetails(tr.dataset.centerId)});
$('hbRows').addEventListener('keydown',e=>{if((e.key==='Enter'||e.key===' ')&&e.target.closest('[data-center-id]')){e.preventDefault();openDetails(e.target.closest('[data-center-id]').dataset.centerId)}});
$('hbDetailsClose').addEventListener('click',closeDetails);detailsModal.addEventListener('click',e=>{if(e.target===detailsModal)closeDetails()});
document.querySelectorAll('[data-detail-tab]').forEach(b=>b.addEventListener('click',()=>showDetailTab(b.dataset.detailTab)));
document.querySelectorAll('[data-info-tab]').forEach(b=>b.addEventListener('click',()=>showInfoTab(b.dataset.infoTab)));
// Municipalities are fetched using the same PSGC endpoints as sorting center registration.
let hubCities=[],hubBarangays=[],hubSelectedCity=null,hubSelectedBarangay=null;
function setHubOptions(kind,items){const list=$(kind==='city'?'hubCityOptions':'hubBarangayOptions'),input=$(kind==='city'?'hubCitySearch':'hubBarangaySearch');const q=input.value.trim().toLocaleLowerCase();list.replaceChildren();const matches=items.filter(o=>isLocation(o)&&objName(o).toLocaleLowerCase().includes(q)).slice(0,100);if(!matches.length){const d=document.createElement('div');d.className='hb-option';d.textContent='No matching locations';list.append(d)}for(const o of matches){const b=document.createElement('button');b.type='button';b.className='hb-option';b.textContent=objName(o);b.addEventListener('mousedown',e=>e.preventDefault());b.addEventListener('click',()=>{input.value=objName(o);$(kind==='city'?'hubCity':'hubBarangay').value=objName(o);list.hidden=true;input.setAttribute('aria-expanded','false');if(kind==='city'){hubSelectedCity=o;hubSelectedBarangay=null;hubBarangays=[];$('hubBarangay').value='';$('hubBarangaySearch').value='';$('hubBarangaySearch').disabled=true;loadHubBarangays(o)}else hubSelectedBarangay=o});list.append(b)}list.hidden=false;input.setAttribute('aria-expanded','true')}
for(const kind of ['city','barangay']){const input=$(kind==='city'?'hubCitySearch':'hubBarangaySearch'),list=$(kind==='city'?'hubCityOptions':'hubBarangayOptions');input.addEventListener('focus',()=>{if(!input.disabled)setHubOptions(kind,kind==='city'?hubCities:hubBarangays)});input.addEventListener('input',()=>{$(kind==='city'?'hubCity':'hubBarangay').value='';if(kind==='city'){hubSelectedCity=null;hubSelectedBarangay=null;$('hubBarangay').value='';$('hubBarangaySearch').value='';$('hubBarangaySearch').disabled=true}else hubSelectedBarangay=null;setHubOptions(kind,kind==='city'?hubCities:hubBarangays)});input.addEventListener('keydown',e=>{if(e.key==='Escape')list.hidden=true;if(e.key==='Enter'&&!list.hidden){const option=list.querySelector('button');if(option){e.preventDefault();option.click()}}});input.addEventListener('blur',()=>setTimeout(()=>{list.hidden=true;input.value=$(kind==='city'?'hubCity':'hubBarangay').value},150))}
async function loadHubBarangays(city){if(hubSelectedCity!==city)return;$('hubBarangaySearch').placeholder='Loading barangays...';try{const barangays=await fetchLocations('cities/'+encodeURIComponent(objCode(city))+'/barangays');if(hubSelectedCity!==city)return;hubBarangays=barangays;$('hubBarangaySearch').disabled=false;$('hubBarangaySearch').placeholder='Search barangay...'}catch(error){if(hubSelectedCity!==city)return;$('hubBarangaySearch').placeholder='Unable to load barangays';hubError('Barangay lookup failed. Check the locations API and select the municipality again.')}}
function hubError(text){$('hbAddHubError').textContent=text;$('hbAddHubError').hidden=false;flash(text,'error')}
async function openHubModal(editId=null){
 hubEditId=editId===null?null:String(editId);
 const c=selectedCenter();if(!c)return;
 const hub=hubEditId===null?null:(c.hubs||[]).find(h=>String(h.id)===hubEditId);
 if(hubEditId!==null&&!hub)return;
 $('hbAddHubForm').reset();
 $('hbAddHubTitle').textContent=hub?'Edit Delivery Hub':'Add Delivery Hub';
 $('hbAddHubForm').querySelector('[type=submit]').textContent=hub?'Save Changes':'Add Delivery Hub';
 $('hubPasswordInfo').hidden=!!hub;
 $('hbAddHubError').hidden=true;$('hubProvince').value=c.province;
 $('hbAddHubSubtitle').textContent='Under '+c.branch_name+' · '+c.province;
 hubSelectedCity=null;hubSelectedBarangay=null;hubCities=[];hubBarangays=[];
 $('hubCity').value='';$('hubBarangay').value='';
 $('hubCitySearch').disabled=true;$('hubBarangaySearch').disabled=true;
 $('hubCitySearch').placeholder='Loading municipalities...';
 // Populate all saved fields before any asynchronous PSGC requests.
 if(hub){
  for(const field of formHubFields()){
   const input=$('hbAddHubForm').elements.namedItem(field);if(input)input.value=hub[field]??'';
  }
  $('hubCity').value=hub.city||'';$('hubCitySearch').value=hub.city||'';
  $('hubBarangay').value=hub.barangay||'';$('hubBarangaySearch').value=hub.barangay||'';
  hubSelectedCity={name:hub.city||''};hubSelectedBarangay={name:hub.barangay||''};
 }
 hubModal.hidden=false;detailsModal.hidden=true;
 try{
  const provinces=await fetchLocations('provinces');
  if(hubEditId!==(hub?String(hub.id):null)||hubModal.hidden)return;
  const parent=provinces.find(p=>objName(p).trim().toLowerCase()===String(c.province).trim().toLowerCase());
  if(!parent)throw Error('Parent province not found');
  hubCities=await fetchLocations('provinces/'+encodeURIComponent(objCode(parent))+'/cities'+(parent.isRegion===true?'?is_region=1':''));
  if(hubEditId!==(hub?String(hub.id):null)||hubModal.hidden)return;
  $('hubCitySearch').disabled=false;$('hubCitySearch').placeholder='Type or select municipality...';
  if(hub){
   const city=hubCities.find(o=>objName(o).trim().toLowerCase()===String(hub.city).trim().toLowerCase());
   if(city){hubSelectedCity=city;await loadHubBarangays(city);
    if(hubEditId!==String(hub.id)||hubModal.hidden)return;
    const brgy=hubBarangays.find(o=>objName(o).trim().toLowerCase()===String(hub.barangay).trim().toLowerCase());
    if(brgy)hubSelectedBarangay=brgy;
   }
   $('hubCity').value=hub.city||'';$('hubCitySearch').value=hub.city||'';
   $('hubBarangay').value=hub.barangay||'';$('hubBarangaySearch').value=hub.barangay||'';
   $('hubBarangaySearch').disabled=false;
  }
 }catch(error){
  if(hub){$('hubCitySearch').disabled=true;$('hubBarangaySearch').disabled=true;
   $('hubCitySearch').placeholder='Lookup unavailable; existing value retained';$('hubBarangaySearch').placeholder='Lookup unavailable; existing value retained';}
  else hubError('Municipality lookup failed. Please check the locations API.');
 }
}
function formHubFields(){return ['hub_name','postal_code','house_no','street','building','subdivision','manager_name','manager_email','manager_phone','login_email']}
function closeHubModal(){const edit=hubEditId!==null;hubEditId=null;hubModal.hidden=true;detailsModal.hidden=edit; if(edit){$('hbHubDetailsModal').hidden=false;openHubDetails(selectedHubId)}else{detailsModal.hidden=false;renderCenterInfo();renderHubRows()}}
$('hbAddHubOpen').addEventListener('click',()=>openHubModal());$('hbEditHub').addEventListener('click',()=>{selectedHubId=String(selectedHubId);openHubModal(selectedHubId)});$('hbAddHubClose').addEventListener('click',closeHubModal);$('hubCancel').addEventListener('click',closeHubModal);hubModal.addEventListener('click',e=>{if(e.target===hubModal)closeHubModal()});

$('hbAddHubForm').addEventListener('submit',async e=>{e.preventDefault();$('hbAddHubError').hidden=true;const formHub=$('hbAddHubForm');for(const input of formHub.querySelectorAll('[required]:not(:disabled)')){if(!input.checkValidity()){input.reportValidity();return}}if(!hubSelectedCity||!hubSelectedBarangay||!$('hubCity').value||!$('hubBarangay').value){hubError('Select a valid municipality and barangay from the suggestions.');return}const c=selectedCenter();if(!c)return;const values=Object.fromEntries(new FormData(formHub).entries());const editing=hubEditId!==null,submit=formHub.querySelector('[type=submit]');submit.disabled=true;
try{const path=editing?'/delivery-hubs/'+encodeURIComponent(hubEditId):'/'+encodeURIComponent(c.id)+'/delivery-hubs';await networkApi(path,{method:editing?'PUT':'POST',body:JSON.stringify(values)});await loadCenters();closeHubModal();showDetailTab('hubs');flash(editing?'Delivery hub updated successfully.':'Delivery hub created successfully.')}
catch(error){hubError(error.message)}finally{submit.disabled=false}
});

function updateArchiveCount(){const counter=$('hbArchiveCount');counter.textContent=String(archived.length);counter.hidden=!archived.length}
function renderArchive(){updateArchiveCount();$('hbArchiveList').innerHTML=archived.length?archived.map(c=>`<div class="hb-archive-item"><div><strong>${esc(c.branch_name)}</strong><small>${esc(c.province)} · ${(c.hubs||[]).length} delivery hub(s) · Archived ${esc(c.archived_at?new Date(c.archived_at).toLocaleDateString('en-PH'):'recently')}</small></div><button class="hb-action primary" type="button" aria-label="Restore archived sorting center" data-restore-id="${esc(c.id)}">Restore</button></div>`).join(''):'<p class="hb-archive-empty">No archived sorting centers yet.</p>'}
async function showArchive(){try{const result=await networkApi('/archive');archived=result.data||[];renderArchive();$('hbArchiveModal').hidden=false;document.body.classList.add('lg-modal-open')}catch(error){flash(error.message,'error')}}
function hideArchive(){$('hbArchiveModal').hidden=true;document.body.classList.remove('lg-modal-open')}
$('hbArchiveOpen').addEventListener('click',showArchive);
$('hbArchiveClose').addEventListener('click',hideArchive);
$('hbArchiveModal').addEventListener('click',e=>{if(e.target===$('hbArchiveModal'))hideArchive()});
$('hbArchiveList').addEventListener('click',async e=>{const b=e.target.closest('[data-restore-id]');if(!b)return;b.disabled=true;try{await networkApi('/'+encodeURIComponent(b.dataset.restoreId)+'/restore',{method:'POST'});await loadCenters();const result=await networkApi('/archive');archived=result.data||[];renderArchive();flash('Sorting center restored successfully.')}catch(error){flash(error.message,'error')}finally{b.disabled=false}});
$('hbArchiveCenter').addEventListener('click',async()=>{const center=selectedCenter();if(!center)return;if(!confirm('Move '+center.branch_name+' and its delivery hubs to Archive? You can restore it later.'))return;try{await networkApi('/'+encodeURIComponent(center.id)+'/archive',{method:'POST'});selectedCenterId=null;await loadCenters();updateArchiveCount();closeDetails();flash('Sorting center moved to Archive.','info')}catch(error){flash(error.message,'error')}});
$('hbDetailsCloseAction').addEventListener('click',closeDetails);
// Reuse the original creation form for sorting-center editing, including PSGC dropdowns.
async function openCenterEdit(){
 const c=selectedCenter();if(!c)return;
 open(true);centerEditId=String(c.id);
 $('hbDialogTitle').textContent='Edit Sorting Center';
 form.querySelector('[type=submit]').textContent='Save Changes';
 $('hbPasswordInfo').hidden=true;
 for(const field of ['branch_name','postal_code','house_no','street','building','subdivision','manager_name','manager_email','manager_phone','login_email']){
  const input=form.elements.namedItem(field);if(input)input.value=c[field]??'';
 }
 // Show existing location values immediately, regardless of API response time.
 for(const kind of ['province','city','barangay']){
  const value=String(c[kind]??'');const state=locationState[kind];
  state.text.value=value;state.hidden.value=value;
  state.selected=value?{name:value}:null;
  state.text.disabled=kind==='province';
 }
 detailsModal.hidden=true;
 try{
  await initProvinces();if(centerEditId!==String(c.id))return;
  const p=locationState.province.items.find(o=>objName(o).trim().toLowerCase()===String(c.province).trim().toLowerCase());
  if(!p)throw Error('Province not found');
  await choose('province',p);if(centerEditId!==String(c.id))return;
  locationState.province.text.disabled=true;
  const ct=locationState.city.items.find(o=>objName(o).trim().toLowerCase()===String(c.city).trim().toLowerCase());
  if(!ct)throw Error('City not found');
  await choose('city',ct);if(centerEditId!==String(c.id))return;
  const br=locationState.barangay.items.find(o=>objName(o).trim().toLowerCase()===String(c.barangay).trim().toLowerCase());
  if(!br)throw Error('Barangay not found');
  await choose('barangay',br);
 }catch(error){
  // Never wipe saved address on a failed PSGC lookup.
  for(const kind of ['province','city','barangay']){
   const state=locationState[kind],value=String(c[kind]??'');
   if(!state.hidden.value){state.text.value=value;state.hidden.value=value;state.selected=value?{name:value}:null;}
  }
 }
}
$('hbEditCenter').addEventListener('click',openCenterEdit);

// Delivery hubs use their own soft-delete archive, scoped to the parent sorting center.
function scopedHubArchive(){return hubArchive.filter(x=>String(x.center_id)===String(selectedCenterId))}
function updateHubArchiveCount(){const count=Number(selectedCenter()?.archived_hubs_count||0);const badge=$('hbHubArchiveCount');badge.textContent=String(count);badge.hidden=!count}
function renderHubArchive(){updateHubArchiveCount();const items=scopedHubArchive();$('hbHubArchiveList').innerHTML=items.length?items.map(x=>`<div class="hb-archive-item"><div><strong>${esc(x.hub_name)}</strong><small>${esc(x.city)} · ${esc(x.manager_name)} · Archived ${esc(new Date(x.archived_at).toLocaleDateString('en-PH'))}</small></div><button type="button" class="hb-action primary" data-hub-restore="${esc(x.id)}">Restore</button></div>`).join(''):'<p class="hb-archive-empty">No archived delivery hubs for this sorting center.</p>'}
async function openHubArchive(){try{const result=await networkApi('/'+encodeURIComponent(selectedCenterId)+'/delivery-hubs/archive');hubArchive=result.data||[];if(selectedCenter())selectedCenter().archived_hubs_count=hubArchive.length;renderHubArchive();$('hbHubArchiveModal').hidden=false;detailsModal.hidden=true;document.body.classList.add('lg-modal-open')}catch(error){flash(error.message,'error')}}
function closeHubArchive(){$('hbHubArchiveModal').hidden=true;detailsModal.hidden=false;showDetailTab('hubs')}
$('hbHubArchiveOpen').addEventListener('click',openHubArchive);$('hbHubArchiveClose').addEventListener('click',closeHubArchive);$('hbHubArchiveModal').addEventListener('click',e=>{if(e.target===$('hbHubArchiveModal'))closeHubArchive()});
$('hbHubArchiveList').addEventListener('click',async e=>{const b=e.target.closest('[data-hub-restore]');if(!b)return;b.disabled=true;try{await networkApiHub('/delivery-hubs/'+encodeURIComponent(b.dataset.hubRestore)+'/restore',{method:'POST'});await loadCenters();const result=await networkApi('/'+encodeURIComponent(selectedCenterId)+'/delivery-hubs/archive');hubArchive=result.data||[];if(selectedCenter())selectedCenter().archived_hubs_count=hubArchive.length;renderHubArchive();renderHubRows();flash('Delivery hub restored successfully.')}catch(error){flash(error.message,'error')}finally{b.disabled=false}});
$('hbArchiveHub').addEventListener('click',()=>{
 const c=selectedCenter(),h=(c?.hubs||[]).find(h=>String(h.id)===String(selectedHubId));if(!h)return;
 $('hbHubDeleteReasonSubtitle').textContent=`Select why ${h.hub_name} should be archived. You can restore it later.`;
 $('hbHubDeleteReason').value='';$('hbHubDeleteDetails').value='';$('hbHubDeleteError').textContent='';
 $('hbHubDeleteReasonModal').hidden=false;
});
const closeHubDeleteReason=()=>{$('hbHubDeleteReasonModal').hidden=true};
$('hbHubDeleteReasonX').addEventListener('click',closeHubDeleteReason);
$('hbHubDeleteCancel').addEventListener('click',closeHubDeleteReason);
$('hbHubDeleteReasonModal').addEventListener('click',e=>{if(e.target===$('hbHubDeleteReasonModal'))closeHubDeleteReason()});
$('hbHubDeleteConfirm').addEventListener('click',async()=>{
 const reason=$('hbHubDeleteReason').value,details=$('hbHubDeleteDetails').value.trim();
 const error=$('hbHubDeleteError');error.textContent='';
 if(!reason){error.textContent='Please select a reason.';return}
 if(reason==='Other'&&!details){error.textContent='Please provide additional details for Other.';return}
 const c=selectedCenter(),h=(c?.hubs||[]).find(h=>String(h.id)===String(selectedHubId));if(!h){error.textContent='Delivery hub is no longer available.';return}
 const button=$('hbHubDeleteConfirm');button.disabled=true;
 try{
  await networkApiHub('/delivery-hubs/'+encodeURIComponent(h.id)+'/archive',{method:'POST',body:JSON.stringify({reason,reason_details:details})});
  closeHubDeleteReason();await loadCenters();updateHubArchiveCount();closeHubDetails();renderHubRows();flash('Delivery hub moved to Archive.','info');
 }catch(err){error.textContent=err.message||'Unable to archive delivery hub.';flash(err.message||'Unable to archive delivery hub.','error')}
 finally{button.disabled=false}
});
updateArchiveCount();draw();loadCenters().catch(error=>{const row=$('hbRows');row.innerHTML=`<tr><td colspan="7" style="text-align:center;padding:35px;color:#b42323">${esc(error.message)}</td></tr>`;flash(error.message,'error')});

document.addEventListener('keydown',e=>{if(e.key==='Escape'){if(!hubModal.hidden)closeHubModal();else if(!detailsModal.hidden)closeDetails()}});
});</script></body></html>