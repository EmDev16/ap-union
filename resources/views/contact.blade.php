<x-layout title="Contact">
    <h1 class="text-2xl font-bold">Contact</h1>
    <p class="mt-2">Heb je een vraag of feedback? Stuur ons een bericht.</p>
    @if (session('status')) <p class="mt-4 text-green-700">{{ session('status') }}</p> @endif
    <form method="POST" action="{{ route('contact.store') }}" class="mt-6 space-y-4">
        @csrf
        <div><label for="name">Naam</label><input id="name" name="name" value="{{ old('name') }}" required class="block w-full"><x-input-error :messages="$errors->get('name')" /></div>
        <div><label for="email">E-mail</label><input id="email" name="email" type="email" value="{{ old('email') }}" required class="block w-full"><x-input-error :messages="$errors->get('email')" /></div>
        <div><label for="subject">Onderwerp</label><input id="subject" name="subject" value="{{ old('subject') }}" required class="block w-full"><x-input-error :messages="$errors->get('subject')" /></div>
        <div><label for="message">Bericht</label><textarea id="message" name="message" required maxlength="5000" class="block w-full" rows="6">{{ old('message') }}</textarea><x-input-error :messages="$errors->get('message')" /></div>
        <button class="rounded bg-indigo-600 px-4 py-2 text-white">Verstuur</button>
    </form>
</x-layout>
