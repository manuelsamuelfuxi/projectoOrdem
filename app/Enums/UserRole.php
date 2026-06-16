<?php

namespace App\Enums;

enum UserRole: string
{
    case SUPER_ADMIN = 'super_admin';
    case ADMIN = 'admin';

    public function label(): string
    {
        return match($this) {
            self::SUPER_ADMIN => 'Super Administrador',
            self::ADMIN => 'Administrador',
        };
    }

    public function permissions(): array
    {
        return match($this) {
            self::SUPER_ADMIN => [
                'create_admin',
                'view_all_applications',
                'approve_application',
                'issue_documents',
                'manage_news',
                'view_financial_reports',
                'system_configuration',
                'delete_any_application'
            ],
            self::ADMIN => [
                'view_all_applications',
                'verify_payments',
                'manage_news',
                'request_correction',
                'view_reports'
            ],
        };
    }
}