<x-layout title="Contact">
    <h1 class="text-2xl font-bold">Contact</h1>
    <p class="mt-2">Heb je een vraag of feedback? Stuur ons een bericht.</p>
    @if (session('status')) <p class="mt-4 text-green-700">{{ session('status') }}</p> @endif
    <form method="POST" action="{{ route('contact.store') }}" class="mt-6 space-y-4">
        @csrf
        <div><label for="name">Naam</label><input id="name" name="name" value="{{ old('name') }}" required class="block w-full"><x-input-error :messages="$errors->get('name')" /></div>
        <div><label for="email">E-mail</label><input id="email" name="email" type="email" value="{{ old('email') }}" required class="block w-full"><x-input-error :messages="$errors->get('email')" /></div>
        <style>
            .contact-switch {
                display: inline-flex;
                border: 1px solid #d1d5db;
                border-radius: 9999px;
                overflow: hidden;
            }

            .contact-switch input {
                position: absolute;
                opacity: 0;
                width: 1px;
                height: 1px;
            }

            .contact-switch label {
                padding: 6px 16px;
                cursor: pointer;
                font-size: 14px;
            }

            .contact-switch input:checked + label {
                background: #4f46e5;
                color: #ffffff;
            }
        </style>

        <div>
            <span class="block">Waarover gaat het?</span>
            <div class="contact-switch mt-1">
                <input type="radio" id="type-question" name="type" value="question" @checked(old('type', 'question') === 'question')>
                <label for="type-question">Vraag</label>
                <input type="radio" id="type-feedback" name="type" value="feedback" @checked(old('type') === 'feedback')>
                <label for="type-feedback">Feedback</label>
            </div>
            <x-input-error :messages="$errors->get('type')" />
        </div>
        <div><label for="subject">Onderwerp</label><input id="subject" name="subject" value="{{ old('subject') }}" required class="block w-full"><x-input-error :messages="$errors->get('subject')" /></div>
        <div><label for="message">Bericht</label><textarea id="message" name="message" required maxlength="5000" class="block w-full" rows="6">{{ old('message') }}</textarea><x-input-error :messages="$errors->get('message')" /></div>
        <button class="rounded bg-indigo-600 px-4 py-2 text-white">Verstuur</button>
    </form>
</x-layout>
