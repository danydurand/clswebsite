<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tk{{$ticket->id}}-PDF</title>
    <style>
        body {
            font-size: 10px;
        }

        .bold {
            font-weight: bold;
        }

        .bordered {
            border: solid 1px black;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .small {
            font-size: 8px;
        }

        .big {
            font-size: 12px;
        }


    </style>
</head>

@php
    use App\Domain\Game\GameServices;

    $paymentProfileId = $ticket->seller->payment_id;
    $explanation = GameServices::expl();
    if ($paymentProfileId) {
        $paymentProfile = \App\Models\Payment::find($paymentProfileId);
        $explanation = $paymentProfile->explanation;
    }

    if ($ticket->bank_id) {
        //--------------------------------------
        // It was a physically sold
        //--------------------------------------
        $companyName = $ticket->bank->name;
        $companyAddr = $ticket->bank->address;
        $companyPhon = $ticket->bank->phone;
    } else {
        //--------------------------------------
        // It was sold online
        //--------------------------------------
        $companyName = sett('company-name', 'string', 'Company');
        $companyAddr = sett('company-address', 'string', 'Address');
        $companyPhon = sett('company-phone', 'string', 'Phone');
    }
@endphp
<body>
    <table style="width: 100%; margin-top: -36px" border="0">
        <tr>
            <td style="text-align: center">
                <div style="font-size: 12px;">
                    {{$companyName}}
                </div>
                <div style="font-size: 8px;">
                    {{$companyAddr}}
                </div>
                <div style="font-size: 8px;">
                    {{$companyPhon}}
                </div>
            </td>
        </tr>
        @if (in_array($ticket->status->value, [
            'cancelled','auto-cancelled'
        ]))
        <tr>
            <td class="center" style="height: 6px">
                {{str_repeat("=",35)}} <br>
                <span>* * *  C A N C E L L E D  * * *</span>
            </td>
        </tr>
        @endif
        <tr>
            <td class="center">
                {{str_repeat("=",35)}} <br>
                <div>{{$ticket->created_at}} </div>
                <div>{{$ticket->code}} </div>
            </td>
        </tr>
        <tr>
            <td class="center" style="width: 100%; vertical-align: middle">
                <img src="data:image/png;base64,'{{DNS1D::getBarcodePNG($ticket->number, 'C39+',.5,35,array(1,1,1), true)}}'" alt="barcode" />
            </td>
        </tr>
        <tr>
            @php
                $ticketDetails = $ticket->ticketDetails()->orderBy('raffle_id')->get();
                $raffleId = null;
            @endphp
            <table style="width: 100%" border="0">
            @foreach ($ticketDetails as $detail)
                @if ($detail->raffle_id != $raffleId)
                @php
                    $raffleId = $detail->raffle_id;
                    $totalByRaffle = $ticketDetails
                                        ->where('raffle_id', $raffleId)
                                        ->sum('stake_amount');
                @endphp
                <tr>
                    <td style="width: 100%; text-align: center;" colspan="3">
                        {{str_repeat("=",35)}} <br>
                        <b>{{$detail->raffle->raffle_code}}: {{$totalByRaffle}}</b>
                        {{str_repeat("=",35)}} <br>
                    </td>
                </tr>
                <tr>
                    <td style="width: 33%" class="bold center">GAME</td>
                    <td style="width: 33%" class="bold center">PLAY</td>
                    <td style="width: 33%" class="bold right">AMNT</td>
                </tr>
                @endif
                <tr>
                    <td style="width: 33%; text-align: center" class="small">{{$detail->game->short_name}}</td>
                    <td style="width: 33%" class="small center">{{$detail->sequence}}</td>
                    <td style="width: 33%; text-align: right" class="small">{{$detail->stake_amount}}</td>
                </tr>
                @if ($loop->index > 8)
                    <div class="page-break"></div>
                @endif
            @endforeach
            </table>
        </tr>
        <tr>
            <td class="center bold">
                <div>
                    -- TOTAL: {{$ticket->stake_amount}} --
                </div>
                <div style="font-size: 7px;">
                    {{$explanation}}
                </div>
                <div>
                    NO TICKET, NO MONEY
                </div>
            </td>
        </tr>
    </table>
</body>

</html>
