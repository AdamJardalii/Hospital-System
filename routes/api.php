<?php
use App\Http\Controllers\{InsuranceController,PatientController,OCRScannerController};
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;


Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum',])->group(function () {
    Route::apiResource('patients', PatientController::class);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::delete('/insurance/{patient}', [InsuranceController::class, 'destroy']);
    Route::post('/ocr/scan', [OCRScannerController::class, 'scanInsurance']);
});