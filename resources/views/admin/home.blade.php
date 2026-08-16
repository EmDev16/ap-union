<x-layout title="Admin">
    <style>
        .admin-layout {
            display: grid;
            width: min(calc(100vw - 48px), var(--page-wide-width));
            margin-left: 50%;
            transform: translateX(-50%);
            grid-template-columns:
                minmax(0, var(--page-content-width))
                minmax(var(--page-side-width), 1fr);
            gap: var(--page-gap);
            align-items: start;
        }

        .admin-main {
            display: grid;
            gap: 16px;
        }

        .admin-side {
            display: grid;
            gap: 16px;
        }

        .admin-box {
            border: 1px solid #d1d5db;
            background: rgba(255, 255, 255, 0.5);
            padding: 16px;
        }

        .admin-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 12px;
        }

        .admin-stat-value {
            font-size: 24px;
            font-weight: 600;
        }

        @media (max-width: 900px) {
            .admin-layout {
                width: 100%;
                margin-left: 0;
                transform: none;
                grid-template-columns: 1fr;
            }
        }
    </style>

    @if (session('status')) <p class="mb-4 text-green-700">{{ session('status') }}</p> @endif

    <div class="admin-layout">
        <main class="admin-main">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Contactberichten</h1>
                <a href="{{ route('admin.contacts.index') }}" class="underline">Alle berichten</a>
            </div>
            <p class="text-gray-600">{{ $openContacts }} berichten wachten nog op een antwoord.</p>

            @forelse ($contacts as $contact)
                <article class="admin-box">
                    <h2 class="font-semibold">{{ $contact->subject }}</h2>
                    <p class="text-sm text-gray-600">
                        {{ $contact->type === 'feedback' ? 'Feedback' : 'Vraag' }} ·
                        {{ $contact->name }} · {{ $contact->email }} ·
                        {{ $contact->created_at->format('d/m/Y H:i') }}
                    </p>
                    <p class="mt-2 whitespace-pre-line">{{ $contact->message }}</p>

                    @if ($contact->reply)
                        <p class="mt-3 whitespace-pre-line text-gray-700">
                            <span class="font-semibold">Antwoord:</span> {{ $contact->reply }}
                        </p>
                        <p class="text-sm text-green-700">Beantwoord op {{ $contact->replied_at?->format('d/m/Y H:i') }}</p>
                    @else
                        <form method="POST" action="{{ route('admin.contacts.reply', $contact) }}" class="mt-3 space-y-2">
                            @csrf
                            <label for="reply-{{ $contact->id }}" class="text-sm">Antwoord</label>
                            <textarea id="reply-{{ $contact->id }}" name="reply" maxlength="2000"
                                class="block w-full border border-gray-300 p-2" required></textarea>
                            @error('reply') <p class="text-red-700">{{ $message }}</p> @enderror
                            <button class="rounded bg-indigo-600 px-4 py-2 text-white">Antwoord versturen</button>
                        </form>
                    @endif
                </article>
            @empty
                <p class="admin-box">Nog geen berichten.</p>
            @endforelse

            <div class="admin-box">
                <h2 class="text-xl font-semibold">Cijfers</h2>
                <div class="admin-stats mt-3">
                    @foreach ($stats as $label => $value)
                        <div>
                            <p class="admin-stat-value">{{ $value }}</p>
                            <p class="text-sm text-gray-600">{{ $label }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="admin-box">
                <h2 class="text-xl font-semibold">Beheer</h2>
                <ul class="mt-2 space-y-1">
                    <li><a href="{{ route('admin.users.index') }}" class="underline">Gebruikers</a></li>
                    <li><a href="{{ route('admin.questions.index') }}" class="underline">Vragen</a></li>
                    <li><a href="{{ route('admin.posts.index') }}" class="underline">Posts in review</a></li>
                    <li><a href="{{ route('admin.appeals.index') }}" class="underline">Beroepen</a></li>
                    <li><a href="{{ route('admin.faqs.index') }}" class="underline">FAQ</a></li>
                    <li><a href="{{ route('admin.news.index') }}" class="underline">Latest News</a></li>
                </ul>
            </div>
        </main>

        <aside class="admin-side">
            <h2 class="text-lg font-semibold flex items-center gap-2">
                <a href="{{ route('notifications.index') }}" class="hover:underline">Notifications</a>
                @if ($notificationCount > 0)
                    <span class="nav-count-badge">{{ $notificationCount }}</span>
                @endif
            </h2>

            @forelse ($notifications as $notification)
                <div class="admin-box">
                    <div class="flex items-start justify-between gap-2">
                        <h3 class="font-medium">
                            <a href="{{ data_get($notification->data, 'url', route('home')) }}" class="hover:underline">
                                {{ data_get($notification->data, 'title') }}
                            </a>
                        </h3>
                        <form method="POST" action="{{ route('notifications.dismiss', $notification->id) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-gray-500 hover:text-gray-900"
                                title="Hide this notification" aria-label="Hide this notification">&times;</button>
                        </form>
                    </div>
                    <p class="text-sm text-gray-600">{{ data_get($notification->data, 'description') }}</p>
                </div>
            @empty
                <div class="admin-box">
                    <p class="text-sm text-gray-600">No new notifications.</p>
                </div>
            @endforelse
        </aside>
    </div>
</x-layout>
