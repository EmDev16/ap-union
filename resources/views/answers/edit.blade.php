<x-layout title="Edit answer">
    <h1 class="text-2xl font-bold">{{ $answer->question->title }}</h1>

    <form method="POST" action="{{ route('answers.update', $answer) }}" class="mt-6 space-y-3">
        @csrf
        @method('PATCH')
        <label for="body" class="block font-medium">Your answer</label>
        <textarea id="body" name="body" rows="12" required
            class="w-full border border-gray-300 p-3">{{ old('body', $answer->body) }}</textarea>
        @error('body')
            <p class="text-red-600 text-sm">{{ $message }}</p>
        @enderror

        <button type="submit" class="rounded bg-indigo-600 px-4 py-2 text-white font-semibold hover:bg-indigo-700">
            Save changes
        </button>
    </form>

    <a href="{{ route('answers.index') }}" class="inline-block mt-6 underline">Back to my answers</a>
</x-layout>
