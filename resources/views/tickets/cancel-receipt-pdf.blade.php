<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CR{{$ticket->id}}-PDF</title>
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
    $explanation = GameServices::expl();

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
        <tr>
            <td class="center" style="height: 6px"> 
                {{str_repeat("=",35)}} <br>
                <span>* * *  T I C K E T&nbsp;&nbsp;&nbsp;C A N C E L L E D  * * *</span>
            </td>
        </tr>
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
                {{str_repeat("=",35)}}
            </td>
        </tr>
        <tr>
            <td class="center bold">
                <div>
                    -- TOTAL: {{$ticket->stake_amount}} --
                </div>
                <div style="margin-top: 6px;">
                    ______________________________________<br>
                    Customer Signature
                </div>
                <div style="font-size: 7px; margin-top: 6px;">
                    {{$explanation}}
                </div>
            </td>
        </tr>
    </table>
</body>

</html>
