<x-layout title="Notifications">
    <h1 class="text-2xl font-bold">Notifications</h1>

    <p class="text-base leading-7 text-[#1b1b18] dark:text-[#000000]">
        All your notifications of the last {{ $months }} months.
    </p>

    <ul class="space-y-4 mt-6">
        @forelse ($notifications as $notification)
            <li class="p-4 border bg-white/5 flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-medium">
                        <a href="{{ data_get($notification->data, 'url', route('home')) }}" class="hover:underline">
                            {{ data_get($notification->data, 'title') }}
                        </a>
                    </h2>
                    <p class="text-gray-600">{{ data_get($notification->data, 'description') }}</p>
                    <p class="text-sm text-gray-400">{{ $notification->created_at->format('d/m/Y H:i') }}</p>
                </div>

                <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}"
                    onsubmit="return confirm('Delete this notification for good?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-800" title="Delete this notification"
                        aria-label="Delete this notification">&times;</button>
                </form>
            </li>
        @empty
            <li class="p-4 border bg-white/5">
                <p class="text-gray-600">You have no notifications.</p>
            </li>
        @endforelse
    </ul>

    <a href="{{ route('home') }}" class="inline-block mt-6 underline">Back to home</a>
</x-layout>
