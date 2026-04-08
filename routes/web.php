<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicBookingController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified', 'onboarding'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/horario', [DashboardController::class, 'updateSchedule'])->name('dashboard.schedule.update');
    Route::post('/dashboard/horario-semanal', [DashboardController::class, 'updateWeeklySchedule'])->name('dashboard.weekly-schedule.update');
    Route::post('/dashboard/citas', [DashboardController::class, 'storeManual'])->name('dashboard.bookings.store');
    Route::delete('/dashboard/citas/{booking}', [DashboardController::class, 'cancel'])->name('dashboard.bookings.cancel');
    Route::patch('/dashboard/citas/{booking}/confirm', [DashboardController::class, 'confirm'])->name('dashboard.bookings.confirm');

    Route::get('/facturacion', [\App\Http\Controllers\BillingController::class, 'index'])->name('billing.index');

    Route::get('/servicios',              [ServiceController::class, 'index'])->name('services.index');
    Route::post('/servicios',             [ServiceController::class, 'store'])->name('services.store');
    Route::patch('/servicios/{service}',  [ServiceController::class, 'update'])->name('services.update');
    Route::delete('/servicios/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Endpoints de agenda para propietario (auth requerido)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/{slug}/agenda-propietario', [PublicBookingController::class, 'ownerCalendar'])->name('booking.owner-calendar');
    Route::get('/{slug}/dia',                [PublicBookingController::class, 'dayBookings'])->name('booking.day-bookings');
});

Route::middleware(['auth', 'verified'])->prefix('onboarding')->name('onboarding.')->group(function () {
    Route::get('/{step}', [OnboardingController::class, 'show'])->name('step');
    Route::post('/1', [OnboardingController::class, 'storeStep1'])->name('store1');
    Route::post('/2', [OnboardingController::class, 'storeStep2'])->name('store2');
    Route::post('/3', [OnboardingController::class, 'storeStep3'])->name('store3');
});

require __DIR__.'/auth.php';

Route::get('/confirmar-cita/{booking}', [\App\Http\Controllers\BookingConfirmationController::class, 'confirm'])
    ->name('booking.confirm')
    ->middleware('signed');

Route::get('/cancelar-cita/{booking}', [\App\Http\Controllers\BookingConfirmationController::class, 'cancel'])
    ->name('booking.cancel')
    ->middleware('signed');

// Gestión de reservas para clientes
use App\Http\Controllers\CustomerBookingController;

Route::prefix('mis-reservas')->name('customer.')->group(function () {
    Route::get('/login',     [CustomerBookingController::class, 'showLogin'])->name('login');
    Route::post('/login',    [CustomerBookingController::class, 'sendOtp'])->name('login.post');
    Route::get('/verificar', [CustomerBookingController::class, 'showVerify'])->name('verify');
    Route::post('/verificar',[CustomerBookingController::class, 'verifyOtp'])->name('verify.post');
    
    Route::middleware([\App\Http\Middleware\EnsureCustomerLoggedIn::class])->group(function () {
        Route::get('/',              [CustomerBookingController::class, 'index'])->name('index');
        Route::get('/{booking}/reprogramar', [CustomerBookingController::class, 'reschedule'])->name('reschedule');
        Route::post('/{booking}/reprogramar', [CustomerBookingController::class, 'update'])->name('reschedule.post');
        Route::post('/{booking}/cancel', [CustomerBookingController::class, 'cancel'])->name('cancel');
        Route::post('/logout',       [CustomerBookingController::class, 'logout'])->name('logout');
    });
});

// Página pública de reservas — al final para no colisionar con rutas anteriores
Route::get('/{slug}/confirmada',    [PublicBookingController::class, 'confirmed'])->name('booking.confirmed');
Route::get('/{slug}/verificar/{pending_booking}', [PublicBookingController::class, 'showVerify'])->name('booking.verify');
Route::post('/{slug}/verificar/{pending_booking}', [PublicBookingController::class, 'verifyCode'])->name('booking.verify.post');
Route::get('/{slug}/calendario',    [PublicBookingController::class, 'calendar'])->name('booking.calendar');
Route::get('/{slug}/disponibilidad',[PublicBookingController::class, 'availability'])->name('booking.availability');
Route::post('/{slug}/reservar',     [PublicBookingController::class, 'store'])->name('booking.store');
Route::get('/{slug}',               [PublicBookingController::class, 'show'])->name('booking.show');


