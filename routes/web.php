<?php

use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\TicketController as AdminTicketController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Portal\DashboardController as PortalDashboardController;
use App\Http\Controllers\Portal\ProjectController as PortalProjectController;
use App\Http\Controllers\Portal\TicketController as PortalTicketController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/home');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'create'])
        ->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])
        ->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'create'])
        ->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'store'])
        ->name('password.update');
});

Route::middleware(['auth', 'active'])->group(function (): void {
    Route::get('/home', HomeController::class)->name('home');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])
        ->name('profile.password.update');

    Route::get('/attachments/{attachment}/download', [AttachmentController::class, 'download'])
        ->name('attachments.download');

    Route::prefix('admin')
        ->name('admin.')
        ->middleware('role:admin')
        ->group(function (): void {
            Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');

            Route::resource('customers', AdminCustomerController::class)
                ->only(['index', 'create', 'store', 'edit', 'update']);

            Route::resource('projects', AdminProjectController::class)
                ->only(['index', 'create', 'store', 'show', 'edit', 'update']);

            Route::get('/tickets', [AdminTicketController::class, 'index'])->name('tickets.index');
            Route::get('/tickets/{ticket}', [AdminTicketController::class, 'show'])->name('tickets.show');
            Route::post('/tickets/{ticket}/replies', [AdminTicketController::class, 'reply'])
                ->name('tickets.reply');
            Route::patch('/tickets/{ticket}/status', [AdminTicketController::class, 'updateStatus'])
                ->name('tickets.status.update');
        });

    Route::prefix('panel')
        ->name('portal.')
        ->middleware('role:customer')
        ->group(function (): void {
            Route::get('/dashboard', PortalDashboardController::class)->name('dashboard');

            Route::get('/projects', [PortalProjectController::class, 'index'])->name('projects.index');
            Route::get('/projects/{project}', [PortalProjectController::class, 'show'])->name('projects.show');

            Route::get('/tickets', [PortalTicketController::class, 'index'])->name('tickets.index');
            Route::get('/tickets/create', [PortalTicketController::class, 'create'])->name('tickets.create');
            Route::post('/tickets', [PortalTicketController::class, 'store'])->name('tickets.store');
            Route::get('/tickets/{ticket}', [PortalTicketController::class, 'show'])->name('tickets.show');
            Route::post('/tickets/{ticket}/replies', [PortalTicketController::class, 'reply'])
                ->name('tickets.reply');
        });
});
