<?php

namespace App\Livewire\Admin\Settings;

use App\Models\CompanySetting;
use Filament\Forms\Components\FileUpload;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Filament\Schemas\Components\Section;
class CompanyProfile extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    /** Form data container (Filament v4 pattern) */
    public ?array $data = [];

    /** The singleton company settings record */
    public CompanySetting $companySetting;

    public function mount(): void
    {
        // Ensure we always have 1 row
        $this->companySetting = CompanySetting::firstOrCreate([], [
            'name' => 'Siatex (BD) Ltd.',
            'base_currency_code' => 'USD',
        ]);

        // Fill form with existing data
        $this->form->fill($this->companySetting->toArray());
    }

    /**
     * Filament v4 schema-based form
     */
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Company Information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Company Name')
                            ->required()
                            ->maxLength(255),

                        Textarea::make('address')
                            ->rows(3),

                        TextInput::make('phone'),

                        TextInput::make('email')
                            ->email(),

                        TextInput::make('base_currency_code')
                            ->label('Base Currency (3 letters)')
                            ->maxLength(3)
                            ->required(),
                    ]),

                Section::make('Branding')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('logo_path')
                            ->label('Logo')
                            ->directory('company')
                            ->image()
                            ->imageEditor()
                            ->imagePreviewHeight('100'),

                        FileUpload::make('seal_path')
                            ->label('Seal')
                            ->directory('company')
                            ->image()
                            ->imageEditor()
                            ->imagePreviewHeight('100'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        $this->companySetting->update($state);

        session()->flash('status', 'Company profile updated.');
    }

    public function render(): View
    {
        return view('livewire.admin.settings.company-profile');
    }
}