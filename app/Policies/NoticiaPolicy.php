<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Noticia;

class NoticiaPolicy
{
    public function criar(User $user): bool
    {
        return $user->isAdmin() || $user->isSuperAdmin();
    }

    public function atualizar(User $user, Noticia $noticia): bool
    {
        return $user->isAdmin() || $user->isSuperAdmin();
    }

    public function publicar(User $user, Noticia $noticia): bool
    {
        return $user->isAdmin() || $user->isSuperAdmin();
    }
}
