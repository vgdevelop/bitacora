<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AssetWorkController;
use App\Http\Controllers\Admin\PlantManagementController;
use App\Http\Controllers\Admin\QrController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\LogbookController;
use App\Http\Controllers\WorkLogController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/bitacora');
Route::middleware('guest')->group(function(){Route::get('/login',[AuthController::class,'showLogin'])->name('login');Route::post('/login',[AuthController::class,'login'])->middleware('throttle:6,1');});
Route::post('/logout',[AuthController::class,'logout'])->middleware('auth')->name('logout');
Route::middleware('auth')->group(function(){
 Route::get('/bitacora',[LogbookController::class,'index'])->name('logbook.index');
 Route::get('/bitacora/{workLog}',[LogbookController::class,'show'])->name('logbook.show');
 Route::get('/instalaciones',[LocationController::class,'index'])->name('locations.index');
 Route::get('/instalaciones/{location}',[LocationController::class,'show'])->name('locations.show');
 Route::get('/equipos/{asset}/trabajo',[AssetWorkController::class,'show'])->name('operator.asset');
 Route::post('/equipos/{asset}/trabajo/iniciar',[AssetWorkController::class,'start'])->middleware('throttle:30,1')->name('operator.start');
 Route::put('/equipos/{asset}/trabajo/{workLog}/finalizar',[AssetWorkController::class,'finish'])->middleware('throttle:30,1')->name('operator.finish');
 Route::middleware('admin')->prefix('admin')->name('admin.')->group(function(){
  Route::get('/planta',[PlantManagementController::class,'index'])->name('plant.index');
  Route::post('/departamentos',[PlantManagementController::class,'storeDepartment'])->name('departments.store');
  Route::put('/departamentos/{department}',[PlantManagementController::class,'updateDepartment'])->name('departments.update');
  Route::delete('/departamentos/{department}',[PlantManagementController::class,'destroyDepartment'])->name('departments.destroy');
  Route::post('/equipos-trabajo',[PlantManagementController::class,'storeTeam'])->name('teams.store');
  Route::put('/equipos-trabajo/{team}',[PlantManagementController::class,'updateTeam'])->name('teams.update');
  Route::delete('/equipos-trabajo/{team}',[PlantManagementController::class,'destroyTeam'])->name('teams.destroy');
  Route::post('/ubicaciones',[PlantManagementController::class,'storeLocation'])->name('locations.store');
  Route::put('/ubicaciones/{location}',[PlantManagementController::class,'updateLocation'])->name('locations.update');
  Route::delete('/ubicaciones/{location}',[PlantManagementController::class,'destroyLocation'])->name('locations.destroy');
  Route::post('/dispositivos',[PlantManagementController::class,'storeAsset'])->name('assets.store');
  Route::put('/dispositivos/{asset}',[PlantManagementController::class,'updateAsset'])->name('assets.update');
  Route::delete('/dispositivos/{asset}',[PlantManagementController::class,'destroyAsset'])->name('assets.destroy');
  Route::post('/usuarios',[PlantManagementController::class,'storeUser'])->name('users.store');
  Route::put('/usuarios/{user}',[PlantManagementController::class,'updateUser'])->name('users.update');
  Route::delete('/usuarios/{user}',[PlantManagementController::class,'destroyUser'])->name('users.destroy');
  Route::get('/codigos-qr',[QrController::class,'index'])->name('qr.index');
  Route::get('/codigos-qr/{asset}.svg',[QrController::class,'image'])->name('qr.image');
  Route::get('/bitacora/nuevo',[WorkLogController::class,'create'])->name('logbook.create');
  Route::post('/bitacora',[WorkLogController::class,'store'])->middleware('throttle:30,1')->name('logbook.store');
  Route::get('/bitacora/{workLog}/editar',[WorkLogController::class,'edit'])->name('logbook.edit');
  Route::put('/bitacora/{workLog}',[WorkLogController::class,'update'])->middleware('throttle:30,1')->name('logbook.update');
 });
});
