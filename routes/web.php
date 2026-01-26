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

    // DASHBOARD REDIRECT (Universal entry point)
    Route::get('/dashboard', function () {
        $user = auth()->user();
        $level = strtoupper($user->level);

        if ($level === 'SHE') {
            return redirect('/she/dashboard');
        } elseif ($level === 'IT' || $level === 'ADMIN') {
            return redirect('/it/dashboard');
        } elseif ($level === 'PIC') {
            return redirect('/pic/dashboard');
        } elseif ($level === 'MANAGER') {
            return redirect('/manager/dashboard');
        }
        return redirect('/');
    });

    /*
    |--------------------------------------------------------------------------
    | IT ROUTES (Login + Permission Required)
    |--------------------------------------------------------------------------
    */
    /*
    |--------------------------------------------------------------------------
    | IT ROUTES
    |--------------------------------------------------------------------------
    */
    Route::prefix('it')->middleware('permission')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index']);
        Route::get('/management-user', [UserController::class, 'index']);
        Route::post('/management-user/store', [UserController::class, 'store']);
        Route::get('/management-user/edit/{id}', [UserController::class, 'edit']);
        Route::put('/management-user/update/{id}', [UserController::class, 'update']);
        Route::delete('/management-user/destroy/{id}', [UserController::class, 'destroy']);

        Route::get('/management-menu', [ManagementMenuController::class, 'index'])->name('it.management-menu');
        Route::post('/management-menu/store', [ManagementMenuController::class, 'store'])->name('it.management-menu.store');
        Route::put('/management-menu/update/{id}', [ManagementMenuController::class, 'update'])->name('it.management-menu.update');
        Route::delete('/management-user/destroy/{id}', [ManagementMenuController::class, 'destroy'])->name('it.management-menu.destroy');

        Route::get('/management-akses', [ManagementAksesController::class, 'index']);
        Route::post('/management-akses/store', [ManagementAksesController::class, 'store']);
    });

    /*
    |--------------------------------------------------------------------------
    | SHE ROUTES
    |--------------------------------------------------------------------------
    */
    Route::prefix('she')->middleware('permission')->group(function () {
        // Dashboard
        Route::get('/dashboard', [SheDashboardController::class, 'index']);
        Route::get('/dashboard/data', [SheDashboardController::class, 'getDashboardData']);
        Route::get('/dashboard/incidents/{category}', [SheDashboardController::class, 'getIncidentDetails'])->name('she.dashboard.incidents');
        Route::get('/dashboard/export', [SheDashboardController::class, 'exportDashboardData'])->name('she.dashboard.export');
        Route::get('/dashboard/export-matrix', [SheDashboardController::class, 'exportMatrix'])->name('she.dashboard.exportMatrix');
        Route::get('/dashboard/debug/work-accident', [SheDashboardController::class, 'debugWorkAccidentData'])->name('she.dashboard.debug');

        // Master Data
        Route::get('/master/pta', [MasterPtaController::class, 'index']);
        Route::post('/master/pta/store', [MasterPtaController::class, 'store']);
        Route::get('/master/pta/edit/{id}', [MasterPtaController::class, 'edit']);
        Route::put('/master/pta/update/{id}', [MasterPtaController::class, 'update']);
        Route::delete('/master/pta/destroy/{id}', [MasterPtaController::class, 'destroy']);

        Route::get('/master/kta', [MasterKtaController::class, 'index']);
        Route::post('/master/kta/store', [MasterKtaController::class, 'store']);
        Route::get('/master/kta/edit/{id}', [MasterKtaController::class, 'edit']);
        Route::put('/master/kta/update/{id}', [MasterKtaController::class, 'update']);
        Route::delete('/master/kta/destroy/{id}', [MasterKtaController::class, 'destroy']);

        Route::get('/master/pb', [MasterPbController::class, 'index']);
        Route::post('/master/pb/store', [MasterPbController::class, 'store']);
        Route::get('/master/pb/edit/{id}', [MasterPbController::class, 'edit']);
        Route::put('/master/pb/update/{id}', [MasterPbController::class, 'update']);
        Route::delete('/master/pb/destroy/{id}', [MasterPbController::class, 'destroy']);

        Route::get('/master/pf', [MasterPfController::class, 'index']);
        Route::post('/master/pf/store', [MasterPfController::class, 'store']);
        Route::get('/master/pf/edit/{id}', [MasterPfController::class, 'edit']);
        Route::put('/master/pf/update/{id}', [MasterPfController::class, 'update']);
        Route::delete('/master/pf/destroy/{id}', [MasterPfController::class, 'destroy']);

        Route::get('/master/pd', [MasterPdController::class, 'index']);
        Route::post('/master/pd/store', [MasterPdController::class, 'store']);
        Route::get('/master/pd/edit/{id}', [MasterPdController::class, 'edit']);
        Route::put('/master/pd/update/{id}', [MasterPdController::class, 'update']);
        Route::delete('/master/pd/destroy/{id}', [MasterPdController::class, 'destroy']);

        // Transactions
        Route::get('/hyari-hatto', [SheHyariHattoController::class, 'index']);
        Route::put('/laporanhyarihatto/update/{id}', [SheHyariHattoController::class, 'update']);
        Route::get('/laporanhyarihatto/download/{id}', [SheHyariHattoController::class, 'downloadPdf']);
        Route::get('/hyari-hatto/export/excel', [SheHyariHattoController::class, 'exportExcel']);

        Route::get('/komitmen-k3', [KomitmenK3Controller::class, 'getlaporank3']);
        Route::get('/komitmen-k3/detail/{section_id}', [KomitmenK3Controller::class, 'getSectionDetail']);
        Route::get('/komitmen-k3/export', [KomitmenK3Controller::class, 'exportSHE']);

        Route::get('/safety-riding', [SheSafetyRidingController::class, 'index']);
        Route::post('/safety-riding/store', [SheSafetyRidingController::class, 'store']);
        Route::get('/safety-riding/edit/{id}', [SheSafetyRidingController::class, 'edit']);
        Route::put('/safety-riding/update/{id}', [SheSafetyRidingController::class, 'update']);
        Route::delete('/safety-riding/destroy/{id}', [SheSafetyRidingController::class, 'destroy']);
        Route::get('/safety-riding/download/{id}', [SheSafetyRidingController::class, 'downloadLaporan'])->name('she.safety-riding.download');
        Route::get('/safety-riding/get-users-by-section/{section_id}', [SheSafetyRidingController::class, 'getUsersBySection']);
        Route::delete('/safety-riding/{id}/delete-image/{imageIndex}', [SheSafetyRidingController::class, 'deleteImage']);
        Route::put('/safety-riding/tindak-lanjut/{id}', [SheSafetyRidingController::class, 'tindakLanjut']);
        Route::get('/safety-riding/export', [SheSafetyRidingController::class, 'export']);

        Route::get('/insiden', [InsidenController::class, 'index']);
        Route::get('/insiden/form', [InsidenController::class, 'create']);
        Route::get('/insiden/edit/{id}', [InsidenController::class, 'edit']);
        Route::put('/insiden/update/{id}', [InsidenController::class, 'update']);
        Route::get('/insiden/detail/{id}', [InsidenController::class, 'show']);
        Route::delete('/insiden/delete/{id}', [InsidenController::class, 'destroy']);
        Route::post('/insiden/store', [InsidenController::class, 'store']);

        Route::get('/safety-patrol', [SheSafetyPatrolController::class, 'index']);
        Route::post('/safety-patrol/store', [SheSafetyPatrolController::class, 'store']);
        Route::put('/safety-patrol/update/{id}', [SheSafetyPatrolController::class, 'update']);
        Route::delete('/safety-patrol/destroy/{id}', [SheSafetyPatrolController::class, 'destroy']);
        Route::get('/safety-patrol/download/{id}', [SheSafetyPatrolController::class, 'downloadLaporan'])->name('she.safety-patrol.download');
        Route::get('/safety-patrol/get-users-by-section/{section_id}', [SheSafetyPatrolController::class, 'getUsersBySection']);
        Route::delete('/safety-patrol/{id}/delete-image/{type}', [SheSafetyPatrolController::class, 'deleteImage']);
        Route::put('/safety-patrol/tindak-lanjut/{id}', [SheSafetyPatrolController::class, 'tindakLanjut']);
        Route::get('/safety-patrol/export', [SheSafetyPatrolController::class, 'export']);
    });

    /*
    |--------------------------------------------------------------------------
    | PIC ROUTES
    |--------------------------------------------------------------------------
    */
    Route::prefix('pic')->middleware('permission')->group(function () {
        // Dashboard
        Route::get('/dashboard', [PicDashboardController::class, 'index']);
        Route::get('/dashboard/data', [PicDashboardController::class, 'getDashboardData']);
        Route::get('/dashboard/incidents/{category}', [PicDashboardController::class, 'getIncidentDetails'])->name('pic.dashboard.incidents');
        Route::get('/dashboard/export', [PicDashboardController::class, 'exportDashboardData'])->name('pic.dashboard.export');
        Route::get('/dashboard/debug/work-accident', [PicDashboardController::class, 'debugWorkAccidentData'])->name('pic.dashboard.debug');

        // Hyari Hatto
        Route::get('/laporanhyarihatto', [PicHyariHattoController::class, 'index']);
        Route::post('/laporanhyarihatto/store', [PicHyariHattoController::class, 'store']);
        Route::get('/laporanhyarihatto/edit/{id}', [PicHyariHattoController::class, 'edit']);
        Route::put('/laporanhyarihatto/update/{id}', [PicHyariHattoController::class, 'update']);
        Route::delete('/laporanhyarihatto/destroy/{id}', [PicHyariHattoController::class, 'destroy']);
        Route::get('/laporanhyarihatto/download/{id}', [PicHyariHattoController::class, 'downloadPdf']);
        Route::get('/hyari-hatto/export/excel', [PicHyariHattoController::class, 'exportExcel']);

        // Komitmen K3
        Route::get('/komitmenk3', [KomitmenK3Controller::class, 'index']);
        Route::post('/komitmenk3/store', [KomitmenK3Controller::class, 'store']);
        Route::get('/komitmenk3/edit/{id}', [KomitmenK3Controller::class, 'edit']);
        Route::put('/komitmenk3/update/{id}', [KomitmenK3Controller::class, 'update']);
        Route::get('/komitmenk3/export', [KomitmenK3Controller::class, 'export']);
        Route::delete('/komitmenk3/destroy/{id}', [KomitmenK3Controller::class, 'destroy']);
        Route::post('/komitmenk3/sync', [KomitmenK3Controller::class, 'syncUsers']);

        // Safety Riding
        Route::get('/safety-riding', [PicSafetyRidingController::class, 'index']);
        Route::put('/safety-riding/upload-after/{id}', [PicSafetyRidingController::class, 'uploadAfter']);
        Route::delete('/safety-riding/delete-after/{id}/{index}', [PicSafetyRidingController::class, 'deleteAfterImage']);
        Route::get('/safety-riding/export', [PicSafetyRidingController::class, 'export']);

        // Insiden
        Route::get('/insiden', [InsidenController::class, 'index']);
        Route::get('/insiden/form', [InsidenController::class, 'create']);
        Route::get('/insiden/edit/{id}', [InsidenController::class, 'edit']);
        Route::put('/insiden/update/{id}', [InsidenController::class, 'update']);
        Route::get('/insiden/detail/{id}', [InsidenController::class, 'show']);
        Route::delete('/insiden/delete/{id}', [InsidenController::class, 'destroy']);
        Route::post('/insiden/store', [InsidenController::class, 'store']);

        // Safety Patrol
        Route::get('/safety-patrol', [PicSafetyPatrolController::class, 'index']);
        Route::put('/safety-patrol/upload-after/{id}', [PicSafetyPatrolController::class, 'uploadAfter']);
        Route::delete('/safety-patrol/delete-after/{id}', [PicSafetyPatrolController::class, 'deleteAfter']);
        Route::get('/safety-patrol/export', [PicSafetyPatrolController::class, 'export']);
        Route::get('/safety-patrol/download/{id}', [PicSafetyPatrolController::class, 'downloadLaporan']);
        Route::get('/safety-patrol/view/{id}', [PicSafetyPatrolController::class, 'viewDetail']);

        // Program Safety
        Route::get('/programsafety', [PICProgramSafetyController::class, 'index'])->name('pic.programsafety');
    });

    /*
    |--------------------------------------------------------------------------
    | MANAGER ROUTES
    |--------------------------------------------------------------------------
    */
    Route::prefix('manager')->middleware('permission')->group(function () {
        Route::get('/dashboard', [ManagerDashboardController::class, 'index']);
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
});
