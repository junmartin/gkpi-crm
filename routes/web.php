<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\JemaatController;
use App\Http\Controllers\FamilyController;
use App\Http\Controllers\FamilyDetailController;
use App\Http\Controllers\IbadahController;
use App\Http\Controllers\SermonController;
// use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AssetTypeController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetMaintController;
// use App\Http\Controllers\AssetPhotoController;



use App\Models\SermonAttendance;
use App\Models\Sermon;
use App\Models\Jemaat;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () { 
    $numberOfSermons = 5; // Define how many sermons back to display
    $attd = Sermon::withAttendance($numberOfSermons)->get();

    $jemaat_by_gender = Jemaat::GroupByGender()->get();
    $jemaat_by_age = Jemaat::groupByAgeCategory()->get();

    return view('dashboard',compact('attd','jemaat_by_gender','jemaat_by_age'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// Route::get('/');

Route::resource('jemaat',JemaatController::class)->middleware(['auth','verified']);
Route::resource('family',FamilyController::class)->middleware(['auth','verified']);
Route::resource('ibadah',IbadahController::class)->middleware(['auth','verified']);
Route::resource('sermon',SermonController::class)->middleware(['auth','verified']);
Route::get('/sermon_report',[SermonController::class,'report'])->middleware(['auth','verified'])->name('sermon.report');
// Route::resource('attendance',AttendanceController::class)->middleware(['auth','verified']);
Route::resource('asset_type',AssetTypeController::class)->middleware(['auth','verified']);
Route::resource('asset',AssetController::class)->middleware(['auth','verified']);
Route::resource('asset_maint',AssetMaintController::class)->middleware(['auth','verified']);
// Route::resource('asset_photo',AssetPhotoController::class)->middleware(['auth','verified']);
// Route::get('/attendance/{sermon_date}/{ibadah_id}', [AttendanceController::class,'adjustment'])->middleware(['auth','verified'])->name('attendance.adjust');
// Route::post('/attendance/{sermon_date}/{ibadah_id}', [AttendanceController::class,'adjustment_update'])->middleware(['auth','verified'])->name('attendance.adjust_update');
Route::get('/jemaat/{jemaat_id}/assign_family', [FamilyDetailController::class,'show'])->middleware(['auth','verified'])->name('assign_family');
Route::post('/jemaat/{jemaat_id}/assign_family', [FamilyDetailController::class,'update'])->middleware(['auth','verified'])->name('assign_family.submit');

Route::get('/template', [JemaatController::class,'template'])->middleware(['auth','verified']);
require __DIR__.'/auth.php';
