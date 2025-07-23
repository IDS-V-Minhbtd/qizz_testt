<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\UserAnswerController;
use App\Http\Controllers\FlashcardContronller;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;

Route::get('/', fn () => view('welcome'));

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/search', [HomeController::class, 'search'])->name('search.quizzes.index');
Route::get('quizzes/{quiz}', [QuizController::class, 'show'])->name('quizzes.show');
Route::middleware(['auth'])->group(function () {
   Route::post('/quiz/check-answer', [QuizController::class, 'checkAnswer'])->name('check.answer');
});

//  CHỈ ADMIN và QUIZZ MANAGER mới có quyền quản lý quizzes và questions
Route::middleware(['auth', 'isAdmin:admin,quizz_manager'])->prefix('admin')->name('admin.')->group(function () {

    // Add alias for admin.dashboard
    Route::get('/', fn () => view('admin.dashboard'))->name('index');
    Route::get('/', fn () => view('admin.dashboard'))->name('dashboard');

    // CRUD quizzes
    Route::resource('quizzes', QuizController::class);
    
    // CRUD nested questions
    Route::resource('quizzes.questions', QuestionController::class);

    //  crud flashcard 
    Route::resource('quizzes.flashcards', FlashcardContronller::class);

    // crud course
    Route::resource('courses', CourseController::class);


    
    // crud lesson
    Route::resource('lessons', LessonController::class);

    // Import quizzes (fix: use GET for import page)
    Route::get('quizzes/{quiz}/questions/import', [QuestionController::class, 'import'])->name('questions.import');
    Route::post('quizzes/{quiz}/questions/import', [QuestionController::class, 'importUpload'])->name('questions.import.upload');
    Route::post('quizzes/{quiz}/questions/import-file', [QuestionController::class, 'importFile'])->name('questions.import.file'); // <-- Add this line
    Route::get('quizzes/{quiz}/questions/import-template', [QuestionController::class, 'importTemplate'])->name('questions.import.template');
    Route::get('quizzes/{quiz}/questions/download-template', [QuestionController::class, 'downloadTemplate'])->name('questions.download-template');
    Route::post('quizzes/{quiz}/questions/import-text', [QuestionController::class, 'importText'])->name('questions.import.text');

  
    // Custom routes cho questions
    Route::get('quizzes/{quiz}/questions/create', [QuestionController::class, 'create'])->name('quizzes.questions.create');
    Route::get('quizzes/{quiz}/questions/{question}/edit', [QuestionController::class, 'edit'])->name('quizzes.questions.edit');
    Route::put('quizzes/{quiz}/questions/{question}/update', [QuestionController::class, 'update'])->name('quizzes.questions.update');
    Route::delete('quizzes/{quiz}/questions/{question}', [QuestionController::class, 'destroy'])->name('quizzes.questions.destroy');

    // Xem kết quả
    Route::get('results/{result}', [ResultController::class, 'show'])->name('results.show');
});

// ✅ CHỈ ADMIN mới có quyền quản lý users
Route::middleware(['auth', 'isAdmin:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UserController::class);
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::post('users/search', [UserController::class, 'search'])->name('users.search');
    Route::get('users/{user}/results', [UserController::class, 'showResults'])->name('users.results');
});

// ✅ USER chơi quiz, quản lý profile, history
Route::middleware('auth')->group(function () {
    Route::post('quizz/{quiz}/submit', [UserAnswerController::class, 'submit'])->name('quizz.submit');
    Route::get('quizz/{quiz}/result', [UserAnswerController::class, 'resultByQuiz'])->name('quizz.result');
    Route::get('quizz/{quiz}', [UserAnswerController::class, 'start'])->name('quizz.index');

    Route::get('profile', [UserController::class, 'profile'])->name('profile');
    Route::match(['post', 'put'], 'profile/update', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::post('profile/delete', [UserController::class, 'deleteProfile'])->name('profile.delete');

    Route::get('history', [UserController::class, 'history'])->name('history');
});

Route::get('test', function () {
    return view('test');
})->name('test');
