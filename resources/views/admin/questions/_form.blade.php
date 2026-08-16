<div>
    <label for="title">Vraag</label>
    <input id="title" name="title" type="text" maxlength="255" class="block w-full" value="{{ old('title', $question->title ?? '') }}" required>
    @error('title') <p class="text-red-700">{{ $message }}</p> @enderror
</div>
<div>
    <label for="description">Toelichting</label>
    <textarea id="description" name="description" maxlength="1000" class="block w-full">{{ old('description', $question->description ?? '') }}</textarea>
    @error('description') <p class="text-red-700">{{ $message }}</p> @enderror
</div>
