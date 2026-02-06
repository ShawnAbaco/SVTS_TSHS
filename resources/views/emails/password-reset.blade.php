<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Password Reset Verification</title>
</head>
<body>
    <h2>Password Reset Verification Code</h2>
    
    <p>Hello <strong>{{ $name }}</strong>,</p>
    
    <p>You have requested to reset your password for the Student Violation Tracking System.</p>
    
    <p>Your verification code is:</p>
    
    <h1 style="background: #3498db; color: white; padding: 20px; text-align: center; font-size: 32px; letter-spacing: 5px; border-radius: 8px;">
        {{ $verification_code }}
    </h1>
    
    <p><strong>This code will expire in {{ $valid_minutes }} minutes.</strong></p>
    
    <p>Enter this code in the password reset form along with your new password.</p>
    
    <p>If you did not request this password reset, please ignore this email and contact the system administrator immediately.</p>
    
    <br>
    <p>Best regards,<br>
    Student Violation Tracking System<br>
    Tagoloan Senior High School</p>
</body>
</html>