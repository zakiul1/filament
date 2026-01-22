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

class BankAccountEdit extends Component implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    public BankAccount $bankAccount;

    public ?array $data = [];

    public function mount(BankAccount $bankAccount): void
    {
        $this->bankAccount = $bankAccount->load(['branch.bank', 'country', 'currency']);

        // ✅ bank_id is UI-only, so we must derive it from branch.bank_id
        $bankId = $this->bankAccount->branch?->bank_id;

        // ✅ Fill state manually so bank + branch both show selected
        $this->form->fill([
            'bank_id' => $bankId, // UI-only
            'bank_branch_id' => $this->bankAccount->bank_branch_id,

            'country_id' => $this->bankAccount->country_id,
            'currency_id' => $this->bankAccount->currency_id,

            'account_title' => $this->bankAccount->account_title,
            'account_number' => $this->bankAccount->account_number,
            'iban' => $this->bankAccount->iban,
            'swift_code' => $this->bankAccount->swift_code,
            'routing_number' => $this->bankAccount->routing_number,
            'is_active' => (bool) $this->bankAccount->is_active,
            'notes' => $this->bankAccount->notes,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema(BankAccountForm::schema())
            ->statePath('data')
            ->model($this->bankAccount);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        unset($data['bank_id']); // ✅ UI-only, never save

        $data['updated_by'] = Auth::id();

        $this->bankAccount->update($data);

        $this->redirectRoute('admin.master.bank-accounts.index');
    }

    public function render()
    {
        return view('livewire.admin.master.bank-accounts.edit');
    }
}