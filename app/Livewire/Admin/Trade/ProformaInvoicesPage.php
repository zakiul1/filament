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
use App\Models\ShipmentMode;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ProformaInvoicesPage extends Component implements HasTable, HasForms, HasActions
{
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;

    protected ?string $model = ProformaInvoice::class;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ProformaInvoice::query()->with(['customer', 'currency'])
            )
            ->columns([
                TextColumn::make('pi_number')
                    ->label('PI No.')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('pi_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('currency.code')
                    ->label('Cur.'),

                TextColumn::make('total_amount')
                    ->label('Total')
                    ->numeric(2)
                    ->sortable(),

                BadgeColumn::make('status')
                    ->colors([
                        'gray' => 'draft',
                        'info' => 'sent',
                        'success' => 'accepted',
                        'danger' => 'cancelled',
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('customer_id')
                    ->label('Customer')
                    ->options(
                        Customer::orderBy('name')->pluck('name', 'id')
                    ),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'sent' => 'Sent',
                        'accepted' => 'Accepted',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('New PI')
                    ->modalHeading('Create Proforma Invoice')
                    ->form($this->getFormSchema()),
            ])
            ->actions([
                EditAction::make()
                    ->modalHeading(fn(ProformaInvoice $record) => 'Edit PI: ' . $record->pi_number)
                    ->form($this->getFormSchema()),

                DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->defaultSort('pi_date', 'desc')
            ->striped()
            ->emptyStateHeading('No Proforma Invoices yet')
            ->emptyStateDescription('Create your first PI to start the commercial workflow.');
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
                        ->maxLength(50)
                        ->helperText('We can automate numbering later (e.g. PI-2025-0001).'),

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
                            Customer::orderBy('name')->pluck('name', 'id')
                        )
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('beneficiary_company_id')
                        ->label('Beneficiary Company')
                        ->options(
                            BeneficiaryCompany::orderBy('short_name')
                                ->pluck('short_name', 'id')  // ⬅️ use existing column
                        )
                        ->searchable()
                        ->preload(),


                    Select::make('currency_id')
                        ->label('Currency')
                        ->options(
                            Currency::where('is_active', true)->orderBy('code')->pluck('code', 'id')
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
                                    $row->id => $row->display_label ?? ($row->bankBranch->bank->short_name ?? '') . ' - ' . $row->account_no,
                                ])
                        )
                        ->searchable(),

                    Select::make('beneficiary_bank_account_id')
                        ->label('Beneficiary Bank A/C')
                        ->options(
                            BeneficiaryBankAccount::with('bankBranch.bank', 'beneficiaryCompany')
                                ->get()
                                ->mapWithKeys(fn($row) => [
                                    $row->id => ($row->beneficiaryCompany->short_name ?? '') . ' - ' . ($row->bankBranch->bank->short_name ?? '') . ' (' . $row->account_no . ')',
                                ])
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
                            Incoterm::orderBy('code')->pluck('code', 'id')
                        )
                        ->searchable()
                        ->preload(),

                    Select::make('shipment_mode_id')
                        ->label('Shipment Mode')
                        ->options(
                            ShipmentMode::orderBy('name')->pluck('name', 'id')
                        )
                        ->searchable()
                        ->preload(),

                    Select::make('payment_term_id')
                        ->label('Payment Term')
                        ->options(
                            PaymentTerm::orderBy('name')->pluck('name', 'id')
                        )
                        ->searchable()
                        ->preload(),

                    Select::make('port_of_loading_id')
                        ->label('Port of Loading')
                        ->options(
                            Port::orderBy('name')->pluck('name', 'id')
                        )
                        ->searchable()
                        ->preload(),

                    Select::make('port_of_discharge_id')
                        ->label('Port of Discharge')
                        ->options(
                            Port::orderBy('name')->pluck('name', 'id')
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
                            Courier::orderBy('name')->pluck('name', 'id')
                        )
                        ->searchable()
                        ->preload(),
                ]),

            Section::make('Line Items')
                ->schema([
                    Repeater::make('items')
                        ->relationship('items')
                        ->label('Items')
                        ->orderable('line_no')
                        ->defaultItems(1)
                        ->columns(6)
                        ->columnSpanFull()
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
                                ->label('Color')
                                ->maxLength(50),

                            TextInput::make('size')
                                ->label('Size')
                                ->maxLength(50),

                            Select::make('factory_subcategory_id')
                                ->label('Product Category')
                                ->options(
                                    FactorySubcategory::where('is_active', true)
                                        ->orderBy('name')
                                        ->pluck('name', 'id')
                                )
                                ->searchable()
                                ->columnSpan(3),

                            TextInput::make('unit')
                                ->label('UOM')
                                ->default('PCS')
                                ->maxLength(10),

                            TextInput::make('order_qty')
                                ->label('Qty')
                                ->numeric()
                                ->reactive(),

                            TextInput::make('unit_price')
                                ->label('Unit Price')
                                ->numeric()
                                ->reactive(),

                            TextInput::make('amount')
                                ->label('Amount')
                                ->numeric()
                                ->dehydrated(true)
                                ->hint('Auto = qty × price')
                                ->afterStateHydrated(function ($component, $state, $record) {
                                    // keep value on edit; simple safeguard
                                })
                                ->afterStateUpdated(function (callable $set, $state, callable $get) {
                                    // manual override allowed; main auto-logic below
                                })
                                ->reactive(),
                        ])
                        ->afterStateUpdated(function (callable $set, callable $get) {
                            // simple recompute total
                            $items = $get('items') ?? [];
                            $subtotal = 0;
                            foreach ($items as $key => $item) {
                                $qty = (float) ($item['order_qty'] ?? 0);
                                $price = (float) ($item['unit_price'] ?? 0);
                                $amount = round($qty * $price, 2);

                                $items[$key]['amount'] = $amount;
                                $subtotal += $amount;
                            }

                            $set('items', $items);
                            $set('subtotal', $subtotal);
                            $set('total_amount', $subtotal - (float) $get('discount_amount') + (float) $get('other_charges'));
                        }),
                ]),

            Section::make('Totals & Notes')
                ->columns(3)
                ->schema([
                    TextInput::make('subtotal')
                        ->label('Subtotal')
                        ->numeric()
                        ->readOnly(),

                    TextInput::make('discount_amount')
                        ->label('Discount')
                        ->numeric()
                        ->default(0)
                        ->reactive()
                        ->afterStateUpdated(function (callable $set, callable $get) {
                            $set(
                                'total_amount',
                                (float) $get('subtotal')
                                - (float) $get('discount_amount')
                                + (float) $get('other_charges')
                            );
                        }),

                    TextInput::make('other_charges')
                        ->label('Other Charges')
                        ->numeric()
                        ->default(0)
                        ->reactive()
                        ->afterStateUpdated(function (callable $set, callable $get) {
                            $set(
                                'total_amount',
                                (float) $get('subtotal')
                                - (float) $get('discount_amount')
                                + (float) $get('other_charges')
                            );
                        }),

                    TextInput::make('total_amount')
                        ->label('Net Total')
                        ->numeric()
                        ->readOnly(),

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

    public function render(): View
    {
        return view('livewire.admin.trade.proforma-invoices-page');
    }
}