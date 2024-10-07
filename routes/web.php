<?php
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GymStaffController;
use App\Http\Controllers\AttendanceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('logout', [LoginController::class, 'logout'])->name('logout');


Route::post('/notifications/mark-as-read/{id}', [AdminController::class, 'markAsRead'])->name('notifications.markAsRead');
Route::get('/notifications/unread-count', [AdminController::class, 'getUnreadCount'])->name('notifications.getUnreadCount');

// Admin routes
Route::group(['middleware' => ['auth', 'admin', \App\Http\Middleware\PreventBackHistory::class]], function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/report-analytics', [AdminController::class, 'reportAnalytics'])->name('admin.reportAnalytics');
    Route::get('/admin/report/download', [AdminController::class, 'downloadReport'])->name('admin.downloadReport');
    Route::get('/admin/print-report', [AdminController::class, 'printReport'])->name('admin.printReport');
    Route::get('/admin/profile', [AdminController::class, 'showProfile'])->name('admin.profile');
    Route::put('/admin/profile/update', [AdminController::class, 'updateProfile'])->name('admin.profile.update');
    Route::put('/admin/profile/picture', [AdminController::class, 'updatePicture'])->name('admin.profile.update_picture');
    Route::get('/admin/subscriptions', [AdminController::class, 'showSubscriptions'])->name('admin.subscription');
    Route::get('/admin/subscriptions/{id}', [AdminController::class, 'getSubscriptionDetails']);
    Route::post('/admin/subscription/add', [AdminController::class, 'addSubscription'])->name('admin.subscription.add');
    Route::put('/admin/subscription/update', [AdminController::class, 'updateSubscription'])->name('admin.subscription.update');
    Route::delete('/admin/subscription/delete', [AdminController::class, 'deleteSubscription'])->name('admin.subscription.delete');
    Route::get('/admin/payments', [AdminController::class, 'showPaymentForm'])->name('admin.payment.form');
    Route::post('/admin/payments/add', [AdminController::class, 'addPayment'])->name('admin.payment.add');
    Route::put('/admin/payments/update', [AdminController::class, 'updatePayment'])->name('admin.payment.update');
    Route::delete('/admin/payments/delete', [AdminController::class, 'deletePayment'])->name('admin.payment.delete');
    Route::get('/admin/members/{id}/subscription', [AdminController::class, 'getMemberSubscription']);
    Route::get('/admin/inventory', [AdminController::class, 'showInventory'])->name('admin.equipment_inventory');
    Route::get('/admin/inventory/{id}', [AdminController::class, 'showInventory'])->name('admin.inventory.details'); 
    Route::post('/admin/inventory/add', [AdminController::class, 'addEquipment'])->name('admin.equipment.add');
    Route::put('/admin/inventory/update', [AdminController::class, 'updateEquipment'])->name('admin.inventory.update');
    Route::delete('/admin/inventory/delete/{id}', [AdminController::class, 'deleteEquipment'])->name('admin.inventory.delete');
    Route::get('/admin/members', [AdminController::class, 'showMembers'])->name('admin.member_management');
    Route::post('/admin/member_management/add', [AdminController::class, 'addMember'])->name('admin.member.add');
    Route::delete('admin/member/delete/{id}', [AdminController::class, 'deleteMember'])->name('admin.member.delete');
    Route::put('/admin/member/update/{id}', [AdminController::class, 'updateMember'])->name('admin.member.update');
    Route::get('/admin/reports', [AdminController::class, 'showReports'])->name('admin.reports');
    Route::post('/admin/attendance/record', [AdminController::class, 'recordAttendance'])->name('admin.recordAttendance');
    Route::post('/admin/attendance/checkout', [AdminController::class, 'checkOut'])->name('admin.checkOut');
    Route::get('/admin/attendance', [AdminController::class, 'showAttendance'])->name('admin.attendance');

    Route::put('/admin/member/renew/{id}', [AdminController::class, 'renew'])->name('admin.member.renew');
    Route::get('/subscription/{id}', [AdminController::class, 'showValidity']);


});

Route::group(['middleware' => ['auth', 'staff']], function () {
    Route::get('/staff/dashboard', [GymStaffController::class, 'dashboard'])->name('staff.dashboard');
    Route::get('/staff/profile', [GymStaffController::class, 'showProfile'])->name('staff.profile');
    Route::post('/staff/profile/update', [GymStaffController::class, 'updateProfile'])->name('staff.profile.update');
    // Other staff routes...
});
Route::get('/attendance', [AttendanceController::class, 'showAttendance'])->name('member.attendance.show');
Route::post('/attendance/generate', [AttendanceController::class, 'generateAttendance'])->name('member.attendance.generate');
Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])->name('member.attendance.check-in');
Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut'])->name('member.attendance.check-out');