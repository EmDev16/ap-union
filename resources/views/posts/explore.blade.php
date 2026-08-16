<x-layout title="Explore">
    <div style="color: #111827;">
        <h1 class="text-2xl font-bold mb-6">Explore</h1>
        
        @if($posts->count() > 0)
            <div class="space-y-6">
                @foreach($posts as $post)
                    @include('posts.card', ['post' => $post])
                @endforeach
            </div>
            
            @auth
                <div class="mt-8">
                    {{ $posts->links() }}
                </div>
            @else
                <div class="mt-8 border border-gray-300 rounded-lg p-4 bg-white">
                    <p class="text-gray-700">
                        This is a sample of {{ $posts->count() }} posts.
                        <a href="{{ route('login') }}" class="underline">Log in</a>
                        @if (Route::has('register'))
                            or <a href="{{ route('register') }}" class="underline">create an account</a>
                        @endif
                        to keep exploring and to follow the members you like.
                    </p>
                </div>
            @endauth
        @else
            <div class="text-center py-12">
                <p class="text-gray-600">No posts yet. Be the first!</p>
            </div>
        @endif
    </div>
</x-layout>
