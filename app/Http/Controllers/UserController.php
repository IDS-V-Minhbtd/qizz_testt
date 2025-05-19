<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Services\UserService;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    protected UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $users = $this->service->listUsers();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(UserRequest $request)
    {
        $data = $request->validated();
        Log::debug('Creating user with data:', $data);

        $this->service->create($data);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully!');
    }

    public function edit($id)
    {
        $user = $this->service->getUserById($id);
        if (!$user) {
            abort(404, 'User not found');
        }
        return view('admin.users.edit', compact('user'));
    }

    public function update(UserRequest $request, $id)
    {
        $data = $request->validated();
        Log::debug("Updating user ID $id with data:", $data);

        if ($this->service->update($id, $data)) {
            return redirect()->route('admin.users.index')->with('success', 'User updated successfully');
        }

        return redirect()->back()->with('error', 'Failed to update user');
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully');
    }
}
