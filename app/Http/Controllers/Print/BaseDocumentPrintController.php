<?php

namespace App\Http\Controllers\Print;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

abstract class BaseDocumentPrintController
{
    /**
     * Must be implemented by each document type.
     */
    abstract protected function getView(): string;
    abstract protected function getFileName($record): string;
    abstract protected function getRelations(): array;

    /**
     * Shared print logic reused by ALL document types.
     */
    public function print($record): Response
    {
        // Load required relationships
        if (!empty($this->getRelations())) {
            $record->load($this->getRelations());
        }

        // Render PDF using view + data
        $pdf = Pdf::loadView($this->getView(), [
            'record' => $record
        ])->setPaper('a4');

        // Stream back to browser
        return $pdf->stream($this->getFileName($record));
    }
}