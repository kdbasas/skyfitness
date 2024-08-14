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
    Route::post('/admin/subscription/add', [AdminController::class, 'addSubscription'])->name('admin.subscription.add');



});
Route::group(['middleware' => ['auth', 'staff']], function () {
    Route::get('/staff/dashboard', [GymStaffController::class, 'dashboard'])->name('staff.dashboard');
    Route::get('/staff/profile', [GymStaffController::class, 'showProfile'])->name('staff.profile');
    Route::post('/staff/profile/update', [GymStaffController::class, 'updateProfile'])->name('staff.profile.update');
    // Other staff routes...
});
