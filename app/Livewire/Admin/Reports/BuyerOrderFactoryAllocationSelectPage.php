<?php

namespace App\Livewire\Admin\Reports;

use App\Models\BuyerOrder;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class BuyerOrderFactoryAllocationSelectPage extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public ?array $data = [
        'buyer_order_id' => null,
    ];

    public function mount(): void
    {
        $this->form->fill($this->data);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->schema([
                Section::make('Select Buyer Order')
                    ->schema([
                        Select::make('buyer_order_id')
                            ->label('Buyer Order')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->options(
                                BuyerOrder::query()
                                    ->orderByDesc('id') // consistent with other select pages
                                    ->pluck('order_number', 'id')
                                    ->toArray()
                            ),
                    ])
                    ->columns(1),
            ]);
    }

    /** ✅ Button should call this */
    public function printAllocation(): void
    {
        $state = $this->form->getState();

        $this->validate([
            'data.buyer_order_id' => ['required', 'integer', 'exists:buyer_orders,id'],
        ]);

        $this->redirectRoute(
            'admin.reports.buyer-orders.factory-allocation.print',
            ['buyerOrder' => $state['buyer_order_id']],
            navigate: true
        );
    }

    public function render(): View
    {
        return view('livewire.admin.reports.buyer-order-factory-allocation-select');
    }
}