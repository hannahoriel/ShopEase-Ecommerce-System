{{-- ShopEase — Admin Account | Profile + Privacy + Notifications --}}
@php
 $adminUser = auth()->user();
 $adminProfile = $adminProfile ?? null;
 $adminName = trim((string) data_get($adminUser,'name',''));
 $adminRole = $adminRole ?? (data_get($adminUser,'role') ?: 'admin');
 $roleLabel = $roleLabel ?? ucwords(str_replace(['_','-'],' ',(string)$adminRole));
 $adminId = $adminId ?? ('ADM-'.str_pad((string)data_get($adminUser,'id','0'),5,'0',STR_PAD_LEFT));
 $adminPhoto = $adminPhoto ?? null;
 $adminCover = $adminCover ?? null;
 $adminNotificationOptions = $adminNotificationOptions ?? [
   'registrations'=>['New Registrations','New user registration submissions requiring review'],
   'seller_compliance'=>['Seller Compliance','Permit and category change requests, and seller warnings'],
   'complaints'=>['Complaints & Disputes','New cases and updates to active disputes'],
   'messages'=>['Messages','New messages and internal conversations'],
   'platform'=>['Platform Announcements','Maintenance and important platform notices'],
 ];
 $adminPreferences = $adminPreferences ?? [];
 $nameParts = preg_split('/\s+/', $adminName, 2);
 $adminFirst = old('first_name', data_get($adminProfile,'first_name') ?: ($nameParts[0] ?? ''));
 $adminLast = old('last_name', data_get($adminProfile,'last_name') ?: ($nameParts[1] ?? ''));
 $adminBirthday = old('birthday',data_get($adminProfile,'birthday'));
 try { $adminBirthday = $adminBirthday ? \Carbon\Carbon::parse($adminBirthday)->format('Y-m-d') : ''; } catch (\Throwable $e) { $adminBirthday = ''; }
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><meta name="csrf-token" content="{{ csrf_token() }}">
<title>Admin Account — ShopEase</title>
<script>
// Keep the Account page at the beginning of the document on reload.
if ('scrollRestoration' in history) history.scrollRestoration = 'manual';
</script>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
@vite(['resources/css/app.css', 'resources/js/app.js'])
<style>

        .seller-account-page,.seller-account-page *{box-sizing:border-box}
        .seller-account-page{font-family:'Poppins',sans-serif;background:#fcf8f6;min-height:100vh;margin-left:270px;padding:120px 25px 44px 20px;color:#182033;transition:margin-left .25s ease}
        .seller-account-page button,.seller-account-page input,.seller-account-page select{font-family:inherit}
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
        #sellerAccountPage #saAvatar{width:124px!important;height:124px!important;min-width:124px;min-height:124px;max-width:124px;max-height:124px;aspect-ratio:1/1;border-radius:50%!important;flex-shrink:0;box-sizing:border-box;}
        #sellerAccountPage #saAvatar>img{display:block;width:100%!important;height:100%!important;min-width:0;min-height:0;aspect-ratio:1/1;object-fit:cover!important;object-position:center;border-radius:50%!important;}
        @media(max-width:760px){#sellerAccountPage #saAvatar{width:98px!important;height:98px!important;min-width:98px;min-height:98px;max-width:98px;max-height:98px;}}
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
        @media(max-width:760px){.seller-account-page{margin-left:0!important;padding:106px 12px 32px}.sa-cover{height:140px}.sa-shop-strip{padding-left:114px;gap:8px;min-height:83px}.sa-avatar{left:10px;top:-40px;width:98px;height:98px}.sa-shop-name{font-size:16px}.sa-section{padding:12px}.sa-business-row{grid-template-columns:1fr}}
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

    
/* ADMIN ACCOUNT: identical seller visual language, admin shell offsets */
.seller-account-page{margin-left:288px;padding:128px 26px 46px 24px;background:#fcf8f6}
.sa-navigation{min-height:570px}
.sa-workspace[hidden],.sa-modal-overlay[hidden]{display:none!important}
.sa-section-icon,.sa-section-icon.purple,.sa-section-icon.red,.sa-settings-emblem{background:none!important;border:0!important;padding:0;color:inherit}
.sa-section-icon{width:27px;height:27px}
.sa-section-icon img,.sa-settings-emblem img{width:26px;height:26px;object-fit:contain;display:block}
.sa-settings-emblem{width:32px;height:32px;display:grid;place-items:center}
.sa-avatar{overflow:visible}
.sa-avatar img{width:100%!important;height:100%!important;object-fit:cover;aspect-ratio:1;border-radius:50%}
.sa-preview-only{font-size:11px;color:#9a6d28;margin:8px 0 0;line-height:1.7}
.sa-readonly-info{background:#f6f6f7!important;color:#697383!important}
.sa-notification-role{font-size:11px;color:#9d1239;background:#fff0f3;padding:6px 12px;border-radius:99px}
.sa-toast{position:fixed;top:110px;right:25px;z-index:3000;max-width:420px;padding:14px 18px;border-radius:10px;border:1px solid #b9dec2;background:#effaf1;color:#246238;font:500 12px Poppins,sans-serif;box-shadow:0 9px 25px #0002}
.sa-toast.error{background:#fff4e9;border-color:#ecd2ad;color:#8c5425}
.sa-toast[hidden]{display:none!important}
.sa-otp-backdrop{position:fixed;inset:0;background:rgba(22,12,12,.55);z-index:2500;display:grid;place-items:center;padding:18px}
.sa-otp-backdrop[hidden]{display:none!important}
.sa-otp-card{position:relative;width:min(480px,100%);max-height:calc(100vh - 36px);overflow-y:auto;border-radius:22px;background:white;text-align:center;padding:38px 32px 30px;box-shadow:0 20px 55px #0003}
.sa-otp-icon{width:88px;height:88px;margin:0 auto 20px;border-radius:50%;background:#ffe1dc;display:grid;place-items:center}
.sa-otp-icon img{width:48px;height:48px;object-fit:contain}
.sa-otp-title{margin:0;font-size:24px;color:#111;font-weight:700}
.sa-otp-subtitle{margin:8px 0 27px;font-size:15px;color:#929292}
.sa-otp-row{display:flex;justify-content:center;gap:10px;margin-bottom:24px}
.sa-otp-cell{width:52px;height:62px;border:1.5px solid #6d6d6d;border-radius:11px;text-align:center;font:600 23px Poppins,sans-serif;outline:none}
.sa-otp-cell:focus{border-color:#7b1b1b;box-shadow:0 0 0 3px #7b1b1b18}
.sa-otp-notice{background:#ffe8e4;padding:12px;border-radius:11px;text-align:left;color:#6e1717;font-size:12px;line-height:1.65;margin-bottom:18px}
.sa-otp-divider{display:flex;align-items:center;gap:14px;color:#929292;margin:0 8px 15px}
.sa-otp-divider:before,.sa-otp-divider:after{content:'';height:1px;flex:1;background:#bdbdbd}
.sa-otp-resend{border:0;background:none;color:#a52a2a;cursor:pointer;font:500 14px Poppins,sans-serif;margin-bottom:18px}
.sa-otp-submit{display:block;width:100%;height:48px;border:0;border-radius:24px;background:#a52a2a;color:white;font:600 16px Poppins,sans-serif;cursor:pointer}
.sa-otp-submit:hover{background:#8f211f}
.sa-otp-error{color:#a52a2a;font-size:11px;margin:0 0 12px}
.sa-otp-close{position:absolute;top:14px;right:17px;border:0;background:#f7f2ef;border-radius:50%;width:32px;height:32px;font-size:21px;cursor:pointer}
.sa-password-eye{position:relative}.sa-password-eye input{padding-right:60px}
.sa-password-toggle{position:absolute;right:9px;top:29px;border:0;background:none;color:#9b1239;font-size:10px;cursor:pointer}
@media(max-width:760px){.seller-account-page{margin-left:0!important;padding:108px 12px 30px}.sa-otp-card{padding:29px 19px 24px}.sa-otp-row{gap:6px}.sa-otp-cell{width:40px;height:53px;font-size:20px}}



/* Seller-matched photo edit icons, perfect circular avatar and preview */
#sellerAccountPage #saAvatar { width:124px!important;height:124px!important;min-width:124px!important;min-height:124px!important;max-width:124px!important;max-height:124px!important;aspect-ratio:1/1;border-radius:50%!important;flex-shrink:0;position:absolute;overflow:visible; }
#sellerAccountPage #saAvatarImage {width:100%!important;height:100%!important;object-fit:cover!important;border-radius:50%!important;display:block;}
#sellerAccountPage .sa-avatar-camera {right:2px;bottom:8px;width:28px;height:28px;display:grid;place-items:center;border:3px solid #fff;border-radius:50%;background:#485467;color:#fff;z-index:5;cursor:pointer;}
#sellerAccountPage .sa-avatar-camera svg {width:14px;height:14px;stroke:currentColor;}
#sellerAccountPage .sa-cover-button svg {width:15px;height:15px;stroke:currentColor;}
#sellerAccountPage .sa-section-icon img {width:26px;height:26px;object-fit:contain;}
.aa-photo-preview[hidden]{display:none!important}
.aa-photo-preview {position:fixed;inset:0;z-index:3000;background:rgba(17,19,25,.65);display:grid;place-items:center;padding:22px}
.aa-photo-preview-card {position:relative;max-width:min(820px,100%);max-height:90vh;background:white;border-radius:15px;padding:22px;overflow:auto;text-align:center;box-shadow:0 18px 60px #0003}
.aa-photo-preview-card img {display:block;max-width:100%;max-height:75vh;object-fit:contain;border-radius:8px;margin:auto}
.aa-photo-preview-card h3 {font:600 16px Poppins,sans-serif;margin:0 38px 16px 0;text-align:left}
.aa-photo-close {position:absolute;top:12px;right:12px;width:32px;height:32px;border:0;border-radius:50%;font-size:22px;cursor:pointer;background:#f6f3f3}
#sellerAccountPage .sa-cover img,#sellerAccountPage .sa-avatar>img {cursor:zoom-in}
/* Match Seller password reset OTP modal, no actual verification in UI-only stage */
.sa-otp-backdrop {background:rgba(22,12,12,.55);backdrop-filter:blur(2px)}
.sa-otp-card {border-radius:22px;padding:40px 32px 30px;box-shadow:0 20px 55px rgba(0,0,0,.2);width:min(480px,100%)}
.sa-otp-icon {background:#ffe1dc;width:88px;height:88px;margin-bottom:20px}
.sa-otp-title {font-size:24px;font-weight:700}
.sa-otp-subtitle {font-size:16px;line-height:1.4;margin:8px 0 28px}
.sa-otp-row {gap:10px;margin-bottom:25px}
.sa-otp-cell {width:52px;height:62px;border-radius:11px}
.sa-otp-notice {background:#ffe8e4;color:#6e1717;border-radius:11px;min-height:48px;padding:12px;margin-bottom:22px}
.sa-otp-submit {height:48px;border-radius:24px;background:#a52a2a;color:#fff;font:600 16px Poppins,sans-serif}
.sa-otp-submit:hover {background:#8f211f}
@media(max-width:760px){#sellerAccountPage #saAvatar{width:98px!important;height:98px!important;min-width:98px!important;min-height:98px!important;max-width:98px!important;max-height:98px!important}.sa-otp-card{padding:32px 22px 25px}.sa-otp-cell{width:40px;height:54px}.sa-otp-row{gap:6px}}


/* Seller-identical email verification modal */
.email-verification-card{position:relative;width:100%;max-width:480px;max-height:calc(100vh - 32px);overflow-y:auto;background:#fff;border-radius:22px;padding:40px 32px 30px;text-align:center;box-shadow:0 20px 55px rgba(0,0,0,.2);font-family:Poppins,sans-serif;box-sizing:border-box}
.email-verification-icon-wrapper{width:88px;height:88px;margin:0 auto 20px;border-radius:50%;background:#ffe1dc;display:flex;align-items:center;justify-content:center}
.email-verification-icon{width:48px;height:48px;object-fit:contain}
.email-verification-title{margin:0;font-size:24px;line-height:1.2;font-weight:700;color:#000}
.email-verification-subtitle{margin:8px 0 28px;font-size:16px;line-height:1.4;font-weight:400;color:#919191}
.email-verification-subtitle strong{color:#6e1717;font-weight:600}
.email-code-container{display:flex;justify-content:center;gap:10px;margin-bottom:25px}
.email-code-input{width:52px;height:62px;border:1.5px solid #6d6d6d;border-radius:11px;background:#fff;text-align:center;font:600 23px Poppins,sans-serif;color:#161616;outline:0;transition:border-color .2s,box-shadow .2s;box-sizing:border-box}
.email-code-input:focus{border-color:#7b1b1b;box-shadow:0 0 0 3px rgba(123,27,27,.08)}
.email-spam-message{width:100%;min-height:48px;padding:10px 12px;margin-bottom:22px;border-radius:11px;background:#ffe8e4;display:flex;align-items:center;text-align:left;color:#6e1717;font-size:13px;font-weight:400;box-sizing:border-box}
.email-spam-icon{width:19px;height:19px;margin-right:9px;flex-shrink:0}
.email-divider{display:flex;align-items:center;gap:14px;margin:0 10px 20px}
.email-divider-line{flex:1;height:1px;background:#bdbdbd}
.email-divider-text{font-size:17px;color:#929292;font-weight:400}
.email-resend-button{border:0;background:transparent;padding:0;margin-bottom:24px;display:inline-flex;align-items:center;justify-content:center;gap:6px;font:400 15px Poppins,sans-serif;color:#a52a2a;cursor:pointer}
.email-resend-button:hover{color:#7b1b1b}.email-resend-icon{width:18px;height:18px}
.email-verify-button{width:100%;height:48px;border:0;border-radius:24px;background:#a52a2a;color:#fff;font:600 16px Poppins,sans-serif;cursor:pointer;transition:background .2s,transform .1s,box-shadow .2s}
.email-verify-button:hover{background:#8f211f;box-shadow:0 5px 14px rgba(123,27,27,.18)}
.email-verify-button:active{transform:scale(.98)}
.sa-security-dialog{width:min(650px,100%);max-width:490px;text-align:center}
.sa-security-dialog .sa-settings-form{text-align:left;margin-top:15px}
.sa-otp-verify{width:100%;margin-top:8px}
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


/* Super Admin-issued ID — view only */
.aa-admin-id-upload{grid-column:span 6}
.aa-id-upload-content{display:flex;align-items:center;gap:13px;min-height:72px;border:1px solid #e5e8ed;border-radius:9px;padding:10px 12px;background:#fff}
.aa-id-thumbnail{width:80px;height:62px;padding:0;flex:0 0 80px;overflow:hidden;border:1px solid #d8dbe1;border-radius:7px;background:#f7f6f5;cursor:zoom-in}
.aa-id-thumbnail[hidden]{display:none!important}
.aa-id-thumbnail img{display:block;width:100%;height:100%;object-fit:contain}
.aa-id-upload-details{display:flex;flex-direction:column;gap:6px;min-width:0;align-items:flex-start}
.aa-id-upload-details .sa-photo-button{padding:8px 11px;font-size:11px}
#aaAdminIdPhotoFilename{font-size:10px;color:#9199a6;overflow-wrap:anywhere}
@media(max-width:530px){.aa-admin-id-upload{grid-column:span 2}}
/* =========================================================
   ADMIN ACCOUNT LAYOUT — stable on initial load and reload
   Sidebar: w-72 (288px) expanded, w-20 (80px) collapsed.
   CSS drives both states before DOMContentLoaded.
   ========================================================= */
#sellerAccountPage.seller-account-page {
    width: auto;
    min-width: 0;
    max-width: none;
    margin: 0 0 0 288px !important;
    padding: 128px 24px 44px 20px;
    transition: margin-left .3s ease;
}
body:has(#admin-sidebar.w-20) #sellerAccountPage.seller-account-page,
body:has(#admin-sidebar.collapsed) #sellerAccountPage.seller-account-page {
    margin-left: 80px !important;
}
#sellerAccountPage .sa-layout {
    width: 100%;
    max-width: none;
    margin: 0;
    grid-template-columns: 284px minmax(0, 1fr);
    gap: 24px;
}
#sellerAccountPage .sa-navigation {
    min-width: 0;
    min-height: 710px;
    top: 126px;
}
#sellerAccountPage .sa-workspace { min-width: 0; }
@media (max-width: 1280px) {
    #sellerAccountPage .sa-layout {grid-template-columns: 230px minmax(0,1fr);gap:18px;}
}
@media (max-width: 1020px) {
    #sellerAccountPage .sa-layout {grid-template-columns: 1fr;gap:16px;}
    #sellerAccountPage .sa-navigation {position:static;min-height:0;}
}
@media (max-width: 760px) {
    #sellerAccountPage.seller-account-page,
    body:has(#admin-sidebar.w-20) #sellerAccountPage.seller-account-page,
    body:has(#admin-sidebar.collapsed) #sellerAccountPage.seller-account-page {
        margin-left: 0 !important;
        padding: 108px 12px 30px;
    }
}

/* FINAL: identical Seller Compliance content frame and sidebar timing. */
@media (min-width:901px){
 html body #sellerAccountPage.seller-account-page{box-sizing:border-box!important;width:auto!important;min-width:0!important;max-width:none!important;margin-left:260px!important;padding-left:25px!important;padding-right:25px!important;transition:margin-left 300ms ease!important}
 html body:has(#admin-sidebar.w-20) #sellerAccountPage.seller-account-page,
 html body:has(#admin-sidebar.collapsed) #sellerAccountPage.seller-account-page,
 html body.sidebar-collapsed #sellerAccountPage.seller-account-page{margin-left:80px!important}
}
@media (min-width:761px) and (max-width:900px){html body #sellerAccountPage.seller-account-page{width:auto!important;max-width:none!important;margin-left:80px!important;padding-left:24px!important;padding-right:24px!important;transition:margin-left 300ms ease!important}}
@media (max-width:760px){html body #sellerAccountPage.seller-account-page{width:auto!important;max-width:none!important;margin-left:0!important;padding-left:16px!important;padding-right:16px!important}}
#sellerAccountPage .sa-layout{width:100%!important;max-width:none!important;margin-left:0!important;margin-right:0!important}

</style>
</head>
<body class="m-0 p-0 bg-[#FCF8F6]">
@section('page-title', 'My Account')
@include('components.admin.sidebar')
@include('components.admin.navbar')
<main class="seller-account-page" id="sellerAccountPage">
  <div class="sa-layout">
   <aside class="sa-navigation" aria-label="Admin account tabs">
    <div class="sa-nav-heading"><svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="7" r="4"/><path d="M4 21v-2a8 8 0 0 1 16 0v2H4Z"/></svg>My Account</div>
    <nav class="sa-nav-list">
      <a class="sa-nav-link active" href="#profile" data-admin-tab="profile" aria-current="page">Profile</a>
      <a class="sa-nav-link" href="#privacy" data-admin-tab="privacy">Privacy Settings</a>
      <a class="sa-nav-link" href="#notifications" data-admin-tab="notifications">Notification Settings</a>
    </nav>
   </aside>
   <div class="sa-workspace sa-tab-panel" id="profile" data-admin-panel="profile">
    <h1 class="sa-page-title">Profile</h1><p class="sa-intro">Manage your administrator information, identity and profile photos.</p>
    <form id="adminProfileForm" method="POST" action="{{ route('admin.account.update') }}" enctype="multipart/form-data">
     @csrf @method('PUT')
     <div class="sa-cover" id="saCover">
      <img id="saCoverImage" src="{{ $adminCover ?: '' }}" alt="Admin cover photo" @if(!$adminCover) style="display:none" @endif>
      <label class="sa-photo-button sa-cover-button" for="saCoverInput"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 7h4l2-3h4l2 3h4v13H4z"/><circle cx="12" cy="13" r="3"/></svg> Change Cover Photo</label>
      <input id="saCoverInput" type="file" name="cover_photo" accept="image/jpeg,image/png,image/webp" hidden>
     </div>
     <div class="sa-shop-strip">
      <div class="sa-avatar" id="saAvatar">
       <img id="saAvatarImage" src="{{ $adminPhoto ?: '' }}" alt="Admin profile photo" @if(!$adminPhoto) style="display:none" @endif>
       <span id="saAvatarFallback" class="sa-avatar-fallback" @if($adminPhoto) hidden @endif>{{ strtoupper(substr($adminName ?: 'A',0,1)) }}</span>
       <label class="sa-avatar-camera" for="saAvatarInput" title="Change profile photo"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 7h4l2-3h4l2 3h4v13H4z"/><circle cx="12" cy="13" r="3"/></svg></label>
      </div>
      <div><h2 class="sa-shop-name">{{ $adminName ?: 'Administrator' }}</h2><p class="sa-shop-caption">{{ $roleLabel }} · ShopEase Administration</p>
       <label class="sa-photo-button sa-change-avatar" for="saAvatarInput">Change Profile Photo</label><input id="saAvatarInput" type="file" name="profile_photo" accept="image/jpeg,image/png,image/webp" hidden></div>
     </div>
     <div class="sa-form-stack">
      <section class="sa-section">
       <div class="sa-section-heading"><span class="sa-section-icon"><img src="{{ asset('icons/seller/account/personal.png') }}" alt=""></span><div><h3 class="sa-section-title">Personal Information</h3><p class="sa-section-subtitle">Your name and personal contact details.</p></div></div>
       <div class="sa-fields">
        <div class="sa-field half"><label for="aaFirst">First Name <span class="sa-required">*</span></label><input id="aaFirst" class="sa-control" name="first_name" value="{{ $adminFirst }}" required></div>
        <div class="sa-field half"><label for="aaLast">Last Name <span class="sa-required">*</span></label><input id="aaLast" class="sa-control" name="last_name" value="{{ $adminLast }}" required></div>
        <div class="sa-field"><label for="aaBirthday">Birthday</label><input id="aaBirthday" type="date" name="birthday" class="sa-control" max="{{ now()->toDateString() }}" value="{{ $adminBirthday }}"></div>
        <div class="sa-field"><label for="aaAge">Age</label><input id="aaAge" class="sa-control" readonly placeholder="Auto generated"></div>
        <div class="sa-field"><label for="aaSex">Sex</label><select id="aaSex" class="sa-control" name="sex"><option value="">Select sex</option>@foreach(['Male','Female','Prefer not to say'] as $sex)<option value="{{ $sex }}" @selected(old('sex',data_get($adminProfile,'sex'))===$sex)>{{ $sex }}</option>@endforeach</select></div>
        <div class="sa-field half"><label for="aaPhone">Contact Number</label><div class="sa-phone"><span class="sa-phone-prefix">+63</span><input id="aaPhone" type="tel" name="contact_number" value="{{ old('contact_number',data_get($adminProfile,'contact_number','')) }}" placeholder="9XXXXXXXXX" inputmode="numeric"></div></div>
        <div class="sa-field half"><label for="aaEmail">Email Address</label><input id="aaEmail" type="email" class="sa-control sa-readonly-info" value="{{ data_get($adminUser,'email') }}" readonly></div>
       </div>
      </section>
      <section class="sa-section">
       <div class="sa-section-heading"><span class="sa-section-icon"><img src="{{ asset('icons/admin/account/admin.png') }}" alt=""></span><div><h3 class="sa-section-title">Administrative Information</h3><p class="sa-section-subtitle">Your system identity and assigned access role.</p></div></div>
       <div class="sa-fields">
        <div class="sa-field half"><label for="aaAdminId">Admin ID</label><input id="aaAdminId" class="sa-control sa-readonly-info" value="{{ $adminId }}" readonly></div>
        <div class="sa-field half"><label for="aaRole">Assigned Role</label><input id="aaRole" class="sa-control sa-readonly-info" value="{{ $roleLabel }}" readonly><p class="sa-request-hint">Role assignments will be managed through admin access controls in a future update.</p></div>
        <div class="sa-field half aa-admin-id-upload">
         <label class="sa-field-label">Admin ID Photo</label>
         @if(!empty($adminIdPhoto))
          <div class="aa-id-upload-content">
           <button type="button" id="aaAdminIdPhotoPreviewButton" class="aa-id-thumbnail" aria-label="View Super Admin-uploaded ID photo">
            <img id="aaAdminIdPhotoImage" src="{{ $adminIdPhoto }}" alt="Admin ID uploaded by Super Admin">
           </button>
           <div class="aa-id-upload-details">
            <span style="font-size:11px;color:#303b4e;font-weight:500">Admin ID on file</span>
            <span style="font-size:10px;color:#9199a6">Uploaded by Super Admin · Click the photo to view</span>
           </div>
          </div>
         @else
          <div class="aa-id-upload-content" role="status" style="font-size:11px;color:#8b94a4">No Admin ID uploaded by Super Admin.</div>
         @endif
         <p class="sa-request-hint">Admin ID documents are managed by the Super Admin and cannot be changed here.</p>
        </div>

       </div>
      </section>
     </div>
     <div class="sa-actions"><button class="sa-action primary" type="submit" id="aaSaveProfile" disabled>Save Changes</button></div>
    </form>
   </div>
   <div class="sa-workspace sa-tab-panel" id="privacy" data-admin-panel="privacy" hidden>
    <h1 class="sa-page-title">Privacy Settings</h1><p class="sa-intro">Secure your administrator account with password and email verification.</p>
    <section class="sa-settings-card">
     <div class="sa-settings-top"><span class="sa-settings-emblem"><img src="{{ asset('icons/seller/account/password.png') }}" alt=""></span><div><h2>Change Password</h2><p>Use your current password and verify your registered email before saving.</p></div></div>
     <form id="aaChangePasswordForm" autocomplete="off">
      <div class="sa-settings-form">
       <div class="sa-full"><label for="aaCurrentPassword">Current Password <span class="sa-required">*</span></label><input type="password" id="aaCurrentPassword" class="sa-control" autocomplete="current-password" required></div>
       <div><label for="aaNewPassword">New Password <span class="sa-required">*</span></label><input type="password" id="aaNewPassword" class="sa-control" autocomplete="new-password" minlength="8" required><small>Use at least eight characters.</small></div>
       <div><label for="aaConfirmPassword">Confirm New Password <span class="sa-required">*</span></label><input type="password" id="aaConfirmPassword" class="sa-control" autocomplete="new-password" minlength="8" required></div>
      </div><div class="sa-settings-actions"><button id="aaChangePasswordButton" class="sa-settings-primary" disabled>Continue to Email Verification</button></div>
     </form>
    </section>
    <section class="sa-settings-card sa-account-recovery"><div><h2>Forgot your password?</h2><p>Verify the email associated with your admin account, then create a new password.</p></div><button type="button" class="sa-settings-outline" id="aaForgotPasswordButton">Reset Password</button></section>
   </div>
   <div class="sa-workspace sa-tab-panel" id="notifications" data-admin-panel="notifications" hidden>
    <h1 class="sa-page-title">Notification Settings</h1><p class="sa-intro">Manage notification types for your assigned administrator role.</p>
    <form id="aaNotificationsForm" method="POST" action="{{ route('admin.account.notifications.update') }}">
     @csrf
     <section class="sa-settings-card">
      <div class="sa-settings-top"><span class="sa-settings-emblem"><img src="{{ asset('icons/seller/account/notification.png') }}" alt=""></span><div><h2>Role-based Notifications</h2><p>Only settings relevant to your access role appear below.</p></div><span class="sa-notification-role">{{ $roleLabel }}</span></div>
      @forelse($adminNotificationOptions as $key=>$opt)
       <label class="sa-toggle-row"><span><strong>{{ $opt[0] }}</strong><small>{{ $opt[1] }}</small></span><span class="sa-switch"><input type="checkbox" name="preferences[{{ $key }}]" value="1" @checked((bool)($adminPreferences[$key]??true))><span class="sa-switch-track"></span></span></label>
      @empty
       <p class="sa-intro">No notification categories assigned to this role yet.</p>
      @endforelse
     </section>
     <div class="sa-settings-actions"><button type="submit" class="sa-settings-primary" id="aaSavePreferences" disabled>Save Preferences</button></div>
    </form>
   </div>
  </div>
</main>

{{-- Image preview (current photo or newly selected photo) --}}
<div class="aa-photo-preview" id="aaPhotoPreview" hidden role="presentation">
  <section class="aa-photo-preview-card" role="dialog" aria-modal="true" aria-labelledby="aaPhotoPreviewTitle">
    <button type="button" class="aa-photo-close" id="aaPhotoPreviewClose" aria-label="Close image preview">×</button>
    <h3 id="aaPhotoPreviewTitle">Photo Preview</h3>
    <img id="aaPhotoPreviewImage" alt="Selected admin image preview">
  </section>
</div>

{{-- Admin email verification interface, matched to Seller Account --}}
<div class="sa-otp-backdrop" id="aaOtpModal" hidden>
 <section class="email-verification-card" role="dialog" aria-modal="true" aria-labelledby="aaOtpTitle" style="position:relative">
  <button type="button" class="sa-modal-close" id="aaCloseOtp" aria-label="Close">×</button>
  <div class="email-verification-icon-wrapper"><img src="{{ asset('icons/login/email-verify.png') }}" class="email-verification-icon" alt="Email verification"></div>
  <h2 class="email-verification-title" id="aaOtpTitle">Verify Your Email</h2>
  <p class="email-verification-subtitle">Email verification for<br><strong>{{ data_get($adminUser,'email') }}</strong></p>
  <div class="email-code-container" id="aaOtpInputs" aria-label="Six digit email verification code">
   @for($i=1;$i<=6;$i++)
    <input type="text" class="email-code-input sa-otp-cell" inputmode="numeric" pattern="[0-9]*" maxlength="1" autocomplete="one-time-code" aria-label="Digit {{ $i }}">
   @endfor
  </div>
  <div class="email-spam-message">
   <svg class="email-spam-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 8v5"/><circle cx="12" cy="16.5" r=".5" fill="currentColor"/></svg>
   <span>Didn’t receive the code? Check your spam folder</span>
  </div>
  <div class="email-divider"><div class="email-divider-line"></div><span class="email-divider-text">or</span><div class="email-divider-line"></div></div>
  <button type="button" class="email-resend-button" id="aaResendOtp">
   <svg class="email-resend-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 11a8 8 0 1 0 2 5"/><path d="M20 5v6h-6"/></svg><span>Resend Code</span>
  </button>
  <p class="sa-otp-error" id="aaOtpError" role="alert" hidden></p>
  <button type="button" class="email-verify-button" id="aaVerifyOtp">Verify Email</button>
 </section>
</div>
<div class="sa-otp-backdrop" id="aaResetModal" hidden>
 <section class="sa-modal-dialog sa-security-dialog" role="dialog" aria-modal="true" aria-labelledby="aaResetTitle">
  <button type="button" class="sa-modal-close" id="aaCloseReset" aria-label="Close">×</button>
  <h2 id="aaResetTitle">Create a New Password</h2>
  <p class="sa-modal-description">Choose a new password for your admin account.</p>
  <form id="aaResetForm" class="sa-settings-form single" style="text-align:left">
   <div><label for="aaResetNew">New Password</label><input class="sa-control" type="password" id="aaResetNew" autocomplete="new-password" minlength="8" required></div>
   <div><label for="aaResetConfirm">Confirm New Password</label><input class="sa-control" type="password" id="aaResetConfirm" autocomplete="new-password" minlength="8" required></div>
   <p class="sa-otp-error" id="aaResetError" role="alert" hidden></p>
   <button class="sa-settings-primary sa-otp-verify" type="submit">Save New Password</button>
  </form>
 </section>
</div>
<script>
document.addEventListener('DOMContentLoaded',()=>{
 const $$=q=>[...document.querySelectorAll(q)], $=id=>document.getElementById(id);
 // Account margins follow the sidebar's w-72 / w-20 classes using CSS.
 // No late inline style changes, so the page won't jump during hydration.
 function switchTab(id){$$('[data-admin-tab]').forEach(a=>{const active=a.dataset.adminTab===id;a.classList.toggle('active',active);if(active)a.setAttribute('aria-current','page');else a.removeAttribute('aria-current');});$$('[data-admin-panel]').forEach(p=>p.hidden=p.dataset.adminPanel!==id);/* Avoid hash navigation and anchor scrolling. */}
 $$('[data-admin-tab]').forEach(a=>a.addEventListener('click',e=>{e.preventDefault();switchTab(a.dataset.adminTab);}));const initialTab=['profile','privacy','notifications'].includes(location.hash.slice(1))?location.hash.slice(1):'profile';
 switchTab(initialTab);
 if(location.hash) history.replaceState(null,'',location.pathname+location.search);
 window.addEventListener('pageshow',()=>{requestAnimationFrame(()=>window.scrollTo({top:0,left:0,behavior:'auto'}));});
 requestAnimationFrame(()=>window.scrollTo({top:0,left:0,behavior:'auto'}));
 function toast(text,err=false){window.showAccountFlash(text,err,err?"Action Failed":"Changes Saved");}
 const birthday=$('aaBirthday'),age=$('aaAge');function calc(){if(!birthday.value){age.value='';return;}const d=new Date(birthday.value+'T00:00:00'),n=new Date();if(isNaN(d)||d>n){age.value='';return;}let v=n.getFullYear()-d.getFullYear();if(n.getMonth()<d.getMonth()||(n.getMonth()===d.getMonth()&&n.getDate()<d.getDate()))v--;age.value=String(v);}birthday.addEventListener('change',calc);calc();
 const form=$('adminProfileForm'),fields=$$('#adminProfileForm input[name],#adminProfileForm select[name]').filter(el=>!['_token','_method'].includes(el.name));
 const original=new Map(fields.map(el=>[el,el.value]));function dirty(){const d=fields.some(el=>el.type==='file'?el.files.length>0:el.value!==original.get(el));$('aaSaveProfile').disabled=!d;}form.addEventListener('input',dirty);form.addEventListener('change',dirty);
 function photo(inputId,imageId){const input=$(inputId),img=$(imageId);input.addEventListener('change',()=>{const f=input.files[0];if(!f){dirty();return;}if(f.size>5*1024*1024||!['image/jpeg','image/png','image/webp'].includes(f.type)){toast('Please choose a JPG, PNG or WebP image under 5 MB.',true);input.value='';dirty();return;}if(img.dataset.objectUrl)URL.revokeObjectURL(img.dataset.objectUrl);img.dataset.objectUrl=URL.createObjectURL(f);img.src=img.dataset.objectUrl;img.style.display='block';if(imageId==='saAvatarImage')$('saAvatarFallback').hidden=true;dirty();});}
 photo('saCoverInput','saCoverImage');photo('saAvatarInput','saAvatarImage');
 const photoPreview=$('aaPhotoPreview'), photoPreviewImg=$('aaPhotoPreviewImage');
 function openPhotoPreview(img,title){if(!img?.src||img.style.display==='none')return;photoPreviewImg.src=img.src;$('aaPhotoPreviewTitle').textContent=title;photoPreview.hidden=false;}
 function closePhotoPreview(){photoPreview.hidden=true;photoPreviewImg.removeAttribute('src');}
 $('saCoverImage').addEventListener('click',()=>openPhotoPreview($('saCoverImage'),'Cover Photo'));
 $('saAvatarImage').addEventListener('click',()=>openPhotoPreview($('saAvatarImage'),'Profile Photo'));
 $('aaAdminIdPhotoPreviewButton')?.addEventListener('click',()=>openPhotoPreview($('aaAdminIdPhotoImage'),'Admin ID Photo'));
 $('aaPhotoPreviewClose').addEventListener('click',closePhotoPreview);
 photoPreview.addEventListener('click',e=>{if(e.target===photoPreview)closePhotoPreview();});

 const notify=$('aaNotificationsForm'),switches=$$('#aaNotificationsForm input[type=checkbox]'),start=switches.map(i=>i.checked);function checkPrefs(){$('aaSavePreferences').disabled=!switches.some((i,k)=>i.checked!==start[k]);}switches.forEach(i=>i.addEventListener('change',checkPrefs));
 const otp=$('aaOtpModal'),reset=$('aaResetModal'),cells=$$('.sa-otp-cell');
 const otpError=$('aaOtpError'),verifyOtp=$('aaVerifyOtp'),resendOtp=$('aaResendOtp');
 const resendLabel=resendOtp.querySelector('span');
 const csrfToken=document.querySelector('meta[name="csrf-token"]')?.content;
 const securityBase=@json(url('/admin/account/security'));
 const adminUserId=@json((int) $adminUser->id);
 let otpPurpose='forgot',verifiedToken=null,otpRequestInProgress=false,otpCooldownInterval=null;
 const otpCooldownKey=purpose=>`admin-security-resend-v1:${adminUserId}:${purpose}`;
 function cooldownRemaining(purpose){return Math.max(0,Math.ceil((Number(sessionStorage.getItem(otpCooldownKey(purpose))||0)-Date.now())/1000));}
 function renderOtpCooldown(){
  const remaining=otpPurpose?cooldownRemaining(otpPurpose):0;
  if(remaining>0){
   resendLabel.textContent=`Resend code in ${String(Math.floor(remaining/60)).padStart(2,'0')}:${String(remaining%60).padStart(2,'0')}`;
   resendOtp.disabled=true;
   if(!otpCooldownInterval)otpCooldownInterval=setInterval(renderOtpCooldown,1000);
   return;
  }
  resendLabel.textContent='Resend Code';
  resendOtp.disabled=otpRequestInProgress;
  if(otpCooldownInterval){clearInterval(otpCooldownInterval);otpCooldownInterval=null;}
  if(otpPurpose)sessionStorage.removeItem(otpCooldownKey(otpPurpose));
 }
 function startOtpCooldown(purpose,seconds=600){
  sessionStorage.setItem(otpCooldownKey(purpose),String(Date.now()+seconds*1000));
  otpPurpose=purpose;
  renderOtpCooldown();
 }
 function close(modal){modal.hidden=true;}
 function showOtpError(message){otpError.textContent=message;otpError.hidden=false;}
 async function securityPost(path,payload){
  const response=await fetch(`${securityBase}${path}`,{
   method:'POST',
   credentials:'same-origin',
   headers:{'X-CSRF-TOKEN':csrfToken,'Content-Type':'application/json','Accept':'application/json'},
   body:JSON.stringify(payload)
  });
  let result={};try{result=await response.json();}catch(error){}
  if(!response.ok){
   const error=new Error(result.message||(result.errors&&Object.values(result.errors).flat()[0])||'The request could not be completed.');
   error.retryAfter=Number(result.retry_after||response.headers.get('Retry-After')||0);
   throw error;
  }
  return result;
 }
 async function requestOtp(purpose,isResend=false){
  otpPurpose=purpose;
  verifiedToken=null;
  otpError.hidden=true;
  if(isResend&&cooldownRemaining(purpose)>0){renderOtpCooldown();return;}
  otpRequestInProgress=true;
  resendOtp.disabled=true;
  try{
   const payload={purpose};
   if(purpose==='change')payload.current_password=$('aaCurrentPassword').value;
   const result=await securityPost('/otp/send',payload);
   if(isResend)startOtpCooldown(purpose);
   else renderOtpCooldown();
   toast(result.message);
  }catch(error){
   if(error.retryAfter>0)startOtpCooldown(purpose,error.retryAfter);
   showOtpError(error.message);
  }finally{
   otpRequestInProgress=false;
   renderOtpCooldown();
  }
 }
 async function openOtp(purpose){
  otpPurpose=purpose;
  cells.forEach(cell=>cell.value='');
  otpError.hidden=true;
  otp.hidden=false;
  renderOtpCooldown();
  cells[0].focus();
  await requestOtp(purpose);
 }
 $('aaForgotPasswordButton').addEventListener('click',()=>openOtp('forgot'));
 const changeForm=$('aaChangePasswordForm'),pw=$('aaCurrentPassword'),np=$('aaNewPassword'),cp=$('aaConfirmPassword');
 function verifyPwForm(){$('aaChangePasswordButton').disabled=!(pw.value&&np.value.length>=8&&np.value===cp.value);}
 [pw,np,cp].forEach(input=>input.addEventListener('input',verifyPwForm));
 changeForm.addEventListener('submit',async event=>{
  event.preventDefault();
  if(!changeForm.reportValidity())return;
  await openOtp('change');
 });
 cells.forEach((cell,index)=>{
  cell.addEventListener('input',()=>{cell.value=cell.value.replace(/\D/g,'').slice(-1);if(cell.value&&index<5)cells[index+1].focus();});
  cell.addEventListener('keydown',event=>{if(event.key==='Backspace'&&!cell.value&&index>0)cells[index-1].focus();});
  cell.addEventListener('paste',event=>{
   event.preventDefault();
   const digits=(event.clipboardData?.getData('text')||'').replace(/\D/g,'').slice(0,6);
   digits.split('').forEach((digit,position)=>cells[position].value=digit);
   cells[Math.min(digits.length,5)].focus();
  });
 });
 resendOtp.addEventListener('click',async()=>{
  if(resendOtp.disabled||otpRequestInProgress||!otpPurpose)return;
  cells.forEach(cell=>cell.value='');
  cells[0].focus();
  await requestOtp(otpPurpose,true);
 });
 renderOtpCooldown();
 verifyOtp.addEventListener('click',async()=>{
  otpError.hidden=true;
  const code=cells.map(cell=>cell.value).join('');
  if(!/^\d{6}$/.test(code)){showOtpError('Enter all six digits.');return;}
  verifyOtp.disabled=true;
  try{
   const result=await securityPost('/otp/verify',{purpose:otpPurpose,code});
   verifiedToken=result.verification_token;
   if(otpPurpose==='forgot'){
    close(otp);
    reset.hidden=false;
    $('aaResetNew').focus();
    return;
   }
   await securityPost('/password/change',{
    current_password:pw.value,
    new_password:np.value,
    new_password_confirmation:cp.value,
    verification_token:verifiedToken
   });
   close(otp);
   changeForm.reset();
   verifyPwForm();
   verifiedToken=null;
   toast('Password changed successfully.');
  }catch(error){
   showOtpError(error.message);
  }finally{
   verifyOtp.disabled=false;
  }
 });
 $('aaCloseOtp').addEventListener('click',()=>close(otp));
 $('aaCloseReset').addEventListener('click',()=>close(reset));
 [otp,reset].forEach(modal=>modal.addEventListener('click',event=>{if(event.target===modal)close(modal);}));
 $('aaResetForm').addEventListener('submit',async event=>{
  event.preventDefault();
  const newPassword=$('aaResetNew').value,confirmation=$('aaResetConfirm').value,errorBox=$('aaResetError');
  errorBox.hidden=true;
  if(newPassword!==confirmation){errorBox.textContent='Passwords do not match.';errorBox.hidden=false;return;}
  const submitButton=event.currentTarget.querySelector('button[type="submit"]');
  submitButton.disabled=true;
  try{
   await securityPost('/password/reset',{
    new_password:newPassword,
    new_password_confirmation:confirmation,
    verification_token:verifiedToken
   });
   close(reset);
   event.currentTarget.reset();
   verifiedToken=null;
   toast('Password reset successfully.');
  }catch(error){
   errorBox.textContent=error.message;
   errorBox.hidden=false;
  }finally{
   submitButton.disabled=false;
  }
 });
 document.addEventListener('keydown',e=>{if(e.key==='Escape'){close(otp);close(reset);closePhotoPreview();}});
});
</script>

<!-- ShopEase account action flash — same UI as Admin Registrations -->
<div id="accountDecisionFlash" class="account-decision-flash" role="status" aria-live="polite" aria-atomic="true">
  <div class="account-flash-body">
    <span class="account-flash-icon" id="accountFlashIcon" aria-hidden="true"></span>
    <div class="account-flash-copy">
      <p class="account-flash-title" id="accountFlashTitle"></p>
      <p class="account-flash-message" id="accountFlashMessage"></p>
    </div>
    <button type="button" class="account-flash-close" id="accountFlashClose" aria-label="Close notification">
      <svg viewBox="0 0 24 24"><path d="M6 6l12 12M18 6 6 18"/></svg>
    </button>
  </div>
</div>


<script>
/* Shared flash component: Admin Registrations appearance and 3800 ms timing. */
(function(){
 const flash=document.getElementById('accountDecisionFlash');
 if(!flash)return;
 const icon=flash.querySelector('#accountFlashIcon');
 const title=flash.querySelector('#accountFlashTitle');
 const message=flash.querySelector('#accountFlashMessage');
 const close=flash.querySelector('#accountFlashClose');
 let timer=null;
 const check='<svg viewBox="0 0 24 24"><path d="m5 12 4 4L19 6"/></svg>';
 const cross='<svg viewBox="0 0 24 24"><path d="M6 6l12 12M18 6 6 18"/></svg>';
 function dismiss(){clearTimeout(timer);flash.classList.remove('show');}
 window.showAccountFlash=function(text,isError=false,heading){
   if(!text)return;
   flash.classList.remove('approved','rejected','error');
   flash.classList.add(isError?'error':'approved');
   flash.setAttribute('role',isError?'alert':'status');
   icon.innerHTML=isError?cross:check;
   title.textContent=heading||(isError?'Action Failed':'Changes Saved');
   message.textContent=String(text);
   clearTimeout(timer);
   flash.classList.add('show');
   timer=setTimeout(dismiss,3800);
 };
 close.addEventListener('click',dismiss);
 const initialSuccess=@json(session('success'));
 const initialError=@json(session('error'));
 const initialValidation=@json($errors->first());
 const which=initialError||initialValidation;
 if(which)window.showAccountFlash(which,true);
 else if(initialSuccess)window.showAccountFlash(initialSuccess,false);
})();
</script>

</body></html>
