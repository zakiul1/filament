<?php

namespace App\Livewire\Admin\Master;

use App\Models\Country;
use Filament\Actions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Actions\CreateAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CountriesPage extends Component implements HasTable, HasForms, HasActions
{
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;

    public function table(Table $table): Table
    {
        return $table
            ->query(Country::query())
            ->columns([
                TextColumn::make('name')
                    ->label('Country')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('code')
                    ->label('Code')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('gray'),

                ToggleColumn::make('is_active')
                    ->label('Active')
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Country')
                    ->modalHeading('Create Country')
                    ->form($this->getFormSchema()),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading(fn (Country $record) => 'Edit Country: ' . $record->name)
                    ->form($this->getFormSchema()),

                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->defaultSort('name')
            ->emptyStateHeading('No countries found')
            ->emptyStateDescription('Add your first country to get started.')
            ->striped();
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('name')
                ->label('Country Name')
                ->required()
                ->maxLength(255),

            TextInput::make('code')
                ->label('ISO Code (3 letters)')
                ->maxLength(3)
                ->placeholder('e.g. BGD, USA, GBR')
                ->alpha()
                ->uppercase(),

            Toggle::make('is_active')
                ->label('Active')
                ->default(true),
        ];
    }

    public function render(): View
    {
        return view('livewire.admin.master.countries-page');
    }
}
