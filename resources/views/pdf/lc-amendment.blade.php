<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>LC Amendment - {{ $record->amendment_number }}</title>
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

        .highlight {
            background: #f8f8f8;
        }
    </style>
</head>

<body>
    @php
        $lc = optional($record->lcReceive);
        $currency = optional($record->currency ?? $lc->currency);
    @endphp

    {{-- HEADER --}}
    <table class="no-border mb-4">
        <tr>
            <td class="text-left">
                <strong>{{ optional($lc->beneficiaryCompany)->display_name ?? (optional($lc->beneficiaryCompany)->short_name ?? 'SIATEX BD LTD.') }}</strong><br>
                {!! nl2br(
                    e(optional($lc->beneficiaryCompany)->address_line_1 . "\n" . optional($lc->beneficiaryCompany)->address_line_2),
                ) !!}
            </td>
            <td class="text-right">
                <span class="small">LC Amendment</span><br>
                <strong style="font-size: 16px;">LETTER OF CREDIT AMENDMENT</strong><br>
                <span class="small">Amendment No: {{ $record->amendment_number }}</span><br>
                <span class="small">Amendment Date: {{ optional($record->amendment_date)->format('d M, Y') }}</span><br>
                <span class="small">Status: {{ ucfirst($record->status) }}</span>
            </td>
        </tr>
    </table>

    {{-- MAIN LC INFO --}}
    <div class="section-title">Original LC Details</div>
    <table class="mb-4">
        <tr>
            <th width="25%">LC No</th>
            <td width="25%">{{ $lc->lc_number }}</td>
            <th width="25%">LC Date</th>
            <td width="25%">{{ optional($lc->lc_date)->format('d M, Y') }}</td>
        </tr>
        <tr>
            <th>Applicant / Customer</th>
            <td colspan="3">
                <strong>{{ optional($lc->customer)->name }}</strong>
                @if (optional($lc->customer)->country)
                    ({{ optional($lc->customer->country)->name }})
                @endif
            </td>
        </tr>
        <tr>
            <th>Beneficiary</th>
            <td colspan="3">
                <strong>{{ optional($lc->beneficiaryCompany)->display_name ?? optional($lc->beneficiaryCompany)->short_name }}</strong>
            </td>
        </tr>
    </table>

    {{-- AMENDMENT SUMMARY --}}
    <div class="section-title">Amendment Summary</div>
    <table class="mb-4">
        <tr>
            <th width="25%">Amendment Type</th>
            <td width="75%">{{ $record->amendment_type }}</td>
        </tr>
        <tr>
            <th>Currency</th>
            <td>{{ $currency->code }}</td>
        </tr>
        <tr class="highlight">
            <th>LC Amount (Previous)</th>
            <td>{{ number_format((float) ($record->old_lc_amount ?? $lc->lc_amount), 2) }}</td>
        </tr>
        <tr class="highlight">
            <th>LC Amount (New)</th>
            <td><strong>{{ number_format((float) ($record->new_lc_amount ?? $lc->lc_amount), 2) }}</strong></td>
        </tr>
        <tr>
            <th>Tolerance +%</th>
            <td>{{ $record->tolerance_plus ?? ($lc->tolerance_plus ?? 0) }}</td>
        </tr>
        <tr>
            <th>Tolerance -%</th>
            <td>{{ $record->tolerance_minus ?? ($lc->tolerance_minus ?? 0) }}</td>
        </tr>
        <tr>
            <th>Expiry Date (New)</th>
            <td>{{ optional($record->expiry_date ?? $lc->expiry_date)->format('d M, Y') }}</td>
        </tr>
        <tr>
            <th>Last Shipment Date (New)</th>
            <td>{{ optional($record->last_shipment_date ?? $lc->last_shipment_date)->format('d M, Y') }}</td>
        </tr>
    </table>

    {{-- TEXTUAL DESCRIPTION --}}
    @if ($record->change_summary)
        <div class="section-title">Details of Amendment</div>
        <p class="small">{!! nl2br(e($record->change_summary)) !!}</p>
    @endif

    @if ($record->remarks)
        <div class="section-title">Additional Remarks</div>
        <p class="small">{!! nl2br(e($record->remarks)) !!}</p>
    @endif

    @if ($record->internal_notes)
        <div class="section-title">Internal Notes</div>
        <p class="small">{!! nl2br(e($record->internal_notes)) !!}</p>
    @endif

    {{-- CONFIRMATION --}}
    <div class="section-title">Confirmation</div>
    <p class="small">
        This amendment forms an integral part of LC no. {{ $lc->lc_number }} and is subject to
        the same terms and conditions not otherwise amended herein.
    </p>

    {{-- SIGNATURE --}}
    <table class="signature-block">
        <tr>
            <td class="signature-cell"></td>
            <td class="signature-cell">
                For and on behalf of<br>
                <strong>{{ optional($lc->beneficiaryCompany)->display_name ?? (optional($lc->beneficiaryCompany)->short_name ?? 'SIATEX BD LTD.') }}</strong>
                <br><br><br><br>
                ___________________________<br>
                Authorised Signature
            </td>
        </tr>
    </table>
</body>

</html>
