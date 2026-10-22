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
 $adminAccountConfig = [
   'securityBase' => url('/admin/account/security'),
   'adminUserId' => (int) $adminUser->id,
   'initialSuccess' => session('success'),
   'initialError' => session('error'),
   'initialValidation' => $errors->first(),
 ];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><meta name="csrf-token" content="{{ csrf_token() }}">
<title>Admin Account — ShopEase</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
@vite(['resources/css/app.css', 'resources/css/admin/account.css', 'resources/js/app.js', 'resources/js/admin/account.js'])

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
    <div class="sa-nav-heading aam-group-heading"><svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="16" rx="3"/><circle cx="9" cy="10" r="2"/><path d="M5.5 16c.4-2 6.6-2 7 0M15 10h4M15 14h4"/></svg>Admin Account</div>
    <nav class="sa-nav-list" aria-label="Admin account management">
      <a class="sa-nav-link" href="#admin-management" data-admin-tab="admin-management">Admin Management</a>
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
   {{-- UI preview only. Never save an administrator or signing credentials in browser storage. --}}
   <div class="sa-workspace sa-tab-panel aam-workspace" id="admin-management" data-admin-panel="admin-management" hidden>
     <div class="aam-heading-row">
       <div><div class="aam-eyebrow">ADMIN ACCOUNT / ACCESS MANAGEMENT</div><h1 class="sa-page-title">Admin Management</h1><p class="sa-intro">Prepare an administrator profile, assign module access, and generate temporary login credentials.</p></div>
       <span class="aam-preview-pill">UI Preview</span>
     </div>
     <form id="aamCreateForm" autocomplete="off">
       <section class="sa-section aam-section">
         <div class="sa-section-heading"><span class="sa-section-icon"><img src="{{ asset('icons/admin/dashboard/sidebar&navbar/admin.png') }}" alt=""></span><div><h3 class="sa-section-title">Administrator Profile</h3><p class="sa-section-subtitle">Basic details for the new administrator account.</p></div></div>
         <div class="sa-fields">
           <div class="sa-field half"><label for="aamFirst">First Name <span class="sa-required">*</span></label><input class="sa-control" id="aamFirst" name="first_name" autocomplete="off" maxlength="80" required placeholder="Enter first name"></div>
           <div class="sa-field half"><label for="aamLast">Last Name <span class="sa-required">*</span></label><input class="sa-control" id="aamLast" name="last_name" autocomplete="off" maxlength="80" required placeholder="Enter last name"></div>
           <div class="sa-field half"><label for="aamMiddle">Middle Name</label><input class="sa-control" id="aamMiddle" name="middle_name" maxlength="80" placeholder="Optional"></div>
           <div class="sa-field half"><label for="aamEmail">Email Address <span class="sa-required">*</span></label><input type="email" class="sa-control" id="aamEmail" name="email" autocomplete="off" required placeholder="admin@example.com"></div>
           <div class="sa-field half"><label for="aamPhone">Contact Number</label><input type="tel" class="sa-control" id="aamPhone" name="contact_number" inputmode="numeric" maxlength="13" placeholder="09XXXXXXXXX"></div>
           <div class="sa-field half"><label for="aamBirthday">Birthday</label><input type="date" class="sa-control" id="aamBirthday" name="birthday" max="{{ now()->toDateString() }}"></div>
           <div class="sa-field half"><label for="aamSex">Sex</label><select class="sa-control" id="aamSex" name="sex"><option value="">Select sex</option><option>Male</option><option>Female</option><option>Prefer not to say</option></select></div>
           <div class="sa-field half"><label for="aamAge">Age</label><input class="sa-control" id="aamAge" readonly placeholder="Calculated from birthday"></div>
           <div class="sa-field half"><label for="aamProfilePhoto">Profile Photo</label><input class="sa-control aam-file" type="file" id="aamProfilePhoto" name="profile_photo" accept="image/png,image/jpeg,image/webp"><p class="sa-request-hint">Optional · JPG, PNG, or WebP · Max 5 MB (preview only)</p></div>
           <div class="sa-field half"><label for="aamIdPhoto">Admin ID Photo</label><input class="sa-control aam-file" type="file" id="aamIdPhoto" name="admin_id_photo" accept="image/png,image/jpeg,image/webp"><p class="sa-request-hint">Optional · For later identity verification (preview only)</p></div>
         </div>
       </section>
       <section class="sa-section aam-section">
         <div class="sa-section-heading"><span class="sa-section-icon purple"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="23" height="23"><path d="M12 3 3 7v5c0 5 3 8 9 10 6-2 9-5 9-10V7l-9-4Z"/><path d="m8 12 3 3 5-6"/></svg></span><div><h3 class="sa-section-title">Role & Access Permissions</h3><p class="sa-section-subtitle">Choose which Admin Sidebar modules this administrator can access.</p></div></div>
         <div class="aam-role-toolbar"><span id="aamRoleCount">0 modules selected</span><button type="button" id="aamSelectAll" class="aam-text-button">Select all</button><button type="button" id="aamClearAll" class="aam-text-button">Clear all</button></div>
         <div class="aam-permission-grid">
           @foreach (['Dashboard','Registrations','User Management','Seller Compliance','Complaints and Disputes','Commission','Logistics Management','Reports','Platform Settings','Notifications','Account Management'] as $permission)
             <label class="aam-permission"><input type="checkbox" name="permissions[]" value="{{ $permission }}" data-aam-permission><span>{{ $permission }}</span></label>
           @endforeach
           <div class="aam-message-permission">
             <label class="aam-permission aam-message-main"><input type="checkbox" id="aamMessages" name="permissions[]" value="Messages" data-aam-permission><span>Messages</span><small>Choose message categories below</small></label>
             <div class="aam-message-categories" id="aamMessageCategories">
               @foreach (['Logistics','Complaints','Buyer','Seller'] as $category)
                 <label class="aam-permission aam-subpermission"><input type="checkbox" name="message_permissions[]" value="{{ $category }}" data-aam-message disabled><span>{{ $category }}</span></label>
               @endforeach
             </div>
           </div>
         </div>
         <p class="sa-request-hint">These selections are only a UI preview. They will not grant actual permissions until server-side policies and middleware are implemented.</p>
       </section>
       <section class="sa-section aam-section">
         <div class="sa-section-heading"><span class="sa-section-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="23" height="23"><rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg></span><div><h3 class="sa-section-title">Temporary Login Credentials</h3><p class="sa-section-subtitle">Generate a strong password to provide securely to the administrator.</p></div></div>
         <div class="aam-credentials-layout">
           <div class="aam-password-field"><label for="aamPassword">Auto-generated Password <span class="sa-required">*</span></label><div class="aam-password-control"><input type="password" id="aamPassword" readonly required autocomplete="off" placeholder="Click Generate Password"><button type="button" id="aamRevealPassword" aria-label="Show or hide password">Show</button></div><small>Randomly generated in your browser. Not saved or sent anywhere.</small></div>
           <button type="button" class="aam-outline-btn" id="aamGenerate">↻ Generate Password</button>
           <button type="button" class="aam-outline-btn" id="aamCopyPassword">Copy Password</button>
         </div>
         <label class="aam-confirm-line"><input type="checkbox" id="aamRequireReset" checked> Require password change on first login <span>(UI placeholder)</span></label>
       </section>
       <div class="aam-form-actions"><button type="reset" class="sa-action" id="aamReset">Clear Form</button><button type="submit" class="sa-action primary" id="aamCreate">Create Admin (Preview)</button></div>
     </form>
     <section class="aam-result" id="aamResult" hidden aria-live="polite"><div class="aam-result-heading"><h2>Administrator Setup Preview</h2><span>Not saved to database</span></div><p id="aamResultSummary"></p><div class="aam-result-credentials"><div><small>Login Email</small><strong id="aamResultEmail"></strong></div><div><small>Temporary Password</small><strong id="aamResultPassword">Hidden — use Copy Credentials</strong></div></div><p id="aamResultRoles"></p><button type="button" class="aam-outline-btn" id="aamCopyCredentials">Copy Credentials</button><p class="sa-request-hint">Share credentials through a secure channel. Actual admin creation, forced password reset, notifications and role enforcement require backend development.</p></section>
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
<script type="application/json" id="adminAccountConfig">@json($adminAccountConfig)</script>

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




</body></html>
