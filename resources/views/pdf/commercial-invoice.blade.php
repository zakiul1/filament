<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Commercial Invoice - {{ $record->invoice_number }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>
    <h2 style="text-align:center;">COMMERCIAL INVOICE</h2>

    <table>
        <tr>
            <td>
                <strong>Invoice No:</strong> {{ $record->invoice_number }}<br>
                <strong>Date:</strong> {{ optional($record->invoice_date)->format('d M, Y') }}
            </td>
            <td>
                <strong>Beneficiary:</strong>
                {{ optional($record->beneficiaryCompany)->display_name ?? optional($record->beneficiaryCompany)->name }}<br>
                <strong>Currency:</strong> {{ optional($record->currency)->code }}
            </td>
        </tr>
        <tr>
            <td>
                <strong>Buyer:</strong><br>
                {{ optional($record->customer)->name }}<br>
                {{ optional($record->customer)->full_address }}
            </td>
            <td>
                <strong>Incoterm:</strong> {{ optional($record->incoterm)->code }}<br>
                <strong>Shipment Mode:</strong> {{ optional($record->shipmentMode)->name }}<br>
                <strong>Port of Loading:</strong> {{ optional($record->portOfLoading)->name }}<br>
                <strong>Port of Discharge:</strong> {{ optional($record->portOfDischarge)->name }}
            </td>
        </tr>
    </table>

    <h4>Line Items</h4>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Style</th>
                <th>Description</th>
                <th>Color</th>
                <th>Size</th>
                <th class="text-right">Qty</th>
                <th>UOM</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($record->items as $i => $item)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $item->style_ref }}</td>
                    <td>{{ $item->item_description }}</td>
                    <td>{{ $item->color }}</td>
                    <td>{{ $item->size }}</td>
                    <td class="text-right">{{ number_format($item->order_qty, 0) }}</td>
                    <td class="text-center">{{ $item->unit }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 4) }}</td>
                    <td class="text-right">{{ number_format($item->amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="8" class="text-right">Total</th>
                <th class="text-right">{{ number_format($record->total_amount, 2) }}</th>
            </tr>
        </tfoot>
    </table>

    <p><strong>Amount in words:</strong> {{ $record->total_amount_in_words }}</p>

    <p style="margin-top:60px; text-align:right;">
        ___________________________<br>
        Authorized Signature
    </p>
</body>

</html>
