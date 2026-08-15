<x-layout title="Public Profile">
    <div style="color: #111827;">
        <div class="bg-white p-6 rounded-lg shadow-sm mb-6">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-start gap-4">
                    @if ($user->profile_photo)
                        <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="{{ $user->name }}" class="h-24 w-24 rounded-full object-cover">
                    @else
                        <div class="h-24 w-24 bg-gray-300 rounded-full"></div>
                    @endif

                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $user->username ?: $user->name }}</h1>
                        @if ($user->username)
                            <p class="text-gray-600">{{ $user->name }}</p>
                        @endif

                        @if ($user->about_me)
                            <p class="text-gray-700 mt-2">{{ $user->about_me }}</p>
                        @endif

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
                    </div>
                </div>

                @auth
                    @if ($user->is(auth()->user()))
                        <div class="flex flex-col gap-2">
                            <a href="{{ route('posts.create') }}" class="inline-block rounded bg-indigo-600 px-4 py-2 text-white font-semibold hover:bg-indigo-700 text-center">Create Post</a>
                            <a href="{{ route('profile.edit') }}" class="inline-block rounded bg-gray-300 px-4 py-2 text-gray-900 font-semibold hover:bg-gray-400 text-center">Edit Profile</a>
                        </div>
                    @else
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

        <section>
            <h2 class="text-xl font-bold mb-6">Posts</h2>
            @forelse ($user->posts()->with('media', 'comments.user', 'comments.replies', 'likes')->orderBy('created_at', 'desc')->get() as $post)
                @include('posts.card', ['post' => $post])
            @empty
                <p>No posts yet.</p>
            @endforelse
        </section>
    </div>
</x-layout>
