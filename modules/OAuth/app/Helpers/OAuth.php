<?php

namespace Modules\OAuth\App\Helpers;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OAuth
{
    public static function check(?string $token = null): ?array
    {
        try {
            if ($token) {
                $response = Http::withToken($token)->get(config('services.users.api_uri').'/user');

                if (! $response->successful()) {
                    Log::error('Token validation failed', [
                        'status_code' => $response->status(),
                        'response' => $response->body(),
                    ]);

                    return null;
                }

                return $response->json();
            }

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
    public static function token(?string $token = null): ?array
    {
        try {
            $auth = self::check($token);

            return ($auth['status'] == 'success') ? $auth['data']['access_token]'] : null;
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
