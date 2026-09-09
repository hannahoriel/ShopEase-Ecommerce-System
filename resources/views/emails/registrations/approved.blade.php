@component('mail::message')
# Welcome to ShopEase, {{ $registration->first_name }}!

Your **{{ ucfirst($registration->user_type) }}** registration has been reviewed and **approved**. You can now log in and start using your account.

@component('mail::button', ['url' => url('/auth/login')])
Log In to ShopEase
@endcomponent

If you didn't request this account, please contact our support team.

Thanks,<br>
The ShopEase Team
@endcomponent
