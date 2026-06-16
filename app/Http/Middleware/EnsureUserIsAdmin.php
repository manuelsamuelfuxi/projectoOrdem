<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
{
    if (!auth()->check()) {
        abort(403, 'Acesso negado.');
    }

    /** @var \App\Models\User $user */
    $user = auth()->user();

    if (!$user->isAdmin()) {
        abort(403, 'Acesso negado.');
    }

    if (!$user->isSuperAdmin() && !$user->is_active) {
        auth()->logout();
        return redirect()->route('login')
            ->with('error', 'A sua conta foi desactivada. Contacte o administrador.');
    }

    return $next($request);
}
}