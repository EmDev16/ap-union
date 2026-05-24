<x-layout title="Latest">
    <h1>Latest news</h1>
    <p class="text-base leading-7 text-[#1b1b18] dark:text-[#000000]">
        Welcome to our latest news page! Here, we bring you the most recent updates and developments in our project,
        the world of technology, science, and innovation. Stay informed about the latest breakthroughs, trends, and
        discoveries that are shaping our future. Whether it's advancements in artificial intelligence, space
        exploration, or cutting-edge gadgets, we've got you covered. Explore our curated selection of news articles
        and stay ahead of the curve with the latest happenings in the tech world.

        <div class="mt-6 border-t border-gray-200 pt-6">
            <h2 class="text-xl font-semibold">Latest News</h2>
            <x-dropdown align="left" width="48">
            <x-slot name="trigger">
                <button
                    class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <span>Filter</span>

                </button>
            </x-slot>
            <x-slot name="content">
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">AP Union</a>
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Worldwide</a>
            </x-slot>
        </x-dropdown>
            <ul class="space-y-4">
                <li class="p-4 border rounded-lg bg-white/5">
                    <h3 class="text-lg font-medium">AP Union Launches New Feature to Enhance User Experience</h3>
                    <p class="text-gray-400">AP Union has announced the launch of a new feature that aims to enhance user experience and provide more personalized content to its users.</p>
                </li>
                <li class="p-4 border rounded-lg bg-white/5">
                    <h3 class="text-lg font-medium">Tech Company X Unveils Revolutionary AI Technology</h3>
                    <p class="text-gray-400">Tech Company X has announced the launch of their groundbreaking AI technology that promises to revolutionize the way we interact with machines.</p>
                </li>
                <li class="p-4 border rounded-lg bg-white/5">
                    <h3 class="text-lg font-medium">AP Union will be offline this weekend for maintenance. </h3>
                    <p class="text-gray-400">AP Union will be undergoing scheduled maintenance this weekend to improve performance and security. The platform will be offline from Saturday 12:00 AM to Sunday 11:59 PM. We apologize for any inconvenience this may cause and appreciate your understanding.</p>
                </li>
                <li class="p-4 border rounded-lg bg-white/5">
                    <h3 class="text-lg font-medium">Scientists Discover New Exoplanet in Habitable Zone</h3>
                    <p class="text-gray-400">A team of scientists has discovered a new exoplanet located in the habitable zone of its star, raising hopes for the possibility of extraterrestrial life.</p>
                </li>
                <li class="p-4 border rounded-lg bg-white/5">
                    <h3 class="text-lg font-medium">AP Union Partners with Local Charities to Support Community Initiatives</h3>
                    <p class="text-gray-400">AP Union has announced a partnership with local charities to support community initiatives and give back to those in need.</p>
                </li>
                <li class="p-4 border rounded-lg bg-white/5">
                    <h3 class="text-lg font-medium">Some charities that could really use your help</h3>
                    <p class="text-gray-400">There are several charities in need of support and volunteers. Consider donating or volunteering your time to make a difference in your community.</p>
                </li>
                 <li class="p-4 border rounded-lg bg-white/5">
                    <h3 class="text-lg font-medium">When thinking summer, we think summer stories!</h3>
                    <p class="text-gray-400">Get ready for a season of exciting summer tales and adventures! And to celebrate it, we're sharing some of the most exciting summer stories from our community.</p>
                </li>
                <li class="p-4 border rounded-lg bg-white/5">
                    <h3 class="text-lg font-medium">Breakthrough in Quantum Computing Achieved</h3>
                    <p class="text-gray-400">Researchers have made a significant breakthrough in quantum computing, paving the way for faster and more efficient processing power.</p>
                </li>
                <li class="p-4 border rounded-lg bg-white/5">
                    <h3 class="text-lg font-medium">AP Union Celebrates 1 Million Users Milestone</h3>
                    <p class="text-gray-400">AP Union has reached a significant milestone, celebrating 1 million users on its platform. The company expresses gratitude to its community for their support and looks forward to continued growth and innovation.</p>    
                </li>
                <li class="p-4 border rounded-lg bg-white/5">
                    <h3 class="text-lg font-medium">Week long focus on art related subjects</h3>
                    <p class="text-gray-400">AP Union will be dedicating the upcoming week to art-related subjects, featuring a series of events, discussions, and content centered around various forms of art. Join us as we explore the world of creativity and expression through art.</p>
                </li>
                <li class="p-4 border rounded-lg bg-white/5">
                    <h3 class="text-lg font-medium">Wishing Merry Christmas to our dear users</h3>
                    <p class="text-gray-400">As we approach the holiday season, AP Union wants to wish our dear users a Merry Christmas and a Happy New Year. Thank you for being part of our community!</p>
                </li>

            </ul>
        </div>
    </p>
</x-layout>