<x-admin.master-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                    Edit LC Receive
                </h2>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    Update LC information and status.
                </p>
            </div>

            <a href="{{ route('admin.trade.lc-receives.index') }}" wire:navigate
                class="text-sm text-zinc-600 underline-offset-4 hover:underline dark:text-zinc-300">
                ← Back to LC list
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <div
                class="overflow-hidden rounded-xl bg-white p-6 shadow-sm dark:bg-zinc-900 dark:ring-1 dark:ring-zinc-700">
                {{-- Filament schema form --}}
                {{ $this->form }}

                <div class="mt-6 flex justify-end gap-3">
                    <a href="{{ route('admin.trade.lc-receives.index') }}" wire:navigate
                        class="inline-flex items-center rounded-lg border border-zinc-300 px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-600 dark:text-zinc-200 dark:hover:bg-zinc-800">
                        Cancel
                    </a>

                    <button type="button" wire:click="update" wire:loading.attr="disabled"
                        class="inline-flex items-center rounded-lg bg-zinc-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-zinc-800 disabled:cursor-not-allowed disabled:opacity-70 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-200">
                        <span wire:loading.remove>
                            Update LC
                        </span>
                        <span wire:loading>
                            Updating...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-admin.master-layout>
