<?php

namespace App\Support\Banking;

use App\Models\Bank;
use App\Models\BankBranch;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get; // ✅ v4
use Filament\Schemas\Components\Utilities\Set; // ✅ v4

class BankAccountForm
{
    public static function schema(): array
    {
        return [
            Section::make('Bank Information')
                ->columns(3)
                ->schema([
                    // ✅ UI-only bank filter (NOT saved to DB)
                    Select::make('bank_id')
                        ->label('Bank')
                        ->options(fn() => Bank::query()->orderBy('name')->pluck('name', 'id')->all())
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live() // ✅ v4 reactive
                        ->afterStateUpdated(function (Set $set) {
                            // when bank changes, reset branch
                            $set('bank_branch_id', null);
                        })
                        // ✅ inline create bank
                        ->createOptionForm([
                            TextInput::make('name')
                                ->label('Bank Name')
                                ->required()
                                ->maxLength(190),
                        ])
                        ->createOptionUsing(function (array $data) {
                            $bank = Bank::create([
                                'name' => $data['name'],
                            ]);

                            return $bank->id;
                        }),

                    // ✅ branch filtered by selected bank + inline create branch
                    Select::make('bank_branch_id')
                        ->label('Bank Branch')
                        ->options(function (Get $get) {
                            $bankId = $get('bank_id');
                            if (!$bankId) {
                                return [];
                            }

                            return BankBranch::query()
                                ->where('bank_id', $bankId)
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->all();
                        })
                        ->searchable()
                        ->preload()
                        ->required()
                        ->disabled(fn(Get $get) => blank($get('bank_id')))
                        ->createOptionForm([
                            TextInput::make('name')
                                ->label('Branch Name')
                                ->required()
                                ->maxLength(190),
                        ])
                        ->createOptionUsing(function (array $data, Get $get) {
                            $bankId = $get('bank_id');

                            $branch = BankBranch::create([
                                'bank_id' => $bankId,
                                'name' => $data['name'],
                            ]);

                            return $branch->id;
                        }),

                    Select::make('country_id')
                        ->label('Country')
                        ->relationship('country', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('currency_id')
                        ->label('Currency')
                        ->relationship('currency', 'code')
                        ->searchable()
                        ->preload()
                        ->nullable(),
                ]),

            Section::make('Account Details')
                ->columns(2)
                ->schema([
                    TextInput::make('account_title')
                        ->required()
                        ->maxLength(190),

                    TextInput::make('account_number')
                        ->required()
                        ->maxLength(190),

                    TextInput::make('iban')
                        ->maxLength(34)
                        ->nullable(),

                    TextInput::make('swift_code')
                        ->maxLength(11)
                        ->nullable(),

                    TextInput::make('routing_number')
                        ->maxLength(20)
                        ->nullable(),

                    Toggle::make('is_active')
                        ->default(true),
                ]),

            Section::make('Notes')
                ->schema([
                    Textarea::make('notes')
                        ->rows(3)
                        ->nullable(),
                ]),
        ];
    }
}