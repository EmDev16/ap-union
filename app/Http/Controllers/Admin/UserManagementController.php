<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserByAdminRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(): View
    {
        $this->authorize('manage', User::class);

        return view('admin.users.index', ['users' => User::orderBy('name')->paginate(15)]);
    }

    public function create(): View
    {
        $this->authorize('manage', User::class);

        return view('admin.users.create');
    }

    public function store(StoreUserByAdminRequest $request): RedirectResponse
    {
        $this->authorize('manage', User::class);

        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $data['is_admin'] = $request->boolean('is_admin');

        User::create($data);

        return to_route('admin.users.index')->with('status', 'Gebruiker aangemaakt.');
    }

    public function toggleAdmin(User $user): RedirectResponse
    {
        $this->authorize('manage', User::class);

        if ($user->is(auth()->user())) {
            return back()->with('error', 'Je kunt je eigen adminrechten niet wijzigen.');
        }

        if ($user->is_admin && User::where('is_admin', true)->count() === 1) {
            return back()->with('error', 'De laatste admin kan niet gedegradeerd worden.');
        }

        $user->update(['is_admin' => ! $user->is_admin]);

        return back()->with('status', 'Gebruikersrechten aangepast.');
    }
}
