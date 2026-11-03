<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Editor = 'editor';
    case User = 'user';

    public function canAccessAdmin(): bool
    {
        return $this !== self::User;
    }
}
