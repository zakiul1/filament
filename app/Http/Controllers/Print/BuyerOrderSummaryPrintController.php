<?php

namespace App\Http\Controllers\Print;

use App\Models\BuyerOrder;
use Illuminate\Support\Facades\DB;

class BuyerOrderSummaryPrintController extends BaseDocumentPrintController
{
    /**
     * ✅ Keep compatibility with routes that call ->show()
     */
    public function show(BuyerOrder $buyerOrder)
    {
        // If your BaseDocumentPrintController has __invoke(), this will work.
        return $this->__invoke($buyerOrder);
    }

    protected function getView(): string
    {
        return 'pdf.buyer-order-summary';
    }

    protected function getFileName($record): string
    {
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

    /**
     * If your BaseDocumentPrintController supports paper options.
     * If not, you can ignore/remove this method.
     */
    protected function getPaper(): array
    {
        return ['A4', 'portrait'];
    }

    /**
     * ✅ Extra data for the Blade view (summary + factoryRows)
     * Your BaseDocumentPrintController MUST call this and merge into view data.
     */
    protected function getExtraViewData($record): array
    {
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