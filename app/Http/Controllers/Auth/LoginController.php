<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware("guest")->except("logout");
    }

    /**
     * Redirect users after login based on their role.
     *
     * @return string
     */
    protected function redirectTo()
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            return route("super-admin.dashboard");
        }

        if ($user->isAdmin()) {
            return route("admin.dashboard");
        }

        return route("home");
    }
}