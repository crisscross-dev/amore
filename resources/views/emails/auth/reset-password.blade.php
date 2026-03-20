<x-mail::message>
# Reset Your Password

Hello {{ $userName }},

You are receiving this email because we received a password reset request for your account at Amore Academy.

<x-mail::button :url="$resetUrl" color="success">
Reset Password
</x-mail::button>

This password reset link will expire in 60 minutes.

If you did not request a password reset, no further action is required. Your account remains secure.

**Security Tips:**
- Never share your password with anyone
- Use a strong, unique password
- If you notice any suspicious activity, contact us immediately

Thanks,<br>
{{ config('app.name') }}

<x-mail::subcopy>
If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:
[{{ $resetUrl }}]({{ $resetUrl }})
</x-mail::subcopy>
</x-mail::message>
