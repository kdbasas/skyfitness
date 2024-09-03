<?php
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GymStaffController;
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
Route::group(['middleware' => ['auth', 'admin']], function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
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
    Route::get('/admin/inventory', [AdminController::class, 'showInventory'])->name('admin.inventory');
    Route::post('/admin/inventory/add', [AdminController::class, 'addEquipment'])->name('admin.inventory.add');
    Route::get('/admin/members', [AdminController::class, 'showMembers'])->name('admin.member_management');
    Route::post('/admin/member_management/add', [AdminController::class, 'addMember'])->name('admin.member.add');
    Route::delete('admin/member/delete/{id}', [AdminController::class, 'deleteMember'])->name('admin.member.delete');
    Route::put('/admin/member/update/{id}', [AdminController::class, 'updateMember'])->name('admin.member.update');
    Route::get('/admin/reports', [AdminController::class, 'showReports'])->name('admin.reports');
    Route::get('/admin/attendance', [AdminController::class, 'showAttendance'])->name('admin.attendance');
});

Route::group(['middleware' => ['auth', 'staff']], function () {
    Route::get('/staff/dashboard', [GymStaffController::class, 'dashboard'])->name('staff.dashboard');
    Route::get('/staff/profile', [GymStaffController::class, 'showProfile'])->name('staff.profile');
    Route::post('/staff/profile/update', [GymStaffController::class, 'updateProfile'])->name('staff.profile.update');
    // Other staff routes...
});
