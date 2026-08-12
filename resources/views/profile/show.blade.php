<x-layout title="Public Profile">
    <div class="max-w-7xl mx-auto py-12 sm:px-6 lg:px-8">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg" style="color: #111827;">
            <div class="max-w-xl space-y-4">
                @if ($user->profile_photo)
                    <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="{{ $user->name }}" class="h-32 w-32 rounded-full object-cover">
                @endif

                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $user->username ?: $user->name }}</h1>

                    @if ($user->username)
                        <p class="text-gray-600">{{ $user->name }}</p>
                    @endif
                </div>

                @if ($user->birthday)
                    <p class="text-gray-600">Birthday: {{ $user->birthday->format('F j, Y') }}</p>
                @endif

                @if ($user->about_me)
                    <p class="text-gray-800">{{ $user->about_me }}</p>
                @endif

                @auth
                    @if ($user->is(auth()->user()))
                        <div class="flex items-center gap-3">
                            <a href="{{ route('posts.create') }}" class="inline-block rounded bg-indigo-600 px-4 py-2" style="color: #ffffff;">Post maken</a>
                            <span class="text-sm">{{ $user->posts->count() }} posts</span>
                        </div>
                    @endif
                @endauth
            </div>
        </div>

        @if (session('status'))
            <p class="mt-4 text-green-700">{{ session('status') }}</p>
        @endif

        <section class="mt-6 space-y-4">
            <h2 class="text-xl font-bold" style="color: #111827;">Posts</h2>
            @forelse ($user->posts as $post)
                <article class="rounded bg-white p-4 shadow" style="color: #111827;">
                    @if ($post->content)<p class="whitespace-pre-line">{{ $post->content }}</p>@endif
                    @foreach ($post->media as $media)
                        @if ($media->type === 'image')
                            <img src="{{ Storage::url($media->path) }}" alt="Post media" class="mt-3 max-h-96 w-full object-contain">
                        @else
                            <video controls class="mt-3 max-h-96 w-full"><source src="{{ Storage::url($media->path) }}"></video>
                        @endif
                    @endforeach
                    <p class="mt-3 text-sm text-gray-600">{{ $post->created_at->format('d/m/Y H:i') }}</p>
                    @auth
                        @if ($post->user_id === auth()->id())
                            <form method="POST" action="{{ route('posts.destroy', $post) }}" class="mt-2">
                                @csrf
                                @method('DELETE')
                                <button class="text-sm text-red-700 underline">Verwijder post</button>
                            </form>
                        @endif
                    @endauth
                </article>
            @empty
                <p style="color: #111827;">Nog geen posts.</p>
            @endforelse
        </section>
    </div>
</x-layout>
