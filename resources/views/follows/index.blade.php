<x-layout :title="$title">
    <h1 class="text-2xl font-bold text-gray-900">{{ $title }}</h1>

    <ul class="mt-6 space-y-3">
        @forelse ($members as $member)
            <li class="flex items-center gap-3 rounded-lg border border-gray-300 bg-white p-4">
                <x-avatar :user="$member" size="h-10 w-10" />
                <a href="{{ route('profile.show', $member) }}" class="font-semibold text-gray-900 hover:underline">
                    {{ $member->username ?: $member->name }}
                </a>
            </li>
        @empty
            <li class="text-gray-700">Nog niemand.</li>
        @endforelse
    </ul>

    <a href="{{ route('profile.show', $user) }}" class="mt-6 inline-block underline">Terug naar het profiel</a>
</x-layout>
