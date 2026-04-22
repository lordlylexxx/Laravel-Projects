<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenant Onboarding Details Resent</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #1f2937; margin: 0; padding: 24px; background: #f8fafc;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 640px; margin: 0 auto; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden;">
        <tr>
            <td style="background: #166534; color: #ffffff; padding: 18px 24px;">
                <h1 style="margin: 0; font-size: 20px;">Tenant onboarding details resent</h1>
            </td>
        </tr>
        <tr>
            <td style="padding: 24px;">
                <p style="margin-top: 0;">Hi {{ $ownerName }},</p>
                <p>Your onboarding details for <strong>{{ $tenantName }}</strong> were resent by <strong>{{ $changedBy }}</strong>.</p>

                <p><strong>Business URL:</strong> <a href="{{ $businessUrl }}" style="color: #166534;">{{ $businessUrl }}</a></p>
                <p><strong>Tenant admin email:</strong> {{ $tenantAdminEmail }}</p>
                <p><strong>Reset password link:</strong> <a href="{{ $resetPasswordUrl }}" style="color: #166534;">{{ $resetPasswordUrl }}</a></p>
                <p><strong>Reason:</strong> {{ $reason }}</p>
            </td>
        </tr>
    </table>
</body>
</html>
