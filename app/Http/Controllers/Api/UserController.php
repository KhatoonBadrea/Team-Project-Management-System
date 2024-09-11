<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    use ApiResponseTrait;

    protected $userService;

    /**
     * constractur to inject User Service Class
     * @param UserService $userService
     */

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
       
    }

    /**
     * Display a listing of the resource. 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $users = $this->userService->getAllUser();
        return $this->successResponse('this is all user', $users, 200);
    }

    /**
     * Store a newly created resource in storage.
     * @param StoreUserRequest $request
     * @return @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreUserRequest $request)
    {
        $validationdata = $request->validated();
        $user = $this->userService->create_user($validationdata);
        return response()->json($user, 201);
    }

    /** 
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = $this->userService->getUserById($id);
        if ($user) {
            return $this->successResponse('User found', $user, 200);
        } else {
            return $this->errorResponse('User not found', [], 404);
        }
    }

    /** 
     * Update the specified resource in storage.
     * @param UpdateUserRequest $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $validatedRequest = $request->validated();

        $updatedUserResource = $this->userService->updateUser($user, $validatedRequest);

        return $this->successResponse($updatedUserResource, 'User updated successfully.', 200);
    }

    /**
     * Remove the specified resource from storage.
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        $result = $this->userService->deleteUser($id);

        if ($result) {
            return $this->successResponse([], 'User deleted successfully.', 200);
        } else {
            return $this->errorResponse('User not found or could not be deleted.', [], 404);
        }
    }
}
