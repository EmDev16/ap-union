@props(['user' => null, 'size' => 'h-10 w-10', 'hidePhoto' => false])

@php($photo = ! $hidePhoto && $user?->profile_photo ? asset('storage/'.$user->profile_photo) : null)

@if ($photo)
    <img src="{{ $photo }}" alt="{{ $user->username ?: $user->name }}"
        class="{{ $size }} rounded-full object-cover shrink-0" style="border-radius:9999px;object-fit:cover;">
@else
    <div class="{{ $size }} rounded-full bg-gray-300 shrink-0" style="border-radius:9999px;"></div>
@endif
