<?php

namespace Modules\Role\App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use Modules\Role\Database\Factories\RoleFactory;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use HasFactory, HasUuids, Searchable, SoftDeletes;

    public function getRouteKeyName()
    {
        return 'name';
    }

    protected static function newFactory()
    {
        return RoleFactory::new();
    }
}
