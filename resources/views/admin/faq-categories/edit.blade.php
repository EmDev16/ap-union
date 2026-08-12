<x-layout title="FAQ-categorie bewerken">
    <h1 class="text-2xl font-bold">FAQ-categorie bewerken</h1>
    <form method="POST" action="{{ route('admin.faq-categories.update', $faqCategory) }}" class="mt-6 space-y-4">
        @csrf
        @method('PATCH')
        @include('admin.faq-categories._form')
        <button class="rounded bg-indigo-600 px-4 py-2 text-white">Bijwerken</button>
    </form>
</x-layout>
