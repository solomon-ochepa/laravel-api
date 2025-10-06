<?php

namespace Modules\User\App\Gateways;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\User\App\Models\User;

class UsersGateway
{
    protected string $api_uri;

    protected string $client_id;

    protected string $client_secret;

    protected string $auth_uri;

    public function __construct()
    {
        $this->api_uri = config('services.users.api_uri');
        $this->client_id = config('services.users.client_id');
        $this->client_secret = config('services.users.client_secret');
        $this->auth_uri = config('services.users.auth_uri');
    }

    /**
     * Get the user profile from the auth server.
     */
    public static function user(): array
    {
        $service = config('services.users');

        try {
            // return $this->get('user')->json();
            $response = Http::withToken(session('access_token'))->get("{$service['api_uri']}/user");

            return $response->json();
        } catch (\Throwable $th) {
            Log::error('Could not retrieve user profile.', ['response' => $th->getMessage()]);

            return [
                'status' => 'fail',
                'message' => 'Could not retrieve user profile.',
            ];
        }
    }

    public function get(string $endpoint)
    {
        return Http::withToken(session('access_token'))->get("{$this->api_uri}/{$endpoint}");
    }

    public function sync(User $user): array
    {
        if (! $user->isDirty()) {
            return [
                'status' => 'error',
                'message' => 'No changes made to the user.',
            ];
        }

        try {
            $url = "{$this->api_uri}/users/{$user->id}";

            $response = Http::withToken(session('access_token'))->put($url, $user->getDirty());

            // Error: Unable to sync user.
            if ($response->failed()) {
                return [
                    'status' => 'error',
                    'message' => __('Unable to sync user.'),
                ];
            }

            $changes = array_filter($response->json('data.user'), fn ($value, $key) => in_array($key, array_keys($user->getDirty())), 1);

            // Error: The user was synced, but the data was not modified.
            if ($changes !== $user->getDirty()) {
                Log::error('The user was synced, but the data was not modified.', [
                    'request' => $user->getDirty(),
                    'response' => $response->json(),
                ]);

                return [
                    'status' => 'error',
                    'message' => __('The user was synced, but the data was not modified.'),
                ];
            }

            return [
                'status' => 'success',
                'data' => $response->json('data'),
            ];
        } catch (Exception $e) {
            Log::error('User could not be sync: '.$e->getMessage(), [
                'exception' => $e,
            ]);

            return [
                'status' => 'error',
                'message' => __('Could not sync user.'),
            ];
        }
    }
}
