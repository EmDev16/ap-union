<x-layout title="My answers">
    <h1 class="text-2xl font-bold">My answers</h1>

    @if (session('status'))
        <p class="mt-4 text-green-700">{{ session('status') }}</p>
    @endif

    <ul class="space-y-4 mt-6">
        @forelse ($answers as $answer)
            <li class="p-4 border bg-white/5">
                <h2 class="text-lg font-medium">
                    <a href="{{ route('questions.show', $answer->question) }}" class="hover:underline">
                        {{ $answer->question->title }}
                    </a>
                </h2>
                <p class="text-gray-600 whitespace-pre-line">{{ $answer->body }}</p>
                <p class="text-sm text-gray-400">Last updated {{ $answer->updated_at->format('d/m/Y H:i') }}</p>
                <a href="{{ route('answers.edit', $answer) }}" class="underline">Edit answer</a>
            </li>
        @empty
            <li class="p-4 border bg-white/5">
                <p class="text-gray-600">You have not answered any questions yet.</p>
            </li>
        @endforelse
    </ul>
</x-layout>
