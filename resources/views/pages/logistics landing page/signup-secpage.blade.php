@php
    $companyData = session('logistics_registration.company', []);
    $savedDocuments = session('logistics_registration.documents', []);
    if (!is_array($companyData)) $companyData = [];
    if (!is_array($savedDocuments)) $savedDocuments = [];
@endphp
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>ShopEase - Logistics Permits & Certificates</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@vite(['resources/css/app.css', 'resources/css/logistics/signup-secpage.css', 'resources/js/logistics/signup-secpage.js', 'resources/js/logistics/logistics-registration-navigation.js'])


</head><body>
<div class="registration-page">
<header class="registration-header"><a class="header-logo-wrapper" href="{{ url('/logistics/landing') }}" aria-label="Exit logistics registration"><img class="signup-logo" src="{{ asset('icons/login/signup-logo.png') }}" alt="ShopEase"></a><div><h1 class="header-title">Logistics <span>Registration</span></h1><p class="header-subtitle">Submit company documents to become a ShopEase logistics partner.</p></div></header>
<nav class="stepper-wrapper" aria-label="Registration progress">
  <a href="/logistics/register/company" class="step step-clickable " ><span class="step-number">1</span><span class="step-label">Company Details</span></a>
  <a href="/logistics/register/permits" class="step step-clickable active" aria-current="step"><span class="step-number">2</span><span class="step-label">Permits &amp; Certificates</span></a>
  <a href="/logistics/register/review" class="step step-clickable " ><span class="step-number">3</span><span class="step-label">Review Information</span></a>
</nav>
<main class="registration-content"><form id="logisticsPermitsForm" autocomplete="off" novalidate method="POST" action="{{ route('logistics.register.permits.submit') }}" enctype="multipart/form-data">@csrf
<section class="registration-card"><h2 class="section-title">Permits &amp; Certificates</h2><p class="section-desc">Upload clear photos or PDF copies of your company's business documents. Your documents will be reviewed during partner verification.</p>
@if(session('status'))<div class="notice success" role="status">{{ session('status') }}</div>@endif
@if($errors->any())<div class="notice error" role="alert">Please check the highlighted uploads. @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif
<div class="notice">Company: <strong>{{ $companyData['company_name'] ?? 'Not saved yet' }}</strong>. Required uploads are marked with <span class="required">*</span>. Save your files here before moving to Step 3 (Review Information).</div>
<div class="document-grid">
<div class="document-field">
<label class="document-label" for="business_registration">SEC / DTI / CDA Registration <span class="required">*</span></label>
<p class="document-help">Registration certificate matching the company type.</p>
<label class="upload-box" id="box_business_registration">
<input type="file" id="business_registration" name="business_registration" accept=".jpg,.jpeg,.png,.pdf,image/jpeg,image/png,application/pdf" {{ empty($savedDocuments['business_registration']['path'] ?? null) ? 'required' : '' }} aria-describedby="error_business_registration" data-document="business_registration">
<span class="upload-contents"><img class="preview-image" id="preview_business_registration" alt="Preview of selected document"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 16-5-5-6 6-3-3-4 4"/></svg><span class="upload-title" id="label_business_registration">Click to upload a document</span><span class="upload-hint">JPG, PNG, PDF · Max 5 MB</span></span>
</label>
@if(!empty($savedDocuments['business_registration']['original_name'] ?? null))<span class="already-file" id="stored_business_registration">Previously uploaded: {{ $savedDocuments['business_registration']['original_name'] }}</span>@endif
@error('business_registration')<small class="field-error">{{ $message }}</small>@enderror
<small class="field-error" id="error_business_registration" aria-live="polite"></small>
</div>
<div class="document-field">
<label class="document-label" for="mayors_permit">Mayor’s / Business Permit <span class="required">*</span></label>
<p class="document-help">Current LGU permit for your registered business address.</p>
<label class="upload-box" id="box_mayors_permit">
<input type="file" id="mayors_permit" name="mayors_permit" accept=".jpg,.jpeg,.png,.pdf,image/jpeg,image/png,application/pdf" {{ empty($savedDocuments['mayors_permit']['path'] ?? null) ? 'required' : '' }} aria-describedby="error_mayors_permit" data-document="mayors_permit">
<span class="upload-contents"><img class="preview-image" id="preview_mayors_permit" alt="Preview of selected document"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 16-5-5-6 6-3-3-4 4"/></svg><span class="upload-title" id="label_mayors_permit">Click to upload a document</span><span class="upload-hint">JPG, PNG, PDF · Max 5 MB</span></span>
</label>
@if(!empty($savedDocuments['mayors_permit']['original_name'] ?? null))<span class="already-file" id="stored_mayors_permit">Previously uploaded: {{ $savedDocuments['mayors_permit']['original_name'] }}</span>@endif
@error('mayors_permit')<small class="field-error">{{ $message }}</small>@enderror
<small class="field-error" id="error_mayors_permit" aria-live="polite"></small>
</div>
<div class="document-field">
<label class="document-label" for="bir_certificate">BIR Certificate of Registration (Form 2303) <span class="required">*</span></label>
<p class="document-help">BIR registration certificate showing business details.</p>
<label class="upload-box" id="box_bir_certificate">
<input type="file" id="bir_certificate" name="bir_certificate" accept=".jpg,.jpeg,.png,.pdf,image/jpeg,image/png,application/pdf" {{ empty($savedDocuments['bir_certificate']['path'] ?? null) ? 'required' : '' }} aria-describedby="error_bir_certificate" data-document="bir_certificate">
<span class="upload-contents"><img class="preview-image" id="preview_bir_certificate" alt="Preview of selected document"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 16-5-5-6 6-3-3-4 4"/></svg><span class="upload-title" id="label_bir_certificate">Click to upload a document</span><span class="upload-hint">JPG, PNG, PDF · Max 5 MB</span></span>
</label>
@if(!empty($savedDocuments['bir_certificate']['original_name'] ?? null))<span class="already-file" id="stored_bir_certificate">Previously uploaded: {{ $savedDocuments['bir_certificate']['original_name'] }}</span>@endif
@error('bir_certificate')<small class="field-error">{{ $message }}</small>@enderror
<small class="field-error" id="error_bir_certificate" aria-live="polite"></small>
</div>
<div class="document-field">
<label class="document-label" for="courier_authority">Courier / Delivery Accreditation (If Applicable) <span style="color:#999;font-size:12px">(Optional)</span></label>
<p class="document-help">Optional permits or accreditations related to delivery operations.</p>
<label class="upload-box" id="box_courier_authority">
<input type="file" id="courier_authority" name="courier_authority" accept=".jpg,.jpeg,.png,.pdf,image/jpeg,image/png,application/pdf"  aria-describedby="error_courier_authority" data-document="courier_authority">
<span class="upload-contents"><img class="preview-image" id="preview_courier_authority" alt="Preview of selected document"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 16-5-5-6 6-3-3-4 4"/></svg><span class="upload-title" id="label_courier_authority">Click to upload if available</span><span class="upload-hint">JPG, PNG, PDF · Max 5 MB</span></span>
</label>
@if(!empty($savedDocuments['courier_authority']['original_name'] ?? null))<span class="already-file" id="stored_courier_authority">Previously uploaded: {{ $savedDocuments['courier_authority']['original_name'] }}</span>@endif
@error('courier_authority')<small class="field-error">{{ $message }}</small>@enderror
<small class="field-error" id="error_courier_authority" aria-live="polite"></small>
</div>
<div class="document-field">
<label class="document-label" for="insurance_certificate">Business / Cargo Liability Insurance <span style="color:#999;font-size:12px">(Optional)</span></label>
<p class="document-help">Current coverage certificate, if your company has one.</p>
<label class="upload-box" id="box_insurance_certificate">
<input type="file" id="insurance_certificate" name="insurance_certificate" accept=".jpg,.jpeg,.png,.pdf,image/jpeg,image/png,application/pdf"  aria-describedby="error_insurance_certificate" data-document="insurance_certificate">
<span class="upload-contents"><img class="preview-image" id="preview_insurance_certificate" alt="Preview of selected document"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 16-5-5-6 6-3-3-4 4"/></svg><span class="upload-title" id="label_insurance_certificate">Click to upload if available</span><span class="upload-hint">JPG, PNG, PDF · Max 5 MB</span></span>
</label>
@if(!empty($savedDocuments['insurance_certificate']['original_name'] ?? null))<span class="already-file" id="stored_insurance_certificate">Previously uploaded: {{ $savedDocuments['insurance_certificate']['original_name'] }}</span>@endif
@error('insurance_certificate')<small class="field-error">{{ $message }}</small>@enderror
<small class="field-error" id="error_insurance_certificate" aria-live="polite"></small>
</div>
<div class="document-field">
<label class="document-label" for="representative_authorization">Valid ID of Authorized Representative <span class="required">*</span></label>
<p class="document-help">Proof that the representative may register for the company.</p>
<label class="upload-box" id="box_representative_authorization">
<input type="file" id="representative_authorization" name="representative_authorization" {{ empty($savedDocuments['representative_authorization']['path'] ?? null) ? 'required' : '' }} accept=".jpg,.jpeg,.png,.pdf,image/jpeg,image/png,application/pdf"  aria-describedby="error_representative_authorization" data-document="representative_authorization">
<span class="upload-contents"><img class="preview-image" id="preview_representative_authorization" alt="Preview of selected document"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 16-5-5-6 6-3-3-4 4"/></svg><span class="upload-title" id="label_representative_authorization">Click to upload a valid ID</span><span class="upload-hint">JPG, PNG, PDF · Max 5 MB</span></span>
</label>
@if(!empty($savedDocuments['representative_authorization']['original_name'] ?? null))<span class="already-file" id="stored_representative_authorization">Previously uploaded: {{ $savedDocuments['representative_authorization']['original_name'] }}</span>@endif
@error('representative_authorization')<small class="field-error">{{ $message }}</small>@enderror
<small class="field-error" id="error_representative_authorization" aria-live="polite"></small>
</div>
</div>
<div class="form-actions"><a href="/logistics/register/company" class="action-button secondary">Back</a><p class="step-notice">Step 2 of 3 · Permits &amp; Certificates</p><button type="submit" id="savePermitsButton" class="action-button primary">Continue</button></div>
</section></form></main></div>

<script>
// Refresh the server-confirmed version when browser Back/Forward restores a cached form.
window.addEventListener('pageshow', function (event) {
    if (event.persisted) window.location.reload();
});
</script>
</body></html>
