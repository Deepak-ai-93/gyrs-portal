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

/*
|--------------------------------------------------------------------------
| Google OAuth
|--------------------------------------------------------------------------
*/

Route::get('/auth/google/redirect', [Auth\GoogleLoginController::class, 'redirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [Auth\GoogleLoginController::class, 'callback'])->name('auth.google.callback');

/*
|--------------------------------------------------------------------------
| Candidate authentication
|--------------------------------------------------------------------------
*/

Route::prefix('candidate')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [Auth\CandidateLoginController::class, 'create'])->name('candidate.login');
        Route::post('/login', [Auth\CandidateLoginController::class, 'store']);

        Route::post('/otp/request', [Auth\CandidateOtpController::class, 'requestCode'])
            ->middleware('throttle:otp-request')
            ->name('candidate.otp.request');
        Route::post('/otp/verify', [Auth\CandidateOtpController::class, 'verifyCode'])
            ->middleware('throttle:otp-verify')
            ->name('candidate.otp.verify');

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

    Route::get('/phone/verify', [Auth\CandidateOtpController::class, 'verifyPhone'])->name('candidate.phone.verify');
    Route::post('/phone/verify', [Auth\CandidateOtpController::class, 'sendPhoneCode'])
        ->middleware('throttle:otp-request')
        ->name('candidate.phone.send');
    Route::post('/phone/confirm', [Auth\CandidateOtpController::class, 'confirmPhone'])
        ->middleware('throttle:otp-verify')
        ->name('candidate.phone.confirm');

    Route::get('/career', [Candidate\CareerAssistantController::class, 'index'])->name('candidate.career.index');
    Route::post('/career/analyze', [Candidate\CareerAssistantController::class, 'store'])
        ->middleware('throttle:ai-resume')
        ->name('candidate.career.analyze');
    Route::get('/career/{analysis}', [Candidate\CareerAssistantController::class, 'show'])->name('candidate.career.show');
    Route::delete('/career/{analysis}', [Candidate\CareerAssistantController::class, 'destroy'])->name('candidate.career.destroy');
    Route::post('/career/{analysis}/apply', [Candidate\CareerAssistantController::class, 'apply'])->name('candidate.career.apply');
    Route::patch('/career/skills/{plan}', [Candidate\CareerAssistantController::class, 'updatePlan'])->name('candidate.career.plan.update');
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

    // Job postings. Write routes are registered first so /jobs/create is not
    // swallowed by the /jobs/{job} show route.
    Route::middleware('permission:jobs.manage')->group(function () {
        Route::get('/jobs/create', [Admin\JobController::class, 'create'])->name('jobs.create');
        Route::post('/jobs', [Admin\JobController::class, 'store'])->name('jobs.store');
        Route::get('/jobs/{job}/edit', [Admin\JobController::class, 'edit'])->name('jobs.edit');
        Route::match(['put', 'patch'], '/jobs/{job}', [Admin\JobController::class, 'update'])->name('jobs.update');
        Route::delete('/jobs/{job}', [Admin\JobController::class, 'destroy'])->name('jobs.destroy');
    });

    Route::middleware('permission:jobs.view')->group(function () {
        Route::get('/jobs', [Admin\JobController::class, 'index'])->name('jobs.index');
        Route::get('/jobs/{job}', [Admin\JobController::class, 'show'])->name('jobs.show');
    });

    Route::middleware('permission:candidates.view')->group(function () {
        Route::get('/candidates', [Admin\CandidateController::class, 'index'])->name('candidates.index');
        Route::get('/candidates/{candidate}', [Admin\CandidateController::class, 'show'])->name('candidates.show');
    });

    Route::patch('/candidates/{candidate}/status', [Admin\CandidateController::class, 'updateStatus'])
        ->middleware('permission:candidates.verify')
        ->name('candidates.status');

    Route::get('/applications', [Admin\ApplicationController::class, 'index'])
        ->middleware('permission:applications.view')
        ->name('applications.index');

    Route::patch('/applications/{application}/status', [Admin\ApplicationController::class, 'updateStatus'])
        ->middleware('permission:applications.manage')
        ->name('applications.status');

    Route::get('/exports/candidates.csv', [Admin\ExportController::class, 'candidates'])
        ->middleware('permission:exports.candidates')
        ->name('exports.candidates');

    // Access control — reserved for super administrators.
    Route::middleware('super_admin')->group(function () {
        Route::resource('administrators', Admin\AdministratorController::class)->except(['show']);
    });
});
