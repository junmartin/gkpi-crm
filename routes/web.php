<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\JemaatController;
use App\Http\Controllers\FamilyController;
use App\Http\Controllers\FamilyDetailController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// Route::get('/');

Route::resource('jemaat',JemaatController::class)->middleware(['auth','verified']);
Route::resource('family',FamilyController::class)->middleware(['auth','verified']);
Route::get('/jemaat/{jemaat_id}/assign_family', [FamilyDetailController::class,'show'])->middleware(['auth','verified'])->name('assign_family');
Route::post('/jemaat/{jemaat_id}/assign_family', [FamilyDetailController::class,'update'])->middleware(['auth','verified'])->name('assign_family.submit');


require __DIR__.'/auth.php';
