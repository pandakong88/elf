<?php

use App\Modules\Kepengasuhan\Http\Controllers\ActivityController;
use App\Modules\Kepengasuhan\Http\Controllers\DormitoryController;
use App\Modules\Kepengasuhan\Http\Controllers\PerizinanController;
use App\Modules\Kepengasuhan\Http\Controllers\ViolationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ELF Kepengasuhan Domain API Routes
| Prefix: /api/v1/kepengasuhan (diset di routes/api.php)
| Middleware: api, auth:api (diset di routes/api.php)
|--------------------------------------------------------------------------
*/

// ─── Asrama & Kamar (Dormitories & Rooms) ──────────────────────────────────
Route::prefix('dormitories')->group(function () {
    Route::get('/',             [DormitoryController::class, 'index'])->name('dormitories.index');
    Route::post('/',            [DormitoryController::class, 'store'])->name('dormitories.store');
    Route::get('/{dormitory}',  [DormitoryController::class, 'show'])->name('dormitories.show');
    Route::post('/{dormitory}/rooms', [DormitoryController::class, 'storeRoom'])->name('dormitories.rooms.store');
});

Route::post('/rooms/{room}/assign',    [DormitoryController::class, 'assignRoom'])->name('rooms.assign');
Route::get('/rooms/{room}/occupants',  [DormitoryController::class, 'occupants'])->name('rooms.occupants');

// ─── Perizinan (Leave / Permission) ────────────────────────────────────────
Route::prefix('perizinan')->group(function () {
    Route::get('/',                     [PerizinanController::class, 'index'])->name('perizinan.index');
    Route::post('/',                    [PerizinanController::class, 'store'])->name('perizinan.store');
    Route::get('/{perizinan}',          [PerizinanController::class, 'show'])->name('perizinan.show');
    Route::post('/{perizinan}/checkout', [PerizinanController::class, 'checkout'])->name('perizinan.checkout');
    Route::post('/{perizinan}/checkin',  [PerizinanController::class, 'checkin'])->name('perizinan.checkin');
});

// ─── Pelanggaran (Violations) ──────────────────────────────────────────────
Route::prefix('violations')->group(function () {
    Route::get('/',                     [ViolationController::class, 'index'])->name('violations.index');
    Route::post('/',                    [ViolationController::class, 'store'])->name('violations.store');
    Route::get('/{violation}',          [ViolationController::class, 'show'])->name('violations.show');
    Route::post('/{violation}/resolve',  [ViolationController::class, 'resolve'])->name('violations.resolve');
});

// ─── Kegiatan & Absensi (Activities & Attendances) ──────────────────────────
Route::prefix('activities')->group(function () {
    Route::get('/',                     [ActivityController::class, 'index'])->name('activities.index');
    Route::post('/',                    [ActivityController::class, 'store'])->name('activities.store');
    Route::get('/{activity}',          [ActivityController::class, 'show'])->name('activities.show');
    Route::post('/{activity}/attendance', [ActivityController::class, 'recordAttendance'])->name('activities.attendance.record');
});
