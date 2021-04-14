<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;   //2021-04-08 add, for View Composer
//use Illuminate\View\View;
use Illuminate\Support\ServiceProvider;
//use App\Http\View\Composers\BlogsComposer;

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
        //2021-04-08 add, for View Composer
        View::composer('front.nav', 'App\View\Composers\BlogComposer');
    }
}
