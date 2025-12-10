<x-admin.master-layout :title="'New LC Amendment'">
    <flux:main class="space-y-6">
        {{-- Page heading --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                    New LC Amendment
                </h1>
                <p class="mt-1 text-sm text-zinc-500">
                    Record an amendment against an existing LC.
                </p>
            </div>

            <a href="{{ route('admin.trade.lc-amendments.index') }}" class="text-sm text-zinc-500 hover:text-zinc-900">
                Back to list
            </a>
        </div>

        {{-- Filament form --}}
        <form wire:submit="create" class="space-y-6">
            {{ $this->form }}

            <div class="flex items-center gap-3">
                <x-primary-button type="submit">
                    Save Amendment
                </x-primary-button>

                <a href="{{ route('admin.trade.lc-amendments.index') }}"
                    class="text-sm text-zinc-500 hover:text-zinc-900">
                    Cancel
                </a>
            </div>
        </form>

        {{-- Needed for Filament actions used inside the form --}}
        <x-filament-actions::modals />
    </flux:main>
</x-admin.master-layout>
