<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Password Reset Successful</title>
</head>
<body>
    <h2>Password Reset Successful</h2>
    
    <p>Hello <strong>{{ $name }}</strong>,</p>
    
    <p>This is to confirm that your password was successfully reset on <strong>{{ $reset_date }}</strong>.</p>
    
    <p><strong>Security Information:</strong></p>
    <ul>
        <li>Reset Date: {{ $reset_date }}</li>
        <li>IP Address: {{ $ip_address }}</li>
    </ul>
    
    <p>If you did not make this change, please contact the system administrator immediately.</p>
    
    <br>
    <p>Best regards,<br>
    Student Violation Tracking System<br>
    Tagoloan Senior High School</p>
</body>
</html>