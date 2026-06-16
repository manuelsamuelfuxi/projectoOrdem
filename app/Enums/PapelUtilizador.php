<?php

namespace App\Enums;

enum PapelUtilizador: string
{
    case SUPER_ADMIN = 'super_admin';
    case ADMIN = 'admin';

    public function rotulo(): string
    {
        return match($this) {
            self::SUPER_ADMIN => 'Super Administrador',
            self::ADMIN => 'Administrador',
        };
    }

    public function permissoes(): array
    {
        return match($this) {
            self::SUPER_ADMIN => [
                'criar_admin',
                'ver_todos_pedidos',
                'aprovar_pedido',
                'emitir_documentos',
                'gerir_noticias',
                'ver_relatorios_financeiros',
                'configurar_sistema'
            ],
            self::ADMIN => [
                'ver_todos_pedidos',
                'verificar_pagamentos',
                'gerir_noticias',
                'solicitar_correccao',
                'ver_relatorios'
            ],
        };
    }
}