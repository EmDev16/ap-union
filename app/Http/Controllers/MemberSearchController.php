<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberSearchController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
        ]);

        $term = $validated['q'] ?? null;

        $members = User::query()
            ->when($term, function ($query) use ($term) {
                $query->where(function ($query) use ($term) {
                    $query->where('username', 'like', '%'.$term.'%')
                        ->orWhere('name', 'like', '%'.$term.'%');
                });
            })
            ->withCount('posts')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('search', [
            'members' => $members,
            'term' => $term,
        ]);
    }
}
