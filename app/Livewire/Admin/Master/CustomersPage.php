<?php

namespace App\Livewire\Admin\Master;

use App\Models\Country;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\Incoterm;
use App\Models\PaymentTerm;
use App\Models\Port;
use App\Models\ShipmentMode;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CustomersPage extends Component implements HasTable, HasForms, HasActions
{
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;

    public function table(Table $table): Table
    {
        return $table
            ->query(Customer::query()->with([
                'country',
                'defaultCurrency',
                'defaultIncoterm',
                'defaultPaymentTerm',
            ]))
            ->columns([
                TextColumn::make('name')
                    ->label('Customer Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('short_name')
                    ->label('Short')
                    ->badge()
                    ->sortable(),

                TextColumn::make('buyer_group')
                    ->label('Group')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('country.name')
                    ->label('Country')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('defaultCurrency.code')
                    ->label('Cur.')
                    ->badge(),

                TextColumn::make('defaultIncoterm.code')
                    ->label('Incoterm')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('defaultPaymentTerm.name')
                    ->label('Payment Term')
                    ->limit(20)
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->trueLabel('Active')
                    ->falseLabel('Inactive')
                    ->placeholder('All'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add Customer')
                    ->modalHeading('Create Customer')
                    ->form($this->getFormSchema()),
            ])
            ->actions([
                EditAction::make()
                    ->modalHeading(fn(Customer $record) => 'Edit: ' . $record->name)
                    ->form($this->getFormSchema()),

                DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->defaultSort('name')
            ->striped()
            ->emptyStateHeading('No customers found')
            ->emptyStateDescription('Add buyers / importers to use in PI, LC, CI, etc.');
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make(3)->schema([
                TextInput::make('name')
                    ->label('Customer Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('short_name')
                    ->label('Short Name')
                    ->maxLength(50)
                    ->placeholder('H&M, C&A, etc.'),

                TextInput::make('code')
                    ->label('Code')
                    ->maxLength(50)
                    ->placeholder('Optional internal code'),
            ]),

            Grid::make(2)->schema([
                TextInput::make('buyer_group')
                    ->label('Buyer Group')
                    ->maxLength(255)
                    ->placeholder('e.g. H&M Group, Inditex'),

                Select::make('country_id')
                    ->label('Country')
                    ->options(
                        Country::where('is_active', true)
                            ->orderBy('name')
                            ->pluck('name', 'id')
                    )
                    ->searchable()
                    ->preload(),
            ]),

            Grid::make(2)->schema([
                TextInput::make('address_line_1')
                    ->label('Address Line 1')
                    ->maxLength(255)
                    ->columnSpanFull(),

                TextInput::make('address_line_2')
                    ->label('Address Line 2')
                    ->maxLength(255)
                    ->columnSpanFull(),

                TextInput::make('city')
                    ->label('City')
                    ->maxLength(100),

                TextInput::make('state')
                    ->label('State / Province')
                    ->maxLength(100),

                TextInput::make('postal_code')
                    ->label('Postal Code')
                    ->maxLength(20),
            ]),

            Grid::make(3)->schema([
                TextInput::make('phone')
                    ->label('Phone')
                    ->maxLength(50),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->maxLength(255),

                TextInput::make('website')
                    ->label('Website')
                    ->url()
                    ->maxLength(255),
            ]),

            Grid::make(2)->schema([
                TextInput::make('contact_person_name')
                    ->label('Contact Person Name')
                    ->maxLength(255),

                TextInput::make('contact_person_designation')
                    ->label('Designation')
                    ->maxLength(255),

                TextInput::make('contact_person_phone')
                    ->label('Contact Phone')
                    ->maxLength(50),

                TextInput::make('contact_person_email')
                    ->label('Contact Email')
                    ->email()
                    ->maxLength(255),
            ]),

            Grid::make(3)->schema([
                TextInput::make('vat_reg_no')
                    ->label('VAT / TAX No.')
                    ->maxLength(100),

                TextInput::make('eori_no')
                    ->label('EORI No.')
                    ->maxLength(100),

                TextInput::make('registration_no')
                    ->label('Registration No.')
                    ->maxLength(100),
            ]),

            Grid::make(3)->schema([
                Select::make('default_currency_id')
                    ->label('Default Currency')
                    ->options(
                        Currency::where('is_active', true)
                            ->orderBy('code')
                            ->pluck('code', 'id')
                    )
                    ->searchable()
                    ->preload(),

                Select::make('default_incoterm_id')
                    ->label('Default Incoterm')
                    ->options(
                        Incoterm::where('is_active', true)
                            ->orderBy('code')
                            ->pluck('code', 'id')
                    )
                    ->searchable()
                    ->preload(),

                Select::make('default_payment_term_id')
                    ->label('Default Payment Term')
                    ->options(
                        PaymentTerm::where('is_active', true)
                            ->orderBy('name')
                            ->pluck('name', 'id')
                    )
                    ->searchable()
                    ->preload(),
            ]),

            Grid::make(2)->schema([
                Select::make('default_shipment_mode_id')
                    ->label('Default Shipment Mode')
                    ->options(
                        ShipmentMode::where('is_active', true)
                            ->orderBy('name')
                            ->pluck('name', 'id')
                    )
                    ->searchable()
                    ->preload(),

                Select::make('default_destination_port_id')
                    ->label('Default Destination Port')
                    ->options(
                        Port::where('is_active', true)
                            ->orderBy('name')
                            ->pluck('name', 'id')
                    )
                    ->searchable()
                    ->preload(),
            ]),

            Grid::make(2)->schema([
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]),

            Textarea::make('notes')
                ->label('Internal Notes')
                ->rows(3)
                ->columnSpanFull(),
        ];
    }

    public function render(): View
    {
        return view('livewire.admin.master.customers-page');
    }
}