<?php
namespace App\Services;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Collection;

class UserService
{
    public function __construct(protected UserRepositoryInterface $userRepo) {}

    public function create(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        return $this->userRepo->create($data);
    }

    public function getById(int $id): ?User
    {
        return $this->userRepo->findById($id);
    }

    public function update(int $id, array $data): bool
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        return $this->userRepo->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->userRepo->delete($id);
    }

    public function getAll(): iterable
    {
        return $this->userRepo->all();
    }

    public function getAllUsersWithRoles(): Collection
    {
        return $this->userRepo->all()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames(),
            ];
        });
    }
}