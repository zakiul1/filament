<?php

namespace App\Livewire\Admin\Master;

use App\Models\Bank;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms;
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

class BanksPage extends Component implements HasTable, HasForms, HasActions
{
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;

    public function table(Table $table): Table
    {
        return $table
            ->query(Bank::query())
            ->columns([
                TextColumn::make('name')
                    ->label('Bank Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('short_name')
                    ->label('Short Name')
                    ->badge()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('swift_code')
                    ->label('SWIFT')
                    ->badge()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('website')
                    ->label('Website')
                    ->url(fn($record) => $record->website, true)
                    ->openUrlInNewTab()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add Bank')
                    ->modalHeading('Create Bank')
                    ->form($this->getFormSchema()),
            ])
            ->actions([
                EditAction::make()
                    ->modalHeading(fn(Bank $record) => 'Edit Bank: ' . $record->name)
                    ->form($this->getFormSchema()),
                DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->defaultSort('name')
            ->striped()
            ->emptyStateHeading('No banks found')
            ->emptyStateDescription('Add your first bank to get started.');
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('name')
                ->label('Bank Name')
                ->required()
                ->maxLength(255),

            TextInput::make('short_name')
                ->label('Short Name')
                ->maxLength(50)
                ->placeholder('HSBC, SCB, CITY, etc.'),
            TextInput::make('swift_code')
                ->label('SWIFT Code')
                ->maxLength(11)
                ->placeholder('Optional')
                ->extraInputAttributes([
                    'style' => 'text-transform: uppercase;',
                    'onInput' => 'this.value = this.value.toUpperCase();',
                ])
                ->dehydrateStateUsing(fn($state) => strtoupper($state)),


            TextInput::make('website')
                ->label('Website')
                ->maxLength(255)
                ->url()
                ->placeholder('https://'),

            TextInput::make('phone')
                ->label('Phone')
                ->maxLength(50)
                ->placeholder('+880...'),

            Toggle::make('is_active')
                ->label('Active')
                ->default(true),
        ];
    }

    public function render(): View
    {
        return view('livewire.admin.master.banks-page');
    }
}