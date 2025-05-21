<?php

namespace App\Services;

use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\ResultRepositoryInterface;
use App\Repositories\Interfaces\QuizRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

    // Lấy danh sách user có phân trang
    public function listUsers($perPage = 10)
    {
        return $this->userRepo->paginate($perPage);
    }

    // Tạo user mới, hash password tự động
    public function create(array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        return $this->userRepo->create($data);
    }

    // Cập nhật user, hash password nếu có thay đổi
    public function update(int $id, array $data): bool
    {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        return $this->userRepo->update($id, $data);
    }

    // Xóa user
    public function delete(int $id): bool
    {
        return $this->userRepo->delete($id);
    }

    // Lấy user theo id
    public function getUserById(int $id)
    {
        return $this->userRepo->findById($id);
    }

    // Tìm user theo username
    public function findByUsername(string $username)
    {
        return $this->userRepo->findByUsername($username);
    }

    // Lấy profile user hiện tại
    public function getProfile()
    {
        $user = Auth::user();
        return $this->userRepo->findProfile($user->id);
    }

    // Cập nhật avatar user kèm xử lý password nếu có
    public function updateAvatar(int $id, array $data)
    {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if (isset($data['avatar'])) {
            $avatarPath = $data['avatar']->store('avatars', 'public');
            $data['avatar'] = $avatarPath;
        } else {
            $data['avatar'] = 'avatars/default.png';
        }

        return $this->userRepo->update($id, $data);
    }

    // Lấy kết quả của user theo ID
    public function showResults($id)
    {
        return $this->resultRepo->showResults($id);
    }
}
