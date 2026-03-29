<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <style>
        /* Reset some default styles */
        body, p, h1, h2, h3, a {
            margin: 0;
            padding: 0;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            color: #333333;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #4CAF50;
            color: #ffffff;
            text-align: center;
            padding: 20px;
        }
        .header h1 {
            font-size: 24px;
        }
        .content {
            padding: 20px;
        }
        .content p {
            margin-bottom: 15px;
            line-height: 1.6;
        }
        .button {
            display: inline-block;
            background-color: #4CAF50;
            color: #ffffff;
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 5px;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #777777;
            padding: 15px 20px;
        }
        /* Responsive */
        @media screen and (max-width: 480px) {
            .container {
                width: 90%;
                margin: 15px auto;
            }
            .header h1 {
                font-size: 20px;
            }
            .button {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Password Reset Request</h1>
        </div>

        <!-- Content -->
        <div class="content">
            <p>Hello, {{ $user->name }}</p>
            <p>We received a request to reset your password. Click the button below to reset it:</p>
            <p style="text-align:center;">
                <a href="{{ $actionlink }}" target="_blank"  class="button">Reset Password</a>
            </p>
            <p> This link is valid for 15 minutes.</p>
            <p>If you did not request this, you can safely ignore this email. Your password will not change.</p>
            <p>Thank you,<br>The {{ config('app.name') }} Team</p>
        </div>

        <!-- Footer -->
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>
</html>