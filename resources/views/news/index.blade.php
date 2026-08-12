<x-layout title="Laatste nieuws">
    <h1 class="text-2xl font-bold">Laatste nieuws</h1>
    <div class="mt-6 space-y-6">
        @forelse ($newsItems as $news)
            <article class="rounded border p-4">
                @if ($news->image)<img src="{{ Storage::url($news->image) }}" alt="" class="mb-4 max-h-72 w-full object-cover">@endif
                <h2 class="text-xl font-semibold"><a href="{{ route('news.show', $news) }}">{{ $news->title }}</a></h2>
                <p class="mt-2">{{ Str::limit($news->content, 180) }}</p>
                <p class="mt-3 text-sm text-gray-600">{{ $news->published_at->format('d/m/Y H:i') }} · {{ $news->user->username ?: $news->user->name }}</p>
            </article>
        @empty <p>Nog geen nieuwsberichten.</p>
        @endforelse
    </div>
</x-layout>
