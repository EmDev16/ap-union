<x-layout title="Home">
    <style>
        .home-layout {
            display: grid;
            width: min(calc(100vw - 48px), var(--page-wide-width));
            margin-left: 50%;
            transform: translateX(-50%);
            grid-template-columns:
                minmax(var(--page-side-width), 1fr)
                minmax(0, var(--page-content-width))
                minmax(var(--page-side-width), 1fr);
            gap: var(--page-gap);
            align-items: start;
        }

        .home-sidebar {
            display: grid;
            gap: 16px;
        }

        .home-main {
            display: grid;
            gap: 16px;
        }

        .home-list {
            display: grid;
            gap: 12px;
        }

        .home-post-list {
            display: grid;
            gap: 16px;
        }

        .home-side-box {
            border: 1px solid #d1d5db;
            background: rgba(255, 255, 255, 0.5);
            padding: 12px;
            font-size: 14px;
        }

        .home-post-box {
            border: 1px solid #d1d5db;
            background: rgba(255, 255, 255, 0.5);
            padding: 20px;
            font-size: 16px;
        }

        .home-sidebar-title {
            font-size: 18px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .home-count-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 22px;
            height: 22px;
            padding: 0 6px;
            border-radius: 999px;
            background: #4f46e5;
            color: #ffffff;
            font-size: 12px;
            font-weight: 600;
        }

        .home-side-box-new {
            border-left: 3px solid #4f46e5;
            background: rgba(79, 70, 229, 0.06);
        }

        .home-main-title {
            font-size: 24px;
            font-weight: 600;
        }

        .home-item-title {
            font-size: 16px;
            font-weight: 500;
        }

        .home-post-title {
            font-size: 20px;
            font-weight: 600;
        }

        .home-muted {
            margin-top: 8px;
            color: #6b7280;
            line-height: 1.6;
        }

        .home-small-text {
            margin-top: 4px;
            color: #9ca3af;
            font-size: 14px;
            line-height: 1.4;
        }

        .question-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            z-index: 50;
        }

        .question-overlay[hidden] {
            display: none;
        }

        .question-overlay-card {
            background: #ffffff;
            border: 1px solid #d1d5db;
            padding: 24px;
            width: min(100%, 480px);
        }

        @media (max-width: 900px) {
            .home-layout {
                width: 100%;
                margin-left: 0;
                transform: none;
                grid-template-columns: 1fr;
            }
        }
    </style>

    <p class="mb-6 text-base leading-7 text-[#1b1b18] dark:text-[#000000]">
        Connect with people in a different and deeper way.
    </p>

    <div class="home-layout">
        <aside class="home-sidebar">
            <div>
                <h2 class="home-sidebar-title">
                    @auth
                        <a href="{{ route('notifications.index') }}" class="hover:underline">Notifications</a>
                        @if ($notificationCount > 0)
                            <span class="home-count-badge">{{ $notificationCount }}</span>
                        @endif
                    @else
                        Notifications
                    @endauth
                </h2>
                <p class="home-muted">
                    New followers, likes, comments, and other interactions with your posts.
                </p>
            </div>

            @guest
                <div class="home-side-box">
                    <h3 class="home-item-title">Nothing here yet</h3>
                    <p class="home-small-text">
                        Sign in to get notified when someone follows you or reacts to your posts.
                    </p>
                </div>
            @else
                <ul class="home-list">
                    @forelse ($notifications as $notification)
                        <li class="home-side-box home-side-box-new">
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="home-item-title">
                                    <a href="{{ data_get($notification->data, 'url', route('home')) }}" class="hover:underline">
                                        {{ data_get($notification->data, 'title') }}
                                    </a>
                                </h3>

                                <form method="POST" action="{{ route('notifications.dismiss', $notification->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-gray-500 hover:text-gray-900"
                                        title="Hide this notification"
                                        aria-label="Hide this notification">&times;</button>
                                </form>
                            </div>
                            <p class="home-small-text">{{ data_get($notification->data, 'description') }}</p>
                        </li>
                    @empty
                        <li class="home-side-box">
                            <p class="home-small-text">No new notifications.</p>
                        </li>
                    @endforelse
                </ul>
            @endguest
        </aside>

        <main class="home-main">
            @guest
                <div>
                    <h1 class="home-main-title">Welcome to AP Union</h1>
                    <p class="home-muted">
                        AP Union is a place for conversation and interest: you follow the people you care
                        about and read their posts in chronological order, without an algorithm in between.
                    </p>
                </div>

                <div class="home-post-box">
                    <h2 class="home-post-title">What you can do with an account</h2>
                    <ul class="home-muted" style="list-style: disc; padding-left: 20px;">
                        <li>Follow members and read their posts in your own chronological feed.</li>
                        <li>Post your own updates, comment on others and answer their questions.</li>
                        <li>Message other members and see your notifications.</li>
                    </ul>
                    <p class="home-muted">
                        Without an account you can still explore a sample of the posts, search members, read the FAQ
                        and the latest news, and contact us.
                    </p>
                    <p class="home-muted">
                        <a href="{{ route('login') }}" class="underline">Log in</a>
                        @if (Route::has('register'))
                            or <a href="{{ route('register') }}" class="underline">create an account</a>
                        @endif
                        to get started.
                    </p>
                </div>
            @else
                <div>
                    <h1 class="home-main-title">Posts</h1>
                    <p class="home-muted">
                        Posts from accounts you follow, in chronological order.
                    </p>
                </div>

                @if ($posts->count() > 0)
                    <div class="home-post-list">
                        @foreach ($posts as $post)
                            @include('posts.card', ['post' => $post])
                        @endforeach
                    </div>

                    <div>
                        {{ $posts->links() }}
                    </div>
                @else
                    <div class="home-post-box">
                        <p class="text-gray-600">
                            You don't follow anyone yet. Go to
                            <a href="{{ route('explore') }}" class="text-indigo-600 underline font-semibold">Explore</a>
                            and follow some users!
                        </p>
                    </div>
                @endif
            @endguest
        </main>

        <aside class="home-sidebar">
            <div>
                <h2 class="home-sidebar-title">
                    @auth
                        <a href="{{ route('questions.index') }}" class="hover:underline">Questions</a>
                    @else
                        Questions
                    @endauth
                </h2>
                <p class="home-muted">
                    New questions, answered questions, and other question activity.
                </p>
            </div>

            @guest
                <div class="home-side-box">
                    <h3 class="home-item-title">Join the discussion</h3>
                    <p class="home-small-text">
                        Questions from the community appear here once you are signed in.
                    </p>
                </div>
            @else
                <ul class="home-list">
                    @forelse ($questions as $question)
                        <li class="home-side-box">
                            <button type="button" class="question-open text-left"
                                data-question-title="{{ $question->title }}"
                                data-question-description="{{ $question->description }}"
                                data-question-url="{{ route('questions.show', $question) }}">
                                <h3 class="home-item-title">{{ $question->title }}</h3>
                                <p class="home-small-text">{{ Str::limit($question->description, 90) }}</p>
                            </button>
                        </li>
                    @empty
                        <li class="home-side-box">
                            <p class="home-small-text">No open questions.</p>
                        </li>
                    @endforelse
                </ul>
            @endguest
        </aside>
    </div>

    @auth
        <div class="question-overlay" id="question-overlay" hidden>
            <div class="question-overlay-card" id="question-overlay-card">
                <h2 class="home-post-title" id="question-overlay-title"></h2>
                <p class="home-muted" id="question-overlay-description"></p>
                <a href="#" id="question-overlay-link"
                    class="inline-block mt-4 rounded bg-indigo-600 px-4 py-2 text-white font-semibold hover:bg-indigo-700">
                    Respond
                </a>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const overlay = document.getElementById('question-overlay');
                const card = document.getElementById('question-overlay-card');
                const title = document.getElementById('question-overlay-title');
                const description = document.getElementById('question-overlay-description');
                const link = document.getElementById('question-overlay-link');

                document.querySelectorAll('.question-open').forEach(function (button) {
                    button.addEventListener('click', function () {
                        title.textContent = button.dataset.questionTitle;
                        description.textContent = button.dataset.questionDescription;
                        link.href = button.dataset.questionUrl;
                        overlay.hidden = false;
                    });
                });

                overlay.addEventListener('click', function (event) {
                    if (! card.contains(event.target)) {
                        overlay.hidden = true;
                    }
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape') {
                        overlay.hidden = true;
                    }
                });
            });
        </script>
    @endauth
</x-layout>
