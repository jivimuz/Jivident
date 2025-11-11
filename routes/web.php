<?php

use App\Http\Controllers\Website\AntrianController;
use App\Http\Controllers\Website\HomeController;
use App\Http\Controllers\Website\ModuleController;
use App\Http\Controllers\Website\UserListController;
use App\Http\Controllers\Website\AuthController;
use App\Http\Controllers\Website\DiperiksaController;
use App\Http\Controllers\Website\profileController;
use App\Http\Controllers\Website\SettingController;
use App\Http\Controllers\Website\EmployeesController;
use App\Http\Controllers\Website\OdontogramController;
use App\Http\Controllers\Website\PatientController;
use App\Http\Controllers\Website\PemeriksaanController;
use App\Http\Controllers\Website\PrintController;
use App\Http\Controllers\Website\RuangPraktekController;
use App\Http\Controllers\Website\RuangTungguController;
use App\Models\RuangPraktek;
use Illuminate\Support\Facades\Route;

Route::get('login', [AuthController::class, 'index'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::get('logout', [AuthController::class, 'logout'])->name('logout');
// Route::get('error', [AuthController::class, 'error'])->name('error');

Route::get('/test', function () {
    return view('3dglb');
});

Route::prefix('monitor')->group(function () {
    Route::get('antrian', function () {
        return view('public/monitor-antrian');
    });
});


//Group By Auth
Route::middleware(['auth'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::post('menu', [HomeController::class, 'menu']);

    Route::prefix('profile')->group(function () {
        Route::post('changePassword', [profileController::class, 'changePassword']);
    });


    Route::middleware(['checkAccess:MTA'])->prefix('access')->group(function () {
        Route::get('/', [ModuleController::class, 'index']);
        Route::post('getMenuList', [ModuleController::class, 'getMenuList']);
        Route::post('getUserList', [ModuleController::class, 'getUserList']);
        Route::post('saveAccess', [ModuleController::class, 'saveAccess']);
    });

    Route::middleware(['checkAccess:MTU'])->prefix('users')->group(function () {
        Route::get('/', [UserListController::class, 'index']);
        Route::post('getList', [UserListController::class, 'getList']);
        Route::post('add', [UserListController::class, 'add']);
        Route::post('edit', [UserListController::class, 'edit']);
        Route::post('cekEmail', [UserListController::class, 'cekEmail']);
        Route::post('cekUsername', [UserListController::class, 'cekUsername']);
        Route::post('saveData', [UserListController::class, 'saveData']);
        Route::post('updateData', [UserListController::class, 'updateData']);
        Route::post('activateUser', [UserListController::class, 'activateUser']);
        Route::post('deactivateUser ', [UserListController::class, 'deactivateUser']);
        Route::post('deleteUser', [UserListController::class, 'deleteUser']);
        Route::post('deleteUserWithEmployee', [UserListController::class, 'deleteUserWithEmployee']);
        Route::post('jabatan ', [UserListController::class, 'jabatan']);
        Route::post('getJabatanList ', [UserListController::class, 'getJabatanList']);
        Route::post('saveJabatan ', [UserListController::class, 'saveJabatan']);
        Route::post('deleteJabatan ', [UserListController::class, 'deleteJabatan']);
    });

    Route::middleware(['checkAccess:SET'])->prefix('setting')->group(function () {
        Route::get('/', [SettingController::class, 'index']);
        Route::post('getParameter', [SettingController::class, 'getParameter']);
        Route::post('saveSettingD', [SettingController::class, 'saveSettingD']);
        Route::post('saveSettingF', [SettingController::class, 'saveSettingF']);
    });

    Route::middleware(['checkAccess:MTR'])->prefix('ruang-praktek')->group(function () {
        Route::get('/', [RuangPraktekController::class, 'index']);
        Route::post('addRoom', [RuangPraktekController::class, 'addRoom']);
        Route::post('getRooms', [RuangPraktekController::class, 'getRooms']);
        Route::post('getDokterList', [RuangPraktekController::class, 'getDokterList']);
        Route::post('updateRoom', [RuangPraktekController::class, 'updateRoom']);
        Route::post('deleteRoom', [RuangPraktekController::class, 'deleteRoom']);
    });

    Route::middleware(['checkAccess:MTE'])->prefix('employees')->group(function () {
        Route::get('/', [EmployeesController::class, 'index']);
        Route::post('getEmployees', [EmployeesController::class, 'getEmployees']);
        Route::post('getList', [EmployeesController::class, 'getList']);

        Route::prefix('{idn}')->group(function () {
            Route::get('/', [EmployeesController::class, 'editEmployee']);

            Route::prefix('profile')->group(function () {
                Route::get('/', [EmployeesController::class, 'profile']);
            });

            Route::prefix('employment')->group(function () {
                Route::get('/', [EmployeesController::class, 'employment']);
            });

            Route::get('assignment', [EmployeesController::class, 'assignment']);

            Route::prefix('contract')->group(function () {
                Route::get('/', [EmployeesController::class, 'contract']);
                Route::post('getEmployeeContract', [EmployeesController::class, 'getEmployeeContract']);
                Route::post('addContract', [EmployeesController::class, 'addContract']);
            });

            Route::get('statutory', [EmployeesController::class, 'statutory']);
            Route::get('salary', [EmployeesController::class, 'salary']);
            Route::get('bank', [EmployeesController::class, 'bank']);
            Route::get('allowancee', [EmployeesController::class, 'allowancee']);
            Route::get('attendance', [EmployeesController::class, 'attendance']);
            Route::get('education', [EmployeesController::class, 'education']);
            Route::get('family', [EmployeesController::class, 'family']);
            Route::get('attachment', [EmployeesController::class, 'attachment']);
            Route::get('others', [EmployeesController::class, 'others']);
            Route::get('account', [EmployeesController::class, 'attachment']);
        });
    });

    Route::middleware(['checkAccess:PA'])->prefix('patient')->group(function () {
        Route::get('/', [PatientController::class, 'index']);
        Route::post('getList', [PatientController::class, 'getList']);
        Route::get('add_patient', [PatientController::class, 'addPatient']);
        Route::post('searchAddress', [PatientController::class, 'searchAddress']);
        Route::post('saveData', [PatientController::class, 'saveData']);
        Route::get('register-patient/{id}', [PatientController::class, 'registerPatient']);
        Route::post('register-patient/{id}', [PatientController::class, 'processPatient']);
    });

    Route::middleware(['checkAccess:DPP'])->prefix('ruang-tunggu')->group(function () {
        Route::get('', [RuangTungguController::class, 'index']);
        Route::post('getList', [RuangTungguController::class, 'getList']);
    });
    Route::middleware(['checkAccess:DPP'])->prefix('pemeriksaan')->group(function () {
        Route::prefix('pasien/{id}')->group(function () {
            Route::get('/', [PemeriksaanController::class, 'index']);

            Route::post('updateKeluhan/{idp}', [PemeriksaanController::class, 'updateKeluhan']);
            Route::post('updateAlergi/{idp}', [PemeriksaanController::class, 'updateAlergi']);
            Route::post('updateCatatan/{idp}', [PemeriksaanController::class, 'updateCatatan']);

            Route::post('vitalsign', [PemeriksaanController::class, 'vitalsign']);
            Route::post('vitalsign/save', [PemeriksaanController::class, 'saveVitalSign']);

            Route::post('soape', [PemeriksaanController::class, 'soape']);
            Route::post('soape/save', [PemeriksaanController::class, 'saveSoape']);

            Route::prefix('odontogram')->group(function () {
                Route::post('/', [OdontogramController::class, 'index']);
                Route::post('field', [OdontogramController::class, 'field']);
                Route::post('save', [OdontogramController::class, 'saveOdonto']);
                Route::post('loadOdonto', [OdontogramController::class, 'loadOdonto']);
            });
        });
    });

    Route::middleware(['checkAccess:DPS'])->prefix('sudah-diperiksa')->group(function () {
        Route::get('/', [DiperiksaController::class, 'index']);
        Route::post('getList', [DiperiksaController::class, 'getList']);
    });

    Route::middleware(['checkAccess:DPA'])->prefix('antrian')->group(function () {
        Route::get('/', [AntrianController::class, 'index']);
    });
});

Route::prefix('print')->group(function () {
    Route::middleware(['checkAccess:PA'])->get('print-antrian/{id}', [PrintController::class, 'antrianPasien']);
});
