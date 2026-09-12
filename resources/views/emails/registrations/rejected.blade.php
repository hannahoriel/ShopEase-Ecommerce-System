@component('mail::message')
# Update on your ShopEase registration

Hi {{ $registration->first_name }}, thanks for your interest in ShopEase. After reviewing your **{{ ucfirst($registration->user_type) }}** registration, we're unable to approve it at this time.

**Reason:** {{ $registration->rejection_reason }}

@if($registration->rejection_details)
**Additional details:** {{ $registration->rejection_details }}
@endif

You're welcome to correct the issue above and submit a new registration.

@component('mail::button', ['url' => url('/auth/signup')])
Register Again
@endcomponent

Thanks,<br>
The ShopEase Team
@endcomponent
