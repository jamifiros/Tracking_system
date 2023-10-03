<!DOCTYPE html>
<html lang="en">
<head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Document</title>
</head>
<body>
    <p>Hello {{ $name }},</p>
    
    <p>We received a request to reset your password. Your password reset code is:</p>

    <p><strong>{{ $code }}</strong></p>

    <p>If you didn't request a password reset, you can ignore this email.</p>

    <p>Thank you!</p>
</body>
</html>