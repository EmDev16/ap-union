<x-layout title="Public Profile">
    <div style="color: #111827;">
        <div class="bg-white p-6 rounded-lg shadow-sm mb-6">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-start gap-4">
                    @auth
                        @if ($user->profile_photo)
                            <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="{{ $user->name }}" class="h-24 w-24 rounded-full object-cover">
                        @else
                            <div class="h-24 w-24 bg-gray-300 rounded-full"></div>
                        @endif
                    @else
                        <div class="h-24 w-24 bg-gray-300 rounded-full"></div>
                    @endauth

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
                            <div>
                                <span class="font-bold">{{ $user->followers()->count() }}</span>
                                <span class="text-gray-600">Volgers</span>
                            </div>
                            <div>
                                <span class="font-bold">{{ $user->following()->count() }}</span>
                                <span class="text-gray-600">Volgen</span>
                            </div>
                            <div>
                                <span class="font-bold">{{ $user->posts->count() }}</span>
                                <span class="text-gray-600">Posts</span>
                            </div>
                        </div>
                        @endguest
                    </div>
                </div>

                @auth
                    @if ($user->is(auth()->user()))
                        <div class="flex flex-col gap-2">
                            @if (auth()->user()->isAdmin())
                                <a href="{{ route('admin.questions.create') }}" class="inline-block rounded bg-indigo-600 px-4 py-2 text-white font-semibold hover:bg-indigo-700 text-center">Create Question</a>
                                <a href="{{ route('admin.questions.index') }}" class="inline-block rounded bg-indigo-600 px-4 py-2 text-white font-semibold hover:bg-indigo-700 text-center">My Questions</a>
                            @else
                                <a href="{{ route('posts.create') }}" class="inline-block rounded bg-indigo-600 px-4 py-2 text-white font-semibold hover:bg-indigo-700 text-center">Create Post</a>
                                <a href="{{ route('answers.index') }}" class="inline-block rounded bg-indigo-600 px-4 py-2 text-white font-semibold hover:bg-indigo-700 text-center">My Answers</a>
                            @endif
                            <a href="{{ route('profile.edit') }}" class="inline-block rounded bg-gray-300 px-4 py-2 text-gray-900 font-semibold hover:bg-gray-400 text-center">Edit Profile</a>
                        </div>
                    @elseif (auth()->user()->isAdmin() === $user->isAdmin())
                        <div>
                            @if ($user->followers()->where('follower_id', auth()->id())->exists())
                                <form action="{{ route('users.unfollow', $user) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded bg-gray-300 px-6 py-2 text-gray-900 font-semibold hover:bg-gray-400">Following</button>
                                </form>
                            @else
                                <form action="{{ route('users.follow', $user) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="rounded bg-indigo-600 px-6 py-2 text-white font-semibold hover:bg-indigo-700">Follow</button>
                                </form>
                            @endif
                        </div>
                    @endauth
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

        <section>
            <h2 class="text-xl font-bold mb-6">Posts</h2>
            @guest
                <div class="border border-gray-300 rounded-lg p-4 bg-white">
                    <p class="font-semibold text-gray-900">This account is private</p>
                    <p class="text-gray-700 mt-2">
                        <a href="{{ route('login') }}" class="underline">Log in</a>
                        @if (Route::has('register'))
                            or <a href="{{ route('register') }}" class="underline">create an account</a>
                        @endif
                        to see the posts of this member.
                    </p>
                </div>
            @else
                @forelse ($user->posts()->unless($user->is(auth()->user()) || auth()->user()->isAdmin(), fn ($query) => $query->published())->with('media', 'comments.user', 'comments.replies', 'likes')->orderBy('created_at', 'desc')->get() as $post)
                    @include('posts.card', ['post' => $post])
                @empty
                    <p>No posts yet.</p>
                @endforelse
            @endguest
        </section>
    </div>
</x-layout>
