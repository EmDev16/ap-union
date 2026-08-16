<x-layout title="Search Members">
    <p class="text-base leading-7 text-[#1b1b18] dark:text-[#000000]">
        This is the search members page. Here you can search for other members of the community and connect with them.
    </p>

    <div>
        <h2 class="text-xl font-semibold">Search for Members</h2>
        <form method="GET" action="{{ route('search') }}" class="flex items-center space-x-4">
            <div class="relative w-full">
                <input type="text" name="q" value="{{ $term }}" placeholder="Search by name."
                    autocomplete="off"
                    data-member-search
                    data-suggestions-url="{{ route('search.suggestions') }}"
                    data-min-length="{{ $minLength }}"
                    class="w-full px-4 py-2 border focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <ul data-member-suggestions hidden
                    class="absolute z-10 w-full bg-white border border-gray-300 divide-y divide-gray-200"></ul>
            </div>
            <button type="submit"
                class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Search</button>
        </form>

        @if ($term !== null)
            <ul class="space-y-4 mt-4">
                @forelse ($members as $member)
                    <li class="p-4 border bg-white/5">
                        <h3 class="text-lg font-medium">
                            <a href="{{ route('profile.show', $member) }}" class="hover:underline"
                                data-member-link data-posts-count="{{ $member->posts_count }}">
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
        @endif

        <div data-recent-members hidden class="mt-8">
            <h2 class="text-xl font-semibold">Recently searched</h2>
            <ul data-recent-members-list class="space-y-4 mt-4"></ul>
            <button type="button" data-recent-members-clear
                class="mt-4 text-sm text-gray-600 underline">Clear this list</button>
        </div>
    </div>

    <script>
        (function () {
            const input = document.querySelector('[data-member-search]');
            const suggestions = document.querySelector('[data-member-suggestions]');
            const recent = document.querySelector('[data-recent-members]');
            const recentList = document.querySelector('[data-recent-members-list]');
            const recentClear = document.querySelector('[data-recent-members-clear]');
            const storageKey = 'ap-union.recent-members';
            const minLength = Number(input.dataset.minLength);

            function readRecent() {
                try {
                    const stored = JSON.parse(window.localStorage.getItem(storageKey) || '[]');
                    return Array.isArray(stored) ? stored : [];
                } catch (error) {
                    return [];
                }
            }

            function remember(member) {
                const members = readRecent().filter((item) => item.url !== member.url);
                members.unshift(member);
                window.localStorage.setItem(storageKey, JSON.stringify(members.slice(0, 10)));
            }

            function renderRecent() {
                const members = readRecent();
                recentList.replaceChildren();

                if (members.length === 0) {
                    recent.hidden = true;
                    return;
                }

                members.forEach((member) => {
                    const item = document.createElement('li');
                    item.className = 'p-4 border bg-white/5';

                    const link = document.createElement('a');
                    link.href = member.url;
                    link.textContent = member.name;
                    link.className = 'text-lg font-medium hover:underline';

                    const count = document.createElement('p');
                    count.className = 'text-gray-600';
                    count.textContent = member.posts_count + ' posts';

                    item.append(link, count);
                    recentList.append(item);
                });

                recent.hidden = false;
            }

            function hideSuggestions() {
                suggestions.replaceChildren();
                suggestions.hidden = true;
            }

            function renderSuggestions(members) {
                suggestions.replaceChildren();

                if (members.length === 0) {
                    hideSuggestions();
                    return;
                }

                members.forEach((member) => {
                    const item = document.createElement('li');

                    const link = document.createElement('a');
                    link.href = member.url;
                    link.className = 'block px-4 py-2 hover:bg-gray-100';
                    link.textContent = member.name + ' · ' + member.posts_count + ' posts';
                    link.addEventListener('click', () => remember(member));

                    item.append(link);
                    suggestions.append(item);
                });

                suggestions.hidden = false;
            }

            let timer = null;

            input.addEventListener('input', function () {
                const term = input.value.trim();
                window.clearTimeout(timer);

                if (term.length < minLength) {
                    hideSuggestions();
                    return;
                }

                timer = window.setTimeout(function () {
                    const url = new URL(input.dataset.suggestionsUrl, window.location.origin);
                    url.searchParams.set('q', term);

                    fetch(url, { headers: { 'Accept': 'application/json' } })
                        .then((response) => response.ok ? response.json() : [])
                        .then(renderSuggestions)
                        .catch(hideSuggestions);
                }, 200);
            });

            document.addEventListener('click', function (event) {
                if (!suggestions.contains(event.target) && event.target !== input) {
                    hideSuggestions();
                }
            });

            document.querySelectorAll('[data-member-link]').forEach(function (link) {
                link.addEventListener('click', function () {
                    remember({
                        name: link.textContent.trim(),
                        posts_count: Number(link.dataset.postsCount),
                        url: link.href,
                    });
                });
            });

            recentClear.addEventListener('click', function () {
                window.localStorage.removeItem(storageKey);
                renderRecent();
            });

            renderRecent();
        })();
    </script>
</x-layout>
