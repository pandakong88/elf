<?php

use App\Modules\Core\Http\Controllers\MasterDataController;
use App\Modules\Core\Http\Controllers\OrganizationController;
use App\Modules\Core\Http\Controllers\PersonController;
use App\Modules\Core\Http\Controllers\UserController;
use App\Modules\Core\Http\Controllers\WorkflowController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ELF Core Domain API Routes
| Prefix: /api/v1/core (diset di routes/api.php)
| Middleware: api, auth:sanctum (diset di routes/api.php)
|--------------------------------------------------------------------------
*/

// ─── Organizations ─────────────────────────────────────────────────────────
Route::prefix('organizations')->group(function () {
    Route::get('/',        [OrganizationController::class, 'index'])->name('organizations.index');
    Route::get('/tree',    [OrganizationController::class, 'tree'])->name('organizations.tree');
    Route::get('/{organization}', [OrganizationController::class, 'show'])->name('organizations.show');
    Route::post('/',       [OrganizationController::class, 'store'])->name('organizations.store');
    Route::put('/{organization}', [OrganizationController::class, 'update'])->name('organizations.update');
});

// ─── Persons ───────────────────────────────────────────────────────────────
Route::prefix('persons')->group(function () {
    Route::get('/',                           [PersonController::class, 'index'])->name('persons.index');
    Route::post('/',                          [PersonController::class, 'store'])->name('persons.store');
    Route::get('/{person}',                   [PersonController::class, 'show'])->name('persons.show');
    Route::put('/{person}',                   [PersonController::class, 'update'])->name('persons.update');
    Route::delete('/{person}',                [PersonController::class, 'destroy'])->name('persons.destroy');
    Route::post('/{person}/roles',            [PersonController::class, 'assignRole'])->name('persons.roles.assign');
});

// ─── Master Data ───────────────────────────────────────────────────────────
Route::prefix('master-data')->group(function () {
    Route::get('/',               [MasterDataController::class, 'index'])->name('master-data.index');
    Route::post('/',              [MasterDataController::class, 'store'])->name('master-data.store');
    Route::put('/{masterData}',   [MasterDataController::class, 'update'])->name('master-data.update');
});

// ─── Workflows ─────────────────────────────────────────────────────────────
Route::prefix('workflows')->group(function () {
    Route::post('/initiate',              [WorkflowController::class, 'initiate'])->name('workflows.initiate');
    Route::get('/{instance}',            [WorkflowController::class, 'show'])->name('workflows.show');
    Route::post('/{instance}/advance',   [WorkflowController::class, 'advance'])->name('workflows.advance');
    Route::post('/{instance}/reject',    [WorkflowController::class, 'reject'])->name('workflows.reject');
});

// ─── Users ─────────────────────────────────────────────────────────────────
Route::prefix('users')->group(function () {
    Route::get('/',                 [UserController::class, 'index'])->name('users.index');
    Route::post('/',                [UserController::class, 'store'])->name('users.store');
    Route::get('/{user}',           [UserController::class, 'show'])->name('users.show');
    Route::put('/{user}',           [UserController::class, 'update'])->name('users.update');
    Route::delete('/{user}',        [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/{user}/roles',    [UserController::class, 'assignRole'])->name('users.roles.assign');
    Route::delete('/{user}/roles',  [UserController::class, 'revokeRole'])->name('users.roles.revoke');
});
