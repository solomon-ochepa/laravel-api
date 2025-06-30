<?php

namespace Modules\User\App\Repositories;

use Modules\User\App\Models\User;

class UserRepository
{
    public function find(mixed $id): User
    {
        return User::where('id', $id)
            ->orWhere('email', $id)
            ->orWhere('username', $id)
            ->firstOrFail();
    }
}
