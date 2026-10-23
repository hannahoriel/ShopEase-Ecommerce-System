@props([
    'name' => '',
    'image' => null,
    'size' => '36px',
    'class' => '',
])

@php
    $avatarName = trim((string) $name);
    $nameParts = preg_split('/\s+/u', $avatarName, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $initialParts = count($nameParts) > 1
        ? [$nameParts[0], $nameParts[count($nameParts) - 1]]
        : $nameParts;
    $initials = collect($initialParts)
        ->map(fn ($part) => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($part, 0, 1)))
        ->implode('');
    $initials = $initials ?: 'U';
@endphp

<span
    {{ $attributes->merge([
        'class' => 'account-avatar '.$class,
        'role' => 'img',
        'aria-label' => ($avatarName ?: 'User').' profile picture',
        'style' => "position:relative;display:inline-flex;align-items:center;justify-content:center;flex:0 0 {$size};width:{$size};height:{$size};overflow:hidden;border-radius:50%;background:#FFE6DE;color:#650B12;font-size:12px;font-weight:700;line-height:1;",
    ]) }}
>
    <span aria-hidden="true">{{ $initials }}</span>
    @if($image)
        <img
            src="{{ $image }}"
            alt=""
            aria-hidden="true"
            style="position:absolute;inset:0;width:100%;height:100%;border-radius:inherit;object-fit:cover;"
            onerror="this.remove()"
        >
    @endif
</span>
