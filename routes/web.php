<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    HomeController, AdminController, ReportController, MessageController,
    ProfileController, DashboardController, TicketController, BlockController,
    LikeController, LanguageController, LandingController, PaymentController,
    PremiumController
};
use App\Http\Controllers\Auth\{
    LoginController, RegisterController, ResetPasswordController, ForgotPasswordController
};
use App\Http\Controllers\Admin\{
    AdmineLteController, AdminMessageController, AdminStateController,
    AdminTicketController, PhotoController
};

// --- Public Routes ---
Route::get('/', [LandingController::class, 'welcome']);
Route::get('/lang/{lang}', [LanguageController::class, 'switch']);
Route::get('/test-modal', fn() => view('test-modal'));

// --- Guest Routes (Login, Register, etc.) ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});


Route::middleware(['auth'])->group(function () {
    Route::get('/upgrade', [PremiumController::class, 'upgrade'])->name('premium.upgrade');
    Route::post('/upgrade/verify-crypto', [PremiumController::class, 'verifyCrypto'])->name('premium.verifyCrypto');
});


// --- Authenticated User Routes ---
Route::middleware('auth')->group(function () {
    
    // Auth & Profile
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/search', [ProfileController::class, 'search'])->name('search');
    Route::get('/upgrade', [PremiumController::class, 'index'])->name('premium.upgrade');

    // Profile Management
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::post('/update', [ProfileController::class, 'update'])->name('update');
        Route::get('/id/{id}', [ProfileController::class, 'show'])->name('show');
        Route::get('/{name}', [HomeController::class, 'showname']); // Generalized route
    });

    // Password Reset (Inside Auth for security/logic check)
    Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

    // Messages
    Route::prefix('messages')->name('messages.')->group(function () {
        Route::get('/', [MessageController::class, 'index'])->name('index');
        Route::get('/{user}', [MessageController::class, 'show'])->name('show');
        Route::post('/', [MessageController::class, 'store'])->name('store');
    });

    // Likes (Consolidated)
    Route::get('/likes', [LikeController::class, 'index'])->name('likes.index');
    Route::get('/likes/received', [LikeController::class, 'index'])->name('likes.received');
    Route::post('/like/{likedUserId}', [LikeController::class, 'store'])->name('like.store');

    // Tickets (User Side)
    Route::prefix('dashboard/tickets')->name('user.tickets.')->group(function () {
        Route::get('/', [TicketController::class, 'index'])->name('index');
        Route::get('/create', [TicketController::class, 'create'])->name('create');
        Route::post('/', [TicketController::class, 'store'])->name('store');
    });

    // Blocking & Reports
    Route::get('/police', [BlockController::class, 'index'])->name('police.index');
    Route::post('/block/{id}', [BlockController::class, 'block'])->name('user.block');
    Route::post('/unblock/{id}', [BlockController::class, 'unblock'])->name('user.unblock');
    Route::post('/report', [ReportController::class, 'store'])->name('report.store');

    // Payments
    Route::post('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments/callback', [PaymentController::class, 'callback'])->name('payments.callback');
});

// --- Admin Routes (Fully Secured) ---
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    
    // Dashboard & Stats
    Route::get('/', [AdmineLteController::class, 'index'])->name('dashboard');
    Route::get('/statedashboard', [AdminStateController::class, 'index'])->name('statedashboard');
    
    // User Management
    Route::prefix('users')->group(function () {
        Route::get('/', [AdmineLteController::class, 'indexUser'])->name('users');
        Route::get('/{user}', [AdmineLteController::class, 'showUser'])->name('users.show');
        Route::post('/{user}/ban', [AdmineLteController::class, 'ban'])->name('users.ban');
        Route::delete('/{user}', [AdmineLteController::class, 'destroy'])->name('users.destroy');
        // Critical actions moved inside admin group
        Route::patch('/make-admin/{id}', [AdminController::class, 'makeAdmin'])->name('makeAdmin');
        Route::patch('/toggle-ban/{id}', [AdminController::class, 'toggleBan'])->name('toggleBan');
    });

    // Content Management
    Route::get('/messages', [AdminMessageController::class, 'index'])->name('messages');
    Route::get('/photos', [PhotoController::class, 'index'])->name('photos.index');
    Route::delete('/photos/{id}', [PhotoController::class, 'destroy'])->name('photos.destroy');

    // Reports Management
    Route::get('/reports', [ReportController::class, 'index'])->name('reports');
    Route::post('/reports/{report}/resolve', [ReportController::class, 'resolve'])->name('reports.resolve');
    Route::delete('/reports/{report}', [ReportController::class, 'destroy'])->name('reports.destroy');

    // Ticket Management (Admin side)
    Route::prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/', [AdminTicketController::class, 'index'])->name('index');
        Route::get('/{id}', [AdminTicketController::class, 'show'])->name('show');
        Route::post('/{id}/reply', [AdminTicketController::class, 'reply'])->name('reply');
        Route::post('/{id}/close', [AdminTicketController::class, 'close'])->name('close');
    });
});