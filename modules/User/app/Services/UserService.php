<?php

namespace Modules\User\App\Services;

use Modules\User\App\Models\User;

class UserService
{
    public static function find(mixed $id): User
    {
        return User::where('id', $id)
            ->orWhere('email', $id)
            ->orWhere('username', $id)
            ->firstOrFail();
    }
}
