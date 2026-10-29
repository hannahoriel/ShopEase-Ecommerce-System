@php
    // Values will be populated by the POST route after company details are stored in session.
    $companyData = session('logistics_registration.company', []);
    if (!is_array($companyData)) $companyData = [];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ShopEase - Logistics Company Registration</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@vite(['resources/css/app.css', 'resources/css/logistics/signup-firstpage.css', 'resources/js/logistics/signup-firstpage.js', 'resources/js/logistics/logistics-registration-navigation.js'])







<meta name="logistics-hidden-icon" content="{{ asset('icons/login/hide-password.png') }}">
<meta name="logistics-shown-icon" content="{{ asset('icons/login/show-password.png') }}">
</head>
<body>
<div class="registration-page">
  <header class="registration-header">
    <a href="{{ url('/logistics/landing') }}" class="header-logo-wrapper" aria-label="Return to logistics landing page">
      <img src="{{ asset('icons/login/signup-logo.png') }}" alt="ShopEase" class="signup-logo">
    </a>
    <div class="header-copy">
      <h1 class="header-title">Logistics <span>Registration</span></h1>
      <p class="header-subtitle">Fill in your company's details to become a ShopEase logistics partner.</p>
    </div>
  </header>
  <nav class="stepper-wrapper" aria-label="Registration progress">
  <a href="/logistics/register/company" class="step step-clickable active" aria-current="step"><span class="step-number">1</span><span class="step-label">Company Details</span></a>
  <a href="/logistics/register/permits" class="step step-clickable " ><span class="step-number">2</span><span class="step-label">Permits &amp; Certificates</span></a>
  <a href="/logistics/register/review" class="step step-clickable " ><span class="step-number">3</span><span class="step-label">Review Information</span></a>
</nav>
  <main class="registration-content">
    <form id="logisticsCompanyForm" autocomplete="off" method="POST" action="{{ route('logistics.register.company.submit') }}" novalidate>
      @csrf
      <div class="registration-card">
        @if ($errors->any())
        <div class="form-message" role="alert">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>
        @endif
        @if(session('status'))<div class="form-message success" role="status">{{ session('status') }}</div>@endif
        <div class="form-grid">
          <h2 class="section-heading">Company Information</h2>
<div class="form-group excel-extra"><label class="form-label" for="sec_registration_number">SEC Registration No. <span class="required">*</span></label><input class="form-input" id="sec_registration_number" name="sec_registration_number" required maxlength="11" pattern="[A-Za-z]{2}[0-9]{9}" placeholder="CS202012345" value="{{ old('sec_registration_number', $companyData['sec_registration_number'] ?? '') }}"><small class="client-error" data-error-for="sec_registration_number"></small></div>
<div class="form-group excel-extra"><label class="form-label" for="tin">TIN <span class="required">*</span></label><input class="form-input" id="tin" name="tin" required maxlength="15" pattern="[0-9]{3}-[0-9]{3}-[0-9]{3}-[0-9]{3}" placeholder="234-567-890-000" value="{{ old('tin', $companyData['tin'] ?? '') }}"><small class="client-error" data-error-for="tin"></small></div>
<div class="form-group" style="grid-column:1/-1"><label class="form-label">Service Areas <span class="required">*</span> (select one or more)</label><div class="excel-areas" id="serviceAreas">@php($regionsList=['NCR','CAR','Region I','Region II','Region III','Region IV-A','MIMAROPA','Region V','Region VI','Region VII','Region VIII','Region IX','Region X','Region XI','Region XII','Region XIII','BARMM']) @foreach($regionsList as $area)<label><input type="checkbox" name="service_areas[]" value="{{ $area }}" @checked(in_array($area,old('service_areas',$companyData['service_areas'] ?? [])))> {{ $area }}</label>@endforeach</div><small class="excel-error" id="serviceAreasError"></small></div>

          <p class="section-caption">Provide your company's official information. Permits and certificates will be uploaded on Step 2.</p>
<div class="form-group"><label class="form-label" for="company_name">Company / Logistics Business Name <span class="required">*</span></label><input type="text" id="company_name" name="company_name" class="form-input" placeholder="Enter your logistics company name" value="{{ old('company_name', $companyData['company_name'] ?? '') }}" required><small class="client-error" data-error-for="company_name"></small></div><div class="form-group"><label class="form-label" for="registered_business_name">Registered Legal Business Name <span class="required">*</span></label><input type="text" id="registered_business_name" name="registered_business_name" class="form-input" placeholder="Enter the registered business name" value="{{ old('registered_business_name', $companyData['registered_business_name'] ?? '') }}" required><small class="client-error" data-error-for="registered_business_name"></small></div><div class="form-group"><label class="form-label" for="company_type">Company Type <span class="required">*</span></label><select id="company_type" name="company_type" class="form-input" required><option value="">Select company type</option><option value="Corporation" @selected(old('company_type', $companyData['company_type'] ?? '') === 'Corporation')>Corporation</option><option value="Partnership" @selected(old('company_type', $companyData['company_type'] ?? '') === 'Partnership')>Partnership</option><option value="Sole Proprietorship" @selected(old('company_type', $companyData['company_type'] ?? '') === 'Sole Proprietorship')>Sole Proprietorship</option><option value="Cooperative" @selected(old('company_type', $companyData['company_type'] ?? '') === 'Cooperative')>Cooperative</option><option value="Other" @selected(old('company_type', $companyData['company_type'] ?? '') === 'Other')>Other</option></select><small class="client-error" data-error-for="company_type"></small></div><div class="form-group"><label class="form-label" for="years_in_operation">Years in Operation</label><input type="number" id="years_in_operation" name="years_in_operation" class="form-input" placeholder="e.g. 5" value="{{ old('years_in_operation', $companyData['years_in_operation'] ?? '') }}" min="0" ><small class="client-error" data-error-for="years_in_operation"></small></div><div class="form-group"><label class="form-label" for="company_email">Company Email Address <span class="required">*</span></label><input type="email" id="company_email" name="company_email" class="form-input" placeholder="Enter official company email" value="{{ old('company_email', $companyData['company_email'] ?? '') }}" autocomplete="email" required><small class="client-error" data-error-for="company_email"></small></div><div class="form-group"><label class="form-label" for="company_phone">Company Contact Number <span class="required">*</span></label><input type="tel" id="company_phone" name="company_phone" class="form-input" placeholder="e.g. 09171234567" value="{{ old('company_phone', $companyData['company_phone'] ?? '') }}" required><small class="client-error" data-error-for="company_phone"></small></div><div class="form-group"><label class="form-label" for="website">Company Website (Optional)</label><input type="url" id="website" name="website" class="form-input" placeholder="https://example.com" value="{{ old('website', $companyData['website'] ?? '') }}" ><small class="client-error" data-error-for="website"></small></div><div class="form-group"><label class="form-label" for="branch_count">Number of Branches (Optional)</label><input type="number" id="branch_count" name="branch_count" class="form-input" placeholder="e.g. 5" value="{{ old('branch_count', $companyData['branch_count'] ?? '') }}" min="0" ><small class="client-error" data-error-for="branch_count"></small></div>
          <hr class="section-divider">
          <h2 class="section-heading">Authorized Representative</h2>
          <p class="section-caption">Who will serve as your company's primary contact for ShopEase?</p>
<div class="form-group"><label class="form-label" for="representative_name">Authorized Representative Name <span class="required">*</span></label><input type="text" id="representative_name" name="representative_name" class="form-input" placeholder="Enter first and last name" value="{{ old('representative_name', $companyData['representative_name'] ?? '') }}" required><small class="client-error" data-error-for="representative_name"></small></div><div class="form-group"><label class="form-label" for="representative_position">Representative Position <span class="required">*</span></label><input type="text" id="representative_position" name="representative_position" class="form-input" placeholder="e.g. Operations Manager" value="{{ old('representative_position', $companyData['representative_position'] ?? '') }}" required><small class="client-error" data-error-for="representative_position"></small></div><div class="form-group"><label class="form-label" for="representative_email">Representative Email Address <span class="required">*</span></label><input type="email" id="representative_email" name="representative_email" class="form-input" placeholder="Enter representative email" value="{{ old('representative_email', $companyData['representative_email'] ?? '') }}" autocomplete="email" required><small class="client-error" data-error-for="representative_email"></small></div><div class="form-group"><label class="form-label" for="representative_phone">Representative Contact Number <span class="required">*</span></label><input type="tel" id="representative_phone" name="representative_phone" class="form-input" placeholder="e.g. 09171234567" value="{{ old('representative_phone', $companyData['representative_phone'] ?? '') }}" required><small class="client-error" data-error-for="representative_phone"></small></div><div class="form-group"><label class="form-label" for="representative_sex">Representative Sex <span class="required">*</span></label><select id="representative_sex" name="representative_sex" class="form-input" required><option value="">Select</option><option value="male" @selected(old('representative_sex', $companyData['representative_sex'] ?? '') === 'male')>Male</option><option value="female" @selected(old('representative_sex', $companyData['representative_sex'] ?? '') === 'female')>Female</option><option value="other" @selected(old('representative_sex', $companyData['representative_sex'] ?? '') === 'other')>Other</option></select><small class="client-error" data-error-for="representative_sex"></small></div><div class="form-group"><label class="form-label" for="representative_birthday">Representative Date of Birth <span class="required">*</span></label><input type="date" id="representative_birthday" name="representative_birthday" class="form-input" value="{{ old('representative_birthday', $companyData['representative_birthday'] ?? '') }}" required><small class="client-error" data-error-for="representative_birthday"></small></div>
          <hr class="section-divider">
          <h2 class="section-heading">Registered Office Address</h2>
          <p class="section-caption">Enter the address of the company's registered head office.</p>
<div class="logistics-address-five">
<div class="form-group"><label class="form-label" for="region">Region <span class="required">*</span></label><select id="region" name="region" class="form-input" required><option value="">Select region</option>
<option value="National Capital Region (NCR)" @selected(old('region', $companyData['region'] ?? '') === 'National Capital Region (NCR)')>National Capital Region (NCR)</option>
<option value="Cordillera Administrative Region (CAR)" @selected(old('region', $companyData['region'] ?? '') === 'Cordillera Administrative Region (CAR)')>Cordillera Administrative Region (CAR)</option>
<option value="Region I (Ilocos Region)" @selected(old('region', $companyData['region'] ?? '') === 'Region I (Ilocos Region)')>Region I (Ilocos Region)</option>
<option value="Region II (Cagayan Valley)" @selected(old('region', $companyData['region'] ?? '') === 'Region II (Cagayan Valley)')>Region II (Cagayan Valley)</option>
<option value="Region III (Central Luzon)" @selected(old('region', $companyData['region'] ?? '') === 'Region III (Central Luzon)')>Region III (Central Luzon)</option>
<option value="Region IV-A (CALABARZON)" @selected(old('region', $companyData['region'] ?? '') === 'Region IV-A (CALABARZON)')>Region IV-A (CALABARZON)</option>
<option value="MIMAROPA Region" @selected(old('region', $companyData['region'] ?? '') === 'MIMAROPA Region')>MIMAROPA Region</option>
<option value="Region V (Bicol Region)" @selected(old('region', $companyData['region'] ?? '') === 'Region V (Bicol Region)')>Region V (Bicol Region)</option>
<option value="Region VI (Western Visayas)" @selected(old('region', $companyData['region'] ?? '') === 'Region VI (Western Visayas)')>Region VI (Western Visayas)</option>
<option value="Region VII (Central Visayas)" @selected(old('region', $companyData['region'] ?? '') === 'Region VII (Central Visayas)')>Region VII (Central Visayas)</option>
<option value="Region VIII (Eastern Visayas)" @selected(old('region', $companyData['region'] ?? '') === 'Region VIII (Eastern Visayas)')>Region VIII (Eastern Visayas)</option>
<option value="Region IX (Zamboanga Peninsula)" @selected(old('region', $companyData['region'] ?? '') === 'Region IX (Zamboanga Peninsula)')>Region IX (Zamboanga Peninsula)</option>
<option value="Region X (Northern Mindanao)" @selected(old('region', $companyData['region'] ?? '') === 'Region X (Northern Mindanao)')>Region X (Northern Mindanao)</option>
<option value="Region XI (Davao Region)" @selected(old('region', $companyData['region'] ?? '') === 'Region XI (Davao Region)')>Region XI (Davao Region)</option>
<option value="Region XII (SOCCSKSARGEN)" @selected(old('region', $companyData['region'] ?? '') === 'Region XII (SOCCSKSARGEN)')>Region XII (SOCCSKSARGEN)</option>
<option value="Region XIII (Caraga)" @selected(old('region', $companyData['region'] ?? '') === 'Region XIII (Caraga)')>Region XIII (Caraga)</option>
<option value="Bangsamoro Autonomous Region in Muslim Mindanao (BARMM)" @selected(old('region', $companyData['region'] ?? '') === 'Bangsamoro Autonomous Region in Muslim Mindanao (BARMM)')>Bangsamoro Autonomous Region in Muslim Mindanao (BARMM)</option>
</select><small class="client-error" data-error-for="region"></small></div>
<div class="form-group"><label class="form-label" for="provinceSearch">Province <span class="required">*</span></label><div class="lg-location" data-location="province"><input id="provinceSearch" class="form-input lg-location-search" autocomplete="off" role="combobox" aria-expanded="false" aria-controls="provinceOptions" placeholder="Type to search province" value="{{ old('province', $companyData['province'] ?? '') }}"><span class="lg-location-arrow" aria-hidden="true"></span><div id="provinceOptions" class="lg-location-options" role="listbox"></div></div><input type="hidden" id="province" name="province" value="{{ old('province', $companyData['province'] ?? '') }}" required><small class="client-error" data-error-for="province"></small></div>
<div class="form-group"><label class="form-label" for="citySearch">City / Municipality <span class="required">*</span></label><div class="lg-location" data-location="city"><input id="citySearch" class="form-input lg-location-search" autocomplete="off" role="combobox" aria-expanded="false" aria-controls="cityOptions" placeholder="Select province first" disabled value="{{ old('city', $companyData['city'] ?? '') }}"><span class="lg-location-arrow" aria-hidden="true"></span><div id="cityOptions" class="lg-location-options" role="listbox"></div></div><input type="hidden" id="city" name="city" value="{{ old('city', $companyData['city'] ?? '') }}" required><small class="client-error" data-error-for="city"></small></div>
<div class="form-group"><label class="form-label" for="barangaySearch">Barangay <span class="required">*</span></label><div class="lg-location" data-location="barangay"><input id="barangaySearch" class="form-input lg-location-search" autocomplete="off" role="combobox" aria-expanded="false" aria-controls="barangayOptions" placeholder="Select municipality first" disabled value="{{ old('barangay', $companyData['barangay'] ?? '') }}"><span class="lg-location-arrow" aria-hidden="true"></span><div id="barangayOptions" class="lg-location-options" role="listbox"></div></div><input type="hidden" id="barangay" name="barangay" value="{{ old('barangay', $companyData['barangay'] ?? '') }}" required><small class="client-error" data-error-for="barangay"></small></div>
<div class="form-group"><label class="form-label" for="postal_code">ZIP Code <span class="required">*</span></label><input type="text" id="postal_code" name="postal_code" class="form-input" placeholder="4-digit ZIP" value="{{ old('postal_code', $companyData['postal_code'] ?? '') }}" inputmode="numeric" minlength="4" maxlength="4" pattern="[0-9]{4}" required><small class="client-error" data-error-for="postal_code"></small></div>
</div>
<!-- Same second address row as ShopEase Seller Registration -->
<input type="hidden" id="street_address" name="street_address" value="{{ old('street_address', $companyData['street_address'] ?? '') }}">
<div class="logistics-address-bottom">
  <div class="form-group"><label class="form-label" for="street">Street</label><input type="text" id="street" name="street" class="form-input" placeholder="Enter street" value="{{ old('street', $companyData['street'] ?? '') }}"><small class="client-error" data-error-for="street"></small></div>
  <div class="form-group"><label class="form-label" for="house_no">House No.</label><input type="text" id="house_no" name="house_no" class="form-input" placeholder="Enter house no." value="{{ old('house_no', $companyData['house_no'] ?? '') }}"><small class="client-error" data-error-for="house_no"></small></div>
  <div class="form-group"><label class="form-label" for="building">Building / Unit</label><input type="text" id="building" name="building" class="form-input" placeholder="Enter building or unit" value="{{ old('building', $companyData['building'] ?? '') }}"><small class="client-error" data-error-for="building"></small></div>
  <div class="form-group"><label class="form-label" for="subdivision">Subdivision</label><input type="text" id="subdivision" name="subdivision" class="form-input" placeholder="Enter subdivision" value="{{ old('subdivision', $companyData['subdivision'] ?? '') }}"><small class="client-error" data-error-for="subdivision"></small></div>
</div>
<hr class="section-divider">
<h2 class="section-heading">Account Security</h2>
<p class="section-caption">Create a secure password for your logistics company account.</p>
<div class="form-group logistics-password-field"><label class="form-label" for="password">Password <span class="required">*</span></label><div class="password-input-wrapper"><input type="password" id="password" name="password" class="form-input" placeholder="Create a password" minlength="8" autocomplete="new-password" required><button type="button" class="password-eye-button" id="passwordEyeButton" aria-label="Show password" aria-pressed="false"><img src="{{ asset('icons/login/hide-password.png') }}" alt="Password hidden" id="passwordEyeIcon"></button></div><small class="field-help">At least 8 characters with uppercase and lowercase letters, a number, and a special character.</small><div class="se-password-rules" id="logisticsPasswordRules" aria-live="polite">
 <span data-rule="length">At least 8 characters</span>
 <span data-rule="upper">One uppercase letter</span>
 <span data-rule="lower">One lowercase letter</span>
 <span data-rule="number">One number</span>
 <span data-rule="special">One special character</span>
</div><small class="client-error" data-error-for="password"></small></div>
<div class="form-group logistics-password-field"><label class="form-label" for="password_confirmation">Confirm Password <span class="required">*</span></label><div class="password-input-wrapper"><input type="password" id="password_confirmation" name="password_confirmation" class="form-input" placeholder="Confirm your password" minlength="8" autocomplete="new-password" required><button type="button" class="password-eye-button" id="passwordConfirmationEyeButton" aria-label="Show password" aria-pressed="false"><img src="{{ asset('icons/login/hide-password.png') }}" alt="Password hidden" id="passwordConfirmationEyeIcon"></button></div><p class="password-match-error" id="passwordConfirmationError" role="alert">Passwords do not match.</p><small class="client-error" data-error-for="password_confirmation"></small></div>
        </div>
        <div class="excel-terms"><label><input id="accept_terms" name="accept_terms" type="checkbox" value="1" @checked(old('accept_terms', $companyData['accept_terms'] ?? false)) required> I agree to the Terms and Conditions and Privacy Policy. <span class="required">*</span></label><small class="excel-error" id="termsError"></small></div><div class="form-actions">
          <span class="step-notice">Step 1 of 3 · Company Details</span>
          <button type="submit" class="next-button" id="nextButton">Continue</button>
        </div>
      </div>
    </form>
  </main>
</div>






<script>
// Refresh the server-confirmed version when browser Back/Forward restores a cached form.
window.addEventListener('pageshow', function (event) {
    if (event.persisted) window.location.reload();
});
</script>
</body>
</html>
