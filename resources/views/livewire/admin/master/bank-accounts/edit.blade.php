<x-admin.master-layout>
    <div class="py-6">
        <div class="mx-auto max-w-3xl space-y-4 sm:px-6 lg:px-8">

            <div
                class="flex items-start justify-between rounded-xl bg-white p-4 shadow-sm dark:bg-zinc-900 dark:ring-1 dark:ring-zinc-700">
                <div>
                    <h2 class="text-lg font-semibold">Edit Bank Account</h2>
                    <p class="mt-1 text-sm text-zinc-500">
                        {{ $bankAccount->account_title }} • {{ $bankAccount->account_number }}
                    </p>
                </div>

                <flux:button type="button" variant="ghost"
                    onclick="window.location.href='{{ route('admin.master.bank-accounts.index') }}'">
                    Back
                </flux:button>
            </div>

            <div class="rounded-xl bg-white p-4 shadow-sm dark:bg-zinc-900 dark:ring-1 dark:ring-zinc-700">
                <form wire:submit.prevent="save" class="space-y-4">
                    {{ $this->form }}

                    <div class="flex justify-end gap-2">
                        <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="save">
                            <span wire:loading.remove wire:target="save">Update</span>
                            <span wire:loading wire:target="save">Updating...</span>
                        </flux:button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    {{-- ✅ REQUIRED for Filament v4 inline createOptionForm() modals --}}
    <x-filament-actions::modals />
</x-admin.master-layout>
