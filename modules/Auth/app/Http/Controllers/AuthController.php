<?php

namespace Modules\Auth\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public $client;

    private $auth_url;

    public function __construct()
    {
        $this->auth_url = env('USERS_AUTH_URI');
    }

    // public function register(Request $request)
    // {
    //     $this->redirect($request);
    // }

    public function login(Request $request)
    {
        return response()->json(['message' => 'Login successful']);
    }
}
