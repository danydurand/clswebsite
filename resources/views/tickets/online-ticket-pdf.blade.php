<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #{{$ticket->id}}</title>
    <style>
        @page {
            margin: 0;
            size: A4;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.6;
            color: #2d3748;
            background: #f7fafc;
        }

        .ticket-wrapper {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Header Section */
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .header::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -5%;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .header-content {
            position: relative;
            z-index: 1;
        }

        .company-name {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }

        .company-details {
            font-size: 11px;
            opacity: 0.95;
            margin-bottom: 5px;
        }

        .ticket-title {
            font-size: 16px;
            font-weight: 600;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.3);
            letter-spacing: 2px;
        }

        /* Status Banner */
        .status-banner {
            background: linear-gradient(135deg, #fc8181 0%, #f56565 100%);
            color: white;
            padding: 15px;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            letter-spacing: 3px;
            border-left: 5px solid #c53030;
            border-right: 5px solid #c53030;
        }

        /* Ticket Info Section */
        .ticket-info {
            padding: 30px 40px;
            background: linear-gradient(to bottom, #edf2f7 0%, #ffffff 100%);
            border-bottom: 3px solid #667eea;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }

        .info-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .info-label {
            font-size: 9px;
            text-transform: uppercase;
            color: #718096;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 13px;
            font-weight: bold;
            color: #2d3748;
        }

        /* Barcode Section */
        .barcode-section {
            text-align: center;
            padding: 25px 40px;
            background: white;
        }

        .barcode-container {
            background: linear-gradient(to bottom, #f7fafc, #ffffff);
            border: 2px dashed #667eea;
            border-radius: 12px;
            padding: 20px;
            display: inline-block;
        }

        .barcode-container img {
            display: block;
            margin: 0 auto;
        }

        .barcode-number {
            font-size: 10px;
            color: #718096;
            margin-top: 10px;
            font-weight: 600;
            letter-spacing: 1px;
        }

        /* Bets Section */
        .bets-section {
            padding: 30px 40px;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .raffle-group {
            margin-bottom: 30px;
            background: #f7fafc;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .raffle-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .raffle-name {
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .raffle-total {
            font-size: 16px;
            font-weight: bold;
            background: rgba(255, 255, 255, 0.2);
            padding: 5px 15px;
            border-radius: 20px;
        }

        .bets-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        .bets-table thead {
            background: linear-gradient(to right, #edf2f7, #e2e8f0);
        }

        .bets-table th {
            padding: 12px 15px;
            text-align: left;
            font-size: 10px;
            font-weight: 700;
            color: #4a5568;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #cbd5e0;
        }

        .bets-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 11px;
        }

        .bets-table tbody tr:hover {
            background: #f7fafc;
        }

        .bets-table tbody tr:last-child td {
            border-bottom: none;
        }

        .game-name {
            font-weight: 600;
            color: #2d3748;
        }

        .bet-sequence {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            color: #667eea;
            font-size: 12px;
        }

        .bet-amount {
            font-weight: bold;
            color: #48bb78;
            text-align: right;
        }

        /* Total Section */
        .total-section {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            color: white;
            padding: 25px 40px;
            margin-top: 30px;
        }

        .total-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .total-label {
            font-size: 18px;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .total-amount {
            font-size: 32px;
            font-weight: bold;
        }

        .explanation {
            font-size: 10px;
            opacity: 0.95;
            line-height: 1.5;
            padding: 15px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 8px;
            margin-top: 15px;
        }

        /* Footer */
        .footer {
            background: #2d3748;
            color: #cbd5e0;
            padding: 20px 40px;
            text-align: center;
        }

        .footer-message {
            font-size: 12px;
            font-weight: 600;
            color: white;
            margin-bottom: 10px;
        }

        .footer-note {
            font-size: 9px;
            opacity: 0.8;
        }

        /* Print Styles */
        @media print {
            body {
                background: white;
            }
            
            .ticket-wrapper {
                box-shadow: none;
            }
        }
    </style>
</head>

@php
    use App\Domain\Game\GameServices;

    $explanation = GameServices::expl();
    
    // For online tickets, use company settings
    $companyName = sett('platform-name', 'string', 'DreamBet');
    $companyAddr = sett('platform-address', 'string', 'Haiti');
    $companyPhon = sett('platform-email', 'string', 'support@dreambet.com');
@endphp

<body>
    <div class="ticket-wrapper">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <div class="company-name">{{$companyName}}</div>
                <div class="company-details">{{$companyAddr}}</div>
                <div class="company-details">{{$companyPhon}}</div>
                <div class="ticket-title">LOTTERY TICKET</div>
            </div>
        </div>

        <!-- Cancelled Banner -->
        @if (in_array($ticket->status->value, ['cancelled','auto-cancelled']))
        <div class="status-banner">
            ⚠ TICKET CANCELLED ⚠
        </div>
        @endif

        <!-- Ticket Info -->
        <div class="ticket-info">
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Customer</div>
                    <div class="info-value">{{$ticket->customer->name}}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Ticket ID</div>
                    <div class="info-value">#{{$ticket->id}}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Date & Time</div>
                    <div class="info-value">{{$ticket->created_at->format('M d, Y - h:i A')}}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Ticket Code</div>
                    <div class="info-value">{{$ticket->code}}</div>
                </div>
            </div>
        </div>

        <!-- Barcode -->
        <div class="barcode-section">
            <div class="barcode-container">
                <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($ticket->number, 'C39+', 2, 60, array(0,0,0), true)}}" alt="barcode" />
                <div class="barcode-number">{{$ticket->number}}</div>
            </div>
        </div>

        <!-- Bets Section -->
        <div class="bets-section">
            <div class="section-title">Your Bets</div>

            @php
                $ticketDetails = $ticket->ticketDetails()->orderBy('raffle_id')->get();
                $raffleId = null;
            @endphp

            @foreach ($ticketDetails as $detail)
                @if ($detail->raffle_id != $raffleId)
                    @php
                        $raffleId = $detail->raffle_id;
                        $totalByRaffle = $ticketDetails
                                            ->where('raffle_id', $raffleId)
                                            ->sum('stake_amount');
                    @endphp
                    
                    @if (!$loop->first)
                        </tbody></table></div>
                    @endif
                    
                    <div class="raffle-group">
                        <div class="raffle-header">
                            <div class="raffle-name">{{$detail->raffle->raffle_code}}</div>
                            <div class="raffle-total">${{number_format($totalByRaffle, 2)}}</div>
                        </div>
                        <table class="bets-table">
                            <thead>
                                <tr>
                                    <th style="width: 30%">Game</th>
                                    <th style="width: 50%">Bet Sequence</th>
                                    <th style="width: 20%; text-align: right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                @endif
                
                <tr>
                    <td class="game-name">{{$detail->game->short_name}}</td>
                    <td class="bet-sequence">{{$detail->sequence}}</td>
                    <td class="bet-amount">${{number_format($detail->stake_amount, 2)}}</td>
                </tr>
                
                @if ($loop->last)
                    </tbody></table></div>
                @endif
            @endforeach
        </div>

        <!-- Total Section -->
        <div class="total-section">
            <div class="total-container">
                <div class="total-label">TOTAL AMOUNT</div>
                <div class="total-amount">${{number_format($ticket->stake_amount, 2)}}</div>
            </div>
            
            @if($explanation)
            <div class="explanation">
                {{$explanation}}
            </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-message">Thank you for playing with {{$companyName}}!</div>
            <div class="footer-note">Keep this ticket safe. You must present it to claim any prizes.</div>
        </div>
    </div>
</body>

</html>