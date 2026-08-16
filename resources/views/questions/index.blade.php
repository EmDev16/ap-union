<x-layout title="Questions">
    <h1 class="text-2xl font-bold">Questions</h1>

    @if (session('status'))
        <p class="mt-4 text-green-700">{{ session('status') }}</p>
    @endif

    <form method="POST" action="{{ route('questions.store') }}" class="mt-6 space-y-3 border p-4 bg-white/5">
        @csrf
        <div>
            <label for="title" class="block font-medium">Ask a question</label>
            <input id="title" name="title" type="text" value="{{ old('title') }}" required
                class="mt-1 w-full border border-gray-300 p-2">
            @error('title')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="description" class="block font-medium">Description (optional)</label>
            <textarea id="description" name="description" rows="3"
                class="mt-1 w-full border border-gray-300 p-2">{{ old('description') }}</textarea>
            @error('description')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="rounded bg-indigo-600 px-4 py-2 text-white font-semibold hover:bg-indigo-700">
            Post question
        </button>
    </form>

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
                <p class="text-gray-600">No questions yet.</p>
            </li>
        @endforelse
    </ul>
</x-layout>
