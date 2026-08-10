<?php

use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CriterionController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProportionController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', fn () => redirect()->route('assessments.index'));

    // Master data: index boleh semua user (lihat), create/edit/delete Owner-only
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::middleware('owner')->group(function () {
        Route::resource('employees', EmployeeController::class)->except(['index', 'show']);
    });

    Route::get('/criteria', [CriterionController::class, 'index'])->name('criteria.index');
    Route::middleware('owner')->group(function () {
        Route::resource('criteria', CriterionController::class)->except(['index', 'show']);
    });

    // Setting proporsi penilaian per jabatan (Owner-only)
    Route::middleware('owner')->group(function () {
        Route::get('/proportions', [ProportionController::class, 'edit'])->name('proportions.edit');
        Route::put('/proportions', [ProportionController::class, 'update'])->name('proportions.update');
        Route::get('/owner/assessments', [AssessmentController::class, 'ownerOverview'])->name('assessments.ownerOverview');
    });

    // Penilaian: semua user boleh membuat & mengelola penilaian sesuai hierarki-nya
    Route::resource('assessments', AssessmentController::class);
    Route::post('assessments/employee', [AssessmentController::class, 'storeEmployee'])
        ->name('assessments.storeEmployee');
    Route::patch('assessments/{assessment}/info', [AssessmentController::class, 'updateInfo'])->name('assessments.updateInfo');

    Route::get('/ranking/{assessment}', [RankingController::class, 'show'])->name('ranking.show');

    // Pengaturan akun (ganti nama & password)
    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
});
