<?php

namespace Modules\OAuth\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\OAuth\App\Models\Client;
use Modules\User\App\Gateways\UsersGateway;
use Modules\User\App\Models\User;

class OAuthController extends Controller
{
    public Client $client;

    public function __construct(Request $request)
    {
        $this->client = new Client;
        // dd($request->all());
    }

    /**
     * Authorization code
     */
    public function redirect(Request $request)
    {
        $users = config('services.users');

        // Users service
        if (empty($users)) {
            Log::error('"Users service" is not found or misconfigured.', []);
            abort(403, '"Users service" is not found or misconfigured.');
        }

        // Client Authentication (if the request has a client_id and is coming from outside the app)
        if ($request->has('client_id')) {
            $this->client = Client::find($request->get('client_id'));
            if ($this->client === null) {
                Log::error('Client not found.', ['client_id' => $request->get('client_id')]);
                abort(404, 'Client not found.');
            } else {
                session()->put('client_id', $state = $this->client->id);
            }
        }

        // security code
        $request->session()->put('state', $state = md5(Str::random(128)));

        $query = http_build_query([
            'client_id' => $users['client_id'],
            'redirect_uri' => $users['callback_uri'],
            'response_type' => 'code',
            'scope' => '',
            'state' => $state,
            'prompt' => 'consent', // "none", "consent", or "login"
        ]);

        return redirect("{$users['auth_uri']}/authorize?{$query}");
    }

    /**
     * Authorization token
     */
    public function callback(Request $request)
    {
        if ($request->session()->has('users_token')) {
            return $this->store();
        }

        $user = config('services.users');

        // Abort if the session doesn't match.
        $state = $request->session()->pull('state');
        abort_unless(strlen($state) && $state === $request->state, 403, 'Session expired. Login again!');

        $response = Http::asForm()->post("{$user['auth_uri']}/token", [
            'grant_type' => 'authorization_code',
            'client_id' => $user['client_id'],
            'client_secret' => $user['client_secret'],
            'redirect_uri' => $user['callback_uri'],
            'code' => $request->code,
        ]);

        if ($response->ok()) {
            $request->session()->put('users_token', $response = $response->json());
            $request->session()->put('access_token', $response['access_token']);

            return $this->store();
        }

        return redirect(route('oauth.redirect'))->with('status', 'Login failed');
    }

    /**
     * Store the user details
     */
    protected function store()
    {
        // Get user profile
        $request = (new UsersGateway)->user();
        if ($request['status'] === 'error') {
            return redirect(route('oauth.redirect'))->with('error', 'Login failed: '.$request['message']);
        }

        $data = $request['data'];

        // Create a user account
        try {
            $user = User::updateOrCreate([
                'phone' => $data['user']['phone'],
            ], [
                'id' => $data['user']['id'],
                'first_name' => $data['user']['first_name'],
                'last_name' => $data['user']['last_name'],
                'username' => $data['user']['username'],
                'email' => $data['user']['email'],
                // 'remember_token' => session('access_token'),
            ]);
        } catch (QueryException $e) {
            if ($e->errorInfo[1] === 1062) { // 1062 is the error code for duplicate entry in MySQL
                echo 'Error: Username already exists. Please choose a different username.';
            } else {
                echo 'An error occurred: '.$e->getMessage();
            }

            // // Delete existing user record
            // $existing = User::withTrashed()
            //     ->whereNot('id', $data['id'])
            //     ->orWhere('username', $data['username'])
            //     ->orWhere('phone', $data['phone'])
            //     ->orWhere('email', $data['email'])
            //     ->forceDelete();

            Log::info($e->getMessage());
        }

        // Login user
        Auth::login($user, true);

        // Dashboard
        // return redirect(route('dashboard'))->with('status', 'Login successfully!');
    }

    protected function refresh()
    {
        $user = config('services.users');

        $response = Http::asForm()->post("{$user['auth_uri']}/token", [
            'grant_type' => 'refresh_token',
            'refresh_token' => 'the-refresh-token',
            'client_id' => $user['client_id'],
            'client_secret' => $user['client_secret'],
            'scope' => '',
        ]);

        return $response->json();
    }
}
