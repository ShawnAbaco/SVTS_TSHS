<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Password Change Verification</title>
</head>
<body>
    <h2>Password Change Verification Code</h2>
    
    <p>Hello <strong>{{ $name }}</strong>,</p>
    
    <p>You have requested to change your password for the Student Violation Tracking System.</p>
    
    <p>Your verification code is:</p>
    
    <h1 style="background: #3498db; color: white; padding: 20px; text-align: center; font-size: 32px; letter-spacing: 5px;">
        {{ $verification_code }}
    </h1>
    
    <p><strong>This code will expire in {{ $valid_minutes }} minutes.</strong></p>
    
    <p>If you did not request this password change, please ignore this email.</p>
    
    <br>
    <p>Best regards,<br>
    Student Violation Tracking System</p>
</body>
</html>