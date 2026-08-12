<x-layout title="Dashboard">
    <section class="rounded bg-white p-6 shadow" style="color: #111827;">
        <h1 class="text-2xl font-bold">Welkom terug, {{ auth()->user()->username ?: auth()->user()->name }}.</h1>
        <p class="mt-2">Deel ideeën die mensen op een oprechte manier samenbrengen.</p>
        <a href="{{ route('posts.create') }}" class="mt-5 inline-block rounded bg-indigo-600 px-4 py-2" style="color: #ffffff;">Maak een post</a>
        <a href="{{ route('profile.show', auth()->user()) }}" class="mt-5 ml-3 inline-block underline">Bekijk mijn profiel</a>
    </section>
</x-layout>
