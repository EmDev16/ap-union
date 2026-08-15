<x-layout title="Nieuwe gebruiker">
    <h1 class="text-2xl font-bold">Nieuwe gebruiker</h1>
    <form method="POST" action="{{ route('admin.users.store') }}" class="mt-6 space-y-4">
        @csrf
        <div><label for="name">Naam</label><input id="name" name="name" value="{{ old('name') }}" required class="block w-full"><x-input-error :messages="$errors->get('name')" /></div>
        <div><label for="username">Username</label><input id="username" name="username" value="{{ old('username') }}" required class="block w-full"><x-input-error :messages="$errors->get('username')" /></div>
        <div><label for="email">E-mail</label><input id="email" name="email" type="email" value="{{ old('email') }}" required class="block w-full"><x-input-error :messages="$errors->get('email')" /></div>
        <div><label for="password">Wachtwoord</label><input id="password" name="password" type="password" required class="block w-full"><x-input-error :messages="$errors->get('password')" /></div>
        <div><label for="password_confirmation">Bevestig wachtwoord</label><input id="password_confirmation" name="password_confirmation" type="password" required class="block w-full"></div>
        <label class="flex gap-2"><input name="is_admin" type="checkbox" value="1" @checked(old('is_admin'))> Maak deze gebruiker admin</label>
        <button class="rounded bg-indigo-600 px-4 py-2 text-white">Gebruiker aanmaken</button>
    </form>
</x-layout>
