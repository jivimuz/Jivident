<?php

use App\Http\Controllers\Website\HomeController;
use App\Http\Controllers\Website\ModuleController;
use App\Http\Controllers\Website\UserListController;
use App\Http\Controllers\Website\AuthController;
use App\Http\Controllers\Website\profileController;
use App\Http\Controllers\Website\SettingController;
use App\Http\Controllers\Website\EmployeesController;
use App\Http\Controllers\Website\PatientController;
use Illuminate\Support\Facades\Route;

Route::get('login', [AuthController::class, 'index'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::get('logout', [AuthController::class, 'logout'])->name('logout');
// Route::get('error', [AuthController::class, 'error'])->name('error');

Route::get('/test', function () {
    return view('3dglb');
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

    Route::middleware(['checkAccess:PAT'])->prefix('patient')->group(function () {
        Route::get('/', [PatientController::class, 'index']);
    });
});
