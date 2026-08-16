<x-layout title="Mijn vragen">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">Mijn vragen</h1>
        <a href="{{ route('admin.questions.create') }}" class="rounded bg-indigo-600 px-4 py-2 text-white">Nieuwe vraag</a>
    </div>

    @if (session('status')) <p class="mt-4 text-green-700">{{ session('status') }}</p> @endif

    <div class="mt-6 space-y-4">
        @forelse ($questions as $question)
            <article class="rounded border p-4">
                <h2 class="font-semibold">{{ $question->title }}</h2>
                @if ($question->description)
                    <p class="mt-1 text-gray-600">{{ $question->description }}</p>
                @endif
                <p class="mt-1 text-sm text-gray-500">
                    {{ $question->created_at->format('d/m/Y') }} · {{ $question->answers_count }} antwoorden
                    @if ($question->answers_publish_on)
                        · antwoorden {{ $question->answersArePublic() ? 'gepubliceerd op' : 'publiceren op' }}
                        {{ $question->answers_publish_on->format('d/m/Y') }}
                    @endif
                </p>
                <a href="{{ route('admin.questions.edit', $question) }}" class="mt-2 inline-block underline">Aanpassen</a>
            </article>
        @empty
            <p>Nog geen vragen gesteld.</p>
        @endforelse
    </div>
</x-layout>
