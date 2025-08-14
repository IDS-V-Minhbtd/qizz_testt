<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\UserController;

use App\Http\Controllers\Api\QuizApiController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\AnswerController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\FlashcardController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\ProgressController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ========== AUTH ==========
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthApiController::class, 'register']);
    Route::post('login', [AuthApiController::class, 'login']);
    Route::post('logout', [AuthApiController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('me', [AuthApiController::class, 'me'])->middleware('auth:sanctum');
});

// ========== USER ==========
Route::middleware('auth:sanctum')->group(function () {
    Route::get('user/profile', [UserController::class, 'profile']);
    Route::put('user/profile', [UserController::class, 'update']);
});

// ========== QUIZZES ==========
Route::prefix('quizzes')->group(function () {
    Route::middleware(['auth:sanctum', 'role:quizz_manager,admin'])->group(function () {
        Route::get('/', [QuizApiController::class, 'index']);
        Route::get('/{id}', [QuizApiController::class, 'show']);
        Route::post('/', [QuizApiController::class, 'store']);
        Route::put('/{id}', [QuizApiController::class, 'update']);
        Route::delete('/{id}', [QuizApiController::class, 'destroy']);
        Route::post('/import', [QuizApiController::class, 'import']);
        Route::post('/clone/{id}', [QuizApiController::class, 'clone']);
    });

    // Questions & answers nested
    Route::prefix('/{quizId}/questions')->group(function () {
        Route::get('/', [QuestionController::class, 'index']);
        Route::get('/{questionId}', [QuestionController::class, 'show']);

        Route::middleware(['auth:sanctum', 'role:quizz_manager,admin'])->group(function () {
            Route::post('/', [QuestionController::class, 'store']);
            Route::put('/{questionId}', [QuestionController::class, 'update']);
            Route::delete('/{questionId}', [QuestionController::class, 'destroy']);
        });

        // answers
        Route::prefix('/{questionId}/answers')->group(function () {
            Route::get('/', [AnswerController::class, 'index']);
            Route::middleware(['auth:sanctum', 'role:quizz_manager,admin'])->group(function () {
                Route::post('/', [AnswerController::class, 'store']);
                Route::put('/{answerId}', [AnswerController::class, 'update']);
                Route::delete('/{answerId}', [AnswerController::class, 'destroy']);
            });
        });
    });
});

// ========== COURSES ==========
Route::prefix('courses')->group(function () {
    Route::get('/', [CourseController::class, 'index']);
    Route::get('/{id}', [CourseController::class, 'show']);

    Route::middleware(['auth:sanctum', 'role:quizz_manager,admin'])->group(function () {
        Route::post('/', [CourseController::class, 'store']);
        Route::put('/{id}', [CourseController::class, 'update']);
        Route::delete('/{id}', [CourseController::class, 'destroy']);
    });

    // Lessons
    Route::prefix('/{courseId}/lessons')->group(function () {
        Route::get('/', [LessonController::class, 'index']);
        Route::get('/{lessonId}', [LessonController::class, 'show']);

        Route::middleware(['auth:sanctum', 'role:quizz_manager,admin'])->group(function () {
            Route::post('/', [LessonController::class, 'store']);
            Route::put('/{lessonId}', [LessonController::class, 'update']);
            Route::delete('/{lessonId}', [LessonController::class, 'destroy']);
        });
    });
});

// ========== FLASHCARDS ==========
Route::prefix('lessons/{lessonId}/flashcards')->group(function () {
    Route::get('/', [FlashcardController::class, 'index']);
    Route::middleware(['auth:sanctum', 'role:quizz_manager,admin'])->group(function () {
        Route::post('/', [FlashcardController::class, 'store']);
        Route::put('/{flashcardId}', [FlashcardController::class, 'update']);
        Route::delete('/{flashcardId}', [FlashcardController::class, 'destroy']);
    });
});

// ========== RESULTS (Làm quiz) ==========
Route::middleware('auth:sanctum')->prefix('results')->group(function () {
    Route::post('/start/{quizId}', [ResultController::class, 'startQuiz']);
    Route::post('/submit/{quizId}', [ResultController::class, 'submitQuiz']);
    Route::get('/history', [ResultController::class, 'history']);
    Route::get('/{id}', [ResultController::class, 'show']);
});

// ========== PROGRESS ==========
Route::middleware('auth:sanctum')->prefix('progress')->group(function () {
    Route::post('/{courseId}/{lessonId}', [ProgressController::class, 'update']);
    Route::get('/{courseId}', [ProgressController::class, 'show']);
});


