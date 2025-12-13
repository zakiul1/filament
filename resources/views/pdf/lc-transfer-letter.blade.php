<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>LC Transfer Letter - {{ $record->transfer_no }}</title>
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

        .small {
            font-size: 11px;
        }

        .muted {
            color: #666;
        }

        .bold {
            font-weight: 700;
        }

        .u {
            text-decoration: underline;
        }

        .mb4 {
            margin-bottom: 4px;
        }

        .mb8 {
            margin-bottom: 8px;
        }

        .mb12 {
            margin-bottom: 12px;
        }

        .mb16 {
            margin-bottom: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .no-border td {
            border: none;
            padding: 0;
            vertical-align: top;
        }

        .items th,
        .items td {
            border: 1px solid #222;
            padding: 6px;
            vertical-align: top;
        }

        .items th {
            background: #f2f2f2;
            font-weight: 700;
            text-align: left;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }
    </style>
</head>

<body>

    @php
        // ✅ CustomerBank -> BankBranch -> Bank
        $cb = $lc?->customerBank;
        $branch = $cb?->bankBranch;
        $bank = $branch?->bank;

        $bankName = $bank?->name ?? 'The Manager';
        $branchName = $branch?->name ?? '';
        $branchAddress = $branch?->address ?? '';

        $lcNo = $lc?->lc_number ?? '-';
        $lcDate = $lc?->lc_date ? $lc->lc_date->format('d-M-Y') : '';
        $shipDate = $lc?->last_shipment_date ? $lc->last_shipment_date->format('d-M-Y') : '';
    @endphp

    {{-- Ref + Date --}}
    <table class="no-border mb12">
        <tr>
            <td>
                <div class="mb4"><span class="bold">Ref:</span> {{ $record->transfer_no }}</div>
            </td>
            <td class="right">
                <div class="mb4"><span class="bold">Date:</span>
                    {{ optional($record->transfer_date)->format('d-M-Y') ?? now()->format('d-M-Y') }}
                </div>
            </td>
        </tr>
    </table>

    {{-- To Address --}}
    <div class="mb12">
        <div class="bold">To</div>
        <div class="bold">{{ $bankName }}</div>
        @if ($branchName)
            <div>{{ $branchName }}</div>
        @endif
        @if ($branchAddress)
            <div class="mb4">{{ $branchAddress }}</div>
        @endif
    </div>

    {{-- Subject --}}
    <div class="mb12">
        <span class="bold u">Subject:</span>
        <span class="bold">Transfer of LC # {{ $lcNo }}</span>
    </div>

    {{-- Opening --}}
    <div class="mb12">
        Dear Sir,<br>
        We would like to request you to transfer the mentioned LC partially. The transfer particulars are as below:
    </div>

    {{-- Particulars (1-9) --}}
    <table class="no-border mb16">
        <tr>
            <td style="width:22px;">1.</td>
            <td style="width:220px;">LC Issuing Bank</td>
            <td>: {{ $bankName }}{{ $branchName ? ' - ' . $branchName : '' }}</td>
        </tr>
        <tr>
            <td>2.</td>
            <td>LC No & Date</td>
            <td>: {{ $lcNo }} @if ($lcDate)
                    / {{ $lcDate }}
                @endif
            </td>
        </tr>
        <tr>
            <td>3.</td>
            <td>1st Beneficiary LC Amount</td>
            <td>:
                {{ $lc?->currency?->code ?? '' }}
                {{ number_format((float) ($lc?->lc_amount ?? 0), 2) }}
            </td>
        </tr>
        <tr>
            <td>4.</td>
            <td>Tenor of LC</td>
            <td>:
                {{ $lc?->lc_type ?? '-' }}
                @if ($lc?->presentation_days)
                    (Presentation {{ $lc->presentation_days }} days)
                @endif
            </td>
        </tr>
        <tr>
            <td>5.</td>
            <td>Latest Date of Shipment</td>
            <td>: {{ $shipDate ?: '-' }}</td>
        </tr>
        <tr>
            <td>6.</td>
            <td>Expiry Date & Place</td>
            <td>: {{ $lc?->expiry_date ? $lc->expiry_date->format('d-M-Y') : '-' }}</td>
        </tr>
        <tr>
            <td>7.</td>
            <td>2nd Beneficiary Name & Address</td>
            <td>:
                {{ $record->factory->name ?? '-' }}
                @if (!empty($record->factory?->address_line_1))
                    , {{ $record->factory->address_line_1 }}
                @endif
                @if (!empty($record->factory?->city))
                    , {{ $record->factory->city }}
                @endif
                @if (!empty($record->factory?->country?->name))
                    , {{ $record->factory->country->name }}
                @endif
            </td>
        </tr>
        <tr>
            <td>8.</td>
            <td>2nd Beneficiary Bank Details</td>
            <td>: (Not available in system – add Factory Bank fields or a Factory Bank Account module)</td>
        </tr>
        <tr>
            <td>9.</td>
            <td>Transferring Amount</td>
            <td>:
                {{ $record->currency?->code ?? ($lc?->currency?->code ?? '') }}
                {{ number_format((float) ($record->transfer_amount ?? 0), 2) }}
            </td>
        </tr>
    </table>

    {{-- Items Table --}}
    <div class="mb8 bold">
        Item Details (As per Proforma Invoice {{ $pi?->pi_number ?? '-' }})
    </div>

    <table class="items">
        <thead>
            <tr>
                <th style="width:70px;">Style No</th>
                <th style="width:70px;">PO No</th>
                <th>Item Description</th>
                <th class="right" style="width:70px;">Quantity</th>
                <th class="right" style="width:80px;">Unit Price</th>
                <th class="right" style="width:90px;">Total Value</th>
                <th style="width:75px;">Ship Date</th>
                <th style="width:110px;">Delivery Instruction</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalQty = 0;
                $totalValue = 0;
            @endphp

            @forelse($items as $it)
                @php
                    $qty = (float) ($it->order_qty ?? 0);
                    $unitPrice = (float) ($it->unit_price ?? 0);
                    $value = (float) ($it->amount ?? $qty * $unitPrice);

                    $totalQty += $qty;
                    $totalValue += $value;
                @endphp
                <tr>
                    <td>{{ $it->style_ref ?? '-' }}</td>
                    <td>{{ $pi?->buyer_reference ?? '-' }}</td>
                    <td>{{ $it->item_description ?? '-' }}</td>
                    <td class="right">{{ number_format($qty, 0) }}</td>
                    <td class="right">{{ number_format($unitPrice, 2) }}</td>
                    <td class="right">{{ number_format($value, 2) }}</td>
                    <td>{{ $shipDate ?: '-' }}</td>
                    <td>{{ $pi?->remarks ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="center muted">No PI items found.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="right">TOTAL</th>
                <th class="right">{{ number_format((float) $totalQty, 0) }}</th>
                <th></th>
                <th class="right">{{ number_format((float) $totalValue, 2) }}</th>
                <th colspan="2"></th>
            </tr>
        </tfoot>
    </table>

    <div class="mb16"></div>

    {{-- Closing --}}
    <div class="mb16">
        Your cooperation in this regard will be highly appreciated.
    </div>

    {{-- Signature --}}
    <table class="no-border">
        <tr>
            <td style="width:60%;">
                <div class="small">
                    <span class="bold">For and on behalf of</span><br>
                    <span class="bold">{{ $lc?->beneficiaryCompany?->name ?? 'SIATEX (BD) LIMITED' }}</span>
                </div>
            </td>
            <td class="right">
                <div class="small muted">Authorized Signature</div>
            </td>
        </tr>
    </table>

</body>

</html>
