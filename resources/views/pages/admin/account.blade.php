{{-- resources/views/pages/seller/account.blade.php
     ShopEase Seller Account — Profile UI. Uses existing seller sidebar/navbar.
     Saving requires a seller.account.update route and matching controller.
--}}
@php
    // Data is loaded by SellerAccountController from the authenticated seller.
    $account = $seller ?? auth()->user();
    $shopName = data_get($account, 'store_name') ?: data_get($account, 'business_name') ?: 'Your Shop';
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
    $sellerAccountSettingsBase = url('/seller/account/settings');
    $sellerAccountUserId = (int) auth()->id();
    $sellerAccountInitialSuccess = session('success');
    $sellerAccountInitialError = session('error');
    $sellerAccountInitialValidation = $errors->first();
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
    @vite(['resources/css/app.css', 'resources/css/seller/dashboard.css', 'resources/css/seller/account.css', 'resources/js/seller/account.js'])
    
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
                            @if($avatarUrl)<img id="saAvatarImage" src="{{ $avatarUrl }}" alt="Shop profile photo">@else<img id="saAvatarImage" alt="Shop profile photo" style="display:none"><span id="saAvatarFallback" class="sa-avatar-fallback">♧</span>@endif
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




<script id="sellerAccountPageConfig" type="application/json">
{
  "selectedCategories": @json($selectedCategories),
  "categoryOptions": @json($categoryOptions),
  "settingsBase": @json($sellerAccountSettingsBase),
  "sellerUserId": @json($sellerAccountUserId),
  "initialSuccess": @json($sellerAccountInitialSuccess),
  "initialError": @json($sellerAccountInitialError),
  "initialValidation": @json($sellerAccountInitialValidation)
}
</script>
</body>
</html>
