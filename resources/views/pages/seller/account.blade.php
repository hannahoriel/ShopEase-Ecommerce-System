{{-- resources/views/pages/seller/account.blade.php
     ShopEase Seller Account — Profile UI. Uses existing seller sidebar/navbar.
     Saving requires a seller.account.update route and matching controller.
--}}
@php
    // Data is loaded by SellerAccountController from the authenticated seller.
    $account = $seller ?? auth()->user();
    $shopName = data_get($account, 'store_name') ?: data_get($account, 'business_name') ?: 'Your Shop';
    $avatarName = data_get(auth()->user(), 'name') ?: $shopName;
    $avatarNameParts = preg_split('/\s+/u', trim($avatarName), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $avatarInitialParts = count($avatarNameParts) > 1
        ? [$avatarNameParts[0], $avatarNameParts[count($avatarNameParts) - 1]]
        : $avatarNameParts;
    $avatarInitials = collect($avatarInitialParts)
        ->map(fn ($part) => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($part, 0, 1)))
        ->implode('') ?: 'S';
    $registration = $registration ?? null;
    $pick = fn ($key, $fallback = '') => old($key, data_get($account, $key) ?? data_get($registration, $key) ?? $fallback);
    $coverUrl = $coverUrl ?? null;
    $avatarUrl = $avatarUrl ?? null;
    $selectedCategories = $selectedCategories ?? [];
    $categoryOptions = $categoryOptions ?? [
        'Pet Supplies', 'Electronics & Gadgets', "Women's Apparel", "Men's Apparel", 'Kids & Baby',
        'Home & Garden', 'Sports & Outdoors', 'Health & Beauty', 'Books & Media', 'Food & Gourmet',
        'Automotive & Motorcycle', 'Furniture & Office Equipment', 'Jewelry & Watches', 'Office & School Supplies'
    ];
    $pendingCategoryRequest = $pendingCategoryRequest ?? null;
    $birthdayDate = old('birthday', data_get($account, 'birthday') ?? data_get($registration, 'birthdate'));
    try { $birthdayDate = $birthdayDate ? \Carbon\Carbon::parse($birthdayDate)->format('Y-m-d') : ''; } catch (\Throwable $e) { $birthdayDate = ''; }
    $approvedPermits = $approvedPermits ?? [];
    $pendingDocumentRequests = $pendingDocumentRequests ?? [];
    $categoryClass = function ($name) {
        $special = [ 'Automotive & Motorcycle' => 'category-automotive-motorcycle' ];
        return $special[$name] ?? 'category-'.trim(preg_replace('/[^a-z0-9]+/', '-', strtolower(str_replace('&', 'and', str_replace("'", '', $name)))), '-');
    };
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Seller Account — ShopEase</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/seller/dashboard.css'])
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

</style>
</head>
<body class="m-0 p-0 bg-[#FCF8F6]">
    @section('page-title', 'My Account')
    @include('components.seller.sidebar')
    @include('components.seller.navbar')

    <main id="sellerAccountPage" class="seller-account-page">
        <div class="sa-layout">
            <aside class="sa-navigation" aria-label="Account settings navigation">
                <div class="sa-nav-heading">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="7" r="4"/><path d="M4 21v-2a8 8 0 0 1 16 0v2H4Z"/></svg>
                    My Account
                </div>
                <nav class="sa-nav-list">
                    <a class="sa-nav-link active" href="#profile" data-settings-tab="profile" aria-current="page">Profile</a>
                    <a class="sa-nav-link" href="#privacy" data-settings-tab="privacy">Privacy Settings</a>
                    <a class="sa-nav-link" href="#notifications" data-settings-tab="notifications">Notification Settings</a>
                    <a class="sa-nav-link" href="#policies" data-settings-tab="policies">Platform Policies</a>
                </nav>
            </aside>

            <div class="sa-workspace sa-tab-panel" id="profile" data-panel="profile">
                <h1 class="sa-page-title">Profile</h1>
                <p class="sa-intro">Manage your seller account information and business details.</p>



                <div class="sa-status" id="saDemoMessage" role="status" aria-live="polite"></div>

                <form id="sellerAccountForm" method="POST" action="{{ route('seller.account.update') }}" enctype="multipart/form-data" >
                    @csrf
                    @method('PUT')

                    <div class="sa-cover" id="saCover">
                        @if($coverUrl)<img id="saCoverImage" src="{{ $coverUrl }}" alt="Shop cover photo">@else<img id="saCoverImage" alt="Shop cover photo" style="display:none">@endif
                        <label class="sa-photo-button sa-cover-button" for="saCoverInput">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7h4l2-3h4l2 3h4v13H4z"/><circle cx="12" cy="13" r="3"/></svg>
                            Change Cover Photo
                        </label>
                        <input type="file" id="saCoverInput" name="cover_photo" accept="image/jpeg,image/png,image/webp" hidden>
                    </div>
                    <div class="sa-shop-strip">
                        <div class="sa-avatar" id="saAvatar">
                            <img id="saAvatarImage" src="{{ $avatarUrl ?: '' }}" alt="Shop profile photo" @if(!$avatarUrl) style="display:none" @endif onerror="this.style.display='none';document.getElementById('saAvatarFallback')?.removeAttribute('hidden')">
                            <span id="saAvatarFallback" class="sa-avatar-fallback" @if($avatarUrl) hidden @endif>{{ $avatarInitials }}</span>
                            <label class="sa-avatar-camera" for="saAvatarInput" title="Change profile photo">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h4l2-3h4l2 3h4v13H4z"/><circle cx="12" cy="13" r="3"/></svg>
                            </label>
                        </div>
                        <div>
                            <h2 class="sa-shop-name">{{ $shopName }}</h2>
                            <p class="sa-shop-caption">Seller Account</p>
                            <label class="sa-photo-button sa-change-avatar" for="saAvatarInput">Change Profile Photo</label>
                            <input id="saAvatarInput" type="file" name="profile_photo" accept="image/jpeg,image/png,image/webp" hidden>
                        </div>
                    </div>

                    <div class="sa-form-stack">
                        <section class="sa-section" aria-labelledby="saPersonalTitle">
                            <div class="sa-section-heading">
                                <span class="sa-section-icon"><img src="{{ asset('icons/seller/account/personal.png') }}" alt="" aria-hidden="true"></span>
                                <div><h3 class="sa-section-title" id="saPersonalTitle">Personal Information</h3><p class="sa-section-subtitle">Update your personal details and contact information.</p></div>
                            </div>
                            <div class="sa-fields">
                                <div class="sa-field"><label for="saLast">Last Name <span class="sa-required">*</span></label><input class="sa-control" id="saLast" name="last_name" value="{{ $pick('last_name') }}" placeholder="Enter your last name" autocomplete="family-name" required></div>
                                <div class="sa-field"><label for="saFirst">First Name <span class="sa-required">*</span></label><input class="sa-control" id="saFirst" name="first_name" value="{{ $pick('first_name') }}" placeholder="Enter your first name" autocomplete="given-name" required></div>
                                <div class="sa-field"><label for="saMiddle">Middle Name</label><input class="sa-control" id="saMiddle" name="middle_initial" value="{{ $pick('middle_initial', data_get($registration, 'middle_name', '')) }}" placeholder="Enter your middle name"></div>
                                <div class="sa-field"><label for="saSex">Sex <span class="sa-required">*</span></label><select class="sa-control" id="saSex" name="sex" required><option value="">Select sex</option><option value="Male" @selected($pick('sex')==='Male')>Male</option><option value="Female" @selected($pick('sex')==='Female')>Female</option><option value="Prefer not to say" @selected($pick('sex')==='Prefer not to say')>Prefer not to say</option></select></div>
                                <div class="sa-field"><label for="saEmail">Email <span class="sa-required">*</span></label><input class="sa-control" id="saEmail" type="email" name="email" value="{{ old('email', data_get(auth()->user(), 'email', '')) }}" autocomplete="email" readonly required><small>Email changes require a separate verification step.</small></div>
                                <div class="sa-field"><label for="saPhone">Contact No. <span class="sa-required">*</span></label><div class="sa-phone"><span class="sa-phone-prefix">+63</span><input id="saPhone" name="contact_no" type="tel" inputmode="numeric" value="{{ $pick('contact_no', data_get($registration, 'phone', '')) }}" placeholder="Enter your contact number" autocomplete="tel-national" required></div></div>
                                <div class="sa-field"><label for="saDob">Birthday <span class="sa-required">*</span></label><input class="sa-control" id="saDob" name="birthday" type="date" value="{{ $birthdayDate }}" max="{{ now()->toDateString() }}" required></div>
                                <div class="sa-field"><label for="saAge">Age</label><input class="sa-control" id="saAge" placeholder="Auto generated" readonly></div>
                            </div>
                        </section>

                        <section class="sa-section" aria-labelledby="saAddressTitle">
                            <div class="sa-section-heading">
                                <span class="sa-section-icon"><img src="{{ asset('icons/seller/account/address.png') }}" alt="" aria-hidden="true"></span>
                                <div><h3 class="sa-section-title" id="saAddressTitle">Address Information</h3><p class="sa-section-subtitle">Set your complete address for deliveries and business verification.</p></div>
                            </div>
                            <div class="sa-fields">
                                <div class="sa-field quarter"><label for="saProvince">Province <span class="sa-required">*</span></label><input class="sa-control" name="province" id="saProvince" placeholder="Enter province" value="{{ $pick('province') }}" required></div>
                                <div class="sa-field quarter"><label for="saMunicipality">Municipality <span class="sa-required">*</span></label><input class="sa-control" name="municipality" id="saMunicipality" placeholder="Enter municipality" value="{{ $pick('municipality') }}" required></div>
                                <div class="sa-field quarter"><label for="saBarangay">Barangay <span class="sa-required">*</span></label><input class="sa-control" name="barangay" id="saBarangay" placeholder="Enter barangay" value="{{ $pick('barangay') }}" required></div>
                                <div class="sa-field quarter"><label for="saZip">Zip Code <span class="sa-required">*</span></label><input class="sa-control" name="zip_code" id="saZip" inputmode="numeric" placeholder="Enter zip code" value="{{ $pick('zip_code') }}" required></div>
                                <div class="sa-field quarter"><label for="saStreet">Street</label><input class="sa-control" name="street" id="saStreet" placeholder="Enter street" value="{{ $pick('street') }}"></div>
                                <div class="sa-field quarter"><label for="saHouse">House No.</label><input class="sa-control" name="house_number" id="saHouse" placeholder="Enter house no." value="{{ $pick('house_number', data_get($registration, 'house_no', '')) }}"></div>
                                <div class="sa-field quarter"><label for="saBuilding">Building</label><input class="sa-control" name="building" id="saBuilding" placeholder="Enter building" value="{{ $pick('building') }}"></div>
                                <div class="sa-field quarter"><label for="saSubdivision">Subdivision</label><input class="sa-control" name="subdivision" id="saSubdivision" placeholder="Enter subdivision" value="{{ $pick('subdivision') }}"></div>
                                <div class="sa-file-field">
                                    <label class="sa-field-label">Valid ID</label>
                                    <div class="sa-document-list">
                                        @if(!empty($validIdAvailable))
                                            <button type="button" class="sa-doc-tile" data-document-url="{{ route('seller.account.document', ['type'=>'valid-id']) }}" data-document-name="Registered Valid ID">
                                                <span class="sa-doc-thumb"><img src="{{ route('seller.account.document', ['type'=>'valid-id']) }}" alt="Valid ID thumbnail" loading="lazy"></span>
                                                <span class="sa-doc-name">Registered Valid ID <small>Click image to preview</small></span>
                                                <span class="sa-doc-eye">↗</span>
                                            </button>
                                        @else
                                            <div class="sa-doc-empty">No approved Valid ID found.</div>
                                        @endif
                                    </div>
                                    <button type="button" class="sa-request-button sa-doc-action" data-open-doc-request="replace_valid_id" @disabled(!empty($pendingDocumentRequests['replace_valid_id']))>
                                        {{ !empty($pendingDocumentRequests['replace_valid_id']) ? 'ID Replacement Pending Approval' : 'Request to Replace Valid ID' }}
                                    </button>
                                </div>
                            </div>
                        </section>

                        <section class="sa-section" aria-labelledby="saBusinessTitle">
                            <div class="sa-section-heading">
                                <span class="sa-section-icon"><img src="{{ asset('icons/seller/account/business.png') }}" alt="" aria-hidden="true"></span>
                                <div><h3 class="sa-section-title" id="saBusinessTitle">Business Information</h3><p class="sa-section-subtitle">Manage business documents and shop categories.</p></div>
                            </div>
                            <div class="sa-business-row">
                                                                 <div>
                                    <label class="sa-field-label">Business Permits</label>
                                    <div class="sa-document-list">
                                        @if(!empty($permitAvailable))
                                            <button type="button" class="sa-doc-tile" data-document-url="{{ route('seller.account.document', ['type'=>'permit']) }}" data-document-name="Original Business Permit">
                                                <span class="sa-doc-thumb"><img src="{{ route('seller.account.document', ['type'=>'permit']) }}" alt="Original business permit" loading="lazy"></span>
                                                <span class="sa-doc-name">Original Business Permit <small>Click image to preview</small></span><span class="sa-doc-eye">↗</span>
                                            </button>
                                        @endif
                                        @foreach($approvedPermits as $permit)
                                            <button type="button" class="sa-doc-tile" data-document-url="{{ route('seller.account.permits.show', ['permit'=>$permit->id]) }}" data-document-name="Business Permit {{ $loop->iteration + 1 }}">
                                                <span class="sa-doc-thumb"><img src="{{ route('seller.account.permits.show', ['permit'=>$permit->id]) }}" alt="Additional business permit" loading="lazy"></span>
                                                <span class="sa-doc-name">{{ $permit->original_name ?: 'Additional Permit '.$loop->iteration }} <small>Click image to preview</small></span><span class="sa-doc-eye">↗</span>
                                            </button>
                                        @endforeach
                                        @if(empty($permitAvailable) && count($approvedPermits) === 0)
                                            <div class="sa-doc-empty">No approved business permit found.</div>
                                        @endif
                                    </div>
                                    <div class="sa-doc-actions">
                                        <button type="button" class="sa-request-button sa-doc-action" data-open-doc-request="add_business_permit" @disabled(!empty($pendingDocumentRequests['add_business_permit']))>{{ !empty($pendingDocumentRequests['add_business_permit']) ? 'Additional Permit Pending' : '+ Add Business Permit' }}</button>
                                        <button type="button" class="sa-request-button sa-doc-action" data-open-doc-request="replace_business_permit" @disabled(!empty($pendingDocumentRequests['replace_business_permit']))>{{ !empty($pendingDocumentRequests['replace_business_permit']) ? 'Permit Replacement Pending' : 'Request Permit Replacement' }}</button>
                                    </div>
                                    <p class="sa-request-hint">Additional and replacement permits require admin approval.</p>
                                 </div><div class="sa-multiselect">
                                    <label style="display:block;font-size:11px;font-weight:500;margin-bottom:9px">Registered Categories</label>
                                    <div class="sa-readonly-tags" aria-label="Approved seller categories">
                                        @forelse($selectedCategories as $category)
                                            <span class="sa-tag sa-tag-static {{ $categoryClass($category) }}">{{ $category }}</span>
                                        @empty
                                            <span class="sa-category-placeholder">No registered categories found.</span>
                                        @endforelse
                                    </div>
                                    <button type="button" class="sa-request-button" id="saOpenCategoryRequest" @disabled($pendingCategoryRequest)>
                                        {{ $pendingCategoryRequest ? 'Category Change Request Pending' : 'Request Admin to Edit Categories' }}
                                    </button>
                                    <p class="sa-request-hint">Category changes require admin approval. Your approved categories remain unchanged until approval.</p>
                                </div>
                            </div>
                        </section>
                    </div>
                    <div class="sa-actions"><button type="submit" id="saSaveChanges" class="sa-action primary" disabled aria-disabled="true">Save Changes</button></div>
                </form>
            </div>

            {{-- PRIVACY SETTINGS --}}
            <div class="sa-workspace sa-tab-panel" id="privacy" data-panel="privacy" hidden>
                <h1 class="sa-page-title">Privacy Settings</h1>
                <p class="sa-intro">Protect your seller account and manage your password securely.</p>
                <section class="sa-settings-card">
                    <div class="sa-settings-top"><span class="sa-settings-emblem"><img src="{{ asset('icons/seller/account/password.png') }}" alt="" aria-hidden="true"></span><div><h2>Change Password</h2><p>Use your current password and verify your registered email before saving.</p></div></div>
                    <form id="saChangePasswordForm" autocomplete="off">
                        <div class="sa-settings-form">
                            <div class="sa-full"><label for="saCurrentPassword">Current Password <span class="sa-required">*</span></label><input type="password" id="saCurrentPassword" name="current_password" autocomplete="current-password" class="sa-control" required></div>
                            <div><label for="saNewPassword">New Password <span class="sa-required">*</span></label><input type="password" id="saNewPassword" name="new_password" autocomplete="new-password" class="sa-control" minlength="8" required><small>At least 8 characters. Use a strong and unique password.</small></div>
                            <div><label for="saConfirmPassword">Confirm New Password <span class="sa-required">*</span></label><input type="password" id="saConfirmPassword" name="new_password_confirmation" autocomplete="new-password" class="sa-control" minlength="8" required></div>
                        </div>
                        <div class="sa-settings-actions"><button type="submit" class="sa-settings-primary" id="saChangePasswordButton" disabled>Continue to Email Verification</button></div>
                    </form>
                </section>
                <section class="sa-settings-card sa-account-recovery"><div><h2>Forgot your password?</h2><p>Verify the email associated with your seller account, then create a new password. Your current password will not be required.</p></div><button type="button" class="sa-settings-outline" id="saForgotPasswordButton">Reset Password</button></section>
            </div>

            {{-- NOTIFICATION SETTINGS --}}
            <div class="sa-workspace sa-tab-panel" id="notifications" data-panel="notifications" hidden>
                <h1 class="sa-page-title">Notification Settings</h1>
                <p class="sa-intro">Choose which ShopEase notifications you want to receive.</p>
                <form id="saNotificationSettingsForm" action="{{ \Illuminate\Support\Facades\Route::has('seller.account.settings.notifications') ? route('seller.account.settings.notifications') : '#' }}" method="POST" data-connected="{{ \Illuminate\Support\Facades\Route::has('seller.account.settings.notifications') ? '1' : '0' }}">
                    @csrf
                    @php $saPreferences = $notificationPreferences ?? []; @endphp
                    <section class="sa-settings-card">
                        <div class="sa-settings-top"><span class="sa-settings-emblem"><img src="{{ asset('icons/seller/account/notification.png') }}" alt="" aria-hidden="true"></span><div><h2>Order & Shop Activity</h2><p>Stay updated on important actions affecting your shop.</p></div></div>
                        @foreach([ 'new_orders' => ['New orders','When a customer places a new order'], 'order_updates' => ['Order updates','Shipping, delivery, cancellation and return updates'], 'messages' => ['Messages','New buyer and admin conversations'], 'reviews' => ['Ratings & reviews','New customer feedback and product ratings'], 'compliance' => ['Compliance & approval','Category requests, permits and compliance notices'] ] as $key => $copy)
                            <label class="sa-toggle-row"><span><strong>{{ $copy[0] }}</strong><small>{{ $copy[1] }}</small></span><span class="sa-switch"><input type="checkbox" name="preferences[{{ $key }}]" value="1" @checked((bool)($saPreferences[$key] ?? true))><span class="sa-switch-track"></span></span></label>
                        @endforeach
                    </section>
                    <section class="sa-settings-card"><div class="sa-settings-top"><span class="sa-settings-emblem"><img src="{{ asset('icons/seller/account/notification.png') }}" alt="" aria-hidden="true"></span><div><h2>Communication Preferences</h2><p>Decide which email notifications and updates matter to you.</p></div></div>
                        @foreach(['email_alerts' => ['Email notifications','Receive a copy of important account alerts by email'], 'announcements' => ['Platform announcements','Feature updates, maintenance and seller announcements'], 'promotions' => ['Marketing & tips','Optional shop growth guides and promotional tips']] as $key=>$copy)
                            <label class="sa-toggle-row"><span><strong>{{ $copy[0] }}</strong><small>{{ $copy[1] }}</small></span><span class="sa-switch"><input type="checkbox" name="preferences[{{ $key }}]" value="1" @checked((bool)($saPreferences[$key] ?? ($key !== 'promotions')))><span class="sa-switch-track"></span></span></label>
                        @endforeach
                    </section>
                    <div class="sa-settings-actions"><button type="submit" class="sa-settings-primary" id="saSaveNotifications" disabled>Save Preferences</button></div>
                </form>
            </div>

            {{-- PLATFORM POLICIES: document style (draft content, not official legal terms) --}}
            <div class="sa-workspace sa-tab-panel" id="policies" data-panel="policies" hidden>
                <h1 class="sa-page-title">Platform Policies</h1>
                <p class="sa-intro">Seller guidelines and platform expectations in a readable document format.</p>
                <article class="sa-policy-document">
                    <header class="sa-policy-header"><div class="sa-policy-stamp">ShopEase · SELLER DOCUMENT</div><h2>Seller Platform Policies</h2><p>Reference guide for seller account holders</p><div class="sa-policy-rule"></div><p class="sa-policy-note">Illustrative policy summary for UI purposes. Replace this text with your organization's approved and current policies before publishing.</p></header>
                    <nav class="sa-policy-index" aria-label="Document contents"><strong>Contents</strong><a href="#saPolicy1">01. Seller responsibilities</a><a href="#saPolicy2">02. Products & listings</a><a href="#saPolicy3">03. Orders & fulfillment</a><a href="#saPolicy4">04. Customer communication</a><a href="#saPolicy5">05. Privacy & security</a><a href="#saPolicy6">06. Compliance & requests</a></nav>
                    <section id="saPolicy1"><h3>01. Seller responsibilities</h3><p>Keep your shop and contact information accurate. Submit valid supporting documents, follow applicable laws and cooperate with verification or compliance checks.</p></section>
                    <section id="saPolicy2"><h3>02. Products & listings</h3><p>Use accurate product photos, descriptions, categories, prices and available stock. Do not list prohibited, counterfeit or misleading products.</p></section>
                    <section id="saPolicy3"><h3>03. Orders & fulfillment</h3><p>Review orders promptly, maintain accurate order statuses and communicate delays, cancellations or fulfillment issues to customers.</p></section>
                    <section id="saPolicy4"><h3>04. Customer communication</h3><p>Respond professionally to inquiries, disputes and feedback. Keep communication respectful, factual and focused on resolving buyer concerns.</p></section>
                    <section id="saPolicy5"><h3>05. Privacy & security</h3><p>Protect account credentials and customer information. Do not disclose sensitive buyer details outside authorized order processing and support workflows.</p></section>
                    <section id="saPolicy6"><h3>06. Compliance & requests</h3><p>Changes to registered business categories and verification documents may require administrator review. Existing approved information remains effective until a request is approved.</p></section>
                    <footer class="sa-policy-footer">ShopEase Seller Guidelines <span>End of document</span></footer>
                </article>
            </div>
        </div>
    </main>

    {{-- Existing and newly selected image preview modal --}}
    <div class="sa-modal-overlay" id="saPreviewModal" role="presentation" hidden>
        <section class="sa-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="saPreviewHeading">
            <button type="button" class="sa-modal-close" data-close-preview aria-label="Close preview">×</button>
            <h2 id="saPreviewHeading">Document Preview</h2>
            <p class="sa-modal-description">Your uploaded document is shown below.</p>
            <div class="sa-preview-media"><img id="saPreviewImage" alt="Uploaded document preview"></div>
        </section>
    </div>

    {{-- Category request modal: never changes approved categories directly --}}
    <div class="sa-modal-overlay" id="saCategoryRequestModal" role="presentation" hidden>
        <section class="sa-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="saRequestHeading">
            <button type="button" class="sa-modal-close" data-close-request aria-label="Close request">×</button>
            <h2 id="saRequestHeading">Request Category Changes</h2>
            <p class="sa-modal-description">Your currently approved categories are selected by default. Select the categories you want the admin to review and explain why.</p>
            <form method="POST" id="saCategoryRequestForm" action="{{ route('seller.account.categories.request') }}">
                @csrf
                <label style="display:block;font-size:12px;font-weight:600;margin-bottom:9px">Requested Categories <span class="sa-required">*</span></label>
                <div class="sa-request-options" id="saRequestChoices"></div>
                <div id="saRequestInputs"></div>
                <label for="saCategoryReason" style="display:block;font-size:12px;font-weight:600;margin-top:17px">Reason for Request <span class="sa-required">*</span></label>
                <textarea name="reason" id="saCategoryReason" class="sa-request-reason" minlength="10" maxlength="1000" required placeholder="Explain why you'd like to add or remove these categories..."></textarea>
                <div class="sa-modal-buttons">
                    <button type="button" data-close-request>Cancel</button>
                    <button type="submit" class="primary">Submit Request</button>
                </div>
            </form>
        </section>
    </div>


    {{-- Document change request (approval required; existing approved documents are unchanged) --}}
    @php
      $saDocRequestRoute = collect(['seller.account.documents.request','seller.account.document.request','seller.account.document-change-request'])
          ->first(fn ($name) => \Illuminate\Support\Facades\Route::has($name));
    @endphp
    <div class="sa-modal-overlay" id="saDocumentRequestModal" hidden>
      <section class="sa-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="saDocumentRequestTitle">
        <button type="button" class="sa-modal-close" data-close-doc-request aria-label="Close">×</button>
        <h2 id="saDocumentRequestTitle">Document Request</h2>
        <p class="sa-modal-description">Upload a document for admin review. Approved files will stay unchanged until your request is approved.</p>
        <form id="saDocumentRequestForm" method="POST" enctype="multipart/form-data"
              action="{{ $saDocRequestRoute ? route($saDocRequestRoute) : '#' }}"
              data-connected="{{ $saDocRequestRoute ? '1' : '0' }}">
          @csrf
          <input type="hidden" id="saDocumentRequestType" name="type">
          <label class="sa-field-label" for="saDocumentNewFile">Document <span class="sa-required">*</span></label>
          <input type="file" id="saDocumentNewFile" name="document" class="sa-document-file" accept="image/jpeg,image/png,image/webp" required>
          <img id="saDocumentNewPreview" class="sa-inline-preview" alt="Selected document preview">
          <label class="sa-field-label" for="saDocumentReason" style="margin-top:17px">Reason for Request <span class="sa-required">*</span></label>
          <textarea id="saDocumentReason" name="reason" class="sa-request-reason" minlength="10" maxlength="1000" required placeholder="Explain why this document needs to be added or replaced..."></textarea>
          <div class="sa-modal-buttons">
            <button type="button" data-close-doc-request>Cancel</button>
            <button type="submit" class="primary">Submit for Approval</button>
          </div>
        </form>
      </section>
    </div>

    {{-- Password email verification --}}
    <div class="sa-modal-overlay" id="saSecurityOtpModal" hidden>
      <section class="email-verification-card" role="dialog" aria-modal="true" aria-labelledby="saSecurityOtpTitle" style="position:relative">
        <button type="button" class="sa-modal-close" id="saCloseOtp" aria-label="Close">×</button>
        <div class="email-verification-icon-wrapper"><img src="{{ asset('icons/login/email-verify.png') }}" alt="Email verification" class="email-verification-icon"></div>
        <h2 class="email-verification-title" id="saSecurityOtpTitle">Verify Your Email</h2>
        <p class="email-verification-subtitle">Email verification for<br><strong>{{ auth()->user()->email }}</strong></p>
        <div class="email-code-container" id="saOtpDigits" aria-label="Six digit email verification code">
           @for($i=1;$i<=6;$i++)
             <input type="text" class="email-code-input sa-otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code" aria-label="Digit {{ $i }}">
           @endfor
        </div>
        <div class="email-spam-message">
          <svg class="email-spam-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 8v5"/><circle cx="12" cy="16.5" r=".5" fill="currentColor"/></svg>
          <span>Didn’t receive the code? Check your spam folder or resend it</span>
        </div>
        <div class="email-divider"><div class="email-divider-line"></div><span class="email-divider-text">or</span><div class="email-divider-line"></div></div>
        <button type="button" class="email-resend-button" id="saResendOtp">
          <svg class="email-resend-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 11a8 8 0 1 0 2 5"/><path d="M20 5v6h-6"/></svg><span>Resend Code</span>
        </button>
        <p class="sa-security-message" id="saOtpError" role="alert" hidden></p>
        <button type="button" class="email-verify-button" id="saVerifyOtp">Verify Email</button>
      </section>
    </div>
    <div class="sa-modal-overlay" id="saForgotPasswordModal" hidden>
      <section class="sa-modal-dialog sa-security-dialog" role="dialog" aria-modal="true" aria-labelledby="saForgotTitle">
        <button type="button" class="sa-modal-close" id="saCloseForgot" aria-label="Close">×</button>
        <h2 id="saForgotTitle">Create a New Password</h2>
        <p class="sa-modal-description">Enter a new password after verifying your registered email.</p>
        <form id="saForgotFinalForm"><div class="sa-settings-form single"><div><label for="saResetNew">New Password</label><input id="saResetNew" type="password" autocomplete="new-password" class="sa-control" minlength="8" required></div><div><label for="saResetConfirm">Confirm New Password</label><input id="saResetConfirm" type="password" autocomplete="new-password" class="sa-control" minlength="8" required></div></div>
          <p class="sa-security-message" id="saResetError" role="alert" hidden></p><button class="sa-settings-primary sa-otp-verify" type="submit">Reset Password</button></form>
      </section>
    </div>
<style>.sa-floating-flash{position:fixed;z-index:99999;top:90px;right:24px;max-width:390px;padding:14px 18px;border-radius:10px;font:500 12px Poppins,sans-serif;box-shadow:0 8px 25px #0002;display:none}.sa-floating-flash.success{display:block;color:#246238;background:#eef9f0;border:1px solid #b9dec2}.sa-floating-flash.error{display:block;color:#9a2f26;background:#fff2ec;border:1px solid #f0c8b8}</style>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        // Account tabs, no navigation away or profile data loss.
        const tabLinks=[...document.querySelectorAll('[data-settings-tab]')];
        const tabPanels=[...document.querySelectorAll('[data-panel]')];
        function activateAccountTab(tab){
          const valid=['profile','privacy','notifications','policies'].includes(tab)?tab:'profile';
          tabPanels.forEach(panel=>panel.hidden=panel.dataset.panel!==valid);
          tabLinks.forEach(link=>{const active=link.dataset.settingsTab===valid;link.classList.toggle('active',active);if(active)link.setAttribute('aria-current','page');else link.removeAttribute('aria-current');});
          history.replaceState(null,'','#'+valid);
        }
        tabLinks.forEach(a=>a.addEventListener('click',e=>{e.preventDefault();activateAccountTab(a.dataset.settingsTab);}));
        activateAccountTab(location.hash.slice(1)||'profile');
        window.addEventListener('hashchange',()=>activateAccountTab(location.hash.slice(1)));

        const root = document.getElementById('sellerAccountPage');
        const sidebar = document.getElementById('sellerSidebar');
        const syncOffset = () => { if (window.innerWidth <= 760) {root.style.marginLeft = '0px';return;} root.style.marginLeft = sidebar && sidebar.classList.contains('seller-sidebar-collapsed') ? '100px' : '270px'; };
        syncOffset(); window.addEventListener('resize', syncOffset);
        if (sidebar) new MutationObserver(syncOffset).observe(sidebar, {attributes:true,attributeFilter:['class']});
        const dob = document.getElementById('saDob'), age = document.getElementById('saAge');
        function calculateAge() { if (!dob.value) {age.value='';return;} const d=new Date(dob.value+'T00:00:00'),now=new Date(); if(isNaN(d.getTime())||d>now){age.value='';return;} let years=now.getFullYear()-d.getFullYear(); if(now.getMonth()<d.getMonth()||(now.getMonth()===d.getMonth()&&now.getDate()<d.getDate())) years--;age.value=years>=0?years:''; }
        dob.addEventListener('change',calculateAge);calculateAge();
        function wireFile(id,imgId,labelId){const input=document.getElementById(id);input.addEventListener('change',()=>{const file=input.files?.[0];if(!file)return;if(file.size>5*1024*1024){window.showAccountFlash('Please choose an image smaller than 5 MB.',true);input.value='';return;}if(!['image/jpeg','image/png','image/webp'].includes(file.type)){window.showAccountFlash('Please choose a JPG, PNG or WebP image.',true);input.value='';return;}if(labelId)document.getElementById(labelId).textContent=file.name;if(imgId){const img=document.getElementById(imgId);if(img.dataset.objectUrl)URL.revokeObjectURL(img.dataset.objectUrl);img.dataset.objectUrl=URL.createObjectURL(file);img.src=img.dataset.objectUrl;img.style.display='block';if(id==='saAvatarInput')document.getElementById('saAvatarFallback')?.remove();}});}
        wireFile('saCoverInput','saCoverImage');wireFile('saAvatarInput','saAvatarImage');
                 // Registered categories are read-only on the profile. Changes require an admin request.

                 // Reset remains native: registered categories cannot be edited directly.
         const form=document.getElementById('sellerAccountForm');
         // Enable Save Changes only when an actual editable field or photo differs
         // from its initial state. Category/document request modals are separate forms.
         const saveChanges = document.getElementById('saSaveChanges');
         const editableFields = [...form.querySelectorAll('input, select, textarea')].filter(el =>
             el.name && !['_token', '_method'].includes(el.name) && !el.disabled && el.type !== 'hidden'
         );
         const initialValues = new Map(editableFields.map(el => [el, el.type === 'checkbox' || el.type === 'radio' ? el.checked : el.value]));
         const isModified = el => {
             if (el.type === 'file') return el.files && el.files.length > 0;
             if (el.type === 'checkbox' || el.type === 'radio') return el.checked !== initialValues.get(el);
             return el.value !== initialValues.get(el);
         };
         const refreshSaveButton = () => {
             const changed = editableFields.some(isModified);
             saveChanges.disabled = !changed;
             saveChanges.setAttribute('aria-disabled', String(!changed));
         };
         form.addEventListener('input', refreshSaveButton);
         form.addEventListener('change', refreshSaveButton);
         form.addEventListener('reset', () => setTimeout(() => { calculateAge(); refreshSaveButton(); }, 0));
         form.addEventListener('submit', e => { if (!editableFields.some(isModified)) e.preventDefault(); });
         refreshSaveButton();
         // Document previews (existing + newly selected files)
         const previewModal=document.getElementById('saPreviewModal');
         const previewImg=document.getElementById('saPreviewImage');
         const previewHeading=document.getElementById('saPreviewHeading');
         let previewObjectUrl=null;
         function showPreview(src,title){
           if(previewObjectUrl){URL.revokeObjectURL(previewObjectUrl);previewObjectUrl=null;}
           previewImg.src=src;previewHeading.textContent=title;
           previewModal.hidden=false;document.body.classList.add('sa-modal-open');
         }
         function closePreview(){previewModal.hidden=true;previewImg.removeAttribute('src');document.body.classList.remove('sa-modal-open');if(previewObjectUrl){URL.revokeObjectURL(previewObjectUrl);previewObjectUrl=null;}}
         document.querySelectorAll('[data-document-url]').forEach(btn=>btn.addEventListener('click',()=>showPreview(btn.dataset.documentUrl,btn.dataset.documentName)));
         document.querySelectorAll('[data-close-preview]').forEach(btn=>btn.addEventListener('click',closePreview));
         previewModal.addEventListener('click',e=>{if(e.target===previewModal)closePreview();});
         // Category change request modal: initial selection always matches approved categories.
         const requestModal=document.getElementById('saCategoryRequestModal');
         const requestCategories=new Set(@json($selectedCategories));
         const allCategories=@json($categoryOptions);
         const choices=document.getElementById('saRequestChoices');
         const inputs=document.getElementById('saRequestInputs');
         function renderRequestChoices(){
           choices.replaceChildren();inputs.replaceChildren();
           allCategories.forEach(name=>{
             const label=document.createElement('label');label.className='sa-request-choice';
             const check=document.createElement('input');check.type='checkbox';check.checked=requestCategories.has(name);
             check.addEventListener('change',()=>{if(check.checked)requestCategories.add(name);else requestCategories.delete(name);renderRequestChoices();});
             const t=document.createElement('span');t.textContent=name;label.append(check,t);choices.append(label);
           });
           requestCategories.forEach(name=>{const h=document.createElement('input');h.type='hidden';h.name='requested_categories[]';h.value=name;inputs.append(h);});
         }
         renderRequestChoices();
         document.getElementById('saOpenCategoryRequest')?.addEventListener('click',()=>{requestModal.hidden=false;document.body.classList.add('sa-modal-open');});
         function closeRequest(){requestModal.hidden=true;document.body.classList.remove('sa-modal-open');}
         document.querySelectorAll('[data-close-request]').forEach(btn=>btn.addEventListener('click',closeRequest));
         requestModal.addEventListener('click',e=>{if(e.target===requestModal)closeRequest();});
         document.getElementById('saCategoryRequestForm').addEventListener('submit',e=>{
           if(!requestCategories.size){e.preventDefault();alert('Select at least one category.');return;}
           const original=new Set(@json($selectedCategories));
           if(original.size===requestCategories.size&&[...requestCategories].every(x=>original.has(x))){e.preventDefault();alert('Please select a different category selection to request a change.');}
         });
         document.addEventListener('keydown',e=>{if(e.key==='Escape'){if(!previewModal.hidden)closePreview();if(!requestModal.hidden)closeRequest();}});


         // Direct image click opens modal; edit controls still open the file picker.
         [['saCoverImage','Cover Photo'],['saAvatarImage','Profile Photo']].forEach(([id,title])=>{
             const img=document.getElementById(id);
             img?.addEventListener('click',()=>{if(img.src&&img.style.display!=='none')showPreview(img.src,title);});
         });
         document.getElementById('saCover')?.addEventListener('click',e=>{
             if(e.target.closest('label,input,button,img'))return;
             const img=document.getElementById('saCoverImage');if(img?.src&&img.style.display!=='none')showPreview(img.src,'Cover Photo');
         });
         // Document request modal: never directly updates approved documents.
         const docModal=document.getElementById('saDocumentRequestModal');
         const docType=document.getElementById('saDocumentRequestType');
         const docTitle=document.getElementById('saDocumentRequestTitle');
         const docFile=document.getElementById('saDocumentNewFile');
         const docPreview=document.getElementById('saDocumentNewPreview');
         let docObjectUrl=null;
         const docNames={replace_valid_id:'Replace Valid ID',add_business_permit:'Add Business Permit',replace_business_permit:'Replace Business Permit'};
         document.querySelectorAll('[data-open-doc-request]').forEach(btn=>btn.addEventListener('click',()=>{
             if(btn.disabled)return;
             document.getElementById('saDocumentRequestForm').reset();
             if(docObjectUrl){URL.revokeObjectURL(docObjectUrl);docObjectUrl=null;}
             docPreview.style.display='none';docPreview.removeAttribute('src');
             docType.value=btn.dataset.openDocRequest;docTitle.textContent=docNames[docType.value]||'Document Request';
             docModal.hidden=false;document.body.classList.add('sa-modal-open');
         }));
         document.getElementById('saDocumentRequestForm')?.addEventListener('submit', e => {
             if (e.currentTarget.dataset.connected !== '1') {
                 e.preventDefault();
                 alert('The document request backend route is not registered. Please connect the document request route first.');
             }
         });
         docFile.addEventListener('change',()=>{
             if(docObjectUrl){URL.revokeObjectURL(docObjectUrl);docObjectUrl=null;}
             const file=docFile.files?.[0];if(!file){docPreview.style.display='none';return;}
             if(file.size>5*1024*1024 || !['image/jpeg','image/png','image/webp'].includes(file.type)){
                 alert('Choose a JPG, PNG or WebP image smaller than 5 MB.');docFile.value='';docPreview.style.display='none';return;
             }
             docObjectUrl=URL.createObjectURL(file);docPreview.src=docObjectUrl;docPreview.style.display='block';
         });
         docPreview.addEventListener('click',()=>{if(docPreview.src)showPreview(docPreview.src,'Selected Document');});
         function closeDoc(){docModal.hidden=true;document.body.classList.remove('sa-modal-open');if(docObjectUrl){URL.revokeObjectURL(docObjectUrl);docObjectUrl=null;}}
         document.querySelectorAll('[data-close-doc-request]').forEach(b=>b.addEventListener('click',closeDoc));
         docModal.addEventListener('click',e=>{if(e.target===docModal)closeDoc();});
         document.addEventListener('keydown',e=>{if(e.key==='Escape'&&!docModal.hidden)closeDoc();});


        const csrf=document.querySelector('meta[name="csrf-token"]')?.content;
        const settingsBase=@json(url('/seller/account/settings'));
        const sellerUserId=@json((int) auth()->id());
        async function settingsPost(path,payload){
          const r=await fetch(settingsBase+path,{method:'POST',headers:{'X-CSRF-TOKEN':csrf,'Content-Type':'application/json','Accept':'application/json'},credentials:'same-origin',body:JSON.stringify(payload)});
          let data={};try{data=await r.json();}catch(e){}
          if(!r.ok){
            const error=new Error(data.message||(data.errors&&Object.values(data.errors).flat()[0])||'The request could not be completed.');
            error.retryAfter=Number(data.retry_after||r.headers.get('Retry-After')||0);
            throw error;
          }
          return data;
        }
        function settingsToast(message,isError=false){window.showAccountFlash(message,isError,isError?"Action Failed":"Changes Saved");}
        const pwForm=document.getElementById('saChangePasswordForm');
        const currentPw=document.getElementById('saCurrentPassword'),newPw=document.getElementById('saNewPassword'),confirmPw=document.getElementById('saConfirmPassword');
        const pwButton=document.getElementById('saChangePasswordButton');
        function pwDirty(){pwButton.disabled=!(currentPw.value&&newPw.value.length>=8&&newPw.value===confirmPw.value);}
        [currentPw,newPw,confirmPw].forEach(x=>x.addEventListener('input',pwDirty));
        const otpModal=document.getElementById('saSecurityOtpModal'),forgotModal=document.getElementById('saForgotPasswordModal');
        const otpInputs=[...document.querySelectorAll('.sa-otp-digit')];
        const otpError=document.getElementById('saOtpError'),verifyOtp=document.getElementById('saVerifyOtp'),resendOtp=document.getElementById('saResendOtp');
        let securityPurpose=null,verifiedToken=null;
        let otpRequestInProgress=false,otpCooldownInterval=null;
        const otpCooldownSeconds=600;
        const otpResendLabel=resendOtp.querySelector('span');
        const otpCooldownKey=purpose=>`seller-security-resend-cooldown-v2:${sellerUserId}:${purpose}`;
        function otpCooldownRemaining(purpose){
          return Math.max(0,Math.ceil((Number(sessionStorage.getItem(otpCooldownKey(purpose))||0)-Date.now())/1000));
        }
        function renderOtpCooldown(){
          const remaining=securityPurpose?otpCooldownRemaining(securityPurpose):0;
          if(remaining>0){
            const minutes=String(Math.floor(remaining/60)).padStart(2,'0');
            const seconds=String(remaining%60).padStart(2,'0');
            otpResendLabel.textContent=`Resend code in ${minutes}:${seconds}`;
            resendOtp.disabled=true;
            if(!otpCooldownInterval)otpCooldownInterval=setInterval(renderOtpCooldown,1000);
            return;
          }
          otpResendLabel.textContent='Resend Code';
          resendOtp.disabled=otpRequestInProgress;
          if(otpCooldownInterval){clearInterval(otpCooldownInterval);otpCooldownInterval=null;}
          if(securityPurpose)sessionStorage.removeItem(otpCooldownKey(securityPurpose));
        }
        function startOtpCooldown(purpose,seconds=otpCooldownSeconds){
          sessionStorage.setItem(otpCooldownKey(purpose),String(Date.now()+seconds*1000));
          securityPurpose=purpose;
          renderOtpCooldown();
        }
        function closeSecurityModal(modal){modal.hidden=true;document.body.classList.remove('sa-modal-open');}
        function showSecurityError(message,where=otpError){where.textContent=message;where.hidden=false;}
        async function requestSecurityOtp(purpose,isResend=false){
          securityPurpose=purpose;
          verifiedToken=null;
          document.getElementById('saSecurityOtpTitle').textContent='Verify Your Email';
          otpInputs.forEach(x=>x.value='');
          otpError.hidden=true;
          if(otpCooldownRemaining(purpose)>0){
            otpModal.hidden=false;
            document.body.classList.add('sa-modal-open');
            renderOtpCooldown();
            otpInputs[0]?.focus();
            settingsToast('A verification code was already sent. Please check your email.',true);
            return;
          }
          otpRequestInProgress=true;
          renderOtpCooldown();
          try{
            const result=await settingsPost('/otp/send',{purpose});
            if(isResend)startOtpCooldown(purpose);
            otpModal.hidden=false;
            document.body.classList.add('sa-modal-open');
            otpInputs[0]?.focus();
            settingsToast(result.message||'A verification code was sent to your registered email.');
          }catch(err){
            if(err.retryAfter>0)startOtpCooldown(purpose,err.retryAfter);
            settingsToast(err.message,true);
          }finally{
            otpRequestInProgress=false;
            renderOtpCooldown();
          }
        }
        pwForm.addEventListener('submit',async e=>{e.preventDefault();if(pwButton.disabled)return;await requestSecurityOtp('change');});
        document.getElementById('saForgotPasswordButton').addEventListener('click',()=>requestSecurityOtp('forgot'));
        otpInputs.forEach((input,index)=>{
          input.addEventListener('input',()=>{input.value=input.value.replace(/\D/g,'').slice(-1);if(input.value&&index<5)otpInputs[index+1].focus();});
          input.addEventListener('keydown',e=>{if(e.key==='Backspace'&&!input.value&&index>0)otpInputs[index-1].focus();});
          input.addEventListener('paste',e=>{e.preventDefault();const digits=(e.clipboardData?.getData('text')||'').replace(/\D/g,'').slice(0,6).split('');digits.forEach((v,i)=>otpInputs[i].value=v);otpInputs[Math.min(digits.length,5)].focus();});
        });
        document.getElementById('saCloseOtp').addEventListener('click',()=>closeSecurityModal(otpModal));
        document.getElementById('saCloseForgot').addEventListener('click',()=>closeSecurityModal(forgotModal));
        resendOtp.addEventListener('click',async()=>{
          if(resendOtp.disabled||!securityPurpose)return;
          otpInputs.forEach(x=>x.value='');
          await requestSecurityOtp(securityPurpose,true);
        });
        renderOtpCooldown();
        verifyOtp.addEventListener('click',async()=>{
          otpError.hidden=true;
          const code=otpInputs.map(x=>x.value).join('');
          if(!/^\d{6}$/.test(code)){
            showSecurityError('Please enter the six-digit verification code.');
            return;
          }
          verifyOtp.disabled=true;
          try{
            const result=await settingsPost('/otp/verify',{purpose:securityPurpose,code});
            verifiedToken=result.verification_token;
            if(securityPurpose==='forgot'){
              closeSecurityModal(otpModal);
              forgotModal.hidden=false;
              document.body.classList.add('sa-modal-open');
              document.getElementById('saResetNew').focus();
            }else{
              await settingsPost('/password/change',{
                current_password:currentPw.value,
                new_password:newPw.value,
                new_password_confirmation:confirmPw.value,
                verification_token:verifiedToken
              });
              closeSecurityModal(otpModal);
              pwForm.reset();
              pwDirty();
              settingsToast('Password changed successfully.');
            }
          }catch(err){
            showSecurityError(err.message);
          }finally{
            verifyOtp.disabled=false;
          }
        });
        document.getElementById('saForgotFinalForm').addEventListener('submit',async e=>{
          e.preventDefault();
          const pw=document.getElementById('saResetNew').value;
          const confirm=document.getElementById('saResetConfirm').value;
          const box=document.getElementById('saResetError');box.hidden=true;
          if(pw!==confirm){showSecurityError('New passwords do not match.',box);return;}
          const submitButton=e.currentTarget.querySelector('button[type="submit"]');
          submitButton.disabled=true;
          try{
            await settingsPost('/password/reset',{
              new_password:pw,
              new_password_confirmation:confirm,
              verification_token:verifiedToken
            });
            closeSecurityModal(forgotModal);
            e.target.reset();
            verifiedToken=null;
            settingsToast('Password reset successfully.');
          }catch(err){
            showSecurityError(err.message,box);
          }finally{
            submitButton.disabled=false;
          }
        });
        [otpModal,forgotModal].forEach(m=>m.addEventListener('click',e=>{if(e.target===m)closeSecurityModal(m);}));
        const prefsForm=document.getElementById('saNotificationSettingsForm'),prefsButton=document.getElementById('saSaveNotifications');
        const prefCheckboxes=[...prefsForm.querySelectorAll('input[type="checkbox"]')];const initialPrefs=prefCheckboxes.map(x=>x.checked);
        function updatePrefsButton(){prefsButton.disabled=!prefCheckboxes.some((x,i)=>x.checked!==initialPrefs[i]);}
        prefCheckboxes.forEach(x=>x.addEventListener('change',updatePrefsButton));
        prefsForm.addEventListener('submit',async e=>{
          e.preventDefault();if(prefsButton.disabled)return;
          if(prefsForm.dataset.connected!=='1'){settingsToast('Notification preferences need the provided backend route to save.',true);return;}
          prefsButton.disabled=true;
          try{
            const values={};prefCheckboxes.forEach(x=>{const key=x.name.match(/\[([^\]]+)\]/)?.[1];if(key)values[key]=x.checked;});
            await settingsPost('/notifications',{preferences:values});prefCheckboxes.forEach((x,i)=>initialPrefs[i]=x.checked);updatePrefsButton();settingsToast('Notification preferences saved successfully.');
          }catch(err){settingsToast(err.message,true);updatePrefsButton();}
        });

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

</body>
</html>
