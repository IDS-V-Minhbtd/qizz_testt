<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\Api\QuizApiController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\AnswerController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\FlashcardController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ========== AUTH ==========
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('me', [AuthController::class, 'me'])->middleware('auth:sanctum');
});

// ========== USER ==========
Route::middleware('auth:sanctum')->group(function () {
    Route::get('user/profile', [UserController::class, 'profile']);
    Route::put('user/profile', [UserController::class, 'update']);
});

// ========== QUIZZES ==========
Route::prefix('quizzes')->group(function () {
    Route::get('/', [QuizController::class, 'index']);
    Route::get('/{id}', [QuizController::class, 'show']);

    Route::middleware(['auth:sanctum', 'role:quizz_manager,admin'])->group(function () {
        Route::post('/', [QuizController::class, 'store']);
        Route::put('/{id}', [QuizController::class, 'update']);
        Route::delete('/{id}', [QuizController::class, 'destroy']);
        Route::post('/import', [QuizController::class, 'import']);
        Route::post('/clone/{id}', [QuizController::class, 'clone']);
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

// ========== COMMENTS ==========
Route::middleware('auth:sanctum')->prefix('comments')->group(function () {
    Route::post('/', [CommentController::class, 'store']);
    Route::delete('/{id}', [CommentController::class, 'destroy']);
});

// ========== NOTES ==========
Route::middleware('auth:sanctum')->prefix('notes')->group(function () {
    Route::get('/', [NoteController::class, 'index']);
    Route::post('/', [NoteController::class, 'store']);
    Route::put('/{id}', [NoteController::class, 'update']);
    Route::delete('/{id}', [NoteController::class, 'destroy']);
});

// ========== ADMIN DASHBOARD ==========
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/stats', [AdminController::class, 'stats']);
    Route::get('/pending', [AdminController::class, 'pendingContent']);
    Route::post('/approve/{type}/{id}', [AdminController::class, 'approve']);
    Route::post('/reject/{type}/{id}', [AdminController::class, 'reject']);
});

// ========== QUIZ API WITH THROTTLE ==========
Route::middleware('throttle:60,1')->group(function () {
    Route::apiResource('quiz', QuizApiController::class);
});
