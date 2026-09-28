<?php
// TEST

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\CalendarEventController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/help', function () {
    return view('help');
})->name('help');

Route::get('/terminos', function () {
    return view('terms.terms');
})->name('terminos');

Route::get('/privacidad', function () {
    return view('terms.privacy');
})->name('privacidad');

Route::get('/aviso-legal', function () {
    return view('terms.aviso-legal');
})->name('aviso-legal');

Route::get('/contacto', function () {
    return view('terms.contact');
})->name('contacto');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/onboarding', [OnboardingController::class, 'show'])->name('onboarding');
    Route::post('/onboarding', [OnboardingController::class, 'store'])->name('onboarding.store');

    Route::get('/boards', [BoardController::class, 'index'])->name('boards.index');
    Route::get('/boards/create', [BoardController::class, 'create'])->name('boards.create');
    Route::post('/boards', [BoardController::class, 'store'])->name('boards.store');
    Route::get('/boards/{board}/content', [BoardController::class, 'content'])->name('boards.content');
    Route::get('/boards/{board}', [BoardController::class, 'show'])->name('boards.show');
    Route::put('/boards/{board}', [BoardController::class, 'update'])->name('boards.update');
    Route::delete('/boards/{board}', [BoardController::class, 'destroy'])->name('boards.destroy');

    Route::post('/todos', [TodoController::class, 'store'])->name('todos.store');
    Route::patch('/todos/{todo}/toggle', [TodoController::class, 'toggle'])->name('todos.toggle');
    Route::put('/todos/{todo}', [TodoController::class, 'update'])->name('todos.update');
    Route::delete('/todos/{todo}', [TodoController::class, 'destroy'])->name('todos.destroy');
    Route::get('/calendar', function () {
        return view('calendar');
    })->name('calendar');
    Route::get('/calendar/events', [CalendarEventController::class, 'index'])->name('calendar.events.index');
    Route::put('/calendar/events/{date}', [CalendarEventController::class, 'store'])
        ->where('date', '\\d{4}-\\d{2}-\\d{2}')
        ->name('calendar.events.store');
    Route::delete('/calendar/events/{date}', [CalendarEventController::class, 'destroy'])
        ->where('date', '\\d{4}-\\d{2}-\\d{2}')
        ->name('calendar.events.destroy');

    Route::get('/settings', function () {
        return view('settings.index');
    })->name('settings.index');
});

Route::get('/hello', function () { return 'hello'; });
Route::get('/test-session', function () { session(['test' => 'value']); return session('test'); });
require __DIR__.'/auth.php';