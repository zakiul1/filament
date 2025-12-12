<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Buyer Order - {{ $record->order_number }}</title>
    <style>
        @page {
            margin: 22px;
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

        .sub {
            font-size: 12px;
            margin: 0;
        }

        .box {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 6px;
        }

        .grid {
            width: 100%;
            border-collapse: collapse;
        }

        .grid td {
            vertical-align: top;
            padding: 4px 0;
        }

        .hr {
            height: 1px;
            background: #e5e5e5;
            margin: 12px 0;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.items th,
        table.items td {
            border: 1px solid #ddd;
            padding: 6px;
        }

        table.items th {
            background: #f5f5f5;
            text-align: left;
            font-weight: 700;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .small {
            font-size: 11px;
        }

        .nowrap {
            white-space: nowrap;
        }
    </style>
</head>

<body>

    <div class="box">
        <div style="display:flex; justify-content:space-between;">
            <div>
                <p class="title">BUYER ORDER</p>
                <p class="sub muted">System Generated Document</p>
            </div>
            <div class="small" style="text-align:right;">
                <div><span class="muted">Order No:</span> <strong>{{ $record->order_number }}</strong></div>
                <div><span class="muted">Order Date:</span>
                    <strong>{{ optional($record->order_date)->format('d-M-Y') ?? $record->order_date }}</strong>
                </div>
                <div><span class="muted">Status:</span> <strong>{{ strtoupper($record->status ?? 'draft') }}</strong>
                </div>
            </div>
        </div>

        <div class="hr"></div>

        <table class="grid">
            <tr>
                <td style="width:50%;">
                    <div class="small muted">Customer</div>
                    <div style="font-weight:700;">{{ $record->customer->name ?? '-' }}</div>
                    <div class="small muted">{{ $record->customer->address ?? '' }}</div>
                </td>
                <td style="width:50%;">
                    <div class="small muted">Beneficiary Company</div>
                    <div style="font-weight:700;">
                        {{ $record->beneficiaryCompany->short_name ?? ($record->beneficiaryCompany->name ?? '-') }}
                    </div>
                    <div class="small muted">{{ $record->beneficiaryCompany->address ?? '' }}</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="small muted">Buyer PO</div>
                    <div style="font-weight:700;">{{ $record->buyer_po ?? '-' }}</div>
                </td>
                <td>
                    <div class="small muted">Season / Department</div>
                    <div style="font-weight:700;">
                        {{ $record->season ?? '-' }} @if ($record->department)
                            / {{ $record->department }}
                        @endif
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="small muted">Merchandiser</div>
                    <div style="font-weight:700;">{{ $record->merchandiser_name ?? '-' }}</div>
                </td>
                <td>
                    <div class="small muted">Shipment Window</div>
                    <div style="font-weight:700;">
                        {{ $record->shipment_date_from ? \Carbon\Carbon::parse($record->shipment_date_from)->format('d-M-Y') : '-' }}
                        &nbsp;to&nbsp;
                        {{ $record->shipment_date_to ? \Carbon\Carbon::parse($record->shipment_date_to)->format('d-M-Y') : '-' }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <table class="items">
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
            @php
                $total = 0;
            @endphp

            @forelse($record->items as $it)
                @php
                    $amount = (float) ($it->amount ?? (float) ($it->order_qty ?? 0) * (float) ($it->unit_price ?? 0));
                    $total += $amount;
                @endphp
                <tr>
                    <td class="center">{{ $it->line_no ?? '-' }}</td>
                    <td class="nowrap">{{ $it->style_ref ?? '-' }}</td>
                    <td>
                        <div style="font-weight:700;">{{ $it->item_description ?? '-' }}</div>
                        <div class="small muted">
                            Category: {{ $it->factorySubcategory->name ?? '-' }}
                            @if ($it->factory)
                                • Default Factory: {{ $it->factory->name }}
                            @endif
                        </div>
                    </td>
                    <td>{{ $it->color ?? '-' }}</td>
                    <td>{{ $it->size ?? '-' }}</td>
                    <td>{{ $it->unit ?? '-' }}</td>
                    <td class="right">{{ number_format((float) ($it->order_qty ?? 0), 0) }}</td>
                    <td class="right">{{ number_format((float) ($it->unit_price ?? 0), 2) }}</td>
                    <td class="right">{{ number_format($amount, 2) }}</td>
                </tr>

                @if ($it->allocations && $it->allocations->count())
                    <tr>
                        <td></td>
                        <td colspan="8" class="small">
                            <div class="muted" style="margin-bottom:4px; font-weight:700;">Factory Allocations</div>
                            <table style="width:100%; border-collapse:collapse;">
                                <tr>
                                    <td style="width:70%; padding:2px 0;" class="muted">Factory</td>
                                    <td style="width:30%; padding:2px 0;" class="muted right">Qty</td>
                                </tr>
                                @foreach ($it->allocations as $al)
                                    <tr>
                                        <td style="padding:2px 0;">{{ $al->factory->name ?? '-' }}</td>
                                        <td style="padding:2px 0;" class="right">
                                            {{ number_format((float) ($al->qty ?? 0), 0) }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="9" class="center muted">No items found</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="8" class="right">Total</th>
                <th class="right">
                    {{ number_format((float) ($record->order_value ?? $total), 2) }}
                </th>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top:18px;" class="box">
        <div class="small muted" style="font-weight:700; margin-bottom:6px;">Remarks</div>
        <div class="small">{{ $record->remarks ?? '-' }}</div>

        <div class="hr"></div>

        <table class="grid">
            <tr>
                <td style="width:50%;">
                    <div class="small muted">Prepared By</div>
                    <div style="height:40px;"></div>
                    <div class="small">________________________</div>
                </td>
                <td style="width:50%;">
                    <div class="small muted">Authorized Sign</div>
                    <div style="height:40px;"></div>
                    <div class="small">________________________</div>
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
