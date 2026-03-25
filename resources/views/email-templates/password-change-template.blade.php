<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Account Details</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Reset some styles for email clients */
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            background-color: #f4f4f4;
            font-family: Arial, sans-serif;
        }
        .email-container {
            max-width: 500px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            padding: 20px;
        }
        .header {
            text-align: center;
            color: #333333;
        }
        .content {
            padding: 10px 0;
            color: #555555;
            line-height: 1.5;
        }
        .credentials {
            background-color: #f9f9f9;
            border-radius: 5px;
            padding: 15px;
            margin: 15px 0;
        }
        .login-button {
            display: inline-block;
            background-color: #007bff;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            font-size: 12px;
            color: #999999;
            text-align: center;
            margin-top: 15px;
        }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 95% !important;
                padding: 15px !important;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h2>Account Details</h2>
        </div>

        <div class="content">
            <p>Assalamu Alaikum,</p>
            <p>Hello {{ $user->name }},</p>
            <p>Your account has been updated successfully. Here are your login credentials:</p>
            <div class="credentials">
                <p><strong>Email/Username:</strong> {{ $user->email }} or {{ $user->username }}</p>
                <p><strong>Password:</strong> {{ $new_password }}</p>
            </div>
            <p style="color:red;">For your security, please log in and change your password immediately.</p>
            <p>If you did not request this change, please ignore this email.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Larablog. All rights reserved.
        </div>
    </div>
</body>
</html>