<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\{
    HomeController,
    AdminController,
    ReportController,
    MessageController,
    ProfileController,
    ProfilePhotoController,
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
    AdminTwoFactorController,
    ForgotPasswordController,
    VerificationController
};

use App\Http\Controllers\Admin\{
    AdmineLteController,
    AdminMessageController,
    AdminStateController,
    AdminTicketController,
    AdminAuditLogController,
    PhotoController
};

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get(
    '/',
    [LandingController::class, 'welcome']
);

Route::get(
    '/lang/{lang}',
    [LanguageController::class, 'switch']
);

Route::get('/test-modal', function () {
    return view('test-modal');
});

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(
    function (): void {
        Route::get(
            '/login',
            [
                LoginController::class,
                'showLoginForm',
            ]
        )->name('login');

        Route::post(
            '/login',
            [
                LoginController::class,
                'login',
            ]
        );

        Route::get(
            '/register',
            [
                RegisterController::class,
                'showRegistrationForm',
            ]
        )->name('register');

        Route::post(
            '/register',
            [
                RegisterController::class,
                'register',
            ]
        );
    }
);

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'not_banned',
])->group(function (): void {
    Route::post(
        '/logout',
        [
            LoginController::class,
            'logout',
        ]
    )->name('logout');

    /*
    |--------------------------------------------------------------------------
    | Email Verification
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/email/verify',
        [
            VerificationController::class,
            'show',
        ]
    )->name('verification.notice');

    Route::get(
        '/email/verify/{id}/{hash}',
        [
            VerificationController::class,
            'verify',
        ]
    )->name('verification.verify');

    Route::post(
        '/email/resend',
        [
            VerificationController::class,
            'resend',
        ]
    )->name('verification.resend');

    Route::get(
        '/home',
        [
            HomeController::class,
            'index',
        ]
    )->name('home');

    Route::get(
        '/dashboard',
        [
            DashboardController::class,
            'index',
        ]
    )->name('dashboard');

    Route::get(
        '/search',
        [
            ProfileController::class,
            'search',
        ]
    )->name('search');

    Route::get(
        '/profile-photo/{user}',
        [
            ProfilePhotoController::class,
            'show',
        ]
    )
        ->whereNumber('user')
        ->name('profile.photo');

    /*
    |--------------------------------------------------------------------------
    | Premium
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/upgrade',
        [
            PremiumController::class,
            'index',
        ]
    )->name('premium.upgrade');

    Route::post(
        '/upgrade/verify-crypto',
        [
            PremiumController::class,
            'verifyCrypto',
        ]
    )
        ->middleware('throttle:5,1')
        ->name('premium.verifyCrypto');

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    Route::prefix('profile')
        ->name('profile.')
        ->group(function (): void {
            Route::get(
                '/edit',
                [
                    ProfileController::class,
                    'edit',
                ]
            )->name('edit');

            Route::post(
                '/update',
                [
                    ProfileController::class,
                    'update',
                ]
            )->name('update');

            Route::get(
                '/id/{id}',
                [
                    ProfileController::class,
                    'show',
                ]
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
    )->name('password.email');

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
    )->name('password.update');

    /*
    |--------------------------------------------------------------------------
    | Messages
    |--------------------------------------------------------------------------
    */

    Route::prefix('messages')
        ->name('messages.')
        ->group(function (): void {
            Route::get(
                '/',
                [
                    MessageController::class,
                    'index',
                ]
            )->name('index');

            Route::get(
                '/{user}',
                [
                    MessageController::class,
                    'show',
                ]
            )->name('show');

            Route::post(
                '/',
                [
                    MessageController::class,
                    'store',
                ]
            )
                ->middleware('throttle:messages.store')
                ->name('store');
        });

    /*
    |--------------------------------------------------------------------------
    | Likes
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/likes',
        [
            LikeController::class,
            'index',
        ]
    )->name('likes.index');

    Route::get(
        '/likes/received',
        [
            LikeController::class,
            'index',
        ]
    )->name('likes.received');

    Route::post(
        '/like/{likedUserId}',
        [
            LikeController::class,
            'store',
        ]
    )
        ->whereNumber('likedUserId')
        ->name('like.store');

    /*
    |--------------------------------------------------------------------------
    | User Tickets
    |--------------------------------------------------------------------------
    */

    Route::prefix('dashboard/tickets')
        ->name('user.tickets.')
        ->group(function (): void {
            Route::get(
                '/',
                [
                    TicketController::class,
                    'index',
                ]
            )->name('index');

            Route::get(
                '/create',
                [
                    TicketController::class,
                    'create',
                ]
            )->name('create');

            Route::post(
                '/',
                [
                    TicketController::class,
                    'store',
                ]
            )
                ->middleware('throttle:tickets.store')
                ->name('store');
        });

    /*
    |--------------------------------------------------------------------------
    | Blocking and Reports
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/police',
        [
            BlockController::class,
            'index',
        ]
    )->name('police.index');

    Route::post(
        '/block/{id}',
        [
            BlockController::class,
            'block',
        ]
    )
        ->whereNumber('id')
        ->name('user.block');

    Route::post(
        '/unblock/{id}',
        [
            BlockController::class,
            'unblock',
        ]
    )
        ->whereNumber('id')
        ->name('user.unblock');

    Route::post(
        '/report',
        [
            ReportController::class,
            'store',
        ]
    )
        ->middleware('throttle:reports.store')
        ->name('report.store');
});

/*
|--------------------------------------------------------------------------
| Admin Login and 2FA
|--------------------------------------------------------------------------
*/

Route::get(
    '/admin/login',
    [
        AdminLoginController::class,
        'showLoginForm',
    ]
)->name('admin.login');

Route::post(
    '/admin/login',
    [
        AdminLoginController::class,
        'login',
    ]
)->name('admin.login.submit');

Route::get(
    '/admin/two-factor',
    [
        AdminTwoFactorController::class,
        'show',
    ]
)->name('admin.two-factor.form');

Route::post(
    '/admin/two-factor',
    [
        AdminTwoFactorController::class,
        'verify',
    ]
)
    ->middleware('throttle:10,1')
    ->name('admin.two-factor.verify');

Route::post(
    '/admin/two-factor/cancel',
    [
        AdminTwoFactorController::class,
        'cancel',
    ]
)->name('admin.two-factor.cancel');

/*
|--------------------------------------------------------------------------
| Protected Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->middleware([
        'auth',
        'not_banned',
        'admin',
        'admin.2fa',
    ])
    ->name('admin.')
    ->group(function (): void {
        Route::get(
            '/',
            [
                AdmineLteController::class,
                'index',
            ]
        )->name('dashboard');

        Route::get(
            '/statedashboard',
            [
                AdminStateController::class,
                'index',
            ]
        )->name('statedashboard');

        /*
        |--------------------------------------------------------------------------
        | User Management
        |--------------------------------------------------------------------------
        */

        Route::prefix('users')->group(
            function (): void {
                Route::get(
                    '/',
                    [
                        AdmineLteController::class,
                        'indexUser',
                    ]
                )->name('users');

                Route::get(
                    '/{user}',
                    [
                        AdmineLteController::class,
                        'showUser',
                    ]
                )->name('users.show');

                Route::post(
                    '/{user}/ban',
                    [
                        AdmineLteController::class,
                        'ban',
                    ]
                )->name('users.ban');

                Route::delete(
                    '/{user}',
                    [
                        AdmineLteController::class,
                        'destroy',
                    ]
                )->name('users.destroy');

                Route::patch(
                    '/{user}/make-admin',
                    [
                        AdminController::class,
                        'makeAdmin',
                    ]
                )->name('makeAdmin');
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Audit Logs
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/audit-logs',
            [
                AdminAuditLogController::class,
                'index',
            ]
        )->name('audit-logs.index');

        /*
        |--------------------------------------------------------------------------
        | Content Management
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/messages',
            [
                AdminMessageController::class,
                'index',
            ]
        )->name('messages');

        Route::get(
            '/photos',
            [
                PhotoController::class,
                'index',
            ]
        )->name('photos.index');

        Route::delete(
            '/photos/{id}',
            [
                PhotoController::class,
                'destroy',
            ]
        )
            ->whereNumber('id')
            ->name('photos.destroy');

        /*
        |--------------------------------------------------------------------------
        | Reports
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/reports',
            [
                ReportController::class,
                'index',
            ]
        )->name('reports');

        Route::post(
            '/reports/{report}/resolve',
            [
                ReportController::class,
                'resolve',
            ]
        )->name('reports.resolve');

        Route::delete(
            '/reports/{report}',
            [
                ReportController::class,
                'destroy',
            ]
        )->name('reports.destroy');

        /*
        |--------------------------------------------------------------------------
        | Tickets
        |--------------------------------------------------------------------------
        */

        Route::prefix('tickets')
            ->name('tickets.')
            ->group(function (): void {
                Route::get(
                    '/',
                    [
                        AdminTicketController::class,
                        'index',
                    ]
                )->name('index');

                Route::get(
                    '/{id}',
                    [
                        AdminTicketController::class,
                        'show',
                    ]
                )
                    ->whereNumber('id')
                    ->name('show');

                Route::post(
                    '/{id}/reply',
                    [
                        AdminTicketController::class,
                        'reply',
                    ]
                )
                    ->whereNumber('id')
                    ->name('reply');

                Route::post(
                    '/{id}/close',
                    [
                        AdminTicketController::class,
                        'close',
                    ]
                )
                    ->whereNumber('id')
                    ->name('close');
            });
    });
