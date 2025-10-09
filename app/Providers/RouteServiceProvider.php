<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Đường dẫn mặc định để chuyển hướng sau khi đăng nhập thành công.
     */
    public const HOME = '/admin/dashboard'; 

    /**
     * Định nghĩa route cho ứng dụng.
     */
    public function boot(): void
    {
        $this->routes(function () {
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::prefix('api')
                ->middleware('api')
                ->group(base_path('routes/api.php'));
        });
    }
}
