<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\QuizRepositoryInterface;
use App\Repositories\QuizRepository;
use App\Services\QuizService;
use App\Repositories\Interfaces\QuestionRepositoryInterface;
use App\Repositories\QuestionRepository;
use App\Services\QuestionService;
use App\Repositories\Interfaces\AnswerRepositoryInterface;
use App\Repositories\AnswerRepository;
use App\Services\AnswerService;
use App\Repositories\Interfaces\ResultRepositoryInterface;
use App\Repositories\ResultRepository;
use App\Services\ResultService;
use App\Repositories\Interfaces\UserAnswerRepositoryInterface;
use App\Repositories\UserAnswerRepository;
use App\Services\UserAnswerService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Đăng ký các repository và service cho Quiz
        $this->app->bind(
            QuizRepositoryInterface::class,
            QuizRepository::class
        );
        $this->app->bind(QuizService::class, function ($app) {
            return new QuizService($app->make(QuizRepositoryInterface::class));
        });

        // Đăng ký các repository và service cho Question
        $this->app->bind(
            QuestionRepositoryInterface::class,
            QuestionRepository::class
        );
        $this->app->bind(QuestionService::class, function ($app) {
            return new QuestionService($app->make(QuestionRepositoryInterface::class));
        });

        // Đăng ký các repository và service cho Answer
        $this->app->bind(
            AnswerRepositoryInterface::class,
            AnswerRepository::class
        );
        $this->app->bind(AnswerService::class, function ($app) {
            return new AnswerService($app->make(AnswerRepositoryInterface::class));
        });

        // Đăng ký các repository và service cho Result
        $this->app->bind(
            ResultRepositoryInterface::class,
            ResultRepository::class
        );
        $this->app->bind(ResultService::class, function ($app) {
            return new ResultService($app->make(ResultRepositoryInterface::class));
        });

        // Đăng ký các repository và service cho UserAnswer
        $this->app->bind(
            UserAnswerRepositoryInterface::class,
            UserAnswerRepository::class
        );
        $this->app->bind(UserAnswerService::class, function ($app) {
            return new UserAnswerService($app->make(UserAnswerRepositoryInterface::class));
        });

        // Đăng ký User Repository và Service (nếu cần)
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );
        $this->app->bind(UserService::class, function ($app) {
            return new UserService($app->make(UserRepositoryInterface::class));
        });
    }

    public function boot(): void
    {
    }
}
