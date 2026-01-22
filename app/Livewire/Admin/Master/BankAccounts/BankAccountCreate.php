<?php

namespace App\Livewire\Admin\Master\BankAccounts;

use Livewire\Component;

use Filament\Schemas\Schema;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;

use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;

use Illuminate\Support\Facades\Auth;

use App\Models\BankAccount;
use App\Support\Banking\BankAccountForm;

class BankAccountCreate extends Component implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'is_active' => true,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema(BankAccountForm::schema())
            ->statePath('data')
            ->model(BankAccount::class);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        unset($data['bank_id']); // UI-only

        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        BankAccount::create($data);

        $this->redirectRoute('admin.master.bank-accounts.index');
    }

    public function render()
    {
        return view('livewire.admin.master.bank-accounts.create');
    }
}