<?php

namespace App\Http\Controllers\Print;

use App\Models\NegotiationLetter;
use Illuminate\Database\Eloquent\Model;

class NegotiationLetterPrintController extends BaseDocumentPrintController
{
    protected function getView(): string
    {
        return 'pdf.negotiation-letter';
    }

    protected function getFileName(Model $record): string
    {
        /** @var NegotiationLetter $record */
        $no = $record->letter_number ?? $record->id;

        return 'NegotiationLetter_' . $no . '.pdf';
    }

    protected function getRelations(): array
    {
        return [
            'commercialInvoice.customer',
            'beneficiaryCompany',
            'currency',
        ];
    }
}