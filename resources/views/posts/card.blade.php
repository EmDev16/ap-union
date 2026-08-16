<div id="post-{{ $post->id }}" class="border border-gray-300 rounded-lg p-4 bg-white shadow-sm scroll-mt-24">
    <!-- Post Header -->
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
            @if($post->user->profile_photo)
                <img src="{{ asset('storage/' . $post->user->profile_photo) }}" alt="{{ $post->user->username ?: $post->user->name }}" class="w-8 h-8 rounded-full object-cover">
            @else
                <div class="w-8 h-8 bg-gray-300 rounded-full"></div>
            @endif
            <div>
                <a href="{{ route('profile.show', $post->user) }}" class="font-bold text-gray-900 hover:underline">{{ $post->user->username ?: $post->user->name }}</a>
                <a href="{{ route('posts.show', $post) }}" class="text-xs text-gray-600 hover:underline">{{ $post->created_at->diffForHumans() }}</a>
            </div>
        </div>
        @auth
            @if(auth()->user()->isAdmin())
                @if($post->isUnderReview())
                    <form action="{{ route('admin.posts.unreview', $post) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm px-3 py-1 bg-gray-300 text-gray-900 rounded hover:bg-gray-400 font-semibold">In review</button>
                    </form>
                @else
                    <form action="{{ route('admin.posts.review', $post) }}" method="POST" class="inline flex items-center gap-2">
                        @csrf
                        <input type="text" name="reason" maxlength="255" placeholder="Reason"
                            class="text-sm px-2 py-1 border border-gray-300 rounded">
                        <button type="submit" class="text-sm px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 font-semibold">Report</button>
                    </form>
                @endif
            @elseif($post->user_id !== auth()->id())
                @if(auth()->user()->following()->where('following_id', $post->user_id)->exists())
                    <form action="{{ route('users.unfollow', $post->user) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm px-3 py-1 bg-gray-300 text-gray-900 rounded hover:bg-gray-400 font-semibold">Following</button>
                    </form>
                @else
                    <form action="{{ route('users.follow', $post->user) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-sm px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700 font-semibold">Follow</button>
                    </form>
                @endif
            @endif
        @endauth
    </div>

    @if($post->isUnderReview())
        <p class="mb-3 text-sm text-red-700">
            This post is under review and hidden from other members.
        </p>
    @endif

    <!-- Post Content -->
    @if($post->content)
        <p class="text-gray-900 mb-4">{{ $post->content }}</p>
    @endif

    <!-- Media -->
    @if($post->media->count() > 0)
        <div class="mb-4 grid gap-2" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));">
            @foreach($post->media as $media)
                @if($media->type === 'image')
                    <img src="{{ Storage::url($media->path) }}" alt="Post media" class="rounded max-w-full h-auto">
                @elseif($media->type === 'video')
                    <video class="rounded max-w-full h-auto" controls>
                        <source src="{{ Storage::url($media->path) }}" type="{{ $media->mime_type ?? 'video/mp4' }}">
                        Your browser does not support the video tag.
                    </video>
                @endif
            @endforeach
        </div>
    @endif

    <!-- Actions -->
    <div class="flex gap-6 text-gray-700 mb-4 border-b pb-2">
        @auth
            @if(auth()->user()->isAdmin())
                <span class="flex items-center gap-1 text-gray-400">
                    <span>🤍</span> <span class="text-sm">{{ $post->likes->count() }}</span>
                </span>
            @elseif(auth()->user()->likes()->where('post_id', $post->id)->exists())
                <form action="{{ route('posts.unlike', $post) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="flex items-center gap-1 hover:text-red-600 font-semibold text-red-600">
                        <span>❤️</span> <span class="text-sm">{{ $post->likes->count() }}</span>
                    </button>
                </form>
            @else
                <form action="{{ route('posts.like', $post) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="flex items-center gap-1 hover:text-red-600 font-semibold">
                        <span>🤍</span> <span class="text-sm">{{ $post->likes->count() }}</span>
                    </button>
                </form>
            @endif
        @else
            <button disabled class="flex items-center gap-1 text-gray-400">
                <span>🤍</span> <span class="text-sm">{{ $post->likes->count() }}</span>
            </button>
        @endauth

        <a href="{{ route('posts.show', $post) }}" class="flex items-center gap-1 hover:text-indigo-600 font-semibold">
            <span>💬</span> <span class="text-sm">{{ $post->comments->count() }}</span>
        </a>

        <button type="button" class="flex items-center gap-1 hover:text-blue-600 font-semibold" onclick="openShareModal({{ $post->id }})">
            <span>🔗</span> <span class="text-sm">Share</span>
        </button>

        @auth
            @if($post->user_id === auth()->id())
                <form action="{{ route('posts.destroy', $post) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="flex items-center gap-1 hover:text-red-600 font-semibold text-red-600">
                        <span>🗑️</span> <span class="text-sm">Delete</span>
                    </button>
                </form>
            @endif
        @endauth
    </div>

    <!-- Comments Section -->
    <div class="space-y-3">
        <!-- Add Comment Form -->
        @auth
            @unless(auth()->user()->isAdmin())
            <form action="{{ route('comments.store', $post) }}" method="POST" class="mb-4">
                @csrf
                <div class="flex gap-2">
                    <input type="text" name="content" placeholder="Add a comment..." maxlength="500" class="flex-1 border border-gray-300 rounded px-2 py-1 text-sm bg-white text-gray-900" required>
                    <button type="submit" class="px-3 py-1 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700 font-semibold">Post</button>
                </div>
                @error('content')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </form>
            @endunless
        @endauth

        <!-- Comments List -->
        @foreach($post->comments()->whereNull('parent_id')->orderBy('created_at', 'desc')->with('replies')->get() as $comment)
            <div id="comment-{{ $comment->id }}" class="border-l-2 border-gray-300 pl-3 py-2 scroll-mt-24">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <a href="{{ route('profile.show', $comment->user) }}" class="font-semibold text-sm text-gray-900 hover:underline">{{ $comment->user->username ?: $comment->user->name }}</a>
                        <p class="text-xs text-gray-600">{{ $comment->created_at->diffForHumans() }}</p>
                    </div>
                    @auth
                        @if($comment->user_id === auth()->id() || auth()->user()->isAdmin())
                            <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="inline" onsubmit="return confirm('Delete?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-semibold">×</button>
                            </form>
                        @endif
                    @endauth
                </div>
                <p class="text-sm text-gray-900 mt-1">{{ $comment->content }}</p>
                
                <!-- Replies -->
                @if($comment->replies->count() > 0)
                    <div class="mt-2 space-y-2">
                        @foreach($comment->replies as $reply)
                            <div id="comment-{{ $reply->id }}" class="border-l-2 border-gray-200 pl-3 py-1 scroll-mt-24">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <a href="{{ route('profile.show', $reply->user) }}" class="font-semibold text-xs text-gray-900 hover:underline">{{ $reply->user->username ?: $reply->user->name }}</a>
                                        <p class="text-xs text-gray-600">{{ $reply->created_at->diffForHumans() }}</p>
                                    </div>
                                    @auth
                                        @if($reply->user_id === auth()->id() || auth()->user()->isAdmin())
                                            <form action="{{ route('comments.destroy', $reply) }}" method="POST" class="inline" onsubmit="return confirm('Delete?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs text-red-500 hover:text-red-700">×</button>
                                            </form>
                                        @endif
                                    @endauth
                                </div>
                                <p class="text-sm text-gray-900">{{ $reply->content }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Reply Form -->
                @auth
                    <button type="button" class="text-xs text-indigo-600 hover:text-indigo-700 font-semibold mt-1" onclick="toggleReplyForm({{ $comment->id }})">
                        Reply
                    </button>
                    <form action="{{ route('comments.store', $post) }}" method="POST" class="mt-2 hidden" id="reply-form-{{ $comment->id }}">
                        @csrf
                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                        <div class="flex gap-2">
                            <input type="text" name="content" placeholder="Reply..." maxlength="500" class="flex-1 border border-gray-300 rounded px-2 py-1 text-xs bg-white text-gray-900" required>
                            <button type="submit" class="px-2 py-1 bg-indigo-600 text-white text-xs rounded hover:bg-indigo-700 font-semibold">Reply</button>
                            <button type="button" class="px-2 py-1 bg-gray-300 text-gray-900 text-xs rounded hover:bg-gray-400" onclick="toggleReplyForm({{ $comment->id }})">Cancel</button>
                        </div>
                    </form>
                @endauth
            </div>
        @endforeach
    </div>
</div>

<script>
function openShareModal(postId) {
    alert('Share via messages coming soon - you can share posts in private messages.');
}

function toggleReplyForm(commentId) {
    const form = document.getElementById('reply-form-' + commentId);
    form.classList.toggle('hidden');
}
</script>
