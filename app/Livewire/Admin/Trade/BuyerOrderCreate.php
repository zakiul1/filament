<?php

namespace App\Livewire\Admin\Trade;

use App\Models\BeneficiaryCompany;
use App\Models\BuyerOrder;
use App\Models\Customer;
use App\Models\Factory;
use App\Models\FactorySubcategory;
use App\Models\LcReceive;
use App\Models\ProformaInvoice;
use App\Models\CommercialInvoice;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class BuyerOrderCreate extends Component implements HasSchemas, HasActions
{
    use InteractsWithSchemas;
    use InteractsWithActions;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'order_date' => now()->toDateString(),
            'status' => 'draft',
            'items' => [
                ['line_no' => 1, 'unit' => 'PCS'],
            ],
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components($this->schema())->statePath('data');
    }

    protected function schema(): array
    {
        return [
            Section::make('Order Header')
                ->columns(4)
                ->schema([
                    TextInput::make('order_number')->required()->maxLength(80),
                    DatePicker::make('order_date')->native(false),
                    TextInput::make('buyer_po_number')->label('Buyer PO')->maxLength(120),
                    Select::make('status')->options([
                        'draft' => 'Draft',
                        'running' => 'Running',
                        'shipped' => 'Shipped',
                        'closed' => 'Closed',
                        'cancelled' => 'Cancelled',
                    ])->default('draft'),
                ]),

            Section::make('Links (Optional)')
                ->columns(3)
                ->schema([
                    Select::make('proforma_invoice_id')
                        ->label('Linked PI')
                        ->options(ProformaInvoice::orderBy('pi_number')->pluck('pi_number', 'id')->toArray())
                        ->searchable(),

                    Select::make('lc_receive_id')
                        ->label('Linked LC')
                        ->options(LcReceive::orderBy('lc_number')->pluck('lc_number', 'id')->toArray())
                        ->searchable(),

                    Select::make('commercial_invoice_id')
                        ->label('Linked CI')
                        ->options(
                            CommercialInvoice::query()
                                ->whereNotNull('invoice_number')
                                ->orderBy('invoice_number')
                                ->pluck('invoice_number', 'id')
                                ->toArray()
                        )
                        ->searchable()
                        ->preload(),










                ]),

            Section::make('Parties & Planning')
                ->columns(4)
                ->schema([
                    Select::make('customer_id')
                        ->options(Customer::orderBy('name')->pluck('name', 'id')->toArray())
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('beneficiary_company_id')
                        ->label('Beneficiary Company')
                        ->options(BeneficiaryCompany::orderBy('short_name')->pluck('short_name', 'id')->toArray())
                        ->searchable()
                        ->preload(),

                    TextInput::make('season')->placeholder('SS26 / FW26')->maxLength(50),
                    TextInput::make('department')->maxLength(80),

                    TextInput::make('merchandiser_name')->maxLength(120),

                    DatePicker::make('shipment_date_from')->native(false),
                    DatePicker::make('shipment_date_to')->native(false),

                    TextInput::make('order_value')
                        ->numeric()
                        ->default(0),
                ]),

            Section::make('Order Items (Styles)')
                ->schema([
                    Repeater::make('items')
                        ->defaultItems(1)
                        ->columns(6)
                        ->schema([
                            TextInput::make('line_no')->numeric()->default(1)->columnSpan(1),

                            TextInput::make('style_ref')->maxLength(80)->columnSpan(2),

                            TextInput::make('item_description')->required()->columnSpan(3),

                            TextInput::make('color')->maxLength(60),
                            TextInput::make('size')->maxLength(60),

                            Select::make('factory_subcategory_id')
                                ->label('Category')
                                ->options(
                                    FactorySubcategory::orderBy('name')->pluck('name', 'id')->toArray()
                                )
                                ->searchable()
                                ->columnSpan(3),

                            Select::make('factory_id')
                                ->label('Default Factory')
                                ->options(Factory::orderBy('name')->pluck('name', 'id')->toArray())
                                ->searchable()
                                ->columnSpan(3),

                            TextInput::make('unit')->default('PCS')->maxLength(20),

                            TextInput::make('order_qty')
                                ->numeric()
                                ->reactive()
                                ->afterStateUpdated(fn($set, $get) => $set(
                                    'amount',
                                    (float) ($get('order_qty') ?? 0) * (float) ($get('unit_price') ?? 0)
                                )),

                            TextInput::make('unit_price')
                                ->numeric()
                                ->reactive()
                                ->afterStateUpdated(fn($set, $get) => $set(
                                    'amount',
                                    (float) ($get('order_qty') ?? 0) * (float) ($get('unit_price') ?? 0)
                                )),

                            TextInput::make('amount')->numeric()->readOnly(),
                            Textarea::make('remarks')->rows(2)->columnSpanFull(),
                        ])
                        ->columnSpanFull(),
                ]),

            Section::make('Notes')
                ->columns(2)
                ->schema([
                    Textarea::make('remarks')->rows(3),
                    Textarea::make('internal_notes')->rows(3),
                ]),
        ];
    }

    public function create(): void
    {
        $data = $this->form->getState();
        $items = $data['items'] ?? [];
        unset($data['items']);

        $total = 0;
        foreach ($items as $item) {
            $total += (float) ($item['amount'] ?? 0);
        }

        $data['order_value'] = (float) ($data['order_value'] ?? 0);
        if ($data['order_value'] <= 0) {
            $data['order_value'] = $total;
        }

        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        $order = BuyerOrder::create($data);

        foreach ($items as $idx => $item) {
            $item['line_no'] = $item['line_no'] ?? ($idx + 1);
            $item['buyer_order_id'] = $order->id;
            $order->items()->create($item);
        }

        session()->flash('success', 'Buyer Order created successfully.');
        $this->redirectRoute('admin.trade.buyer-orders.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.trade.buyer-order-create');
    }
}