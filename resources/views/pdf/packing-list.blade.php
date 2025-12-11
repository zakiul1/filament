<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Packing List - {{ $record->pl_number }}</title>
    <style>
        @page {
            margin: 20mm;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            color: #111;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 4px 6px;
            border: 1px solid #333;
        }

        th {
            background: #eee;
        }

        .no-border th,
        .no-border td {
            border: none;
            padding: 2px 4px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .mb-4 {
            margin-bottom: 16px;
        }

        .section-title {
            font-weight: bold;
            margin-top: 12px;
            margin-bottom: 4px;
            font-size: 12px;
            text-transform: uppercase;
        }

        .small {
            font-size: 10px;
        }

        .signature-block {
            margin-top: 40px;
            width: 100%;
        }

        .signature-cell {
            width: 50%;
            text-align: center;
            font-size: 10px;
        }
    </style>
</head>

<body>
    @php
        $ci = optional($record->commercialInvoice);
        $customer = optional($ci->customer ?? $record->customer);
        $beneficiary = optional($record->beneficiaryCompany);
    @endphp

    {{-- HEADER --}}
    <table class="no-border mb-4">
        <tr>
            <td class="text-left">
                <strong>{{ $beneficiary->display_name ?? ($beneficiary->short_name ?? 'SIATEX BD LTD.') }}</strong><br>
                {!! nl2br(e($beneficiary->address_line_1 . "\n" . $beneficiary->address_line_2)) !!}
            </td>
            <td class="text-right">
                <span class="small">Packing List</span><br>
                <strong style="font-size: 16px;">PACKING LIST</strong><br>
                <span class="small">PL No: {{ $record->pl_number }}</span><br>
                <span class="small">PL Date: {{ optional($record->pl_date)->format('d M, Y') }}</span><br>
                @if ($ci)
                    <span class="small">Invoice No: {{ $ci->invoice_number }}</span><br>
                @endif
            </td>
        </tr>
    </table>

    {{-- SHIPPER / CONSIGNEE --}}
    <div class="section-title">Shipper & Consignee</div>
    <table class="mb-4">
        <tr>
            <th width="20%">Shipper</th>
            <td width="30%">
                <strong>{{ $beneficiary->display_name ?? ($beneficiary->short_name ?? 'SIATEX BD LTD.') }}</strong><br>
                {!! nl2br(e($beneficiary->address_line_1 . "\n" . $beneficiary->address_line_2)) !!}
            </td>
            <th width="20%">Consignee</th>
            <td width="30%">
                <strong>{{ $customer->name ?? '' }}</strong><br>
                {!! nl2br(e(($customer->address_line_1 ?? '') . "\n" . ($customer->address_line_2 ?? ''))) !!}
            </td>
        </tr>
    </table>

    {{-- MAIN PACKING TABLE --}}
    <div class="section-title">Packing Details</div>
    <table>
        <thead>
            <tr>
                <th width="7%">Line</th>
                <th width="10%">Ctn From</th>
                <th width="10%">Ctn To</th>
                <th width="10%">Total Ctn</th>
                <th width="28%">Description</th>
                <th width="10%">Qty / Ctn</th>
                <th width="10%">Total Qty</th>
                <th width="7%">N.W.</th>
                <th width="8%">G.W.</th>
                <th width="10%">CBM</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalCartons = 0;
                $totalQty = 0;
                $totalNW = 0;
                $totalGW = 0;
                $totalCBM = 0;
            @endphp

            @foreach ($record->items as $item)
                @php
                    $ctns = $item->total_cartons ?? max(0, $item->carton_to - $item->carton_from + 1);
                    $qty = $item->total_qty ?? $ctns * $item->qty_per_carton;
                    $totalCartons += $ctns;
                    $totalQty += $qty;
                    $totalNW += (float) $item->net_weight;
                    $totalGW += (float) $item->gross_weight;
                    $totalCBM += (float) $item->cbm;
                @endphp
                <tr>
                    <td class="text-center">{{ $item->line_no }}</td>
                    <td class="text-center">{{ $item->carton_from }}</td>
                    <td class="text-center">{{ $item->carton_to }}</td>
                    <td class="text-center">{{ $ctns }}</td>
                    <td>{{ $item->description }}</td>
                    <td class="text-center">{{ $item->qty_per_carton }}</td>
                    <td class="text-center">{{ $qty }}</td>
                    <td class="text-right">{{ number_format($item->net_weight, 2) }}</td>
                    <td class="text-right">{{ number_format($item->gross_weight, 2) }}</td>
                    <td class="text-right">{{ number_format($item->cbm, 3) }}</td>
                </tr>
            @endforeach

            <tr>
                <th colspan="3" class="text-right">TOTAL</th>
                <th class="text-center">{{ $totalCartons }}</th>
                <th></th>
                <th></th>
                <th class="text-center">{{ $totalQty }}</th>
                <th class="text-right">{{ number_format($totalNW, 2) }}</th>
                <th class="text-right">{{ number_format($totalGW, 2) }}</th>
                <th class="text-right">{{ number_format($totalCBM, 3) }}</th>
            </tr>
        </tbody>
    </table>

    {{-- REMARKS --}}
    @if ($record->remarks)
        <div class="section-title">Remarks</div>
        <p class="small">{!! nl2br(e($record->remarks)) !!}</p>
    @endif

    {{-- SIGNATURE --}}
    <table class="signature-block">
        <tr>
            <td class="signature-cell"></td>
            <td class="signature-cell">
                For and on behalf of<br>
                <strong>{{ $beneficiary->display_name ?? ($beneficiary->short_name ?? 'SIATEX BD LTD.') }}</strong>
                <br><br><br><br>
                ___________________________<br>
                Authorised Signature
            </td>
        </tr>
    </table>
</body>

</html>
