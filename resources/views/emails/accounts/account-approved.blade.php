<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Approved - Amore Academy</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            background-color: #f8f9fa;
        }
        .container {
            background-color: #ffffff;
            margin: 20px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .content {
            padding: 30px 20px;
        }
        .congratulations {
            text-align: center;
            margin-bottom: 30px;
        }
        .congratulations h2 {
            color: #28a745;
            font-size: 24px;
            margin: 0 0 10px 0;
        }
        .account-details {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #28a745;
        }
        .account-details h3 {
            margin-top: 0;
            color: #28a745;
            font-size: 18px;
        }
        .detail-item {
            margin: 8px 0;
            padding: 5px 0;
        }
        .detail-label {
            font-weight: 600;
            color: #495057;
        }
        .next-steps {
            background-color: #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .next-steps h3 {
            margin-top: 0;
            color: #495057;
        }
        .step {
            margin: 10px 0;
            padding-left: 20px;
            position: relative;
        }
        .step:before {
            content: "✓";
            color: #28a745;
            font-weight: bold;
            position: absolute;
            left: 0;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
            text-align: center;
        }
        .footer {
            background-color: #343a40;
            color: #adb5bd;
            padding: 20px;
            text-align: center;
            font-size: 14px;
        }
        .footer h4 {
            margin: 0 0 10px 0;
            color: #ffffff;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
        @media (max-width: 600px) {
            .container {
                margin: 10px;
            }
            .header {
                padding: 20px 15px;
            }
            .content {
                padding: 20px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Account Approved!</h1>
            <p>Welcome to Amore Academy</p>
        </div>

        <div class="content">
            <div class="congratulations">
                <h2>Hello {{ $user->first_name }} {{ $user->last_name }}!</h2>
                <p>Your account has been <strong>approved</strong> and is now <strong>active</strong>. You can now access all features of the Amore Academy system.</p>
            </div>

            <div class="account-details">
                <h3>📋 Your Account Details</h3>
                <div class="detail-item">
                    <span class="detail-label">Email:</span> {{ $user->email }}
                </div>
                <div class="detail-item">
                    <span class="detail-label">Account Type:</span> {{ ucfirst($user->account_type) }}
                </div>
                @if($user->account_type === 'student')
                <div class="detail-item">
                    <span class="detail-label">Grade Level:</span> {{ $user->grade_level ?? 'Not specified' }}
                </div>
                <div class="detail-item">
                    <span class="detail-label">LRN:</span> {{ $user->lrn ?? 'Not specified' }}
                </div>
                @else
                <div class="detail-item">
                    <span class="detail-label">Department:</span> {{ $user->department ?? 'Not specified' }}
                </div>
                @endif
            </div>

            <div class="next-steps">
                <h3>🚀 Next Steps</h3>
                <div class="step">Login to your account using your registered email and password</div>
                <div class="step">Complete your profile if you haven't already</div>
                <div class="step">Explore the available features based on your account type</div>
            </div>

            <div style="text-align: center;">
                <a href="{{ url('/login') }}" class="cta-button">Login to Your Account</a>
            </div>

            <div class="warning">
                <strong>Need Help?</strong><br>
                If you have any questions or need assistance, please contact the administration.
            </div>
        </div>

        <div class="footer">
            <h4>Amore Academy</h4>
            <p>Excellence in Education</p>
            <p style="font-size: 12px; margin-top: 10px;">
                This is an automated message. Please do not reply to this email.
            </p>
        </div>
    </div>
</body>
</html>


SUCCESS