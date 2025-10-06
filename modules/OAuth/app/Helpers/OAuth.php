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

            $response = self::token($token);

            return [
                'status' => 'success',
                'data' => $response, // ->json(),
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
            $service = config('services.users');

            $response = Http::asForm()
                ->timeout(5)
                ->retry(2, 100)
                ->post("{$service['auth_uri']}/token", self::credentials());

            if (! $response->successful()) {
                Log::error('Token request failed', [
                    'status_code' => $response->status(),
                    'response' => $response->body(),
                ]);

                return [
                    'status' => 'error',
                    'message' => __('Token request failed'),
                ];
            }

            return [
                'status' => 'success',
                'data' => $response->json(),
            ];
        } catch (Exception $e) {
            Log::error('Token request failed', [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);

            return [
                'status' => 'error',
                'message' => __('Token request failed'),
            ];
        }
    }

    private static function credentials(): array
    {
        $service = config('services.users');

        return [
            'grant_type' => 'client_credentials',
            'client_id' => $service['client_id'],
            'client_secret' => $service['client_secret'] ?? '',
            'scope' => '',
        ];
    }
}
