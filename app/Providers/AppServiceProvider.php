<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\RulesController;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::if('volunteer', function () {
            if (auth()->check()) {
                $id = Auth::user()->id;
                $volunteer = DB::select("select * from users where id={$id} and (idrole=1 or idrole=2)");
                if (sizeof($volunteer)>0) {
                    return true;
                } else {
                    return false;
                }
            }
            return false;
        });

        Blade::if('member', function () {
            return auth()->check() && !Auth::user()->banned;
        });

        Blade::if('useridequals', function ($userid) {
            return auth()->check() && $userid==Auth::id();
        });

        Blade::if('secretary', function () {
            if (auth()->check()) {
                $id = Auth::user()->id;
                $secretary = DB::select("SELECT * from users where id={$id} and idrole=2 LIMIT 1");
                return (sizeof($secretary)>0);
            }
            return false;
        });

        Blade::if('underrentgamelimit', function ($count) {
            return RulesController::getRentGameLimit()>$count;
        });

        Blade::if('broken', function ($id) {
            $broken = DB::select("SELECT * from currentdamaged where idgame=${id}");
            return (sizeof($broken)>0);
        });

        Blade::if('userowesrefund', function () {
            $id = Auth::user()->id;
            $broken = DB::select("SELECT * from currentdamaged where iduser=${id}");
            return (sizeof($broken)>0);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
