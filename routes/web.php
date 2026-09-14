<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\StudentAttemptController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\StudentMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {

    Route::get('/index', function () {
        return view('index');
    })->name('index');


    Route::get('/registerStudent', [AuthController::class, 'showRegisterStudent'])->name('registerStudent');
    Route::get('/registerTeacher', [AuthController::class, 'showRegisterTeacher'])->name('registerTeacher');
    Route::get('/loginStudent', [AuthController::class, 'showLoginStudent'])->name('loginStudent');
    Route::get('/loginTeacher', [AuthController::class, 'showLoginTeacher'])->name('loginTeacher');

    // Maks 5 percobaan per menit per IP, menahan brute force password & spam akun
    Route::middleware('throttle:5,1')->group(function () {
        Route::post('/registerStudent', [AuthController::class, 'registerStudent'])->name('registerStudent');
        Route::post('/registerTeacher', [AuthController::class, 'registerTeacher'])->name('registerTeacher');
        Route::post('/loginStudent', [AuthController::class, 'loginStudent'])->name('loginStudent');
        Route::post('/loginTeacher', [AuthController::class, 'loginTeacher'])->name('loginTeacher');
    });

});
Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');

Route::middleware([AuthMiddleware::class])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/subject/{id}', [DashboardController::class, 'showSubject'])->name('showSubject');
    Route::post('/subjects', [DashboardController::class, 'storeSubject'])->name('subjects.store');

    Route::get('/module/{id}', [DashboardController::class, 'showModule'])->name('showModule');
    Route::post('/modules', [ModuleController::class, 'storeModule'])->name('modules.store');
    Route::post('/practices', [ModuleController::class, 'storePractice'])->name('practices.store');
    Route::post('/questions', [ModuleController::class, 'storeQuestion'])->name('questions.store');
    Route::post('/answer-keys', [ModuleController::class, 'storeAnswerKey'])->name('answerkeys.store');
    Route::post('/attempts/{attempt}/grade', [StudentAttemptController::class, 'grade'])->name('attempts.grade');

    Route::get('/modules/{module}/edit', [ModuleController::class, 'editModule'])->name('modules.edit');
    Route::put('/modules/{module}', [ModuleController::class, 'updateModule'])->name('modules.update');
    Route::delete('/modules/{module}', [ModuleController::class, 'destroyModule'])->name('modules.destroy');

    Route::put('/practices/{practice}', [ModuleController::class, 'updatePractice'])->name('practices.update');
    Route::delete('/practices/{practice}', [ModuleController::class, 'destroyPractice'])->name('practices.destroy');

    Route::post('/questions/{question}', [ModuleController::class, 'updateQuestion'])->name('questions.update');
    Route::delete('/questions/{question}', [ModuleController::class, 'destroyQuestion'])->name('questions.destroy');

    Route::delete('/answer-keys/{answer_key}', [ModuleController::class, 'destroyAnswerKey'])->name('answerkeys.destroy');

    // Bukti bayar disimpan privat; controller hanya mengizinkan admin atau siswa pemiliknya
    Route::get('/payments/{payment}/proof', [DashboardController::class, 'paymentProof'])->name('payments.proof');

    Route::middleware(StudentMiddleware::class)->group(function() {
        Route::post('/enroll/{id}', [DashboardController::class, 'enroll'])->name('enroll.subject');
        Route::post('/payments/pay', [DashboardController::class, 'submitProof'])->name('payments.submit');
        Route::post('/attempts', [StudentAttemptController::class, 'store'])->name('attempts.store');
    });
    Route::middleware(AdminMiddleware::class)->prefix('admin')->name('admin.')->group(function() {
        Route::get('/role', [AdminController::class, 'role'])->name('role');
        Route::get('/subject', [AdminController::class, 'subject'])->name('subject');
        Route::get('/verify', [AdminController::class, 'showVerify'])->name('verify');

        Route::put('/payments/{payment}/verify', [AdminController::class, 'verifyPayment'])->name('payments.verify');
        Route::put('/payments/{payment}/reject', [AdminController::class, 'rejectPayment'])->name('payments.reject');

        Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
        Route::post('/users/{user}/make-admin', [AdminController::class, 'makeAdmin'])->name('users.makeAdmin');

        Route::get('/subjects/{subject}/edit', [AdminController::class, 'editSubject'])->name('subjects.edit');
        Route::put('/subjects/{subject}', [AdminController::class, 'updateSubject'])->name('subjects.update');
        Route::delete('/subjects/{subject}', [AdminController::class, 'destroySubject'])->name('subjects.destroy');

        Route::get('/income', [AdminController::class, 'showIncome'])->name('income');
    });
});
