<?php

use App\Http\Controllers\Admin\FailedJobsController;
use App\Http\Controllers\Admisi\AdmisiController;
use App\Http\Controllers\Billing\BillingController;
use App\Http\Controllers\Billing\ClaimsController;
use App\Http\Controllers\Inpatient\InpatientController;
use App\Http\Controllers\Lab\LabController;
use App\Http\Controllers\Online\OnlineRegistrationController;
use App\Http\Controllers\Pharmacy\PharmacyController;
use App\Http\Controllers\Queue\QueueController;
use App\Http\Controllers\Radiology\RadiologyController;
use App\Http\Controllers\Registration\RegistrationController;
use App\Http\Controllers\Report\ReportController;
use App\Http\Controllers\RME\RMEController;
use App\Http\Controllers\Settings\ApiSettingsController;
use App\Http\Controllers\Settings\DoctorScheduleController;
use App\Http\Controllers\Settings\MasterDataController;
use App\Http\Controllers\Settings\PermissionController;
use App\Http\Controllers\Settings\StaffController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login'));

// Authentication routes
Route::get('/login', [\App\Http\Controllers\Auth\AuthController::class, 'showLogin'])->name('login');
// Rate limited to 5 attempts per minute per IP (Req 20.5)
Route::post('/login', [\App\Http\Controllers\Auth\AuthController::class, 'login'])
    ->name('login.post')
    ->middleware('throttle:5,1');
Route::post('/logout', [\App\Http\Controllers\Auth\AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Public queue display — no auth required
Route::get('/queue/display/{poliId}', [QueueController::class, 'display'])->name('queue.display');

// Online registration — no auth required (Req 18.1)
Route::prefix('online')->name('online.')->group(function () {
    Route::get('/', [OnlineRegistrationController::class, 'index'])->name('index');
    Route::get('/schedules', [OnlineRegistrationController::class, 'getSchedules'])->name('schedules');
    Route::post('/', [OnlineRegistrationController::class, 'store'])->name('store');
    Route::get('/success/{noRawat}', [OnlineRegistrationController::class, 'success'])->name('success');
});

// Online registration — no auth required (Req 18.1)
Route::prefix('online')->name('online.')->group(function () {
    Route::get('/', [OnlineRegistrationController::class, 'index'])->name('index');
    Route::get('/schedules', [OnlineRegistrationController::class, 'getSchedules'])->name('schedules');
    Route::post('/', [OnlineRegistrationController::class, 'store'])->name('store');
    Route::get('/success/{noRawat}', [OnlineRegistrationController::class, 'success'])->name('success');
});

// Authenticated routes
Route::middleware(['auth', 'session.timeout'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // Master Data routes
    Route::middleware(['permission:master.data'])->group(function () {
        // Master Data dashboard
        Route::get('/master', fn() => view('master.dashboard'))->name('master.dashboard');
        $masterEntities = [
            'polis'        => 'polis',
            'drugs'        => 'drugs',
            'doctors'      => 'doctors',
            'icd10-codes'  => 'icd10-codes',
            'icd9cm-codes' => 'icd9cm-codes',
            'rooms'        => 'rooms',
        ];

        foreach ($masterEntities as $slug => $entity) {
            $routeName = "master.{$slug}";
            Route::get("/master/{$slug}", fn() => app(MasterDataController::class, ['entity' => $entity])->index(request()))->name("{$routeName}.index");
            Route::get("/master/{$slug}/create", fn() => app(MasterDataController::class, ['entity' => $entity])->create())->name("{$routeName}.create");
            Route::post("/master/{$slug}", fn() => app(MasterDataController::class, ['entity' => $entity])->store(request()))->name("{$routeName}.store");
            Route::get("/master/{$slug}/{id}/edit", fn(int $id) => app(MasterDataController::class, ['entity' => $entity])->edit($id))->name("{$routeName}.edit");
            Route::put("/master/{$slug}/{id}", fn(int $id) => app(MasterDataController::class, ['entity' => $entity])->update(request(), $id))->name("{$routeName}.update");
            Route::delete("/master/{$slug}/{id}", fn(int $id) => app(MasterDataController::class, ['entity' => $entity])->destroy($id))->name("{$routeName}.destroy");
        }

        // Doctor Schedule routes (Req 19.3–19.5)
        Route::get('/master/schedules', [DoctorScheduleController::class, 'index'])->name('master.schedules.index');
        Route::get('/master/schedules/create', [DoctorScheduleController::class, 'create'])->name('master.schedules.create');
        Route::post('/master/schedules', [DoctorScheduleController::class, 'store'])->name('master.schedules.store');
        Route::get('/master/schedules/{id}/edit', [DoctorScheduleController::class, 'edit'])->name('master.schedules.edit');
        Route::put('/master/schedules/{id}', [DoctorScheduleController::class, 'update'])->name('master.schedules.update');
        Route::delete('/master/schedules/{id}', [DoctorScheduleController::class, 'destroy'])->name('master.schedules.destroy');

        // Non-doctor staff management routes (Req 19.2)
        Route::get('/master/staff', [StaffController::class, 'index'])->name('master.staff.index');
        Route::get('/master/staff/create', [StaffController::class, 'create'])->name('master.staff.create');
        Route::post('/master/staff', [StaffController::class, 'store'])->name('master.staff.store');
        Route::get('/master/staff/{id}/edit', [StaffController::class, 'edit'])->name('master.staff.edit');
        Route::put('/master/staff/{id}', [StaffController::class, 'update'])->name('master.staff.update');
        Route::delete('/master/staff/{id}', [StaffController::class, 'destroy'])->name('master.staff.destroy');

        // API Settings routes
        Route::get('/master/api-settings', [ApiSettingsController::class, 'index'])->name('master.api-settings.index');
        Route::get('/master/api-settings/{integrationName}/edit', [ApiSettingsController::class, 'edit'])->name('master.api-settings.edit');
        Route::put('/master/api-settings/{integrationName}', [ApiSettingsController::class, 'update'])->name('master.api-settings.update');
        Route::post('/master/api-settings/{integrationName}/toggle', [ApiSettingsController::class, 'toggle'])->name('master.api-settings.toggle');
        Route::post('/master/api-settings/{integrationName}/test-connection', [ApiSettingsController::class, 'testConnection'])->name('master.api-settings.test-connection');

        // Permission management routes
        Route::get('/master/permissions', [PermissionController::class, 'index'])->name('master.permissions.index');
        Route::get('/master/permissions/{role}', [PermissionController::class, 'show'])->name('master.permissions.show');
        Route::post('/master/permissions/{role}', [PermissionController::class, 'update'])->name('master.permissions.update');

        // Action Masters (Tarif & Jenis Tindakan)
        Route::get('/master/action-masters', fn() => app(MasterDataController::class, ['entity' => 'action-masters'])->index(request()))->name('master.action-masters.index');
        Route::get('/master/action-masters/create', fn() => app(MasterDataController::class, ['entity' => 'action-masters'])->create())->name('master.action-masters.create');
        Route::post('/master/action-masters', fn() => app(MasterDataController::class, ['entity' => 'action-masters'])->store(request()))->name('master.action-masters.store');
        Route::get('/master/action-masters/{id}/edit', fn(int $id) => app(MasterDataController::class, ['entity' => 'action-masters'])->edit($id))->name('master.action-masters.edit');
        Route::put('/master/action-masters/{id}', fn(int $id) => app(MasterDataController::class, ['entity' => 'action-masters'])->update(request(), $id))->name('master.action-masters.update');
        Route::delete('/master/action-masters/{id}', fn(int $id) => app(MasterDataController::class, ['entity' => 'action-masters'])->destroy($id))->name('master.action-masters.destroy');
    });

    // Queue management routes
    Route::middleware(['permission:queue'])->prefix('queue')->name('queue.')->group(function () {
        Route::get('/', [QueueController::class, 'index'])->name('index');
        Route::patch('/{queueId}/status', [QueueController::class, 'updateStatus'])->name('update-status');
    });

    // Registration routes
    Route::middleware(['permission:registration'])->prefix('registration')->name('registration.')->group(function () {
        Route::get('/', [RegistrationController::class, 'index'])->name('index');
        Route::get('/create', [RegistrationController::class, 'create'])->name('create');
        Route::post('/', [RegistrationController::class, 'store'])->name('store');
        Route::get('/{patientId}/visit/create', [RegistrationController::class, 'createVisit'])->name('create-visit');
        Route::post('/{patientId}/visit', [RegistrationController::class, 'storeVisit'])->name('store-visit');
        Route::get('/{patientId}', [RegistrationController::class, 'show'])->name('show');
    });

    // Admisi routes
    Route::middleware(['permission:admisi'])->prefix('admisi')->name('admisi.')->group(function () {
        Route::get('/', [AdmisiController::class, 'index'])->name('index');
        Route::get('/{visitId}/confirm', [AdmisiController::class, 'confirm'])->name('confirm');
        Route::post('/{visitId}', [AdmisiController::class, 'store'])->name('store');
    });

    // RME routes
    Route::middleware(['permission:rme'])->prefix('rme')->name('rme.')->group(function () {
        Route::get('/', [RMEController::class, 'index'])->name('index');
        Route::get('/search/icd10', [RMEController::class, 'searchIcd10'])->name('search.icd10');
        Route::get('/search/icd9cm', [RMEController::class, 'searchIcd9cm'])->name('search.icd9cm');
        Route::get('/{visitId}', [RMEController::class, 'show'])->name('show');
        Route::post('/{visitId}', [RMEController::class, 'store'])->name('store');
        Route::post('/{visitId}/skdp', [RMEController::class, 'skdp'])->name('skdp');
    });

    // Billing routes
    Route::middleware(['permission:billing'])->prefix('billing')->name('billing.')->group(function () {
        Route::get('/', [BillingController::class, 'index'])->name('index');
        Route::get('/claims', [BillingController::class, 'claims'])->name('claims');
        Route::get('/{visitId}', [BillingController::class, 'show'])->name('show');
        Route::post('/{billId}/payment', [BillingController::class, 'processPayment'])->name('payment');
    });

    // Pharmacy routes
    Route::middleware(['permission:pharmacy'])->prefix('pharmacy')->name('pharmacy.')->group(function () {
        Route::get('/', [PharmacyController::class, 'index'])->name('index');
        Route::get('/stock', [PharmacyController::class, 'stock'])->name('stock');
        Route::get('/{prescriptionId}', [PharmacyController::class, 'show'])->name('show');
        Route::post('/{prescriptionId}/validate', [PharmacyController::class, 'validate'])->name('validate');
        Route::post('/{prescriptionId}/dispense', [PharmacyController::class, 'dispense'])->name('dispense');
        Route::post('/{prescriptionId}/soap', [PharmacyController::class, 'soapFarmasi'])->name('soap');
    });

    // Berkas Digital — halaman pencarian pasien
    Route::middleware(['permission:claims'])->prefix('berkas-digital')->name('berkas-digital.')->group(function () {
        Route::get('/', [ClaimsController::class, 'search'])->name('index');
    });

    // Claims routes
    Route::middleware(['permission:claims'])->prefix('claims')->name('claims.')->group(function () {
        Route::get('/{patientId}', [ClaimsController::class, 'index'])->name('index');
        Route::get('/{visitId}/detail', [ClaimsController::class, 'show'])->name('show');
        Route::get('/{visitId}/draft', [ClaimsController::class, 'createDraft'])->name('draft');
        Route::get('/{visitId}/export-pdf', [ClaimsController::class, 'exportPdf'])->name('export-pdf');
    });

    // Report routes (Req 17.1, 17.2, 17.3, 17.4)
    Route::middleware(['permission:report'])->prefix('report')->name('report.')->group(function () {
        Route::get('/visits',   [ReportController::class, 'visits'])->name('visits');
        Route::get('/diseases', [ReportController::class, 'diseases'])->name('diseases');
        Route::get('/financial',[ReportController::class, 'financial'])->name('financial');

        // Export PDF (Req 17.4)
        Route::get('/visits/export/pdf',    [ReportController::class, 'exportVisitsPdf'])->name('visits.export.pdf');
        Route::get('/diseases/export/pdf',  [ReportController::class, 'exportDiseasesPdf'])->name('diseases.export.pdf');
        Route::get('/financial/export/pdf', [ReportController::class, 'exportFinancialPdf'])->name('financial.export.pdf');

        // Export Excel (Req 17.4)
        Route::get('/visits/export/excel',    [ReportController::class, 'exportVisitsExcel'])->name('visits.export.excel');
        Route::get('/diseases/export/excel',  [ReportController::class, 'exportDiseasesExcel'])->name('diseases.export.excel');
        Route::get('/financial/export/excel', [ReportController::class, 'exportFinancialExcel'])->name('financial.export.excel');

        // Download async export (Req 17.5)
        Route::get('/export/download/{filename}', [ReportController::class, 'downloadExport'])->name('export.download');
    });

    // Admin: Failed Jobs monitoring (Req 11.6)
    Route::middleware(['permission:admin'])->prefix('admin/failed-jobs')->name('admin.failed-jobs.')->group(function () {
        Route::get('/', [FailedJobsController::class, 'index'])->name('index');
        Route::post('/{uuid}/retry', [FailedJobsController::class, 'retry'])->name('retry');
        Route::post('/retry-all', [FailedJobsController::class, 'retryAll'])->name('retry-all');
        Route::delete('/{uuid}', [FailedJobsController::class, 'destroy'])->name('destroy');
        Route::delete('/', [FailedJobsController::class, 'clearAll'])->name('clear');
    });

    // Inpatient routes
    Route::middleware(['permission:inpatient'])->prefix('inpatient')->name('inpatient.')->group(function () {
        Route::get('/', [InpatientController::class, 'index'])->name('index');
        Route::get('/beds', [InpatientController::class, 'beds'])->name('beds');
        Route::get('/{visitId}', [InpatientController::class, 'show'])->name('show');
        Route::post('/{visitId}', [InpatientController::class, 'store'])->name('store');
        Route::patch('/{visitId}/notes', [InpatientController::class, 'updateNotes'])->name('update-notes');
        Route::patch('/{visitId}/discharge', [InpatientController::class, 'discharge'])->name('discharge');
    });

    // Lab routes
    Route::middleware(['permission:lab'])->prefix('lab')->name('lab.')->group(function () {
        Route::get('/', [LabController::class, 'index'])->name('index');
        Route::get('/{labRequestId}', [LabController::class, 'show'])->name('show');
        Route::post('/{labRequestId}/result', [LabController::class, 'storeResult'])->name('store-result');
    });

    // Radiology routes
    Route::middleware(['permission:radiology'])->prefix('radiology')->name('radiology.')->group(function () {
        Route::get('/', [RadiologyController::class, 'index'])->name('index');
        Route::get('/{radiologyRequestId}', [RadiologyController::class, 'show'])->name('show');
        Route::post('/{radiologyRequestId}/result', [RadiologyController::class, 'storeResult'])->name('store-result');
    });
});
