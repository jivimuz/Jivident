<?php

use App\Http\Controllers\Website\ActivityLogController;
use App\Http\Controllers\Website\AntrianController;
use App\Http\Controllers\Website\HomeController;
use App\Http\Controllers\Website\ModuleController;
use App\Http\Controllers\Website\UserListController;
use App\Http\Controllers\Website\AuthController;
use App\Http\Controllers\Website\BillingController;
use App\Http\Controllers\Website\DibatalkanController;
use App\Http\Controllers\Website\DiperiksaController;
use App\Http\Controllers\Website\profileController;
use App\Http\Controllers\Website\SettingController;
use App\Http\Controllers\Website\EmployeesController;
use App\Http\Controllers\Website\CutiController;
use App\Http\Controllers\Website\ICDController;
use App\Http\Controllers\Website\JadwalKerjaController;
use App\Http\Controllers\Website\MonitorController;
use App\Http\Controllers\Website\OdontogramController;
use App\Http\Controllers\Website\PatientController;
use App\Http\Controllers\Website\PayrollController;
use App\Http\Controllers\Website\PaymentsController;
use App\Http\Controllers\Website\PemeriksaanController;
use App\Http\Controllers\Website\PosController;
use App\Http\Controllers\Website\PrintController;
use App\Http\Controllers\Website\ProductCategoriesController;
use App\Http\Controllers\Website\ProductController;
use App\Http\Controllers\Website\ReceiveOrderController;
use App\Http\Controllers\Website\ReportController;
use App\Http\Controllers\Website\RoleController;
use App\Http\Controllers\Website\RuangPraktekController;
use App\Http\Controllers\Website\RuangTungguController;
use App\Http\Controllers\Website\SupplierController;
use App\Http\Controllers\Website\TindakanController;
use App\Http\Controllers\Website\TransactionController;
use App\Models\RuangPraktek;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('login', [AuthController::class, 'index'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::get('logout', [AuthController::class, 'logout'])->name('logout');
Route::get('auth-license', [AuthController::class, 'authLicense']);
Route::get('license', [AuthController::class, 'license'])->name('license');
Route::post('license/validate', [AuthController::class, 'validateLicense'])->name('license.validate');
Route::get('error', [AuthController::class, 'error'])->name('error');

Route::get('/test', function () {
    return view('3dglb');
});

Route::prefix('monitor')->group(function () {
    Route::get('antrian', [MonitorController::class, 'antrian']);
    Route::get('currentAntrian', [MonitorController::class, 'currentAntrian']);
});


Route::get('_ops/config-cache', function () {

    // ✅ cuma boleh kalau APP_DEBUG=true
    abort_unless(config('app.debug') === true, 404);

    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('config:cache');

    return response()->json([
        'ok' => true,
        'message' => 'config:cache done',
        'output' => Artisan::output(),
    ]);
});

Route::get('_ops/storage-link', function () {
    abort_unless(request('key') === env('TOOLS_KEY'), 403);

    $target = storage_path('app/public');
    $link   = public_path('storage');

    if (file_exists($link)) {
        return response()->json(['ok' => true, 'message' => 'public/storage already exists']);
    }

    // beberapa hosting disable symlink()
    $ok = symlink($target, $link);

    if (!$ok) {
        return response()->json([
            'ok' => false,
            'message' => 'symlink() gagal (mungkin diblok hosting). Buat manual lewat file manager/ssh.',
            'target' => $target,
            'link' => $link,
        ], 500);
    }

    return response()->json(['ok' => true, 'message' => 'symlink created', 'link' => $link, 'target' => $target]);
})->middleware('throttle:5,10');

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

    Route::middleware(['checkAccess:RPA'])->prefix('report')->group(function () {
        Route::get('/', [ReportController::class, 'index']);
        Route::post('getOmsetBulanan', [ReportController::class, 'getOmsetBulanan']);
        Route::post('getTindakanTerlaris', [ReportController::class, 'getTindakanTerlaris']);
        Route::post('getPerformaDokter', [ReportController::class, 'getPerformaDokter']);
        Route::post('getDemografiPasien', [ReportController::class, 'getDemografiPasien']);
    });

    Route::middleware(['checkAccess:JDW'])->prefix('jadwal-kerja')->group(function () {
        Route::get('/', [JadwalKerjaController::class, 'index']);
        Route::post('getList', [JadwalKerjaController::class, 'getList']);
        Route::post('store', [JadwalKerjaController::class, 'store']);
        Route::post('update/{id}', [JadwalKerjaController::class, 'update']);
        Route::post('delete/{id}', [JadwalKerjaController::class, 'destroy']);
        Route::post('getTemplates', [JadwalKerjaController::class, 'getTemplates']);
        Route::post('storeTemplate', [JadwalKerjaController::class, 'storeTemplate']);
        Route::post('deleteTemplate/{id}', [JadwalKerjaController::class, 'destroyTemplate']);
        Route::post('getEmployeeList', [JadwalKerjaController::class, 'getEmployeeList']);
    });

    Route::middleware(['checkAccess:CUT'])->prefix('cuti')->group(function () {
        Route::get('/', [CutiController::class, 'index']);
        Route::post('getList', [CutiController::class, 'getList']);
        Route::post('store', [CutiController::class, 'store']);
        Route::post('approve/{id}', [CutiController::class, 'approve']);
        Route::post('reject/{id}', [CutiController::class, 'reject']);
        Route::post('delete/{id}', [CutiController::class, 'destroy']);
        Route::post('getEmployeeList', [CutiController::class, 'getEmployeeList']);
        Route::post('getJenisCutiList', [CutiController::class, 'getJenisCutiList']);
    });

    Route::middleware(['checkAccess:ROL'])->prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index']);
        Route::post('getList', [RoleController::class, 'getList']);
        Route::post('add', [RoleController::class, 'add']);
        Route::post('edit/{id}', [RoleController::class, 'edit']);
        Route::post('store', [RoleController::class, 'store']);
        Route::post('update/{id}', [RoleController::class, 'update']);
        Route::post('delete/{id}', [RoleController::class, 'destroy']);
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

    Route::middleware(['checkAccess:LOG'])->prefix('activity-log')->group(function () {
        Route::get('/', [ActivityLogController::class, 'index']);
        Route::post('getList', [ActivityLogController::class, 'getList']);
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
            Route::post('statutory/save', [EmployeesController::class, 'saveStatutory']);
            Route::get('salary', [EmployeesController::class, 'salary']);
            Route::post('salary/save', [EmployeesController::class, 'saveSalary']);
            Route::post('salary/calculate', [PayrollController::class, 'calculate']);
            Route::post('salary/generateSlip', [PayrollController::class, 'generateSlip']);
            Route::post('salary/getSlips', [PayrollController::class, 'getSlips']);
            Route::get('bank', [EmployeesController::class, 'bank']);
        });
    });
    Route::middleware(['checkAccess:MTT'])->prefix('tindakan')->group(function () {
        Route::get('/', [TindakanController::class, 'index']);
        Route::post('getList', [TindakanController::class, 'getList']);
        Route::post('saveTindakan', [TindakanController::class, 'saveTindakan']);
        Route::post('deleteTindakan', [TindakanController::class, 'deleteTindakan']);
        Route::post('showTindakan', [TindakanController::class, 'showTindakan']);
        Route::post('updateTindakan', [TindakanController::class, 'updateTindakan']);
        Route::post('getTindakanList', [TindakanController::class, 'getTindakanList']);
        Route::post('getDokterList', [TindakanController::class, 'getDokterList']);
    });

    Route::middleware(['checkAccess:MTP'])->prefix('payments')->group(function () {
        Route::get('/', [PaymentsController::class, 'index']);
        Route::post('getList', [PaymentsController::class, 'getList']);
        Route::post('getPaymentsById', [PaymentsController::class, 'getPaymentsById']);
        Route::post('insertOrUpdatePayments', [PaymentsController::class, 'insertOrUpdatePayments']);
        Route::post('deletePayment', [PaymentsController::class, 'deletePayment']);
    });



    Route::middleware(['checkAccess:PA'])->prefix('patient')->group(function () {
        Route::get('/', [PatientController::class, 'index']);
        Route::post('getList', [PatientController::class, 'getList']);
        Route::get('add_patient', [PatientController::class, 'addPatient']);
        Route::get('edit_patient/{id}', [PatientController::class, 'editPatient']);
        Route::post('searchAddress', [PatientController::class, 'searchAddress']);
        Route::post('saveData', [PatientController::class, 'saveData']);
        Route::post('updateData', [PatientController::class, 'updateData']);
        Route::get('register-patient/{id}', [PatientController::class, 'registerPatient']);
        Route::post('register-patient/{id}', [PatientController::class, 'processPatient']);
    });

    Route::middleware(['checkAccess:DPP'])->prefix('ruang-tunggu')->group(function () {
        Route::get('/', [RuangTungguController::class, 'index']);
        Route::post('getList', [RuangTungguController::class, 'getList']);
        Route::post('batalkan-pasien', [RuangTungguController::class, 'batalkanPasien']);
    });
    Route::middleware(['checkAccess:DPP'])->prefix('pemeriksaan')->group(function () {

        Route::post('getProductList', [PemeriksaanController::class, 'getProductList']);
        Route::post('getPenggunaanList', [PemeriksaanController::class, 'getPenggunaanList']);

        Route::prefix('pasien/{id}')->group(function () {
            Route::get('/', [PemeriksaanController::class, 'index']);

            Route::post('updateKeluhan/{idp}', [PemeriksaanController::class, 'updateKeluhan']);
            Route::post('updateAlergi/{idp}', [PemeriksaanController::class, 'updateAlergi']);
            Route::post('updateCatatan/{idp}', [PemeriksaanController::class, 'updateCatatan']);
            Route::post('updateStatus', [PemeriksaanController::class, 'updateStatus']);

            Route::post('vitalsign', [PemeriksaanController::class, 'vitalsign']);
            Route::post('vitalsign/save', [PemeriksaanController::class, 'saveVitalSign']);

            Route::post('soape', [PemeriksaanController::class, 'soape']);
            Route::post('soape/save', [PemeriksaanController::class, 'saveSoape']);

            Route::post('tindakan', [PemeriksaanController::class, 'tindakan']);
            Route::post('tindakan/save', [PemeriksaanController::class, 'saveTindakan']);

            Route::post('diagnosa', [PemeriksaanController::class, 'diagnosa']);
            Route::post('diagnosa/save', [PemeriksaanController::class, 'saveDiagnosa']);

            Route::post('resep', [PemeriksaanController::class, 'resep']);
            Route::post('resep/save', [PemeriksaanController::class, 'saveOrUpdateResep']);
            Route::post('resep/load', [PemeriksaanController::class, 'getCurrentResep']);

            Route::post('cetaksurat', [PemeriksaanController::class, 'cetaksurat']);
            Route::post('cetaksurat/save', [PemeriksaanController::class, 'saveCetaksurat']);

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

    Route::middleware(['checkAccess:DBT'])->prefix('dibatalkan')->group(function () {
        Route::get('/', [DibatalkanController::class, 'index']);
        Route::post('getList', [DibatalkanController::class, 'getList']);
    });

    Route::middleware(['checkAccess:DPA'])->prefix('antrian')->group(function () {
        Route::get('/', [AntrianController::class, 'index']);
    });

    Route::middleware(['checkAccess:TR'])->prefix('transaction')->group(function () {
        Route::get('/', [TransactionController::class, 'index']);
        Route::post('getList', [TransactionController::class, 'getList']);
        Route::post('getOmset', [TransactionController::class, 'getOmset']);
        Route::post('addTransaction', [TransactionController::class, 'addTransaction']);
        Route::post('submitTransaction', [TransactionController::class, 'submitTransaction']);
        Route::get('print/{no_transaction}/{mode}', [TransactionController::class, 'printInvoice']);
    });


    Route::middleware(['checkAccess:BL'])->prefix('billing')->group(function () {
        Route::get('/', [BillingController::class, 'index']);
        Route::post('getList', [BillingController::class, 'getList']);
        Route::get('detail/{id}', [BillingController::class, 'billingDetail']);
        Route::post('getTagihanList', [BillingController::class, 'getTagihanList']);
        Route::post('process-paid', [BillingController::class, 'processPaid']);
    });

    Route::middleware(['checkAccess:ICD'])->prefix('icd')->group(function () {
        Route::get('/', [ICDController::class, 'index']);

        // Data tables
        Route::get('dataICDIX', [ICDController::class, 'dataICDIX'])->name('icd.data.ix');
        Route::get('dataICDX', [ICDController::class, 'dataICDX'])->name('icd.data.x');

        // Modal GET
        Route::get('addICDIX', [ICDController::class, 'addICDIX'])->name('icd.add.ix');
        Route::get('addICDX', [ICDController::class, 'addICDX'])->name('icd.add.x');
        Route::get('showICD/{id}/{tipe}', [ICDController::class, 'showICD'])->name('icd.show');
        Route::get('editICDIX/{id}', [ICDController::class, 'editICDIX'])->name('icd.edit.ix');
        Route::get('editICDX/{id}', [ICDController::class, 'editICDX'])->name('icd.edit.x');

        // Store / Update
        Route::post('storeICDIX', [ICDController::class, 'storeICDIX'])->name('icd.store.ix');
        Route::post('storeICDX', [ICDController::class, 'storeICDX'])->name('icd.store.x');
        Route::post('updateICDIX/{id}', [ICDController::class, 'updateICDIX'])->name('icd.update.ix');
        Route::post('updateICDX/{id}', [ICDController::class, 'updateICDX'])->name('icd.update.x');

        // Delete
        Route::post('deleteICD/{id}/{tipe}', [ICDController::class, 'deleteICD'])->name('icd.delete');

        Route::post('getDokterList', [ICDController::class, 'getDokterList']);
        Route::post('getDiagnosaList', [ICDController::class, 'getDiagnosaList']);
    });

    Route::middleware(['checkAccess:PR'])->group(function () {
        Route::prefix('product')->group(function () {
            Route::get('/', [ProductController::class, 'index']);
            Route::get('add', [ProductController::class, 'add']);
            Route::get('getList', [ProductController::class, 'getList']);
            Route::post('store', [ProductController::class, 'store']);
            Route::get('edit/{id}', [ProductController::class, 'edit']);
            Route::post('update/{id}', [ProductController::class, 'update']);
            Route::post('delete/{id}', [ProductController::class, 'destroy']);
            Route::get('import/template', [ProductController::class, 'downloadImportTemplate']);
            Route::post('import', [ProductController::class, 'import']);
        });
        Route::prefix('categories')->group(function () {
            Route::get('getList', [ProductCategoriesController::class, 'getList']);
            Route::post('store', [ProductCategoriesController::class, 'store']);
            Route::get('edit/{id}', [ProductCategoriesController::class, 'edit']);
            Route::post('update/{id}', [ProductCategoriesController::class, 'update']);
            Route::post('delete/{id}', [ProductCategoriesController::class, 'destroy']);
        });
    });

    Route::middleware(['checkAccess:POS'])->prefix('pos')->group(function () {
        Route::get('/', [PosController::class, 'index']);
        Route::post('getProductList', [PosController::class, 'getProductList']);
        Route::post('checkout', [PosController::class, 'checkout']);
        Route::post('void/{id}', [PosController::class, 'void']);
        Route::post('getList', [PosController::class, 'getList']);
        Route::get('receipt/{id}', [PosController::class, 'receipt']);
    });

    Route::middleware(['checkAccess:SUP'])->prefix('supplier')->group(function () {
        Route::get('/', [SupplierController::class, 'index']);
        Route::get('get-data', [SupplierController::class, 'getData']);
        Route::post('store', [SupplierController::class, 'store']);
        Route::get('show/{id}', [SupplierController::class, 'show']);
        Route::post('update/{id}', [SupplierController::class, 'update']);
        Route::post('delete/{id}', [SupplierController::class, 'destroy']);
    });

    Route::middleware(['checkAccess:RO'])->prefix('receive_order')->group(function () {
        Route::get('/', [ReceiveOrderController::class, 'index']);
        Route::get('get-data', [ReceiveOrderController::class, 'getData']);
        Route::get('create', [ReceiveOrderController::class, 'create']);
        Route::post('store', [ReceiveOrderController::class, 'store']);
        Route::get('edit/{id}', [ReceiveOrderController::class, 'edit']);
        Route::get('show/{id}', [ReceiveOrderController::class, 'show']);
        Route::post('update/{id}', [ReceiveOrderController::class, 'update']);
        Route::post('delete/{id}', [ReceiveOrderController::class, 'destroy']);
        Route::post('approve/{id}', [ReceiveOrderController::class, 'approve']);
        Route::post('getSupplierList', [ReceiveOrderController::class, 'getSupplierList']);
        Route::post('getProductList', [ReceiveOrderController::class, 'getProductList']);
        Route::post('getUnitList', [ReceiveOrderController::class, 'getUnitList']);
    });
})->middleware('throttle:300,1');

Route::prefix('print')->group(function () {
    Route::middleware(['checkAccess:PA'])->get('print-antrian/{id}', [PrintController::class, 'antrianPasien']);
    Route::middleware(['checkAccess:DPP'])->get('print-resep-pasien/{id}', [PrintController::class, 'printResepPasien']);
    Route::middleware(['checkAccess:DPP'])->get('print-surat-sehat-pasien/{id}', [PrintController::class, 'printSuratSehatPasien']);
    Route::middleware(['checkAccess:DPP'])->get('print-surat-sakit-pasien/{id}', [PrintController::class, 'printSuratSakitPasien']);
    Route::middleware(['checkAccess:DPP'])->get('print-surat-rujukan-pasien/{id}', [PrintController::class, 'printSuratRujukanPasien']);
});
