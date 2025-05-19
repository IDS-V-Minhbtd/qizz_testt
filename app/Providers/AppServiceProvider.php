<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Repository + Interface
use App\Repositories\Interfaces\QuizRepositoryInterface;
use App\Repositories\QuizRepository;

use App\Repositories\Interfaces\QuestionRepositoryInterface;
use App\Repositories\QuestionRepository;

use App\Repositories\Interfaces\AnswerRepositoryInterface;
use App\Repositories\AnswerRepository;

use App\Repositories\Interfaces\ResultRepositoryInterface;
use App\Repositories\ResultRepository;

use App\Repositories\Interfaces\UserAnswerRepositoryInterface;
use App\Repositories\UserAnswerRepository;

use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\UserRepository;



// Services
use App\Services\QuizService;
use App\Services\QuestionService;
use App\Services\ResultService;
use App\Services\UserAnswerService;
use App\Services\UserService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind Repositories
        $this->app->bind(QuizRepositoryInterface::class, QuizRepository::class);
        $this->app->bind(QuestionRepositoryInterface::class, QuestionRepository::class);
        $this->app->bind(AnswerRepositoryInterface::class, AnswerRepository::class);
        $this->app->bind(ResultRepositoryInterface::class, ResultRepository::class);
        $this->app->bind(UserAnswerRepositoryInterface::class, UserAnswerRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);


        $this->app->bind(QuizService::class, function ($app) {
            return new QuizService(
                $app->make(QuizRepositoryInterface::class)
            );
        });

        $this->app->bind(QuestionService::class, function ($app) {
            return new QuestionService(
                $app->make(QuestionRepositoryInterface::class),
                $app->make(QuizRepositoryInterface::class),
                $app->make(AnswerRepositoryInterface::class) // Add the missing dependency
            );
        });

        
        $this->app->bind(ResultService::class, function ($app) {
            return new ResultService(
                $app->make(ResultRepositoryInterface::class),
                $app->make(UserAnswerRepositoryInterface::class),
                $app->make(QuizRepositoryInterface::class),
                $app->make(AnswerRepositoryInterface::class)
            );
        });

        $this->app->singleton(UserAnswerService::class, function ($app) {
            return new UserAnswerService(
                $app->make(UserAnswerRepositoryInterface::class), 
                $app->make(AnswerRepositoryInterface::class),    
                $app->make(QuestionRepositoryInterface::class),   
                $app->make(QuizRepositoryInterface::class),
                $app->make(ResultRepositoryInterface::class)
            );
        });

        $this->app->bind(UserService::class, function ($app) {
            return new UserService(
                $app->make(UserRepositoryInterface::class),
                $app->make(ResultRepositoryInterface::class)
            );
        });
    }

    public function boot(): void
    {
        //
    }
}
