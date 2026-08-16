<x-layout title="New conversation">
    <h1 class="text-2xl font-bold">New conversation</h1>

    <p class="text-base leading-7 text-[#1b1b18] dark:text-[#000000]">
        You can only start a conversation with members you follow.
    </p>

    <x-input-error :messages="$errors->get('user_id')" class="mt-4" />

    <ul class="space-y-4 mt-6">
        @forelse ($members as $member)
            <li class="p-4 border bg-white/5 flex items-center justify-between gap-4">
                <a href="{{ route('profile.show', $member) }}" class="text-lg font-medium hover:underline">
                    {{ $member->username ?: $member->name }}
                </a>

                <form method="POST" action="{{ route('messages.store') }}">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $member->id }}">
                    <button type="submit"
                        class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600">Message</button>
                </form>
            </li>
        @empty
            <li class="p-4 border bg-white/5">
                <p class="text-gray-600">
                    You don't follow anyone yet. Go to
                    <a href="{{ route('explore') }}" class="text-indigo-600 underline font-semibold">Explore</a>
                    and follow some members first.
                </p>
            </li>
        @endforelse
    </ul>

    <a href="{{ route('messages') }}" class="inline-block mt-6 underline">Back to your conversations</a>
</x-layout>
