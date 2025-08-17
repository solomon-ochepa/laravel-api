<?php

namespace Modules\OAuth\App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

// use Modules\OAuth\Database\Factories\ClientFactory;

class Client extends Model
{
    use HasUuids;

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
}
