<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body>
    <h1>Hello {{ $name }},</h1>
    <p>We received a request to reset your password. Click the link below to reset it:</p>
    <a href="{{ $url }}">Reset Password</a>
    <p>If you did not request a password reset, please ignore this email.</p>
</body>
</html>
