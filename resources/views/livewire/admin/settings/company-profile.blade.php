<x-admin.system-layout :title="__('Company Settings')">
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <x-primary-button type="submit" class="">
            {{ __('Save') }}
        </x-primary-button>
    </form>

    @if (session('status'))
        <p class="mt-4 text-sm text-green-600">
            {{ session('status') }}
        </p>
    @endif

    <x-filament-actions::modals />
</x-admin.system-layout>
