<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Logistics Earnings — ShopEase</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>

/* Shared logistics shell — same dimensions and collapse behavior as Logistics Dashboard. */
:root{--lg-maroon:#7B1B1B;--lg-dark:#52070B;--lg-peach:#FFE6DE;--lg-bg:#FCF8F6;--lg-line:#F0E9E6;--lg-ink:#17120F;--lg-muted:#8C8784}
html,body.lg-body{overflow-x:hidden}
body.lg-body{margin:0;background:var(--lg-bg);font-family:Poppins,Arial,sans-serif;color:var(--lg-ink)}
body.lg-body .lg-sidebar{position:fixed;left:0;top:0;bottom:0;width:260px;z-index:50;background:linear-gradient(155deg,#7B1B1B 0%,#52070B 80%);border-top-right-radius:58px;padding:20px 14px 18px;display:flex;flex-direction:column;transition:width .3s ease,transform .3s ease;box-shadow:4px 0 18px #52070b12;overflow:hidden;color:#fff}
body.lg-body .lg-sidebar-logo{height:74px;display:flex;align-items:center;justify-content:center;gap:7px;margin-bottom:20px;white-space:nowrap}
body.lg-body .lg-sidebar-logo img{max-width:115px;max-height:53px;object-fit:contain}
body.lg-body .lg-sidebar-menu{overflow-y:auto;overflow-x:hidden;flex:1;scrollbar-width:none}
body.lg-body .lg-sidebar-menu::-webkit-scrollbar{display:none}
body.lg-body .lg-nav-category{margin:15px 13px 9px;font-size:10px;letter-spacing:1.4px;color:#f5c8c7;font-weight:600}
body.lg-body .lg-nav-link{height:43px;width:100%;display:flex;align-items:center;gap:12px;padding:0 13px;border:0;border-radius:999px;color:#fff;font-size:12px;font-weight:500;text-align:left;white-space:nowrap}
body.lg-body .lg-nav-link img{width:20px;height:20px;object-fit:contain;flex-shrink:0}
body.lg-body .lg-navbar{position:fixed;top:0;left:0;right:0;height:88px;background:var(--lg-peach);z-index:35;display:flex;align-items:center;justify-content:space-between;padding:0 26px 0 288px;transition:padding-left .3s ease;gap:15px}
body.lg-body .lg-nav-left,body.lg-body .lg-nav-right{display:flex;align-items:center;gap:22px}
body.lg-body .lg-nav-left h2{font-size:21px;font-weight:700;margin:0;white-space:nowrap}
body.lg-body .lg-icon-button{border:0;background:transparent;display:grid;place-items:center;width:30px;height:34px;color:#52070B;flex-shrink:0;cursor:pointer}
body.lg-body .lg-icon-button svg{width:24px;height:24px}
body.lg-body .lg-icon-button img{width:24px;height:26px;object-fit:contain}
body.lg-body .lg-navbar-caption{font-size:12px;color:#815d57}
body.lg-body .lg-profile-wrap{position:relative}
body.lg-body .lg-profile-btn{border:1px solid #ebe0dc;background:#fff;border-radius:10px;padding:6px 10px;display:flex;align-items:center;gap:9px;box-shadow:0 2px 8px #0000000b;color:#29211f;text-align:left;min-width:190px;cursor:pointer}
body.lg-body .lg-profile-btn strong{display:block;font-size:12px;font-weight:600;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
body.lg-body .lg-profile-btn small{font-size:10px;color:#a19b98}
body.lg-body .lg-profile-chevron{margin-left:auto}
body.lg-body .lg-profile-menu{position:absolute;right:0;top:53px;width:214px;border:1px solid var(--lg-line);box-shadow:0 12px 30px #0002;border-radius:13px;background:white;padding:8px;z-index:100}
body.lg-body .lg-profile-menu[hidden]{display:none}
body.lg-body .lg-profile-menu>span{display:block;font-size:10px;color:var(--lg-muted);padding:9px}
body.lg-body .lg-profile-menu a,body.lg-body .lg-profile-menu button{display:block;width:100%;text-align:left;border:0;background:transparent;padding:10px;border-radius:7px;color:var(--lg-ink);font-size:12px;cursor:pointer}
body.lg-body .lg-profile-menu a:hover,body.lg-body .lg-profile-menu button:hover{background:#FFF2EE}
body.lg-collapsed .lg-sidebar{width:80px}
body.lg-collapsed .lg-navbar{padding-left:108px}
body.lg-collapsed .lg-sidebar-logo img{max-width:60px}
body.lg-collapsed .lg-sidebar-logo>span,body.lg-collapsed .lg-nav-category,body.lg-collapsed .lg-nav-link span,body.lg-collapsed .lg-sidebar-bottom{display:none}
body.lg-collapsed .lg-nav-link{justify-content:center;padding:0}
@media(max-width:900px) and (min-width:701px){
 body.lg-body .lg-sidebar{width:80px}
 body.lg-body .lg-sidebar-logo img{max-width:60px}
 body.lg-body .lg-nav-category,body.lg-body .lg-nav-link span{display:none}
 body.lg-body .lg-nav-link{justify-content:center;padding:0}
 body.lg-body .lg-navbar{padding-left:108px}
 body.lg-body.lg-collapsed .lg-sidebar{width:260px;box-shadow:0 0 0 100vw #0003}
 body.lg-body.lg-collapsed .lg-nav-category,body.lg-body.lg-collapsed .lg-nav-link span{display:block}
 body.lg-body.lg-collapsed .lg-nav-link{justify-content:flex-start;padding:0 13px}
}
@media(max-width:700px){
 body.lg-body .lg-sidebar{width:260px;transform:translateX(-100%)}
 body.lg-body.lg-collapsed .lg-sidebar{transform:translateX(0);box-shadow:0 0 0 100vw #0003}
 body.lg-body .lg-navbar,body.lg-body.lg-collapsed .lg-navbar{padding:0 14px;height:75px}
 body.lg-body .lg-nav-left h2{font-size:16px}
 body.lg-body .lg-nav-right{gap:8px}
 body.lg-body .lg-navbar-caption,body.lg-body .lg-profile-btn small,body.lg-body .lg-profile-chevron{display:none}
 body.lg-body .lg-profile-btn{min-width:unset}
 body.lg-body .lg-profile-btn strong{font-size:10px;max-width:92px}
 body.lg-body.lg-collapsed .lg-nav-category,body.lg-body.lg-collapsed .lg-nav-link span{display:block}
 body.lg-body.lg-collapsed .lg-nav-link{justify-content:flex-start;padding:0 13px}
}

        :root{
            --maroon:#7B1B1B;--maroon-dark:#52070B;--peach:#FFE6DE;--bg:#FCF8F6;--line:#EFE7E4;--ink:#1F1715;--muted:#8F8684;
            --green:#1F8A5A;--green-bg:#E9F7F0;--orange:#B56B18;--orange-bg:#FFF2E2;--red:#B23B4C;--red-bg:#FCECEF;
        }
        *{box-sizing:border-box}
        body.lg-body{margin:0;background:var(--bg);font-family:Poppins,sans-serif;color:var(--ink)}
        button,input,select{font-family:inherit}
        a{text-decoration:none}
        .lg-ne-main{margin-left:260px;padding:112px 24px 36px;min-height:100vh;transition:margin-left .3s ease}
        body.lg-collapsed .lg-ne-main{margin-left:80px}
        .lg-ne-shell{max-width:1480px;margin:0 auto}
        .lg-ne-head{display:flex;justify-content:space-between;align-items:flex-start;gap:18px;margin-bottom:18px}
        .lg-ne-overline{display:block;font-size:11px;letter-spacing:1.8px;font-weight:700;color:#A03342;margin-bottom:6px}
        .lg-ne-head h1{margin:0;font-size:31px;line-height:1.2;font-weight:750}
        .lg-ne-head p{margin:8px 0 0;color:var(--muted);font-size:13px;line-height:1.7;max-width:720px}
        .lg-ne-head-right{display:flex;align-items:center;gap:10px;flex-wrap:wrap;justify-content:flex-end}
        .lg-chip{display:inline-flex;align-items:center;gap:7px;padding:10px 13px;border-radius:999px;background:white;border:1px solid var(--line);font-size:11px;color:#6E5E5E;font-weight:600}
        .lg-chip-dot{width:8px;height:8px;border-radius:999px;background:#33A06F}
        .lg-chip.preview .lg-chip-dot{background:#C27B2E}
        .lg-note{display:flex;align-items:flex-start;gap:10px;padding:12px 14px;border-radius:13px;background:#FFF8F1;border:1px solid #F3E4D5;color:#8B683A;font-size:11px;line-height:1.75;margin-bottom:20px}
        .lg-note .dot{width:9px;height:9px;border-radius:999px;background:#D1A05D;flex:0 0 9px;margin-top:5px}
        .lg-ne-hero{display:grid;grid-template-columns:minmax(0,1.3fr) minmax(320px,.7fr);gap:18px;margin-bottom:18px}
        .lg-card{background:white;border:1px solid var(--line);border-radius:18px;box-shadow:0 1px 5px rgba(0,0,0,.05)}
        .lg-balance-card{padding:25px 24px;background:linear-gradient(120deg,#651015 0%,#8A2530 70%,#A94D4A 100%);color:white;overflow:hidden;position:relative}
        .lg-balance-card::after{content:'';position:absolute;right:-38px;top:-35px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,.07)}
        .lg-balance-label{font-size:12px;color:#F6D8DA;display:block}
        .lg-balance-amount{font-size:40px;font-weight:800;letter-spacing:-1px;margin:7px 0 8px}
        .lg-balance-sub{margin:0;max-width:640px;color:#F8E8EA;font-size:12px;line-height:1.8}
        .lg-balance-tags{display:flex;gap:9px;flex-wrap:wrap;margin-top:16px}
        .lg-balance-tag{padding:8px 12px;border:1px solid rgba(255,255,255,.2);background:rgba(255,255,255,.1);border-radius:999px;font-size:11px;font-weight:600}
        .lg-side-summary{padding:20px 18px}
        .lg-side-summary h3{margin:0 0 6px;font-size:17px}
        .lg-side-summary p{margin:0;color:var(--muted);font-size:11px;line-height:1.7}
        .lg-mini-stack{display:grid;gap:11px;margin-top:16px}
        .lg-mini-item{padding:13px 14px;border-radius:14px;background:#FCF7F6;border:1px solid #F2E7E5}
        .lg-mini-item strong{display:block;font-size:18px;color:#2A1D1E;margin-top:4px}
        .lg-mini-item span{font-size:10px;letter-spacing:.6px;color:#977F82;font-weight:700;text-transform:uppercase}
        .lg-stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px;margin-bottom:18px}
        .lg-stat{padding:20px 18px;min-height:112px}
        .lg-stat-top{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:10px}
        .lg-stat-kicker{font-size:11px;color:#8A7D7D;font-weight:600}
        .lg-stat-pill{display:inline-flex;align-items:center;justify-content:center;min-width:30px;height:30px;padding:0 10px;border-radius:999px;font-size:10px;font-weight:700}
        .green{background:var(--green-bg);color:var(--green)} .orange{background:var(--orange-bg);color:var(--orange)} .red{background:var(--red-bg);color:var(--red)} .maroon{background:#F7EDEF;color:var(--maroon)}
        .lg-stat strong{display:block;font-size:27px;line-height:1.18;font-weight:750;margin-bottom:6px}
        .lg-stat p{margin:0;color:var(--muted);font-size:11px;line-height:1.7}
        .lg-grid{display:grid;grid-template-columns:minmax(0,1.2fr) minmax(330px,.8fr);gap:18px;margin-bottom:18px;align-items:start}
        .lg-panel-head{display:flex;justify-content:space-between;align-items:flex-start;gap:14px;padding:20px 20px 0}
        .lg-panel-head h2{margin:0;font-size:17px}
        .lg-panel-head p{margin:5px 0 0;color:var(--muted);font-size:11px;line-height:1.7}
        .lg-pill{display:inline-flex;align-items:center;justify-content:center;height:30px;padding:0 12px;border-radius:999px;background:#F7EDEF;color:var(--maroon);font-size:11px;font-weight:700;white-space:nowrap}
        .lg-chart-wrap{padding:14px 20px 20px}
        .lg-chart{height:320px;display:flex;align-items:flex-end;gap:12px;padding:18px 6px 32px 54px;position:relative;border-bottom:1px solid #EEE4E2}
        .lg-chart-grid{position:absolute;left:54px;right:6px;top:18px;bottom:32px;pointer-events:none}
        .lg-chart-line{position:absolute;left:0;right:0;border-top:1px dashed #E8DFDE}
        .lg-chart-line label{position:absolute;left:-52px;top:-10px;font-size:11px;color:#A39593}
        .lg-bar-col{flex:1;min-width:28px;height:100%;display:flex;flex-direction:column;justify-content:flex-end;align-items:center;gap:10px;position:relative}
        .lg-bar{width:min(100%,40px);background:linear-gradient(180deg,#C72055 0%,#9F133E 100%);border-radius:10px 10px 4px 4px;position:relative;transition:transform .2s ease,filter .2s ease;box-shadow:0 6px 16px rgba(159,19,62,.15)}
        .lg-bar:hover{transform:translateY(-4px);filter:brightness(1.03)}
        .lg-bar-date{font-size:11px;color:#978D8B;white-space:nowrap}
        .lg-tooltip{position:absolute;left:50%;transform:translate(-50%,-10px);bottom:calc(100% + 8px);background:#2D2321;color:white;padding:8px 10px;border-radius:10px;font-size:11px;line-height:1.45;white-space:nowrap;opacity:0;pointer-events:none;transition:opacity .18s ease,transform .18s ease;box-shadow:0 10px 24px rgba(0,0,0,.18)}
        .lg-tooltip strong{display:block;font-size:11px;margin-bottom:2px}
        .lg-tooltip::after{content:'';position:absolute;left:50%;bottom:-5px;transform:translateX(-50%) rotate(45deg);width:10px;height:10px;background:#2D2321}
        .lg-bar:hover .lg-tooltip{opacity:1;transform:translate(-50%,0)}
        .lg-allocation{padding:14px 18px 18px;display:grid;gap:12px}
        .lg-allocation-card{padding:15px 15px 14px;border-radius:14px;background:#FCF7F6;border:1px solid #F2E7E5}
        .lg-allocation-top{display:flex;justify-content:space-between;gap:12px;align-items:center;margin-bottom:10px}
        .lg-allocation-card h3{margin:0;font-size:14px}
        .lg-allocation-card span{font-size:10px;padding:7px 10px;border-radius:999px;background:white;border:1px solid #EAD8D7;color:#8B6465;font-weight:700;white-space:nowrap}
        .lg-allocation-card strong{display:block;font-size:23px;margin-bottom:6px;color:#241818}
        .lg-allocation-card p{margin:0;color:var(--muted);font-size:11px;line-height:1.7}
        .lg-toolbar{display:flex;align-items:flex-end;gap:12px;flex-wrap:wrap;padding:18px 20px 12px}
        .lg-field{display:flex;flex-direction:column;gap:6px}
        .lg-field label{font-size:11px;font-weight:600;color:#675C5B}
        .lg-control{height:38px;border-radius:10px;border:1px solid #DDD6D3;background:white;padding:0 12px;color:#453C3B;font-size:12px;min-width:148px;outline:none}
        .lg-search{min-width:280px;flex:1;position:relative}
        .lg-search input{width:100%;padding-right:38px}
        .lg-search svg{position:absolute;right:12px;top:50%;transform:translateY(-50%);width:16px;height:16px;color:#B2AAAA}
        .lg-table-wrap{overflow:auto;padding:0 20px 0}
        .lg-table{width:100%;border-collapse:collapse;min-width:860px}
        .lg-table th{font-size:11px;color:#8E8382;font-weight:600;text-align:left;padding:14px 12px;border-bottom:1px solid #EEE5E3;white-space:nowrap}
        .lg-table td{font-size:12px;color:#3B3030;padding:15px 12px;border-bottom:1px solid #F3ECEA;vertical-align:middle}
        .lg-table tr:hover td{background:#FFF9F8}
        .lg-table strong{display:block;font-size:12px;color:#2A2020}
        .lg-table small{display:block;font-size:10px;color:#958B89;margin-top:3px}
        .lg-amount.pos{color:#228354;font-weight:700}.lg-amount.neg{color:#B23B4C;font-weight:700}
        .lg-status{display:inline-flex;align-items:center;justify-content:center;padding:6px 10px;border-radius:999px;font-size:10px;font-weight:700;white-space:nowrap}
        .lg-status.credited{background:#E9F7F0;color:#1D8657}.lg-status.pending{background:#FFF2E2;color:#B56B18}.lg-status.adjusted{background:#FCECEF;color:#B23B4C}.lg-table-foot{display:flex;justify-content:space-between;gap:14px;align-items:center;padding:14px 20px 18px;color:var(--muted);font-size:11px}
        .lg-table-foot strong{color:#403433}
        .lg-unit-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;padding:0 20px 20px}
        .lg-unit-card{padding:16px 15px;border-radius:16px;background:#FCF7F6;border:1px solid #F0E3E1}
        .lg-unit-top{display:flex;justify-content:space-between;gap:12px;align-items:center;margin-bottom:12px}
        .lg-unit-top h3{margin:0;font-size:14px}.lg-unit-top span{font-size:10px;padding:6px 10px;border-radius:999px;background:white;border:1px solid #E8D9D7;color:#856868;font-weight:700}
        .lg-unit-metrics{display:flex;justify-content:space-between;gap:16px}
        .lg-unit-metrics div{flex:1}
        .lg-unit-metrics small{display:block;font-size:10px;color:#988C8C;margin-bottom:4px}
        .lg-unit-metrics strong{display:block;font-size:18px}
        .lg-empty{padding:36px 18px;text-align:center;color:#918786;font-size:12px}
        @media (max-width:1200px){.lg-stats{grid-template-columns:repeat(2,minmax(0,1fr))}.lg-grid,.lg-ne-hero{grid-template-columns:1fr}.lg-unit-grid{grid-template-columns:1fr 1fr}}
        @media (max-width:900px){.lg-ne-main{margin-left:80px;padding:110px 16px 26px}.lg-ne-head{flex-direction:column;align-items:flex-start}.lg-ne-head-right{justify-content:flex-start}.lg-search{min-width:100%;flex-basis:100%}}
        @media (max-width:700px){.lg-ne-main,body.lg-collapsed .lg-ne-main{margin-left:0;padding-top:98px}.lg-stats,.lg-unit-grid{grid-template-columns:1fr}.lg-chart{height:280px;padding-left:44px;gap:8px}.lg-chart-grid{left:44px}.lg-chart-line label{left:-42px;font-size:10px}.lg-bar{width:min(100%,28px)}.lg-toolbar{padding-top:16px}.lg-control{min-width:100%}.lg-search{min-width:100%}.lg-table{min-width:720px}}

        /* Network Earnings refinements: match compact Logistics typography. */
        .lg-ne-main{padding-top:112px}
        .lg-ne-hero{grid-template-columns:minmax(0,1.35fr) minmax(300px,.65fr)}
        .lg-ne-hero>.lg-card{min-height:245px}
        .lg-balance-card{padding:25px 28px}
        .lg-balance-amount{font-size:clamp(27px,2.5vw,35px);font-weight:650;letter-spacing:-.7px}
        .lg-balance-sub{font-size:12px;font-weight:400;line-height:1.75;max-width:620px}
        .lg-balance-tag{font-size:10px;font-weight:500;padding:7px 10px}
        .lg-side-summary{padding:20px}
        .lg-side-summary h3{font-size:15px;font-weight:600}
        .lg-side-summary p{font-size:11px}
        .lg-mini-stack{gap:8px;margin-top:12px}
        .lg-mini-item{padding:11px 13px}
        .lg-mini-item strong{font-size:13px;font-weight:600;line-height:1.45}
        .lg-mini-item span{font-size:9px}
        .lg-stat{min-height:105px;padding:18px 16px}
        .lg-stat strong{font-size:22px;font-weight:650}
        .lg-stat p{font-size:11px}
        .lg-panel-head h2{font-size:16px;font-weight:600}
        .lg-grid{grid-template-columns:minmax(0,1.2fr) minmax(315px,.8fr);align-items:stretch}
        .lg-grid>.lg-card{height:100%}
        .lg-allocation{gap:9px;padding:12px 17px 16px}
        .lg-allocation-card{padding:11px 13px;border-radius:11px}
        .lg-allocation-top{margin-bottom:5px}
        .lg-allocation-card h3{font-size:12px;font-weight:600}
        .lg-allocation-card span{font-size:9px;padding:5px 8px}
        .lg-allocation-card strong{font-size:19px;font-weight:650;margin-bottom:3px}
        .lg-allocation-card p{font-size:10px;line-height:1.55}
        .lg-unit-hierarchy{grid-template-columns:repeat(2,minmax(0,1fr));align-items:start}
        .lg-parent-unit{padding:18px 16px;background:#fff}
        .lg-parent-caption{font-size:9px;letter-spacing:1.15px;font-weight:700;color:#A14A56}
        .lg-unit-top h3{font-size:14px;font-weight:600;margin-top:3px}
        .lg-parent-metrics{background:#FCF7F6;border:1px solid #F0E6E3;padding:12px 13px;border-radius:11px}
        .lg-parent-metrics strong{font-size:16px;font-weight:650}
        .lg-child-list{margin-top:13px;border-left:2px solid #E7CFD1;margin-left:9px;display:grid;gap:8px}
        .lg-child-unit{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:10px 12px 10px 18px;background:#FCF8F7;border:1px solid #F0E5E3;border-radius:10px;position:relative}
        .lg-tree-line{position:absolute;left:-2px;top:50%;width:12px;border-top:2px solid #D2A6AC}
        .lg-child-identity small{display:block;font-size:9px;color:#A08A8C;line-height:1.45;margin-bottom:4px}
        .lg-child-identity strong{font-size:12px;font-weight:600}
        .lg-child-figures{text-align:right;flex-shrink:0}
        .lg-child-figures span{display:block;font-size:10px;color:#978585}
        .lg-child-figures strong{display:block;font-size:13px;font-weight:650;color:#6E2731;margin-top:3px}
        @media(max-width:1200px){.lg-grid{grid-template-columns:1fr}.lg-ne-hero{grid-template-columns:1fr}.lg-unit-hierarchy{grid-template-columns:1fr}}
        @media(max-width:700px){.lg-ne-main{padding-top:100px}.lg-unit-hierarchy{grid-template-columns:1fr}.lg-child-unit{align-items:flex-start;flex-direction:column}.lg-child-figures{text-align:left}}

        /* Global date selection and refreshed finance layout */
        .lg-global-period{display:flex;justify-content:space-between;align-items:center;gap:18px;flex-wrap:wrap;padding:16px 20px;margin-bottom:18px}
        .lg-global-period-head strong{display:block;font-size:15px;font-weight:600;color:#302325}
        .lg-global-period-head small{display:block;margin-top:5px;font-size:11px;color:#948485}
        .lg-global-period-controls{display:flex;align-items:end;gap:10px;flex-wrap:wrap}
        .lg-global-period-controls .lg-control{min-width:155px}
        .lg-period-reset{height:38px;border:1px solid #E2D1CF;background:#fff;border-radius:9px;color:#80323D;font-size:11px;font-weight:600;padding:0 14px;cursor:pointer}
        .lg-period-reset:hover{background:#FFF5F3}
        .lg-ne-main .lg-card strong,.lg-ne-main .lg-card h3{font-weight:600}
        .lg-ne-hero{grid-template-columns:minmax(0,1.35fr) minmax(290px,.65fr)}
        .lg-ne-hero>.lg-card{min-height:205px}
        .lg-balance-card{padding:25px 28px}
        .lg-balance-amount{font-size:32px;font-weight:600}
        .lg-ne-hero .lg-side-summary{padding:18px 20px}
        .lg-ne-hero .lg-mini-item{padding:9px 13px}
        .lg-ne-hero .lg-mini-item strong{font-size:13px}
        .lg-ne-main .lg-stat strong{font-size:22px;font-weight:600}
        .lg-allocation-card{padding:9px 12px}
        .lg-allocation-card strong{font-size:17px;font-weight:600}
        .lg-allocation-card p{font-size:10px}
        .lg-period-error{font-size:11px;color:#AA3A46;min-height:14px;flex-basis:100%;text-align:right}
        .lg-pager{display:flex;gap:6px;align-items:center}
        .lg-pager button{width:30px;height:30px;border-radius:7px;border:1px solid #E9D9D7;background:white;color:#65333C;cursor:pointer}
        .lg-pager button:disabled{opacity:.4;cursor:default}
        @media(max-width:850px){.lg-global-period-controls{width:100%}.lg-global-period-controls .lg-field{flex:1}.lg-ne-hero{grid-template-columns:1fr}}
        @media(max-width:600px){.lg-global-period{padding:14px}.lg-global-period-controls .lg-field{flex:1 1 42%}.lg-global-period-controls .lg-control{min-width:100%}.lg-period-reset{width:100%}}

        /* Reporting period — matched to Admin Reports date toolbar */
        .lg-global-period{display:grid;grid-template-columns:minmax(250px,1fr) auto auto;align-items:center;gap:14px;padding:16px 24px;background:#fff;border:1px solid #e4e6ec;border-radius:17px}
        .lg-global-period-head{display:flex;align-items:center;gap:14px;min-width:0}
        .lg-period-calendar{width:46px;height:46px;flex:0 0 46px;border-radius:12px;background:#fbf0f3;display:grid;place-items:center;color:#ae1946}
        .lg-period-calendar svg{width:23px;height:23px}
        .lg-global-period-head strong{font-size:14px;font-weight:600;color:#17243c}
        .lg-global-period-head small{font-size:11px;color:#9b9eb4;margin-top:4px}
        .lg-global-period-controls{display:flex;align-items:center;gap:12px;flex-wrap:nowrap}
        .lg-global-period-controls .lg-field{display:flex;flex-direction:row;align-items:center;gap:9px}
        .lg-global-period-controls .lg-field label{font-size:12px;color:#969bb0;font-weight:600;white-space:nowrap}
        .lg-global-period-controls .lg-control{height:44px;min-width:0;width:160px;border:1px solid #dbe0e9;border-radius:11px;color:#17243c;font-size:13px;background:#fff}
        .lg-period-divider{color:#afb4c0;font-size:17px}
        .lg-period-buttons{display:flex;align-items:center;gap:9px}
        .lg-period-reset,.lg-period-apply,.lg-period-shortcut{height:44px;padding:0 15px;border-radius:11px;white-space:nowrap;font-size:12px;font-weight:600;cursor:pointer;transition:background .15s,border-color .15s}
        .lg-period-reset,.lg-period-shortcut{border:1px solid #e0e3ea;background:#fff;color:#78819a}
        .lg-period-reset:hover,.lg-period-shortcut:hover{background:#fff5f5;border-color:#d6b4bd}
        .lg-period-apply{border:1px solid #9d1240;background:#9d1240;color:#fff}
        .lg-period-apply:hover{background:#7d0e31}
        .lg-period-error{grid-column:1/-1;margin:0;font-size:11px;color:#a43648;text-align:right;min-height:0}
        .lg-period-error:empty{display:none}
        .lg-page-numbers{display:flex;align-items:center;gap:6px}
        .lg-pager .lg-page-numbers button,.lg-pager>button{width:30px;height:30px;min-width:30px;border:1px solid #e5dadd;border-radius:7px;background:#fff;color:#6b6265;font-size:12px;font-weight:500;display:inline-flex;align-items:center;justify-content:center;padding:0;cursor:pointer}
        .lg-pager .lg-page-numbers button:hover:not(:disabled),.lg-pager>button:hover:not(:disabled){background:#fff1f3;border-color:#d5b2bb}
        .lg-pager .lg-page-numbers button.active{background:#7b1b1b;color:#fff;border-color:#7b1b1b}
        .lg-pager button:disabled{opacity:.4;cursor:not-allowed}
        .lg-pager .lg-dots{color:#9a9294;font-size:12px;padding:0 3px}
        @media(max-width:1400px){.lg-global-period{grid-template-columns:1fr}.lg-global-period-controls{flex-wrap:wrap}.lg-period-buttons{justify-content:flex-end}}
        @media(max-width:670px){.lg-global-period{padding:14px}.lg-global-period-controls{display:grid;grid-template-columns:1fr auto 1fr;gap:8px}.lg-global-period-controls .lg-field{flex-direction:column;align-items:flex-start}.lg-global-period-controls .lg-control{width:100%;max-width:none}.lg-period-buttons{width:100%;justify-content:stretch;flex-wrap:wrap}.lg-period-buttons button{flex:1}.lg-table-foot{flex-wrap:wrap}.lg-pager{margin-left:auto}}

        /* Reporting period: icon-only reset at the far right, dates auto-apply. */
        .lg-global-period{grid-template-columns:minmax(235px,1fr) auto 46px}
        .lg-period-buttons{justify-self:end;margin-left:auto}
        /* Exact Admin Registrations reset button: plain icon, no outlined square. */
        .lg-period-reset.lg-reset-icon{
            width:40px;height:38px;min-width:40px;padding:0;
            display:inline-flex;align-items:center;justify-content:center;
            border:0;background:transparent;color:#111827;border-radius:8px;
            box-shadow:none;cursor:pointer;flex:none;
            transition:background-color .2s ease,transform .2s ease;
        }
        .lg-period-reset.lg-reset-icon:hover{background:#FFF0EC;border:0}
        .lg-period-reset.lg-reset-icon:active{transform:scale(.95)}
        .lg-period-reset.lg-reset-icon:focus-visible{outline:2px solid #7B1B1B;outline-offset:2px}
        .lg-reset-symbol{width:18px;height:18px;display:block;transform-origin:center}
        @keyframes lgResetSpin{to{transform:rotate(360deg)}}
        .lg-period-reset.lg-reset-spinning .lg-reset-symbol{animation:lgResetSpin .65s cubic-bezier(.22,.61,.36,1) both}
        @media(max-width:1400px){.lg-global-period{grid-template-columns:minmax(230px,1fr) auto 46px}.lg-period-buttons{width:auto;justify-self:end}}
        @media(max-width:1100px){.lg-global-period{grid-template-columns:1fr auto}.lg-global-period-head{grid-column:1/-1}.lg-global-period-controls{grid-column:1}.lg-period-buttons{grid-column:2;grid-row:2}}
        @media(max-width:670px){.lg-global-period-controls{display:flex;gap:9px;flex-wrap:wrap}.lg-global-period-controls .lg-field{flex:1 1 120px;align-items:flex-start}.lg-global-period-controls .lg-control{width:100%}.lg-period-buttons{display:flex;width:auto;justify-content:flex-end;align-self:end}.lg-period-buttons button{flex:none}.lg-global-period{grid-template-columns:minmax(0,1fr) 46px}}
        @media(prefers-reduced-motion:reduce){.lg-period-reset.lg-reset-spinning .lg-reset-symbol{animation-duration:.01ms}}
    </style>
</head>
<body class="lg-body">
    <x-logistics.sidebar />
    <x-logistics.navbar />

    <main class="lg-ne-main">
        <div class="lg-ne-shell">
            <section class="lg-global-period lg-card" aria-label="Network earnings reporting period">
                <div class="lg-global-period-head">
                    <span class="lg-period-calendar" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 3v4M17 3v4M3 10h18M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01"/></svg></span>
                    <span><strong>Reporting period</strong><small>Select a date range to update every section</small></span>
                </div>
                <div class="lg-global-period-controls">
                    <div class="lg-field"><label for="lgDateFrom">From</label><input class="lg-control" id="lgDateFrom" type="date"></div>
                    <span class="lg-period-divider" aria-hidden="true">—</span>
                    <div class="lg-field"><label for="lgDateTo">To</label><input class="lg-control" id="lgDateTo" type="date"></div>
                </div>
                <div class="lg-period-buttons">
                    <button class="lg-period-reset lg-reset-icon" type="button" id="lgResetDates" aria-label="Reset all filters" title="Reset all filters">
                        <svg class="lg-reset-symbol" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 11a8.1 8.1 0 0 0-15.5-2M4 5v4h4M4 13a8.1 8.1 0 0 0 15.5 2M20 19v-4h-4"/>
                        </svg>
                    </button>
                </div>
                <p class="lg-period-error" id="lgPeriodError" role="status" aria-live="polite"></p>
            </section>

            <section class="lg-ne-hero">
                <article class="lg-balance-card lg-card">
                    <span class="lg-balance-label">Total Network Shipping Earnings</span>
                    <div class="lg-balance-amount" id="lgHeroGross">₱0.00</div>
                    <p class="lg-balance-sub">Shipping income from delivered, completed parcels for the selected period, using the fee recorded for each shipment.</p>
                    <div class="lg-balance-tags"><span class="lg-balance-tag">Completed parcel fees</span><span class="lg-balance-tag">Live database ledger</span></div>
                </article>
                <aside class="lg-side-summary lg-card">
                    <h3>Shipping Fee Ownership</h3>
                    <p>Shipping fees belong to the Logistics Company. Sorting centers and delivery hubs manage parcel operations and COD collections; they do not receive a separate earnings share.</p>
                    <div class="lg-mini-stack">
                        <div class="lg-mini-item"><span>Logistics Company</span><strong>100% of shipping fees</strong></div>
                        <div class="lg-mini-item"><span>Sorting Centers</span><strong>Operational units only</strong></div>
                        <div class="lg-mini-item"><span>Delivery Hubs</span><strong>Collect and distribute COD</strong></div>
                    </div>
                </aside>
            </section>

            <section class="lg-stats">
                <article class="lg-stat lg-card"><div class="lg-stat-top"><span class="lg-stat-kicker">Total Network Earnings</span><span class="lg-stat-pill maroon">Network</span></div><strong id="lgGrossStat">₱0.00</strong><p>Combined shipping fees for the selected period.</p></article>
                <article class="lg-stat lg-card"><div class="lg-stat-top"><span class="lg-stat-kicker">Company Shipping Income</span><span class="lg-stat-pill green">100% fees</span></div><strong id="lgShareStat">₱0.00</strong><p>Company-held shipping fees from the selected parcel records.</p></article>
                <article class="lg-stat lg-card"><div class="lg-stat-top"><span class="lg-stat-kicker">Total Parcels</span><span class="lg-stat-pill orange">Completed</span></div><strong id="lgParcelStat">0</strong><p>Completed parcels credited in this period.</p></article>
                <article class="lg-stat lg-card"><div class="lg-stat-top"><span class="lg-stat-kicker">Average Shipping Fee</span><span class="lg-stat-pill maroon">Per parcel</span></div><strong id="lgAverageFee">₱0.00</strong><p>Average recorded fee for completed parcels.</p></article>
            </section>

            <section class="lg-grid">
                <article class="lg-card">
                    <div class="lg-panel-head">
                        <div>
                            <h2>Shipping Fee Trend</h2>
                            <p>Daily parcel shipping fees in the selected date range.</p>
                        </div>
                        <span class="lg-pill">PHP</span>
                    </div>
                    <div class="lg-chart-wrap">
                        <div class="lg-chart" id="lgEarningsChart"></div>
                    </div>
                </article>
                <aside class="lg-card">
                    <div class="lg-panel-head">
                        <div>
                            <h2>Shipping Fee Ownership</h2>
                            <p>Company-owned shipping income and non-earning operational units.</p>
                        </div>
                    </div>
                    <div class="lg-allocation">
                        <article class="lg-allocation-card"><div class="lg-allocation-top"><h3>Logistics Company</h3><span>Receives fees</span></div><strong id="lgAllocationLogistics">₱0.00</strong><p>All shipping-fee credits recorded for completed parcels.</p></article>
                        <article class="lg-allocation-card"><div class="lg-allocation-top"><h3>Sorting Centers</h3><span>No share</span></div><strong id="lgAllocationSorting">₱0.00</strong><p>Parcel handling and inter-center transfers only.</p></article>
                        <article class="lg-allocation-card"><div class="lg-allocation-top"><h3>Delivery Hubs</h3><span>No share</span></div><strong id="lgAllocationHubs">₱0.00</strong><p>Cash collections and COD distribution only.</p></article>
                    </div>
                </aside>
            </section>

            <section class="lg-card" style="margin-bottom:18px;">
                <div class="lg-panel-head">
                    <div>
                        <h2>Shipping Fee Ledger</h2>
                        <p>Recorded shipping fee for each completed parcel.</p>
                    </div>
                    <span class="lg-pill" id="lgLedgerCount">0 entries</span>
                </div>
                <div class="lg-toolbar">
                    <div class="lg-field lg-search">
                        <label for="lgLedgerSearch">Search</label>
                        <input class="lg-control" id="lgLedgerSearch" type="text" placeholder="Search order ref, route, or status">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"></circle><path d="M20 20l-4-4"></path></svg>
                    </div>
                </div>
                <div class="lg-table-wrap">
                    <table class="lg-table">
                        <thead>
                            <tr>
                                <th>Date &amp; Time</th>
                                <th>Order Reference</th>
                                <th>Route / Source</th>
                                <th>Status</th>
                                <th style="text-align:right;">Amount</th>
                            </tr>
                        </thead>
                        <tbody id="lgLedgerRows"></tbody>
                    </table>
                </div>
                <div class="lg-table-foot">
                    <span id="lgLedgerSummary">Showing 0 entries</span>
                    <div class="lg-pager" aria-label="Transaction history pagination"><button type="button" id="lgPrevPage" aria-label="Previous page">‹</button><div class="lg-page-numbers" id="lgPageNumbers"></div><button type="button" id="lgNextPage" aria-label="Next page">›</button></div>
                </div>
            </section>

            <section class="lg-card">
                <div class="lg-panel-head">
                    <div>
                        <h2>Network Unit Snapshot</h2>
                        <p>Registered delivery hubs are grouped beneath their parent sorting center. Amounts reflect completed parcel fees in the selected period.</p>
                    </div>
                    <span class="lg-pill">Live data</span>
                </div>
                <div class="lg-unit-grid lg-unit-hierarchy" id="lgNetworkSnapshot"></div>
            </section>
        </div>
    </main>

    <script>
        (() => {
            const body = document.body;
            const sidebarToggle = document.getElementById('lgSidebarToggle');
            const profileButton = document.getElementById('lgProfileButton');
            const profileMenu = document.getElementById('lgProfileMenu');
            const notifyButton = document.getElementById('lgNotifyButton');
            const changeProfileButton = document.getElementById('lgChangeProfilePhoto');
            const profilePhotoInput = document.getElementById('lgProfilePhotoInput');
            const chartElement = document.getElementById('lgEarningsChart');
            const ledgerRows = document.getElementById('lgLedgerRows');
            const ledgerCount = document.getElementById('lgLedgerCount');
            const ledgerSummary = document.getElementById('lgLedgerSummary');
            const searchInput = document.getElementById('lgLedgerSearch');
            const dateFrom = document.getElementById('lgDateFrom');
            const dateTo = document.getElementById('lgDateTo');
            const periodError = document.getElementById('lgPeriodError');
            const pageNumbers = document.getElementById('lgPageNumbers');
            const prevBtn = document.getElementById('lgPrevPage');
            const nextBtn = document.getElementById('lgNextPage');
            let transactions = [];
            let networkUnits = [];
            const split = {logistics:1, sorting:0, hubs:0};
            const pageSize = 12;
            let page = 1;
            let periodRows = [];

            const peso = value => new Intl.NumberFormat('en-PH',{style:'currency',currency:'PHP'}).format(value);
            const number = value => new Intl.NumberFormat('en-PH').format(value);
            const byId = id => document.getElementById(id);
            const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
            function prettyDate(value) {
                const date = new Date(value.replace(' ','T'));
                return Number.isNaN(date.getTime()) ? value : new Intl.DateTimeFormat('en-PH',{month:'short',day:'2-digit',year:'numeric',hour:'numeric',minute:'2-digit'}).format(date);
            }
            // Today is evaluated in Asia/Manila, not the browser's timezone.
            const manilaParts = Object.fromEntries(new Intl.DateTimeFormat('en-US',{timeZone:'Asia/Manila',year:'numeric',month:'2-digit',day:'2-digit'}).formatToParts(new Date()).filter(p=>p.type!=='literal').map(p=>[p.type,p.value]));
            const isoToday = `${manilaParts.year}-${manilaParts.month}-${manilaParts.day}`;
            function setThisMonth() {
                dateFrom.value = isoToday.slice(0,7) + '-01';
                dateTo.value = isoToday;
                page = 1;
                updateAll();
            }
            function selectedPeriod() {
                if (!dateFrom.value || !dateTo.value || dateFrom.value > dateTo.value) {
                    periodError.textContent = 'Select a valid From and To date.';
                    return null;
                }
                periodError.textContent = '';
                return true;
            }
            async function updateAll() {
                const rows = selectedPeriod();
                if (!rows) return;
                chartElement.innerHTML = '<div class="lg-empty">Loading earnings…</div>';
                try {
                    const response = await fetch(`{{ route('logistics.earnings.data') }}?from=${encodeURIComponent(dateFrom.value)}&to=${encodeURIComponent(dateTo.value)}`, {headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'}});
                    if (!response.ok) throw new Error('Could not load earnings. Please refresh and try again.');
                    const data = await response.json();
                    transactions = data.transactions || [];
                    networkUnits = data.units || [];
                    periodRows = transactions;
                    const gross = Number(data.summary?.gross || 0);
                    const parcels = Number(data.summary?.parcels || 0);
                byId('lgHeroGross').textContent = peso(gross);
                byId('lgGrossStat').textContent = peso(gross);
                byId('lgShareStat').textContent = peso(gross);
                byId('lgParcelStat').textContent = number(parcels);
                byId('lgAllocationLogistics').textContent = peso(gross);
                byId('lgAllocationSorting').textContent = peso(0);
                byId('lgAllocationHubs').textContent = peso(0);
                byId('lgAverageFee').textContent = peso(data.summary?.shipping_fee || 0);
                renderChart(data.daily || []);
                renderNetwork(networkUnits);
                renderLedger();
                } catch (error) {
                    periodError.textContent = error.message;
                    chartElement.innerHTML = '<div class="lg-empty">Earnings data could not be loaded.</div>';
                    ledgerRows.innerHTML = '<tr><td colspan="5" class="lg-empty">Earnings data could not be loaded.</td></tr>';
                }
            }
            function renderLedger() {
                const q = searchInput.value.trim().toLowerCase();
                const rows = periodRows.filter(row => [row.reference,row.route,row.status,row.center,row.hub].join(' ').toLowerCase().includes(q))
                    .sort((a,b)=>b.date.localeCompare(a.date));
                const totalPages = Math.max(1,Math.ceil(rows.length/pageSize));
                page = Math.max(1,Math.min(page,totalPages));
                const current = rows.slice((page-1)*pageSize,page*pageSize);
                ledgerRows.innerHTML = current.length ? current.map(row=>`<tr>
                    <td><strong>${escapeHtml(prettyDate(row.date))}</strong><small>Completed parcel</small></td>
                    <td><strong>${escapeHtml(row.reference)}</strong><small>ShopEase order</small></td>
                    <td><strong>${escapeHtml(row.route)}</strong><small>${escapeHtml(row.center)}</small></td>
                    <td><span class="lg-status credited">Credited</span></td>
                    <td style="text-align:right" class="lg-amount pos">+${peso(row.amount)}</td>
                </tr>`).join('') : '<tr><td colspan="5" class="lg-empty">No completed parcels match this search or period.</td></tr>';
                ledgerCount.textContent = `${number(rows.length)} entries`;
                ledgerSummary.textContent = rows.length ? `Showing ${(page-1)*pageSize+1}–${Math.min(page*pageSize,rows.length)} of ${number(rows.length)}` : 'Showing 0 entries';
                const pages = new Set([1,totalPages,page-1,page,page+1]);
                const visiblePages = [...pages].filter(n=>n>=1&&n<=totalPages).sort((a,b)=>a-b);
                let last = 0;
                pageNumbers.replaceChildren();
                visiblePages.forEach(n => {
                    if (last && n-last>1) {
                        const dots=document.createElement('span');dots.className='lg-dots';dots.textContent='…';pageNumbers.appendChild(dots);
                    }
                    const button=document.createElement('button');button.type='button';
                    button.textContent=String(n);button.classList.toggle('active',n===page);
                    if(n===page)button.setAttribute('aria-current','page');
                    button.setAttribute('aria-label',`Page ${n}`);
                    button.addEventListener('click',()=>{page=n;renderLedger();});
                    pageNumbers.appendChild(button);last=n;
                });
                prevBtn.disabled = page===1;
                nextBtn.disabled = page===totalPages;
            }
            function renderNetwork(units) {
                const target = byId('lgNetworkSnapshot');
                target.innerHTML = units.length ? units.map(center => {
                    const hubs = center.hubs.map(hub=>{
                        return `<div class="lg-child-unit"><div class="lg-child-identity"><span class="lg-tree-line"></span><div><small>DELIVERY HUB · UNDER ${escapeHtml(center.name)}</small><strong>${escapeHtml(hub.name)}</strong></div></div><div class="lg-child-figures"><span>${number(hub.parcels)} parcels</span><strong>${peso(hub.amount)}</strong></div></div>`;
                    }).join('');
                    return `<article class="lg-unit-card lg-parent-unit"><div class="lg-unit-top"><div><span class="lg-parent-caption">SORTING CENTER</span><h3>${escapeHtml(center.name)}</h3></div><span>${center.hubs.length} delivery hubs</span></div><div class="lg-unit-metrics lg-parent-metrics"><div><small>Completed parcels</small><strong>${number(center.parcels)}</strong></div><div><small>Shipping fee volume</small><strong>${peso(center.amount)}</strong></div></div><div class="lg-child-list">${hubs}</div></article>`;
                }).join('') : '<div class="lg-empty">No sorting center or hub earnings in this period.</div>';
            }
            function renderChart(daily) {
                const days = daily.map(row=>[row.date,Number(row.amount)]).sort((a,b)=>a[0].localeCompare(b[0]));
                const data = days.length > 15 ? days.filter((_,i)=>i % Math.ceil(days.length/15)===0 || i===days.length-1) : days;
                if (!data.length) {chartElement.innerHTML='<div class="lg-empty">No completed parcels in the selected period.</div>';return;}
                const maxValue=Math.max(50,...data.map(x=>x[1]));
                const grid=document.createElement('div');grid.className='lg-chart-grid';
                for(let i=0;i<4;i++){
                    const line=document.createElement('div');line.className='lg-chart-line';
                    line.style.top=`${i/3*100}%`;
                    const label=document.createElement('label');label.textContent=peso(maxValue*(1-i/3)).replace('.00','');
                    line.appendChild(label);grid.appendChild(line);
                }
                chartElement.replaceChildren(grid);
                data.forEach(([date,amount],i)=>{
                    const col=document.createElement('div');col.className='lg-bar-col';
                    const bar=document.createElement('div');bar.className='lg-bar';
                    const target=`${Math.max(3,amount/maxValue*100)}%`;
                    bar.style.height='0%';bar.style.setProperty('--target-height',target);
                    bar.style.animation=`lgRise 750ms ${i*55}ms ease-out forwards`;
                    const tooltip=document.createElement('div');tooltip.className='lg-tooltip';
                    const labelDate=new Date(date+'T12:00:00');
                    const shortDate=new Intl.DateTimeFormat('en-PH',{month:'short',day:'2-digit'}).format(labelDate);
                    tooltip.innerHTML=`<strong>${shortDate}</strong>${peso(amount)}`;
                    bar.appendChild(tooltip);
                    const label=document.createElement('span');label.className='lg-bar-date';label.textContent=shortDate;
                    col.append(bar,label);chartElement.appendChild(col);
                });
            }
            if(!byId('lgRiseStyle')){
                const style=document.createElement('style');style.id='lgRiseStyle';
                style.textContent='@keyframes lgRise{from{height:0%}to{height:var(--target-height)}}';
                document.head.appendChild(style);
            }
            searchInput.addEventListener('input',()=>{page=1;renderLedger();});
            // Update every section immediately when either date changes; no Apply button or reload.
            for (const dateInput of [dateFrom,dateTo]) {
                dateInput.addEventListener('change',()=>{page=1;updateAll();});
                dateInput.addEventListener('keydown',event=>{if(event.key==='Enter'){event.preventDefault();page=1;updateAll();}});
            }
            const resetButton = byId('lgResetDates');
            resetButton.addEventListener('click',()=>{
                resetButton.classList.remove('lg-reset-spinning');
                // Restart the rotation on every click, even repeated quick clicks.
                void resetButton.offsetWidth;
                resetButton.classList.add('lg-reset-spinning');
                // Clear the ledger search and pagination as well as the global date range.
                searchInput.value = '';
                page = 1;
                setThisMonth();
            });
            resetButton.addEventListener('animationend',event=>{
                if(event.animationName==='lgResetSpin') resetButton.classList.remove('lg-reset-spinning');
            });
            prevBtn.addEventListener('click',()=>{page--;renderLedger();});
            nextBtn.addEventListener('click',()=>{page++;renderLedger();});
            setThisMonth();

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', () => {
                    body.classList.toggle('lg-collapsed');
                    sidebarToggle.setAttribute('aria-expanded', String(!body.classList.contains('lg-collapsed')));
                });
            }

            if (profileButton && profileMenu) {
                profileButton.addEventListener('click', () => {
                    const hidden = !profileMenu.hidden;
                    profileMenu.hidden = hidden;
                    profileButton.setAttribute('aria-expanded', String(!hidden));
                });
                document.addEventListener('click', (event) => {
                    if (!event.target.closest('.lg-profile-wrap')) {
                        profileMenu.hidden = true;
                        profileButton.setAttribute('aria-expanded', 'false');
                    }
                });
            }

            if (notifyButton) {
                notifyButton.addEventListener('click', () => window.alert('No live notifications connected yet.'));
            }

            if (changeProfileButton && profilePhotoInput) {
                changeProfileButton.addEventListener('click', () => profilePhotoInput.click());
            }
        })();
    </script>
</body>
</html>
