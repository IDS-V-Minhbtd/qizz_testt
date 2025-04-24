<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Services\UserService;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $data = $this->service->listUsersWithRoles();
        return view('users.index', $data);
    }

    public function create()
    {
        
        return view('users.create', compact('roles'));
    }

    public function store(UserRequest $request)
    {
        $this->service->create($request->validated());
        return redirect()->route('users.index')->with('success', 'User created successfully!');
    }

    public function edit(User $user)
    {
        
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(UserRequest $request, User $user)
    {
        $this->service->update($user, $request->validated());
        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $result = $this->service->delete($user);

        return redirect()->route('users.index')->with($result['status'] ? 'success' : 'error', $result['message']);
    }

}
