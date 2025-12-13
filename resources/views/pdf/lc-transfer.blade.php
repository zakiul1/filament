<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>LC Transfer - {{ $record->transfer_no }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 22px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111;
        }

        .title {
            font-size: 18px;
            font-weight: 700;
            margin: 0 0 6px 0;
        }

        .muted {
            color: #666;
        }

        .small {
            font-size: 11px;
        }

        .box {
            border: 1px solid #ddd;
            padding: 10px;
        }

        .hr {
            height: 1px;
            background: #e5e5e5;
            margin: 12px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th {
            border: 1px solid #ddd;
            padding: 6px;
            vertical-align: top;
        }

        th {
            background: #f5f5f5;
            text-align: left;
            font-weight: 700;
        }

        .no-border td,
        .no-border th {
            border: none;
            padding: 0;
        }

        .right {
            text-align: right;
        }
    </style>
</head>

<body>

    {{-- Header --}}
    <div class="box">
        <table class="no-border">
            <tr>
                <td>
                    <div class="title">LC TRANSFER</div>
                    <div class="small muted">System Generated</div>
                </td>
                <td style="text-align:right; width:260px;">
                    <div class="small">
                        <div><span class="muted">Transfer No:</span> <strong>{{ $record->transfer_no }}</strong></div>
                        <div>
                            <span class="muted">Transfer Date:</span>
                            <strong>{{ optional($record->transfer_date)->format('d-M-Y') ?? '-' }}</strong>
                        </div>
                        <div><span class="muted">Status:</span>
                            <strong>{{ strtoupper($record->status ?? '-') }}</strong>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="hr"></div>

    {{-- LC & Parties --}}
    <div class="box">
        <table>
            <tr>
                <th style="width:180px;">Source LC</th>
                <td>
                    <div><span class="muted">LC Ref/No:</span>
                        <strong>{{ $record->lcReceive->lc_no ?? ($record->lcReceive->lc_number ?? '-') }}</strong>
                    </div>
                    <div class="small muted">This transfer is linked to the received LC record.</div>
                </td>
            </tr>

            <tr>
                <th>Customer / Applicant</th>
                <td>
                    <strong>{{ $record->lcReceive->customer->name ?? '-' }}</strong>
                </td>
            </tr>

            <tr>
                <th>Factory / Supplier</th>
                <td>
                    <strong>{{ $record->factory->name ?? '-' }}</strong><br>
                    <span class="small muted">
                        {{ $record->factory->address_line_1 ?? '' }}
                        {{ $record->factory->city ? ', ' . $record->factory->city : '' }}
                    </span>
                </td>
            </tr>

            <tr>
                <th>Bank</th>
                <td>
                    <strong>{{ $record->lcReceive->bank->name ?? '-' }}</strong>
                </td>
            </tr>
        </table>
    </div>

    <div class="hr"></div>

    {{-- Amount --}}
    <div class="box">
        <table>
            <tr>
                <th style="width:180px;">Currency</th>
                <td><strong>{{ $record->currency->code ?? ($record->currency->name ?? '-') }}</strong></td>
            </tr>

            <tr>
                <th>Transfer Amount</th>
                <td class="right">
                    <strong>{{ number_format((float) $record->transfer_amount, 2) }}</strong>
                </td>
            </tr>

            <tr>
                <th>Tolerance (+)</th>
                <td class="right">
                    {{ $record->tolerance_plus !== null ? number_format((float) $record->tolerance_plus, 2) . '%' : '-' }}
                </td>
            </tr>

            <tr>
                <th>Tolerance (-)</th>
                <td class="right">
                    {{ $record->tolerance_minus !== null ? number_format((float) $record->tolerance_minus, 2) . '%' : '-' }}
                </td>
            </tr>
        </table>
    </div>

    {{-- Remarks --}}
    @if (!empty($record->remarks))
        <div class="hr"></div>
        <div class="box">
            <div style="font-weight:700; margin-bottom:6px;">Remarks</div>
            <div>{{ $record->remarks }}</div>
        </div>
    @endif

    {{-- Footer --}}
    <div class="hr"></div>
    <div class="small muted">
        Printed on: {{ now()->format('d-M-Y h:i A') }}
    </div>

</body>

</html>
