<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UserRequest;
use App\Services\UserService;
use App\Services\ResultService;
use App\Http\Resources\UserResource;
use App\Http\Resources\ResultResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserApiController extends Controller
{
    protected $userService;
    protected $resultService;

    public function __construct(UserService $userService, ResultService $resultService)
    {
        $this->middleware('auth:sanctum')->except(['index']);
        $this->userService = $userService;
        $this->resultService = $resultService;
        Log::info('UserApiController instantiated');
    }

    /**
     * Get list of users
     */
    public function index()
    {
        Log::info('UserApiController@index called');
        $users = $this->userService->listUsers();
        Log::info('User list fetched', ['count' => $users->total()]);
        return UserResource::collection($users);
    }

    /**
     * Get data for creating a user
     */
    public function create()
    {
        Log::info('UserApiController@create called');
        return response()->json([
            'success' => true,
            'message' => 'Ready to create a new user.'
        ], 200);
    }

    /**
     * Store a new user
     */
    public function store(UserRequest $request)
    {
        Log::info('UserApiController@store called');
        $user = $this->userService->createUser($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'User created successfully.',
            'data' => new UserResource($user)
        ], 201);
    }

    /**
     * Get data for editing a user
     */
    public function edit($id)
    {
        Log::info("UserApiController@edit called for user ID: $id");
        $user = $this->userService->getUserById($id);
        if (!$user) {
            Log::warning("User not found with ID: $id");
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => new UserResource($user)
        ], 200);
    }

    /**
     * Update a user
     */
    public function update(UserRequest $request, $id)
    {
        Log::info("UserApiController@update called for user ID: $id");
        $user = $this->userService->updateUser($id, $request->validated());
        if (!$user) {
            Log::warning("User not found with ID: $id");
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
        Log::info("User updated successfully", ['user_id' => $id]);
        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => new UserResource($user)
        ], 200);
    }

    /**
     * Delete a user
     */
    public function destroy($id)
    {
        Log::info("UserApiController@destroy called for user ID: $id");
        $deleted = $this->userService->delete($id);
        if (!$deleted) {
            Log::warning("User not found with ID: $id");
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
        Log::info("User deleted successfully", ['user_id' => $id]);
        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ], 200);
    }

    /**
     * Get results for a user
     */
    public function showResults($id)
    {
        $user = $this->userService->getUserById($id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
        $results = $this->resultService->getResultsByUserId($id);
        return response()->json([
            'success' => true,
            'data' => [
                'user' => new UserResource($user),
                'results' => ResultResource::collection($results)
            ]
        ], 200);
    }

    /**
     * Get user profile
     */
    public function profile()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn cần đăng nhập để xem trang cá nhân.'
            ], 401);
        }
        return response()->json([
            'success' => true,
            'data' => new UserResource($user)
        ], 200);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn cần đăng nhập để cập nhật thông tin cá nhân.'
            ], 401);
        }

        $updatedUser = $this->userService->updateProfile($user->id, $request);
        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thông tin thành công.',
            'data' => new UserResource($updatedUser)
        ], 200);
    }

    /**
     * Delete user profile
     */
    public function deleteProfile()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn cần đăng nhập để xóa tài khoản.'
            ], 401);
        }

        $this->userService->deleteProfile($user->id);
        Auth::logout();
        return response()->json([
            'success' => true,
            'message' => 'Tài khoản đã được xóa thành công.'
        ], 200);
    }

    /**
     * Get user quiz history
     */
    public function history()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn cần đăng nhập để xem lịch sử làm bài.'
            ], 401);
        }

        $results = $this->resultService->getResultsByUserId($user->id);
        return response()->json([
            'success' => true,
            'data' => ResultResource::collection($results)
        ], 200);
    }
}