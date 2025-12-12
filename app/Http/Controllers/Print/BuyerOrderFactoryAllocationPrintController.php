<?php

namespace App\Http\Controllers\Print;

use App\Http\Controllers\Controller;
use App\Models\BuyerOrder;
use Barryvdh\DomPDF\Facade\Pdf;

class BuyerOrderFactoryAllocationPrintController extends Controller
{
    public function show(BuyerOrder $buyerOrder)
    {
        $record = $buyerOrder->load([
            'customer',
            'beneficiaryCompany',
            'items',
            'items.allocations.factory',
        ]);

        // Build factory-wise structure
        $factories = [];

        foreach ($record->items as $item) {
            foreach ($item->allocations as $allocation) {
                $factoryId = $allocation->factory_id;
                $factoryName = $allocation->factory->name ?? 'Unknown Factory';

                if (!isset($factories[$factoryId])) {
                    $factories[$factoryId] = [
                        'factory' => $allocation->factory,
                        'name' => $factoryName,
                        'items' => [],
                        'total_qty' => 0,
                        'total_amount' => 0,
                    ];
                }

                $qty = (float) ($allocation->qty ?? 0);
                $unitPrice = (float) ($item->unit_price ?? 0);
                $amount = $qty * $unitPrice;

                $factories[$factoryId]['items'][] = [
                    'line_no' => $item->line_no,
                    'style_ref' => $item->style_ref,
                    'item_description' => $item->item_description,
                    'color' => $item->color,
                    'size' => $item->size,
                    'unit' => $item->unit,
                    'unit_price' => $unitPrice,
                    'qty' => $qty,
                    'amount' => $amount,
                ];

                $factories[$factoryId]['total_qty'] += $qty;
                $factories[$factoryId]['total_amount'] += $amount;
            }
        }

        // Sort factories by name (nice output)
        usort($factories, fn($a, $b) => strcmp($a['name'], $b['name']));

        $pdf = Pdf::loadView('pdf.buyer-order-factory-allocation', [
            'record' => $record,
            'factories' => $factories,
        ])->setPaper('A4', 'portrait');

        $filename = 'BuyerOrder_FactoryAlloc_' . ($record->order_number ?? $record->id) . '.pdf';

        return $pdf->stream($filename);
    }
}