<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Enums\PapelUtilizador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    public function index()
    {
        $admins = User::where("role", PapelUtilizador::ADMIN)
            ->orderBy("created_at", "desc")
            ->paginate(10);

        return view("super-admin.admins.index", compact("admins"));
    }

    public function create()
    {
        return view("super-admin.admins.criar");
    }

    public function store(Request $request)
    {
        $request->validate([
            "name"     => ["required", "string", "max:255"],
            "email"    => ["required", "email", "unique:users,email"],
            "password" => ["required", "confirmed", Password::min(8)],
        ], [
            "name.required"     => "O nome é obrigatório.",
            "email.required"    => "O email é obrigatório.",
            "email.unique"      => "Este email já está registado.",
            "password.required" => "A senha é obrigatória.",
            "password.confirmed"=> "A confirmação da senha não coincide.",
        ]);

        User::create([
            "name"      => $request->name,
            "email"     => $request->email,
            "password"  => Hash::make($request->password),
            "role"      => PapelUtilizador::ADMIN,
            "is_active" => true,
        ]);

        return redirect()
            ->route("super-admin.admins.index")
            ->with("success", "Administrador criado com sucesso.");
    }

    public function edit(User $admin)
    {
        abort_if($admin->role !== PapelUtilizador::ADMIN, 403);

        return view("super-admin.admins.editar", compact("admin"));
    }

    public function update(Request $request, User $admin)
    {
        abort_if($admin->role !== PapelUtilizador::ADMIN, 403);

        $request->validate([
            "name"      => ["required", "string", "max:255"],
            "email"     => ["required", "email", "unique:users,email,{$admin->id}"],
            "is_active" => ["boolean"],
        ], [
            "name.required"  => "O nome é obrigatório.",
            "email.required" => "O email é obrigatório.",
            "email.unique"   => "Este email já está em uso.",
        ]);

        $admin->update([
            "name"      => $request->name,
            "email"     => $request->email,
            "is_active" => $request->boolean("is_active"),
        ]);

        return redirect()
            ->route("super-admin.admins.index")
            ->with("success", "Administrador actualizado com sucesso.");
    }

    public function destroy(User $admin)
    {
        abort_if($admin->role !== PapelUtilizador::ADMIN, 403);

        $admin->delete();

        return redirect()
            ->route("super-admin.admins.index")
            ->with("success", "Administrador removido com sucesso.");
    }
}