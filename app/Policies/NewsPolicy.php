<?php

namespace App\Policies;

use App\Models\News;
use App\Models\User;

class NewsPolicy
{
    public function viewAny(?User $user): bool { return true; }
    public function view(?User $user, News $news): bool { return true; }
    public function create(User $user): bool { return $user->is_admin; }
    public function update(User $user, News $news): bool { return $user->is_admin; }
    public function delete(User $user, News $news): bool { return $user->is_admin; }
}
