<x-layout title="FAQ page">
    <h1>Explore</h1>

    <p class="text-base leading-7 text-[#1b1b18] dark:text-[#000000]">
        Explore all posts from accounts you're not following yet. This is a great way to discover new content and
        connect with people who share your interests.
    </p>

    <div>
        <x-dropdown align="left" width="48">
            <x-slot name="trigger">
                <button
                    class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <span>Filter</span>
                    <svg class="ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                        aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M5.23 7.21a.75.75 0 011.06.02L10 11.584l3.71-4.354a.75.75 0 111.14.976l-4 4.708a.75.75 0 01-1.14 0l-4-4.708a.75.75 0 01.02-1.06z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </x-slot>
            <x-slot name="content">
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Most Recent</a>
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Most Popular</a>
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Trending</a>
            </x-slot>
        </x-dropdown>
        <ul class="space-y-4">
            <li class="p-4 border rounded-lg bg-white/5">
                <h3 class="text-xl font-semibold">Post Title 1</h3>
                <p class="text-gray-400">This is a brief description of the post content. It gives you an idea of what
                    the post is about.</p>
            </li>
            <li class="p-4 border rounded-lg bg-white/5">
                <h3 class="text-xl font-semibold">Post Title 2</h3>
                <p class="text-gray-400">This is a brief description of the post content. It gives you an idea of what
                    the post is about.</p>
            </li>
            <li class="p-4 border rounded-lg bg-white/5">
                <h3 class="text-xl font-semibold">Post Title 3</h3>
                <p class="text-gray-400">This is a brief description of the post content. It gives you an idea of what
                    the post is about.</p>
            </li>
            <li class="p-4 border rounded-lg bg-white/5">
                <h3 class="text-xl font-semibold">Post Title 4</h3>
                <p class="text-gray-400">This is a brief description of the post content. It gives you an idea of what
                    the post is about.</p>
            </li>
        </ul>

    </div>

</x-layout>