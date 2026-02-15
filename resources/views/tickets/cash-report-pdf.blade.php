<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CashReport-PDF</title>
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
            font-size: 7px;
        }

        .big {
            font-size: 12px;
        }


    </style>
</head>

@php
    $record = $records->first();
    if ($record->bank_id) {
        //--------------------------------------
        // It was a physically sold
        //--------------------------------------
        $companyName = $record->bank->name;
        $companyAddr = $record->bank->address;
        $companyPhon = $record->bank->phone;
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
    </table>
    <table style="width: 100%;" border="0">
        <tr>
            <td style="text-align: center" class="small">
                {{ $title }} <br>
                {{ $subTitle }}
            </td>
        </tr>
    </table>
    <table style="width: 100%;" border="0">
        <tr>
            <th style="width: 20%" class="small bold center">DATE</th>
            <th style="width: 10%" class="small bold center">QTY</th>
            <th style="width: 20%" class="small bold center">SOLD</th>
            <th style="width: 10%" class="small bold center">COMMISSION</th>
            <th style="width: 20%" class="small bold right">PRIZE</th>
            <th style="width: 20%" class="small bold right">PROFIT</th>
        </tr>
        @foreach ($records as $record)
        <tr>
            <td style="width: 20%" class="small center">{{$record->created_at->format('Y-m-d')}}</td>
            <td style="width: 10%" class="small center">{{$record->qty}}</td>
            <td style="width: 20%" class="small center">{{$record->sold}}</td>
            <td style="width: 10%" class="small center">{{$record->commission}}</td>
            <td style="width: 20%" class="small right">{{$record->prize}}</td>
            <td style="width: 20%" class="small right">{{$record->profit}}</td>
        </tr>
        {{-- @if ($loop->index > 10)
            <div class="page-break"></div>
        @endif --}}
        </tr>
        @endforeach
    </table>
</body>

</html>
