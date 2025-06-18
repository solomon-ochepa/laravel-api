<?php

namespace Modules\Auth\App\Models;

use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Laravel\Passport\Client as PassportClient;
use Spatie\Permission\Traits\HasRoles;

class Client extends PassportClient implements AuthorizableContract
{
    use Authorizable, HasRoles;

    // public $guard_name = 'api';

    public function guardName()
    {
        return 'api';
    }
}
