<!DOCTYPE html>
<html>
<head>
    <title>Verify Your Email Address</title>
</head>
<body>
    <h1>Verify Your Email Address</h1>
    <p>Hi, {{ $name }}</p>
    <p>Thank you for registering! Please verify your email address by clicking the link below:</p>
    <a href="{{ $url }}">Verify Email Address</a>
    <p>If you did not register for an account, please ignore this email.</p>
    <p>Thanks,<br>{{ config('app.name') }}</p>
</body>
</html>
