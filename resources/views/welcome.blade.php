<x-layout title="Home">
    @php
        $notifications = $notifications ?? [
            [
                'title' => 'Notification 1',
                'description' => 'This is a brief description of the notification. It gives you an idea of what the notification is about.',
            ],
            [
                'title' => 'Notification 2',
                'description' => 'This is a brief description of the notification. It gives you an idea of what the notification is about.',
            ],
            [
                'title' => 'Notification 3',
                'description' => 'This is a brief description of the notification. It gives you an idea of what the notification is about.',
            ],
            [
                'title' => 'Notification 4',
                'description' => 'This is a brief description of the notification. It gives you an idea of what the notification is about.',
            ],
        ];

        $posts = $posts ?? [
            [
                'title' => 'Post Title 1 / Following A',
                'description' => 'This is a brief description of the post content. It gives you an idea of what the post is about.',
            ],
            [
                'title' => 'Post Title 2 / Following B',
                'description' => 'This is a brief description of the post content. It gives you an idea of what the post is about. But this post has a video with it.',
            ],
            [
                'title' => 'Post Title 3 / Following C',
                'description' => 'This is a brief description of the post content. It gives you an idea of what the post is about.',
            ],
            [
                'title' => 'Post Title 4 / Following D',
                'description' => 'This is a brief description of the post content. It gives you an idea of what the post is about. But this post has pictures with it.',
            ],
        ];

        $questions = $questions ?? [
            [
                'title' => 'Question 1',
                'description' => 'This is a brief description of the question. It gives you an idea of what the question is about.',
            ],
            [
                'title' => 'Question 2',
                'description' => 'This is a brief description of the question. It gives you an idea of what the question is about.',
            ],
            [
                'title' => 'Question 3',
                'description' => 'This is a brief description of the question. It gives you an idea of what the question is about.',
            ],
            [
                'title' => 'Question 4',
                'description' => 'This is a brief description of the question. It gives you an idea of what the question is about.',
            ],
        ];
    @endphp

    <style>
        .home-layout {
            display: grid;
            width: calc(100vw - 48px);
            margin-left: 50%;
            transform: translateX(-50%);
            grid-template-columns: minmax(180px, 1fr) minmax(0, 896px) minmax(180px, 1fr);
            gap: 24px;
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
                <h2 class="home-sidebar-title">Notifications</h2>
                <p class="home-muted">
                    New followers, likes, comments, and other interactions with your posts.
                </p>
            </div>

            <ul class="home-list">
                @foreach ($notifications as $notification)
                    <li class="home-side-box">
                        <h3 class="home-item-title">{{ data_get($notification, 'title') }}</h3>
                        <p class="home-small-text">{{ data_get($notification, 'description') }}</p>
                    </li>
                @endforeach
            </ul>
        </aside>

        <main class="home-main">
            <div>
                <h1 class="home-main-title">Posts</h1>
                <p class="home-muted">
                    Posts from accounts you follow, in chronological order.
                </p>
            </div>

            <ul class="home-post-list">
                @foreach ($posts as $post)
                    <li class="home-post-box">
                        <h3 class="home-post-title">{{ data_get($post, 'title') }}</h3>
                        <p class="home-muted">{{ data_get($post, 'description') }}</p>
                    </li>
                @endforeach
            </ul>
        </main>

        <aside class="home-sidebar">
            <div>
                <h2 class="home-sidebar-title">Questions</h2>
                <p class="home-muted">
                    New questions, answered questions, and other question activity.
                </p>
            </div>

            <ul class="home-list">
                @foreach ($questions as $question)
                    <li class="home-side-box">
                        <h3 class="home-item-title">{{ data_get($question, 'title') }}</h3>
                        <p class="home-small-text">{{ data_get($question, 'description') }}</p>
                    </li>
                @endforeach
            </ul>
        </aside>
    </div>
</x-layout>
