<?php

namespace App\Livewire\Admin\Master;

use App\Models\PaymentTerm;
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

class PaymentTermsPage extends Component implements HasTable, HasForms, HasActions
{
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;

    public function table(Table $table): Table
    {
        return $table
            ->query(PaymentTerm::query())
            ->columns([
                TextColumn::make('name')
                    ->label('Payment Term')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('code')
                    ->label('Code')
                    ->badge()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('days_due')
                    ->label('Days')
                    ->sortable()
                    ->alignRight()
                    ->formatStateUsing(fn($state) => $state ? $state . ' days' : '—'),

                ToggleColumn::make('is_active')
                    ->label('Active'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add Payment Term')
                    ->modalHeading('Create Payment Term')
                    ->form($this->getFormSchema()),
            ])
            ->actions([
                EditAction::make()
                    ->modalHeading(fn(PaymentTerm $record) => 'Edit Payment Term: ' . $record->name)
                    ->form($this->getFormSchema()),

                DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->defaultSort('name')
            ->striped()
            ->emptyStateHeading('No payment terms found')
            ->emptyStateDescription('Add LC, TT, DP terms to get started.');
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('name')
                ->label('Payment Term')
                ->required()
                ->maxLength(255)
                ->placeholder('LC at Sight, LC 30 Days, TT Advance 100%, etc.'),

            TextInput::make('code')
                ->label('Code')
                ->maxLength(50)
                ->placeholder('LC_SIGHT, LC_30D, TT_ADV'),

            TextInput::make('days_due')
                ->label('Days Due')
                ->numeric()
                ->minValue(0)
                ->maxValue(365)
                ->placeholder('e.g. 0, 30, 60'),

            Textarea::make('description')
                ->label('Description')
                ->rows(3)
                ->placeholder('Optional notes, e.g. payment condition.'),

            Toggle::make('is_active')
                ->label('Active')
                ->default(true),
        ];
    }

    public function render(): View
    {
        return view('livewire.admin.master.payment-terms-page');
    }
}