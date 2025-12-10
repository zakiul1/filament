<x-admin.master-layout title="Edit LC Transfer">
    <flux:main>
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">
                    Edit LC Transfer #{{ $record->transfer_no }}
                </h1>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    Update LC transfer details.
                </p>
            </div>

            <flux:button href="{{ route('admin.trade.lc-transfers.index') }}" icon="arrow-left" variant="ghost">
                Back to list
            </flux:button>
        </div>

        <form wire:submit.prevent="update" class="space-y-6">
            {{ $this->form }}

            <div>
                <flux:button type="submit" variant="primary" icon="check-circle">
                    Update Transfer
                </flux:button>
            </div>
        </form>
    </flux:main>
</x-admin.master-layout>
