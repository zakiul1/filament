<x-admin.master-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                    Buyer Order Summary
                </h2>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    Select a buyer order to view summary & print.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <div
                class="overflow-hidden rounded-xl bg-white p-4 shadow-sm dark:bg-zinc-900 dark:ring-1 dark:ring-zinc-700">
                {{ $this->form }}
            </div>

            <div class="mt-4 flex gap-2">
                <flux:button type="button" variant="primary" wire:click="viewSummary">
                    View Summary
                </flux:button>

                <flux:button type="button" variant="ghost" href="{{ route('admin.reports.trade.index') }}">
                    Back
                </flux:button>

            </div>
        </div>
    </div>
</x-admin.master-layout>
