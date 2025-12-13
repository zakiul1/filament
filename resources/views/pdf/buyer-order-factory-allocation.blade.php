<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Buyer Order Factory Allocation - {{ $record->order_number }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 22px;
        }

        html,
        body {
            width: 100%;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111;
        }

        .muted {
            color: #666;
        }

        .title {
            font-size: 18px;
            font-weight: 700;
            margin: 0 0 6px 0;
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
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
            vertical-align: top;
            word-wrap: break-word;
        }

        th {
            background: #f5f5f5;
            font-weight: 700;
            text-align: left;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .header-table,
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
        }

        .header-table td,
        .meta-table td {
            border: none;
            padding: 0;
            vertical-align: top;
        }

        .page-break {
            page-break-before: always;
        }
    </style>

</head>

<body>
    {{-- Header --}}
    <div class="box">
        <table class="header-table">
            <tr>
                <td>
                    <p class="title">BUYER ORDER – FACTORY ALLOCATION</p>
                    <p class="small muted">System Generated</p>
                </td>
                <td style="text-align:right; width:260px;">
                    <div class="small">
                        <div><span class="muted">Order No:</span> <strong>{{ $record->order_number }}</strong></div>
                        <div>
                            <span class="muted">Order Date:</span>
                            <strong>{{ optional($record->order_date)->format('d-M-Y') ?? $record->order_date }}</strong>
                        </div>
                        <div><span class="muted">Customer:</span> <strong>{{ $record->customer->name ?? '-' }}</strong>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="hr"></div>

    @php
        $grandQty = 0;
        $grandAmount = 0;
    @endphp

    @forelse($factories as $idx => $fx)
        @php
            $factory = $fx['factory'];
            $grandQty += (float) $fx['total_qty'];
            $grandAmount += (float) $fx['total_amount'];
        @endphp

        <div class="box" style="margin-bottom:10px;">
            <table class="meta-table">
                <tr>
                    <td>
                        <div class="small muted">Factory</div>
                        <div style="font-weight:700; font-size:14px;">{{ $fx['name'] }}</div>
                        <div class="small muted">
                            {{ $factory->address_line_1 ?? '' }}
                            {{ $factory->city ? ', ' . $factory->city : '' }}
                            {{ $factory->country?->name ? ', ' . $factory->country->name : '' }}
                        </div>
                    </td>
                    <td style="text-align:right; width:220px;">
                        <div class="small">
                            <div>
                                <span class="muted">Total Qty:</span>
                                <strong>{{ number_format((float) $fx['total_qty'], 0) }}</strong>
                            </div>
                            <div>
                                <span class="muted">Total Amount:</span>
                                <strong>{{ number_format((float) $fx['total_amount'], 2) }}</strong>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <table>
            <thead>
                <tr>
                    <th class="center" style="width:40px;">Ln</th>
                    <th style="width:90px;">Style</th>
                    <th>Description</th>
                    <th style="width:70px;">Color</th>
                    <th style="width:70px;">Size</th>
                    <th style="width:60px;">Unit</th>
                    <th class="right" style="width:70px;">Qty</th>
                    <th class="right" style="width:90px;">Unit Price</th>
                    <th class="right" style="width:100px;">Amount</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($fx['items'] as $row)
                    <tr>
                        <td class="center">{{ $row['line_no'] ?? '-' }}</td>
                        <td>{{ $row['style_ref'] ?? '-' }}</td>
                        <td>{{ $row['item_description'] ?? '-' }}</td>
                        <td>{{ $row['color'] ?? '-' }}</td>
                        <td>{{ $row['size'] ?? '-' }}</td>
                        <td>{{ $row['unit'] ?? '-' }}</td>
                        <td class="right">{{ number_format((float) $row['qty'], 0) }}</td>
                        <td class="right">{{ number_format((float) $row['unit_price'], 2) }}</td>
                        <td class="right">{{ number_format((float) $row['amount'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr>
                    <th colspan="6" class="right">Factory Total</th>
                    <th class="right">{{ number_format((float) $fx['total_qty'], 0) }}</th>
                    <th></th>
                    <th class="right">{{ number_format((float) $fx['total_amount'], 2) }}</th>
                </tr>
            </tfoot>
        </table>

        @if ($idx < count($factories) - 1)
            <div class="page-break"></div>
        @endif

    @empty
        <p class="muted">No allocations found for this Buyer Order.</p>
    @endforelse

    {{-- Grand Totals --}}
    <div class="hr"></div>
    <div class="box">
        <table class="meta-table">
            <tr>
                <td style="font-weight:700;">Grand Totals</td>
                <td style="text-align:right; width:220px;">
                    <div class="small">
                        <div><span class="muted">Qty:</span>
                            <strong>{{ number_format((float) $grandQty, 0) }}</strong>
                        </div>
                        <div><span class="muted">Amount:</span>
                            <strong>{{ number_format((float) $grandAmount, 2) }}</strong>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
