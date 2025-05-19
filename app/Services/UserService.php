<?php

namespace App\Services;

use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Common\Constant;

class UserService
{
    protected UserRepositoryInterface $userRepo;

    public function __construct(UserRepositoryInterface $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function listUsers(): iterable
    {
        return $this->userRepo->all();
    }

    public function create(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        return $this->userRepo->create($data);
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

    public function getUserById(int $id)
    {
        return $this->userRepo->findById($id);
    }

    public function getAll(){
        return $this->userRepo->getAll();
    }

    public function findByName($name){
        Log::info($name);
        return $this->userRepo->findByName($name);
    }

    public function findById($id){
        return $this->userRepo->findById($id);
    }

    public function createUser(array $data){
        if(!isset($data['role_id'])){
            $data['role_id']=Constant::MEMBER_ROLE;
        }
        $data['password'] = bcrypt($data['password']);
        $data['avatar'] = 'avatars/default.png';
        return $this->userRepo->createUser($data);
    }

    public function updateUser($id, array $data){
        if (!isset($data['password']) || empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = bcrypt($data['password']);
        }
        return $this->userRepo->updateUser($id, $data);
    }

    public function deleteUser($id){
        return $this->userRepo->deleteUser($id);
    }

    public function updateAvatar($id, array $data){
        if (!isset($data['password']) || empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = bcrypt($data['password']);
        }
        if (isset($data['avatar'])) {
            $avatarPath = $data['avatar']->store('avatars', 'public');
            $data['avatar'] = $avatarPath;
        } else {
            $data['avatar'] = `avatars/default.png`;
        }

        return $this->userRepo->updateUser($id, $data);
    }

    public function getProfile(){
        $user = Auth::user();
        return $this->userRepo->findProfile($user->id);
    }
}