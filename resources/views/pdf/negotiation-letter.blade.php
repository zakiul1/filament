<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Negotiation Letter - {{ $record->letter_number }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
        }

        .page {
            width: 100%;
        }

        .header {
            margin-bottom: 20px;
        }

        .company-name {
            font-size: 14px;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
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

        .mt-4 {
            margin-top: 16px;
        }

        .small {
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            vertical-align: top;
            padding: 2px 0;
        }

        .signature-box {
            margin-top: 40px;
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="page">

        {{-- Header: Beneficiary & Date --}}
        <div class="header">
            <table>
                <tr>
                    <td>
                        @if ($record->beneficiaryCompany)
                            <div class="company-name">
                                {{ $record->beneficiaryCompany->name ?? $record->beneficiaryCompany->short_name }}
                            </div>
                            <div class="small">
                                {{ $record->beneficiaryCompany->address_line_1 ?? '' }}<br>
                                {{ $record->beneficiaryCompany->address_line_2 ?? '' }}
                            </div>
                        @endif
                    </td>
                    <td class="text-right">
                        @if ($record->letter_date)
                            {{ optional($record->letter_date)->format('d M Y') }}
                        @endif
                        <br>
                        Ref: {{ $record->letter_number }}
                    </td>
                </tr>
            </table>
        </div>

        {{-- Bank Address --}}
        <div class="mt-2">
            <strong>{{ $record->bank_name ?? '-' }}</strong><br>
            {{ $record->bank_branch ?? '' }}<br>
            @if (!empty($record->swift_code))
                SWIFT: {{ $record->swift_code }}<br>
            @endif
        </div>

        <div class="mt-2">
            Dear Sir(s),
        </div>

        {{-- Subject line --}}
        <div class="mt-2">
            <strong>Sub: Submission of export documents for negotiation</strong>
        </div>

        {{-- Body --}}
        <div class="mt-3">
            We are submitting herewith export documents for negotiation against:
        </div>

        @php
            // ✅ Correct buyer from Commercial Invoice
            $buyer = $record->commercialInvoice?->customer;

            // ✅ LC number: use lcReceive if you have relation, otherwise show linked lc_receive_id if exists
            $lcNo = $record->lcReceive?->lc_number ?? null;
        @endphp

        <div class="mt-2">
            <table>
                <tr>
                    <td style="width: 30%;">Customer (Buyer)</td>
                    <td style="width: 70%;">
                        : {{ $buyer?->name ?? '-' }}
                    </td>
                </tr>

                <tr>
                    <td>LC No.</td>
                    <td>
                        : {{ $lcNo ?? '-' }}
                    </td>
                </tr>

                <tr>
                    <td>Commercial Invoice No.</td>
                    <td>
                        : {{ $record->commercialInvoice?->invoice_number ?? '-' }}
                        @if ($record->commercialInvoice?->invoice_date)
                            dated {{ optional($record->commercialInvoice->invoice_date)->format('d M Y') }}
                        @endif
                    </td>
                </tr>

                <tr>
                    <td>Invoice Amount</td>
                    <td>
                        : {{ $record->currency?->code ?? '' }}
                        {{ number_format((float) ($record->invoice_amount ?? 0), 2) }}
                    </td>
                </tr>

                <tr>
                    <td>Deductions</td>
                    <td>
                        : {{ $record->currency?->code ?? '' }}
                        {{ number_format((float) ($record->deductions ?? 0), 2) }}
                    </td>
                </tr>

                <tr>
                    <td><strong>Net Payable</strong></td>
                    <td>
                        : <strong>{{ $record->currency?->code ?? '' }}
                            {{ number_format((float) ($record->net_payable_amount ?? 0), 2) }}</strong>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Enclosed documents --}}
        <div class="mt-3">
            The following documents are enclosed herewith for negotiation / collection:
        </div>
        <div class="mt-1 small">
            • Original Commercial Invoice<br>
            • Packing List<br>
            • Bill of Lading / Air Waybill (if applicable)<br>
            • Bill(s) of Exchange<br>
            • Certificate of Origin and other required export documents
        </div>

        {{-- Custom remarks --}}
        @if (!empty($record->remarks))
            <div class="mt-3">
                {!! nl2br(e($record->remarks)) !!}
            </div>
        @endif

        <div class="mt-3">
            We kindly request you to negotiate the above documents as per LC terms and
            credit the proceeds to our account after deduction of your usual charges.
        </div>

        {{-- Signature --}}
        <div class="signature-box">
            For and on behalf of<br>
            {{ $record->beneficiaryCompany?->name ?? ($record->beneficiaryCompany?->short_name ?? '') }}
            <br><br><br>
            _______________________________<br>
            Authorised Signatory
        </div>

    </div>
</body>

</html>
