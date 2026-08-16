<x-layout title="Follow requests">
    <h1 class="text-2xl font-bold text-gray-900">Volgverzoeken</h1>
    <p class="mt-2 text-gray-700">Wie je aanvaardt, ziet al je posts.</p>

    @if (session('success'))
        <p class="mt-4 text-green-700">{{ session('success') }}</p>
    @endif

    <ul class="mt-6 space-y-3">
        @forelse ($requests as $member)
            <li class="flex items-center justify-between gap-4 rounded-lg border border-gray-300 bg-white p-4">
                <div class="flex items-center gap-3">
                    <x-avatar :user="$member" size="h-10 w-10" />
                    <a href="{{ route('profile.show', $member) }}" class="font-semibold text-gray-900 hover:underline">
                        {{ $member->username ?: $member->name }}
                    </a>
                </div>

                <div class="flex items-center gap-2">
                    <form method="POST" action="{{ route('follows.accept', $member) }}">
                        @csrf
                        <button type="submit"
                            class="rounded bg-indigo-600 px-4 py-2 text-white font-semibold whitespace-nowrap"
                            style="background-color:#4f46e5;color:#ffffff;">Aanvaarden</button>
                    </form>
                    <form method="POST" action="{{ route('follows.decline', $member) }}"
                        onsubmit="return confirm('Dit verzoek weigeren?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="rounded bg-gray-300 px-4 py-2 text-gray-900 font-semibold whitespace-nowrap">Weigeren</button>
                    </form>
                </div>
            </li>
        @empty
            <li class="text-gray-700">Geen openstaande verzoeken.</li>
        @endforelse
    </ul>
</x-layout>
