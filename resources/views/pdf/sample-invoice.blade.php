{{-- resources/views/pdf/sample-invoice.blade.php --}}
@php
    $company = $record->beneficiaryCompany;
    $customer = $record->customer;
    $currency = $record->currency;
    $items = $record->items;
    $incoterm = $record->incoterm;
    $shipmentMode = $record->shipmentMode;
    $pol = $record->portOfLoading;
    $pod = $record->portOfDischarge;
    $courier = $record->courier;
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sample Invoice - {{ $record->sample_number }}</title>
    <style>
        * {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            box-sizing: border-box;
        }

        body {
            margin: 20px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .fw-bold {
            font-weight: bold;
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

        .mb-0 {
            margin-bottom: 0;
        }

        .mb-1 {
            margin-bottom: 4px;
        }

        .mb-2 {
            margin-bottom: 8px;
        }

        .mb-3 {
            margin-bottom: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .bordered th,
        .bordered td {
            border: 1px solid #333;
            padding: 4px;
        }

        .no-border td {
            border: none;
            padding: 2px 0;
        }

        .small {
            font-size: 10px;
        }
    </style>
</head>

<body>
    {{-- Company Header --}}
    <table class="no-border">
        <tr>
            <td class="text-left fw-bold" style="font-size: 14px;">
                {{ $company->legal_name ?? ($company->display_name ?? 'Beneficiary Company') }}
            </td>
        </tr>
        <tr>
            <td class="text-left small">
                {{ $company->address_line_1 ?? '' }}
                @if (!empty($company->address_line_2))
                    , {{ $company->address_line_2 }}
                @endif
                @if (!empty($company->city))
                    , {{ $company->city }}
                @endif
            </td>
        </tr>
        @if (!empty($company->country))
            <tr>
                <td class="text-left small">
                    {{ $company->country->name ?? '' }}
                </td>
            </tr>
        @endif
        @if (!empty($company->phone))
            <tr>
                <td class="text-left small">
                    Tel: {{ $company->phone }}
                    @if (!empty($company->email))
                        | Email: {{ $company->email }}
                    @endif
                </td>
            </tr>
        @endif
    </table>

    <h2 class="text-center mb-0" style="margin-top: 8px;">SAMPLE INVOICE</h2>

    {{-- Basic Info --}}
    <table class="no-border" style="margin-top: 12px;">
        <tr>
            <td style="width: 60%;">
                <table class="no-border">
                    <tr>
                        <td class="fw-bold" style="width: 25%;">To:</td>
                        <td>
                            {{ $customer->name ?? '' }}<br>
                            @if (!empty($customer->address_line_1))
                                {{ $customer->address_line_1 }}<br>
                            @endif
                            @if (!empty($customer->address_line_2))
                                {{ $customer->address_line_2 }}<br>
                            @endif
                            @if (!empty($customer->city))
                                {{ $customer->city }}
                            @endif
                            @if (!empty($customer->country))
                                , {{ $customer->country->name }}
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 40%;">
                <table class="no-border">
                    <tr>
                        <td class="fw-bold" style="width: 45%;">Sample Inv. No:</td>
                        <td>{{ $record->sample_number }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Date:</td>
                        <td>{{ optional($record->sample_date)->format('d M, Y') }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Currency:</td>
                        <td>{{ $currency->code ?? '' }}</td>
                    </tr>
                    @if ($incoterm)
                        <tr>
                            <td class="fw-bold">Incoterm:</td>
                            <td>{{ $incoterm->code }}
                                {{ $incoterm->description ? ' - ' . $incoterm->description : '' }}
                            </td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    {{-- Shipment / Courier Info --}}
    <table class="no-border" style="margin-top: 6px;">
        <tr>
            <td style="width: 33%;">
                <span class="fw-bold">Port of Loading:</span><br>
                {{ $pol->name ?? '' }}
            </td>
            <td style="width: 33%;">
                <span class="fw-bold">Port of Discharge:</span><br>
                {{ $pod->name ?? '' }}
            </td>
            <td style="width: 34%;">
                <span class="fw-bold">Courier:</span><br>
                @if ($courier)
                    {{ $courier->name }}
                    @if ($record->courier_tracking_no)
                        <br>Tracking: {{ $record->courier_tracking_no }}
                    @endif
                @endif
            </td>
        </tr>
    </table>

    {{-- Items --}}
    <table class="bordered" style="margin-top: 12px;">
        <thead>
            <tr>
                <th style="width: 4%;">Ln</th>
                <th style="width: 14%;">Style</th>
                <th style="width: 28%;">Description</th>
                <th style="width: 10%;">Sample Type</th>
                <th style="width: 8%;">Color</th>
                <th style="width: 8%;">Size</th>
                <th style="width: 8%;">UOM</th>
                <th style="width: 10%;">Qty</th>
                <th style="width: 10%;">Unit Price</th>
                <th style="width: 10%;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @php
                $line = 1;
            @endphp

            @foreach ($items as $item)
                <tr>
                    <td class="text-center">{{ $item->line_no ?? $line }}</td>
                    <td>{{ $item->style_ref }}</td>
                    <td>
                        {{ $item->item_description }}
                        @if ($item->factorySubcategory)
                            <br><span class="small">({{ $item->factorySubcategory->name }})</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->sample_type }}</td>
                    <td class="text-center">{{ $item->color }}</td>
                    <td class="text-center">{{ $item->size }}</td>
                    <td class="text-center">{{ $item->unit }}</td>
                    <td class="text-right">{{ number_format($item->quantity, 2) }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 4) }}</td>
                    <td class="text-right">{{ number_format($item->amount, 2) }}</td>
                </tr>

                @php $line++; @endphp
            @endforeach
        </tbody>
    </table>

    {{-- Totals --}}
    <table class="no-border" style="margin-top: 10px;">
        <tr>
            <td style="width: 60%;">
                <span class="fw-bold">Amount in Words:</span><br>
                {{ $record->total_amount_in_words ?? '' }}
            </td>
            <td style="width: 40%;">
                <table class="bordered">
                    <tr>
                        <td class="fw-bold" style="width: 50%;">Subtotal</td>
                        <td class="text-right" style="width: 50%;">
                            {{ number_format($record->subtotal, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Discount</td>
                        <td class="text-right">
                            {{ number_format($record->discount_amount, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Other Charges</td>
                        <td class="text-right">
                            {{ number_format($record->other_charges, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Total</td>
                        <td class="text-right">
                            {{ number_format($record->total_amount, 2) }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Remarks --}}
    @if ($record->remarks)
        <div class="mt-3">
            <span class="fw-bold">Remarks:</span><br>
            {!! nl2br(e($record->remarks)) !!}
        </div>
    @endif

</body>

</html>
