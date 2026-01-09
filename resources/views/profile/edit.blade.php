<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profiel bewerken') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <form method="post" action="{{ route('profiles.update', $user->id) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="username" :value="__('Gebruikersnaam')" />
                        <x-text-input id="username" name="username" type="text" class="mt-1 block w-full" :value="old('username', $user->profile?->username)" maxlength="50" />
                        <x-input-error class="mt-2" :messages="$errors->get('username')" />
                    </div>

                    <div>
                        <x-input-label for="bio" :value="__('Bio')" />
                        <textarea id="bio" name="bio" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" maxlength="1000">{{ old('bio', $user->profile?->bio) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('bio')" />
                    </div>

                    <div>
                        <x-input-label for="birthday" :value="__('Geboortedatum')" />
                        <x-text-input id="birthday" name="birthday" type="date" class="mt-1 block w-full" :value="old('birthday', $user->profile?->birthday)" />
                        <x-input-error class="mt-2" :messages="$errors->get('birthday')" />
                    </div>

                    <div>
                        <x-input-label for="avatar" :value="__('Avatar')" />
                        <input id="avatar" name="avatar" type="file" class="mt-1 block w-full" accept="image/*" />
                        <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Opslaan') }}</x-primary-button>
                        @if (session('success'))
                            <p class="text-sm text-green-600 dark:text-green-400">{{ session('success') }}</p>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Client-side validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const bio = document.getElementById('bio').value;
            if (bio && bio.length > 1000) {
                e.preventDefault();
                alert('Bio mag niet langer dan 1000 karakters zijn');
                return false;
            }
        });
    </script>
</x-app-layout>
