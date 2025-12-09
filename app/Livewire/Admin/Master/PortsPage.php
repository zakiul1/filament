<?php

namespace App\Livewire\Admin\Master;

use App\Models\Country;
use App\Models\Port;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
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

class PortsPage extends Component implements HasTable, HasForms, HasActions
{
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;

    public function table(Table $table): Table
    {
        return $table
            ->query(Port::query()->with('country'))
            ->columns([
                TextColumn::make('name')
                    ->label('Port Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('code')
                    ->label('Code')
                    ->badge()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('country.name')
                    ->label('Country')
                    ->sortable()
                    ->searchable(),

                BadgeColumn::make('mode')
                    ->label('Mode')
                    ->formatStateUsing(fn(string $state) => ucfirst($state))
                    ->colors([
                        'primary' => 'sea',
                        'success' => 'air',
                        'warning' => 'courier',
                    ]),

                ToggleColumn::make('is_active')
                    ->label('Active'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add Port')
                    ->modalHeading('Create Port')
                    ->form($this->getFormSchema()),
            ])
            ->actions([
                EditAction::make()
                    ->modalHeading(fn(Port $record) => 'Edit Port: ' . $record->name)
                    ->form($this->getFormSchema()),

                DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->defaultSort('name')
            ->striped()
            ->emptyStateHeading('No ports found')
            ->emptyStateDescription('Add seaport, airport, or courier hubs to get started.');
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('name')
                ->label('Port Name')
                ->required()
                ->maxLength(255),

            TextInput::make('code')
                ->label('Port Code')
                ->maxLength(20)
                ->placeholder('Optional'),

            Select::make('country_id')
                ->label('Country')
                ->options(
                    Country::where('is_active', true)
                        ->orderBy('name')
                        ->pluck('name', 'id')
                )
                ->searchable()
                ->preload()
                ->required(),

            Select::make('mode')
                ->label('Mode')
                ->options([
                    'sea' => 'Sea',
                    'air' => 'Air',
                    'courier' => 'Courier',
                ])
                ->required()
                ->default('sea'),

            Toggle::make('is_active')
                ->label('Active')
                ->default(true),
        ];
    }

    public function render(): View
    {
        return view('livewire.admin.master.ports-page');
    }
}