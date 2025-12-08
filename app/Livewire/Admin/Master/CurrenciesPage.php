<?php

namespace App\Livewire\Admin\Master;

use App\Models\Currency;
use Filament\Actions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CurrenciesPage extends Component implements HasTable, HasForms, HasActions
{
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;

    public function table(Table $table): Table
    {
        return $table
            ->query(Currency::query())
            ->columns([
                TextColumn::make('name')
                    ->label('Currency')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('code')
                    ->label('Code')
                    ->badge()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('symbol')
                    ->label('Symbol')
                    ->alignCenter(),

                IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean(),

                ToggleColumn::make('is_active')
                    ->label('Active'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Currency')
                    ->modalHeading('Create Currency')
                    ->form($this->getFormSchema()),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading(fn (Currency $record) => 'Edit Currency: ' . $record->code)
                    ->form($this->getFormSchema()),

                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->defaultSort('code')
            ->emptyStateHeading('No currencies found')
            ->emptyStateDescription('Add at least one currency to get started.')
            ->striped();
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('name')
                ->label('Currency Name')
                ->required()
                ->maxLength(255),

            TextInput::make('code')
                ->label('Code')
                ->required()
                ->maxLength(3)
                ->uppercase()
                ->placeholder('USD, EUR, BDT'),

            TextInput::make('symbol')
                ->label('Symbol')
                ->maxLength(8)
                ->placeholder('$, €, ৳'),

            Toggle::make('is_default')
                ->label('Default Currency')
                ->helperText('Only one currency will be used as system default.'),

            Toggle::make('is_active')
                ->label('Active')
                ->default(true),
        ];
    }

    public function render(): View
    {
        return view('livewire.admin.master.currencies-page');
    }
}
