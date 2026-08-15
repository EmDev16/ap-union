<x-layout title="Nieuwe FAQ">
    <h1 class="text-2xl font-bold">Nieuwe FAQ</h1>
    <form method="POST" action="{{ route('admin.faqs.store') }}" class="mt-6 space-y-4">
        @csrf
        @include('admin.faqs._form')
        <button class="rounded bg-indigo-600 px-4 py-2 text-white">Opslaan</button>
    </form>
</x-layout>
