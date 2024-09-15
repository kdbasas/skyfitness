<?php

namespace App\Providers;

use App\Models\Subscription;
use App\Models\Member;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth; 
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('*', function ($view) {
            $notifications = [];
    
            if (Auth::check()) {
                $admin = Auth::user();
    
                // Welcome notification
                if (session()->has('just_logged_in')) {
                    $notifications[] = [
                        'message' => "Welcome, {$admin->name}!",
                        'type' => 'info',
                    ];
                    session()->forget('just_logged_in');
                }
                // Subscription expiry notifications
                $now = Carbon::now();
                $expiringSubscriptions = Member::whereDate('date_expired', '=', $now->toDateString())->get();
                foreach ($expiringSubscriptions as $member) {
                    $notifications[] = [
                        'message' => "Subscription for {$member->first_name} {$member->last_name} has expired today!",
                        'type' => 'danger',
                    ];
                }

                // Future expiry notifications
                $futureExpiringSubscriptions = Member::whereDate('date_expired', '<=', $now->addDays(5)->toDateString())->get();
                foreach ($futureExpiringSubscriptions as $member) {
                    $notifications[] = [
                        'message' => "Subscription for {$member->first_name} {$member->last_name} is expiring in 5 days!",
                        'type' => 'warning',
                    ];
                }
            }
            $view->with('notifications', $notifications);
        });
    }
}