<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Collection;

class UserRepository implements UserRepositoryInterface
{
    public function all(): Collection
    {
        return User::all(); // This returns a collection of User objects
    }

    public function getAll()
    {
        return User::all(); // Implement the getAll method
    }
    public function findById(int $id): ?User // Ensure this matches the interface
    {
        return User::find($id);
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $user = $this->findById($id);
        if ($user) {
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

    public function findByName($name)
    {
        return User::where('name', $name)->first();
    }

    public function createUser(array $data): User
    {
        return User::create($data);
    }

    public function updateUser($id, array $data): bool
    {
        $user = $this->findById($id);
        if ($user) {
            return $user->update($data);
        }
        return false;
    }

    public function deleteUser($id): bool
    {
        $user = $this->findById($id);
        if ($user) {
            return $user->delete();
        }
        return false;
    }

    public function findProfile($id)
    {
        return User::find($id);
    }
}
