<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super-admin';
    case AdminPpid = 'admin-ppid';
    case Editor = 'editor';
    case Operator = 'operator';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::AdminPpid => 'Admin PPID',
            self::Editor => 'Editor',
            self::Operator => 'Operator',
        };
    }

    /**
     * @return list<string>
     */
    public function abilities(): array
    {
        return match ($this) {
            self::SuperAdmin => [
                'access-admin',
                'manage-users',
                'manage-settings',
                'manage-menus',
                'manage-pages',
                'manage-posts',
                'manage-announcements',
                'manage-documents',
                'manage-categories',
                'manage-services',
                'manage-faqs',
                'manage-units',
                'manage-contacts',
                'view-activity-logs',
            ],
            self::AdminPpid => [
                'access-admin',
                'manage-menus',
                'manage-pages',
                'manage-posts',
                'manage-announcements',
                'manage-documents',
                'manage-categories',
                'manage-faqs',
                'view-activity-logs',
            ],
            self::Editor => [
                'access-admin',
                'manage-pages',
                'manage-posts',
                'manage-announcements',
                'manage-faqs',
            ],
            self::Operator => [
                'access-admin',
                'manage-documents',
                'manage-services',
            ],
        };
    }

    public function can(string $ability): bool
    {
        return in_array($ability, $this->abilities(), true);
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
