<x-layout title="Public Profile">
    <div style="color: #111827;">
        <div class="bg-white p-6 rounded-lg shadow-sm mb-6">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-start gap-4">
                    <x-avatar :user="$user" size="h-24 w-24" :hide-photo="! auth()->check()" />

                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $user->username ?: $user->name }}</h1>
                        @auth
                            @if ($user->username)
                                <p class="text-gray-600">{{ $user->name }}</p>
                            @endif
                        @endauth

                        @if ($user->about_me)
                            <p class="text-gray-700 mt-2">{{ $user->about_me }}</p>
                        @endif

                        @guest
                            <div class="flex gap-6 mt-3 text-sm">
                                <div>
                                    <span class="font-bold">{{ $user->posts_count }}</span>
                                    <span class="text-gray-600">Posts</span>
                                </div>
                            </div>
                        @else
                            <div class="flex gap-6 mt-3 text-sm">
                                <a href="{{ route('follows.followers', $user) }}" class="hover:underline">
                                    <span class="font-bold">{{ $user->followers()->count() }}</span>
                                    <span class="text-gray-600">Volgers</span>
                                </a>
                                <a href="{{ route('follows.following', $user) }}" class="hover:underline">
                                    <span class="font-bold">{{ $user->following()->count() }}</span>
                                    <span class="text-gray-600">Volgen</span>
                                </a>
                                <div>
                                    <span class="font-bold">{{ $user->posts_count }}</span>
                                    <span class="text-gray-600">Posts</span>
                                </div>
                            </div>
                        @endguest

                        @if ($user->interests->isNotEmpty())
                            <div class="flex flex-wrap gap-2 mt-3">
                                @foreach ($user->interests as $interest)
                                    <a href="{{ route('search', ['q' => $interest->name]) }}"
                                        class="rounded-full border border-gray-300 px-3 py-1 text-xs text-gray-700 hover:bg-gray-100"
                                        style="border-radius:9999px;white-space:nowrap;">{{ $interest->name }}</a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                @auth
                    @if ($user->is(auth()->user()))
                        <div class="flex flex-col items-stretch gap-2">
                            @if (auth()->user()->isAdmin())
                                <a href="{{ route('admin.questions.create') }}" class="inline-block rounded bg-indigo-600 px-4 py-2 text-white font-semibold hover:bg-indigo-700 text-center whitespace-nowrap">Create Question</a>
                                <a href="{{ route('admin.questions.index') }}" class="inline-block rounded bg-indigo-600 px-4 py-2 text-white font-semibold hover:bg-indigo-700 text-center whitespace-nowrap">My Questions</a>
                            @else
                                <a href="{{ route('posts.create') }}" class="inline-block rounded bg-indigo-600 px-4 py-2 text-white font-semibold hover:bg-indigo-700 text-center whitespace-nowrap">Create Post</a>
                                <a href="{{ route('posts.showcase') }}" class="inline-block rounded bg-indigo-600 px-4 py-2 text-white font-semibold hover:bg-indigo-700 text-center whitespace-nowrap">Choose Visible Posts</a>
                                <a href="{{ route('answers.index') }}" class="inline-block rounded bg-indigo-600 px-4 py-2 text-white font-semibold hover:bg-indigo-700 text-center whitespace-nowrap">My Answers</a>
                            @endif
                            <a href="{{ route('follows.requests') }}" class="inline-block rounded bg-gray-300 px-4 py-2 text-gray-900 font-semibold hover:bg-gray-400 text-center whitespace-nowrap">
                                Follow Requests ({{ auth()->user()->followRequests()->count() }})
                            </a>
                            <a href="{{ route('profile.edit') }}" class="inline-block rounded bg-gray-300 px-4 py-2 text-gray-900 font-semibold hover:bg-gray-400 text-center whitespace-nowrap">Edit Profile</a>
                        </div>
                    @elseif (auth()->user()->isAdmin() === $user->isAdmin())
                        <div>
                            @if (auth()->user()->follows($user))
                                <form action="{{ route('users.unfollow', $user) }}" method="POST"
                                    onsubmit="return confirm('{{ $user->username ?: $user->name }} niet meer volgen?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded bg-gray-300 px-6 py-2 text-gray-900 font-semibold hover:bg-gray-400 whitespace-nowrap">Following</button>
                                </form>
                            @elseif (auth()->user()->hasPendingRequestFor($user))
                                <form action="{{ route('users.unfollow', $user) }}" method="POST"
                                    onsubmit="return confirm('Je volgverzoek intrekken?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded bg-gray-300 px-6 py-2 text-gray-900 font-semibold hover:bg-gray-400 whitespace-nowrap">Requested</button>
                                </form>
                            @else
                                <form action="{{ route('users.follow', $user) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="rounded bg-indigo-600 px-6 py-2 text-white font-semibold hover:bg-indigo-700 whitespace-nowrap">Follow</button>
                                </form>
                            @endif
                        </div>
                    @endif
                @endauth
            </div>
        </div>

        @if (session('status'))
            <p class="mb-4 text-green-700">{{ session('status') }}</p>
        @endif

        @if ($user->isAdmin())
            <section class="mb-6">
                <h2 class="text-xl font-bold mb-6">Questions</h2>
                @forelse ($questions as $question)
                    <article class="border border-gray-300 rounded-lg p-4 bg-white mb-3">
                        <h3 class="font-semibold text-gray-900">{{ $question->title }}</h3>
                        @if ($question->description)
                            <p class="text-gray-700 mt-1">{{ $question->description }}</p>
                        @endif
                        <p class="text-sm text-gray-600 mt-1">
                            {{ $question->created_at->format('d/m/Y') }} · {{ $question->answers_count }} answers
                        </p>
                    </article>
                @empty
                    <p class="text-gray-700">No questions yet.</p>
                @endforelse
            </section>
        @endif

        @if ($answers->isNotEmpty())
            <section class="mb-6">
                <h2 class="text-xl font-bold mb-6">Published answers</h2>
                @foreach ($answers as $answer)
                    <article class="border border-gray-300 rounded-lg p-4 bg-white mb-3">
                        <h3 class="font-semibold text-gray-900">{{ $answer->question->title }}</h3>
                        <p class="text-gray-900 mt-2 whitespace-pre-line">{{ $answer->body }}</p>
                        <p class="text-sm text-gray-600 mt-1">
                            Published on {{ $answer->question->answers_publish_on->format('d/m/Y') }}
                        </p>
                    </article>
                @endforeach
            </section>
        @endif

        <section>
            <h2 class="text-xl font-bold mb-6">Posts</h2>

            @unless ($showsEveryPost)
                <div class="border border-gray-300 rounded-lg p-4 bg-white mb-3">
                    <p class="font-semibold text-gray-900">This account is private</p>
                    <p class="text-gray-700 mt-2">
                        @guest
                            <a href="{{ route('login') }}" class="underline">Log in</a>
                            @if (Route::has('register'))
                                or <a href="{{ route('register') }}" class="underline">create an account</a>
                            @endif
                            and follow this member to see every post. Below are the posts they chose to show.
                        @else
                            Follow this member and wait for their approval to see every post.
                            Below are the posts they chose to show.
                        @endguest
                    </p>
                </div>
            @endunless

            @forelse ($posts as $post)
                @include('posts.card', ['post' => $post])
            @empty
                <p>No posts yet.</p>
            @endforelse
        </section>
    </div>
</x-layout>
