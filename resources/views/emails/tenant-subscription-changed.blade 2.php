<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenant Subscription Updated</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #1f2937; margin: 0; padding: 24px; background: #f8fafc;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 640px; margin: 0 auto; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden;">
        <tr>
            <td style="background: #166534; color: #ffffff; padding: 18px 24px;">
                <h1 style="margin: 0; font-size: 20px;">Tenant Subscription Updated</h1>
            </td>
        </tr>
        <tr>
            <td style="padding: 24px;">
                <p style="margin-top: 0;">Hi {{ $ownerName }},</p>
                <p>Your tenant subscription for <strong>{{ $tenantName }}</strong> was updated by <strong>{{ $changedBy }}</strong>.</p>
                <p><strong>Plan:</strong> {{ \App\Models\Tenant::planLabel($plan) }}</p>
                <p><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $subscriptionStatus)) }}</p>
                @if($periodEndsAt)
                    <p><strong>Current period ends:</strong> {{ \Illuminate\Support\Carbon::parse($periodEndsAt)->format('M d, Y') }}</p>
                @endif
                @if(!empty($reason))
                    <p><strong>Reason:</strong> {{ $reason }}</p>
                @endif
            </td>
        </tr>
    </table>
</body>
</html>
