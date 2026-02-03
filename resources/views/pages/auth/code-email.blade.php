<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verification Code</title>
</head>
<body>
    <p>Dear {{ $admin->username ?? 'Admin' }},</p>

    <p>We received a request to reset your admin account password on the Gigat Platform.</p>

    <p><strong>Your verification code is:</strong> {{ $code }}</p>

    <p>Please enter this code on the verification page. This code is valid for 5 minutes.</p>

    <p>If you did not request a password reset, you can safely ignore this email.</p>

    <p>Regards,<br>
    Gigat Platform Support Team</p>
</body>
</html>
