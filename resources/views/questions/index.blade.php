<x-layout title="Questions">
    <h1 class="text-2xl font-bold">Questions</h1>

    <p class="text-base leading-7 text-[#1b1b18] dark:text-[#000000]">
        The questions of the last {{ $months }} months that you have not answered yet.
    </p>

    <ul class="space-y-4 mt-6">
        @forelse ($questions as $question)
            <li class="p-4 border bg-white/5">
                <h2 class="text-lg font-medium">
                    <a href="{{ route('questions.show', $question) }}" class="hover:underline">{{ $question->title }}</a>
                </h2>
                @if ($question->description)
                    <p class="text-gray-600">{{ $question->description }}</p>
                @endif
                <p class="text-sm text-gray-400">
                    Asked by {{ $question->user->username ?: $question->user->name }}
                    on {{ $question->created_at->format('d/m/Y') }}
                </p>
            </li>
        @empty
            <li class="p-4 border bg-white/5">
                <p class="text-gray-600">You have answered every question. Nice work.</p>
            </li>
        @endforelse
    </ul>

    <a href="{{ route('answers.index') }}" class="inline-block mt-6 underline">See my answers</a>
</x-layout>
