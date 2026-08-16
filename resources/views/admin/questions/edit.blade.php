<x-layout title="Vraag aanpassen">
    <h1 class="text-2xl font-bold">Vraag aanpassen</h1>
    <form method="POST" action="{{ route('admin.questions.update', $question) }}" class="mt-6 space-y-4">
        @csrf
        @method('PATCH')
        @include('admin.questions._form')
        <button class="rounded bg-indigo-600 px-4 py-2 text-white">Opslaan</button>
    </form>
</x-layout>
