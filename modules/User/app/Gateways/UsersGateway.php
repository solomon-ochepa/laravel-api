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

    public static function auth(): array
    {
        try {
            $service = config('services.users');

            $response = Http::asForm()
                ->timeout(5)
                ->retry(2, 100)
                ->post("{$service['auth_uri']}/token", [
                    'grant_type' => 'client_credentials',
                    'client_id' => $service['client_id'],
                    'client_secret' => $service['client_secret'] ?? '',
                    'scope' => '',
                ]);

            return [
                'status' => 'success',
                'data' => $response->json(),
            ];
        } catch (\Throwable $th) {
            Log::error('Could not authenticate user.', [
                'message' => $th->getMessage(),
                'exception' => $th,
            ]);

            return [
                'status' => 'error',
                'message' => __('Could not authenticate user.'),
            ];
        }
    }

    /**
     * Get or validate an access token
     */
    public static function token(?string $token): ?array
    {
        try {
            if (! $token) {
                $auth = self::auth();

                return ($auth['status'] == 'success') ? $auth : null;
            }

            $response = Http::withToken($token)->get(config('services.users.api_uri').'/user');

            if (! $response->successful()) {
                Log::error('Token validation failed', [
                    'status_code' => $response->status(),
                    'response' => $response->body(),
                ]);

                return null;
            }

            return $response->json();
        } catch (Exception $e) {
            Log::error('Failed to get access token from server.', [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);

            return [
                'status' => 'error',
                'message' => __('Failed to get access token from server.'),
            ];
        }
    }

    /**
     * Validate token with Users service via introspection
     */
    public static function check(string $token): bool
    {
        try {
            return (bool) self::token($token);
        } catch (\Exception $e) {
            Log::error('Token validation error', [
                'exception' => $e->getMessage(),
                'token_prefix' => substr($token, 0, 20).'...',
            ]);

            return false;
        }
    }
}
