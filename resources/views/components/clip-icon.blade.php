@props(['class' => 'w-5 h-5'])

<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"
    {{ $attributes->merge(['class' => $class]) }} aria-hidden="true">
    <path stroke-linecap="round" stroke-linejoin="round"
        d="M18.5 10.5 11 18a4.5 4.5 0 0 1-6.36-6.36l8.13-8.13a3 3 0 0 1 4.24 4.24l-8.13 8.13a1.5 1.5 0 0 1-2.12-2.12l7.42-7.42" />
</svg>
