<?php

namespace Modules\User\App\Http\Controllers\Api;

use App\Helpers\JSend;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\User\App\Http\Requests\CreateUserRequest;
use Modules\User\App\Http\Requests\UpdateUserRequest;
use Modules\User\App\Models\User;
use Modules\User\App\Resources\UserCollection;
use Throwable;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            return JSend::success(['users' => new UserCollection(User::paginate(100))]);
        } catch (Throwable $th) {
            Log::error('Unable to retrieve users.', [
                'message' => $th->getMessage(),
                'exception' => $th,
            ]);

            return JSend::error($th->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateUserRequest $request): JsonResponse
    {
        return JSend::success([]);
    }

    /**
     * Show the specified resource.
     */
    public function show(User $user): JsonResponse
    {
        return JSend::success([]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        return JSend::success([]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): JsonResponse
    {
        return JSend::success([]);
    }
}
