<x-layout title="Public Profile">
    <div class="max-w-7xl mx-auto py-12 sm:px-6 lg:px-8">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-xl space-y-4">
                @if ($user->profile_photo)
                    <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="{{ $user->name }}" class="h-32 w-32 rounded-full object-cover">
                @endif

                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $user->username ?: $user->name }}</h1>

                    @if ($user->username)
                        <p class="text-gray-600">{{ $user->name }}</p>
                    @endif
                </div>

                @if ($user->birthday)
                    <p class="text-gray-600">Birthday: {{ $user->birthday->format('F j, Y') }}</p>
                @endif

                @if ($user->about_me)
                    <p class="text-gray-800">{{ $user->about_me }}</p>
                @endif
            </div>
        </div>
    </div>
</x-layout>
