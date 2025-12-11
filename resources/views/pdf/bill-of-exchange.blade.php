<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Bill of Exchange - {{ $boe->boe_number }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
        }

        .page {
            width: 100%;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .title {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .subtitle {
            font-size: 12px;
            margin-top: 2px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th {
            padding: 4px 6px;
            vertical-align: top;
        }

        .bordered {
            border: 1px solid #000;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .mt-1 {
            margin-top: 4px;
        }

        .mt-2 {
            margin-top: 8px;
        }

        .mt-3 {
            margin-top: 12px;
        }

        .small {
            font-size: 10px;
        }
    </style>
</head>

<body>
    <div class="page">
        <div class="header">
            <div class="title">
                BILL OF EXCHANGE – {{ $boe->boe_type === 'FIRST' ? 'FIRST' : 'SECOND' }} OF EXCHANGE
            </div>
            @if ($boe->beneficiaryCompany)
                <div class="subtitle">
                    {{ $boe->beneficiaryCompany->name ?? $boe->beneficiaryCompany->short_name }}
                </div>
            @endif
        </div>

        <table class="bordered">
            <tr>
                <td class="bordered" style="width: 60%;">
                    <strong>Place & Date of Drawing</strong><br>
                    {{ $boe->place_of_drawing }}
                    @if ($boe->issue_date)
                        , {{ $boe->issue_date->format('d M Y') }}
                    @endif
                </td>
                <td class="bordered right" style="width: 40%;">
                    <strong>Amount</strong><br>
                    {{ $boe->currency?->code }} {{ number_format($boe->amount, 2) }}
                </td>
            </tr>
            <tr>
                <td class="bordered" colspan="2">
                    <strong>At {{ $boe->tenor_days }} days after sight / date</strong>
                    pay this {{ strtolower($boe->boe_type) }} of Exchange to the order of
                    @if ($boe->beneficiaryCompany)
                        <strong>{{ $boe->beneficiaryCompany->name ?? $boe->beneficiaryCompany->short_name }}</strong>
                    @else
                        <strong>__________________________</strong>
                    @endif
                    the sum of <strong>{{ $boe->amount_in_words }}</strong>.
                </td>
            </tr>
            <tr>
                <td class="bordered" colspan="2">
                    <strong>Drawee (Customer)</strong><br>
                    {{ $boe->customer?->name }}<br>
                    {!! nl2br(e($boe->drawee_address)) !!}
                </td>
            </tr>
            <tr>
                <td class="bordered" colspan="2">
                    <strong>Drawee Bank</strong><br>
                    {{ $boe->drawee_bank_name }}<br>
                    {!! nl2br(e($boe->drawee_bank_address)) !!}
                </td>
            </tr>
            <tr>
                <td class="bordered">
                    <strong>Related LC</strong><br>
                    @if ($boe->lcReceive)
                        LC No: {{ $boe->lcReceive->lc_number }}<br>
                        Issue Date: {{ optional($boe->lcReceive->lc_date)->format('d M Y') }}
                    @else
                        &nbsp;
                    @endif
                </td>
                <td class="bordered">
                    <strong>Commercial Invoice</strong><br>
                    @if ($boe->commercialInvoice)
                        Invoice No: {{ $boe->commercialInvoice->invoice_number }}<br>
                        Date: {{ optional($boe->commercialInvoice->invoice_date)->format('d M Y') }}
                    @else
                        &nbsp;
                    @endif
                </td>
            </tr>
        </table>

        <div class="mt-3">
            <table style="width:100%;">
                <tr>
                    <td style="width:60%;"></td>
                    <td class="center" style="width:40%;">
                        For and on behalf of<br>
                        @if ($boe->beneficiaryCompany)
                            {{ $boe->beneficiaryCompany->name ?? $boe->beneficiaryCompany->short_name }}
                        @endif
                        <br><br><br>
                        _______________________________<br>
                        Authorised Signature
                    </td>
                </tr>
            </table>
        </div>

        <div class="mt-2 small">
            NOTE: This {{ strtolower($boe->boe_type) }} of Exchange becomes void when the other
            of the same tenor and date is paid.
        </div>
    </div>
</body>

</html>
