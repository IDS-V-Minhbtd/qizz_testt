<?php

namespace App\Services;

use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\ResultRepositoryInterface;
use App\Repositories\Interfaces\QuizRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;

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
        if (!empty($data['password'])) {
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

    //     Xử lý upload avatar dùng chung
    protected function handleAvatarUpload(?UploadedFile $avatar): ?string
    {
        if ($avatar && $avatar->isValid()) {
            return $avatar->store('avatars', 'public');
        }

        return null;
    }

    //     Cập nhật profile gồm avatar + password
    public function updateProfile(int $id, array $data): bool
    {
        $user = Auth::user();
        if (!$user || $user->id !== $id) {
            return false;
        }

        // Xử lý avatar nếu có
        if (isset($data['avatar']) && $data['avatar'] instanceof \Illuminate\Http\UploadedFile) {
            $avatarPath = $this->handleAvatarUpload($data['avatar']);
            if ($avatarPath) {
                $data['avatar'] = $avatarPath;
            } else {
                unset($data['avatar']);
            }
        } else {
            unset($data['avatar']);
        }

        // Xử lý mật khẩu nếu có
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        return $this->userRepo->update($id, $data);
    }

    //     Xoá profile của user hiện tại
    public function profileDelete(int $id): bool
    {
        $user = Auth::user();
        if (!$user || $user->id !== $id) {
            return false;
        }
        return $this->userRepo->delete($id);
    }

    //     Lấy kết quả của user theo ID
    public function getResultsByUserId(int $id)
    {
        return $this->resultRepo->showResults($id);
    }
}
