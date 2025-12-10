<?php

namespace App\Livewire\Admin\Trade;

use App\Models\BeneficiaryBankAccount;
use App\Models\BeneficiaryCompany;
use App\Models\Courier;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\CustomerBank;
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
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ProformaInvoiceCreate extends Component implements HasSchemas, HasActions
{
    use InteractsWithSchemas;
    use InteractsWithActions;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'pi_date' => now()->toDateString(),
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

    protected function getFormSchema(): array
    {
        return [
            Section::make('PI Header')
                ->columns(4)
                ->schema([
                    TextInput::make('pi_number')
                        ->label('PI Number')
                        ->required()
                        ->maxLength(50),

                    DatePicker::make('pi_date')
                        ->label('PI Date')
                        ->required()
                        ->native(false),

                    TextInput::make('revision_no')
                        ->label('Rev.')
                        ->numeric()
                        ->minValue(0),

                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'draft' => 'Draft',
                            'sent' => 'Sent to Buyer',
                            'accepted' => 'Accepted',
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

                    Select::make('customer_bank_id')
                        ->label('Customer Bank')
                        ->options(
                            CustomerBank::with('bankBranch.bank')
                                ->get()
                                ->mapWithKeys(fn($row) => [
                                    $row->id => ($row->bankBranch->bank->short_name ?? '') . ' - ' . $row->account_no,
                                ])
                                ->toArray()
                        )
                        ->searchable(),

                    Select::make('beneficiary_bank_account_id')
                        ->label('Beneficiary Bank A/C')
                        ->options(
                            BeneficiaryBankAccount::with('bankBranch.bank', 'beneficiaryCompany')
                                ->get()
                                ->mapWithKeys(fn($row) => [
                                    $row->id => ($row->beneficiaryCompany->short_name ?? '') .
                                        ' - ' .
                                        ($row->bankBranch->bank->short_name ?? '') .
                                        ' (' . $row->account_no . ')',
                                ])
                                ->toArray()
                        )
                        ->searchable(),

                    TextInput::make('buyer_reference')
                        ->label('Buyer Ref / PO')
                        ->maxLength(100),
                ]),

            Section::make('Terms & Shipment')
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

                    Select::make('payment_term_id')
                        ->label('Payment Term')
                        ->options(
                            PaymentTerm::orderBy('name')
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

                    TextInput::make('place_of_delivery')
                        ->label('Place of Delivery')
                        ->maxLength(255),

                    TextInput::make('shipment_lead_time_days')
                        ->label('Lead Time (days)')
                        ->numeric(),

                    DatePicker::make('shipment_date_from')
                        ->label('Shipment From')
                        ->native(false),

                    DatePicker::make('shipment_date_to')
                        ->label('Shipment To')
                        ->native(false),

                    DatePicker::make('validity_date')
                        ->label('PI Valid Up To')
                        ->native(false),

                    Select::make('courier_id')
                        ->label('Courier (for docs)')
                        ->options(
                            Courier::orderBy('name')
                                ->pluck('name', 'id')
                                ->toArray()
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

                            TextInput::make('unit')
                                ->label('UOM')
                                ->default('PCS'),

                            TextInput::make('order_qty')
                                ->label('Qty')
                                ->numeric()
                                ->reactive()
                                ->afterStateUpdated(
                                    fn($set, $get) =>
                                    $set(
                                        'amount',
                                        (float) ($get('order_qty') ?? 0) * (float) ($get('unit_price') ?? 0),
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
                                        (float) ($get('order_qty') ?? 0) * (float) ($get('unit_price') ?? 0),
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

        $pi = ProformaInvoice::create($data);

        foreach ($items as $index => $itemData) {
            $itemData['line_no'] = $itemData['line_no'] ?? ($index + 1);
            $itemData['proforma_invoice_id'] = $pi->id;

            ProformaInvoiceItem::create($itemData);
        }

        session()->flash('success', 'Proforma Invoice created successfully.');

        $this->redirectRoute('admin.trade.proforma-invoices.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.trade.proforma-invoice-create');
    }
}