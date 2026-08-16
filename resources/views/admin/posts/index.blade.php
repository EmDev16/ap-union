<x-layout title="Posts in review">
    <h1 class="text-2xl font-bold">Posts in review</h1>
    <p class="mt-2 text-gray-600">Deze posts zijn verborgen voor andere leden.</p>

    @if (session('status')) <p class="mt-4 text-green-700">{{ session('status') }}</p> @endif

    <div class="mt-6 space-y-4">
        @forelse ($posts as $post)
            <article class="rounded border p-4">
                <p class="text-sm text-gray-600">
                    {{ $post->user->username ?: $post->user->name }} ·
                    {{ $post->created_at->format('d/m/Y H:i') }} ·
                    in review sinds {{ $post->under_review_at->format('d/m/Y H:i') }}
                </p>
                <p class="mt-2 whitespace-pre-line">{{ $post->content }}</p>
                @if ($post->review_reason)
                    <p class="mt-2 text-sm text-gray-600">Reden: {{ $post->review_reason }}</p>
                @endif

                <div class="mt-3 flex items-center gap-4">
                    <a href="{{ route('posts.show', $post) }}" class="underline">Bekijk post</a>
                    <form method="POST" action="{{ route('admin.posts.unreview', $post) }}">
                        @csrf
                        @method('DELETE')
                        <button class="rounded bg-indigo-600 px-4 py-2 text-white">Terug online zetten</button>
                    </form>
                </div>
            </article>
        @empty
            <p>Geen posts in review.</p>
        @endforelse
    </div>
</x-layout>
