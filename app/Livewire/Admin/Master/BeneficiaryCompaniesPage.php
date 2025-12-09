<?php

namespace App\Livewire\Admin\Master;

use App\Models\BeneficiaryCompany;
use App\Models\Country;
use App\Models\Currency;
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

class BeneficiaryCompaniesPage extends Component implements HasTable, HasForms, HasActions
{
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                BeneficiaryCompany::query()->with(['country', 'defaultCurrency'])
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Company Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('short_name')
                    ->label('Short')
                    ->badge()
                    ->sortable(),

                TextColumn::make('country.name')
                    ->label('Country')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('defaultCurrency.code')
                    ->label('Currency')
                    ->badge(),

                IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add Beneficiary Company')
                    ->modalHeading('Create Beneficiary Company')
                    ->form($this->getFormSchema()),
            ])
            ->actions([
                EditAction::make()
                    ->modalHeading(fn(BeneficiaryCompany $record) => 'Edit: ' . $record->name)
                    ->form($this->getFormSchema()),

                DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->defaultSort('name')
            ->striped()
            ->emptyStateHeading('No beneficiary companies found')
            ->emptyStateDescription('Add Siatex, Aptex, Dotta Tex, etc. to get started.');
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make(2)->schema([
                TextInput::make('name')
                    ->label('Company Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('short_name')
                    ->label('Short Name')
                    ->maxLength(50)
                    ->placeholder('SIATEX, APTEX, DOTTA'),

                TextInput::make('trade_name')
                    ->label('Trade Name')
                    ->maxLength(255)
                    ->columnSpanFull(),

                TextInput::make('group_name')
                    ->label('Group Name')
                    ->maxLength(255)
                    ->columnSpanFull(),
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

            Grid::make(3)->schema([
                TextInput::make('phone')
                    ->label('Phone')
                    ->maxLength(50),

                TextInput::make('mobile')
                    ->label('Mobile')
                    ->maxLength(50),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->maxLength(255),

                TextInput::make('website')
                    ->label('Website')
                    ->url()
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]),

            // Garments / compliance refs
            Grid::make(3)->schema([
                TextInput::make('erc_no')
                    ->label('ERC No.')
                    ->maxLength(100),

                TextInput::make('irc_no')
                    ->label('IRC No.')
                    ->maxLength(100),

                TextInput::make('bin_no')
                    ->label('BIN No.')
                    ->maxLength(100),

                TextInput::make('vat_reg_no')
                    ->label('VAT Reg. No.')
                    ->maxLength(100),

                TextInput::make('tin_no')
                    ->label('TIN No.')
                    ->maxLength(100),

                TextInput::make('bond_license_no')
                    ->label('Bond License No.')
                    ->maxLength(100),
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
                Select::make('default_currency_id')
                    ->label('Default Currency')
                    ->options(
                        Currency::where('is_active', true)
                            ->orderBy('code')
                            ->pluck('code', 'id')
                    )
                    ->searchable()
                    ->preload(),

                Toggle::make('is_default')
                    ->label('Default Exporter')
                    ->helperText('Main beneficiary used most frequently (e.g. Siatex BD Ltd).'),

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
        return view('livewire.admin.master.beneficiary-companies-page');
    }
}