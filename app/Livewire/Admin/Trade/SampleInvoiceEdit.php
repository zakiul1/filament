<?php

namespace App\Livewire\Admin\Trade;

use App\Models\BeneficiaryCompany;
use App\Models\Courier;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\FactorySubcategory;
use App\Models\Incoterm;
use App\Models\Port;
use App\Models\SampleInvoice;
use App\Models\SampleInvoiceItem;
use App\Models\ShipmentMode;
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

class SampleInvoiceEdit extends Component implements HasSchemas, HasActions
{
    use InteractsWithSchemas;
    use InteractsWithActions;

    public SampleInvoice $record;

    public ?array $data = [];

    public function mount(SampleInvoice $record): void
    {
        $this->record = $record;

        $this->form->fill(array_merge(
            $record->toArray(),
            [
                'items' => $record->items()
                    ->orderBy('line_no')
                    ->get()
                    ->map(function (SampleInvoiceItem $item) {
                        return $item->only([
                            'line_no',
                            'style_ref',
                            'item_description',
                            'color',
                            'size',
                            'factory_subcategory_id',
                            'unit',
                            'quantity',
                            'unit_price',
                            'amount',
                            'sample_type',
                        ]);
                    })
                    ->toArray(),
            ]
        ));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components($this->getFormSchema())
            ->statePath('data');
    }

    protected function getFormSchema(): array
    {
        // Same schema as create
        return [
            Section::make('Sample Invoice Header')
                ->columns(4)
                ->schema([
                    TextInput::make('sample_number')
                        ->label('Sample Inv. Number')
                        ->required()
                        ->maxLength(50),

                    DatePicker::make('sample_date')
                        ->label('Date')
                        ->required()
                        ->native(false),

                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'draft' => 'Draft',
                            'sent' => 'Sent',
                            'approved' => 'Approved',
                            'cancelled' => 'Cancelled',
                        ])
                        ->default('draft'),
                ]),

            Section::make('Parties')
                ->columns(3)
                ->schema([
                    Select::make('customer_id')
                        ->label('Customer')
                        ->options(
                            Customer::orderBy('name')
                                ->pluck('name', 'id')
                                ->toArray()
                        )
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('beneficiary_company_id')
                        ->label('Beneficiary Company')
                        ->options(
                            BeneficiaryCompany::orderBy('short_name')
                                ->pluck('short_name', 'id')
                                ->toArray()
                        )
                        ->searchable()
                        ->preload(),

                    Select::make('currency_id')
                        ->label('Currency')
                        ->options(
                            Currency::where('is_active', true)
                                ->orderBy('code')
                                ->pluck('code', 'id')
                                ->toArray()
                        )
                        ->searchable()
                        ->preload()
                        ->required(),
                ]),

            Section::make('Shipment & Terms')
                ->columns(3)
                ->schema([
                    Select::make('incoterm_id')
                        ->label('Incoterm')
                        ->options(
                            Incoterm::orderBy('code')
                                ->pluck('code', 'id')
                                ->toArray()
                        )
                        ->searchable()
                        ->preload(),

                    Select::make('shipment_mode_id')
                        ->label('Shipment Mode')
                        ->options(
                            ShipmentMode::orderBy('name')
                                ->pluck('name', 'id')
                                ->toArray()
                        )
                        ->searchable()
                        ->preload(),

                    Select::make('port_of_loading_id')
                        ->label('Port of Loading')
                        ->options(
                            Port::orderBy('name')
                                ->pluck('name', 'id')
                                ->toArray()
                        )
                        ->searchable()
                        ->preload(),

                    Select::make('port_of_discharge_id')
                        ->label('Port of Discharge')
                        ->options(
                            Port::orderBy('name')
                                ->pluck('name', 'id')
                                ->toArray()
                        )
                        ->searchable()
                        ->preload(),

                    Select::make('courier_id')
                        ->label('Courier')
                        ->options(
                            Courier::orderBy('name')
                                ->pluck('name', 'id')
                                ->toArray()
                        )
                        ->searchable()
                        ->preload(),

                    TextInput::make('courier_tracking_no')
                        ->label('Courier Tracking No.')
                        ->maxLength(100),
                ]),

            Section::make('Sample Items')
                ->schema([
                    Repeater::make('items')
                        ->label('Items')
                        ->defaultItems(1)
                        ->columns(6)
                        ->schema([
                            TextInput::make('line_no')
                                ->label('Line')
                                ->numeric()
                                ->minValue(1)
                                ->default(1)
                                ->columnSpan(1),

                            TextInput::make('style_ref')
                                ->label('Style Ref')
                                ->maxLength(50)
                                ->columnSpan(2),

                            TextInput::make('item_description')
                                ->label('Description')
                                ->required()
                                ->columnSpan(3),

                            TextInput::make('color')
                                ->label('Color'),

                            TextInput::make('size')
                                ->label('Size'),

                            Select::make('factory_subcategory_id')
                                ->label('Product Category')
                                ->options(
                                    FactorySubcategory::where('is_active', true)
                                        ->orderBy('name')
                                        ->pluck('name', 'id')
                                        ->toArray()
                                )
                                ->searchable()
                                ->columnSpan(3),

                            TextInput::make('sample_type')
                                ->label('Sample Type')
                                ->placeholder('Fit / Size set / PP etc.'),

                            TextInput::make('unit')
                                ->label('UOM')
                                ->default('PCS'),

                            TextInput::make('quantity')
                                ->label('Qty')
                                ->numeric()
                                ->reactive()
                                ->afterStateUpdated(
                                    fn($set, $get) =>
                                    $set(
                                        'amount',
                                        (float) ($get('quantity') ?? 0) * (float) ($get('unit_price') ?? 0),
                                    )
                                ),

                            TextInput::make('unit_price')
                                ->label('Unit Price')
                                ->numeric()
                                ->reactive()
                                ->afterStateUpdated(
                                    fn($set, $get) =>
                                    $set(
                                        'amount',
                                        (float) ($get('quantity') ?? 0) * (float) ($get('unit_price') ?? 0),
                                    )
                                ),

                            TextInput::make('amount')
                                ->label('Amount')
                                ->numeric()
                                ->readOnly(),
                        ])
                        ->columnSpanFull(),
                ]),

            Section::make('Totals & Notes')
                ->columns(3)
                ->schema([
                    TextInput::make('discount_amount')
                        ->label('Discount')
                        ->numeric()
                        ->default(0),

                    TextInput::make('other_charges')
                        ->label('Other Charges')
                        ->numeric()
                        ->default(0),

                    TextInput::make('total_amount_in_words')
                        ->label('Amount in Words')
                        ->columnSpanFull(),

                    Textarea::make('remarks')
                        ->label('Remarks')
                        ->rows(3)
                        ->columnSpanFull(),

                    Textarea::make('internal_notes')
                        ->label('Internal Notes')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
        ];
    }

    public function update(): void
    {
        $data = $this->form->getState();
        $items = $data['items'] ?? [];
        unset($data['items']);

        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += (float) ($item['amount'] ?? 0);
        }

        $data['subtotal'] = $subtotal;
        $data['discount_amount'] = (float) ($data['discount_amount'] ?? 0);
        $data['other_charges'] = (float) ($data['other_charges'] ?? 0);
        $data['total_amount'] = $subtotal - $data['discount_amount'] + $data['other_charges'];

        $data['updated_by'] = auth()->id();

        $this->record->update($data);

        // reset items: easiest way
        $this->record->items()->delete();

        foreach ($items as $index => $itemData) {
            $itemData['line_no'] = $itemData['line_no'] ?? ($index + 1);
            $itemData['sample_invoice_id'] = $this->record->id;

            SampleInvoiceItem::create($itemData);
        }

        session()->flash('success', 'Sample Invoice updated successfully.');

        $this->redirectRoute('admin.trade.sample-invoices.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.trade.sample-invoice-edit');
    }
}