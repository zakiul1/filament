<?php

namespace App\Livewire\Admin\Master;

use App\Models\FactoryCategory;
use App\Models\FactorySubcategory;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms;
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

class FactorySubcategoriesPage extends Component implements HasTable, HasForms, HasActions
{
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                FactorySubcategory::query()->with('category')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Subcategory')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable(),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add Subcategory')
                    ->form($this->getFormSchema()),
            ])
            ->actions([
                EditAction::make()
                    ->form($this->getFormSchema()),

                DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->defaultSort('name')
            ->striped();
    }

    protected function getFormSchema(): array
    {
        return [
            Select::make('factory_category_id')
                ->label('Category')
                ->options(
                    FactoryCategory::where('is_active', true)
                        ->orderBy('name')
                        ->pluck('name', 'id')
                )
                ->searchable()
                ->preload()
                ->required(),

            TextInput::make('name')
                ->label('Name')
                ->required()
                ->maxLength(255),

            TextInput::make('slug')
                ->label('Slug')
                ->maxLength(255),

            Textarea::make('description')
                ->label('Description')
                ->rows(3),

            Toggle::make('is_active')
                ->label('Active')
                ->default(true),
        ];
    }

    public function render(): View
    {
        return view('livewire.admin.master.factory-subcategories-page');
    }
}