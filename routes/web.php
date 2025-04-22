<?php
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GymStaffController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\FeedbackController;
use App\Http\Middleware\StaffMiddleware;
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




// Admin routes
Route::group(['middleware' => ['auth', 'admin', \App\Http\Middleware\PreventBackHistory::class]], function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/report-analytics', [AdminController::class, 'reportAnalytics'])->name('admin.reportAnalytics');
    Route::get('/admin/report/download', [AdminController::class, 'downloadReport'])->name('admin.downloadReport');
    Route::get('/admin/print-report', [AdminController::class, 'printReport'])->name('admin.printReport');
    Route::get('/admin/profile', [AdminController::class, 'showProfile'])->name('admin.profile');
    Route::post('/admin/register/new', [AdminController::class, 'registerNewAdmin'])->name('admin.register.new');
    Route::put('/admin/profile/update', [AdminController::class, 'updateProfile'])->name('admin.profile.update');
    Route::put('/admin/profile/picture', [AdminController::class, 'updatePicture'])->name('admin.profile.update_picture');
    Route::get('/admin/subscriptions', [AdminController::class, 'showSubscriptions'])->name('admin.subscription');
    Route::get('/admin/subscriptions/{id}', [AdminController::class, 'getSubscriptionDetails']);
    Route::post('/admin/subscription/add', [AdminController::class, 'addSubscription'])->name('admin.subscription.add');
    Route::put('/admin/subscription/update', [AdminController::class, 'updateSubscription'])->name('admin.subscription.update');
    Route::delete('/admin/subscription/delete', [AdminController::class, 'deleteSubscription'])->name('admin.subscription.delete');
    Route::get('/calculate-amount', 'AdminController@calculateAmount');
    Route::get('/admin/payments/{memberId?}', [AdminController::class, 'showPaymentForm'])->name('admin.payment.form');
    Route::post('/admin/payments/add', [AdminController::class, 'addPayment'])->name('admin.payment.add');
    Route::get('admin/payment/edit/{id}', [AdminController::class, 'editPayment'])->name('admin.payment.edit');
    Route::put('admin/payment/update/{id}', [AdminController::class, 'updatePayment'])->name('admin.payment.update');
    Route::delete('/admin/payments/{id}/delete', [AdminController::class, 'deletePayment'])->name('admin.payment.delete');
    Route::get('/admin/payment/report/download', [AdminController::class, 'downloadpaymentPDF'])->name('admin.payment-pdf.download');
    Route::get('/admin/member/report', [AdminController::class, 'generateMemberReport'])->name('admin.member_report');
    Route::get('/admin/members/{id}/subscription', [AdminController::class, 'getMemberSubscription']);
    Route::get('/admin/inventory', [AdminController::class, 'showInventory'])->name('admin.equipment_inventory');
    Route::get('/admin/inventory/{id}', [AdminController::class, 'showInventory'])->name('admin.inventory.details'); 
    Route::post('/admin/inventory/add', [AdminController::class, 'addEquipment'])->name('admin.equipment.add');
    Route::put('/admin/inventory/update', [AdminController::class, 'updateEquipment'])->name('admin.inventory.update');
    Route::delete('/admin/inventory/delete/{id}', [AdminController::class, 'deleteEquipment'])->name('admin.inventory.delete');
    Route::get('/admin/equipment/report', [AdminController::class, 'generateEquipmentReport'])->name('admin.equipment.report.download');
    Route::get('/admin/members', [AdminController::class, 'showMembers'])->name('admin.member_management');
    Route::post('/admin/member_management/add', [AdminController::class, 'addMember'])->name('admin.member.add');
    Route::delete('admin/member/delete/{id}', [AdminController::class, 'deleteMember'])->name('admin.member.delete');
    Route::put('/admin/member/update/{id}', [AdminController::class, 'updateMember'])->name('admin.member.update');
    Route::get('/admin/reports', [AdminController::class, 'showReports'])->name('admin.reports');
    Route::post('/admin/attendance/record', [AdminController::class, 'recordAttendance'])->name('admin.recordAttendance');
    Route::get('/admin/attendance/view/{member_id}', [AdminController::class, 'viewAttendance'])->name('admin.attendance.view');
    Route::post('/admin/attendance/checkout', [AdminController::class, 'checkOut'])->name('admin.checkOut');
    Route::get('/admin/attendance', [AdminController::class, 'showAttendance'])->name('admin.attendance');
    Route::get('/attendance/printpdf', [AdminController::class, 'generatePdf'])->name('attendance.pdf');
    Route::get('/subscription/{id}', [AdminController::class, 'showValidity']);
    Route::get('/notifications', [AdminController::class, 'getNotifications']);
    Route::post('/notifications/mark-all-as-read', [AdminController::class, 'markAllAsRead']);
    Route::post('/notifications/mark-as-read', [AdminController::class, 'markAsRead']);
    Route::get('/notifications/unread-count', [AdminController::class, 'getUnreadNotificationCount']);
    Route::get('print-receipt/{member_id}', [AdminController::class, 'printReceipt'])->name('admin.print.receipt');
    Route::post('/admin/member/renew/{id}', [AdminController::class, 'renew'])->name('admin.member.renew');
    Route::get('/admin/payment-history', [AdminController::class, 'showPaymentHistory'])->name('admin.payment.history');
    Route::get('/admin/payment-history/download', [AdminController::class, 'downloadPaymentHistory'])->name('admin.payment.history.download');
    Route::get('/admin/staff-management', [AdminController::class, 'showStaffManagement'])->name('admin.staff.management');
    Route::post('/admin/staff-management/add', [AdminController::class, 'storeStaff'])->name('admin.staff.add');
    Route::put('/admin/staff-management/update/{id}', [AdminController::class, 'updateStaff'])->name('admin.staff.edit');
    Route::delete('/admin/staff-management/delete/{id}', [AdminController::class, 'deleteStaff'])->name('admin.staff.delete');
    Route::get('/admin/feedbacks', [AdminController::class, 'showFeedbacks'])->name('admin.feedback');
    Route::delete('/admin/feedback/{id}', [AdminController::class, 'destroy'])->name('admin.feedback.delete');

});


Route::group(['middleware' => ['auth:gym_staff']], function () {
    Route::get('/gym_staff/dashboard', [GymStaffController::class, 'dashboard'])->name('gym_staff.dashboard');
    Route::get('/gym_staff/members', [GymStaffController::class, 'showMembers'])->name('gym_staff.member_management');
    Route::post('/gym_staff/member_management/add', [GymStaffController::class, 'addMember'])->name('gym_staff.member.add');
    Route::delete('gym_staff/member/delete/{id}', [GymStaffController::class, 'deleteMember'])->name('gym_staff.member.delete');
    Route::put('/gym_staff/member/update/{id}', [GymStaffController::class, 'updateMember'])->name('gym_staff.member.update');
    Route::get('/staff/profile', [GymStaffController::class, 'showProfile'])->name('staff.profile');
    Route::post('/staff/profile/update', [GymStaffController::class, 'updateProfile'])->name('staff.profile.update');
    Route::get('/gym_staff/attendance', [GymStaffController::class, 'showAttendance'])->name('gym_staff.attendance');
    Route::get('/gym_staff/attendance/view/{member_id}', [GymStaffController::class, 'viewAttendance'])->name('gym_staff.attendance.view');
    Route::get('/attendance/pdf', [GymStaffController::class, 'generatePdf'])->name('gym_staff.attendance.pdf');
    Route::get('/gym_staff/payments/{memberId?}', [GymStaffController::class, 'showPaymentForm'])->name('gym_staff.payment.form');
    Route::post('/gym_staff/payments/add', [GymStaffController::class, 'addPayment'])->name('gym_staff.payment.add');
    Route::get('gym_staff/payment/edit/{id}', [GymStaffController::class, 'editPayment'])->name('gym_staff.payment.edit');
    Route::put('gym_staff/payment/update/{id}', [GymStaffController::class, 'updatePayment'])->name('gym_staff.payment.update');
    Route::delete('/gym_staff/payments/{id}/delete', [GymStaffController::class, 'deletePayment'])->name('gym_staff.payment.delete');
    Route::get('gymstaff_print-receipt/{member_id}', [GymStaffController::class, 'printReceipt'])->name('gym_staff.print.receipt');
    // Other staff routes...
});
Route::get('/attendance', [AttendanceController::class, 'showAttendance'])->name('member.attendance.show');
Route::post('/attendance/generate', [AttendanceController::class, 'generateAttendance'])->name('member.attendance.generate');
Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])->name('member.attendance.check-in');
Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut'])->name('member.attendance.check-out');
Route::post('feedback/submit', [FeedbackController::class, 'submitFeedback'])->name('feedback.submit');


