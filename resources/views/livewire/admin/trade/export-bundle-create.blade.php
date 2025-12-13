<x-admin.master-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                Create Export Bundle
            </h2>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                Select a Commercial Invoice to generate all export documents.
            </p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <div
                class="overflow-hidden rounded-xl bg-white p-4 shadow-sm dark:bg-zinc-900 dark:ring-1 dark:ring-zinc-700">
                {{ $this->form }}
            </div>

            <div class="mt-4 flex gap-2">
                <flux:button type="button" variant="primary" wire:click="createBundle">
                    Generate Bundle
                </flux:button>

                <flux:button type="button" variant="ghost" href="{{ route('admin.trade.export-bundles.index') }}">
                    Back
                </flux:button>
            </div>
        </div>
    </div>
</x-admin.master-layout>
