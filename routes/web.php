<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;
use Illuminate\Support\Facades\Route;

// Landing page route (accessible to everyone)
Route::get('/', function () {
    return view('landingpage');
})->name('landing');

// Public routes
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    // Profile routes - Using custom profile page for all users
    Route::get('/profile', function () {
        return view('profile.new-edit');
    })->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Dashboard routes based on role
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isOwner()) {
            return redirect()->route('owner.dashboard');
        }
        // Client dashboard - show the client dashboard view
        return view('client.dashboard');
    })->name('dashboard');
    
    // Redirect authenticated users from landing to dashboard
    Route::get('/home', function () {
        return redirect()->route('dashboard');
    })->name('home');
});

// Admin Routes
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/users', function () {
        return view('admin.users');
    })->name('users');
    
    Route::get('/bookings', function () {
        return view('admin.bookings');
    })->name('bookings');
    
    Route::get('/messages', [\App\Http\Controllers\MessageController::class, 'adminIndex'])
        ->name('messages');
});

// Owner Routes
Route::middleware(['auth', 'verified'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('/accommodations', \App\Http\Controllers\AccommodationController::class);
});

// Client Accommodation Routes (public to authenticated users)
Route::middleware(['auth', 'verified'])->prefix('accommodations')->name('accommodations.')->group(function () {
    Route::get('/', [\App\Http\Controllers\AccommodationController::class, 'index'])->name('index');
    Route::get('/{accommodation}', [\App\Http\Controllers\AccommodationController::class, 'show'])->name('show');
    Route::post('/{accommodation}/book', [\App\Http\Controllers\BookingController::class, 'store'])->name('book');
});

// Booking Routes
Route::middleware(['auth', 'verified'])->prefix('bookings')->name('bookings.')->group(function () {
    Route::get('/', [\App\Http\Controllers\BookingController::class, 'index'])->name('index');
    Route::get('/{booking}', [\App\Http\Controllers\BookingController::class, 'show'])->name('show');
    Route::put('/{booking}/status', [\App\Http\Controllers\BookingController::class, 'updateStatus'])->name('update-status');
    Route::put('/{booking}/cancel', [\App\Http\Controllers\BookingController::class, 'cancel'])->name('cancel');
    Route::put('/{booking}/mark-paid', [\App\Http\Controllers\BookingController::class, 'markAsPaid'])->name('mark-paid');
    Route::put('/{booking}/complete', [\App\Http\Controllers\BookingController::class, 'complete'])->name('complete');
    Route::post('/{booking}/message', [\App\Http\Controllers\BookingController::class, 'sendMessage'])->name('message');
});

// Message Routes
Route::middleware(['auth', 'verified'])->prefix('messages')->name('messages.')->group(function () {
    Route::get('/', [\App\Http\Controllers\MessageController::class, 'index'])->name('index');
    Route::get('/{message}', [\App\Http\Controllers\MessageController::class, 'show'])->name('show');
    Route::post('/', [\App\Http\Controllers\MessageController::class, 'store'])->name('store');
    Route::post('/{message}/reply', [\App\Http\Controllers\MessageController::class, 'reply'])->name('reply');
    Route::put('/{message}/read', [\App\Http\Controllers\MessageController::class, 'markAsRead'])->name('mark-read');
    Route::put('/{message}/archive', [\App\Http\Controllers\MessageController::class, 'archive'])->name('archive');
    Route::delete('/{message}', [\App\Http\Controllers\MessageController::class, 'destroy'])->name('destroy');
});

require __DIR__.'/auth.php';

