<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Verify Your Email Address') }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f3f4f6;
        }

        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: linear-gradient(135deg, #2563eb 0%, #9333ea 100%);
            padding: 40px 20px;
            text-align: center;
        }

        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }

        .content {
            padding: 40px 30px;
        }

        .greeting {
            font-size: 18px;
            color: #1f2937;
            margin-bottom: 20px;
        }

        .message {
            font-size: 16px;
            color: #4b5563;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .button-container {
            text-align: center;
            margin: 40px 0;
        }

        .verify-button {
            display: inline-block;
            padding: 16px 40px;
            background: linear-gradient(135deg, #2563eb 0%, #9333ea 100%);
            color: #ffffff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 4px 6px rgba(37, 99, 235, 0.3);
            transition: transform 0.2s;
        }

        .verify-button:hover {
            transform: translateY(-2px);
        }

        .alternative-link {
            margin-top: 30px;
            padding: 20px;
            background-color: #f9fafb;
            border-radius: 8px;
        }

        .alternative-link p {
            font-size: 14px;
            color: #6b7280;
            margin: 0 0 10px 0;
        }

        .alternative-link a {
            color: #2563eb;
            word-break: break-all;
            font-size: 13px;
        }

        .footer {
            padding: 30px;
            text-align: center;
            background-color: #f9fafb;
            border-top: 1px solid #e5e7eb;
        }

        .footer p {
            margin: 5px 0;
            font-size: 14px;
            color: #6b7280;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="header">
            <h1>{{ $companyName }}</h1>
        </div>

        <div class="content">
            <p class="greeting">{{ __('Hello') }} {{ $user->name }},</p>

            <p class="message">
                {{ __('Thank you for registering with :company! To complete your registration and start enjoying our services, please verify your email address by clicking the button below.', ['company' => $companyName]) }}
            </p>

            <div class="button-container">
                <a href="{{ $verificationUrl }}" class="verify-button">
                    {{ __('Verify Email Address') }}
                </a>
            </div>

            <p class="message">
                {{ __('This verification link will expire in 60 minutes.') }}
            </p>

            <div class="alternative-link">
                <p>{{ __('If you\'re having trouble clicking the button, copy and paste the URL below into your web browser:') }}
                </p>
                <a href="{{ $verificationUrl }}">{{ $verificationUrl }}</a>
            </div>

            <p class="message" style="margin-top: 30px; font-size: 14px; color: #6b7280;">
                {{ __('If you did not create an account, no further action is required.') }}
            </p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ $companyName }}. {{ __('All rights reserved.') }}</p>
        </div>
    </div>
</body>

</html>