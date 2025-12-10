<x-admin.master-layout :title="__('New Proforma Invoice')">
    <div class="max-w-6xl mx-auto space-y-6">
        <form wire:submit.prevent="create" class="space-y-6">
            {{ $this->form }}

            <div class="flex justify-end">
                <x-primary-button type="submit">
                    {{ __('Save PI') }}
                </x-primary-button>
            </div>
        </form>
    </div>

    <x-filament-actions::modals />
</x-admin.master-layout>
