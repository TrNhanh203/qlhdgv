<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\University;
use App\Observers\AuditObserver;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\Lecture;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void

    {
        University::observe(AuditObserver::class);
        Faculty::observe(AuditObserver::class);
        Department::observe(AuditObserver::class);
        Lecture::observe(AuditObserver::class);
        View::composer('*', function ($view) {
            $shortName = 'Admin';

            if (Auth::check()) {
                $user = Auth::user();
                if ($user->university_id) {
                    $university = University::find($user->university_id);
                    $shortName = $university?->short_name ?? 'Admin';
                }
            }

            $view->with('shortName', $shortName);
        });
        View::composer('*', function ($view) {
        $view->with('user', Auth::user());
    });
    }
}
