<?php

namespace App\Livewire\Admin\Master;

use App\Models\Country;
use App\Models\Currency;
use App\Models\Factory;
use App\Models\FactorySubcategory;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
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

class FactoriesPage extends Component implements HasTable, HasForms, HasActions
{
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;

    // 🔑 Tell Filament which model the form works with
    protected ?string $model = Factory::class;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Factory::query()
                    ->with(['country', 'defaultCurrency', 'subcategories.category'])
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Factory Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('short_name')
                    ->label('Short')
                    ->badge()
                    ->sortable(),

                TextColumn::make('factory_type')
                    ->label('Type')
                    ->badge()
                    ->sortable(),

                TextColumn::make('country.name')
                    ->label('Country')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('total_lines')
                    ->label('Lines')
                    ->sortable(),

                TextColumn::make('capacity_pcs_per_month')
                    ->label('Capacity / Month')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('subcategories.name')
                    ->label('Products / Categories')
                    ->badge()
                    ->limitList(3)
                    ->separator(', ')
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('factory_type')
                    ->label('Type')
                    ->options([
                        'knit' => 'Knit',
                        'woven' => 'Woven',
                        'sweater' => 'Sweater',
                        'denim' => 'Denim',
                    ]),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add Factory')
                    ->modalHeading('Create Factory')
                    ->form($this->getFormSchema()),
            ])
            ->actions([
                EditAction::make()
                    ->modalHeading(fn(Factory $record) => 'Edit Factory: ' . $record->name)
                    ->form($this->getFormSchema()),

                DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->defaultSort('name')
            ->striped()
            ->emptyStateHeading('No factories found')
            ->emptyStateDescription('Add factories to use in LC transfers, production planning, etc.');
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Basic Information')
                ->columns(3)
                ->schema([
                    TextInput::make('name')
                        ->label('Factory Name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('short_name')
                        ->label('Short Name')
                        ->maxLength(50)
                        ->placeholder('e.g. ABC KNIT, XYZ WOVEN'),

                    TextInput::make('code')
                        ->label('Code')
                        ->maxLength(50)
                        ->placeholder('Internal code (optional)'),

                    Select::make('factory_type')
                        ->label('Factory Type')
                        ->options([
                            'knit' => 'Knit',
                            'woven' => 'Woven',
                            'sweater' => 'Sweater',
                            'denim' => 'Denim',
                            'other' => 'Other',
                        ])
                        ->nullable(),
                ]),

            Section::make('Address & Location')
                ->columns(2)
                ->schema([
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

                    Select::make('country_id')
                        ->label('Country')
                        ->options(
                            Country::where('is_active', true)
                                ->orderBy('name')
                                ->pluck('name', 'id')
                        )
                        ->searchable()
                        ->preload(),
                ]),

            Section::make('Contact')
                ->columns(3)
                ->schema([
                    TextInput::make('phone')
                        ->label('Phone')
                        ->maxLength(50),

                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->maxLength(255),

                    TextInput::make('website')
                        ->label('Website')
                        ->url()
                        ->maxLength(255),

                    TextInput::make('contact_person_name')
                        ->label('Contact Person Name')
                        ->maxLength(255),

                    TextInput::make('contact_person_designation')
                        ->label('Designation')
                        ->maxLength(255),

                    TextInput::make('contact_person_phone')
                        ->label('Contact Phone')
                        ->maxLength(50),

                    TextInput::make('contact_person_email')
                        ->label('Contact Email')
                        ->email()
                        ->maxLength(255),
                ]),

            Section::make('Capability')
                ->columns(3)
                ->schema([
                    TextInput::make('total_lines')
                        ->label('Total Lines')
                        ->numeric()
                        ->minValue(0),

                    TextInput::make('capacity_pcs_per_month')
                        ->label('Capacity / Month (PCS)')
                        ->numeric()
                        ->minValue(0),

                    Select::make('default_currency_id')
                        ->label('Default Currency')
                        ->options(
                            Currency::where('is_active', true)
                                ->orderBy('code')
                                ->pluck('code', 'id')
                        )
                        ->searchable()
                        ->preload(),

                    Textarea::make('capability_notes')
                        ->label('Capability Notes')
                        ->rows(3)
                        ->columnSpanFull()
                        ->placeholder('e.g. Knit tops, fleece, 12GG sweater, denim bottoms, etc.'),
                ]),

            Section::make('Categories & Products')
                ->schema([
                    Select::make('subcategories')
                        ->label('Product Categories')
                        ->multiple()
                        ->relationship('subcategories', 'name')
                        ->options(
                            FactorySubcategory::where('is_active', true)
                                ->orderBy('name')
                                ->pluck('name', 'id')
                        )
                        ->searchable()
                        ->preload()
                        ->helperText('Select applicable product subcategories for this factory.'),
                ]),

            Section::make('Factory Images')
                ->schema([
                    FileUpload::make('images')
                        ->label('Factory Images')
                        ->multiple()
                        ->image()
                        ->reorderable()
                        ->directory('factories/images')
                        ->imageEditor()
                        ->imagePreviewHeight('150')
                        ->maxSize(4096)
                        ->helperText('Upload multiple images of the factory (front view, production floor, etc.).'),
                ]),

            Section::make('Status & Notes')
                ->columns(2)
                ->schema([
                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),

                    Textarea::make('notes')
                        ->label('Internal Notes')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
        ];
    }

    public function render(): View
    {
        return view('livewire.admin.master.factories-page');
    }
}