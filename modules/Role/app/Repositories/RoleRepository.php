<?php

namespace Modules\Role\App\Repositories;

use Modules\Role\App\Models\Role;

class RoleRepository
{
    public function find(mixed $id): Role
    {
        return Role::where('id', $id)
            ->orWhere('name', $id)
            ->firstOrFail();
    }
}
