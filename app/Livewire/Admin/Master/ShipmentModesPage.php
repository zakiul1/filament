<?php

namespace App\Livewire\Admin\Master;

use App\Models\ShipmentMode;
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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ShipmentModesPage extends Component implements HasTable, HasForms, HasActions
{
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;

    public function table(Table $table): Table
    {
        return $table
            ->query(ShipmentMode::query())
            ->columns([
                TextColumn::make('name')
                    ->label('Mode')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('code')
                    ->label('Code')
                    ->badge()
                    ->sortable()
                    ->searchable(),

                ToggleColumn::make('is_active')
                    ->label('Active'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add Shipment Mode')
                    ->modalHeading('Create Shipment Mode')
                    ->form($this->getFormSchema()),
            ])
            ->actions([
                EditAction::make()
                    ->modalHeading(fn(ShipmentMode $record) => 'Edit Shipment Mode: ' . $record->name)
                    ->form($this->getFormSchema()),

                DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->defaultSort('name')
            ->striped()
            ->emptyStateHeading('No shipment modes found')
            ->emptyStateDescription('Add Sea, Air, Courier, etc. to get started.');
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('name')
                ->label('Mode Name')
                ->required()
                ->maxLength(50)
                ->placeholder('Sea, Air, Courier'),

            TextInput::make('code')
                ->label('Code')
                ->maxLength(10)
                ->placeholder('SEA, AIR, COURIER'),

            Toggle::make('is_active')
                ->label('Active')
                ->default(true),
        ];
    }

    public function render(): View
    {
        return view('livewire.admin.master.shipment-modes-page');
    }
}