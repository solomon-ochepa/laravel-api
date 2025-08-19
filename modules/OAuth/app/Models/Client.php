<?php

namespace Modules\OAuth\App\Models;

use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Laravel\Passport\Client as BaseClient;
use Spatie\Permission\Traits\HasRoles;

class Client extends BaseClient implements AuthorizableContract
{
    use Authorizable, HasRoles;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'owner_id',
        'owner_type',
        'name',
        'secret',
        'provider',
        'redirect_uris',
        'grant_types',
        'revoked',
    ];

    public $table = 'oauth_clients';

    public function guardName()
    {
        return 'api';
    }
}
