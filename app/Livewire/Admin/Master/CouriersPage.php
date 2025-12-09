<?php

namespace App\Livewire\Admin\Master;

use App\Models\Country;
use App\Models\Courier;
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
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CouriersPage extends Component implements HasTable, HasForms, HasActions
{
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;

    public function table(Table $table): Table
    {
        return $table
            ->query(Courier::query()->with('country'))
            ->columns([
                TextColumn::make('name')
                    ->label('Courier Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('short_name')
                    ->label('Short')
                    ->badge()
                    ->sortable(),

                BadgeColumn::make('service_type')
                    ->label('Service')
                    ->formatStateUsing(fn(string $state) => ucfirst($state))
                    ->colors([
                        'primary' => 'international',
                        'success' => 'local',
                        'warning' => 'both',
                    ]),

                TextColumn::make('country.name')
                    ->label('Country')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('account_number')
                    ->label('Account No.')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('website')
                    ->label('Website')
                    ->url(fn($record) => $record->website, true)
                    ->openUrlInNewTab()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('supports_documents')
                    ->label('Docs')
                    ->boolean(),

                IconColumn::make('supports_parcels')
                    ->label('Parcels')
                    ->boolean(),

                IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('service_type')
                    ->label('Service Type')
                    ->options([
                        'international' => 'International',
                        'local' => 'Local',
                        'both' => 'Both',
                    ]),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add Courier')
                    ->modalHeading('Create Courier')
                    ->form($this->getFormSchema()),
            ])
            ->actions([
                EditAction::make()
                    ->modalHeading(fn(Courier $record) => 'Edit Courier: ' . $record->name)
                    ->form($this->getFormSchema()),

                DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->defaultSort('name')
            ->striped()
            ->emptyStateHeading('No couriers found')
            ->emptyStateDescription('Add DHL, FedEx, UPS, local couriers, etc. to get started.');
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make(2)->schema([
                TextInput::make('name')
                    ->label('Courier Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('short_name')
                    ->label('Short Name')
                    ->maxLength(50)
                    ->placeholder('DHL, FEDEX, UPS'),

                Select::make('service_type')
                    ->label('Service Type')
                    ->options([
                        'international' => 'International',
                        'local' => 'Local',
                        'both' => 'Both',
                    ])
                    ->required()
                    ->default('international'),
            ]),

            Grid::make(2)->schema([
                TextInput::make('account_number')
                    ->label('Account Number')
                    ->maxLength(100),

                Select::make('country_id')
                    ->label('Base Country')
                    ->options(
                        Country::where('is_active', true)
                            ->orderBy('name')
                            ->pluck('name', 'id')
                    )
                    ->searchable()
                    ->preload(),
            ]),

            Grid::make(3)->schema([
                TextInput::make('contact_person')
                    ->label('Contact Person')
                    ->maxLength(255),

                TextInput::make('contact_phone')
                    ->label('Contact Phone')
                    ->maxLength(50),

                TextInput::make('contact_email')
                    ->label('Contact Email')
                    ->email()
                    ->maxLength(255),
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
            ]),

            Grid::make(2)->schema([
                TextInput::make('website')
                    ->label('Website')
                    ->maxLength(255)
                    ->url(),

                TextInput::make('tracking_url_template')
                    ->label('Tracking URL Template')
                    ->maxLength(255)
                    ->placeholder('https://...{TRACKING_NO}'),
            ]),

            Grid::make(4)->schema([
                Toggle::make('supports_documents')
                    ->label('Documents')
                    ->default(true),

                Toggle::make('supports_parcels')
                    ->label('Parcels')
                    ->default(true),

                Toggle::make('supports_import')
                    ->label('Import')
                    ->default(true),

                Toggle::make('supports_export')
                    ->label('Export')
                    ->default(true),
            ]),

            Grid::make(2)->schema([
                Toggle::make('is_default')
                    ->label('Default Courier')
                    ->helperText('Use as default in shipment/LC document screens.'),

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
        return view('livewire.admin.master.couriers-page');
    }
}