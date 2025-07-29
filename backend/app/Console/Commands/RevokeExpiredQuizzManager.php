<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;

class RevokeExpiredQuizzManager extends Command
{
    protected $signature = 'quiz:revoke-expired-quizz-manager';
    protected $description = 'Thu hồi quyền quizz_manager đã hết hạn dùng thử';

    public function handle()
    {
        $users = User::whereNotNull('quizz_manager_until')
            ->where('quizz_manager_until', '<', Carbon::now())
            ->get();

        foreach ($users as $user) {
            $user->update([
                'quizz_manager_until' => null,
                'role' => 'user', // reset về mặc định
            ]);
            $this->info("Đã thu hồi quyền của user ID: {$user->id}");
        }
    }
}
