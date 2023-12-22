@component('mail::message')
    # Verify Your Email Address

    Thanks for registering! Please click the button below to verify your email address:
    
    @component('mail::button', ['url' => route('verify-token', $user->verification_token)])
        Verify Email
    @endcomponent

    If you did not create an account, no further action is required.

@endcomponent