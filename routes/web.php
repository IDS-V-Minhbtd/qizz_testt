<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\UserAnswerController;

Route::get('/', fn () => view('welcome'));

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');


Route::get('quizzes/{quiz}', [QuizController::class, 'show'])->name('quizzes.show');


Route::middleware(['isAdmin:admin', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', fn () => view('admin.dashboard'))->name('dashboard');



    Route::resource('users', UserController::class);
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::post('users/search', [UserController::class, 'search'])->name('users.search');
    Route::get('users/{user}/results', [UserController::class, 'showResults'])->name('users.results');


    Route::resource('quizzes', QuizController::class);
    Route::resource('quizzes.questions', QuestionController::class)->shallow();
    Route::get('quizzes/{quiz}/questions/create', [QuestionController::class, 'create'])->name('quizzes.questions.create');
    Route::get('quizzes/{quiz}/questions/{question}/edit', [QuestionController::class, 'edit'])->name('quizzes.questions.edit');
    Route::put('quizzes/{quiz}/questions/{question}/update', [QuestionController::class, 'update'])->name('quizzes.questions.update');
    Route::delete('quizzes/{quiz}/questions/{question}', [QuestionController::class, 'destroy'])->name('quizzes.questions.destroy');
    Route::get('results/{result}', [ResultController::class, 'show'])->name('results.show');
});

Route::middleware('auth')->group(function () {
    Route::post('quizz/{quiz}/submit', [UserAnswerController::class, 'submit'])->name('quizz.submit');
    Route::get('quizz/{quiz}/result', [UserAnswerController::class, 'resultByQuiz'])->name('quizz.result');
    Route::get('quizz/{quiz}', [UserAnswerController::class, 'start'])->name('quizz.index');

    Route::get('profile', [UserController::class, 'profile'])->name('profile');
    Route::post('profile/edit', [UserController::class, 'editProfile'])->name('profile.edit');
});
