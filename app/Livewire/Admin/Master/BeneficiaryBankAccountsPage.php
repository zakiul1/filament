<?php

namespace App\Livewire\Admin\Master;

use App\Models\BankBranch;
use App\Models\BeneficiaryBankAccount;
use App\Models\BeneficiaryCompany;
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

class BeneficiaryBankAccountsPage extends Component implements HasTable, HasForms, HasActions
{
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                BeneficiaryBankAccount::query()->with(['beneficiaryCompany', 'bankBranch.bank', 'currency'])
            )
            ->columns([
                TextColumn::make('beneficiaryCompany.short_name')
                    ->label('Beneficiary')
                    ->sortable()
                    ->searchable()
                    ->weight('medium'),

                TextColumn::make('bankBranch.bank.name')
                    ->label('Bank')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('bankBranch.name')
                    ->label('Branch')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('currency.code')
                    ->label('Currency')
                    ->badge(),

                TextColumn::make('account_title')
                    ->label('Account Title')
                    ->searchable(),

                TextColumn::make('account_number')
                    ->label('Account No.')
                    ->copyable()
                    ->copyMessage('Account number copied')
                    ->copyMessageDuration(1500),

                IconColumn::make('is_lc_account')
                    ->label('LC')
                    ->boolean(),

                IconColumn::make('is_tt_account')
                    ->label('TT')
                    ->boolean(),

                IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add Beneficiary Bank Account')
                    ->modalHeading('Create Beneficiary Bank Account')
                    ->form($this->getFormSchema()),
            ])
            ->actions([
                EditAction::make()
                    ->modalHeading(fn(BeneficiaryBankAccount $record) => 'Edit: ' . $record->account_title)
                    ->form($this->getFormSchema()),
                DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->defaultSort('beneficiaryCompany.short_name')
            ->striped()
            ->emptyStateHeading('No beneficiary bank accounts found')
            ->emptyStateDescription('Add bank accounts for each exporter (beneficiary company).');
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make(2)->schema([
                Select::make('beneficiary_company_id')
                    ->label('Beneficiary Company')
                    ->options(
                        BeneficiaryCompany::where('is_active', true)
                            ->orderBy('short_name')
                            ->pluck('short_name', 'id')
                    )
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('bank_branch_id')
                    ->label('Bank Branch')
                    ->options(
                        BankBranch::where('is_active', true)
                            ->with('bank')
                            ->get()
                            ->sortBy(fn($b) => $b->bank->name . ' - ' . $b->name)
                            ->mapWithKeys(fn($b) => [
                                $b->id => $b->bank->name . ' - ' . $b->name,
                            ])
                    )
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('currency_id')
                    ->label('Currency')
                    ->options(
                        Currency::where('is_active', true)
                            ->orderBy('code')
                            ->pluck('code', 'id')
                    )
                    ->searchable()
                    ->preload()
                    ->required(),
            ]),

            Grid::make(2)->schema([
                TextInput::make('account_title')
                    ->label('Account Title')
                    ->required()
                    ->maxLength(255),

                TextInput::make('account_number')
                    ->label('Account Number')
                    ->required()
                    ->maxLength(100),
            ]),

            Grid::make(3)->schema([
                TextInput::make('iban')
                    ->label('IBAN')
                    ->maxLength(34),

                TextInput::make('swift_code')
                    ->label('SWIFT (Override)')
                    ->maxLength(11)
                    ->placeholder('Optional if different from branch SWIFT')
                    ->extraInputAttributes([
                        'style' => 'text-transform: uppercase;',
                        'onInput' => 'this.value = this.value.toUpperCase();',
                    ])
                    ->dehydrateStateUsing(fn($state) => strtoupper($state)),


                TextInput::make('routing_number')
                    ->label('Routing / ABA')
                    ->maxLength(20),
            ]),

            Grid::make(4)->schema([
                Toggle::make('is_lc_account')
                    ->label('LC Account')
                    ->default(true),

                Toggle::make('is_tt_account')
                    ->label('TT Account')
                    ->default(true),

                Toggle::make('is_default')
                    ->label('Default for Beneficiary + Currency')
                    ->helperText('Use as default in LC / CI for this beneficiary & currency.'),

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
        return view('livewire.admin.master.beneficiary-bank-accounts-page');
    }
}