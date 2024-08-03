<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body>
    <h1>Reset Your Password</h1>
    <p>Click the button below to reset your password:</p>
    <a href="{{ url('password/reset', $token + '?email=' + $email) }}">Reset Password</a>
    <p>If you did not request this, please ignore this email.</p>
</body>
</html>
