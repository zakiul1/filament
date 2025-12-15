<?php

namespace App\Http\Controllers\Print;

use App\Models\PackingList;
use Illuminate\Database\Eloquent\Model;

class PackingListPrintController extends BaseDocumentPrintController
{
    protected function getView(): string
    {
        return 'pdf.packing-list';
    }

    protected function getFileName(Model $record): string
    {
        /** @var PackingList $record */
        $no = $record->pl_number ?? $record->id;

        return 'PackingList_' . $no . '.pdf';
    }

    protected function getRelations(): array
    {
        return [
            'items',
            'commercialInvoice.customer.country',
            'beneficiaryCompany',
        ];
    }
}