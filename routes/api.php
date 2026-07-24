<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ELF API Routes
| Base: /api
|--------------------------------------------------------------------------
*/

// ─── API v1 ────────────────────────────────────────────────────────────────
Route::prefix('v1')->group(function () {

    // Auth routes (public / login & internal auth:api are handled inside)
    Route::prefix('auth')
         ->name('api.v1.auth.')
         ->group(base_path('routes/api/auth.php'));

    // Protected Domain Routes
    Route::middleware('auth:api')->group(function () {

        // Core Domain — Organizations, Persons, Master Data, Workflows
        Route::prefix('core')
             ->name('api.v1.core.')
             ->group(base_path('routes/api/core.php'));

        // Domain routes lainnya akan ditambahkan di sini:
        Route::prefix('kepengasuhan')
             ->name('api.v1.kepengasuhan.')
             ->group(base_path('routes/api/kepengasuhan.php'));

        // Route::prefix('madrasah')->name('api.v1.madrasah.')->group(base_path('routes/api/madrasah.php'));
        // Route::prefix('keuangan')->name('api.v1.keuangan.')->group(base_path('routes/api/keuangan.php'));
        // Route::prefix('koperasi')->name('api.v1.koperasi.')->group(base_path('routes/api/koperasi.php'));
    });
});
