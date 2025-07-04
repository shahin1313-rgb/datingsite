<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\AdmineLteController;


Route::get('/', function () {
    return view('welcome');
});

// Auth::routes();
// Routes for authenticated users
Route::middleware('auth')->group(function () {




    // Password Reset Routes ff
    Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');


    // Profile Update
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/profile/{name}', [HomeController::class, 'showname'])->name('profile');

    Route::get('/search', [ProfileController::class, 'search'])->name('search');

    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::get('/profile/{id}', [ProfileController::class, 'show'])->name('profile.show');

    // Inbox page
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');

    // Conversation between two users
    Route::get('/messages/{user}', [MessageController::class, 'show'])->name('messages.show');

    // Send a message
    Route::post('/messages/{user}', [MessageController::class, 'store'])->name('messages.store');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
/// n

Route::post('/report', [ReportController::class, 'store'])->name('report.store')->middleware('auth');


// Route::middleware(['auth', 'admin'])->group(function () {
//     Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
// });
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [AdmineLteController::class, 'index'])->name('admin.dashboard');
    Route::get('/users', [AdmineLteController::class, 'indexUser'])->name('admin.users');
    Route::post('/users/{user}/ban', [AdmineLteController::class, 'ban'])->name('admin.users.ban');
    Route::get('/users/{user}', [AdmineLteController::class, 'showUser'])->name('admin.users.show');

    Route::delete('/users/{user}', [AdmineLteController::class, 'destroy'])->name('admin.users.destroy');
});
Route::patch('/admin/make-admin/{id}', [AdminController::class, 'makeAdmin'])->name('admin.makeAdmin');

Route::patch('/admin/toggle-ban/{id}', [AdminController::class, 'toggleBan'])->name('admin.toggleBan');

///// Report h
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {

    // نمایش لیست گزارش‌ها
    Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports');

    // بررسی گزارش (تغییر وضعیت به resolved)
    Route::post('/reports/{report}/resolve', [ReportController::class, 'resolve'])->name('admin.reports.resolve');

    // حذف گزارش
    Route::delete('/reports/{report}', [ReportController::class, 'destroy'])->name('admin.reports.destroy');
});


Route::middleware('guest')->group(function () {


    // Authentication Routes
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);


    // Registration Routes
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});
