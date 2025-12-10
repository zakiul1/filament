<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Proforma Invoice</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            margin: 20px;
        }

        .header,
        .section {
            margin-bottom: 12px;
        }

        .company-name {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            text-decoration: underline;
            margin-bottom: 6px;
        }

        .small {
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-table td {
            padding: 3px 4px;
        }

        .box-table th,
        .box-table td {
            border: 1px solid #000;
            padding: 3px 4px;
            vertical-align: top;
        }

        .box-table th {
            background: #f0f0f0;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .w-50 {
            width: 50%;
        }

        .mt-10 {
            margin-top: 10px;
        }

        .mt-20 {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    @php
        $customer = $pi->customer;
        $beneficiary = $pi->beneficiaryCompany;
        $currency = $pi->currency;
    @endphp

    {{-- HEADER --}}
    <div class="header">
        <table>
            <tr>
                <td class="w-50">
                    @if ($beneficiary)
                        <div class="company-name">
                            {{ $beneficiary->display_name ?? ($beneficiary->name ?? $beneficiary->short_name) }}</div>
                        <div>{{ $beneficiary->address_line_1 }}</div>
                        @if ($beneficiary->address_line_2)
                            <div>{{ $beneficiary->address_line_2 }}</div>
                        @endif
                        <div>
                            {{ $beneficiary->city }}
                            @if ($beneficiary->state)
                                , {{ $beneficiary->state }}
                            @endif
                            @if ($beneficiary->country)
                                , {{ $beneficiary->country->name ?? '' }}
                            @endif
                        </div>
                        @if ($beneficiary->email)
                            <div class="small">Email: {{ $beneficiary->email }}</div>
                        @endif
                        @if ($beneficiary->phone)
                            <div class="small">Phone: {{ $beneficiary->phone }}</div>
                        @endif
                    @else
                        <div class="company-name">[Beneficiary Company]</div>
                    @endif
                </td>
                <td class="w-50 text-right small">
                    <div>PI No: <strong>{{ $pi->pi_number }}</strong></div>
                    <div>PI Date: <strong>{{ optional($pi->pi_date)->format('d M Y') }}</strong></div>
                    @if ($pi->revision_no)
                        <div>Revision: <strong>{{ $pi->revision_no }}</strong></div>
                    @endif
                    @if ($pi->status)
                        <div>Status: <strong>{{ strtoupper($pi->status) }}</strong></div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <div class="title">PROFORMA INVOICE</div>

    {{-- PARTIES --}}
    <div class="section">
        <table class="box-table">
            <tr>
                <th class="w-50">Buyer</th>
                <th class="w-50">Consignee / Notify</th>
            </tr>
            <tr>
                <td>
                    @if ($customer)
                        <strong>{{ $customer->name }}</strong><br>
                        {{ $customer->address_line_1 }}<br>
                        @if ($customer->address_line_2)
                            {{ $customer->address_line_2 }}<br>
                        @endif
                        {{ $customer->city }}
                        @if ($customer->state)
                            , {{ $customer->state }}
                        @endif
                        @if ($customer->country)
                            , {{ $customer->country->name ?? '' }}
                        @endif
                        <br>
                        @if ($customer->email)
                            <span class="small">Email: {{ $customer->email }}</span><br>
                        @endif
                        @if ($customer->phone)
                            <span class="small">Phone: {{ $customer->phone }}</span>
                        @endif
                    @else
                        [Buyer details]
                    @endif
                </td>
                <td>
                    {{-- For now repeat buyer, adjust later if you add consignee fields --}}
                    @if ($customer)
                        <strong>{{ $customer->name }}</strong><br>
                        {{ $customer->address_line_1 }}<br>
                        @if ($customer->address_line_2)
                            {{ $customer->address_line_2 }}<br>
                        @endif
                        {{ $customer->city }}
                        @if ($customer->state)
                            , {{ $customer->state }}
                        @endif
                        @if ($customer->country)
                            , {{ $customer->country->name ?? '' }}
                        @endif
                    @else
                        [Consignee / Notify]
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- PAYMENT / SHIPMENT SUMMARY --}}
    <div class="section">
        <table class="box-table">
            <tr>
                <th>Currency</th>
                <th>Incoterm</th>
                <th>Shipment Mode</th>
                <th>Payment Term</th>
            </tr>
            <tr>
                <td>{{ $currency?->code ?? '' }}</td>
                <td>
                    {{ $pi->incoterm?->code }}
                    @if ($pi->place_of_delivery)
                        - {{ $pi->place_of_delivery }}
                    @endif
                </td>
                <td>{{ $pi->shipmentMode?->name }}</td>
                <td>{{ $pi->paymentTerm?->name }}</td>
            </tr>
            <tr>
                <th>Port of Loading</th>
                <th>Port of Discharge</th>
                <th>Shipment Window</th>
                <th>PI Valid Up To</th>
            </tr>
            <tr>
                <td>{{ $pi->portOfLoading?->name }}</td>
                <td>{{ $pi->portOfDischarge?->name }}</td>
                <td>
                    @if ($pi->shipment_date_from)
                        {{ optional($pi->shipment_date_from)->format('d M Y') }}
                    @endif
                    @if ($pi->shipment_date_to)
                        – {{ optional($pi->shipment_date_to)->format('d M Y') }}
                    @endif
                </td>
                <td>
                    @if ($pi->validity_date)
                        {{ optional($pi->validity_date)->format('d M Y') }}
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- LINE ITEMS --}}
    <div class="section">
        <table class="box-table">
            <thead>
                <tr>
                    <th style="width: 4%">Ln</th>
                    <th style="width: 14%">Style / Ref</th>
                    <th style="width: 32%">Description</th>
                    <th style="width: 10%">Color</th>
                    <th style="width: 8%">Size</th>
                    <th style="width: 8%">UOM</th>
                    <th style="width: 10%" class="text-right">Qty</th>
                    <th style="width: 12%" class="text-right">Unit Price</th>
                    <th style="width: 12%" class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @php $currencyCode = $currency?->code ?? ''; @endphp
                @foreach ($pi->items as $item)
                    <tr>
                        <td class="text-center">{{ $item->line_no }}</td>
                        <td>{{ $item->style_ref }}</td>
                        <td>
                            {{ $item->item_description }}
                            @if ($item->factorySubcategory)
                                <br><span class="small">{{ $item->factorySubcategory->name }}</span>
                            @endif
                        </td>
                        <td>{{ $item->color }}</td>
                        <td>{{ $item->size }}</td>
                        <td class="text-center">{{ $item->unit }}</td>
                        <td class="text-right">{{ number_format((float) $item->order_qty, 0) }}</td>
                        <td class="text-right">{{ $currencyCode }} {{ number_format((float) $item->unit_price, 4) }}
                        </td>
                        <td class="text-right">{{ $currencyCode }} {{ number_format((float) $item->amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="8" class="text-right">Sub Total</th>
                    <th class="text-right">{{ $currencyCode }} {{ number_format((float) $pi->subtotal, 2) }}</th>
                </tr>
                <tr>
                    <th colspan="8" class="text-right">Discount</th>
                    <th class="text-right">- {{ $currencyCode }} {{ number_format((float) $pi->discount_amount, 2) }}
                    </th>
                </tr>
                <tr>
                    <th colspan="8" class="text-right">Other Charges</th>
                    <th class="text-right">{{ $currencyCode }} {{ number_format((float) $pi->other_charges, 2) }}</th>
                </tr>
                <tr>
                    <th colspan="8" class="text-right">Total</th>
                    <th class="text-right">{{ $currencyCode }} {{ number_format((float) $pi->total_amount, 2) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- AMOUNT IN WORDS & REMARKS --}}
    <div class="section">
        <table class="box-table">
            <tr>
                <th style="width: 60%">Amount in Words</th>
                <th style="width: 40%">Buyer Remarks</th>
            </tr>
            <tr>
                <td>{{ $pi->total_amount_in_words }}</td>
                <td>{{ $pi->remarks }}</td>
            </tr>
        </table>
    </div>

    {{-- SIGNATURE --}}
    <div class="section mt-20">
        <table class="meta-table">
            <tr>
                <td class="w-50">
                    <div class="small">Prepared By:</div>
                    <br><br><br>
                    <div>______________________________</div>
                </td>
                <td class="w-50 text-right">
                    <div class="small">For and on behalf of</div>
                    <div><strong>{{ $beneficiary->display_name ?? ($beneficiary->name ?? '') }}</strong></div>
                    <br><br><br>
                    <div>Authorized Signature &amp; Company Seal</div>
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
