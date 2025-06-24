<?php

namespace Modules\Auth\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Modules\User\App\Models\User;

class TokenController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->user = auth('sanctum')->user();

        if (! $this->user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated.',
            ]);
        }

        $this->middleware(['auth:sanctum'])->only(['index']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if ($this->user->tokens->count()) {
            $tokens = $this->user->tokens->collect()->map(function ($token) {
                return $token->only([
                    'name',
                    'abilities',
                    'last_used_at',
                    'expires_at',
                    'created_at',
                    'updated_at',
                ]);
            });

            return response()->json([
                'status' => true,
                'data' => [
                    'tokens' => $tokens,
                ],
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Tokens not found.',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string'],
            'password' => ['required'],
            'ref' => ['required'],
        ]);

        // Validation failed
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ]);
        }

        // Find user
        $user = User::where('username', $request->username)
            ->orWhere('email', $request->username)
            ->orWhere('phone', trim($request->username, '+'))
            ->first();

        // Invalid credentials
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'The provided credentials are incorrect.',
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'access_token' => $user->createToken($request->ref)->plainTextToken,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
