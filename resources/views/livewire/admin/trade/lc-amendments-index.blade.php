<x-admin.master-layout :title="'LC Amendments'">
    <flux:main class="p-6">
        <div class="mx-auto max-w-6xl space-y-6">
            <div class="flex items-center justify-between">
                <h1 class="text-lg font-semibold">
                    {{ __('LC Amendments') }}
                </h1>

                <flux:button variant="primary" icon="plus" href="{{ route('admin.trade.lc-amendments.create') }}"
                    wire:navigate>
                    {{ __('New Amendment') }}
                </flux:button>
            </div>

            {{-- Filament table --}}
            {{ $this->table }}
        </div>
    </flux:main>
</x-admin.master-layout>
