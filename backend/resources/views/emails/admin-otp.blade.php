<!DOCTYPE html>
<html>
<head>
    <title>Admin OTP Verification</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #fbbf24, #f97316);
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            padding: 0;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(to right, #fbbf24, #f97316);
            padding: 30px;
            text-align: center;
        }

        .header img {
            height: 50px;
            width: auto;
        }

        .content {
            padding: 40px 30px;
        }

        h1 {
            text-align: center;
            color: #1f2937;
            font-size: 24px;
            margin-bottom: 20px;
            font-weight: 700;
        }

        p {
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 15px;
            color: #4b5563;
        }

        .otp-box {
            background: linear-gradient(to right, #fbbf24, #f97316);
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            margin: 30px 0;
        }

        .otp {
            font-size: 36px;
            font-weight: bold;
            color: #ffffff;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
        }

        .validity {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }

        .validity p {
            margin: 0;
            color: #92400e;
            font-size: 14px;
        }

        .instructions {
            background-color: #fee2e2;
            border-left: 4px solid #ef4444;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }

        .instructions p {
            margin: 0 0 10px 0;
            color: #991b1b;
            font-size: 14px;
            font-weight: 600;
        }

        .instructions ul {
            margin: 10px 0 0 20px;
            color: #991b1b;
        }

        .instructions li {
            margin-bottom: 8px;
            font-size: 13px;
        }

        .security-notice {
            background-color: #f3f4f6;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            border: 1px solid #d1d5db;
        }

        .security-notice p {
            margin: 0;
            font-size: 13px;
            color: #374151;
        }

        .footer {
            text-align: center;
            padding: 20px;
            background-color: #f9fafb;
            border-top: 1px solid #e5e7eb;
        }

        .footer p {
            font-size: 13px;
            color: #6b7280;
            margin: 5px 0;
        }

        .footer a {
            color: #f97316;
            text-decoration: none;
            font-weight: 600;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        @media only screen and (max-width: 600px) {
            .container {
                margin: 10px;
            }

            .content {
                padding: 30px 20px;
            }

            h1 {
                font-size: 20px;
            }

            .otp {
                font-size: 28px;
                letter-spacing: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header with Logo -->
        <div class="header">
            <img src="{{ $logoUrl ?? 'https://your-domain.com/assets/logo.png' }}" alt="MoonLaunch Logo">
        </div>

        <!-- Content -->
        <div class="content">
            <h1>🔐 Admin Login Verification</h1>
            
            <p>Hello Admin,</p>
            <p>Someone (hopefully you!) is attempting to log in to the <strong>MoonLaunch Admin Panel</strong>. To complete the login process, please use the One-Time Password (OTP) below:</p>
            
            <!-- OTP Display -->
            <div class="otp-box">
                <div class="otp">{{ $otp }}</div>
            </div>

            <!-- Validity Notice -->
            <div class="validity">
                <p><strong>⏰ This OTP is valid for 10 minutes only.</strong> After that, you will need to request a new one.</p>
            </div>

            <!-- Instructions -->
            <div class="instructions">
                <p><strong>How to Use Your OTP:</strong></p>
                <ul>
                    <li>Return to the admin login page</li>
                    <li>Enter the 6-digit OTP code in the verification field</li>
                    <li>Click "Verify OTP" to access your dashboard</li>
                </ul>
            </div>

            <!-- Security Notice -->
            <div class="security-notice">
                <p><strong>⚠️ Security Notice:</strong> If you did not attempt to log in to the admin panel, please ignore this email and contact your system administrator immediately. Your account security may be compromised.</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>MoonLaunch Admin Team</strong></p>
            <p>This is an automated security email. Please do not reply.</p>
            <p>Need help? Contact <a href="mailto:support@moonlaunch.com">support@moonlaunch.com</a></p>
        </div>
    </div>
</body>
</html>