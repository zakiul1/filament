<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>LC Receive - {{ $record->lc_number }}</title>
    <style>
        @page {
            margin: 20mm;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            color: #111;
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

        .mb-2 {
            margin-bottom: 8px;
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
    {{-- HEADER --}}
    <table class="no-border mb-4">
        <tr>
            <td class="text-left">
                <strong>{{ optional($record->beneficiaryCompany)->display_name ?? (optional($record->beneficiaryCompany)->short_name ?? 'SIATEX BD LTD.') }}</strong><br>
                {!! nl2br(
                    e(
                        optional($record->beneficiaryCompany)->address_line_1 .
                            "\n" .
                            optional($record->beneficiaryCompany)->address_line_2,
                    ),
                ) !!}
            </td>
            <td class="text-right">
                <span class="small">Letter of Credit</span><br>
                <strong style="font-size: 16px;">LC RECEIVE ADVICE</strong><br>
                <span class="small">LC No: {{ $record->lc_number }}</span><br>
                <span class="small">LC Date: {{ optional($record->lc_date)->format('d M, Y') }}</span><br>
                <span class="small">LC Type: {{ $record->lc_type }}</span><br>
                <span class="small">Status: {{ ucfirst($record->status) }}</span>
            </td>
        </tr>
    </table>

    {{-- PARTIES & BANKS --}}
    <div class="section-title">Applicant / Beneficiary & Banks</div>
    <table class="mb-4">
        <tr>
            <th width="25%">Applicant / Customer</th>
            <td width="75%">
                <strong>{{ optional($record->customer)->name }}</strong><br>
                {!! nl2br(e(optional($record->customer)->address_line_1 . "\n" . optional($record->customer)->address_line_2)) !!}<br>
                {{ optional(optional($record->customer)->country)->name }}
            </td>
        </tr>
        <tr>
            <th>Beneficiary</th>
            <td>
                <strong>{{ optional($record->beneficiaryCompany)->display_name ?? optional($record->beneficiaryCompany)->short_name }}</strong><br>
                {!! nl2br(
                    e(
                        optional($record->beneficiaryCompany)->address_line_1 .
                            "\n" .
                            optional($record->beneficiaryCompany)->address_line_2,
                    ),
                ) !!}
            </td>
        </tr>
        <tr>
            <th>Issuing Bank (Customer Bank)</th>
            <td>
                @php
                    $cb = optional($record->customerBank);
                    $cbBranch = optional($cb->bankBranch);
                    $cbBank = optional($cbBranch->bank);
                @endphp
                <strong>{{ $cbBank->short_name ?? $cbBank->name }}</strong><br>
                {{ $cbBranch->branch_name }}<br>
                {!! nl2br(e($cbBranch->address_line_1 . "\n" . $cbBranch->address_line_2)) !!}
            </td>
        </tr>
        <tr>
            <th>Advising / Beneficiary Bank</th>
            <td>
                @php
                    $bb = optional($record->beneficiaryBankAccount);
                    $bbBranch = optional($bb->bankBranch);
                    $bbBank = optional($bbBranch->bank);
                @endphp
                <strong>{{ $bbBank->short_name ?? $bbBank->name }}</strong><br>
                A/C No: {{ $bb->account_no }}<br>
                {{ $bbBranch->branch_name }}<br>
                {!! nl2br(e($bbBranch->address_line_1 . "\n" . $bbBranch->address_line_2)) !!}
            </td>
        </tr>
        @if ($record->reference_pi_number)
            <tr>
                <th>Linked PI No</th>
                <td>{{ $record->reference_pi_number }}</td>
            </tr>
        @endif
    </table>

    {{-- AMOUNT & VALIDITY --}}
    <div class="section-title">LC Amount & Validity</div>
    <table class="mb-4">
        <tr>
            <th width="25%">Currency</th>
            <td width="25%">{{ optional($record->currency)->code }}</td>
            <th width="25%">LC Amount</th>
            <td width="25%" class="text-right">
                {{ number_format((float) $record->lc_amount, 2) }}
            </td>
        </tr>
        <tr>
            <th>Tolerance +%</th>
            <td>{{ $record->tolerance_plus ?? 0 }}</td>
            <th>Tolerance -%</th>
            <td>{{ $record->tolerance_minus ?? 0 }}</td>
        </tr>
        <tr>
            <th>Amount in Words</th>
            <td colspan="3">{{ $record->lc_amount_in_words }}</td>
        </tr>
        <tr>
            <th>Expiry Date</th>
            <td>{{ optional($record->expiry_date)->format('d M, Y') }}</td>
            <th>Last Shipment Date</th>
            <td>{{ optional($record->last_shipment_date)->format('d M, Y') }}</td>
        </tr>
        <tr>
            <th>Presentation Days</th>
            <td colspan="3">
                {{ $record->presentation_days }}
                <span class="small">days after shipment allowed for document presentation.</span>
            </td>
        </tr>
    </table>

    {{-- SHIPMENT DETAILS --}}
    <div class="section-title">Shipment Details</div>
    <table class="mb-4">
        <tr>
            <th width="25%">Incoterm</th>
            <td width="25%">{{ optional($record->incoterm)->code }}</td>
            <th width="25%">Shipment Mode</th>
            <td width="25%">{{ optional($record->shipmentMode)->name }}</td>
        </tr>
        <tr>
            <th>Port of Loading</th>
            <td>{{ optional($record->portOfLoading)->name }}</td>
            <th>Port of Discharge</th>
            <td>{{ optional($record->portOfDischarge)->name }}</td>
        </tr>
        <tr>
            <th>Partial Shipment</th>
            <td>{{ $record->partial_shipment_allowed ? 'Allowed' : 'Not Allowed' }}</td>
            <th>Transshipment</th>
            <td>{{ $record->transshipment_allowed ? 'Allowed' : 'Not Allowed' }}</td>
        </tr>
        <tr>
            <th>Reimbursement Bank</th>
            <td colspan="3">{{ $record->reimbursement_bank }}</td>
        </tr>
    </table>

    {{-- REMARKS --}}
    @if ($record->remarks)
        <div class="section-title">LC Remarks</div>
        <p class="small">{!! nl2br(e($record->remarks)) !!}</p>
    @endif

    @if ($record->internal_notes)
        <div class="section-title">Internal Notes</div>
        <p class="small">{!! nl2br(e($record->internal_notes)) !!}</p>
    @endif

    {{-- SIGNATURE --}}
    <table class="signature-block">
        <tr>
            <td class="signature-cell"></td>
            <td class="signature-cell">
                For and on behalf of<br>
                <strong>{{ optional($record->beneficiaryCompany)->display_name ?? (optional($record->beneficiaryCompany)->short_name ?? 'SIATEX BD LTD.') }}</strong>
                <br><br><br><br>
                ___________________________<br>
                Authorised Signature
            </td>
        </tr>
    </table>
</body>

</html>
