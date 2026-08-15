<x-layout title="FAQ-categorieën beheren">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">FAQ-categorieën</h1>
        <a href="{{ route('admin.faq-categories.create') }}" class="rounded bg-indigo-600 px-4 py-2 text-white">Nieuwe categorie</a>
    </div>

    @if (session('status')) <p class="mt-4 text-green-700">{{ session('status') }}</p> @endif
    @if (session('error')) <p class="mt-4 text-red-700">{{ session('error') }}</p> @endif

    <ul class="mt-6 space-y-3">
        @forelse ($categories as $category)
            <li class="flex items-center justify-between rounded border p-4">
                <span>{{ $category->name }} ({{ $category->faqs_count }} FAQ’s)</span>
                <div class="flex gap-3">
                    <a class="underline" href="{{ route('admin.faq-categories.edit', $category) }}">Bewerk</a>
                    <form method="POST" action="{{ route('admin.faq-categories.destroy', $category) }}">
                        @csrf
                        @method('DELETE')
                        <button class="underline text-red-700">Verwijder</button>
                    </form>
                </div>
            </li>
        @empty
            <li>Nog geen categorieën.</li>
        @endforelse
    </ul>
</x-layout>
