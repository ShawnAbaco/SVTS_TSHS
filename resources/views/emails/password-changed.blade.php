<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Password Changed Successfully</title>
</head>
<body>
    <h2>Password Changed Successfully</h2>
    
    <p>Hello <strong>{{ $name }}</strong>,</p>
    
    <p>This is to confirm that your password was successfully changed on <strong>{{ $change_date }}</strong>.</p>
    
    <p><strong>Security Information:</strong></p>
    <ul>
        <li>Change Date: {{ $change_date }}</li>
        <li>IP Address: {{ $ip_address }}</li>
    </ul>
    
    <p>If you did not make this change, please contact the system administrator immediately.</p>
    
    <br>
    <p>Best regards,<br>
    Student Violation Tracking System</p>
</body>
</html>