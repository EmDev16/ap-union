<div>
    <label for="name">Naam</label>
    <input id="name" name="name" type="text" value="{{ old('name', $faqCategory->name ?? '') }}" class="block w-full" required maxlength="255">
    @error('name') <p class="text-red-700">{{ $message }}</p> @enderror
</div>
