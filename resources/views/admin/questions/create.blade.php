<x-layout title="Nieuwe vraag">
    <h1 class="text-2xl font-bold">Nieuwe vraag</h1>
    <p class="mt-2 text-gray-600">Deze vraag verschijnt bij de leden rechts op hun homepagina.</p>
    <form method="POST" action="{{ route('admin.questions.store') }}" class="mt-6 space-y-4">
        @csrf
        @include('admin.questions._form')
        <button class="rounded bg-indigo-600 px-4 py-2 text-white">Opslaan</button>
    </form>
</x-layout>
