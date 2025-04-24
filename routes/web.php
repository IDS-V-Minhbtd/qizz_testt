<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => view('welcome'));

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

// Admin routes
Route::middleware(['isAdmin:admin', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {return view('admin.dashboard');})->name('dashboard');

    Route::resource('users', UserController::class);

    // Quizzes CRUD
    Route::resource('quizzes', QuizController::class);

    Route::get('quizzes/create', function () {
        $questions = \App\Models\Question::all(); // Fetch all questions
        return view('quizzes.create', ['questions' => $questions]);
    })->name('quizzes.create');

    // Nested: Questions within Quizzes
    Route::prefix('quizzes/{quiz}')->name('quizzes.')->group(function () {
        Route::get('questions', [QuestionController::class, 'index'])->name('questions.index');
        Route::get('questions/create', [QuestionController::class, 'create'])->name('questions.create');
        Route::post('questions', [QuestionController::class, 'store'])->name('questions.store');
        Route::get('questions/{question}/edit', [QuestionController::class, 'edit'])->name('questions.edit');
        Route::put('questions/{question}', [QuestionController::class, 'update'])->name('questions.update');
        Route::delete('questions/{question}', [QuestionController::class, 'destroy'])->name('questions.destroy');

        Route::get('results', [ResultController::class, 'index'])->name('results.index');
    });

    Route::get('results/{result}', [ResultController::class, 'show'])->name('results.show');
});

// User routes
Route::middleware(['auth'])->group(function(){
    Route::get('/quizzes', [QuizController::class, 'index'])->name('quizzes.index');
    Route::get('/quizzes/{quiz}', [QuizController::class, 'show'])->name('quizzes.show');
    Route::post('/quizzes/{quiz}/submit', [QuizController::class, 'submit'])->name('quizzes.submit');
    Route::get('/quizzes/{quiz}/result', [ResultController::class, 'showUserResult'])->name('quizzes.result.show');
});
Route::get('/quizzes/{quiz}/take', [QuizController::class, 'take'])->name('quizzes.take');


Route::get('/quiz', function () {
    return view('quiz');
});