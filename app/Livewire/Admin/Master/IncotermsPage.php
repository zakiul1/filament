<?php

namespace App\Livewire\Admin\Master;

use App\Models\Incoterm;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class IncotermsPage extends Component implements HasTable, HasForms, HasActions
{
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;

    public function table(Table $table): Table
    {
        return $table
            ->query(Incoterm::query())
            ->columns([
                TextColumn::make('code')
                    ->label('Code')
                    ->badge()
                    ->sortable()
                    ->searchable()
                    ->weight('medium'),

                TextColumn::make('name')
                    ->label('Name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('version')
                    ->label('Version')
                    ->badge()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                ToggleColumn::make('is_active')
                    ->label('Active'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add Incoterm')
                    ->modalHeading('Create Incoterm')
                    ->form($this->getFormSchema()),
            ])
            ->actions([
                EditAction::make()
                    ->modalHeading(fn(Incoterm $record) => 'Edit Incoterm: ' . $record->code)
                    ->form($this->getFormSchema()),

                DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->defaultSort('code')
            ->striped()
            ->emptyStateHeading('No Incoterms found')
            ->emptyStateDescription('Add FOB, CIF, CFR, etc. to get started.');
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('code')
                ->label('Code')
                ->required()
                ->maxLength(10)
                ->placeholder('FOB, CIF, CFR')
                ->extraInputAttributes([
                    'style' => 'text-transform: uppercase;',
                    'onInput' => 'this.value = this.value.toUpperCase();',
                ])
                ->dehydrateStateUsing(fn($state) => strtoupper($state)),


            TextInput::make('name')
                ->label('Name')
                ->maxLength(255)
                ->placeholder('Free On Board, Cost Insurance Freight, etc.'),

            TextInput::make('version')
                ->label('Version')
                ->maxLength(10)
                ->placeholder('2020'),

            Textarea::make('description')
                ->label('Description')
                ->rows(3)
                ->placeholder('Optional notes or explanation.'),

            Toggle::make('is_active')
                ->label('Active')
                ->default(true),
        ];
    }

    public function render(): View
    {
        return view('livewire.admin.master.incoterms-page');
    }
}