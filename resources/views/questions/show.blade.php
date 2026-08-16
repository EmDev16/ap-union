<x-layout title="Answer a question">
    <h1 class="text-2xl font-bold">{{ $question->title }}</h1>

    @if ($question->description)
        <p class="mt-2 text-gray-600">{{ $question->description }}</p>
    @endif

    <p class="text-sm text-gray-400 mt-1">
        Asked by {{ $question->user->username ?: $question->user->name }}
        on {{ $question->created_at->format('d/m/Y') }}
    </p>

    <form method="POST" action="{{ route('answers.store', $question) }}" class="mt-6 space-y-3">
        @csrf
        <label for="body" class="block font-medium">Your answer</label>
        <textarea id="body" name="body" rows="12" required
            class="w-full border border-gray-300 p-3">{{ old('body', $answer?->body) }}</textarea>
        @error('body')
            <p class="text-red-600 text-sm">{{ $message }}</p>
        @enderror

        <button type="submit" class="rounded bg-indigo-600 px-4 py-2 text-white font-semibold hover:bg-indigo-700">
            Save answer
        </button>
    </form>

    <a href="{{ route('home') }}" class="inline-block mt-6 underline">Back to home</a>
</x-layout>
