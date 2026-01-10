@foreach($feed as $post)
    {{ $post->content }}
@endforeach

{{ $feed->links() }}
