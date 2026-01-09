<form method="post" action="{{ route('contact.store') }}" class="space-y-6">
    @csrf

    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Naam') }}</label>
        <input id="name" name="name" type="text" required maxlength="255" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" :value="old('name')" />
        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="email" class="block text-sm font-medium text-gray-700">{{ __('Email') }}</label>
        <input id="email" name="email" type="email" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" :value="old('email')" />
        @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="subject" class="block text-sm font-medium text-gray-700">{{ __('Onderwerp') }}</label>
        <input id="subject" name="subject" type="text" maxlength="255" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" :value="old('subject')" />
        @error('subject') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="message" class="block text-sm font-medium text-gray-700">{{ __('Bericht') }}</label>
        <textarea id="message" name="message" required maxlength="5000" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('message') }}</textarea>
        @error('message') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white">
        {{ __('Verstuur') }}
    </button>
</form>

<script>
    document.querySelector('form').addEventListener('submit', function(e) {
        const email = document.getElementById('email').value;
        const message = document.getElementById('message').value;
        
        if (!email.includes('@')) {
            e.preventDefault();
            alert('Voer een geldig e-mailadres in');
            return false;
        }
        
        if (message.length < 10) {
            e.preventDefault();
            alert('Bericht moet minstens 10 karakters zijn');
            return false;
        }
    });
</script>
