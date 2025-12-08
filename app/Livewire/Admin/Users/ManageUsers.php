<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Livewire\Component;
use Filament\Tables;
use Filament\Forms;
use Filament\Actions;
use Filament\Tables\Table;
use Filament\Forms\Form;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Contracts\HasActions;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions\Concerns\InteractsWithActions;
use Illuminate\Contracts\View\View;

class ManageUsers extends Component implements HasTable, HasForms, HasActions
{
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;  // THIS WAS MISSING!

    public ?array $data = [];

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query()->with('roles'))
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->formatStateUsing(
                        fn($state, $record) =>
                        $record->roles->pluck('name')->implode(', ') ?: '—'
                    ),
            ])
            ->actions([
                Actions\EditAction::make()
                    ->form($this->getUserFormSchema())
                    ->fillForm(fn(User $record) => $record->only('name', 'email') + ['roles' => $record->roles->pluck('id')])
                    ->action(function (User $record, array $data) {
                        $record->update([
                            'name' => $data['name'],
                            'email' => $data['email'],
                        ]);
                        $record->roles()->sync($data['roles'] ?? []);
                    }),
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->form($this->getUserFormSchema())
                    ->action(function (array $data) {
                        $user = User::create([
                            'name' => $data['name'],
                            'email' => $data['email'],
                            'password' => bcrypt('temporary123'),
                        ]);
                        $user->roles()->sync($data['roles'] ?? []);
                    }),
            ]);
    }

    public function getUserFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('email')
                ->email()
                ->required()
                ->unique(User::class, 'email', ignoreRecord: true)
                ->maxLength(255),

            Forms\Components\Select::make('roles')
                ->multiple()
                ->relationship('roles', 'name')
                ->preload()
                ->searchable()
                ->required(),
        ];
    }

    public function render(): View
    {
        return view('livewire.admin.users.manage-users');
    }
}