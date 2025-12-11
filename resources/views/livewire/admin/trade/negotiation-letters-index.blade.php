<x-admin.master-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-lg font-semibold">Negotiation Letters</h2>
            <p class="text-sm text-zinc-500">Submission / negotiation documents</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto space-y-6">
            <div class="bg-white dark:bg-zinc-900 rounded-xl p-4 shadow-sm">
                {{ $this->table }}
            </div>
        </div>
    </div>
</x-admin.master-layout>
