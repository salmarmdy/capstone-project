<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PemeriksaanController;
use App\Http\Controllers\ReminderSimController;
use App\Http\Controllers\ReminderStnkController;
use App\Http\Controllers\ChecklistItemController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\StnkController;
use App\Http\Controllers\AdminVehicleController;
use App\Http\Controllers\SelfCheckController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\AuthController;

// Guest routes (tidak perlu login)
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });
    
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});


Route::get('/admin-cms', function () {
    return view('dashboard');
})->name('admin.dashboard');

Route::resource('/admin-cms/pemeriksaan', PemeriksaanController::class);
Route::get('/admin-cms/pemeriksaan/{id}/detail', [PemeriksaanController::class, 'detail'])->name('pages.pemeriksaan.index');
Route::get('/admin-cms/pemeriksaan/{id}/report', [PemeriksaanController::class, 'report'])->name('pages.pemeriksaan.report');
Route::resource('/admin-cms/reminder-sim', ReminderSimController::class);
Route::resource('/admin-cms/reminder-stnk', ReminderStnkController::class);

// Employee Routes
Route::resource('/admin-cms/karyawan', EmployeeController::class);
Route::post('/admin-cms/karyawan/create', [EmployeeController::class, 'store'])->name('employee-create');
Route::get('/admin-cms/karyawan', [EmployeeController::class, 'index'])->name('employee-index');
Route::get('/admin-cms/karyawan/{id}', [EmployeeController::class, 'show'])->name('employee-show');
Route::get('/admin-cms/karyawan/{id}/delete', [EmployeeController::class, 'destroy'])->name('employee-delete');
Route::put('/admin-cms/karyawan/{id}', [EmployeeController::class, 'update'])->name('employee-update');
Route::post('/admin-cms/karyawan/{id}/create-account', [EmployeeController::class, 'createUserAccount'])->name('employee-create-account');


// Vehicle Routes
Route::prefix('admin-cms')->group(function () {
    // Vehicle routes
    Route::get('/kendaraan', [AdminVehicleController::class, 'index'])->name('pages.kendaraan.index');
    Route::get('/kendaraan/create', [AdminVehicleController::class, 'create'])->name('pages.kendaraan.create');
    Route::post('/kendaraan', [AdminVehicleController::class, 'store'])->name('pages.kendaraan.store');
    Route::get('/kendaraan/{id}', [AdminVehicleController::class, 'show'])->name('pages.kendaraan.show');
    Route::get('/kendaraan/{id}/edit', [AdminVehicleController::class, 'edit'])->name('pages.kendaraan.edit');
    Route::put('/kendaraan/{id}', [AdminVehicleController::class, 'update'])->name('pages.kendaraan.update');
    Route::delete('/kendaraan/{id}', [AdminVehicleController::class, 'destroy'])->name('pages.kendaraan.destroy');

    Route::prefix('item-checklist')->name('pages.checklist.')->group(function () {
        Route::get('/', [ChecklistController::class, 'index'])->name('index');
        Route::post('/', [ChecklistController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [ChecklistController::class, 'edit'])->name('edit');
        Route::post('/{id}', [ChecklistController::class, 'update'])->name('update');
        Route::delete('/{id}', [ChecklistController::class, 'destroy'])->name('destroy');
    });
});

Route::resource('/self-check', SelfCheckController::class);
Route::resource('/vehincles', VehinclesController::class);

Route::resource('/notification', NotificationController::class);

Route::get('/', function () {
    return redirect()->route('login');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth.session')->group(function () { 
    Route::prefix('/vehicles')->name('pages.employee.vehicles.')->group(function () {
        Route::get('/', [VehicleController::class, 'index'])->name('index');
        Route::get('/create', [VehicleController::class, 'create'])->name('create');
        Route::post('/', [VehicleController::class, 'store'])->name('store');
        Route::get('/{id}', [VehicleController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [VehicleController::class, 'edit'])->name('edit');
        Route::put('/{id}', [VehicleController::class, 'update'])->name('update');
        Route::delete('/{id}', [VehicleController::class, 'destroy'])->name('destroy');

        Route::get('/assess/{id}', [VehicleController::class, 'assess'])->name('assessment');
        Route::post('/assess/store', [VehicleController::class, 'assessStore'])->name('assessment-store');

        Route::get('/assessment/{id}/edit', [VehicleController::class, 'editAssessment'])->name('assessment-edit');
        Route::put('/assessment/{id}', [VehicleController::class, 'updateAssessment'])->name('assessment-update');
    });
});

// Alternative shorter routes (optional)
// Route::resource('vehicles', VehicleController::class);