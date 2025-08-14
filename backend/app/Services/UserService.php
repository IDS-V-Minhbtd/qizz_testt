<?php

namespace App\Services;

use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\ResultRepositoryInterface;
use App\Repositories\Interfaces\QuizRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserService
{
    protected UserRepositoryInterface $userRepo;
    protected ResultRepositoryInterface $resultRepo;
    protected QuizRepositoryInterface $quizRepo;

    public function __construct(
        UserRepositoryInterface $userRepo,
        ResultRepositoryInterface $resultRepo,
        QuizRepositoryInterface $quizRepo
    ) {
        $this->userRepo = $userRepo;
        $this->resultRepo = $resultRepo;
        $this->quizRepo = $quizRepo;
    }

    public function listUsers($perPage = 10)
    {
        return $this->userRepo->paginate($perPage);
    }

    public function getUserById(int $id)
    {
        return $this->userRepo->findById($id);
    }

    public function createUser(array $data)
    {
        if (!isset($data['role_id'])) {
            $data['role_id'] = 2; // Default role
        }

        $data['username'] = $data['name']; // Nếu DB dùng username

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $this->userRepo->create($data);
    }

    public function updateUser(int $id, array $data): bool
    {
        $user = $this->getUserById($id);
        if (!$user) {
            throw new \Exception('User not found');
        }

        if (isset($data['role_id'])) {
            $data['role'] = $data['role_id'] == 1 ? 'admin' : 'user';
            unset($data['role_id']);
        }

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        return $this->userRepo->update($id, $data);
    }

    public function delete(int $id): bool
    {
        $user = $this->getUserById($id);
        if (!$user) {
            throw new \Exception('User not found');
        }

        return $this->userRepo->delete($id);
    }

    public function findByUsername(string $username)
    {
        return $this->userRepo->findByUsername($username);
    }

    public function getProfile()
    {
        $user = Auth::user();
        if (!$user) {
            throw new \Exception('User not authenticated');
        }
        return $this->userRepo->findProfile($user->id);
    }

    protected function handleAvatarUpload(?UploadedFile $avatar, ?string $oldAvatar = null): ?string
    {
        if ($avatar && $avatar->isValid()) {
            if ($oldAvatar) {
                Storage::disk('public')->delete($oldAvatar);
            }
            return $avatar->store('avatars', 'public');
        }
        return null;
    }

    public function updateProfile(int $id, Request $request)
    {
        $user = $this->getUserById($id);
        if (!$user || !Auth::user() || Auth::user()->id !== $id) {
            throw new \Exception('Unauthorized or user not found');
        }

        $data = $request->only(['name', 'email', 'password', 'avatar']);
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'avatar' => 'nullable|image|max:2048',
        ];

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }

        if (isset($data['avatar']) && $data['avatar'] instanceof UploadedFile) {
            $avatarPath = $this->handleAvatarUpload($data['avatar'], $user->avatar);
            if ($avatarPath) {
                $data['avatar'] = $avatarPath;
            } else {
                unset($data['avatar']);
            }
        } else {
            unset($data['avatar']);
        }

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $this->userRepo->update($id, $data);
        return $this->getUserById($id);
    }

    public function deleteProfile(int $id): bool
    {
        $user = $this->getUserById($id);
        if (!$user || !Auth::user() || Auth::user()->id !== $id) {
            throw new \Exception('Unauthorized or user not found');
        }

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        return $this->userRepo->delete($id);
    }

    public function getResultsByUserId(int $id)
    {
        return $this->resultRepo->showResults($id);
    }
}