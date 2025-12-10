<?php

// app/Livewire/Admin/Trade/CommercialInvoiceCreate.php

namespace App\Livewire\Admin\Trade;

use App\Models\BeneficiaryCompany;
use App\Models\CommercialInvoice;
use App\Models\CommercialInvoiceItem;
use App\Models\Courier;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\FactorySubcategory;
use App\Models\Incoterm;
use App\Models\PaymentTerm;
use App\Models\Port;
use App\Models\ProformaInvoice;
use App\Models\ProformaInvoiceItem;
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

class CommercialInvoiceCreate extends Component implements HasSchemas, HasActions
{
    use InteractsWithSchemas;
    use InteractsWithActions;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'invoice_date' => now()->toDateString(),
            'status' => 'draft',
            'items' => [
                ['line_no' => 1, 'unit' => 'PCS'],
            ],
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components($this->getFormSchema())
            ->statePath('data');
    }

    public function getFormSchema(): array
    {
        return [
            Section::make('Invoice Header')
                ->columns(4)
                ->schema([
                    TextInput::make('invoice_number')
                        ->label('Invoice Number')
                        ->required()
                        ->maxLength(100),

                    DatePicker::make('invoice_date')
                        ->label('Invoice Date')
                        ->required()
                        ->native(false),

                    Select::make('proforma_invoice_id')
                        ->label('Proforma Invoice')
                        ->options(
                            ProformaInvoice::orderBy('pi_number')
                                ->pluck('pi_number', 'id')->toArray()
                        )
                        ->searchable()
                        ->preload()
                        ->columnSpan(2),

                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'draft' => 'Draft',
                            'confirmed' => 'Confirmed',
                            'submitted' => 'Submitted',
                            'paid' => 'Paid',
                            'cancelled' => 'Cancelled',
                        ])
                        ->default('draft'),
                ]),

            Section::make('Parties & Banks')
                ->columns(3)
                ->schema([
                    Select::make('customer_id')
                        ->label('Customer')
                        ->options(
                            Customer::orderBy('name')
                                ->pluck('name', 'id')->toArray()
                        )
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('beneficiary_company_id')
                        ->label('Beneficiary Company')
                        ->options(
                            BeneficiaryCompany::orderBy('short_name')
                                ->pluck('short_name', 'id')->toArray()
                        )
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('currency_id')
                        ->label('Currency')
                        ->options(
                            Currency::where('is_active', true)
                                ->orderBy('code')
                                ->pluck('code', 'id')->toArray()
                        )
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('lc_receive_id')
                        ->label('Linked LC (optional)')
                        ->options(
                            \App\Models\LcReceive::orderBy('lc_number')
                                ->pluck('lc_number', 'id')->toArray()
                        )
                        ->searchable()
                        ->preload()
                        ->columnSpan(3),
                ]),

            Section::make('Terms & Shipment')
                ->columns(3)
                ->schema([
                    Select::make('incoterm_id')
                        ->label('Incoterm')
                        ->options(
                            Incoterm::orderBy('code')
                                ->pluck('code', 'id')->toArray()
                        )
                        ->searchable()
                        ->preload(),

                    Select::make('shipment_mode_id')
                        ->label('Shipment Mode')
                        ->options(
                            ShipmentMode::orderBy('name')
                                ->pluck('name', 'id')->toArray()
                        )
                        ->searchable()
                        ->preload(),

                    Select::make('payment_term_id')
                        ->label('Payment Term')
                        ->options(
                            PaymentTerm::orderBy('name')
                                ->pluck('name', 'id')->toArray()
                        )
                        ->searchable()
                        ->preload(),

                    Select::make('port_of_loading_id')
                        ->label('Port of Loading')
                        ->options(
                            Port::orderBy('name')
                                ->pluck('name', 'id')->toArray()
                        )
                        ->searchable()
                        ->preload(),

                    Select::make('port_of_discharge_id')
                        ->label('Port of Discharge')
                        ->options(
                            Port::orderBy('name')
                                ->pluck('name', 'id')->toArray()
                        )
                        ->searchable()
                        ->preload(),

                    TextInput::make('place_of_delivery')
                        ->label('Place of Delivery')
                        ->maxLength(255),

                    Select::make('courier_id')
                        ->label('Courier (docs)')
                        ->options(
                            Courier::orderBy('name')
                                ->pluck('name', 'id')->toArray()
                        )
                        ->searchable()
                        ->preload(),
                ]),

            Section::make('Line Items')
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
                                ->default(1),

                            TextInput::make('style_ref')
                                ->label('Style Ref')
                                ->maxLength(100)
                                ->columnSpan(2),

                            TextInput::make('item_description')
                                ->label('Description')
                                ->required()
                                ->columnSpan(3),

                            TextInput::make('hs_code')
                                ->label('HS Code')
                                ->maxLength(50),

                            Select::make('factory_subcategory_id')
                                ->label('Product Category')
                                ->options(
                                    FactorySubcategory::where('is_active', true)
                                        ->orderBy('name')
                                        ->pluck('name', 'id')->toArray()
                                )
                                ->searchable()
                                ->columnSpan(2),

                            TextInput::make('color')
                                ->label('Color'),

                            TextInput::make('size')
                                ->label('Size'),

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

                            TextInput::make('carton_count')
                                ->label('Cartons')
                                ->numeric(),

                            TextInput::make('net_weight')
                                ->label('Net Wt (kg)')
                                ->numeric(),

                            TextInput::make('gross_weight')
                                ->label('Gross Wt (kg)')
                                ->numeric(),

                            TextInput::make('cbm')
                                ->label('CBM')
                                ->numeric(),
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
                        ->label('Buyer Remarks')
                        ->rows(3)
                        ->columnSpanFull(),

                    Textarea::make('internal_notes')
                        ->label('Internal Notes')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
        ];
    }

    public function create(): void
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

        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        $ci = CommercialInvoice::create($data);

        foreach ($items as $index => $itemData) {
            $itemData['line_no'] = $itemData['line_no'] ?? ($index + 1);
            $itemData['commercial_invoice_id'] = $ci->id;

            CommercialInvoiceItem::create($itemData);
        }

        session()->flash('success', 'Commercial Invoice created successfully.');

        $this->redirectRoute('admin.trade.commercial-invoices.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.trade.commercial-invoice-create');
    }
}