<x-layout title="Home">
    <p class="text-base leading-7 text-[#1b1b18] dark:text-[#000000]">
        This is the search members page. Here you can search for other members of the community and connect with them.
        You can also filter your search results by interests, and more.
    </p>

    <div>
        <h2 class="text-xl font-semibold">Search for Members</h2>
        <form class="flex items
-center space-x-4">
            <input type="text" placeholder="Search by name."
                class="w-full px-4 py-2 border focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <button type="submit"
                class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Search</button>
        </form>
        <ul class="space-y-4 mt-4">
            <li class="p-4 border bg-white/5">
                <h3 class="text-lg font-medium">User A</h3>
                <p class="text-gray-400"> Interests: Music, Travel</p>
            </li>
            <li class="p-4 border bg-white/5">
                <h3 class="text-lg font-medium">User B</h3>
                <p class="text-gray-400"> Interests: Art, Food</p>
            </li>
            <li class="p-4 border bg-white/5">
                <h3 class="text-lg font-medium">User C</h3>
                <p class="text-gray-400"> Interests: Sports, Technology</p>
            </li>
            <li class="p-4 border bg-white/5">
                <h3 class="text-lg font-medium">User D</h3>
                <p class="text-gray-400"> Interests: Fashion, Fitness</p>
            </li>
        </ul>
    </div>
</x-layout>