<?php

namespace App\Support;

use Illuminate\Support\Facades\Session;

class Modal
{
    public static function erro(string $titulo, string $mensagem, string $botao = 'Entendi'): void
    {
        self::flash('error', $titulo, $mensagem, $botao);
    }

    public static function aviso(string $titulo, string $mensagem, string $botao = 'Entendi'): void
    {
        self::flash('warning', $titulo, $mensagem, $botao);
    }

    public static function sucesso(string $titulo, string $mensagem, string $botao = 'Continuar'): void
    {
        self::flash('success', $titulo, $mensagem, $botao);
    }

    private static function flash(string $tipo, string $titulo, string $mensagem, string $botao): void
    {
        Session::flash('modal', compact('tipo', 'titulo', 'mensagem', 'botao'));
    }
}