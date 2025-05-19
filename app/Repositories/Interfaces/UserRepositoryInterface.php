<?php

namespace App\Repositories\Interfaces;

use App\Models\User;

interface UserRepositoryInterface
{
    public function getAll();
    public function findByName($name);
    public function findById(int $id): ?User; // Add type hinting for $id and return type
    public function createUser(array $data);
    public function updateUser($id, array $data);
    public function deleteUser($id);
    public function findProfile($id);
}