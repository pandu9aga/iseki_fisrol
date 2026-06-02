<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admins\AdminDashboardController;
use App\Http\Controllers\Admins\DataUserController;
use App\Http\Controllers\Admins\TemuanController;
use App\Http\Controllers\Admins\ReportController;
use App\Http\Controllers\Admins\MemberController;
use App\Http\Controllers\Admins\PatrolController;
use App\Http\Controllers\Admins\PatrolMemberController;
use App\Http\Controllers\Admins\NilaiController;
use App\Http\Controllers\Admins\AverageController;
use App\Http\Controllers\Admins\ReportKontribusiController;
use App\Http\Controllers\Admins\DailyPatrolController;
use App\Http\Controllers\Admins\DailyPatrolMemberController;
use App\Http\Controllers\Admins\DailyTemuanController;


use App\Http\Controllers\Users\UserDashboardController;
use App\Http\Controllers\Users\UserPatrolController;
use App\Http\Controllers\Users\UserPatrolMemberController;
use App\Http\Controllers\Users\UserTemuanController;
use App\Http\Controllers\Users\UserNilaiController;
use App\Http\Controllers\Users\UserDailyPatrolController;
use App\Http\Controllers\Users\UserDailyPatrolMemberController;
use App\Http\Controllers\Users\UserDailyTemuanController;





Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    if (session('login_type') == 1) {
        return redirect()->route('admins.dashboard.index');
    } elseif (session('login_type') == 2) {
        return redirect()->route('users.dashboard.index');
    } else {
        return redirect()->route('login');
    }
})->name('dashboard');


// Auth
// Auth
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login/admin', [AuthController::class, 'login_admin'])->name('login.admin');
Route::post('/login/member', [AuthController::class, 'login_member'])->name('login.member');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(AdminMiddleware::class)->group(function () {
    // Dashboard
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admins.dashboard.index');
    Route::get('/data_user', [DataUserController::class, 'index'])->name('data_user');
    Route::post('/data_user', [DataUserController::class, 'create'])->name('user.create');
    Route::put('/data_user/{id}', [DataUserController::class, 'update'])->name('user.update');
    Route::delete('/data_user/{id}', [DataUserController::class, 'destroy'])->name('user.destroy');

    // Member
    Route::get('/member', [MemberController::class, 'index'])->name('member');
    Route::post('/member', [MemberController::class, 'create'])->name('member.create');
    Route::put('/member/{id}', [MemberController::class, 'update'])->name('member.update');
    Route::delete('/member/{id}', [MemberController::class, 'destroy'])->name('member.destroy');
    Route::post('/member/import', [MemberController::class, 'import'])->name('member.import');
    Route::get('/member/upload', [MemberController::class, 'upload'])->name('member.upload');

    // Report
    Route::get('/report', [ReportController::class, 'index'])->name('report');

    // Patrol
    Route::get('/patrol', [PatrolController::class, 'index'])->name('patrol');         // GET /patrol
    Route::post('/patrol', [PatrolController::class, 'create'])->name('patrol.create');      // POST /patrol
    Route::put('/patrol/{id}', [PatrolController::class, 'update'])->name('patrol.update');   // PUT /patrol/{id}
    Route::delete('/patrol/{id}', [PatrolController::class, 'destroy'])->name('patrol.destroy'); // DELETE /patrol/{id}

    // Patrol Member
    Route::get('/patrol_member/{id}', [PatrolMemberController::class, 'index'])->name('patrol_member.index');
    Route::post('/patrol_member', [PatrolMemberController::class, 'create'])->name('patrol_member.create');
    Route::put('/patrol_member/update/{id}', [PatrolMemberController::class, 'update'])->name('patrol_member.update');
    Route::delete('/patrol_member/{id}', [PatrolMemberController::class, 'destroy'])->name('patrol_member.destroy');

    // Temuan
    Route::get('/temuan/{id}', [TemuanController::class, 'index'])->name('temuan.index');
    Route::post('/temuan/{id}', [TemuanController::class, 'store'])->name('temuan.store');
    Route::put('/temuan/{id}', [TemuanController::class, 'update'])->name('temuan.update');
    Route::delete('/temuan/{id}', [TemuanController::class, 'destroy'])->name('temuan.destroy');
    Route::put('/temuan/{id}/status', [TemuanController::class, 'updateStatus'])->name('temuan.updateStatus');
    Route::get('/temuan/export/{id}', [TemuanController::class, 'exportToPPT'])->name('temuan.export');
    Route::put('/temuan/{id}/rotate', [TemuanController::class, 'updateRotation'])->name('temuan.rotate');

    // Employee Search (AJAX)
    Route::get('/admin/employee/search', [TemuanController::class, 'searchEmployee'])->name('admin.employee.search');
    Route::get('/admin/employee/validate', [TemuanController::class, 'validateNik'])->name('admin.employee.validate');

    // Route::get('/nilai', [NilaiController::class, 'index'])->name('nilai.index');
    Route::get('/nilai/{id}', [NilaiController::class, 'index'])->name('nilai.index');
    Route::post('/nilai/{id}', [NilaiController::class, 'store'])->name('nilai.store');

    Route::get('/average/{patrolId}', [AverageController::class, 'index'])->name('average.index');

    // Laporan Kontribusi
    Route::get('/admin/reports/kontribusi', [ReportKontribusiController::class, 'index'])->name('admins.reports.kontribusi');

    // Daily Patrol
    Route::get('/daily_patrol', [DailyPatrolController::class, 'index'])->name('daily_patrol');
    Route::post('/daily_patrol', [DailyPatrolController::class, 'create'])->name('daily_patrol.create');
    Route::put('/daily_patrol/{id}', [DailyPatrolController::class, 'update'])->name('daily_patrol.update');
    Route::delete('/daily_patrol/{id}', [DailyPatrolController::class, 'destroy'])->name('daily_patrol.destroy');

    // Daily Patrol Member
    Route::get('/daily_patrol_member/{id}', [DailyPatrolMemberController::class, 'index'])->name('daily_patrol_member.index');
    Route::post('/daily_patrol_member', [DailyPatrolMemberController::class, 'create'])->name('daily_patrol_member.create');
    Route::put('/daily_patrol_member/update/{id}', [DailyPatrolMemberController::class, 'update'])->name('daily_patrol_member.update');
    Route::delete('/daily_patrol_member/{id}', [DailyPatrolMemberController::class, 'destroy'])->name('daily_patrol_member.destroy');

    // Daily Temuan
    Route::get('/daily_temuan/{id}', [DailyTemuanController::class, 'index'])->name('daily_temuan.index');
    Route::post('/daily_temuan/{id}', [DailyTemuanController::class, 'store'])->name('daily_temuan.store');
    Route::put('/daily_temuan/{id}', [DailyTemuanController::class, 'update'])->name('daily_temuan.update');
    Route::delete('/daily_temuan/{id}', [DailyTemuanController::class, 'destroy'])->name('daily_temuan.destroy');
    Route::put('/daily_temuan/{id}/status', [DailyTemuanController::class, 'updateStatus'])->name('daily_temuan.updateStatus');
    Route::get('/daily_temuan/export/{id}', [DailyTemuanController::class, 'exportToPPT'])->name('daily_temuan.export');
    Route::put('/daily_temuan/{id}/rotate', [DailyTemuanController::class, 'updateRotation'])->name('daily_temuan.rotate');
});


Route::middleware(AuthMiddleware::class)->group(function () {

    Route::get('/user/patrols', [UserDashboardController::class, 'index'])->name('users.patrols.index');

    Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('users.dashboard.index');

    Route::get('/user_patrol', [UserPatrolController::class, 'index'])->name('user_patrol');
    Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('users.dashboard.index'); // Data User

    Route::get('/user_patrol', [UserPatrolController::class, 'index'])->name('user_patrol');         // GET /patrol
    Route::post('/user_patrol', [UserPatrolController::class, 'create'])->name('user_patrol.create');      // POST /patrol
    Route::put('/user_patrol/{id}', [UserPatrolController::class, 'update'])->name('user_patrol.update');   // PUT /patrol/{id}
    Route::delete('/user_patrol/{id}', [UserPatrolController::class, 'destroy'])->name('user_patrol.destroy'); // DELETE /patrol/{id}

    Route::get('/user_patrol_member/{id}', [UserPatrolMemberController::class, 'index'])->name('user_patrol_member.index');

    Route::get('/user_temuan/{id}', [UserTemuanController::class, 'index'])->name('user_temuan.index');
    Route::post('/user_temuan/{id}', [UserTemuanController::class, 'store'])->name('user_temuan.store');
    Route::put('/user_temuan/{id}', [UserTemuanController::class, 'update'])->name('user_temuan.update');
    Route::delete('/user_temuan/{id}', [UserTemuanController::class, 'destroy'])->name('user_temuan.destroy');
    Route::put('/user_temuan/{id}', [UserTemuanController::class, 'update'])->name('user_temuan.update');
    Route::get('/user_temuan/{id}', [UserTemuanController::class, 'index'])->name('user_temuan.index');

    // Route::get('/user_nilai', [UserNilaiController::class, 'index'])->name('user_nilai.index');
    // Route::get('/user_nilai/{id}', [UserNilaiController::class, 'index'])->name('user_nilai.index');

    Route::get('/user_nilai/{id}', [UserNilaiController::class, 'index'])->name('user_nilai.index');
    Route::post('/user_nilai/{id}', [UserNilaiController::class, 'store'])->name('user_nilai.store');

    // User Daily Patrol
    Route::get('/user_daily_patrol', [UserDailyPatrolController::class, 'index'])->name('user_daily_patrol');
    Route::post('/user_daily_patrol', [UserDailyPatrolController::class, 'create'])->name('user_daily_patrol.create');
    Route::put('/user_daily_patrol/{id}', [UserDailyPatrolController::class, 'update'])->name('user_daily_patrol.update');
    Route::delete('/user_daily_patrol/{id}', [UserDailyPatrolController::class, 'destroy'])->name('user_daily_patrol.destroy');

    Route::get('/user_daily_patrol_member/{id}', [UserDailyPatrolMemberController::class, 'index'])->name('user_daily_patrol_member.index');

    Route::get('/user_daily_temuan/{id}', [UserDailyTemuanController::class, 'index'])->name('user_daily_temuan.index');
    Route::post('/user_daily_temuan/{id}', [UserDailyTemuanController::class, 'store'])->name('user_daily_temuan.store');
    Route::put('/user_daily_temuan/{id}', [UserDailyTemuanController::class, 'update'])->name('user_daily_temuan.update');
    Route::delete('/user_daily_temuan/{id}', [UserDailyTemuanController::class, 'destroy'])->name('user_daily_temuan.destroy');
});
