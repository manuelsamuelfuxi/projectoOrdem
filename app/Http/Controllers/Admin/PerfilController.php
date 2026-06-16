<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PerfilController extends Controller
{
    public function editar()
    {
        $utilizador = auth()->user();
        return view("admin.perfil.editar", compact("utilizador"));
    }

    public function atualizar(Request $request)
    {
        $utilizador = auth()->user();

        $request->validate([
            "name"  => ["required", "string", "max:255"],
            "email" => ["required", "email", "max:255", "unique:users,email,{$utilizador->id}"],
        ], [
            "name.required"  => "O nome é obrigatório.",
            "email.required" => "O email é obrigatório.",
            "email.email"    => "Introduza um email válido.",
            "email.unique"   => "Este email já está em uso.",
        ]);

        $utilizador->update([
            "name"  => $request->name,
            "email" => $request->email,
        ]);

        return redirect()
            ->route("admin.perfil.editar")
            ->with("success", "Perfil actualizado com sucesso.");
    }

    public function atualizarSenha(Request $request)
    {
        $request->validate([
            "senha_actual" => ["required"],
            "nova_senha"   => ["required", "confirmed", Password::min(8)],
        ], [
            "senha_actual.required" => "A senha actual é obrigatória.",
            "nova_senha.required"   => "A nova senha é obrigatória.",
            "nova_senha.confirmed"  => "A confirmação da senha não coincide.",
        ]);

        $utilizador = auth()->user();

        if (!Hash::check($request->senha_actual, $utilizador->password)) {
            return back()
                ->withErrors(["senha_actual" => "A senha actual está incorrecta."])
                ->withInput();
        }

        $utilizador->update([
            "password" => Hash::make($request->nova_senha),
        ]);

        return redirect()
            ->route("admin.perfil.editar")
            ->with("success", "Senha alterada com sucesso.");
    }
}