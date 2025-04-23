<?php
namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    public function create(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        return User::create($data);
    }

    public function findById(int $id): ?User
    {
        return User::find($id);
    }
    public function update(int $id, array $data): bool
    {
        $user = $this->findById($id);
        if ($user) {
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }
            return $user->update($data);
        }
        return false;
    }
    public function delete(int $id): bool
    {
        $user = $this->findById($id);
        if ($user) {
            return $user->delete();
        }
        return false;
    }
    public function all(): iterable
    {
        return User::all();
    }
}