<?php

namespace App\Livewire\Admin\Master;

use App\Models\Bank;
use App\Models\BankBranch;
use App\Models\Country;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms;
use Filament\Forms\Components\Select;
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
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class BankBranchesPage extends Component implements HasTable, HasForms, HasActions
{
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;

    public function table(Table $table): Table
    {
        return $table
            ->query(BankBranch::query()->with(['bank', 'country']))
            ->columns([
                TextColumn::make('bank.name')
                    ->label('Bank')
                    ->sortable()
                    ->searchable()
                    ->weight('medium'),

                TextColumn::make('name')
                    ->label('Branch Name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('city')
                    ->label('City')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('country.name')
                    ->label('Country')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('swift_code')
                    ->label('SWIFT')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add Branch')
                    ->modalHeading('Create Bank Branch')
                    ->form($this->getFormSchema()),
            ])
            ->actions([
                EditAction::make()
                    ->modalHeading(fn(BankBranch $record) => 'Edit Branch: ' . $record->name)
                    ->form($this->getFormSchema()),

                DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->defaultSort('bank.name')
            ->striped()
            ->emptyStateHeading('No bank branches found')
            ->emptyStateDescription('Add your first branch to get started.');
    }

    protected function getFormSchema(): array
    {
        return [
            Select::make('bank_id')
                ->label('Bank')
                ->options(
                    Bank::where('is_active', true)
                        ->orderBy('name')
                        ->pluck('name', 'id')
                )
                ->searchable()
                ->preload()
                ->required(),

            TextInput::make('name')
                ->label('Branch Name')
                ->required()
                ->maxLength(255),

            TextInput::make('branch_code')
                ->label('Branch Code')
                ->maxLength(20)
                ->placeholder('Optional'),

            TextInput::make('swift_code')
                ->label('SWIFT Code')
                ->maxLength(11)
                ->placeholder('Optional')
                ->extraInputAttributes([
                    'style' => 'text-transform: uppercase;',
                    'onInput' => 'this.value = this.value.toUpperCase();',
                ])
                ->dehydrateStateUsing(fn($state) => strtoupper($state)),


            TextInput::make('city')
                ->label('City')
                ->maxLength(100),

            Select::make('country_id')
                ->label('Country')
                ->options(
                    Country::where('is_active', true)
                        ->orderBy('name')
                        ->pluck('name', 'id')
                )
                ->searchable()
                ->preload(),

            TextInput::make('address')
                ->label('Address')
                ->maxLength(255),

            Toggle::make('is_active')
                ->label('Active')
                ->default(true),
        ];
    }

    public function render(): View
    {
        return view('livewire.admin.master.bank-branches-page');
    }
}