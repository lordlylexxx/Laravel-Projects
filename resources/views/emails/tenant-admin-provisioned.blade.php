<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Admin Account Ready</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #1f2937; margin: 0; padding: 24px; background: #f8fafc;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 640px; margin: 0 auto; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden;">
        <tr>
            <td style="background: #166534; color: #ffffff; padding: 18px 24px;">
                <h1 style="margin: 0; font-size: 20px;">Your business account is ready</h1>
            </td>
        </tr>
        <tr>
            <td style="padding: 24px;">
                <p style="margin-top: 0;">Hi {{ $ownerName }},</p>
                <p>Your business was registered successfully on ImpaStay. We also created a tenant admin account for <strong>{{ $businessName }}</strong>.</p>

                <h2 style="font-size: 16px; margin: 24px 0 10px; color: #111827;">Business URL</h2>
                <p style="margin: 0 0 16px;"><a href="{{ $businessUrl }}" style="color: #166534;">{{ $businessUrl }}</a></p>

                <h2 style="font-size: 16px; margin: 24px 0 10px; color: #111827;">Tenant Admin Credentials</h2>
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse: collapse; border: 1px solid #e5e7eb;">
                    <tr>
                        <td style="padding: 10px; width: 180px; border-bottom: 1px solid #e5e7eb; background: #f9fafb;"><strong>Username (email)</strong></td>
                        <td style="padding: 10px; border-bottom: 1px solid #e5e7eb;">{{ $adminEmail }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; width: 180px; background: #f9fafb;"><strong>Random password</strong></td>
                        <td style="padding: 10px;">{{ $adminPassword }}</td>
                    </tr>
                </table>

                <p style="margin: 20px 0 0;">For security, please sign in and change this password immediately.</p>
            </td>
        </tr>
    </table>
</body>
</html>
