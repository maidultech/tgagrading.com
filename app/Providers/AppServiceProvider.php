<?php

namespace App\Providers;

use App\Models\Blog;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
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
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
    
            // Redirect www to non-www
            if (Request::getHost() && str_starts_with(Request::getHost(), 'www.')) {
                $nonWww = str_replace('www.', '', Request::getHost());
                $redirectUrl = 'https://' . $nonWww . Request::getRequestUri();
    
                header("Location: $redirectUrl", true, 301);
                exit(); // Make sure to exit after redirection
            }
        }
        
        Paginator::useBootstrap();
        view()->composer('frontend.layouts.footer', function ($view) {
            $view->with('latest_blogs', Blog::where('status', 1)->orderBy('id', 'DESC')->limit(5)->get());
        });

        view()->share('setting',getSetting());
    }
}
