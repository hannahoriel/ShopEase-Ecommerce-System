@component('mail::message')
# Your ShopEase account has been {{ $status }}

Hi {{ $user->first_name ?: $user->name }},

Your ShopEase account access has been **{{ $status }}** by an administrator.

**Reason:** {{ $reason }}

@if($status === 'suspended' && $duration)
**Suspension duration:** {{ $duration }} {{ $duration === 1 ? 'day' : 'days' }}
@endif

@if($details)
**Additional details:** {{ $details }}
@endif

@if($status === 'suspended')
Your account will be reactivated after the suspension period. Please contact ShopEase support if you believe this action was made in error.
@else
Your account is currently deactivated. Please contact ShopEase support if you believe this action was made in error.
@endif

Thanks,<br>
The ShopEase Team
@endcomponent
