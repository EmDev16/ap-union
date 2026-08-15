<x-layout title="FAQ beheren">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">FAQ’s beheren</h1>
        <a href="{{ route('admin.faqs.create') }}" class="rounded bg-indigo-600 px-4 py-2 text-white">Nieuwe FAQ</a>
    </div>

    @if (session('status')) <p class="mt-4 text-green-700">{{ session('status') }}</p> @endif

    <div class="mt-6 space-y-4">
        @forelse ($faqs as $faq)
            <article class="rounded border p-4">
                <p class="text-sm text-gray-600">{{ $faq->category->name }}</p>
                <h2 class="font-semibold">{{ $faq->question }}</h2>
                <p class="mt-1">{{ $faq->answer }}</p>
                <div class="mt-3 flex gap-3">
                    <a class="underline" href="{{ route('admin.faqs.edit', $faq) }}">Bewerk</a>
                    <form method="POST" action="{{ route('admin.faqs.destroy', $faq) }}">
                        @csrf
                        @method('DELETE')
                        <button class="underline text-red-700">Verwijder</button>
                    </form>
                </div>
            </article>
        @empty
            <p>Nog geen FAQ’s.</p>
        @endforelse
    </div>
</x-layout>
