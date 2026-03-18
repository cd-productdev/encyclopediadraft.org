<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f9f9f9; padding: 20px; border-radius: 5px;">
        <p>Someone, probably you, from IP address <strong>{{ $ipAddress }}</strong>,</p>
        
        <p>has registered an account "<strong>{{ $username }}</strong>" with this email address on {{ config('app.name', 'WikiEngine Bios & Wiki') }}.</p>
        
        <p>To confirm that this account really does belong to you and activate email features on {{ config('app.name', 'WikiEngine Bios & Wiki') }}, open this link in your browser:</p>
        
        <p style="margin: 20px 0;">
            <a href="{{ $confirmationUrl }}" style="color: #0066cc; word-break: break-all;">{{ $confirmationUrl }}</a>
        </p>
        
        <p>If you did <strong>*not*</strong> register the account, follow this link to cancel the email address confirmation:</p>
        
        <p style="margin: 20px 0;">
            <a href="{{ $invalidationUrl }}" style="color: #cc0000; word-break: break-all;">{{ $invalidationUrl }}</a>
        </p>
        
        <p>This confirmation code will expire at <strong>{{ $expiresAt }}</strong>.</p>
    </div>
</body>
</html>






