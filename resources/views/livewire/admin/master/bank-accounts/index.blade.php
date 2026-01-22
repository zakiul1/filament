<x-admin.master-layout>
    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-4 sm:px-6 lg:px-8">

            <div
                class="flex items-start justify-between rounded-xl bg-white p-4 shadow-sm dark:bg-zinc-900 dark:ring-1 dark:ring-zinc-700">
                <div>
                    <h2 class="text-lg font-semibold">Bank Accounts</h2>
                    <p class="mt-1 text-sm text-zinc-500">Core bank accounts used across modules.</p>
                </div>

                <flux:button type="button" variant="primary"
                    onclick="window.location.href='{{ route('admin.master.bank-accounts.create') }}'">
                    Create Bank Account
                </flux:button>
            </div>

            <div class="rounded-xl bg-white p-4 shadow-sm dark:bg-zinc-900 dark:ring-1 dark:ring-zinc-700">
                {{ $this->table }}
            </div>

        </div>
    </div>
</x-admin.master-layout>
