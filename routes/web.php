<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\MultiStepRegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PasswordChangeController;
use App\Http\Controllers\ConsumablesController;
use App\Http\Controllers\ConsumableRequestController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\AccountSettingsController;
use App\Http\Controllers\AccountReactivationController;
use App\Http\Controllers\SystemSettingsController;

// ── Landing ──
Route::get('/', function () {
    if (\App\Models\SystemStatus::isDown('cs')) {
        // Logged-in admins can still see welcome page
        $user = auth()->user();
        if (!$user || !in_array($user->role, ['admin', 'superadmin'])) {
            $status = \App\Models\SystemStatus::current('cs');
            return response()->view('maintenance', compact('status'), 503);
        }
    }
    return view('welcome');
})->name('home');

// ── Multi-step Registration ──
Route::get('/register',              [MultiStepRegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/register/send-otp',   [MultiStepRegisterController::class, 'sendOtp'])->name('register.send-otp');
Route::post('/register/verify-otp', [MultiStepRegisterController::class, 'verifyOtp'])->name('register.verify-otp');
Route::get('/register/departments', [MultiStepRegisterController::class, 'getDepartments'])->name('register.departments');
Route::post('/register',            [MultiStepRegisterController::class, 'register'])->name('register.submit');

// ── Forgot Password ──
Route::get('/forgot-password',         [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password/send',   [ForgotPasswordController::class, 'sendCode'])->name('password.send');
Route::post('/forgot-password/verify', [ForgotPasswordController::class, 'verifyCode'])->name('password.verify');
Route::post('/forgot-password/reset',  [ForgotPasswordController::class, 'resetPassword'])->name('password.reset');

// ── Account Reactivation (public — no auth needed) ──
Route::post('/account/reactivate', [AccountReactivationController::class, 'reactivate'])->name('account.reactivate');
Route::post('/account/cancel-reactivate', [AccountReactivationController::class, 'cancel'])->name('account.cancel-reactivate');

// ── Built-in Auth (login/logout only) ──
Auth::routes(['register' => false, 'reset' => false]);

// ── Authenticated Routes ──
Route::middleware(['auth'])->group(function () {

    // ── Dashboard ──
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Password ──
    Route::put('/password/change', [PasswordChangeController::class, 'update'])->name('password.change');

    // ── Consumables (Browse + Admin Inventory) ──
    Route::get('/consumables', [ConsumablesController::class, 'index'])->name('consumables');

    // ── Consumable Requests ──
    Route::get('/consumable-requests',                              [ConsumableRequestController::class, 'index'])->name('consumable-requests');
    Route::post('/consumable-requests',                             [ConsumableRequestController::class, 'store'])->name('consumable-requests.store');
    Route::get('/consumable-requests/available-items',             [ConsumableRequestController::class, 'availableItems'])->name('consumable-requests.available-items');
    Route::get('/consumable-requests/{consumableRequest}',         [ConsumableRequestController::class, 'show'])->name('consumable-requests.show');
    Route::get('/consumable-requests/{consumableRequest}/report',  [ConsumableRequestController::class, 'report'])->name('consumable-requests.report');

    // ── Consumable Reports (Admin/Super Admin) ──
    Route::get('/consumables/reports', function () {
        return 'Consumption Reports — coming soon';
    })->name('consumables.reports');

    // ── Notifications (Admin/Super Admin) ──
    Route::get('/notifications',          [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/poll',     [NotificationController::class, 'poll'])->name('notifications.poll');
    Route::post('/notifications/{deletionRequest}/approve', [NotificationController::class, 'approve'])->name('notifications.approve');
    Route::post('/notifications/{deletionRequest}/reject',  [NotificationController::class, 'reject'])->name('notifications.reject');

    // ── Users (Admin/Super Admin) ──
    Route::get('/users',                  [UserManagementController::class, 'index'])->name('users');
    Route::get('/users/{user}',           [UserManagementController::class, 'show'])->name('users.show');
    Route::patch('/users/{user}/approve', [UserManagementController::class, 'approve'])->name('users.approve');
    Route::delete('/users/{user}',        [UserManagementController::class, 'destroy'])->name('users.destroy');

    // ── Account Settings ──
    Route::get('/account',                [AccountSettingsController::class, 'index'])->name('account.settings');
    Route::post('/account/deactivate',    [AccountSettingsController::class, 'deactivate'])->name('account.deactivate');
    Route::post('/account/request-deletion', [AccountSettingsController::class, 'requestDeletion'])->name('account.request-deletion');

    // ── System Settings (Super Admin only) ──
    Route::get('/system-settings', [SystemSettingsController::class, 'index'])->name('system.settings');

});