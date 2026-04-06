@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-bold text-sm text-gray-700 dark:text-gray-300 tracking-wide mb-1', 'style' => "font-family: 'Almarai', sans-serif;"]) }}>
    {{ $value ?? $slot }}
</label>
