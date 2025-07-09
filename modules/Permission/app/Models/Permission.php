<?php

namespace Modules\Permission\App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use Modules\Permission\Database\Factories\PermissionFactory;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    use HasFactory, HasUuids, Searchable, SoftDeletes;

    public function getRouteKeyName()
    {
        return 'name';
    }

    public function toSearchableArray()
    {
        return [
            'name' => $this->name,
        ];
    }

    protected static function newFactory()
    {
        return PermissionFactory::new();
    }
}
