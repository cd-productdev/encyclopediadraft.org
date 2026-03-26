<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your account</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f9f9f9; padding: 20px; border-radius: 5px;">
        <p>Hello <strong>{{ $userName }}</strong>,</p>

        <p>An account has been created for you on <strong>{{ config('app.name') }}</strong>.</p>

        <p>You can sign in with the following credentials:</p>

        <ul style="list-style: none; padding-left: 0;">
            <li><strong>Email:</strong> {{ $email }}</li>
            <li><strong>Password:</strong> {{ $plainPassword }}</li>
        </ul>

        <p style="margin: 20px 0;">
            <a href="{{ $loginUrl }}" style="display: inline-block; background-color: #2563eb; color: #fff; padding: 10px 18px; text-decoration: none; border-radius: 6px;">Sign in</a>
        </p>

        <p style="font-size: 14px; color: #555;">For security, change your password after you log in.</p>
    </div>
</body>
</html>
