<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Services\UserService;
use App\Services\ResultService;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    protected UserService $userService;
    protected ResultService $resultService;

    public function __construct(UserService $userService, ResultService $resultService)
    {
        $this->userService = $userService;
        $this->resultService = $resultService;
        Log::info('UserController instantiated');
    }

    public function index()
    {
        Log::info('UserController@index called');

        $users = $this->userService->listUsers();

        Log::info('User list fetched', ['count' => $users->total()]);

        return view('admin.user.index', compact('users'));
    }

    public function create()
    {
        Log::info('UserController@create called');
        return view('admin.user.create');
    }

    public function store(UserRequest $request)
    {
        Log::info('UserController@store called');

        $data = $request->validated();

        if (!isset($data['role_id'])) {
            $data['role_id'] = 2; // Default role
        }

        $data['username'] = $data['name']; // Nếu DB dùng username

        $this->userService->create($data);

        return redirect()->route('admin.user.index')->with('success', 'User created successfully.');
    }

    public function edit($id)
    {
        Log::info("UserController@edit called for user ID: $id");

        $user = $this->userService->getUserById($id);
        if (!$user) {
            Log::warning("User not found with ID: $id");
            abort(404, 'User not found');
        }

        return view('admin.user.edit', compact('user'));
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
        $user = $this->userService->getUserById($id);
        $results = $this->resultService->getResultsByUserId($id);

        return view('admin.user.result', compact('user', 'results'));
    }

    // Hiển thị profile người dùng hiện tại
    public function profile()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để xem trang cá nhân.');
        }
        return view('profile', compact('user'));
    }

    // Cập nhật profile (AJAX hoặc form thường)
    public function updateProfile(\Illuminate\Http\Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Bạn cần đăng nhập để cập nhật thông tin cá nhân.'], 401);
        }

        $data = $request->only(['name', 'email', 'password', 'avatar']);
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'avatar' => 'nullable|image|max:2048',
        ];
        $validator = \Validator::make($data, $rules);
        if ($validator->fails()) {
            // Nếu là AJAX thì trả về JSON, nếu không thì redirect
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar');
        } else {
            unset($data['avatar']);
        }

        try {
            $this->userService->updateProfile($user->id, $data);
            $updatedUser = $this->userService->getUserById($user->id);
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'name' => $updatedUser->name,
                        'email' => $updatedUser->email,
                        'avatar_url' => $updatedUser->avatar ? asset('storage/' . $updatedUser->avatar) : asset('storage/avatars/default-avatar.png'),
                    ]
                ]);
            }
            return redirect()->route('profile')->with('success', 'Cập nhật thông tin thành công.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Cập nhật thất bại: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Cập nhật thất bại: ' . $e->getMessage());
        }
    }

    // Xóa profile (tài khoản)
    public function deleteProfile()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Bạn cần đăng nhập để xóa tài khoản.'], 401);
        }

        try {
            if ($this->userService->profileDelete($user->id)) {
                auth()->logout();
                return response()->json(['success' => true, 'message' => 'Tài khoản đã được xóa thành công.', 'redirect' => route('home')]);
            }
            return response()->json(['success' => false, 'message' => 'Xóa tài khoản thất bại.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra khi xóa tài khoản.']);
        }
    }
    public function history()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để xem lịch sử làm bài.');
        }

        $results = $this->resultService->getResultsByUserId($user->id);

        return view('history', compact('results'));
    }
}
