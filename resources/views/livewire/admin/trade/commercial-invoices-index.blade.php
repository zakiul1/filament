<x-admin.master-layout :title="'Commercial Invoices'">
    <flux:main class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                    Commercial Invoices
                </h1>
                <p class="mt-1 text-sm text-zinc-500">
                    List of all commercial invoices issued from PI / LC.
                </p>
            </div>
        </div>

        {{ $this->table }}
    </flux:main>
</x-admin.master-layout>
