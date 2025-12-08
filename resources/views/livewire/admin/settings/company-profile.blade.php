<div class="max-w-3xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">Company Profile</h1>

    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <button type="submit">
            Save
        </button>
    </form>

    @if (session('status'))
        <div class="mt-4 text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif

    {{-- Needed for Filament action/file-upload modals --}}
    <x-filament-actions::modals />
</div>
