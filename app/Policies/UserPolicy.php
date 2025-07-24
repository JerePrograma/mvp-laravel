<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $actor)
    {
        return $actor->is_admin;
    }

    public function view(User $actor, User $subject)
    {
        return $actor->id === $subject->id || $actor->is_admin;
    }

    public function create(User $actor)
    {
        return $actor->is_admin;
    }

    public function update(User $actor, User $subject)
    {
        return $actor->id === $subject->id || $actor->is_admin;
    }

    public function delete(User $actor, User $subject)
    {
        return $actor->is_admin;
    }
}
