<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberSearchController extends Controller
{
    /**
     * Minimum number of characters before members are looked up.
     */
    private const MIN_TERM_LENGTH = 3;

    public function index(Request $request): View
    {
        $term = $this->term($request);

        if ($request->user()?->isAdmin()) {
            $members = $this->visibleTo($request->user())
                ->when($term !== null, fn (Builder $query) => $this->match($query, (string) $term))
                ->withCount('posts')
                ->with('interests')
                ->get();

            return view('search', [
                'members' => $members,
                'term' => $term,
                'minLength' => self::MIN_TERM_LENGTH,
                'showPostCounts' => true,
            ]);
        }

        $members = $term === null
            ? collect()
            : $this->search($request, $term)->with('interests')->get();

        return view('search', [
            'members' => $members,
            'term' => $term,
            'minLength' => self::MIN_TERM_LENGTH,
            'showPostCounts' => false,
        ]);
    }

    public function suggestions(Request $request): JsonResponse
    {
        $term = $this->term($request);

        if ($term === null) {
            return response()->json([]);
        }

        return response()->json(
            $this->search($request, $term)->limit(10)->get()->map(fn (User $user) => [
                'name' => $user->username ?: $user->name,
                'url' => route('profile.show', $user),
            ])
        );
    }

    private function term(Request $request): ?string
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
        ]);

        $term = trim($validated['q'] ?? '');

        return mb_strlen($term) >= self::MIN_TERM_LENGTH ? $term : null;
    }

    /**
     * @return Builder<User>
     */
    private function search(Request $request, string $term): Builder
    {
        return $this->match($this->visibleTo($request->user()), $term);
    }

    /**
     * Admin accounts stay out of the member search of ordinary users.
     *
     * @return Builder<User>
     */
    private function visibleTo(?User $viewer): Builder
    {
        return User::query()
            ->when(! $viewer?->isAdmin(), fn (Builder $query) => $query->where('is_admin', false))
            ->when($viewer !== null, fn (Builder $query) => $query->whereKeyNot($viewer->id))
            ->orderBy('name');
    }

    /**
     * @param  Builder<User>  $query
     * @return Builder<User>
     */
    private function match(Builder $query, string $term): Builder
    {
        return $query->where(function (Builder $query) use ($term) {
            $query->where('username', 'like', '%'.$term.'%')
                ->orWhere('name', 'like', '%'.$term.'%')
                ->orWhereHas('interests', fn (Builder $interests) => $interests->where('name', 'like', '%'.$term.'%'));
        });
    }
}
