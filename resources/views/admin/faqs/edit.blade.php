<x-layout title="FAQ bewerken">
    <h1 class="text-2xl font-bold">FAQ bewerken</h1>
    <form method="POST" action="{{ route('admin.faqs.update', $faq) }}" class="mt-6 space-y-4">
        @csrf
        @method('PATCH')
        @include('admin.faqs._form')
        <button class="rounded bg-indigo-600 px-4 py-2 text-white">Bijwerken</button>
    </form>
</x-layout>
