<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: linear-gradient(to right, #2563eb, #9333ea);
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
        }

        .content {
            background: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-top: none;
        }

        .field {
            margin-bottom: 20px;
        }

        .label {
            font-weight: bold;
            color: #4b5563;
            display: block;
            margin-bottom: 5px;
        }

        .value {
            color: #1f2937;
        }

        .message-box {
            background: white;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
            white-space: pre-wrap;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1 style="margin: 0; font-size: 24px;">New Contact Form Submission</h1>
    </div>

    <div class="content">
        <div class="field">
            <span class="label">From:</span>
            <span class="value">{{ $contactName }}</span>
        </div>

        <div class="field">
            <span class="label">Email:</span>
            <span class="value">{{ $contactEmail }}</span>
        </div>

        <div class="field">
            <span class="label">Subject:</span>
            <span class="value">{{ $contactSubject }}</span>
        </div>

        <div class="field">
            <span class="label">Message:</span>
            <div class="message-box">{{ $contactMessage }}</div>
        </div>

        <div class="footer">
            <p>This message was sent from the DreamBet contact form.</p>
            <p>Reply directly to this email to respond to {{ $contactName }}.</p>
        </div>
    </div>
</body>

</html>