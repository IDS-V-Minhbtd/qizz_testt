<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Services\UserService;
use App\Services\ResultService;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    protected UserService $userService; // Correct property name
    protected ResultService $resultService;

    public function __construct(UserService $userService, ResultService $resultService)
    {
        $this->userService = $userService; // Initialize property
        $this->resultService = $resultService; // Initialize property
        Log::info('UserController instantiated');
    }

    public function index()
    {
        Log::info('UserController@index called');

        $users = $this->userService->listUsers(); // returns paginator

        Log::info('User list fetched', ['count' => $users->total()]);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        Log::info('UserController@create called');
        return view('admin.users.create');
    }

    public function store(UserRequest $request)
    {
        Log::info('UserController@store called');

        $data = $request->validated();

        // Gộp thêm role_id nếu cần
        if (!$request->has('role_id')) {
            $data['role_id'] = 2; // hoặc Constant::MEMBER_ROLE;
        }

        // Nếu có username (hoặc name), xử lý lại theo đúng database
        $data['username'] = $data['name']; // nếu DB dùng username

        $this->userService->create($data);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit($id)
    {
        Log::info("UserController@edit called for user ID: $id");

        $user = $this->userService->getUserById($id);
        if (!$user) {
            Log::warning("User not found with ID: $id");
            abort(404, 'User not found');
        }

        return view('admin.users.edit', compact('user'));
    }

    public function update(UserRequest $request, $id)
    {
        $data = $request->validated();
        Log::info("UserController@update called for user ID: $id", ['data' => $data]);

        try {
            if ($this->userService->update($id, $data)) {
                Log::info("User updated successfully", ['user_id' => $id]);
                return redirect()->route('admin.users.index')->with('success', 'User updated successfully');
            }
            Log::warning("Failed to update user", ['user_id' => $id]);
            return redirect()->back()->with('error', 'Failed to update user');
        } catch (\Exception $e) {
            Log::error('User update exception', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to update user');
        }
    }

    public function destroy($id)
    {
        Log::info("UserController@destroy called for user ID: $id");

        try {
            $this->userService->delete($id);
            Log::info("User deleted successfully", ['user_id' => $id]);
            return redirect()->route('admin.users.index')->with('success', 'User deleted successfully');
        } catch (\Exception $e) {
            Log::error('User deletion failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to delete user');
        }
    }
    public function showResults($id)
    {
        $user = $this->userService->getUserById($id); // Use correct property
        $results = $this->resultService->getResultsByUserId($id);

        return view('admin.users.result', compact('user', 'results'));
    }
}
