<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TrainingMaterialsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\IT\DashboardController;
use App\Http\Controllers\IT\UserController;
use App\Http\Controllers\SHE\MasterPtaController;
use App\Http\Controllers\SHE\MasterKtaController;
use App\Http\Controllers\SHE\MasterPbController;
use App\Http\Controllers\PIC\HyariController;
use App\Http\Controllers\PIC\PicDashboardController;
use App\Http\Controllers\SHE\SheDashboardController;
use App\Http\Controllers\PIC\PicHyariHattoController;
use App\Http\Controllers\PIC\KomitmenK3Controller;
use App\Http\Controllers\SHE\SheHyariHattoController;
use App\Http\Controllers\SHE\InsidenController;
use App\Http\Controllers\SHE\MasterPfController;
use App\Http\Controllers\SHE\MasterPdController;
use App\Http\Controllers\SHE\SheSafetyRidingController;
use App\Http\Controllers\PIC\PicSafetyRidingController;
use App\Http\Controllers\PIC\PicSafetyPatrolController;
use App\Http\Controllers\SHE\SheSafetyPatrolController;
use App\Http\Controllers\Manager\ProgramSafetyController;
use App\Http\Controllers\Manager\HyariHattoController;
use App\Http\Controllers\Manager\ManagerInsidenController;
use App\Http\Controllers\Manager\ManagerKomitmenK3Controller;
use App\Http\Controllers\Manager\ManagerSafetyRidingController;
use App\Http\Controllers\Manager\ManagerSafetyPatrolController;
use App\Http\Controllers\PIC\PICProgramSafetyController;
use App\Http\Controllers\IT\ManagementMenuController;
use App\Http\Controllers\IT\ManagementAksesController;
use App\Http\Controllers\Manager\ManagerDashboardController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (TANPA LOGIN)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('hhome');
});

// LOGIN – hanya untuk tamu (guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.process');
});


/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES (HARUS LOGIN)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // LOGOUT
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    /*
    |--------------------------------------------------------------------------
    | IT ROUTES (Login + Permission Required)
    |--------------------------------------------------------------------------
    */
    Route::prefix('it')->middleware('permission')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index']);
        Route::get('/management-user', [UserController::class, 'index']);


    });
    Route::prefix('it')->group(function () {

        Route::get('/management-menu', [ManagementMenuController::class, 'index'])
            ->name('it.management-menu');

        Route::post('/management-menu/store', [ManagementMenuController::class, 'store'])
            ->name('it.management-menu.store');

        Route::put('/management-menu/update/{id}', [ManagementMenuController::class, 'update'])
            ->name('it.management-menu.update');

        Route::delete('/management-menu/destroy/{id}', [ManagementMenuController::class, 'destroy'])
            ->name('it.management-menu.destroy');


        Route::get('/management-akses', [ManagementAksesController::class, 'index']);
        Route::post('/management-akses/store', [ManagementAksesController::class, 'store']);
    });


    // // Jalur akses tanpa middleware permission jika diperlukan (opsional sesuai kode awal Anda)
    // Route::prefix('it')->middleware(['auth'])->group(function () {
    //     // Baris ini tetap dipertahankan sesuai permintaan Anda
    //     Route::get('management-menu', [ManagementMenuController::class, 'index'])->name('it.management-menu');
    // });

    Route::prefix('she')->middleware('permission')->group(function () {
        Route::get('/she/master/pta', [MasterPtaController::class, 'index']);
    });

    Route::prefix('she')->middleware('permission')->group(function () {
        Route::get('/she/master/kta', [MasterPtaController::class, 'index']);
    });

    Route::prefix('she')->middleware('permission')->group(function () {
        Route::get('/she/master/pb', [MasterPbController::class, 'index']);
    });

    Route::prefix('pic')->middleware('permission')->group(function () {
        Route::get('/dashboard', [PicDashboardController::class, 'index']);
    });

    Route::prefix('manager')->middleware('permission')->group(function () {
        Route::get('/dashboard', [ManagerDashboardController::class, 'index']);
    });

    // --- USER MANAGEMENT ROUTES ---
    Route::get('/management-user', [UserController::class, 'index']);
    Route::post('it/management-user/store', [UserController::class, 'store']);
    Route::get('/it/management-user/edit/{id}', [UserController::class, 'edit']);
    Route::put('/it/management-user/update/{id}', [UserController::class, 'update']);
    Route::delete('it/management-user/destroy/{id}', [UserController::class, 'destroy']);






    // --- MASTER DATA SHE ROUTES ---
    Route::get('/she/master/pta', [MasterPtaController::class, 'index']);
    Route::post('/she/master/pta/store', [MasterPtaController::class, 'store']);
    Route::get('/she/master/pta/edit/{id}', [MasterPtaController::class, 'edit']);
    Route::put('/she/master/pta/update/{id}', [MasterPtaController::class, 'update']);
    Route::delete('/she/master/pta/destroy/{id}', [MasterPtaController::class, 'destroy']);

    Route::get('/she/master/kta', [MasterKtaController::class, 'index']);
    Route::post('/she/master/kta/store', [MasterKtaController::class, 'store']);
    Route::get('/she/master/kta/edit/{id}', [MasterKtaController::class, 'edit']);
    Route::put('/she/master/kta/update/{id}', [MasterKtaController::class, 'update']);
    Route::delete('/she/master/kta/destroy/{id}', [MasterKtaController::class, 'destroy']);

    Route::get('/she/master/pb', [MasterPbController::class, 'index']);
    Route::post('/she/master/pb/store', [MasterPbController::class, 'store']);
    Route::get('/she/master/pb/edit/{id}', [MasterPbController::class, 'edit']);
    Route::put('/she/master/pb/update/{id}', [MasterPbController::class, 'update']);
    Route::delete('/she/master/pb/destroy/{id}', [MasterPbController::class, 'destroy']);

    // --- LAPORAN HYARI HATTO ---
    Route::get('/pic/laporanhyarihatto', [PicHyariHattoController::class, 'index']);
    Route::post('/pic/laporanhyarihatto/store', [PicHyariHattoController::class, 'store']);
    Route::get('/pic/laporanhyarihatto/edit/{id}', [PicHyariHattoController::class, 'edit']);
    Route::put('/pic/laporanhyarihatto/update/{id}', [PicHyariHattoController::class, 'update']);
    Route::delete('/pic/laporanhyarihatto/destroy/{id}', [PicHyariHattoController::class, 'destroy']);
    Route::get('/pic/laporanhyarihatto/download/{id}', [PicHyariHattoController::class, 'downloadPdf']);
    Route::get('/pic/hyari-hatto/export/excel', [PicHyariHattoController::class, 'exportExcel']);

    Route::get('/she/hyari-hatto', [SheHyariHattoController::class, 'index']);
    Route::put('/she/laporanhyarihatto/update/{id}', [SheHyariHattoController::class, 'update']);
    Route::get('/she/laporanhyarihatto/download/{id}', [SheHyariHattoController::class, 'downloadPdf']);
    Route::get('/she/hyari-hatto/export/excel', [SheHyariHattoController::class, 'exportExcel']);

    // --- KOMITMEN K3 ---
    Route::get('/pic/komitmenk3', [KomitmenK3Controller::class, 'index']);
    Route::post('/pic/komitmenk3/store', [KomitmenK3Controller::class, 'store']);
    Route::get('/pic/komitmenk3/edit/{id}', [KomitmenK3Controller::class, 'edit']);
    Route::put('/pic/komitmenk3/update/{id}', [KomitmenK3Controller::class, 'update']);
    Route::delete('/pic/komitmenk3/destroy/{id}', [KomitmenK3Controller::class, 'destroy']);
    Route::post('pic/komitmenk3/sync', [KomitmenK3Controller::class, 'syncUsers']);

    Route::get('/she/komitmen-k3', [KomitmenK3Controller::class, 'getlaporank3']);
    Route::get('/she/komitmen-k3/detail/{section_id}', [KomitmenK3Controller::class, 'getSectionDetail']);

    // --- MASTER PF & PD ---
    Route::get('/she/master/pf', [MasterPfController::class, 'index']);
    Route::post('/she/master/pf/store', [MasterPfController::class, 'store']);
    Route::get('/she/master/pf/edit/{id}', [MasterPfController::class, 'edit']);
    Route::put('/she/master/pf/update/{id}', [MasterPfController::class, 'update']);
    Route::delete('/she/master/pf/destroy/{id}', [MasterPfController::class, 'destroy']);

    Route::get('/she/master/pd', [MasterPdController::class, 'index']);
    Route::post('/she/master/pd/store', [MasterPdController::class, 'store']);
    Route::get('/she/master/pd/edit/{id}', [MasterPdController::class, 'edit']);
    Route::put('/she/master/pd/update/{id}', [MasterPdController::class, 'update']);
    Route::delete('/she/master/pd/destroy/{id}', [MasterPdController::class, 'destroy']);

    // --- SAFETY RIDING ---
    Route::get('/she/safety-riding', [SheSafetyRidingController::class, 'index']);
    Route::post('/she/safety-riding/store', [SheSafetyRidingController::class, 'store']);
    Route::get('/she/safety-riding/edit/{id}', [SheSafetyRidingController::class, 'edit']);
    Route::put('/she/safety-riding/update/{id}', [SheSafetyRidingController::class, 'update']);
    Route::delete('/she/safety-riding/destroy/{id}', [SheSafetyRidingController::class, 'destroy']);
    Route::get('/she/safety-riding/download/{id}', [SheSafetyRidingController::class, 'downloadLaporan'])->name('laporan.download');
    Route::get('/she/safety-riding/get-users-by-section/{section_id}', [SheSafetyRidingController::class, 'getUsersBySection']);
    Route::delete('/she/safety-riding/{id}/delete-image/{imageIndex}', [SheSafetyRidingController::class, 'deleteImage']);
    Route::put('/she/safety-riding/tindak-lanjut/{id}', [SheSafetyRidingController::class, 'tindakLanjut']);

    Route::get('/pic/safety-riding', [PicSafetyRidingController::class, 'index']);
    Route::put('/pic/safety-riding/upload-after/{id}', [PicSafetyRidingController::class, 'uploadAfter']);
    Route::delete('/pic/safety-riding/delete-after/{id}/{index}', [PicSafetyRidingController::class, 'deleteAfterImage']);

    // --- INSIDEN ---
    Route::get('/she/insiden', [InsidenController::class, 'index']);
    Route::get('/she/insiden/form', [InsidenController::class, 'create']);
    Route::get('/she/insiden/edit/{id}', [InsidenController::class, 'edit']);
    Route::put('/she/insiden/update/{id}', [InsidenController::class, 'update']);
    Route::get('/she/insiden/detail/{id}', [InsidenController::class, 'show']);
    Route::delete('/she/insiden/delete/{id}', [InsidenController::class, 'destroy']);
    Route::post('/she/insiden/store', [InsidenController::class, 'store']);

    Route::get('/pic/insiden', [InsidenController::class, 'index']);
    Route::get('/pic/insiden/form', [InsidenController::class, 'create']);
    Route::get('/pic/insiden/edit/{id}', [InsidenController::class, 'edit']);
    Route::put('/pic/insiden/update/{id}', [InsidenController::class, 'update']);
    Route::get('/pic/insiden/detail/{id}', [InsidenController::class, 'show']);
    Route::delete('/pic/insiden/delete/{id}', [InsidenController::class, 'destroy']);
    Route::post('/pic/insiden/store', [InsidenController::class, 'store']);

    // --- SAFETY PATROL ---
    Route::get('/she/safety-patrol', [SheSafetyPatrolController::class, 'index']);
    Route::post('/she/safety-patrol/store', [SheSafetyPatrolController::class, 'store']);
    Route::put('/she/safety-patrol/update/{id}', [SheSafetyPatrolController::class, 'update']);
    Route::delete('/she/safety-patrol/destroy/{id}', [SheSafetyPatrolController::class, 'destroy']);
    Route::get('/she/safety-Patrol/download/{id}', [SheSafetyPatrolController::class, 'downloadLaporan'])->name('laporan.download');
    Route::get('/she/safety-Patrol/get-users-by-section/{section_id}', [SheSafetyPatrolController::class, 'getUsersBySection']);
    Route::delete('/she/safety-patrol/{id}/delete-image/{type}', [SheSafetyPatrolController::class, 'deleteImage']);
    Route::put('/she/safety-patrol/tindak-lanjut/{id}', [SheSafetyPatrolController::class, 'tindakLanjut']);

    Route::prefix('pic')->group(function () {
        Route::get('/safety-patrol', [PicSafetyPatrolController::class, 'index']);
        Route::put('/safety-patrol/upload-after/{id}', [PicSafetyPatrolController::class, 'uploadAfter']);
        Route::delete('/safety-patrol/delete-after/{id}', [PicSafetyPatrolController::class, 'deleteAfter']);
        Route::get('/safety-patrol/export', [PicSafetyPatrolController::class, 'export']);
        Route::get('/safety-patrol/download/{id}', [PicSafetyPatrolController::class, 'downloadLaporan']);
        Route::get('/safety-patrol/view/{id}', [PicSafetyPatrolController::class, 'viewDetail']);
    });

    Route::prefix('pic')->group(function () {
        Route::get('/programsafety', [PICProgramSafetyController::class, 'index'])->name('pic.programsafety');
    });

    Route::prefix('she')->group(function () {
        Route::get('/dashboard', [SheDashboardController::class, 'index']);
        Route::get('/dashboard/data', [SheDashboardController::class, 'getDashboardData']);
        Route::get('/dashboard/incidents/{category}', [SheDashboardController::class, 'getIncidentDetails'])->name('she.dashboard.incidents');
        Route::get('/dashboard/export', [SheDashboardController::class, 'exportDashboardData'])->name('she.dashboard.export');
        Route::get('/dashboard/debug/work-accident', [SheDashboardController::class, 'debugWorkAccidentData'])->name('she.dashboard.debug');
    });

    Route::get('/she/dashboard', [SheDashboardController::class, 'index']);
});

/*
|--------------------------------------------------------------------------
| MANAGER ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('manager')->group(function () {
    Route::get('/programsafety', [ProgramSafetyController::class, 'index']);
    Route::post('/programsafety', [ProgramSafetyController::class, 'store']);
    Route::put('/programsafety/{id}', [ProgramSafetyController::class, 'update']);
    Route::delete('/programsafety/{id}', [ProgramSafetyController::class, 'destroy']);

    Route::get('/hyari-hatto', [HyariHattoController::class, 'index'])->name('manager.hyarihatto');
    Route::get('/insiden', [ManagerInsidenController::class, 'index'])->name('manager.insiden');
    Route::get('/komitmen-k3', [ManagerKomitmenK3Controller::class, 'index'])->name('manager.komitmen-k3');
    Route::get('/safety-riding', [ManagerSafetyRidingController::class, 'index']);
    Route::get('/safety-patrol', [ManagerSafetyPatrolController::class, 'index'])->name('manager.safety-patrol');
});