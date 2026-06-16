<?php

namespace App\Policies;

use App\Models\User;

class AdminPolicy
{
    public function acessarAdmin(User $user): bool
    {
        return $user->isAdmin() || $user->isSuperAdmin();
    }

    public function acessarSuperAdmin(User $user): bool
    {
        return $user->isSuperAdmin();
    }
}
