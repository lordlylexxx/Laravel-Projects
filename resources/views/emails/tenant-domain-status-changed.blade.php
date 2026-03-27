<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenant Domain Status Updated</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #1f2937; margin: 0; padding: 24px; background: #f8fafc;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 640px; margin: 0 auto; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden;">
        <tr>
            <td style="background: #166534; color: #ffffff; padding: 18px 24px;">
                <h1 style="margin: 0; font-size: 20px;">Tenant Domain Status Updated</h1>
            </td>
        </tr>
        <tr>
            <td style="padding: 24px;">
                <p style="margin-top: 0;">Hi {{ $ownerName }},</p>
                <p>The domain access status for <strong>{{ $tenantName }}</strong> was changed by <strong>{{ $changedBy }}</strong>.</p>
                <p><strong>New status:</strong> {{ $enabled ? 'Enabled' : 'Disabled' }}</p>
                <p><strong>Business URL:</strong> <a href="{{ $businessUrl }}" style="color: #166534;">{{ $businessUrl }}</a></p>
                @if(!empty($reason))
                    <p><strong>Reason:</strong> {{ $reason }}</p>
                @endif
            </td>
        </tr>
    </table>
</body>
</html>
