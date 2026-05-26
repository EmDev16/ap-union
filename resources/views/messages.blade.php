<x-layout title="Home">
    <h1>Messages</h1>

    <p class="text-base leading-7 text-[#1b1b18] dark:text-[#000000]">
        This is the messages page. Here you can view and manage your messages with other users. You can also start new
        conversations and keep track of your message history.
    </p>

    <div>
        <h2 class="text-xl font-semibold">Your Conversations</h2>
        <x-dropdown align="left" width="48">
            <x-slot name="trigger">
                <button
                    class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <span>Filter</span>

                </button>
            </x-slot>
            <x-slot name="content">
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Unread</a>
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Archived</a>
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Request</a>
            </x-slot>
        </x-dropdown>

        <ul class="space-y-4">
            <li class="p-4 border bg-white/5">
                <h3 class="text-lg font-medium">Conversation with User A</h3>
                <p class="text-gray-400">Last message: "Hey, how are you?"</p>
            </li>
            <li class="p-4 border bg-white/5">
                <h3 class="text-lg font-medium">Conversation with User B</h3>
                <p class="text-gray-400">Last message: "Let's catch up soon!"</p>
            </li>
            <li class="p-4 border bg-white/5">
                <h3 class="text-lg font-medium">Conversation with User C</h3>
                <p class="text-gray-400">Last message: "Can you help me with this?"</p>
            </li>
            <li class="p-4 border bg-white/5">
                <h3 class="text-lg font-medium">Conversation with User D</h3>
                <p class="text-gray-400">Last message: "Thanks for your help!"</p>
            </li>
        </ul>

</x-layout>