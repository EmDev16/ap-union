<x-layout title="Dashboard">
    <div style="color: #111827;">
        <h1 class="text-2xl font-bold mb-6">Your Feed</h1>
        
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
                <p class="text-gray-600">You don't follow anyone yet. Go to <a href="{{ route('explore') }}" class="text-indigo-600 underline font-semibold">Explore</a> and follow some users!</p>
            </div>
        @endif
    </div>
</x-layout>
