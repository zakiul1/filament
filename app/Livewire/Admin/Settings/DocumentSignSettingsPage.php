<?php

namespace App\Livewire\Admin\Settings;

use App\Models\DocumentSignSetting;
use App\Models\Signatory;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables;
use Filament\Schemas\Components\Section;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class DocumentSignSettingsPage extends Component implements HasTable, HasSchemas, HasActions
{
    use InteractsWithTable;
    use InteractsWithSchemas;
    use InteractsWithActions;

    protected function getDocumentTypes(): array
    {
        return [
            'PI' => 'Proforma Invoice',
            'COMMERCIAL_INVOICE' => 'Commercial Invoice',
            'LC_TRANSFER' => 'LC Transfer',
            'LC_AMENDMENT' => 'LC Amendment',
            'BOE' => 'Bill of Exchange',
            'NEGOTIATION_LETTER' => 'Negotiation Letter',
            'SAMPLE_INVOICE' => 'Sample Invoice',
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(DocumentSignSetting::query()->with('signatory'))
            ->columns([
                Tables\Columns\TextColumn::make('document_type')
                    ->label('Document Type')
                    ->formatStateUsing(fn($state) => $this->getDocumentTypes()[$state] ?? $state)
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'PI' => 'info',
                        'COMMERCIAL_INVOICE' => 'success',
                        default => 'gray',
                    })
                    ->searchable(),

                Tables\Columns\TextColumn::make('signatory.name')
                    ->label('Signatory')
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\ToggleColumn::make('show_signature')
                    ->label('Signature'),

                Tables\Columns\ToggleColumn::make('show_seal')
                    ->label('Seal'),
            ])
            ->actions([
                EditAction::make()
                    ->modalHeading(
                        fn(DocumentSignSetting $record) =>
                        'Edit: ' . ($this->getDocumentTypes()[$record->document_type] ?? $record->document_type)
                    )
                    ->form($this->getFormSchema())
                    ->fillForm(fn(DocumentSignSetting $record) => $record->toArray()),

                DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add Document Mapping')
                    ->modalHeading('Add New Document Signatory Mapping')
                    ->form($this->getFormSchema())
                    ->createAnother(false),
            ])
            ->emptyStateHeading('No document mappings yet')
            ->emptyStateDescription('Add your first document signatory mapping.')
            ->emptyStateIcon('heroicon-o-document-text')
            ->striped()
            ->defaultSort('document_type');
    }

    public function getFormSchema(): array
    {
        return [
            Section::make('Mapping Configuration')
                ->schema([
                    Forms\Components\Select::make('document_type')
                        ->label('Document Type')
                        ->options($this->getDocumentTypes())
                        ->required()
                        ->unique(DocumentSignSetting::class, 'document_type', ignoreRecord: true)
                        ->searchable()
                        ->preload(),

                    Forms\Components\Select::make('signatory_id')
                        ->label('Signatory')
                        ->options(
                            Signatory::where('is_active', true)
                                ->orderBy('name')
                                ->pluck('name', 'id')
                        )
                        ->required()
                        ->searchable()
                        ->preload(),

                    Forms\Components\Toggle::make('show_signature')
                        ->label('Show Signature on Document')
                        ->default(true)
                        ->inline(false),

                    Forms\Components\Toggle::make('show_seal')
                        ->label('Show Company Seal on Document')
                        ->default(false)
                        ->inline(false),
                ])
                ->columns(2),
        ];
    }

    public function render(): View
    {
        return view('livewire.admin.settings.document-sign-settings-page');
    }
}