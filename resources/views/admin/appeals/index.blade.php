<x-layout title="Beroepen">
    <h1 class="text-2xl font-bold">Beroepen</h1>
    <p class="mt-2 text-gray-600">Motiveer je beslissing altijd, de gebruiker krijgt jouw uitleg als bericht.</p>

    @if (session('status')) <p class="mt-4 text-green-700">{{ session('status') }}</p> @endif

    <div class="mt-6 space-y-4">
        @forelse ($appeals as $appeal)
            <article id="appeal-{{ $appeal->id }}" class="rounded border p-4">
                <p class="text-sm text-gray-600">
                    {{ $appeal->user->username ?: $appeal->user->name }} ·
                    @switch($appeal->stage)
                        @case(\App\Models\PostAppeal::CONTEST) eerste betwisting @break
                        @case(\App\Models\PostAppeal::SECOND) tweede betwisting @break
                        @default laatste beroep na verwijdering
                    @endswitch
                    · {{ $appeal->created_at->format('d/m/Y H:i') }}
                </p>

                <p class="mt-2 whitespace-pre-line">{{ $appeal->post->content }}</p>

                @if ($appeal->post->review_reason)
                    <p class="mt-2 text-sm text-gray-600">Gemeld om: {{ $appeal->post->review_reason }}</p>
                @endif

                @if ($appeal->reason)
                    <p class="mt-2 text-sm">Reden van de gebruiker: {{ $appeal->reason }}</p>
                @endif

                @foreach ($appeal->post->appeals->where('resolved_at', '!=', null) as $earlier)
                    <p class="mt-2 text-sm text-gray-600">
                        Eerdere motivatie ({{ $earlier->admin?->username ?: $earlier->admin?->name }}):
                        {{ $earlier->response }}
                    </p>
                @endforeach

                <form method="POST" action="{{ route('admin.appeals.update', $appeal) }}" class="mt-3 space-y-2">
                    @csrf
                    @method('PATCH')
                    <label class="block text-sm">
                        Jouw motivatie
                        <textarea name="response" rows="3" required
                            class="mt-1 w-full rounded border border-gray-300 p-2"></textarea>
                    </label>

                    @if ($appeal->stage === \App\Models\PostAppeal::SECOND)
                        <label class="block text-sm">
                            Beslissing
                            <select name="decision" required class="mt-1 rounded border border-gray-300 p-2">
                                <option value="{{ \App\Models\PostAppeal::REMOVED }}">Post verwijderen</option>
                                <option value="{{ \App\Models\PostAppeal::RESTORED }}">Post terug online</option>
                            </select>
                        </label>
                    @elseif ($appeal->stage === \App\Models\PostAppeal::AFTER_DELETE)
                        <label class="block text-sm">
                            Beslissing
                            <select name="decision" required class="mt-1 rounded border border-gray-300 p-2">
                                <option value="{{ \App\Models\PostAppeal::UPHELD }}">Verwijdering behouden</option>
                                <option value="{{ \App\Models\PostAppeal::RESTORED }}">Post terug online</option>
                            </select>
                        </label>
                    @endif

                    @error('response') <p class="text-sm text-red-700">{{ $message }}</p> @enderror

                    <button class="rounded bg-indigo-600 px-4 py-2 text-white">Versturen</button>
                </form>
            </article>
        @empty
            <p>Geen open beroepen.</p>
        @endforelse
    </div>

    @if ($handled->isNotEmpty())
        <h2 class="mt-8 text-xl font-semibold">Afgehandeld</h2>
        <ul class="mt-2 space-y-1 text-sm text-gray-600">
            @foreach ($handled as $appeal)
                <li>
                    {{ $appeal->resolved_at->format('d/m/Y') }} ·
                    {{ $appeal->user->username ?: $appeal->user->name }} · {{ $appeal->decision }}
                </li>
            @endforeach
        </ul>
    @endif
</x-layout>
