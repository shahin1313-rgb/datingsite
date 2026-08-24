<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\{
    HomeController,
    AdminController,
    ReportController,
    MessageController,
    ProfileController,
    DashboardController,
    TicketController,
    BlockController,
    LikeController,
    LanguageController,
    LandingController,
    PremiumController
};

use App\Http\Controllers\Auth\{
    LoginController,
    RegisterController,
    ResetPasswordController,
    AdminLoginController,
    ForgotPasswordController,
    VerificationController
};

use App\Http\Controllers\Admin\{
    AdmineLteController,
    AdminMessageController,
    AdminStateController,
    AdminTicketController,
    PhotoController
};

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [LandingController::class, 'welcome']);

Route::get('/lang/{lang}', [
    LanguageController::class,
    'switch',
]);

Route::get('/test-modal', function () {
    return view('test-modal');
});

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get(
        '/login',
        [LoginController::class, 'showLoginForm']
    )->name('login');

    Route::post(
        '/login',
        [LoginController::class, 'login']
    )->middleware('throttle:10,1');

    Route::get(
        '/register',
        [RegisterController::class, 'showRegistrationForm']
    )->name('register');

    /*
     * هر IP حداکثر پنج درخواست ثبت‌نام
     * در یک دقیقه می‌تواند ارسال کند.
     */
    Route::post(
        '/register',
        [RegisterController::class, 'register']
    )->middleware('throttle:5,1');
});

/*
|--------------------------------------------------------------------------
| Email Verification and Logout
|--------------------------------------------------------------------------
|
| کاربر تأییدنشده باید امکان مشاهده صفحه تأیید،
| ارسال مجدد لینک و خروج از حساب را داشته باشد.
|
*/

Route::middleware([
    'auth',
    'not_banned',
])->group(function () {
    Route::post(
        '/logout',
        [LoginController::class, 'logout']
    )->name('logout');

    Route::get(
        '/email/verify',
        [VerificationController::class, 'show']
    )->name('verification.notice');

    Route::get(
        '/email/verify/{id}/{hash}',
        [VerificationController::class, 'verify']
    )
        ->whereNumber('id')
        ->name('verification.verify');

    Route::post(
        '/email/resend',
        [VerificationController::class, 'resend']
    )->name('verification.resend');
});

/*
|--------------------------------------------------------------------------
| Verified User Routes
|--------------------------------------------------------------------------
|
| کاربر تا قبل از تأیید ایمیل به هیچ‌کدام از
| قسمت‌های اصلی سایت دسترسی ندارد.
|
*/

Route::middleware([
    'auth',
    'verified',
    'not_banned',
])->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Main Pages
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/home',
        [HomeController::class, 'index']
    )->name('home');

    Route::get(
        '/dashboard',
        [DashboardController::class, 'index']
    )->name('dashboard');

    Route::get(
        '/search',
        [ProfileController::class, 'search']
    )->name('search');

    /*
    |--------------------------------------------------------------------------
    | Premium
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/upgrade',
        [PremiumController::class, 'index']
    )->name('premium.upgrade');

    Route::post(
        '/upgrade/verify-crypto',
        [PremiumController::class, 'verifyCrypto']
    )
        ->middleware('throttle:5,1')
        ->name('premium.verifyCrypto');

    /*
    |--------------------------------------------------------------------------
    | Profile Management
    |--------------------------------------------------------------------------
    */

    Route::prefix('profile')
        ->name('profile.')
        ->group(function () {
            Route::get(
                '/edit',
                [ProfileController::class, 'edit']
            )->name('edit');

            Route::post(
                '/update',
                [ProfileController::class, 'update']
            )->name('update');

            /*
             * تنها مسیر مجاز نمایش پروفایل عمومی.
             *
             * مسیر قدیمی /profile/{id} که به
             * HomeController متصل بود حذف شده است.
             */
            Route::get(
                '/id/{id}',
                [ProfileController::class, 'show']
            )
                ->whereNumber('id')
                ->name('show');
        });

    /*
    |--------------------------------------------------------------------------
    | Password Reset
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/password/reset',
        [
            ForgotPasswordController::class,
            'showLinkRequestForm',
        ]
    )->name('password.request');

    Route::post(
        '/password/email',
        [
            ForgotPasswordController::class,
            'sendResetLinkEmail',
        ]
    )
        ->middleware('throttle:5,1')
        ->name('password.email');

    Route::get(
        '/password/reset/{token}',
        [
            ResetPasswordController::class,
            'showResetForm',
        ]
    )->name('password.reset');

    Route::post(
        '/password/reset',
        [
            ResetPasswordController::class,
            'reset',
        ]
    )
        ->middleware('throttle:5,1')
        ->name('password.update');

    /*
    |--------------------------------------------------------------------------
    | Messages
    |--------------------------------------------------------------------------
    */

    Route::prefix('messages')
        ->name('messages.')
        ->group(function () {
            Route::get(
                '/',
                [MessageController::class, 'index']
            )->name('index');

            Route::get(
                '/{user}',
                [MessageController::class, 'show']
            )->name('show');

            Route::post(
                '/',
                [MessageController::class, 'store']
            )
                ->middleware('throttle:20,1')
                ->name('store');
        });

    /*
    |--------------------------------------------------------------------------
    | Likes
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/likes',
        [LikeController::class, 'index']
    )->name('likes.index');

    Route::get(
        '/likes/received',
        [LikeController::class, 'index']
    )->name('likes.received');

    Route::post(
        '/like/{likedUserId}',
        [LikeController::class, 'store']
    )
        ->whereNumber('likedUserId')
        ->middleware('throttle:20,1')
        ->name('like.store');

    /*
    |--------------------------------------------------------------------------
    | User Tickets
    |--------------------------------------------------------------------------
    */

    Route::prefix('dashboard/tickets')
        ->name('user.tickets.')
        ->group(function () {
            Route::get(
                '/',
                [TicketController::class, 'index']
            )->name('index');

            Route::get(
                '/create',
                [TicketController::class, 'create']
            )->name('create');

            Route::post(
                '/',
                [TicketController::class, 'store']
            )
                ->middleware('throttle:5,1')
                ->name('store');
        });

    /*
    |--------------------------------------------------------------------------
    | Blocking and Reports
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/police',
        [BlockController::class, 'index']
    )->name('police.index');

    Route::post(
        '/block/{id}',
        [BlockController::class, 'block']
    )
        ->whereNumber('id')
        ->middleware('throttle:20,1')
        ->name('user.block');

    Route::post(
        '/unblock/{id}',
        [BlockController::class, 'unblock']
    )
        ->whereNumber('id')
        ->middleware('throttle:20,1')
        ->name('user.unblock');

    Route::post(
        '/report',
        [ReportController::class, 'store']
    )
        ->middleware('throttle:5,1')
        ->name('report.store');
});

/*
|--------------------------------------------------------------------------
| Admin Login Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get(
        '/admin/login',
        [AdminLoginController::class, 'showLoginForm']
    )->name('admin.login');

    Route::post(
        '/admin/login',
        [AdminLoginController::class, 'login']
    )
        ->middleware('throttle:5,1')
        ->name('admin.login.submit');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->middleware([
        'auth',
        'not_banned',
        'admin',
    ])
    ->name('admin.')
    ->group(function () {
        /*
        |--------------------------------------------------------------------------
        | Admin Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/',
            [AdmineLteController::class, 'index']
        )->name('dashboard');

        Route::get(
            '/statedashboard',
            [AdminStateController::class, 'index']
        )->name('statedashboard');

        /*
        |--------------------------------------------------------------------------
        | User Management
        |--------------------------------------------------------------------------
        */

        Route::prefix('users')->group(function () {
            Route::get(
                '/',
                [AdmineLteController::class, 'indexUser']
            )->name('users');

            Route::get(
                '/{user}',
                [AdmineLteController::class, 'showUser']
            )->name('users.show');

            Route::post(
                '/{user}/ban',
                [AdmineLteController::class, 'ban']
            )->name('users.ban');

            Route::delete(
                '/{user}',
                [AdmineLteController::class, 'destroy']
            )->name('users.destroy');

            Route::patch(
                '/make-admin/{id}',
                [AdminController::class, 'makeAdmin']
            )
                ->whereNumber('id')
                ->name('makeAdmin');

            Route::patch(
                '/toggle-ban/{id}',
                [AdminController::class, 'toggleBan']
            )
                ->whereNumber('id')
                ->name('toggleBan');
        });

        /*
        |--------------------------------------------------------------------------
        | Content Management
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/messages',
            [AdminMessageController::class, 'index']
        )->name('messages');

        Route::get(
            '/photos',
            [PhotoController::class, 'index']
        )->name('photos.index');

        Route::delete(
            '/photos/{id}',
            [PhotoController::class, 'destroy']
        )
            ->whereNumber('id')
            ->name('photos.destroy');

        /*
        |--------------------------------------------------------------------------
        | Reports Management
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/reports',
            [ReportController::class, 'index']
        )->name('reports');

        Route::post(
            '/reports/{report}/resolve',
            [ReportController::class, 'resolve']
        )->name('reports.resolve');

        Route::delete(
            '/reports/{report}',
            [ReportController::class, 'destroy']
        )->name('reports.destroy');

        /*
        |--------------------------------------------------------------------------
        | Ticket Management
        |--------------------------------------------------------------------------
        */

        Route::prefix('tickets')
            ->name('tickets.')
            ->group(function () {
                Route::get(
                    '/',
                    [AdminTicketController::class, 'index']
                )->name('index');

                Route::get(
                    '/{id}',
                    [AdminTicketController::class, 'show']
                )
                    ->whereNumber('id')
                    ->name('show');

                Route::post(
                    '/{id}/reply',
                    [AdminTicketController::class, 'reply']
                )
                    ->whereNumber('id')
                    ->name('reply');

                Route::post(
                    '/{id}/close',
                    [AdminTicketController::class, 'close']
                )
                    ->whereNumber('id')
                    ->name('close');
            });
    });