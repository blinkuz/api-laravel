@component('mail::message')
    # Change Password

    @component('mail::button', ['url' => 'http://localhost:8000/api/view-token/'.$token])
        Reset Password
    @endcomponent

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
