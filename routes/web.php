<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth;
use App\Http\Controllers\Candidate;
use App\Http\Controllers\Public\JobController as PublicJobController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/

Route::get('/', [PublicJobController::class, 'index'])->name('home');
Route::get('/jobs/{job:slug}', [PublicJobController::class, 'show'])->name('jobs.show');

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
})->name('health');

if (app()->environment('local')) {
    Route::get('/dev/login-admin', function () {
        $admin = \App\Models\User::query()->where('role', \App\Models\User::ROLE_ADMIN)->firstOrFail();
        auth()->login($admin);

        return redirect()->route('admin.dashboard');
    })->name('dev.login.admin');

    Route::get('/dev/login-candidate', function () {
        $candidate = \App\Models\User::query()->where('role', \App\Models\User::ROLE_CANDIDATE)->firstOrFail();
        auth()->login($candidate);

        return redirect()->route('candidate.dashboard');
    })->name('dev.login.candidate');
}

/*
|--------------------------------------------------------------------------
| Candidate authentication
|--------------------------------------------------------------------------
*/

Route::prefix('candidate')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [Auth\CandidateLoginController::class, 'create'])->name('candidate.login');
        Route::post('/login', [Auth\CandidateLoginController::class, 'store']);

        Route::get('/register', [Auth\CandidateRegisterController::class, 'create'])->name('candidate.register');
        Route::post('/register', [Auth\CandidateRegisterController::class, 'store']);
    });

    Route::middleware('auth')->group(function () {
        Route::post('/logout', [Auth\CandidateLoginController::class, 'destroy'])->name('candidate.logout');
    });
});

/*
|--------------------------------------------------------------------------
| Candidate application actions
|--------------------------------------------------------------------------
*/

Route::post('/jobs/{job}/applications', [Candidate\ApplicationController::class, 'store'])
    ->middleware(['auth', 'role:candidate'])
    ->name('jobs.applications.store');

Route::post('/jobs/{job}/interested', [Candidate\InterestedJobController::class, 'toggle'])
    ->middleware(['auth', 'role:candidate'])
    ->name('candidate.interested.toggle');

/*
|--------------------------------------------------------------------------
| Candidate authenticated area
|--------------------------------------------------------------------------
*/

Route::prefix('candidate')->middleware(['auth', 'role:candidate'])->group(function () {
    Route::get('/dashboard', [Candidate\DashboardController::class, 'index'])->name('candidate.dashboard');
    Route::patch('/profile', [Candidate\ProfileController::class, 'update'])->name('candidate.profile.update');
    Route::get('/interested', [Candidate\InterestedJobController::class, 'index'])->name('candidate.interested.index');
});

/*
|--------------------------------------------------------------------------
| Admin authentication
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [Auth\AdminLoginController::class, 'create'])->name('login');
        Route::post('/login', [Auth\AdminLoginController::class, 'store']);
    });
});

/*
|--------------------------------------------------------------------------
| Admin authenticated area
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::post('/logout', [Auth\AdminLoginController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('jobs', Admin\JobController::class);

    Route::get('/candidates', [Admin\CandidateController::class, 'index'])->name('candidates.index');
    Route::get('/candidates/{candidate}', [Admin\CandidateController::class, 'show'])->name('candidates.show');
    Route::patch('/candidates/{candidate}/status', [Admin\CandidateController::class, 'updateStatus'])->name('candidates.status');

    Route::get('/applications', [Admin\ApplicationController::class, 'index'])->name('applications.index');
    Route::patch('/applications/{application}/status', [Admin\ApplicationController::class, 'updateStatus'])->name('applications.status');

    Route::get('/exports/candidates.csv', [Admin\ExportController::class, 'candidates'])->name('exports.candidates');
});
