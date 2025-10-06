<?php

namespace Modules\OAuth\App\Http\Middleware;

use App\Helpers\JSend;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Modules\User\App\Gateways\UsersGateway;
use Modules\User\App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class ValidateToken
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // Get the access token from the Authorization header
            $token = $request->bearerToken();

            if (! $token) {
                return response()->json([
                    'error' => 'missing_authorization_token',
                    'message' => 'Authorization token is required',
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Check cache first
            $cache_key = 'token_validation:'.md5($token);
            $response = Cache::get($cache_key);

            if (! $response) {
                // Validate token with Users service
                $response = UsersGateway::token($token);

                // Cache valid tokens for 5 minutes to reduce Users service calls
                if ($response['status'] == 'success') {
                    Cache::put($cache_key, $response, 300); // 5 minutes
                }
            }

            if ($response['status'] !== 'success') {
                return JSend::error('Token is invalid or expired', Response::HTTP_UNAUTHORIZED);
            }

            // Store user information in the request for controller access
            $request->merge([
                'auth_user' => $response['data']['user'],
                'auth_token' => $token,
            ]);

            // Add user to request for Laravel's auth system
            if (isset($response['data']['user'])) {
                $user = new User($response['data']['user']);
                $request->setUserResolver(function () use ($user) {
                    return $user;
                });
                Auth::guard('api')->setUser($user);
            }
        } catch (\Throwable $th) {
            // throw $th;
        }

        return $next($request);
    }
}
