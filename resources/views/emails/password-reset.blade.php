<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #f9f9f9; padding: 25px; border-radius: 8px;">
        
        <p>Hello,</p>

        <p>You recently requested to reset your password for your 
            <strong>{{ config('app.name', 'WikiEngine Bios & Wiki') }}</strong> account.
        </p>

        <p>Please click the button below to reset your password:</p>

        <p style="text-align: center; margin: 30px 0;">
            <a href="{{ config('app.frontend_url') }}"
               style="background-color: #0066cc; color: white; padding: 12px 24px; 
                      text-decoration: none; border-radius: 5px; display: inline-block;">
                Reset Password
            </a>
        </p>

        <p><strong>Your reset token:</strong> {{ $token }}</p>

        <p>This password reset link will expire on <strong>{{ $expiresAt }}</strong>.</p>

        <p>If you did not request a password reset, you can safely ignore this email.</p>

        <p style="margin-top: 40px; color: #666; font-size: 12px; text-align: center;">
            This is an automated message. Please do not reply to this email.
        </p>
    </div>
</body>
</html>




