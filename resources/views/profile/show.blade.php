@auth
    @if(auth()->id() !== $user->id)
        @if(auth()->user()->isFollowing($user))
            <form method="POST" action="{{ route('unfollow', $user) }}">
                @csrf
                @delete
                <button class="btn">Ontvolg</button>
            </form>
        @else
            <form method="POST" action="{{ route('follow', $user) }}">
                @csrf
                <button class="btn">Volg</button>
            </form>
        @endif
    @endif
@endauth
