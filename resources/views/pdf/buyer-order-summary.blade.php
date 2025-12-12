<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Buyer Order Summary</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111;
        }

        .row {
            width: 100%;
        }

        .muted {
            color: #666;
        }

        .h1 {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }

        .card {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border-bottom: 1px solid #eee;
            padding: 6px;
        }

        th {
            text-align: left;
            background: #f6f6f6;
        }

        .right {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="row">
        <p class="h1">Buyer Order Summary</p>
        <p class="muted" style="margin-top: 4px;">
            Order No: <strong>{{ $record->order_number }}</strong>
            @if ($record->order_date)
                • Date: <strong>{{ \Carbon\Carbon::parse($record->order_date)->format('d M, Y') }}</strong>
            @endif
        </p>
    </div>

    <div class="card">
        <table>
            <tr>
                <td><strong>
                        @if ($record->customer?->name)
                            — Customer:
                            <span class="font-medium">{{ $record->customer->name }}</span>
                        @endif
                </td>
                <td><strong>Beneficiary</strong><br><span
                        class="muted">{{ $record->beneficiaryCompany?->short_name ?? '-' }}</span></td>
                <td><strong>Status</strong><br><span class="muted">{{ $record->status ?? '-' }}</span></td>
            </tr>
        </table>
    </div>

    <div class="card">
        <table>
            <tr>
                <td><strong>Total Styles</strong><br>{{ $summary['total_styles'] ?? 0 }}</td>
                <td><strong>Total Order
                        Qty</strong><br>{{ number_format((float) ($summary['total_order_qty'] ?? 0), 2) }}</td>
                <td><strong>Allocated Qty</strong><br>{{ number_format((float) ($summary['allocated_qty'] ?? 0), 2) }}
                </td>
                <td><strong>Remaining Qty</strong><br>{{ number_format((float) ($summary['remaining_qty'] ?? 0), 2) }}
                </td>
            </tr>
            <tr>
                <td colspan="4"><strong>Order Value</strong>:
                    {{ number_format((float) ($summary['order_value'] ?? 0), 2) }}
                </td>
            </tr>
        </table>
    </div>

    <div class="card">
        <p style="margin: 0 0 6px 0;"><strong>Factory Allocation Breakdown</strong></p>
        <table>
            <thead>
                <tr>
                    <th>Factory</th>
                    <th class="right">Allocated Qty</th>
                    <th class="right">Value</th>
                </tr>
            </thead>
            <tbody>
                @forelse($factoryRows as $row)
                    <tr>
                        <td>{{ $row->factory_name }}</td>
                        <td class="right">{{ number_format((float) $row->total_qty, 2) }}</td>
                        <td class="right">{{ number_format((float) $row->total_value, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="muted">No allocations found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</body>

</html>
