<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Welcome to :company!', ['company' => $companyName]) }}</title>
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
            padding: 50px 20px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0 0 10px 0;
            font-size: 32px;
            font-weight: 700;
        }
        .header p {
            color: #e0e7ff;
            margin: 0;
            font-size: 18px;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 20px;
            color: #1f2937;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .message {
            font-size: 16px;
            color: #4b5563;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .features {
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 30px;
            margin: 30px 0;
        }
        .feature-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
        }
        .feature-item:last-child {
            margin-bottom: 0;
        }
        .feature-icon {
            width: 24px;
            height: 24px;
            background: linear-gradient(135deg, #2563eb 0%, #9333ea 100%);
            border-radius: 50%;
            margin-right: 15px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        .feature-text {
            flex: 1;
        }
        .feature-title {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 5px;
        }
        .feature-description {
            font-size: 14px;
            color: #6b7280;
        }
        .button-container {
            text-align: center;
            margin: 40px 0;
        }
        .action-button {
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
        .action-button:hover {
            transform: translateY(-2px);
        }
        .support-box {
            background-color: #eff6ff;
            border-left: 4px solid #2563eb;
            padding: 20px;
            margin-top: 30px;
            border-radius: 4px;
        }
        .support-box p {
            margin: 0;
            font-size: 14px;
            color: #1e40af;
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
            <h1>{{ __('Welcome!') }}</h1>
            <p>{{ __('Your account is now active') }}</p>
        </div>
        
        <div class="content">
            <p class="greeting">{{ __('Hello') }} {{ $user->name }}! 🎉</p>
            
            <p class="message">
                {{ __('Welcome to :company! We\'re thrilled to have you join our community. Your email has been successfully verified and your account is now fully active.', ['company' => $companyName]) }}
            </p>
            
            <div class="features">
                <div class="feature-item">
                    <div class="feature-icon">1</div>
                    <div class="feature-text">
                        <div class="feature-title">{{ __('Browse Raffles') }}</div>
                        <div class="feature-description">{{ __('Explore our exciting raffles and find the perfect opportunity to win big.') }}</div>
                    </div>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">2</div>
                    <div class="feature-text">
                        <div class="feature-title">{{ __('Make a Deposit') }}</div>
                        <div class="feature-description">{{ __('Add funds to your account securely and start participating in raffles.') }}</div>
                    </div>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">3</div>
                    <div class="feature-text">
                        <div class="feature-title">{{ __('Purchase Tickets') }}</div>
                        <div class="feature-description">{{ __('Buy tickets for your favorite raffles and increase your chances of winning.') }}</div>
                    </div>
                </div>
            </div>
            
            <div class="button-container">
                <a href="{{ config('app.url') }}/dashboard" class="action-button">
                    {{ __('Go to Dashboard') }}
                </a>
            </div>
            
            <div class="support-box">
                <p>
                    <strong>{{ __('Need help?') }}</strong><br>
                    {{ __('Our support team is here to assist you. Contact us at :email', ['email' => config('mail.from.address')]) }}
                </p>
            </div>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ $companyName }}. {{ __('All rights reserved.') }}</p>
        </div>
    </div>
</body>
</html>
