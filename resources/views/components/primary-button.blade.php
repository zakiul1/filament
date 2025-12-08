@props([
    'type' => 'button',
])

<button
    type="{{ $type }}"
    {{ $attributes->class([
        'px-4 py-2 rounded-lg text-sm font-medium transition',
        'bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-zinc-700 dark:hover:bg-zinc-600',
        'focus:outline-none focus:ring-2 focus:ring-zinc-500 focus:ring-offset-2',
        'disabled:opacity-50 disabled:cursor-not-allowed',
    ]) }}
>
    {{ $slot }}
</button>
