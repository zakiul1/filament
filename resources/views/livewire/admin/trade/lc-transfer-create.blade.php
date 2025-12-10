<x-admin.master-layout title="New LC Transfer">
    <flux:main>
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">New LC Transfer</h1>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    Allocate part of a customer LC to a factory.
                </p>
            </div>

            <flux:button href="{{ route('admin.trade.lc-transfers.index') }}" icon="arrow-left" variant="ghost">
                Back to list
            </flux:button>
        </div>

        <form wire:submit.prevent="create" class="space-y-6">
            {{ $this->form }}

            <div>
                <flux:button type="submit" variant="primary" icon="check-circle">
                    Save Transfer
                </flux:button>
            </div>
        </form>
    </flux:main>
</x-admin.master-layout>
