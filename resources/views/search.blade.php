<x-layout title="Search Members">
    <p class="text-base leading-7 text-[#1b1b18] dark:text-[#000000]">
        This is the search members page. Here you can search for other members of the community and connect with them.
    </p>

    <div>
        <h2 class="text-xl font-semibold">Search for Members</h2>
        <form method="GET" action="{{ route('search') }}" class="flex items-center space-x-4">
            <input type="text" name="q" value="{{ $term }}" placeholder="Search by name."
                class="w-full px-4 py-2 border focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <button type="submit"
                class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Search</button>
        </form>

        <ul class="space-y-4 mt-4">
            @forelse ($members as $member)
                <li class="p-4 border bg-white/5">
                    <h3 class="text-lg font-medium">
                        <a href="{{ route('profile.show', $member) }}" class="hover:underline">
                            {{ $member->username ?: $member->name }}
                        </a>
                    </h3>
                    <p class="text-gray-600">{{ $member->posts_count }} posts</p>
                </li>
            @empty
                <li class="p-4 border bg-white/5">
                    <p class="text-gray-600">No members found.</p>
                </li>
            @endforelse
        </ul>

        <div class="mt-8">
            {{ $members->links() }}
        </div>
    </div>
</x-layout>
