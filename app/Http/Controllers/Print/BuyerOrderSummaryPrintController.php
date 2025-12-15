<?php

namespace App\Http\Controllers\Print;

use App\Models\BuyerOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BuyerOrderSummaryPrintController extends BaseDocumentPrintController
{
    protected function getView(): string
    {
        return 'pdf.buyer-order-summary';
    }

    protected function getFileName(Model $record): string
    {
        /** @var BuyerOrder $record */
        return 'BuyerOrderSummary_' . ($record->order_number ?? $record->id) . '.pdf';
    }

    protected function getRelations(): array
    {
        return [
            'customer',
            'beneficiaryCompany',
            'items',
        ];
    }

    protected function getPaper(): array
    {
        return ['A4', 'portrait'];
    }

    protected function getExtraViewData(Model $record): array
    {
        /** @var BuyerOrder $record */
        $orderId = $record->id;

        $totalStyles = (int) $record->items->count();
        $totalOrderQty = (float) $record->items->sum('order_qty');
        $itemsTotalValue = (float) $record->items->sum('amount');

        $allocatedQty = (float) DB::table('buyer_order_item_allocations as a')
            ->join('buyer_order_items as i', 'i.id', '=', 'a.buyer_order_item_id')
            ->where('i.buyer_order_id', $orderId)
            ->sum('a.qty');

        $remainingQty = (float) ($totalOrderQty - $allocatedQty);

        $orderValue = (float) ($record->order_value ?? 0);
        if ($orderValue <= 0) {
            $orderValue = $itemsTotalValue;
        }

        $factoryRows = DB::table('buyer_order_item_allocations as a')
            ->join('buyer_order_items as i', 'i.id', '=', 'a.buyer_order_item_id')
            ->join('factories as f', 'f.id', '=', 'a.factory_id')
            ->where('i.buyer_order_id', $orderId)
            ->groupBy('a.factory_id', 'f.name')
            ->selectRaw('
                f.name as factory_name,
                SUM(a.qty) as total_qty,
                SUM(a.qty * COALESCE(i.unit_price, 0)) as total_value
            ')
            ->orderBy('f.name')
            ->get();

        $summary = [
            'total_styles' => $totalStyles,
            'total_order_qty' => $totalOrderQty,
            'allocated_qty' => $allocatedQty,
            'remaining_qty' => $remainingQty,
            'order_value' => $orderValue,
        ];

        return [
            'summary' => $summary,
            'factoryRows' => $factoryRows,
        ];
    }
}