<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Middleware\AdminAuthMiddleware; 
Route::view('/', 'business.auth');
// Admin routes with prefix
    Route::prefix('admin')->name('admin.')->group(function () {

            Route::get('/login', [AdminController::class, 'showLoginForm'])->name('login');
            Route::post('/login', [AdminController::class, 'submitLogin'])->name('login.submit');
            Route::post('/verify-otp', [AdminController::class, 'verifyOTP'])->name('verify.otp');
            Route::get('/resend-otp', [AdminController::class, 'resendOTP'])->name('resend.otp');
            Route::post('/forgot-password', [AdminController::class, 'forget_sendOtp'])
                ->name('forgot.password.submit');

            Route::post('/forgot-password/verify-otp', [AdminController::class, 'forget_verifyOtp'])
                ->name('forgot.password.verify.otp');

            Route::get('/forgot-password/resend-otp', [AdminController::class, 'forget_resendOtp'])
                ->name('forgot.password.resend.otp');

            Route::post('/reset-password', [AdminController::class, 'forget_resetPassword'])
                ->name('reset.password.submit');
    // Authenticated routes
        Route::middleware(AdminAuthMiddleware::class)->group(function () {
            Route::post('/logout', [AdminController::class, 'logout'])->name('logout');

            // Dashboard and other admin routes...
            Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
            Route::get('/users', [AdminController::class, 'get_user'])->name('user');
            Route::get('/users/search', [AdminController::class, 'search'])->name('users.search');
            Route::delete('/users/{id}', [AdminController::class, 'destroy'])->name('users.destroy');
            Route::put('/users/{id}/block', [AdminController::class, 'blockUser'])->name('users.block');
            Route::put('/users/{id}/unblock', [AdminController::class, 'unblockUser'])->name('users.unblock');
            Route::put('/users/{id}/reset-password', [AdminController::class, 'resetUserPassword'])->name('users.reset.password');
            Route::get('/audit-log', [AdminController::class, 'audit_log'])->name('audit-log');
            Route::get('/audit-log/search', [AdminController::class, 'audit_search'])->name('audit-log.search');
            Route::get('/audit-log/export', [AdminController::class, 'export'])->name('audit-log.export');
            Route::get('/roles', [AdminController::class, 'get_roles'])->name('roles');
            Route::get('/roles/search', [AdminController::class, 'role_search'])->name('roles.search');
            Route::post('/roles', [AdminController::class, 'role_store'])->name('roles.store');
            Route::put('/roles/{id}', [AdminController::class, 'role_update'])->name('roles.update');
            Route::delete('/roles/{id}', [AdminController::class, 'role_destroy'])->name('roles.destroy');

            // Dashboard routes
            Route::get('/admin-login', function () {
                return view('business.admin-login');
            })->name('admin.admin-login');

            Route::get('/coin-table', function () {
                return view('business.coin-table');
            })->name('admin.coin-table');

            Route::get('/launch-button', function () {
                return view('business.launch-button');
            })->name('admin.launch-button');

            Route::get('/threshold-alerts', function () {
                return view('business.threshold-alerts');
            })->name('admin.threshold-alerts');

            Route::get('/rewards-analytics', function () {
                return view('business.rewards-analytics');
            })->name('admin.rewards-analytics');

            Route::get('/socials-moderation', function () {
                return view('business.socials-moderation');
            })->name('admin.socials-moderation');

            Route::get('/kill-switches', function () {
                return view('business.kill-switches');
            })->name('admin.kill-switches');

            // User routes
            Route::get('/user', function () {
                return view('business.user');
            })->name('admin.user');

            Route::get('/user-activity', function () {
                return view('business.user-activity');
            })->name('admin.user-activity');

            // CloudWatch routes
            Route::get('/cloudwatch', function () {
                return view('business.cloud_watch');
            })->name('admin.cloudwatch');

            Route::get('/api-errors', function () {
                return view('business.api-errors');
            })->name('admin.api-errors');

            Route::get('/backend-downtime', function () {
                return view('business.backend-downtime');
            })->name('admin.backend-downtime');

            Route::get('/budget-alerts', function () {
                return view('business.budget-alerts');
            })->name('admin.budget-alerts');

            Route::get('/job-failures', function () {
                return view('business.job-failures');
            })->name('admin.job-failures');

            // Admin Toggle routes
            Route::get('/admin-toggle', function () {
                return view('business.admin_toggle');
            })->name('admin.admin-toggle');

            Route::get('/disable-coin-creation', function () {
                return view('business.disable-coin-creation');
            })->name('admin.disable-coin-creation');

            Route::get('/pause-trading', function () {
                return view('business.pause-trading');
            })->name('admin.pause-trading');

            Route::get('/pause-pancakeswap', function () {
                return view('business.pause-pancakeswap');
            })->name('admin.pause-pancakeswap');

            Route::get('/freeze-rewards', function () {
                return view('business.freeze-rewards');
            })->name('admin.freeze-rewards');

            Route::get('/blacklist-wallet', function () {
                return view('business.blacklist-wallet');
            })->name('admin.blacklist-wallet');

            // Account Settings route
            Route::get('/account-settings', function () {
                return view('business.account_setting');
            })->name('admin.account-settings');
        });

});

Route::view('/dashboard', 'business.dashboard');
Route::view('/restaurants', 'business.resturants');
Route::view('/tables', 'business.tables');
Route::view('/menu', 'business.menu');
Route::view('/offers', 'business.special-offers');
Route::view('/settings', 'business.settings');



// Auth endpoints (temporary placeholders)
Route::post('/login', function () {
    // TODO: handle login later
    return back();
})->name('login');

Route::post('/register', function () {
    // TODO: handle signup later
    // Example: after signup you can show verify section by setting session flag
    return back()->with('auth_view', 'verify');
})->name('register');

Route::post('/auth/verify', function () {
    // TODO: verify OTP later
    return back()->with('auth_view', 'login');
})->name('auth.verify');

Route::get('/auth/resend', function () {
    // TODO: resend OTP later
    return back()->with('auth_view', 'verify');
})->name('auth.resend');