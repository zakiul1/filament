<?php

namespace App\Livewire\Admin\Reports;

use App\Models\BuyerOrder;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class BuyerOrderSummaryPage extends Component implements HasSchemas, HasActions
{
    use InteractsWithSchemas;
    use InteractsWithActions;

    public ?BuyerOrder $buyerOrder = null;

    public ?array $data = [
        'buyer_order_id' => null,
    ];

    public array $summary = [];

    /** @var array<int, array<string, mixed>> */
    public array $factoryRows = [];

    public function mount(?BuyerOrder $buyerOrder = null): void
    {
        if ($buyerOrder?->exists) {
            $this->buyerOrder = $buyerOrder;
            $this->form->fill(['buyer_order_id' => $buyerOrder->id]);
            $this->loadAndBuild();
            return;
        }

        // Default state (no buyer order selected yet)
        $this->form->fill(['buyer_order_id' => null]);
        $this->summary = [];
        $this->factoryRows = [];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Select Buyer Order')
                    ->columns(1)
                    ->schema([
                        Select::make('buyer_order_id')
                            ->label('Buyer Order')
                            ->required()
                            ->options(
                                BuyerOrder::query()
                                    ->whereNotNull('order_number')
                                    ->orderBy('order_number')
                                    ->pluck('order_number', 'id')
                                    ->toArray()
                            )
                            ->searchable()
                            ->preload(),
                    ]),
            ]);
    }

    public function viewSummary(): void
    {
        $data = $this->form->getState();

        $this->validate([
            'data.buyer_order_id' => ['required', 'integer', 'exists:buyer_orders,id'],
        ]);

        $this->buyerOrder = BuyerOrder::query()->findOrFail((int) $data['buyer_order_id']);
        $this->loadAndBuild();
    }

    protected function loadAndBuild(): void
    {
        if (!$this->buyerOrder?->exists) {
            $this->summary = [];
            $this->factoryRows = [];
            return;
        }

        $orderId = $this->buyerOrder->id;

        // Load header relations for display
        $this->buyerOrder->load([
            'customer',
            'beneficiaryCompany',
        ]);

        // Totals from DB (fast + avoids big array issues)
        $totalStyles = (int) DB::table('buyer_order_items')
            ->where('buyer_order_id', $orderId)
            ->count();

        $totalOrderQty = (float) DB::table('buyer_order_items')
            ->where('buyer_order_id', $orderId)
            ->sum('order_qty');

        $itemsTotalValue = (float) DB::table('buyer_order_items')
            ->where('buyer_order_id', $orderId)
            ->sum('amount');

        $allocatedQty = (float) DB::table('buyer_order_item_allocations as a')
            ->join('buyer_order_items as i', 'i.id', '=', 'a.buyer_order_item_id')
            ->where('i.buyer_order_id', $orderId)
            ->sum('a.qty');

        $remainingQty = (float) ($totalOrderQty - $allocatedQty);

        $orderValue = (float) ($this->buyerOrder->order_value ?? 0);
        if ($orderValue <= 0) {
            $orderValue = $itemsTotalValue;
        }

        // Factory rows
        $rows = DB::table('buyer_order_item_allocations as a')
            ->join('buyer_order_items as i', 'i.id', '=', 'a.buyer_order_item_id')
            ->join('factories as f', 'f.id', '=', 'a.factory_id')
            ->where('i.buyer_order_id', $orderId)
            ->groupBy('a.factory_id', 'f.name')
            ->selectRaw('
                a.factory_id,
                f.name as factory_name,
                SUM(a.qty) as total_qty,
                SUM(a.qty * COALESCE(i.unit_price, 0)) as total_value
            ')
            ->orderBy('f.name')
            ->get()
            ->map(fn($r) => [
                'factory_id' => (int) $r->factory_id,
                'factory_name' => (string) $r->factory_name,
                'total_qty' => (float) $r->total_qty,
                'total_value' => (float) $r->total_value,
            ])
            ->toArray();

        $this->factoryRows = $rows;

        $this->summary = [
            'total_styles' => $totalStyles,
            'total_order_qty' => $totalOrderQty,
            'allocated_qty' => $allocatedQty,
            'remaining_qty' => $remainingQty,
            'order_value' => $orderValue,
            'items_total_value' => $itemsTotalValue,
        ];
    }

    public function render(): View
    {
        return view('livewire.admin.reports.buyer-order-summary-index', [
            'record' => $this->buyerOrder,
            'summary' => $this->summary,
            'factoryRows' => $this->factoryRows,
        ]);
    }
}