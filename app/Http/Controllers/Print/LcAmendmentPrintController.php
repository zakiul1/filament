<?php

namespace App\Http\Controllers\Print;

use App\Models\LcAmendment;
use Illuminate\Database\Eloquent\Model;

class LcAmendmentPrintController extends BaseDocumentPrintController
{
    protected function getView(): string
    {
        return 'pdf.lc-amendment';
    }

    protected function getFileName(Model $record): string
    {
        /** @var LcAmendment $record */
        $no = $record->amendment_number ?? $record->id;

        return 'LCA-' . $no . '.pdf';
    }

    protected function getRelations(): array
    {
        return [
            'lcReceive.customer.country',
            'lcReceive.beneficiaryCompany',
            'lcReceive.currency',
        ];
    }
}