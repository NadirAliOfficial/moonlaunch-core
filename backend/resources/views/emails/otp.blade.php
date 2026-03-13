<!DOCTYPE html>
<html>
<head>
    <title>OTP Code</title>
    <style>
        /* General reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #FFE600; /* Fallback background */
            background: linear-gradient(135deg, #FFE600, #075BAB, #DB2519); /* Gradient Background */
            color: #ffffff;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #075BAB;
            font-size: 28px;
            margin-bottom: 20px;
        }

        p {
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 15px;
        }

        .otp {
            font-size: 28px;
            font-weight: bold;
            padding: 15px;
            background: #075BAB;
            color: #fff;
            text-align: center;
            margin: 20px 0;
            border-radius: 5px;
        }

        .instructions {
            background-color: #DB2519;
            color: #fff;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            font-size: 14px;
        }

        .footer {
            text-align: center;
            font-size: 14px;
            margin-top: 20px;
            color: #888;
        }

        .footer a {
            color: #075BAB;
            text-decoration: none;
        }

        .important {
            font-weight: bold;
            color: #FFD700;
        }

        /* Responsive styling */
        @media only screen and (max-width: 600px) {
            .container {
                padding: 20px;
            }

            h1 {
                font-size: 24px;
            }

            .otp {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>OTP Code for Registration</h1>
        <p>Hello,</p>
        <p>Thank you for signing up with us! To complete your registration, please use the OTP (One-Time Password) below.</p>
        
        <div class="otp">{{ $otp }}</div>

        <p>This OTP code is valid for the next 10 minutes. After that, it will expire, and you will need to request a new one.</p>

        <div class="instructions">
            <p><strong>How to Use Your OTP:</strong></p>
            <ul>
                <li>Open the registration page where you started the sign-up process.</li>
                <li>Enter the OTP code in the provided field.</li>
                <li>Click on "Verify" to complete your registration.</li>
            </ul>
        </div>

        <p class="important">If you did not request this OTP, please ignore this email or contact support immediately.</p>

        <p>If you have any issues, please feel free to reach out to our <a href="mailto:support@yourcompany.com">support team</a>.</p>

        <div class="footer">
            <p>Thank you for choosing us! <br> Moonlaunch</p>
        </div>  
    </div>
</body>
</html>
    