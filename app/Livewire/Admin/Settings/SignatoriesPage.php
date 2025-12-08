<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Signatory;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables;
use Filament\Actions\DeleteAction;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;


use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class SignatoriesPage extends Component implements HasTable, HasSchemas, HasActions
{
    use InteractsWithTable;
    use InteractsWithSchemas;
    use InteractsWithActions;

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->query(Signatory::query())
            ->columns([
                ImageColumn::make('signature_path')
                    ->label('Signature')
                    ->height(50)
                    ->width(120)
                    ->defaultImageUrl(asset('images/signature-placeholder.png'))
                    ->circular()
                    ->extraImgAttributes(['loading' => 'lazy']),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('designation')
                    ->searchable()
                    ->sortable()
                    ->color('gray'),

                ToggleColumn::make('is_active')
                    ->label('Active')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('All')
                    ->trueLabel('Active Only')
                    ->falseLabel('Inactive Only'),
            ])
            ->actions([
                EditAction::make()
                    ->modalHeading(fn(Signatory $record) => 'Edit Signatory: ' . $record->name)
                    ->form($this->getFormSchema())
                    ->fillForm(fn(Signatory $record) => $record->toArray()),

                DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading(fn(Signatory $record) => 'Delete ' . $record->name . '?')
                    ->modalDescription('This action cannot be undone.'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add New Signatory')
                    ->modalHeading('Create Signatory')
                    ->form($this->getFormSchema())
                    ->createAnother(false),
            ])
            ->emptyStateHeading('No signatories found')
            ->emptyStateDescription('Add your first authorized signatory to get started.')
            ->emptyStateIcon('heroicon-o-user-plus')
            ->striped()
            ->defaultSort('name');
    }

    public function getFormSchema(): array
    {
        return [
            Section::make('Signatory Details')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->autofocus(),

                    Forms\Components\TextInput::make('designation')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('e.g. Managing Director'),

                    Forms\Components\FileUpload::make('signature_path')
                        ->label('Digital Signature')
                        ->image()
                        ->imageEditor()
                        ->directory('signatories')
                        ->preserveFilenames()
                        ->maxSize(2048) // 2MB
                        ->imagePreviewHeight('120')
                        ->imageCropAspectRatio('3:1')
                        ->imageResizeTargetWidth('600')
                        ->imageResizeTargetHeight('200')
                        ->acceptedFileTypes(['image/png'])
                        ->helperText('Transparent PNG recommended (600×200px)'),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Active Status')
                        ->default(true)
                        ->helperText('Only active signatories can be selected in documents'),
                ]),
        ];
    }

    public function render(): View
    {
        return view('livewire.admin.settings.signatories-page');
    }
}