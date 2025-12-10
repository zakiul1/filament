<x-admin.master-layout title="LC Transfers">
    <flux:main>
        <div class="mb-6">
            <h1 class="text-2xl font-semibold tracking-tight">LC Transfers</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                Manage transfers from customer LCs to factories.
            </p>
        </div>

        {{ $this->table }}
    </flux:main>
</x-admin.master-layout>
