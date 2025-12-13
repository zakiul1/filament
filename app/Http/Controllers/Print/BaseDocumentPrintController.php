<?php

namespace App\Http\Controllers\Print;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Model;

abstract class BaseDocumentPrintController extends Controller
{
    /**
     * Support invokable controller routes:
     * Route::get('/xxx/{model}/print', Controller::class)
     */
    public function __invoke(Model $record)
    {
        return $this->streamPdf($record);
    }

    /**
     * Use this from controllers that have ->show()
     */
    protected function streamPdf(Model $record)
    {
        $record->load($this->getRelations());

        $pdf = Pdf::loadView($this->getView(), [
            'record' => $record,
        ])->setPaper('A4', 'portrait');

        return $pdf->stream($this->getFileName($record));
    }

    abstract protected function getView(): string;

    abstract protected function getFileName(Model $record): string;

    protected function getRelations(): array
    {
        return [];
    }
}