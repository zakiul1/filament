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
        return $schema->schema([
            Section::make('Select Buyer Order')
                ->schema([
                    Select::make('buyer_order_id')
                        ->label('Buyer Order')
                        ->options(
                            BuyerOrder::query()
                                ->orderBy('order_number')
                                ->pluck('order_number', 'id')
                                ->toArray()
                        )
                        ->searchable()
                        ->preload()
                        ->required(),
                ])
                ->columns(1),
        ])->statePath('data');
    }

    public function render(): View
    {
        $buyerOrderId = $this->data['buyer_order_id'] ?? null;

        return view('livewire.admin.reports.buyer-order-factory-allocation-select', [
            'buyerOrderId' => $buyerOrderId,
        ]);
    }
}