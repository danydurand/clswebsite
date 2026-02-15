<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Lottery Ticket</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f7fafc;
        }

        .email-container {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }

        .header p {
            margin: 10px 0 0;
            font-size: 14px;
            opacity: 0.9;
        }

        .content {
            padding: 30px;
        }

        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 20px;
        }

        .message {
            color: #4a5568;
            margin-bottom: 25px;
            line-height: 1.8;
        }

        .ticket-info {
            background: linear-gradient(to bottom, #edf2f7, #ffffff);
            border-left: 4px solid #667eea;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
        }

        .ticket-info h3 {
            margin: 0 0 15px;
            color: #2d3748;
            font-size: 16px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #718096;
            font-size: 14px;
        }

        .info-value {
            color: #2d3748;
            font-weight: 500;
            font-size: 14px;
        }

        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 14px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 20px 0;
            text-align: center;
        }

        .cta-button:hover {
            opacity: 0.9;
        }

        .footer {
            background: #2d3748;
            color: #cbd5e0;
            padding: 25px 30px;
            text-align: center;
            font-size: 13px;
        }

        .footer-message {
            margin-bottom: 15px;
            color: white;
            font-weight: 600;
        }

        .footer-note {
            font-size: 12px;
            opacity: 0.8;
            margin-top: 10px;
        }

        .attachment-note {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            color: #92400e;
        }

        .attachment-note strong {
            display: block;
            margin-bottom: 5px;
            color: #78350f;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>🎫 {{ sett('platform-name', 'string', 'DreamBet') }}</h1>
            <p>Your Lottery Ticket Confirmation</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">Hello {{ $ticket->customer->name }}!</div>

            <div class="message">
                Thank you for playing with us! Your lottery ticket has been successfully created and is attached to this
                email as a PDF document.
            </div>

            <!-- Ticket Information -->
            <div class="ticket-info">
                <h3>📋 Ticket Details</h3>
                <div class="info-row">
                    <span class="info-label">Ticket ID:</span>
                    <span class="info-value">#{{ $ticket->id }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Ticket Code:</span>
                    <span class="info-value">{{ $ticket->code }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Date:</span>
                    <span class="info-value">{{ $ticket->created_at->format('M d, Y - h:i A') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Total Amount:</span>
                    <span class="info-value">${{ number_format($ticket->stake_amount, 2) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Number of Bets:</span>
                    <span class="info-value">{{ $ticket->ticketDetails->count() }}</span>
                </div>
            </div>

            <!-- Attachment Note -->
            <div class="attachment-note">
                <strong>📎 PDF Attachment</strong>
                Your complete ticket with all bet details is attached to this email as a PDF file. Please download and
                keep it safe for your records.
            </div>

            <!-- Call to Action -->
            <div style="text-align: center;">
                <a href="{{ route('tickets.print', $ticket->id) }}" class="cta-button">
                    View Ticket Online
                </a>
            </div>

            <div class="message" style="margin-top: 25px;">
                <strong>Important:</strong> Please keep this ticket safe. You must present it to claim any prizes. Good
                luck! 🍀
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-message">Thank you for playing with {{ sett('platform-name', 'string', 'DreamBet') }}!
            </div>
            <div class="footer-note">
                {{ sett('platform-address', 'string', 'Haiti') }}<br>
                {{ sett('platform-email', 'string', 'support@dreambet.com') }}
            </div>
            <div class="footer-note" style="margin-top: 15px;">
                This is an automated message. Please do not reply to this email.
            </div>
        </div>
    </div>
</body>

</html>