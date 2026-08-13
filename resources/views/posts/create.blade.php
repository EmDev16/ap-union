<x-layout title="Create Post">
    <div style="color: #111827;">
    <h1 class="text-2xl font-bold">Share Your Idea</h1>
    <p class="mt-2 text-sm">Add text, photos and/or videos.</p>
    <form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data" class="mt-6 space-y-4">
        @csrf
        <div>
            <label for="content" class="block text-gray-900 font-semibold mb-2">Text</label>
            <textarea id="content" name="content" rows="7" maxlength="5000" class="block w-full border border-gray-400 bg-white p-2 text-gray-900" placeholder="What's on your mind?">{{ old('content') }}</textarea>
            <x-input-error :messages="$errors->get('content')" />
        </div>
        <div>
            <label for="media" class="block text-gray-900 font-semibold mb-2">Photos or Videos (max. 5 files, 20 MB each)</label>
            <input id="media" name="media[]" type="file" accept="image/jpeg,image/png,image/webp,video/mp4,video/webm" multiple class="block w-full border border-gray-400 bg-white p-2">
            <x-input-error :messages="$errors->get('media')" />
            <x-input-error :messages="$errors->get('media.*')" />
        </div>
        <div class="flex items-center gap-4">
            <button type="submit" class="rounded bg-indigo-600 px-4 py-2 text-white font-semibold hover:bg-indigo-700">Publish Post</button>
            <a href="{{ route('profile.show', auth()->user()) }}" class="underline text-gray-700 hover:text-gray-900 font-semibold">Cancel</a>
        </div>
    </form>
    </div>
</x-layout>
