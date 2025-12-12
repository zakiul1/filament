<?php

namespace App\Livewire\Admin\Trade;

use App\Models\BuyerOrder;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class BuyerOrderSummaryPage extends Component
{
    public BuyerOrder $order;

    public array $summary = [];

    /** @var array<int, array<string, mixed>> */
    public array $factoryRows = [];

    public function mount(BuyerOrder $order): void
    {
        $this->order = $order->load([
            'customer',
            'beneficiaryCompany',
            'items.allocations.factory',
        ]);

        $this->buildSummary();
    }

    protected function buildSummary(): void
    {
        // Reset (important to avoid double-counting on re-render)
        $totalStyles = 0;
        $totalOrderQty = 0.0;
        $allocatedQty = 0.0;
        $itemsTotalValue = 0.0;

        // Factory aggregation keyed by factory name (or id if you prefer)
        $factories = [];

        $totalStyles = $this->order->items->count();

        foreach ($this->order->items as $item) {
            $itemQty = (float) ($item->order_qty ?? 0);
            $unitPrice = (float) ($item->unit_price ?? 0);
            $itemAmount = (float) ($item->amount ?? ($itemQty * $unitPrice));

            $totalOrderQty += $itemQty;
            $itemsTotalValue += $itemAmount;

            foreach ($item->allocations as $allocation) {
                $qty = (float) ($allocation->qty ?? 0);
                $allocatedQty += $qty;

                $factoryName = $allocation->factory?->name ?? 'Unknown';

                if (!isset($factories[$factoryName])) {
                    $factories[$factoryName] = [
                        'factory_name' => $factoryName,
                        'total_qty' => 0.0,
                        'total_value' => 0.0,
                    ];
                }

                $factories[$factoryName]['total_qty'] += $qty;
                $factories[$factoryName]['total_value'] += ($qty * $unitPrice);
            }
        }

        $remainingQty = (float) ($totalOrderQty - $allocatedQty);

        $orderValue = (float) ($this->order->order_value ?? 0);
        if ($orderValue <= 0) {
            $orderValue = $itemsTotalValue;
        }

        // Sort factory rows by name
        $this->factoryRows = array_values($factories);
        usort($this->factoryRows, fn($a, $b) => strcmp($a['factory_name'], $b['factory_name']));

        $this->summary = [
            'total_styles' => (int) $totalStyles,
            'total_order_qty' => (float) $totalOrderQty,
            'allocated_qty' => (float) $allocatedQty,
            'remaining_qty' => (float) $remainingQty,
            'order_value' => (float) $orderValue,
            'items_total_value' => (float) $itemsTotalValue,
        ];
    }

    public function render(): View
    {
        return view('livewire.admin.trade.buyer-order-summary', [
            'order' => $this->order,
            'summary' => $this->summary,
            'factoryRows' => $this->factoryRows,
        ]);
    }
}