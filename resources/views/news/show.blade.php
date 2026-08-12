<x-layout :title="$news->title">
    <article><h1 class="text-2xl font-bold">{{ $news->title }}</h1><p class="mt-2 text-sm text-gray-600">{{ $news->published_at->format('d/m/Y H:i') }} · Door {{ $news->user->username ?: $news->user->name }}</p>@if ($news->image)<img src="{{ Storage::url($news->image) }}" alt="" class="my-6 max-h-96 w-full object-cover">@endif<div class="whitespace-pre-line">{{ $news->content }}</div></article>
</x-layout>
