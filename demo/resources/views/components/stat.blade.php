@props(['label', 'value', 'accent' => 'text-white'])

<article class="rounded-lg border border-white/10 bg-white/[0.04] p-5">
    <p class="text-sm font-medium text-neutral-400">{{ $label }}</p>
    <p class="mt-3 text-3xl font-extrabold {{ $accent }}">{{ $value }}</p>
</article>

