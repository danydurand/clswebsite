<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tk{{$ticket->id}}-PDF</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
            font-size: 10px;
            background: linear-gradient(135deg, #2563eb 0%, #9333ea 100%);
            padding: 20px;
        }

        .ticket-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            max-width: 600px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 15px;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #2563eb 0%, #9333ea 100%);
            color: white;
            padding: 15px;
            border-radius: 8px;
        }

        .company-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .company-info {
            font-size: 9px;
            opacity: 0.9;
        }

        .cancelled-banner {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            text-align: center;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-weight: bold;
            font-size: 12px;
            letter-spacing: 2px;
        }

        .ticket-info {
            text-align: center;
            margin: 15px 0;
            padding: 10px;
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            border-radius: 8px;
        }

        .ticket-code {
            font-size: 9px;
            color: #4b5563;
            margin: 5px 0;
        }

        .barcode-container {
            text-align: center;
            margin: 15px 0;
            padding: 10px;
            background: white;
            border: 2px dashed #2563eb;
            border-radius: 8px;
        }

        .separator {
            text-align: center;
            color: #2563eb;
            font-weight: bold;
            margin: 10px 0;
            font-size: 11px;
        }

        .raffle-header {
            background: linear-gradient(135deg, #2563eb 0%, #9333ea 100%);
            color: white;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
            font-weight: bold;
            margin: 10px 0;
            font-size: 11px;
        }

        .bets-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        .bets-table th {
            background: linear-gradient(135deg, #3b82f6 0%, #a855f7 100%);
            color: white;
            padding: 8px;
            font-size: 9px;
            font-weight: bold;
            text-align: center;
        }

        .bets-table td {
            padding: 6px;
            font-size: 9px;
            border-bottom: 1px solid #e5e7eb;
            text-align: center;
        }

        .bets-table tr:hover {
            background: #f9fafb;
        }

        .total-section {
            text-align: center;
            margin-top: 15px;
            padding: 15px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border-radius: 8px;
        }

        .total-amount {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .explanation {
            font-size: 8px;
            margin: 10px 0;
            opacity: 0.9;
        }

        .footer-text {
            font-size: 10px;
            font-weight: bold;
            margin-top: 10px;
        }

        .bold {
            font-weight: bold;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .page-break {
            page-break-after: always;
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
    <div class="ticket-container">
        <!-- Header -->
        <div class="header">
            <div class="company-name">{{$companyName}}</div>
            <div class="company-info">{{$companyAddr}}</div>
            <div class="company-info">{{$companyPhon}}</div>
        </div>

        <!-- Cancelled Banner -->
        @if (in_array($ticket->status->value, ['cancelled', 'auto-cancelled']))
            <div class="cancelled-banner">
                ★ ★ ★ C A N C E L L E D ★ ★ ★
            </div>
        @endif

        <!-- Ticket Info -->
        <div class="ticket-info">
            <div class="ticket-code">{{$ticket->customer->name}}</div>
            <div class="ticket-code">{{$ticket->created_at}}</div>
            <div class="ticket-code">{{$ticket->code}}</div>
        </div>

        <!-- Barcode -->
        <div class="barcode-container">
            <img src="data:image/png;base64,'{{DNS1D::getBarcodePNG($ticket->number, 'C39+', .5, 35, array(1, 1, 1), true)}}'"
                alt="barcode" />
        </div>

        <!-- Ticket Details -->
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

                <div class="separator">═══════════════════════════════════</div>
                <div class="raffle-header">
                    {{$detail->raffle->raffle_code}}: ${{$totalByRaffle}}
                </div>

                <table class="bets-table">
                    <thead>
                        <tr>
                            <th>GAME</th>
                            <th>PLAY</th>
                            <th>AMOUNT</th>
                        </tr>
                    </thead>
                    <tbody>
            @endif

                    <tr>
                        <td>{{$detail->game->short_name}}</td>
                        <td class="bold">{{$detail->sequence}}</td>
                        <td class="right">${{$detail->stake_amount}}</td>
                    </tr>

                    @if ($loop->last || $ticketDetails[$loop->index + 1]->raffle_id != $raffleId)
                            </tbody>
                        </table>
                    @endif

            @if ($loop->index > 8)
                <div class="page-break"></div>
            @endif
        @endforeach

        <!-- Total Section -->
        <div class="total-section">
            <div class="total-amount">TOTAL: ${{$ticket->stake_amount}}</div>
            <div class="explanation">{{$explanation}}</div>
        </div>
    </div>
</body>

</html>