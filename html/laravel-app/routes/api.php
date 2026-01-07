<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\ParentLoginController;
use App\Http\Controllers\Auth\StudentLoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\ParentController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Parent\AbsenceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// 登録
Route::post('/register/verify-classroom', [RegisterController::class, 'verifyClassroom']);
Route::post('/register/parent', [RegisterController::class, 'registerParent']);

// 管理者認証ルート
Route::prefix('admin')->group(function () {
    // ログイン・認証
    Route::post('/login', [AdminLoginController::class, 'login']);
    Route::post('/verify-2fa', [AdminLoginController::class, 'verify2FA']);
    
    // 認証が必要なルート
    Route::middleware(['admin.auth', 'two_factor'])->group(function () {
        Route::post('/logout', [AdminLoginController::class, 'logout']);
        Route::get('/me', [AdminLoginController::class, 'me']);
        
        // クラス管理
        Route::get('/classes', [ClassController::class, 'index']);
        Route::post('/classes', [ClassController::class, 'store']);
        Route::get('/classes/{id}', [ClassController::class, 'show']);
        Route::put('/classes/{id}', [ClassController::class, 'update']);
        Route::delete('/classes/{id}', [ClassController::class, 'destroy']);
        
        // 生徒管理
        Route::get('/students', [StudentController::class, 'index']);
        Route::post('/students', [StudentController::class, 'store']);
        Route::get('/students/{id}', [StudentController::class, 'show']);
        Route::put('/students/{id}', [StudentController::class, 'update']);
        Route::delete('/students/{id}', [StudentController::class, 'destroy']);
        
        // 保護者管理
        Route::get('/parents', [ParentController::class, 'index']);
        Route::post('/parents', [ParentController::class, 'store']);
        Route::get('/parents/{id}', [ParentController::class, 'show']);
        Route::put('/parents/{id}', [ParentController::class, 'update']);
        Route::delete('/parents/{id}', [ParentController::class, 'destroy']);
        
        // CSVインポート
        Route::post('/import/students', [ImportController::class, 'importStudents']);
        Route::post('/import/classes', [ImportController::class, 'importClasses']);
        Route::post('/import/teachers', [ImportController::class, 'importTeachers']);
    });
});

// 生徒認証ルート
Route::prefix('student')->group(function () {
    // ログイン・認証
    Route::post('/login', [StudentLoginController::class, 'login']);
    Route::post('/verify-2fa', [StudentLoginController::class, 'verify2FA']);
});

// 保護者認証ルート
Route::prefix('parent')->group(function () {
    // ログイン・認証
    Route::post('/login', [ParentLoginController::class, 'login']);
    Route::post('/verify-2fa', [ParentLoginController::class, 'verify2FA']);
    
    // 認証が必要なルート
    Route::middleware(['parent.auth', 'two_factor'])->group(function () {
        Route::post('/logout', [ParentLoginController::class, 'logout']);
        Route::get('/me', [ParentLoginController::class, 'me']);
        Route::post('/change-password', [ParentLoginController::class, 'changePassword']);
        
        // 欠席連絡管理
        Route::get('/absences', [AbsenceController::class, 'index']);
        Route::post('/absences', [AbsenceController::class, 'store']);
        Route::get('/absences/{id}', [AbsenceController::class, 'show']);
        Route::put('/absences/{id}', [AbsenceController::class, 'update']);
        Route::delete('/absences/{id}', [AbsenceController::class, 'destroy']);
    });
});
