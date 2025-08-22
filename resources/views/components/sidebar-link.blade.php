@props(['active', 'icon' => null])

@php
$classes = ($active ?? false) ? 'sidebar-link active' : 'sidebar-link';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)
        <span class="mr-2">{!! $icon !!}</span>
    @endif
    <span>{{ $slot }}</span>
</a>
