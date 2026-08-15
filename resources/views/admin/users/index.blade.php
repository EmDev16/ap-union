<x-layout title="Gebruikers beheren">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">Gebruikers beheren</h1>
        <a href="{{ route('admin.users.create') }}" class="rounded bg-indigo-600 px-4 py-2 text-white">Nieuwe gebruiker</a>
    </div>

    @if (session('status')) <p class="mt-4 text-green-700">{{ session('status') }}</p> @endif
    @if (session('error')) <p class="mt-4 text-red-700">{{ session('error') }}</p> @endif

    <div class="mt-6 overflow-x-auto">
        <table class="w-full text-left">
            <thead><tr class="border-b"><th class="p-2">Naam</th><th class="p-2">Username</th><th class="p-2">E-mail</th><th class="p-2">Rol</th><th class="p-2">Actie</th></tr></thead>
            <tbody>
                @foreach ($users as $user)
                    <tr class="border-b">
                        <td class="p-2">{{ $user->name }}</td>
                        <td class="p-2">{{ $user->username ?: '—' }}</td>
                        <td class="p-2">{{ $user->email }}</td>
                        <td class="p-2">{{ $user->is_admin ? 'Admin' : 'Gebruiker' }}</td>
                        <td class="p-2">
                            @if (! $user->is(auth()->user()))
                                <form method="POST" action="{{ route('admin.users.toggle-admin', $user) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="underline">{{ $user->is_admin ? 'Adminrechten afnemen' : 'Maak admin' }}</button>
                                </form>
                            @else
                                <span class="text-sm text-gray-600">Eigen account</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $users->links() }}</div>
</x-layout>
