<x-layout title="Nieuwe post">
    <div style="color: #111827;">
    <h1 class="text-2xl font-bold">Deel je idee</h1>
    <p class="mt-2 text-sm">Voeg tekst, foto’s en/of video’s toe.</p>
    <form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data" class="mt-6 space-y-4">
        @csrf
        <div>
            <label for="content">Tekst</label>
            <textarea id="content" name="content" rows="7" maxlength="5000" class="block w-full border border-gray-400 bg-white p-2">{{ old('content') }}</textarea>
            <x-input-error :messages="$errors->get('content')" />
        </div>
        <div>
            <label for="media">Foto’s of video’s (max. 5 bestanden, 20 MB per bestand)</label>
            <input id="media" name="media[]" type="file" accept="image/jpeg,image/png,image/webp,video/mp4,video/webm" multiple class="block w-full border border-gray-400 bg-white p-2">
            <x-input-error :messages="$errors->get('media')" />
            <x-input-error :messages="$errors->get('media.*')" />
        </div>
        <div class="flex items-center gap-4">
            <button type="submit" class="rounded bg-indigo-600 px-4 py-2" style="color: #ffffff;">Publiceer post</button>
            <a href="{{ route('profile.show', auth()->user()) }}" class="underline">Annuleren</a>
        </div>
    </form>
    </div>
</x-layout>
