<x-admin.master-layout :title="__('Edit Proforma Invoice')">
    <div class="max-w-6xl mx-auto space-y-6">
        <form wire:submit.prevent="update" class="space-y-6">
            {{ $this->form }}

            <div class="flex justify-end">
                <x-primary-button type="submit">
                    {{ __('Update PI') }}
                </x-primary-button>
            </div>
        </form>
    </div>

    <x-filament-actions::modals />
</x-admin.master-layout>
