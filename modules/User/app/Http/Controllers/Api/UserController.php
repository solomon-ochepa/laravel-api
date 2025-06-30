<?php

namespace Modules\User\App\Http\Controllers\Api;

use App\Helpers\JSend;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\User\App\Http\Requests\CreateUserRequest;
use Modules\User\App\Http\Requests\UpdateUserRequest;
use Modules\User\App\Models\User;
use Modules\User\App\Repositories\UserRepository;
use Modules\User\App\Resources\UserCollection;
use Modules\User\App\Resources\UserResource;
use Throwable;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            return JSend::success(['users' => new UserCollection(User::paginate($request->limit ?? 100))]);
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
    public function show(string $user_id): JsonResponse
    {
        try {
            $user = User::findOrFail($user_id);

            return JSend::success(['user' => new UserResource($user)]);
        } catch (ModelNotFoundException $e) {
            return JSend::fail(['message' => 'User not found']);
        } catch (Throwable $th) {
            Log::error('Unable to retrieve user.', [
                'message' => $th->getMessage(),
                'exception' => $th,
            ]);

            return JSend::error($th->getMessage());
        }
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
    public function destroy(string $id): JsonResponse
    {
        try {
            $user = (new UserRepository)->find($id);
            $user->delete();

            return JSend::success(['message' => 'User deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return JSend::fail(['message' => 'User not found']);
        } catch (\Throwable $e) {
            Log::error("Could not delete user - {$id}", ['exception' => $e->getMessage()]);

            return JSend::error('Could not delete user');
        }
    }
}
