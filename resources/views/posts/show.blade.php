<x-layout title="Post">
    <div style="color: #111827;">
        <a href="{{ url()->previous() }}" class="inline-block mb-4 underline">Back</a>

        @include('posts.card', ['post' => $post])
    </div>
</x-layout>
