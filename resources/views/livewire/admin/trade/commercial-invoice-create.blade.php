<x-admin.master-layout :title="'New Commercial Invoice'">
    <flux:main class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                    New Commercial Invoice
                </h1>
                <p class="mt-1 text-sm text-zinc-500">
                    Prepare a commercial invoice based on PI and LC.
                </p>
            </div>

            <a href="{{ route('admin.trade.commercial-invoices.index') }}"
                class="text-sm text-zinc-500 hover:text-zinc-900">
                Back to list
            </a>
        </div>

        <form wire:submit="create" class="space-y-6">
            {{ $this->form }}

            <div class="flex items-center gap-3">
                <x-primary-button type="submit">
                    Save Invoice
                </x-primary-button>

                <a href="{{ route('admin.trade.commercial-invoices.index') }}"
                    class="text-sm text-zinc-500 hover:text-zinc-900">
                    Cancel
                </a>
            </div>
        </form>

        <x-filament-actions::modals />
    </flux:main>
</x-admin.master-layout>
