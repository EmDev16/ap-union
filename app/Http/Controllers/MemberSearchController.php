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

        $members = $term === null
            ? collect()
            : $this->search($term)->get();

        return view('search', [
            'members' => $members,
            'term' => $term,
            'minLength' => self::MIN_TERM_LENGTH,
        ]);
    }

    public function suggestions(Request $request): JsonResponse
    {
        $term = $this->term($request);

        if ($term === null) {
            return response()->json([]);
        }

        return response()->json(
            $this->search($term)->limit(10)->get()->map(fn (User $user) => [
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
    private function search(string $term)
    {
        return User::query()
            ->where(function ($query) use ($term) {
                $query->where('username', 'like', '%'.$term.'%')
                    ->orWhere('name', 'like', '%'.$term.'%');
            })
            ->orderBy('name');
    }
}
