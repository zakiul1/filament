<x-admin.master-layout :title="'Edit Commercial Invoice'">
    <flux:main class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                    Edit Commercial Invoice
                </h1>
                <p class="mt-1 text-sm text-zinc-500">
                    Update this commercial invoice.
                </p>
            </div>

            <a href="{{ route('admin.trade.commercial-invoices.index') }}"
                class="text-sm text-zinc-500 hover:text-zinc-900">
                Back to list
            </a>
        </div>

        <form wire:submit="update" class="space-y-6">
            {{ $this->form }}

            <div class="flex items-center gap-3">
                <x-primary-button type="submit">
                    Update Invoice
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
