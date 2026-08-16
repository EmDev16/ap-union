<x-layout title="Messages">
    <style>
        .messages-layout {
            width: min(calc(100vw - 48px), var(--page-wide-width));
            margin-left: 50%;
            transform: translateX(-50%);
            display: grid;
            gap: var(--page-gap);
            align-items: start;
        }

        .messages-layout.is-open {
            grid-template-columns: minmax(0, 1fr) minmax(0, 2fr);
        }

        .messages-thread {
            display: grid;
            gap: 12px;
            max-height: 520px;
            overflow-y: auto;
        }

        .message-row {
            display: flex;
        }

        .message-row.is-mine {
            justify-content: flex-end;
        }

        .message-bubble {
            border: 1px solid #d1d5db;
            background: #ffffff;
            padding: 12px;
            overflow-wrap: anywhere;
            max-width: 80%;
        }

        .message-bubble.is-mine {
            background: #eef2ff;
        }

        .message-avatar {
            width: 32px;
            height: 32px;
            border-radius: 9999px;
            object-fit: cover;
            background: #d1d5db;
        }

        .message-unread {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 24px;
            height: 24px;
            padding: 0 6px;
            border-radius: 9999px;
            background: #4f46e5;
            color: #ffffff;
            font-size: 12px;
        }

        @media (max-width: 900px) {
            .messages-layout,
            .messages-layout.is-open {
                width: 100%;
                margin-left: 0;
                transform: none;
                grid-template-columns: 1fr;
            }
        }
    </style>

    <h1 class="text-2xl font-bold">Messages</h1>

    <p class="text-base leading-7 text-[#1b1b18] dark:text-[#000000]">
        This is the messages page. Here you can view and manage your messages with other users. You can also start new
        conversations and keep track of your message history.
    </p>

    @guest
        <div class="mt-4 p-4 border bg-white/5">
            <h3 class="text-lg font-medium">Sign in to start a conversation</h3>
            <p class="text-gray-600 mt-2">
                Your conversations appear here once you are signed in.
                <a href="{{ route('login') }}" class="underline">Log in</a>
                @if (Route::has('register'))
                    or <a href="{{ route('register') }}" class="underline">create an account</a>
                @endif
                to message other members.
            </p>
        </div>
    @else
        <div class="messages-layout mt-6 {{ $conversation ? 'is-open' : '' }}">
            <section>
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold">Your Conversations</h2>

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <span>Filter{{ $filter === 'unread' ? ': unread' : '' }}</span>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <a href="{{ route('messages') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">All</a>
                            <a href="{{ route('messages', ['filter' => 'unread']) }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Unread</a>
                        </x-slot>
                    </x-dropdown>
                </div>

                <a href="{{ route('messages.create') }}"
                    class="mt-4 inline-flex items-center justify-center w-10 h-10 rounded-full bg-indigo-600 text-white text-xl hover:bg-indigo-700"
                    title="Start a new conversation">+</a>

                <ul class="space-y-4 mt-4">
                    @forelse ($conversations as $item)
                        @php
                            $partner = $item->partnerFor(auth()->user());
                            $unread = $item->unreadCountFor(auth()->user());
                            $latest = $item->visibleMessagesFor(auth()->user())->last();
                            $preview = $latest === null
                                ? 'No messages yet'
                                : (filled($latest->body) ? Str::limit($latest->body, 40) : 'Picture');
                        @endphp
                        <li class="p-4 border bg-white/5 {{ $conversation && $conversation->is($item) ? 'border-indigo-500' : '' }}">
                            <a href="{{ route('messages.show', $item) }}" class="flex items-center justify-between gap-4">
                                <span class="flex items-center gap-3">
                                    @if ($partner?->profile_photo)
                                        <img src="{{ asset('storage/' . $partner->profile_photo) }}"
                                            alt="{{ $partner->username ?: $partner->name }}" class="message-avatar">
                                    @else
                                        <span class="message-avatar block"></span>
                                    @endif
                                    <span>
                                        <span class="text-lg font-medium block">
                                            {{ $partner?->username ?: $partner?->name }}
                                        </span>
                                        <span class="text-gray-500 text-sm">
                                            {{ $preview }}
                                        </span>
                                    </span>
                                </span>
                                @if ($unread > 0)
                                    <span class="message-unread">{{ $unread }}</span>
                                @endif
                            </a>
                        </li>
                    @empty
                        <li class="p-4 border bg-white/5">
                            <p class="text-gray-600">
                                {{ $filter === 'unread' ? 'No unread conversations.' : 'You have no conversations yet.' }}
                            </p>
                        </li>
                    @endforelse
                </ul>
            </section>

            @if ($conversation)
                @php
                    $partner = $conversation->partnerFor(auth()->user());
                @endphp
                <section class="p-4 border bg-white/5">
                    <div class="flex items-center justify-between gap-4">
                        <h2 class="text-xl font-semibold flex items-center gap-3">
                            @if ($partner?->profile_photo)
                                <img src="{{ asset('storage/' . $partner->profile_photo) }}"
                                    alt="{{ $partner->username ?: $partner->name }}" class="message-avatar">
                            @else
                                <span class="message-avatar block"></span>
                            @endif
                            <a href="{{ route('profile.show', $partner) }}" class="hover:underline">
                                {{ $partner?->username ?: $partner?->name }}
                            </a>
                        </h2>

                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="text-gray-500 hover:text-gray-700" title="Conversation settings"
                                    aria-label="Conversation settings">&#9881;</button>
                            </x-slot>
                            <x-slot name="content">
                                <form method="POST" action="{{ route('messages.destroy', $conversation) }}"
                                    onsubmit="return confirm('Delete this conversation for you? The other person keeps it.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Delete conversation for me
                                    </button>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>

                    <div class="messages-thread mt-4" data-message-thread>
                        @foreach ($conversation->visibleMessagesFor(auth()->user()) as $message)
                            <div class="message-row {{ $message->user_id === auth()->id() ? 'is-mine' : '' }}">
                                <div class="message-bubble {{ $message->user_id === auth()->id() ? 'is-mine' : '' }}"
                                    data-message-id="{{ $message->id }}">
                                    @if ($message->replyTo)
                                        <p class="text-xs text-gray-500 border-l-2 border-gray-300 pl-2 mb-2">
                                            {{ $message->replyTo->user->username ?: $message->replyTo->user->name }}:
                                            {{ Str::limit($message->replyTo->body, 60) }}
                                        </p>
                                    @endif

                                    @if ($message->body)
                                        <p class="text-gray-900 whitespace-pre-line">{{ $message->body }}</p>
                                    @endif

                                    @if ($message->image_path)
                                        <img src="{{ Storage::url($message->image_path) }}" alt="Message picture"
                                            class="mt-2 max-w-full h-auto">
                                    @endif

                                    <div class="flex items-center justify-between gap-4 mt-2">
                                        <span class="text-xs text-gray-500">{{ $message->created_at->format('d/m/Y H:i') }}</span>
                                        <button type="button" class="text-xs text-indigo-600 underline"
                                            data-reply-to="{{ $message->id }}"
                                            data-reply-label="{{ Str::limit($message->body, 40) }}">Reply</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <form method="POST" action="{{ route('messages.reply', $conversation) }}"
                        enctype="multipart/form-data" class="mt-8 space-y-2">
                        @csrf
                        <input type="hidden" name="reply_to_id" value="" data-reply-input>

                        <p hidden data-reply-preview class="text-sm text-gray-600">
                            Replying to <span data-reply-preview-text></span>
                            <button type="button" class="underline" data-reply-cancel>cancel</button>
                        </p>

                        <textarea name="body" rows="3" placeholder="Write a message."
                            class="w-full px-4 py-2 border focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('body') }}</textarea>
                        <x-input-error :messages="$errors->get('body')" />

                        <input type="file" name="image" accept="image/*" class="hidden" id="message-image"
                            data-image-input>
                        <x-input-error :messages="$errors->get('image')" />

                        <div class="flex items-center justify-end gap-3">
                            <span class="text-sm text-gray-500" data-image-name hidden></span>

                            <label for="message-image" class="cursor-pointer text-xl text-gray-500 hover:text-gray-700"
                                title="Add a picture" aria-label="Add a picture">&#128206;</label>

                            <button type="submit"
                                class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600">Send</button>
                        </div>
                    </form>

                    <script>
                        (function () {
                            const input = document.querySelector('[data-reply-input]');
                            const preview = document.querySelector('[data-reply-preview]');
                            const previewText = document.querySelector('[data-reply-preview-text]');
                            const thread = document.querySelector('[data-message-thread]');

                            document.querySelectorAll('[data-reply-to]').forEach(function (button) {
                                button.addEventListener('click', function () {
                                    input.value = button.dataset.replyTo;
                                    previewText.textContent = button.dataset.replyLabel;
                                    preview.hidden = false;
                                });
                            });

                            document.querySelector('[data-reply-cancel]').addEventListener('click', function () {
                                input.value = '';
                                preview.hidden = true;
                            });

                            const imageInput = document.querySelector('[data-image-input]');
                            const imageName = document.querySelector('[data-image-name]');

                            imageInput.addEventListener('change', function () {
                                imageName.textContent = imageInput.files.length ? imageInput.files[0].name : '';
                                imageName.hidden = imageInput.files.length === 0;
                            });

                            thread.scrollTop = thread.scrollHeight;
                        })();
                    </script>
                </section>
            @endif
        </div>
    @endguest
</x-layout>
