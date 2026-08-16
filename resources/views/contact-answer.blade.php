<x-layout title="Answer to your message">
    <h1 class="text-2xl font-bold">{{ $contact->subject }}</h1>
    <p class="mt-2 text-sm text-gray-600">
        Sent on {{ $contact->created_at->format('d/m/Y H:i') }} · {{ ucfirst($contact->type ?: 'question') }}
    </p>

    <article class="mt-6 rounded border p-4">
        <h2 class="font-semibold">Your message</h2>
        <p class="mt-2 whitespace-pre-line">{{ $contact->message }}</p>
    </article>

    <article class="mt-4 rounded border p-4">
        <h2 class="font-semibold">Answer from the AP Union team</h2>
        @if ($contact->reply)
            <p class="mt-1 text-sm text-gray-600">{{ $contact->replied_at?->format('d/m/Y H:i') }}</p>
            <p class="mt-2 whitespace-pre-line">{{ $contact->reply }}</p>
        @else
            <p class="mt-2">Your message has not been answered yet.</p>
        @endif
    </article>

    <a href="{{ route('contact.create') }}" class="mt-6 inline-block underline">Send another message</a>
</x-layout>
