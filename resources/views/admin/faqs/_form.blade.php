<div>
    <label for="faq_category_id">Categorie</label>
    <select id="faq_category_id" name="faq_category_id" class="block w-full" required>
        <option value="">Kies een categorie</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}" @selected(old('faq_category_id', $faq->faq_category_id ?? '') == $category->id)>{{ $category->name }}</option>
        @endforeach
    </select>
    @error('faq_category_id') <p class="text-red-700">{{ $message }}</p> @enderror
</div>
<div>
    <label for="question">Vraag</label>
    <textarea id="question" name="question" class="block w-full" required>{{ old('question', $faq->question ?? '') }}</textarea>
    @error('question') <p class="text-red-700">{{ $message }}</p> @enderror
</div>
<div>
    <label for="answer">Antwoord</label>
    <textarea id="answer" name="answer" class="block w-full" required>{{ old('answer', $faq->answer ?? '') }}</textarea>
    @error('answer') <p class="text-red-700">{{ $message }}</p> @enderror
</div>
