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
<div>
    <label for="answers_publish_on">Antwoorden publiceren op</label>
    <p class="text-sm text-gray-600">Op deze dag komen alle antwoorden op deze vraag samen op de profielen van de leden.</p>
    <input id="answers_publish_on" name="answers_publish_on" type="date" class="block w-full"
        value="{{ old('answers_publish_on', optional($question->answers_publish_on ?? null)->format('Y-m-d')) }}">
    @error('answers_publish_on') <p class="text-red-700">{{ $message }}</p> @enderror
</div>
