<x-layout title="Explore">
    <div style="color: #111827;">
        <h1 class="text-2xl font-bold mb-6">Explore</h1>
        
        @if($posts->count() > 0)
            <div class="space-y-6">
                @foreach($posts as $post)
                    @include('posts.card', ['post' => $post])
                @endforeach
            </div>
            
            <div class="mt-8">
                {{ $posts->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-gray-600">No posts yet. Be the first!</p>
            </div>
        @endif
    </div>
</x-layout>
