<?php

namespace Modules\User\App\Services;

use App\Helpers\JSend;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\User\App\Models\User;

class UserService
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
    public function register(Request $request) // : JsonResponse
    {
        $response = Http::asForm()
            ->post("{$this->api_uri}/users", [
                'grant_type' => 'client_credentials',
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                'scope' => '',
                'prompt' => 'login',
            ]);

        // try {
        // return $this->get('user')->json();
        // $response = Http::withToken(session('access_token'))->post("{$this->api_uri}/users", $request->all());
        // dd($response->body());

        // return $response->json();
        // } catch (\Throwable $th) {
        //     Log::error('Failed to retrieve user profile.', ['response' => $th->getMessage()]);

        //     return JSend::error('Failed to retrieve user profile.');
        // }
    }

    /**
     * Get the user profile from the auth server.
     */
    public function user(): array
    {
        try {
            // return $this->get('user')->json();
            $response = Http::withToken(session('access_token'))->get("{$this->api_uri}/user");

            return $response->json();
        } catch (\Throwable $th) {
            Log::error('Failed to retrieve user profile.', ['response' => $th->getMessage()]);

            return ['status' => 'error', 'message' => 'Failed to retrieve user profile.'];
        }
    }

    public function get(string $endpoint)
    {
        $token = $this->accessToken();
        if ($token['status'] == 'error') {
            return $token;
        }

        return Http::withToken(session('access_token'))->get("{$this->api_uri}/{$endpoint}");
    }

    public function sync(User $user): array
    {
        if (! $user->isDirty()) {
            return ['status' => 'error', 'message' => 'No changes made!'];
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

            // Error: The user was synced, but the data was not modified.
            $changes = array_filter($response->json('data.user'), fn ($value, $key) => in_array($key, array_keys($user->getDirty())), 1);
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
            Log::error("User sync error for {$user->id}: ".$e->getMessage(), [
                'exception' => $e,
            ]);

            return [
                'status' => 'error',
                'message' => __('Unable to sync user.'),
            ];
        }
    }

    protected function accessToken(): string|array
    {
        try {
            $response = Http::asForm()
                ->timeout(5)
                ->retry(2, 100)
                ->post("{$this->auth_uri}/token", [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->client_id,
                    'client_secret' => $this->client_secret,
                    'scope' => '',
                ]);

            return [
                'status' => 'success',
                'data' => $response->json(),
            ];
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
}
